<?php
/**
 * Front Page Template
 * 
 * @package VaneFrance
 */

get_header(); ?>

<!-- Hero Section -->
<section class="vf-hero">
    <div class="vf-container">
        <div class="vf-hero-content">
            <h1 class="vf-animate-on-scroll">
                <?php echo get_theme_mod('vf_hero_title', 'Vane France'); ?>
            </h1>
            <p class="vf-animate-on-scroll">
                <?php echo get_theme_mod('vf_hero_subtitle', 'Perfumería Francesa de Excelencia'); ?>
            </p>
            <div class="vf-cta-buttons vf-animate-on-scroll">
                <?php 
                $emprendedor_page = get_page_by_path('catalogo-emprendedor');
                $cliente_page = get_page_by_path('catalogo-cliente');
                ?>
                
                <?php if ($emprendedor_page) : ?>
                    <a href="<?php echo esc_url(get_permalink($emprendedor_page)); ?>" class="vf-btn vf-btn-primary">
                        <?php _e('Plan Emprendedor', 'vane-france'); ?>
                    </a>
                <?php endif; ?>
                
                <?php if ($cliente_page) : ?>
                    <a href="<?php echo esc_url(get_permalink($cliente_page)); ?>" class="vf-btn vf-btn-secondary">
                        <?php _e('Cliente', 'vane-france'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Main Content Area -->
<main class="vf-container">
    <div class="vf-content-area">
        
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <div class="vf-front-page-content">
                    <?php the_content(); ?>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>

        <!-- Featured Products Section -->
        <?php if (class_exists('WooCommerce')) : ?>
            <section class="vf-featured-products vf-animate-on-scroll">
                <h2><?php _e('Productos Destacados', 'vane-france'); ?></h2>
                
                <?php
                $featured_args = array(
                    'post_type' => 'product',
                    'posts_per_page' => 8,
                    'meta_query' => array(
                        array(
                            'key' => '_featured',
                            'value' => 'yes'
                        )
                    )
                );
                
                $featured_products = new WP_Query($featured_args);
                
                if ($featured_products->have_posts()) :
                ?>
                    <div class="woocommerce">
                        <ul class="products columns-4">
                            <?php while ($featured_products->have_posts()) : $featured_products->the_post(); ?>
                                <?php wc_get_template_part('content', 'product'); ?>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p><?php _e('No hay productos destacados disponibles.', 'vane-france'); ?></p>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <!-- Product Categories Section -->
        <?php if (class_exists('WooCommerce')) : ?>
            <section class="vf-product-categories vf-animate-on-scroll">
                <h2><?php _e('Nuestras Categorías', 'vane-france'); ?></h2>
                
                <?php
                $categories = get_terms(array(
                    'taxonomy' => 'product_cat',
                    'hide_empty' => true,
                    'number' => 6,
                    'exclude' => array(15), // Exclude uncategorized
                ));
                
                if (!empty($categories) && !is_wp_error($categories)) :
                ?>
                    <div class="vf-categories-grid">
                        <?php foreach ($categories as $category) : 
                            $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                            $image_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : get_template_directory_uri() . '/assets/img/product-1.jpg';
                        ?>
                            <div class="vf-category-card">
                                <a href="<?php echo esc_url(get_term_link($category)); ?>">
                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($category->name); ?>">
                                    <div class="vf-category-info">
                                        <h3><?php echo esc_html($category->name); ?></h3>
                                        <p><?php printf(_n('%d producto', '%d productos', $category->count, 'vane-france'), $category->count); ?></p>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <!-- Blog Preview Section -->
        <section class="vf-blog-preview vf-animate-on-scroll">
            <h2><?php _e('Últimas Noticias', 'vane-france'); ?></h2>
            
            <?php
            $blog_args = array(
                'post_type' => 'post',
                'posts_per_page' => 3,
                'post_status' => 'publish'
            );
            
            $blog_posts = new WP_Query($blog_args);
            
            if ($blog_posts->have_posts()) :
            ?>
                <div class="vf-blog-grid">
                    <?php while ($blog_posts->have_posts()) : $blog_posts->the_post(); ?>
                        <article class="vf-post-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('vf-blog-thumb', array('class' => 'vf-post-featured-image')); ?>
                                </a>
                            <?php endif; ?>
                            
                            <div class="vf-post-content">
                                <h3 class="vf-post-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                
                                <div class="vf-post-meta">
                                    <span><?php echo get_the_date(); ?></span>
                                    <span><?php _e('Por', 'vane-france'); ?> <?php the_author(); ?></span>
                                </div>
                                
                                <p class="vf-post-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
                                </p>
                                
                                <a href="<?php the_permalink(); ?>" class="vf-read-more">
                                    <?php _e('Leer más', 'vane-france'); ?>
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
                
                <div class="vf-blog-cta">
                    <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="vf-btn vf-btn-primary">
                        <?php _e('Ver Todos los Artículos', 'vane-france'); ?>
                    </a>
                </div>
                
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <p><?php _e('No hay artículos disponibles.', 'vane-france'); ?></p>
            <?php endif; ?>
        </section>

        <!-- About Section -->
        <section class="vf-about-section vf-animate-on-scroll">
            <div class="vf-about-content">
                <div class="vf-about-text">
                    <h2><?php _e('Sobre Vane France', 'vane-france'); ?></h2>
                    <p><?php _e('Somos una perfumería francesa especializada en fragancias de alta calidad. Con más de 10 años de experiencia, ofrecemos productos exclusivos tanto para emprendedores como para clientes finales.', 'vane-france'); ?></p>
                    <p><?php _e('Nuestro compromiso es brindar la mejor atención al cliente y productos de la más alta calidad, directamente importados de Francia.', 'vane-france'); ?></p>
                    
                    <div class="vf-about-features">
                        <div class="vf-feature">
                            <h4><?php _e('Calidad Premium', 'vane-france'); ?></h4>
                            <p><?php _e('Productos importados directamente de Francia', 'vane-france'); ?></p>
                        </div>
                        <div class="vf-feature">
                            <h4><?php _e('Atención Personalizada', 'vane-france'); ?></h4>
                            <p><?php _e('Asesoramiento experto para cada cliente', 'vane-france'); ?></p>
                        </div>
                        <div class="vf-feature">
                            <h4><?php _e('Envío Rápido', 'vane-france'); ?></h4>
                            <p><?php _e('Entrega en toda Colombia', 'vane-france'); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="vf-about-image">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/storefront.jpg" alt="<?php _e('Tienda Vane France', 'vane-france'); ?>">
                </div>
            </div>
        </section>
    </div>
</main>

<style>
/* Front page specific styles */
.vf-featured-products,
.vf-product-categories,
.vf-blog-preview,
.vf-about-section {
    margin: 50px 0;
    padding: 30px 0;
}

.vf-featured-products h2,
.vf-product-categories h2,
.vf-blog-preview h2,
.vf-about-section h2 {
    text-align: center;
    margin-bottom: 40px;
    font-size: 2.5rem;
}

.vf-categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 25px;
    margin-top: 30px;
}

.vf-category-card {
    background: var(--vf-white);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: var(--vf-shadow);
    transition: all 0.3s ease;
}

.vf-category-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--vf-shadow-hover);
}

.vf-category-card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.vf-category-info {
    padding: 20px;
    text-align: center;
}

.vf-category-info h3 {
    color: var(--vf-navy);
    margin-bottom: 8px;
    font-size: 1.2rem;
}

.vf-category-info p {
    color: #666;
    font-size: 0.9rem;
}

.vf-category-card a {
    text-decoration: none;
    color: inherit;
}

.vf-blog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.vf-blog-cta {
    text-align: center;
}

.vf-about-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    align-items: center;
}

.vf-about-features {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
    margin-top: 30px;
}

.vf-feature {
    padding: 20px;
    background: rgba(0, 35, 149, 0.05);
    border-radius: 10px;
    border-left: 4px solid var(--vf-navy);
}

.vf-feature h4 {
    color: var(--vf-navy);
    margin-bottom: 10px;
    font-size: 1.1rem;
}

.vf-feature p {
    color: #555;
    margin: 0;
    line-height: 1.5;
}

.vf-about-image img {
    width: 100%;
    border-radius: 15px;
    box-shadow: var(--vf-shadow);
}

@media (max-width: 768px) {
    .vf-about-content {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .vf-categories-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
    }
    
    .vf-blog-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .vf-featured-products h2,
    .vf-product-categories h2,
    .vf-blog-preview h2,
    .vf-about-section h2 {
        font-size: 2rem;
    }
}
</style>

<?php get_footer(); ?>