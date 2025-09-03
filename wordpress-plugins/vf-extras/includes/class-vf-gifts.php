<?php
/**
 * VF Gifts - Gift program functionality
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class VF_Gifts {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('woocommerce_cart_calculate_fees', array($this, 'check_and_add_gift'));
        add_action('woocommerce_checkout_order_processed', array($this, 'track_gift_usage'));
        add_filter('woocommerce_cart_item_price', array($this, 'modify_gift_price_display'), 10, 3);
        add_filter('woocommerce_cart_item_subtotal', array($this, 'modify_gift_subtotal_display'), 10, 3);
        add_action('woocommerce_add_to_cart', array($this, 'prevent_manual_gift_addition'), 10, 2);
    }
    
    /**
     * Check cart and add gift if conditions are met
     */
    public function check_and_add_gift() {
        if (is_admin() && !defined('DOING_AJAX')) {
            return;
        }
        
        $gift_min_amount = get_option('vf_gift_min_amount', 100000);
        $gift_product_id = get_option('vf_gift_product_id', 0);
        $gift_required_role = get_option('vf_gift_required_role', '');
        
        if (!$gift_product_id || $gift_min_amount <= 0) {
            return;
        }
        
        // Get cart subtotal (excluding gifts)
        $cart_subtotal = $this->get_cart_subtotal_excluding_gifts();
        
        if ($cart_subtotal >= $gift_min_amount) {
            // Check role requirement
            if ($gift_required_role && !$this->user_has_required_role($gift_required_role)) {
                return;
            }
            
            // Check if gift is already in cart
            if (!$this->is_gift_in_cart($gift_product_id)) {
                $this->add_gift_to_cart($gift_product_id);
            }
        } else {
            // Remove gift if minimum amount is not met
            $this->remove_gift_from_cart($gift_product_id);
        }
    }
    
    /**
     * Get cart subtotal excluding gift items
     */
    private function get_cart_subtotal_excluding_gifts() {
        $subtotal = 0;
        
        foreach (WC()->cart->get_cart() as $cart_item) {
            if (!isset($cart_item['is_gift']) || !$cart_item['is_gift']) {
                $subtotal += $cart_item['line_subtotal'];
            }
        }
        
        return $subtotal;
    }
    
    /**
     * Check if user has required role
     */
    private function user_has_required_role($required_role) {
        if (!is_user_logged_in()) {
            return false;
        }
        
        $user = wp_get_current_user();
        return in_array($required_role, $user->roles);
    }
    
    /**
     * Check if gift is already in cart
     */
    private function is_gift_in_cart($gift_product_id) {
        foreach (WC()->cart->get_cart() as $cart_item) {
            if ($cart_item['product_id'] == $gift_product_id && 
                isset($cart_item['is_gift']) && $cart_item['is_gift']) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Add gift to cart
     */
    private function add_gift_to_cart($gift_product_id) {
        $product = wc_get_product($gift_product_id);
        if (!$product || !$product->is_purchasable()) {
            return false;
        }
        
        // Add gift with custom cart item data
        $cart_item_key = WC()->cart->add_to_cart(
            $gift_product_id, 
            1, 
            0, 
            array(),
            array(
                'is_gift' => true,
                'original_price' => $product->get_price(),
                'gift_message' => __('¡Regalo por compra mínima!', 'vf-extras')
            )
        );
        
        if ($cart_item_key) {
            // Set price to 0 for the gift
            WC()->cart->cart_contents[$cart_item_key]['data']->set_price(0);
            
            // Add notice
            wc_add_notice(
                sprintf(
                    __('¡Felicitaciones! Has recibido %s como regalo por tu compra.', 'vf-extras'),
                    $product->get_name()
                ),
                'success'
            );
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Remove gift from cart
     */
    private function remove_gift_from_cart($gift_product_id) {
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            if ($cart_item['product_id'] == $gift_product_id && 
                isset($cart_item['is_gift']) && $cart_item['is_gift']) {
                WC()->cart->remove_cart_item($cart_item_key);
                
                wc_add_notice(
                    __('El regalo ha sido removido porque no cumples el monto mínimo requerido.', 'vf-extras'),
                    'notice'
                );
                break;
            }
        }
    }
    
    /**
     * Modify gift price display in cart
     */
    public function modify_gift_price_display($price, $cart_item, $cart_item_key) {
        if (isset($cart_item['is_gift']) && $cart_item['is_gift']) {
            return '<span class="gift-price">' . __('GRATIS', 'vf-extras') . '</span>';
        }
        return $price;
    }
    
    /**
     * Modify gift subtotal display in cart
     */
    public function modify_gift_subtotal_display($subtotal, $cart_item, $cart_item_key) {
        if (isset($cart_item['is_gift']) && $cart_item['is_gift']) {
            $gift_message = isset($cart_item['gift_message']) ? $cart_item['gift_message'] : __('Regalo', 'vf-extras');
            return '<span class="gift-subtotal">' . __('GRATIS', 'vf-extras') . '<br><small class="gift-message">' . esc_html($gift_message) . '</small></span>';
        }
        return $subtotal;
    }
    
    /**
     * Prevent manual addition of gift products
     */
    public function prevent_manual_gift_addition($cart_item_key, $product_id) {
        $gift_product_id = get_option('vf_gift_product_id', 0);
        
        if ($product_id == $gift_product_id) {
            $cart_item = WC()->cart->get_cart_item($cart_item_key);
            
            // If it's not marked as a gift, remove it
            if (!isset($cart_item['is_gift']) || !$cart_item['is_gift']) {
                WC()->cart->remove_cart_item($cart_item_key);
                
                wc_add_notice(
                    __('Este producto solo se puede agregar automáticamente como regalo.', 'vf-extras'),
                    'error'
                );
            }
        }
    }
    
    /**
     * Track gift usage when order is processed
     */
    public function track_gift_usage($order_id) {
        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }
        
        $gift_product_id = get_option('vf_gift_product_id', 0);
        $has_gift = false;
        
        foreach ($order->get_items() as $item) {
            if ($item->get_product_id() == $gift_product_id) {
                $has_gift = true;
                break;
            }
        }
        
        if ($has_gift) {
            // Track gift usage
            $gift_usage = get_option('vf_gift_usage_stats', array());
            $current_month = date('Y-m');
            
            if (!isset($gift_usage[$current_month])) {
                $gift_usage[$current_month] = 0;
            }
            
            $gift_usage[$current_month]++;
            update_option('vf_gift_usage_stats', $gift_usage);
            
            // Add order note
            $order->add_order_note(__('Orden incluye regalo por programa de fidelización.', 'vf-extras'));
        }
    }
    
    /**
     * Get gift program statistics
     */
    public static function get_gift_stats() {
        $gift_usage = get_option('vf_gift_usage_stats', array());
        $gift_product_id = get_option('vf_gift_product_id', 0);
        $gift_min_amount = get_option('vf_gift_min_amount', 100000);
        
        // Calculate total gifts given
        $total_gifts = array_sum($gift_usage);
        
        // Calculate current month gifts
        $current_month = date('Y-m');
        $current_month_gifts = isset($gift_usage[$current_month]) ? $gift_usage[$current_month] : 0;
        
        // Calculate estimated value of gifts given
        $gift_value = 0;
        if ($gift_product_id) {
            $product = wc_get_product($gift_product_id);
            if ($product) {
                $gift_value = $product->get_price() * $total_gifts;
            }
        }
        
        return array(
            'total_gifts_given' => $total_gifts,
            'current_month_gifts' => $current_month_gifts,
            'estimated_gift_value' => $gift_value,
            'formatted_gift_value' => wc_price($gift_value),
            'gift_product_name' => $gift_product_id ? get_the_title($gift_product_id) : __('No configurado', 'vf-extras'),
            'minimum_amount' => $gift_min_amount,
            'formatted_minimum' => wc_price($gift_min_amount),
            'monthly_usage' => $gift_usage
        );
    }
    
    /**
     * Check if customer qualifies for gift
     */
    public static function customer_qualifies_for_gift($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        if (!$user_id) {
            return false;
        }
        
        $gift_required_role = get_option('vf_gift_required_role', '');
        
        if (!$gift_required_role) {
            return true; // No role requirement
        }
        
        $user = get_user_by('id', $user_id);
        if (!$user) {
            return false;
        }
        
        return in_array($gift_required_role, $user->roles);
    }
    
    /**
     * Get gift eligibility info for current cart
     */
    public static function get_cart_gift_info() {
        if (!WC()->cart) {
            return array(
                'eligible' => false,
                'message' => __('Carrito no disponible', 'vf-extras')
            );
        }
        
        $gift_min_amount = get_option('vf_gift_min_amount', 100000);
        $gift_product_id = get_option('vf_gift_product_id', 0);
        $gift_required_role = get_option('vf_gift_required_role', '');
        
        if (!$gift_product_id) {
            return array(
                'eligible' => false,
                'message' => __('Programa de regalos no configurado', 'vf-extras')
            );
        }
        
        $instance = self::get_instance();
        $cart_subtotal = $instance->get_cart_subtotal_excluding_gifts();
        $remaining_amount = $gift_min_amount - $cart_subtotal;
        
        // Check role requirement
        if ($gift_required_role && !$instance->user_has_required_role($gift_required_role)) {
            return array(
                'eligible' => false,
                'message' => sprintf(
                    __('Solo usuarios con rol %s pueden recibir regalos', 'vf-extras'),
                    $gift_required_role
                )
            );
        }
        
        if ($cart_subtotal >= $gift_min_amount) {
            $product = wc_get_product($gift_product_id);
            return array(
                'eligible' => true,
                'has_gift' => $instance->is_gift_in_cart($gift_product_id),
                'message' => sprintf(
                    __('¡Felicitaciones! Recibes %s como regalo', 'vf-extras'),
                    $product ? $product->get_name() : __('producto regalo', 'vf-extras')
                )
            );
        } else {
            return array(
                'eligible' => false,
                'remaining_amount' => $remaining_amount,
                'formatted_remaining' => wc_price($remaining_amount),
                'formatted_minimum' => wc_price($gift_min_amount),
                'message' => sprintf(
                    __('Agrega %s más para recibir un regalo gratis', 'vf-extras'),
                    wc_price($remaining_amount)
                )
            );
        }
    }
}