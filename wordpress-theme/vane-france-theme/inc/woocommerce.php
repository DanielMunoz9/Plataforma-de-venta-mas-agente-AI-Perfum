<?php
/**
 * WooCommerce Integration for Vane France Theme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Remove default WooCommerce styling
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

/**
 * Add "Especial" badge for products tagged as "emprendedor"
 */
function vf_add_emprendedor_badge() {
    global $product;
    
    if (!$product) return;
    
    $product_tags = wp_get_post_terms($product->get_id(), 'product_tag', array('fields' => 'slugs'));
    
    if (in_array('emprendedor', $product_tags)) {
        echo '<span class="product-especial-badge"><i class="fas fa-star me-1"></i>Especial</span>';
    }
}
add_action('woocommerce_before_single_product_summary', 'vf_add_emprendedor_badge', 6);
add_action('woocommerce_before_shop_loop_item_title', 'vf_add_emprendedor_badge', 6);

/**
 * Custom WooCommerce wrapper start
 */
function vf_woocommerce_wrapper_start() {
    echo '<div class="container mt-4"><div class="row"><div class="col-lg-8">';
}
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
add_action('woocommerce_before_main_content', 'vf_woocommerce_wrapper_start', 10);

/**
 * Custom WooCommerce wrapper end
 */
function vf_woocommerce_wrapper_end() {
    echo '</div><div class="col-lg-4">';
    get_sidebar();
    echo '</div></div></div>';
}
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
add_action('woocommerce_after_main_content', 'vf_woocommerce_wrapper_end', 10);

/**
 * Add WhatsApp floating button on single product pages
 */
function vf_add_whatsapp_button_single_product() {
    if (!is_product()) return;
    
    $whatsapp_number = get_option('vf_whatsapp_number', '3193605666');
    $product_title = get_the_title();
    $product_url = get_permalink();
    $message = urlencode("¿No lo encuentras? WhatsApp - Producto: {$product_title} - {$product_url}");
    
    ?>
    <a href="https://wa.me/<?php echo esc_attr($whatsapp_number); ?>?text=<?php echo $message; ?>" 
       class="whatsapp-float" 
       target="_blank" 
       aria-label="Contactar por WhatsApp">
        <i class="fab fa-whatsapp"></i>
        <span class="whatsapp-text">¿No lo encuentras?</span>
    </a>
    
    <style>
    .whatsapp-float {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: linear-gradient(45deg, #25d366, #20c55a);
        color: white;
        padding: 1rem;
        border-radius: 50px;
        box-shadow: 0 4px 20px rgba(37, 211, 102, 0.3);
        z-index: 1000;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        animation: float 3s ease-in-out infinite;
    }

    .whatsapp-float:hover {
        transform: scale(1.1);
        color: white;
        text-decoration: none;
        box-shadow: 0 6px 25px rgba(37, 211, 102, 0.4);
    }

    .whatsapp-float i {
        font-size: 1.5rem;
    }

    .whatsapp-text {
        font-size: 0.9rem;
        font-weight: 600;
        white-space: nowrap;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    @media (max-width: 768px) {
        .whatsapp-text {
            display: none;
        }
        
        .whatsapp-float {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            justify-content: center;
            padding: 0;
        }
    }
    </style>
    <?php
}
add_action('wp_footer', 'vf_add_whatsapp_button_single_product');

/**
 * Track product views for reporting
 */
function vf_track_product_views() {
    if (!is_product()) return;
    
    global $post;
    $views = get_post_meta($post->ID, 'vf_views', true);
    $views = $views ? intval($views) + 1 : 1;
    update_post_meta($post->ID, 'vf_views', $views);
}
add_action('wp', 'vf_track_product_views');

/**
 * Custom product loop structure
 */
function vf_custom_product_loop_start() {
    echo '<div class="vf-products-grid">';
}
add_action('woocommerce_output_content_wrapper', 'vf_custom_product_loop_start');

/**
 * Customize product loop item
 */
function vf_custom_product_loop_item_start() {
    echo '<div class="vf-product-item">';
}
add_action('woocommerce_before_shop_loop_item', 'vf_custom_product_loop_item_start', 5);

function vf_custom_product_loop_item_end() {
    echo '</div>';
}
add_action('woocommerce_after_shop_loop_item', 'vf_custom_product_loop_item_end', 25);

/**
 * Hide prices for emprendedor products when user is not logged in
 */
function vf_hide_emprendedor_prices($price, $product) {
    if (!is_user_logged_in()) {
        $product_tags = wp_get_post_terms($product->get_id(), 'product_tag', array('fields' => 'slugs'));
        
        if (in_array('emprendedor', $product_tags)) {
            return '<span class="emprendedor-price-hidden">
                        <i class="fas fa-lock me-1"></i>Precio exclusivo para emprendedores
                        <br><small><a href="' . wp_login_url(get_permalink()) . '">Inicia sesión</a> para ver precio</small>
                    </span>';
        }
    }
    
    return $price;
}
add_filter('woocommerce_get_price_html', 'vf_hide_emprendedor_prices', 10, 2);

/**
 * Add custom CSS for WooCommerce elements
 */
function vf_woocommerce_custom_styles() {
    if (!is_woocommerce() && !is_cart() && !is_checkout() && !is_account_page()) {
        return;
    }
    ?>
    <style>
    /* WooCommerce Custom Styles */
    .woocommerce .content-area,
    .woocommerce-page .content-area {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 18px;
        box-shadow: 0 8px 32px #00239533;
        padding: 2rem;
        margin-top: 2rem;
    }

    .woocommerce ul.products,
    .woocommerce-page ul.products {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .woocommerce ul.products li.product,
    .woocommerce-page ul.products li.product {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        margin: 0;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .woocommerce ul.products li.product:hover,
    .woocommerce-page ul.products li.product:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    }

    .woocommerce ul.products li.product img,
    .woocommerce-page ul.products li.product img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .woocommerce ul.products li.product:hover img,
    .woocommerce-page ul.products li.product:hover img {
        transform: scale(1.05);
    }

    .woocommerce ul.products li.product .woocommerce-loop-product__title,
    .woocommerce-page ul.products li.product .woocommerce-loop-product__title {
        color: #002395;
        font-family: 'Playfair Display', serif;
        font-size: 1.2rem;
        padding: 1rem 1rem 0.5rem;
        margin: 0;
        line-height: 1.4;
    }

    .woocommerce ul.products li.product .price,
    .woocommerce-page ul.products li.product .price {
        color: #ed2939;
        font-weight: bold;
        font-size: 1.3rem;
        padding: 0 1rem;
        margin-bottom: 1rem;
    }

    .woocommerce ul.products li.product .button,
    .woocommerce-page ul.products li.product .button {
        background: linear-gradient(45deg, #ed2939, #ff4757);
        color: white;
        border: none;
        border-radius: 0;
        padding: 1rem;
        margin: 0;
        margin-top: auto;
        text-align: center;
        font-weight: 600;
        transition: all 0.3s;
        text-decoration: none;
    }

    .woocommerce ul.products li.product .button:hover,
    .woocommerce-page ul.products li.product .button:hover {
        background: linear-gradient(45deg, #002395, #1a4bb8);
        color: white;
    }

    /* Single Product Styles */
    .woocommerce div.product,
    .woocommerce-page div.product {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .woocommerce div.product .product_title,
    .woocommerce-page div.product .product_title {
        color: #002395;
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    .woocommerce div.product p.price,
    .woocommerce-page div.product p.price {
        color: #ed2939;
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 2rem;
    }

    .woocommerce div.product .woocommerce-product-details__short-description,
    .woocommerce-page div.product .woocommerce-product-details__short-description {
        color: #666;
        font-size: 1.1rem;
        line-height: 1.6;
        margin-bottom: 2rem;
    }

    .woocommerce div.product form.cart .button,
    .woocommerce-page div.product form.cart .button {
        background: linear-gradient(45deg, #ed2939, #ff4757);
        color: white;
        border: none;
        border-radius: 25px;
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        transition: all 0.3s;
    }

    .woocommerce div.product form.cart .button:hover,
    .woocommerce-page div.product form.cart .button:hover {
        background: linear-gradient(45deg, #002395, #1a4bb8);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(237, 41, 57, 0.3);
    }

    /* Cart and Checkout Styles */
    .woocommerce table.shop_table,
    .woocommerce-page table.shop_table {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .woocommerce table.shop_table th,
    .woocommerce-page table.shop_table th {
        background: #002395;
        color: white;
        font-family: 'Playfair Display', serif;
    }

    .woocommerce .cart-collaterals,
    .woocommerce-page .cart-collaterals {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        margin-top: 2rem;
    }

    /* Account Pages */
    .woocommerce-account .woocommerce-MyAccount-navigation,
    .woocommerce-account .woocommerce-MyAccount-content {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .woocommerce-account .woocommerce-MyAccount-navigation ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .woocommerce-account .woocommerce-MyAccount-navigation ul li {
        margin-bottom: 0.5rem;
    }

    .woocommerce-account .woocommerce-MyAccount-navigation ul li a {
        display: block;
        padding: 1rem;
        background: rgba(0, 35, 149, 0.05);
        color: #002395;
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.3s;
    }

    .woocommerce-account .woocommerce-MyAccount-navigation ul li a:hover,
    .woocommerce-account .woocommerce-MyAccount-navigation ul li.is-active a {
        background: #002395;
        color: white;
    }

    /* Emprendedor Price Hidden */
    .emprendedor-price-hidden {
        background: rgba(255, 193, 7, 0.1);
        border: 2px solid #ffc107;
        border-radius: 10px;
        padding: 1rem;
        display: inline-block;
        color: #856404;
        font-weight: 600;
        text-align: center;
    }

    .emprendedor-price-hidden a {
        color: #ed2939;
        text-decoration: none;
        font-weight: bold;
    }

    .emprendedor-price-hidden a:hover {
        text-decoration: underline;
    }

    /* Messages */
    .woocommerce-message,
    .woocommerce-info,
    .woocommerce-error {
        border-radius: 10px;
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
    }

    .woocommerce-message {
        background: rgba(40, 167, 69, 0.1);
        border: 2px solid #28a745;
        color: #155724;
    }

    .woocommerce-info {
        background: rgba(0, 123, 255, 0.1);
        border: 2px solid #007bff;
        color: #004085;
    }

    .woocommerce-error {
        background: rgba(220, 53, 69, 0.1);
        border: 2px solid #dc3545;
        color: #721c24;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .woocommerce ul.products,
        .woocommerce-page ul.products {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .woocommerce div.product .product_title,
        .woocommerce-page div.product .product_title {
            font-size: 2rem;
        }
        
        .content-area {
            padding: 1rem;
        }
    }
    </style>
    <?php
}
add_action('wp_head', 'vf_woocommerce_custom_styles');

/**
 * Add custom breadcrumbs for WooCommerce
 */
function vf_custom_woocommerce_breadcrumbs() {
    if (!is_woocommerce()) return;
    
    ?>
    <div class="vf-breadcrumbs mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?php echo esc_url(home_url('/')); ?>">
                        <i class="fas fa-home me-1"></i>Inicio
                    </a>
                </li>
                <?php if (is_shop()) : ?>
                    <li class="breadcrumb-item active">Tienda</li>
                <?php elseif (is_product_category()) : ?>
                    <li class="breadcrumb-item">
                        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">Tienda</a>
                    </li>
                    <li class="breadcrumb-item active"><?php single_cat_title(); ?></li>
                <?php elseif (is_product()) : ?>
                    <li class="breadcrumb-item">
                        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">Tienda</a>
                    </li>
                    <li class="breadcrumb-item active"><?php the_title(); ?></li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>
    
    <style>
    .vf-breadcrumbs .breadcrumb {
        background: rgba(0, 35, 149, 0.05);
        border-radius: 25px;
        padding: 1rem 1.5rem;
        margin: 0;
    }
    
    .vf-breadcrumbs .breadcrumb-item a {
        color: #002395;
        text-decoration: none;
    }
    
    .vf-breadcrumbs .breadcrumb-item a:hover {
        color: #ed2939;
    }
    
    .vf-breadcrumbs .breadcrumb-item.active {
        color: #666;
    }
    </style>
    <?php
}
add_action('woocommerce_before_main_content', 'vf_custom_woocommerce_breadcrumbs', 15);