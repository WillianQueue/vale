<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Vale IA</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
            color: #333;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif;
        }

        .container {
            width: 100%;
            max-width: 420px;
        }

        .card {
            padding: 40px;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .logo-section {
            margin-bottom: 32px;
            text-align: center;
        }

        .logo {
            margin-bottom: 8px;
            background: linear-gradient(135deg, #16302a 0%, #5c8a6c 100%);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            font-size: 28px;
            font-weight: 700;
            -webkit-text-fill-color: transparent;
        }

        .tagline {
            color: #666;
            font-size: 13px;
            font-weight: 500;
            letter-spacing: .5px;
        }

        h1 {
            margin-top: 24px;
            margin-bottom: 8px;
            color: #0f1d18;
            font-size: 24px;
            font-weight: 600;
        }

        .subtitle {
            margin-bottom: 28px;
            color: #999;
            font-size: 14px;
            line-height: 1.5;
        }

        .error-box {
            margin-bottom: 20px;
            padding: 12px 14px;
            border: 1px solid #fcc;
            border-radius: 8px;
            background: #fee;
            color: #c33;
            font-size: 13px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #0f1d18;
            font-size: 13px;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            background: #fafafa;
            font-family: inherit;
            font-size: 14px;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }

        input:focus {
            border-color: #5c8a6c;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(92, 138, 108, .1);
        }

        input::placeholder {
            color: #bbb;
        }

        .btn-primary {
            width: 100%;
            margin-top: 8px;
            padding: 12px;
            border: 0;
            border-radius: 8px;
            background: linear-gradient(135deg, #16302a 0%, #0f1d18 100%);
            color: #fff;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: opacity .2s, box-shadow .2s, transform .2s;
        }

        .btn-primary:hover {
            opacity: .9;
            box-shadow: 0 4px 12px rgba(15, 29, 24, .2);
            transform: translateY(-1px);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .footer-text {
            margin-top: 24px;
            color: #999;
            font-size: 13px;
            text-align: center;
        }

        .footer-text a {
            color: #5c8a6c;
            font-weight: 600;
            text-decoration: none;
            transition: color .2s;
        }

        .footer-text a:hover {
            color: #16302a;
        }

        .back-link {
            display: block;
            margin-top: 16px;
            color: #777;
            font-size: 13px;
            text-align: center;
            text-decoration: none;
        }

        .back-link:hover {
            color: #16302a;
        }

        @media (max-width: 480px) {
            .card {
                padding: 24px;
            }

            .logo {
                font-size: 24px;
            }

            h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <main class="container">
        <section class="card">
            <div class="logo-section">
                <div class="logo">Vale IA</div>
                <div class="tagline">Pesquisa com Inteligência Artificial</div>
            </div>

            <h1>Bem-vindo de volta</h1>

            <p class="subtitle">
                Faça login para acessar suas pesquisas e conversas salvas.
            </p>

            @if ($errors->any())
                <div class="error-box">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('status'))
                <div class="error-box">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="seu@email.com"
                        autocomplete="email"
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
                        placeholder="Digite sua senha"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="btn-primary">
                    Entrar
                </button>
            </form>

            <p class="footer-text">
                Não tem uma conta?
                <a href="{{ route('register') }}">Cadastre-se</a>
            </p>

            <a href="{{ route('home') }}" class="back-link">
                Voltar para o chat
            </a>
        </section>
    </main>
</body>
</html>