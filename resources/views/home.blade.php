<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vale IA - Chat</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            width: 100%;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            background: #fff;
            color: #333;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            border-bottom: 1px solid #e5e5e5;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            height: 56px;
        }

        .logo {
            font-size: 18px;
            font-weight: 700;
            background: linear-gradient(135deg, #16302a 0%, #5c8a6c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn-header {
            padding: 8px 14px;
            border: 1px solid #d1d5db;
            background: white;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            color: #333;
            text-decoration: none;
        }

        .btn-header:hover {
            background: #f9f9f9;
            border-color: #ccc;
        }

        .btn-header-primary {
            background: linear-gradient(135deg, #16302a 0%, #0f1d18 100%);
            color: white;
            border: none;
        }

        .btn-header-primary:hover {
            opacity: 0.9;
        }

        /* Main Layout */
        .main-container {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        .sidebar {
            width: 260px;
            background: #fff;
            border-right: 1px solid #e5e5e5;
            display: flex;
            flex-direction: column;
            padding: 12px;
        }

        .sidebar-header {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }

        .btn-new-chat {
            flex: 1;
            padding: 10px 12px;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-new-chat:hover {
            background: #f9f9f9;
            border-color: #ccc;
        }

        .chat-history {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 12px;
        }

        .chat-history::-webkit-scrollbar {
            width: 6px;
        }

        .chat-history::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-history::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }

        .chat-history::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        .history-item {
            padding: 10px 12px;
            border-radius: 6px;
            font-size: 13px;
            color: #666;
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: all 0.2s;
            margin-bottom: 4px;
        }

        .history-item:hover {
            background: #f3f4f6;
            color: #333;
        }

        .sidebar-footer {
            border-top: 1px solid #e5e5e5;
            padding-top: 12px;
            font-size: 12px;
            color: #999;
            text-align: center;
        }

        /* Chat Area */
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #fff;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* Empty State */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            text-align: center;
            padding: 40px 20px;
        }

        .empty-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .empty-title {
            font-size: 28px;
            font-weight: 600;
            color: #0f1d18;
            margin-bottom: 8px;
        }

        .empty-description {
            font-size: 16px;
            color: #666;
            margin-bottom: 32px;
            max-width: 400px;
        }

        .quick-prompts {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .quick-prompt {
            padding: 14px 12px;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 13px;
            text-align: center;
            line-height: 1.4;
        }

        .quick-prompt:hover {
            background: #f9f9f9;
            border-color: #5c8a6c;
            color: #5c8a6c;
        }

        /* Message Styles */
        .message {
            display: flex;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
        }

        .message.user {
            justify-content: flex-end;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .message.assistant .message-avatar {
            background: linear-gradient(135deg, #16302a 0%, #5c8a6c 100%);
            color: white;
        }

        .message.user .message-avatar {
            background: #e5e7eb;
            color: #333;
        }

        .message-content {
            max-width: 600px;
            padding: 12px 16px;
            border-radius: 8px;
            line-height: 1.6;
            word-wrap: break-word;
        }

        .message.assistant .message-content {
            background: #f3f4f6;
            color: #333;
        }

        .message.user .message-content {
            background: linear-gradient(135deg, #16302a 0%, #0f1d18 100%);
            color: white;
        }

        /* Input Area */
        .input-area {
            border-top: 1px solid #e5e5e5;
            padding: 16px 20px;
            background: #fff;
        }

        .input-wrapper {
            max-width: 900px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            gap: 8px;
        }

        .input-field {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            resize: none;
            min-height: 44px;
            max-height: 200px;
            transition: all 0.2s;
        }

        .input-field:focus {
            outline: none;
            border-color: #5c8a6c;
            box-shadow: 0 0 0 3px rgba(92, 138, 108, 0.1);
        }

        .input-field::placeholder {
            color: #999;
        }

        .btn-send {
            padding: 12px 20px;
            background: linear-gradient(135deg, #16302a 0%, #0f1d18 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .btn-send:hover:not(:disabled) {
            opacity: 0.9;
            box-shadow: 0 4px 12px rgba(15, 29, 24, 0.2);
            transform: translateY(-1px);
        }

        .btn-send:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .header {
                flex-wrap: wrap;
            }

            .chat-messages {
                padding: 12px 16px;
                gap: 12px;
            }

            .message-content {
                max-width: 100%;
            }

            .input-area {
                padding: 12px 16px;
            }

            .quick-prompts {
                grid-template-columns: 1fr;
            }

            .empty-title {
                font-size: 24px;
            }

            .empty-description {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">Vale IA</div>
        <div class="header-actions">
            <a href="{{ route('login') }}" class="btn-header">Entrar</a>
            <a href="{{ route('register') }}" class="btn-header btn-header-primary">Cadastrar</a>
        </div>
    </div>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <button class="btn-new-chat" onclick="location.reload()">
                    + Nova conversa
                </button>
            </div>

            <div class="chat-history" id="chatHistory">
                <!-- Histórico será preenchido aqui -->
            </div>

            <div class="sidebar-footer">
                Vale IA v1.0
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-area">
            <!-- Messages -->
            <div class="chat-messages" id="chatMessages">
                <div class="empty-state">
                    <div class="empty-icon">🌱</div>
                    <div class="empty-title">O que você gostaria de saber?</div>
                    <div class="empty-description">
                        Faça perguntas sobre o Vale do Paraíba e receba respostas inteligentes
                    </div>

                    <div class="quick-prompts">
                        <div class="quick-prompt" onclick="sendQuickMessage('Quais são as principais cidades do Vale do Paraíba?')">
                            🏙️ Principais cidades
                        </div>
                        <div class="quick-prompt" onclick="sendQuickMessage('Qual é a economia do Vale?')">
                            💼 Economia
                        </div>
                        <div class="quick-prompt" onclick="sendQuickMessage('Quais são os pontos turísticos?')">
                            🎭 Turismo
                        </div>
                        <div class="quick-prompt" onclick="sendQuickMessage('Fale sobre a história do Vale')">
                            📚 História
                        </div>
                    </div>
                </div>
            </div>

            <!-- Input -->
            <div class="input-area">
                <div class="input-wrapper">
                    <textarea 
                        class="input-field" 
                        id="messageInput" 
                        placeholder="Escreva sua pergunta sobre o Vale do Paraíba..."
                        onkeypress="handleKeyPress(event)"
                    ></textarea>
                    <button class="btn-send" id="sendBtn" onclick="sendMessage()">Enviar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const input = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');
        const chatMessages = document.getElementById('chatMessages');
        const chatHistory = document.getElementById('chatHistory');

        const responses = {
            'cidades': 'O Vale do Paraíba é composto por importantes municípios como São José dos Campos, Taubaté, Guaratinguetá, Pindamonhangaba, Cruzeiro, Resende, Volta Redonda e Campos do Jordão. Cada uma dessas cidades desempenha papel significativo na região.',
            'economia': 'A economia do Vale do Paraíba é diversificada com destaque para: Tecnologia (especialmente em São José dos Campos), Educação, Turismo, Indústria Automóvel, Comércio e Serviços. É uma das regiões mais desenvolvidas do estado de São Paulo.',
            'turismo': 'Os principais atrativos turísticos incluem: Campos do Jordão (estância climática), as Serras da Mantiqueira, parques naturais, cânions, museus, patrimônio histórico colonial, além de diversos hotéis, pousadas e centros de lazer.',
            'história': 'O Vale do Paraíba tem uma história rica, sendo importante durante o Brasil Colônia, passando pelo ciclo do café no século XIX, e se consolidando como polo industrial no século XX. Possui diversos patrimônios históricos preservados.'
        };

        let messageCount = 0;

        function sendMessage() {
            const message = input.value.trim();
            if (!message) return;

            // Clear empty state on first message
            if (messageCount === 0) {
                chatMessages.innerHTML = '';
            }

            // Add user message
            addMessage(message, 'user');
            input.value = '';
            input.style.height = 'auto';
            sendBtn.disabled = true;

            // Simulate AI response
            setTimeout(() => {
                const response = getResponse(message);
                addMessage(response, 'assistant');
                sendBtn.disabled = false;
                input.focus();
            }, 600);

            // Add to history
            addToHistory(message);
        }

        function sendQuickMessage(message) {
            input.value = message;
            sendMessage();
        }

        function addMessage(text, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;
            
            const avatar = document.createElement('div');
            avatar.className = 'message-avatar';
            avatar.textContent = sender === 'user' ? '👤' : '🤖';

            const content = document.createElement('div');
            content.className = 'message-content';
            content.textContent = text;

            if (sender === 'user') {
                messageDiv.appendChild(content);
                messageDiv.appendChild(avatar);
            } else {
                messageDiv.appendChild(avatar);
                messageDiv.appendChild(content);
            }

            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            messageCount++;
        }

        function getResponse(question) {
            const lower = question.toLowerCase();
            for (const [key, value] of Object.entries(responses)) {
                if (lower.includes(key)) {
                    return value;
                }
            }
            return 'Ótima pergunta! Para respostas mais detalhadas sobre esse tema, crie uma conta Vale IA para acesso aos recursos completos com IA avançada.';
        }

        function addToHistory(message) {
            const item = document.createElement('div');
            item.className = 'history-item';
            item.textContent = message.substring(0, 30) + (message.length > 30 ? '...' : '');
            item.title = message;
            item.onclick = () => {
                chatMessages.innerHTML = '';
                sendQuickMessage(message);
            };
            chatHistory.insertBefore(item, chatHistory.firstChild);
        }

        function handleKeyPress(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage();
            }
        }

        // Auto-resize textarea
        input.addEventListener('input', () => {
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 200) + 'px';
        });

        // Focus on load
        input.focus();
    </script>
</body>
</html>
