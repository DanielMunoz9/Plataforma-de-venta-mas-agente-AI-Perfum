/**
 * Agente AI "Amélie" - Chat Widget Simplificado
 * Enfoque en funcionalidad básica y personalidad core
 */

class AmelieChat {
    constructor() {
        this.sessionId = null;
        this.isOpen = false;
        this.conversationHistory = [];
        
        this.initializeChat();
        this.showWelcomeMessage();
    }

    initializeChat() {
        // Crear el HTML del chat widget
        this.createChatWidget();
        
        // Configurar event listeners
        this.setupEventListeners();
    }

    createChatWidget() {
        const placeholder = document.getElementById('agente-ai-placeholder');
        if (!placeholder) return;

        placeholder.innerHTML = `
            <div id="amelie-chat-widget" class="amelie-chat-widget">
                <!-- Chat Button -->
                <div id="chat-button" class="chat-button">
                    <div class="chat-avatar">💎</div>
                    <span class="chat-text">Amélie</span>
                    <div class="chat-notification" id="chat-notification">
                        <div class="notification-dot"></div>
                        <span class="notification-text">¡Hola! Soy Amélie 💎</span>
                    </div>
                </div>

                <!-- Chat Window -->
                <div id="chat-window" class="chat-window">
                    <div class="chat-header">
                        <div class="agent-info">
                            <div class="agent-avatar">💎</div>
                            <div class="agent-details">
                                <h4>Amélie</h4>
                                <span class="agent-status">Consultora de Fragancias</span>
                            </div>
                        </div>
                        <button id="close-chat" class="close-chat-btn">×</button>
                    </div>

                    <div class="chat-messages" id="chat-messages">
                        <!-- Los mensajes aparecerán aquí -->
                    </div>

                    <div class="chat-typing" id="chat-typing" style="display: none;">
                        <div class="typing-indicator">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <span class="typing-text">Amélie está escribiendo...</span>
                    </div>

                    <div class="chat-input-container">
                        <div class="quick-actions" id="quick-actions">
                            <button class="quick-action-btn" data-message="Hola, necesito ayuda con perfumes">
                                🌹 Ayuda con perfumes
                            </button>
                            <button class="quick-action-btn" data-message="¿Cuáles son sus precios?">
                                💰 Ver precios
                            </button>
                            <button class="quick-action-btn" data-message="¿Qué me recomiendas?">
                                ✨ Pedir recomendación
                            </button>
                        </div>
                        <div class="chat-input-wrapper">
                            <input 
                                type="text" 
                                id="chat-input" 
                                placeholder="Escribe tu mensaje a Amélie..."
                                maxlength="500"
                                autocomplete="off"
                            >
                            <button id="send-message" class="send-btn">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    setupEventListeners() {
        // Toggle chat window
        document.getElementById('chat-button').addEventListener('click', () => {
            this.toggleChat();
        });

        // Close chat
        document.getElementById('close-chat').addEventListener('click', () => {
            this.closeChat();
        });

        // Send message
        document.getElementById('send-message').addEventListener('click', () => {
            this.sendMessage();
        });

        // Enter key to send message
        document.getElementById('chat-input').addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Quick action buttons
        document.querySelectorAll('.quick-action-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const message = e.target.getAttribute('data-message');
                this.sendQuickMessage(message);
            });
        });
    }

    toggleChat() {
        this.isOpen = !this.isOpen;
        const chatWindow = document.getElementById('chat-window');
        const chatButton = document.getElementById('chat-button');
        const notification = document.getElementById('chat-notification');

        if (this.isOpen) {
            chatWindow.style.display = 'flex';
            chatButton.style.display = 'none';
            notification.style.display = 'none';
            this.scrollToBottom();
            document.getElementById('chat-input').focus();
        } else {
            chatWindow.style.display = 'none';
            chatButton.style.display = 'flex';
        }
    }

    closeChat() {
        this.isOpen = false;
        document.getElementById('chat-window').style.display = 'none';
        document.getElementById('chat-button').style.display = 'flex';
    }

    async sendMessage(messageText = null) {
        const input = document.getElementById('chat-input');
        const message = messageText || input.value.trim();

        if (!message) return;

        // Clear input
        if (!messageText) input.value = '';

        // Hide quick actions after first message
        this.hideQuickActions();

        // Add user message to chat
        this.addMessage(message, 'user');

        // Show typing indicator
        this.showTyping();

        try {
            // Send message to backend
            const response = await fetch('/api/ai/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: message })
            });

            const data = await response.json();

            if (response.ok) {
                // Hide typing and show response
                this.hideTyping();
                this.addMessage(data.response, 'agent');
                this.sessionId = data.session_id;

                // Update conversation history
                this.conversationHistory.push(
                    { message: message, type: 'user', timestamp: new Date() },
                    { message: data.response, type: 'agent', timestamp: new Date() }
                );

            } else {
                this.hideTyping();
                this.addMessage('Lo siento, tengo problemas técnicos. ¿Puedes intentar de nuevo?', 'agent');
            }

        } catch (error) {
            console.error('Error sending message:', error);
            this.hideTyping();
            this.addMessage('Disculpa, parece que hay un problema de conexión. Intenta de nuevo en un momento.', 'agent');
        }
    }

    sendQuickMessage(message) {
        this.sendMessage(message);
    }

    addMessage(message, type) {
        const messagesContainer = document.getElementById('chat-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}-message`;

        const timestamp = new Date().toLocaleTimeString('es-ES', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });

        if (type === 'agent') {
            messageDiv.innerHTML = `
                <div class="message-avatar">💎</div>
                <div class="message-content">
                    <div class="message-text">${message}</div>
                    <div class="message-time">${timestamp}</div>
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="message-content">
                    <div class="message-text">${message}</div>
                    <div class="message-time">${timestamp}</div>
                </div>
            `;
        }

        messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();

        // Animate message appearance
        setTimeout(() => {
            messageDiv.classList.add('message-appear');
        }, 50);
    }

    showTyping() {
        const typingIndicator = document.getElementById('chat-typing');
        typingIndicator.style.display = 'flex';
        this.scrollToBottom();
    }

    hideTyping() {
        const typingIndicator = document.getElementById('chat-typing');
        typingIndicator.style.display = 'none';
    }

    hideQuickActions() {
        const quickActions = document.getElementById('quick-actions');
        if (this.conversationHistory.length === 0) {
            quickActions.style.display = 'none';
        }
    }

    scrollToBottom() {
        const messagesContainer = document.getElementById('chat-messages');
        setTimeout(() => {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }, 100);
    }

    showWelcomeMessage() {
        // Mostrar notificación de bienvenida después de 3 segundos
        setTimeout(() => {
            if (!this.isOpen && this.conversationHistory.length === 0) {
                this.showNotification("¿Te ayudo a encontrar tu fragancia perfecta?");
            }
        }, 3000);
    }

    showNotification(message) {
        const notification = document.getElementById('chat-notification');
        const notificationText = notification.querySelector('.notification-text');
        
        notificationText.textContent = message;
        notification.style.display = 'block';
        notification.classList.add('notification-appear');

        // Hide after 5 seconds
        setTimeout(() => {
            notification.classList.remove('notification-appear');
            setTimeout(() => {
                notification.style.display = 'none';
            }, 300);
        }, 5000);
    }

    // Public methods for integration
    openChat() {
        if (!this.isOpen) {
            this.toggleChat();
        }
    }

    sendProgrammaticMessage(message) {
        this.openChat();
        setTimeout(() => {
            this.sendMessage(message);
        }, 500);
    }
}

// Initialize Amélie Chat when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for other scripts to load
    setTimeout(() => {
        window.amelieChat = new AmelieChat();
        
        // Global functions for external integration
        window.openAmelieChat = () => window.amelieChat.openChat();
        window.askAmelie = (message) => window.amelieChat.sendProgrammaticMessage(message);
        
    }, 1000);
});

// CSS Animation helpers
function addCSSAnimations() {
    const style = document.createElement('style');
    style.textContent = `
        @keyframes messageAppear {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes notificationBounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-5px);
            }
            60% {
                transform: translateY(-3px);
            }
        }

        .message-appear {
            animation: messageAppear 0.3s ease-out;
        }

        .notification-appear {
            animation: notificationBounce 0.6s ease-out;
        }
    `;
    document.head.appendChild(style);
}

// Add animations when script loads
addCSSAnimations();