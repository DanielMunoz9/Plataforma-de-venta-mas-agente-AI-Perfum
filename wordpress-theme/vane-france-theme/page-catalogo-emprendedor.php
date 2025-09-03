<?php
/**
 * Template Name: Catálogo Emprendedor
 * 
 * @package VaneFrance
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="catalog-page emprendedor-catalog">
        <header class="catalog-header">
            <div class="catalog-hero">
                <h1 class="catalog-title"><?php esc_html_e('Catálogo Emprendedor', 'vane-france'); ?></h1>
                <p class="catalog-subtitle"><?php esc_html_e('Productos especiales con descuentos exclusivos para emprendedores', 'vane-france'); ?></p>
                <div class="catalog-badge">
                    <span class="especial-badge"><?php esc_html_e('Especial', 'vane-france'); ?></span>
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
                                <input type="range" id="price-range" min="0" max="500000" step="10000" value="500000">
                                <div class="price-display">
                                    <span><?php esc_html_e('Hasta: $', 'vane-france'); ?><span id="price-value">500,000</span></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="catalog-products">
                <div class="products-header">
                    <div class="products-count">
                        <span id="products-count-text"><?php esc_html_e('Cargando productos...', 'vane-france'); ?></span>
                    </div>
                    
                    <div class="products-sort">
                        <select id="sort-products">
                            <option value="date"><?php esc_html_e('Más recientes', 'vane-france'); ?></option>
                            <option value="price"><?php esc_html_e('Precio: menor a mayor', 'vane-france'); ?></option>
                            <option value="price-desc"><?php esc_html_e('Precio: mayor a menor', 'vane-france'); ?></option>
                            <option value="name"><?php esc_html_e('Nombre A-Z', 'vane-france'); ?></option>
                            <option value="popularity"><?php esc_html_e('Más populares', 'vane-france'); ?></option>
                        </select>
                    </div>
                </div>

                <div id="products-grid" class="woocommerce-products loading">
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

        <div class="emprendedor-benefits">
            <h2><?php esc_html_e('Beneficios para Emprendedores', 'vane-france'); ?></h2>
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <h3><?php esc_html_e('Precios Especiales', 'vane-france'); ?></h3>
                    <p><?php esc_html_e('Descuentos exclusivos en todos los productos marcados como "Especial".', 'vane-france'); ?></p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 0 0-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10 2 10 2 10 2 10 2 10 2c-1.66 0-3 1.34-3 3 0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM7 5c0-.55.45-1 1-1s1 .45 1 1-.45 1-1 1-1-.45-1-1z"/>
                        </svg>
                    </div>
                    <h3><?php esc_html_e('Envío Gratis', 'vane-france'); ?></h3>
                    <p><?php esc_html_e('Envío gratuito en compras superiores a $150,000 para emprendedores.', 'vane-france'); ?></p>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h3><?php esc_html_e('Soporte Prioritario', 'vane-france'); ?></h3>
                    <p><?php esc_html_e('Atención personalizada y soporte técnico especializado para tu negocio.', 'vane-france'); ?></p>
                </div>
            </div>
        </div>

        <div class="emprendedor-cta">
            <div class="cta-content">
                <h2><?php esc_html_e('¿Quieres ser distribuidor?', 'vane-france'); ?></h2>
                <p><?php esc_html_e('Únete a nuestra red de distribuidores y accede a beneficios exclusivos.', 'vane-france'); ?></p>
                <div class="cta-buttons">
                    <?php $whatsapp_number = get_option('vf_whatsapp_number'); ?>
                    <?php if ($whatsapp_number) : ?>
                        <a href="https://wa.me/<?php echo esc_attr($whatsapp_number); ?>?text=<?php echo urlencode('Hola, me interesa ser distribuidor de Vane France'); ?>" 
                           class="cta-btn primary" 
                           target="_blank" 
                           rel="noopener">
                            <?php esc_html_e('Contactar por WhatsApp', 'vane-france'); ?>
                        </a>
                    <?php endif; ?>
                    <a href="tel:+573193605666" class="cta-btn secondary">
                        <?php esc_html_e('Llamar ahora', 'vane-france'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let loading = false;
    let hasMoreProducts = true;
    let currentFilters = {
        category: 'all',
        maxPrice: 500000,
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
        // For now, we'll simulate with the shortcode
        fetch(ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=load_catalog_products&tag=emprendedor&page=${currentPage}&filters=${JSON.stringify(currentFilters)}&nonce=${vfTheme.nonce}`
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
/* Emprendedor Catalog Styles */
.emprendedor-catalog {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.catalog-header {
    margin-bottom: 3rem;
}

.catalog-hero {
    background: var(--vf-gradient-nav);
    color: var(--vf-white);
    padding: 3rem 2rem;
    border-radius: 12px;
    text-align: center;
    position: relative;
}

.catalog-title {
    font-size: 2.5rem;
    font-family: var(--font-primary);
    margin-bottom: 1rem;
    color: var(--vf-white);
    text-shadow: none;
}

.catalog-subtitle {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.catalog-badge {
    display: inline-block;
}

.especial-badge {
    background: var(--vf-white);
    color: var(--vf-navy);
    padding: 0.5rem 1.5rem;
    border-radius: 25px;
    font-weight: 700;
    font-size: 1.1rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.catalog-content {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 3rem;
    margin-bottom: 4rem;
}

.catalog-filters {
    background: var(--vf-white);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: var(--vf-shadow-light);
    height: fit-content;
    position: sticky;
    top: 2rem;
}

.filter-section h3 {
    color: var(--vf-navy);
    margin-bottom: 1.5rem;
    font-family: var(--font-primary);
    border-bottom: 2px solid var(--vf-red);
    padding-bottom: 0.5rem;
}

.filter-group {
    margin-bottom: 2rem;
}

.filter-group h4 {
    color: var(--vf-navy);
    margin-bottom: 1rem;
    font-size: 1rem;
}

.filter-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.filter-list li {
    margin-bottom: 0.5rem;
}

.filter-link {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem;
    color: var(--vf-dark-gray);
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.filter-link:hover,
.filter-link.active {
    background: var(--vf-red);
    color: var(--vf-white);
    text-decoration: none;
}

.filter-link .count {
    font-size: 0.85rem;
    opacity: 0.8;
}

.price-filter {
    margin-top: 1rem;
}

.price-filter input[type="range"] {
    width: 100%;
    margin-bottom: 0.5rem;
}

.price-display {
    text-align: center;
    color: var(--vf-red);
    font-weight: 500;
}

.catalog-products {
    min-height: 600px;
}

.products-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.products-count {
    font-weight: 500;
    color: var(--vf-navy);
}

.products-sort select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: var(--vf-white);
    color: var(--vf-dark-gray);
}

.loading-spinner {
    text-align: center;
    padding: 3rem;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid var(--vf-red);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.load-more-container {
    text-align: center;
    margin-top: 3rem;
}

.load-more-btn {
    background: var(--vf-red);
    color: var(--vf-white);
    border: none;
    padding: 1rem 2rem;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.3s ease;
}

.load-more-btn:hover {
    background: var(--vf-navy);
}

.emprendedor-benefits {
    margin: 4rem 0;
}

.emprendedor-benefits h2 {
    text-align: center;
    margin-bottom: 2rem;
    color: var(--vf-navy);
    font-family: var(--font-primary);
}

.benefits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.benefit-card {
    background: var(--vf-white);
    padding: 2rem;
    border-radius: 12px;
    box-shadow: var(--vf-shadow-light);
    text-align: center;
    transition: transform 0.3s ease;
}

.benefit-card:hover {
    transform: translateY(-5px);
}

.benefit-icon {
    color: var(--vf-red);
    margin-bottom: 1rem;
}

.benefit-card h3 {
    color: var(--vf-navy);
    margin-bottom: 1rem;
}

.emprendedor-cta {
    background: var(--vf-gradient-nav);
    color: var(--vf-white);
    padding: 3rem 2rem;
    border-radius: 12px;
    text-align: center;
}

.emprendedor-cta h2 {
    color: var(--vf-white);
    margin-bottom: 1rem;
    text-shadow: none;
}

.emprendedor-cta p {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.cta-btn {
    padding: 1rem 2rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.cta-btn.primary {
    background: var(--vf-white);
    color: var(--vf-navy);
}

.cta-btn.primary:hover {
    background: var(--vf-light-gray);
    color: var(--vf-navy);
    text-decoration: none;
}

.cta-btn.secondary {
    background: transparent;
    color: var(--vf-white);
    border: 2px solid var(--vf-white);
}

.cta-btn.secondary:hover {
    background: var(--vf-white);
    color: var(--vf-navy);
    text-decoration: none;
}

/* Responsive Design */
@media (max-width: 768px) {
    .emprendedor-catalog {
        padding: 1rem;
    }
    
    .catalog-content {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .catalog-filters {
        position: static;
    }
    
    .catalog-title {
        font-size: 2rem;
    }
    
    .products-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .benefits-grid {
        grid-template-columns: 1fr;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<?php
get_footer();