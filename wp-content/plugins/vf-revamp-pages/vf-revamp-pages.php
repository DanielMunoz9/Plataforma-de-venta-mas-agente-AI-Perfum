<?php
/**
 * Plugin Name: VF Revamp Pages
 * Description: Paquete de plantillas para Vane France. Proporciona la plantilla de página "Plan Emprendedor".
 * Version: 1.0.0
 * Author: Daniel Munoz + Copilot
 * License: GPLv2 or later
 * Text Domain: vf-revamp-pages
 */

if (!defined('ABSPATH')) { exit; }

// Rutas del plugin
if (!defined('VF_REVAMP_PAGES_PATH')) {
    define('VF_REVAMP_PAGES_PATH', plugin_dir_path(__FILE__));
}
if (!defined('VF_REVAMP_PAGES_URL')) {
    define('VF_REVAMP_PAGES_URL', plugin_dir_url(__FILE__));
}

// Registrar la plantilla de página desde el plugin (editores nuevos)
add_filter('theme_page_templates', function ($templates, $theme, $post, $post_type) {
    if ($post_type !== 'page') {
        return $templates;
    }
    $templates['vf-revamp-pages/vf-b2b.php'] = __('Plan Emprendedor', 'vf-revamp-pages');
    return $templates;
}, 10, 4);

// Compatibilidad con filtros antiguos
add_filter('page_templates', function ($templates) {
    if (is_array($templates)) {
        $templates['vf-revamp-pages/vf-b2b.php'] = __('Plan Emprendedor', 'vf-revamp-pages');
    }
    return $templates;
});

// Cargar la plantilla del plugin cuando esté seleccionada en la página
add_filter('template_include', function ($template) {
    if (is_page()) {
        $page_id = get_queried_object_id();
        $selected = get_page_template_slug($page_id);
        if ($selected === 'vf-revamp-pages/vf-b2b.php' || $selected === 'vf-b2b.php') {
            $plugin_template = VF_REVAMP_PAGES_PATH . 'templates/vf-b2b.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
    }
    return $template;
}, 99);

// Limpiar caché de plantillas para que aparezca de inmediato en el editor
function vf_revamp_pages_clear_cache() {
    if (function_exists('wp_get_theme')) {
        $themes = wp_get_themes();
        foreach ($themes as $obj) {
            if (method_exists($obj, 'get_stylesheet')) {
                $cache_key = 'page_templates-' . md5($obj->get_theme_root() . '/' . $obj->get_stylesheet());
                wp_cache_delete($cache_key, 'themes');
            }
        }
    }
}
register_activation_hook(__FILE__, 'vf_revamp_pages_clear_cache');
add_action('after_switch_theme', 'vf_revamp_pages_clear_cache');
