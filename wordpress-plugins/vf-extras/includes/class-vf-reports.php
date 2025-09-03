<?php
/**
 * VF Reports - Analytics and reporting functionality
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class VF_Reports {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // No hooks needed here, methods are called directly
    }
    
    /**
     * Get revenue data for the last 30 days
     */
    public function get_revenue_data($days = 30) {
        global $wpdb;
        
        $start_date = date('Y-m-d', strtotime("-{$days} days"));
        $end_date = date('Y-m-d');
        
        $query = $wpdb->prepare("
            SELECT 
                DATE(p.post_date) as order_date,
                COALESCE(SUM(pm.meta_value), 0) as total_revenue
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_order_total'
            WHERE p.post_type = 'shop_order'
            AND p.post_status IN ('wc-completed', 'wc-processing')
            AND DATE(p.post_date) BETWEEN %s AND %s
            GROUP BY DATE(p.post_date)
            ORDER BY order_date ASC
        ", $start_date, $end_date);
        
        $results = $wpdb->get_results($query);
        
        // Fill in missing dates with zero revenue
        $data = array();
        $current_date = strtotime($start_date);
        $end_timestamp = strtotime($end_date);
        
        while ($current_date <= $end_timestamp) {
            $date_str = date('Y-m-d', $current_date);
            $revenue = 0;
            
            foreach ($results as $result) {
                if ($result->order_date === $date_str) {
                    $revenue = floatval($result->total_revenue);
                    break;
                }
            }
            
            $data[] = array(
                'date' => $date_str,
                'revenue' => $revenue,
                'formatted_date' => date_i18n('M d', $current_date),
                'formatted_revenue' => number_format($revenue, 0, ',', '.')
            );
            
            $current_date = strtotime('+1 day', $current_date);
        }
        
        return $data;
    }
    
    /**
     * Get orders data for the last 30 days
     */
    public function get_orders_data($days = 30) {
        global $wpdb;
        
        $start_date = date('Y-m-d', strtotime("-{$days} days"));
        $end_date = date('Y-m-d');
        
        $query = $wpdb->prepare("
            SELECT 
                DATE(post_date) as order_date,
                COUNT(*) as order_count
            FROM {$wpdb->posts}
            WHERE post_type = 'shop_order'
            AND post_status IN ('wc-completed', 'wc-processing', 'wc-pending')
            AND DATE(post_date) BETWEEN %s AND %s
            GROUP BY DATE(post_date)
            ORDER BY order_date ASC
        ", $start_date, $end_date);
        
        $results = $wpdb->get_results($query);
        
        // Fill in missing dates with zero orders
        $data = array();
        $current_date = strtotime($start_date);
        $end_timestamp = strtotime($end_date);
        
        while ($current_date <= $end_timestamp) {
            $date_str = date('Y-m-d', $current_date);
            $orders = 0;
            
            foreach ($results as $result) {
                if ($result->order_date === $date_str) {
                    $orders = intval($result->order_count);
                    break;
                }
            }
            
            $data[] = array(
                'date' => $date_str,
                'orders' => $orders,
                'formatted_date' => date_i18n('M d', $current_date)
            );
            
            $current_date = strtotime('+1 day', $current_date);
        }
        
        return $data;
    }
    
    /**
     * Get top 10 products by views
     */
    public function get_top_products_by_views($limit = 10) {
        global $wpdb;
        
        $query = $wpdb->prepare("
            SELECT 
                p.ID,
                p.post_title,
                COALESCE(pm.meta_value, 0) as views,
                pm2.meta_value as price
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'vf_views'
            LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_price'
            WHERE p.post_type = 'product'
            AND p.post_status = 'publish'
            ORDER BY CAST(pm.meta_value AS UNSIGNED) DESC
            LIMIT %d
        ", $limit);
        
        $results = $wpdb->get_results($query);
        
        $data = array();
        foreach ($results as $result) {
            $product = wc_get_product($result->ID);
            if (!$product) continue;
            
            $data[] = array(
                'id' => $result->ID,
                'title' => $result->post_title,
                'views' => intval($result->views),
                'price' => $product->get_price(),
                'formatted_price' => $product->get_price_html(),
                'url' => get_permalink($result->ID),
                'thumbnail' => get_the_post_thumbnail_url($result->ID, 'thumbnail'),
                'stock_status' => $product->get_stock_status(),
                'stock_quantity' => $product->get_stock_quantity()
            );
        }
        
        return $data;
    }
    
    /**
     * Get top selling products
     */
    public function get_top_selling_products($days = 30, $limit = 10) {
        global $wpdb;
        
        $start_date = date('Y-m-d', strtotime("-{$days} days"));
        
        $query = $wpdb->prepare("
            SELECT 
                pm.meta_value as product_id,
                p.post_title,
                SUM(pm2.meta_value) as total_sold,
                SUM(pm3.meta_value) as total_revenue
            FROM {$wpdb->posts} po
            JOIN {$wpdb->postmeta} pm ON po.ID = pm.post_id AND pm.meta_key = '_product_id'
            JOIN {$wpdb->postmeta} pm2 ON po.ID = pm2.post_id AND pm2.meta_key = '_qty'
            JOIN {$wpdb->postmeta} pm3 ON po.ID = pm3.post_id AND pm3.meta_key = '_line_total'
            JOIN {$wpdb->posts} p ON pm.meta_value = p.ID
            JOIN {$wpdb->posts} parent ON po.post_parent = parent.ID
            WHERE po.post_type = 'shop_order_line_item'
            AND parent.post_status IN ('wc-completed', 'wc-processing')
            AND DATE(parent.post_date) >= %s
            GROUP BY pm.meta_value
            ORDER BY total_sold DESC
            LIMIT %d
        ", $start_date, $limit);
        
        $results = $wpdb->get_results($query);
        
        $data = array();
        foreach ($results as $result) {
            $product = wc_get_product($result->product_id);
            if (!$product) continue;
            
            $data[] = array(
                'id' => $result->product_id,
                'title' => $result->post_title,
                'total_sold' => intval($result->total_sold),
                'total_revenue' => floatval($result->total_revenue),
                'formatted_revenue' => wc_price($result->total_revenue),
                'url' => get_permalink($result->product_id),
                'thumbnail' => get_the_post_thumbnail_url($result->product_id, 'thumbnail')
            );
        }
        
        return $data;
    }
    
    /**
     * Get customer statistics
     */
    public function get_customer_stats() {
        global $wpdb;
        
        // Total customers
        $total_customers = $wpdb->get_var("
            SELECT COUNT(*)
            FROM {$wpdb->users} u
            JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
            WHERE um.meta_key = 'wp_capabilities'
            AND um.meta_value LIKE '%customer%'
        ");
        
        // New customers this month
        $start_of_month = date('Y-m-01');
        $new_customers_month = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*)
            FROM {$wpdb->users} u
            JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
            WHERE um.meta_key = 'wp_capabilities'
            AND um.meta_value LIKE '%customer%'
            AND DATE(u.user_registered) >= %s
        ", $start_of_month));
        
        // Distributors
        $distributors = $wpdb->get_var("
            SELECT COUNT(*)
            FROM {$wpdb->users} u
            JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
            WHERE um.meta_key = 'wp_capabilities'
            AND um.meta_value LIKE '%distribuidor%'
        ");
        
        // Customers with orders
        $customers_with_orders = $wpdb->get_var("
            SELECT COUNT(DISTINCT pm.meta_value)
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_customer_user'
            WHERE p.post_type = 'shop_order'
            AND p.post_status IN ('wc-completed', 'wc-processing')
            AND pm.meta_value > 0
        ");
        
        return array(
            'total_customers' => intval($total_customers),
            'new_customers_month' => intval($new_customers_month),
            'distributors' => intval($distributors),
            'customers_with_orders' => intval($customers_with_orders),
            'conversion_rate' => $total_customers > 0 ? round(($customers_with_orders / $total_customers) * 100, 2) : 0
        );
    }
    
    /**
     * Get order statistics
     */
    public function get_order_stats($days = 30) {
        global $wpdb;
        
        $start_date = date('Y-m-d', strtotime("-{$days} days"));
        
        // Total orders
        $total_orders = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*)
            FROM {$wpdb->posts}
            WHERE post_type = 'shop_order'
            AND post_status IN ('wc-completed', 'wc-processing', 'wc-pending')
            AND DATE(post_date) >= %s
        ", $start_date));
        
        // Completed orders
        $completed_orders = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*)
            FROM {$wpdb->posts}
            WHERE post_type = 'shop_order'
            AND post_status = 'wc-completed'
            AND DATE(post_date) >= %s
        ", $start_date));
        
        // Pending orders
        $pending_orders = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*)
            FROM {$wpdb->posts}
            WHERE post_type = 'shop_order'
            AND post_status = 'wc-pending'
            AND DATE(post_date) >= %s
        ", $start_date));
        
        // Total revenue
        $total_revenue = $wpdb->get_var($wpdb->prepare("
            SELECT SUM(pm.meta_value)
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_order_total'
            WHERE p.post_type = 'shop_order'
            AND p.post_status IN ('wc-completed', 'wc-processing')
            AND DATE(p.post_date) >= %s
        ", $start_date));
        
        // Average order value
        $avg_order_value = $completed_orders > 0 ? ($total_revenue / $completed_orders) : 0;
        
        return array(
            'total_orders' => intval($total_orders),
            'completed_orders' => intval($completed_orders),
            'pending_orders' => intval($pending_orders),
            'total_revenue' => floatval($total_revenue) ?: 0,
            'formatted_revenue' => wc_price($total_revenue ?: 0),
            'avg_order_value' => $avg_order_value,
            'formatted_avg' => wc_price($avg_order_value),
            'completion_rate' => $total_orders > 0 ? round(($completed_orders / $total_orders) * 100, 2) : 0
        );
    }
    
    /**
     * Get product statistics
     */
    public function get_product_stats() {
        global $wpdb;
        
        // Total products
        $total_products = $wpdb->get_var("
            SELECT COUNT(*)
            FROM {$wpdb->posts}
            WHERE post_type = 'product'
            AND post_status = 'publish'
        ");
        
        // Products in stock
        $in_stock = $wpdb->get_var("
            SELECT COUNT(*)
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_stock_status'
            WHERE p.post_type = 'product'
            AND p.post_status = 'publish'
            AND pm.meta_value = 'instock'
        ");
        
        // Products out of stock
        $out_of_stock = $wpdb->get_var("
            SELECT COUNT(*)
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_stock_status'
            WHERE p.post_type = 'product'
            AND p.post_status = 'publish'
            AND pm.meta_value = 'outofstock'
        ");
        
        // Low stock products (less than 5 units)
        $low_stock = $wpdb->get_var("
            SELECT COUNT(*)
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_stock'
            WHERE p.post_type = 'product'
            AND p.post_status = 'publish'
            AND CAST(pm.meta_value AS UNSIGNED) <= 5
            AND CAST(pm.meta_value AS UNSIGNED) > 0
        ");
        
        // Featured products
        $featured = $wpdb->get_var("
            SELECT COUNT(*)
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_featured'
            WHERE p.post_type = 'product'
            AND p.post_status = 'publish'
            AND pm.meta_value = 'yes'
        ");
        
        return array(
            'total_products' => intval($total_products),
            'in_stock' => intval($in_stock),
            'out_of_stock' => intval($out_of_stock),
            'low_stock' => intval($low_stock),
            'featured' => intval($featured),
            'stock_rate' => $total_products > 0 ? round(($in_stock / $total_products) * 100, 2) : 0
        );
    }
    
    /**
     * Get comprehensive reports data for AJAX
     */
    public function get_reports_data() {
        return array(
            'revenue_data' => $this->get_revenue_data(30),
            'orders_data' => $this->get_orders_data(30),
            'top_products_views' => $this->get_top_products_by_views(10),
            'top_selling_products' => $this->get_top_selling_products(30, 10),
            'customer_stats' => $this->get_customer_stats(),
            'order_stats' => $this->get_order_stats(30),
            'product_stats' => $this->get_product_stats()
        );
    }
    
    /**
     * Get low stock products
     */
    public function get_low_stock_products($threshold = 5) {
        global $wpdb;
        
        $query = $wpdb->prepare("
            SELECT 
                p.ID,
                p.post_title,
                pm.meta_value as stock_quantity
            FROM {$wpdb->posts} p
            JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_stock'
            WHERE p.post_type = 'product'
            AND p.post_status = 'publish'
            AND CAST(pm.meta_value AS UNSIGNED) <= %d
            AND CAST(pm.meta_value AS UNSIGNED) > 0
            ORDER BY CAST(pm.meta_value AS UNSIGNED) ASC
        ", $threshold);
        
        $results = $wpdb->get_results($query);
        
        $data = array();
        foreach ($results as $result) {
            $product = wc_get_product($result->ID);
            if (!$product) continue;
            
            $data[] = array(
                'id' => $result->ID,
                'title' => $result->post_title,
                'stock_quantity' => intval($result->stock_quantity),
                'price' => $product->get_price(),
                'formatted_price' => $product->get_price_html(),
                'url' => get_permalink($result->ID),
                'edit_url' => get_edit_post_link($result->ID),
                'thumbnail' => get_the_post_thumbnail_url($result->ID, 'thumbnail')
            );
        }
        
        return $data;
    }
}