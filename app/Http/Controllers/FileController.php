<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FileController extends Controller
{
    private string $caminhoPasta;

    public function __construct()
    {
        $this->caminhoPasta = storage_path('app/documentos');

        // Garante que o diretório exista
        if (!File::exists($this->caminhoPasta)) {
            File::makeDirectory($this->caminhoPasta, 0755, true);
        }
    }

    public function index()
    {
        $arquivosBrutos = File::files($this->caminhoPasta);
        $arquivos = [];

        foreach ($arquivosBrutos as $arquivo) {
            $arquivos[] = [
                'nome' => $arquivo->getFilename(),
                'tamanho' => round($arquivo->getSize() / 1024, 2) . ' KB',
                'data' => date('d/m/Y H:i', $arquivo->getMTime())
            ];
        }

        return view('admin.files', compact('arquivos'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'arquivo' => 'required|file|mimes:txt,md,json,pdf|max:10240'
        ], [
            'arquivo.required' => 'Selecione um arquivo.',
            'arquivo.mimes' => 'Apenas arquivos .txt, .md, .json e .pdf são permitidos.',
            'arquivo.max' => 'O tamanho máximo do arquivo é de 10MB.'
        ]);

        $file = $request->file('arquivo');
        $extensao = strtolower($file->getClientOriginalExtension());
        $nomeBase = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $nomeBase = $nomeBase !== '' ? $nomeBase : 'documento';
        $nomeArquivo = sprintf(
            '%s-%s.%s',
            $nomeBase,
            Str::lower(Str::random(8)),
            $extensao
        );

        $file->move($this->caminhoPasta, $nomeArquivo);

        return back()->with('sucesso', "Arquivo '{$nomeArquivo}' enviado com sucesso!");
    }

    public function deletar($nome)
    {
        $caminhoArquivo = $this->caminhoPasta . '/' . basename($nome);

        if (File::exists($caminhoArquivo)) {
            File::delete($caminhoArquivo);
            return back()->with('sucesso', "Arquivo '{$nome}' excluído com sucesso!");
        }

        return back()->withErrors(['erro' => 'Arquivo não encontrado.']);
    }
}
