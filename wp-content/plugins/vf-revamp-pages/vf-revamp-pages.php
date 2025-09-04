<?php
/**
 * Plugin Name: VF Revamp Pages
 * Description: Registra plantillas VF Revamp (Home, B2C, B2B) y encola assets.
 * Version: 1.0.0
 * Author: DanielMunoz9
 * Text Domain: vf-revamp-pages
 */
if (!defined('ABSPATH')) exit;

define('VF_REVAMP_PAGES_PATH', plugin_dir_path(__FILE__));
define('VF_REVAMP_PAGES_URL', plugin_dir_url(__FILE__));
define('VF_REVAMP_PAGES_VER', '1.0.0');

function vf_revamp_pages_registered_templates() {
    return array(
        'vf-revamp-pages/vf-home-revamp.php' => __('VF Home (Revamp)', 'vf-revamp-pages'),
        'vf-revamp-pages/vf-b2c.php'         => __('Quiero mi fragancia', 'vf-revamp-pages'),
        'vf-revamp-pages/vf-b2b.php'         => __('Plan Emprendedor', 'vf-revamp-pages'),
    );
}

add_filter('theme_page_templates', function ($templates, $theme, $post, $post_type) {
    if ($post_type !== 'page') return $templates;
    return array_merge($templates, vf_revamp_pages_registered_templates());
}, 10, 4);

add_filter('page_templates', function ($templates) {
    if (!is_array($templates)) return $templates;
    return array_merge($templates, vf_revamp_pages_registered_templates());
}, 10);

add_filter('template_include', function ($template) {
    if (!is_page()) return $template;

    $selected = get_page_template_slug(get_queried_object_id());
    $map = array(
        'vf-revamp-pages/vf-home-revamp.php' => 'templates/vf-home-revamp.php',
        'vf-revamp-pages/vf-b2c.php'         => 'templates/vf-b2c.php',
        'vf-revamp-pages/vf-b2b.php'         => 'templates/vf-b2b.php',
        // Soporte si WP guarda solo el nombre
        'vf-home-revamp.php' => 'templates/vf-home-revamp.php',
        'vf-b2c.php'         => 'templates/vf-b2c.php',
        'vf-b2b.php'         => 'templates/vf-b2b.php',
    );

    if ($selected && isset($map[$selected])) {
        $candidate = VF_REVAMP_PAGES_PATH . $map[$selected];
        if (file_exists($candidate)) {
            return $candidate;
        }
    }
    return $template;
}, 99);

function vf_revamp_pages_is_plugin_template() {
    if (!is_page()) return false;
    $selected = get_page_template_slug(get_queried_object_id());
    return in_array($selected, array(
        'vf-revamp-pages/vf-home-revamp.php',
        'vf-revamp-pages/vf-b2c.php',
        'vf-revamp-pages/vf-b2b.php',
        'vf-home-revamp.php',
        'vf-b2c.php',
        'vf-b2b.php',
    ), true);
}

add_action('wp_enqueue_scripts', function () {
    if (!vf_revamp_pages_is_plugin_template()) return;
    wp_enqueue_style('vf-revamp', VF_REVAMP_PAGES_URL . 'assets/css/vf-revamp.css', array(), VF_REVAMP_PAGES_VER);
    wp_enqueue_script('vf-revamp', VF_REVAMP_PAGES_URL . 'assets/js/vf-revamp.js', array(), VF_REVAMP_PAGES_VER, true);
});

function vf_revamp_pages_clear_cache() {
    if (!function_exists('wp_get_themes')) return;
    $themes = wp_get_themes();
    foreach ($themes as $obj) {
        if (method_exists($obj, 'get_stylesheet')) {
            $cache_key = 'page_templates-' . md5($obj->get_theme_root() . '/' . $obj->get_stylesheet());
            wp_cache_delete($cache_key, 'themes');
        }
    }
}
add_action('switch_theme', 'vf_revamp_pages_clear_cache');
register_activation_hook(__FILE__, 'vf_revamp_pages_clear_cache');
