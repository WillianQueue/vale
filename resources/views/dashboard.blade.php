<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Vale IA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        .navbar {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .logo {
            font-size: 20px;
            font-weight: 700;
            background: linear-gradient(135deg, #16302a 0%, #5c8a6c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-name {
            font-size: 14px;
            font-weight: 500;
            color: #666;
        }

        .btn-logout {
            padding: 8px 16px;
            background: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            color: #333;
            display: inline-block;
        }

        .btn-logout:hover {
            background: #e8e8e8;
            border-color: #ccc;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 24px;
        }

        .welcome-section {
            background: white;
            border-radius: 12px;
            padding: 40px;
            margin-bottom: 32px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        h1 {
            font-size: 32px;
            font-weight: 700;
            color: #0f1d18;
            margin-bottom: 12px;
        }

        .subtitle {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .action-card {
            background: linear-gradient(135deg, #16302a 0%, #0f1d18 100%);
            color: white;
            padding: 24px;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 100px;
        }

        .action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(15, 29, 24, 0.2);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 32px;
        }

        .info-card {
            background: white;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #5c8a6c;
        }

        .info-card h3 {
            font-size: 14px;
            font-weight: 600;
            color: #5c8a6c;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-card p {
            font-size: 24px;
            font-weight: 700;
            color: #0f1d18;
        }

        .chat-section {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .chat-section h2 {
            font-size: 24px;
            font-weight: 600;
            color: #0f1d18;
            margin-bottom: 24px;
        }

        .chat-input-group {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }

        .chat-input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s;
        }

        .chat-input:focus {
            outline: none;
            border-color: #5c8a6c;
            box-shadow: 0 0 0 3px rgba(92, 138, 108, 0.1);
        }

        .btn-send {
            padding: 12px 24px;
            background: linear-gradient(135deg, #16302a 0%, #0f1d18 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-send:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 12px;
            }

            .container {
                padding: 20px 16px;
            }

            h1 {
                font-size: 24px;
            }

            .welcome-section {
                padding: 24px;
            }

            .chat-input-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">Vale IA</div>
        <div class="user-menu">
            <span class="user-name">{{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn-logout">Sair</button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Bem-vindo, {{ Auth::user()->name }}! 👋</h1>
            <p class="subtitle">
                Você está logado no Vale IA. Explore pesquisas inteligentes sobre o Vale do Paraíba.
            </p>

            <div class="quick-actions">
                <button class="action-card">
                    🔍 Nova Pesquisa
                </button>
                <button class="action-card">
                    💬 Conversar
                </button>
                <button class="action-card">
                    📊 Histórico
                </button>
                <button class="action-card">
                    ⚙️ Configurações
                </button>
            </div>
        </div>

        <!-- Stats -->
        <div class="info-grid">
            <div class="info-card">
                <h3>Pesquisas</h3>
                <p>0</p>
            </div>
            <div class="info-card">
                <h3>Conversas</h3>
                <p>0</p>
            </div>
            <div class="info-card">
                <h3>Última Atividade</h3>
                <p>Agora</p>
            </div>
        </div>

        <!-- Chat Section -->
        <div class="chat-section" style="margin-top: 32px;">
            <h2>Inicie uma Conversa</h2>
            
            <div class="chat-input-group">
                <input 
                    type="text" 
                    class="chat-input" 
                    placeholder="Pergunte algo sobre o Vale do Paraíba..."
                >
                <button class="btn-send">Enviar</button>
            </div>

            <div class="empty-state">
                <div class="empty-state-icon">💭</div>
                <p>Nenhuma conversa iniciada. Comece digitando uma pergunta acima!</p>
            </div>
        </div>
    </div>
</body>
</html>
