<?php
/**
 * Theme Name: Vane France
 * Description: Tema personalizado para Vane France - Perfumería Francesa de alta gama con integración WooCommerce
 * Version: 1.0
 * Author: Vane France Team
 * Text Domain: vane-france
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Theme setup
function vane_france_setup() {
    // Add theme support for title tag
    add_theme_support('title-tag');
    
    // Add theme support for post thumbnails
    add_theme_support('post-thumbnails');
    
    // Add theme support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    
    // Add theme support for HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // Add WooCommerce support
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Menú Principal', 'vane-france'),
        'footer'  => __('Menú Footer', 'vane-france'),
    ));
}
add_action('after_setup_theme', 'vane_france_setup');

// Enqueue styles and scripts
function vane_france_scripts() {
    // Enqueue main stylesheet
    wp_enqueue_style('vane-france-style', get_stylesheet_uri());
    
    // Enqueue Bootstrap CSS
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
    
    // Enqueue Google Fonts
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap');
    
    // Enqueue Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
    
    // Enqueue custom theme styles
    wp_enqueue_style('vane-france-custom', get_template_directory_uri() . '/assets/css/custom.css', array(), '1.0.0');
    
    // Enqueue Bootstrap JS
    wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array(), '5.3.3', true);
    
    // Enqueue custom theme scripts
    wp_enqueue_script('vane-france-custom', get_template_directory_uri() . '/assets/js/custom.js', array('jquery'), '1.0.0', true);
    
    // Localize script for AJAX
    wp_localize_script('vane-france-custom', 'vane_france_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('vane_france_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'vane_france_scripts');

// Register widget areas
function vane_france_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar Principal', 'vane-france'),
        'id'            => 'sidebar-1',
        'description'   => __('Aparece en la barra lateral de blog y páginas', 'vane-france'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer 1', 'vane-france'),
        'id'            => 'footer-1',
        'description'   => __('Aparece en la primera columna del footer', 'vane-france'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer 2', 'vane-france'),
        'id'            => 'footer-2',
        'description'   => __('Aparece en la segunda columna del footer', 'vane-france'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer 3', 'vane-france'),
        'id'            => 'footer-3',
        'description'   => __('Aparece en la tercera columna del footer', 'vane-france'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'vane_france_widgets_init');

// Theme activation hook
function vane_france_activation() {
    // Create catalog pages
    $emprendedor_page = array(
        'post_title'    => 'Catálogo Emprendedor',
        'post_content'  => '[vf_catalog plan="emprendedor" per_page="12"]',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_author'   => 1,
    );
    
    $cliente_page = array(
        'post_title'    => 'Catálogo Cliente',
        'post_content'  => '[vf_catalog plan="cliente" per_page="12"]',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_author'   => 1,
    );
    
    // Insert pages if they don't exist
    if (!get_page_by_title('Catálogo Emprendedor')) {
        wp_insert_post($emprendedor_page);
    }
    
    if (!get_page_by_title('Catálogo Cliente')) {
        wp_insert_post($cliente_page);
    }
    
    // Set default WhatsApp number
    if (!get_option('vf_whatsapp_number')) {
        update_option('vf_whatsapp_number', '3193605666');
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'vane_france_activation');

// Include theme functions
require_once get_template_directory() . '/inc/shortcodes.php';
require_once get_template_directory() . '/inc/woocommerce.php';
require_once get_template_directory() . '/inc/customizer.php';

// Admin menu for theme options
function vane_france_admin_menu() {
    add_theme_page(
        'Opciones del Tema',
        'Vane France',
        'manage_options',
        'vane-france-options',
        'vane_france_options_page'
    );
}
add_action('admin_menu', 'vane_france_admin_menu');

function vane_france_options_page() {
    if (isset($_POST['submit'])) {
        update_option('vf_whatsapp_number', sanitize_text_field($_POST['vf_whatsapp_number']));
        echo '<div class="notice notice-success"><p>Configuración guardada.</p></div>';
    }
    
    $whatsapp_number = get_option('vf_whatsapp_number', '3193605666');
    ?>
    <div class="wrap">
        <h1>Opciones del Tema Vane France</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">Número de WhatsApp</th>
                    <td>
                        <input type="text" name="vf_whatsapp_number" value="<?php echo esc_attr($whatsapp_number); ?>" class="regular-text" />
                        <p class="description">Número de WhatsApp para el botón flotante (solo números, sin espacios ni símbolos)</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}