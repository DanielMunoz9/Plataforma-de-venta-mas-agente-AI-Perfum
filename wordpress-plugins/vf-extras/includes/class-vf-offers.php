<?php
/**
 * VF Offers - Custom Post Type for Offers
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class VF_Offers {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'register_post_type'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
        add_shortcode('vf_offers', array($this, 'offers_shortcode'));
    }
    
    /**
     * Register Offers Post Type
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x('Ofertas', 'Post type general name', 'vf-extras'),
            'singular_name'         => _x('Oferta', 'Post type singular name', 'vf-extras'),
            'menu_name'             => _x('Ofertas', 'Admin Menu text', 'vf-extras'),
            'name_admin_bar'        => _x('Oferta', 'Add New on Toolbar', 'vf-extras'),
            'add_new'               => __('Agregar Nueva', 'vf-extras'),
            'add_new_item'          => __('Agregar Nueva Oferta', 'vf-extras'),
            'new_item'              => __('Nueva Oferta', 'vf-extras'),
            'edit_item'             => __('Editar Oferta', 'vf-extras'),
            'view_item'             => __('Ver Oferta', 'vf-extras'),
            'all_items'             => __('Todas las Ofertas', 'vf-extras'),
            'search_items'          => __('Buscar Ofertas', 'vf-extras'),
            'parent_item_colon'     => __('Oferta Padre:', 'vf-extras'),
            'not_found'             => __('No se encontraron ofertas.', 'vf-extras'),
            'not_found_in_trash'    => __('No se encontraron ofertas en la papelera.', 'vf-extras'),
            'featured_image'        => _x('Imagen de la Oferta', 'Overrides the "Featured Image" phrase', 'vf-extras'),
            'set_featured_image'    => _x('Establecer imagen de la oferta', 'Overrides the "Set featured image" phrase', 'vf-extras'),
            'remove_featured_image' => _x('Remover imagen de la oferta', 'Overrides the "Remove featured image" phrase', 'vf-extras'),
            'use_featured_image'    => _x('Usar como imagen de la oferta', 'Overrides the "Use as featured image" phrase', 'vf-extras'),
            'archives'              => _x('Archivo de Ofertas', 'The post type archive label', 'vf-extras'),
            'insert_into_item'      => _x('Insertar en oferta', 'Overrides the "Insert into post"/"Insert into page" phrase', 'vf-extras'),
            'uploaded_to_this_item' => _x('Subido a esta oferta', 'Overrides the "Uploaded to this post"/"Uploaded to this page" phrase', 'vf-extras'),
            'filter_items_list'     => _x('Filtrar lista de ofertas', 'Screen reader text for the filter links heading', 'vf-extras'),
            'items_list_navigation' => _x('Navegación de lista de ofertas', 'Screen reader text for the pagination heading', 'vf-extras'),
            'items_list'            => _x('Lista de ofertas', 'Screen reader text for the items list heading', 'vf-extras'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => false, // We'll add it to our custom menu
            'query_var'          => true,
            'rewrite'            => array('slug' => 'ofertas'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'          => 'dashicons-tag',
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'show_in_rest'       => true,
        );

        register_post_type('vf_offer', $args);
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'vf_offer_details',
            __('Detalles de la Oferta', 'vf-extras'),
            array($this, 'offer_details_meta_box'),
            'vf_offer',
            'normal',
            'high'
        );
    }
    
    /**
     * Offer details meta box
     */
    public function offer_details_meta_box($post) {
        wp_nonce_field('vf_offer_meta_box', 'vf_offer_meta_box_nonce');
        
        $offer_type = get_post_meta($post->ID, '_vf_offer_type', true);
        $discount_value = get_post_meta($post->ID, '_vf_discount_value', true);
        $valid_from = get_post_meta($post->ID, '_vf_valid_from', true);
        $valid_until = get_post_meta($post->ID, '_vf_valid_until', true);
        $target_products = get_post_meta($post->ID, '_vf_target_products', true);
        $minimum_amount = get_post_meta($post->ID, '_vf_minimum_amount', true);
        $max_uses = get_post_meta($post->ID, '_vf_max_uses', true);
        $current_uses = get_post_meta($post->ID, '_vf_current_uses', true) ?: 0;
        $offer_code = get_post_meta($post->ID, '_vf_offer_code', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Tipo de Oferta', 'vf-extras'); ?></th>
                <td>
                    <select name="vf_offer_type" id="vf_offer_type">
                        <option value="percentage" <?php selected($offer_type, 'percentage'); ?>><?php _e('Descuento Porcentual', 'vf-extras'); ?></option>
                        <option value="fixed" <?php selected($offer_type, 'fixed'); ?>><?php _e('Descuento Fijo', 'vf-extras'); ?></option>
                        <option value="bogo" <?php selected($offer_type, 'bogo'); ?>><?php _e('Compra uno, lleva dos', 'vf-extras'); ?></option>
                        <option value="free_shipping" <?php selected($offer_type, 'free_shipping'); ?>><?php _e('Envío Gratis', 'vf-extras'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Valor del Descuento', 'vf-extras'); ?></th>
                <td>
                    <input type="number" name="vf_discount_value" value="<?php echo esc_attr($discount_value); ?>" step="0.01" />
                    <p class="description"><?php _e('Para porcentaje usa números del 1-100, para descuento fijo usa el valor en pesos', 'vf-extras'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Código de la Oferta', 'vf-extras'); ?></th>
                <td>
                    <input type="text" name="vf_offer_code" value="<?php echo esc_attr($offer_code); ?>" />
                    <p class="description"><?php _e('Código único para aplicar la oferta (opcional)', 'vf-extras'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Válida Desde', 'vf-extras'); ?></th>
                <td>
                    <input type="date" name="vf_valid_from" value="<?php echo esc_attr($valid_from); ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Válida Hasta', 'vf-extras'); ?></th>
                <td>
                    <input type="date" name="vf_valid_until" value="<?php echo esc_attr($valid_until); ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Productos Objetivo', 'vf-extras'); ?></th>
                <td>
                    <input type="text" name="vf_target_products" value="<?php echo esc_attr($target_products); ?>" />
                    <p class="description"><?php _e('IDs de productos separados por comas (vacío = todos los productos)', 'vf-extras'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Monto Mínimo', 'vf-extras'); ?></th>
                <td>
                    <input type="number" name="vf_minimum_amount" value="<?php echo esc_attr($minimum_amount); ?>" step="0.01" />
                    <p class="description"><?php _e('Monto mínimo de compra para aplicar la oferta', 'vf-extras'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Usos Máximos', 'vf-extras'); ?></th>
                <td>
                    <input type="number" name="vf_max_uses" value="<?php echo esc_attr($max_uses); ?>" />
                    <p class="description"><?php _e('Número máximo de veces que se puede usar esta oferta (vacío = ilimitado)', 'vf-extras'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Usos Actuales', 'vf-extras'); ?></th>
                <td>
                    <strong><?php echo esc_html($current_uses); ?></strong>
                    <p class="description"><?php _e('Número de veces que se ha usado esta oferta', 'vf-extras'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Save meta boxes
     */
    public function save_meta_boxes($post_id) {
        if (!isset($_POST['vf_offer_meta_box_nonce'])) {
            return;
        }
        
        if (!wp_verify_nonce($_POST['vf_offer_meta_box_nonce'], 'vf_offer_meta_box')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        $fields = array(
            'vf_offer_type',
            'vf_discount_value',
            'vf_offer_code',
            'vf_valid_from',
            'vf_valid_until',
            'vf_target_products',
            'vf_minimum_amount',
            'vf_max_uses'
        );
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
    
    /**
     * Offers shortcode
     */
    public function offers_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 6,
            'columns' => 3,
            'show_expired' => false
        ), $atts, 'vf_offers');

        $args = array(
            'post_type' => 'vf_offer',
            'posts_per_page' => intval($atts['limit']),
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        );
        
        // Filter out expired offers unless specifically requested
        if (!$atts['show_expired']) {
            $args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key' => '_vf_valid_until',
                    'value' => date('Y-m-d'),
                    'compare' => '>=',
                    'type' => 'DATE'
                ),
                array(
                    'key' => '_vf_valid_until',
                    'compare' => 'NOT EXISTS'
                )
            );
        }

        $offers = new WP_Query($args);

        if (!$offers->have_posts()) {
            return '<div class="vf-no-offers">' . __('No hay ofertas disponibles en este momento.', 'vf-extras') . '</div>';
        }

        $columns = intval($atts['columns']);
        $column_class = 12 / $columns;
        
        ob_start();
        ?>
        <div class="vf-offers-container">
            <div class="row">
                <?php while ($offers->have_posts()) : $offers->the_post(); ?>
                    <?php
                    $offer_type = get_post_meta(get_the_ID(), '_vf_offer_type', true);
                    $discount_value = get_post_meta(get_the_ID(), '_vf_discount_value', true);
                    $valid_until = get_post_meta(get_the_ID(), '_vf_valid_until', true);
                    $offer_code = get_post_meta(get_the_ID(), '_vf_offer_code', true);
                    $current_uses = get_post_meta(get_the_ID(), '_vf_current_uses', true) ?: 0;
                    $max_uses = get_post_meta(get_the_ID(), '_vf_max_uses', true);
                    ?>
                    <div class="col-lg-<?php echo $column_class; ?> col-md-6 mb-4">
                        <div class="vf-offer-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="offer-image">
                                    <?php the_post_thumbnail('medium', array('class' => 'img-fluid')); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="offer-content">
                                <div class="offer-badge">
                                    <?php
                                    switch ($offer_type) {
                                        case 'percentage':
                                            echo esc_html($discount_value) . '% OFF';
                                            break;
                                        case 'fixed':
                                            echo '$' . number_format($discount_value) . ' OFF';
                                            break;
                                        case 'bogo':
                                            echo '2x1';
                                            break;
                                        case 'free_shipping':
                                            echo __('ENVÍO GRATIS', 'vf-extras');
                                            break;
                                    }
                                    ?>
                                </div>
                                
                                <h3 class="offer-title"><?php the_title(); ?></h3>
                                
                                <div class="offer-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                                
                                <?php if ($offer_code) : ?>
                                    <div class="offer-code">
                                        <span class="code-label"><?php _e('Código:', 'vf-extras'); ?></span>
                                        <span class="code-value"><?php echo esc_html($offer_code); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="offer-meta">
                                    <?php if ($valid_until) : ?>
                                        <div class="offer-expiry">
                                            <i class="fas fa-calendar-alt"></i>
                                            <?php printf(__('Válida hasta: %s', 'vf-extras'), date_i18n(get_option('date_format'), strtotime($valid_until))); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($max_uses && $max_uses > 0) : ?>
                                        <div class="offer-usage">
                                            <i class="fas fa-users"></i>
                                            <?php printf(__('Usada %d de %d veces', 'vf-extras'), $current_uses, $max_uses); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="offer-actions">
                                    <a href="<?php the_permalink(); ?>" class="btn btn-primary offer-btn">
                                        <?php _e('Ver Oferta', 'vf-extras'); ?>
                                        <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <style>
        .vf-offers-container {
            margin: 2rem 0;
        }

        .vf-offer-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            position: relative;
        }

        .vf-offer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .offer-image {
            position: relative;
            overflow: hidden;
        }

        .offer-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .vf-offer-card:hover .offer-image img {
            transform: scale(1.05);
        }

        .offer-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(45deg, #ed2939, #ff4757);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
            z-index: 10;
            animation: pulse 2s infinite;
        }

        .offer-content {
            padding: 1.5rem;
        }

        .offer-title {
            color: #002395;
            font-size: 1.3rem;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .offer-excerpt {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .offer-code {
            background: rgba(0, 35, 149, 0.1);
            border: 2px dashed #002395;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .code-label {
            color: #666;
            font-size: 0.9rem;
        }

        .code-value {
            color: #002395;
            font-weight: bold;
            font-size: 1.2rem;
            margin-left: 0.5rem;
            letter-spacing: 2px;
        }

        .offer-meta {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1.5rem;
        }

        .offer-meta div {
            margin-bottom: 0.5rem;
        }

        .offer-meta i {
            margin-right: 0.5rem;
            color: #ed2939;
        }

        .offer-btn {
            background: linear-gradient(45deg, #002395, #1a4bb8);
            border: none;
            border-radius: 25px;
            padding: 0.75rem 2rem;
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
        }

        .offer-btn:hover {
            background: linear-gradient(45deg, #ed2939, #ff4757);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(237, 41, 57, 0.3);
        }

        .vf-no-offers {
            text-align: center;
            padding: 3rem;
            background: rgba(0, 35, 149, 0.05);
            border-radius: 15px;
            color: #666;
            font-size: 1.1rem;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @media (max-width: 768px) {
            .offer-code {
                padding: 0.75rem;
            }
            
            .code-value {
                font-size: 1rem;
                display: block;
                margin-left: 0;
                margin-top: 0.25rem;
            }
        }
        </style>

        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
    
    /**
     * Check if offer is valid
     */
    public static function is_offer_valid($offer_id) {
        $valid_from = get_post_meta($offer_id, '_vf_valid_from', true);
        $valid_until = get_post_meta($offer_id, '_vf_valid_until', true);
        $max_uses = get_post_meta($offer_id, '_vf_max_uses', true);
        $current_uses = get_post_meta($offer_id, '_vf_current_uses', true) ?: 0;
        
        $now = date('Y-m-d');
        
        // Check date validity
        if ($valid_from && $now < $valid_from) {
            return false;
        }
        
        if ($valid_until && $now > $valid_until) {
            return false;
        }
        
        // Check usage limit
        if ($max_uses && $current_uses >= $max_uses) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Increment offer usage
     */
    public static function increment_usage($offer_id) {
        $current_uses = get_post_meta($offer_id, '_vf_current_uses', true) ?: 0;
        update_post_meta($offer_id, '_vf_current_uses', $current_uses + 1);
    }
}