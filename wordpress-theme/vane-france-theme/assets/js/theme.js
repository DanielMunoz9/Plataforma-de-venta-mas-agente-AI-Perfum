/**
 * Vane France Theme JavaScript
 * 
 * @package VaneFrance
 * @version 1.0.0
 */

(function($) {
    'use strict';

    // Theme initialization
    const VaneFranceTheme = {
        
        init: function() {
            this.bindEvents();
            this.initAnimations();
            this.initCarousels();
            this.initScrollEffects();
            this.initSearchOverlay();
            this.initMobileMenu();
            this.initNewsletterForm();
            this.initQuantityButtons();
            this.initProductGallery();
        },

        bindEvents: function() {
            $(document).ready(this.onDocumentReady.bind(this));
            $(window).on('load', this.onWindowLoad.bind(this));
            $(window).on('resize', this.onWindowResize.bind(this));
            $(window).on('scroll', this.onWindowScroll.bind(this));
        },

        onDocumentReady: function() {
            console.log('Vane France Theme Loaded');
            this.initSmoothScrolling();
            this.initTooltips();
        },

        onWindowLoad: function() {
            this.initMasonry();
            this.fadeInContent();
        },

        onWindowResize: function() {
            this.handleResponsiveElements();
        },

        onWindowScroll: function() {
            this.handleScrollAnimations();
            this.updateProgressBar();
        },

        // Smooth scrolling for anchor links
        initSmoothScrolling: function() {
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                
                const target = $(this.getAttribute('href'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 800);
                }
            });
        },

        // Initialize animations using Intersection Observer
        initAnimations: function() {
            if ('IntersectionObserver' in window) {
                const observerOptions = {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                };

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                            
                            // Add staggered animation for child elements
                            const children = entry.target.querySelectorAll('.woocommerce-product-card, .post-card, .benefit-card, .feature-card');
                            children.forEach((child, index) => {
                                setTimeout(() => {
                                    child.style.animation = 'fadeInUp 0.6s ease forwards';
                                }, index * 100);
                            });
                        }
                    });
                }, observerOptions);

                // Observe elements with fade-in-on-scroll class
                document.querySelectorAll('.fade-in-on-scroll').forEach(el => {
                    observer.observe(el);
                });

                // Observe product grids and post grids
                document.querySelectorAll('.woocommerce-products, .posts-grid, .categories-grid').forEach(el => {
                    observer.observe(el);
                });
            }
        },

        // Initialize carousels/sliders
        initCarousels: function() {
            // Product carousel on front page
            if ($('.hero-section').length) {
                this.initHeroAnimations();
            }

            // Initialize any Swiper carousels
            if (typeof Swiper !== 'undefined') {
                const productSlider = new Swiper('.product-slider', {
                    slidesPerView: 1,
                    spaceBetween: 20,
                    loop: true,
                    autoplay: {
                        delay: 5000,
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 2,
                        },
                        768: {
                            slidesPerView: 3,
                        },
                        1024: {
                            slidesPerView: 4,
                        },
                    },
                });
            }
        },

        // Hero section animations
        initHeroAnimations: function() {
            const heroTitle = document.querySelector('.hero-title');
            const heroSubtitle = document.querySelector('.hero-subtitle');
            const heroCtas = document.querySelector('.hero-ctas');

            if (heroTitle) {
                setTimeout(() => {
                    heroTitle.style.animation = 'fadeInUp 1s ease forwards';
                }, 500);
            }

            if (heroSubtitle) {
                setTimeout(() => {
                    heroSubtitle.style.animation = 'fadeInUp 1s ease forwards';
                }, 700);
            }

            if (heroCtas) {
                setTimeout(() => {
                    heroCtas.style.animation = 'fadeInUp 1s ease forwards';
                }, 900);
                
                // Add individual CTA button animations
                const ctaButtons = heroCtas.querySelectorAll('.hero-cta');
                ctaButtons.forEach((button, index) => {
                    setTimeout(() => {
                        button.style.transform = 'translateY(0)';
                        button.style.opacity = '1';
                    }, 1000 + (index * 200));
                });
            }
        },

        // Scroll effects
        initScrollEffects: function() {
            // Parallax effect for hero section
            $(window).on('scroll', function() {
                const scrolled = $(this).scrollTop();
                const hero = $('.hero-section');
                
                if (hero.length) {
                    const speed = scrolled * 0.5;
                    hero.css('transform', `translateY(${speed}px)`);
                }

                // Floating elements effect
                $('.floating-element').each(function() {
                    const speed = $(this).data('speed') || 0.5;
                    const yPos = -(scrolled * speed);
                    $(this).css('transform', `translateY(${yPos}px)`);
                });
            });
        },

        // Handle scroll animations
        handleScrollAnimations: function() {
            const scrollTop = $(window).scrollTop();
            const windowHeight = $(window).height();

            // Fade in elements as they come into view
            $('.fade-in-on-scroll:not(.visible)').each(function() {
                const elementTop = $(this).offset().top;
                
                if (elementTop < scrollTop + windowHeight - 100) {
                    $(this).addClass('visible');
                }
            });

            // Update header on scroll
            const header = $('.site-header');
            if (scrollTop > 100) {
                header.addClass('scrolled');
            } else {
                header.removeClass('scrolled');
            }
        },

        // Search overlay
        initSearchOverlay: function() {
            const searchTrigger = $('.search-trigger');
            const searchOverlay = $('.search-overlay');
            const searchClose = $('.search-close');

            searchTrigger.on('click', function(e) {
                e.preventDefault();
                searchOverlay.addClass('active');
                searchOverlay.find('input').focus();
            });

            searchClose.on('click', function() {
                searchOverlay.removeClass('active');
            });

            // Close on escape key
            $(document).on('keydown', function(e) {
                if (e.keyCode === 27 && searchOverlay.hasClass('active')) {
                    searchOverlay.removeClass('active');
                }
            });
        },

        // Mobile menu
        initMobileMenu: function() {
            const menuToggle = $('.menu-toggle');
            const mobileMenu = $('.mobile-menu');
            const menuClose = $('.menu-close');

            menuToggle.on('click', function() {
                mobileMenu.addClass('active');
                $('body').addClass('menu-open');
            });

            menuClose.on('click', function() {
                mobileMenu.removeClass('active');
                $('body').removeClass('menu-open');
            });

            // Close on outside click
            $(document).on('click', function(e) {
                if (mobileMenu.hasClass('active') && !$(e.target).closest('.mobile-menu, .menu-toggle').length) {
                    mobileMenu.removeClass('active');
                    $('body').removeClass('menu-open');
                }
            });
        },

        // Newsletter form handling
        initNewsletterForm: function() {
            $('.newsletter-form').on('submit', function(e) {
                e.preventDefault();
                
                const form = $(this);
                const email = form.find('input[type="email"]').val();
                const submitBtn = form.find('button[type="submit"]');
                const originalText = submitBtn.text();

                if (!email) {
                    alert('Por favor ingresa tu email.');
                    return;
                }

                // Disable submit button
                submitBtn.prop('disabled', true).text('Suscribiendo...');

                // AJAX request
                $.ajax({
                    url: vfTheme.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'vf_newsletter_signup',
                        email: email,
                        nonce: vfTheme.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            form.html('<p class="success-message">¡Gracias por suscribirte! Pronto recibirás noticias sobre nuestros productos.</p>');
                        } else {
                            alert(response.data || 'Error al suscribirse. Inténtalo de nuevo.');
                            submitBtn.prop('disabled', false).text(originalText);
                        }
                    },
                    error: function() {
                        alert('Error al procesar la solicitud. Inténtalo de nuevo.');
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });
        },

        // WooCommerce quantity buttons
        initQuantityButtons: function() {
            $(document).on('click', '.quantity-plus, .quantity-minus', function(e) {
                e.preventDefault();
                
                const button = $(this);
                const input = button.siblings('input[type="number"]');
                let value = parseInt(input.val()) || 0;
                const max = parseInt(input.attr('max')) || 999;
                const min = parseInt(input.attr('min')) || 1;

                if (button.hasClass('quantity-plus') && value < max) {
                    value++;
                } else if (button.hasClass('quantity-minus') && value > min) {
                    value--;
                }

                input.val(value).trigger('change');
            });
        },

        // Product gallery
        initProductGallery: function() {
            // Lightbox for product images
            if ($.fn.magnificPopup) {
                $('.product-gallery').magnificPopup({
                    delegate: 'a',
                    type: 'image',
                    gallery: {
                        enabled: true
                    },
                    image: {
                        titleSrc: 'title'
                    }
                });
            }

            // Product image zoom
            $('.product-image').on('mouseenter', function() {
                $(this).find('img').addClass('zoomed');
            }).on('mouseleave', function() {
                $(this).find('img').removeClass('zoomed');
            });
        },

        // Initialize tooltips
        initTooltips: function() {
            $('[data-tooltip]').each(function() {
                const element = $(this);
                const tooltipText = element.data('tooltip');
                
                element.on('mouseenter', function() {
                    const tooltip = $('<div class="tooltip">' + tooltipText + '</div>');
                    $('body').append(tooltip);
                    
                    const offset = element.offset();
                    tooltip.css({
                        top: offset.top - tooltip.outerHeight() - 10,
                        left: offset.left + (element.outerWidth() / 2) - (tooltip.outerWidth() / 2)
                    });
                });
                
                element.on('mouseleave', function() {
                    $('.tooltip').remove();
                });
            });
        },

        // Masonry layout for blog posts
        initMasonry: function() {
            if ($.fn.masonry && $('.posts-masonry').length) {
                $('.posts-masonry').masonry({
                    itemSelector: '.post-card',
                    columnWidth: '.post-card',
                    gutter: 20,
                    percentPosition: true
                });
            }
        },

        // Progress bar for reading
        updateProgressBar: function() {
            if ($('.reading-progress').length) {
                const winScroll = $(window).scrollTop();
                const height = $(document).height() - $(window).height();
                const scrolled = (winScroll / height) * 100;
                
                $('.reading-progress .progress-bar').css('width', scrolled + '%');
            }
        },

        // Fade in content on load
        fadeInContent: function() {
            $('.fade-on-load').each(function(index) {
                $(this).delay(index * 100).animate({
                    opacity: 1,
                    transform: 'translateY(0)'
                }, 600);
            });
        },

        // Handle responsive elements
        handleResponsiveElements: function() {
            const windowWidth = $(window).width();
            
            // Mobile adjustments
            if (windowWidth < 768) {
                $('.desktop-only').hide();
                $('.mobile-only').show();
            } else {
                $('.desktop-only').show();
                $('.mobile-only').hide();
            }
        },

        // Utility functions
        utils: {
            debounce: function(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            },

            throttle: function(func, limit) {
                let inThrottle;
                return function() {
                    const args = arguments;
                    const context = this;
                    if (!inThrottle) {
                        func.apply(context, args);
                        inThrottle = true;
                        setTimeout(() => inThrottle = false, limit);
                    }
                };
            },

            formatPrice: function(price) {
                return new Intl.NumberFormat('es-CO', {
                    style: 'currency',
                    currency: 'COP',
                    minimumFractionDigits: 0
                }).format(price);
            },

            validateEmail: function(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
        }
    };

    // Initialize theme when DOM is ready
    $(document).ready(function() {
        VaneFranceTheme.init();
    });

    // Make theme object globally available
    window.VaneFranceTheme = VaneFranceTheme;

    // Additional CSS animations via JavaScript
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        
        .product-image img.zoomed {
            transform: scale(1.1);
            transition: transform 0.3s ease;
        }
        
        .site-header.scrolled {
            background: rgba(0, 35, 149, 0.95);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .tooltip {
            position: absolute;
            background: var(--vf-navy);
            color: var(--vf-white);
            padding: 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
            z-index: 1000;
            pointer-events: none;
        }
        
        .tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 5px solid transparent;
            border-top-color: var(--vf-navy);
        }
        
        .reading-progress {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: rgba(0, 0, 0, 0.1);
            z-index: 1001;
        }
        
        .reading-progress .progress-bar {
            height: 100%;
            background: var(--vf-red);
            transition: width 0.3s ease;
        }
        
        .success-message {
            color: var(--vf-red);
            font-weight: 500;
            text-align: center;
            padding: 1rem;
            background: var(--vf-light-gray);
            border-radius: 4px;
        }
    `;
    document.head.appendChild(style);

})(jQuery);