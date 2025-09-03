<?php
/**
 * The template for displaying archive pages
 *
 * @package VaneFrance
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="blog-layout">
        <div class="blog-posts">
            <?php if (have_posts()) : ?>
                
                <header class="page-header archive-header">
                    <?php if (is_category()) : ?>
                        <h1 class="page-title">
                            <?php printf(esc_html__('Categoría: %s', 'vane-france'), single_cat_title('', false)); ?>
                        </h1>
                        <?php
                        $category_description = category_description();
                        if (!empty($category_description)) :
                        ?>
                            <div class="archive-description"><?php echo $category_description; ?></div>
                        <?php endif; ?>
                        
                    <?php elseif (is_tag()) : ?>
                        <h1 class="page-title">
                            <?php printf(esc_html__('Etiqueta: %s', 'vane-france'), single_tag_title('', false)); ?>
                        </h1>
                        <?php
                        $tag_description = tag_description();
                        if (!empty($tag_description)) :
                        ?>
                            <div class="archive-description"><?php echo $tag_description; ?></div>
                        <?php endif; ?>
                        
                    <?php elseif (is_author()) : ?>
                        <h1 class="page-title">
                            <?php printf(esc_html__('Autor: %s', 'vane-france'), get_the_author()); ?>
                        </h1>
                        <?php
                        $author_description = get_the_author_meta('description');
                        if (!empty($author_description)) :
                        ?>
                            <div class="archive-description author-description">
                                <div class="author-avatar">
                                    <?php echo get_avatar(get_the_author_meta('user_email'), 80); ?>
                                </div>
                                <div class="author-info">
                                    <?php echo $author_description; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                    <?php elseif (is_date()) : ?>
                        <h1 class="page-title">
                            <?php
                            if (is_year()) {
                                printf(esc_html__('Año: %s', 'vane-france'), get_the_date('Y'));
                            } elseif (is_month()) {
                                printf(esc_html__('Mes: %s', 'vane-france'), get_the_date('F Y'));
                            } elseif (is_day()) {
                                printf(esc_html__('Día: %s', 'vane-france'), get_the_date());
                            }
                            ?>
                        </h1>
                        
                    <?php else : ?>
                        <?php the_archive_title('<h1 class="page-title">', '</h1>'); ?>
                        <?php the_archive_description('<div class="archive-description">', '</div>'); ?>
                    <?php endif; ?>
                    
                    <div class="archive-meta">
                        <span class="posts-count">
                            <?php
                            global $wp_query;
                            $total_posts = $wp_query->found_posts;
                            printf(
                                _n(
                                    '%d entrada encontrada',
                                    '%d entradas encontradas',
                                    $total_posts,
                                    'vane-france'
                                ),
                                $total_posts
                            );
                            ?>
                        </span>
                    </div>
                </header>

                <div class="archive-posts">
                    <?php while (have_posts()) : the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-card archive-post-card'); ?>>
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
                                    
                                    <?php if (!is_author()) : ?>
                                        <span class="meta-separator">•</span>
                                        <span class="post-author">
                                            <?php esc_html_e('Por', 'vane-france'); ?> 
                                            <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                                                <?php the_author(); ?>
                                            </a>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if (!is_category() && has_category()) : ?>
                                        <span class="meta-separator">•</span>
                                        <span class="post-categories">
                                            <?php the_category(', '); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if (get_comments_number() > 0) : ?>
                                        <span class="meta-separator">•</span>
                                        <span class="post-comments">
                                            <a href="<?php echo esc_url(get_comments_link()); ?>">
                                                <?php
                                                printf(
                                                    _n(
                                                        '%d comentario',
                                                        '%d comentarios',
                                                        get_comments_number(),
                                                        'vane-france'
                                                    ),
                                                    get_comments_number()
                                                );
                                                ?>
                                            </a>
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
                                    
                                    <?php if (!is_tag() && has_tag()) : ?>
                                        <div class="post-tags">
                                            <?php
                                            $tags = get_the_tags();
                                            if ($tags && count($tags) <= 3) {
                                                the_tags('', ' ');
                                            }
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <nav class="pagination-nav" aria-label="<?php esc_attr_e('Navegación de archivo', 'vane-france'); ?>">
                    <?php vane_france_pagination(); ?>
                </nav>

            <?php else : ?>
                
                <section class="no-results not-found">
                    <header class="page-header">
                        <h1 class="page-title"><?php esc_html_e('No se encontraron entradas', 'vane-france'); ?></h1>
                    </header>
                    
                    <div class="page-content">
                        <?php if (is_category()) : ?>
                            <p><?php esc_html_e('No hay entradas en esta categoría aún.', 'vane-france'); ?></p>
                        <?php elseif (is_tag()) : ?>
                            <p><?php esc_html_e('No hay entradas con esta etiqueta aún.', 'vane-france'); ?></p>
                        <?php elseif (is_author()) : ?>
                            <p><?php esc_html_e('Este autor aún no ha publicado ninguna entrada.', 'vane-france'); ?></p>
                        <?php else : ?>
                            <p><?php esc_html_e('No se encontraron entradas para este archivo.', 'vane-france'); ?></p>
                        <?php endif; ?>
                        
                        <div class="archive-suggestions">
                            <h3><?php esc_html_e('¿Qué te gustaría hacer?', 'vane-france'); ?></h3>
                            <ul>
                                <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Ir al inicio', 'vane-france'); ?></a></li>
                                <li><a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>"><?php esc_html_e('Ver todas las entradas', 'vane-france'); ?></a></li>
                                <?php if (class_exists('WooCommerce')) : ?>
                                    <li><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php esc_html_e('Ver productos', 'vane-france'); ?></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        
                        <?php get_search_form(); ?>
                    </div>
                </section>

            <?php endif; ?>
        </div>

        <?php get_sidebar(); ?>
    </div>
</main>

<style>
/* Archive Page Styles */
.archive-header {
    background: var(--vf-white);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: var(--vf-shadow-light);
    margin-bottom: 2rem;
}

.archive-header .page-title {
    color: var(--vf-navy);
    font-family: var(--font-primary);
    margin-bottom: 1rem;
    font-size: 2.2rem;
}

.archive-description {
    color: var(--vf-dark-gray);
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.archive-description.author-description {
    display: flex;
    gap: 1.5rem;
    align-items: center;
}

.archive-description .author-avatar img {
    border-radius: 50%;
}

.archive-meta {
    padding-top: 1rem;
    border-top: 1px solid #eee;
    font-size: 0.9rem;
    color: var(--vf-red);
    font-weight: 500;
}

.archive-posts {
    display: grid;
    gap: 2rem;
}

.archive-post-card {
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 1.5rem;
    align-items: start;
}

.archive-post-card .post-thumbnail {
    width: 100%;
}

.archive-post-card .post-thumbnail img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
}

.archive-post-card .post-content {
    padding: 0;
}

.archive-post-card .post-title {
    font-size: 1.3rem;
    margin-bottom: 0.8rem;
}

.archive-post-card .post-excerpt {
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.archive-post-card .post-footer {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.archive-suggestions {
    background: var(--vf-light-gray);
    padding: 2rem;
    border-radius: 8px;
    margin: 2rem 0;
}

.archive-suggestions h3 {
    margin-bottom: 1rem;
    color: var(--vf-navy);
}

.archive-suggestions ul {
    list-style: none;
    padding: 0;
    margin-bottom: 1.5rem;
}

.archive-suggestions li {
    margin-bottom: 0.5rem;
}

.archive-suggestions a {
    color: var(--vf-red);
    text-decoration: none;
    font-weight: 500;
}

.archive-suggestions a:hover {
    color: var(--vf-navy);
    text-decoration: underline;
}

/* Category and Tag specific styles */
.category .archive-header,
.tag .archive-header {
    position: relative;
}

.category .archive-header::before,
.tag .archive-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--vf-red);
    border-radius: 2px 0 0 2px;
}

/* Author archive specific styles */
.author .archive-header {
    text-align: center;
}

.author .archive-description {
    text-align: left;
    max-width: 600px;
    margin: 1rem auto;
}

/* Date archive specific styles */
.date .archive-header {
    background: var(--vf-gradient-nav);
    color: var(--vf-white);
}

.date .archive-header .page-title {
    color: var(--vf-white);
    text-shadow: none;
}

.date .archive-meta {
    border-top-color: rgba(255, 255, 255, 0.3);
    color: var(--vf-white);
}

/* Responsive Design */
@media (max-width: 768px) {
    .archive-post-card {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .archive-post-card .post-thumbnail img {
        height: 200px;
    }
    
    .archive-header {
        padding: 1.5rem;
    }
    
    .archive-header .page-title {
        font-size: 1.8rem;
    }
    
    .archive-description.author-description {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
}

@media (max-width: 480px) {
    .archive-header .page-title {
        font-size: 1.5rem;
    }
    
    .archive-posts {
        gap: 1.5rem;
    }
}
</style>

<?php
get_footer();