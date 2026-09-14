<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Smalot\PdfParser\Parser as PdfParser;

class ChatController extends Controller
{
    public function index(): View
    {
        $historico = auth()->check()
            ? auth()->user()->chats()->latest()->get()
            : collect();

        return view('chat', compact('historico'));
    }

    public function enviar(Request $request): Response
    {
        set_time_limit(0);
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '512M');

        $validated = $request->validate([
            'pergunta' => ['required', 'string', 'max:1000'],
        ]);

        $pergunta = trim($validated['pergunta']);

        $contexto = $this->carregarContextoDePasta(
            storage_path('app/documentos'),
            $pergunta
        );

        $prompt = $this->montarPrompt($pergunta, $contexto);

        try {
            $ollamaResponse = Http::connectTimeout(10)
                ->withOptions([
                    'stream' => true,
                ])
                ->timeout(0)
                ->post('http://localhost:11434/api/generate', [
                    'model' => 'gemma3:12b',
                    'prompt' => $prompt,
                    'stream' => true,
                ]);
        } catch (\Throwable $exception) {
            Log::error('Erro ao conectar ao Ollama.', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'O servidor do Ollama está offline ou inacessível.',
            ], 503);
        }

        if (!$ollamaResponse->successful()) {
            return response()->json([
                'message' => 'Erro ao comunicar com a API do Ollama.',
            ], 502);
        }

        $usuarioId = auth()->id();
        $fonte = $contexto !== ''
            ? 'documentos'
            : 'conhecimento_geral';

        return response()->stream(function () use (
            $ollamaResponse,
            $pergunta,
            $usuarioId,
            $fonte
        ): void {
            $respostaCompleta = '';
            $buffer = '';
            $corpo = $ollamaResponse->toPsrResponse()->getBody();

            while (!$corpo->eof()) {
                $buffer .= $corpo->read(8192);
                $linhas = explode("\n", $buffer);
                $buffer = array_pop($linhas) ?? '';

                foreach ($linhas as $linha) {
                    $linha = trim($linha);

                    if ($linha === '') {
                        continue;
                    }

                    $dados = json_decode($linha, true);

                    if (!is_array($dados)) {
                        continue;
                    }

                    $trecho = (string) ($dados['response'] ?? '');

                    if ($trecho !== '') {
                        $respostaCompleta .= $trecho;

                        echo json_encode([
                            'text' => $trecho,
                            'done' => false,
                        ], JSON_UNESCAPED_UNICODE) . "\n";

                        if (ob_get_level() > 0) {
                            ob_flush();
                        }

                        flush();
                    }

                    if (($dados['done'] ?? false) === true) {
                        break 2;
                    }
                }
            }

            if (trim($buffer) !== '') {
                $dados = json_decode(trim($buffer), true);

                if (is_array($dados)) {
                    $trecho = (string) ($dados['response'] ?? '');

                    if ($trecho !== '') {
                        $respostaCompleta .= $trecho;

                        echo json_encode([
                            'text' => $trecho,
                            'done' => false,
                        ], JSON_UNESCAPED_UNICODE) . "\n";
                    }
                }
            }

            if ($usuarioId !== null && trim($respostaCompleta) !== '') {
                Chat::create([
                    'user_id' => $usuarioId,
                    'pergunta' => $pergunta,
                    'resposta' => trim($respostaCompleta),
                    'fonte' => $fonte,
                ]);
            }

            echo json_encode([
                'text' => '',
                'done' => true,
            ], JSON_UNESCAPED_UNICODE) . "\n";

            if (ob_get_level() > 0) {
                ob_flush();
            }

            flush();
        }, 200, [
            'Content-Type' => 'application/x-ndjson; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function destroy(Chat $chat): JsonResponse
    {
        abort_unless(
            auth()->check() && $chat->user_id === auth()->id(),
            403
        );

        $chat->delete();

        return response()->json([
            'status' => 'sucesso',
            'message' => 'Conversa excluída com sucesso.',
        ]);
    }

    private function montarPrompt(
        string $pergunta,
        string $contexto
    ): string {
        $prompt = implode("\n", [
            'Você é a Vale, uma assistente de pesquisa especializada no Vale do Paraíba (SP/RJ/MG).',
            'Responda sempre em português do Brasil.',
            'Responda em texto corrido, com parágrafos claros e diretos.',
            'Não use Markdown, hashtags, negrito ou listas com marcadores.',
            'Não invente fontes ou informações.',
        ]);

        if ($contexto !== '') {
            $prompt .= "\n\nDOCUMENTOS OFICIAIS:\n";
            $prompt .= $contexto;
            $prompt .= "\nBaseie a resposta prioritariamente nesses documentos.";
        }

        return $prompt . "\n\nPERGUNTA:\n" . $pergunta;
    }

    private function carregarContextoDePasta(
        string $caminhoPasta,
        string $query
    ): string {
        if (!is_dir($caminhoPasta)) {
            return '';
        }

        $arquivos = glob(
            $caminhoPasta . '/*.{txt,md,json,pdf}',
            GLOB_BRACE
        );

        if ($arquivos === false || $arquivos === []) {
            return '';
        }

        $palavrasChave = array_filter(
            preg_split('/\s+/', mb_strtolower($query)) ?: [],
            fn (string $palavra): bool => mb_strlen(trim($palavra)) > 2
        );

        $contextoEncontrado = '';

        $pdfParser = class_exists(PdfParser::class)
            ? new PdfParser()
            : null;

        foreach ($arquivos as $arquivo) {
            $extensao = strtolower(
                pathinfo($arquivo, PATHINFO_EXTENSION)
            );

            $conteudo = '';

            try {
                if ($extensao === 'pdf') {
                    if ($pdfParser === null) {
                        continue;
                    }

                    $conteudo = $pdfParser
                        ->parseFile($arquivo)
                        ->getText();
                } else {
                    $conteudo = file_get_contents($arquivo) ?: '';
                }
            } catch (\Throwable $exception) {
                Log::warning('Não foi possível ler o documento.', [
                    'arquivo' => $arquivo,
                    'message' => $exception->getMessage(),
                ]);

                continue;
            }

            if (trim($conteudo) === '') {
                continue;
            }

            $conteudoLower = mb_strtolower($conteudo);
            $relevante = $palavrasChave === [];

            foreach ($palavrasChave as $palavra) {
                if (str_contains($conteudoLower, trim($palavra))) {
                    $relevante = true;
                    break;
                }
            }

            if (!$relevante) {
                continue;
            }

            $contextoEncontrado .= sprintf(
                "--- Documento: %s ---\n%s\n\n",
                basename($arquivo),
                mb_substr($conteudo, 0, 12000)
            );
        }

        return $contextoEncontrado;
    }
}