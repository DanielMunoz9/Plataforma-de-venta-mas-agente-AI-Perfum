<?php
/**
 * Plugin Name: VF Revamp Pages (Plantillas por plugin)
 * Description: Registra plantillas: VF Home (Revamp), Quiero mi fragancia (B2C), Plan Emprendedor (B2B) y encola estilos/JS con ondas y flores.
 * Version: 1.0.0
 * Author: VF
 * License: GPLv2 or later
 * Text Domain: vf-revamp-pages
 */

if (!defined('ABSPATH')) { exit; }

// Constantes del plugin
if (!defined('VF_REVAMP_PAGES_PATH')) {
    define('VF_REVAMP_PAGES_PATH', plugin_dir_path(__FILE__));
}
if (!defined('VF_REVAMP_PAGES_URL')) {
    define('VF_REVAMP_PAGES_URL', plugin_dir_url(__FILE__));
}
if (!defined('VF_REVAMP_PAGES_VER')) {
    define('VF_REVAMP_PAGES_VER', '1.0.0');
}

function vf_revamp_pages_registered_templates() {
    return [
        'vf-revamp-pages/vf-home-revamp.php' => __('VF Home (Revamp)', 'vf-revamp-pages'),
        'vf-revamp-pages/vf-b2c.php' => __('Quiero mi fragancia', 'vf-revamp-pages'),
        'vf-revamp-pages/vf-b2b.php' => __('Plan Emprendedor', 'vf-revamp-pages'),
    ];
}

// Registrar plantillas (editores nuevos)
add_filter('theme_page_templates', function ($templates, $theme, $post, $post_type) {
    if ($post_type === 'page') {
        $templates = array_merge($templates, vf_revamp_pages_registered_templates());
    }
    return $templates;
}, 10, 4);

// Compatibilidad (filtros antiguos)
add_filter('page_templates', function ($templates) {
    if (is_array($templates)) {
        $templates = array_merge($templates, vf_revamp_pages_registered_templates());
    }
    return $templates;
});

// Cargar la plantilla seleccionada desde el plugin
add_filter('template_include', function ($template) {
    if (!is_page()) { return $template; }

    $selected = get_page_template_slug(get_queried_object_id());
    $map = [
        'vf-revamp-pages/vf-home-revamp.php' => 'templates/vf-home-revamp.php',
        'vf-revamp-pages/vf-b2c.php'         => 'templates/vf-b2c.php',
        'vf-revamp-pages/vf-b2b.php'         => 'templates/vf-b2b.php',
        // Soporte si WP guarda solo el nombre
        'vf-home-revamp.php' => 'templates/vf-home-revamp.php',
        'vf-b2c.php'         => 'templates/vf-b2c.php',
        'vf-b2b.php'         => 'templates/vf-b2b.php',
    ];

    if ($selected && isset($map[$selected])) {
        $plugin_template = VF_REVAMP_PAGES_PATH . $map[$selected];
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
}, 99);

// Utilidad: ¿estamos en alguna plantilla del plugin?
function vf_revamp_pages_is_plugin_template() {
    if (!is_page()) { return false; }
    $selected = get_page_template_slug(get_queried_object_id());
    return in_array($selected, [
        'vf-revamp-pages/vf-home-revamp.php',
        'vf-revamp-pages/vf-b2c.php',
        'vf-revamp-pages/vf-b2b.php',
        'vf-home-revamp.php',
        'vf-b2c.php',
        'vf-b2b.php',
    ], true);
}

// Encolar estilos/scripts solo en páginas que usan estas plantillas
add_action('wp_enqueue_scripts', function() {
    if (!vf_revamp_pages_is_plugin_template()) { return; }
    wp_enqueue_style('vf-revamp', VF_REVAMP_PAGES_URL . 'assets/css/vf-revamp.css', [], VF_REVAMP_PAGES_VER);
    wp_enqueue_script('vf-revamp', VF_REVAMP_PAGES_URL . 'assets/js/vf-revamp.js', [], VF_REVAMP_PAGES_VER, true);
});

// Limpiar caché de plantillas al activar/cambiar tema
function vf_revamp_pages_clear_cache() {
    if (!function_exists('wp_get_themes')) { return; }
    $themes = wp_get_themes();
    foreach ($themes as $obj) {
        if (method_exists($obj, 'get_stylesheet')) {
            $cache_key = 'page_templates-' . md5($obj->get_theme_root() . '/' . $obj->get_stylesheet());
            wp_cache_delete($cache_key, 'themes');
        }
    }
}
register_activation_hook(__FILE__, 'vf_revamp_pages_clear_cache');
add_action('after_switch_theme', 'vf_revamp_pages_clear_cache');
