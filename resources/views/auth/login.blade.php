<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Assistente IA</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f4f6f8; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 320px; }
        input { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #2563eb; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
        .error { color: red; font-size: 0.85em; }
    </style>
</head>
<body>
<div class="card">
    <h2>Entrar</h2>
    @if($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <input type="email" name="email" placeholder="E-mail" required>
        <input type="password" name="password" placeholder="Senha" required>
        <button type="submit">Entrar</button>
    </form>
    <p><small>Não tem conta? <a href="{{ route('register') }}">Cadastre-se</a></small></p>
</div>
</body>
</html>
