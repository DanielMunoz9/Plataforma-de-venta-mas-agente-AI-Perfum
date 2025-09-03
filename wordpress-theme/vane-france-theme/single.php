<?php
/**
 * The template for displaying all single posts
 *
 * @package VaneFrance
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="blog-layout">
        <div class="blog-posts">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
                    <header class="entry-header">
                        <div class="entry-meta">
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
                        
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                        
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="entry-thumbnail">
                                <?php the_post_thumbnail('vf-featured'); ?>
                            </div>
                        <?php endif; ?>
                    </header>

                    <div class="entry-content">
                        <?php
                        the_content(sprintf(
                            wp_kses(
                                __('Continuar leyendo<span class="screen-reader-text"> "%s"</span>', 'vane-france'),
                                array(
                                    'span' => array(
                                        'class' => array(),
                                    ),
                                )
                            ),
                            get_the_title()
                        ));

                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . esc_html__('Páginas:', 'vane-france'),
                            'after'  => '</div>',
                        ));
                        ?>
                    </div>

                    <footer class="entry-footer">
                        <?php if (has_tag()) : ?>
                            <div class="entry-tags">
                                <span class="tags-label"><?php esc_html_e('Etiquetas:', 'vane-france'); ?></span>
                                <?php the_tags('', ' '); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="entry-share">
                            <span class="share-label"><?php esc_html_e('Compartir:', 'vane-france'); ?></span>
                            <div class="share-buttons">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" 
                                   target="_blank" rel="noopener" class="share-btn facebook">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    Facebook
                                </a>
                                
                                <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title()); ?>&url=<?php echo urlencode(get_permalink()); ?>" 
                                   target="_blank" rel="noopener" class="share-btn twitter">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                    </svg>
                                    Twitter
                                </a>
                                
                                <a href="https://api.whatsapp.com/send?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" 
                                   target="_blank" rel="noopener" class="share-btn whatsapp">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                    </svg>
                                    WhatsApp
                                </a>
                            </div>
                        </div>
                    </footer>
                </article>

                <!-- Author Bio -->
                <?php if (get_the_author_meta('description')) : ?>
                    <div class="author-bio">
                        <div class="author-avatar">
                            <?php echo get_avatar(get_the_author_meta('user_email'), 80); ?>
                        </div>
                        <div class="author-info">
                            <h3 class="author-name">
                                <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                                    <?php the_author(); ?>
                                </a>
                            </h3>
                            <div class="author-description">
                                <?php the_author_meta('description'); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Post Navigation -->
                <nav class="post-navigation" aria-label="<?php esc_attr_e('Navegación de entradas', 'vane-france'); ?>">
                    <div class="nav-links">
                        <?php
                        $prev_post = get_previous_post();
                        $next_post = get_next_post();
                        
                        if ($prev_post) :
                        ?>
                            <div class="nav-previous">
                                <a href="<?php echo esc_url(get_permalink($prev_post)); ?>" rel="prev">
                                    <span class="nav-subtitle"><?php esc_html_e('Anterior', 'vane-france'); ?></span>
                                    <span class="nav-title"><?php echo esc_html(get_the_title($prev_post)); ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($next_post) : ?>
                            <div class="nav-next">
                                <a href="<?php echo esc_url(get_permalink($next_post)); ?>" rel="next">
                                    <span class="nav-subtitle"><?php esc_html_e('Siguiente', 'vane-france'); ?></span>
                                    <span class="nav-title"><?php echo esc_html(get_the_title($next_post)); ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </nav>

                <?php
                // If comments are open or we have at least one comment, load up the comment template.
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
                ?>

            <?php endwhile; ?>
        </div>

        <?php get_sidebar(); ?>
    </div>
</main>

<style>
/* Single Post Styles */
.single-post {
    margin-bottom: 3rem;
}

.entry-header {
    margin-bottom: 2rem;
}

.entry-meta {
    font-size: 0.9rem;
    color: var(--vf-red);
    margin-bottom: 1rem;
    font-weight: 500;
}

.entry-meta a {
    color: inherit;
    text-decoration: none;
}

.entry-meta a:hover {
    color: var(--vf-navy);
    text-decoration: underline;
}

.entry-title {
    color: var(--vf-navy);
    font-family: var(--font-primary);
    margin-bottom: 1.5rem;
    line-height: 1.3;
}

.entry-thumbnail {
    margin-bottom: 2rem;
}

.entry-thumbnail img {
    width: 100%;
    height: auto;
    border-radius: 12px;
    box-shadow: var(--vf-shadow-light);
}

.entry-content {
    line-height: 1.8;
    margin-bottom: 2rem;
}

.entry-content h2,
.entry-content h3,
.entry-content h4 {
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.entry-content p {
    margin-bottom: 1.5rem;
}

.entry-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1rem 0;
}

.entry-content blockquote {
    border-left: 4px solid var(--vf-red);
    padding-left: 1.5rem;
    margin: 2rem 0;
    font-style: italic;
    color: var(--vf-navy);
}

.page-links {
    margin-top: 2rem;
    text-align: center;
}

.page-links a {
    display: inline-block;
    padding: 0.5rem 1rem;
    margin: 0 0.25rem;
    background: var(--vf-white);
    color: var(--vf-navy);
    text-decoration: none;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.page-links a:hover {
    background: var(--vf-red);
    color: var(--vf-white);
    border-color: var(--vf-red);
}

.entry-footer {
    border-top: 1px solid #eee;
    padding-top: 2rem;
    margin-top: 2rem;
}

.entry-tags {
    margin-bottom: 1.5rem;
}

.tags-label {
    font-weight: 500;
    margin-right: 0.5rem;
}

.entry-tags a {
    background: var(--vf-light-gray);
    color: var(--vf-navy);
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.85rem;
    margin-right: 0.5rem;
    transition: background 0.3s ease;
}

.entry-tags a:hover {
    background: var(--vf-red);
    color: var(--vf-white);
}

.entry-share {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.share-label {
    font-weight: 500;
}

.share-buttons {
    display: flex;
    gap: 0.5rem;
}

.share-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    text-decoration: none;
    border-radius: 4px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.share-btn.facebook {
    background: #1877f2;
    color: white;
}

.share-btn.twitter {
    background: #1da1f2;
    color: white;
}

.share-btn.whatsapp {
    background: #25d366;
    color: white;
}

.share-btn:hover {
    transform: translateY(-2px);
    text-decoration: none;
    color: white;
}

.author-bio {
    display: flex;
    gap: 1.5rem;
    background: var(--vf-light-gray);
    padding: 2rem;
    border-radius: 12px;
    margin: 3rem 0;
}

.author-avatar img {
    border-radius: 50%;
}

.author-name {
    margin-bottom: 0.5rem;
}

.author-name a {
    color: var(--vf-navy);
    text-decoration: none;
}

.author-name a:hover {
    color: var(--vf-red);
}

.author-description {
    line-height: 1.6;
}

.post-navigation {
    margin: 3rem 0;
}

.nav-links {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.nav-previous,
.nav-next {
    background: var(--vf-white);
    border-radius: 8px;
    box-shadow: var(--vf-shadow-light);
    transition: transform 0.3s ease;
}

.nav-previous:hover,
.nav-next:hover {
    transform: translateY(-3px);
}

.nav-previous a,
.nav-next a {
    display: block;
    padding: 1.5rem;
    text-decoration: none;
    color: var(--vf-dark-gray);
}

.nav-next {
    text-align: right;
}

.nav-subtitle {
    display: block;
    font-size: 0.9rem;
    color: var(--vf-red);
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.nav-title {
    display: block;
    font-weight: 600;
    font-family: var(--font-primary);
    color: var(--vf-navy);
}

/* Responsive Design */
@media (max-width: 768px) {
    .entry-share {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .share-buttons {
        flex-wrap: wrap;
    }
    
    .author-bio {
        flex-direction: column;
        text-align: center;
    }
    
    .nav-links {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .nav-next {
        text-align: left;
    }
}
</style>

<?php
get_footer();