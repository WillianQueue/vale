<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Smalot\PdfParser\Parser as PdfParser;
use Symfony\Component\HttpFoundation\Response;

class ChatController extends Controller
{
    public function index(): View
    {
        $usuario = Auth::user();
        $historico = collect();

        if ($usuario instanceof User) {
            $historico = $usuario
                ->conversations()
                ->with(['chats' => fn ($query) => $query->oldest()])
                ->latest('updated_at')
                ->get();
        }

        return view('chat', compact('historico'));
    }

    public function enviar(Request $request): Response
    {
        set_time_limit(330);
        ini_set('max_execution_time', '330');
        ini_set('memory_limit', '512M');

        $validated = $request->validate([
            'pergunta' => ['required', 'string', 'max:1000'],
            'conversation_id' => ['nullable', 'integer'],
        ]);

        $pergunta = trim($validated['pergunta']);
        $contexto = $this->carregarContextoDePasta(
            storage_path('app/documentos'),
            $pergunta
        );
        $prompt = $this->montarPrompt($pergunta, $contexto);
        $usuarioId = Auth::id();
        $conversa = null;

        if ($usuarioId !== null && !empty($validated['conversation_id'])) {
            $conversa = Conversation::query()
                ->where('user_id', $usuarioId)
                ->findOrFail($validated['conversation_id']);
        }

        try {
            $ollamaResponse = Http::connectTimeout(10)
                ->withOptions([
                    'stream' => true,
                ])
                ->timeout(300)
                ->post('http://127.0.0.1:11434/api/generate', [
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
            Log::error('Ollama retornou um erro.', [
                'status' => $ollamaResponse->status(),
                'body' => $ollamaResponse->body(),
            ]);

            return response()->json([
                'message' => 'Erro ao comunicar com a API do Ollama.',
            ], 502);
        }

        $fonte = $contexto !== ''
            ? 'documentos'
            : 'conhecimento_geral';

        return response()->stream(function () use (
            $ollamaResponse,
            $pergunta,
            $usuarioId,
            $conversa,
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

                        $this->liberarBuffer();
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

            $respostaCompleta = trim($respostaCompleta);
            $conversaAtual = $conversa;

            if ($usuarioId !== null && $respostaCompleta !== '') {
                if ($conversaAtual === null) {
                    $conversaAtual = Conversation::create([
                        'user_id' => $usuarioId,
                        'title' => mb_substr($pergunta, 0, 120),
                    ]);
                }

                Chat::create([
                    'user_id' => $usuarioId,
                    'conversation_id' => $conversaAtual->id,
                    'pergunta' => $pergunta,
                    'resposta' => $respostaCompleta,
                    'fonte' => $fonte,
                ]);

                $conversaAtual->touch();
            }

            echo json_encode([
                'text' => '',
                'done' => true,
                'conversation_id' => $conversaAtual->id ?? $conversa?->id,
            ], JSON_UNESCAPED_UNICODE) . "\n";

            $this->liberarBuffer();
        }, 200, [
            'Content-Type' => 'application/x-ndjson; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function show(Conversation $conversation): JsonResponse
    {
        abort_unless(
            Auth::check() && $conversation->user_id === Auth::id(),
            403
        );

        return response()->json([
            'id' => $conversation->id,
            'title' => $conversation->title,
            'messages' => $conversation
                ->chats()
                ->oldest()
                ->get(['pergunta', 'resposta'])
                ->map(fn (Chat $chat): array => [
                    'question' => $chat->pergunta,
                    'answer' => $chat->resposta,
                ])
                ->values(),
        ]);
    }

    public function destroy(Conversation $conversation): JsonResponse
    {
        abort_unless(
            Auth::check() && $conversation->user_id === Auth::id(),
            403
        );

        $conversation->delete();

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
        $limiteContexto = 24000;
        $pdfParser = class_exists(PdfParser::class)
            ? new PdfParser()
            : null;

        foreach ($arquivos as $arquivo) {
            $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
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
                $palavra = trim($palavra);

                if ($palavra !== '' && str_contains(
                    $conteudoLower,
                    $palavra
                )) {
                    $relevante = true;
                    break;
                }
            }

            if ($relevante) {
                $espacoRestante = $limiteContexto - mb_strlen($contextoEncontrado);

                if ($espacoRestante <= 0) {
                    break;
                }

                $contextoEncontrado .= sprintf(
                    "--- Documento: %s ---\n%s\n\n",
                    basename($arquivo),
                    mb_substr($conteudo, 0, min(6000, $espacoRestante))
                );
            }
        }

        return $contextoEncontrado;
    }

    private function liberarBuffer(): void
    {
        if (ob_get_level() > 0) {
            ob_flush();
        }

        flush();
    }
}
