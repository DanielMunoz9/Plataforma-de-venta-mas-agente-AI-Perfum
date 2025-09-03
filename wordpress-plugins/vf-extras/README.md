# VF Extras Plugin

Comprehensive WordPress plugin for Vane France that adds essential e-commerce features, custom post types, admin dashboard, reporting, and gift management system.

## Features

### Custom Post Types
- **Ofertas (vf_offer)**: Special offers management system
- Custom fields and meta boxes for offer details
- Archive and single templates support

### Admin Dashboard
- **Main Dashboard**: Overview with key metrics
- **Reports**: Revenue and orders analytics with Chart.js
- **Settings**: WhatsApp integration and social media links
- **Stock Management**: Quick inventory updates
- **Gift System**: Automatic gift assignment based on cart value

### Shortcodes
- `[vf_offers limit="6"]`: Display offers grid

### WooCommerce Integration
- Product view tracking
- Automatic gift system based on cart subtotal
- Custom user role "Distribuidor"
- Enhanced My Account section with technical support

### Key Components

#### Dashboard Features
- Today's sales and orders count
- Low stock product alerts
- Active offers counter
- Recent orders display

#### Reports System
- Last 30 days revenue chart (Line chart)
- Last 30 days orders chart (Bar chart)
- Top 10 products by views
- Product performance analytics

#### Settings Management
- WhatsApp number configuration
- Social media links (Facebook, Instagram, Twitter)
- Gift system configuration
  - Minimum threshold amount
  - Gift product selection
  - Required user role (optional)

#### Stock Management
- Quick stock updates by product ID
- Current stock levels overview
- Low stock alerts
- Bulk inventory management

#### My Account Integration
- "Soporte Técnico" endpoint
- Direct WhatsApp support link
- Business hours and contact information

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through WordPress admin
3. Configure settings in **Vane France > Ajustes**
4. Set up WooCommerce integration if needed

## Configuration

### WhatsApp Integration
Navigate to **Vane France > Ajustes** and configure:
- WhatsApp number with country code (e.g., 573193605666)

### Gift System
Configure automatic gifts in **Ajustes**:
- Set minimum cart amount for gift activation
- Select gift product from existing products
- Set required user role (optional)

### Social Media
Add your social media URLs:
- Facebook page URL
- Instagram profile URL
- Twitter profile URL

## Usage

### Ofertas Management
1. Go to **Vane France > Ofertas**
2. Create new offers with title, content, and featured image
3. Display offers using `[vf_offers]` shortcode

### Stock Management
1. Access **Vane France > Stock Rápido**
2. Update stock by product ID
3. Monitor current stock levels
4. View low stock alerts

### Reports & Analytics
1. Visit **Vane France > Reportes**
2. View revenue and orders charts
3. Analyze top products by views
4. Export data for further analysis

## Technical Details

### Database
- Adds custom post type `vf_offer`
- Tracks product views in `vf_views` meta field
- Stores configuration in WordPress options table

### Hooks & Filters
- `woocommerce_single_product_summary`: Track product views
- `woocommerce_cart_calculate_fees`: Add automatic gifts
- `woocommerce_account_menu_items`: Add support menu item

### User Roles
- Adds "Distribuidor" role with basic permissions
- Supports role-based gift system

### Security
- Nonce verification for all forms
- Input sanitization and validation
- Capability checks for admin functions

## Requirements

- WordPress 5.0+
- PHP 7.4+
- WooCommerce plugin (for e-commerce features)

## File Structure

```
vf-extras/
├── vf-extras.php          # Main plugin file
└── README.md              # This documentation
```

## Support

For support and customization, contact the Vane France development team.

## Changelog

### Version 1.0.0
- Initial release
- Dashboard with key metrics
- Reports with Chart.js integration
- Stock management system
- Gift automation
- My Account integration
- Ofertas custom post type
- Complete admin interface