<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Vale IA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 420px;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 40px;
            backdrop-filter: blur(10px);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #16302a 0%, #5c8a6c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .tagline {
            font-size: 13px;
            color: #666;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        h1 {
            font-size: 24px;
            font-weight: 600;
            color: #0f1d18;
            margin-bottom: 8px;
            margin-top: 24px;
        }

        .subtitle {
            font-size: 14px;
            color: #999;
            margin-bottom: 28px;
            line-height: 1.5;
        }

        .error-box {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #0f1d18;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s;
            background: #fafafa;
        }

        input:focus {
            outline: none;
            border-color: #5c8a6c;
            background: white;
            box-shadow: 0 0 0 3px rgba(92, 138, 108, 0.1);
        }

        input::placeholder {
            color: #bbb;
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #16302a 0%, #0f1d18 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 8px;
        }

        .btn-primary:hover {
            opacity: 0.9;
            box-shadow: 0 4px 12px rgba(15, 29, 24, 0.2);
            transform: translateY(-1px);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
            gap: 12px;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: #eee;
        }

        .divider-text {
            font-size: 12px;
            color: #999;
            font-weight: 500;
        }

        .btn-demo {
            width: 100%;
            padding: 12px;
            background: #f0f0f0;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-demo:hover {
            background: #e8e8e8;
            border-color: #ccc;
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #999;
        }

        .footer-text a {
            color: #5c8a6c;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .footer-text a:hover {
            color: #16302a;
        }

        @media (max-width: 480px) {
            .card {
                padding: 24px;
            }

            h1 {
                font-size: 20px;
            }

            .logo {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo-section">
                <div class="logo">Vale IA</div>
                <div class="tagline">Pesquisa com Inteligência Artificial</div>
            </div>

            <h1>Bem-vindo de volta</h1>
            <p class="subtitle">Faça login para acessar suas pesquisas e conversas.</p>

            @if($errors->any())
                <div class="error-box">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email"
                        name="email" 
                        placeholder="seu@email.com" 
                        value="{{ old('email') }}"
                        required 
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        placeholder="••••••••" 
                        required
                    >
                </div>

                <button type="submit" class="btn-primary">Entrar</button>
            </form>

            <div class="divider">
                <div class="divider-line"></div>
                <div class="divider-text">ou</div>
                <div class="divider-line"></div>
            </div>

            <form action="{{ route('demo.login') }}" method="POST">
                @csrf
                <button type="submit" class="btn-demo">Testar sem Login</button>
            </form>

            <p class="footer-text">
                Não tem conta? <a href="{{ route('register') }}">Cadastre-se</a>
            </p>
        </div>
    </div>
</body>
</html>
