<?php
/**
 * Plugin Name: VF Revamp Pages
 * Description: Page template pack for Vane France. Provides the "Plan Emprendedor" page template.
 * Version: 1.0.0
 * Author: Daniel Munoz + Copilot
 * License: GPLv2 or later
 */

if (!defined('ABSPATH')) { exit; }

// Define paths
if (!defined('VF_REVAMP_PAGES_PATH')) {
    define('VF_REVAMP_PAGES_PATH', plugin_dir_path(__FILE__));
}
if (!defined('VF_REVAMP_PAGES_URL')) {
    define('VF_REVAMP_PAGES_URL', plugin_dir_url(__FILE__));
}

// Register page template from plugin
add_filter('theme_page_templates', function ($templates, $theme, $post, $post_type) {
    if ($post_type !== 'page') {
        return $templates;
    }
    // Key must be unique; for plugins, using a pseudo path is common
    $templates['vf-revamp-pages/vf-b2b.php'] = __('Plan Emprendedor', 'vf-revamp-pages');
    return $templates;
}, 10, 4);

// Support older WP versions where the filter signature differs
add_filter('page_templates', function ($templates) {
    if (is_array($templates)) {
        $templates['vf-revamp-pages/vf-b2b.php'] = __('Plan Emprendedor', 'vf-revamp-pages');
    }
    return $templates;
});

// Load the plugin template when selected on a page
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

// Clear page template cache so the template appears immediately in the editor
function vf_revamp_pages_clear_cache() {
    if (function_exists('wp_get_theme')) {
        $theme = wp_get_theme();
        if (method_exists($theme, 'get_stylesheet')) {
            $themes = wp_get_themes();
            foreach ($themes as $name => $obj) {
                $cache_key = 'page_templates-' . md5($obj->get_theme_root() . '/' . $obj->get_stylesheet());
                wp_cache_delete($cache_key, 'themes');
            }
        }
    }
}
register_activation_hook(__FILE__, 'vf_revamp_pages_clear_cache');
add_action('after_switch_theme', 'vf_revamp_pages_clear_cache');
