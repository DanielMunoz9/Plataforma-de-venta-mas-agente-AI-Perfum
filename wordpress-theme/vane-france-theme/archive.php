<?php
/**
 * The template for displaying archive pages
 */

get_header(); ?>

<main class="container mt-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="content-section">
                <!-- Archive Header -->
                <header class="archive-header mb-4">
                    <?php if (is_category()) : ?>
                        <h1 class="archive-title">
                            <i class="fas fa-folder me-2"></i>
                            Categoría: <?php single_cat_title(); ?>
                        </h1>
                        <?php if (category_description()) : ?>
                            <div class="archive-description">
                                <?php echo category_description(); ?>
                            </div>
                        <?php endif; ?>
                    <?php elseif (is_tag()) : ?>
                        <h1 class="archive-title">
                            <i class="fas fa-tag me-2"></i>
                            Etiqueta: <?php single_tag_title(); ?>
                        </h1>
                        <?php if (tag_description()) : ?>
                            <div class="archive-description">
                                <?php echo tag_description(); ?>
                            </div>
                        <?php endif; ?>
                    <?php elseif (is_author()) : ?>
                        <h1 class="archive-title">
                            <i class="fas fa-user me-2"></i>
                            Autor: <?php echo get_the_author(); ?>
                        </h1>
                        <?php if (get_the_author_meta('description')) : ?>
                            <div class="archive-description">
                                <?php echo get_the_author_meta('description'); ?>
                            </div>
                        <?php endif; ?>
                    <?php elseif (is_date()) : ?>
                        <h1 class="archive-title">
                            <i class="fas fa-calendar me-2"></i>
                            Archivo: 
                            <?php
                            if (is_month()) {
                                echo get_the_date('F Y');
                            } elseif (is_year()) {
                                echo get_the_date('Y');
                            } else {
                                echo get_the_date();
                            }
                            ?>
                        </h1>
                    <?php else : ?>
                        <h1 class="archive-title">
                            <i class="fas fa-archive me-2"></i>
                            Archivo
                        </h1>
                    <?php endif; ?>
                    
                    <!-- Post count -->
                    <div class="archive-meta">
                        <?php
                        global $wp_query;
                        $total_posts = $wp_query->found_posts;
                        $posts_per_page = get_option('posts_per_page');
                        $current_page = max(1, get_query_var('paged'));
                        $start_post = ($current_page - 1) * $posts_per_page + 1;
                        $end_post = min($current_page * $posts_per_page, $total_posts);
                        
                        if ($total_posts > 0) :
                        ?>
                            <p class="results-count">
                                Mostrando <?php echo $start_post; ?>-<?php echo $end_post; ?> de <?php echo $total_posts; ?> publicaciones
                            </p>
                        <?php endif; ?>
                    </div>
                </header>

                <!-- Archive Content -->
                <?php if (have_posts()) : ?>
                    <div class="archive-posts">
                        <?php while (have_posts()) : the_post(); ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class('blog-post archive-post'); ?>>
                                <div class="row">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="col-md-4">
                                            <div class="post-thumbnail">
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php the_post_thumbnail('medium', array('class' => 'img-fluid rounded')); ?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                    <?php else : ?>
                                        <div class="col-12">
                                    <?php endif; ?>
                                        <div class="post-content">
                                            <h2 class="post-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h2>
                                            
                                            <div class="post-meta">
                                                <span class="meta-item">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                                                </span>
                                                <span class="meta-item">
                                                    <i class="fas fa-user me-1"></i>
                                                    <?php the_author(); ?>
                                                </span>
                                                <span class="meta-item">
                                                    <i class="fas fa-folder me-1"></i>
                                                    <?php the_category(', '); ?>
                                                </span>
                                                <?php if (get_comments_number() > 0) : ?>
                                                    <span class="meta-item">
                                                        <i class="fas fa-comments me-1"></i>
                                                        <?php comments_number('0', '1', '%'); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="post-excerpt">
                                                <?php the_excerpt(); ?>
                                            </div>
                                            
                                            <div class="post-actions">
                                                <a href="<?php the_permalink(); ?>" class="read-more-btn">
                                                    Leer más <i class="fas fa-arrow-right ms-1"></i>
                                                </a>
                                                
                                                <?php if (has_tag()) : ?>
                                                    <div class="post-tags mt-2">
                                                        <?php the_tags('<span class="tag">', '</span> <span class="tag">', '</span>'); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endwhile; ?>
                        
                        <!-- Pagination -->
                        <nav class="archive-pagination mt-5">
                            <?php
                            echo paginate_links(array(
                                'mid_size' => 2,
                                'prev_text' => '<i class="fas fa-chevron-left"></i> Anterior',
                                'next_text' => 'Siguiente <i class="fas fa-chevron-right"></i>',
                                'type' => 'list',
                                'class' => 'pagination-vf'
                            ));
                            ?>
                        </nav>
                    </div>
                <?php else : ?>
                    <div class="no-posts text-center py-5">
                        <div class="no-posts-icon mb-3">
                            <i class="fas fa-search fa-4x text-muted"></i>
                        </div>
                        <h2>No se encontraron publicaciones</h2>
                        <p>No hay contenido disponible para esta categoría o búsqueda.</p>
                        
                        <!-- Suggestions -->
                        <div class="suggestions mt-4">
                            <h4>Te sugerimos:</h4>
                            <ul class="list-unstyled">
                                <li><a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-outline-primary me-2 mb-2">Ir al inicio</a></li>
                                <li><a href="<?php echo esc_url(get_permalink(get_page_by_title('Catálogo Emprendedor'))); ?>" class="btn btn-outline-secondary me-2 mb-2">Ver productos para emprendedores</a></li>
                                <li><a href="<?php echo esc_url(get_permalink(get_page_by_title('Catálogo Cliente'))); ?>" class="btn btn-outline-secondary me-2 mb-2">Ver catálogo de clientes</a></li>
                            </ul>
                        </div>
                        
                        <!-- Search form -->
                        <div class="search-suggestion mt-4">
                            <h5>¿Buscas algo específico?</h5>
                            <?php get_search_form(); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-lg-4">
            <?php get_sidebar(); ?>
        </div>
    </div>
</main>

<style>
/* Archive Page Styles */
.archive-header {
    background: linear-gradient(45deg, rgba(0, 35, 149, 0.05), rgba(237, 41, 57, 0.05));
    border-radius: 15px;
    padding: 2rem;
    border: 2px solid rgba(0, 35, 149, 0.1);
    text-align: center;
    margin-bottom: 2rem;
}

.archive-title {
    color: #002395;
    font-size: 2.5rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
}

.archive-description {
    color: #666;
    font-size: 1.1rem;
    line-height: 1.6;
    max-width: 600px;
    margin: 0 auto;
}

.archive-meta {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(0, 35, 149, 0.1);
}

.results-count {
    color: #666;
    font-size: 0.9rem;
    margin: 0;
    font-style: italic;
}

.archive-post {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}

.archive-post:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.archive-post .post-title {
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.archive-post .post-title a {
    color: #002395;
    text-decoration: none;
    transition: color 0.3s;
}

.archive-post .post-title a:hover {
    color: #ed2939;
}

.post-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
    color: #666;
    font-size: 0.9rem;
}

.meta-item {
    display: flex;
    align-items: center;
}

.meta-item a {
    color: #666;
    text-decoration: none;
}

.meta-item a:hover {
    color: #ed2939;
}

.post-excerpt {
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.read-more-btn {
    background: linear-gradient(45deg, #ed2939, #ff4757);
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
}

.read-more-btn:hover {
    background: linear-gradient(45deg, #002395, #1a4bb8);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(237, 41, 57, 0.3);
}

.post-tags .tag {
    background: rgba(0, 35, 149, 0.1);
    color: #002395;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    text-decoration: none;
    margin-right: 0.5rem;
    display: inline-block;
    transition: all 0.3s;
}

.post-tags .tag:hover {
    background: #ed2939;
    color: white;
    transform: translateY(-1px);
}

/* Pagination Styles */
.archive-pagination {
    text-align: center;
}

.archive-pagination ul {
    display: inline-flex;
    list-style: none;
    padding: 0;
    margin: 0;
    background: white;
    border-radius: 25px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.archive-pagination li {
    margin: 0;
}

.archive-pagination a,
.archive-pagination span {
    display: block;
    padding: 0.75rem 1rem;
    color: #002395;
    text-decoration: none;
    transition: all 0.3s;
    border-right: 1px solid rgba(0, 35, 149, 0.1);
}

.archive-pagination li:last-child a,
.archive-pagination li:last-child span {
    border-right: none;
}

.archive-pagination a:hover {
    background: #ed2939;
    color: white;
}

.archive-pagination .current {
    background: #002395;
    color: white;
    font-weight: bold;
}

/* No Posts Styles */
.no-posts {
    background: white;
    border-radius: 15px;
    padding: 3rem 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.no-posts-icon {
    opacity: 0.3;
}

.suggestions ul {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.5rem;
}

.search-suggestion {
    max-width: 400px;
    margin: 0 auto;
}

.search-suggestion .search-form {
    display: flex;
    margin-top: 1rem;
}

.search-suggestion input[type="search"] {
    flex: 1;
    border: 2px solid #ddd;
    border-radius: 25px 0 0 25px;
    padding: 0.75rem 1rem;
    border-right: none;
}

.search-suggestion button {
    background: #ed2939;
    color: white;
    border: 2px solid #ed2939;
    border-radius: 0 25px 25px 0;
    padding: 0.75rem 1.5rem;
    cursor: pointer;
    transition: background 0.3s;
}

.search-suggestion button:hover {
    background: #002395;
    border-color: #002395;
}

/* Responsive */
@media (max-width: 768px) {
    .archive-title {
        font-size: 2rem;
        flex-direction: column;
        text-align: center;
    }
    
    .post-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .archive-pagination ul {
        flex-wrap: wrap;
        border-radius: 15px;
    }
    
    .archive-pagination a,
    .archive-pagination span {
        border-right: none;
        border-bottom: 1px solid rgba(0, 35, 149, 0.1);
    }
    
    .suggestions ul {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<?php get_footer(); ?>