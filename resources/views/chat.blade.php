<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Assistente Ollama - Painel</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: #f4f6f8; margin: 0; padding: 20px; color: #333; }
        .header { display: flex; justify-content: space-between; align-items: center; max-width: 800px; margin: 0 auto 20px auto; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 8px; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
        textarea { width: 100%; height: 80px; padding: 10px; border: 1px solid #ccc; border-radius: 6px; resize: vertical; }
        button.btn-send { background: #2563eb; color: white; border: none; padding: 10px 18px; border-radius: 6px; font-weight: bold; cursor: pointer; margin-top: 8px; }
        .history { margin-top: 30px; }
        .chat-item { background: #f8fafc; border-left: 4px solid #cbd5e1; padding: 14px; border-radius: 6px; margin-bottom: 16px; }
        .badge { font-size: 11px; font-weight: bold; padding: 3px 8px; border-radius: 12px; text-transform: uppercase; }
        .badge-doc { background: #dbeafe; color: #1e40af; }
        .badge-geral { background: #fef3c7; color: #92400e; }
        .time { font-size: 0.8em; color: #888; margin-left: 8px; }
        .pergunta { font-weight: bold; color: #1e293b; margin-bottom: 6px; }
        .resposta { white-space: pre-wrap; line-height: 1.5; color: #334155; }
        .logout-btn { background: #dc2626; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>

<div class="header">
  <h3>Olá, {{ auth()->user()->name }}</h3>
    <div>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.files.index') }}" style="background: #16a34a; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; margin-right: 8px; font-weight: bold;">
                📁 Gerenciar Arquivos
            </a>
        @endif

        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="logout-btn">Sair</button>
        </form>
    </div>
</div>

<div class="container">
    <h2>Perguntar ao Ollama</h2>
    <form id="chatForm">
        <textarea id="pergunta" placeholder="Digite sua pergunta..." required></textarea>
        <button type="submit" id="btnEnviar" class="btn-send">Enviar Pergunta</button>
    </form>
    <div id="status" style="display:none; margin-top:10px; color:#666;">Consultando documentos e aguardando resposta...</div>

    <div class="history">
        <h3>Seu Histórico de Conversas</h3>
        <div id="historicoContainer">
            @foreach($historico as $chat)
                <div class="chat-item" style="border-left-color: {{ $chat->fonte === 'documentos' ? '#2563eb' : '#d97706' }};">
                    <span class="badge {{ $chat->fonte === 'documentos' ? 'badge-doc' : 'badge-geral' }}">
                        {{ $chat->fonte === 'documentos' ? 'Documentos' : 'Conhecimento Geral' }}
                    </span>
                    <span class="time">{{ $chat->created_at->format('d/m/Y H:i') }}</span>
                    <div class="pergunta">Q: {{ $chat->pergunta }}</div>
                    <div class="resposta">{{ $chat->resposta }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('chatForm');
    const perguntaInput = document.getElementById('pergunta');
    const btnEnviar = document.getElementById('btnEnviar');
    const statusDiv = document.getElementById('status');
    const historicoContainer = document.getElementById('historicoContainer');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const pergunta = perguntaInput.value.trim();
        if (!pergunta) return;

        btnEnviar.disabled = true;
        statusDiv.style.display = 'block';

        try {
            const response = await fetch('{{ route("chat.enviar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ pergunta: pergunta })
            });

            const data = await response.json();

            if (response.ok) {
                // Adiciona o novo item no topo do histórico
                const isDoc = data.fonte === 'documentos';
                const newItemHtml = `
                    <div class="chat-item" style="border-left-color: ${isDoc ? '#2563eb' : '#d97706'};">
                        <span class="badge ${isDoc ? 'badge-doc' : 'badge-geral'}">
                            ${isDoc ? 'Documentos' : 'Conhecimento Geral'}
                        </span>
                        <span class="time">${data.data}</span>
                        <div class="pergunta">Q: ${data.pergunta}</div>
                        <div class="resposta">${data.resposta}</div>
                    </div>
                `;
                historicoContainer.insertAdjacentHTML('afterbegin', newItemHtml);
                perguntaInput.value = '';
            } else {
                alert('Erro ao processar requisição.');
            }
        } catch (error) {
           console.error('Erro detalhado:', error);
    alert('Erro: ' + error.message);
        } finally {
            btnEnviar.disabled = false;
            statusDiv.style.display = 'none';
        }
    });
</script>

</body>
</html>
