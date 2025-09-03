# VF Extras Plugin

Business logic features for Vane France including CPT Ofertas, reports, settings, and gift program.

## Features

### Custom Post Type: Ofertas
- **Custom Post Type**: `vf_offer` for managing special offers
- **Shortcode**: `[vf_offers limit="6"]` to display offers anywhere
- **Full CRUD**: Create, read, update, and delete offers from WordPress admin
- **Featured Images**: Support for offer thumbnails and images
- **Archive Pages**: Public archive and single offer pages

### Admin Dashboard
- **Vane France Menu**: Centralized admin menu for all business features
- **Sales Overview**: Today's sales, monthly sales, and total orders
- **Quick Actions**: Fast access to create offers, update stock, and view reports
- **Popular Products**: Top products by views with real-time statistics

### Reports & Analytics
- **Revenue Charts**: 30-day revenue and order trends using Chart.js
- **Top Products**: Top 10 products by views with interactive bar charts
- **Real-time Data**: AJAX-powered charts that update without page refresh
- **Export Ready**: Data structured for easy export and analysis

### Settings Management
- **Contact Information**: WhatsApp number, Instagram, Facebook, TikTok URLs
- **Gift Program**: Automated gift product addition based on cart value
- **Role-based Gifts**: Optional role requirements (distribuidor only)
- **Minimum Amounts**: Configurable minimum purchase for gifts

### Gift Program
- **Automatic Addition**: Auto-adds gift products when cart minimum is reached
- **Role-based Logic**: Optional restriction to distribuidor role users
- **Smart Removal**: Removes gifts if cart falls below minimum
- **Configurable Products**: Any WooCommerce product can be a gift

### User Roles
- **Distribuidor Role**: Special role created on plugin activation
- **Role Permissions**: Basic read permissions with extensible capabilities
- **Gift Eligibility**: Optional gift program restriction to distributors

### My Account Integration
- **Support Endpoint**: New "Soporte Técnico" section in WooCommerce My Account
- **WhatsApp Integration**: Direct link to WhatsApp support with pre-filled message
- **Contact Information**: Complete business contact details
- **Professional Layout**: Matches WooCommerce account page styling

### Stock Management
- **Quick Stock Tool**: Rapid inventory updates by product ID
- **Bulk Updates**: Update multiple products simultaneously
- **Real-time Lookup**: Live product name and current stock display
- **AJAX Interface**: Smooth user experience without page reloads

### Product Analytics
- **View Tracking**: Automatic tracking of product page views
- **Meta Storage**: Views stored as post meta `vf_views`
- **Popular Products**: Identification of most viewed products
- **Report Integration**: View data integrated into admin reports

## Installation

### Quick Install
1. Download `vf-extras.zip` from the `/dist` directory
2. Go to **Plugins > Add New > Upload Plugin** in WordPress admin
3. Upload the ZIP file and activate the plugin

### Manual Install
1. Extract files to `/wp-content/plugins/vf-extras/`
2. Activate the plugin in **Plugins** menu

## Configuration

### Initial Setup
1. **Activate Plugin**: The plugin will automatically create the "distribuidor" role
2. **Configure Settings**: Go to **Vane France > Ajustes** to set up:
   - WhatsApp number (format: +573193605666)
   - Social media URLs (Instagram, Facebook, TikTok)
   - Gift program settings

### Gift Program Setup
1. **Enable Program**: Check "Habilitar Regalos" in settings
2. **Set Minimum**: Configure minimum purchase amount (default: $150,000)
3. **Select Gift Product**: Choose which product to give as gift
4. **Role Requirement**: Optionally restrict to distribuidor users only

### Creating Offers
1. Go to **Vane France > Ofertas > Add New**
2. Create offer with title, content, and featured image
3. Publish the offer
4. Display offers using `[vf_offers]` shortcode

## Usage

### Shortcodes

#### Display Offers
```
[vf_offers limit="6" orderby="date" order="DESC"]
```

**Parameters:**
- `limit`: Number of offers to display (default: 6)
- `orderby`: Sort field (date, title, menu_order)
- `order`: Sort direction (ASC, DESC)

### Admin Functionality

#### Dashboard Access
- Navigate to **Vane France** in admin menu
- View sales overview and quick actions
- Access all plugin features from submenu

#### Reports
- Go to **Vane France > Reportes**
- View 30-day revenue and order charts
- See top 10 products by views
- Charts load via AJAX for real-time data

#### Stock Management
- Access **Vane France > Stock Rápido**
- Enter product IDs to add to update list
- Set new stock quantities
- Update all products at once

### Frontend Features

#### My Account Integration
- Users see "Soporte Técnico" in account menu
- Direct WhatsApp contact with pre-filled support message
- Complete business contact information
- Professional support experience

#### Gift Program
- Automatic gift addition when cart minimum reached
- Smart gift removal when cart drops below minimum
- Role-based eligibility (if configured)
- Seamless WooCommerce integration

## Developer Information

### Hooks & Filters

#### Actions
- `vf_extras_activated` - Fired on plugin activation
- `vf_extras_offer_created` - Fired when new offer is created
- `vf_extras_gift_added` - Fired when gift product is added to cart

#### Filters
- `vf_extras_gift_minimum` - Filter gift minimum amount
- `vf_extras_gift_products` - Filter available gift products
- `vf_extras_reports_data` - Filter reports data before display

### Database

#### Options
- `vf_whatsapp_number` - WhatsApp contact number
- `vf_instagram_url` - Instagram profile URL
- `vf_facebook_url` - Facebook page URL
- `vf_tiktok_url` - TikTok profile URL
- `vf_gift_enabled` - Gift program enabled status
- `vf_gift_minimum` - Minimum purchase for gifts
- `vf_gift_product_id` - Product ID for gifts
- `vf_gift_required_role` - Required role for gifts

#### Post Meta
- `vf_views` - Product view count (stored on products)

#### User Roles
- `distribuidor` - Special distributor role with basic permissions

### AJAX Endpoints

#### Admin AJAX
- `vf_update_stock` - Update product stock quantities
- `vf_get_reports_data` - Fetch reports data for charts
- `vf_get_product_data` - Get product info for stock tool

### File Structure
```
vf-extras/
├── vf-extras.php          # Main plugin file
├── README.md              # Documentation
├── assets/                # Plugin assets (CSS, JS)
│   ├── admin.css         # Admin styling
│   ├── admin.js          # Admin JavaScript
│   └── frontend.css      # Frontend styling
└── languages/            # Translation files
    └── vf-extras.pot     # Template for translations
```

## Requirements

### WordPress
- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

### Dependencies
- **WooCommerce 4.0+**: Required for e-commerce features
- **jQuery**: For admin interface interactions
- **Chart.js**: Loaded via CDN for reports charts

### Recommended
- **Vane France Theme**: For optimal integration and styling
- **SSL Certificate**: For secure WhatsApp and social media links

## Compatibility

### WooCommerce Versions
- WooCommerce 4.0+
- WooCommerce 5.0+
- WooCommerce 6.0+
- WooCommerce 7.0+
- WooCommerce 8.0+

### WordPress Versions
- WordPress 5.0+
- WordPress 5.5+
- WordPress 6.0+
- WordPress 6.1+
- WordPress 6.2+
- WordPress 6.3+
- WordPress 6.4+

## Security Features

### Data Validation
- All user inputs are sanitized and validated
- AJAX requests protected with WordPress nonces
- Capability checks for admin functions
- SQL injection prevention with prepared statements

### Permission Checks
- Admin functionality requires `manage_options` capability
- Role-based access to distributor features
- Secure AJAX endpoints with proper authentication

## Performance

### Optimizations
- Efficient database queries with proper indexing
- AJAX loading for dynamic content
- Conditional script loading (admin vs frontend)
- Optimized shortcode with caching considerations

### Best Practices
- Minimal frontend footprint
- Admin scripts only loaded on relevant pages
- Database queries optimized for performance
- Proper use of WordPress caching mechanisms

## Troubleshooting

### Common Issues

#### Gift Products Not Adding
1. Check if gift program is enabled in settings
2. Verify minimum amount is correctly set
3. Ensure gift product exists and is in stock
4. Check user role requirements

#### Reports Not Loading
1. Verify user has admin privileges
2. Check browser console for JavaScript errors
3. Ensure Chart.js is loading correctly
4. Check AJAX endpoint accessibility

#### Stock Tool Issues
1. Verify product IDs are correct
2. Check user permissions (manage_options required)
3. Ensure WooCommerce is active and products exist
4. Test AJAX functionality

### Debug Mode
Enable WordPress debug mode to see detailed error information:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## License

This plugin is licensed under the GPL v2 or later. You are free to modify and distribute it according to the GPL license terms.

---

**VF Extras Plugin** - Essential business logic features for Vane France perfumery platform.