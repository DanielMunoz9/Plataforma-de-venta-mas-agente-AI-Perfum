# Vane France WordPress Theme

A premium WooCommerce-ready WordPress theme designed specifically for Vane France perfumery, featuring French flag color palette and comprehensive e-commerce functionality.

## Features

### Design & Layout
- **French Flag Color Palette**: Navy blue (#002395), white (#ffffff), and red (#ed2939)
- **Responsive Design**: Mobile-first approach with optimized layouts for all devices
- **Hero Section**: Minimal design with animated CTAs for "Plan Emprendedor" and "Cliente"
- **Blog Layout**: Right sidebar with card-based design
- **Modern Typography**: Playfair Display serif font for elegance

### WooCommerce Integration
- **Dual Catalog System**: Separate catalogs for entrepreneurs and clients via shortcodes
- **Product Badges**: Blue "Especial" badges for products tagged "emprendedor"
- **Enhanced Product Cards**: Quick view, wishlist, ratings, and meta information
- **Unified Cart/Checkout**: Single checkout process for both catalog types
- **Product Categories**: Beautiful category showcase with hover effects

### Special Features
- **WhatsApp Integration**: Floating button and product-specific WhatsApp links
- **Scroll Animations**: IntersectionObserver-based animations for smooth user experience
- **Search Enhancement**: Improved search functionality with suggestions
- **Newsletter Integration**: Subscription forms with validation
- **Social Media**: Ready-to-use social media links and sharing buttons

### Blog Features
- **Right Sidebar Layout**: Customizable widget areas
- **Post Cards**: Modern card design with featured images
- **Author Bios**: Author information display
- **Social Sharing**: Built-in social sharing buttons
- **Related Posts**: Contextual content recommendations

## Installation

### Requirements
- WordPress 5.0 or higher
- PHP 7.4 or higher
- WooCommerce plugin (for e-commerce features)
- Wompi payment gateway plugin (sold separately)

### Installation Steps

1. **Upload Theme**
   - Download the `vane-france-theme.zip` from the `/dist` folder
   - Go to WordPress Admin → Appearance → Themes
   - Click "Add New" → "Upload Theme"
   - Choose the ZIP file and click "Install Now"
   - Click "Activate"

2. **Required Plugins**
   ```
   - WooCommerce (free)
   - Wompi Payment Gateway (separate installation)
   - VF Extras Plugin (included in distribution)
   - VF Amelie Plugin (included in distribution)
   ```

3. **Initial Setup**
   - The theme automatically creates "Catálogo Emprendedor" and "Catálogo Cliente" pages
   - Configure WhatsApp number in Appearance → Customize → Contact Settings
   - Set up your logo in Appearance → Customize → Site Identity
   - Configure WooCommerce settings and payment methods

## Configuration

### Theme Customizer Options

Navigate to **Appearance → Customize** to configure:

#### Contact Settings
- **WhatsApp Number**: Enter with country code (e.g., 573193605666)

#### Hero Section
- **Hero Title**: Main title displayed on homepage
- **Hero Subtitle**: Subtitle text below main title

#### Site Identity
- **Custom Logo**: Upload your logo
- **Site Title & Tagline**: Basic site information

### Theme Options Page

Navigate to **Appearance → Theme Options** for:
- WhatsApp number configuration
- Additional theme-specific settings

### Navigation Menus

Configure menus in **Appearance → Menus**:
- **Primary Menu**: Main navigation in header
- **Footer Menu**: Links in footer area

### Widget Areas

Configure widgets in **Appearance → Widgets**:
- **Blog Sidebar**: Right sidebar for blog pages
- **Footer Area 1**: First footer widget area
- **Footer Area 2**: Second footer widget area

## Shortcodes

### Catalog Shortcode
Display products by type:
```php
[vf_catalog type="emprendedor" limit="12"]
[vv_catalog type="cliente" limit="8"]
```

**Parameters:**
- `type`: "emprendedor" or "cliente"
- `limit`: Number of products to display (default: 12)

## Customization

### Colors
The theme uses CSS custom properties for easy color customization:
```css
:root {
  --vf-navy: #002395;
  --vf-white: #ffffff;
  --vf-red: #ed2939;
  --vf-light-gray: #f8f9fa;
  --vf-dark-gray: #343a40;
}
```

### Adding Custom Styles
1. Use **Appearance → Customize → Additional CSS**
2. Or create a child theme for extensive modifications

### Asset Replacement

#### Replace Placeholder Images
1. Go to **Media → Library**
2. Upload your actual images
3. Update the following files via FTP if needed:
   - `/assets/img/logo.png` - Your logo
   - `/assets/img/storefront.jpg` - Store front image
   - `/assets/img/product-1.jpg` - Product placeholder 1
   - `/assets/img/product-2.jpg` - Product placeholder 2

## Business Information

The theme includes the following business information in the footer:

### Addresses
- Cl. 12 #13-99 a 13, 1, Bogotá
- Cl. 12 #13-69 Local 102, Bogotá

### Contact
- **Phone**: 319 3605666
- **WhatsApp**: Configurable via theme options

### Hours
- Monday-Saturday: 9 a.m.–7 p.m.
- Sunday: Closed

## WooCommerce Setup

### Product Configuration
1. **Create Product Tags**:
   - "emprendedor" - for entrepreneur products
   - "cliente" - for client products

2. **Product Categories**:
   - Set up relevant categories for your products
   - Upload category images for better display

3. **Featured Products**:
   - Mark products as "featured" to display on homepage

### Payment Gateway
- Install and configure Wompi payment gateway
- The theme is optimized to work with Wompi for Colombian payments

## Page Templates

### Custom Page Templates
- **Front Page**: Homepage with hero section and featured content
- **Catálogo Emprendedor**: Entrepreneur catalog page
- **Catálogo Cliente**: Client catalog page

### Blog Templates
- **Index**: Blog listing with sidebar
- **Archive**: Category/tag archive pages
- **Single**: Individual blog post page
- **Sidebar**: Customizable sidebar content

## Troubleshooting

### Common Issues

#### WhatsApp Button Not Showing
- Ensure WhatsApp number is configured in Customizer
- Check that the number includes country code

#### Catalog Pages Empty
- Verify WooCommerce is active
- Check that products are published and visible
- Ensure products have correct tags ("emprendedor" or "cliente")

#### Images Not Loading
- Replace placeholder image files with actual images
- Check file permissions on uploads directory
- Verify image URLs in Media Library

#### Styling Issues
- Clear any caching plugins
- Check for plugin conflicts
- Ensure theme is fully activated

### Performance Optimization
- Use image compression plugins
- Install caching plugin (W3 Total Cache, WP Rocket)
- Optimize database regularly
- Use CDN for better loading times

## Support & Updates

### Documentation
- Theme documentation is included in this README
- WooCommerce documentation: https://docs.woocommerce.com/

### Customization Services
For custom modifications or additional features, contact the development team.

### Compatibility
- Tested with WordPress 6.4
- Compatible with WooCommerce 8.x
- PHP 7.4+ required
- Modern browsers supported

## File Structure

```
vane-france-theme/
├── assets/
│   ├── img/          # Placeholder images
│   └── js/           # Theme JavaScript
├── woocommerce/      # WooCommerce templates
├── style.css         # Main stylesheet
├── functions.php     # Theme functions
├── header.php        # Header template
├── footer.php        # Footer template
├── front-page.php    # Homepage template
├── index.php         # Blog index
├── single.php        # Single post
├── archive.php       # Archive pages
├── sidebar.php       # Sidebar content
├── page-catalogo-emprendedor.php  # Entrepreneur catalog
├── page-catalogo-cliente.php      # Client catalog
└── README.md         # This file
```

## License

This theme is licensed under GPL v2 or later.

## Credits

- Developed for Vane France perfumery
- Uses Playfair Display font from Google Fonts
- Built with modern WordPress and WooCommerce standards
- Responsive design optimized for all devices