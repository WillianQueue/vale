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

    public function enviar(Request $request): JsonResponse
    {
        set_time_limit(0);
        ini_set('max_execution_time', '0');
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
                ->timeout(300)
                ->post('http://127.0.0.1:11434/api/generate', [
                    'model' => 'gemma3:12b',
                    'prompt' => $prompt,
                    'stream' => false,
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

        $resposta = trim((string) $ollamaResponse->json('response'));

        if ($resposta === '') {
            return response()->json([
                'message' => 'O modelo não retornou uma resposta.',
            ], 502);
        }

        if ($usuarioId !== null && $conversa === null) {
            $conversa = Conversation::create([
                'user_id' => $usuarioId,
                'title' => mb_substr($pergunta, 0, 120),
            ]);
        }

        if ($conversa !== null) {
            Chat::create([
                'user_id' => $usuarioId,
                'conversation_id' => $conversa->id,
                'pergunta' => $pergunta,
                'resposta' => $resposta,
                'fonte' => $contexto !== ''
                    ? 'documentos'
                    : 'conhecimento_geral',
            ]);

            $conversa->touch();
        }

        return response()->json([
            'text' => $resposta,
            'done' => true,
            'conversation_id' => $conversa?->id,
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
                $contextoEncontrado .= sprintf(
                    "--- Documento: %s ---\n%s\n\n",
                    basename($arquivo),
                    mb_substr($conteudo, 0, 12000)
                );
            }
        }

        return $contextoEncontrado;
    }
}
