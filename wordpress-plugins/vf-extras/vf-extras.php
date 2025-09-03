<?php
/**
 * Plugin Name: VF Extras
 * Plugin URI: https://vane-france.com
 * Description: Business logic features for Vane France including CPT Ofertas, reports, settings, and gift program.
 * Version: 1.0.0
 * Author: Vane France
 * Text Domain: vf-extras
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
define('VF_EXTRAS_VERSION', '1.0.0');
define('VF_EXTRAS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('VF_EXTRAS_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Main VF Extras Plugin Class
 */
class VF_Extras_Plugin {
    
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
        // Register CPT
        $this->register_ofertas_cpt();
        
        // Admin functionality
        if (is_admin()) {
            add_action('admin_menu', array($this, 'admin_menu'));
            add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        }
        
        // Frontend functionality
        add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
        add_shortcode('vf_offers', array($this, 'offers_shortcode'));
        
        // WooCommerce hooks
        if (class_exists('WooCommerce')) {
            add_action('woocommerce_before_calculate_totals', array($this, 'add_gift_product'));
            add_action('wp', array($this, 'track_product_views'));
            add_filter('woocommerce_account_menu_items', array($this, 'add_account_menu_item'));
            add_action('init', array($this, 'add_account_endpoint'));
            add_action('woocommerce_account_soporte-tecnico_endpoint', array($this, 'account_support_content'));
        }
        
        // AJAX handlers
        add_action('wp_ajax_vf_update_stock', array($this, 'ajax_update_stock'));
        add_action('wp_ajax_vf_get_reports_data', array($this, 'ajax_get_reports_data'));
    }
    
    /**
     * Load text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain('vf-extras', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create distribuidor role
        $this->create_distribuidor_role();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Set default options
        $this->set_default_options();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Create distribuidor role
     */
    private function create_distribuidor_role() {
        add_role('distribuidor', __('Distribuidor', 'vf-extras'), array(
            'read' => true,
            'read_private_posts' => false,
            'edit_posts' => false,
        ));
    }
    
    /**
     * Set default options
     */
    private function set_default_options() {
        $defaults = array(
            'vf_whatsapp_number' => '',
            'vf_instagram_url' => '',
            'vf_facebook_url' => '',
            'vf_tiktok_url' => '',
            'vf_gift_enabled' => false,
            'vf_gift_minimum' => 150000,
            'vf_gift_product_id' => '',
            'vf_gift_required_role' => ''
        );
        
        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }
    
    /**
     * Register Ofertas CPT
     */
    public function register_ofertas_cpt() {
        $labels = array(
            'name' => __('Ofertas', 'vf-extras'),
            'singular_name' => __('Oferta', 'vf-extras'),
            'menu_name' => __('Ofertas', 'vf-extras'),
            'add_new' => __('Agregar Nueva', 'vf-extras'),
            'add_new_item' => __('Agregar Nueva Oferta', 'vf-extras'),
            'edit_item' => __('Editar Oferta', 'vf-extras'),
            'new_item' => __('Nueva Oferta', 'vf-extras'),
            'view_item' => __('Ver Oferta', 'vf-extras'),
            'search_items' => __('Buscar Ofertas', 'vf-extras'),
            'not_found' => __('No se encontraron ofertas', 'vf-extras'),
            'not_found_in_trash' => __('No se encontraron ofertas en la papelera', 'vf-extras'),
        );
        
        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => false, // We'll add it to our custom menu
            'query_var' => true,
            'rewrite' => array('slug' => 'ofertas'),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_rest' => true,
        );
        
        register_post_type('vf_offer', $args);
    }
    
    /**
     * Admin menu
     */
    public function admin_menu() {
        add_menu_page(
            __('Vane France', 'vf-extras'),
            __('Vane France', 'vf-extras'),
            'manage_options',
            'vane-france',
            array($this, 'admin_dashboard'),
            'dashicons-store',
            30
        );
        
        add_submenu_page(
            'vane-france',
            __('Reportes', 'vf-extras'),
            __('Reportes', 'vf-extras'),
            'manage_options',
            'vf-reports',
            array($this, 'admin_reports')
        );
        
        add_submenu_page(
            'vane-france',
            __('Ajustes', 'vf-extras'),
            __('Ajustes', 'vf-extras'),
            'manage_options',
            'vf-settings',
            array($this, 'admin_settings')
        );
        
        add_submenu_page(
            'vane-france',
            __('Stock Rápido', 'vf-extras'),
            __('Stock Rápido', 'vf-extras'),
            'manage_options',
            'vf-stock',
            array($this, 'admin_stock')
        );
        
        add_submenu_page(
            'vane-france',
            __('Ofertas', 'vf-extras'),
            __('Ofertas', 'vf-extras'),
            'manage_options',
            'edit.php?post_type=vf_offer'
        );
    }
    
    /**
     * Admin scripts
     */
    public function admin_scripts($hook) {
        if (strpos($hook, 'vane-france') !== false || strpos($hook, 'vf-') !== false) {
            wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.9.1', true);
            wp_enqueue_script('vf-admin', VF_EXTRAS_PLUGIN_URL . 'assets/admin.js', array('jquery', 'chart-js'), VF_EXTRAS_VERSION, true);
            wp_enqueue_style('vf-admin', VF_EXTRAS_PLUGIN_URL . 'assets/admin.css', array(), VF_EXTRAS_VERSION);
            
            wp_localize_script('vf-admin', 'vfExtras', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('vf_extras_nonce'),
                'strings' => array(
                    'success' => __('Guardado exitosamente', 'vf-extras'),
                    'error' => __('Error al procesar la solicitud', 'vf-extras'),
                    'confirm' => __('¿Estás seguro?', 'vf-extras'),
                )
            ));
        }
    }
    
    /**
     * Frontend scripts
     */
    public function frontend_scripts() {
        wp_enqueue_style('vf-extras', VF_EXTRAS_PLUGIN_URL . 'assets/frontend.css', array(), VF_EXTRAS_VERSION);
    }
    
    /**
     * Admin dashboard
     */
    public function admin_dashboard() {
        ?>
        <div class="wrap">
            <h1><?php _e('Panel de Control - Vane France', 'vf-extras'); ?></h1>
            
            <div class="vf-dashboard">
                <div class="vf-dashboard-widgets">
                    <div class="vf-widget">
                        <h3><?php _e('Resumen de Ventas', 'vf-extras'); ?></h3>
                        <div class="vf-stats">
                            <?php
                            $today_sales = $this->get_today_sales();
                            $month_sales = $this->get_month_sales();
                            $total_orders = $this->get_total_orders();
                            ?>
                            <div class="vf-stat">
                                <span class="vf-stat-number">$<?php echo number_format($today_sales); ?></span>
                                <span class="vf-stat-label"><?php _e('Ventas de Hoy', 'vf-extras'); ?></span>
                            </div>
                            <div class="vf-stat">
                                <span class="vf-stat-number">$<?php echo number_format($month_sales); ?></span>
                                <span class="vf-stat-label"><?php _e('Ventas del Mes', 'vf-extras'); ?></span>
                            </div>
                            <div class="vf-stat">
                                <span class="vf-stat-number"><?php echo $total_orders; ?></span>
                                <span class="vf-stat-label"><?php _e('Órdenes Totales', 'vf-extras'); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="vf-widget">
                        <h3><?php _e('Acciones Rápidas', 'vf-extras'); ?></h3>
                        <div class="vf-quick-actions">
                            <a href="<?php echo admin_url('post-new.php?post_type=vf_offer'); ?>" class="button button-primary">
                                <?php _e('Crear Nueva Oferta', 'vf-extras'); ?>
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=vf-stock'); ?>" class="button">
                                <?php _e('Actualizar Stock', 'vf-extras'); ?>
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=vf-reports'); ?>" class="button">
                                <?php _e('Ver Reportes', 'vf-extras'); ?>
                            </a>
                        </div>
                    </div>
                    
                    <div class="vf-widget">
                        <h3><?php _e('Productos Populares', 'vf-extras'); ?></h3>
                        <div class="vf-popular-products">
                            <?php $this->display_popular_products(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Admin reports page
     */
    public function admin_reports() {
        ?>
        <div class="wrap">
            <h1><?php _e('Reportes', 'vf-extras'); ?></h1>
            
            <div class="vf-reports">
                <div class="vf-chart-container">
                    <h3><?php _e('Ingresos y Órdenes - Últimos 30 días', 'vf-extras'); ?></h3>
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
                
                <div class="vf-chart-container">
                    <h3><?php _e('Top 10 Productos por Visualizaciones', 'vf-extras'); ?></h3>
                    <canvas id="topProductsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load charts data via AJAX
            jQuery.post(ajaxurl, {
                action: 'vf_get_reports_data',
                nonce: vfExtras.nonce
            }, function(response) {
                if (response.success) {
                    createRevenueChart(response.data.revenue);
                    createTopProductsChart(response.data.topProducts);
                }
            });
        });
        
        function createRevenueChart(data) {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Ingresos',
                            data: data.revenue,
                            borderColor: '#002395',
                            backgroundColor: 'rgba(0, 35, 149, 0.1)',
                            tension: 0.1
                        },
                        {
                            label: 'Órdenes',
                            data: data.orders,
                            borderColor: '#ed2939',
                            backgroundColor: 'rgba(237, 41, 57, 0.1)',
                            tension: 0.1,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    }
                }
            });
        }
        
        function createTopProductsChart(data) {
            const ctx = document.getElementById('topProductsChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Visualizaciones',
                        data: data.views,
                        backgroundColor: '#002395',
                        borderColor: '#ed2939',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        </script>
        <?php
    }
    
    /**
     * Admin settings page
     */
    public function admin_settings() {
        if (isset($_POST['submit'])) {
            // Save settings
            update_option('vf_whatsapp_number', sanitize_text_field($_POST['vf_whatsapp_number']));
            update_option('vf_instagram_url', esc_url_raw($_POST['vf_instagram_url']));
            update_option('vf_facebook_url', esc_url_raw($_POST['vf_facebook_url']));
            update_option('vf_tiktok_url', esc_url_raw($_POST['vf_tiktok_url']));
            update_option('vf_gift_enabled', isset($_POST['vf_gift_enabled']));
            update_option('vf_gift_minimum', intval($_POST['vf_gift_minimum']));
            update_option('vf_gift_product_id', intval($_POST['vf_gift_product_id']));
            update_option('vf_gift_required_role', sanitize_text_field($_POST['vf_gift_required_role']));
            
            echo '<div class="notice notice-success"><p>' . __('Configuración guardada.', 'vf-extras') . '</p></div>';
        }
        
        $whatsapp = get_option('vf_whatsapp_number', '');
        $instagram = get_option('vf_instagram_url', '');
        $facebook = get_option('vf_facebook_url', '');
        $tiktok = get_option('vf_tiktok_url', '');
        $gift_enabled = get_option('vf_gift_enabled', false);
        $gift_minimum = get_option('vf_gift_minimum', 150000);
        $gift_product_id = get_option('vf_gift_product_id', '');
        $gift_required_role = get_option('vf_gift_required_role', '');
        ?>
        <div class="wrap">
            <h1><?php _e('Ajustes de Vane France', 'vf-extras'); ?></h1>
            
            <form method="post" action="">
                <?php wp_nonce_field('vf_settings', 'vf_settings_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Número de WhatsApp', 'vf-extras'); ?></th>
                        <td>
                            <input type="text" name="vf_whatsapp_number" value="<?php echo esc_attr($whatsapp); ?>" class="regular-text" placeholder="+573193605666" />
                            <p class="description"><?php _e('Número con código de país (ej: +573193605666)', 'vf-extras'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('URL de Instagram', 'vf-extras'); ?></th>
                        <td>
                            <input type="url" name="vf_instagram_url" value="<?php echo esc_attr($instagram); ?>" class="regular-text" />
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('URL de Facebook', 'vf-extras'); ?></th>
                        <td>
                            <input type="url" name="vf_facebook_url" value="<?php echo esc_attr($facebook); ?>" class="regular-text" />
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('URL de TikTok', 'vf-extras'); ?></th>
                        <td>
                            <input type="url" name="vf_tiktok_url" value="<?php echo esc_attr($tiktok); ?>" class="regular-text" />
                        </td>
                    </tr>
                </table>
                
                <h2><?php _e('Programa de Regalos', 'vf-extras'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Habilitar Regalos', 'vf-extras'); ?></th>
                        <td>
                            <input type="checkbox" name="vf_gift_enabled" value="1" <?php checked($gift_enabled); ?> />
                            <p class="description"><?php _e('Agregar automáticamente productos de regalo cuando se cumpla el monto mínimo', 'vf-extras'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Monto Mínimo', 'vf-extras'); ?></th>
                        <td>
                            <input type="number" name="vf_gift_minimum" value="<?php echo esc_attr($gift_minimum); ?>" min="0" step="1000" />
                            <p class="description"><?php _e('Monto mínimo de compra para recibir regalo', 'vf-extras'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Producto Regalo', 'vf-extras'); ?></th>
                        <td>
                            <select name="vf_gift_product_id">
                                <option value=""><?php _e('Seleccionar producto', 'vf-extras'); ?></option>
                                <?php
                                $products = wc_get_products(array('limit' => -1));
                                foreach ($products as $product) {
                                    echo '<option value="' . $product->get_id() . '"' . selected($gift_product_id, $product->get_id(), false) . '>' . $product->get_name() . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Rol Requerido', 'vf-extras'); ?></th>
                        <td>
                            <select name="vf_gift_required_role">
                                <option value=""><?php _e('Todos los usuarios', 'vf-extras'); ?></option>
                                <option value="distribuidor" <?php selected($gift_required_role, 'distribuidor'); ?>><?php _e('Solo distribuidores', 'vf-extras'); ?></option>
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
     * Admin stock page
     */
    public function admin_stock() {
        ?>
        <div class="wrap">
            <h1><?php _e('Stock Rápido', 'vf-extras'); ?></h1>
            
            <div class="vf-stock-tool">
                <h3><?php _e('Actualización Rápida de Inventario', 'vf-extras'); ?></h3>
                
                <form id="vf-stock-form">
                    <?php wp_nonce_field('vf_stock', 'vf_stock_nonce'); ?>
                    
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('ID del Producto', 'vf-extras'); ?></th>
                                <th><?php _e('Nombre del Producto', 'vf-extras'); ?></th>
                                <th><?php _e('Stock Actual', 'vf-extras'); ?></th>
                                <th><?php _e('Nuevo Stock', 'vf-extras'); ?></th>
                                <th><?php _e('Acción', 'vf-extras'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="stock-table-body">
                            <!-- Dynamic content loaded via JavaScript -->
                        </tbody>
                    </table>
                    
                    <div class="vf-stock-actions">
                        <input type="number" id="product-id-input" placeholder="<?php _e('ID del producto', 'vf-extras'); ?>" />
                        <button type="button" id="add-product-btn" class="button"><?php _e('Agregar Producto', 'vf-extras'); ?></button>
                        <button type="button" id="update-all-btn" class="button button-primary"><?php _e('Actualizar Todo', 'vf-extras'); ?></button>
                    </div>
                </form>
            </div>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stockTable = document.getElementById('stock-table-body');
            const productIdInput = document.getElementById('product-id-input');
            const addProductBtn = document.getElementById('add-product-btn');
            const updateAllBtn = document.getElementById('update-all-btn');
            
            // Add product to table
            addProductBtn.addEventListener('click', function() {
                const productId = productIdInput.value.trim();
                if (!productId) return;
                
                // Fetch product data and add to table
                jQuery.post(ajaxurl, {
                    action: 'vf_get_product_data',
                    product_id: productId,
                    nonce: vfExtras.nonce
                }, function(response) {
                    if (response.success) {
                        addProductRow(response.data);
                        productIdInput.value = '';
                    } else {
                        alert(response.data || 'Producto no encontrado');
                    }
                });
            });
            
            // Update all stocks
            updateAllBtn.addEventListener('click', function() {
                const updates = [];
                const rows = stockTable.querySelectorAll('tr');
                
                rows.forEach(row => {
                    const productId = row.dataset.productId;
                    const newStock = row.querySelector('.new-stock-input').value;
                    if (newStock !== '') {
                        updates.push({
                            product_id: productId,
                            stock: parseInt(newStock)
                        });
                    }
                });
                
                if (updates.length === 0) {
                    alert('No hay actualizaciones para realizar');
                    return;
                }
                
                jQuery.post(ajaxurl, {
                    action: 'vf_update_stock',
                    updates: updates,
                    nonce: vfExtras.nonce
                }, function(response) {
                    if (response.success) {
                        alert('Stock actualizado correctamente');
                        location.reload();
                    } else {
                        alert(response.data || 'Error al actualizar stock');
                    }
                });
            });
            
            function addProductRow(product) {
                const row = document.createElement('tr');
                row.dataset.productId = product.id;
                row.innerHTML = `
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>${product.stock}</td>
                    <td><input type="number" class="new-stock-input" min="0" placeholder="Nuevo stock" /></td>
                    <td><button type="button" class="button remove-product">Remover</button></td>
                `;
                
                row.querySelector('.remove-product').addEventListener('click', function() {
                    row.remove();
                });
                
                stockTable.appendChild(row);
            }
        });
        </script>
        <?php
    }
    
    /**
     * Offers shortcode
     */
    public function offers_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 6,
            'orderby' => 'date',
            'order' => 'DESC'
        ), $atts);
        
        $offers = get_posts(array(
            'post_type' => 'vf_offer',
            'posts_per_page' => intval($atts['limit']),
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
            'post_status' => 'publish'
        ));
        
        if (!$offers) {
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
                        <h3 class="vf-offer-title">
                            <a href="<?php echo get_permalink($offer->ID); ?>">
                                <?php echo get_the_title($offer->ID); ?>
                            </a>
                        </h3>
                        
                        <div class="vf-offer-excerpt">
                            <?php echo wp_trim_words(get_the_excerpt($offer->ID), 20); ?>
                        </div>
                        
                        <div class="vf-offer-meta">
                            <span class="vf-offer-date">
                                <?php echo get_the_date('', $offer->ID); ?>
                            </span>
                        </div>
                        
                        <a href="<?php echo get_permalink($offer->ID); ?>" class="vf-offer-link">
                            <?php _e('Ver Oferta', 'vf-extras'); ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
    
    /**
     * Add gift product to cart
     */
    public function add_gift_product($cart) {
        if (is_admin() || !get_option('vf_gift_enabled')) {
            return;
        }
        
        $gift_product_id = get_option('vf_gift_product_id');
        $gift_minimum = get_option('vf_gift_minimum');
        $required_role = get_option('vf_gift_required_role');
        
        if (!$gift_product_id || !$gift_minimum) {
            return;
        }
        
        // Check user role if required
        if ($required_role && !current_user_can($required_role)) {
            return;
        }
        
        $subtotal = 0;
        $has_gift = false;
        
        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            if ($cart_item['product_id'] == $gift_product_id) {
                $has_gift = true;
                continue;
            }
            $subtotal += $cart_item['line_total'];
        }
        
        if ($subtotal >= $gift_minimum && !$has_gift) {
            $cart->add_to_cart($gift_product_id, 1, 0, array(), array('is_gift' => true));
        } elseif ($subtotal < $gift_minimum && $has_gift) {
            // Remove gift if minimum not met
            foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
                if ($cart_item['product_id'] == $gift_product_id && isset($cart_item['is_gift'])) {
                    $cart->remove_cart_item($cart_item_key);
                }
            }
        }
    }
    
    /**
     * Track product views
     */
    public function track_product_views() {
        if (is_product()) {
            global $post;
            $views = get_post_meta($post->ID, 'vf_views', true);
            $views = $views ? intval($views) + 1 : 1;
            update_post_meta($post->ID, 'vf_views', $views);
        }
    }
    
    /**
     * Add account menu item
     */
    public function add_account_menu_item($items) {
        $items['soporte-tecnico'] = __('Soporte Técnico', 'vf-extras');
        return $items;
    }
    
    /**
     * Add account endpoint
     */
    public function add_account_endpoint() {
        add_rewrite_endpoint('soporte-tecnico', EP_ROOT | EP_PAGES);
    }
    
    /**
     * Account support content
     */
    public function account_support_content() {
        $whatsapp_number = get_option('vf_whatsapp_number');
        $message = urlencode('Hola, necesito soporte técnico para mi cuenta en Vane France');
        ?>
        <div class="vf-support-section">
            <h3><?php _e('Soporte Técnico', 'vf-extras'); ?></h3>
            <p><?php _e('¿Necesitas ayuda? Nuestro equipo de soporte está aquí para asistirte.', 'vf-extras'); ?></p>
            
            <?php if ($whatsapp_number) : ?>
                <div class="vf-support-whatsapp">
                    <a href="https://wa.me/<?php echo esc_attr($whatsapp_number); ?>?text=<?php echo $message; ?>" 
                       target="_blank" 
                       rel="noopener"
                       class="button button-primary">
                        <?php _e('Contactar por WhatsApp', 'vf-extras'); ?>
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="vf-support-info">
                <h4><?php _e('Información de Contacto', 'vf-extras'); ?></h4>
                <p><strong><?php _e('Teléfono:', 'vf-extras'); ?></strong> 319 3605666</p>
                <p><strong><?php _e('Horario:', 'vf-extras'); ?></strong> Lunes a Sábado, 9:00 AM - 7:00 PM</p>
                <p><strong><?php _e('Ubicaciones:', 'vf-extras'); ?></strong></p>
                <ul>
                    <li>Cl. 12 #13-99 a 13, 1, Bogotá</li>
                    <li>Cl. 12 #13-69 Local 102, Bogotá</li>
                </ul>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX: Update stock
     */
    public function ajax_update_stock() {
        check_ajax_referer('vf_extras_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para realizar esta acción.', 'vf-extras'));
        }
        
        $updates = $_POST['updates'];
        
        foreach ($updates as $update) {
            $product_id = intval($update['product_id']);
            $stock = intval($update['stock']);
            
            $product = wc_get_product($product_id);
            if ($product) {
                $product->set_stock_quantity($stock);
                $product->save();
            }
        }
        
        wp_send_json_success(__('Stock actualizado correctamente', 'vf-extras'));
    }
    
    /**
     * AJAX: Get reports data
     */
    public function ajax_get_reports_data() {
        check_ajax_referer('vf_extras_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('No tienes permisos para realizar esta acción.', 'vf-extras'));
        }
        
        $revenue_data = $this->get_revenue_data();
        $top_products_data = $this->get_top_products_data();
        
        wp_send_json_success(array(
            'revenue' => $revenue_data,
            'topProducts' => $top_products_data
        ));
    }
    
    /**
     * Helper functions
     */
    private function get_today_sales() {
        global $wpdb;
        
        $today = date('Y-m-d');
        $result = $wpdb->get_var($wpdb->prepare("
            SELECT SUM(pm.meta_value) 
            FROM {$wpdb->postmeta} pm
            JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key = '_order_total'
            AND p.post_type = 'shop_order'
            AND p.post_status = 'wc-completed'
            AND DATE(p.post_date) = %s
        ", $today));
        
        return $result ? floatval($result) : 0;
    }
    
    private function get_month_sales() {
        global $wpdb;
        
        $month = date('Y-m');
        $result = $wpdb->get_var($wpdb->prepare("
            SELECT SUM(pm.meta_value) 
            FROM {$wpdb->postmeta} pm
            JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key = '_order_total'
            AND p.post_type = 'shop_order'
            AND p.post_status = 'wc-completed'
            AND DATE_FORMAT(p.post_date, '%%Y-%%m') = %s
        ", $month));
        
        return $result ? floatval($result) : 0;
    }
    
    private function get_total_orders() {
        global $wpdb;
        
        $result = $wpdb->get_var("
            SELECT COUNT(*) 
            FROM {$wpdb->posts} 
            WHERE post_type = 'shop_order' 
            AND post_status = 'wc-completed'
        ");
        
        return $result ? intval($result) : 0;
    }
    
    private function display_popular_products() {
        global $wpdb;
        
        $products = $wpdb->get_results("
            SELECT post_id, meta_value as views 
            FROM {$wpdb->postmeta} 
            WHERE meta_key = 'vf_views' 
            ORDER BY CAST(meta_value AS UNSIGNED) DESC 
            LIMIT 5
        ");
        
        if ($products) {
            echo '<ul class="vf-popular-list">';
            foreach ($products as $item) {
                $product = wc_get_product($item->post_id);
                if ($product) {
                    echo '<li>';
                    echo '<strong>' . $product->get_name() . '</strong>';
                    echo ' - ' . $item->views . ' ' . __('visualizaciones', 'vf-extras');
                    echo '</li>';
                }
            }
            echo '</ul>';
        } else {
            echo '<p>' . __('No hay datos de visualizaciones.', 'vf-extras') . '</p>';
        }
    }
    
    private function get_revenue_data() {
        global $wpdb;
        
        $data = array(
            'labels' => array(),
            'revenue' => array(),
            'orders' => array()
        );
        
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $data['labels'][] = date('M j', strtotime($date));
            
            // Revenue
            $revenue = $wpdb->get_var($wpdb->prepare("
                SELECT SUM(pm.meta_value) 
                FROM {$wpdb->postmeta} pm
                JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                WHERE pm.meta_key = '_order_total'
                AND p.post_type = 'shop_order'
                AND p.post_status = 'wc-completed'
                AND DATE(p.post_date) = %s
            ", $date));
            
            $data['revenue'][] = $revenue ? floatval($revenue) : 0;
            
            // Orders
            $orders = $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) 
                FROM {$wpdb->posts} 
                WHERE post_type = 'shop_order'
                AND post_status = 'wc-completed'
                AND DATE(post_date) = %s
            ", $date));
            
            $data['orders'][] = $orders ? intval($orders) : 0;
        }
        
        return $data;
    }
    
    private function get_top_products_data() {
        global $wpdb;
        
        $products = $wpdb->get_results("
            SELECT post_id, meta_value as views 
            FROM {$wpdb->postmeta} 
            WHERE meta_key = 'vf_views' 
            ORDER BY CAST(meta_value AS UNSIGNED) DESC 
            LIMIT 10
        ");
        
        $data = array(
            'labels' => array(),
            'views' => array()
        );
        
        foreach ($products as $item) {
            $product = wc_get_product($item->post_id);
            if ($product) {
                $data['labels'][] = $product->get_name();
                $data['views'][] = intval($item->views);
            }
        }
        
        return $data;
    }
}

// Initialize plugin
VF_Extras_Plugin::get_instance();