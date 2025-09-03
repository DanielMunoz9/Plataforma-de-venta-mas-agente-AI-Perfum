<?php
/**
 * The template for displaying all single posts
 */

get_header(); ?>

<main class="container mt-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="content-section">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
                        
                        <!-- Post Header -->
                        <header class="post-header mb-4">
                            <h1 class="post-title"><?php the_title(); ?></h1>
                            
                            <div class="post-meta">
                                <div class="meta-item">
                                    <i class="fas fa-calendar me-2"></i>
                                    <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-user me-2"></i>
                                    <?php the_author(); ?>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-folder me-2"></i>
                                    <?php the_category(', '); ?>
                                </div>
                                <?php if (get_comments_number() > 0) : ?>
                                    <div class="meta-item">
                                        <i class="fas fa-comments me-2"></i>
                                        <?php comments_number('0 comentarios', '1 comentario', '% comentarios'); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (has_tag()) : ?>
                                    <div class="meta-item">
                                        <i class="fas fa-tags me-2"></i>
                                        <?php the_tags('', ', ', ''); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </header>

                        <!-- Featured Image -->
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail mb-4">
                                <?php the_post_thumbnail('large', array('class' => 'img-fluid rounded')); ?>
                                <?php if (get_the_post_thumbnail_caption()) : ?>
                                    <figcaption class="thumbnail-caption mt-2 text-muted">
                                        <?php echo get_the_post_thumbnail_caption(); ?>
                                    </figcaption>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Post Content -->
                        <div class="post-content">
                            <?php the_content(); ?>
                            
                            <?php
                            wp_link_pages(array(
                                'before' => '<div class="page-links">' . esc_html__('Pages:', 'vane-france'),
                                'after'  => '</div>',
                            ));
                            ?>
                        </div>

                        <!-- Post Footer -->
                        <footer class="post-footer mt-4 pt-4 border-top">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php if (has_tag()) : ?>
                                        <div class="post-tags">
                                            <strong>Etiquetas:</strong>
                                            <?php the_tags('<span class="tag">', '</span> <span class="tag">', '</span>'); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div class="share-buttons">
                                        <strong>Compartir:</strong>
                                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" 
                                           target="_blank" 
                                           class="share-btn facebook">
                                            <i class="fab fa-facebook"></i>
                                        </a>
                                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" 
                                           target="_blank" 
                                           class="share-btn twitter">
                                            <i class="fab fa-twitter"></i>
                                        </a>
                                        <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' - ' . get_permalink()); ?>" 
                                           target="_blank" 
                                           class="share-btn whatsapp">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </footer>

                    </article>

                    <!-- Author Bio -->
                    <?php if (get_the_author_meta('description')) : ?>
                        <div class="author-bio mt-4 p-4 bg-light rounded">
                            <div class="d-flex">
                                <div class="author-avatar me-3">
                                    <?php echo get_avatar(get_the_author_meta('ID'), 80, '', '', array('class' => 'rounded-circle')); ?>
                                </div>
                                <div class="author-info">
                                    <h4 class="author-name">Sobre <?php the_author(); ?></h4>
                                    <p class="author-description"><?php the_author_meta('description'); ?></p>
                                    <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" class="btn btn-outline-primary btn-sm">
                                        Ver más posts de <?php the_author(); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Navigation -->
                    <nav class="post-navigation mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $prev_post = get_previous_post();
                                if ($prev_post) :
                                ?>
                                    <div class="nav-previous">
                                        <a href="<?php echo get_permalink($prev_post->ID); ?>" class="nav-link">
                                            <i class="fas fa-chevron-left me-2"></i>
                                            <div>
                                                <small class="text-muted">Anterior</small>
                                                <div class="nav-title"><?php echo get_the_title($prev_post->ID); ?></div>
                                            </div>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <?php
                                $next_post = get_next_post();
                                if ($next_post) :
                                ?>
                                    <div class="nav-next text-md-end">
                                        <a href="<?php echo get_permalink($next_post->ID); ?>" class="nav-link">
                                            <div>
                                                <small class="text-muted">Siguiente</small>
                                                <div class="nav-title"><?php echo get_the_title($next_post->ID); ?></div>
                                            </div>
                                            <i class="fas fa-chevron-right ms-2"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </nav>

                    <!-- Related Posts -->
                    <?php
                    $categories = get_the_category();
                    if ($categories) :
                        $category_ids = wp_list_pluck($categories, 'term_id');
                        $related_posts = get_posts(array(
                            'category__in'   => $category_ids,
                            'post__not_in'   => array(get_the_ID()),
                            'posts_per_page' => 3,
                            'post_status'    => 'publish'
                        ));
                        
                        if ($related_posts) :
                    ?>
                        <section class="related-posts mt-5">
                            <h3 class="section-title">Posts Relacionados</h3>
                            <div class="row">
                                <?php foreach ($related_posts as $related_post) : ?>
                                    <div class="col-md-4 mb-4">
                                        <article class="related-post-card">
                                            <?php if (has_post_thumbnail($related_post->ID)) : ?>
                                                <a href="<?php echo get_permalink($related_post->ID); ?>">
                                                    <?php echo get_the_post_thumbnail($related_post->ID, 'medium', array('class' => 'img-fluid rounded')); ?>
                                                </a>
                                            <?php endif; ?>
                                            <h5 class="mt-3">
                                                <a href="<?php echo get_permalink($related_post->ID); ?>">
                                                    <?php echo get_the_title($related_post->ID); ?>
                                                </a>
                                            </h5>
                                            <p class="text-muted small">
                                                <?php echo get_the_excerpt($related_post->ID); ?>
                                            </p>
                                        </article>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php 
                        endif;
                    endif; 
                    ?>

                    <!-- Comments -->
                    <?php
                    if (comments_open() || get_comments_number()) :
                        comments_template();
                    endif;
                    ?>

                <?php endwhile; ?>
            </div>
        </div>

        <div class="col-lg-4">
            <?php get_sidebar(); ?>
        </div>
    </div>
</main>

<style>
/* Single Post Styles */
.single-post {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.post-title {
    color: #002395;
    font-size: 2.5rem;
    line-height: 1.2;
    margin-bottom: 1rem;
}

.post-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #eee;
    color: #666;
}

.meta-item {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
}

.meta-item a {
    color: #666;
    text-decoration: none;
}

.meta-item a:hover {
    color: #ed2939;
}

.post-content {
    line-height: 1.8;
    font-size: 1.1rem;
}

.post-content h2,
.post-content h3,
.post-content h4 {
    color: #002395;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.post-content p {
    margin-bottom: 1.5rem;
}

.post-content blockquote {
    background: rgba(0, 35, 149, 0.05);
    border-left: 4px solid #ed2939;
    padding: 1rem 1.5rem;
    margin: 2rem 0;
    font-style: italic;
}

.post-tags .tag {
    background: rgba(0, 35, 149, 0.1);
    color: #002395;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    text-decoration: none;
    margin-right: 0.5rem;
}

.post-tags .tag:hover {
    background: #ed2939;
    color: white;
}

.share-buttons {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.share-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    color: white;
    text-decoration: none;
    transition: transform 0.3s;
}

.share-btn:hover {
    transform: scale(1.1);
    color: white;
}

.share-btn.facebook { background: #3b5998; }
.share-btn.twitter { background: #1da1f2; }
.share-btn.whatsapp { background: #25d366; }

.author-bio {
    border: 2px solid rgba(0, 35, 149, 0.1);
}

.post-navigation .nav-link {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: rgba(0, 35, 149, 0.05);
    border-radius: 10px;
    color: #002395;
    text-decoration: none;
    transition: all 0.3s;
}

.post-navigation .nav-link:hover {
    background: rgba(237, 41, 57, 0.1);
    color: #ed2939;
    transform: translateX(5px);
}

.post-navigation .nav-next .nav-link:hover {
    transform: translateX(-5px);
}

.nav-title {
    font-weight: 600;
    font-size: 0.9rem;
}

.related-posts {
    background: rgba(0, 35, 149, 0.02);
    border-radius: 15px;
    padding: 2rem;
}

.section-title {
    color: #002395;
    text-align: center;
    margin-bottom: 2rem;
    position: relative;
}

.section-title::after {
    content: '';
    display: block;
    width: 60px;
    height: 3px;
    background: #ed2939;
    margin: 0.5rem auto 0;
}

.related-post-card {
    background: white;
    border-radius: 10px;
    padding: 1rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.related-post-card:hover {
    transform: translateY(-5px);
}

.related-post-card h5 a {
    color: #002395;
    text-decoration: none;
}

.related-post-card h5 a:hover {
    color: #ed2939;
}

/* Responsive */
@media (max-width: 768px) {
    .post-title {
        font-size: 2rem;
    }
    
    .post-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .share-buttons {
        justify-content: center;
        margin-top: 1rem;
    }
    
    .post-navigation .nav-link {
        text-align: center;
        margin-bottom: 1rem;
    }
}
</style>

<?php get_footer(); ?>