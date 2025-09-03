<?php
/**
 * Plugin Name: VF Amélie
 * Plugin URI: https://vane-france.com
 * Description: Plugin de integración de chat con IA Amélie para Vane France - Widget de chat flotante con conexión al backend Flask
 * Version: 1.0.0
 * Author: Vane France Team
 * Text Domain: vf-amelie
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('VF_AMELIE_VERSION', '1.0.0');
define('VF_AMELIE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VF_AMELIE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('VF_AMELIE_PLUGIN_FILE', __FILE__);

/**
 * Main VF Amélie Plugin Class
 */
class VF_Amelie_Plugin {
    
    /**
     * Single instance of the plugin
     */
    private static $instance = null;
    
    /**
     * Get single instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        add_action('init', array($this, 'init'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Initialize the plugin
     */
    public function init() {
        // Load includes
        $this->load_includes();
        
        // Initialize hooks
        $this->init_hooks();
    }
    
    /**
     * Load plugin includes
     */
    private function load_includes() {
        require_once VF_AMELIE_PLUGIN_DIR . 'includes/class-vf-amelie-chat.php';
        require_once VF_AMELIE_PLUGIN_DIR . 'includes/class-vf-amelie-admin.php';
        require_once VF_AMELIE_PLUGIN_DIR . 'includes/class-vf-amelie-api.php';
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Initialize classes
        VF_Amelie_Chat::get_instance();
        VF_Amelie_Admin::get_instance();
        VF_Amelie_API::get_instance();
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // Add shortcode
        add_shortcode('amelie_chat', array($this, 'amelie_chat_shortcode'));
        
        // Add floating chat widget
        add_action('wp_footer', array($this, 'add_floating_chat_widget'));
        
        // AJAX handlers
        add_action('wp_ajax_vf_amelie_chat', array($this, 'ajax_chat'));
        add_action('wp_ajax_nopriv_vf_amelie_chat', array($this, 'ajax_chat'));
    }
    
    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain('vf-amelie', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Set default options
        $default_options = array(
            'vf_amelie_api_base_url' => '',
            'vf_amelie_api_secret' => '',
            'vf_amelie_show_floating' => true,
            'vf_amelie_widget_position' => 'bottom-right',
            'vf_amelie_widget_color' => '#002395',
            'vf_amelie_chat_title' => 'Amélie - Asistente Virtual',
            'vf_amelie_welcome_message' => '¡Hola! Soy Amélie, tu asistente virtual de Vane France. ¿En qué puedo ayudarte hoy?',
            'vf_amelie_enable_analytics' => true,
        );
        
        foreach ($default_options as $key => $value) {
            if (get_option($key) === false) {
                update_option($key, $value);
            }
        }
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clean up if needed
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        // Only load on frontend
        if (is_admin()) {
            return;
        }
        
        wp_enqueue_style(
            'vf-amelie-frontend',
            VF_AMELIE_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            VF_AMELIE_VERSION
        );
        
        wp_enqueue_script(
            'vf-amelie-frontend',
            VF_AMELIE_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            VF_AMELIE_VERSION,
            true
        );
        
        // Localize script with settings
        wp_localize_script('vf-amelie-frontend', 'vf_amelie', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('vf_amelie_nonce'),
            'api_base_url' => get_option('vf_amelie_api_base_url', ''),
            'widget_color' => get_option('vf_amelie_widget_color', '#002395'),
            'chat_title' => get_option('vf_amelie_chat_title', 'Amélie - Asistente Virtual'),
            'welcome_message' => get_option('vf_amelie_welcome_message', '¡Hola! Soy Amélie, tu asistente virtual de Vane France. ¿En qué puedo ayudarte hoy?'),
            'strings' => array(
                'type_message' => __('Escribe tu mensaje...', 'vf-amelie'),
                'send' => __('Enviar', 'vf-amelie'),
                'connecting' => __('Conectando...', 'vf-amelie'),
                'error_occurred' => __('Ocurrió un error. Por favor, intenta nuevamente.', 'vf-amelie'),
                'network_error' => __('Error de conexión. Verifica tu internet.', 'vf-amelie'),
                'minimize' => __('Minimizar', 'vf-amelie'),
                'close' => __('Cerrar', 'vf-amelie'),
            )
        ));
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on our admin pages
        if (strpos($hook, 'vane-france-amelie') === false) {
            return;
        }
        
        wp_enqueue_style(
            'vf-amelie-admin',
            VF_AMELIE_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            VF_AMELIE_VERSION
        );
        
        wp_enqueue_script(
            'vf-amelie-admin',
            VF_AMELIE_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            VF_AMELIE_VERSION,
            true
        );
        
        wp_localize_script('vf-amelie-admin', 'vf_amelie_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('vf_amelie_admin_nonce'),
        ));
    }
    
    /**
     * Amélie chat shortcode
     */
    public function amelie_chat_shortcode($atts) {
        $atts = shortcode_atts(array(
            'width' => '100%',
            'height' => '400px',
            'title' => get_option('vf_amelie_chat_title', 'Amélie - Asistente Virtual'),
            'welcome' => get_option('vf_amelie_welcome_message', '¡Hola! Soy Amélie, tu asistente virtual de Vane France. ¿En qué puedo ayudarte hoy?'),
            'position' => 'embedded'
        ), $atts, 'amelie_chat');
        
        $chat_id = 'vf-amelie-chat-' . uniqid();
        
        ob_start();
        ?>
        <div id="<?php echo esc_attr($chat_id); ?>" class="vf-amelie-chat-embed" 
             style="width: <?php echo esc_attr($atts['width']); ?>; height: <?php echo esc_attr($atts['height']); ?>;"
             data-title="<?php echo esc_attr($atts['title']); ?>"
             data-welcome="<?php echo esc_attr($atts['welcome']); ?>">
            
            <div class="vf-amelie-chat-header">
                <div class="vf-amelie-avatar">
                    <i class="fas fa-gem"></i>
                </div>
                <div class="vf-amelie-header-info">
                    <h4><?php echo esc_html($atts['title']); ?></h4>
                    <span class="vf-amelie-status">En línea</span>
                </div>
            </div>
            
            <div class="vf-amelie-chat-messages">
                <div class="vf-amelie-message vf-amelie-bot-message">
                    <div class="vf-amelie-message-avatar">
                        <i class="fas fa-gem"></i>
                    </div>
                    <div class="vf-amelie-message-content">
                        <p><?php echo esc_html($atts['welcome']); ?></p>
                        <span class="vf-amelie-message-time"><?php echo current_time('H:i'); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="vf-amelie-chat-input">
                <div class="vf-amelie-input-group">
                    <input type="text" 
                           class="vf-amelie-message-input" 
                           placeholder="<?php _e('Escribe tu mensaje...', 'vf-amelie'); ?>"
                           maxlength="500">
                    <button type="button" class="vf-amelie-send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <div class="vf-amelie-typing-indicator" style="display: none;">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span>Amélie está escribiendo...</span>
                </div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            VF_Amelie.initEmbeddedChat('<?php echo esc_js($chat_id); ?>');
        });
        </script>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Add floating chat widget
     */
    public function add_floating_chat_widget() {
        if (!get_option('vf_amelie_show_floating', true)) {
            return;
        }
        
        $position = get_option('vf_amelie_widget_position', 'bottom-right');
        $color = get_option('vf_amelie_widget_color', '#002395');
        $title = get_option('vf_amelie_chat_title', 'Amélie - Asistente Virtual');
        $welcome = get_option('vf_amelie_welcome_message', '¡Hola! Soy Amélie, tu asistente virtual de Vane France. ¿En qué puedo ayudarte hoy?');
        
        ?>
        <!-- VF Amélie Floating Chat Widget -->
        <div id="vf-amelie-floating-widget" class="vf-amelie-widget-<?php echo esc_attr($position); ?>" style="--widget-color: <?php echo esc_attr($color); ?>;">
            <!-- Chat Button -->
            <div class="vf-amelie-chat-button" title="<?php echo esc_attr($title); ?>">
                <div class="vf-amelie-button-icon">
                    <i class="fas fa-gem"></i>
                </div>
                <div class="vf-amelie-button-pulse"></div>
                <div class="vf-amelie-notification-badge" style="display: none;">1</div>
            </div>
            
            <!-- Chat Window -->
            <div class="vf-amelie-chat-window" style="display: none;">
                <div class="vf-amelie-chat-header">
                    <div class="vf-amelie-header-left">
                        <div class="vf-amelie-avatar">
                            <i class="fas fa-gem"></i>
                        </div>
                        <div class="vf-amelie-header-info">
                            <h4><?php echo esc_html($title); ?></h4>
                            <span class="vf-amelie-status">En línea</span>
                        </div>
                    </div>
                    <div class="vf-amelie-header-actions">
                        <button type="button" class="vf-amelie-minimize-btn" title="<?php _e('Minimizar', 'vf-amelie'); ?>">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="vf-amelie-close-btn" title="<?php _e('Cerrar', 'vf-amelie'); ?>">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <div class="vf-amelie-chat-messages">
                    <div class="vf-amelie-message vf-amelie-bot-message">
                        <div class="vf-amelie-message-avatar">
                            <i class="fas fa-gem"></i>
                        </div>
                        <div class="vf-amelie-message-content">
                            <p><?php echo esc_html($welcome); ?></p>
                            <span class="vf-amelie-message-time"><?php echo current_time('H:i'); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="vf-amelie-chat-input">
                    <div class="vf-amelie-input-group">
                        <input type="text" 
                               class="vf-amelie-message-input" 
                               placeholder="<?php _e('Escribe tu mensaje...', 'vf-amelie'); ?>"
                               maxlength="500">
                        <button type="button" class="vf-amelie-send-btn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <div class="vf-amelie-typing-indicator" style="display: none;">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span>Amélie está escribiendo...</span>
                    </div>
                </div>
                
                <div class="vf-amelie-chat-footer">
                    <small>Powered by <strong>Vane France</strong></small>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX handler for chat
     */
    public function ajax_chat() {
        check_ajax_referer('vf_amelie_nonce', 'nonce');
        
        $message = sanitize_text_field($_POST['message']);
        if (empty($message)) {
            wp_send_json_error(__('Mensaje vacío', 'vf-amelie'));
        }
        
        // Get API settings
        $api_base_url = get_option('vf_amelie_api_base_url', '');
        $api_secret = get_option('vf_amelie_api_secret', '');
        
        if (empty($api_base_url)) {
            wp_send_json_error(__('API no configurada', 'vf-amelie'));
        }
        
        // Send request to Flask backend
        $api = VF_Amelie_API::get_instance();
        $response = $api->send_chat_message($message, $api_base_url, $api_secret);
        
        if (is_wp_error($response)) {
            wp_send_json_error($response->get_error_message());
        }
        
        // Track analytics if enabled
        if (get_option('vf_amelie_enable_analytics', true)) {
            $this->track_chat_interaction($message, $response['reply']);
        }
        
        wp_send_json_success($response);
    }
    
    /**
     * Track chat interaction for analytics
     */
    private function track_chat_interaction($user_message, $bot_reply) {
        $interactions = get_option('vf_amelie_chat_analytics', array());
        
        $interaction = array(
            'timestamp' => current_time('mysql'),
            'user_message' => substr($user_message, 0, 100), // Limit length for privacy
            'bot_reply' => substr($bot_reply, 0, 100),
            'user_id' => get_current_user_id(),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 200)
        );
        
        $interactions[] = $interaction;
        
        // Keep only last 1000 interactions
        if (count($interactions) > 1000) {
            $interactions = array_slice($interactions, -1000);
        }
        
        update_option('vf_amelie_chat_analytics', $interactions);
    }
    
    /**
     * Get chat analytics
     */
    public static function get_chat_analytics() {
        $interactions = get_option('vf_amelie_chat_analytics', array());
        
        $total_chats = count($interactions);
        $today_chats = 0;
        $week_chats = 0;
        $unique_users = array();
        
        $today = date('Y-m-d');
        $week_ago = date('Y-m-d', strtotime('-7 days'));
        
        foreach ($interactions as $interaction) {
            $date = date('Y-m-d', strtotime($interaction['timestamp']));
            
            if ($date === $today) {
                $today_chats++;
            }
            
            if ($date >= $week_ago) {
                $week_chats++;
            }
            
            if ($interaction['user_id'] > 0) {
                $unique_users[] = $interaction['user_id'];
            }
        }
        
        $unique_users = array_unique($unique_users);
        
        return array(
            'total_chats' => $total_chats,
            'today_chats' => $today_chats,
            'week_chats' => $week_chats,
            'unique_users' => count($unique_users),
            'avg_chats_per_day' => $total_chats > 0 ? round($total_chats / max(1, count(array_unique(array_map(function($i) { return date('Y-m-d', strtotime($i['timestamp'])); }, $interactions)))), 2) : 0
        );
    }
}

// Initialize the plugin
VF_Amelie_Plugin::get_instance();