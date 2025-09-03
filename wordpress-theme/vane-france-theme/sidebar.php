<?php
/**
 * The sidebar containing the main widget area
 */

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<aside id="secondary" class="widget-area">
    <div class="sidebar">
        <?php dynamic_sidebar('sidebar-1'); ?>
        
        <!-- Default widgets if no widgets are added -->
        <?php if (!is_active_sidebar('sidebar-1')) : ?>
            
            <!-- Search Widget -->
            <div class="widget">
                <h3 class="widget-title">Buscar</h3>
                <?php get_search_form(); ?>
            </div>

            <!-- Recent Posts Widget -->
            <div class="widget">
                <h3 class="widget-title">Publicaciones Recientes</h3>
                <ul class="recent-posts-widget">
                    <?php
                    $recent_posts = wp_get_recent_posts(array(
                        'numberposts' => 5,
                        'post_status' => 'publish'
                    ));
                    
                    foreach ($recent_posts as $post) :
                    ?>
                        <li class="recent-post-item">
                            <a href="<?php echo get_permalink($post['ID']); ?>" class="recent-post-link">
                                <?php echo $post['post_title']; ?>
                            </a>
                            <small class="recent-post-date"><?php echo get_the_date('', $post['ID']); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Categories Widget -->
            <div class="widget">
                <h3 class="widget-title">Categorías</h3>
                <ul class="categories-widget">
                    <?php
                    $categories = get_categories(array(
                        'orderby' => 'name',
                        'order'   => 'ASC'
                    ));
                    
                    foreach ($categories as $category) :
                    ?>
                        <li class="category-item">
                            <a href="<?php echo get_category_link($category->term_id); ?>" class="category-link">
                                <?php echo $category->name; ?>
                                <span class="post-count">(<?php echo $category->count; ?>)</span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- WooCommerce Product Categories -->
            <?php if (class_exists('WooCommerce')) : ?>
                <div class="widget">
                    <h3 class="widget-title">Categorías de Productos</h3>
                    <ul class="product-categories-widget">
                        <?php
                        $product_categories = get_terms(array(
                            'taxonomy' => 'product_cat',
                            'hide_empty' => true,
                        ));
                        
                        foreach ($product_categories as $category) :
                        ?>
                            <li class="product-category-item">
                                <a href="<?php echo get_term_link($category); ?>" class="product-category-link">
                                    <?php echo $category->name; ?>
                                    <span class="product-count">(<?php echo $category->count; ?>)</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Newsletter Signup -->
            <div class="widget newsletter-widget">
                <h3 class="widget-title">Newsletter</h3>
                <p>Suscríbete para recibir ofertas exclusivas y novedades sobre nuestros perfumes franceses.</p>
                <form class="newsletter-form" method="post" action="#">
                    <div class="form-group mb-3">
                        <input type="email" class="form-control" placeholder="Tu correo electrónico" required>
                    </div>
                    <button type="submit" class="btn btn-vf-primary w-100">
                        <i class="fas fa-envelope me-2"></i>Suscribirse
                    </button>
                </form>
            </div>

            <!-- Contact Info -->
            <div class="widget contact-widget">
                <h3 class="widget-title">Contacto</h3>
                <div class="contact-info">
                    <p class="contact-item">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <strong>Direcciones:</strong><br>
                        Cl. 12 #13-99 a 13, 1, Bogotá<br>
                        Cl. 12 #13-69 Local 102, Bogotá
                    </p>
                    <p class="contact-item">
                        <i class="fas fa-phone me-2"></i>
                        <a href="tel:3193605666">319 3605666</a>
                    </p>
                    <p class="contact-item">
                        <i class="fas fa-clock me-2"></i>
                        <strong>Horarios:</strong><br>
                        Lun - Sáb: 9:00 AM - 7:00 PM<br>
                        Dom: Cerrado
                    </p>
                    <div class="social-links mt-3">
                        <a href="#" class="social-link me-2" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link me-2" aria-label="Facebook">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="social-link me-2" aria-label="TikTok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                        <a href="https://wa.me/<?php echo esc_attr(get_option('vf_whatsapp_number', '3193605666')); ?>" class="social-link" aria-label="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tags Cloud -->
            <div class="widget">
                <h3 class="widget-title">Etiquetas</h3>
                <?php
                $tags = get_tags(array(
                    'orderby' => 'count',
                    'order' => 'DESC',
                    'number' => 20
                ));
                
                if ($tags) :
                ?>
                    <div class="tag-cloud">
                        <?php foreach ($tags as $tag) : ?>
                            <a href="<?php echo get_tag_link($tag->term_id); ?>" class="tag-link" style="font-size: <?php echo min(18, 10 + $tag->count); ?>px;">
                                <?php echo $tag->name; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Amélie Chat Widget -->
            <div class="widget amelie-widget">
                <h3 class="widget-title">Asistente Virtual</h3>
                <p>¿Tienes preguntas? Chatea con Amélie, nuestra asistente virtual.</p>
                <?php echo do_shortcode('[amelie_chat]'); ?>
            </div>

        <?php endif; ?>
    </div>
</aside>

<style>
/* Sidebar Widgets Styling */
.widget {
    margin-bottom: 2rem;
}

.recent-posts-widget,
.categories-widget,
.product-categories-widget {
    list-style: none;
    padding: 0;
}

.recent-post-item,
.category-item,
.product-category-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
}

.recent-post-item:last-child,
.category-item:last-child,
.product-category-item:last-child {
    border-bottom: none;
}

.recent-post-link,
.category-link,
.product-category-link {
    color: #002395;
    text-decoration: none;
    transition: color 0.3s;
}

.recent-post-link:hover,
.category-link:hover,
.product-category-link:hover {
    color: #ed2939;
}

.recent-post-date {
    color: #666;
    font-size: 0.8rem;
    display: block;
    margin-top: 0.25rem;
}

.post-count,
.product-count {
    color: #666;
    font-size: 0.9rem;
}

.newsletter-widget {
    background: linear-gradient(45deg, rgba(0, 35, 149, 0.1), rgba(237, 41, 57, 0.1));
    border: 2px solid #ed2939;
    border-radius: 15px;
    padding: 1.5rem;
}

.contact-widget {
    background: rgba(0, 35, 149, 0.05);
    border-radius: 15px;
    padding: 1.5rem;
}

.contact-item {
    margin-bottom: 1rem;
    line-height: 1.6;
}

.contact-item a {
    color: #ed2939;
    text-decoration: none;
}

.contact-item a:hover {
    color: #002395;
}

.social-links {
    text-align: center;
}

.social-link {
    color: #002395;
    font-size: 1.5rem;
    transition: color 0.3s, transform 0.3s;
}

.social-link:hover {
    color: #ed2939;
    transform: scale(1.2);
}

.tag-cloud {
    text-align: left;
}

.tag-link {
    display: inline-block;
    background: rgba(0, 35, 149, 0.1);
    color: #002395;
    padding: 0.25rem 0.5rem;
    margin: 0.25rem;
    border-radius: 15px;
    text-decoration: none;
    transition: all 0.3s;
}

.tag-link:hover {
    background: #ed2939;
    color: white;
}

.amelie-widget {
    background: linear-gradient(45deg, rgba(237, 41, 57, 0.1), rgba(0, 35, 149, 0.1));
    border: 2px solid #002395;
    border-radius: 15px;
    padding: 1.5rem;
    text-align: center;
}

/* Search Form Styling */
.search-form {
    display: flex;
    gap: 0.5rem;
}

.search-form input[type="search"] {
    flex: 1;
    border: 1px solid #ddd;
    border-radius: 25px;
    padding: 0.5rem 1rem;
}

.search-form button {
    background: #ed2939;
    color: white;
    border: none;
    border-radius: 25px;
    padding: 0.5rem 1rem;
    cursor: pointer;
    transition: background 0.3s;
}

.search-form button:hover {
    background: #002395;
}
</style>