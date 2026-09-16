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
            display: flex;
            align-items: center;
            gap: 8px;
            color: #e8e6e0;
            font-size: 15px;
            font-weight: 600;
        }

        .brand-name {
            color: #e8e6e0;
        }

        .brand-label {
            color: var(--accent);
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

        .admin-actions {
            display: flex;
            flex-direction: column;
            gap: 2px;
            margin-top: 16px;
            padding: 12px 0 0;
            border-top: 1px solid rgba(255, 255, 255, .08);
        }

        .admin-action {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border: 0;
            border-radius: 7px;
            background: transparent;
            color: #8aab99;
            font-family: Arial, sans-serif;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: background .12s, color .12s;
        }

        .admin-action:hover {
            background: rgba(255, 255, 255, .06);
            color: #e8e6e0;
        }

        .admin-action svg {
            width: 15px;
            height: 15px;
            flex-shrink: 0;
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

        .topbar .icon-button {
            width: 34px;
            height: 34px;
            border-radius: 7px;
            color: var(--muted);
            font-size: 0;
            transition: background .15s, color .15s;
        }

        .topbar .icon-button:hover {
            background: var(--surface-soft);
            color: var(--ink);
        }

        .topbar .icon-button svg {
            width: 17px;
            height: 17px;
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

        .message+.message {
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

        .typing-message .message-content {
            width: fit-content;
            min-width: 58px;
            padding: 8px 12px;
            border-radius: 14px 14px 14px 4px;
            background: var(--surface);
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

            0%,
            80%,
            100% {
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
            align-items: center;
            justify-content: space-between;
            margin-top: 18px;
            color: var(--ink);
            font-size: 14px;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 42px;
            height: 24px;
        }

        .switch input {
            width: 0;
            height: 0;
            opacity: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            border-radius: 24px;
            background: var(--border);
            transition: .2s;
        }

        .slider::before {
            position: absolute;
            content: "";
            width: 18px;
            height: 18px;
            left: 3px;
            bottom: 3px;
            border-radius: 50%;
            background: #fff;
            transition: .2s;
        }

        .switch input:checked + .slider {
            background: var(--accent);
        }

        .switch input:checked + .slider::before {
            transform: translateX(18px);
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

        /* Visual inspired by Claude, with original Vale branding. */
        :root {
            --bg: #f5f5f5;
            --surface: #ffffff;
            --surface-soft: #e8e8e8;
            --border: #dddddd;
            --ink: #0f1d18;
            --muted: #666666;
            --accent: #5c8a6c;
            --accent-hover: #16302a;
            --sidebar: #e8e8e8;
            --user: transparent;
        }

        body.dark {
            --bg: #0f1d18;
            --surface: #16302a;
            --surface-soft: #24453a;
            --border: #315748;
            --ink: #f5f5f5;
            --muted: #b9c8bf;
            --sidebar: #0b1511;
            --user: transparent;
        }

        body {
            font-family: Georgia, 'Times New Roman', serif;
        }

        .sidebar {
            width: 286px;
            padding: 22px 14px 16px;
            background: var(--sidebar);
            border-right: 1px solid var(--border);
            transform: translateX(0);
        }

        .sidebar-header {
            padding: 0 8px 28px;
        }

        .brand {
            gap: 7px;
            color: var(--ink);
            font-family: Arial, sans-serif;
            font-size: 17px;
            letter-spacing: -.02em;
        }

        .brand-name {
            color: var(--ink);
        }

        .brand-label {
            color: var(--accent);
        }

        .sidebar .icon-button,
        .sidebar .menu-button {
            color: var(--muted);
        }

        .new-chat {
            padding: 12px 13px;
            border: 1px solid var(--border);
            border-radius: 9px;
            background: var(--surface);
            color: var(--ink);
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        .new-chat:hover,
        .history-item:hover {
            background: var(--surface-soft);
        }

        .history {
            margin-top: 30px;
        }

        .history-title {
            padding: 0 10px 10px;
            color: var(--muted);
            font-family: Arial, sans-serif;
            font-size: 10px;
            letter-spacing: .12em;
        }

        .history-item {
            margin-bottom: 2px;
        }

        .history-question {
            padding: 10px;
            color: var(--ink);
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        .history-question:hover {
            color: var(--accent);
        }

        .delete-chat {
            color: var(--muted);
        }

        .sidebar-footer {
            border-top-color: var(--border);
        }

        .admin-action {
            border: 0;
            color: var(--muted);
        }

        .admin-action:hover {
            background: var(--surface-soft);
            color: var(--ink);
        }

        .user-name {
            color: var(--ink);
            font-family: Arial, sans-serif;
        }

        .main {
            margin-left: 286px;
        }

        .topbar {
            min-height: 64px;
            padding: 14px 30px;
            border-bottom: 0;
            background: var(--bg);
        }

        .menu-button {
            display: none;
        }

        .model-pill {
            padding: 0;
            border: 0;
            background: transparent;
            color: var(--muted);
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        .topbar-link {
            color: var(--muted);
            font-family: Arial, sans-serif;
        }

        .topbar-link:hover {
            background: var(--surface-soft);
            color: var(--ink);
        }

        .chat-scroll {
            padding: 0 28px 35px;
        }

        .chat-inner {
            max-width: 760px;
        }

        .greeting {
            padding: 12vh 0 34px;
        }

        .greeting-label {
            margin-bottom: 16px;
            color: var(--accent);
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        .greeting h1 {
            max-width: 680px;
            margin-bottom: 14px;
            font-size: clamp(28px, 4vw, 40px);
            font-weight: 400;
            letter-spacing: -.035em;
            line-height: 1.15;
        }

        .greeting p {
            max-width: 550px;
            color: var(--muted);
            font-size: 16px;
        }

        .suggestions {
            gap: 12px;
            margin-top: 34px;
        }

        .suggestion {
            min-height: 92px;
            padding: 16px;
            border-color: var(--border);
            border-radius: 12px;
            background: var(--surface);
            font-family: Arial, sans-serif;
        }

        .suggestion:hover {
            border-color: var(--accent);
            box-shadow: 0 5px 18px rgba(15, 29, 24, .08);
        }

        .suggestion strong {
            color: var(--ink);
            font-size: 13px;
        }

        .message {
            gap: 16px;
            padding: 22px 0;
        }

        .message + .message {
            border-top: 0;
        }

        .message.user {
            flex-direction: row-reverse;
            justify-content: flex-start;
            text-align: right;
        }

        .message.assistant {
            justify-content: flex-start;
            text-align: left;
        }

        .message-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--surface-soft);
            color: var(--accent);
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .message.user .message-icon {
            background: var(--accent);
            color: #fff;
        }

        .message-content,
        .message.user .message-content {
            max-width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 16px;
            background: transparent;
            color: var(--ink);
            font-size: 16px;
            line-height: 1.7;
        }

        .message.user .message-content {
            margin-left: auto;
            border-color: #c8dccd;
            border-radius: 16px 16px 4px 16px;
            background: #e8f1ea;
        }

        .message.assistant .message-content {
            margin-right: auto;
            border-radius: 16px 16px 16px 4px;
            background: var(--surface);
        }

        body.dark .message.user .message-content {
            border-color: #426451;
            background: #244534;
        }

        .input-area {
            padding: 0 28px 18px;
        }

        .input-wrap {
            max-width: 760px;
            padding: 13px 13px 13px 18px;
            border-color: var(--border);
            border-radius: 18px;
            background: var(--surface);
            box-shadow: 0 6px 24px rgba(15, 29, 24, .08);
        }

        .input-wrap:focus-within {
            border-color: var(--accent);
            box-shadow: 0 6px 24px rgba(92, 138, 108, .16);
        }

        textarea {
            font-family: Arial, sans-serif;
            font-size: 15px;
        }

        .send-button {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--accent);
            font-family: Arial, sans-serif;
        }

        .send-button:disabled {
            background: var(--surface-soft);
            color: var(--muted);
        }

        .hint {
            padding-top: 10px;
            font-family: Arial, sans-serif;
        }

        @media (max-width: 900px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main {
                margin-left: 0;
            }

            .menu-button {
                display: flex;
            }
        }

        .message.typing-message {
            align-items: center;
        }

        .message.typing-message .message-content {
            display: inline-flex;
            flex: 0 0 auto;
            width: max-content;
            min-width: 0;
            max-width: max-content;
            min-height: 0;
            height: auto;
            margin-right: auto;
            padding: 7px 11px;
            border-radius: 14px 14px 14px 4px;
            line-height: 1;
        }

        .messages,
        .message {
            width: 100%;
            min-width: 0;
        }

        .message-content,
        .message.user .message-content,
        .message.assistant .message-content {
            box-sizing: border-box;
            flex: 0 1 auto;
            width: auto;
            min-width: 0;
            max-width: calc(100% - 46px);
            line-height: 1.85;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .message-icon {
            align-self: flex-start;
        }

        .message.typing-message .message-content {
            width: max-content;
            max-width: calc(100% - 46px);
        }

    </style>
</head>

<body>
    <div class="app">
        <div class="backdrop" id="backdrop"></div>

        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="brand">
                    <span class="brand-name">Vale</span>
                    <span class="brand-label">IA</span>
                </div>

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
                    @foreach ($historico ?? [] as $conversa)
                    <div
                        class="history-item"
                        data-conversation-id="{{ $conversa->id }}"
                        data-show-url="{{ route('chat.show', $conversa) }}"
                        data-delete-url="{{ route('chat.destroy', $conversa) }}">
                        <button type="button" class="history-question">
                            {{ \Illuminate\Support\Str::limit($conversa->title, 42) }}
                        </button>

                        <button
                            type="button"
                            class="delete-chat"
                            title="Excluir conversa"
                            aria-label="Excluir conversa">
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

            @auth
            @if (Auth::user()->isAdmin())
            <div class="admin-actions">
                <a href="{{ route('admin.accounts') }}" class="admin-action">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    Gerenciar contas
                </a>
                <a href="{{ route('admin.files') }}" class="admin-action">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                        <polyline points="17 8 12 3 7 8" />
                        <line x1="12" y1="3" x2="12" y2="15" />
                    </svg>
                    Armazenar arquivos
                </a>
            </div>
            @endif
            @endauth

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

                    <button type="button" class="icon-button" id="openSettings" title="Configurações">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="3" />
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09A1.65 1.65 0 0 0 19.4 15Z" />
                        </svg>
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
                                data-question="Quais são as principais cidades do Vale do Paraíba?">
                                <strong>Principais cidades</strong>
                                <span>Conheça os municípios da região</span>
                            </button>

                            <button
                                type="button"
                                class="suggestion"
                                data-question="Qual é a economia do Vale do Paraíba?">
                                <strong>Economia</strong>
                                <span>Atividades econômicas da região</span>
                            </button>

                            <button
                                type="button"
                                class="suggestion"
                                data-question="Quais são os principais pontos turísticos do Vale do Paraíba?">
                                <strong>Turismo</strong>
                                <span>Locais e atrações turísticas</span>
                            </button>

                            <button
                                type="button"
                                class="suggestion"
                                data-question="Fale sobre a história do Vale do Paraíba.">
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
                        placeholder="Pergunte algo sobre o Vale do Paraíba..."></textarea>

                    <button
                        type="button"
                        class="send-button"
                        id="sendButton"
                        disabled>
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
                <span>Modo escuro</span>
                <span class="switch">
                    <input type="checkbox" id="darkMode">
                    <span class="slider"></span>
                </span>
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

        let currentConversationId = null;

        function addHistory(question, answer, conversationId) {
            const item = document.createElement('div');
            const questionButton = document.createElement('button');
            const deleteButton = document.createElement('button');

            item.className = 'history-item';
            item.dataset.conversationId = conversationId;
            item.dataset.showUrl = `/chat/${conversationId}`;
            item.dataset.deleteUrl = `/chat/${conversationId}`;

            questionButton.type = 'button';
            questionButton.className = 'history-question';
            questionButton.textContent = question.length > 42 ?
                `${question.substring(0, 42)}...` :
                question;

            deleteButton.type = 'button';
            deleteButton.className = 'delete-chat';
            deleteButton.title = 'Excluir conversa';
            deleteButton.textContent = '×';

            item.appendChild(questionButton);
            item.appendChild(deleteButton);
            historyList.prepend(item);
        }

        async function openConversation(item) {
            messages.innerHTML = '';
            hideError();
            greeting.style.display = 'none';
            currentConversationId = item.dataset.conversationId || null;

            try {
                const response = await fetch(item.dataset.showUrl, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(
                        data.message ||
                        'Não foi possível carregar a conversa.'
                    );
                }

                data.messages.forEach((message) => {
                    addMessage('user', message.question);
                    addMessage('assistant', message.answer);
                });
            } catch (exception) {
                showError(
                    exception.message ||
                    'Não foi possível carregar a conversa.'
                );
            }

            composer.value = '';
            composer.style.height = '28px';
            sendButton.disabled = true;
            scrollBottom();

            sidebar.classList.remove('open');
            backdrop.classList.remove('open');
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

            message.className = 'message assistant typing-message';
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

            if (!pergunta) {
                return;
            }

            hideError();
            addMessage('user', pergunta);
            composer.value = '';
            composer.style.height = '28px';
            sendButton.disabled = true;

            const answer = addTyping();
            let respostaRecebida = false;
            let filaDeTexto = '';
            let digitando = false;

            const aguardar = (tempo) => new Promise((resolve) => {
                setTimeout(resolve, tempo);
            });

            async function digitarTexto(texto) {
                filaDeTexto += texto;

                if (digitando) {
                    return;
                }

                digitando = true;

                while (filaDeTexto.length > 0) {
                    if (!respostaRecebida) {
                        answer.textContent = '';
                        respostaRecebida = true;
                    }

                    answer.textContent += filaDeTexto.charAt(0);
                    filaDeTexto = filaDeTexto.substring(1);
                    scrollBottom();
                    await aguardar(4);
                }

                digitando = false;
            }

            try {
                const response = await fetch(
                    @json(route('chat.enviar')),
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            pergunta: pergunta,
                            conversation_id: currentConversationId
                        })
                    }
                );

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
                let respostaCompleta = '';
                let conversationIdRecebida = currentConversationId;

                while (true) {
                    const { value, done } = await reader.read();

                    if (done) {
                        break;
                    }

                    buffer += decoder.decode(value, {
                        stream: true
                    });

                    const linhas = buffer.split('\n');
                    buffer = linhas.pop() || '';

                    for (const linha of linhas) {
                        if (!linha.trim()) {
                            continue;
                        }

                        let data;

                        try {
                            data = JSON.parse(linha);
                        } catch (exception) {
                            continue;
                        }

                        if (data.text) {
                            respostaCompleta += data.text;
                            digitarTexto(data.text);
                        }

                        if (data.conversation_id) {
                            conversationIdRecebida =
                                data.conversation_id;
                        }
                    }
                }

                buffer += decoder.decode();

                if (buffer.trim()) {
                    let data;

                    try {
                        data = JSON.parse(buffer);
                    } catch (exception) {
                        data = null;
                    }

                    if (data?.text) {
                        respostaCompleta += data.text;
                        digitarTexto(data.text);
                    }

                    if (data?.conversation_id) {
                        conversationIdRecebida = data.conversation_id;
                    }
                }

                if (!respostaCompleta.trim()) {
                    throw new Error(
                        'O modelo não retornou uma resposta.'
                    );
                }

                while (digitando || filaDeTexto.length > 0) {
                    await aguardar(20);
                }

                if (!currentConversationId && conversationIdRecebida) {
                    currentConversationId = conversationIdRecebida;
                    addHistory(
                        pergunta,
                        respostaCompleta,
                        currentConversationId
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

            const question = item
                .querySelector('.history-question')
                ?.textContent || 'Esta conversa';

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

                if (item.dataset.conversationId === currentConversationId) {
                    resetChat();
                }
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
            currentConversationId = null;
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

                if (item && item.dataset.showUrl) {
                    openConversation(item);
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