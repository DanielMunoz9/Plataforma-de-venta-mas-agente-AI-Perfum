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

/**
 * Theme Setup
 */
function vf_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // WooCommerce support
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'vane-france'),
        'footer' => __('Footer Menu', 'vane-france'),
    ));
    
    // Add image sizes
    add_image_size('vf-featured', 800, 400, true);
    add_image_size('vf-product', 300, 300, true);
    add_image_size('vf-blog-thumb', 400, 250, true);
}
add_action('after_setup_theme', 'vf_theme_setup');

/**
 * Enqueue Scripts and Styles
 */
function vf_enqueue_scripts() {
    // Enqueue styles
    wp_enqueue_style('vf-style', get_stylesheet_uri(), array(), '1.0.0');
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap', array(), null);
    
    // Enqueue scripts
    wp_enqueue_script('vf-theme-js', get_template_directory_uri() . '/assets/js/theme.js', array('jquery'), '1.0.0', true);
    
    // Localize script for AJAX
    wp_localize_script('vf-theme-js', 'vf_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('vf_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'vf_enqueue_scripts');

/**
 * Register Widget Areas
 */
function vf_widgets_init() {
    register_sidebar(array(
        'name'          => __('Blog Sidebar', 'vane-france'),
        'id'            => 'blog-sidebar',
        'description'   => __('Widgets for the blog sidebar', 'vane-france'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer Area 1', 'vane-france'),
        'id'            => 'footer-1',
        'description'   => __('Footer widget area 1', 'vane-france'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer Area 2', 'vane-france'),
        'id'            => 'footer-2',
        'description'   => __('Footer widget area 2', 'vane-france'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'vf_widgets_init');

/**
 * Theme Options
 */
function vf_customize_register($wp_customize) {
    // WhatsApp Number Setting
    $wp_customize->add_section('vf_contact', array(
        'title'    => __('Contact Settings', 'vane-france'),
        'priority' => 120,
    ));
    
    $wp_customize->add_setting('vf_whatsapp_number', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('vf_whatsapp_number', array(
        'label'    => __('WhatsApp Number', 'vane-france'),
        'section'  => 'vf_contact',
        'type'     => 'text',
        'description' => __('Enter WhatsApp number (e.g., 573193605666)', 'vane-france'),
    ));
    
    // Hero Section
    $wp_customize->add_section('vf_hero', array(
        'title'    => __('Hero Section', 'vane-france'),
        'priority' => 121,
    ));
    
    $wp_customize->add_setting('vf_hero_title', array(
        'default'           => 'Vane France',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('vf_hero_title', array(
        'label'    => __('Hero Title', 'vane-france'),
        'section'  => 'vf_hero',
        'type'     => 'text',
    ));
    
    $wp_customize->add_setting('vf_hero_subtitle', array(
        'default'           => 'Perfumería Francesa de Excelencia',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('vf_hero_subtitle', array(
        'label'    => __('Hero Subtitle', 'vane-france'),
        'section'  => 'vf_hero',
        'type'     => 'textarea',
    ));
}
add_action('customize_register', 'vf_customize_register');

/**
 * Create Catalog Pages on Theme Activation
 */
function vf_create_catalog_pages() {
    // Check if pages already exist
    $emprendedor_page = get_page_by_title('Catálogo Emprendedor');
    $cliente_page = get_page_by_title('Catálogo Cliente');
    
    if (!$emprendedor_page) {
        $emprendedor_content = '[vf_catalog type="emprendedor"]';
        
        wp_insert_post(array(
            'post_title'    => 'Catálogo Emprendedor',
            'post_content'  => $emprendedor_content,
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_name'     => 'catalogo-emprendedor'
        ));
    }
    
    if (!$cliente_page) {
        $cliente_content = '[vf_catalog type="cliente"]';
        
        wp_insert_post(array(
            'post_title'    => 'Catálogo Cliente',
            'post_content'  => $cliente_content,
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_name'     => 'catalogo-cliente'
        ));
    }
}
add_action('after_switch_theme', 'vf_create_catalog_pages');

/**
 * Catalog Shortcode
 */
function vf_catalog_shortcode($atts) {
    $atts = shortcode_atts(array(
        'type' => 'cliente',
        'limit' => 12
    ), $atts);
    
    if (!class_exists('WooCommerce')) {
        return '<p>' . __('WooCommerce is required for the catalog feature.', 'vane-france') . '</p>';
    }
    
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => intval($atts['limit']),
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_visibility',
                'value' => array('catalog', 'visible'),
                'compare' => 'IN'
            )
        )
    );
    
    // Add tag filter for emprendedor/cliente
    if ($atts['type'] === 'emprendedor') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => 'emprendedor',
            ),
        );
    } else {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_tag',
                'field'    => 'slug',
                'terms'    => 'cliente',
            ),
        );
    }
    
    $products = new WP_Query($args);
    
    ob_start();
    
    if ($products->have_posts()) {
        echo '<div class="woocommerce"><ul class="products columns-3">';
        
        while ($products->have_posts()) {
            $products->the_post();
            wc_get_template_part('content', 'product');
        }
        
        echo '</ul></div>';
        wp_reset_postdata();
    } else {
        echo '<p>' . __('No products found for this catalog.', 'vane-france') . '</p>';
    }
    
    return ob_get_clean();
}
add_shortcode('vf_catalog', 'vf_catalog_shortcode');

/**
 * Add "Especial" Badge for Emprendedor Products
 */
function vf_add_especial_badge() {
    global $product;
    
    if (!$product) return;
    
    $product_tags = wp_get_post_terms($product->get_id(), 'product_tag');
    
    foreach ($product_tags as $tag) {
        if ($tag->slug === 'emprendedor') {
            echo '<span class="especial-badge">' . __('Especial', 'vane-france') . '</span>';
            break;
        }
    }
}
add_action('woocommerce_before_shop_loop_item_title', 'vf_add_especial_badge');
add_action('woocommerce_before_single_product_summary', 'vf_add_especial_badge');

/**
 * WhatsApp Button on Single Product
 */
function vf_add_whatsapp_button() {
    $whatsapp_number = get_theme_mod('vf_whatsapp_number', '');
    
    if (empty($whatsapp_number)) {
        return;
    }
    
    global $product;
    $product_name = $product ? $product->get_name() : '';
    $product_url = $product ? get_permalink($product->get_id()) : '';
    
    $message = sprintf(
        __('Hola, estoy interesado en el producto: %s - %s', 'vane-france'),
        $product_name,
        $product_url
    );
    
    $whatsapp_url = 'https://wa.me/' . $whatsapp_number . '?text=' . urlencode($message);
    
    echo '<div class="vf-whatsapp-product">';
    echo '<a href="' . esc_url($whatsapp_url) . '" target="_blank" class="vf-whatsapp-float" style="position: static; margin-top: 20px;">';
    echo __('¿No lo encuentras? WhatsApp', 'vane-france');
    echo '</a>';
    echo '</div>';
}
add_action('woocommerce_single_product_summary', 'vf_add_whatsapp_button', 35);

/**
 * Floating WhatsApp Button
 */
function vf_floating_whatsapp() {
    $whatsapp_number = get_theme_mod('vf_whatsapp_number', '');
    
    if (empty($whatsapp_number)) {
        return;
    }
    
    $message = __('Hola, me gustaría obtener más información sobre sus productos.', 'vane-france');
    $whatsapp_url = 'https://wa.me/' . $whatsapp_number . '?text=' . urlencode($message);
    
    echo '<a href="' . esc_url($whatsapp_url) . '" target="_blank" class="vf-whatsapp-float">';
    echo __('WhatsApp', 'vane-france');
    echo '</a>';
}
add_action('wp_footer', 'vf_floating_whatsapp');

/**
 * Custom Excerpt Length
 */
function vf_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'vf_excerpt_length');

/**
 * Custom Excerpt More
 */
function vf_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'vf_excerpt_more');

/**
 * Add Theme Options to Admin
 */
function vf_add_theme_options_page() {
    add_theme_page(
        __('Vane France Options', 'vane-france'),
        __('Theme Options', 'vane-france'),
        'manage_options',
        'vf-theme-options',
        'vf_theme_options_page'
    );
}
add_action('admin_menu', 'vf_add_theme_options_page');

function vf_theme_options_page() {
    if (isset($_POST['submit'])) {
        update_option('vf_whatsapp_number', sanitize_text_field($_POST['vf_whatsapp_number']));
        echo '<div class="notice notice-success"><p>' . __('Settings saved!', 'vane-france') . '</p></div>';
    }
    
    $whatsapp_number = get_option('vf_whatsapp_number', '');
    ?>
    <div class="wrap">
        <h1><?php _e('Vane France Theme Options', 'vane-france'); ?></h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('WhatsApp Number', 'vane-france'); ?></th>
                    <td>
                        <input type="text" name="vf_whatsapp_number" value="<?php echo esc_attr($whatsapp_number); ?>" class="regular-text" />
                        <p class="description"><?php _e('Enter your WhatsApp number with country code (e.g., 573193605666)', 'vane-france'); ?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Add Body Classes
 */
function vf_body_classes($classes) {
    $classes[] = 'vane-france-theme';
    
    if (is_woocommerce() || is_cart() || is_checkout()) {
        $classes[] = 'woocommerce-page';
    }
    
    return $classes;
}
add_filter('body_class', 'vf_body_classes');

/**
 * Security: Remove WordPress version from head
 */
remove_action('wp_head', 'wp_generator');

/**
 * Optimize WordPress Admin Bar
 */
function vf_admin_bar_style() {
    if (is_admin_bar_showing()) {
        echo '<style>html { margin-top: 32px !important; }</style>';
    }
}
add_action('wp_head', 'vf_admin_bar_style');