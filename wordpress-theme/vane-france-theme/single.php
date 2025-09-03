<?php
/**
 * Single Post Template
 * 
 * @package VaneFrance
 */

get_header(); ?>

<main class="vf-container">
    <div class="vf-content-area">
        <div class="vf-blog-layout">
            <div class="vf-blog-main">
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class('vf-single-post'); ?>>
                        <!-- Post Header -->
                        <header class="vf-post-header">
                            <h1 class="vf-post-title"><?php the_title(); ?></h1>
                            
                            <div class="vf-post-meta">
                                <span><i class="icon-calendar"></i> <?php echo get_the_date(); ?></span>
                                <span><i class="icon-user"></i> <?php _e('Por', 'vane-france'); ?> <?php the_author(); ?></span>
                                <?php if (has_category()) : ?>
                                    <span><i class="icon-folder"></i> <?php the_category(', '); ?></span>
                                <?php endif; ?>
                                <?php if (has_tag()) : ?>
                                    <span><i class="icon-tag"></i> <?php the_tags('', ', '); ?></span>
                                <?php endif; ?>
                                <?php if (comments_open() || get_comments_number()) : ?>
                                    <span><i class="icon-comment"></i> <?php comments_number('0 comentarios', '1 comentario', '% comentarios'); ?></span>
                                <?php endif; ?>
                            </div>
                        </header>

                        <!-- Featured Image -->
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="vf-post-featured-image">
                                <?php the_post_thumbnail('vf-featured', array('class' => 'img-fluid')); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Post Content -->
                        <div class="vf-post-content">
                            <?php the_content(); ?>
                            
                            <?php
                            wp_link_pages(array(
                                'before' => '<div class="vf-page-links">' . __('Páginas:', 'vane-france'),
                                'after'  => '</div>',
                                'link_before' => '<span>',
                                'link_after'  => '</span>',
                            ));
                            ?>
                        </div>

                        <!-- Post Footer -->
                        <footer class="vf-post-footer">
                            <?php if (has_tag()) : ?>
                                <div class="vf-post-tags">
                                    <h4><?php _e('Etiquetas:', 'vane-france'); ?></h4>
                                    <?php the_tags('<ul class="vf-tag-list"><li>', '</li><li>', '</li></ul>'); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Social Share -->
                            <div class="vf-social-share">
                                <h4><?php _e('Compartir:', 'vane-france'); ?></h4>
                                <div class="vf-share-buttons">
                                    <?php
                                    $post_url = get_permalink();
                                    $post_title = get_the_title();
                                    ?>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($post_url); ?>" target="_blank" class="vf-share-btn facebook">
                                        Facebook
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($post_url); ?>&text=<?php echo urlencode($post_title); ?>" target="_blank" class="vf-share-btn twitter">
                                        Twitter
                                    </a>
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($post_url); ?>" target="_blank" class="vf-share-btn linkedin">
                                        LinkedIn
                                    </a>
                                    <?php
                                    $whatsapp_number = get_theme_mod('vf_whatsapp_number', '');
                                    if (!empty($whatsapp_number)) :
                                        $whatsapp_text = sprintf(__('Te recomiendo este artículo: %s - %s', 'vane-france'), $post_title, $post_url);
                                    ?>
                                        <a href="https://wa.me/<?php echo $whatsapp_number; ?>?text=<?php echo urlencode($whatsapp_text); ?>" target="_blank" class="vf-share-btn whatsapp">
                                            WhatsApp
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </footer>

                        <!-- Author Bio -->
                        <?php
                        $author_bio = get_the_author_meta('description');
                        if (!empty($author_bio)) :
                        ?>
                            <div class="vf-author-bio">
                                <div class="vf-author-avatar">
                                    <?php echo get_avatar(get_the_author_meta('ID'), 80); ?>
                                </div>
                                <div class="vf-author-info">
                                    <h4><?php _e('Sobre el autor:', 'vane-france'); ?> <?php the_author(); ?></h4>
                                    <p><?php echo $author_bio; ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Post Navigation -->
                        <nav class="vf-post-navigation">
                            <div class="nav-links">
                                <?php
                                $prev_post = get_previous_post();
                                $next_post = get_next_post();
                                ?>
                                
                                <?php if ($prev_post) : ?>
                                    <div class="nav-previous">
                                        <span class="nav-label"><?php _e('Artículo anterior', 'vane-france'); ?></span>
                                        <a href="<?php echo get_permalink($prev_post->ID); ?>" class="nav-title">
                                            <?php echo get_the_title($prev_post->ID); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($next_post) : ?>
                                    <div class="nav-next">
                                        <span class="nav-label"><?php _e('Siguiente artículo', 'vane-france'); ?></span>
                                        <a href="<?php echo get_permalink($next_post->ID); ?>" class="nav-title">
                                            <?php echo get_the_title($next_post->ID); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </nav>
                    </article>

                    <!-- Comments -->
                    <?php
                    if (comments_open() || get_comments_number()) {
                        comments_template();
                    }
                    ?>

                <?php endwhile; ?>
            </div>

            <!-- Sidebar -->
            <aside class="vf-blog-sidebar">
                <?php get_sidebar(); ?>
            </aside>
        </div>
    </div>
</main>

<style>
/* Single post specific styles */
.vf-single-post {
    background: var(--vf-white);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: var(--vf-shadow);
    margin-bottom: 40px;
}

.vf-post-header {
    padding: 40px 40px 20px;
    border-bottom: 1px solid rgba(0, 35, 149, 0.1);
}

.vf-post-header .vf-post-title {
    font-size: 2.5rem;
    margin-bottom: 20px;
    line-height: 1.2;
    color: var(--vf-navy);
}

.vf-post-header .vf-post-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    color: #666;
    font-size: 0.95rem;
}

.vf-post-header .vf-post-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.vf-post-featured-image {
    width: 100%;
    overflow: hidden;
}

.vf-post-featured-image img {
    width: 100%;
    height: auto;
    max-height: 400px;
    object-fit: cover;
}

.vf-post-content {
    padding: 40px;
    line-height: 1.8;
    font-size: 1.1rem;
}

.vf-post-content h2,
.vf-post-content h3,
.vf-post-content h4 {
    margin: 30px 0 15px;
    color: var(--vf-navy);
}

.vf-post-content p {
    margin-bottom: 20px;
}

.vf-post-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 20px 0;
}

.vf-page-links {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.vf-page-links span {
    display: inline-block;
    margin: 0 5px;
    padding: 8px 15px;
    background: var(--vf-navy);
    color: var(--vf-white);
    border-radius: 5px;
    text-decoration: none;
}

.vf-post-footer {
    padding: 30px 40px;
    background: rgba(0, 35, 149, 0.05);
    border-top: 1px solid rgba(0, 35, 149, 0.1);
}

.vf-post-tags {
    margin-bottom: 30px;
}

.vf-post-tags h4 {
    color: var(--vf-navy);
    margin-bottom: 15px;
    font-size: 1.1rem;
}

.vf-tag-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.vf-tag-list li {
    background: var(--vf-navy);
    color: var(--vf-white);
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.9rem;
}

.vf-tag-list a {
    color: inherit;
    text-decoration: none;
}

.vf-social-share h4 {
    color: var(--vf-navy);
    margin-bottom: 15px;
    font-size: 1.1rem;
}

.vf-share-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.vf-share-btn {
    padding: 10px 20px;
    border-radius: 25px;
    text-decoration: none;
    color: var(--vf-white);
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.vf-share-btn.facebook { background: #3b5998; }
.vf-share-btn.twitter { background: #1da1f2; }
.vf-share-btn.linkedin { background: #0077b5; }
.vf-share-btn.whatsapp { background: #25d366; }

.vf-share-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--vf-shadow);
    color: var(--vf-white);
}

.vf-author-bio {
    display: flex;
    gap: 20px;
    padding: 30px 40px;
    background: rgba(237, 41, 57, 0.05);
    border-top: 1px solid rgba(237, 41, 57, 0.1);
}

.vf-author-avatar img {
    border-radius: 50%;
    width: 80px;
    height: 80px;
}

.vf-author-info h4 {
    color: var(--vf-navy);
    margin-bottom: 10px;
    font-size: 1.2rem;
}

.vf-author-info p {
    color: #555;
    line-height: 1.6;
    margin: 0;
}

.vf-post-navigation {
    padding: 40px;
    background: var(--vf-white);
    border-radius: 15px;
    box-shadow: var(--vf-shadow);
    margin-top: 40px;
}

.nav-links {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.nav-previous,
.nav-next {
    padding: 20px;
    background: rgba(0, 35, 149, 0.05);
    border-radius: 10px;
    transition: all 0.3s ease;
}

.nav-previous:hover,
.nav-next:hover {
    background: rgba(0, 35, 149, 0.1);
    transform: translateY(-2px);
}

.nav-next {
    text-align: right;
}

.nav-label {
    display: block;
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.nav-title {
    color: var(--vf-navy);
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    line-height: 1.3;
}

.nav-title:hover {
    color: var(--vf-red);
}

@media (max-width: 768px) {
    .vf-post-header,
    .vf-post-content,
    .vf-post-footer,
    .vf-author-bio,
    .vf-post-navigation {
        padding: 20px;
    }
    
    .vf-post-header .vf-post-title {
        font-size: 2rem;
    }
    
    .vf-post-header .vf-post-meta {
        flex-direction: column;
        gap: 10px;
    }
    
    .vf-author-bio {
        flex-direction: column;
        text-align: center;
    }
    
    .nav-links {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .nav-next {
        text-align: left;
    }
    
    .vf-share-buttons {
        justify-content: center;
    }
}
</style>

<?php get_footer(); ?>