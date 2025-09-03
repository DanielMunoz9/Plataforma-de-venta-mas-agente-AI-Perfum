<?php
/**
 * Template Name: Catálogo Cliente
 * 
 * @package VaneFrance
 */

get_header(); ?>

<main class="vf-container">
    <div class="vf-content-area">
        <header class="vf-catalog-header">
            <h1><?php _e('Catálogo Cliente', 'vane-france'); ?></h1>
            <p><?php _e('Descubre nuestra selección premium de fragancias francesas', 'vane-france'); ?></p>
        </header>

        <div class="vf-catalog-content">
            <?php
            while (have_posts()) :
                the_post();
                the_content();
            endwhile;
            ?>
            
            <!-- Custom Cliente Products Display -->
            <div class="vf-cliente-products">
                <?php echo do_shortcode('[vf_catalog type="cliente" limit="12"]'); ?>
            </div>
            
            <!-- Cliente Features -->
            <section class="vf-cliente-features">
                <h2><?php _e('¿Por qué elegir Vane France?', 'vane-france'); ?></h2>
                <div class="vf-features-grid">
                    <div class="vf-feature-card">
                        <div class="feature-icon">🇫🇷</div>
                        <h3><?php _e('Auténticas Fragancias Francesas', 'vane-france'); ?></h3>
                        <p><?php _e('Importamos directamente desde Francia los mejores perfumes y cosméticos de alta calidad.', 'vane-france'); ?></p>
                    </div>
                    <div class="vf-feature-card">
                        <div class="feature-icon">✨</div>
                        <h3><?php _e('Calidad Premium', 'vane-france'); ?></h3>
                        <p><?php _e('Todos nuestros productos están certificados y garantizan la más alta calidad y durabilidad.', 'vane-france'); ?></p>
                    </div>
                    <div class="vf-feature-card">
                        <div class="feature-icon">💝</div>
                        <h3><?php _e('Experiencia Única', 'vane-france'); ?></h3>
                        <p><?php _e('Cada fragancia cuenta una historia y te transporta a los jardines de la Provenza francesa.', 'vane-france'); ?></p>
                    </div>
                    <div class="vf-feature-card">
                        <div class="feature-icon">🎁</div>
                        <h3><?php _e('Regalos Perfectos', 'vane-france'); ?></h3>
                        <p><?php _e('Encuentra el regalo ideal con nuestras elegantes presentaciones y empaques especiales.', 'vane-france'); ?></p>
                    </div>
                </div>
            </section>
            
            <!-- Product Categories -->
            <?php if (class_exists('WooCommerce')) : ?>
                <section class="vf-product-categories-section">
                    <h2><?php _e('Explora Nuestras Categorías', 'vane-france'); ?></h2>
                    
                    <?php
                    $categories = get_terms(array(
                        'taxonomy' => 'product_cat',
                        'hide_empty' => true,
                        'number' => 4,
                        'exclude' => array(15), // Exclude uncategorized
                    ));
                    
                    if (!empty($categories) && !is_wp_error($categories)) :
                    ?>
                        <div class="vf-categories-showcase">
                            <?php foreach ($categories as $category) : 
                                $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                                $image_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : get_template_directory_uri() . '/assets/img/product-1.jpg';
                            ?>
                                <div class="vf-category-showcase">
                                    <a href="<?php echo esc_url(get_term_link($category)); ?>">
                                        <div class="category-image">
                                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($category->name); ?>">
                                            <div class="category-overlay">
                                                <h3><?php echo esc_html($category->name); ?></h3>
                                                <p><?php printf(_n('%d producto', '%d productos', $category->count, 'vane-france'), $category->count); ?></p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
            
            <!-- Testimonials -->
            <section class="vf-testimonials">
                <h2><?php _e('Lo que dicen nuestros clientes', 'vane-france'); ?></h2>
                <div class="vf-testimonials-grid">
                    <div class="vf-testimonial-card">
                        <div class="testimonial-content">
                            <p>"Los perfumes de Vane France son increíbles. La calidad es excepcional y la duración es perfecta. Definitivamente volveré a comprar."</p>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-info">
                                <h4>María González</h4>
                                <span>Cliente satisfecha</span>
                            </div>
                            <div class="rating">⭐⭐⭐⭐⭐</div>
                        </div>
                    </div>
                    
                    <div class="vf-testimonial-card">
                        <div class="testimonial-content">
                            <p>"Excelente atención al cliente y productos de primera calidad. Me encanta la variedad de fragancias que ofrecen."</p>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-info">
                                <h4>Carlos Rodríguez</h4>
                                <span>Cliente frecuente</span>
                            </div>
                            <div class="rating">⭐⭐⭐⭐⭐</div>
                        </div>
                    </div>
                    
                    <div class="vf-testimonial-card">
                        <div class="testimonial-content">
                            <p>"La experiencia de compra fue fantástica. Los productos llegaron perfectamente empacados y el aroma es divino."</p>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-info">
                                <h4>Ana López</h4>
                                <span>Cliente nueva</span>
                            </div>
                            <div class="rating">⭐⭐⭐⭐⭐</div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- CTA Section -->
            <section class="vf-cliente-cta">
                <div class="vf-cta-content">
                    <h2><?php _e('¿Necesitas ayuda para elegir?', 'vane-france'); ?></h2>
                    <p><?php _e('Nuestros expertos están aquí para ayudarte a encontrar la fragancia perfecta para ti.', 'vane-france'); ?></p>
                    
                    <?php
                    $whatsapp_number = get_theme_mod('vf_whatsapp_number', '');
                    if (!empty($whatsapp_number)) :
                        $whatsapp_message = __('Hola, me gustaría recibir asesoría para elegir la fragancia perfecta.', 'vane-france');
                        $whatsapp_url = 'https://wa.me/' . $whatsapp_number . '?text=' . urlencode($whatsapp_message);
                    ?>
                        <a href="<?php echo esc_url($whatsapp_url); ?>" class="vf-btn vf-btn-primary" target="_blank">
                            <?php _e('Asesoría por WhatsApp', 'vane-france'); ?>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (class_exists('WooCommerce')) : ?>
                        <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="vf-btn vf-btn-secondary">
                            <?php _e('Ver Toda la Tienda', 'vane-france'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</main>

<style>
/* Cliente catalog specific styles - inherits from emprendedor styles */
.vf-cliente-features {
    margin: 60px 0;
    padding: 50px 0;
    background: rgba(237, 41, 57, 0.05);
    border-radius: 15px;
}

.vf-cliente-features h2 {
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 50px;
    color: var(--vf-navy);
}

.vf-features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    padding: 0 30px;
}

.vf-feature-card {
    background: var(--vf-white);
    padding: 40px 30px;
    border-radius: 15px;
    text-align: center;
    box-shadow: var(--vf-shadow);
    transition: all 0.3s ease;
}

.vf-feature-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--vf-shadow-hover);
}

.feature-icon {
    font-size: 3rem;
    margin-bottom: 20px;
}

.vf-feature-card h3 {
    color: var(--vf-navy);
    font-size: 1.4rem;
    margin-bottom: 15px;
}

.vf-feature-card p {
    color: #555;
    line-height: 1.6;
}

.vf-product-categories-section {
    margin: 60px 0;
}

.vf-product-categories-section h2 {
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 50px;
    color: var(--vf-navy);
}

.vf-categories-showcase {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
}

.vf-category-showcase {
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: var(--vf-shadow);
    transition: all 0.3s ease;
}

.vf-category-showcase:hover {
    transform: translateY(-5px);
    box-shadow: var(--vf-shadow-hover);
}

.category-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.category-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.vf-category-showcase:hover .category-image img {
    transform: scale(1.05);
}

.category-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0, 35, 149, 0.9));
    color: var(--vf-white);
    padding: 30px 20px 20px;
    text-align: center;
}

.category-overlay h3 {
    color: var(--vf-white);
    font-size: 1.3rem;
    margin-bottom: 5px;
}

.category-overlay p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.9rem;
}

.vf-category-showcase a {
    text-decoration: none;
    color: inherit;
}

.vf-testimonials {
    margin: 60px 0;
    padding: 50px 30px;
    background: rgba(0, 35, 149, 0.05);
    border-radius: 15px;
}

.vf-testimonials h2 {
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 50px;
    color: var(--vf-navy);
}

.vf-testimonials-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.vf-testimonial-card {
    background: var(--vf-white);
    padding: 30px;
    border-radius: 15px;
    box-shadow: var(--vf-shadow);
    transition: all 0.3s ease;
}

.vf-testimonial-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--vf-shadow-hover);
}

.testimonial-content {
    margin-bottom: 25px;
}

.testimonial-content p {
    font-style: italic;
    font-size: 1.1rem;
    line-height: 1.6;
    color: #555;
    margin: 0;
}

.testimonial-author {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 20px;
    border-top: 1px solid rgba(0, 35, 149, 0.1);
}

.author-info h4 {
    color: var(--vf-navy);
    font-size: 1.1rem;
    margin-bottom: 5px;
}

.author-info span {
    color: #666;
    font-size: 0.9rem;
}

.rating {
    font-size: 1.2rem;
}

.vf-cliente-cta {
    text-align: center;
    padding: 60px 30px;
    background: linear-gradient(135deg, var(--vf-red), var(--vf-navy));
    color: var(--vf-white);
    border-radius: 15px;
    margin: 50px 0;
}

.vf-cliente-cta .vf-cta-content h2 {
    color: var(--vf-white);
    font-size: 2.5rem;
    margin-bottom: 20px;
}

.vf-cliente-cta .vf-cta-content p {
    font-size: 1.2rem;
    margin-bottom: 40px;
    opacity: 0.9;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.vf-cliente-cta .vf-btn {
    margin: 0 10px 10px;
    border: 2px solid var(--vf-white);
}

.vf-cliente-cta .vf-btn-primary {
    background: var(--vf-white);
    color: var(--vf-red);
}

.vf-cliente-cta .vf-btn-primary:hover {
    background: transparent;
    color: var(--vf-white);
}

.vf-cliente-cta .vf-btn-secondary {
    background: transparent;
    color: var(--vf-white);
}

.vf-cliente-cta .vf-btn-secondary:hover {
    background: var(--vf-white);
    color: var(--vf-navy);
}

@media (max-width: 768px) {
    .vf-features-grid,
    .vf-categories-showcase,
    .vf-testimonials-grid {
        grid-template-columns: 1fr;
    }
    
    .vf-feature-card,
    .vf-testimonial-card {
        padding: 30px 20px;
    }
    
    .vf-testimonials {
        padding: 40px 20px;
    }
    
    .testimonial-author {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .vf-cliente-cta .vf-cta-content h2 {
        font-size: 2rem;
    }
    
    .vf-cliente-cta .vf-btn {
        display: block;
        width: 100%;
        max-width: 300px;
        margin: 10px auto;
    }
}
</style>

<?php get_footer(); ?>