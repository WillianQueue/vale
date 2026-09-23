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

        .account-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .account-actions form {
            display: flex;
            margin: 0;
        }

        .account-menu {
            position: relative;
        }

        .account-menu summary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            padding: 7px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--surface);
            color: var(--ink);
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            list-style: none;
        }

        .account-menu summary::-webkit-details-marker {
            display: none;
        }

        .account-menu summary::after {
            margin-left: 7px;
            content: '▾';
            color: var(--muted);
            font-size: 11px;
        }

        .account-menu[open] summary {
            border-color: var(--accent);
        }

        .account-menu-panel {
            position: absolute;
            z-index: 5;
            top: calc(100% + 8px);
            right: 0;
            width: 270px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--surface);
            box-shadow: 0 12px 30px rgba(15, 29, 24, .14);
        }

        .account-menu-panel form {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin: 0;
        }

        .edit-account {
            border: 0;
        }

        .edit-account summary {
            display: flex;
            align-items: center;
            min-height: 34px;
            padding: 7px 12px;
            border-radius: 8px;
            color: var(--ink);
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            list-style: none;
            transition: background .15s, color .15s;
        }

        .edit-account summary::-webkit-details-marker {
            display: none;
        }

        .edit-account summary:hover {
            background: var(--surface-soft);
        }

        .edit-account-form {
            margin: 8px 0 4px;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px;
            background: var(--surface);
        }

        .account-menu-panel label {
            color: var(--muted);
            font-size: 11px;
            font-weight: 600;
        }

        .account-menu-panel input {
            width: 100%;
            padding: 8px 9px;
            border: 1px solid var(--border);
            border-radius: 7px;
            color: var(--ink);
            font: 13px Arial, sans-serif;
        }

        .account-menu-panel input:focus {
            outline: 2px solid rgba(92, 138, 108, .22);
            border-color: var(--accent);
        }

        .menu-separator {
            height: 1px;
            margin: 12px 0;
            background: var(--border);
        }

        .menu-action {
            width: 100%;
        }

        .menu-action.delete {
            justify-content: center;
            padding-right: 12px;
            padding-left: 12px;
        }

        .account-actions .delete {
            width: 100%;
            border-color: #a33d32;
            background: #a33d32;
            color: #fff;
        }

        .account-actions .delete:hover {
            border-color: #8f2d20;
            background: #8f2d20;
        }

        .account-menu-panel .delete {
            width: 100%;
            border-color: #a33d32;
            background: #a33d32;
            color: #fff;
            justify-content: center;
            text-align: center;
            transition: background .15s, border-color .15s;
        }

        .account-menu-panel .delete:hover {
            border-color: #8f2d20;
            background: #8f2d20;
            color: #fff;
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
            <a href="{{ route('chat') }}" class="back-button">← Voltar ao chat</a>
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
                            <th>Ação</th>
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
                                    <details class="account-menu">
                                        <summary>Ações</summary>
                                        <div class="account-menu-panel">
                                            <details class="edit-account">
                                                <summary>Editar</summary>
                                                <form class="edit-account-form" method="POST" action="{{ route('admin.accounts.update', $usuario) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <label for="name-{{ $usuario->id }}">Nome</label>
                                                    <input id="name-{{ $usuario->id }}" name="name" type="text" value="{{ $usuario->name }}" required maxlength="255">
                                                    <label for="email-{{ $usuario->id }}">E-mail</label>
                                                    <input id="email-{{ $usuario->id }}" name="email" type="email" value="{{ $usuario->email }}" required maxlength="255">
                                                    <label for="password-{{ $usuario->id }}">Nova senha (opcional)</label>
                                                    <input id="password-{{ $usuario->id }}" name="password" type="password" minlength="8" autocomplete="new-password">
                                                    <label for="password-confirmation-{{ $usuario->id }}">Confirmar nova senha</label>
                                                    <input id="password-confirmation-{{ $usuario->id }}" name="password_confirmation" type="password" minlength="8" autocomplete="new-password">
                                                    <button class="btn promote menu-action" type="submit">
                                                        Salvar alterações
                                                    </button>
                                                </form>
                                            </details>

                                            @if(auth()->id() !== $usuario->id)
                                                <form method="POST" action="{{ route('admin.accounts.toggle-admin', $usuario) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="btn {{ $usuario->is_admin ? 'remove' : 'promote' }} menu-action" type="submit">
                                                        {{ $usuario->is_admin ? 'Remover adm' : 'Promover a adm' }}
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('admin.accounts.delete', $usuario) }}" onsubmit="return confirm('Tem certeza que deseja excluir esta conta? Esta ação não pode ser desfeita.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn delete menu-action" type="submit">Excluir conta</button>
                                                </form>
                                            @else
                                                <span class="current">Esta é a conta atual</span>
                                            @endif
                                        </div>
                                    </details>
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
