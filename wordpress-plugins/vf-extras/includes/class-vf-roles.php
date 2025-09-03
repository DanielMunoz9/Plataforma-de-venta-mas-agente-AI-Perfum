<?php
/**
 * VF Roles - User role management
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class VF_Roles {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'maybe_create_roles'));
        add_action('wp_login', array($this, 'track_user_login'), 10, 2);
        add_filter('user_row_actions', array($this, 'add_user_actions'), 10, 2);
        add_action('wp_ajax_vf_promote_to_distribuidor', array($this, 'ajax_promote_to_distribuidor'));
    }
    
    /**
     * Create distribuidor role if it doesn't exist
     */
    public function maybe_create_roles() {
        if (!get_role('distribuidor')) {
            $this->create_distribuidor_role();
        }
    }
    
    /**
     * Create distribuidor role
     */
    public static function create_distribuidor_role() {
        // Get customer capabilities as base
        $customer_caps = get_role('customer')->capabilities;
        
        // Add distribuidor specific capabilities
        $distribuidor_caps = array_merge($customer_caps, array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'view_distribuidor_prices' => true,
            'access_distribuidor_reports' => true,
            'manage_distribuidor_orders' => true,
        ));
        
        add_role(
            'distribuidor',
            __('Distribuidor', 'vf-extras'),
            $distribuidor_caps
        );
        
        return true;
    }
    
    /**
     * Track user login for statistics
     */
    public function track_user_login($user_login, $user) {
        // Update last login time
        update_user_meta($user->ID, 'vf_last_login', current_time('mysql'));
        
        // Track login count
        $login_count = get_user_meta($user->ID, 'vf_login_count', true);
        $login_count = $login_count ? intval($login_count) + 1 : 1;
        update_user_meta($user->ID, 'vf_login_count', $login_count);
        
        // Track login by role
        if (in_array('distribuidor', $user->roles)) {
            $this->track_distribuidor_activity($user->ID, 'login');
        }
    }
    
    /**
     * Track distribuidor activity
     */
    private function track_distribuidor_activity($user_id, $activity_type) {
        $activity_log = get_user_meta($user_id, 'vf_distribuidor_activity', true);
        if (!is_array($activity_log)) {
            $activity_log = array();
        }
        
        $activity_log[] = array(
            'type' => $activity_type,
            'timestamp' => current_time('mysql'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        );
        
        // Keep only last 50 activities
        if (count($activity_log) > 50) {
            $activity_log = array_slice($activity_log, -50);
        }
        
        update_user_meta($user_id, 'vf_distribuidor_activity', $activity_log);
    }
    
    /**
     * Add user actions in admin
     */
    public function add_user_actions($actions, $user_object) {
        if (!current_user_can('edit_users')) {
            return $actions;
        }
        
        // Don't show for current user or admin users
        if ($user_object->ID == get_current_user_id() || in_array('administrator', $user_object->roles)) {
            return $actions;
        }
        
        // Add promote to distribuidor action for customers
        if (in_array('customer', $user_object->roles) && !in_array('distribuidor', $user_object->roles)) {
            $actions['promote_distribuidor'] = sprintf(
                '<a href="#" class="vf-promote-distribuidor" data-user-id="%d">%s</a>',
                $user_object->ID,
                __('Promover a Distribuidor', 'vf-extras')
            );
        }
        
        // Add demote from distribuidor action
        if (in_array('distribuidor', $user_object->roles)) {
            $actions['demote_distribuidor'] = sprintf(
                '<a href="#" class="vf-demote-distribuidor" data-user-id="%d">%s</a>',
                $user_object->ID,
                __('Quitar Rol Distribuidor', 'vf-extras')
            );
        }
        
        return $actions;
    }
    
    /**
     * AJAX handler to promote user to distribuidor
     */
    public function ajax_promote_to_distribuidor() {
        check_ajax_referer('vf_extras_admin_nonce', 'nonce');
        
        if (!current_user_can('edit_users')) {
            wp_send_json_error(__('Permisos insuficientes', 'vf-extras'));
        }
        
        $user_id = intval($_POST['user_id']);
        $action = sanitize_text_field($_POST['action_type']);
        
        $user = get_user_by('id', $user_id);
        if (!$user) {
            wp_send_json_error(__('Usuario no encontrado', 'vf-extras'));
        }
        
        if ($action === 'promote') {
            $user->add_role('distribuidor');
            $this->track_distribuidor_activity($user_id, 'promoted');
            $message = __('Usuario promovido a distribuidor exitosamente', 'vf-extras');
        } else {
            $user->remove_role('distribuidor');
            $this->track_distribuidor_activity($user_id, 'demoted');
            $message = __('Rol distribuidor removido exitosamente', 'vf-extras');
        }
        
        wp_send_json_success($message);
    }
    
    /**
     * Get distribuidor statistics
     */
    public static function get_distribuidor_stats() {
        global $wpdb;
        
        // Get all distributors
        $distributors = get_users(array(
            'role' => 'distribuidor',
            'fields' => 'all'
        ));
        
        $total_distributors = count($distributors);
        $active_distributors = 0;
        $recent_logins = 0;
        $total_orders = 0;
        $total_revenue = 0;
        
        $thirty_days_ago = date('Y-m-d H:i:s', strtotime('-30 days'));
        $seven_days_ago = date('Y-m-d H:i:s', strtotime('-7 days'));
        
        foreach ($distributors as $distributor) {
            $last_login = get_user_meta($distributor->ID, 'vf_last_login', true);
            
            // Count active distributors (logged in within last 30 days)
            if ($last_login && $last_login > $thirty_days_ago) {
                $active_distributors++;
            }
            
            // Count recent logins (within last 7 days)
            if ($last_login && $last_login > $seven_days_ago) {
                $recent_logins++;
            }
            
            // Get orders for this distributor
            $orders = wc_get_orders(array(
                'customer_id' => $distributor->ID,
                'status' => array('wc-completed', 'wc-processing'),
                'limit' => -1,
                'date_created' => '>=' . strtotime('-30 days')
            ));
            
            $distributor_orders = count($orders);
            $distributor_revenue = 0;
            
            foreach ($orders as $order) {
                $distributor_revenue += $order->get_total();
            }
            
            $total_orders += $distributor_orders;
            $total_revenue += $distributor_revenue;
            
            // Update distributor stats
            update_user_meta($distributor->ID, 'vf_distribuidor_orders_30d', $distributor_orders);
            update_user_meta($distributor->ID, 'vf_distribuidor_revenue_30d', $distributor_revenue);
        }
        
        return array(
            'total_distributors' => $total_distributors,
            'active_distributors' => $active_distributors,
            'recent_logins' => $recent_logins,
            'total_orders' => $total_orders,
            'total_revenue' => $total_revenue,
            'formatted_revenue' => wc_price($total_revenue),
            'avg_orders_per_distributor' => $total_distributors > 0 ? round($total_orders / $total_distributors, 2) : 0,
            'avg_revenue_per_distributor' => $total_distributors > 0 ? ($total_revenue / $total_distributors) : 0,
            'formatted_avg_revenue' => $total_distributors > 0 ? wc_price($total_revenue / $total_distributors) : wc_price(0),
            'activity_rate' => $total_distributors > 0 ? round(($active_distributors / $total_distributors) * 100, 2) : 0
        );
    }
    
    /**
     * Get top performing distributors
     */
    public static function get_top_distributors($limit = 10) {
        $distributors = get_users(array(
            'role' => 'distribuidor',
            'fields' => 'all'
        ));
        
        $distributor_data = array();
        
        foreach ($distributors as $distributor) {
            $orders_30d = get_user_meta($distributor->ID, 'vf_distribuidor_orders_30d', true) ?: 0;
            $revenue_30d = get_user_meta($distributor->ID, 'vf_distribuidor_revenue_30d', true) ?: 0;
            $last_login = get_user_meta($distributor->ID, 'vf_last_login', true);
            $login_count = get_user_meta($distributor->ID, 'vf_login_count', true) ?: 0;
            
            $distributor_data[] = array(
                'id' => $distributor->ID,
                'name' => $distributor->display_name,
                'email' => $distributor->user_email,
                'orders_30d' => intval($orders_30d),
                'revenue_30d' => floatval($revenue_30d),
                'formatted_revenue' => wc_price($revenue_30d),
                'last_login' => $last_login,
                'formatted_last_login' => $last_login ? date_i18n('M j, Y g:i A', strtotime($last_login)) : __('Nunca', 'vf-extras'),
                'login_count' => intval($login_count),
                'registered_date' => $distributor->user_registered,
                'profile_url' => get_edit_user_link($distributor->ID)
            );
        }
        
        // Sort by revenue
        usort($distributor_data, function($a, $b) {
            return $b['revenue_30d'] <=> $a['revenue_30d'];
        });
        
        return array_slice($distributor_data, 0, $limit);
    }
    
    /**
     * Check if user can view distribuidor prices
     */
    public static function can_view_distribuidor_prices($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        if (!$user_id) {
            return false;
        }
        
        $user = get_user_by('id', $user_id);
        if (!$user) {
            return false;
        }
        
        return in_array('distribuidor', $user->roles) || in_array('administrator', $user->roles);
    }
    
    /**
     * Get user role display name
     */
    public static function get_user_role_display($user_id) {
        $user = get_user_by('id', $user_id);
        if (!$user) {
            return __('Usuario no encontrado', 'vf-extras');
        }
        
        $roles = $user->roles;
        $role_names = array();
        
        foreach ($roles as $role) {
            switch ($role) {
                case 'administrator':
                    $role_names[] = __('Administrador', 'vf-extras');
                    break;
                case 'distribuidor':
                    $role_names[] = __('Distribuidor', 'vf-extras');
                    break;
                case 'customer':
                    $role_names[] = __('Cliente', 'vf-extras');
                    break;
                default:
                    $role_names[] = ucfirst($role);
            }
        }
        
        return implode(', ', $role_names);
    }
    
    /**
     * Get distribuidor registration trends
     */
    public static function get_distribuidor_registration_trends($days = 30) {
        global $wpdb;
        
        $start_date = date('Y-m-d', strtotime("-{$days} days"));
        
        // Get distributors registered in the period
        $query = $wpdb->prepare("
            SELECT 
                DATE(u.user_registered) as reg_date,
                COUNT(*) as registrations
            FROM {$wpdb->users} u
            JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
            WHERE um.meta_key = 'wp_capabilities'
            AND um.meta_value LIKE '%distribuidor%'
            AND DATE(u.user_registered) >= %s
            GROUP BY DATE(u.user_registered)
            ORDER BY reg_date ASC
        ", $start_date);
        
        $results = $wpdb->get_results($query);
        
        // Fill in missing dates
        $data = array();
        $current_date = strtotime($start_date);
        $end_timestamp = strtotime(date('Y-m-d'));
        
        while ($current_date <= $end_timestamp) {
            $date_str = date('Y-m-d', $current_date);
            $registrations = 0;
            
            foreach ($results as $result) {
                if ($result->reg_date === $date_str) {
                    $registrations = intval($result->registrations);
                    break;
                }
            }
            
            $data[] = array(
                'date' => $date_str,
                'registrations' => $registrations,
                'formatted_date' => date_i18n('M d', $current_date)
            );
            
            $current_date = strtotime('+1 day', $current_date);
        }
        
        return $data;
    }
}