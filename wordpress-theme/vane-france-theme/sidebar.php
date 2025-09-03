<?php
/**
 * Sidebar Template
 * 
 * @package VaneFrance
 */

if (!is_active_sidebar('blog-sidebar')) {
    return;
}
?>

<div class="vf-sidebar-content">
    <!-- Search Widget -->
    <div class="widget vf-search-widget">
        <h3 class="widget-title"><?php _e('Buscar', 'vane-france'); ?></h3>
        <form role="search" method="get" class="vf-search-form" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="search" class="search-field" placeholder="<?php _e('Buscar artículos...', 'vane-france'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
            <button type="submit" class="search-submit">
                <span><?php _e('Buscar', 'vane-france'); ?></span>
            </button>
        </form>
    </div>

    <!-- Categories Widget -->
    <div class="widget vf-categories-widget">
        <h3 class="widget-title"><?php _e('Categorías', 'vane-france'); ?></h3>
        <ul class="vf-categories-list">
            <?php
            wp_list_categories(array(
                'orderby'    => 'count',
                'order'      => 'DESC',
                'show_count' => true,
                'title_li'   => '',
                'number'     => 10,
            ));
            ?>
        </ul>
    </div>

    <!-- Recent Posts Widget -->
    <div class="widget vf-recent-posts-widget">
        <h3 class="widget-title"><?php _e('Artículos Recientes', 'vane-france'); ?></h3>
        <?php
        $recent_posts = wp_get_recent_posts(array(
            'numberposts' => 5,
            'post_status' => 'publish'
        ));
        
        if (!empty($recent_posts)) :
        ?>
            <ul class="vf-recent-posts-list">
                <?php foreach ($recent_posts as $post) : ?>
                    <li>
                        <a href="<?php echo get_permalink($post['ID']); ?>">
                            <?php if (has_post_thumbnail($post['ID'])) : ?>
                                <div class="recent-post-thumb">
                                    <?php echo get_the_post_thumbnail($post['ID'], array(60, 60)); ?>
                                </div>
                            <?php endif; ?>
                            <div class="recent-post-content">
                                <h4><?php echo $post['post_title']; ?></h4>
                                <span class="recent-post-date"><?php echo get_the_date('', $post['ID']); ?></span>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- Popular Tags Widget -->
    <div class="widget vf-tags-widget">
        <h3 class="widget-title"><?php _e('Etiquetas Populares', 'vane-france'); ?></h3>
        <?php
        $tags = get_tags(array(
            'orderby' => 'count',
            'order' => 'DESC',
            'number' => 15
        ));
        
        if (!empty($tags)) :
        ?>
            <div class="vf-tag-cloud">
                <?php foreach ($tags as $tag) : ?>
                    <a href="<?php echo get_tag_link($tag->term_id); ?>" class="vf-tag-link">
                        <?php echo $tag->name; ?>
                        <span class="tag-count">(<?php echo $tag->count; ?>)</span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Newsletter Signup -->
    <div class="widget vf-newsletter-widget">
        <h3 class="widget-title"><?php _e('Newsletter', 'vane-france'); ?></h3>
        <p><?php _e('Suscríbete para recibir las últimas noticias sobre perfumería y tendencias.', 'vane-france'); ?></p>
        <form class="vf-newsletter-signup" method="post" action="#" onsubmit="return false;">
            <input type="email" placeholder="<?php _e('Tu email', 'vane-france'); ?>" required class="newsletter-email">
            <button type="submit" class="newsletter-submit">
                <?php _e('Suscribirse', 'vane-france'); ?>
            </button>
        </form>
        <p class="newsletter-note"><?php _e('*No spam, solo contenido de calidad.', 'vane-france'); ?></p>
    </div>

    <!-- Products Widget (if WooCommerce is active) -->
    <?php if (class_exists('WooCommerce')) : ?>
        <div class="widget vf-products-widget">
            <h3 class="widget-title"><?php _e('Productos Destacados', 'vane-france'); ?></h3>
            <?php
            $featured_products = wc_get_featured_product_ids();
            
            if (!empty($featured_products)) :
                $products_query = new WP_Query(array(
                    'post_type' => 'product',
                    'post__in' => array_slice($featured_products, 0, 3),
                    'posts_per_page' => 3
                ));
                
                if ($products_query->have_posts()) :
            ?>
                <ul class="vf-sidebar-products">
                    <?php while ($products_query->have_posts()) : $products_query->the_post(); ?>
                        <?php global $product; ?>
                        <li class="vf-sidebar-product">
                            <a href="<?php the_permalink(); ?>">
                                <div class="sidebar-product-image">
                                    <?php echo woocommerce_get_product_thumbnail(); ?>
                                </div>
                                <div class="sidebar-product-info">
                                    <h4><?php the_title(); ?></h4>
                                    <span class="price"><?php echo $product->get_price_html(); ?></span>
                                </div>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>
        <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Social Follow Widget -->
    <div class="widget vf-social-widget">
        <h3 class="widget-title"><?php _e('Síguenos', 'vane-france'); ?></h3>
        <div class="vf-social-buttons">
            <a href="#" class="social-btn facebook" target="_blank" rel="noopener">
                <span>Facebook</span>
            </a>
            <a href="#" class="social-btn instagram" target="_blank" rel="noopener">
                <span>Instagram</span>
            </a>
            <a href="#" class="social-btn twitter" target="_blank" rel="noopener">
                <span>Twitter</span>
            </a>
            <?php
            $whatsapp_number = get_theme_mod('vf_whatsapp_number', '');
            if (!empty($whatsapp_number)) :
            ?>
                <a href="https://wa.me/<?php echo $whatsapp_number; ?>" class="social-btn whatsapp" target="_blank" rel="noopener">
                    <span>WhatsApp</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- WordPress Widgets -->
    <?php dynamic_sidebar('blog-sidebar'); ?>
</div>

<style>
/* Sidebar Styles */
.vf-sidebar-content {
    /* Styling handled by main stylesheet */
}

.widget {
    background: var(--vf-white);
    padding: 25px;
    margin-bottom: 30px;
    border-radius: 15px;
    box-shadow: var(--vf-shadow);
}

.widget-title {
    color: var(--vf-navy);
    font-size: 1.3rem;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid rgba(0, 35, 149, 0.1);
    font-weight: 700;
}

/* Search Widget */
.vf-search-form {
    display: flex;
    gap: 5px;
}

.search-field {
    flex: 1;
    padding: 12px 15px;
    border: 2px solid rgba(0, 35, 149, 0.2);
    border-radius: 8px;
    font-size: 1rem;
    background: var(--vf-white);
}

.search-field:focus {
    outline: none;
    border-color: var(--vf-navy);
}

.search-submit {
    padding: 12px 20px;
    background: var(--vf-navy);
    color: var(--vf-white);
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.search-submit:hover {
    background: var(--vf-red);
}

/* Categories List */
.vf-categories-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.vf-categories-list li {
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(0, 35, 149, 0.1);
}

.vf-categories-list li:last-child {
    border-bottom: none;
}

.vf-categories-list a {
    color: var(--vf-dark-gray);
    text-decoration: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: color 0.3s ease;
}

.vf-categories-list a:hover {
    color: var(--vf-navy);
}

/* Recent Posts */
.vf-recent-posts-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.vf-recent-posts-list li {
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(0, 35, 149, 0.1);
}

.vf-recent-posts-list li:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.vf-recent-posts-list a {
    display: flex;
    gap: 12px;
    text-decoration: none;
    color: inherit;
    align-items: flex-start;
}

.recent-post-thumb img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

.recent-post-content h4 {
    color: var(--vf-navy);
    font-size: 0.95rem;
    line-height: 1.3;
    margin-bottom: 5px;
    transition: color 0.3s ease;
}

.vf-recent-posts-list a:hover .recent-post-content h4 {
    color: var(--vf-red);
}

.recent-post-date {
    color: #666;
    font-size: 0.8rem;
}

/* Tag Cloud */
.vf-tag-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.vf-tag-link {
    display: inline-block;
    padding: 6px 12px;
    background: rgba(0, 35, 149, 0.1);
    color: var(--vf-navy);
    text-decoration: none;
    border-radius: 15px;
    font-size: 0.85rem;
    transition: all 0.3s ease;
}

.vf-tag-link:hover {
    background: var(--vf-navy);
    color: var(--vf-white);
}

.tag-count {
    font-size: 0.75rem;
    opacity: 0.7;
}

/* Newsletter Widget */
.vf-newsletter-signup {
    margin: 15px 0;
}

.newsletter-email {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid rgba(0, 35, 149, 0.2);
    border-radius: 8px;
    margin-bottom: 10px;
    font-size: 1rem;
}

.newsletter-email:focus {
    outline: none;
    border-color: var(--vf-navy);
}

.newsletter-submit {
    width: 100%;
    padding: 12px 20px;
    background: var(--vf-red);
    color: var(--vf-white);
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.newsletter-submit:hover {
    background: #c21e2e;
}

.newsletter-note {
    font-size: 0.8rem;
    color: #666;
    margin-top: 10px;
    font-style: italic;
}

/* Sidebar Products */
.vf-sidebar-products {
    list-style: none;
    padding: 0;
    margin: 0;
}

.vf-sidebar-product {
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(0, 35, 149, 0.1);
}

.vf-sidebar-product:last-child {
    border-bottom: none;
}

.vf-sidebar-product a {
    display: flex;
    gap: 12px;
    text-decoration: none;
    color: inherit;
}

.sidebar-product-image {
    flex-shrink: 0;
}

.sidebar-product-image img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

.sidebar-product-info h4 {
    color: var(--vf-navy);
    font-size: 0.95rem;
    margin-bottom: 5px;
    line-height: 1.3;
}

.sidebar-product-info .price {
    color: var(--vf-red);
    font-weight: bold;
}

/* Social Buttons */
.vf-social-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.social-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px 15px;
    border-radius: 8px;
    text-decoration: none;
    color: var(--vf-white);
    font-weight: 600;
    transition: all 0.3s ease;
}

.social-btn.facebook { background: #3b5998; }
.social-btn.instagram { background: #e4405f; }
.social-btn.twitter { background: #1da1f2; }
.social-btn.whatsapp { background: #25d366; }

.social-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--vf-shadow);
    color: var(--vf-white);
}

@media (max-width: 768px) {
    .widget {
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .widget-title {
        font-size: 1.2rem;
    }
    
    .vf-recent-posts-list a,
    .vf-sidebar-product a {
        flex-direction: column;
        text-align: center;
    }
    
    .recent-post-thumb,
    .sidebar-product-image {
        align-self: center;
    }
}
</style>