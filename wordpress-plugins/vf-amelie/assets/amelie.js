/**
 * VF Amélie Chat JavaScript
 * Diamond-themed AI chat widget for Vane France
 */

(function($) {
    'use strict';

    // Chat manager object
    const AmelieChat = {
        // State management
        isOpen: false,
        isMinimized: false,
        isTyping: false,
        messageHistory: [],
        
        // DOM elements
        $chatButton: null,
        $chatWindow: null,
        $messagesContainer: null,
        $messageInput: null,
        $sendButton: null,
        $typingIndicator: null,
        
        // Configuration
        config: {
            apiBaseUrl: '',
            maxRetries: 3,
            retryDelay: 1000,
            typingDelay: 500,
            messageDelay: 1000,
        },

        // Initialize chat
        init: function() {
            this.config.apiBaseUrl = amelieChat.apiBaseUrl;
            this.bindElements();
            this.bindEvents();
            this.loadStoredMessages();
            this.initializeCustomColors();
            
            console.log('Amélie Chat initialized');
        },

        // Bind DOM elements
        bindElements: function() {
            this.$chatButton = $('#amelie-chat-toggle');
            this.$chatWindow = $('#amelie-chat-window');
            this.$messagesContainer = $('#amelie-chat-messages');
            this.$messageInput = $('#amelie-message-input');
            this.$sendButton = $('#amelie-send-btn');
            this.$typingIndicator = $('#amelie-typing');
        },

        // Bind events
        bindEvents: function() {
            const self = this;

            // Chat button click
            this.$chatButton.on('click', function() {
                self.toggleChat();
            });

            // Close button
            $('#amelie-close').on('click', function() {
                self.closeChat();
            });

            // Minimize button
            $('#amelie-minimize').on('click', function() {
                self.minimizeChat();
            });

            // Send button
            this.$sendButton.on('click', function() {
                self.sendMessage();
            });

            // Enter key to send message
            this.$messageInput.on('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    self.sendMessage();
                }
            });

            // Auto-resize textarea
            this.$messageInput.on('input', function() {
                self.autoResizeTextarea(this);
            });

            // Typing indicator
            this.$messageInput.on('input', function() {
                clearTimeout(self.typingTimeout);
                self.typingTimeout = setTimeout(function() {
                    // Could send typing status to server here
                }, 500);
            });

            // Click outside to close (optional)
            $(document).on('click', function(e) {
                if (self.isOpen && !$(e.target).closest('.amelie-floating-chat').length) {
                    // Uncomment to close on outside click
                    // self.closeChat();
                }
            });

            // Escape key to close
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && self.isOpen) {
                    self.closeChat();
                }
            });
        },

        // Toggle chat window
        toggleChat: function() {
            if (this.isOpen) {
                this.closeChat();
            } else {
                this.openChat();
            }
        },

        // Open chat window
        openChat: function() {
            this.isOpen = true;
            this.isMinimized = false;
            this.$chatWindow.addClass('show');
            this.$messageInput.focus();
            this.scrollToBottom();
            
            // Hide notification if present
            $('.amelie-chat-notification').removeClass('show');
            
            // Track event
            this.trackEvent('chat_opened');
        },

        // Close chat window
        closeChat: function() {
            this.isOpen = false;
            this.isMinimized = false;
            this.$chatWindow.removeClass('show');
            
            // Track event
            this.trackEvent('chat_closed');
        },

        // Minimize chat window
        minimizeChat: function() {
            this.isMinimized = true;
            this.$chatWindow.removeClass('show');
            
            // Show notification dot
            $('.amelie-chat-notification').addClass('show');
            
            // Track event
            this.trackEvent('chat_minimized');
        },

        // Send message
        sendMessage: function() {
            const message = this.$messageInput.val().trim();
            
            if (!message || this.isTyping) {
                return;
            }

            // Add user message to chat
            this.addMessage(message, 'user');
            
            // Clear input
            this.$messageInput.val('').trigger('input');
            
            // Send to API
            this.sendToAPI(message);
            
            // Track event
            this.trackEvent('message_sent', { message_length: message.length });
        },

        // Add message to chat
        addMessage: function(content, type = 'bot', timestamp = null) {
            if (!timestamp) {
                timestamp = this.getCurrentTime();
            }

            const messageClass = type === 'user' ? 'amelie-user-message' : 'amelie-bot-message';
            const avatarIcon = type === 'user' ? this.getUserIcon() : this.getBotIcon();
            
            const messageHTML = `
                <div class="amelie-message ${messageClass}">
                    <div class="amelie-message-avatar">
                        ${avatarIcon}
                    </div>
                    <div class="amelie-message-content">
                        ${this.formatMessage(content)}
                    </div>
                    <div class="amelie-message-time">
                        ${timestamp}
                    </div>
                </div>
            `;

            this.$messagesContainer.append(messageHTML);
            this.scrollToBottom();
            
            // Store message
            this.messageHistory.push({
                content: content,
                type: type,
                timestamp: timestamp
            });
            
            this.saveMessages();
        },

        // Send message to API
        sendToAPI: function(message) {
            const self = this;
            
            // Show typing indicator
            this.showTyping();
            
            // Prepare request data
            const requestData = {
                action: 'amelie_chat',
                message: message,
                nonce: amelieChat.nonce
            };

            // Send AJAX request
            $.ajax({
                url: amelieChat.ajaxUrl,
                type: 'POST',
                data: requestData,
                timeout: 30000,
                success: function(response) {
                    self.hideTyping();
                    
                    if (response.success) {
                        // Add delay for more natural feel
                        setTimeout(function() {
                            self.addMessage(response.data.message, 'bot', response.data.timestamp);
                        }, self.config.messageDelay);
                    } else {
                        self.addMessage(response.data || amelieChat.strings.error, 'bot');
                    }
                },
                error: function(xhr, status, error) {
                    self.hideTyping();
                    
                    let errorMessage = amelieChat.strings.networkError;
                    
                    if (status === 'timeout') {
                        errorMessage = 'La respuesta está tardando más de lo esperado. Por favor, inténtalo de nuevo.';
                    } else if (xhr.status === 0) {
                        errorMessage = 'Sin conexión a internet. Verifica tu conexión.';
                    }
                    
                    self.addMessage(errorMessage, 'bot');
                    
                    // Track error
                    self.trackEvent('api_error', {
                        status: status,
                        error: error,
                        xhr_status: xhr.status
                    });
                }
            });
        },

        // Show typing indicator
        showTyping: function() {
            this.isTyping = true;
            this.$typingIndicator.show();
            this.$sendButton.prop('disabled', true);
            this.scrollToBottom();
        },

        // Hide typing indicator
        hideTyping: function() {
            this.isTyping = false;
            this.$typingIndicator.hide();
            this.$sendButton.prop('disabled', false);
        },

        // Format message content
        formatMessage: function(content) {
            // Basic HTML escaping
            content = $('<div>').text(content).html();
            
            // Convert line breaks to <br>
            content = content.replace(/\n/g, '<br>');
            
            // Convert URLs to links
            const urlRegex = /(https?:\/\/[^\s]+)/g;
            content = content.replace(urlRegex, '<a href="$1" target="_blank" rel="noopener">$1</a>');
            
            // Convert email addresses to links
            const emailRegex = /([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/g;
            content = content.replace(emailRegex, '<a href="mailto:$1">$1</a>');
            
            return content;
        },

        // Get bot icon SVG
        getBotIcon: function() {
            return `
                <div class="amelie-diamond-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2L8 8h8l-4-6zm-6.5 7L1 15l4.5-6zm0 0h13L14 15 10 9h-4.5zM10 15l2 7 2-7h-4zm8.5-6L23 15l-4.5-6z"/>
                    </svg>
                </div>
            `;
        },

        // Get user icon SVG
        getUserIcon: function() {
            return `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            `;
        },

        // Get current time
        getCurrentTime: function() {
            const now = new Date();
            return now.toLocaleTimeString('es-ES', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        },

        // Auto-resize textarea
        autoResizeTextarea: function(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 80) + 'px';
        },

        // Scroll to bottom of messages
        scrollToBottom: function() {
            const container = this.$messagesContainer[0];
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },

        // Initialize custom colors
        initializeCustomColors: function() {
            if (amelieChat.primaryColor && amelieChat.accentColor) {
                const style = document.createElement('style');
                style.textContent = `
                    :root {
                        --amelie-primary: ${amelieChat.primaryColor};
                        --amelie-accent: ${amelieChat.accentColor};
                        --amelie-gradient: linear-gradient(135deg, ${amelieChat.primaryColor} 0%, ${amelieChat.accentColor} 100%);
                    }
                `;
                document.head.appendChild(style);
            }
        },

        // Save messages to localStorage
        saveMessages: function() {
            try {
                localStorage.setItem('amelie_chat_history', JSON.stringify(this.messageHistory));
            } catch (e) {
                // localStorage not available or full
                console.warn('Could not save chat history:', e);
            }
        },

        // Load stored messages
        loadStoredMessages: function() {
            try {
                const stored = localStorage.getItem('amelie_chat_history');
                if (stored) {
                    this.messageHistory = JSON.parse(stored);
                    // Optionally restore recent messages to UI
                    // this.restoreMessages();
                }
            } catch (e) {
                console.warn('Could not load chat history:', e);
                this.messageHistory = [];
            }
        },

        // Clear chat history
        clearHistory: function() {
            this.messageHistory = [];
            this.$messagesContainer.find('.amelie-message:not(:first-child)').remove();
            this.saveMessages();
            this.trackEvent('history_cleared');
        },

        // Track events (for analytics)
        trackEvent: function(eventName, data = {}) {
            // Send to analytics if available
            if (typeof gtag !== 'undefined') {
                gtag('event', eventName, {
                    event_category: 'Amélie Chat',
                    ...data
                });
            }
            
            // Send to WordPress if available
            if (typeof wp !== 'undefined' && wp.hooks) {
                wp.hooks.doAction('amelie_chat_event', eventName, data);
            }
            
            console.log('Amélie Chat Event:', eventName, data);
        },

        // Utility methods
        utils: {
            // Debounce function
            debounce: function(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            },

            // Throttle function
            throttle: function(func, limit) {
                let inThrottle;
                return function() {
                    const args = arguments;
                    const context = this;
                    if (!inThrottle) {
                        func.apply(context, args);
                        inThrottle = true;
                        setTimeout(() => inThrottle = false, limit);
                    }
                };
            },

            // Format file size
            formatFileSize: function(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
        }
    };

    // Shortcode chat functions (global scope)
    window.amelieSendMessage = function(chatId) {
        const inputId = `amelie-embedded-input-${chatId}`;
        const messagesId = `amelie-embedded-messages-${chatId}`;
        const typingId = `amelie-embedded-typing-${chatId}`;
        
        const $input = $(`#${inputId}`);
        const $messages = $(`#${messagesId}`);
        const $typing = $(`#${typingId}`);
        
        const message = $input.val().trim();
        
        if (!message) return;
        
        // Add user message
        const timestamp = new Date().toLocaleTimeString('es-ES', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        const userMessageHTML = `
            <div class="amelie-message amelie-user-message">
                <div class="amelie-message-avatar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
                <div class="amelie-message-content">
                    ${$('<div>').text(message).html()}
                </div>
                <div class="amelie-message-time">
                    ${timestamp}
                </div>
            </div>
        `;
        
        $messages.append(userMessageHTML);
        $input.val('');
        
        // Show typing
        $typing.show();
        
        // Scroll to bottom
        $messages[0].scrollTop = $messages[0].scrollHeight;
        
        // Send to API
        $.ajax({
            url: amelieChat.ajaxUrl,
            type: 'POST',
            data: {
                action: 'amelie_chat',
                message: message,
                nonce: amelieChat.nonce
            },
            success: function(response) {
                $typing.hide();
                
                const botMessage = response.success ? response.data.message : (response.data || amelieChat.strings.error);
                const botTimestamp = response.success ? response.data.timestamp : timestamp;
                
                const botMessageHTML = `
                    <div class="amelie-message amelie-bot-message">
                        <div class="amelie-message-avatar">
                            <div class="amelie-diamond-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2L8 8h8l-4-6zm-6.5 7L1 15l4.5-6zm0 0h13L14 15 10 9h-4.5zM10 15l2 7 2-7h-4zm8.5-6L23 15l-4.5-6z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="amelie-message-content">
                            ${$('<div>').text(botMessage).html()}
                        </div>
                        <div class="amelie-message-time">
                            ${botTimestamp}
                        </div>
                    </div>
                `;
                
                setTimeout(function() {
                    $messages.append(botMessageHTML);
                    $messages[0].scrollTop = $messages[0].scrollHeight;
                }, 500);
            },
            error: function() {
                $typing.hide();
                
                const errorMessageHTML = `
                    <div class="amelie-message amelie-bot-message">
                        <div class="amelie-message-avatar">
                            <div class="amelie-diamond-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2L8 8h8l-4-6zm-6.5 7L1 15l4.5-6zm0 0h13L14 15 10 9h-4.5zM10 15l2 7 2-7h-4zm8.5-6L23 15l-4.5-6z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="amelie-message-content">
                            ${amelieChat.strings.networkError}
                        </div>
                        <div class="amelie-message-time">
                            ${timestamp}
                        </div>
                    </div>
                `;
                
                $messages.append(errorMessageHTML);
                $messages[0].scrollTop = $messages[0].scrollHeight;
            }
        });
    };

    window.amelieNewChat = function(chatId) {
        const messagesId = `amelie-embedded-messages-${chatId}`;
        const $messages = $(`#${messagesId}`);
        
        // Clear messages except welcome message
        $messages.find('.amelie-message:not(:first-child)').remove();
        
        // Track event
        AmelieChat.trackEvent('new_chat_started', { chat_id: chatId });
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        // Only initialize if floating chat elements exist
        if ($('#amelie-floating-chat').length > 0) {
            AmelieChat.init();
        }
        
        // Handle Enter key in embedded chats
        $(document).on('keydown', '[id^="amelie-embedded-input-"]', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                const chatId = this.id.replace('amelie-embedded-input-', '');
                amelieSendMessage(chatId);
            }
        });
        
        // Auto-resize embedded textareas
        $(document).on('input', '[id^="amelie-embedded-input-"]', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 80) + 'px';
        });
    });

    // Make AmelieChat available globally for debugging
    window.AmelieChat = AmelieChat;

})(jQuery);