<?php
/**
 * The main template file
 *
 * @package VaneFrance
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="blog-layout">
        <div class="blog-posts">
            <?php if (have_posts()) : ?>
                
                <header class="page-header">
                    <?php if (is_home() && !is_front_page()) : ?>
                        <h1 class="page-title"><?php single_post_title(); ?></h1>
                    <?php elseif (is_home()) : ?>
                        <h1 class="page-title"><?php esc_html_e('Últimas Noticias', 'vane-france'); ?></h1>
                    <?php else : ?>
                        <?php the_archive_title('<h1 class="page-title">', '</h1>'); ?>
                        <?php the_archive_description('<div class="archive-description">', '</div>'); ?>
                    <?php endif; ?>
                </header>

                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('vf-blog-thumb'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="post-content">
                            <div class="post-meta">
                                <time class="post-date" datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo get_the_date(); ?>
                                </time>
                                <span class="meta-separator">•</span>
                                <span class="post-author">
                                    <?php esc_html_e('Por', 'vane-france'); ?> 
                                    <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                                        <?php the_author(); ?>
                                    </a>
                                </span>
                                <?php if (has_category()) : ?>
                                    <span class="meta-separator">•</span>
                                    <span class="post-categories">
                                        <?php the_category(', '); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <h2 class="post-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                            <div class="post-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <div class="post-footer">
                                <a href="<?php the_permalink(); ?>" class="read-more">
                                    <?php esc_html_e('Leer más', 'vane-france'); ?>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/>
                                    </svg>
                                </a>
                                
                                <?php if (has_tag()) : ?>
                                    <div class="post-tags">
                                        <?php the_tags('', ' '); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>

                <nav class="pagination-nav" aria-label="<?php esc_attr_e('Navegación de entradas', 'vane-france'); ?>">
                    <?php vane_france_pagination(); ?>
                </nav>

            <?php else : ?>
                
                <section class="no-results not-found">
                    <header class="page-header">
                        <h1 class="page-title"><?php esc_html_e('No se encontraron resultados', 'vane-france'); ?></h1>
                    </header>
                    
                    <div class="page-content">
                        <?php if (is_home() && current_user_can('publish_posts')) : ?>
                            <p>
                                <?php
                                printf(
                                    wp_kses(
                                        __('¿Listo para publicar tu primera entrada? <a href="%1$s">Comienza aquí</a>.', 'vane-france'),
                                        array(
                                            'a' => array(
                                                'href' => array(),
                                            ),
                                        )
                                    ),
                                    esc_url(admin_url('post-new.php'))
                                );
                                ?>
                            </p>
                        <?php elseif (is_search()) : ?>
                            <p><?php esc_html_e('Lo sentimos, pero no se encontraron resultados para tu búsqueda. Intenta con palabras clave diferentes.', 'vane-france'); ?></p>
                            <?php get_search_form(); ?>
                        <?php else : ?>
                            <p><?php esc_html_e('Parece que no podemos encontrar lo que estás buscando. Tal vez la búsqueda pueda ayudar.', 'vane-france'); ?></p>
                            <?php get_search_form(); ?>
                        <?php endif; ?>
                    </div>
                </section>

            <?php endif; ?>
        </div>

        <?php get_sidebar(); ?>
    </div>
</main>

<style>
/* Blog Index Styles */
.page-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--vf-red);
}

.page-title {
    color: var(--vf-navy);
    font-family: var(--font-primary);
    margin-bottom: 0.5rem;
}

.archive-description {
    color: var(--vf-dark-gray);
    font-size: 1.1rem;
    line-height: 1.6;
}

.post-meta {
    font-size: 0.9rem;
    color: var(--vf-red);
    margin-bottom: 1rem;
    font-weight: 500;
}

.post-meta a {
    color: inherit;
    text-decoration: none;
}

.post-meta a:hover {
    color: var(--vf-navy);
    text-decoration: underline;
}

.meta-separator {
    margin: 0 0.5rem;
    opacity: 0.7;
}

.post-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.read-more {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--vf-red);
    text-decoration: none;
    font-weight: 500;
    font-family: var(--font-primary);
    transition: color 0.3s ease;
}

.read-more:hover {
    color: var(--vf-navy);
    text-decoration: none;
}

.read-more svg {
    transition: transform 0.3s ease;
}

.read-more:hover svg {
    transform: translateX(3px);
}

.post-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.post-tags a {
    background: var(--vf-light-gray);
    color: var(--vf-navy);
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    text-decoration: none;
    font-size: 0.8rem;
    transition: background 0.3s ease;
}

.post-tags a:hover {
    background: var(--vf-red);
    color: var(--vf-white);
}

.pagination-nav {
    margin-top: 3rem;
    text-align: center;
}

.pagination-nav .page-numbers {
    display: inline-flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 0.5rem;
}

.pagination-nav .page-numbers li {
    margin: 0;
}

.pagination-nav .page-numbers a,
.pagination-nav .page-numbers span {
    display: block;
    padding: 0.5rem 1rem;
    background: var(--vf-white);
    color: var(--vf-navy);
    text-decoration: none;
    border-radius: 4px;
    border: 1px solid #ddd;
    transition: all 0.3s ease;
}

.pagination-nav .page-numbers a:hover,
.pagination-nav .page-numbers .current {
    background: var(--vf-red);
    color: var(--vf-white);
    border-color: var(--vf-red);
}

.no-results {
    text-align: center;
    padding: 3rem 2rem;
}

.no-results .page-content {
    margin-top: 2rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .post-footer {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .post-meta {
        font-size: 0.8rem;
    }
    
    .pagination-nav .page-numbers {
        flex-wrap: wrap;
        justify-content: center;
    }
}
</style>

<?php
get_footer();