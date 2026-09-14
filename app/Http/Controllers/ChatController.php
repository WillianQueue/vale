<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function index()
    {
        // Carrega as conversas do usuário autenticado
        $historico = auth()->user()->chats()->latest()->get();

        return view('chat', compact('historico'));
    }

    public function enviar(Request $request)
    {
        set_time_limit(0);
        // Eleva o limite de memória para processar PDFs pesados
ini_set('memory_limit', '512M');
        $request->validate([
            'pergunta' => 'required|string|max:1000'
        ]);

        $pergunta = trim($request->input('pergunta'));
        $caminhoPasta = storage_path('app/documentos');

        // 1. Busca contexto nos documentos locais
        $contexto = $this->carregarContextoDePasta($caminhoPasta, $pergunta);
        $usouDocumentos = !empty($contexto);

        // 2. Chama a API do Ollama enviando o prompt atualizado da Vale
        $resposta = $this->perguntarOllama($pergunta, $contexto);

        // 3. Salva no banco vinculando ao usuário autenticado
        $chat = Chat::create([
            'user_id' => auth()->id(),
            'pergunta' => $pergunta,
            'resposta' => $resposta,
            'fonte' => $usouDocumentos ? 'documentos' : 'conhecimento_geral'
        ]);

        return response()->json([
            'status' => 'sucesso',
            'pergunta' => $chat->pergunta,
            'resposta' => $chat->resposta,
            'fonte' => $chat->fonte,
            'data' => $chat->created_at->format('d/m/Y H:i')
        ]);
    }

   private function carregarContextoDePasta(string $caminhoPasta, string $query): string
{
    if (!is_dir($caminhoPasta)) {
        return "";
    }

    $arquivos = glob($caminhoPasta . "/*.{txt,md,json,pdf}", GLOB_BRACE);
    if (empty($arquivos)) {
        return "";
    }

    $contextoEncontrado = "";
    $palavrasChave = array_filter(explode(" ", strtolower($query)), fn($p) => strlen(trim($p)) > 2);

    // Instancia o parser de PDF com tratamento de exceção
    $pdfParser = class_exists(\Smalot\PdfParser\Parser::class) ? new \Smalot\PdfParser\Parser() : null;

    foreach ($arquivos as $arquivo) {
        $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
        $conteudo = "";

        try {
            if ($extensao === 'pdf') {
                if ($pdfParser) {
                    $pdf = $pdfParser->parseFile($arquivo);
                    $conteudo = $pdf->getText();
                }
            } else {
                $conteudo = file_get_contents($arquivo);
            }
        } catch (\Throwable $e) {
            // Ignora arquivos corrompidos ou PDFs que não puderem ser lidos
            continue;
        }

        if (empty($conteudo)) {
            continue;
        }

        $conteudoLower = strtolower($conteudo);

        if (empty($palavrasChave)) {
            $nomeArquivo = basename($arquivo);
            $contextoEncontrado .= "--- Documento: {$nomeArquivo} ---\n{$conteudo}\n\n";
            continue;
        }

        foreach ($palavrasChave as $palavra) {
            if (str_contains($conteudoLower, trim($palavra))) {
                $nomeArquivo = basename($arquivo);
                $contextoEncontrado .= "--- Documento: {$nomeArquivo} ---\n{$conteudo}\n\n";
                break;
            }
        }
    }

    return $contextoEncontrado;
}

    private function perguntarOllama(string $prompt, string $contexto): string
    {
        $adminDocsContext = $contexto;

        // Montagem do SYSTEM_PROMPT conforme especificado
        $systemPrompt = 'Você é a Vale, uma assistente de pesquisa especializada no Vale do Paraíba (SP/RJ/MG), priorize utilizar a lingua portugues do Brasil. IMPORTANTE: Responda SEMPRE em texto corrido, parágrafos limpos e diretos. NUNCA utilize marcações de Markdown, títulos com hashtags (###), negritos (**), listas com marcadores ou estruturas complexas. Entregue apenas a informação pura em texto normal.' .
            ($adminDocsContext ? "\n\nATENÇÃO DE PRIORIDADE MÁXIMA: Você DEVE priorizar e basear suas respostas primariamente nos documentos oficiais carregados pelo administrador listados abaixo. Se a resposta constar nesses arquivos, utilize-os como fonte principal, sempre de as fontes das informacoes utilizadas:\n" . $adminDocsContext : '');

        $systemPrompt .= "\n\nPERGUNTA DO USUÁRIO:\n" . $prompt;

        try {
            $response = Http::timeout(-1)->post('http://localhost:11434/api/generate', [
                'model' => 'gemma3:12b',
                'prompt' => $systemPrompt,
                'stream' => false
            ]);

            if ($response->successful()) {
                return $response->json('response') ?? 'Erro na resposta do modelo.';
            }

            return 'Erro ao comunicar com a API do Ollama.';
        } catch (\Exception $e) {
            return 'O servidor do Ollama parece estar offline ou inacessível.';
        }
    }
}
