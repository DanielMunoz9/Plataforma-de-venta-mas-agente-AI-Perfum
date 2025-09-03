<?php
/**
 * WooCommerce Product Content Template
 *
 * @package VaneFrance
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>

<li <?php wc_product_class( 'vf-product-item', $product ); ?>>
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	/**
	 * Hook: woocommerce_before_shop_loop_item_title.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item_title' );
	?>

	<div class="product-info">
		<?php
		/**
		 * Hook: woocommerce_shop_loop_item_title.
		 *
		 * @hooked woocommerce_template_loop_product_title - 10
		 */
		do_action( 'woocommerce_shop_loop_item_title' );

		/**
		 * Hook: woocommerce_after_shop_loop_item_title.
		 *
		 * @hooked woocommerce_template_loop_rating - 5
		 * @hooked woocommerce_template_loop_price - 10
		 */
		do_action( 'woocommerce_after_shop_loop_item_title' );
		?>

		<div class="vf-product-actions">
			<?php
			/**
			 * Hook: woocommerce_after_shop_loop_item.
			 *
			 * @hooked woocommerce_template_loop_product_link_close - 5
			 * @hooked woocommerce_template_loop_add_to_cart - 10
			 */
			do_action( 'woocommerce_after_shop_loop_item' );
			?>
		</div>

		<!-- Product Meta -->
		<div class="vf-product-meta">
			<?php if ( $product->is_on_sale() ) : ?>
				<span class="vf-sale-badge"><?php _e( 'Oferta', 'vane-france' ); ?></span>
			<?php endif; ?>

			<?php if ( ! $product->is_in_stock() ) : ?>
				<span class="vf-stock-badge out-of-stock"><?php _e( 'Agotado', 'vane-france' ); ?></span>
			<?php else : ?>
				<span class="vf-stock-badge in-stock"><?php _e( 'Disponible', 'vane-france' ); ?></span>
			<?php endif; ?>
		</div>

		<!-- Quick View Button -->
		<div class="vf-quick-actions">
			<a href="<?php the_permalink(); ?>" class="vf-quick-view" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
				<?php _e( 'Vista Rápida', 'vane-france' ); ?>
			</a>
			
			<?php if ( class_exists( 'WC_Wishlist' ) ) : ?>
				<button class="vf-wishlist-btn" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
					<span class="wishlist-icon">♡</span>
					<span class="wishlist-text"><?php _e( 'Favoritos', 'vane-france' ); ?></span>
				</button>
			<?php endif; ?>
		</div>

		<!-- Product Rating -->
		<?php if ( wc_review_ratings_enabled() ) : ?>
			<div class="vf-product-rating">
				<?php echo wc_get_rating_html( $product->get_average_rating() ); ?>
				<span class="rating-count">
					(<?php echo $product->get_review_count(); ?> <?php _e( 'reseñas', 'vane-france' ); ?>)
				</span>
			</div>
		<?php endif; ?>

		<!-- Product Short Description -->
		<?php if ( $product->get_short_description() ) : ?>
			<div class="vf-product-excerpt">
				<?php echo wp_trim_words( $product->get_short_description(), 12, '...' ); ?>
			</div>
		<?php endif; ?>
	</div>
</li>

<style>
/* WooCommerce Product Item Styles */
.vf-product-item {
	position: relative;
	transition: all 0.3s ease;
	overflow: hidden;
}

.vf-product-item:hover {
	transform: translateY(-5px);
}

/* Product Actions */
.vf-product-actions {
	margin-top: 15px;
}

.vf-product-actions .button {
	width: 100%;
	background: var(--vf-navy);
	color: var(--vf-white);
	border: none;
	padding: 12px 20px;
	border-radius: 25px;
	font-weight: 600;
	text-transform: uppercase;
	letter-spacing: 1px;
	transition: all 0.3s ease;
	font-size: 0.9rem;
}

.vf-product-actions .button:hover {
	background: var(--vf-red);
	transform: translateY(-2px);
	box-shadow: var(--vf-shadow);
}

.vf-product-actions .added_to_cart {
	background: #28a745;
	margin-top: 10px;
	text-align: center;
	padding: 8px;
	border-radius: 5px;
	color: var(--vf-white);
	text-decoration: none;
	display: block;
	font-size: 0.9rem;
}

/* Product Meta */
.vf-product-meta {
	display: flex;
	gap: 8px;
	margin-top: 10px;
	flex-wrap: wrap;
}

.vf-sale-badge,
.vf-stock-badge {
	font-size: 0.75rem;
	padding: 4px 8px;
	border-radius: 12px;
	font-weight: 600;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

.vf-sale-badge {
	background: var(--vf-red);
	color: var(--vf-white);
}

.vf-stock-badge.in-stock {
	background: #28a745;
	color: var(--vf-white);
}

.vf-stock-badge.out-of-stock {
	background: #dc3545;
	color: var(--vf-white);
}

/* Quick Actions */
.vf-quick-actions {
	position: absolute;
	top: 15px;
	right: 15px;
	display: flex;
	flex-direction: column;
	gap: 8px;
	opacity: 0;
	transform: translateX(20px);
	transition: all 0.3s ease;
}

.vf-product-item:hover .vf-quick-actions {
	opacity: 1;
	transform: translateX(0);
}

.vf-quick-view,
.vf-wishlist-btn {
	background: rgba(255, 255, 255, 0.95);
	border: none;
	padding: 8px 12px;
	border-radius: 20px;
	font-size: 0.8rem;
	font-weight: 600;
	color: var(--vf-navy);
	text-decoration: none;
	cursor: pointer;
	transition: all 0.3s ease;
	white-space: nowrap;
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.vf-quick-view:hover,
.vf-wishlist-btn:hover {
	background: var(--vf-navy);
	color: var(--vf-white);
	transform: scale(1.05);
}

.vf-wishlist-btn {
	display: flex;
	align-items: center;
	gap: 4px;
}

.wishlist-icon {
	font-size: 1rem;
}

/* Product Rating */
.vf-product-rating {
	margin-top: 10px;
	display: flex;
	align-items: center;
	gap: 8px;
	font-size: 0.9rem;
}

.vf-product-rating .star-rating {
	font-size: 0.9rem;
}

.rating-count {
	color: #666;
	font-size: 0.8rem;
}

/* Product Excerpt */
.vf-product-excerpt {
	margin-top: 10px;
	color: #666;
	font-size: 0.9rem;
	line-height: 1.4;
}

/* Sale Flash Override */
.woocommerce .onsale {
	background: var(--vf-red);
	color: var(--vf-white);
	border-radius: 50%;
	width: 50px;
	height: 50px;
	line-height: 46px;
	text-align: center;
	font-weight: 700;
	font-size: 0.8rem;
	top: 10px;
	left: 10px;
	margin: 0;
	position: absolute;
	z-index: 10;
}

/* Price Styling */
.woocommerce .price {
	font-weight: 700;
	font-size: 1.2rem;
}

.woocommerce .price del {
	opacity: 0.6;
	font-size: 0.9rem;
}

.woocommerce .price ins {
	text-decoration: none;
	color: var(--vf-red);
}

/* Responsive Design */
@media (max-width: 768px) {
	.vf-quick-actions {
		position: static;
		flex-direction: row;
		justify-content: center;
		opacity: 1;
		transform: none;
		margin-top: 10px;
	}
	
	.vf-quick-view,
	.vf-wishlist-btn {
		font-size: 0.75rem;
		padding: 6px 10px;
	}
	
	.vf-product-meta {
		justify-content: center;
	}
	
	.vf-product-rating {
		justify-content: center;
	}
}

/* Loading State */
.vf-product-item.loading {
	opacity: 0.6;
	pointer-events: none;
}

.vf-product-item.loading::after {
	content: '';
	position: absolute;
	top: 50%;
	left: 50%;
	width: 20px;
	height: 20px;
	margin: -10px 0 0 -10px;
	border: 2px solid var(--vf-navy);
	border-top: 2px solid transparent;
	border-radius: 50%;
	animation: spin 1s linear infinite;
}

@keyframes spin {
	0% { transform: rotate(0deg); }
	100% { transform: rotate(360deg); }
}
</style>