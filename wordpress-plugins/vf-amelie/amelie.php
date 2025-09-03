<?php
/**
 * Plugin Name: VF Amélie
 * Plugin URI: https://vane-france.com
 * Description: AI chat integration for Vane France with diamond-themed floating chat widget and shortcode support.
 * Version: 1.0.0
 * Author: Vane France
 * Text Domain: vf-amelie
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('VF_AMELIE_VERSION', '1.0.0');
define('VF_AMELIE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('VF_AMELIE_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Main VF Amélie Plugin Class
 */
class VF_Amelie_Plugin {
    
    /**
     * Single instance of the plugin
     */
    private static $instance = null;
    
    /**
     * Get instance
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
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Admin functionality
        if (is_admin()) {
            add_action('admin_menu', array($this, 'admin_menu'));
            add_action('admin_init', array($this, 'admin_init'));
        }
        
        // Frontend functionality
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'render_floating_chat'));
        add_shortcode('amelie_chat', array($this, 'chat_shortcode'));
        
        // AJAX handlers (both admin and frontend)
        add_action('wp_ajax_amelie_chat', array($this, 'ajax_chat_handler'));
        add_action('wp_ajax_nopriv_amelie_chat', array($this, 'ajax_chat_handler'));
    }
    
    /**
     * Load text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain('vf-amelie', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Set default options
        $this->set_default_options();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Cleanup if needed
    }
    
    /**
     * Set default options
     */
    private function set_default_options() {
        $defaults = array(
            'amelie_api_base_url' => '',
            'amelie_api_secret' => '',
            'amelie_show_floating' => true,
            'amelie_chat_title' => __('Amélie - Asistente Virtual', 'vf-amelie'),
            'amelie_welcome_message' => __('¡Hola! Soy Amélie, tu asistente virtual de Vane France. ¿En qué puedo ayudarte hoy?', 'vf-amelie'),
            'amelie_placeholder_text' => __('Escribe tu pregunta aquí...', 'vf-amelie'),
            'amelie_button_position' => 'bottom-right',
            'amelie_primary_color' => '#002395',
            'amelie_accent_color' => '#ed2939',
        );
        
        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }
    
    /**
     * Admin menu
     */
    public function admin_menu() {
        // Add to Vane France menu if vf-extras is active
        if (function_exists('VF_Extras_Plugin')) {
            add_submenu_page(
                'vane-france',
                __('Amélie Chat', 'vf-amelie'),
                __('Amélie Chat', 'vf-amelie'),
                'manage_options',
                'amelie-settings',
                array($this, 'admin_page')
            );
        } else {
            // Create own menu if vf-extras is not active
            add_menu_page(
                __('Amélie Chat', 'vf-amelie'),
                __('Amélie Chat', 'vf-amelie'),
                'manage_options',
                'amelie-settings',
                array($this, 'admin_page'),
                'dashicons-format-chat',
                35
            );
        }
    }
    
    /**
     * Admin init
     */
    public function admin_init() {
        // Register settings
        register_setting('amelie_settings', 'amelie_api_base_url', array(
            'sanitize_callback' => 'esc_url_raw'
        ));
        
        register_setting('amelie_settings', 'amelie_api_secret', array(
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        register_setting('amelie_settings', 'amelie_show_floating', array(
            'sanitize_callback' => 'rest_sanitize_boolean'
        ));
        
        register_setting('amelie_settings', 'amelie_chat_title', array(
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        register_setting('amelie_settings', 'amelie_welcome_message', array(
            'sanitize_callback' => 'sanitize_textarea_field'
        ));
        
        register_setting('amelie_settings', 'amelie_placeholder_text', array(
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        register_setting('amelie_settings', 'amelie_button_position', array(
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        register_setting('amelie_settings', 'amelie_primary_color', array(
            'sanitize_callback' => 'sanitize_hex_color'
        ));
        
        register_setting('amelie_settings', 'amelie_accent_color', array(
            'sanitize_callback' => 'sanitize_hex_color'
        ));
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style('vf-amelie', VF_AMELIE_PLUGIN_URL . 'assets/amelie.css', array(), VF_AMELIE_VERSION);
        wp_enqueue_script('vf-amelie', VF_AMELIE_PLUGIN_URL . 'assets/amelie.js', array('jquery'), VF_AMELIE_VERSION, true);
        
        // Localize script
        wp_localize_script('vf-amelie', 'amelieChat', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('amelie_chat_nonce'),
            'apiBaseUrl' => get_option('amelie_api_base_url', ''),
            'welcomeMessage' => get_option('amelie_welcome_message', ''),
            'placeholderText' => get_option('amelie_placeholder_text', ''),
            'chatTitle' => get_option('amelie_chat_title', ''),
            'primaryColor' => get_option('amelie_primary_color', '#002395'),
            'accentColor' => get_option('amelie_accent_color', '#ed2939'),
            'strings' => array(
                'typing' => __('Amélie está escribiendo...', 'vf-amelie'),
                'error' => __('Lo siento, no pude procesar tu mensaje. Inténtalo de nuevo.', 'vf-amelie'),
                'networkError' => __('Error de conexión. Verifica tu conexión a internet.', 'vf-amelie'),
                'send' => __('Enviar', 'vf-amelie'),
                'minimize' => __('Minimizar', 'vf-amelie'),
                'close' => __('Cerrar', 'vf-amelie'),
                'newChat' => __('Nueva conversación', 'vf-amelie'),
            )
        ));
    }
    
    /**
     * Admin page
     */
    public function admin_page() {
        if (isset($_POST['submit'])) {
            echo '<div class="notice notice-success"><p>' . __('Configuración guardada.', 'vf-amelie') . '</p></div>';
        }
        ?>
        <div class="wrap">
            <h1><?php _e('Configuración de Amélie Chat', 'vf-amelie'); ?></h1>
            
            <div class="amelie-admin-container">
                <div class="amelie-admin-main">
                    <form method="post" action="options.php">
                        <?php
                        settings_fields('amelie_settings');
                        do_settings_sections('amelie_settings');
                        ?>
                        
                        <div class="amelie-settings-section">
                            <h2><?php _e('Configuración de API', 'vf-amelie'); ?></h2>
                            <p class="description"><?php _e('Configura la conexión con el backend de Amélie para el chat con IA.', 'vf-amelie'); ?></p>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="amelie_api_base_url"><?php _e('URL Base de la API', 'vf-amelie'); ?></label>
                                    </th>
                                    <td>
                                        <input type="url" 
                                               id="amelie_api_base_url" 
                                               name="amelie_api_base_url" 
                                               value="<?php echo esc_attr(get_option('amelie_api_base_url')); ?>" 
                                               class="regular-text"
                                               placeholder="https://tu-api.com" />
                                        <p class="description"><?php _e('URL base del backend de Amélie (sin la ruta del endpoint)', 'vf-amelie'); ?></p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="amelie_api_secret"><?php _e('Clave Secreta de la API', 'vf-amelie'); ?></label>
                                    </th>
                                    <td>
                                        <input type="password" 
                                               id="amelie_api_secret" 
                                               name="amelie_api_secret" 
                                               value="<?php echo esc_attr(get_option('amelie_api_secret')); ?>" 
                                               class="regular-text" />
                                        <p class="description"><?php _e('Clave secreta para autenticación (opcional). Se enviará como header X-API-KEY.', 'vf-amelie'); ?></p>
                                        <p class="description"><strong><?php _e('IMPORTANTE:', 'vf-amelie'); ?></strong> <?php _e('Esta clave se almacena de forma segura y nunca se expone en el frontend.', 'vf-amelie'); ?></p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="amelie-settings-section">
                            <h2><?php _e('Configuración del Chat', 'vf-amelie'); ?></h2>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="amelie_show_floating"><?php _e('Mostrar Chat Flotante', 'vf-amelie'); ?></label>
                                    </th>
                                    <td>
                                        <input type="checkbox" 
                                               id="amelie_show_floating" 
                                               name="amelie_show_floating" 
                                               value="1" 
                                               <?php checked(get_option('amelie_show_floating', true)); ?> />
                                        <p class="description"><?php _e('Mostrar automáticamente el botón de chat flotante en todo el sitio', 'vf-amelie'); ?></p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="amelie_chat_title"><?php _e('Título del Chat', 'vf-amelie'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" 
                                               id="amelie_chat_title" 
                                               name="amelie_chat_title" 
                                               value="<?php echo esc_attr(get_option('amelie_chat_title')); ?>" 
                                               class="regular-text" />
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="amelie_welcome_message"><?php _e('Mensaje de Bienvenida', 'vf-amelie'); ?></label>
                                    </th>
                                    <td>
                                        <textarea id="amelie_welcome_message" 
                                                  name="amelie_welcome_message" 
                                                  rows="3" 
                                                  class="large-text"><?php echo esc_textarea(get_option('amelie_welcome_message')); ?></textarea>
                                        <p class="description"><?php _e('Mensaje que Amélie mostrará al iniciar una conversación', 'vf-amelie'); ?></p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="amelie_placeholder_text"><?php _e('Texto del Placeholder', 'vf-amelie'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" 
                                               id="amelie_placeholder_text" 
                                               name="amelie_placeholder_text" 
                                               value="<?php echo esc_attr(get_option('amelie_placeholder_text')); ?>" 
                                               class="regular-text" />
                                        <p class="description"><?php _e('Texto mostrado en el campo de entrada del chat', 'vf-amelie'); ?></p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="amelie_button_position"><?php _e('Posición del Botón', 'vf-amelie'); ?></label>
                                    </th>
                                    <td>
                                        <select id="amelie_button_position" name="amelie_button_position">
                                            <option value="bottom-right" <?php selected(get_option('amelie_button_position'), 'bottom-right'); ?>><?php _e('Abajo Derecha', 'vf-amelie'); ?></option>
                                            <option value="bottom-left" <?php selected(get_option('amelie_button_position'), 'bottom-left'); ?>><?php _e('Abajo Izquierda', 'vf-amelie'); ?></option>
                                            <option value="top-right" <?php selected(get_option('amelie_button_position'), 'top-right'); ?>><?php _e('Arriba Derecha', 'vf-amelie'); ?></option>
                                            <option value="top-left" <?php selected(get_option('amelie_button_position'), 'top-left'); ?>><?php _e('Arriba Izquierda', 'vf-amelie'); ?></option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="amelie-settings-section">
                            <h2><?php _e('Personalización Visual', 'vf-amelie'); ?></h2>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="amelie_primary_color"><?php _e('Color Primario', 'vf-amelie'); ?></label>
                                    </th>
                                    <td>
                                        <input type="color" 
                                               id="amelie_primary_color" 
                                               name="amelie_primary_color" 
                                               value="<?php echo esc_attr(get_option('amelie_primary_color')); ?>" />
                                        <p class="description"><?php _e('Color principal del chat (por defecto: azul marino de Vane France)', 'vf-amelie'); ?></p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="amelie_accent_color"><?php _e('Color de Acento', 'vf-amelie'); ?></label>
                                    </th>
                                    <td>
                                        <input type="color" 
                                               id="amelie_accent_color" 
                                               name="amelie_accent_color" 
                                               value="<?php echo esc_attr(get_option('amelie_accent_color')); ?>" />
                                        <p class="description"><?php _e('Color de acento para elementos destacados (por defecto: rojo de Vane France)', 'vf-amelie'); ?></p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <?php submit_button(); ?>
                    </form>
                </div>
                
                <div class="amelie-admin-sidebar">
                    <div class="amelie-info-box">
                        <h3><?php _e('Shortcode', 'vf-amelie'); ?></h3>
                        <p><?php _e('Usa este shortcode para insertar el chat en cualquier página o entrada:', 'vf-amelie'); ?></p>
                        <code>[amelie_chat]</code>
                        
                        <h4><?php _e('Parámetros opcionales:', 'vf-amelie'); ?></h4>
                        <ul>
                            <li><code>title</code> - <?php _e('Título personalizado del chat', 'vf-amelie'); ?></li>
                            <li><code>height</code> - <?php _e('Altura del chat (por defecto: 400px)', 'vf-amelie'); ?></li>
                            <li><code>width</code> - <?php _e('Ancho del chat (por defecto: 100%)', 'vf-amelie'); ?></li>
                        </ul>
                        
                        <h4><?php _e('Ejemplo:', 'vf-amelie'); ?></h4>
                        <code>[amelie_chat title="Consultas Especiales" height="500px"]</code>
                    </div>
                    
                    <div class="amelie-info-box">
                        <h3><?php _e('Configuración de API', 'vf-amelie'); ?></h3>
                        <p><?php _e('Para conectar con el backend de Amélie:', 'vf-amelie'); ?></p>
                        <ol>
                            <li><?php _e('Ingresa la URL base de tu API (ej: https://tu-api.com)', 'vf-amelie'); ?></li>
                            <li><?php _e('Opcionalmente, ingresa la clave secreta para autenticación', 'vf-amelie'); ?></li>
                            <li><?php _e('El plugin enviará POST a [URL]/api/ai/chat', 'vf-amelie'); ?></li>
                            <li><?php _e('El JSON enviado será: {"message": "texto del usuario"}', 'vf-amelie'); ?></li>
                        </ol>
                        
                        <h4><?php _e('Seguridad:', 'vf-amelie'); ?></h4>
                        <p><?php _e('Las claves secretas nunca se exponen en el frontend. Se envían solo como headers del servidor.', 'vf-amelie'); ?></p>
                    </div>
                    
                    <div class="amelie-info-box">
                        <h3><?php _e('Soporte', 'vf-amelie'); ?></h3>
                        <p><?php _e('Para soporte técnico:', 'vf-amelie'); ?></p>
                        <ul>
                            <li><?php _e('Teléfono: 319 3605666', 'vf-amelie'); ?></li>
                            <li><?php _e('Horario: Lun-Sáb 9AM-7PM', 'vf-amelie'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
        .amelie-admin-container {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .amelie-admin-main {
            flex: 2;
        }
        
        .amelie-admin-sidebar {
            flex: 1;
            max-width: 300px;
        }
        
        .amelie-settings-section {
            background: #fff;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .amelie-settings-section h2 {
            margin-top: 0;
            margin-bottom: 1rem;
            color: #002395;
            border-bottom: 2px solid #ed2939;
            padding-bottom: 0.5rem;
        }
        
        .amelie-info-box {
            background: #f9f9f9;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #002395;
            border-radius: 4px;
        }
        
        .amelie-info-box h3,
        .amelie-info-box h4 {
            margin-top: 0;
            color: #002395;
        }
        
        .amelie-info-box code {
            background: #002395;
            color: #fff;
            padding: 0.2rem 0.4rem;
            border-radius: 3px;
            font-size: 0.9rem;
        }
        
        .amelie-info-box ul,
        .amelie-info-box ol {
            margin: 0.5rem 0;
            padding-left: 1.5rem;
        }
        
        .amelie-info-box li {
            margin-bottom: 0.25rem;
        }
        
        @media (max-width: 768px) {
            .amelie-admin-container {
                flex-direction: column;
            }
            
            .amelie-admin-sidebar {
                max-width: none;
            }
        }
        </style>
        <?php
    }
    
    /**
     * Render floating chat widget
     */
    public function render_floating_chat() {
        // Only show if enabled and API is configured
        if (!get_option('amelie_show_floating') || !get_option('amelie_api_base_url')) {
            return;
        }
        
        $position = get_option('amelie_button_position', 'bottom-right');
        ?>
        <div id="amelie-floating-chat" class="amelie-floating-chat <?php echo esc_attr($position); ?>">
            <div class="amelie-chat-button" id="amelie-chat-toggle">
                <div class="amelie-diamond-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2L8 8h8l-4-6zm-6.5 7L1 15l4.5-6zm0 0h13L14 15 10 9h-4.5zM10 15l2 7 2-7h-4zm8.5-6L23 15l-4.5-6z"/>
                    </svg>
                </div>
                <div class="amelie-chat-notification" id="amelie-chat-notification"></div>
            </div>
            
            <div class="amelie-chat-window" id="amelie-chat-window">
                <div class="amelie-chat-header">
                    <div class="amelie-chat-avatar">
                        <div class="amelie-diamond-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2L8 8h8l-4-6zm-6.5 7L1 15l4.5-6zm0 0h13L14 15 10 9h-4.5zM10 15l2 7 2-7h-4zm8.5-6L23 15l-4.5-6z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="amelie-chat-title">
                        <h4><?php echo esc_html(get_option('amelie_chat_title')); ?></h4>
                        <span class="amelie-status online"><?php _e('En línea', 'vf-amelie'); ?></span>
                    </div>
                    <div class="amelie-chat-controls">
                        <button class="amelie-minimize-btn" id="amelie-minimize">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 14H4v-4h16"/>
                            </svg>
                        </button>
                        <button class="amelie-close-btn" id="amelie-close">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="amelie-chat-messages" id="amelie-chat-messages">
                    <div class="amelie-message amelie-bot-message">
                        <div class="amelie-message-avatar">
                            <div class="amelie-diamond-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2L8 8h8l-4-6zm-6.5 7L1 15l4.5-6zm0 0h13L14 15 10 9h-4.5zM10 15l2 7 2-7h-4zm8.5-6L23 15l-4.5-6z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="amelie-message-content">
                            <?php echo esc_html(get_option('amelie_welcome_message')); ?>
                        </div>
                        <div class="amelie-message-time">
                            <?php echo current_time('H:i'); ?>
                        </div>
                    </div>
                </div>
                
                <div class="amelie-chat-input">
                    <div class="amelie-input-container">
                        <textarea id="amelie-message-input" 
                                  placeholder="<?php echo esc_attr(get_option('amelie_placeholder_text')); ?>"
                                  rows="1"></textarea>
                        <button id="amelie-send-btn" class="amelie-send-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="amelie-typing-indicator" id="amelie-typing" style="display: none;">
                        <div class="amelie-typing-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <span class="amelie-typing-text"><?php _e('Amélie está escribiendo...', 'vf-amelie'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Chat shortcode
     */
    public function chat_shortcode($atts) {
        $atts = shortcode_atts(array(
            'title' => get_option('amelie_chat_title'),
            'height' => '400px',
            'width' => '100%',
        ), $atts);
        
        static $shortcode_id = 0;
        $shortcode_id++;
        
        ob_start();
        ?>
        <div class="amelie-embedded-chat" 
             id="amelie-embedded-<?php echo $shortcode_id; ?>"
             style="width: <?php echo esc_attr($atts['width']); ?>; height: <?php echo esc_attr($atts['height']); ?>;">
            
            <div class="amelie-embedded-header">
                <div class="amelie-chat-avatar">
                    <div class="amelie-diamond-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L8 8h8l-4-6zm-6.5 7L1 15l4.5-6zm0 0h13L14 15 10 9h-4.5zM10 15l2 7 2-7h-4zm8.5-6L23 15l-4.5-6z"/>
                        </svg>
                    </div>
                </div>
                <h4><?php echo esc_html($atts['title']); ?></h4>
                <button class="amelie-new-chat-btn" onclick="amelieNewChat('<?php echo $shortcode_id; ?>')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                    </svg>
                </button>
            </div>
            
            <div class="amelie-embedded-messages" id="amelie-embedded-messages-<?php echo $shortcode_id; ?>">
                <div class="amelie-message amelie-bot-message">
                    <div class="amelie-message-avatar">
                        <div class="amelie-diamond-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2L8 8h8l-4-6zm-6.5 7L1 15l4.5-6zm0 0h13L14 15 10 9h-4.5zM10 15l2 7 2-7h-4zm8.5-6L23 15l-4.5-6z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="amelie-message-content">
                        <?php echo esc_html(get_option('amelie_welcome_message')); ?>
                    </div>
                    <div class="amelie-message-time">
                        <?php echo current_time('H:i'); ?>
                    </div>
                </div>
            </div>
            
            <div class="amelie-embedded-input">
                <div class="amelie-input-container">
                    <textarea id="amelie-embedded-input-<?php echo $shortcode_id; ?>" 
                              placeholder="<?php echo esc_attr(get_option('amelie_placeholder_text')); ?>"
                              rows="1"></textarea>
                    <button onclick="amelieSendMessage('<?php echo $shortcode_id; ?>')" class="amelie-send-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/>
                        </svg>
                    </button>
                </div>
                <div class="amelie-typing-indicator" id="amelie-embedded-typing-<?php echo $shortcode_id; ?>" style="display: none;">
                    <div class="amelie-typing-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <span class="amelie-typing-text"><?php _e('Amélie está escribiendo...', 'vf-amelie'); ?></span>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * AJAX chat handler
     */
    public function ajax_chat_handler() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'amelie_chat_nonce')) {
            wp_send_json_error(__('Sesión expirada. Recarga la página.', 'vf-amelie'));
        }
        
        $message = sanitize_textarea_field($_POST['message']);
        
        if (empty($message)) {
            wp_send_json_error(__('El mensaje no puede estar vacío.', 'vf-amelie'));
        }
        
        $api_base_url = get_option('amelie_api_base_url');
        
        if (empty($api_base_url)) {
            wp_send_json_error(__('El chat no está configurado correctamente.', 'vf-amelie'));
        }
        
        // Prepare API request
        $api_url = rtrim($api_base_url, '/') . '/api/ai/chat';
        $api_secret = get_option('amelie_api_secret');
        
        $headers = array(
            'Content-Type' => 'application/json',
        );
        
        // Add API key header if secret is provided
        if (!empty($api_secret)) {
            $headers['X-API-KEY'] = $api_secret;
        }
        
        $body = json_encode(array(
            'message' => $message
        ));
        
        // Make API request
        $response = wp_remote_post($api_url, array(
            'headers' => $headers,
            'body' => $body,
            'timeout' => 30,
        ));
        
        // Handle API response
        if (is_wp_error($response)) {
            wp_send_json_error(__('Error de conexión con el servidor.', 'vf-amelie'));
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        
        if ($response_code !== 200) {
            wp_send_json_error(__('El servidor respondió con un error.', 'vf-amelie'));
        }
        
        $data = json_decode($response_body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(__('Respuesta del servidor inválida.', 'vf-amelie'));
        }
        
        // Extract response message
        $bot_response = '';
        if (isset($data['response'])) {
            $bot_response = $data['response'];
        } elseif (isset($data['message'])) {
            $bot_response = $data['message'];
        } elseif (isset($data['reply'])) {
            $bot_response = $data['reply'];
        } else {
            $bot_response = __('Lo siento, no pude generar una respuesta en este momento.', 'vf-amelie');
        }
        
        wp_send_json_success(array(
            'message' => sanitize_textarea_field($bot_response),
            'timestamp' => current_time('H:i')
        ));
    }
}

// Initialize plugin
VF_Amelie_Plugin::get_instance();