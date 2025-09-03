    </div><!-- #content -->

    <footer id="colophon" class="site-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3><?php esc_html_e('Direcciones', 'vane-france'); ?></h3>
                <ul>
                    <li>Cl. 12 #13-99 a 13, 1, Bogotá</li>
                    <li>Cl. 12 #13-69 Local 102, Bogotá</li>
                </ul>
                
                <h3><?php esc_html_e('Teléfono', 'vane-france'); ?></h3>
                <p><a href="tel:+573193605666">319 3605666</a></p>
            </div>
            
            <div class="footer-section">
                <h3><?php esc_html_e('Horario', 'vane-france'); ?></h3>
                <ul>
                    <li>Lunes: 9 a.m.–7 p.m.</li>
                    <li>Martes: 9 a.m.–7 p.m.</li>
                    <li>Miércoles: 9 a.m.–7 p.m.</li>
                    <li>Jueves: 9 a.m.–7 p.m.</li>
                    <li>Viernes: 9 a.m.–7 p.m.</li>
                    <li>Sábado: 9 a.m.–7 p.m.</li>
                    <li>Domingo: Cerrado</li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3><?php esc_html_e('Síguenos', 'vane-france'); ?></h3>
                <ul>
                    <li><a href="#" target="_blank" rel="noopener"><?php esc_html_e('Instagram', 'vane-france'); ?></a></li>
                    <li><a href="#" target="_blank" rel="noopener"><?php esc_html_e('Facebook', 'vane-france'); ?></a></li>
                    <li><a href="#" target="_blank" rel="noopener"><?php esc_html_e('TikTok', 'vane-france'); ?></a></li>
                </ul>
                
                <h3><?php esc_html_e('Newsletter', 'vane-france'); ?></h3>
                <p><?php esc_html_e('Recibe noticias sobre nuevos productos y ofertas exclusivas.', 'vane-france'); ?></p>
                <form class="newsletter-form" action="#" method="post">
                    <input type="email" name="newsletter_email" placeholder="<?php esc_attr_e('Tu email', 'vane-france'); ?>" required>
                    <button type="submit"><?php esc_html_e('Suscribirse', 'vane-france'); ?></button>
                </form>
            </div>
            
            <?php if (is_active_sidebar('footer-1') || is_active_sidebar('footer-2') || is_active_sidebar('footer-3')) : ?>
                <div class="footer-section">
                    <?php
                    if (is_active_sidebar('footer-1')) {
                        dynamic_sidebar('footer-1');
                    } elseif (is_active_sidebar('footer-2')) {
                        dynamic_sidebar('footer-2');
                    } elseif (is_active_sidebar('footer-3')) {
                        dynamic_sidebar('footer-3');
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php esc_html_e('Todos los derechos reservados.', 'vane-france'); ?></p>
            <?php
            wp_nav_menu(array(
                'theme_location' => 'footer',
                'menu_class'     => 'footer-nav',
                'container'      => false,
                'depth'          => 1,
                'fallback_cb'    => false,
            ));
            ?>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>