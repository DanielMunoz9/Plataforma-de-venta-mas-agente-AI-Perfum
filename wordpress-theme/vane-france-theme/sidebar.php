<?php
/**
 * The sidebar containing the main widget area
 *
 * @package VaneFrance
 */

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<aside id="secondary" class="sidebar widget-area" role="complementary">
    <?php if (is_active_sidebar('sidebar-1')) : ?>
        <?php dynamic_sidebar('sidebar-1'); ?>
    <?php else : ?>
        
        <!-- Default sidebar content when no widgets are added -->
        <div class="widget widget_search">
            <h3 class="widget-title"><?php esc_html_e('Buscar', 'vane-france'); ?></h3>
            <?php get_search_form(); ?>
        </div>
        
        <?php if (class_exists('WooCommerce')) : ?>
            <div class="widget widget_product_categories">
                <h3 class="widget-title"><?php esc_html_e('Categorías de Productos', 'vane-france'); ?></h3>
                <?php
                $product_categories = get_terms(array(
                    'taxonomy' => 'product_cat',
                    'hide_empty' => false,
                    'parent' => 0,
                    'number' => 5
                ));
                
                if (!empty($product_categories) && !is_wp_error($product_categories)) :
                ?>
                    <ul class="product-categories-list">
                        <?php foreach ($product_categories as $category) : ?>
                            <li>
                                <a href="<?php echo esc_url(get_term_link($category)); ?>">
                                    <?php echo esc_html($category->name); ?>
                                    <span class="category-count">(<?php echo $category->count; ?>)</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="widget-footer">
                        <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="view-all-link">
                            <?php esc_html_e('Ver todos los productos', 'vane-france'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php
        // Recent posts widget
        $recent_posts = new WP_Query(array(
            'post_type' => 'post',
            'posts_per_page' => 5,
            'post_status' => 'publish'
        ));
        
        if ($recent_posts->have_posts()) :
        ?>
            <div class="widget widget_recent_entries">
                <h3 class="widget-title"><?php esc_html_e('Entradas Recientes', 'vane-france'); ?></h3>
                <ul class="recent-posts-list">
                    <?php while ($recent_posts->have_posts()) : $recent_posts->the_post(); ?>
                        <li class="recent-post-item">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="recent-post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail(array(60, 60)); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="recent-post-content">
                                <h4 class="recent-post-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h4>
                                <div class="recent-post-meta">
                                    <time datetime="<?php echo get_the_date('c'); ?>">
                                        <?php echo get_the_date(); ?>
                                    </time>
                                </div>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
                <div class="widget-footer">
                    <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="view-all-link">
                        <?php esc_html_e('Ver todas las entradas', 'vane-france'); ?>
                    </a>
                </div>
            </div>
            <?php wp_reset_postdata(); ?>
        <?php endif; ?>
        
        <!-- Categories widget -->
        <?php
        $categories = get_categories(array(
            'orderby' => 'count',
            'order' => 'DESC',
            'number' => 8,
            'hide_empty' => true
        ));
        
        if (!empty($categories)) :
        ?>
            <div class="widget widget_categories">
                <h3 class="widget-title"><?php esc_html_e('Categorías', 'vane-france'); ?></h3>
                <ul class="categories-list">
                    <?php foreach ($categories as $category) : ?>
                        <li class="category-item">
                            <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>">
                                <?php echo esc_html($category->name); ?>
                                <span class="category-count">(<?php echo $category->count; ?>)</span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <!-- Tags cloud widget -->
        <?php
        $tags = get_tags(array(
            'orderby' => 'count',
            'order' => 'DESC',
            'number' => 20,
            'hide_empty' => true
        ));
        
        if (!empty($tags)) :
        ?>
            <div class="widget widget_tag_cloud">
                <h3 class="widget-title"><?php esc_html_e('Etiquetas Populares', 'vane-france'); ?></h3>
                <div class="tag-cloud">
                    <?php foreach ($tags as $tag) : ?>
                        <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" 
                           class="tag-link" 
                           style="font-size: <?php echo min(16, 10 + ($tag->count * 2)); ?>px;">
                            <?php echo esc_html($tag->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Contact info widget -->
        <div class="widget widget_contact_info">
            <h3 class="widget-title"><?php esc_html_e('Información de Contacto', 'vane-france'); ?></h3>
            <div class="contact-info">
                <div class="contact-item">
                    <strong><?php esc_html_e('Direcciones:', 'vane-france'); ?></strong>
                    <p>Cl. 12 #13-99 a 13, 1, Bogotá</p>
                    <p>Cl. 12 #13-69 Local 102, Bogotá</p>
                </div>
                
                <div class="contact-item">
                    <strong><?php esc_html_e('Teléfono:', 'vane-france'); ?></strong>
                    <p><a href="tel:+573193605666">319 3605666</a></p>
                </div>
                
                <div class="contact-item">
                    <strong><?php esc_html_e('Horario:', 'vane-france'); ?></strong>
                    <p>Lunes a Sábado: 9 a.m.–7 p.m.</p>
                    <p>Domingo: Cerrado</p>
                </div>
            </div>
            
            <?php $whatsapp_number = get_option('vf_whatsapp_number'); ?>
            <?php if ($whatsapp_number) : ?>
                <div class="whatsapp-contact">
                    <a href="https://wa.me/<?php echo esc_attr($whatsapp_number); ?>" 
                       target="_blank" 
                       rel="noopener"
                       class="whatsapp-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                        </svg>
                        <?php esc_html_e('Contáctanos por WhatsApp', 'vane-france'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Newsletter signup widget -->
        <div class="widget widget_newsletter">
            <h3 class="widget-title"><?php esc_html_e('Newsletter', 'vane-france'); ?></h3>
            <p><?php esc_html_e('Suscríbete para recibir ofertas exclusivas y noticias sobre nuevos productos.', 'vane-france'); ?></p>
            <form class="newsletter-form" action="#" method="post">
                <div class="form-group">
                    <input type="email" 
                           name="newsletter_email" 
                           placeholder="<?php esc_attr_e('Tu email', 'vane-france'); ?>" 
                           required
                           class="newsletter-input">
                </div>
                <button type="submit" class="newsletter-btn">
                    <?php esc_html_e('Suscribirse', 'vane-france'); ?>
                </button>
                <input type="hidden" name="action" value="vf_newsletter_signup">
                <?php wp_nonce_field('vf_newsletter', 'vf_newsletter_nonce'); ?>
            </form>
        </div>
        
    <?php endif; ?>
</aside>

<style>
/* Sidebar Styles */
.sidebar .widget {
    background: var(--vf-white);
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: var(--vf-shadow-light);
    margin-bottom: 2rem;
}

.sidebar .widget:last-child {
    margin-bottom: 0;
}

.widget-title {
    color: var(--vf-navy);
    font-family: var(--font-primary);
    font-size: 1.2rem;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--vf-red);
    position: relative;
}

.widget-title::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 30px;
    height: 2px;
    background: var(--vf-navy);
}

/* Search widget */
.widget_search .search-form {
    display: flex;
    gap: 0.5rem;
}

.widget_search .search-field {
    flex: 1;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.9rem;
}

.widget_search .search-submit {
    background: var(--vf-red);
    color: var(--vf-white);
    border: none;
    padding: 0.8rem 1.2rem;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    transition: background 0.3s ease;
}

.widget_search .search-submit:hover {
    background: var(--vf-navy);
}

/* Product categories widget */
.product-categories-list,
.categories-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.product-categories-list li,
.categories-list li {
    margin-bottom: 0.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #f0f0f0;
}

.product-categories-list li:last-child,
.categories-list li:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.product-categories-list a,
.categories-list a {
    color: var(--vf-dark-gray);
    text-decoration: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: color 0.3s ease;
}

.product-categories-list a:hover,
.categories-list a:hover {
    color: var(--vf-red);
}

.category-count {
    color: var(--vf-red);
    font-size: 0.85rem;
    font-weight: 500;
}

/* Recent posts widget */
.recent-posts-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.recent-post-item {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f0f0f0;
}

.recent-post-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.recent-post-thumbnail img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
}

.recent-post-content {
    flex: 1;
}

.recent-post-title {
    margin: 0 0 0.25rem 0;
    font-size: 0.9rem;
    line-height: 1.3;
}

.recent-post-title a {
    color: var(--vf-navy);
    text-decoration: none;
    font-weight: 500;
}

.recent-post-title a:hover {
    color: var(--vf-red);
}

.recent-post-meta {
    font-size: 0.8rem;
    color: var(--vf-red);
}

/* Tag cloud widget */
.tag-cloud {
    line-height: 1.8;
}

.tag-link {
    display: inline-block;
    background: var(--vf-light-gray);
    color: var(--vf-navy);
    padding: 0.2rem 0.6rem;
    margin: 0.2rem;
    border-radius: 12px;
    text-decoration: none;
    font-size: 0.85rem;
    transition: all 0.3s ease;
}

.tag-link:hover {
    background: var(--vf-red);
    color: var(--vf-white);
    transform: translateY(-1px);
}

/* Contact info widget */
.contact-info {
    line-height: 1.6;
}

.contact-item {
    margin-bottom: 1.5rem;
}

.contact-item:last-child {
    margin-bottom: 0;
}

.contact-item strong {
    display: block;
    color: var(--vf-navy);
    margin-bottom: 0.5rem;
}

.contact-item p {
    margin: 0.25rem 0;
    color: var(--vf-dark-gray);
}

.contact-item a {
    color: var(--vf-red);
    text-decoration: none;
}

.contact-item a:hover {
    color: var(--vf-navy);
    text-decoration: underline;
}

.whatsapp-contact {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #f0f0f0;
}

.whatsapp-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: #25D366;
    color: white;
    padding: 0.8rem 1.2rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
    width: 100%;
    justify-content: center;
}

.whatsapp-btn:hover {
    background: #1fb854;
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
}

/* Newsletter widget */
.widget_newsletter p {
    margin-bottom: 1rem;
    font-size: 0.9rem;
    line-height: 1.5;
}

.newsletter-form .form-group {
    margin-bottom: 1rem;
}

.newsletter-input {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.9rem;
}

.newsletter-input:focus {
    outline: none;
    border-color: var(--vf-red);
}

.newsletter-btn {
    width: 100%;
    background: var(--vf-red);
    color: var(--vf-white);
    border: none;
    padding: 0.8rem;
    border-radius: 4px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.3s ease;
}

.newsletter-btn:hover {
    background: var(--vf-navy);
}

/* Widget footer */
.widget-footer {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #f0f0f0;
    text-align: center;
}

.view-all-link {
    color: var(--vf-red);
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: color 0.3s ease;
}

.view-all-link:hover {
    color: var(--vf-navy);
    text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 768px) {
    .sidebar {
        margin-top: 2rem;
    }
    
    .sidebar .widget {
        padding: 1.25rem;
    }
    
    .recent-post-item {
        gap: 0.75rem;
    }
    
    .recent-post-thumbnail img {
        width: 50px;
        height: 50px;
    }
}
</style>