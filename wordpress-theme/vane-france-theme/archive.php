<?php
/**
 * Archive Template
 * 
 * @package VaneFrance
 */

get_header(); ?>

<main class="vf-container">
    <div class="vf-content-area">
        <header class="vf-archive-header">
            <?php the_archive_title('<h1>', '</h1>'); ?>
            <?php the_archive_description('<p>', '</p>'); ?>
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
                        <p><?php _e('Lo sentimos, no hay artículos disponibles en esta categoría.', 'vane-france'); ?></p>
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
/* Archive specific styles */
.vf-archive-header {
    text-align: center;
    margin-bottom: 50px;
    padding-bottom: 30px;
    border-bottom: 2px solid rgba(0, 35, 149, 0.1);
}

.vf-archive-header h1 {
    font-size: 2.8rem;
    margin-bottom: 15px;
    color: var(--vf-navy);
}

.vf-archive-header p {
    font-size: 1.1rem;
    color: #666;
    font-style: italic;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

/* Styles for different archive types */
.category .vf-archive-header h1::before {
    content: "📁 ";
}

.tag .vf-archive-header h1::before {
    content: "🏷️ ";
}

.date .vf-archive-header h1::before {
    content: "📅 ";
}

.author .vf-archive-header h1::before {
    content: "👤 ";
}

@media (max-width: 768px) {
    .vf-archive-header h1 {
        font-size: 2.2rem;
    }
    
    .vf-archive-header p {
        font-size: 1rem;
    }
}
</style>

<?php get_footer(); ?>