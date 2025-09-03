# Vane France WordPress Theme

Elegant French perfumery theme with WooCommerce integration featuring a navy blue dominant color scheme inspired by the French flag palette, dual catalog system, and modern responsive design.

## Features

### Core Functionality
- **Dual Catalog System**: Separate catalogs for "emprendedor" and "cliente" products
- **WooCommerce Integration**: Full WooCommerce support with custom styling
- **"Especial" Product Badges**: Automatic blue badges for products tagged "emprendedor"
- **WhatsApp Integration**: Floating WhatsApp button on product pages
- **Responsive Design**: Mobile-first approach with smooth animations
- **Blog with Right Sidebar**: Complete blog functionality with card layouts

### Design Elements
- **French Flag Color Palette**: Navy blue (#002395), red (#ed2939), white (#ffffff)
- **Premium Typography**: Playfair Display for headings, Roboto for body text
- **Smooth Animations**: CSS3 and JavaScript animations using IntersectionObserver
- **Professional Layout**: Clean, modern design with subtle shadows and gradients

### Business Features
- **Auto-Page Creation**: Automatically creates catalog pages on theme activation
- **Contact Information**: Built-in business info with exact addresses and hours
- **Newsletter Integration**: Newsletter signup forms throughout the site
- **SEO Optimized**: Clean markup and performance optimizations
- **Customizer Options**: WhatsApp number and hero section customization

## Installation

### Quick Install (Recommended)
1. Download the theme ZIP file from `/dist/vane-france-theme.zip`
2. In WordPress Admin, go to **Appearance > Themes > Add New > Upload Theme**
3. Upload the ZIP file and activate the theme
4. Configure WhatsApp number in **Appearance > Customize > WhatsApp Settings**

### Manual Install
1. Extract the theme files to `/wp-content/themes/vane-france-theme/`
2. Activate the theme in **Appearance > Themes**
3. Configure theme options in the Customizer

## Configuration

### Required Plugins
- **WooCommerce**: For e-commerce functionality
- **Wompi Plugin**: For payment processing (install separately)

### Theme Setup
1. **WhatsApp Configuration**:
   - Go to **Appearance > Customize > WhatsApp Settings**
   - Enter your WhatsApp number with country code (e.g., +573193605666)

2. **Logo Setup**:
   - Upload your logo in **Appearance > Customize > Site Identity**
   - Recommended size: 200x80 pixels

3. **Menu Configuration**:
   - Go to **Appearance > Menus**
   - Create menus for "Primary Navigation" and "Footer Navigation"

4. **Homepage Setup**:
   - Go to **Settings > Reading**
   - Set "Front page displays" to "A static page"
   - Create a new page and select it as the front page

### Product Configuration
1. **Create Product Tags**:
   - Go to **Products > Tags**
   - Create tags: `emprendedor` and `cliente`

2. **Tag Products**:
   - Edit products and assign appropriate tags
   - Products tagged "emprendedor" will show blue "Especial" badges

3. **Product Categories**:
   - Create categories like "Eau de Parfum", "Eau de Toilette", etc.
   - Organize products for better filtering

## Customization

### Color Scheme
The theme uses CSS custom properties for easy color customization:

```css
:root {
  --vf-navy: #002395;      /* Primary navy blue */
  --vf-red: #ed2939;       /* Accent red */
  --vf-white: #ffffff;     /* Background white */
  --vf-light-gray: #f8f9fa; /* Light backgrounds */
  --vf-dark-gray: #222222;  /* Text color */
}
```

### Hero Section
Customize the hero section in **Appearance > Customize > Hero Section**:
- Hero Title (default: "Vane France")
- Hero Subtitle (default: "Perfumería Francesa de Alta Gama")

### Business Information
The footer automatically displays the business information:
- **Addresses**: Cl. 12 #13-99 a 13, 1, Bogotá & Cl. 12 #13-69 Local 102, Bogotá
- **Phone**: 319 3605666
- **Hours**: Monday-Saturday 9 AM-7 PM, Sunday Closed

### Widgets
Configure sidebar widgets in **Appearance > Widgets**:
- **Primary Sidebar**: Main blog sidebar
- **Footer Areas 1-3**: Three footer widget areas

## Template Files

### Main Templates
- `style.css` - Main stylesheet with theme information
- `functions.php` - Theme functions and features
- `header.php` - Site header and navigation
- `footer.php` - Site footer with business info
- `front-page.php` - Homepage template with hero and CTAs
- `index.php` - Blog posts listing
- `single.php` - Individual blog post template
- `archive.php` - Category/tag archive template
- `sidebar.php` - Sidebar with widgets

### Special Pages
- `page-catalogo-emprendedor.php` - Emprendedor catalog template
- `page-catalogo-cliente.php` - Cliente catalog template

### WooCommerce
- `woocommerce/content-product.php` - Product card with "Especial" badges

### Assets
- `assets/js/theme.js` - Theme JavaScript functionality
- `assets/img/` - Theme images (logo, products, storefront)

## Shortcodes

### Product Catalog
Display products by tag:
```
[vf_product_catalog tag="emprendedor" limit="12"]
[vf_product_catalog tag="cliente" limit="8"]
```

## Developer Notes

### Hooks and Filters
The theme provides several hooks for customization:
- `vane_france_before_header` - Before header content
- `vane_france_after_footer` - After footer content
- `vane_france_product_badge` - Customize product badges

### JavaScript Events
- `vf:theme:loaded` - Fired when theme JS is loaded
- `vf:product:added` - Fired when product is added to cart
- `vf:newsletter:subscribed` - Fired on newsletter subscription

### Performance Features
- **Optimized Loading**: Conditional script loading
- **Image Optimization**: WebP support and lazy loading
- **Minified Assets**: Compressed CSS and JS in production
- **Disabled Features**: Removes unused WordPress features (emojis, etc.)

## Browser Support
- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
- iOS Safari 12+
- Android Chrome 60+

## Changelog

### Version 1.0.0
- Initial release
- Dual catalog system
- WooCommerce integration
- WhatsApp floating button
- Blog with right sidebar
- French flag color scheme
- Responsive design
- Animation system
- Auto page creation

## Support

For theme support and customization:
- **Phone**: 319 3605666
- **Business Hours**: Monday-Saturday 9 AM-7 PM
- **Locations**: 
  - Cl. 12 #13-99 a 13, 1, Bogotá
  - Cl. 12 #13-69 Local 102, Bogotá

## License

This theme is licensed under the GPL v2 or later. You are free to modify and distribute it according to the GPL license terms.

---

**Vane France Theme** - Elegant French perfumery WordPress theme with WooCommerce integration.