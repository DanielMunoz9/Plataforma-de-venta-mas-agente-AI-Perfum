<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <!-- Logo/Brand -->
            <a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>">
                <?php
                if (has_custom_logo()) {
                    the_custom_logo();
                } else {
                    bloginfo('name');
                }
                ?>
            </a>

            <!-- Mobile menu toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class'     => 'navbar-nav ms-auto',
                    'container'      => false,
                    'fallback_cb'    => 'vane_france_fallback_menu',
                    'walker'         => new WP_Bootstrap_Navwalker(),
                ));
                ?>
                
                <!-- WooCommerce Cart -->
                <?php if (class_exists('WooCommerce')) : ?>
                    <ul class="navbar-nav ms-3">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="cartDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="badge bg-danger ms-1"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="cartDropdown">
                                <li>
                                    <div class="dropdown-item-text">
                                        <?php if (WC()->cart->get_cart_contents_count() > 0) : ?>
                                            <small><?php echo WC()->cart->get_cart_contents_count(); ?> artículos en el carrito</small>
                                            <br>
                                            <strong>Total: <?php echo WC()->cart->get_cart_total(); ?></strong>
                                        <?php else : ?>
                                            <small>Tu carrito está vacío</small>
                                        <?php endif; ?>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo esc_url(wc_get_cart_url()); ?>">Ver carrito</a></li>
                                <li><a class="dropdown-item" href="<?php echo esc_url(wc_get_checkout_url()); ?>">Finalizar compra</a></li>
                            </ul>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

<?php
// Fallback menu function
function vane_france_fallback_menu() {
    echo '<ul class="navbar-nav ms-auto">';
    echo '<li class="nav-item"><a class="nav-link" href="' . esc_url(home_url('/')) . '">Inicio</a></li>';
    echo '<li class="nav-item"><a class="nav-link" href="' . esc_url(get_permalink(get_page_by_title('Catálogo Emprendedor'))) . '">Plan Emprendedor</a></li>';
    echo '<li class="nav-item"><a class="nav-link" href="' . esc_url(get_permalink(get_page_by_title('Catálogo Cliente'))) . '">Cliente</a></li>';
    if (get_option('page_for_posts')) {
        echo '<li class="nav-item"><a class="nav-link" href="' . esc_url(get_permalink(get_option('page_for_posts'))) . '">Blog</a></li>';
    }
    echo '<li class="nav-item"><a class="nav-link" href="' . esc_url(home_url('/contacto')) . '">Contacto</a></li>';
    echo '</ul>';
}

// Bootstrap NavWalker for proper menu styling
class WP_Bootstrap_Navwalker extends Walker_Nav_Menu {
    
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"dropdown-menu\">\n";
    }

    function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        
        if (in_array('menu-item-has-children', $classes)) {
            $class_names .= ' nav-item dropdown';
        } else {
            $class_names .= ' nav-item';
        }
        
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        
        $output .= $indent . '<li' . $id . $class_names .'>';
        
        $attributes = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
        $attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target     ) .'"' : '';
        $attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn        ) .'"' : '';
        $attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url        ) .'"' : '';
        
        if (in_array('menu-item-has-children', $classes)) {
            $attributes .= ' class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false"';
        } else {
            $attributes .= ' class="nav-link"';
        }
        
        $item_output = isset($args->before) ? $args->before : '';
        $item_output .= '<a' . $attributes .'>';
        $item_output .= (isset($args->link_before) ? $args->link_before : '') . apply_filters('the_title', $item->title, $item->ID) . (isset($args->link_after) ? $args->link_after : '');
        $item_output .= '</a>';
        $item_output .= isset($args->after) ? $args->after : '';
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}