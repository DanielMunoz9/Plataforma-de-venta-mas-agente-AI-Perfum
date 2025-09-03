<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php bloginfo('description'); ?>">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="vf-header">
    <div class="vf-container">
        <nav class="vf-nav">
            <!-- Logo -->
            <div class="vf-logo-container">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="vf-logo">
                        <?php echo get_theme_mod('vf_hero_title', 'Vane France'); ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Main Navigation -->
            <div class="vf-nav-main">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'vf-nav-menu',
                    'fallback_cb'    => 'vf_fallback_menu',
                ));
                ?>
            </div>

            <!-- Mobile Menu Toggle -->
            <button class="vf-mobile-toggle" id="mobile-menu-toggle" aria-label="<?php _e('Toggle navigation', 'vane-france'); ?>">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </nav>
    </div>
</header>

<?php
/**
 * Fallback menu if no menu is assigned
 */
function vf_fallback_menu() {
    echo '<ul class="vf-nav-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">' . __('Inicio', 'vane-france') . '</a></li>';
    
    if (class_exists('WooCommerce')) {
        echo '<li><a href="' . esc_url(wc_get_page_permalink('shop')) . '">' . __('Tienda', 'vane-france') . '</a></li>';
    }
    
    // Catalog pages
    $emprendedor_page = get_page_by_path('catalogo-emprendedor');
    if ($emprendedor_page) {
        echo '<li><a href="' . esc_url(get_permalink($emprendedor_page)) . '">' . __('Plan Emprendedor', 'vane-france') . '</a></li>';
    }
    
    $cliente_page = get_page_by_path('catalogo-cliente');
    if ($cliente_page) {
        echo '<li><a href="' . esc_url(get_permalink($cliente_page)) . '">' . __('Cliente', 'vane-france') . '</a></li>';
    }
    
    echo '<li><a href="' . esc_url(home_url('/blog')) . '">' . __('Blog', 'vane-france') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/contacto')) . '">' . __('Contacto', 'vane-france') . '</a></li>';
    
    if (class_exists('WooCommerce')) {
        echo '<li><a href="' . esc_url(wc_get_cart_url()) . '">' . __('Carrito', 'vane-france') . ' <span class="cart-count">(' . WC()->cart->get_cart_contents_count() . ')</span></a></li>';
    }
    
    echo '</ul>';
}
?>