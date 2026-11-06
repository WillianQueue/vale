<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar contas - Vale IA</title>
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
            padding: 14px 12px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            vertical-align: middle;
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

        .account-name {
            color: var(--ink);
            font-weight: 500;
        }

        .account-email,
        .normal,
        .current {
            color: var(--muted);
        }

        .status {
            color: var(--accent);
            font-weight: 600;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            border: 1px solid transparent;
            border-radius: 8px;
            padding: 7px 12px;
            cursor: pointer;
            font-family: Arial, sans-serif;
            font-size: 12px;
            font-weight: 600;
            line-height: 1;
            white-space: nowrap;
            transition: background .15s, border-color .15s, color .15s;
        }

        .promote {
            border-color: var(--accent);
            background: var(--accent);
            color: #fff;
        }

        .promote:hover {
            background: var(--accent-hover);
        }

        .remove {
            border-color: #e4c5c0;
            background: #fff8f6;
            color: #8f2d20;
        }

        .remove:hover {
            background: #fff0ed;
        }

        .delete {
            border-color: #ead8d4;
            background: transparent;
            color: #a33d32;
        }

        .delete:hover {
            background: #fff0ed;
        }

        .edit-button {
            border-color: var(--accent);
            background: var(--accent);
            color: #fff;
            text-decoration: none;
        }

        .edit-button:hover {
            border-color: var(--accent-hover);
            background: var(--accent-hover);
        }

        .pagination {
            display: flex;
            justify-content: flex-end;
            gap: 6px;
            margin-top: 20px;
        }

        .pagination a,
        .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 9px;
            border: 1px solid var(--border);
            border-radius: 7px;
            color: var(--muted);
            font-size: 13px;
            text-decoration: none;
        }

        .pagination a:hover,
        .pagination .active span {
            border-color: var(--accent);
            background: var(--accent);
            color: #fff;
        }

        .alert {
            margin-bottom: 20px;
            padding: 12px 14px;
            border-radius: 9px;
            font-size: 14px;
        }

        .success {
            background: #e8f1ea;
            color: #285a39;
        }

        .error {
            background: #fff0ed;
            color: #8f2d20;
        }

        @media (max-width: 640px) {
            .topbar {
                padding: 12px 16px;
            }

            .topbar-link {
                display: none;
            }

            .content {
                width: min(100% - 28px, 920px);
                padding-top: 34px;
            }

            .panel {
                padding: 18px;
            }

            th:nth-child(2),
            td:nth-child(2) {
                display: none;
            }

            .account-actions {
                align-items: stretch;
                flex-direction: column;
                gap: 5px;
            }

            .account-actions .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="brand">
            <span>Vale</span>
            <span class="brand-label">IA</span>
        </div>

        <div class="topbar-actions">
            <a href="{{ route('admin.files') }}" class="topbar-link">Armazenar arquivos</a>
            <a href="{{ route('chat') }}" class="back-button">Voltar ao chat</a>
        </div>
    </header>

    <main class="content">
        <div class="page-heading">
            <div class="eyebrow">Área administrativa</div>
            <h1>Gerenciar contas</h1>
            <p>Controle quais contas possuem acesso às funções administrativas da Vale IA.</p>
        </div>

        @if(session('sucesso'))
            <div class="alert success">{{ session('sucesso') }}</div>
        @endif

        @if($errors->any())
            <div class="alert error">{{ $errors->first() }}</div>
        @endif

        <section class="panel">
            <div class="panel-heading">
                <div>
                    <h2>Contas cadastradas</h2>
                    <p class="panel-description">Promova usuários ou remova o acesso administrativo.</p>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Permissão</th>
                            <th>Editar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $usuario)
                            <tr>
                                <td class="account-name">{{ $usuario->name }}</td>
                                <td class="account-email">{{ $usuario->email }}</td>
                                <td class="{{ $usuario->is_admin ? 'status' : 'normal' }}">
                                    {{ $usuario->is_admin ? 'Administrador' : 'Usuário' }}
                                </td>
                                <td>
                                    <a class="btn edit-button" href="{{ route('admin.accounts.edit', $usuario) }}">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $usuarios->links('pagination::simple-default') }}
        </section>
    </main>
</body>
</html>
