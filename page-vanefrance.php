<?php
/**
 * Template Name: Página — Vane France
 * Description: Template preparado para WordPress. Coloca este archivo en tu theme hijo.
 *
 * Instrucciones:
 * - Sube el GIF y el MP4 a la Mediateca y copia sus IDs a $gif_id y $video_id abajo.
 * - Coloca los assets en /vf-assets/ dentro de tu theme hijo.
 */

get_header();

// --- Ajusta aquí los IDs de attachments (opcional) ---
$gif_id   = 0; // attachment ID del GIF en la Mediateca (ej. 123) o 0 para fallback
$video_id = 0; // attachment ID del MP4 en la Mediateca (ej. 456) o 0 para no mostrar video
// ----------------------------------------------------------------

$gif_url   = $gif_id ? wp_get_attachment_url( (int) $gif_id ) : get_stylesheet_directory_uri() . '/vf-assets/assets/img/eiffel.gif';
$video_url = $video_id ? wp_get_attachment_url( (int) $video_id ) : '';

?>
<link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/vf-assets/assets/css/vf-style.css' ); ?>" />

<div class="vf-topbar">Hecho en Colombia • Insumos nacionales • Mayoristas y retail</div>

<section class="vf-hero" id="inicio" aria-label="Hero Vane France">
  <header class="vf-header vf-container">
    <div class="vf-header-inner">
      <div style="display:flex;align-items:center;gap:12px">
        <div class="vf-logo">Vane France</div>
        <div class="header-socials" role="navigation" aria-label="Redes sociales">
          <a class="social-a" href="https://vm.tiktok.com/ZSHtqTGegGusA-lCKcC/" target="_blank" rel="noopener" title="TikTok" aria-label="TikTok">
            <svg viewBox="0 0 24 24" fill="none"><path d="M12 2v6.6A4.4 4.4 0 1016.4 13V7h3V3h-7z" fill="#000"/></svg>
          </a>
          <a class="social-a" href="https://www.instagram.com/stories/vane_francee/" target="_blank" rel="noopener" title="Instagram" aria-label="Instagram">
            <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="4" stroke="#E1306C" stroke-width="1.6" fill="none"/><circle cx="12" cy="12" r="3.2" stroke="#E1306C" stroke-width="1.6" fill="none"/></svg>
          </a>
          <a class="social-a" href="https://business.facebook.com/" target="_blank" rel="noopener" title="Facebook" aria-label="Facebook">
            <svg viewBox="0 0 24 24" fill="none"><path d="M15 3h3v4h-3v14h-4V7H9V3h3V1.8C12 1 12.8 1 13.6 1h.8v2z" fill="#1877F2"/></svg>
          </a>
        </div>
      </div>

      <nav class="vf-nav" aria-label="Navegación principal">
        <a class="vf-link" href="#categorias">Categorías</a>
        <a class="vf-link" href="#blog">Blog</a>
        <a class="vf-link" href="#pqr">PQR</a>
        <a class="vf-link primary" href="#contacto">Contacto</a>
      </nav>
    </div>
  </header>

  <div class="flowers" aria-hidden="true">
    <span class="flower f1"></span><span class="flower f2"></span><span class="flower f3"></span>
    <span class="flower f4"></span><span class="flower f5"></span><span class="flower f6"></span>
  </div>

  <div class="hero-wrap vf-container">
    <div class="hero-video" aria-label="Video principal">
      <div class="video-wrap">
        <div class="video-layer">
          <?php if ( $video_url ) : ?>
            <video id="heroVideo" src="<?php echo esc_url( $video_url ); ?>" muted playsinline loop autoplay poster="<?php echo esc_url( get_stylesheet_directory_uri() . '/vf-assets/assets/img/hero-poster.jpg' ); ?>" aria-label="Video principal"></video>
          <?php else: ?>
            <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/vf-assets/assets/img/hero-poster.jpg' ); ?>" alt="Hero poster" style="width:100%;height:auto;border-radius:12px;display:block" />
          <?php endif; ?>
        </div>

        <div class="wave top" aria-hidden="true"></div>
        <div class="wave mid" aria-hidden="true"></div>
        <div class="wave bot" aria-hidden="true"></div>
      </div>
    </div>

    <aside class="hero-panel" aria-label="Panel de marca">
      <div style="position:relative;width:100%;">
        <img class="vf-gif-big" id="mainGif" src="<?php echo esc_url( $gif_url ); ?>" alt="Vane France Q" />
        <div class="gif-overlay">VANE FRANCE Q</div>
      </div>

      <div class="vf-gif-brand"><span class="vf-name">VANE FRANCE</span>
        <div class="vf-gif-desc">💼Perfumería mayorista & Proveedor de insumos.<br/>🌍 Tu mejor aliado comercial.<br/>✨ Perfumes de lujo que impulsan tu negocio💰</div>
      </div>

      <h1 class="vf-brand">Vane France</h1>
      <p class="vf-desc">Perfumería de lujo con esencia francesa. Proveedor mayorista de insumos.</p>

      <div class="vf-cta-row">
        <button id="open-b2c" class="vf-btn primary" type="button">Quiero mi fragancia</button>
        <button id="open-b2b" class="vf-btn secondary" type="button">Plan Emprendedor</button>
      </div>

      <div class="vf-badge">🇨🇴 Insumos nacionales • Entrega en toda Colombia</div>
    </aside>
  </div>

  <div class="hero-bottom-mask" aria-hidden="true">
    <svg viewBox="0 0 1440 120" preserveAspectRatio="none"><path d="M0,48 C120,96 360,8 540,26 C720,44 900,110 1080,92 C1260,74 1320,40 1440,56 L1440,120 L0,120 Z" fill="#ffffff"/></svg>
  </div>
</section>

<!-- Aquí puedes incluir el resto de secciones o usar get_template_part -->
<?php get_template_part( 'template-parts/vanefrance', 'sections' ); ?>

<script src="<?php echo esc_url( get_stylesheet_directory_uri() . '/vf-assets/assets/js/vf-script.js' ); ?>"></script>
<?php get_footer(); ?>