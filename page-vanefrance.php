<?php
/**
 * Template Name: Vane France Landing Page
 * 
 * Custom page template for Vane France landing page
 * 
 * @package VaneFrance
 * @version 1.0.0
 */

get_header(); ?>

<main id="vf-main" class="vf-main-content">
    
    <!-- Hero Section with Video Background -->
    <section class="vf-hero">
        <!-- Video Background -->
        <video class="vf-hero-video" autoplay muted loop playsinline>
            <source src="<?php echo get_template_directory_uri(); ?>/vf-assets/videos/hero-video.mp4" type="video/mp4">
            <source src="<?php echo get_template_directory_uri(); ?>/vf-assets/videos/hero-video.webm" type="video/webm">
            <!-- Fallback image if video doesn't load -->
            <img src="<?php echo get_template_directory_uri(); ?>/vf-assets/images/hero-fallback.jpg" alt="Vane France Hero">
        </video>
        
        <!-- Overlay -->
        <div class="vf-hero-overlay"></div>
        
        <!-- Hero Content -->
        <div class="vf-hero-content vf-fade-in">
            <h1 class="vf-hero-title">Vane France</h1>
            <p class="vf-hero-subtitle">Perfumería de lujo, inspiración francesa</p>
            <a href="#vf-features" class="vf-btn-primary">Descubrir Colección</a>
        </div>
        
        <!-- Video Controls -->
        <div class="vf-video-controls">
            <button class="vf-video-btn vf-mute-btn" aria-label="Mute/Unmute video">
                <i class="fas fa-volume-mute"></i>
            </button>
            <button class="vf-video-btn vf-play-btn" aria-label="Play/Pause video">
                <i class="fas fa-pause"></i>
            </button>
        </div>
    </section>

    <!-- Features Section -->
    <section id="vf-features" class="vf-section vf-features">
        <div class="vf-container">
            <h2 class="vf-section-title vf-fade-in">Nuestra Excelencia</h2>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="vf-feature-card vf-slide-in-left">
                        <div class="vf-feature-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h3 class="vf-feature-title">Ingredientes Naturales</h3>
                        <p class="vf-feature-description">
                            Utilizamos únicamente los mejores ingredientes naturales 
                            seleccionados de las regiones más prestigiosas de Francia.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="vf-feature-card vf-fade-in">
                        <div class="vf-feature-icon">
                            <i class="fas fa-crown"></i>
                        </div>
                        <h3 class="vf-feature-title">Artesanía Francesa</h3>
                        <p class="vf-feature-description">
                            Cada fragancia es creada siguiendo las tradiciones 
                            centenarias de la perfumería francesa de alta costura.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="vf-feature-card vf-slide-in-right">
                        <div class="vf-feature-icon">
                            <i class="fas fa-gem"></i>
                        </div>
                        <h3 class="vf-feature-title">Exclusividad</h3>
                        <p class="vf-feature-description">
                            Colecciones limitadas que reflejan elegancia, 
                            sofisticación y la esencia del lujo francés.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Showcase Section -->
    <section class="vf-section vf-video-section">
        <div class="vf-container">
            <h2 class="vf-section-title vf-fade-in">Descubre Nuestro Arte</h2>
            
            <div class="vf-video-container vf-fade-in">
                <video class="vf-video" controls poster="<?php echo get_template_directory_uri(); ?>/vf-assets/images/video-poster.jpg">
                    <source src="<?php echo get_template_directory_uri(); ?>/vf-assets/videos/showcase-video.mp4" type="video/mp4">
                    <source src="<?php echo get_template_directory_uri(); ?>/vf-assets/videos/showcase-video.webm" type="video/webm">
                    Su navegador no soporta el elemento video.
                </video>
                
                <!-- Video Controls Overlay -->
                <div class="vf-video-controls">
                    <button class="vf-video-btn vf-mute-btn" aria-label="Mute/Unmute video">
                        <i class="fas fa-volume-up"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Collection Section -->
    <section class="vf-section">
        <div class="vf-container">
            <h2 class="vf-section-title vf-fade-in">Colección Signature</h2>
            
            <div class="row">
                <div class="col-lg-6 vf-slide-in-left">
                    <div class="vf-collection-item">
                        <img src="<?php echo get_template_directory_uri(); ?>/vf-assets/images/collection-1.jpg" 
                             alt="Collection 1" class="img-fluid rounded">
                        <h3>Essence de Paris</h3>
                        <p>Una fragancia que captura la esencia romántica de París, 
                           con notas florales delicadas y un toque de elegancia atemporal.</p>
                    </div>
                </div>
                
                <div class="col-lg-6 vf-slide-in-right">
                    <div class="vf-collection-item">
                        <img src="<?php echo get_template_directory_uri(); ?>/vf-assets/images/collection-2.jpg" 
                             alt="Collection 2" class="img-fluid rounded">
                        <h3>Riviera Dreams</h3>
                        <p>Inspirada en la Costa Azul francesa, esta fragancia combina 
                           frescura mediterránea con la sofisticación del sur de Francia.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="vf-section vf-features">
        <div class="vf-container">
            <h2 class="vf-section-title vf-fade-in">Contacto Exclusivo</h2>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="vf-feature-card vf-fade-in">
                        <p class="vf-feature-description">
                            Para consultas sobre nuestra colección exclusiva y 
                            experiencias personalizadas, contacte con nuestro 
                            equipo de asesores especializados.
                        </p>
                        
                        <div class="vf-contact-info">
                            <p><strong>Email:</strong> info@vanefrance.com</p>
                            <p><strong>Teléfono:</strong> +33 1 42 60 30 30</p>
                            <p><strong>Boutique:</strong> 75001 Paris, Francia</p>
                        </div>
                        
                        <a href="mailto:info@vanefrance.com" class="vf-btn-primary">
                            Contactar Ahora
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<!-- Custom Footer for Vane France -->
<footer class="vf-footer">
    <div class="vf-container">
        <p>&copy; <?php echo date('Y'); ?> Vane France. Tous droits réservés.</p>
        
        <div class="vf-social-links">
            <a href="#" class="vf-social-link" aria-label="Facebook">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="#" class="vf-social-link" aria-label="Instagram">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="#" class="vf-social-link" aria-label="Twitter">
                <i class="fab fa-twitter"></i>
            </a>
        </div>
    </div>
</footer>

<?php get_footer(); ?>