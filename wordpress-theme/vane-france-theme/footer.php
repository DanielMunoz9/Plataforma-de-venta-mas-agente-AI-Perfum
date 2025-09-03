<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <!-- Business Info Section -->
            <div class="footer-section">
                <h4><i class="fas fa-store me-2"></i>Nuestras Tiendas</h4>
                <div class="mb-3">
                    <p><i class="fas fa-map-marker-alt me-2"></i><strong>Tienda 1:</strong></p>
                    <p>Cl. 12 #13-99 a 13, 1<br>Bogotá, Colombia</p>
                </div>
                <div class="mb-3">
                    <p><i class="fas fa-map-marker-alt me-2"></i><strong>Tienda 2:</strong></p>
                    <p>Cl. 12 #13-69 Local 102<br>Bogotá, Colombia</p>
                </div>
                <p><i class="fas fa-phone me-2"></i><a href="tel:3193605666">319 3605666</a></p>
            </div>

            <!-- Hours Section -->
            <div class="footer-section">
                <h4><i class="fas fa-clock me-2"></i>Horarios de Atención</h4>
                <div class="hours-list">
                    <p><strong>Lunes:</strong> 9:00 AM - 7:00 PM</p>
                    <p><strong>Martes:</strong> 9:00 AM - 7:00 PM</p>
                    <p><strong>Miércoles:</strong> 9:00 AM - 7:00 PM</p>
                    <p><strong>Jueves:</strong> 9:00 AM - 7:00 PM</p>
                    <p><strong>Viernes:</strong> 9:00 AM - 7:00 PM</p>
                    <p><strong>Sábado:</strong> 9:00 AM - 7:00 PM</p>
                    <p><strong>Domingo:</strong> <span style="color: #ed2939;">Cerrado</span></p>
                </div>
            </div>

            <!-- Quick Links Section -->
            <div class="footer-section">
                <h4><i class="fas fa-link me-2"></i>Enlaces Rápidos</h4>
                <div class="footer-links">
                    <p><a href="<?php echo esc_url(home_url('/')); ?>">Inicio</a></p>
                    <p><a href="<?php echo esc_url(get_permalink(get_page_by_title('Catálogo Emprendedor'))); ?>">Plan Emprendedor</a></p>
                    <p><a href="<?php echo esc_url(get_permalink(get_page_by_title('Catálogo Cliente'))); ?>">Catálogo Cliente</a></p>
                    <?php if (get_option('page_for_posts')) : ?>
                        <p><a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>">Blog</a></p>
                    <?php endif; ?>
                    <p><a href="<?php echo esc_url(home_url('/contacto')); ?>">Contacto</a></p>
                    <?php if (class_exists('WooCommerce')) : ?>
                        <p><a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>">Mi Cuenta</a></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Social & Newsletter Section -->
            <div class="footer-section">
                <h4><i class="fas fa-share-alt me-2"></i>Síguenos</h4>
                <div class="social-links mb-3">
                    <a href="#" class="me-3" aria-label="Instagram"><i class="fab fa-instagram fa-2x"></i></a>
                    <a href="#" class="me-3" aria-label="Facebook"><i class="fab fa-facebook fa-2x"></i></a>
                    <a href="#" class="me-3" aria-label="TikTok"><i class="fab fa-tiktok fa-2x"></i></a>
                    <a href="https://wa.me/<?php echo esc_attr(get_option('vf_whatsapp_number', '3193605666')); ?>" class="me-3" aria-label="WhatsApp"><i class="fab fa-whatsapp fa-2x"></i></a>
                </div>

                <!-- Newsletter Signup -->
                <div class="newsletter-form">
                    <h5>Newsletter</h5>
                    <p style="font-size: 0.9rem;">Recibe ofertas exclusivas</p>
                    <form class="newsletter-signup" method="post" action="#">
                        <div class="input-group mb-2">
                            <input type="email" class="form-control" placeholder="Tu email" required>
                            <button class="btn btn-outline-light" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Widget Areas -->
        <?php if (is_active_sidebar('footer-1') || is_active_sidebar('footer-2') || is_active_sidebar('footer-3')) : ?>
            <div class="footer-widgets">
                <div class="row">
                    <div class="col-md-4">
                        <?php dynamic_sidebar('footer-1'); ?>
                    </div>
                    <div class="col-md-4">
                        <?php dynamic_sidebar('footer-2'); ?>
                    </div>
                    <div class="col-md-4">
                        <?php dynamic_sidebar('footer-3'); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p>&copy; <?php echo date('Y'); ?> Vane France. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>Perfumería Francesa de Alta Gama</p>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- WhatsApp Floating Button (only on single product pages) -->
<?php if (is_product() && class_exists('WooCommerce')) : ?>
    <?php $whatsapp_number = get_option('vf_whatsapp_number', '3193605666'); ?>
    <a href="https://wa.me/<?php echo esc_attr($whatsapp_number); ?>?text=¿No%20lo%20encuentras?%20WhatsApp" 
       class="whatsapp-float" 
       target="_blank" 
       aria-label="Contactar por WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
<?php endif; ?>

<?php wp_footer(); ?>

<!-- Custom JavaScript for animations -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe elements for animation
    const animateElements = document.querySelectorAll('.blog-post, .product, .content-section');
    animateElements.forEach(function(el) {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });

    // Newsletter form submission
    const newsletterForm = document.querySelector('.newsletter-signup');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            if (email) {
                alert('¡Gracias por suscribirte! Pronto recibirás nuestras ofertas exclusivas.');
                this.querySelector('input[type="email"]').value = '';
            }
        });
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add loading animation to buttons
    document.querySelectorAll('.btn-vf-primary, .btn-vf-secondary').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Cargando...';
            this.disabled = true;
            
            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;
            }, 1000);
        });
    });
});
</script>

</body>
</html>