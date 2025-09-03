<?php
/**
 * VF Admin - Admin interface and menu management
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class VF_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_notices', array($this, 'admin_notices'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __('Vane France', 'vf-extras'),
            __('Vane France', 'vf-extras'),
            'manage_woocommerce',
            'vane-france',
            array($this, 'dashboard_page'),
            'dashicons-store',
            25
        );
        
        // Dashboard (default)
        add_submenu_page(
            'vane-france',
            __('Dashboard', 'vf-extras'),
            __('Dashboard', 'vf-extras'),
            'manage_woocommerce',
            'vane-france',
            array($this, 'dashboard_page')
        );
        
        // Reports
        add_submenu_page(
            'vane-france',
            __('Reportes', 'vf-extras'),
            __('Reportes', 'vf-extras'),
            'manage_woocommerce',
            'vane-france-reports',
            array($this, 'reports_page')
        );
        
        // Settings
        add_submenu_page(
            'vane-france',
            __('Ajustes', 'vf-extras'),
            __('Ajustes', 'vf-extras'),
            'manage_options',
            'vane-france-settings',
            array($this, 'settings_page')
        );
        
        // Quick Stock
        add_submenu_page(
            'vane-france',
            __('Stock Rápido', 'vf-extras'),
            __('Stock Rápido', 'vf-extras'),
            'manage_woocommerce',
            'vane-france-stock',
            array($this, 'stock_page')
        );
        
        // Offers (redirect to post type)
        add_submenu_page(
            'vane-france',
            __('Ofertas', 'vf-extras'),
            __('Ofertas', 'vf-extras'),
            'manage_woocommerce',
            'edit.php?post_type=vf_offer'
        );
        
        // Add Offers submenu items
        add_submenu_page(
            'vane-france',
            __('Nueva Oferta', 'vf-extras'),
            __('Nueva Oferta', 'vf-extras'),
            'manage_woocommerce',
            'post-new.php?post_type=vf_offer'
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        // WhatsApp settings
        register_setting('vf_settings_group', 'vf_whatsapp_number');
        
        // Social media settings
        register_setting('vf_settings_group', 'vf_instagram_url');
        register_setting('vf_settings_group', 'vf_facebook_url');
        register_setting('vf_settings_group', 'vf_tiktok_url');
        
        // Gift program settings
        register_setting('vf_settings_group', 'vf_gift_min_amount');
        register_setting('vf_settings_group', 'vf_gift_product_id');
        register_setting('vf_settings_group', 'vf_gift_required_role');
        
        // Email settings
        register_setting('vf_settings_group', 'vf_notification_email');
        register_setting('vf_settings_group', 'vf_low_stock_notifications');
    }
    
    /**
     * Dashboard page
     */
    public function dashboard_page() {
        $reports = VF_Reports::get_instance();
        $order_stats = $reports->get_order_stats(30);
        $customer_stats = $reports->get_customer_stats();
        $product_stats = $reports->get_product_stats();
        $distribuidor_stats = VF_Roles::get_distribuidor_stats();
        $stock_stats = VF_Stock::get_stock_statistics();
        $gift_stats = VF_Gifts::get_gift_stats();
        
        ?>
        <div class="wrap vf-admin-dashboard">
            <h1 class="wp-heading-inline"><?php _e('Dashboard Vane France', 'vf-extras'); ?></h1>
            
            <div class="vf-dashboard-stats">
                <div class="vf-stats-grid">
                    <!-- Revenue Card -->
                    <div class="vf-stat-card vf-revenue-card">
                        <div class="vf-stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="vf-stat-content">
                            <h3><?php echo $order_stats['formatted_revenue']; ?></h3>
                            <p><?php _e('Ingresos (30 días)', 'vf-extras'); ?></p>
                            <small class="vf-stat-meta">
                                <?php printf(__('Promedio por orden: %s', 'vf-extras'), $order_stats['formatted_avg']); ?>
                            </small>
                        </div>
                    </div>
                    
                    <!-- Orders Card -->
                    <div class="vf-stat-card vf-orders-card">
                        <div class="vf-stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="vf-stat-content">
                            <h3><?php echo $order_stats['total_orders']; ?></h3>
                            <p><?php _e('Órdenes Totales', 'vf-extras'); ?></p>
                            <small class="vf-stat-meta">
                                <?php printf(__('%d completadas (%s%%)', 'vf-extras'), $order_stats['completed_orders'], $order_stats['completion_rate']); ?>
                            </small>
                        </div>
                    </div>
                    
                    <!-- Customers Card -->
                    <div class="vf-stat-card vf-customers-card">
                        <div class="vf-stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="vf-stat-content">
                            <h3><?php echo $customer_stats['total_customers']; ?></h3>
                            <p><?php _e('Clientes Totales', 'vf-extras'); ?></p>
                            <small class="vf-stat-meta">
                                <?php printf(__('%d nuevos este mes', 'vf-extras'), $customer_stats['new_customers_month']); ?>
                            </small>
                        </div>
                    </div>
                    
                    <!-- Distributors Card -->
                    <div class="vf-stat-card vf-distributors-card">
                        <div class="vf-stat-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="vf-stat-content">
                            <h3><?php echo $distribuidor_stats['total_distributors']; ?></h3>
                            <p><?php _e('Distribuidores', 'vf-extras'); ?></p>
                            <small class="vf-stat-meta">
                                <?php printf(__('%d activos (%s%%)', 'vf-extras'), $distribuidor_stats['active_distributors'], $distribuidor_stats['activity_rate']); ?>
                            </small>
                        </div>
                    </div>
                    
                    <!-- Products Card -->
                    <div class="vf-stat-card vf-products-card">
                        <div class="vf-stat-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="vf-stat-content">
                            <h3><?php echo $product_stats['total_products']; ?></h3>
                            <p><?php _e('Productos', 'vf-extras'); ?></p>
                            <small class="vf-stat-meta">
                                <?php printf(__('%d en stock (%s%%)', 'vf-extras'), $product_stats['in_stock'], $product_stats['stock_rate']); ?>
                            </small>
                        </div>
                    </div>
                    
                    <!-- Gifts Card -->
                    <div class="vf-stat-card vf-gifts-card">
                        <div class="vf-stat-icon">
                            <i class="fas fa-gift"></i>
                        </div>
                        <div class="vf-stat-content">
                            <h3><?php echo $gift_stats['total_gifts_given']; ?></h3>
                            <p><?php _e('Regalos Otorgados', 'vf-extras'); ?></p>
                            <small class="vf-stat-meta">
                                <?php printf(__('Valor: %s', 'vf-extras'), $gift_stats['formatted_gift_value']); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="vf-quick-actions">
                <h2><?php _e('Acciones Rápidas', 'vf-extras'); ?></h2>
                <div class="vf-actions-grid">
                    <a href="<?php echo admin_url('admin.php?page=vane-france-reports'); ?>" class="vf-quick-action">
                        <i class="fas fa-chart-line"></i>
                        <span><?php _e('Ver Reportes', 'vf-extras'); ?></span>
                    </a>
                    <a href="<?php echo admin_url('admin.php?page=vane-france-stock'); ?>" class="vf-quick-action">
                        <i class="fas fa-warehouse"></i>
                        <span><?php _e('Gestionar Stock', 'vf-extras'); ?></span>
                    </a>
                    <a href="<?php echo admin_url('post-new.php?post_type=vf_offer'); ?>" class="vf-quick-action">
                        <i class="fas fa-tag"></i>
                        <span><?php _e('Nueva Oferta', 'vf-extras'); ?></span>
                    </a>
                    <a href="<?php echo admin_url('admin.php?page=vane-france-settings'); ?>" class="vf-quick-action">
                        <i class="fas fa-cog"></i>
                        <span><?php _e('Configuración', 'vf-extras'); ?></span>
                    </a>
                </div>
            </div>
            
            <!-- Alerts Section -->
            <?php $low_stock_alerts = VF_Stock::get_low_stock_alerts(5); ?>
            <?php if (!empty($low_stock_alerts)) : ?>
                <div class="vf-alerts-section">
                    <h2><?php _e('Alertas de Stock', 'vf-extras'); ?></h2>
                    <div class="vf-alerts-list">
                        <?php foreach (array_slice($low_stock_alerts, 0, 5) as $alert) : ?>
                            <div class="vf-alert vf-alert-<?php echo $alert['alert_level']; ?>">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span class="vf-alert-text">
                                    <strong><?php echo esc_html($alert['name']); ?></strong>
                                    <?php printf(__('- Solo quedan %d unidades', 'vf-extras'), $alert['stock_quantity']); ?>
                                </span>
                                <a href="<?php echo $alert['edit_url']; ?>" class="vf-alert-action">
                                    <?php _e('Editar', 'vf-extras'); ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                        <?php if (count($low_stock_alerts) > 5) : ?>
                            <div class="vf-alert-more">
                                <a href="<?php echo admin_url('admin.php?page=vane-france-stock'); ?>">
                                    <?php printf(__('Ver todas (%d alertas)', 'vf-extras'), count($low_stock_alerts)); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <style>
        .vf-admin-dashboard {
            max-width: 1200px;
        }
        
        .vf-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .vf-stat-card {
            background: white;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 16px;
            border-left: 4px solid #0073aa;
        }
        
        .vf-revenue-card { border-left-color: #00a32a; }
        .vf-orders-card { border-left-color: #0073aa; }
        .vf-customers-card { border-left-color: #d63638; }
        .vf-distributors-card { border-left-color: #f56e28; }
        .vf-products-card { border-left-color: #8c8f94; }
        .vf-gifts-card { border-left-color: #a7aaad; }
        
        .vf-stat-icon {
            background: #f0f0f1;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #50575e;
        }
        
        .vf-stat-content h3 {
            margin: 0 0 8px 0;
            font-size: 28px;
            font-weight: 600;
            color: #1d2327;
        }
        
        .vf-stat-content p {
            margin: 0 0 4px 0;
            font-size: 14px;
            color: #50575e;
            font-weight: 500;
        }
        
        .vf-stat-meta {
            font-size: 12px;
            color: #8c8f94;
        }
        
        .vf-quick-actions {
            margin: 40px 0;
        }
        
        .vf-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }
        
        .vf-quick-action {
            background: white;
            border: 1px solid #c3c4c7;
            border-radius: 6px;
            padding: 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
            color: #1d2327;
        }
        
        .vf-quick-action:hover {
            border-color: #0073aa;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            color: #0073aa;
        }
        
        .vf-quick-action i {
            font-size: 20px;
            color: #8c8f94;
        }
        
        .vf-quick-action:hover i {
            color: #0073aa;
        }
        
        .vf-alerts-section {
            margin-top: 40px;
        }
        
        .vf-alerts-list {
            margin-top: 16px;
        }
        
        .vf-alert {
            background: white;
            border-left: 4px solid #dba617;
            padding: 12px 16px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .vf-alert-danger { border-left-color: #d63638; }
        .vf-alert-warning { border-left-color: #dba617; }
        .vf-alert-info { border-left-color: #0073aa; }
        
        .vf-alert i {
            color: #dba617;
        }
        
        .vf-alert-danger i { color: #d63638; }
        .vf-alert-warning i { color: #dba617; }
        .vf-alert-info i { color: #0073aa; }
        
        .vf-alert-text {
            flex: 1;
        }
        
        .vf-alert-action {
            color: #0073aa;
            text-decoration: none;
            font-weight: 500;
        }
        
        .vf-alert-more {
            text-align: center;
            padding: 12px;
        }
        
        .vf-alert-more a {
            color: #0073aa;
            text-decoration: none;
        }
        </style>
        <?php
    }
    
    /**
     * Reports page
     */
    public function reports_page() {
        ?>
        <div class="wrap vf-admin-reports">
            <h1 class="wp-heading-inline"><?php _e('Reportes Vane France', 'vf-extras'); ?></h1>
            
            <div class="vf-reports-container">
                <!-- Revenue Chart -->
                <div class="vf-chart-container">
                    <h3><?php _e('Ingresos Últimos 30 Días', 'vf-extras'); ?></h3>
                    <canvas id="vf-revenue-chart" width="400" height="200"></canvas>
                </div>
                
                <!-- Orders Chart -->
                <div class="vf-chart-container">
                    <h3><?php _e('Órdenes Últimos 30 Días', 'vf-extras'); ?></h3>
                    <canvas id="vf-orders-chart" width="400" height="200"></canvas>
                </div>
                
                <!-- Top Products by Views -->
                <div class="vf-table-container">
                    <h3><?php _e('Top 10 Productos por Vistas', 'vf-extras'); ?></h3>
                    <div id="vf-top-products-table"></div>
                </div>
                
                <!-- Top Selling Products -->
                <div class="vf-table-container">
                    <h3><?php _e('Top 10 Productos Más Vendidos', 'vf-extras'); ?></h3>
                    <div id="vf-top-selling-table"></div>
                </div>
                
                <!-- Distributors Performance -->
                <div class="vf-table-container">
                    <h3><?php _e('Top Distribuidores', 'vf-extras'); ?></h3>
                    <div id="vf-distributors-table"></div>
                </div>
            </div>
        </div>
        
        <style>
        .vf-admin-reports {
            max-width: 1200px;
        }
        
        .vf-reports-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }
        
        .vf-chart-container,
        .vf-table-container {
            background: white;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .vf-chart-container h3,
        .vf-table-container h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #1d2327;
        }
        
        .vf-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .vf-table th,
        .vf-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #f0f0f1;
        }
        
        .vf-table th {
            background: #f9f9f9;
            font-weight: 600;
            color: #1d2327;
        }
        
        .vf-table tr:hover {
            background: #f9f9f9;
        }
        
        .vf-loading {
            text-align: center;
            padding: 40px;
            color: #8c8f94;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            // Load reports data
            $.post(ajaxurl, {
                action: 'vf_get_reports_data',
                nonce: '<?php echo wp_create_nonce('vf_extras_admin_nonce'); ?>'
            }, function(response) {
                if (response.success) {
                    const data = response.data;
                    
                    // Revenue Chart
                    new Chart($('#vf-revenue-chart')[0].getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: data.revenue_data.map(item => item.formatted_date),
                            datasets: [{
                                label: 'Ingresos',
                                data: data.revenue_data.map(item => item.revenue),
                                borderColor: '#00a32a',
                                backgroundColor: 'rgba(0, 163, 42, 0.1)',
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return '$' + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                    
                    // Orders Chart
                    new Chart($('#vf-orders-chart')[0].getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: data.orders_data.map(item => item.formatted_date),
                            datasets: [{
                                label: 'Órdenes',
                                data: data.orders_data.map(item => item.orders),
                                backgroundColor: '#0073aa'
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
                    
                    // Top Products Table
                    let topProductsHtml = '<table class="vf-table"><thead><tr><th>Producto</th><th>Vistas</th><th>Precio</th></tr></thead><tbody>';
                    data.top_products_views.forEach(product => {
                        topProductsHtml += `<tr>
                            <td><a href="${product.url}" target="_blank">${product.title}</a></td>
                            <td>${product.views}</td>
                            <td>${product.formatted_price}</td>
                        </tr>`;
                    });
                    topProductsHtml += '</tbody></table>';
                    $('#vf-top-products-table').html(topProductsHtml);
                    
                    // Top Selling Table
                    let topSellingHtml = '<table class="vf-table"><thead><tr><th>Producto</th><th>Vendidos</th><th>Ingresos</th></tr></thead><tbody>';
                    data.top_selling_products.forEach(product => {
                        topSellingHtml += `<tr>
                            <td><a href="${product.url}" target="_blank">${product.title}</a></td>
                            <td>${product.total_sold}</td>
                            <td>${product.formatted_revenue}</td>
                        </tr>`;
                    });
                    topSellingHtml += '</tbody></table>';
                    $('#vf-top-selling-table').html(topSellingHtml);
                    
                    // Distributors Table (would need additional data)
                    $('#vf-distributors-table').html('<p class="vf-loading">Datos de distribuidores disponibles próximamente...</p>');
                }
            });
        });
        </script>
        <?php
    }
    
    /**
     * Settings page
     */
    public function settings_page() {
        if (isset($_POST['submit'])) {
            // Save settings
            update_option('vf_whatsapp_number', sanitize_text_field($_POST['vf_whatsapp_number']));
            update_option('vf_instagram_url', esc_url_raw($_POST['vf_instagram_url']));
            update_option('vf_facebook_url', esc_url_raw($_POST['vf_facebook_url']));
            update_option('vf_tiktok_url', esc_url_raw($_POST['vf_tiktok_url']));
            update_option('vf_gift_min_amount', floatval($_POST['vf_gift_min_amount']));
            update_option('vf_gift_product_id', intval($_POST['vf_gift_product_id']));
            update_option('vf_gift_required_role', sanitize_text_field($_POST['vf_gift_required_role']));
            update_option('vf_notification_email', sanitize_email($_POST['vf_notification_email']));
            update_option('vf_low_stock_notifications', isset($_POST['vf_low_stock_notifications']));
            
            echo '<div class="notice notice-success"><p>' . __('Configuración guardada exitosamente.', 'vf-extras') . '</p></div>';
        }
        
        // Get current values
        $whatsapp_number = get_option('vf_whatsapp_number', '3193605666');
        $instagram_url = get_option('vf_instagram_url', '');
        $facebook_url = get_option('vf_facebook_url', '');
        $tiktok_url = get_option('vf_tiktok_url', '');
        $gift_min_amount = get_option('vf_gift_min_amount', 100000);
        $gift_product_id = get_option('vf_gift_product_id', 0);
        $gift_required_role = get_option('vf_gift_required_role', '');
        $notification_email = get_option('vf_notification_email', get_option('admin_email'));
        $low_stock_notifications = get_option('vf_low_stock_notifications', true);
        
        ?>
        <div class="wrap vf-admin-settings">
            <h1 class="wp-heading-inline"><?php _e('Ajustes Vane France', 'vf-extras'); ?></h1>
            
            <form method="post" action="">
                <?php wp_nonce_field('vf_settings_nonce'); ?>
                
                <!-- WhatsApp Settings -->
                <div class="vf-settings-section">
                    <h2><?php _e('Configuración de WhatsApp', 'vf-extras'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Número de WhatsApp', 'vf-extras'); ?></th>
                            <td>
                                <input type="text" name="vf_whatsapp_number" value="<?php echo esc_attr($whatsapp_number); ?>" class="regular-text" />
                                <p class="description"><?php _e('Número de WhatsApp para botón flotante y soporte (solo números)', 'vf-extras'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Social Media Settings -->
                <div class="vf-settings-section">
                    <h2><?php _e('Redes Sociales', 'vf-extras'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Instagram URL', 'vf-extras'); ?></th>
                            <td>
                                <input type="url" name="vf_instagram_url" value="<?php echo esc_attr($instagram_url); ?>" class="regular-text" />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Facebook URL', 'vf-extras'); ?></th>
                            <td>
                                <input type="url" name="vf_facebook_url" value="<?php echo esc_attr($facebook_url); ?>" class="regular-text" />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('TikTok URL', 'vf-extras'); ?></th>
                            <td>
                                <input type="url" name="vf_tiktok_url" value="<?php echo esc_attr($tiktok_url); ?>" class="regular-text" />
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Gift Program Settings -->
                <div class="vf-settings-section">
                    <h2><?php _e('Programa de Regalos', 'vf-extras'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Monto Mínimo', 'vf-extras'); ?></th>
                            <td>
                                <input type="number" name="vf_gift_min_amount" value="<?php echo esc_attr($gift_min_amount); ?>" step="0.01" />
                                <p class="description"><?php _e('Monto mínimo de compra para recibir regalo', 'vf-extras'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Producto Regalo', 'vf-extras'); ?></th>
                            <td>
                                <select name="vf_gift_product_id">
                                    <option value="0"><?php _e('Seleccionar producto...', 'vf-extras'); ?></option>
                                    <?php
                                    $products = wc_get_products(array('limit' => -1, 'status' => 'publish'));
                                    foreach ($products as $product) {
                                        printf(
                                            '<option value="%d" %s>%s</option>',
                                            $product->get_id(),
                                            selected($gift_product_id, $product->get_id(), false),
                                            esc_html($product->get_name())
                                        );
                                    }
                                    ?>
                                </select>
                                <p class="description"><?php _e('Producto que se otorgará como regalo', 'vf-extras'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Rol Requerido', 'vf-extras'); ?></th>
                            <td>
                                <select name="vf_gift_required_role">
                                    <option value=""><?php _e('Todos los usuarios', 'vf-extras'); ?></option>
                                    <option value="distribuidor" <?php selected($gift_required_role, 'distribuidor'); ?>><?php _e('Solo Distribuidores', 'vf-extras'); ?></option>
                                    <option value="customer" <?php selected($gift_required_role, 'customer'); ?>><?php _e('Solo Clientes', 'vf-extras'); ?></option>
                                </select>
                                <p class="description"><?php _e('Rol de usuario requerido para recibir regalos', 'vf-extras'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Notification Settings -->
                <div class="vf-settings-section">
                    <h2><?php _e('Notificaciones', 'vf-extras'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Email de Notificaciones', 'vf-extras'); ?></th>
                            <td>
                                <input type="email" name="vf_notification_email" value="<?php echo esc_attr($notification_email); ?>" class="regular-text" />
                                <p class="description"><?php _e('Email para recibir notificaciones del sistema', 'vf-extras'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Notificaciones de Stock Bajo', 'vf-extras'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="vf_low_stock_notifications" value="1" <?php checked($low_stock_notifications); ?> />
                                    <?php _e('Enviar notificaciones cuando el stock esté bajo', 'vf-extras'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <?php submit_button(__('Guardar Configuración', 'vf-extras')); ?>
            </form>
        </div>
        
        <style>
        .vf-admin-settings {
            max-width: 1000px;
        }
        
        .vf-settings-section {
            background: white;
            border-radius: 8px;
            padding: 24px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .vf-settings-section h2 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #1d2327;
            border-bottom: 1px solid #f0f0f1;
            padding-bottom: 10px;
        }
        </style>
        <?php
    }
    
    /**
     * Stock management page
     */
    public function stock_page() {
        ?>
        <div class="wrap vf-admin-stock">
            <h1 class="wp-heading-inline"><?php _e('Gestión Rápida de Stock', 'vf-extras'); ?></h1>
            
            <!-- Search Section -->
            <div class="vf-stock-search">
                <div class="vf-search-controls">
                    <input type="text" id="vf-product-search" placeholder="<?php _e('Buscar productos por nombre o SKU...', 'vf-extras'); ?>" />
                    <button type="button" id="vf-search-btn" class="button button-primary"><?php _e('Buscar', 'vf-extras'); ?></button>
                    <button type="button" id="vf-show-low-stock" class="button"><?php _e('Mostrar Stock Bajo', 'vf-extras'); ?></button>
                </div>
            </div>
            
            <!-- Products Table -->
            <div class="vf-stock-table-container">
                <div id="vf-stock-loading" class="vf-loading" style="display: none;">
                    <p><?php _e('Cargando productos...', 'vf-extras'); ?></p>
                </div>
                <div id="vf-stock-results"></div>
            </div>
            
            <!-- Bulk Update Section -->
            <div class="vf-bulk-update-section" style="display: none;">
                <h3><?php _e('Actualización Masiva', 'vf-extras'); ?></h3>
                <div class="vf-bulk-controls">
                    <button type="button" id="vf-bulk-update-btn" class="button button-primary"><?php _e('Actualizar Seleccionados', 'vf-extras'); ?></button>
                    <button type="button" id="vf-clear-selection" class="button"><?php _e('Limpiar Selección', 'vf-extras'); ?></button>
                </div>
            </div>
        </div>
        
        <style>
        .vf-admin-stock {
            max-width: 1200px;
        }
        
        .vf-stock-search {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .vf-search-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        #vf-product-search {
            flex: 1;
            max-width: 400px;
        }
        
        .vf-stock-table-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .vf-stock-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .vf-stock-table th,
        .vf-stock-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #f0f0f1;
        }
        
        .vf-stock-table th {
            background: #f9f9f9;
            font-weight: 600;
        }
        
        .vf-stock-input {
            width: 80px;
            text-align: center;
        }
        
        .vf-update-stock-btn {
            margin-left: 5px;
        }
        
        .vf-stock-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .vf-stock-status.in-stock {
            background: #d4edda;
            color: #155724;
        }
        
        .vf-stock-status.out-of-stock {
            background: #f8d7da;
            color: #721c24;
        }
        
        .vf-stock-status.low-stock {
            background: #fff3cd;
            color: #856404;
        }
        
        .vf-bulk-update-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .vf-loading {
            text-align: center;
            padding: 40px;
            color: #8c8f94;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            let selectedProducts = [];
            
            // Search products
            $('#vf-search-btn').on('click', function() {
                const searchTerm = $('#vf-product-search').val();
                searchProducts(searchTerm);
            });
            
            // Show low stock products
            $('#vf-show-low-stock').on('click', function() {
                showLowStockProducts();
            });
            
            // Search on enter
            $('#vf-product-search').on('keypress', function(e) {
                if (e.which === 13) {
                    $('#vf-search-btn').click();
                }
            });
            
            function searchProducts(searchTerm) {
                $('#vf-stock-loading').show();
                $('#vf-stock-results').empty();
                
                $.post(ajaxurl, {
                    action: 'vf_search_products',
                    search_term: searchTerm,
                    limit: 50,
                    nonce: '<?php echo wp_create_nonce('vf_extras_admin_nonce'); ?>'
                }, function(response) {
                    $('#vf-stock-loading').hide();
                    
                    if (response.success && response.data.length > 0) {
                        displayProductsTable(response.data);
                    } else {
                        $('#vf-stock-results').html('<p><?php _e('No se encontraron productos.', 'vf-extras'); ?></p>');
                    }
                });
            }
            
            function showLowStockProducts() {
                $('#vf-stock-loading').show();
                $('#vf-stock-results').empty();
                
                // Simulate API call for low stock products
                setTimeout(function() {
                    $('#vf-stock-loading').hide();
                    $('#vf-stock-results').html('<p><?php _e('Funcionalidad de stock bajo disponible próximamente...', 'vf-extras'); ?></p>');
                }, 1000);
            }
            
            function displayProductsTable(products) {
                let html = '<table class="vf-stock-table">';
                html += '<thead><tr>';
                html += '<th><input type="checkbox" id="vf-select-all"></th>';
                html += '<th><?php _e('Producto', 'vf-extras'); ?></th>';
                html += '<th><?php _e('SKU', 'vf-extras'); ?></th>';
                html += '<th><?php _e('Stock Actual', 'vf-extras'); ?></th>';
                html += '<th><?php _e('Nuevo Stock', 'vf-extras'); ?></th>';
                html += '<th><?php _e('Acciones', 'vf-extras'); ?></th>';
                html += '</tr></thead><tbody>';
                
                products.forEach(function(product) {
                    html += '<tr>';
                    html += `<td><input type="checkbox" class="vf-product-select" value="${product.id}"></td>`;
                    html += `<td>${product.name}</td>`;
                    html += `<td>${product.sku || '-'}</td>`;
                    html += `<td>${product.formatted_stock}</td>`;
                    html += `<td><input type="number" class="vf-stock-input" data-product-id="${product.id}" value="${product.stock_quantity || 0}" min="0"></td>`;
                    html += `<td><button type="button" class="button vf-update-stock-btn" data-product-id="${product.id}"><?php _e('Actualizar', 'vf-extras'); ?></button></td>`;
                    html += '</tr>';
                });
                
                html += '</tbody></table>';
                $('#vf-stock-results').html(html);
                
                // Show bulk update section if products found
                $('.vf-bulk-update-section').show();
            }
            
            // Handle individual stock updates
            $(document).on('click', '.vf-update-stock-btn', function() {
                const productId = $(this).data('product-id');
                const newStock = $(`.vf-stock-input[data-product-id="${productId}"]`).val();
                
                updateProductStock(productId, newStock, $(this));
            });
            
            function updateProductStock(productId, stockQuantity, button) {
                const originalText = button.text();
                button.text('<?php _e('Actualizando...', 'vf-extras'); ?>').prop('disabled', true);
                
                $.post(ajaxurl, {
                    action: 'vf_quick_stock_update',
                    product_id: productId,
                    stock_quantity: stockQuantity,
                    manage_stock: true,
                    stock_status: 'instock',
                    nonce: '<?php echo wp_create_nonce('vf_extras_admin_nonce'); ?>'
                }, function(response) {
                    button.text(originalText).prop('disabled', false);
                    
                    if (response.success) {
                        button.text('<?php _e('✓ Actualizado', 'vf-extras'); ?>');
                        setTimeout(function() {
                            button.text(originalText);
                        }, 2000);
                        
                        if (response.data.alert) {
                            alert(response.data.alert);
                        }
                    } else {
                        alert('Error: ' + response.data);
                    }
                });
            }
        });
        </script>
        <?php
    }
    
    /**
     * Admin notices
     */
    public function admin_notices() {
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            ?>
            <div class="notice notice-error">
                <p><?php _e('VF Extras requiere que WooCommerce esté instalado y activado.', 'vf-extras'); ?></p>
            </div>
            <?php
        }
        
        // Check for low stock alerts on dashboard
        if (isset($_GET['page']) && $_GET['page'] === 'vane-france') {
            $low_stock_count = count(VF_Stock::get_low_stock_alerts(5));
            if ($low_stock_count > 0) {
                ?>
                <div class="notice notice-warning">
                    <p>
                        <?php printf(__('Tienes %d productos con stock bajo. ', 'vf-extras'), $low_stock_count); ?>
                        <a href="<?php echo admin_url('admin.php?page=vane-france-stock'); ?>"><?php _e('Ver detalles', 'vf-extras'); ?></a>
                    </p>
                </div>
                <?php
            }
        }
    }
}