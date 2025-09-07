<?php
// vf-assets/snippets/vf-functions-snippet.php
// Snippet to enqueue Vane France template assets. Place in your theme's functions.php or include this file.

function vf_enqueue_assets() {
  // Sólo cargar en la plantilla de Vane France (descomenta el return para limitarlo)
  if ( ! is_page_template( 'page-vanefrance.php' ) ) {
    // return; // Descomenta si quieres que los assets se carguen únicamente en la plantilla
  }

  wp_enqueue_style(
    'vf-style',
    get_stylesheet_directory_uri() . '/vf-assets/assets/css/vf-style.css',
    array(),
    '1.0.0'
  );

  wp_enqueue_script(
    'vf-script',
    get_stylesheet_directory_uri() . '/vf-assets/assets/js/vf-script.js',
    array(),
    '1.0.0',
    true
  );
}
add_action( 'wp_enqueue_scripts', 'vf_enqueue_assets' );
?>