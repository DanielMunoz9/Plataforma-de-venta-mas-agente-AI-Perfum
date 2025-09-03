<?php
/**
 * Plugin Name: VF Extras - Vane France Extensions
 * Description: Essential features for Vane France including Custom Post Types, Admin Dashboard, Reports, Gifts System, and Stock Management.
 * Version: 1.0.0
 * Author: Vane France Team
 * Text Domain: vf-extras
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
define('VF_EXTRAS_VERSION', '1.0.0');
define('VF_EXTRAS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VF_EXTRAS_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main VF Extras Class
 */
class VF_Extras {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        // Initialize components
        $this->register_post_types();
        $this->add_admin_menu();
        $this->init_hooks();
        $this->register_shortcodes();
        $this->add_user_roles();
        $this->init_woocommerce_hooks();
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('vf-extras', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Plugin Activation
     */
    public function activate() {
        $this->register_post_types();
        $this->add_user_roles();
        flush_rewrite_rules();
        
        // Create default options
        add_option('vf_whatsapp_number', '');
        add_option('vf_gifts_threshold', 100000);
        add_option('vf_gifts_product_id', '');
        add_option('vf_gifts_required_role', '');
    }
    
    /**
     * Plugin Deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    /**
     * Register Custom Post Types
     */
    public function register_post_types() {
        // Register Ofertas CPT
        register_post_type('vf_offer', array(
            'labels' => array(
                'name'               => __('Ofertas', 'vf-extras'),
                'singular_name'      => __('Oferta', 'vf-extras'),
                'menu_name'          => __('Ofertas', 'vf-extras'),
                'add_new'            => __('Añadir Nueva', 'vf-extras'),
                'add_new_item'       => __('Añadir Nueva Oferta', 'vf-extras'),
                'edit_item'          => __('Editar Oferta', 'vf-extras'),
                'new_item'           => __('Nueva Oferta', 'vf-extras'),
                'view_item'          => __('Ver Oferta', 'vf-extras'),
                'search_items'       => __('Buscar Ofertas', 'vf-extras'),
                'not_found'          => __('No se encontraron ofertas', 'vf-extras'),
                'not_found_in_trash' => __('No se encontraron ofertas en la papelera', 'vf-extras'),
            ),
            'public'        => true,
            'has_archive'   => true,
            'show_in_menu'  => false, // We'll add it to our custom menu
            'menu_icon'     => 'dashicons-tag',
            'supports'      => array('title', 'editor', 'thumbnail', 'excerpt'),
            'rewrite'       => array('slug' => 'ofertas'),
            'show_in_rest'  => true,
        ));
    }
    
    /**
     * Add Admin Menu
     */
    public function add_admin_menu() {
        add_action('admin_menu', array($this, 'create_admin_menu'));
    }
    
    public function create_admin_menu() {
        // Main menu
        add_menu_page(
            __('Vane France', 'vf-extras'),
            __('Vane France', 'vf-extras'),
            'manage_options',
            'vf-dashboard',
            array($this, 'dashboard_page'),
            'dashicons-store',
            30
        );
        
        // Submenu pages
        add_submenu_page(
            'vf-dashboard',
            __('Dashboard', 'vf-extras'),
            __('Dashboard', 'vf-extras'),
            'manage_options',
            'vf-dashboard',
            array($this, 'dashboard_page')
        );
        
        add_submenu_page(
            'vf-dashboard',
            __('Reportes', 'vf-extras'),
            __('Reportes', 'vf-extras'),
            'manage_options',
            'vf-reports',
            array($this, 'reports_page')
        );
        
        add_submenu_page(
            'vf-dashboard',
            __('Ajustes', 'vf-extras'),
            __('Ajustes', 'vf-extras'),
            'manage_options',
            'vf-settings',
            array($this, 'settings_page')
        );
        
        add_submenu_page(
            'vf-dashboard',
            __('Stock Rápido', 'vf-extras'),
            __('Stock Rápido', 'vf-extras'),
            'manage_options',
            'vf-stock',
            array($this, 'stock_page')
        );
        
        add_submenu_page(
            'vf-dashboard',
            __('Ofertas', 'vf-extras'),
            __('Ofertas', 'vf-extras'),
            'edit_posts',
            'edit.php?post_type=vf_offer'
        );
    }
    
    /**
     * Dashboard Page
     */
    public function dashboard_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Panel de Control - Vane France', 'vf-extras'); ?></h1>
            
            <div class="vf-dashboard-widgets">
                <div class="vf-widget-row">
                    <!-- Sales Overview -->
                    <div class="vf-widget">
                        <h3><?php _e('Ventas Hoy', 'vf-extras'); ?></h3>
                        <div class="vf-stat">
                            <span class="vf-stat-number"><?php echo $this->get_today_sales(); ?></span>
                            <span class="vf-stat-label"><?php _e('COP', 'vf-extras'); ?></span>
                        </div>
                    </div>
                    
                    <!-- Orders Today -->
                    <div class="vf-widget">
                        <h3><?php _e('Pedidos Hoy', 'vf-extras'); ?></h3>
                        <div class="vf-stat">
                            <span class="vf-stat-number"><?php echo $this->get_today_orders(); ?></span>
                            <span class="vf-stat-label"><?php _e('pedidos', 'vf-extras'); ?></span>
                        </div>
                    </div>
                    
                    <!-- Low Stock -->
                    <div class="vf-widget">
                        <h3><?php _e('Stock Bajo', 'vf-extras'); ?></h3>
                        <div class="vf-stat">
                            <span class="vf-stat-number"><?php echo $this->get_low_stock_count(); ?></span>
                            <span class="vf-stat-label"><?php _e('productos', 'vf-extras'); ?></span>
                        </div>
                    </div>
                    
                    <!-- Active Offers -->
                    <div class="vf-widget">
                        <h3><?php _e('Ofertas Activas', 'vf-extras'); ?></h3>
                        <div class="vf-stat">
                            <span class="vf-stat-number"><?php echo $this->get_active_offers(); ?></span>
                            <span class="vf-stat-label"><?php _e('ofertas', 'vf-extras'); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="vf-widget vf-widget-full">
                    <h3><?php _e('Pedidos Recientes', 'vf-extras'); ?></h3>
                    <?php $this->display_recent_orders(); ?>
                </div>
            </div>
        </div>
        
        <style>
        .vf-dashboard-widgets {
            margin-top: 20px;
        }
        .vf-widget-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .vf-widget {
            background: #fff;
            border: 1px solid #c3c4c7;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        }
        .vf-widget-full {
            grid-column: 1 / -1;
        }
        .vf-widget h3 {
            margin: 0 0 15px 0;
            color: #1d2327;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .vf-stat {
            display: flex;
            align-items: baseline;
            gap: 8px;
        }
        .vf-stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #002395;
        }
        .vf-stat-label {
            font-size: 1rem;
            color: #666;
        }
        .vf-recent-orders {
            max-height: 300px;
            overflow-y: auto;
        }
        .vf-order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f1;
        }
        .vf-order-item:last-child {
            border-bottom: none;
        }
        </style>
        <?php
    }
    
    /**
     * Reports Page
     */
    public function reports_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Reportes', 'vf-extras'); ?></h1>
            
            <!-- Revenue Chart -->
            <div class="vf-report-section">
                <h2><?php _e('Ingresos Últimos 30 Días', 'vf-extras'); ?></h2>
                <canvas id="vf-revenue-chart" width="400" height="200"></canvas>
            </div>
            
            <!-- Orders Chart -->
            <div class="vf-report-section">
                <h2><?php _e('Pedidos Últimos 30 Días', 'vf-extras'); ?></h2>
                <canvas id="vf-orders-chart" width="400" height="200"></canvas>
            </div>
            
            <!-- Top Products -->
            <div class="vf-report-section">
                <h2><?php _e('Top 10 Productos por Visualizaciones', 'vf-extras'); ?></h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Producto', 'vf-extras'); ?></th>
                            <th><?php _e('Visualizaciones', 'vf-extras'); ?></th>
                            <th><?php _e('Ventas', 'vf-extras'); ?></th>
                            <th><?php _e('Ingresos', 'vf-extras'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $this->display_top_products(); ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Revenue Chart
            const revenueCtx = document.getElementById('vf-revenue-chart').getContext('2d');
            const revenueData = <?php echo json_encode($this->get_revenue_data()); ?>;
            
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: revenueData.labels,
                    datasets: [{
                        label: '<?php _e('Ingresos (COP)', 'vf-extras'); ?>',
                        data: revenueData.values,
                        borderColor: '#002395',
                        backgroundColor: 'rgba(0, 35, 149, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Orders Chart
            const ordersCtx = document.getElementById('vf-orders-chart').getContext('2d');
            const ordersData = <?php echo json_encode($this->get_orders_data()); ?>;
            
            new Chart(ordersCtx, {
                type: 'bar',
                data: {
                    labels: ordersData.labels,
                    datasets: [{
                        label: '<?php _e('Pedidos', 'vf-extras'); ?>',
                        data: ordersData.values,
                        backgroundColor: '#ed2939',
                        borderColor: '#ed2939',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
        </script>
        
        <style>
        .vf-report-section {
            background: #fff;
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #c3c4c7;
            border-radius: 8px;
        }
        .vf-report-section h2 {
            margin-top: 0;
            color: #002395;
        }
        .vf-report-section canvas {
            max-width: 100%;
            margin: 20px 0;
        }
        </style>
        <?php
    }
    
    /**
     * Settings Page
     */
    public function settings_page() {
        if (isset($_POST['submit'])) {
            update_option('vf_whatsapp_number', sanitize_text_field($_POST['vf_whatsapp_number']));
            update_option('vf_facebook_url', esc_url_raw($_POST['vf_facebook_url']));
            update_option('vf_instagram_url', esc_url_raw($_POST['vf_instagram_url']));
            update_option('vf_twitter_url', esc_url_raw($_POST['vf_twitter_url']));
            update_option('vf_gifts_threshold', intval($_POST['vf_gifts_threshold']));
            update_option('vf_gifts_product_id', intval($_POST['vf_gifts_product_id']));
            update_option('vf_gifts_required_role', sanitize_text_field($_POST['vf_gifts_required_role']));
            
            echo '<div class="notice notice-success"><p>' . __('Configuración guardada correctamente.', 'vf-extras') . '</p></div>';
        }
        
        $whatsapp_number = get_option('vf_whatsapp_number', '');
        $facebook_url = get_option('vf_facebook_url', '');
        $instagram_url = get_option('vf_instagram_url', '');
        $twitter_url = get_option('vf_twitter_url', '');
        $gifts_threshold = get_option('vf_gifts_threshold', 100000);
        $gifts_product_id = get_option('vf_gifts_product_id', '');
        $gifts_required_role = get_option('vf_gifts_required_role', '');
        ?>
        <div class="wrap">
            <h1><?php _e('Ajustes de Vane France', 'vf-extras'); ?></h1>
            
            <form method="post" action="">
                <?php wp_nonce_field('vf_settings_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Número de WhatsApp', 'vf-extras'); ?></th>
                        <td>
                            <input type="text" name="vf_whatsapp_number" value="<?php echo esc_attr($whatsapp_number); ?>" class="regular-text" />
                            <p class="description"><?php _e('Incluir código de país (ej: 573193605666)', 'vf-extras'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Facebook URL', 'vf-extras'); ?></th>
                        <td>
                            <input type="url" name="vf_facebook_url" value="<?php echo esc_attr($facebook_url); ?>" class="regular-text" />
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Instagram URL', 'vf-extras'); ?></th>
                        <td>
                            <input type="url" name="vf_instagram_url" value="<?php echo esc_attr($instagram_url); ?>" class="regular-text" />
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Twitter URL', 'vf-extras'); ?></th>
                        <td>
                            <input type="url" name="vf_twitter_url" value="<?php echo esc_attr($twitter_url); ?>" class="regular-text" />
                        </td>
                    </tr>
                </table>
                
                <h2><?php _e('Sistema de Regalos', 'vf-extras'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Monto Mínimo para Regalo', 'vf-extras'); ?></th>
                        <td>
                            <input type="number" name="vf_gifts_threshold" value="<?php echo esc_attr($gifts_threshold); ?>" class="regular-text" />
                            <p class="description"><?php _e('Monto mínimo en COP para activar regalo automático', 'vf-extras'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Producto Regalo', 'vf-extras'); ?></th>
                        <td>
                            <?php
                            $products = get_posts(array(
                                'post_type' => 'product',
                                'numberposts' => -1,
                                'post_status' => 'publish'
                            ));
                            ?>
                            <select name="vf_gifts_product_id">
                                <option value=""><?php _e('Seleccionar producto', 'vf-extras'); ?></option>
                                <?php foreach ($products as $product) : ?>
                                    <option value="<?php echo $product->ID; ?>" <?php selected($gifts_product_id, $product->ID); ?>>
                                        <?php echo $product->post_title; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Rol Requerido (Opcional)', 'vf-extras'); ?></th>
                        <td>
                            <select name="vf_gifts_required_role">
                                <option value=""><?php _e('Todos los usuarios', 'vf-extras'); ?></option>
                                <option value="distribuidor" <?php selected($gifts_required_role, 'distribuidor'); ?>><?php _e('Distribuidor', 'vf-extras'); ?></option>
                                <option value="customer" <?php selected($gifts_required_role, 'customer'); ?>><?php _e('Cliente', 'vf-extras'); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Stock Management Page
     */
    public function stock_page() {
        if (isset($_POST['update_stock'])) {
            $product_id = intval($_POST['product_id']);
            $new_stock = intval($_POST['new_stock']);
            
            if ($product_id && class_exists('WooCommerce')) {
                $product = wc_get_product($product_id);
                if ($product) {
                    $product->set_stock_quantity($new_stock);
                    $product->save();
                    echo '<div class="notice notice-success"><p>' . sprintf(__('Stock actualizado para el producto ID %d', 'vf-extras'), $product_id) . '</p></div>';
                } else {
                    echo '<div class="notice notice-error"><p>' . __('Producto no encontrado', 'vf-extras') . '</p></div>';
                }
            }
        }
        ?>
        <div class="wrap">
            <h1><?php _e('Gestión Rápida de Stock', 'vf-extras'); ?></h1>
            
            <div class="vf-stock-update">
                <h2><?php _e('Actualizar Stock por ID', 'vf-extras'); ?></h2>
                <form method="post" action="">
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('ID del Producto', 'vf-extras'); ?></th>
                            <td><input type="number" name="product_id" required class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Nueva Cantidad', 'vf-extras'); ?></th>
                            <td><input type="number" name="new_stock" required class="regular-text" /></td>
                        </tr>
                    </table>
                    <?php submit_button(__('Actualizar Stock', 'vf-extras'), 'primary', 'update_stock'); ?>
                </form>
            </div>
            
            <!-- Current Stock Levels -->
            <div class="vf-current-stock">
                <h2><?php _e('Niveles de Stock Actuales', 'vf-extras'); ?></h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('ID', 'vf-extras'); ?></th>
                            <th><?php _e('Producto', 'vf-extras'); ?></th>
                            <th><?php _e('Stock Actual', 'vf-extras'); ?></th>
                            <th><?php _e('Estado', 'vf-extras'); ?></th>
                            <th><?php _e('Acciones', 'vf-extras'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $this->display_stock_levels(); ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <style>
        .vf-stock-update {
            background: #fff;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #c3c4c7;
            border-radius: 8px;
        }
        .stock-low { color: #d63638; font-weight: bold; }
        .stock-ok { color: #00a32a; }
        </style>
        <?php
    }
    
    /**
     * Initialize Hooks
     */
    public function init_hooks() {
        // Track product views
        add_action('woocommerce_single_product_summary', array($this, 'track_product_view'));
        
        // Add My Account endpoint
        add_action('init', array($this, 'add_my_account_endpoint'));
        add_filter('woocommerce_account_menu_items', array($this, 'add_my_account_menu_item'));
        add_action('woocommerce_account_soporte-tecnico_endpoint', array($this, 'soporte_tecnico_content'));
    }
    
    /**
     * Register Shortcodes
     */
    public function register_shortcodes() {
        add_shortcode('vf_offers', array($this, 'offers_shortcode'));
    }
    
    /**
     * Offers Shortcode
     */
    public function offers_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 6
        ), $atts);
        
        $offers = get_posts(array(
            'post_type' => 'vf_offer',
            'posts_per_page' => intval($atts['limit']),
            'post_status' => 'publish'
        ));
        
        if (empty($offers)) {
            return '<p>' . __('No hay ofertas disponibles.', 'vf-extras') . '</p>';
        }
        
        ob_start();
        ?>
        <div class="vf-offers-grid">
            <?php foreach ($offers as $offer) : ?>
                <div class="vf-offer-card">
                    <?php if (has_post_thumbnail($offer->ID)) : ?>
                        <div class="vf-offer-image">
                            <?php echo get_the_post_thumbnail($offer->ID, 'medium'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="vf-offer-content">
                        <h3><?php echo get_the_title($offer->ID); ?></h3>
                        <p><?php echo get_the_excerpt($offer->ID); ?></p>
                        <a href="<?php echo get_permalink($offer->ID); ?>" class="vf-offer-link">
                            <?php _e('Ver Oferta', 'vf-extras'); ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <style>
        .vf-offers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .vf-offer-card {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .vf-offer-card:hover {
            transform: translateY(-5px);
        }
        .vf-offer-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .vf-offer-content {
            padding: 20px;
        }
        .vf-offer-content h3 {
            color: #002395;
            margin-bottom: 10px;
        }
        .vf-offer-link {
            display: inline-block;
            background: #ed2939;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
            transition: background 0.3s ease;
        }
        .vf-offer-link:hover {
            background: #002395;
            color: #fff;
        }
        </style>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Add User Roles
     */
    public function add_user_roles() {
        add_role('distribuidor', __('Distribuidor', 'vf-extras'), array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
        ));
    }
    
    /**
     * Initialize WooCommerce Hooks
     */
    public function init_woocommerce_hooks() {
        if (class_exists('WooCommerce')) {
            // Gift system
            add_action('woocommerce_cart_calculate_fees', array($this, 'add_gift_to_cart'));
        }
    }
    
    /**
     * Track Product Views
     */
    public function track_product_view() {
        global $product;
        if (!$product) return;
        
        $views = get_post_meta($product->get_id(), 'vf_views', true);
        $views = $views ? intval($views) + 1 : 1;
        update_post_meta($product->get_id(), 'vf_views', $views);
    }
    
    /**
     * Add My Account Endpoint
     */
    public function add_my_account_endpoint() {
        add_rewrite_endpoint('soporte-tecnico', EP_ROOT | EP_PAGES);
    }
    
    /**
     * Add My Account Menu Item
     */
    public function add_my_account_menu_item($items) {
        $items['soporte-tecnico'] = __('Soporte Técnico', 'vf-extras');
        return $items;
    }
    
    /**
     * Soporte Técnico Content
     */
    public function soporte_tecnico_content() {
        $whatsapp_number = get_option('vf_whatsapp_number', '');
        $message = __('Hola, necesito soporte técnico para mi cuenta en Vane France.', 'vf-extras');
        $whatsapp_url = 'https://wa.me/' . $whatsapp_number . '?text=' . urlencode($message);
        ?>
        <div class="vf-soporte-tecnico">
            <h3><?php _e('Soporte Técnico', 'vf-extras'); ?></h3>
            <p><?php _e('¿Necesitas ayuda? Contáctanos a través de WhatsApp para recibir soporte técnico personalizado.', 'vf-extras'); ?></p>
            
            <?php if (!empty($whatsapp_number)) : ?>
                <a href="<?php echo esc_url($whatsapp_url); ?>" class="button wc-forward" target="_blank">
                    <?php _e('Contactar Soporte por WhatsApp', 'vf-extras'); ?>
                </a>
            <?php else : ?>
                <p><?php _e('El número de WhatsApp no está configurado. Contacta al administrador.', 'vf-extras'); ?></p>
            <?php endif; ?>
            
            <div class="vf-support-info">
                <h4><?php _e('Horarios de Atención', 'vf-extras'); ?></h4>
                <ul>
                    <li><?php _e('Lunes a Sábado: 9:00 AM - 7:00 PM', 'vf-extras'); ?></li>
                    <li><?php _e('Domingo: Cerrado', 'vf-extras'); ?></li>
                </ul>
                
                <h4><?php _e('Información de Contacto', 'vf-extras'); ?></h4>
                <ul>
                    <li><?php _e('Teléfono: 319 3605666', 'vf-extras'); ?></li>
                    <li><?php _e('Dirección: Cl. 12 #13-99 a 13, 1, Bogotá', 'vf-extras'); ?></li>
                    <li><?php _e('Dirección: Cl. 12 #13-69 Local 102, Bogotá', 'vf-extras'); ?></li>
                </ul>
            </div>
        </div>
        
        <style>
        .vf-soporte-tecnico {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .vf-support-info {
            margin-top: 30px;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
        }
        .vf-support-info h4 {
            color: #002395;
            margin-bottom: 10px;
        }
        .vf-support-info ul {
            margin: 0 0 20px 20px;
        }
        </style>
        <?php
    }
    
    /**
     * Add Gift to Cart
     */
    public function add_gift_to_cart() {
        if (is_admin() && !defined('DOING_AJAX')) return;
        
        $threshold = get_option('vf_gifts_threshold', 100000);
        $gift_product_id = get_option('vf_gifts_product_id', '');
        $required_role = get_option('vf_gifts_required_role', '');
        
        if (empty($gift_product_id) || $threshold <= 0) return;
        
        // Check user role requirement
        if (!empty($required_role) && !current_user_can($required_role)) return;
        
        $subtotal = WC()->cart->subtotal;
        
        if ($subtotal >= $threshold) {
            // Check if gift is already in cart
            $gift_in_cart = false;
            foreach (WC()->cart->get_cart() as $cart_item) {
                if ($cart_item['product_id'] == $gift_product_id) {
                    $gift_in_cart = true;
                    break;
                }
            }
            
            if (!$gift_in_cart) {
                WC()->cart->add_fee(__('Regalo Especial', 'vf-extras'), 0);
                WC()->cart->add_to_cart($gift_product_id, 1, 0, array(), array('vf_gift' => true));
            }
        }
    }
    
    /**
     * Enqueue Scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_style('vf-extras-style', VF_EXTRAS_PLUGIN_URL . 'assets/style.css', array(), VF_EXTRAS_VERSION);
        wp_enqueue_script('vf-extras-script', VF_EXTRAS_PLUGIN_URL . 'assets/script.js', array('jquery'), VF_EXTRAS_VERSION, true);
    }
    
    /**
     * Admin Enqueue Scripts
     */
    public function admin_enqueue_scripts() {
        wp_enqueue_style('vf-extras-admin', VF_EXTRAS_PLUGIN_URL . 'assets/admin.css', array(), VF_EXTRAS_VERSION);
        wp_enqueue_script('vf-extras-admin', VF_EXTRAS_PLUGIN_URL . 'assets/admin.js', array('jquery'), VF_EXTRAS_VERSION, true);
    }
    
    /**
     * Helper Functions
     */
    private function get_today_sales() {
        if (!class_exists('WooCommerce')) return 0;
        
        $today = date('Y-m-d');
        $orders = wc_get_orders(array(
            'status' => 'completed',
            'date_created' => $today,
        ));
        
        $total = 0;
        foreach ($orders as $order) {
            $total += $order->get_total();
        }
        
        return number_format($total, 0, ',', '.');
    }
    
    private function get_today_orders() {
        if (!class_exists('WooCommerce')) return 0;
        
        $today = date('Y-m-d');
        return count(wc_get_orders(array(
            'date_created' => $today,
        )));
    }
    
    private function get_low_stock_count() {
        if (!class_exists('WooCommerce')) return 0;
        
        $products = wc_get_products(array(
            'stock_quantity' => array(0, 5),
            'stock_status' => 'instock',
        ));
        
        return count($products);
    }
    
    private function get_active_offers() {
        return wp_count_posts('vf_offer')->publish;
    }
    
    private function display_recent_orders() {
        if (!class_exists('WooCommerce')) {
            echo '<p>' . __('WooCommerce no está activo.', 'vf-extras') . '</p>';
            return;
        }
        
        $orders = wc_get_orders(array(
            'limit' => 10,
            'orderby' => 'date',
            'order' => 'DESC',
        ));
        
        if (empty($orders)) {
            echo '<p>' . __('No hay pedidos recientes.', 'vf-extras') . '</p>';
            return;
        }
        
        echo '<div class="vf-recent-orders">';
        foreach ($orders as $order) {
            echo '<div class="vf-order-item">';
            echo '<span>#' . $order->get_id() . ' - ' . $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() . '</span>';
            echo '<span>' . wc_price($order->get_total()) . '</span>';
            echo '<span>' . $order->get_status() . '</span>';
            echo '<span>' . $order->get_date_created()->format('d/m/Y H:i') . '</span>';
            echo '</div>';
        }
        echo '</div>';
    }
    
    private function get_revenue_data() {
        if (!class_exists('WooCommerce')) return array('labels' => [], 'values' => []);
        
        $data = array('labels' => [], 'values' => []);
        
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $data['labels'][] = date('d/m', strtotime($date));
            
            $orders = wc_get_orders(array(
                'status' => 'completed',
                'date_created' => $date,
            ));
            
            $total = 0;
            foreach ($orders as $order) {
                $total += $order->get_total();
            }
            $data['values'][] = $total;
        }
        
        return $data;
    }
    
    private function get_orders_data() {
        if (!class_exists('WooCommerce')) return array('labels' => [], 'values' => []);
        
        $data = array('labels' => [], 'values' => []);
        
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $data['labels'][] = date('d/m', strtotime($date));
            
            $orders = wc_get_orders(array(
                'date_created' => $date,
            ));
            
            $data['values'][] = count($orders);
        }
        
        return $data;
    }
    
    private function display_top_products() {
        if (!class_exists('WooCommerce')) {
            echo '<tr><td colspan="4">' . __('WooCommerce no está activo.', 'vf-extras') . '</td></tr>';
            return;
        }
        
        $products = get_posts(array(
            'post_type' => 'product',
            'posts_per_page' => 10,
            'meta_key' => 'vf_views',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
        ));
        
        if (empty($products)) {
            echo '<tr><td colspan="4">' . __('No hay datos de visualizaciones.', 'vf-extras') . '</td></tr>';
            return;
        }
        
        foreach ($products as $product_post) {
            $product = wc_get_product($product_post->ID);
            $views = get_post_meta($product_post->ID, 'vf_views', true) ?: 0;
            
            echo '<tr>';
            echo '<td>' . $product->get_name() . '</td>';
            echo '<td>' . $views . '</td>';
            echo '<td>' . $product->get_total_sales() . '</td>';
            echo '<td>' . wc_price($product->get_total_sales() * $product->get_price()) . '</td>';
            echo '</tr>';
        }
    }
    
    private function display_stock_levels() {
        if (!class_exists('WooCommerce')) {
            echo '<tr><td colspan="5">' . __('WooCommerce no está activo.', 'vf-extras') . '</td></tr>';
            return;
        }
        
        $products = wc_get_products(array(
            'limit' => 20,
            'orderby' => 'stock_quantity',
            'order' => 'ASC',
        ));
        
        foreach ($products as $product) {
            $stock = $product->get_stock_quantity();
            $status_class = $stock <= 5 ? 'stock-low' : 'stock-ok';
            $status_text = $stock <= 5 ? __('Stock Bajo', 'vf-extras') : __('OK', 'vf-extras');
            
            echo '<tr>';
            echo '<td>' . $product->get_id() . '</td>';
            echo '<td>' . $product->get_name() . '</td>';
            echo '<td class="' . $status_class . '">' . ($stock ?: __('Sin stock', 'vf-extras')) . '</td>';
            echo '<td class="' . $status_class . '">' . $status_text . '</td>';
            echo '<td><a href="' . get_edit_post_link($product->get_id()) . '">' . __('Editar', 'vf-extras') . '</a></td>';
            echo '</tr>';
        }
    }
}

// Initialize the plugin
new VF_Extras();