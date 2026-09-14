<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vale IA Chat</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            width: 100%;
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #333;
            background: #fff;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 56px;
            padding: 12px 20px;
            border-bottom: 1px solid #e5e5e5;
        }

        .logo {
            color: #16302a;
            font-size: 18px;
            font-weight: 700;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-info {
            padding-right: 12px;
            border-right: 1px solid #e5e5e5;
            color: #666;
            font-size: 13px;
        }

        .btn-logout,
        .btn-new-chat,
        .btn-send {
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-logout {
            padding: 8px 14px;
            border: 1px solid #d1d5db;
            background: #fff;
            color: #333;
        }

        .btn-logout:hover,
        .btn-new-chat:hover {
            background: #f9f9f9;
        }

        .main-container {
            display: flex;
            flex: 1;
            min-height: 0;
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            width: 260px;
            padding: 12px;
            border-right: 1px solid #e5e5e5;
        }

        .btn-new-chat {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            background: #fff;
        }

        .chat-history {
            flex: 1;
            overflow-y: auto;
            margin-top: 16px;
        }

        .history-item {
            overflow: hidden;
            padding: 10px 12px;
            margin-bottom: 4px;
            border-radius: 6px;
            color: #666;
            cursor: pointer;
            font-size: 13px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .history-item:hover {
            background: #f3f4f6;
            color: #333;
        }

        .sidebar-footer {
            padding-top: 12px;
            border-top: 1px solid #e5e5e5;
            color: #999;
            font-size: 12px;
            text-align: center;
        }

        .chat-area {
            display: flex;
            flex: 1;
            flex-direction: column;
            min-width: 0;
        }

        .chat-messages {
            display: flex;
            flex: 1;
            flex-direction: column;
            gap: 16px;
            overflow-y: auto;
            padding: 20px;
        }

        .empty-state {
            display: flex;
            flex: 1;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            text-align: center;
        }

        .empty-icon {
            margin-bottom: 20px;
            font-size: 64px;
        }

        .empty-title {
            margin-bottom: 8px;
            color: #0f1d18;
            font-size: 28px;
        }

        .empty-description {
            max-width: 400px;
            color: #666;
            font-size: 16px;
        }

        .quick-prompts {
            display: grid;
            grid-template-columns: repeat(2, minmax(150px, 1fr));
            gap: 12px;
            max-width: 500px;
            margin-top: 28px;
        }

        .quick-prompt {
            padding: 14px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            font-size: 13px;
            text-align: center;
        }

        .quick-prompt:hover {
            border-color: #5c8a6c;
            color: #5c8a6c;
        }

        .message {
            display: flex;
            gap: 12px;
            max-width: 800px;
            animation: slideIn .3s ease-out;
        }

        .message.user {
            align-self: flex-end;
            flex-direction: row-reverse;
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
            display: flex;
            flex: 0 0 32px;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: #e5e7eb;
        }

        .message.assistant .message-avatar {
            background: linear-gradient(135deg, #16302a, #5c8a6c);
            color: #fff;
        }

        .message-content {
            max-width: 650px;
            padding: 12px 16px;
            border-radius: 8px;
            line-height: 1.6;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .message.user .message-content {
            background: linear-gradient(135deg, #16302a, #0f1d18);
            color: #fff;
        }

        .message.assistant .message-content {
            background: #f3f4f6;
            color: #333;
        }

        .input-area {
            padding: 16px 20px;
            border-top: 1px solid #e5e5e5;
        }

        .input-wrapper {
            display: flex;
            gap: 8px;
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
        }

        .input-field {
            flex: 1;
            min-height: 44px;
            max-height: 200px;
            padding: 12px 16px;
            resize: none;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            font-family: inherit;
            font-size: 14px;
        }

        .input-field:focus {
            border-color: #5c8a6c;
            box-shadow: 0 0 0 3px rgba(92, 138, 108, .1);
        }

        .btn-send {
            height: 44px;
            padding: 0 20px;
            border: 0;
            background: linear-gradient(135deg, #16302a, #0f1d18);
            color: #fff;
        }

        .btn-send:disabled {
            cursor: not-allowed;
            opacity: .5;
        }

        .error-message {
            padding: 8px 20px;
            color: #991b1b;
            font-size: 14px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .message {
                max-width: 100%;
            }

            .quick-prompts {
                grid-template-columns: 1fr;
            }

            .chat-messages {
                padding: 12px 16px;
            }

            .input-area {
                padding: 12px 16px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">Vale IA</div>

        <div class="header-actions">
            <div class="user-info">{{ Auth::user()->name }}</div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">Sair</button>
            </form>
        </div>
    </header>

    <main class="main-container">
        <aside class="sidebar">
            <button type="button" class="btn-new-chat" id="newChatBtn">
                + Nova conversa
            </button>

            <div class="chat-history" id="chatHistory">
                @foreach ($historico ?? [] as $chat)
                    <div
                        class="history-item"
                        title="{{ $chat->pergunta }}"
                        data-question="{{ $chat->pergunta }}"
                    >
                        {{ \Illuminate\Support\Str::limit($chat->pergunta, 40) }}
                    </div>
                @endforeach
            </div>

            <div class="sidebar-footer">
                Vale IA v1.0
            </div>
        </aside>

        <section class="chat-area">
            <div class="chat-messages" id="chatMessages">
                <div class="empty-state" id="emptyState">
                    <div class="empty-icon">🌱</div>

                    <h1 class="empty-title">
                        O que você gostaria de saber?
                    </h1>

                    <p class="empty-description">
                        Faça perguntas sobre o Vale do Paraíba e receba respostas inteligentes.
                    </p>

                    <div class="quick-prompts">
                        <button
                            type="button"
                            class="quick-prompt"
                            data-question="Quais são as principais cidades do Vale do Paraíba?"
                        >
                            🏙️ Principais cidades
                        </button>

                        <button
                            type="button"
                            class="quick-prompt"
                            data-question="Qual é a economia do Vale do Paraíba?"
                        >
                            💼 Economia
                        </button>

                        <button
                            type="button"
                            class="quick-prompt"
                            data-question="Quais são os pontos turísticos do Vale do Paraíba?"
                        >
                            🎭 Turismo
                        </button>

                        <button
                            type="button"
                            class="quick-prompt"
                            data-question="Fale sobre a história do Vale do Paraíba."
                        >
                            📚 História
                        </button>
                    </div>
                </div>
            </div>

            <div id="errorMessage" class="error-message" hidden></div>

            <div class="input-area">
                <div class="input-wrapper">
                    <textarea
                        id="messageInput"
                        class="input-field"
                        placeholder="Escreva sua pergunta sobre o Vale do Paraíba..."
                        rows="1"
                    ></textarea>

                    <button type="button" id="sendBtn" class="btn-send">
                        Enviar
                    </button>
                </div>
            </div>
        </section>
    </main>

    <script>
        const input = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');
        const chatMessages = document.getElementById('chatMessages');
        const chatHistory = document.getElementById('chatHistory');
        const errorMessage = document.getElementById('errorMessage');
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content');

        function clearEmptyState() {
            const emptyState = document.getElementById('emptyState');

            if (emptyState) {
                emptyState.remove();
            }
        }

        function addMessage(text, sender) {
            clearEmptyState();

            const message = document.createElement('div');
            message.className = `message ${sender}`;

            const avatar = document.createElement('div');
            avatar.className = 'message-avatar';
            avatar.textContent = sender === 'user' ? '👤' : '🤖';

            const content = document.createElement('div');
            content.className = 'message-content';
            content.textContent = text;

            message.appendChild(avatar);
            message.appendChild(content);
            chatMessages.appendChild(message);

            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function addToHistory(question) {
            const item = document.createElement('div');

            item.className = 'history-item';
            item.title = question;
            item.dataset.question = question;
            item.textContent = question.length > 40
                ? `${question.substring(0, 40)}...`
                : question;

            chatHistory.prepend(item);
        }

        function showError(message) {
            errorMessage.textContent = message;
            errorMessage.hidden = false;
        }

        function hideError() {
            errorMessage.textContent = '';
            errorMessage.hidden = true;
        }

        async function sendMessage(question = null) {
            const pergunta = (question ?? input.value).trim();

            if (!pergunta || sendBtn.disabled) {
                return;
            }

            hideError();
            addMessage(pergunta, 'user');
            addToHistory(pergunta);

            input.value = '';
            input.style.height = '44px';
            sendBtn.disabled = true;
            sendBtn.textContent = 'Aguarde...';

            try {
                const response = await fetch('{{ route('chat.enviar') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        pergunta: pergunta
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(
                        data.message || 'Não foi possível enviar a pergunta.'
                    );
                }

                if (!data.resposta) {
                    throw new Error('O servidor retornou uma resposta vazia.');
                }

                addMessage(data.resposta, 'assistant');
            } catch (error) {
                console.error(error);
                showError(
                    error.message ||
                    'Não foi possível obter uma resposta da Vale IA.'
                );
            } finally {
                sendBtn.disabled = false;
                sendBtn.textContent = 'Enviar';
                input.focus();
            }
        }

        function resetChat() {
            chatMessages.innerHTML = `
                <div class="empty-state" id="emptyState">
                    <div class="empty-icon">🌱</div>
                    <h1 class="empty-title">O que você gostaria de saber?</h1>
                    <p class="empty-description">
                        Faça perguntas sobre o Vale do Paraíba e receba respostas inteligentes.
                    </p>
                </div>
            `;

            hideError();
            input.value = '';
            input.style.height = '44px';
            input.focus();
        }

        sendBtn.addEventListener('click', () => sendMessage());

        input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage();
            }
        });

        input.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = `${Math.min(this.scrollHeight, 200)}px`;
        });

        document
            .getElementById('newChatBtn')
            .addEventListener('click', resetChat);

        document
            .querySelectorAll('.quick-prompt')
            .forEach((button) => {
                button.addEventListener('click', () => {
                    sendMessage(button.dataset.question);
                });
            });

        chatHistory.addEventListener('click', function (event) {
            const item = event.target.closest('.history-item');

            if (item) {
                sendMessage(item.dataset.question);
            }
        });

        input.focus();
    </script>
</body>
</html>