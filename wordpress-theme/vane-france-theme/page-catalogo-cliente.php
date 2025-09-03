<?php
/**
 * Template Name: Catálogo Cliente
 * 
 * @package VaneFrance
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="catalog-page cliente-catalog">
        <header class="catalog-header">
            <div class="catalog-hero">
                <h1 class="catalog-title"><?php esc_html_e('Catálogo Cliente', 'vane-france'); ?></h1>
                <p class="catalog-subtitle"><?php esc_html_e('Descubre nuestra colección completa de fragancias francesas de alta calidad', 'vane-france'); ?></p>
                <div class="catalog-badge">
                    <span class="quality-badge"><?php esc_html_e('Premium', 'vane-france'); ?></span>
                </div>
            </div>
        </header>

        <div class="catalog-content">
            <div class="catalog-filters">
                <div class="filter-section">
                    <h3><?php esc_html_e('Filtros', 'vane-france'); ?></h3>
                    
                    <?php if (class_exists('WooCommerce')) : ?>
                        <!-- Category filter -->
                        <?php
                        $product_categories = get_terms(array(
                            'taxonomy' => 'product_cat',
                            'hide_empty' => true,
                            'parent' => 0
                        ));
                        
                        if (!empty($product_categories)) :
                        ?>
                            <div class="filter-group">
                                <h4><?php esc_html_e('Categorías', 'vane-france'); ?></h4>
                                <ul class="filter-list">
                                    <li>
                                        <a href="?category=all" class="filter-link active" data-category="all">
                                            <?php esc_html_e('Todas las categorías', 'vane-france'); ?>
                                        </a>
                                    </li>
                                    <?php foreach ($product_categories as $category) : ?>
                                        <li>
                                            <a href="?category=<?php echo esc_attr($category->slug); ?>" 
                                               class="filter-link" 
                                               data-category="<?php echo esc_attr($category->slug); ?>">
                                                <?php echo esc_html($category->name); ?>
                                                <span class="count">(<?php echo $category->count; ?>)</span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Price filter -->
                        <div class="filter-group">
                            <h4><?php esc_html_e('Rango de Precio', 'vane-france'); ?></h4>
                            <div class="price-filter">
                                <input type="range" id="price-range" min="0" max="800000" step="10000" value="800000">
                                <div class="price-display">
                                    <span><?php esc_html_e('Hasta: $', 'vane-france'); ?><span id="price-value">800,000</span></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Brand filter -->
                        <div class="filter-group">
                            <h4><?php esc_html_e('Tipo de Fragancia', 'vane-france'); ?></h4>
                            <ul class="filter-list checkbox-list">
                                <li>
                                    <label>
                                        <input type="checkbox" name="fragrance_type" value="eau-de-parfum">
                                        <?php esc_html_e('Eau de Parfum', 'vane-france'); ?>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="checkbox" name="fragrance_type" value="eau-de-toilette">
                                        <?php esc_html_e('Eau de Toilette', 'vane-france'); ?>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="checkbox" name="fragrance_type" value="perfume">
                                        <?php esc_html_e('Perfume', 'vane-france'); ?>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="checkbox" name="fragrance_type" value="cologne">
                                        <?php esc_html_e('Cologne', 'vane-france'); ?>
                                    </label>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Gender filter -->
                        <div class="filter-group">
                            <h4><?php esc_html_e('Para', 'vane-france'); ?></h4>
                            <ul class="filter-list checkbox-list">
                                <li>
                                    <label>
                                        <input type="checkbox" name="gender" value="mujer">
                                        <?php esc_html_e('Mujer', 'vane-france'); ?>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="checkbox" name="gender" value="hombre">
                                        <?php esc_html_e('Hombre', 'vane-france'); ?>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="checkbox" name="gender" value="unisex">
                                        <?php esc_html_e('Unisex', 'vane-france'); ?>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="catalog-products">
                <div class="products-header">
                    <div class="products-count">
                        <span id="products-count-text"><?php esc_html_e('Cargando productos...', 'vane-france'); ?></span>
                    </div>
                    
                    <div class="view-options">
                        <button class="view-btn active" data-view="grid">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M3 11h8V3H3v8zm2-6h4v4H5V5zM13 3v8h8V3h-8zm6 6h-4V5h4v4zM3 21h8v-8H3v8zm2-6h4v4H5v-4zM18 13h-2v2h2v-2zM18 17h-2v2h2v-2zM16 15h2v2h-2v-2zM18 19h2v2h-2v-2z"/>
                            </svg>
                        </button>
                        <button class="view-btn" data-view="list">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M4 14h4v-4H4v4zm0 5h4v-4H4v4zM4 9h4V5H4v4zm5 5h12v-4H9v4zm0 5h12v-4H9v4zM9 5v4h12V5H9z"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="products-sort">
                        <select id="sort-products">
                            <option value="date"><?php esc_html_e('Más recientes', 'vane-france'); ?></option>
                            <option value="price"><?php esc_html_e('Precio: menor a mayor', 'vane-france'); ?></option>
                            <option value="price-desc"><?php esc_html_e('Precio: mayor a menor', 'vane-france'); ?></option>
                            <option value="name"><?php esc_html_e('Nombre A-Z', 'vane-france'); ?></option>
                            <option value="popularity"><?php esc_html_e('Más populares', 'vane-france'); ?></option>
                            <option value="rating"><?php esc_html_e('Mejor valorados', 'vane-france'); ?></option>
                        </select>
                    </div>
                </div>

                <div id="products-grid" class="woocommerce-products grid-view loading">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                        <p><?php esc_html_e('Cargando productos...', 'vane-france'); ?></p>
                    </div>
                </div>

                <div id="load-more-container" class="load-more-container" style="display: none;">
                    <button id="load-more-btn" class="load-more-btn">
                        <?php esc_html_e('Cargar más productos', 'vane-france'); ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="cliente-features">
            <h2><?php esc_html_e('¿Por qué elegir Vane France?', 'vane-france'); ?></h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <h3><?php esc_html_e('Calidad Premium', 'vane-france'); ?></h3>
                    <p><?php esc_html_e('Fragancias auténticas francesas de las mejores casas perfumeras.', 'vane-france'); ?></p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 11H7v9a2 2 0 002 2h8a2 2 0 002-2V9h-8v2zm3-7c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1m0-2C9.79 2 8 3.79 8 6s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zM7.5 12c-.83 0-1.5.67-1.5 1.5S6.67 15 7.5 15 9 14.33 9 13.5 8.33 12 7.5 12zm9 0c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z"/>
                        </svg>
                    </div>
                    <h3><?php esc_html_e('Garantía de Autenticidad', 'vane-france'); ?></h3>
                    <p><?php esc_html_e('Todos nuestros productos son 100% originales con garantía de autenticidad.', 'vane-france'); ?></p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 0 0-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10 2 10 2 10 2 10 2 10 2c-1.66 0-3 1.34-3 3 0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM7 5c0-.55.45-1 1-1s1 .45 1 1-.45 1-1 1-1-.45-1-1z"/>
                        </svg>
                    </div>
                    <h3><?php esc_html_e('Envío Seguro', 'vane-france'); ?></h3>
                    <p><?php esc_html_e('Embalaje especial y envío asegurado para proteger tus fragancias.', 'vane-france'); ?></p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h3><?php esc_html_e('Atención Personalizada', 'vane-france'); ?></h3>
                    <p><?php esc_html_e('Asesoramiento experto para ayudarte a encontrar tu fragancia perfecta.', 'vane-france'); ?></p>
                </div>
            </div>
        </div>

        <div class="newsletter-section-catalog">
            <div class="newsletter-content">
                <h2><?php esc_html_e('Mantente al día con las últimas fragancias', 'vane-france'); ?></h2>
                <p><?php esc_html_e('Suscríbete y recibe las últimas novedades, ofertas exclusivas y consejos de perfumería.', 'vane-france'); ?></p>
                <form class="newsletter-form" action="#" method="post">
                    <input type="email" name="newsletter_email" placeholder="<?php esc_attr_e('Ingresa tu email', 'vane-france'); ?>" required>
                    <button type="submit"><?php esc_html_e('Suscribirse', 'vane-france'); ?></button>
                    <input type="hidden" name="action" value="vf_newsletter_signup">
                    <?php wp_nonce_field('vf_newsletter', 'vf_newsletter_nonce'); ?>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let loading = false;
    let hasMoreProducts = true;
    let currentView = 'grid';
    let currentFilters = {
        category: 'all',
        maxPrice: 800000,
        fragranceTypes: [],
        genders: [],
        sort: 'date'
    };

    // Load products function
    function loadProducts(reset = false) {
        if (loading) return;
        loading = true;

        if (reset) {
            currentPage = 1;
            document.getElementById('products-grid').innerHTML = '<div class="loading-spinner"><div class="spinner"></div><p>Cargando productos...</p></div>';
        }

        // Here you would make an AJAX call to load products
        fetch(ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=load_catalog_products&tag=cliente&page=${currentPage}&filters=${JSON.stringify(currentFilters)}&nonce=${vfTheme.nonce}`
        })
        .then(response => response.json())
        .then(data => {
            if (reset) {
                document.getElementById('products-grid').innerHTML = data.html;
            } else {
                document.getElementById('products-grid').insertAdjacentHTML('beforeend', data.html);
            }
            
            hasMoreProducts = data.has_more;
            document.getElementById('products-count-text').textContent = data.count_text;
            
            if (hasMoreProducts) {
                document.getElementById('load-more-container').style.display = 'block';
            } else {
                document.getElementById('load-more-container').style.display = 'none';
            }
            
            loading = false;
        })
        .catch(error => {
            console.error('Error loading products:', error);
            loading = false;
        });
    }

    // View toggle handlers
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            currentView = this.dataset.view;
            const productsGrid = document.getElementById('products-grid');
            
            if (currentView === 'list') {
                productsGrid.classList.remove('grid-view');
                productsGrid.classList.add('list-view');
            } else {
                productsGrid.classList.remove('list-view');
                productsGrid.classList.add('grid-view');
            }
        });
    });

    // Filter handlers
    document.querySelectorAll('.filter-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            document.querySelectorAll('.filter-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            currentFilters.category = this.dataset.category;
            loadProducts(true);
        });
    });

    // Checkbox filters
    document.querySelectorAll('input[name="fragrance_type"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            currentFilters.fragranceTypes = Array.from(document.querySelectorAll('input[name="fragrance_type"]:checked')).map(cb => cb.value);
            loadProducts(true);
        });
    });

    document.querySelectorAll('input[name="gender"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            currentFilters.genders = Array.from(document.querySelectorAll('input[name="gender"]:checked')).map(cb => cb.value);
            loadProducts(true);
        });
    });

    // Price filter
    const priceRange = document.getElementById('price-range');
    const priceValue = document.getElementById('price-value');
    
    priceRange.addEventListener('input', function() {
        const value = parseInt(this.value);
        priceValue.textContent = value.toLocaleString();
        currentFilters.maxPrice = value;
        
        clearTimeout(this.priceTimeout);
        this.priceTimeout = setTimeout(() => {
            loadProducts(true);
        }, 500);
    });

    // Sort handler
    document.getElementById('sort-products').addEventListener('change', function() {
        currentFilters.sort = this.value;
        loadProducts(true);
    });

    // Load more button
    document.getElementById('load-more-btn').addEventListener('click', function() {
        currentPage++;
        loadProducts(false);
    });

    // Initial load
    loadProducts(true);
});
</script>

<style>
/* Cliente Catalog Styles */
.cliente-catalog {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.catalog-hero {
    background: linear-gradient(135deg, var(--vf-navy) 0%, var(--vf-red) 100%);
    color: var(--vf-white);
    padding: 3rem 2rem;
    border-radius: 12px;
    text-align: center;
    position: relative;
}

.quality-badge {
    background: rgba(255, 255, 255, 0.2);
    color: var(--vf-white);
    padding: 0.5rem 1.5rem;
    border-radius: 25px;
    font-weight: 700;
    font-size: 1.1rem;
    border: 2px solid var(--vf-white);
    backdrop-filter: blur(10px);
}

.checkbox-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.checkbox-list li {
    margin-bottom: 0.5rem;
}

.checkbox-list label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: background 0.3s ease;
}

.checkbox-list label:hover {
    background: var(--vf-light-gray);
}

.checkbox-list input[type="checkbox"] {
    margin: 0;
}

.view-options {
    display: flex;
    gap: 0.5rem;
}

.view-btn {
    background: var(--vf-white);
    border: 1px solid #ddd;
    padding: 0.5rem;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
    color: var(--vf-dark-gray);
}

.view-btn:hover,
.view-btn.active {
    background: var(--vf-red);
    color: var(--vf-white);
    border-color: var(--vf-red);
}

.products-grid.list-view .woocommerce-product-card {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem;
}

.products-grid.list-view .product-image {
    flex-shrink: 0;
    width: 150px;
}

.products-grid.list-view .product-image img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.products-grid.list-view .product-info {
    flex: 1;
    text-align: left;
}

.cliente-features {
    margin: 4rem 0;
}

.cliente-features h2 {
    text-align: center;
    margin-bottom: 2rem;
    color: var(--vf-navy);
    font-family: var(--font-primary);
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.feature-card {
    background: var(--vf-white);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: var(--vf-shadow-light);
    text-align: center;
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.feature-icon {
    color: var(--vf-red);
    margin-bottom: 1rem;
}

.feature-card h3 {
    color: var(--vf-navy);
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.newsletter-section-catalog {
    background: var(--vf-light-gray);
    padding: 3rem 2rem;
    border-radius: 12px;
    text-align: center;
    margin-top: 4rem;
}

.newsletter-section-catalog h2 {
    color: var(--vf-navy);
    margin-bottom: 1rem;
}

.newsletter-section-catalog p {
    margin-bottom: 2rem;
    color: var(--vf-dark-gray);
}

.newsletter-section-catalog .newsletter-form {
    max-width: 400px;
    margin: 0 auto;
    display: flex;
    gap: 0.5rem;
}

.newsletter-section-catalog .newsletter-form input {
    flex: 1;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.newsletter-section-catalog .newsletter-form button {
    background: var(--vf-red);
    color: var(--vf-white);
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 4px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.3s ease;
}

.newsletter-section-catalog .newsletter-form button:hover {
    background: var(--vf-navy);
}

/* Responsive Design */
@media (max-width: 768px) {
    .cliente-catalog {
        padding: 1rem;
    }
    
    .catalog-content {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .catalog-filters {
        position: static;
    }
    
    .products-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .view-options {
        justify-content: center;
    }
    
    .products-grid.list-view .woocommerce-product-card {
        flex-direction: column;
        text-align: center;
    }
    
    .products-grid.list-view .product-image {
        width: 100%;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
    }
    
    .newsletter-section-catalog .newsletter-form {
        flex-direction: column;
    }
}
</style>

<?php
get_footer();