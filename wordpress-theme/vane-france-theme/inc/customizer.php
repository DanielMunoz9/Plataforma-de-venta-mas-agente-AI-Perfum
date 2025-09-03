<?php
/**
 * Customizer settings for Vane France Theme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add customizer settings
 */
function vane_france_customize_register($wp_customize) {
    
    // Vane France Panel
    $wp_customize->add_panel('vane_france_panel', array(
        'title' => __('Vane France Settings', 'vane-france'),
        'description' => __('Configuraciones del tema Vane France', 'vane-france'),
        'priority' => 30,
    ));

    // Contact Section
    $wp_customize->add_section('vane_france_contact', array(
        'title' => __('Información de Contacto', 'vane-france'),
        'panel' => 'vane_france_panel',
        'priority' => 10,
    ));

    // WhatsApp Number
    $wp_customize->add_setting('vf_whatsapp_number', array(
        'default' => '3193605666',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('vf_whatsapp_number_control', array(
        'label' => __('Número de WhatsApp', 'vane-france'),
        'description' => __('Número de WhatsApp para el botón flotante (solo números)', 'vane-france'),
        'section' => 'vane_france_contact',
        'settings' => 'vf_whatsapp_number',
        'type' => 'text',
    ));

    // Phone Number
    $wp_customize->add_setting('vf_phone_number', array(
        'default' => '319 3605666',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('vf_phone_number_control', array(
        'label' => __('Teléfono de Contacto', 'vane-france'),
        'description' => __('Número de teléfono principal para mostrar en el sitio', 'vane-france'),
        'section' => 'vane_france_contact',
        'settings' => 'vf_phone_number',
        'type' => 'text',
    ));

    // Address 1
    $wp_customize->add_setting('vf_address_1', array(
        'default' => 'Cl. 12 #13-99 a 13, 1, Bogotá',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('vf_address_1_control', array(
        'label' => __('Dirección Tienda 1', 'vane-france'),
        'section' => 'vane_france_contact',
        'settings' => 'vf_address_1',
        'type' => 'text',
    ));

    // Address 2
    $wp_customize->add_setting('vf_address_2', array(
        'default' => 'Cl. 12 #13-69 Local 102, Bogotá',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('vf_address_2_control', array(
        'label' => __('Dirección Tienda 2', 'vane-france'),
        'section' => 'vane_france_contact',
        'settings' => 'vf_address_2',
        'type' => 'text',
    ));

    // Social Media Section
    $wp_customize->add_section('vane_france_social', array(
        'title' => __('Redes Sociales', 'vane-france'),
        'panel' => 'vane_france_panel',
        'priority' => 20,
    ));

    // Instagram URL
    $wp_customize->add_setting('vf_instagram_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('vf_instagram_url_control', array(
        'label' => __('URL de Instagram', 'vane-france'),
        'section' => 'vane_france_social',
        'settings' => 'vf_instagram_url',
        'type' => 'url',
    ));

    // Facebook URL
    $wp_customize->add_setting('vf_facebook_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('vf_facebook_url_control', array(
        'label' => __('URL de Facebook', 'vane-france'),
        'section' => 'vane_france_social',
        'settings' => 'vf_facebook_url',
        'type' => 'url',
    ));

    // TikTok URL
    $wp_customize->add_setting('vf_tiktok_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('vf_tiktok_url_control', array(
        'label' => __('URL de TikTok', 'vane-france'),
        'section' => 'vane_france_social',
        'settings' => 'vf_tiktok_url',
        'type' => 'url',
    ));

    // Hero Section
    $wp_customize->add_section('vane_france_hero', array(
        'title' => __('Sección Hero', 'vane-france'),
        'panel' => 'vane_france_panel',
        'priority' => 30,
    ));

    // Hero Title
    $wp_customize->add_setting('vf_hero_title', array(
        'default' => 'Vane France',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('vf_hero_title_control', array(
        'label' => __('Título del Hero', 'vane-france'),
        'section' => 'vane_france_hero',
        'settings' => 'vf_hero_title',
        'type' => 'text',
    ));

    // Hero Subtitle
    $wp_customize->add_setting('vf_hero_subtitle', array(
        'default' => 'Perfumería Francesa de Alta Gama',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('vf_hero_subtitle_control', array(
        'label' => __('Subtítulo del Hero', 'vane-france'),
        'section' => 'vane_france_hero',
        'settings' => 'vf_hero_subtitle',
        'type' => 'text',
    ));

    // Hero Background Image
    $wp_customize->add_setting('vf_hero_background', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'vf_hero_background_control', array(
        'label' => __('Imagen de Fondo del Hero', 'vane-france'),
        'section' => 'vane_france_hero',
        'settings' => 'vf_hero_background',
    )));

    // Enable/Disable Hero Section
    $wp_customize->add_setting('vf_show_hero', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('vf_show_hero_control', array(
        'label' => __('Mostrar Sección Hero', 'vane-france'),
        'description' => __('Mostrar u ocultar la sección hero en la página de inicio', 'vane-france'),
        'section' => 'vane_france_hero',
        'settings' => 'vf_show_hero',
        'type' => 'checkbox',
    ));

    // Colors Section
    $wp_customize->add_section('vane_france_colors', array(
        'title' => __('Colores del Tema', 'vane-france'),
        'panel' => 'vane_france_panel',
        'priority' => 40,
    ));

    // Primary Color (Navy Blue)
    $wp_customize->add_setting('vf_primary_color', array(
        'default' => '#002395',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'vf_primary_color_control', array(
        'label' => __('Color Primario', 'vane-france'),
        'description' => __('Color azul marino principal del tema', 'vane-france'),
        'section' => 'vane_france_colors',
        'settings' => 'vf_primary_color',
    )));

    // Secondary Color (Red)
    $wp_customize->add_setting('vf_secondary_color', array(
        'default' => '#ed2939',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'vf_secondary_color_control', array(
        'label' => __('Color Secundario', 'vane-france'),
        'description' => __('Color rojo secundario del tema', 'vane-france'),
        'section' => 'vane_france_colors',
        'settings' => 'vf_secondary_color',
    )));

    // Typography Section
    $wp_customize->add_section('vane_france_typography', array(
        'title' => __('Tipografía', 'vane-france'),
        'panel' => 'vane_france_panel',
        'priority' => 50,
    ));

    // Main Font
    $wp_customize->add_setting('vf_main_font', array(
        'default' => 'Playfair Display',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('vf_main_font_control', array(
        'label' => __('Fuente Principal', 'vane-france'),
        'description' => __('Fuente principal del tema (actual: Playfair Display)', 'vane-france'),
        'section' => 'vane_france_typography',
        'settings' => 'vf_main_font',
        'type' => 'select',
        'choices' => array(
            'Playfair Display' => 'Playfair Display (Recomendado)',
            'Lora' => 'Lora',
            'Crimson Text' => 'Crimson Text',
            'EB Garamond' => 'EB Garamond',
            'Libre Baskerville' => 'Libre Baskerville',
        ),
    ));

    // Advanced Section
    $wp_customize->add_section('vane_france_advanced', array(
        'title' => __('Configuración Avanzada', 'vane-france'),
        'panel' => 'vane_france_panel',
        'priority' => 60,
    ));

    // Custom CSS
    $wp_customize->add_setting('vf_custom_css', array(
        'default' => '',
        'sanitize_callback' => 'wp_strip_all_tags',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('vf_custom_css_control', array(
        'label' => __('CSS Personalizado', 'vane-france'),
        'description' => __('CSS adicional para personalizar el tema', 'vane-france'),
        'section' => 'vane_france_advanced',
        'settings' => 'vf_custom_css',
        'type' => 'textarea',
    ));

    // Custom JavaScript
    $wp_customize->add_setting('vf_custom_js', array(
        'default' => '',
        'sanitize_callback' => 'wp_strip_all_tags',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('vf_custom_js_control', array(
        'label' => __('JavaScript Personalizado', 'vane-france'),
        'description' => __('JavaScript adicional (sin etiquetas script)', 'vane-france'),
        'section' => 'vane_france_advanced',
        'settings' => 'vf_custom_js',
        'type' => 'textarea',
    ));
}
add_action('customize_register', 'vane_france_customize_register');

/**
 * Output custom CSS in head
 */
function vane_france_customizer_css() {
    $primary_color = get_theme_mod('vf_primary_color', '#002395');
    $secondary_color = get_theme_mod('vf_secondary_color', '#ed2939');
    $main_font = get_theme_mod('vf_main_font', 'Playfair Display');
    $custom_css = get_theme_mod('vf_custom_css', '');
    
    ?>
    <style type="text/css" id="vane-france-customizer-css">
        :root {
            --vf-primary-color: <?php echo esc_html($primary_color); ?>;
            --vf-secondary-color: <?php echo esc_html($secondary_color); ?>;
            --vf-main-font: '<?php echo esc_html($main_font); ?>', serif;
        }
        
        /* Apply custom colors */
        body {
            background: linear-gradient(135deg, <?php echo esc_html($primary_color); ?> 0%, #fff 50%, <?php echo esc_html($secondary_color); ?> 100%);
            font-family: var(--vf-main-font);
        }
        
        h1, h2, h3, h4, h5, h6 {
            color: <?php echo esc_html($primary_color); ?>;
            font-family: var(--vf-main-font);
        }
        
        .navbar-brand,
        .blog-post-title a,
        .post-title,
        .archive-title,
        .widget-title {
            color: <?php echo esc_html($primary_color); ?> !important;
        }
        
        .btn-vf-primary {
            background: linear-gradient(45deg, <?php echo esc_html($primary_color); ?>, <?php echo lighten_color($primary_color, 20); ?>);
        }
        
        .btn-vf-secondary {
            background: linear-gradient(45deg, <?php echo esc_html($secondary_color); ?>, <?php echo lighten_color($secondary_color, 20); ?>);
        }
        
        .site-header {
            background: linear-gradient(90deg, <?php echo esc_html($primary_color); ?> 60%, <?php echo esc_html($secondary_color); ?> 100%);
        }
        
        .site-footer {
            background: linear-gradient(135deg, <?php echo esc_html($primary_color); ?> 0%, <?php echo lighten_color($primary_color, 30); ?> 100%);
        }
        
        .product-especial-badge {
            background: linear-gradient(45deg, <?php echo esc_html($primary_color); ?>, <?php echo lighten_color($primary_color, 20); ?>);
        }
        
        .read-more,
        .read-more-btn {
            color: <?php echo esc_html($secondary_color); ?>;
        }
        
        .read-more:hover,
        .read-more-btn:hover {
            color: <?php echo esc_html($primary_color); ?>;
        }
        
        /* Custom CSS from customizer */
        <?php echo wp_strip_all_tags($custom_css); ?>
    </style>
    <?php
}
add_action('wp_head', 'vane_france_customizer_css');

/**
 * Output custom JavaScript in footer
 */
function vane_france_customizer_js() {
    $custom_js = get_theme_mod('vf_custom_js', '');
    
    if (!empty($custom_js)) {
        ?>
        <script type="text/javascript" id="vane-france-customizer-js">
            <?php echo wp_strip_all_tags($custom_js); ?>
        </script>
        <?php
    }
}
add_action('wp_footer', 'vane_france_customizer_js');

/**
 * Helper function to lighten a color
 */
function lighten_color($color, $percent) {
    $color = str_replace('#', '', $color);
    $rgb = array_map('hexdec', str_split($color, 2));
    
    foreach ($rgb as &$component) {
        $component = min(255, $component + ($percent * 255 / 100));
    }
    
    return '#' . implode('', array_map(function($component) {
        return str_pad(dechex($component), 2, '0', STR_PAD_LEFT);
    }, $rgb));
}

/**
 * Customizer live preview JavaScript
 */
function vane_france_customizer_preview_js() {
    wp_enqueue_script(
        'vane-france-customizer-preview',
        get_template_directory_uri() . '/assets/js/customizer-preview.js',
        array('customize-preview'),
        '1.0.0',
        true
    );
}
add_action('customize_preview_init', 'vane_france_customizer_preview_js');