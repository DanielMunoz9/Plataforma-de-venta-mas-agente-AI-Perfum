/**
 * Vane France Landing Page JavaScript
 * Using vf- prefix to avoid conflicts with existing scripts
 */

class VfLandingPage {
    constructor() {
        this.init();
    }

    init() {
        this.setupVideoControls();
        this.setupScrollAnimations();
        this.setupSmoothScrolling();
        this.autoplayVideo();
        
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.onDOMReady();
            });
        } else {
            this.onDOMReady();
        }
    }

    onDOMReady() {
        console.log('Vane France Landing Page initialized');
        this.observeAnimations();
    }

    /**
     * Setup video autoplay and mute controls
     */
    autoplayVideo() {
        const heroVideo = document.querySelector('.vf-hero-video');
        if (heroVideo) {
            // Set video properties for autoplay
            heroVideo.muted = true;
            heroVideo.autoplay = true;
            heroVideo.loop = true;
            heroVideo.playsInline = true;

            // Attempt to play video (some browsers may block autoplay)
            heroVideo.play().catch(error => {
                console.log('Autoplay was prevented:', error);
                // Fallback: show play button or handle autoplay failure
                this.handleAutoplayFailure(heroVideo);
            });
        }
    }

    /**
     * Handle autoplay failure
     */
    handleAutoplayFailure(video) {
        // Add a play button overlay if autoplay fails
        const playButton = document.createElement('button');
        playButton.className = 'vf-video-play-overlay';
        playButton.innerHTML = '<i class="fas fa-play"></i>';
        playButton.style.cssText = `
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(212, 175, 55, 0.9);
            border: none;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            color: white;
            font-size: 24px;
            cursor: pointer;
            z-index: 10;
            transition: all 0.3s ease;
        `;

        playButton.addEventListener('click', () => {
            video.play();
            playButton.remove();
        });

        video.parentElement.appendChild(playButton);
    }

    /**
     * Setup video controls (play/pause and mute/unmute)
     */
    setupVideoControls() {
        const video = document.querySelector('.vf-video, .vf-hero-video');
        const muteBtn = document.querySelector('.vf-mute-btn');
        const playBtn = document.querySelector('.vf-play-btn');

        if (video && muteBtn) {
            muteBtn.addEventListener('click', () => {
                video.muted = !video.muted;
                muteBtn.innerHTML = video.muted ? 
                    '<i class="fas fa-volume-mute"></i>' : 
                    '<i class="fas fa-volume-up"></i>';
            });
        }

        if (video && playBtn) {
            playBtn.addEventListener('click', () => {
                if (video.paused) {
                    video.play();
                    playBtn.innerHTML = '<i class="fas fa-pause"></i>';
                } else {
                    video.pause();
                    playBtn.innerHTML = '<i class="fas fa-play"></i>';
                }
            });
        }

        // Update play button state when video ends
        if (video) {
            video.addEventListener('ended', () => {
                if (playBtn) {
                    playBtn.innerHTML = '<i class="fas fa-play"></i>';
                }
            });
        }
    }

    /**
     * Setup scroll-triggered animations
     */
    setupScrollAnimations() {
        // Create intersection observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        this.animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('vf-visible');
                }
            });
        }, observerOptions);
    }

    /**
     * Observe elements for animations
     */
    observeAnimations() {
        const animatedElements = document.querySelectorAll(
            '.vf-fade-in, .vf-slide-in-left, .vf-slide-in-right'
        );
        
        animatedElements.forEach(element => {
            this.animationObserver.observe(element);
        });
    }

    /**
     * Setup smooth scrolling for anchor links
     */
    setupSmoothScrolling() {
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        
        anchorLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                const href = link.getAttribute('href');
                if (href === '#') return;
                
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    /**
     * Add parallax effect to hero section
     */
    setupParallax() {
        const hero = document.querySelector('.vf-hero');
        if (!hero) return;

        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallax = scrolled * 0.5;
            hero.style.transform = `translateY(${parallax}px)`;
        });
    }

    /**
     * Initialize contact form handling (if present)
     */
    setupContactForm() {
        const form = document.querySelector('.vf-contact-form');
        if (!form) return;

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleFormSubmission(form);
        });
    }

    /**
     * Handle form submission
     */
    handleFormSubmission(form) {
        const formData = new FormData(form);
        const submitBtn = form.querySelector('.vf-submit-btn');
        
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';
        }

        // Here you would typically send the form data to your server
        // For now, we'll just simulate a successful submission
        setTimeout(() => {
            this.showFormSuccess();
            form.reset();
            
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar';
            }
        }, 2000);
    }

    /**
     * Show form success message
     */
    showFormSuccess() {
        const message = document.createElement('div');
        message.className = 'vf-form-success';
        message.innerHTML = '¡Mensaje enviado con éxito!';
        message.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #d4af37;
            color: white;
            padding: 15px 25px;
            border-radius: 5px;
            z-index: 1000;
            animation: slideInRight 0.3s ease;
        `;

        document.body.appendChild(message);

        setTimeout(() => {
            message.remove();
        }, 5000);
    }

    /**
     * Setup lazy loading for images
     */
    setupLazyLoading() {
        const images = document.querySelectorAll('img[data-src]');
        
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.add('vf-loaded');
                    imageObserver.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    }

    /**
     * Add CSS animations dynamically
     */
    addDynamicStyles() {
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    }
}

// Initialize the Vane France Landing Page
const vfLandingPage = new VfLandingPage();

// Export for potential external use
window.VfLandingPage = VfLandingPage;