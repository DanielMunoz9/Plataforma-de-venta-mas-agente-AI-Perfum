<?php
/**
 * Template Name: Catálogo Emprendedor
 * 
 * @package VaneFrance
 */

get_header(); ?>

<main class="vf-container">
    <div class="vf-content-area">
        <header class="vf-catalog-header">
            <h1><?php _e('Plan Emprendedor', 'vane-france'); ?></h1>
            <p><?php _e('Productos especiales con descuentos exclusivos para emprendedores', 'vane-france'); ?></p>
        </header>

        <div class="vf-catalog-content">
            <?php
            while (have_posts()) :
                the_post();
                the_content();
            endwhile;
            ?>
            
            <!-- Custom Emprendedor Products Display -->
            <div class="vf-emprendedor-products">
                <?php echo do_shortcode('[vf_catalog type="emprendedor" limit="12"]'); ?>
            </div>
            
            <!-- Emprendedor Benefits -->
            <section class="vf-emprendedor-benefits">
                <h2><?php _e('Beneficios del Plan Emprendedor', 'vane-france'); ?></h2>
                <div class="vf-benefits-grid">
                    <div class="vf-benefit-card">
                        <div class="benefit-icon">💼</div>
                        <h3><?php _e('Descuentos Especiales', 'vane-france'); ?></h3>
                        <p><?php _e('Hasta 30% de descuento en productos seleccionados para emprendedores.', 'vane-france'); ?></p>
                    </div>
                    <div class="vf-benefit-card">
                        <div class="benefit-icon">📦</div>
                        <h3><?php _e('Compra por Mayor', 'vane-france'); ?></h3>
                        <p><?php _e('Acceso a cantidades mayoristas con precios preferenciales.', 'vane-france'); ?></p>
                    </div>
                    <div class="vf-benefit-card">
                        <div class="benefit-icon">🚚</div>
                        <h3><?php _e('Envío Gratuito', 'vane-france'); ?></h3>
                        <p><?php _e('Envío gratis en pedidos superiores a $150.000 COP.', 'vane-france'); ?></p>
                    </div>
                    <div class="vf-benefit-card">
                        <div class="benefit-icon">🎯</div>
                        <h3><?php _e('Asesoría Personalizada', 'vane-france'); ?></h3>
                        <p><?php _e('Soporte exclusivo para hacer crecer tu negocio.', 'vane-france'); ?></p>
                    </div>
                </div>
            </section>
            
            <!-- Call to Action -->
            <section class="vf-cta-section">
                <div class="vf-cta-content">
                    <h2><?php _e('¿Listo para comenzar?', 'vane-france'); ?></h2>
                    <p><?php _e('Únete a nuestro programa de emprendedores y comienza a construir tu negocio hoy mismo.', 'vane-france'); ?></p>
                    <?php
                    $whatsapp_number = get_theme_mod('vf_whatsapp_number', '');
                    if (!empty($whatsapp_number)) :
                        $whatsapp_message = __('Hola, estoy interesado en el Plan Emprendedor de Vane France. ¿Podrían darme más información?', 'vane-france');
                        $whatsapp_url = 'https://wa.me/' . $whatsapp_number . '?text=' . urlencode($whatsapp_message);
                    ?>
                        <a href="<?php echo esc_url($whatsapp_url); ?>" class="vf-btn vf-btn-primary" target="_blank">
                            <?php _e('Contactar por WhatsApp', 'vane-france'); ?>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (class_exists('WooCommerce')) : ?>
                        <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="vf-btn vf-btn-secondary">
                            <?php _e('Crear Cuenta', 'vane-france'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</main>

<style>
/* Emprendedor catalog specific styles */
.vf-catalog-header {
    text-align: center;
    margin-bottom: 50px;
    padding: 50px 0;
    background: linear-gradient(45deg, rgba(0, 35, 149, 0.1), rgba(237, 41, 57, 0.1));
    border-radius: 15px;
}

.vf-catalog-header h1 {
    font-size: 3rem;
    margin-bottom: 20px;
    color: var(--vf-navy);
}

.vf-catalog-header p {
    font-size: 1.3rem;
    color: #666;
    max-width: 600px;
    margin: 0 auto;
}

.vf-emprendedor-products {
    margin: 50px 0;
}

.vf-emprendedor-benefits {
    margin: 60px 0;
    padding: 50px 0;
    background: rgba(0, 35, 149, 0.05);
    border-radius: 15px;
}

.vf-emprendedor-benefits h2 {
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 50px;
    color: var(--vf-navy);
}

.vf-benefits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    padding: 0 30px;
}

.vf-benefit-card {
    background: var(--vf-white);
    padding: 40px 30px;
    border-radius: 15px;
    text-align: center;
    box-shadow: var(--vf-shadow);
    transition: all 0.3s ease;
}

.vf-benefit-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--vf-shadow-hover);
}

.benefit-icon {
    font-size: 3rem;
    margin-bottom: 20px;
}

.vf-benefit-card h3 {
    color: var(--vf-navy);
    font-size: 1.4rem;
    margin-bottom: 15px;
}

.vf-benefit-card p {
    color: #555;
    line-height: 1.6;
}

.vf-cta-section {
    text-align: center;
    padding: 60px 30px;
    background: linear-gradient(135deg, var(--vf-navy), var(--vf-red));
    color: var(--vf-white);
    border-radius: 15px;
    margin: 50px 0;
}

.vf-cta-content h2 {
    color: var(--vf-white);
    font-size: 2.5rem;
    margin-bottom: 20px;
}

.vf-cta-content p {
    font-size: 1.2rem;
    margin-bottom: 40px;
    opacity: 0.9;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.vf-cta-section .vf-btn {
    margin: 0 10px 10px;
    border: 2px solid var(--vf-white);
}

.vf-cta-section .vf-btn-primary {
    background: var(--vf-white);
    color: var(--vf-navy);
}

.vf-cta-section .vf-btn-primary:hover {
    background: transparent;
    color: var(--vf-white);
}

.vf-cta-section .vf-btn-secondary {
    background: transparent;
    color: var(--vf-white);
}

.vf-cta-section .vf-btn-secondary:hover {
    background: var(--vf-white);
    color: var(--vf-navy);
}

@media (max-width: 768px) {
    .vf-catalog-header h1 {
        font-size: 2.5rem;
    }
    
    .vf-catalog-header p {
        font-size: 1.1rem;
    }
    
    .vf-benefits-grid {
        grid-template-columns: 1fr;
        padding: 0 15px;
    }
    
    .vf-benefit-card {
        padding: 30px 20px;
    }
    
    .vf-cta-content h2 {
        font-size: 2rem;
    }
    
    .vf-cta-content p {
        font-size: 1.1rem;
    }
    
    .vf-cta-section .vf-btn {
        display: block;
        width: 100%;
        max-width: 300px;
        margin: 10px auto;
    }
}
</style>

<?php get_footer(); ?>