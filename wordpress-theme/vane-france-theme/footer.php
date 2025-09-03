<footer class="vf-footer">
    <div class="vf-container">
        <div class="vf-footer-content">
            <!-- Business Information -->
            <div class="vf-footer-section">
                <h3><?php _e('Nuestras Tiendas', 'vane-france'); ?></h3>
                <div class="vf-business-info">
                    <h4><?php _e('Direcciones:', 'vane-france'); ?></h4>
                    <ul>
                        <li>Cl. 12 #13-99 a 13, 1, Bogotá</li>
                        <li>Cl. 12 #13-69 Local 102, Bogotá</li>
                    </ul>
                    
                    <h4><?php _e('Teléfono:', 'vane-france'); ?></h4>
                    <p><a href="tel:+573193605666">319 3605666</a></p>
                    
                    <h4><?php _e('Horario:', 'vane-france'); ?></h4>
                    <ul>
                        <li><?php _e('Lunes: 9 a.m.–7 p.m.', 'vane-france'); ?></li>
                        <li><?php _e('Martes: 9 a.m.–7 p.m.', 'vane-france'); ?></li>
                        <li><?php _e('Miércoles: 9 a.m.–7 p.m.', 'vane-france'); ?></li>
                        <li><?php _e('Jueves: 9 a.m.–7 p.m.', 'vane-france'); ?></li>
                        <li><?php _e('Viernes: 9 a.m.–7 p.m.', 'vane-france'); ?></li>
                        <li><?php _e('Sábado: 9 a.m.–7 p.m.', 'vane-france'); ?></li>
                        <li><?php _e('Domingo: Cerrado', 'vane-france'); ?></li>
                    </ul>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="vf-footer-section">
                <h3><?php _e('Enlaces Rápidos', 'vane-france'); ?></h3>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Inicio', 'vane-france'); ?></a></li>
                    <?php if (class_exists('WooCommerce')) : ?>
                        <li><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"><?php _e('Tienda', 'vane-france'); ?></a></li>
                    <?php endif; ?>
                    
                    <?php 
                    $emprendedor_page = get_page_by_path('catalogo-emprendedor');
                    if ($emprendedor_page) :
                    ?>
                        <li><a href="<?php echo esc_url(get_permalink($emprendedor_page)); ?>"><?php _e('Plan Emprendedor', 'vane-france'); ?></a></li>
                    <?php endif; ?>
                    
                    <?php 
                    $cliente_page = get_page_by_path('catalogo-cliente');
                    if ($cliente_page) :
                    ?>
                        <li><a href="<?php echo esc_url(get_permalink($cliente_page)); ?>"><?php _e('Cliente', 'vane-france'); ?></a></li>
                    <?php endif; ?>
                    
                    <li><a href="<?php echo esc_url(home_url('/blog')); ?>"><?php _e('Blog', 'vane-france'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/contacto')); ?>"><?php _e('Contacto', 'vane-france'); ?></a></li>
                    
                    <?php if (class_exists('WooCommerce')) : ?>
                        <li><a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"><?php _e('Mi Cuenta', 'vane-france'); ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Social Media & Newsletter -->
            <div class="vf-footer-section">
                <h3><?php _e('Síguenos', 'vane-france'); ?></h3>
                <div class="vf-social-links">
                    <a href="#" target="_blank" rel="noopener" aria-label="Facebook">Facebook</a>
                    <a href="#" target="_blank" rel="noopener" aria-label="Instagram">Instagram</a>
                    <a href="#" target="_blank" rel="noopener" aria-label="Twitter">Twitter</a>
                    <?php
                    $whatsapp_number = get_theme_mod('vf_whatsapp_number', '');
                    if (!empty($whatsapp_number)) :
                        $whatsapp_url = 'https://wa.me/' . $whatsapp_number;
                    ?>
                        <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener" aria-label="WhatsApp">WhatsApp</a>
                    <?php endif; ?>
                </div>
                
                <div class="vf-newsletter">
                    <h4><?php _e('Newsletter', 'vane-france'); ?></h4>
                    <p><?php _e('Suscríbete para recibir ofertas exclusivas y novedades.', 'vane-france'); ?></p>
                    <form class="vf-newsletter-form" method="post" action="#" onsubmit="return false;">
                        <input type="email" placeholder="<?php _e('Tu email', 'vane-france'); ?>" required>
                        <button type="submit"><?php _e('Suscribirse', 'vane-france'); ?></button>
                    </form>
                </div>
            </div>

            <!-- Footer Widget Area -->
            <?php if (is_active_sidebar('footer-1') || is_active_sidebar('footer-2')) : ?>
                <div class="vf-footer-section">
                    <?php if (is_active_sidebar('footer-1')) : ?>
                        <div class="footer-widget-area">
                            <?php dynamic_sidebar('footer-1'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (is_active_sidebar('footer-2')) : ?>
                        <div class="footer-widget-area">
                            <?php dynamic_sidebar('footer-2'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer Bottom -->
        <div class="vf-footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('Todos los derechos reservados.', 'vane-france'); ?></p>
            <p><?php _e('Perfumería Francesa de Excelencia | Vane France', 'vane-france'); ?></p>
            
            <?php
            // Footer menu
            wp_nav_menu(array(
                'theme_location' => 'footer',
                'container'      => false,
                'menu_class'     => 'vf-footer-menu',
                'depth'          => 1,
                'fallback_cb'    => false,
            ));
            ?>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

<style>
/* Footer specific styles */
.vf-footer {
    margin-top: 50px;
}

.vf-business-info h4 {
    color: var(--vf-white);
    margin: 15px 0 8px 0;
    font-size: 1.1rem;
}

.vf-business-info ul {
    margin: 0 0 15px 0;
}

.vf-business-info li {
    margin-bottom: 5px;
}

.vf-social-links {
    display: flex;
    gap: 15px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.vf-social-links a {
    padding: 8px 15px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.vf-social-links a:hover {
    background: var(--vf-red);
    transform: translateY(-2px);
}

.vf-newsletter-form {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.vf-newsletter-form input {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 5px;
    background: rgba(255, 255, 255, 0.9);
    color: var(--vf-dark-gray);
}

.vf-newsletter-form button {
    padding: 10px 20px;
    background: var(--vf-red);
    color: var(--vf-white);
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
    white-space: nowrap;
}

.vf-newsletter-form button:hover {
    background: #c21e2e;
}

.vf-footer-menu {
    display: flex;
    gap: 20px;
    justify-content: center;
    list-style: none;
    margin: 15px 0 0 0;
    padding: 0;
}

.vf-footer-menu a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.3s ease;
}

.vf-footer-menu a:hover {
    color: var(--vf-red);
}

@media (max-width: 768px) {
    .vf-social-links {
        justify-content: center;
    }
    
    .vf-newsletter-form {
        flex-direction: column;
    }
    
    .vf-footer-menu {
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
}
</style>

</body>
</html>