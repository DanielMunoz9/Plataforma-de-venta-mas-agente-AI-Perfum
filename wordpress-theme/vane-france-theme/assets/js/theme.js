/**
 * Vane France Theme JavaScript
 * 
 * @package VaneFrance
 * @version 1.0.0
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ============ SCROLL ANIMATIONS ============
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('vf-in-view');
            }
        });
    }, observerOptions);
    
    // Observe all elements with animation class
    document.querySelectorAll('.vf-animate-on-scroll').forEach(el => {
        observer.observe(el);
    });
    
    // ============ MOBILE MENU ============
    const mobileToggle = document.getElementById('mobile-menu-toggle');
    const navMenu = document.querySelector('.vf-nav-menu');
    
    if (mobileToggle && navMenu) {
        mobileToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            navMenu.classList.toggle('mobile-active');
            document.body.classList.toggle('menu-open');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!mobileToggle.contains(e.target) && !navMenu.contains(e.target)) {
                mobileToggle.classList.remove('active');
                navMenu.classList.remove('mobile-active');
                document.body.classList.remove('menu-open');
            }
        });
    }
    
    // ============ SMOOTH SCROLLING ============
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
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
    
    // ============ HEADER SCROLL EFFECT ============
    const header = document.querySelector('.vf-header');
    let lastScrollTop = 0;
    
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        // Hide header on scroll down, show on scroll up
        if (scrollTop > lastScrollTop && scrollTop > 200) {
            header.classList.add('header-hidden');
        } else {
            header.classList.remove('header-hidden');
        }
        
        lastScrollTop = scrollTop;
    });
    
    // ============ PRODUCT QUICK VIEW ============
    document.querySelectorAll('.vf-quick-view').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            if (productId) {
                openQuickView(productId);
            }
        });
    });
    
    function openQuickView(productId) {
        // Create modal
        const modal = document.createElement('div');
        modal.className = 'vf-quick-view-modal';
        modal.innerHTML = `
            <div class="vf-modal-backdrop"></div>
            <div class="vf-modal-content">
                <button class="vf-modal-close">&times;</button>
                <div class="vf-modal-body">
                    <div class="vf-loading">Cargando...</div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        document.body.classList.add('modal-open');
        
        // Close modal functionality
        const closeModal = () => {
            modal.remove();
            document.body.classList.remove('modal-open');
        };
        
        modal.querySelector('.vf-modal-close').addEventListener('click', closeModal);
        modal.querySelector('.vf-modal-backdrop').addEventListener('click', closeModal);
        
        // ESC key to close
        document.addEventListener('keydown', function escHandler(e) {
            if (e.key === 'Escape') {
                closeModal();
                document.removeEventListener('keydown', escHandler);
            }
        });
        
        // Load product content (simplified - would need AJAX implementation)
        setTimeout(() => {
            modal.querySelector('.vf-modal-body').innerHTML = `
                <h3>Vista Rápida del Producto</h3>
                <p>Contenido del producto ${productId}</p>
                <button class="vf-btn vf-btn-primary">Añadir al Carrito</button>
            `;
        }, 500);
    }
    
    // ============ WISHLIST FUNCTIONALITY ============
    document.querySelectorAll('.vf-wishlist-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const icon = this.querySelector('.wishlist-icon');
            
            // Toggle wishlist state
            if (icon.textContent === '♡') {
                icon.textContent = '♥';
                this.classList.add('in-wishlist');
                showNotification('Producto añadido a favoritos', 'success');
            } else {
                icon.textContent = '♡';
                this.classList.remove('in-wishlist');
                showNotification('Producto eliminado de favoritos', 'info');
            }
        });
    });
    
    // ============ NOTIFICATION SYSTEM ============
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `vf-notification vf-notification-${type}`;
        notification.textContent = message;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Remove after delay
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    // ============ SEARCH ENHANCEMENT ============
    const searchForm = document.querySelector('.vf-search-form');
    if (searchForm) {
        const searchInput = searchForm.querySelector('.search-field');
        
        // Add search suggestions (basic implementation)
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                if (query.length > 2) {
                    // Here you would implement search suggestions
                    console.log('Searching for:', query);
                }
            });
        }
    }
    
    // ============ NEWSLETTER FORM ============
    document.querySelectorAll('.vf-newsletter-form, .vf-newsletter-signup').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            if (validateEmail(email)) {
                // Simulate subscription
                showNotification('¡Gracias por suscribirte! Te enviaremos ofertas exclusivas.', 'success');
                this.reset();
            } else {
                showNotification('Por favor ingresa un email válido.', 'error');
            }
        });
    });
    
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    // ============ CART UPDATES ============
    if (typeof wc_add_to_cart_params !== 'undefined') {
        // Update cart count on AJAX add to cart
        document.addEventListener('added_to_cart', function() {
            updateCartCount();
        });
    }
    
    function updateCartCount() {
        // This would update the cart count in the header
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            // Fetch updated count via AJAX or update from WooCommerce data
            console.log('Cart updated');
        }
    }
    
    // ============ LAZY LOADING IMAGES ============
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // ============ BACK TO TOP BUTTON ============
    const backToTop = document.createElement('button');
    backToTop.className = 'vf-back-to-top';
    backToTop.innerHTML = '↑';
    backToTop.title = 'Volver arriba';
    document.body.appendChild(backToTop);
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTop.classList.add('show');
        } else {
            backToTop.classList.remove('show');
        }
    });
    
    backToTop.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // ============ PRODUCT IMAGE ZOOM ============
    document.querySelectorAll('.woocommerce-product-gallery__image img').forEach(img => {
        img.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
        });
        
        img.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // ============ FORM VALIDATION ============
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('error');
                    isValid = false;
                } else {
                    field.classList.remove('error');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Por favor completa todos los campos requeridos.', 'error');
            }
        });
    });
    
    // Remove error class on input
    document.querySelectorAll('input, textarea, select').forEach(field => {
        field.addEventListener('input', function() {
            this.classList.remove('error');
        });
    });
    
});

// ============ THEME UTILITIES ============
window.VaneFrance = {
    showNotification: function(message, type = 'info') {
        // Use the notification function defined above
        if (typeof showNotification === 'function') {
            showNotification(message, type);
        }
    },
    
    scrollToElement: function(selector) {
        const element = document.querySelector(selector);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    },
    
    toggleClass: function(selector, className) {
        const element = document.querySelector(selector);
        if (element) {
            element.classList.toggle(className);
        }
    }
};