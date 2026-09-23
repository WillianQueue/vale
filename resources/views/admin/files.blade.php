<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Armazenar arquivos - Vale IA</title>
    <style>
        :root {
            --bg: #f5f5f5;
            --surface: #fff;
            --surface-soft: #e8e8e8;
            --border: #ddd;
            --ink: #0f1d18;
            --muted: #666;
            --accent: #5c8a6c;
            --accent-hover: #16302a;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: Arial, sans-serif;
        }

        .app {
            min-height: 100vh;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 64px;
            padding: 14px 30px;
            border-bottom: 1px solid var(--border);
            background: var(--bg);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 7px;
            color: var(--ink);
            font-size: 17px;
            font-weight: 600;
        }

        .brand-label {
            color: var(--accent);
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar-link,
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 7px;
            color: var(--muted);
            font-size: 13px;
            text-decoration: none;
            transition: background .15s, color .15s;
        }

        .topbar-link:hover,
        .back-button:hover {
            background: var(--surface);
            color: var(--ink);
        }

        .content {
            width: min(920px, calc(100% - 40px));
            margin: 0 auto;
            padding: 54px 0 40px;
        }

        .page-heading {
            margin-bottom: 28px;
        }

        .eyebrow {
            margin-bottom: 8px;
            color: var(--accent);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0 0 8px;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: clamp(28px, 4vw, 38px);
            font-weight: 500;
            letter-spacing: -.02em;
        }

        .page-heading p {
            margin: 0;
            color: var(--muted);
            font-size: 15px;
        }

        .panel {
            padding: 24px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: var(--surface);
        }

        .upload-panel {
            margin-bottom: 20px;
        }

        .panel-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        h2 {
            margin: 0;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 21px;
            font-weight: 500;
        }

        .panel-description {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .upload-form {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            border: 1px dashed #b8cdbd;
            border-radius: 12px;
            background: #f7faf7;
        }

        input[type="file"] {
            min-width: 0;
            flex: 1;
            color: var(--muted);
            font-size: 13px;
        }

        input[type="file"]::file-selector-button {
            margin-right: 10px;
            padding: 8px 11px;
            border: 1px solid var(--border);
            border-radius: 7px;
            background: var(--surface);
            color: var(--ink);
            cursor: pointer;
        }

        .btn-submit,
        .btn-delete {
            border: 0;
            border-radius: 7px;
            cursor: pointer;
            font-family: Arial, sans-serif;
            font-size: 13px;
            transition: background .15s, color .15s;
        }

        .btn-submit {
            padding: 10px 15px;
            background: var(--accent);
            color: #fff;
            white-space: nowrap;
        }

        .btn-submit:hover {
            background: var(--accent-hover);
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th,
        td {
            padding: 14px 10px;
            border-bottom: 1px solid var(--border);
            text-align: left;
        }

        th {
            color: var(--muted);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .file-name {
            color: var(--ink);
            font-weight: 500;
        }

        .file-meta {
            color: var(--muted);
        }

        .btn-delete {
            padding: 7px 10px;
            background: transparent;
            color: #a33d32;
        }

        .btn-delete:hover {
            background: #fff0ed;
        }

        .empty {
            padding: 30px 10px;
            color: var(--muted);
            text-align: center;
        }

        .alert {
            margin-bottom: 20px;
            padding: 12px 14px;
            border-radius: 9px;
            font-size: 14px;
        }

        .alert-success {
            background: #e8f1ea;
            color: #285a39;
        }

        .alert-error {
            background: #fff0ed;
            color: #8f2d20;
        }

        @media (max-width: 640px) {
            .topbar {
                padding: 12px 16px;
            }

            .content {
                width: min(100% - 28px, 920px);
                padding-top: 34px;
            }

            .panel {
                padding: 18px;
            }

            .upload-form {
                align-items: stretch;
                flex-direction: column;
            }

            .btn-submit {
                width: 100%;
            }

            th:nth-child(2),
            td:nth-child(2),
            th:nth-child(3),
            td:nth-child(3) {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="app">
        <header class="topbar">
            <div class="brand">
                <span>Vale</span>
                <span class="brand-label">IA</span>
            </div>

            <div class="topbar-actions">
                <a href="{{ route('admin.accounts') }}" class="topbar-link">Gerenciar contas</a>
                <a href="{{ route('chat') }}" class="back-button">← Voltar ao chat</a>
            </div>
        </header>

        <main class="content">
            <div class="page-heading">
                <div class="eyebrow">Área administrativa</div>
                <h1>Armazenar arquivos</h1>
                <p>Adicione documentos para servir como base de conhecimento da Vale IA.</p>
            </div>

            @if(session('sucesso'))
                <div class="alert alert-success">{{ session('sucesso') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            <section class="panel upload-panel">
                <div class="panel-heading">
                    <div>
                        <h2>Novo documento</h2>
                        <p class="panel-description">Formatos aceitos: .txt, .md, .json e .pdf · até 10 MB</p>
                    </div>
                </div>

                <form class="upload-form" action="{{ route('admin.files.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="arquivo" accept=".txt,.md,.json,.pdf" required>
                    <button type="submit" class="btn-submit">Enviar arquivo</button>
                </form>
            </section>

            <section class="panel">
                <div class="panel-heading">
                    <div>
                        <h2>Documentos atuais</h2>
                        <p class="panel-description">Arquivos disponíveis para consulta pela assistente.</p>
                    </div>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome do arquivo</th>
                                <th>Tamanho</th>
                                <th>Última modificação</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($arquivos as $arq)
                                <tr>
                                    <td class="file-name">{{ $arq['nome'] }}</td>
                                    <td class="file-meta">{{ $arq['tamanho'] }}</td>
                                    <td class="file-meta">{{ $arq['data'] }}</td>
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
                                    <td colspan="4" class="empty">Nenhum arquivo encontrado na pasta de documentos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
