<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin - Gerenciar Arquivos da IA</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: #f4f6f8; margin: 0; padding: 20px; color: #333; }
        .header { display: flex; justify-content: space-between; align-items: center; max-width: 900px; margin: 0 auto 20px auto; }
        .container { max-width: 900px; margin: 0 auto; background: white; border-radius: 8px; padding: 24px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
        .nav-btn { text-decoration: none; background: #64748b; color: white; padding: 8px 14px; border-radius: 6px; font-weight: bold; }
        .card-upload { background: #f8fafc; border: 2px dashed #cbd5e1; padding: 20px; border-radius: 8px; margin-bottom: 24px; }
        input[type="file"] { margin-bottom: 12px; }
        button.btn-submit { background: #16a34a; color: white; border: none; padding: 10px 18px; border-radius: 6px; font-weight: bold; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #e2e8f0; }
        th { background: #f1f5f9; color: #475569; }
        .alert-success { background: #dcfce7; color: #166534; padding: 12px; border-radius: 6px; margin-bottom: 16px; }
        .alert-error { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 16px; }
        .btn-delete { background: #dc2626; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 0.85em; }
    </style>
</head>
<body>

<div class="header">
    <h2>📁 Gerenciador de Arquivos (Base do Vale)</h2>
    <a href="{{ route('chat') }}" class="nav-btn">← Voltar ao Chat</a>
</div>

<div class="container">

    @if(session('sucesso'))
        <div class="alert-success">{{ session('sucesso') }}</div>
    @endif

    @if($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <!-- Formulário de Upload -->
   <!-- Formulário de Upload em resources/views/admin/files.blade.php -->
<div class="card-upload">
    <h3>Subir Novo Documento (.txt, .md, .json, .pdf)</h3>
    <form action="{{ route('admin.files.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="arquivo" accept=".txt,.md,.json,.pdf" required>
        <br>
        <button type="submit" class="btn-submit">Enviar Arquivo</button>
    </form>
</div>

    <!-- Lista de Arquivos -->
    <h3>Documentos Atuais na Pasta</h3>
    <table>
        <thead>
            <tr>
                <th>Nome do Arquivo</th>
                <th>Tamanho</th>
                <th>Última Modificação</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($arquivos as $arq)
                <tr>
                    <td><strong>{{ $arq['nome'] }}</strong></td>
                    <td>{{ $arq['tamanho'] }}</td>
                    <td>{{ $arq['data'] }}</td>
                    <td>
                        <form action="{{ route('admin.files.deletar', $arq['nome']) }}" method="POST" onsubmit="return confirm('Deseja excluir este arquivo?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #888;">Nenhum arquivo encontrado na pasta de documentos.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

</body>
</html>
