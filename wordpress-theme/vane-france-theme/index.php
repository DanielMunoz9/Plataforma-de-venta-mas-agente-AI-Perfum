<?php
/**
 * The main template file for Vane France Theme
 */

get_header(); ?>

<main class="container mt-4">
    <div class="row">
        <div class="col-lg-8">
            <?php if (is_home() && !is_paged()): ?>
                <!-- Hero Section for Home Page -->
                <section class="hero-section mb-5">
                    <div class="hero-content">
                        <h1 class="text-center">Vane France</h1>
                        <p class="text-center">Perfumería Francesa de Alta Gama</p>
                        <div class="cta-buttons">
                            <a href="<?php echo esc_url(get_permalink(get_page_by_title('Catálogo Emprendedor'))); ?>" class="btn-vf-primary">
                                <i class="fas fa-briefcase me-2"></i>Plan Emprendedor
                            </a>
                            <a href="<?php echo esc_url(get_permalink(get_page_by_title('Catálogo Cliente'))); ?>" class="btn-vf-secondary">
                                <i class="fas fa-user me-2"></i>Cliente
                            </a>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <div class="content-section">
                <?php if (have_posts()) : ?>
                    <div class="blog-posts">
                        <?php while (have_posts()) : the_post(); ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class('blog-post'); ?>>
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="blog-post-image-container">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('large', array('class' => 'blog-post-image')); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="blog-post-content">
                                    <h2 class="blog-post-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>
                                    
                                    <div class="blog-post-meta">
                                        <i class="fas fa-calendar me-2"></i>
                                        <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                                        <span class="mx-2">|</span>
                                        <i class="fas fa-user me-2"></i>
                                        <?php the_author(); ?>
                                        <?php if (get_comments_number() > 0) : ?>
                                            <span class="mx-2">|</span>
                                            <i class="fas fa-comments me-2"></i>
                                            <?php comments_number('0 comentarios', '1 comentario', '% comentarios'); ?>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="blog-post-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                    
                                    <a href="<?php the_permalink(); ?>" class="read-more">
                                        Leer más <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </article>
                        <?php endwhile; ?>
                        
                        <!-- Pagination -->
                        <div class="pagination-wrapper text-center mt-4">
                            <?php
                            echo paginate_links(array(
                                'mid_size' => 2,
                                'prev_text' => '<i class="fas fa-chevron-left"></i> Anterior',
                                'next_text' => 'Siguiente <i class="fas fa-chevron-right"></i>',
                                'class' => 'pagination-vf'
                            ));
                            ?>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="no-posts text-center py-5">
                        <h2>No hay publicaciones disponibles</h2>
                        <p>Actualmente no hay contenido para mostrar.</p>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn-vf-primary">
                            <i class="fas fa-home me-2"></i>Volver al inicio
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-lg-4">
            <?php get_sidebar(); ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>