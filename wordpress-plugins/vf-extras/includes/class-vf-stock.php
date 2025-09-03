<?php
/**
 * VF Stock - Quick stock management functionality
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class VF_Stock {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_ajax_vf_quick_stock_update', array($this, 'ajax_quick_stock_update'));
        add_action('wp_ajax_vf_bulk_stock_update', array($this, 'ajax_bulk_stock_update'));
        add_action('wp_ajax_vf_get_product_stock', array($this, 'ajax_get_product_stock'));
        add_action('wp_ajax_vf_search_products', array($this, 'ajax_search_products'));
        add_action('woocommerce_product_after_variable_attributes', array($this, 'add_variation_stock_fields'), 10, 3);
        add_action('woocommerce_save_product_variation', array($this, 'save_variation_stock'), 10, 2);
    }
    
    /**
     * AJAX handler for quick stock update
     */
    public function ajax_quick_stock_update() {
        check_ajax_referer('vf_extras_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(__('Permisos insuficientes', 'vf-extras'));
        }
        
        $product_id = intval($_POST['product_id']);
        $stock_quantity = intval($_POST['stock_quantity']);
        $manage_stock = isset($_POST['manage_stock']) ? true : false;
        $stock_status = sanitize_text_field($_POST['stock_status']);
        
        $product = wc_get_product($product_id);
        if (!$product) {
            wp_send_json_error(__('Producto no encontrado', 'vf-extras'));
        }
        
        // Save old stock for comparison
        $old_stock = $product->get_stock_quantity();
        $old_status = $product->get_stock_status();
        
        try {
            // Update stock management
            $product->set_manage_stock($manage_stock);
            
            if ($manage_stock) {
                $product->set_stock_quantity($stock_quantity);
                $product->set_stock_status($stock_quantity > 0 ? 'instock' : 'outofstock');
            } else {
                $product->set_stock_status($stock_status);
            }
            
            $product->save();
            
            // Log the stock change
            $this->log_stock_change($product_id, $old_stock, $stock_quantity, get_current_user_id());
            
            // Check for low stock alert
            $low_stock_threshold = get_option('woocommerce_notify_low_stock_amount', 2);
            $alert_message = '';
            
            if ($manage_stock && $stock_quantity <= $low_stock_threshold && $stock_quantity > 0) {
                $alert_message = sprintf(
                    __('¡Atención! El producto "%s" tiene stock bajo (%d unidades)', 'vf-extras'),
                    $product->get_name(),
                    $stock_quantity
                );
            }
            
            wp_send_json_success(array(
                'message' => __('Stock actualizado correctamente', 'vf-extras'),
                'new_stock' => $stock_quantity,
                'new_status' => $product->get_stock_status(),
                'formatted_stock' => $this->format_stock_display($product),
                'alert' => $alert_message
            ));
            
        } catch (Exception $e) {
            wp_send_json_error(__('Error al actualizar el stock: ', 'vf-extras') . $e->getMessage());
        }
    }
    
    /**
     * AJAX handler for bulk stock update
     */
    public function ajax_bulk_stock_update() {
        check_ajax_referer('vf_extras_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(__('Permisos insuficientes', 'vf-extras'));
        }
        
        $updates = $_POST['updates'];
        if (!is_array($updates)) {
            wp_send_json_error(__('Datos de actualización inválidos', 'vf-extras'));
        }
        
        $success_count = 0;
        $error_count = 0;
        $errors = array();
        
        foreach ($updates as $update) {
            $product_id = intval($update['product_id']);
            $stock_quantity = intval($update['stock_quantity']);
            
            $product = wc_get_product($product_id);
            if (!$product) {
                $error_count++;
                $errors[] = sprintf(__('Producto ID %d no encontrado', 'vf-extras'), $product_id);
                continue;
            }
            
            try {
                $old_stock = $product->get_stock_quantity();
                
                $product->set_manage_stock(true);
                $product->set_stock_quantity($stock_quantity);
                $product->set_stock_status($stock_quantity > 0 ? 'instock' : 'outofstock');
                $product->save();
                
                $this->log_stock_change($product_id, $old_stock, $stock_quantity, get_current_user_id());
                $success_count++;
                
            } catch (Exception $e) {
                $error_count++;
                $errors[] = sprintf(
                    __('Error en producto %s: %s', 'vf-extras'),
                    $product->get_name(),
                    $e->getMessage()
                );
            }
        }
        
        $message = sprintf(
            __('Actualización masiva completada: %d exitosos, %d errores', 'vf-extras'),
            $success_count,
            $error_count
        );
        
        wp_send_json_success(array(
            'message' => $message,
            'success_count' => $success_count,
            'error_count' => $error_count,
            'errors' => $errors
        ));
    }
    
    /**
     * AJAX handler to get product stock info
     */
    public function ajax_get_product_stock() {
        check_ajax_referer('vf_extras_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(__('Permisos insuficientes', 'vf-extras'));
        }
        
        $product_id = intval($_POST['product_id']);
        $product = wc_get_product($product_id);
        
        if (!$product) {
            wp_send_json_error(__('Producto no encontrado', 'vf-extras'));
        }
        
        $stock_data = array(
            'id' => $product_id,
            'name' => $product->get_name(),
            'sku' => $product->get_sku(),
            'type' => $product->get_type(),
            'manage_stock' => $product->get_manage_stock(),
            'stock_quantity' => $product->get_stock_quantity(),
            'stock_status' => $product->get_stock_status(),
            'formatted_stock' => $this->format_stock_display($product),
            'price' => $product->get_price(),
            'formatted_price' => $product->get_price_html(),
            'edit_url' => get_edit_post_link($product_id),
            'view_url' => get_permalink($product_id)
        );
        
        // Add variation data for variable products
        if ($product->is_type('variable')) {
            $variations = $product->get_available_variations();
            $variation_data = array();
            
            foreach ($variations as $variation) {
                $variation_obj = wc_get_product($variation['variation_id']);
                if ($variation_obj) {
                    $variation_data[] = array(
                        'id' => $variation['variation_id'],
                        'attributes' => $variation['attributes'],
                        'manage_stock' => $variation_obj->get_manage_stock(),
                        'stock_quantity' => $variation_obj->get_stock_quantity(),
                        'stock_status' => $variation_obj->get_stock_status(),
                        'formatted_stock' => $this->format_stock_display($variation_obj)
                    );
                }
            }
            
            $stock_data['variations'] = $variation_data;
        }
        
        wp_send_json_success($stock_data);
    }
    
    /**
     * AJAX handler to search products
     */
    public function ajax_search_products() {
        check_ajax_referer('vf_extras_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(__('Permisos insuficientes', 'vf-extras'));
        }
        
        $search_term = sanitize_text_field($_POST['search_term']);
        $limit = intval($_POST['limit']) ?: 20;
        
        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            's' => $search_term,
            'meta_query' => array(
                array(
                    'key' => '_visibility',
                    'value' => array('catalog', 'visible'),
                    'compare' => 'IN'
                )
            )
        );
        
        // Also search by SKU
        if (!empty($search_term)) {
            $args['meta_query']['relation'] = 'OR';
            $args['meta_query'][] = array(
                'key' => '_sku',
                'value' => $search_term,
                'compare' => 'LIKE'
            );
        }
        
        $products = new WP_Query($args);
        $results = array();
        
        if ($products->have_posts()) {
            while ($products->have_posts()) {
                $products->the_post();
                $product = wc_get_product(get_the_ID());
                
                if ($product) {
                    $results[] = array(
                        'id' => $product->get_id(),
                        'name' => $product->get_name(),
                        'sku' => $product->get_sku(),
                        'type' => $product->get_type(),
                        'stock_quantity' => $product->get_stock_quantity(),
                        'stock_status' => $product->get_stock_status(),
                        'formatted_stock' => $this->format_stock_display($product),
                        'price' => $product->get_price(),
                        'formatted_price' => $product->get_price_html(),
                        'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail')
                    );
                }
            }
            wp_reset_postdata();
        }
        
        wp_send_json_success($results);
    }
    
    /**
     * Format stock display
     */
    private function format_stock_display($product) {
        if (!$product->get_manage_stock()) {
            return $product->get_stock_status() === 'instock' ? 
                __('En stock', 'vf-extras') : 
                __('Sin stock', 'vf-extras');
        }
        
        $stock_quantity = $product->get_stock_quantity();
        
        if ($stock_quantity === null) {
            return __('No gestionado', 'vf-extras');
        }
        
        if ($stock_quantity <= 0) {
            return __('Sin stock', 'vf-extras');
        }
        
        if ($stock_quantity <= get_option('woocommerce_notify_low_stock_amount', 2)) {
            return sprintf(__('%d (Stock bajo)', 'vf-extras'), $stock_quantity);
        }
        
        return sprintf(__('%d unidades', 'vf-extras'), $stock_quantity);
    }
    
    /**
     * Log stock changes
     */
    private function log_stock_change($product_id, $old_stock, $new_stock, $user_id) {
        $log_entry = array(
            'product_id' => $product_id,
            'old_stock' => $old_stock,
            'new_stock' => $new_stock,
            'user_id' => $user_id,
            'timestamp' => current_time('mysql'),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        );
        
        $stock_log = get_option('vf_stock_change_log', array());
        $stock_log[] = $log_entry;
        
        // Keep only last 1000 entries
        if (count($stock_log) > 1000) {
            $stock_log = array_slice($stock_log, -1000);
        }
        
        update_option('vf_stock_change_log', $stock_log);
    }
    
    /**
     * Get stock change history
     */
    public static function get_stock_change_history($product_id = null, $limit = 50) {
        $stock_log = get_option('vf_stock_change_log', array());
        
        if ($product_id) {
            $stock_log = array_filter($stock_log, function($entry) use ($product_id) {
                return $entry['product_id'] == $product_id;
            });
        }
        
        // Sort by timestamp descending
        usort($stock_log, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        return array_slice($stock_log, 0, $limit);
    }
    
    /**
     * Get low stock alerts
     */
    public static function get_low_stock_alerts($threshold = null) {
        if ($threshold === null) {
            $threshold = get_option('woocommerce_notify_low_stock_amount', 2);
        }
        
        global $wpdb;
        
        $query = $wpdb->prepare("
            SELECT 
                p.ID,
                p.post_title,
                pm1.meta_value as stock_quantity,
                pm2.meta_value as sku
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_stock'
            LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_sku'
            JOIN {$wpdb->postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_manage_stock' AND pm3.meta_value = 'yes'
            WHERE p.post_type = 'product'
            AND p.post_status = 'publish'
            AND CAST(pm1.meta_value AS UNSIGNED) <= %d
            AND CAST(pm1.meta_value AS UNSIGNED) >= 0
            ORDER BY CAST(pm1.meta_value AS UNSIGNED) ASC
        ", $threshold);
        
        $results = $wpdb->get_results($query);
        
        $alerts = array();
        foreach ($results as $result) {
            $product = wc_get_product($result->ID);
            if (!$product) continue;
            
            $stock_quantity = intval($result->stock_quantity);
            $alert_level = 'warning';
            
            if ($stock_quantity === 0) {
                $alert_level = 'danger';
            } elseif ($stock_quantity === 1) {
                $alert_level = 'warning';
            } else {
                $alert_level = 'info';
            }
            
            $alerts[] = array(
                'product_id' => $result->ID,
                'name' => $result->post_title,
                'sku' => $result->sku,
                'stock_quantity' => $stock_quantity,
                'alert_level' => $alert_level,
                'price' => $product->get_price(),
                'formatted_price' => $product->get_price_html(),
                'edit_url' => get_edit_post_link($result->ID),
                'view_url' => get_permalink($result->ID),
                'thumbnail' => get_the_post_thumbnail_url($result->ID, 'thumbnail')
            );
        }
        
        return $alerts;
    }
    
    /**
     * Add stock fields to variation admin
     */
    public function add_variation_stock_fields($loop, $variation_data, $variation) {
        $variation_obj = wc_get_product($variation->ID);
        if (!$variation_obj) return;
        
        ?>
        <div class="vf-quick-stock-variation">
            <p class="form-row form-row-full">
                <label><?php _e('Stock Rápido:', 'vf-extras'); ?></label>
                <input type="number" 
                       class="vf-variation-stock" 
                       data-variation-id="<?php echo $variation->ID; ?>"
                       value="<?php echo esc_attr($variation_obj->get_stock_quantity()); ?>" 
                       placeholder="<?php _e('Cantidad', 'vf-extras'); ?>" />
                <button type="button" 
                        class="button vf-update-variation-stock" 
                        data-variation-id="<?php echo $variation->ID; ?>">
                    <?php _e('Actualizar', 'vf-extras'); ?>
                </button>
            </p>
        </div>
        <?php
    }
    
    /**
     * Save variation stock via AJAX
     */
    public function save_variation_stock($variation_id, $i) {
        // This is handled via AJAX in the frontend
    }
    
    /**
     * Get stock statistics
     */
    public static function get_stock_statistics() {
        global $wpdb;
        
        // Total products with stock management
        $managed_products = $wpdb->get_var("
            SELECT COUNT(*)
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_manage_stock'
            WHERE p.post_type = 'product'
            AND p.post_status = 'publish'
            AND pm.meta_value = 'yes'
        ");
        
        // Products with stock
        $in_stock_products = $wpdb->get_var("
            SELECT COUNT(*)
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_manage_stock'
            JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_stock'
            WHERE p.post_type = 'product'
            AND p.post_status = 'publish'
            AND pm1.meta_value = 'yes'
            AND CAST(pm2.meta_value AS UNSIGNED) > 0
        ");
        
        // Low stock threshold
        $low_stock_threshold = get_option('woocommerce_notify_low_stock_amount', 2);
        
        // Low stock products
        $low_stock_products = count(self::get_low_stock_alerts($low_stock_threshold));
        
        // Out of stock products
        $out_of_stock_products = $wpdb->get_var("
            SELECT COUNT(*)
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_manage_stock'
            JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_stock'
            WHERE p.post_type = 'product'
            AND p.post_status = 'publish'
            AND pm1.meta_value = 'yes'
            AND CAST(pm2.meta_value AS UNSIGNED) = 0
        ");
        
        // Total stock value
        $total_stock_value = $wpdb->get_var("
            SELECT SUM(CAST(pm1.meta_value AS UNSIGNED) * CAST(pm2.meta_value AS DECIMAL(10,2)))
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_stock'
            JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_price'
            JOIN {$wpdb->postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_manage_stock'
            WHERE p.post_type = 'product'
            AND p.post_status = 'publish'
            AND pm3.meta_value = 'yes'
            AND CAST(pm1.meta_value AS UNSIGNED) > 0
        ");
        
        return array(
            'managed_products' => intval($managed_products),
            'in_stock_products' => intval($in_stock_products),
            'low_stock_products' => $low_stock_products,
            'out_of_stock_products' => intval($out_of_stock_products),
            'stock_percentage' => $managed_products > 0 ? round(($in_stock_products / $managed_products) * 100, 2) : 0,
            'total_stock_value' => floatval($total_stock_value) ?: 0,
            'formatted_stock_value' => wc_price($total_stock_value ?: 0),
            'low_stock_threshold' => $low_stock_threshold
        );
    }
}