/**
 * Vane France Theme Custom JavaScript
 * Enhanced functionality and animations for the Vane France WordPress theme
 */

(function($) {
    'use strict';

    // DOM Ready
    $(document).ready(function() {
        VaneFrance.init();
    });

    // Main VaneFrance object
    window.VaneFrance = {
        
        // Initialize all functions
        init: function() {
            this.setupAnimations();
            this.setupProductViews();
            this.setupWhatsAppIntegration();
            this.setupFormEnhancements();
            this.setupScrollEffects();
            this.setupModalSystem();
            this.setupSearchEnhancements();
            this.setupNewsletterForm();
            this.setupParticleEffect();
            this.setupResponsiveMenu();
            this.setupLazyLoading();
            console.log('Vane France Theme initialized successfully');
        },

        // Animation System
        setupAnimations: function() {
            // Intersection Observer for scroll animations
            if ('IntersectionObserver' in window) {
                const observerOptions = {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                };

                const observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const element = entry.target;
                            element.classList.add('animate-slide-bottom');
                            observer.unobserve(element);
                        }
                    });
                }, observerOptions);

                // Observe elements
                $('.blog-post, .product-card, .offer-card, .content-section').each(function() {
                    observer.observe(this);
                });
            }

            // Hero section animations
            $('.hero-content h1').addClass('animate-slide-left');
            $('.hero-content p').addClass('animate-slide-right animate-delay-1');
            $('.cta-buttons .btn-vf-primary').addClass('animate-slide-bottom animate-delay-2');
            $('.cta-buttons .btn-vf-secondary').addClass('animate-slide-bottom animate-delay-3');
        },

        // Product view tracking
        setupProductViews: function() {
            if ($('body').hasClass('single-product')) {
                // Track product view via AJAX
                const productId = $('body').attr('class').match(/postid-(\d+)/);
                if (productId && vane_france_ajax) {
                    $.post(vane_france_ajax.ajax_url, {
                        action: 'vf_track_product_view',
                        product_id: productId[1],
                        nonce: vane_france_ajax.nonce
                    });
                }
            }
        },

        // WhatsApp Integration
        setupWhatsAppIntegration: function() {
            // Enhanced WhatsApp button with product context
            $('.whatsapp-float').on('click', function(e) {
                const productTitle = $('.product_title').text() || $('.post-title').text() || 'Página web';
                const currentUrl = window.location.href;
                const message = encodeURIComponent(`Hola, estoy interesado en: ${productTitle}. ${currentUrl}`);
                
                const whatsappUrl = $(this).attr('href');
                if (whatsappUrl.indexOf('text=') === -1) {
                    $(this).attr('href', whatsappUrl + '?text=' + message);
                }
            });

            // WhatsApp quick actions
            $('.whatsapp-quick-action').on('click', function(e) {
                e.preventDefault();
                const action = $(this).data('action');
                let message = '';
                
                switch(action) {
                    case 'support':
                        message = 'Hola, necesito soporte técnico.';
                        break;
                    case 'catalog':
                        message = 'Hola, me gustaría ver el catálogo completo.';
                        break;
                    case 'prices':
                        message = 'Hola, me gustaría información sobre precios especiales.';
                        break;
                    default:
                        message = 'Hola, tengo una consulta.';
                }
                
                const whatsappNumber = $(this).data('number') || '3193605666';
                window.open(`https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`, '_blank');
            });
        },

        // Form Enhancements
        setupFormEnhancements: function() {
            // Floating label effects
            $('.vf-form-floating input, .vf-form-floating textarea').on('focus blur', function() {
                $(this).parent().toggleClass('focused', $(this).is(':focus') || $(this).val().length > 0);
            });

            // Real-time validation
            $('input[type="email"]').on('blur', function() {
                const email = $(this).val();
                const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                $(this).toggleClass('is-valid', isValid && email.length > 0);
                $(this).toggleClass('is-invalid', !isValid && email.length > 0);
            });

            // Phone number formatting
            $('input[type="tel"]').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                if (value.length >= 10) {
                    value = value.replace(/(\d{3})(\d{3})(\d{4})/, '$1 $2 $3');
                }
                $(this).val(value);
            });
        },

        // Scroll Effects
        setupScrollEffects: function() {
            // Scroll progress indicator
            $(window).on('scroll', function() {
                const scrollTop = $(window).scrollTop();
                const documentHeight = $(document).height() - $(window).height();
                const scrollPercent = (scrollTop / documentHeight) * 100;
                
                let $indicator = $('.vf-scroll-indicator');
                if ($indicator.length === 0) {
                    $indicator = $('<div class="vf-scroll-indicator"></div>').appendTo('body');
                }
                $indicator.css('width', scrollPercent + '%');
            });

            // Smooth scroll for anchor links
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                const target = $($(this).attr('href'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 800);
                }
            });

            // Parallax effect for hero section
            if ($('.hero-section').length) {
                $(window).on('scroll', function() {
                    const scrolled = $(window).scrollTop();
                    const parallax = scrolled * 0.5;
                    $('.hero-section').css('transform', `translateY(${parallax}px)`);
                });
            }
        },

        // Modal System
        setupModalSystem: function() {
            // Quick view modal for products
            $('.quick-view').on('click', function(e) {
                e.preventDefault();
                const productUrl = $(this).closest('.product-card').find('.product-title a').attr('href');
                VaneFrance.openProductModal(productUrl);
            });

            // Generic modal system
            $('[data-vf-modal]').on('click', function(e) {
                e.preventDefault();
                const modalId = $(this).data('vf-modal');
                VaneFrance.openModal(modalId);
            });

            // Close modal events
            $(document).on('click', '.vf-modal-close, .vf-modal', function(e) {
                if (e.target === this) {
                    VaneFrance.closeModal();
                }
            });

            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    VaneFrance.closeModal();
                }
            });
        },

        // Search Enhancements
        setupSearchEnhancements: function() {
            // Live search functionality
            let searchTimeout;
            $('.search-form input[type="search"]').on('input', function() {
                const query = $(this).val();
                clearTimeout(searchTimeout);
                
                if (query.length >= 3) {
                    searchTimeout = setTimeout(function() {
                        VaneFrance.performLiveSearch(query);
                    }, 300);
                }
            });

            // Search suggestions
            $('.search-form input[type="search"]').on('focus', function() {
                VaneFrance.showSearchSuggestions();
            });
        },

        // Newsletter Form
        setupNewsletterForm: function() {
            $('.newsletter-form, .newsletter-signup').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);
                const $email = $form.find('input[type="email"]');
                const email = $email.val();

                if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    VaneFrance.showNotification('Por favor, ingresa un email válido.', 'error');
                    return;
                }

                // Disable form during submission
                $form.find('button').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Enviando...');

                // Simulate API call (replace with actual newsletter service)
                setTimeout(function() {
                    VaneFrance.showNotification('¡Gracias por suscribirte! Pronto recibirás nuestras ofertas exclusivas.', 'success');
                    $email.val('');
                    $form.find('button').prop('disabled', false).html('<i class="fas fa-envelope me-2"></i>Suscribirse');
                }, 2000);
            });
        },

        // Particle Background Effect
        setupParticleEffect: function() {
            if (window.innerWidth > 768) { // Only on desktop
                this.createParticles();
            }
        },

        createParticles: function() {
            const $hero = $('.hero-section');
            if ($hero.length === 0) return;

            const $particleContainer = $('<div class="vf-particles"></div>').appendTo($hero);
            
            for (let i = 0; i < 20; i++) {
                setTimeout(() => {
                    this.createParticle($particleContainer);
                }, i * 1000);
            }

            // Create new particle every 2 seconds
            setInterval(() => {
                this.createParticle($particleContainer);
            }, 2000);
        },

        createParticle: function($container) {
            const $particle = $('<div class="vf-particle"></div>');
            const size = Math.random() * 10 + 5;
            const left = Math.random() * 100;
            const animationDuration = Math.random() * 10 + 15;

            $particle.css({
                width: size + 'px',
                height: size + 'px',
                left: left + '%',
                animationDuration: animationDuration + 's'
            });

            $container.append($particle);

            // Remove particle after animation
            setTimeout(() => {
                $particle.remove();
            }, animationDuration * 1000);
        },

        // Responsive Menu
        setupResponsiveMenu: function() {
            $('.navbar-toggler').on('click', function() {
                $(this).toggleClass('active');
                $('.navbar-collapse').toggleClass('show');
            });

            // Close menu when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.navbar').length) {
                    $('.navbar-collapse').removeClass('show');
                    $('.navbar-toggler').removeClass('active');
                }
            });
        },

        // Lazy Loading
        setupLazyLoading: function() {
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    });
                });

                $('.lazy').each(function() {
                    imageObserver.observe(this);
                });
            }
        },

        // Utility Functions
        openModal: function(modalId) {
            const $modal = $('#' + modalId);
            if ($modal.length) {
                $modal.fadeIn(300);
                $('body').css('overflow', 'hidden');
            }
        },

        closeModal: function() {
            $('.vf-modal').fadeOut(300);
            $('body').css('overflow', 'auto');
        },

        openProductModal: function(productUrl) {
            // Implementation for product quick view
            console.log('Opening product modal for:', productUrl);
            // This would load product content via AJAX
        },

        performLiveSearch: function(query) {
            // Implementation for live search
            console.log('Performing live search for:', query);
            // This would make AJAX calls to search endpoint
        },

        showSearchSuggestions: function() {
            // Implementation for search suggestions
            console.log('Showing search suggestions');
        },

        showNotification: function(message, type) {
            const $notification = $(`
                <div class="vf-notification vf-notification-${type}">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    ${message}
                    <button class="vf-notification-close">&times;</button>
                </div>
            `);

            $('body').append($notification);
            
            setTimeout(() => {
                $notification.addClass('show');
            }, 100);

            // Auto remove after 5 seconds
            setTimeout(() => {
                $notification.removeClass('show');
                setTimeout(() => $notification.remove(), 300);
            }, 5000);

            // Manual close
            $notification.find('.vf-notification-close').on('click', function() {
                $notification.removeClass('show');
                setTimeout(() => $notification.remove(), 300);
            });
        }
    };

    // Enhanced Add to Cart functionality
    $(document).on('click', '.add_to_cart_button', function() {
        const $button = $(this);
        const originalText = $button.html();
        
        $button.html('<i class="fas fa-spinner fa-spin me-2"></i>Agregando...');
        
        setTimeout(() => {
            $button.html('<i class="fas fa-check me-2"></i>¡Agregado!');
            VaneFrance.showNotification('Producto agregado al carrito exitosamente', 'success');
            
            setTimeout(() => {
                $button.html(originalText);
            }, 2000);
        }, 1000);
    });

    // Global error handling
    window.onerror = function(msg, url, lineNo, columnNo, error) {
        console.error('Vane France Theme Error:', {
            message: msg,
            url: url,
            line: lineNo,
            column: columnNo,
            error: error
        });
    };

})(jQuery);

// Add notification styles dynamically
const notificationStyles = `
<style>
.vf-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border-radius: 10px;
    padding: 1rem 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    z-index: 10001;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    max-width: 400px;
    transform: translateX(100%);
    transition: transform 0.3s ease;
}

.vf-notification.show {
    transform: translateX(0);
}

.vf-notification-success {
    border-left: 4px solid #28a745;
    color: #155724;
}

.vf-notification-error {
    border-left: 4px solid #dc3545;
    color: #721c24;
}

.vf-notification-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    margin-left: auto;
    opacity: 0.7;
}

.vf-notification-close:hover {
    opacity: 1;
}

@media (max-width: 768px) {
    .vf-notification {
        top: 10px;
        right: 10px;
        left: 10px;
        max-width: none;
    }
}
</style>
`;

document.head.insertAdjacentHTML('beforeend', notificationStyles);