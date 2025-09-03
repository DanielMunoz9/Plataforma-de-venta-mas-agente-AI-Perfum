<?php
/**
 * Blog Index Template
 * 
 * @package VaneFrance
 */

get_header(); ?>

<main class="vf-container">
    <div class="vf-content-area">
        <header class="vf-archive-header">
            <h1><?php _e('Blog', 'vane-france'); ?></h1>
            <p><?php _e('Últimas noticias y tendencias en perfumería', 'vane-france'); ?></p>
        </header>

        <div class="vf-blog-layout">
            <div class="vf-blog-main">
                <?php if (have_posts()) : ?>
                    <div class="vf-posts-container">
                        <?php while (have_posts()) : the_post(); ?>
                            <article <?php post_class('vf-post-card vf-animate-on-scroll'); ?>>
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('vf-blog-thumb', array('class' => 'vf-post-featured-image')); ?>
                                    </a>
                                <?php endif; ?>
                                
                                <div class="vf-post-content">
                                    <h2 class="vf-post-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>
                                    
                                    <div class="vf-post-meta">
                                        <span><i class="icon-calendar"></i> <?php echo get_the_date(); ?></span>
                                        <span><i class="icon-user"></i> <?php _e('Por', 'vane-france'); ?> <?php the_author(); ?></span>
                                        <?php if (has_category()) : ?>
                                            <span><i class="icon-folder"></i> <?php the_category(', '); ?></span>
                                        <?php endif; ?>
                                        <?php if (comments_open() || get_comments_number()) : ?>
                                            <span><i class="icon-comment"></i> <?php comments_number('0 comentarios', '1 comentario', '% comentarios'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="vf-post-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                    
                                    <a href="<?php the_permalink(); ?>" class="vf-read-more">
                                        <?php _e('Leer más', 'vane-france'); ?>
                                    </a>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>

                    <!-- Pagination -->
                    <nav class="vf-pagination">
                        <?php
                        echo paginate_links(array(
                            'prev_text' => __('&laquo; Anterior', 'vane-france'),
                            'next_text' => __('Siguiente &raquo;', 'vane-france'),
                            'before_page_number' => '<span>',
                            'after_page_number' => '</span>',
                            'type' => 'list',
                        ));
                        ?>
                    </nav>

                <?php else : ?>
                    <div class="vf-no-posts">
                        <h2><?php _e('No se encontraron artículos', 'vane-france'); ?></h2>
                        <p><?php _e('Lo sentimos, no hay artículos disponibles en este momento.', 'vane-france'); ?></p>
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="vf-btn vf-btn-primary">
                            <?php _e('Volver al inicio', 'vane-france'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside class="vf-blog-sidebar">
                <?php get_sidebar(); ?>
            </aside>
        </div>
    </div>
</main>

<style>
/* Blog index specific styles */
.vf-archive-header {
    text-align: center;
    margin-bottom: 50px;
    padding-bottom: 30px;
    border-bottom: 2px solid rgba(0, 35, 149, 0.1);
}

.vf-archive-header h1 {
    font-size: 3rem;
    margin-bottom: 15px;
    color: var(--vf-navy);
}

.vf-archive-header p {
    font-size: 1.2rem;
    color: #666;
    font-style: italic;
}

.vf-posts-container {
    margin-bottom: 40px;
}

.vf-post-card {
    margin-bottom: 40px;
}

.vf-post-meta span {
    margin-right: 20px;
}

.vf-post-meta i {
    margin-right: 5px;
    color: var(--vf-navy);
}

.vf-pagination {
    margin-top: 50px;
}

.vf-pagination .page-numbers {
    display: flex;
    justify-content: center;
    list-style: none;
    padding: 0;
    margin: 0;
}

.vf-pagination .page-numbers li {
    margin: 0 5px;
}

.vf-pagination .page-numbers a,
.vf-pagination .page-numbers span {
    display: block;
    padding: 12px 18px;
    text-decoration: none;
    background: var(--vf-white);
    color: var(--vf-navy);
    border: 2px solid var(--vf-navy);
    border-radius: 5px;
    transition: all 0.3s ease;
    font-weight: 600;
}

.vf-pagination .page-numbers a:hover,
.vf-pagination .page-numbers .current {
    background: var(--vf-navy);
    color: var(--vf-white);
    transform: translateY(-2px);
}

.vf-no-posts {
    text-align: center;
    padding: 80px 20px;
    background: rgba(0, 35, 149, 0.05);
    border-radius: 15px;
}

.vf-no-posts h2 {
    color: var(--vf-navy);
    margin-bottom: 20px;
    font-size: 2rem;
}

.vf-no-posts p {
    margin-bottom: 30px;
    color: #666;
    font-size: 1.1rem;
}

@media (max-width: 768px) {
    .vf-archive-header h1 {
        font-size: 2.5rem;
    }
    
    .vf-archive-header p {
        font-size: 1rem;
    }
    
    .vf-post-meta {
        flex-direction: column;
        gap: 8px;
    }
    
    .vf-post-meta span {
        margin-right: 0;
    }
    
    .vf-pagination .page-numbers {
        flex-wrap: wrap;
        gap: 5px;
    }
    
    .vf-pagination .page-numbers a,
    .vf-pagination .page-numbers span {
        padding: 10px 14px;
        font-size: 0.9rem;
    }
}
</style>

<?php get_footer(); ?>