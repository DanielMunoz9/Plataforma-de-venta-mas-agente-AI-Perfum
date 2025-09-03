<?php
/**
 * The front page template file
 *
 * @package VaneFrance
 */

get_header();
?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">
                <?php echo get_theme_mod('vf_hero_title', 'Vane France'); ?>
            </h1>
            <p class="hero-subtitle">
                <?php echo get_theme_mod('vf_hero_subtitle', 'Perfumería Francesa de Alta Gama'); ?>
            </p>
            <div class="hero-ctas">
                <a href="<?php echo esc_url(get_permalink(get_page_by_path('catalogo-emprendedor'))); ?>" class="hero-cta">
                    <?php esc_html_e('Plan Emprendedor', 'vane-france'); ?>
                </a>
                <a href="<?php echo esc_url(get_permalink(get_page_by_path('catalogo-cliente'))); ?>" class="hero-cta secondary">
                    <?php esc_html_e('Cliente', 'vane-france'); ?>
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="featured-products fade-in-on-scroll">
        <h2><?php esc_html_e('Productos Destacados', 'vane-france'); ?></h2>
        <?php
        if (class_exists('WooCommerce')) {
            echo do_shortcode('[vf_product_catalog tag="featured" limit="8"]');
        }
        ?>
    </section>

    <!-- Categories Section -->
    <section class="product-categories fade-in-on-scroll">
        <h2><?php esc_html_e('Nuestras Colecciones', 'vane-france'); ?></h2>
        <div class="categories-grid">
            <div class="category-card">
                <div class="category-image">
                    <img src="<?php echo VF_THEME_URL; ?>/assets/img/product-1.jpg" alt="<?php esc_attr_e('Para Emprendedores', 'vane-france'); ?>">
                </div>
                <div class="category-content">
                    <h3><?php esc_html_e('Para Emprendedores', 'vane-france'); ?></h3>
                    <p><?php esc_html_e('Productos especiales con descuentos exclusivos para emprendedores.', 'vane-france'); ?></p>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('catalogo-emprendedor'))); ?>" class="category-link">
                        <?php esc_html_e('Ver Catálogo', 'vane-france'); ?>
                    </a>
                </div>
            </div>
            
            <div class="category-card">
                <div class="category-image">
                    <img src="<?php echo VF_THEME_URL; ?>/assets/img/product-2.jpg" alt="<?php esc_attr_e('Para Clientes', 'vane-france'); ?>">
                </div>
                <div class="category-content">
                    <h3><?php esc_html_e('Para Clientes', 'vane-france'); ?></h3>
                    <p><?php esc_html_e('Nuestra selección completa de fragancias francesas de alta calidad.', 'vane-france'); ?></p>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('catalogo-cliente'))); ?>" class="category-link">
                        <?php esc_html_e('Ver Catálogo', 'vane-france'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section fade-in-on-scroll">
        <div class="about-content">
            <div class="about-text">
                <h2><?php esc_html_e('Sobre Vane France', 'vane-france'); ?></h2>
                <p><?php esc_html_e('Somos una perfumería especializada en fragancias francesas de alta gama. Nuestra pasión por la elegancia y la calidad nos lleva a seleccionar cuidadosamente cada producto que ofrecemos.', 'vane-france'); ?></p>
                <p><?php esc_html_e('Con años de experiencia en el sector, nos enorgullece brindar tanto a clientes individuales como a emprendedores la oportunidad de acceder a las mejores fragancias del mercado.', 'vane-france'); ?></p>
                <ul class="about-features">
                    <li><?php esc_html_e('Fragancias auténticas francesas', 'vane-france'); ?></li>
                    <li><?php esc_html_e('Precios especiales para emprendedores', 'vane-france'); ?></li>
                    <li><?php esc_html_e('Atención personalizada', 'vane-france'); ?></li>
                    <li><?php esc_html_e('Envíos a toda Colombia', 'vane-france'); ?></li>
                </ul>
            </div>
            <div class="about-image">
                <img src="<?php echo VF_THEME_URL; ?>/assets/img/storefront.jpg" alt="<?php esc_attr_e('Vane France Store', 'vane-france'); ?>">
            </div>
        </div>
    </section>

    <!-- Latest Blog Posts -->
    <?php
    $latest_posts = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => 3,
        'post_status' => 'publish'
    ));
    
    if ($latest_posts->have_posts()) :
    ?>
    <section class="latest-posts fade-in-on-scroll">
        <h2><?php esc_html_e('Últimas Noticias', 'vane-france'); ?></h2>
        <div class="posts-grid">
            <?php while ($latest_posts->have_posts()) : $latest_posts->the_post(); ?>
                <article class="post-card">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('vf-blog-thumb'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="post-content">
                        <div class="post-meta">
                            <time datetime="<?php echo get_the_date('c'); ?>">
                                <?php echo get_the_date(); ?>
                            </time>
                        </div>
                        
                        <h3 class="post-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        
                        <div class="post-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                        
                        <a href="<?php the_permalink(); ?>" class="read-more">
                            <?php esc_html_e('Leer más', 'vane-france'); ?>
                        </a>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        
        <div class="view-all-posts">
            <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="hero-cta">
                <?php esc_html_e('Ver Todas las Noticias', 'vane-france'); ?>
            </a>
        </div>
    </section>
    <?php
    wp_reset_postdata();
    endif;
    ?>

    <!-- Newsletter Section -->
    <section class="newsletter-section fade-in-on-scroll">
        <div class="newsletter-content">
            <h2><?php esc_html_e('Mantente Informado', 'vane-france'); ?></h2>
            <p><?php esc_html_e('Suscríbete a nuestro newsletter y recibe ofertas exclusivas, noticias sobre nuevos productos y consejos de perfumería.', 'vane-france'); ?></p>
            <form class="newsletter-form" action="#" method="post">
                <input type="email" name="newsletter_email" placeholder="<?php esc_attr_e('Ingresa tu email', 'vane-france'); ?>" required>
                <button type="submit"><?php esc_html_e('Suscribirse', 'vane-france'); ?></button>
                <input type="hidden" name="action" value="vf_newsletter_signup">
                <?php wp_nonce_field('vf_newsletter', 'vf_newsletter_nonce'); ?>
            </form>
        </div>
    </section>
</main>

<style>
/* Front Page Specific Styles */
.featured-products,
.product-categories,
.about-section,
.latest-posts,
.newsletter-section {
    margin-bottom: 4rem;
    padding: 2rem 0;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.category-card {
    background: var(--vf-white);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--vf-shadow-light);
    transition: transform 0.3s ease;
}

.category-card:hover {
    transform: translateY(-5px);
}

.category-image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.category-content {
    padding: 1.5rem;
}

.category-content h3 {
    margin-bottom: 1rem;
    color: var(--vf-navy);
}

.category-link {
    display: inline-block;
    margin-top: 1rem;
    color: var(--vf-red);
    text-decoration: none;
    font-weight: 500;
    font-family: var(--font-primary);
}

.category-link:hover {
    color: var(--vf-navy);
    text-decoration: underline;
}

.about-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    align-items: center;
}

.about-features {
    list-style: none;
    padding: 0;
    margin-top: 1.5rem;
}

.about-features li {
    padding: 0.5rem 0;
    position: relative;
    padding-left: 1.5rem;
}

.about-features li::before {
    content: "✓";
    position: absolute;
    left: 0;
    color: var(--vf-red);
    font-weight: bold;
}

.about-image img {
    width: 100%;
    border-radius: 12px;
    box-shadow: var(--vf-shadow);
}

.posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.view-all-posts {
    text-align: center;
    margin-top: 2rem;
}

.newsletter-section {
    background: var(--vf-gradient-nav);
    color: var(--vf-white);
    padding: 3rem 2rem;
    border-radius: 12px;
    text-align: center;
}

.newsletter-section h2 {
    color: var(--vf-white);
    text-shadow: none;
}

.newsletter-content .newsletter-form {
    max-width: 400px;
    margin: 2rem auto 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .about-content {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.2rem;
    }
    
    .categories-grid {
        grid-template-columns: 1fr;
    }
    
    .posts-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
get_footer();