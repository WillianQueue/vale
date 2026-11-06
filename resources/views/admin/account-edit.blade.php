<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar conta - Vale IA</title>
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
            --danger: #a33d32;
            --danger-hover: #8f2d20;
        }

        * {
            box-sizing: border-box;
        }

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
        }

        .brand {
            color: var(--ink);
            font-size: 17px;
            font-weight: 600;
        }

        .brand span {
            color: var(--accent);
        }

        .back-link {
            padding: 9px 12px;
            border-radius: 7px;
            color: var(--muted);
            font-size: 13px;
            text-decoration: none;
        }

        .back-link:hover {
            background: var(--surface);
            color: var(--ink);
        }

        main {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(280px, .7fr);
            align-content: center;
            gap: 28px;
            width: min(1120px, calc(100% - 48px));
            min-height: calc(100vh - 64px);
            margin: 0 auto;
            padding: 48px 0;
        }

        .intro {
            grid-column: 1 / -1;
            margin-bottom: 4px;
        }

        .eyebrow {
            margin: 0 0 8px;
            color: var(--accent);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        h1,
        h2 {
            margin: 0;
            font-family: Georgia, 'Times New Roman', serif;
            font-weight: 500;
        }

        h1 {
            font-size: clamp(30px, 4vw, 42px);
        }

        h2 {
            font-size: 22px;
        }

        .subtitle,
        .description {
            color: var(--muted);
            line-height: 1.6;
        }

        .subtitle {
            margin: 8px 0 0;
        }

        .card {
            padding: clamp(22px, 4vw, 36px);
            border: 1px solid var(--border);
            border-radius: 16px;
            background: var(--surface);
        }

        .description {
            margin: 8px 0 24px;
            font-size: 14px;
        }

        .field {
            display: grid;
            gap: 7px;
            margin-bottom: 18px;
        }

        label {
            color: var(--muted);
            font-size: 13px;
            font-weight: 600;
        }

        input {
            width: 100%;
            min-height: 44px;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--surface);
            color: var(--ink);
            font: 15px Arial, sans-serif;
        }

        input:focus {
            outline: 2px solid rgba(92, 138, 108, .22);
            border-color: var(--accent);
        }

        .hint {
            margin: -10px 0 18px;
            color: var(--muted);
            font-size: 12px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 10px 16px;
            border: 1px solid transparent;
            border-radius: 8px;
            cursor: pointer;
            font: 600 13px Arial, sans-serif;
            text-decoration: none;
            transition: background .15s, border-color .15s, color .15s;
        }

        .primary {
            background: var(--accent);
            color: #fff;
        }

        .primary:hover {
            background: var(--accent-hover);
        }

        .secondary {
            border-color: var(--border);
            background: var(--surface);
            color: var(--ink);
        }

        .secondary:hover {
            background: var(--surface-soft);
        }

        .danger {
            width: 100%;
            border-color: var(--danger);
            background: var(--danger);
            color: #fff;
        }

        .danger:hover {
            border-color: var(--danger-hover);
            background: var(--danger-hover);
        }

        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 24px;
        }

        .permission {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 16px 0;
            border-top: 1px solid var(--border);
        }

        .permission strong {
            display: block;
            margin-bottom: 5px;
        }

        .permission span {
            color: var(--muted);
            font-size: 13px;
        }

        .permission form,
        .delete-form {
            margin: 0;
        }

        .alert {
            grid-column: 1 / -1;
            padding: 13px 15px;
            border-radius: 9px;
            background: #fff0ed;
            color: var(--danger-hover);
            font-size: 14px;
        }

        .alert ul {
            margin: 0;
            padding-left: 20px;
        }

        .status {
            display: inline-block;
            padding: 6px 9px;
            border-radius: 999px;
            background: #e8f1ea;
            color: #285a39;
            font-size: 12px;
            font-weight: 600;
        }

        .delete-section {
            margin-top: 22px;
            padding-top: 22px;
            border-top: 1px solid var(--border);
        }

        .delete-section h2 {
            font-size: 19px;
        }

        .delete-section .description {
            margin-bottom: 16px;
        }

        @media (max-width: 760px) {
            .topbar {
                padding: 12px 18px;
            }

            main {
                grid-template-columns: 1fr;
                width: min(100% - 32px, 560px);
                min-height: auto;
                padding: 34px 0;
            }

            .intro,
            .alert {
                grid-column: auto;
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="brand">Vale <span>IA</span></div>
        <a class="back-link" href="{{ route('admin.accounts') }}">← Voltar às contas</a>
    </header>

    <main>
        <div class="intro">
            <p class="eyebrow">Área administrativa</p>
            <h1>Editar conta</h1>
            <p class="subtitle">Atualize os dados e as permissões desta conta.</p>
        </div>

        @if($errors->any())
            <div class="alert" role="alert">
                <ul>
                    @foreach($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="card">
            <h2>Dados da conta</h2>
            <p class="description">Conta de {{ $usuario->name }}</p>

            <form method="POST" action="{{ route('admin.accounts.update', $usuario) }}">
                @csrf
                @method('PUT')

                <div class="field">
                    <label for="name">Nome</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $usuario->name) }}" required maxlength="255" autocomplete="name">
                </div>

                <div class="field">
                    <label for="email">E-mail</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $usuario->email) }}" required maxlength="255" autocomplete="email">
                </div>

                <div class="field">
                    <label for="password">Nova senha (opcional)</label>
                    <input id="password" name="password" type="password" minlength="8" autocomplete="new-password">
                </div>
                <p class="hint">Deixe em branco para manter a senha atual.</p>

                <div class="field">
                    <label for="password_confirmation">Confirmar nova senha</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password">
                </div>

                <div class="form-actions">
                    <button class="button primary" type="submit">Salvar alterações</button>
                    <a class="button secondary" href="{{ route('admin.accounts') }}">Cancelar</a>
                </div>
            </form>
        </section>

        <aside class="card">
            <h2>Permissões e ações</h2>
            <p class="description">Gerencie o acesso administrativo desta conta.</p>

            <div class="permission">
                <div>
                    <strong>Permissão atual</strong>
                    <span class="status">{{ $usuario->is_admin ? 'Administrador' : 'Usuário' }}</span>
                </div>
                @if(auth()->id() !== $usuario->id)
                    <form method="POST" action="{{ route('admin.accounts.toggle-admin', $usuario) }}">
                        @csrf
                        @method('PATCH')
                        <button class="button secondary" type="submit">
                            {{ $usuario->is_admin ? 'Remover adm' : 'Promover a adm' }}
                        </button>
                    </form>
                @endif
            </div>

            @if(auth()->id() !== $usuario->id)
                <div class="delete-section">
                    <h2>Excluir conta</h2>
                    <p class="description">Esta ação não pode ser desfeita.</p>
                    <form class="delete-form" method="POST" action="{{ route('admin.accounts.delete', $usuario) }}" onsubmit="return confirm('Tem certeza que deseja excluir esta conta? Esta ação não pode ser desfeita.');">
                        @csrf
                        @method('DELETE')
                        <button class="button danger" type="submit">Excluir conta</button>
                    </form>
                </div>
            @else
                <p class="description">Esta é a conta atualmente conectada. As permissões e a exclusão estão desabilitadas.</p>
            @endif
        </aside>
    </main>
</body>
</html>
