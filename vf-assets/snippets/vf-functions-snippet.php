<?php
/**
 * Vane France Landing Page Assets Enqueue
 * 
 * This snippet should be included in your theme's functions.php file
 * or copied into it to load the Vane France landing page assets.
 * 
 * To load assets only on the specific template, uncomment the 
 * return statement in the is_page_template() check below.
 */

// Prevent direct access
if (!defined('ABSPATH')) exit;

/**
 * Check if current page uses the Vane France template
 */
function vf_is_vanefrance_page() {
    // Uncomment the line below to load assets only on the Vane France template
    // if (!is_page_template('page-vanefrance.php')) return false;
    
    return is_page_template('page-vanefrance.php') || 
           (is_page() && get_page_template_slug() === 'page-vanefrance.php');
}

/**
 * Enqueue Vane France assets
 */
function vf_enqueue_vanefrance_assets() {
    // Only load on Vane France pages (comment out to load globally)
    if (!vf_is_vanefrance_page()) return;
    
    // Define asset paths - adjust these paths according to your setup
    $vf_assets_url = get_template_directory_uri() . '/vf-assets/assets/';
    
    // If vf-assets is in the root directory, use this instead:
    // $vf_assets_url = home_url('/vf-assets/assets/');
    
    // Enqueue Vane France CSS
    wp_enqueue_style(
        'vf-vanefrance-style',
        $vf_assets_url . 'css/vf-style.css',
        array(), // Dependencies
        '1.0.0', // Version
        'all' // Media type
    );
    
    // Enqueue Vane France JavaScript
    wp_enqueue_script(
        'vf-vanefrance-script',
        $vf_assets_url . 'js/vf-script.js',
        array('jquery'), // Dependencies - include jQuery if needed
        '1.0.0', // Version
        true // Load in footer
    );
    
    // Enqueue Font Awesome for icons (if not already loaded)
    if (!wp_style_is('font-awesome', 'enqueued')) {
        wp_enqueue_style(
            'font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
            array(),
            '6.0.0'
        );
    }
    
    // Enqueue Google Fonts for Playfair Display (if not already loaded)
    if (!wp_style_is('google-fonts-playfair', 'enqueued')) {
        wp_enqueue_style(
            'google-fonts-playfair',
            'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap',
            array(),
            null
        );
    }
}

// Hook the function to wp_enqueue_scripts
add_action('wp_enqueue_scripts', 'vf_enqueue_vanefrance_assets');

/**
 * Add custom body class for Vane France pages
 */
function vf_add_vanefrance_body_class($classes) {
    if (vf_is_vanefrance_page()) {
        $classes[] = 'vf-vanefrance-page';
    }
    return $classes;
}
add_filter('body_class', 'vf_add_vanefrance_body_class');

/**
 * Optimize video loading for Vane France page
 */
function vf_add_video_preload() {
    if (vf_is_vanefrance_page()) {
        echo '<link rel="preload" as="video" href="' . get_template_directory_uri() . '/vf-assets/videos/hero-video.mp4" type="video/mp4">';
    }
}
add_action('wp_head', 'vf_add_video_preload');

/**
 * Add meta tags for Vane France page SEO
 */
function vf_add_vanefrance_meta_tags() {
    if (vf_is_vanefrance_page()) {
        echo '<meta name="description" content="Vane France - Perfumería de lujo con inspiración francesa">';
        echo '<meta property="og:title" content="Vane France - Perfumería de Lujo">';
        echo '<meta property="og:description" content="Descubre nuestra colección exclusiva de fragancias francesas">';
        echo '<meta property="og:type" content="website">';
        echo '<meta name="twitter:card" content="summary_large_image">';
    }
}
add_action('wp_head', 'vf_add_vanefrance_meta_tags');

/**
 * Custom excerpt length for Vane France page
 */
function vf_custom_excerpt_length($length) {
    if (vf_is_vanefrance_page()) {
        return 25;
    }
    return $length;
}
add_filter('excerpt_length', 'vf_custom_excerpt_length');

/**
 * Remove default WordPress styles and scripts on Vane France page if needed
 * Uncomment the functions below to remove default WordPress assets
 */

/*
function vf_remove_default_styles() {
    if (vf_is_vanefrance_page()) {
        // Remove default WordPress styles
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('global-styles');
    }
}
add_action('wp_enqueue_scripts', 'vf_remove_default_styles', 100);
*/

/*
function vf_remove_default_scripts() {
    if (vf_is_vanefrance_page()) {
        // Remove default WordPress scripts
        wp_dequeue_script('wp-embed');
    }
}
add_action('wp_enqueue_scripts', 'vf_remove_default_scripts', 100);
*/

/**
 * Allow HTML5 video tag in posts/pages
 */
function vf_allow_video_tags($tags, $context) {
    if ($context === 'post') {
        $tags['video'] = array(
            'autoplay' => true,
            'controls' => true,
            'height' => true,
            'loop' => true,
            'muted' => true,
            'poster' => true,
            'preload' => true,
            'src' => true,
            'width' => true,
            'playsinline' => true,
            'class' => true,
            'id' => true,
        );
        $tags['source'] = array(
            'src' => true,
            'type' => true,
        );
    }
    return $tags;
}
add_filter('wp_kses_allowed_html', 'vf_allow_video_tags', 10, 2);