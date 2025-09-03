<?php
/**
 * Plugin Name: VF Amelie - AI Chat Assistant
 * Description: Floating AI chat widget and shortcode integration for Vane France with secure API communication.
 * Version: 1.0.0
 * Author: Vane France Team
 * Text Domain: vf-amelie
 * Domain Path: /languages
 * Requires WP: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * License: GPL v2 or later
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('VF_AMELIE_VERSION', '1.0.0');
define('VF_AMELIE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VF_AMELIE_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main VF Amelie Class
 */
class VF_Amelie {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        // Initialize components
        $this->add_admin_menu();
        $this->register_shortcodes();
        $this->init_ajax_hooks();
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        // Add floating chat widget
        add_action('wp_footer', array($this, 'render_floating_chat'));
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('vf-amelie', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Plugin Activation
     */
    public function activate() {
        // Create default options
        add_option('amelie_api_base_url', '');
        add_option('amelie_api_secret', '');
        add_option('amelie_auto_show_chat', true);
        add_option('amelie_chat_enabled', true);
        add_option('amelie_welcome_message', __('¡Hola! Soy Amélie, tu asistente virtual de Vane France. ¿En qué puedo ayudarte hoy?', 'vf-amelie'));
        add_option('amelie_chat_position', 'bottom-right');
        add_option('amelie_chat_theme', 'diamond');
    }
    
    /**
     * Plugin Deactivation
     */
    public function deactivate() {
        // Clean up if needed
    }
    
    /**
     * Add Admin Menu
     */
    public function add_admin_menu() {
        add_action('admin_menu', array($this, 'create_admin_menu'));
    }
    
    public function create_admin_menu() {
        add_options_page(
            __('Amelie Chat Settings', 'vf-amelie'),
            __('Amelie Chat', 'vf-amelie'),
            'manage_options',
            'amelie-settings',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Settings Page
     */
    public function settings_page() {
        if (isset($_POST['submit'])) {
            // Verify nonce
            if (!wp_verify_nonce($_POST['amelie_nonce'], 'amelie_settings')) {
                wp_die(__('Security check failed', 'vf-amelie'));
            }
            
            // Update settings (sanitize inputs)
            update_option('amelie_api_base_url', esc_url_raw($_POST['amelie_api_base_url']));
            update_option('amelie_api_secret', sanitize_text_field($_POST['amelie_api_secret']));
            update_option('amelie_auto_show_chat', isset($_POST['amelie_auto_show_chat']));
            update_option('amelie_chat_enabled', isset($_POST['amelie_chat_enabled']));
            update_option('amelie_welcome_message', sanitize_textarea_field($_POST['amelie_welcome_message']));
            update_option('amelie_chat_position', sanitize_text_field($_POST['amelie_chat_position']));
            update_option('amelie_chat_theme', sanitize_text_field($_POST['amelie_chat_theme']));
            
            echo '<div class="notice notice-success"><p>' . __('Configuración guardada correctamente.', 'vf-amelie') . '</p></div>';
        }
        
        // Get current settings
        $api_base_url = get_option('amelie_api_base_url', '');
        $api_secret = get_option('amelie_api_secret', '');
        $auto_show_chat = get_option('amelie_auto_show_chat', true);
        $chat_enabled = get_option('amelie_chat_enabled', true);
        $welcome_message = get_option('amelie_welcome_message', __('¡Hola! Soy Amélie, tu asistente virtual de Vane France. ¿En qué puedo ayudarte hoy?', 'vf-amelie'));
        $chat_position = get_option('amelie_chat_position', 'bottom-right');
        $chat_theme = get_option('amelie_chat_theme', 'diamond');
        ?>
        <div class="wrap">
            <h1><?php _e('Configuración de Amelie Chat', 'vf-amelie'); ?></h1>
            
            <div class="amelie-admin-header">
                <div class="amelie-branding">
                    <h2>💎 Amélie AI Assistant</h2>
                    <p><?php _e('Asistente de inteligencia artificial para Vane France', 'vf-amelie'); ?></p>
                </div>
            </div>
            
            <form method="post" action="">
                <?php wp_nonce_field('amelie_settings', 'amelie_nonce'); ?>
                
                <div class="amelie-settings-tabs">
                    <div class="tab-content active" id="api-settings">
                        <h2><?php _e('Configuración de API', 'vf-amelie'); ?></h2>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php _e('URL Base de la API', 'vf-amelie'); ?></th>
                                <td>
                                    <input type="url" name="amelie_api_base_url" value="<?php echo esc_attr($api_base_url); ?>" class="regular-text" placeholder="https://api.ejemplo.com" />
                                    <p class="description">
                                        <?php _e('URL del servidor Flask donde está desplegada la API de Amélie', 'vf-amelie'); ?>
                                        <br><strong><?php _e('Ejemplo:', 'vf-amelie'); ?></strong> https://api.vane-france.com
                                    </p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row"><?php _e('Clave Secreta de API', 'vf-amelie'); ?></th>
                                <td>
                                    <input type="password" name="amelie_api_secret" value="<?php echo esc_attr($api_secret); ?>" class="regular-text" placeholder="<?php _e('Opcional - dejar vacío si no se requiere', 'vf-amelie'); ?>" />
                                    <p class="description">
                                        <?php _e('Clave secreta para autenticación (opcional). Se enviará como header X-API-KEY.', 'vf-amelie'); ?>
                                        <br><strong><?php _e('Importante:', 'vf-amelie'); ?></strong> <?php _e('Esta clave NO se muestra en el código frontend por seguridad.', 'vf-amelie'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="tab-content" id="chat-settings">
                        <h2><?php _e('Configuración del Chat', 'vf-amelie'); ?></h2>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php _e('Habilitar Chat', 'vf-amelie'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="amelie_chat_enabled" value="1" <?php checked($chat_enabled); ?> />
                                        <?php _e('Activar el sistema de chat de Amélie', 'vf-amelie'); ?>
                                    </label>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row"><?php _e('Mostrar Automáticamente', 'vf-amelie'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="amelie_auto_show_chat" value="1" <?php checked($auto_show_chat); ?> />
                                        <?php _e('Mostrar el botón flotante automáticamente en todas las páginas', 'vf-amelie'); ?>
                                    </label>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row"><?php _e('Mensaje de Bienvenida', 'vf-amelie'); ?></th>
                                <td>
                                    <textarea name="amelie_welcome_message" rows="3" class="large-text"><?php echo esc_textarea($welcome_message); ?></textarea>
                                    <p class="description"><?php _e('Mensaje que se muestra cuando el usuario abre el chat', 'vf-amelie'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row"><?php _e('Posición del Chat', 'vf-amelie'); ?></th>
                                <td>
                                    <select name="amelie_chat_position">
                                        <option value="bottom-right" <?php selected($chat_position, 'bottom-right'); ?>><?php _e('Inferior Derecha', 'vf-amelie'); ?></option>
                                        <option value="bottom-left" <?php selected($chat_position, 'bottom-left'); ?>><?php _e('Inferior Izquierda', 'vf-amelie'); ?></option>
                                        <option value="top-right" <?php selected($chat_position, 'top-right'); ?>><?php _e('Superior Derecha', 'vf-amelie'); ?></option>
                                        <option value="top-left" <?php selected($chat_position, 'top-left'); ?>><?php _e('Superior Izquierda', 'vf-amelie'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row"><?php _e('Tema Visual', 'vf-amelie'); ?></th>
                                <td>
                                    <select name="amelie_chat_theme">
                                        <option value="diamond" <?php selected($chat_theme, 'diamond'); ?>><?php _e('Diamante (Recomendado)', 'vf-amelie'); ?></option>
                                        <option value="modern" <?php selected($chat_theme, 'modern'); ?>><?php _e('Moderno', 'vf-amelie'); ?></option>
                                        <option value="classic" <?php selected($chat_theme, 'classic'); ?>><?php _e('Clásico', 'vf-amelie'); ?></option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <?php submit_button(); ?>
            </form>
            
            <!-- API Test Section -->
            <div class="amelie-test-section">
                <h2><?php _e('Probar Conexión con la API', 'vf-amelie'); ?></h2>
                <p><?php _e('Usa este botón para verificar que la conexión con la API funciona correctamente.', 'vf-amelie'); ?></p>
                <button type="button" id="test-api-connection" class="button button-secondary">
                    <?php _e('Probar Conexión', 'vf-amelie'); ?>
                </button>
                <div id="api-test-result"></div>
            </div>
            
            <!-- Usage Instructions -->
            <div class="amelie-usage-section">
                <h2><?php _e('Instrucciones de Uso', 'vf-amelie'); ?></h2>
                <div class="amelie-instructions">
                    <h3><?php _e('Shortcode', 'vf-amelie'); ?></h3>
                    <p><?php _e('Utiliza el shortcode para incluir el chat en cualquier página o entrada:', 'vf-amelie'); ?></p>
                    <code>[amelie_chat]</code>
                    
                    <h3><?php _e('Chat Flotante', 'vf-amelie'); ?></h3>
                    <p><?php _e('El chat flotante aparece automáticamente si está habilitado en la configuración.', 'vf-amelie'); ?></p>
                    
                    <h3><?php _e('Configuración de la API Backend', 'vf-amelie'); ?></h3>
                    <p><?php _e('Asegúrate de que tu servidor Flask tenga un endpoint:', 'vf-amelie'); ?></p>
                    <code>POST /api/ai/chat</code>
                    <p><?php _e('Que reciba JSON:', 'vf-amelie'); ?> <code>{"message": "texto del usuario"}</code></p>
                    <p><?php _e('Y retorne JSON:', 'vf-amelie'); ?> <code>{"reply": "respuesta de la IA"}</code></p>
                </div>
            </div>
        </div>
        
        <style>
        .amelie-admin-header {
            background: linear-gradient(135deg, #002395, #ed2939);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .amelie-branding h2 {
            color: white;
            margin: 0 0 10px 0;
            font-size: 1.8rem;
        }
        .amelie-branding p {
            margin: 0;
            opacity: 0.9;
        }
        .amelie-test-section,
        .amelie-usage-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #dee2e6;
        }
        .amelie-instructions {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .amelie-instructions h3 {
            color: #002395;
            margin-top: 20px;
        }
        .amelie-instructions code {
            background: #f1f3f4;
            padding: 8px 12px;
            border-radius: 4px;
            display: inline-block;
            margin: 5px 0;
            font-family: 'Courier New', monospace;
        }
        #api-test-result {
            margin-top: 15px;
            padding: 10px;
            border-radius: 4px;
            display: none;
        }
        #api-test-result.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        #api-test-result.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        </style>
        
        <script>
        document.getElementById('test-api-connection').addEventListener('click', function() {
            const button = this;
            const result = document.getElementById('api-test-result');
            
            button.disabled = true;
            button.textContent = '<?php _e('Probando...', 'vf-amelie'); ?>';
            
            // Test API connection via AJAX
            fetch(ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=test_amelie_api&nonce=<?php echo wp_create_nonce('test_amelie_api'); ?>'
            })
            .then(response => response.json())
            .then(data => {
                result.style.display = 'block';
                if (data.success) {
                    result.className = 'success';
                    result.textContent = '<?php _e('✓ Conexión exitosa con la API', 'vf-amelie'); ?>';
                } else {
                    result.className = 'error';
                    result.textContent = '✗ ' + (data.data || '<?php _e('Error de conexión', 'vf-amelie'); ?>');
                }
            })
            .catch(error => {
                result.style.display = 'block';
                result.className = 'error';
                result.textContent = '✗ <?php _e('Error al probar la conexión', 'vf-amelie'); ?>: ' + error.message;
            })
            .finally(() => {
                button.disabled = false;
                button.textContent = '<?php _e('Probar Conexión', 'vf-amelie'); ?>';
            });
        });
        </script>
        <?php
    }
    
    /**
     * Register Shortcodes
     */
    public function register_shortcodes() {
        add_shortcode('amelie_chat', array($this, 'chat_shortcode'));
    }
    
    /**
     * Chat Shortcode
     */
    public function chat_shortcode($atts) {
        $atts = shortcode_atts(array(
            'height' => '400px',
            'width' => '100%',
            'theme' => get_option('amelie_chat_theme', 'diamond')
        ), $atts);
        
        if (!get_option('amelie_chat_enabled', true)) {
            return '<p>' . __('El chat de Amélie está deshabilitado.', 'vf-amelie') . '</p>';
        }
        
        $chat_id = 'amelie-chat-' . uniqid();
        
        ob_start();
        ?>
        <div id="<?php echo $chat_id; ?>" class="amelie-chat-container amelie-shortcode-chat amelie-theme-<?php echo esc_attr($atts['theme']); ?>" 
             style="height: <?php echo esc_attr($atts['height']); ?>; width: <?php echo esc_attr($atts['width']); ?>;">
            
            <div class="amelie-chat-header">
                <div class="amelie-avatar">💎</div>
                <div class="amelie-info">
                    <h4><?php _e('Amélie', 'vf-amelie'); ?></h4>
                    <span class="amelie-status"><?php _e('Asistente Virtual', 'vf-amelie'); ?></span>
                </div>
            </div>
            
            <div class="amelie-chat-messages">
                <div class="amelie-message amelie-bot-message">
                    <div class="amelie-message-content">
                        <?php echo esc_html(get_option('amelie_welcome_message', __('¡Hola! ¿En qué puedo ayudarte?', 'vf-amelie'))); ?>
                    </div>
                    <div class="amelie-message-time"><?php echo date('H:i'); ?></div>
                </div>
            </div>
            
            <div class="amelie-chat-input">
                <div class="amelie-input-container">
                    <input type="text" placeholder="<?php _e('Escribe tu mensaje...', 'vf-amelie'); ?>" class="amelie-message-input" />
                    <button type="button" class="amelie-send-button">
                        <span>➤</span>
                    </button>
                </div>
                <div class="amelie-typing-indicator" style="display: none;">
                    <span><?php _e('Amélie está escribiendo...', 'vf-amelie'); ?></span>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render Floating Chat
     */
    public function render_floating_chat() {
        if (!get_option('amelie_chat_enabled', true) || !get_option('amelie_auto_show_chat', true)) {
            return;
        }
        
        $position = get_option('amelie_chat_position', 'bottom-right');
        $theme = get_option('amelie_chat_theme', 'diamond');
        ?>
        <div id="amelie-floating-chat" class="amelie-floating-chat amelie-position-<?php echo esc_attr($position); ?> amelie-theme-<?php echo esc_attr($theme); ?>">
            <!-- Floating Button -->
            <div class="amelie-float-button" id="amelie-toggle-chat">
                <div class="amelie-button-icon">💎</div>
                <div class="amelie-button-text"><?php _e('Chat', 'vf-amelie'); ?></div>
                <div class="amelie-notification-badge" style="display: none;">1</div>
            </div>
            
            <!-- Chat Window -->
            <div class="amelie-chat-window" id="amelie-chat-window" style="display: none;">
                <div class="amelie-chat-header">
                    <div class="amelie-header-content">
                        <div class="amelie-avatar">💎</div>
                        <div class="amelie-info">
                            <h4><?php _e('Amélie', 'vf-amelie'); ?></h4>
                            <span class="amelie-status online"><?php _e('En línea', 'vf-amelie'); ?></span>
                        </div>
                    </div>
                    <button class="amelie-close-chat" id="amelie-close-chat">×</button>
                </div>
                
                <div class="amelie-chat-messages" id="amelie-chat-messages">
                    <div class="amelie-message amelie-bot-message">
                        <div class="amelie-message-content">
                            <?php echo esc_html(get_option('amelie_welcome_message', __('¡Hola! ¿En qué puedo ayudarte?', 'vf-amelie'))); ?>
                        </div>
                        <div class="amelie-message-time"><?php echo date('H:i'); ?></div>
                    </div>
                </div>
                
                <div class="amelie-chat-input">
                    <div class="amelie-input-container">
                        <input type="text" placeholder="<?php _e('Escribe tu mensaje...', 'vf-amelie'); ?>" class="amelie-message-input" id="amelie-message-input" />
                        <button type="button" class="amelie-send-button" id="amelie-send-button">
                            <span>➤</span>
                        </button>
                    </div>
                    <div class="amelie-typing-indicator" id="amelie-typing-indicator" style="display: none;">
                        <div class="amelie-typing-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <span><?php _e('Amélie está escribiendo...', 'vf-amelie'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Initialize AJAX Hooks
     */
    public function init_ajax_hooks() {
        add_action('wp_ajax_amelie_chat', array($this, 'handle_chat_ajax'));
        add_action('wp_ajax_nopriv_amelie_chat', array($this, 'handle_chat_ajax'));
        add_action('wp_ajax_test_amelie_api', array($this, 'test_api_connection'));
    }
    
    /**
     * Handle Chat AJAX
     */
    public function handle_chat_ajax() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'amelie_chat_nonce')) {
            wp_die('Security check failed');
        }
        
        $message = sanitize_text_field($_POST['message']);
        $api_base_url = get_option('amelie_api_base_url', '');
        $api_secret = get_option('amelie_api_secret', '');
        
        if (empty($api_base_url)) {
            wp_send_json_error(__('API no configurada', 'vf-amelie'));
            return;
        }
        
        // Prepare API request
        $api_url = rtrim($api_base_url, '/') . '/api/ai/chat';
        $headers = array(
            'Content-Type' => 'application/json',
        );
        
        // Add API key header if configured (SECURITY: only if secret is set)
        if (!empty($api_secret)) {
            $headers['X-API-KEY'] = $api_secret;
        }
        
        $body = json_encode(array(
            'message' => $message
        ));
        
        // Make API request
        $response = wp_remote_post($api_url, array(
            'timeout' => 30,
            'headers' => $headers,
            'body' => $body,
        ));
        
        if (is_wp_error($response)) {
            wp_send_json_error(__('Error de conexión con la API', 'vf-amelie'));
            return;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        
        if ($response_code !== 200) {
            wp_send_json_error(__('Error en la API (código ' . $response_code . ')', 'vf-amelie'));
            return;
        }
        
        $data = json_decode($response_body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(__('Respuesta inválida de la API', 'vf-amelie'));
            return;
        }
        
        if (!isset($data['reply'])) {
            wp_send_json_error(__('Formato de respuesta incorrecto', 'vf-amelie'));
            return;
        }
        
        wp_send_json_success(array(
            'reply' => sanitize_text_field($data['reply'])
        ));
    }
    
    /**
     * Test API Connection
     */
    public function test_api_connection() {
        if (!wp_verify_nonce($_POST['nonce'], 'test_amelie_api')) {
            wp_send_json_error(__('Security check failed', 'vf-amelie'));
            return;
        }
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Insufficient permissions', 'vf-amelie'));
            return;
        }
        
        $api_base_url = get_option('amelie_api_base_url', '');
        $api_secret = get_option('amelie_api_secret', '');
        
        if (empty($api_base_url)) {
            wp_send_json_error(__('URL de API no configurada', 'vf-amelie'));
            return;
        }
        
        // Test with a simple message
        $api_url = rtrim($api_base_url, '/') . '/api/ai/chat';
        $headers = array(
            'Content-Type' => 'application/json',
        );
        
        if (!empty($api_secret)) {
            $headers['X-API-KEY'] = $api_secret;
        }
        
        $body = json_encode(array(
            'message' => 'Test de conexión'
        ));
        
        $response = wp_remote_post($api_url, array(
            'timeout' => 10,
            'headers' => $headers,
            'body' => $body,
        ));
        
        if (is_wp_error($response)) {
            wp_send_json_error($response->get_error_message());
            return;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            wp_send_json_error(__('Código de respuesta HTTP: ', 'vf-amelie') . $response_code);
            return;
        }
        
        wp_send_json_success(__('Conexión exitosa con la API', 'vf-amelie'));
    }
    
    /**
     * Enqueue Scripts
     */
    public function enqueue_scripts() {
        if (!get_option('amelie_chat_enabled', true)) {
            return;
        }
        
        wp_enqueue_style('amelie-chat-style', VF_AMELIE_PLUGIN_URL . 'assets/amelie.css', array(), VF_AMELIE_VERSION);
        wp_enqueue_script('amelie-chat-script', VF_AMELIE_PLUGIN_URL . 'assets/amelie.js', array('jquery'), VF_AMELIE_VERSION, true);
        
        // Localize script
        wp_localize_script('amelie-chat-script', 'amelieAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('amelie_chat_nonce'),
            'messages' => array(
                'error' => __('Error al enviar mensaje', 'vf-amelie'),
                'typing' => __('Escribiendo...', 'vf-amelie'),
                'retry' => __('Reintentar', 'vf-amelie'),
                'today' => __('Hoy', 'vf-amelie'),
                'yesterday' => __('Ayer', 'vf-amelie'),
            )
        ));
    }
    
    /**
     * Admin Enqueue Scripts
     */
    public function admin_enqueue_scripts() {
        $screen = get_current_screen();
        if ($screen && $screen->id === 'settings_page_amelie-settings') {
            wp_enqueue_style('amelie-admin-style', VF_AMELIE_PLUGIN_URL . 'assets/amelie-admin.css', array(), VF_AMELIE_VERSION);
        }
    }
}

// Initialize the plugin
new VF_Amelie();