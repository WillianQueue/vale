<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vale IA</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg: #f4f8f5;
            --surface: #fff;
            --surface-soft: #eaf2ec;
            --border: #d3e2da;
            --ink: #0f1d18;
            --muted: #61776b;
            --accent: #4a7658;
            --accent-hover: #3c6a54;
            --sidebar: #0f1d18;
            --user: #0f1d18;
        }

        body.dark {
            --bg: #0f1d18;
            --surface: #162821;
            --surface-soft: #1e352b;
            --border: #294637;
            --ink: #e4ede7;
            --muted: #a5bdb0;
            --sidebar: #09120e;
            --user: #244534;
        }

        html,
        body {
            width: 100%;
            height: 100%;
        }

        body {
            overflow: hidden;
            background: var(--bg);
            color: var(--ink);
            font-family: Arial, sans-serif;
        }

        button,
        textarea {
            font: inherit;
        }

        button {
            border: 0;
            cursor: pointer;
        }

        .app,
        .main {
            width: 100%;
            height: 100vh;
        }

        .app {
            display: flex;
        }

        .sidebar {
            position: fixed;
            z-index: 20;
            inset: 0 auto 0 0;
            display: flex;
            flex-direction: column;
            width: 270px;
            padding: 16px 12px;
            background: var(--sidebar);
            transform: translateX(-100%);
            transition: transform .2s ease;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .backdrop {
            position: fixed;
            z-index: 15;
            inset: 0;
            display: none;
            background: rgba(0, 0, 0, .4);
        }

        .backdrop.open {
            display: block;
        }

        .sidebar-header,
        .topbar,
        .topbar-left,
        .topbar-right,
        .brand,
        .sidebar-footer {
            display: flex;
            align-items: center;
        }

        .sidebar-header,
        .topbar {
            justify-content: space-between;
        }

        .sidebar-header {
            padding-bottom: 20px;
        }

        .brand {
            gap: 8px;
            color: #e8e6e0;
            font-size: 15px;
            font-weight: 600;
        }

        .brand span {
            color: var(--accent);
        }

        .icon-button,
        .menu-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: transparent;
            color: #8aab99;
            font-size: 20px;
        }

        .icon-button:hover,
        .menu-button:hover {
            background: rgba(255, 255, 255, .08);
        }

        .new-chat {
            width: 100%;
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 8px;
            background: rgba(255, 255, 255, .06);
            color: #e8e6e0;
            text-align: left;
        }

        .new-chat:hover {
            background: rgba(255, 255, 255, .11);
        }

        .history {
            flex: 1;
            overflow-y: auto;
            margin-top: 22px;
        }

        .history-title {
            padding: 0 8px 8px;
            color: #8aab99;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .history-item {
            display: flex;
            align-items: center;
            gap: 4px;
            width: 100%;
            margin-bottom: 3px;
            border-radius: 7px;
            background: transparent;
        }

        .history-item:hover {
            background: rgba(255, 255, 255, .08);
        }

        .history-question {
            flex: 1;
            overflow: hidden;
            padding: 9px 6px 9px 10px;
            border: 0;
            background: transparent;
            color: #b5b3ac;
            cursor: pointer;
            font-size: 13px;
            text-align: left;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .history-question:hover {
            color: #fff;
        }

        .delete-chat {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            margin-right: 4px;
            border-radius: 5px;
            background: transparent;
            color: #8aab99;
            font-size: 18px;
            opacity: 0;
        }

        .history-item:hover .delete-chat {
            opacity: 1;
        }

        .delete-chat:hover {
            background: rgba(200, 80, 60, .3);
            color: #ffb4a3;
        }

        .sidebar-footer {
            gap: 9px;
            padding-top: 14px;
            border-top: 1px solid rgba(255, 255, 255, .08);
        }

        .avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--accent);
            color: #fff;
            font-size: 11px;
            font-weight: bold;
        }

        .user-name {
            overflow: hidden;
            color: #e8e6e0;
            font-size: 13px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .main {
            display: flex;
            flex-direction: column;
            min-width: 0;
            background: var(--bg);
        }

        .topbar {
            min-height: 58px;
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
        }

        .topbar-left,
        .topbar-right {
            gap: 10px;
        }

        .menu-button {
            color: var(--muted);
        }

        .model-pill {
            padding: 6px 13px;
            border: 1px solid var(--border);
            border-radius: 20px;
            background: var(--surface);
            color: var(--muted);
            font-size: 12px;
        }

        .topbar-link {
            padding: 8px 12px;
            border-radius: 7px;
            background: transparent;
            color: var(--muted);
            font-size: 13px;
            text-decoration: none;
        }

        .topbar-link:hover {
            background: var(--surface);
            color: var(--ink);
        }

        .topbar form {
            display: inline;
        }

        .chat-scroll {
            display: flex;
            flex: 1;
            justify-content: center;
            overflow-y: auto;
            padding: 0 20px 24px;
        }

        .chat-inner {
            width: 100%;
            max-width: 700px;
        }

        .greeting {
            padding: 42px 4px 24px;
        }

        .greeting-label {
            margin-bottom: 10px;
            color: var(--accent);
            font-size: 12px;
            font-weight: bold;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .greeting h1 {
            margin-bottom: 10px;
            font-size: 25px;
            line-height: 1.3;
        }

        .greeting p {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.5;
        }

        .suggestions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 9px;
            margin-top: 25px;
        }

        .suggestion {
            padding: 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--surface);
            color: var(--ink);
            text-align: left;
        }

        .suggestion:hover {
            border-color: #9ec4ae;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .06);
        }

        .suggestion strong,
        .suggestion span {
            display: block;
        }

        .suggestion strong {
            margin-bottom: 4px;
            font-size: 13px;
        }

        .suggestion span {
            color: var(--muted);
            font-size: 12px;
        }

        .messages {
            display: flex;
            flex-direction: column;
        }

        .message {
            display: flex;
            gap: 12px;
            padding: 17px 0;
        }

        .message + .message {
            border-top: 1px solid var(--border);
        }

        .message.user {
            flex-direction: row-reverse;
        }

        .message-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 29px;
            height: 29px;
            flex-shrink: 0;
            border-radius: 8px;
            background: var(--ink);
            color: #fff;
        }

        .message.user .message-icon {
            background: var(--accent);
        }

        .message-content {
            max-width: 88%;
            color: var(--ink);
            font-size: 14px;
            line-height: 1.65;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .message.user .message-content {
            max-width: 78%;
            padding: 10px 14px;
            border-radius: 14px 14px 4px 14px;
            background: var(--user);
            color: #fff;
        }

        .typing {
            display: inline-flex;
            gap: 4px;
            padding: 5px 2px;
        }

        .typing span {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--muted);
            animation: blink 1.4s infinite ease-in-out both;
        }

        .typing span:nth-child(1) {
            animation-delay: -.32s;
        }

        .typing span:nth-child(2) {
            animation-delay: -.16s;
        }

        @keyframes blink {
            0%, 80%, 100% {
                opacity: .4;
                transform: scale(0);
            }

            40% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .error {
            display: none;
            width: calc(100% - 40px);
            max-width: 700px;
            margin: 0 auto 10px;
            padding: 10px;
            border-radius: 8px;
            background: #fff0ed;
            color: #8f2d20;
            font-size: 13px;
        }

        .error.visible {
            display: block;
        }

        .input-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 20px 8px;
        }

        .input-wrap {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            width: 100%;
            max-width: 700px;
            padding: 10px 10px 10px 16px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: var(--surface);
            box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
        }

        .input-wrap:focus-within {
            border-color: #9ec4ae;
        }

        textarea {
            flex: 1;
            min-height: 28px;
            max-height: 140px;
            resize: none;
            border: 0;
            outline: 0;
            background: transparent;
            color: var(--ink);
            font-size: 14px;
            line-height: 1.55;
        }

        textarea::placeholder {
            color: var(--muted);
        }

        .send-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            border-radius: 9px;
            background: var(--accent);
            color: #fff;
            font-size: 20px;
        }

        .send-button:hover:not(:disabled) {
            background: var(--accent-hover);
        }

        .send-button:disabled {
            cursor: not-allowed;
            background: var(--border);
        }

        .hint {
            padding: 8px;
            color: var(--muted);
            font-size: 11px;
            text-align: center;
        }

        .modal {
            position: fixed;
            z-index: 50;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, .4);
        }

        .modal.open {
            display: flex;
        }

        .modal-card {
            width: 90%;
            max-width: 380px;
            padding: 24px;
            border-radius: 16px;
            background: var(--surface);
            color: var(--ink);
        }

        .modal-card h2 {
            margin-bottom: 8px;
            font-size: 18px;
        }

        .modal-card p {
            color: var(--muted);
            font-size: 14px;
        }

        .modal-close {
            display: block;
            margin: 20px 0 0 auto;
            padding: 9px 16px;
            border-radius: 7px;
            background: var(--ink);
            color: var(--bg);
        }

        .theme-option {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-top: 18px;
            color: var(--ink);
            font-size: 14px;
        }

        @media (max-width: 640px) {
            .topbar {
                padding: 10px 14px;
            }

            .chat-scroll,
            .input-area {
                padding-right: 14px;
                padding-left: 14px;
            }

            .suggestions {
                grid-template-columns: 1fr;
            }

            .topbar-link {
                display: none;
            }

            .error {
                width: calc(100% - 28px);
            }

            .message-content,
            .message.user .message-content {
                max-width: 88%;
            }
        }
    </style>
</head>

<body>
    <div class="app">
        <div class="backdrop" id="backdrop"></div>

        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="brand">Va<span>le</span> IA</div>

                <button type="button" class="icon-button" id="closeSidebar">
                    ×
                </button>
            </div>

            <button type="button" class="new-chat" id="newChat">
                + Nova conversa
            </button>

            <div class="history">
                <div class="history-title">Conversas recentes</div>

                <div id="historyList">
                    @auth
                        @foreach ($historico ?? [] as $chat)
                            <div
                                class="history-item"
                                data-question="{{ $chat->pergunta }}"
                                data-delete-url="{{ route('chat.destroy', $chat) }}"
                            >
                                <button type="button" class="history-question">
                                    {{ \Illuminate\Support\Str::limit($chat->pergunta, 42) }}
                                </button>

                                <button
                                    type="button"
                                    class="delete-chat"
                                    title="Excluir conversa"
                                    aria-label="Excluir conversa"
                                >
                                    ×
                                </button>
                            </div>
                        @endforeach
                    @else
                        <div class="history-question">
                            Faça login para salvar o histórico.
                        </div>
                    @endauth
                </div>
            </div>

            <div class="sidebar-footer">
                @auth
                    <div class="avatar">
                        {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
                    </div>

                    <div class="user-name">
                        {{ Auth::user()->name }}
                    </div>
                @else
                    <div class="avatar">VI</div>
                    <div class="user-name">Visitante</div>
                @endauth
            </div>
        </aside>

        <main class="main">
            <header class="topbar">
                <div class="topbar-left">
                    <button type="button" class="menu-button" id="openSidebar">
                        ☰
                    </button>

                    <div class="model-pill">
                        Vale · assistente de pesquisa
                    </div>
                </div>

                <div class="topbar-right">
                    @auth
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="topbar-link">
                                Sair
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="topbar-link">Entrar</a>
                        <a href="{{ route('register') }}" class="topbar-link">Cadastrar</a>
                    @endauth

                    <button type="button" class="icon-button" id="openSettings">
                        ⚙
                    </button>
                </div>
            </header>

            <div class="chat-scroll" id="chatScroll">
                <div class="chat-inner">
                    <section class="greeting" id="greeting">
                        <div class="greeting-label">Vale IA</div>

                        <h1>
                            Em que questão sobre o Vale do Paraíba posso apoiar sua pesquisa?
                        </h1>

                        <p>
                            Assistente voltada a pesquisadores, estudantes e interessados na região.
                        </p>

                        <div class="suggestions">
                            <button
                                type="button"
                                class="suggestion"
                                data-question="Quais são as principais cidades do Vale do Paraíba?"
                            >
                                <strong>Principais cidades</strong>
                                <span>Conheça os municípios da região</span>
                            </button>

                            <button
                                type="button"
                                class="suggestion"
                                data-question="Qual é a economia do Vale do Paraíba?"
                            >
                                <strong>Economia</strong>
                                <span>Atividades econômicas da região</span>
                            </button>

                            <button
                                type="button"
                                class="suggestion"
                                data-question="Quais são os principais pontos turísticos do Vale do Paraíba?"
                            >
                                <strong>Turismo</strong>
                                <span>Locais e atrações turísticas</span>
                            </button>

                            <button
                                type="button"
                                class="suggestion"
                                data-question="Fale sobre a história do Vale do Paraíba."
                            >
                                <strong>História</strong>
                                <span>Conheça a formação histórica do Vale</span>
                            </button>
                        </div>
                    </section>

                    <section class="messages" id="messages"></section>
                </div>
            </div>

            <div class="error" id="error"></div>

            <div class="input-area">
                <div class="input-wrap">
                    <textarea
                        id="composer"
                        rows="1"
                        placeholder="Pergunte algo sobre o Vale do Paraíba..."
                    ></textarea>

                    <button
                        type="button"
                        class="send-button"
                        id="sendButton"
                        disabled
                    >
                        ↑
                    </button>
                </div>

                <div class="hint">
                    A Vale pode cometer erros. Confirme os dados antes de citar.
                </div>
            </div>
        </main>
    </div>

    <div class="modal" id="settingsModal">
        <div class="modal-card">
            <h2>Configurações</h2>
            <p>Altere o tema visual da aplicação.</p>

            <label class="theme-option">
                <input type="checkbox" id="darkMode">
                Modo escuro
            </label>

            <button type="button" class="modal-close" id="closeSettings">
                Concluído
            </button>
        </div>
    </div>

    <script>
        const composer = document.getElementById('composer');
        const sendButton = document.getElementById('sendButton');
        const messages = document.getElementById('messages');
        const greeting = document.getElementById('greeting');
        const chatScroll = document.getElementById('chatScroll');
        const historyList = document.getElementById('historyList');
        const error = document.getElementById('error');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('backdrop');
        const settingsModal = document.getElementById('settingsModal');
        const darkMode = document.getElementById('darkMode');
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content');

        function scrollBottom() {
            chatScroll.scrollTop = chatScroll.scrollHeight;
        }

        function showError(message) {
            error.textContent = message;
            error.classList.add('visible');
        }

        function hideError() {
            error.textContent = '';
            error.classList.remove('visible');
        }

        function addHistory(question) {
            const item = document.createElement('div');
            const questionButton = document.createElement('button');

            item.className = 'history-item';
            item.dataset.question = question;

            questionButton.type = 'button';
            questionButton.className = 'history-question';
            questionButton.textContent = question.length > 42
                ? `${question.substring(0, 42)}...`
                : question;

            item.appendChild(questionButton);
            historyList.prepend(item);
        }

        function addMessage(role, text) {
            greeting.style.display = 'none';

            const message = document.createElement('div');
            const icon = document.createElement('div');
            const content = document.createElement('div');

            message.className = `message ${role}`;
            icon.className = 'message-icon';
            content.className = 'message-content';

            icon.textContent = role === 'user' ? '●' : '✦';
            content.textContent = text;

            message.appendChild(icon);
            message.appendChild(content);
            messages.appendChild(message);

            scrollBottom();

            return content;
        }

        function addTyping() {
            greeting.style.display = 'none';

            const message = document.createElement('div');
            const icon = document.createElement('div');
            const content = document.createElement('div');

            message.className = 'message assistant';
            icon.className = 'message-icon';
            content.className = 'message-content';

            icon.textContent = '✦';
            content.innerHTML = `
                <span class="typing">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            `;

            message.appendChild(icon);
            message.appendChild(content);
            messages.appendChild(message);

            scrollBottom();

            return content;
        }

        async function sendMessage(question = null) {
            const pergunta = (question ?? composer.value).trim();

            if (!pergunta || sendButton.disabled) {
                return;
            }

            hideError();
            addMessage('user', pergunta);
            addHistory(pergunta);

            composer.value = '';
            composer.style.height = '28px';
            sendButton.disabled = true;

            const answer = addTyping();
            answer.textContent = '';

            try {
                const response = await fetch('{{ route('chat.enviar') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/x-ndjson',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        pergunta: pergunta
                    })
                });

                if (!response.ok) {
                    const errorData = await response
                        .json()
                        .catch(() => ({}));

                    throw new Error(
                        errorData.message ||
                        'Não foi possível enviar a pergunta.'
                    );
                }

                if (!response.body) {
                    throw new Error(
                        'O navegador não suporta resposta em streaming.'
                    );
                }

                const reader = response.body.getReader();
                const decoder = new TextDecoder('utf-8');
                let buffer = '';

                while (true) {
                    const { value, done } = await reader.read();

                    if (done) {
                        break;
                    }

                    buffer += decoder.decode(value, {
                        stream: true
                    });

                    const lines = buffer.split('\n');
                    buffer = lines.pop() || '';

                    for (const line of lines) {
                        if (!line.trim()) {
                            continue;
                        }

                        const data = JSON.parse(line);

                        if (data.text) {
                            answer.textContent += data.text;
                            scrollBottom();
                        }
                    }
                }

                buffer += decoder.decode();

                if (buffer.trim()) {
                    const data = JSON.parse(buffer);

                    if (data.text) {
                        answer.textContent += data.text;
                    }
                }

                if (!answer.textContent.trim()) {
                    throw new Error(
                        'O modelo não retornou uma resposta.'
                    );
                }
            } catch (exception) {
                answer.textContent =
                    'Não foi possível obter uma resposta da Vale IA.';

                showError(
                    exception.message ||
                    'Verifique se o Ollama está em execução.'
                );
            } finally {
                sendButton.disabled = composer.value.trim() === '';
                composer.focus();
            }
        }

        async function deleteChat(item) {
            if (!item || !item.dataset.deleteUrl) {
                return;
            }

            const question = item.dataset.question;

            if (!confirm(`Excluir esta conversa?\n\n${question}`)) {
                return;
            }

            const deleteButton = item.querySelector('.delete-chat');
            deleteButton.disabled = true;

            try {
                const response = await fetch(item.dataset.deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await response
                    .json()
                    .catch(() => ({}));

                if (!response.ok) {
                    throw new Error(
                        data.message ||
                        'Não foi possível excluir a conversa.'
                    );
                }

                item.remove();
            } catch (exception) {
                deleteButton.disabled = false;
                showError(
                    exception.message ||
                    'Erro ao excluir a conversa.'
                );
            }
        }

        function resetChat() {
            messages.innerHTML = '';
            greeting.style.display = '';
            composer.value = '';
            composer.style.height = '28px';
            hideError();
            composer.focus();
        }

        document
            .getElementById('openSidebar')
            .addEventListener('click', () => {
                sidebar.classList.add('open');
                backdrop.classList.add('open');
            });

        document
            .getElementById('closeSidebar')
            .addEventListener('click', () => {
                sidebar.classList.remove('open');
                backdrop.classList.remove('open');
            });

        backdrop.addEventListener('click', () => {
            sidebar.classList.remove('open');
            backdrop.classList.remove('open');
        });

        document
            .getElementById('newChat')
            .addEventListener('click', resetChat);

        document
            .querySelectorAll('.suggestion')
            .forEach((button) => {
                button.addEventListener('click', () => {
                    sendMessage(button.dataset.question);
                });
            });

        historyList.addEventListener('click', (event) => {
            const deleteButton = event.target.closest('.delete-chat');

            if (deleteButton) {
                event.stopPropagation();
                deleteChat(deleteButton.closest('.history-item'));
                return;
            }

            const questionButton = event.target.closest(
                '.history-question'
            );

            if (questionButton) {
                const item = questionButton.closest('.history-item');

                if (item && item.dataset.question) {
                    sendMessage(item.dataset.question);
                }
            }
        });

        sendButton.addEventListener('click', () => {
            sendMessage();
        });

        composer.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage();
            }
        });

        composer.addEventListener('input', () => {
            composer.style.height = 'auto';
            composer.style.height = `${Math.min(
                composer.scrollHeight,
                140
            )}px`;

            sendButton.disabled = composer.value.trim() === '';
        });

        document
            .getElementById('openSettings')
            .addEventListener('click', () => {
                settingsModal.classList.add('open');
            });

        document
            .getElementById('closeSettings')
            .addEventListener('click', () => {
                settingsModal.classList.remove('open');
            });

        settingsModal.addEventListener('click', (event) => {
            if (event.target === settingsModal) {
                settingsModal.classList.remove('open');
            }
        });

        darkMode.addEventListener('change', () => {
            document.body.classList.toggle('dark', darkMode.checked);

            localStorage.setItem(
                'vale_theme',
                darkMode.checked ? 'dark' : 'light'
            );
        });

        if (localStorage.getItem('vale_theme') === 'dark') {
            document.body.classList.add('dark');
            darkMode.checked = true;
        }
    </script>
</body>
</html>