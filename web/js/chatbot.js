/**
 * Smart Help Chatbot Logic
 */
document.addEventListener('DOMContentLoaded', function() {
    const chatBtn = document.getElementById('chatbot-button');
    const chatWin = document.getElementById('chatbot-window');
    const chatClose = document.getElementById('chatbot-close');
    const chatForm = document.getElementById('chatbot-form');
    const chatInput = document.getElementById('chatbot-input-field');
    const chatMessages = document.getElementById('chatbot-messages');
    const typingIndicator = document.getElementById('typing-indicator');

    // Knowledge Base em Português para Respostas Instantâneas (0ms Latency)
    const responses = [
        {
            keywords: ['ola', 'oi', 'hello', 'hi', 'hey', 'tudo bem', 'bom dia', 'boa tarde', 'boa noite'],
            response: "Olá! Eu sou o <strong>Linky</strong>, seu assistente virtual. Como posso ajudar você a gerenciar seus links hoje?"
        },
        {
            keywords: ['criar', 'novo', 'encurtar', 'gerar', 'link', 'url', 'create'],
            response: "Para criar um novo link encurtado, vá na seção <strong>Links</strong> no menu lateral e clique em <strong>New Link</strong>. Basta colar a sua URL longa e clicar em salvar!"
        },
        {
            keywords: ['qr', 'code', 'qrcode'],
            response: "Você pode baixar QR Codes para qualquer link encurtado! Vá na sua lista de <strong>Links</strong>, clique em <strong>View</strong> no link desejado e lá você terá as opções de download em PNG ou SVG."
        },
        {
            keywords: ['relatorio', 'pdf', 'exportar', 'grafico', 'report'],
            response: "Precisa de um relatório profissional? Clique em <strong>Reports</strong> no menu lateral. Você poderá selecionar links específicos e gerar um PDF completo com estatísticas de acessos."
        },
        {
            keywords: ['senha', 'proteger', 'protegido', 'password'],
            response: "Sim! Você pode proteger seus links com senha. Ao criar ou editar um link, marque a caixa <strong>Password Protected</strong> e defina a senha que o usuário deverá digitar para ser redirecionado."
        },
        {
            keywords: ['deletar', 'apagar', 'excluir', 'remover', 'delete'],
            response: "Para excluir um link, vá em <strong>Links</strong> na barra lateral, clique no botão <strong>View</strong> do link correspondente e depois clique no botão vermelho <strong>Delete</strong> no topo da página."
        },
        {
            keywords: ['campanha', 'campaign', 'organizar'],
            response: "As <strong>Campanhas</strong> ajudam a organizar seus links em grupos (ex: 'Promoção de Verão'). Vá em <strong>Campaigns</strong> no menu lateral para criar campanhas e ver estatísticas agrupadas."
        },
        {
            keywords: ['bot', 'crawler', 'filtro', 'whatsapp', 'google'],
            response: "Nosso sistema filtra cliques de robôs de redes sociais e crawlers (como o do WhatsApp ou Google) para garantir que suas estatísticas reflitam apenas cliques de humanos reais!"
        }
    ];

    // Toggle Chat
    chatBtn.addEventListener('click', () => {
        const isVisible = chatWin.style.display === 'flex';
        chatWin.style.display = isVisible ? 'none' : 'flex';
        if (!isVisible && chatMessages.children.length === 0) {
            setTimeout(() => addBotMessage("Olá! Eu sou o <strong>Linky</strong>, seu assistente virtual. Como posso ajudar você hoje?"), 500);
        }
    });

    chatClose.addEventListener('click', () => {
        chatWin.style.display = 'none';
    });

    // Send Message
    chatForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const text = chatInput.value.trim();
        if (!text) return;

        addUserMessage(text);
        chatInput.value = '';
        
        processResponse(text);
    });

    function addUserMessage(text) {
        const div = document.createElement('div');
        div.className = 'message user';
        div.textContent = text;
        chatMessages.appendChild(div);
        scrollToBottom();
    }

    function addBotMessage(text) {
        typingIndicator.style.display = 'none';
        const div = document.createElement('div');
        div.className = 'message bot';
        div.innerHTML = text; // Allow HTML for links
        chatMessages.appendChild(div);
        scrollToBottom();
    }

    function processResponse(text) {
        const cleanText = text.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim();
        
        // 1. Otimização Frontend: Correspondência local instantânea por palavras-chave
        for (const item of responses) {
            if (item.keywords.some(keyword => cleanText.includes(keyword))) {
                // Simula digitação curtíssima para naturalidade e responde instantaneamente
                typingIndicator.style.display = 'block';
                scrollToBottom();
                setTimeout(() => {
                    addBotMessage(item.response);
                }, 300);
                return;
            }
        }

        // 2. Fallback para o backend Gemini super rápido
        typingIndicator.style.display = 'block';
        scrollToBottom();

        $.post('/chat/query', {
            message: text,
            _csrf: $('meta[name="csrf-token"]').attr('content')
        })
        .done(function(data) {
            addBotMessage(data.response);
        })
        .fail(function() {
            addBotMessage("Desculpe, estou com dificuldades para me conectar. Por favor, tente novamente.");
        })
        .always(function() {
            typingIndicator.style.display = 'none';
        });
    }

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});
