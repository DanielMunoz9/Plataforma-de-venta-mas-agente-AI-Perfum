<?php
/**
 * Shortcodes for Vane France Theme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Catalog Shortcode for Plan Emprendedor and Cliente
 * Usage: [vf_catalog plan="emprendedor" per_page="12"]
 */
function vf_catalog_shortcode($atts) {
    $atts = shortcode_atts(array(
        'plan' => 'emprendedor',
        'per_page' => 12,
        'columns' => 3,
        'orderby' => 'menu_order',
        'order' => 'ASC'
    ), $atts, 'vf_catalog');

    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return '<div class="alert alert-warning">WooCommerce debe estar instalado para mostrar el catálogo.</div>';
    }

    $plan = sanitize_text_field($atts['plan']);
    $per_page = intval($atts['per_page']);
    $columns = intval($atts['columns']);
    
    // Set up query args
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => $per_page,
        'post_status' => 'publish',
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
        'meta_query' => array(
            array(
                'key' => '_visibility',
                'value' => array('catalog', 'visible'),
                'compare' => 'IN'
            )
        )
    );

    // Add tag filter for plan
    if ($plan === 'emprendedor' || $plan === 'cliente') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => $plan,
            ),
        );
    }

    $products = new WP_Query($args);
    
    if (!$products->have_posts()) {
        return '<div class="alert alert-info">No hay productos disponibles para el plan ' . esc_html($plan) . '.</div>';
    }

    ob_start();
    ?>
    <div class="vf-catalog vf-catalog-<?php echo esc_attr($plan); ?>">
        <div class="catalog-header mb-4">
            <h2 class="catalog-title">
                <?php if ($plan === 'emprendedor') : ?>
                    <i class="fas fa-briefcase me-2"></i>Catálogo Plan Emprendedor
                <?php else : ?>
                    <i class="fas fa-user me-2"></i>Catálogo Cliente
                <?php endif; ?>
            </h2>
            <p class="catalog-description">
                <?php if ($plan === 'emprendedor') : ?>
                    Productos exclusivos para emprendedores con precios especiales
                <?php else : ?>
                    Nuestra selección de perfumes franceses de alta gama
                <?php endif; ?>
            </p>
        </div>

        <?php if ($plan === 'emprendedor' && !is_user_logged_in()) : ?>
            <div class="login-notice alert alert-warning">
                <i class="fas fa-lock me-2"></i>
                <strong>Precios exclusivos para emprendedores:</strong> 
                <a href="<?php echo wp_login_url(get_permalink()); ?>">Inicia sesión</a> 
                para ver precios especiales.
            </div>
        <?php endif; ?>

        <div class="products woocommerce">
            <div class="row">
                <?php while ($products->have_posts()) : $products->the_post(); ?>
                    <?php global $product; ?>
                    <div class="col-lg-<?php echo 12 / $columns; ?> col-md-6 col-sm-6 mb-4">
                        <div class="product-card" data-product-id="<?php the_ID(); ?>">
                            <div class="product-image-container">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('woocommerce_thumbnail', array('class' => 'product-image')); ?>
                                    <?php else : ?>
                                        <img src="<?php echo wc_placeholder_img_src(); ?>" alt="<?php the_title(); ?>" class="product-image">
                                    <?php endif; ?>
                                </a>
                                
                                <?php if ($plan === 'emprendedor') : ?>
                                    <span class="product-especial-badge">Especial</span>
                                <?php endif; ?>
                                
                                <div class="product-actions">
                                    <?php if ($product->is_purchasable() && $product->is_in_stock()) : ?>
                                        <?php
                                        echo apply_filters(
                                            'woocommerce_loop_add_to_cart_link',
                                            sprintf(
                                                '<a href="%s" data-quantity="%s" class="%s" %s><i class="fas fa-shopping-cart me-1"></i>%s</a>',
                                                esc_url($product->add_to_cart_url()),
                                                esc_attr(isset($args['quantity']) ? $args['quantity'] : 1),
                                                esc_attr(isset($args['class']) ? $args['class'] : 'btn btn-primary add-to-cart'),
                                                isset($args['attributes']) ? wc_implode_html_attributes($args['attributes']) : '',
                                                esc_html($product->add_to_cart_text())
                                            ),
                                            $product,
                                            $args
                                        );
                                        ?>
                                    <?php endif; ?>
                                    <a href="<?php the_permalink(); ?>" class="btn btn-outline-secondary quick-view">
                                        <i class="fas fa-eye me-1"></i>Ver
                                    </a>
                                </div>
                            </div>
                            
                            <div class="product-info">
                                <h3 class="product-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                
                                <div class="product-price">
                                    <?php if ($plan === 'emprendedor' && !is_user_logged_in()) : ?>
                                        <span class="price-hidden">Precio exclusivo</span>
                                    <?php else : ?>
                                        <?php echo $product->get_price_html(); ?>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="product-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <?php if ($products->max_num_pages > 1) : ?>
            <div class="catalog-pagination text-center mt-4">
                <?php
                echo paginate_links(array(
                    'total' => $products->max_num_pages,
                    'current' => max(1, get_query_var('paged')),
                    'prev_text' => '<i class="fas fa-chevron-left"></i> Anterior',
                    'next_text' => 'Siguiente <i class="fas fa-chevron-right"></i>',
                ));
                ?>
            </div>
        <?php endif; ?>
    </div>

    <style>
    .vf-catalog {
        margin: 2rem 0;
    }

    .catalog-header {
        text-align: center;
        background: linear-gradient(45deg, rgba(0, 35, 149, 0.05), rgba(237, 41, 57, 0.05));
        border-radius: 15px;
        padding: 2rem;
        border: 2px solid rgba(0, 35, 149, 0.1);
    }

    .catalog-title {
        color: #002395;
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    .catalog-description {
        color: #666;
        font-size: 1.1rem;
        margin: 0;
    }

    .login-notice {
        background: rgba(255, 193, 7, 0.1);
        border: 2px solid #ffc107;
        border-radius: 10px;
    }

    .product-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    }

    .product-image-container {
        position: relative;
        overflow: hidden;
    }

    .product-image {
        width: 100%;
        height: 250px;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .product-card:hover .product-image {
        transform: scale(1.05);
    }

    .product-actions {
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 0.5rem;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .product-card:hover .product-actions {
        opacity: 1;
    }

    .product-actions .btn {
        border-radius: 20px;
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
    }

    .product-info {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-title {
        font-size: 1.2rem;
        margin-bottom: 1rem;
    }

    .product-title a {
        color: #002395;
        text-decoration: none;
        transition: color 0.3s;
    }

    .product-title a:hover {
        color: #ed2939;
    }

    .product-price {
        font-size: 1.3rem;
        font-weight: bold;
        color: #ed2939;
        margin-bottom: 1rem;
    }

    .price-hidden {
        color: #666;
        font-style: italic;
    }

    .product-excerpt {
        color: #666;
        line-height: 1.5;
        margin-top: auto;
    }

    .catalog-pagination .page-numbers {
        background: white;
        border: 2px solid #002395;
        color: #002395;
        padding: 0.5rem 1rem;
        margin: 0 0.25rem;
        border-radius: 25px;
        text-decoration: none;
        transition: all 0.3s;
    }

    .catalog-pagination .page-numbers:hover,
    .catalog-pagination .page-numbers.current {
        background: #002395;
        color: white;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .catalog-title {
            font-size: 1.5rem;
        }
        
        .product-actions {
            position: static;
            transform: none;
            opacity: 1;
            justify-content: center;
            margin-top: 1rem;
        }
    }
    </style>

    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('vf_catalog', 'vf_catalog_shortcode');

/**
 * Offers Shortcode
 * Usage: [vf_offers limit="6"]
 */
function vf_offers_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 6,
        'columns' => 3
    ), $atts, 'vf_offers');

    $limit = intval($atts['limit']);
    $columns = intval($atts['columns']);

    $args = array(
        'post_type' => 'vf_offer',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    );

    $offers = new WP_Query($args);

    if (!$offers->have_posts()) {
        return '<div class="alert alert-info">No hay ofertas disponibles en este momento.</div>';
    }

    ob_start();
    ?>
    <div class="vf-offers">
        <div class="row">
            <?php while ($offers->have_posts()) : $offers->the_post(); ?>
                <div class="col-lg-<?php echo 12 / $columns; ?> col-md-6 mb-4">
                    <div class="offer-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="offer-image">
                                <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="offer-content">
                            <h3 class="offer-title"><?php the_title(); ?></h3>
                            <div class="offer-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="btn btn-vf-primary">
                                Ver oferta <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <style>
    .offer-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s;
        height: 100%;
    }

    .offer-card:hover {
        transform: translateY(-5px);
    }

    .offer-image img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .offer-content {
        padding: 1.5rem;
    }

    .offer-title {
        color: #002395;
        font-size: 1.3rem;
        margin-bottom: 1rem;
    }

    .offer-excerpt {
        color: #666;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }
    </style>

    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('vf_offers', 'vf_offers_shortcode');