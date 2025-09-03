<?php
/**
 * The template for displaying product content within loops
 *
 * @package VaneFrance
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
    return;
}
?>
<div <?php wc_product_class('woocommerce-product-card', $product); ?>>
    <?php
    /**
     * Hook: woocommerce_before_shop_loop_item.
     */
    do_action('woocommerce_before_shop_loop_item');
    ?>

    <?php
    // Check if product has "emprendedor" tag for special badge
    if (has_term('emprendedor', 'product_tag', $product->get_id())) :
    ?>
        <div class="product-badge especial">
            <?php esc_html_e('Especial', 'vane-france'); ?>
        </div>
    <?php endif; ?>

    <?php
    // Check for sale badge
    if ($product->is_on_sale()) :
    ?>
        <div class="product-badge sale">
            <?php esc_html_e('Oferta', 'vane-france'); ?>
        </div>
    <?php endif; ?>

    <div class="product-image">
        <?php
        /**
         * Hook: woocommerce_before_shop_loop_item_title.
         */
        do_action('woocommerce_before_shop_loop_item_title');
        ?>

        <a href="<?php echo esc_url(get_permalink()); ?>" class="product-link">
            <?php
            if (has_post_thumbnail()) {
                the_post_thumbnail('vf-product', array(
                    'class' => 'product-main-image',
                    'alt' => get_the_title()
                ));
            } else {
                echo '<img src="' . VF_THEME_URL . '/assets/img/product-1.jpg" alt="' . esc_attr(get_the_title()) . '" class="product-main-image">';
            }
            ?>
        </a>

        <?php
        // Quick view button
        if (function_exists('woocommerce_template_loop_add_to_cart')) :
        ?>
            <div class="product-actions">
                <button class="quick-view-btn" data-product-id="<?php echo esc_attr($product->get_id()); ?>" title="<?php esc_attr_e('Vista rápida', 'vane-france'); ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                    </svg>
                </button>
                
                <?php if (!$product->is_type('variable')) : ?>
                    <button class="add-to-wishlist-btn" data-product-id="<?php echo esc_attr($product->get_id()); ?>" title="<?php esc_attr_e('Agregar a favoritos', 'vane-france'); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="product-info">
        <?php
        /**
         * Hook: woocommerce_shop_loop_item_title.
         */
        do_action('woocommerce_shop_loop_item_title');
        ?>

        <h3 class="product-title">
            <a href="<?php echo esc_url(get_permalink()); ?>">
                <?php echo wp_kses_post(get_the_title()); ?>
            </a>
        </h3>

        <?php
        // Product short description
        $short_description = $product->get_short_description();
        if ($short_description) :
        ?>
            <div class="product-excerpt">
                <?php echo wp_kses_post(wp_trim_words($short_description, 15)); ?>
            </div>
        <?php endif; ?>

        <?php
        // Product categories
        $categories = get_the_terms($product->get_id(), 'product_cat');
        if ($categories && !is_wp_error($categories)) :
        ?>
            <div class="product-categories">
                <?php
                $category_names = array();
                foreach ($categories as $category) {
                    if ($category->parent == 0) { // Only show top-level categories
                        $category_names[] = '<a href="' . esc_url(get_term_link($category)) . '">' . esc_html($category->name) . '</a>';
                    }
                }
                echo implode(', ', array_slice($category_names, 0, 2)); // Show max 2 categories
                ?>
            </div>
        <?php endif; ?>

        <?php
        /**
         * Hook: woocommerce_after_shop_loop_item_title.
         */
        do_action('woocommerce_after_shop_loop_item_title');
        ?>

        <div class="product-price-container">
            <?php
            // Product price
            echo $product->get_price_html();
            ?>
        </div>

        <?php
        // Product rating
        if (wc_review_ratings_enabled()) {
            $rating_count = $product->get_rating_count();
            $average = $product->get_average_rating();

            if ($rating_count > 0) :
            ?>
                <div class="product-rating">
                    <div class="star-rating" role="img" aria-label="<?php printf(esc_attr__('Rated %s out of 5', 'woocommerce'), $average); ?>">
                        <span style="width:<?php echo (($average / 5) * 100); ?>%">
                            <strong class="rating"><?php echo esc_html($average); ?></strong> <?php esc_html_e('out of 5', 'woocommerce'); ?>
                        </span>
                    </div>
                    <span class="rating-count">(<?php echo esc_html($rating_count); ?>)</span>
                </div>
            <?php endif; ?>
        <?php } ?>

        <?php
        // Product tags for emprendedor products
        if (has_term('emprendedor', 'product_tag', $product->get_id())) :
            $emprendedor_tags = get_the_terms($product->get_id(), 'product_tag');
            if ($emprendedor_tags && !is_wp_error($emprendedor_tags)) :
        ?>
                <div class="product-tags emprendedor-tags">
                    <?php
                    foreach ($emprendedor_tags as $tag) {
                        if ($tag->slug === 'emprendedor') {
                            echo '<span class="product-tag emprendedor-tag">' . esc_html($tag->name) . '</span>';
                        }
                    }
                    ?>
                </div>
        <?php 
            endif;
        endif; 
        ?>

        <div class="product-footer">
            <?php
            /**
             * Hook: woocommerce_after_shop_loop_item.
             */
            do_action('woocommerce_after_shop_loop_item');
            ?>

            <?php
            // Custom add to cart button
            if ($product->is_purchasable() && $product->is_in_stock()) :
            ?>
                <div class="product-add-to-cart">
                    <?php if ($product->is_type('simple')) : ?>
                        <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
                            <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="single_add_to_cart_button button alt">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M7 18c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12L8.1 13h7.45c.75 0 1.41-.41 1.75-1.03L21.7 4H5.21l-.94-2H1zm16 16c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                                <?php esc_html_e('Agregar al carrito', 'vane-france'); ?>
                            </button>
                        </form>
                    <?php else : ?>
                        <a href="<?php echo esc_url($product->get_permalink()); ?>" class="button product_type_variable">
                            <?php esc_html_e('Ver opciones', 'vane-france'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php elseif (!$product->is_in_stock()) : ?>
                <div class="product-stock-status out-of-stock">
                    <span><?php esc_html_e('Agotado', 'vane-france'); ?></span>
                </div>
            <?php endif; ?>

            <?php
            // Stock level for emprendedor products
            if (has_term('emprendedor', 'product_tag', $product->get_id()) && $product->managing_stock()) :
                $stock_quantity = $product->get_stock_quantity();
                if ($stock_quantity <= 5 && $stock_quantity > 0) :
            ?>
                    <div class="low-stock-notice">
                        <span><?php printf(esc_html__('¡Solo quedan %d unidades!', 'vane-france'), $stock_quantity); ?></span>
                    </div>
            <?php 
                endif;
            endif; 
            ?>
        </div>
    </div>
</div>

<style>
/* Product Card Specific Styles */
.woocommerce-product-card {
    position: relative;
    background: var(--vf-white);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--vf-shadow-light);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.woocommerce-product-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--vf-shadow);
}

.product-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    z-index: 2;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-badge.especial {
    background: var(--vf-navy);
    color: var(--vf-white);
    animation: pulse 2s infinite;
}

.product-badge.sale {
    background: var(--vf-red);
    color: var(--vf-white);
    top: 10px;
    left: 10px;
    right: auto;
}

.product-image {
    position: relative;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-image:hover img {
    transform: scale(1.05);
}

.product-actions {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    gap: 0.5rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.woocommerce-product-card:hover .product-actions {
    opacity: 1;
}

.quick-view-btn,
.add-to-wishlist-btn {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: var(--vf-navy);
}

.quick-view-btn:hover,
.add-to-wishlist-btn:hover {
    background: var(--vf-red);
    color: var(--vf-white);
    transform: scale(1.1);
}

.product-info {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-title {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    line-height: 1.3;
}

.product-title a {
    color: var(--vf-navy);
    text-decoration: none;
    transition: color 0.3s ease;
}

.product-title a:hover {
    color: var(--vf-red);
}

.product-excerpt {
    color: var(--vf-dark-gray);
    font-size: 0.9rem;
    line-height: 1.4;
    margin-bottom: 0.5rem;
}

.product-categories {
    font-size: 0.85rem;
    margin-bottom: 1rem;
}

.product-categories a {
    color: var(--vf-red);
    text-decoration: none;
}

.product-categories a:hover {
    text-decoration: underline;
}

.product-price-container {
    margin-bottom: 1rem;
}

.product-price-container .price {
    font-size: 1.2rem;
    font-weight: 700;
    font-family: var(--font-primary);
    color: var(--vf-red);
}

.product-price-container .price del {
    color: #999;
    font-weight: normal;
    font-size: 0.9em;
}

.product-rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    font-size: 0.85rem;
}

.star-rating {
    position: relative;
    display: inline-block;
    width: 80px;
    height: 16px;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23ddd"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>') repeat-x;
    background-size: 16px 16px;
}

.star-rating span {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23ffc107"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>') repeat-x;
    background-size: 16px 16px;
    overflow: hidden;
}

.rating-count {
    color: #666;
}

.product-tags {
    margin-bottom: 1rem;
}

.product-tag {
    display: inline-block;
    background: var(--vf-light-gray);
    color: var(--vf-navy);
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    margin-right: 0.25rem;
}

.product-tag.emprendedor-tag {
    background: var(--vf-navy);
    color: var(--vf-white);
}

.product-footer {
    margin-top: auto;
}

.product-add-to-cart {
    margin-bottom: 1rem;
}

.single_add_to_cart_button {
    width: 100%;
    background: var(--vf-red);
    color: var(--vf-white);
    border: none;
    padding: 0.8rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    text-decoration: none;
}

.single_add_to_cart_button:hover {
    background: var(--vf-navy);
    color: var(--vf-white);
    text-decoration: none;
    transform: translateY(-1px);
}

.product_type_variable {
    width: 100%;
    background: var(--vf-navy);
    color: var(--vf-white);
    border: none;
    padding: 0.8rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.product_type_variable:hover {
    background: var(--vf-red);
    color: var(--vf-white);
    text-decoration: none;
}

.product-stock-status {
    text-align: center;
    padding: 0.8rem;
    background: #f8f9fa;
    border-radius: 6px;
    color: #666;
    font-weight: 500;
}

.product-stock-status.out-of-stock {
    background: #ffeaea;
    color: #d32f2f;
}

.low-stock-notice {
    background: #fff3cd;
    color: #856404;
    padding: 0.5rem;
    border-radius: 4px;
    text-align: center;
    font-size: 0.85rem;
    font-weight: 500;
}

/* List view styles */
.list-view .woocommerce-product-card {
    flex-direction: row;
    align-items: center;
    padding: 1.5rem;
}

.list-view .product-image {
    flex-shrink: 0;
    width: 150px;
    margin-right: 1.5rem;
}

.list-view .product-image img {
    height: 150px;
}

.list-view .product-info {
    flex: 1;
    padding: 0;
}

.list-view .product-actions {
    position: relative;
    top: auto;
    left: auto;
    transform: none;
    opacity: 1;
    margin-top: 1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .list-view .woocommerce-product-card {
        flex-direction: column;
        text-align: center;
    }
    
    .list-view .product-image {
        width: 100%;
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .product-actions {
        position: relative;
        top: auto;
        left: auto;
        transform: none;
        opacity: 1;
        margin-top: 1rem;
    }
}
</style>