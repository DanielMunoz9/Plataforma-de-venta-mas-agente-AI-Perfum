<?php
/**
 * Plugin Name: VF Extras
 * Plugin URI: https://vane-france.com
 * Description: Plugin de funcionalidades adicionales para Vane France - Incluye reportes, ofertas, gestión de stock y programa de regalos
 * Version: 1.0.0
 * Author: Vane France Team
 * Text Domain: vf-extras
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('VF_EXTRAS_VERSION', '1.0.0');
define('VF_EXTRAS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VF_EXTRAS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('VF_EXTRAS_PLUGIN_FILE', __FILE__);

/**
 * Main VF Extras Plugin Class
 */
class VF_Extras_Plugin {
    
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
        
        // Initialize components
        $this->init_hooks();
        
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }
        
        // Initialize WooCommerce features
        $this->init_woocommerce_features();
    }
    
    /**
     * Load plugin includes
     */
    private function load_includes() {
        require_once VF_EXTRAS_PLUGIN_DIR . 'includes/class-vf-offers.php';
        require_once VF_EXTRAS_PLUGIN_DIR . 'includes/class-vf-reports.php';
        require_once VF_EXTRAS_PLUGIN_DIR . 'includes/class-vf-gifts.php';
        require_once VF_EXTRAS_PLUGIN_DIR . 'includes/class-vf-roles.php';
        require_once VF_EXTRAS_PLUGIN_DIR . 'includes/class-vf-stock.php';
        require_once VF_EXTRAS_PLUGIN_DIR . 'admin/class-vf-admin.php';
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Initialize classes
        VF_Offers::get_instance();
        VF_Reports::get_instance();
        VF_Gifts::get_instance();
        VF_Roles::get_instance();
        VF_Stock::get_instance();
        VF_Admin::get_instance();
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // AJAX handlers
        add_action('wp_ajax_vf_update_stock', array($this, 'ajax_update_stock'));
        add_action('wp_ajax_vf_get_reports_data', array($this, 'ajax_get_reports_data'));
    }
    
    /**
     * Initialize WooCommerce features
     */
    private function init_woocommerce_features() {
        // Add WhatsApp support endpoint to My Account
        add_action('init', array($this, 'add_whatsapp_endpoint'));
        add_filter('woocommerce_account_menu_items', array($this, 'add_whatsapp_menu_item'));
        add_action('woocommerce_account_whatsapp-support_endpoint', array($this, 'whatsapp_support_content'));
        
        // Gift program hooks
        add_action('woocommerce_cart_calculate_fees', array($this, 'add_gift_to_cart'));
    }
    
    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain('vf-extras', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create offers post type
        VF_Offers::register_post_type();
        
        // Create distribuidor role
        VF_Roles::create_distribuidor_role();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Set default options
        $default_options = array(
            'vf_whatsapp_number' => '3193605666',
            'vf_instagram_url' => '',
            'vf_facebook_url' => '',
            'vf_tiktok_url' => '',
            'vf_gift_min_amount' => 100000,
            'vf_gift_product_id' => 0,
            'vf_gift_required_role' => '',
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
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'vf-extras-frontend',
            VF_EXTRAS_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            VF_EXTRAS_VERSION,
            true
        );
        
        wp_enqueue_style(
            'vf-extras-frontend',
            VF_EXTRAS_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            VF_EXTRAS_VERSION
        );
        
        // Localize script
        wp_localize_script('vf-extras-frontend', 'vf_extras_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('vf_extras_nonce'),
        ));
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on our admin pages
        if (strpos($hook, 'vane-france') === false) {
            return;
        }
        
        wp_enqueue_script(
            'vf-extras-admin',
            VF_EXTRAS_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'chart-js'),
            VF_EXTRAS_VERSION,
            true
        );
        
        wp_enqueue_style(
            'vf-extras-admin',
            VF_EXTRAS_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            VF_EXTRAS_VERSION
        );
        
        // Enqueue Chart.js
        wp_enqueue_script(
            'chart-js',
            'https://cdn.jsdelivr.net/npm/chart.js',
            array(),
            '3.9.1',
            true
        );
        
        // Localize script
        wp_localize_script('vf-extras-admin', 'vf_extras_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('vf_extras_admin_nonce'),
        ));
    }
    
    /**
     * Add WhatsApp support endpoint
     */
    public function add_whatsapp_endpoint() {
        add_rewrite_endpoint('whatsapp-support', EP_ROOT | EP_PAGES);
    }
    
    /**
     * Add WhatsApp menu item to My Account
     */
    public function add_whatsapp_menu_item($items) {
        // Insert before logout
        $logout = $items['customer-logout'];
        unset($items['customer-logout']);
        
        $items['whatsapp-support'] = __('Soporte Técnico', 'vf-extras');
        $items['customer-logout'] = $logout;
        
        return $items;
    }
    
    /**
     * WhatsApp support content
     */
    public function whatsapp_support_content() {
        $whatsapp_number = get_option('vf_whatsapp_number', '3193605666');
        $current_user = wp_get_current_user();
        $message = urlencode("Hola, necesito soporte técnico. Usuario: {$current_user->display_name}");
        
        ?>
        <div class="vf-whatsapp-support">
            <h3><?php _e('Soporte Técnico por WhatsApp', 'vf-extras'); ?></h3>
            
            <div class="support-info">
                <p><?php _e('¿Necesitas ayuda? Nuestro equipo de soporte está disponible por WhatsApp para resolver tus dudas.', 'vf-extras'); ?></p>
                
                <div class="support-hours">
                    <h4><?php _e('Horarios de Atención', 'vf-extras'); ?></h4>
                    <ul>
                        <li><strong><?php _e('Lunes a Sábado:', 'vf-extras'); ?></strong> 9:00 AM - 7:00 PM</li>
                        <li><strong><?php _e('Domingo:', 'vf-extras'); ?></strong> <?php _e('Cerrado', 'vf-extras'); ?></li>
                    </ul>
                </div>
                
                <div class="support-actions">
                    <a href="https://wa.me/<?php echo esc_attr($whatsapp_number); ?>?text=<?php echo $message; ?>" 
                       class="button button-primary whatsapp-btn" 
                       target="_blank">
                        <i class="fab fa-whatsapp"></i>
                        <?php _e('Contactar Soporte', 'vf-extras'); ?>
                    </a>
                </div>
            </div>
            
            <div class="support-topics">
                <h4><?php _e('Temas de Soporte Disponibles', 'vf-extras'); ?></h4>
                <div class="topics-grid">
                    <div class="topic-item">
                        <a href="https://wa.me/<?php echo esc_attr($whatsapp_number); ?>?text=<?php echo urlencode('Consulta sobre pedidos y envíos'); ?>" target="_blank">
                            <i class="fas fa-shipping-fast"></i>
                            <span><?php _e('Pedidos y Envíos', 'vf-extras'); ?></span>
                        </a>
                    </div>
                    <div class="topic-item">
                        <a href="https://wa.me/<?php echo esc_attr($whatsapp_number); ?>?text=<?php echo urlencode('Consulta sobre productos y catálogo'); ?>" target="_blank">
                            <i class="fas fa-box"></i>
                            <span><?php _e('Productos', 'vf-extras'); ?></span>
                        </a>
                    </div>
                    <div class="topic-item">
                        <a href="https://wa.me/<?php echo esc_attr($whatsapp_number); ?>?text=<?php echo urlencode('Consulta sobre facturación y pagos'); ?>" target="_blank">
                            <i class="fas fa-credit-card"></i>
                            <span><?php _e('Facturación', 'vf-extras'); ?></span>
                        </a>
                    </div>
                    <div class="topic-item">
                        <a href="https://wa.me/<?php echo esc_attr($whatsapp_number); ?>?text=<?php echo urlencode('Consulta sobre programa de emprendedores'); ?>" target="_blank">
                            <i class="fas fa-briefcase"></i>
                            <span><?php _e('Plan Emprendedor', 'vf-extras'); ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
        .vf-whatsapp-support {
            max-width: 800px;
        }
        
        .support-info {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        .support-hours ul {
            list-style: none;
            padding: 0;
        }
        
        .support-hours li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .whatsapp-btn {
            background: #25d366 !important;
            border-color: #25d366 !important;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            border-radius: 25px;
            text-decoration: none;
        }
        
        .whatsapp-btn:hover {
            background: #20c55a !important;
            border-color: #20c55a !important;
        }
        
        .whatsapp-btn i {
            margin-right: 0.5rem;
        }
        
        .topics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .topic-item {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
        }
        
        .topic-item:hover {
            border-color: #25d366;
            transform: translateY(-5px);
        }
        
        .topic-item a {
            color: #495057;
            text-decoration: none;
            display: block;
        }
        
        .topic-item:hover a {
            color: #25d366;
        }
        
        .topic-item i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            display: block;
        }
        </style>
        <?php
    }
    
    /**
     * Add gift to cart
     */
    public function add_gift_to_cart() {
        if (is_admin() && !defined('DOING_AJAX')) return;
        
        $gift_min_amount = get_option('vf_gift_min_amount', 100000);
        $gift_product_id = get_option('vf_gift_product_id', 0);
        $gift_required_role = get_option('vf_gift_required_role', '');
        
        if (!$gift_product_id) return;
        
        $cart_subtotal = WC()->cart->get_subtotal();
        
        if ($cart_subtotal >= $gift_min_amount) {
            // Check role requirement
            if ($gift_required_role && !current_user_can($gift_required_role)) {
                return;
            }
            
            // Check if gift is already in cart
            $gift_in_cart = false;
            foreach (WC()->cart->get_cart() as $cart_item) {
                if ($cart_item['product_id'] == $gift_product_id) {
                    $gift_in_cart = true;
                    break;
                }
            }
            
            if (!$gift_in_cart) {
                WC()->cart->add_to_cart($gift_product_id, 1, 0, array(), array(
                    'is_gift' => true,
                    'original_price' => 0
                ));
            }
        }
    }
    
    /**
     * AJAX: Update stock
     */
    public function ajax_update_stock() {
        check_ajax_referer('vf_extras_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_die(__('Acceso denegado', 'vf-extras'));
        }
        
        $product_id = intval($_POST['product_id']);
        $stock_quantity = intval($_POST['stock_quantity']);
        
        $product = wc_get_product($product_id);
        if (!$product) {
            wp_send_json_error(__('Producto no encontrado', 'vf-extras'));
        }
        
        $product->set_manage_stock(true);
        $product->set_stock_quantity($stock_quantity);
        $product->save();
        
        wp_send_json_success(__('Stock actualizado correctamente', 'vf-extras'));
    }
    
    /**
     * AJAX: Get reports data
     */
    public function ajax_get_reports_data() {
        check_ajax_referer('vf_extras_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_die(__('Acceso denegado', 'vf-extras'));
        }
        
        $reports = VF_Reports::get_instance();
        $data = $reports->get_reports_data();
        
        wp_send_json_success($data);
    }
    
    /**
     * WooCommerce missing notice
     */
    public function woocommerce_missing_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php _e('VF Extras requiere que WooCommerce esté instalado y activado.', 'vf-extras'); ?></p>
        </div>
        <?php
    }
}

// Initialize the plugin
VF_Extras_Plugin::get_instance();