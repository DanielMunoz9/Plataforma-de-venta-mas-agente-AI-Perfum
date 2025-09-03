<?php
/**
 * Vane France Theme Functions
 * 
 * @package VaneFrance
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Theme constants
define('VF_THEME_VERSION', '1.0.0');
define('VF_THEME_URL', get_template_directory_uri());
define('VF_THEME_PATH', get_template_directory());

/**
 * Theme Setup
 */
function vane_france_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support('custom-logo');
    add_theme_support('customize-selective-refresh-widgets');
    
    // WooCommerce support
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Navigation', 'vane-france'),
        'footer'  => esc_html__('Footer Navigation', 'vane-france'),
    ));
    
    // Add image sizes
    add_image_size('vf-featured', 800, 400, true);
    add_image_size('vf-product', 300, 300, true);
    add_image_size('vf-blog-thumb', 400, 250, true);
}
add_action('after_setup_theme', 'vane_france_setup');

/**
 * Enqueue styles and scripts
 */
function vane_france_scripts() {
    // Styles
    wp_enqueue_style('vane-france-style', get_stylesheet_uri(), array(), VF_THEME_VERSION);
    wp_enqueue_style('vane-france-google-fonts', 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400;500&display=swap', array(), null);
    
    // Scripts
    wp_enqueue_script('vane-france-theme', VF_THEME_URL . '/assets/js/theme.js', array('jquery'), VF_THEME_VERSION, true);
    
    // Localize script with theme data
    wp_localize_script('vane-france-theme', 'vfTheme', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('vf_theme_nonce'),
        'whatsappNumber' => get_option('vf_whatsapp_number', ''),
    ));
    
    // Conditional scripts
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'vane_france_scripts');

/**
 * Register widget areas
 */
function vane_france_widgets_init() {
    register_sidebar(array(
        'name'          => esc_html__('Primary Sidebar', 'vane-france'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Add widgets here.', 'vane-france'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => esc_html__('Footer Area 1', 'vane-france'),
        'id'            => 'footer-1',
        'description'   => esc_html__('Add widgets here.', 'vane-france'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="footer-widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => esc_html__('Footer Area 2', 'vane-france'),
        'id'            => 'footer-2',
        'description'   => esc_html__('Add widgets here.', 'vane-france'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="footer-widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => esc_html__('Footer Area 3', 'vane-france'),
        'id'            => 'footer-3',
        'description'   => esc_html__('Add widgets here.', 'vane-france'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="footer-widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'vane_france_widgets_init');

/**
 * Theme activation - Auto-create pages
 */
function vane_france_activation() {
    // Create Catálogo Emprendedor page
    $emprendedor_page = get_page_by_title('Catálogo Emprendedor');
    if (!$emprendedor_page) {
        wp_insert_post(array(
            'post_title'   => 'Catálogo Emprendedor',
            'post_content' => '[vf_product_catalog tag="emprendedor"]',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_name'    => 'catalogo-emprendedor'
        ));
    }
    
    // Create Catálogo Cliente page
    $cliente_page = get_page_by_title('Catálogo Cliente');
    if (!$cliente_page) {
        wp_insert_post(array(
            'post_title'   => 'Catálogo Cliente',
            'post_content' => '[vf_product_catalog tag="cliente"]',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_name'    => 'catalogo-cliente'
        ));
    }
}
add_action('after_switch_theme', 'vane_france_activation');

/**
 * Add theme options to customizer
 */
function vane_france_customize_register($wp_customize) {
    // WhatsApp section
    $wp_customize->add_section('vf_whatsapp', array(
        'title'    => __('WhatsApp Settings', 'vane-france'),
        'priority' => 30,
    ));
    
    $wp_customize->add_setting('vf_whatsapp_number', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('vf_whatsapp_number', array(
        'label'   => __('WhatsApp Number', 'vane-france'),
        'section' => 'vf_whatsapp',
        'type'    => 'text',
        'description' => __('Enter WhatsApp number with country code (e.g., +573193605666)', 'vane-france'),
    ));
    
    // Hero section
    $wp_customize->add_section('vf_hero', array(
        'title'    => __('Hero Section', 'vane-france'),
        'priority' => 31,
    ));
    
    $wp_customize->add_setting('vf_hero_title', array(
        'default'           => 'Vane France',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('vf_hero_title', array(
        'label'   => __('Hero Title', 'vane-france'),
        'section' => 'vf_hero',
        'type'    => 'text',
    ));
    
    $wp_customize->add_setting('vf_hero_subtitle', array(
        'default'           => 'Perfumería Francesa de Alta Gama',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('vf_hero_subtitle', array(
        'label'   => __('Hero Subtitle', 'vane-france'),
        'section' => 'vf_hero',
        'type'    => 'text',
    ));
}
add_action('customize_register', 'vane_france_customize_register');

/**
 * Product catalog shortcode
 */
function vane_france_product_catalog_shortcode($atts) {
    $atts = shortcode_atts(array(
        'tag' => '',
        'limit' => 12,
    ), $atts);
    
    if (!$atts['tag']) {
        return '<p>Please specify a product tag.</p>';
    }
    
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => intval($atts['limit']),
        'tax_query' => array(
            array(
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($atts['tag']),
            ),
        ),
    );
    
    $products = new WP_Query($args);
    
    if (!$products->have_posts()) {
        return '<p>No products found for this category.</p>';
    }
    
    ob_start();
    ?>
    <div class="woocommerce-products">
        <?php while ($products->have_posts()) : $products->the_post(); ?>
            <div class="woocommerce-product-card">
                <?php if (has_term('emprendedor', 'product_tag')) : ?>
                    <div class="product-badge especial">Especial</div>
                <?php endif; ?>
                
                <div class="product-image">
                    <a href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('vf-product'); ?>
                        <?php else : ?>
                            <img src="<?php echo VF_THEME_URL; ?>/assets/img/product-1.jpg" alt="<?php the_title(); ?>">
                        <?php endif; ?>
                    </a>
                </div>
                
                <div class="product-info">
                    <h3 class="product-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <div class="product-price">
                        <?php
                        $product = wc_get_product(get_the_ID());
                        echo $product ? $product->get_price_html() : '';
                        ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('vf_product_catalog', 'vane_france_product_catalog_shortcode');

/**
 * WhatsApp floating button
 */
function vane_france_whatsapp_button() {
    $whatsapp_number = get_option('vf_whatsapp_number');
    
    if (is_product() && $whatsapp_number) {
        $message = urlencode('¿No lo encuentras? WhatsApp - ' . get_the_title());
        $whatsapp_url = "https://wa.me/{$whatsapp_number}?text={$message}";
        
        echo '<a href="' . esc_url($whatsapp_url) . '" class="whatsapp-float" target="_blank" rel="noopener">';
        echo '<div class="whatsapp-tooltip">¿No lo encuentras? WhatsApp</div>';
        echo '<svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/></svg>';
        echo '</a>';
    }
}
add_action('wp_footer', 'vane_france_whatsapp_button');

/**
 * Custom body classes
 */
function vane_france_body_classes($classes) {
    // Add class based on current page template
    if (is_page_template('page-catalogo-emprendedor.php')) {
        $classes[] = 'page-emprendedor';
    } elseif (is_page_template('page-catalogo-cliente.php')) {
        $classes[] = 'page-cliente';
    }
    
    return $classes;
}
add_filter('body_class', 'vane_france_body_classes');

/**
 * Excerpt length
 */
function vane_france_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'vane_france_excerpt_length');

/**
 * Excerpt more text
 */
function vane_france_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'vane_france_excerpt_more');

/**
 * Blog pagination
 */
function vane_france_pagination() {
    global $wp_query;
    
    $big = 999999999;
    
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $wp_query->max_num_pages,
        'prev_text' => '&laquo; ' . __('Previous', 'vane-france'),
        'next_text' => __('Next', 'vane-france') . ' &raquo;',
        'type' => 'list',
        'end_size' => 3,
        'mid_size' => 3
    ));
}

/**
 * Custom comment form
 */
function vane_france_comment_form($args) {
    $args['class_submit'] = 'btn btn-primary';
    $args['submit_button'] = '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />';
    return $args;
}
add_filter('comment_form_defaults', 'vane_france_comment_form');

/**
 * WooCommerce customizations
 */
// Remove WooCommerce default styles
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

// Modify WooCommerce product loop
remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);

/**
 * Add "Especial" badge to products with "emprendedor" tag
 */
function vane_france_product_badge() {
    global $product;
    
    if (has_term('emprendedor', 'product_tag', $product->get_id())) {
        echo '<div class="product-badge especial">Especial</div>';
    }
}
add_action('woocommerce_before_shop_loop_item_title', 'vane_france_product_badge', 5);

/**
 * Custom login logo
 */
function vane_france_login_logo() {
    ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo VF_THEME_URL; ?>/assets/img/logo.png);
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            width: 100%;
            height: 80px;
        }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'vane_france_login_logo');

/**
 * Security improvements
 */
// Remove WordPress version from head
remove_action('wp_head', 'wp_generator');

// Remove really simple discovery link
remove_action('wp_head', 'rsd_link');

// Remove windows live writer link
remove_action('wp_head', 'wlwmanifest_link');

// Remove shortlink
remove_action('wp_head', 'wp_shortlink_wp_head');

/**
 * Theme update checker (for future updates)
 */
function vane_france_check_for_updates() {
    // This would connect to your update server
    // For now, just add a notice in admin
    if (is_admin() && current_user_can('manage_options')) {
        $current_version = wp_get_theme()->get('Version');
        // You could check against a remote version here
    }
}
add_action('admin_init', 'vane_france_check_for_updates');

/**
 * Performance optimizations
 */
// Disable emojis
function vane_france_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'vane_france_disable_emojis');

// Remove query strings from static resources
function vane_france_remove_query_strings($src) {
    $parts = explode('?ver', $src);
    return $parts[0];
}
add_filter('script_loader_src', 'vane_france_remove_query_strings', 15, 1);
add_filter('style_loader_src', 'vane_france_remove_query_strings', 15, 1);