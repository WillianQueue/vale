<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar contas - Vale IA</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; color: #0f1d18; }
        .header, .container { max-width: 900px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .header h1 { font-size: 22px; margin: 0; }
        .nav-btn { text-decoration: none; background: #16302a; color: #fff; padding: 9px 14px; border-radius: 7px; }
        .container { background: #fff; border: 1px solid #ddd; border-radius: 12px; padding: 24px; }
        .alert { padding: 12px; border-radius: 7px; margin-bottom: 16px; }
        .success { background: #dcfce7; color: #166534; }
        .error { background: #fee2e2; color: #991b1b; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 13px 10px; text-align: left; border-bottom: 1px solid #e8e8e8; }
        th { color: #666; font-size: 12px; text-transform: uppercase; }
        .status { color: #5c8a6c; font-weight: bold; }
        .normal { color: #666; }
        .btn { border: 0; border-radius: 6px; padding: 8px 11px; color: #fff; cursor: pointer; }
        .promote { background: #5c8a6c; }
        .remove { background: #a33d32; }
        .current { color: #666; font-size: 13px; }
    </style>
</head>
<body>
    <header class="header">
        <h1>Administrar contas</h1>
        <a href="{{ route('chat') }}" class="nav-btn">Voltar ao chat</a>
    </header>

    <main class="container">
        @if(session('sucesso'))
            <div class="alert success">{{ session('sucesso') }}</div>
        @endif

        @if($errors->any())
            <div class="alert error">{{ $errors->first() }}</div>
        @endif

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
                        <td>{{ $usuario->name }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td class="{{ $usuario->is_admin ? 'status' : 'normal' }}">
                            {{ $usuario->is_admin ? 'Administrador' : 'Usuário' }}
                        </td>
                        <td>
                            @if(auth()->id() === $usuario->id)
                                <span class="current">Conta atual</span>
                            @else
                                <form method="POST" action="{{ route('admin.accounts.toggle-admin', $usuario) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn {{ $usuario->is_admin ? 'remove' : 'promote' }}" type="submit">
                                        {{ $usuario->is_admin ? 'Remover admin' : 'Tornar admin' }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $usuarios->links() }}
    </main>
</body>
</html>
