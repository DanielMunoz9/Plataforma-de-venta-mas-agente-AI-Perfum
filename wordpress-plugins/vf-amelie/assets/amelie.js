/**
 * Amelie Chat JavaScript
 * Diamond-themed AI Assistant for Vane France
 * 
 * @package VF-Amelie
 * @version 1.0.0
 */

(function($) {
    'use strict';

    // Global Amelie object
    window.Amelie = {
        isOpen: false,
        isTyping: false,
        chatHistory: [],
        retryAttempts: 0,
        maxRetries: 3,
        
        // Configuration
        config: {
            animationSpeed: 300,
            typingDelay: 1000,
            retryDelay: 2000,
            maxMessageLength: 500,
            autoScrollDelay: 100
        }
    };

    /**
     * Initialize Amelie Chat
     */
    function initAmelieChat() {
        // Initialize floating chat
        initFloatingChat();
        
        // Initialize shortcode chats
        initShortcodeChats();
        
        // Bind global events
        bindGlobalEvents();
        
        // Load chat history from localStorage
        loadChatHistory();
        
        console.log('Amelie Chat initialized');
    }

    /**
     * Initialize Floating Chat
     */
    function initFloatingChat() {
        const $floatingChat = $('#amelie-floating-chat');
        if (!$floatingChat.length) return;

        const $toggleButton = $('#amelie-toggle-chat');
        const $closeButton = $('#amelie-close-chat');
        const $chatWindow = $('#amelie-chat-window');

        // Toggle chat window
        $toggleButton.on('click', function() {
            toggleChatWindow();
        });

        // Close chat window
        $closeButton.on('click', function() {
            closeChatWindow();
        });

        // Initialize chat input for floating chat
        initChatInput($chatWindow);

        // Add keyboard shortcut (Ctrl+Shift+A)
        $(document).on('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && e.keyCode === 65) {
                e.preventDefault();
                toggleChatWindow();
            }
        });
    }

    /**
     * Initialize Shortcode Chats
     */
    function initShortcodeChats() {
        $('.amelie-shortcode-chat').each(function() {
            const $chatContainer = $(this);
            initChatInput($chatContainer);
        });
    }

    /**
     * Initialize Chat Input for a Container
     */
    function initChatInput($container) {
        const $input = $container.find('.amelie-message-input');
        const $sendButton = $container.find('.amelie-send-button');
        const $messagesContainer = $container.find('.amelie-chat-messages');

        // Send message on button click
        $sendButton.on('click', function() {
            sendMessage($container);
        });

        // Send message on Enter key
        $input.on('keypress', function(e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                sendMessage($container);
            }
        });

        // Auto-resize input on typing
        $input.on('input', function() {
            autoResizeInput($(this));
        });

        // Character counter
        $input.on('input', function() {
            updateCharacterCounter($container, $(this).val().length);
        });

        // Focus input when chat window opens
        $container.on('amelie:opened', function() {
            setTimeout(() => {
                $input.focus();
            }, Amelie.config.animationSpeed);
        });
    }

    /**
     * Toggle Chat Window
     */
    function toggleChatWindow() {
        const $chatWindow = $('#amelie-chat-window');
        
        if (Amelie.isOpen) {
            closeChatWindow();
        } else {
            openChatWindow();
        }
    }

    /**
     * Open Chat Window
     */
    function openChatWindow() {
        const $chatWindow = $('#amelie-chat-window');
        const $floatingChat = $('#amelie-floating-chat');
        
        Amelie.isOpen = true;
        
        $chatWindow.show().addClass('amelie-visible');
        $floatingChat.addClass('amelie-chat-open');
        
        // Hide notification badge
        $('.amelie-notification-badge').hide();
        
        // Trigger custom event
        $floatingChat.trigger('amelie:opened');
        
        // Track analytics
        trackEvent('chat_opened', { source: 'floating_button' });
    }

    /**
     * Close Chat Window
     */
    function closeChatWindow() {
        const $chatWindow = $('#amelie-chat-window');
        const $floatingChat = $('#amelie-floating-chat');
        
        Amelie.isOpen = false;
        
        $chatWindow.removeClass('amelie-visible');
        $floatingChat.removeClass('amelie-chat-open');
        
        setTimeout(() => {
            if (!Amelie.isOpen) {
                $chatWindow.hide();
            }
        }, Amelie.config.animationSpeed);
        
        // Trigger custom event
        $floatingChat.trigger('amelie:closed');
        
        // Track analytics
        trackEvent('chat_closed', { duration: Date.now() - Amelie.sessionStart });
    }

    /**
     * Send Message
     */
    function sendMessage($container) {
        const $input = $container.find('.amelie-message-input');
        const message = $input.val().trim();

        if (!message) return;
        
        if (message.length > Amelie.config.maxMessageLength) {
            showError($container, `Mensaje demasiado largo (máximo ${Amelie.config.maxMessageLength} caracteres)`);
            return;
        }

        if (Amelie.isTyping) {
            showError($container, 'Espera a que Amélie termine de responder');
            return;
        }

        // Clear input
        $input.val('');
        updateCharacterCounter($container, 0);

        // Add user message to chat
        addMessage($container, message, 'user');

        // Show typing indicator
        showTypingIndicator($container);

        // Send to API
        sendToAPI(message, $container);

        // Track analytics
        trackEvent('message_sent', { 
            message_length: message.length,
            container_type: $container.hasClass('amelie-shortcode-chat') ? 'shortcode' : 'floating'
        });
    }

    /**
     * Send Message to API
     */
    function sendToAPI(message, $container) {
        Amelie.isTyping = true;
        Amelie.retryAttempts = 0;

        makeAPIRequest(message, $container);
    }

    /**
     * Make API Request
     */
    function makeAPIRequest(message, $container) {
        $.ajax({
            url: amelieAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'amelie_chat',
                message: message,
                nonce: amelieAjax.nonce
            },
            timeout: 30000,
            success: function(response) {
                handleAPISuccess(response, $container);
            },
            error: function(xhr, status, error) {
                handleAPIError(xhr, status, error, message, $container);
            }
        });
    }

    /**
     * Handle API Success
     */
    function handleAPISuccess(response, $container) {
        hideTypingIndicator($container);
        Amelie.isTyping = false;
        Amelie.retryAttempts = 0;

        if (response.success && response.data && response.data.reply) {
            // Simulate typing delay for more natural feel
            setTimeout(() => {
                addMessage($container, response.data.reply, 'bot');
                
                // Track successful response
                trackEvent('message_received', { 
                    response_length: response.data.reply.length 
                });
            }, Amelie.config.typingDelay);
        } else {
            showError($container, response.data || 'Error desconocido del servidor');
        }
    }

    /**
     * Handle API Error
     */
    function handleAPIError(xhr, status, error, originalMessage, $container) {
        hideTypingIndicator($container);
        Amelie.isTyping = false;

        let errorMessage = 'Error de conexión';
        
        if (status === 'timeout') {
            errorMessage = 'Tiempo de espera agotado';
        } else if (xhr.responseJSON && xhr.responseJSON.data) {
            errorMessage = xhr.responseJSON.data;
        } else if (status === 'error') {
            errorMessage = 'Error del servidor';
        }

        showError($container, errorMessage, originalMessage);
        
        // Track error
        trackEvent('message_error', { 
            error_type: status,
            error_message: errorMessage,
            retry_attempt: Amelie.retryAttempts
        });
    }

    /**
     * Add Message to Chat
     */
    function addMessage($container, message, type) {
        const $messagesContainer = $container.find('.amelie-chat-messages');
        const timestamp = getCurrentTime();
        
        const messageHtml = `
            <div class="amelie-message amelie-${type}-message">
                <div class="amelie-message-content">${escapeHtml(message)}</div>
                <div class="amelie-message-time">${timestamp}</div>
            </div>
        `;

        $messagesContainer.append(messageHtml);
        
        // Auto-scroll to bottom
        setTimeout(() => {
            scrollToBottom($messagesContainer);
        }, Amelie.config.autoScrollDelay);

        // Save to chat history
        saveChatHistory(message, type);

        // Trigger custom event
        $container.trigger('amelie:message:added', { message, type, timestamp });
    }

    /**
     * Show Typing Indicator
     */
    function showTypingIndicator($container) {
        const $typingIndicator = $container.find('.amelie-typing-indicator');
        $typingIndicator.show();
        
        // Scroll to show typing indicator
        setTimeout(() => {
            scrollToBottom($container.find('.amelie-chat-messages'));
        }, 100);
    }

    /**
     * Hide Typing Indicator
     */
    function hideTypingIndicator($container) {
        const $typingIndicator = $container.find('.amelie-typing-indicator');
        $typingIndicator.hide();
    }

    /**
     * Show Error Message
     */
    function showError($container, message, originalMessage = null) {
        const $messagesContainer = $container.find('.amelie-chat-messages');
        
        let retryButton = '';
        if (originalMessage && Amelie.retryAttempts < Amelie.maxRetries) {
            retryButton = `<button class="amelie-retry-button" data-message="${escapeHtml(originalMessage)}">${amelieAjax.messages.retry}</button>`;
        }
        
        const errorHtml = `
            <div class="amelie-error-message">
                <div>❌ ${escapeHtml(message)}</div>
                ${retryButton}
            </div>
        `;

        $messagesContainer.append(errorHtml);
        
        // Bind retry button
        $messagesContainer.find('.amelie-retry-button').last().on('click', function() {
            const messageToRetry = $(this).data('message');
            $(this).closest('.amelie-error-message').remove();
            Amelie.retryAttempts++;
            sendToAPI(messageToRetry, $container);
        });

        scrollToBottom($messagesContainer);
    }

    /**
     * Auto-resize Input
     */
    function autoResizeInput($input) {
        $input.css('height', 'auto');
        const scrollHeight = $input[0].scrollHeight;
        const maxHeight = 100; // Maximum height in pixels
        
        if (scrollHeight <= maxHeight) {
            $input.css('height', scrollHeight + 'px');
        } else {
            $input.css('height', maxHeight + 'px');
        }
    }

    /**
     * Update Character Counter
     */
    function updateCharacterCounter($container, count) {
        let $counter = $container.find('.amelie-char-counter');
        
        if (!$counter.length) {
            $counter = $('<div class="amelie-char-counter"></div>');
            $container.find('.amelie-chat-input').append($counter);
        }
        
        const remaining = Amelie.config.maxMessageLength - count;
        $counter.text(`${count}/${Amelie.config.maxMessageLength}`);
        
        if (remaining < 50) {
            $counter.addClass('warning');
        } else {
            $counter.removeClass('warning');
        }
        
        if (remaining < 0) {
            $counter.addClass('error');
        } else {
            $counter.removeClass('error');
        }
    }

    /**
     * Scroll to Bottom
     */
    function scrollToBottom($container) {
        if ($container.length) {
            $container.animate({
                scrollTop: $container[0].scrollHeight
            }, Amelie.config.animationSpeed);
        }
    }

    /**
     * Get Current Time
     */
    function getCurrentTime() {
        const now = new Date();
        return now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    /**
     * Save Chat History
     */
    function saveChatHistory(message, type) {
        Amelie.chatHistory.push({
            message: message,
            type: type,
            timestamp: Date.now()
        });

        // Keep only last 50 messages
        if (Amelie.chatHistory.length > 50) {
            Amelie.chatHistory = Amelie.chatHistory.slice(-50);
        }

        // Save to localStorage
        try {
            localStorage.setItem('amelie_chat_history', JSON.stringify(Amelie.chatHistory));
        } catch (e) {
            console.warn('Failed to save chat history to localStorage:', e);
        }
    }

    /**
     * Load Chat History
     */
    function loadChatHistory() {
        try {
            const saved = localStorage.getItem('amelie_chat_history');
            if (saved) {
                Amelie.chatHistory = JSON.parse(saved);
            }
        } catch (e) {
            console.warn('Failed to load chat history from localStorage:', e);
            Amelie.chatHistory = [];
        }
    }

    /**
     * Bind Global Events
     */
    function bindGlobalEvents() {
        // Close chat when clicking outside
        $(document).on('click', function(e) {
            if (Amelie.isOpen && !$(e.target).closest('#amelie-floating-chat').length) {
                closeChatWindow();
            }
        });

        // Handle window resize
        $(window).on('resize', debounce(function() {
            // Adjust chat window position if needed
            adjustChatPosition();
        }, 250));

        // Handle page visibility change
        document.addEventListener('visibilitychange', function() {
            if (document.hidden && Amelie.isOpen) {
                // Optional: pause chat when page is hidden
                trackEvent('page_hidden', { chat_open: true });
            }
        });

        // Initialize session start time
        Amelie.sessionStart = Date.now();
    }

    /**
     * Adjust Chat Position
     */
    function adjustChatPosition() {
        const $chatWindow = $('#amelie-chat-window');
        if (!$chatWindow.length) return;

        const windowWidth = $(window).width();
        
        if (windowWidth <= 480) {
            $chatWindow.css({
                width: 'calc(100vw - 40px)',
                height: 'calc(100vh - 120px)'
            });
        } else {
            $chatWindow.css({
                width: '',
                height: ''
            });
        }
    }

    /**
     * Track Analytics Event
     */
    function trackEvent(eventName, properties = {}) {
        // Basic event tracking - can be extended with Google Analytics, etc.
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, {
                event_category: 'amelie_chat',
                ...properties
            });
        }
        
        // Console logging for debugging
        console.log('Amelie Event:', eventName, properties);
    }

    /**
     * Debounce Function
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Public API
     */
    window.Amelie.api = {
        open: openChatWindow,
        close: closeChatWindow,
        toggle: toggleChatWindow,
        sendMessage: function(message, container) {
            const $container = container ? $(container) : $('#amelie-floating-chat');
            const $input = $container.find('.amelie-message-input');
            $input.val(message);
            sendMessage($container);
        },
        clearHistory: function() {
            Amelie.chatHistory = [];
            localStorage.removeItem('amelie_chat_history');
            $('.amelie-chat-messages').empty();
        },
        isOpen: function() {
            return Amelie.isOpen;
        },
        getHistory: function() {
            return [...Amelie.chatHistory];
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        initAmelieChat();
    });

    // Make sure it works with dynamic content
    $(document).on('amelie:reinit', function() {
        initAmelieChat();
    });

})(jQuery);

/* ====== ADDITIONAL CSS FOR CHARACTER COUNTER ====== */
const additionalCSS = `
.amelie-char-counter {
    font-size: 0.7rem;
    color: #666;
    text-align: right;
    margin-top: 5px;
    transition: color 0.3s ease;
}

.amelie-char-counter.warning {
    color: #f59e0b;
}

.amelie-char-counter.error {
    color: #ef4444;
    font-weight: bold;
}

.amelie-input-container {
    position: relative;
}

/* Enhanced error styles */
.amelie-error-message {
    animation: amelie-shake 0.5s ease-in-out;
}

@keyframes amelie-shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* Loading state for send button */
.amelie-send-button.loading {
    animation: amelie-spin 1s linear infinite;
}

@keyframes amelie-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
`;

// Inject additional CSS
if (typeof document !== 'undefined') {
    const style = document.createElement('style');
    style.textContent = additionalCSS;
    document.head.appendChild(style);
}