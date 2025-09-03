# Vane France WordPress Platform

Complete WordPress deliverable for Vane France perfumery with theme, plugins, and AI integration. This package provides a comprehensive e-commerce solution with dual catalog system, WooCommerce integration, and AI-powered chat support.

## 📦 Package Contents

### Theme: Vane France
- **Location**: `wordpress-theme/vane-france-theme/`
- **ZIP File**: `dist/vane-france-theme.zip`
- **Description**: Elegant French perfumery theme with navy blue dominant color scheme
- **Features**: Dual catalog system, WooCommerce integration, blog with sidebar, responsive design

### Plugin: VF Extras
- **Location**: `wordpress-plugins/vf-extras/`
- **ZIP File**: `dist/vf-extras.zip`
- **Description**: Business logic features including CPT Ofertas, reports, and gift program
- **Features**: Admin dashboard, sales reports, stock management, user roles

### Plugin: VF Amélie
- **Location**: `wordpress-plugins/vf-amelie/`
- **ZIP File**: `dist/vf-amelie.zip`
- **Description**: AI chat integration with diamond-themed floating widget
- **Features**: Flask backend connection, shortcode support, secure API communication

## 🚀 Quick Installation

### Step 1: Install Theme
1. Download `dist/vane-france-theme.zip`
2. In WordPress Admin: **Appearance > Themes > Add New > Upload Theme**
3. Upload ZIP file and click **Activate**

### Step 2: Install Plugins
1. Download `dist/vf-extras.zip` and `dist/vf-amelie.zip`
2. In WordPress Admin: **Plugins > Add New > Upload Plugin**
3. Upload each ZIP file and click **Activate** for both plugins

### Step 3: Install Required Plugin
1. Install **WooCommerce** from WordPress repository
2. Install **Wompi** payment plugin (available separately)
3. Complete WooCommerce setup wizard

## ⚙️ Configuration Guide

### Theme Configuration

#### 1. Basic Setup
```
WordPress Admin > Appearance > Customize
```
- **Site Identity**: Upload logo (recommended: 200x80px)
- **WhatsApp Settings**: Enter number with country code (+573193605666)
- **Hero Section**: Customize title and subtitle

#### 2. Menu Setup
```
WordPress Admin > Appearance > Menus
```
- Create **Primary Navigation** menu
- Create **Footer Navigation** menu
- Assign to appropriate locations

#### 3. Homepage Setup
```
WordPress Admin > Settings > Reading
```
- Set "Front page displays" to "A static page"
- Create new page for homepage or use existing

### VF Extras Plugin Configuration

#### 1. Access Settings
```
WordPress Admin > Vane France > Ajustes
```

#### 2. Contact Information
- **WhatsApp Number**: +573193605666 (with country code)
- **Social Media**: Instagram, Facebook, TikTok URLs
- **Business Hours**: Auto-populated in footer

#### 3. Gift Program Setup
- **Enable Gifts**: Check to activate automatic gift program
- **Minimum Amount**: Set threshold (default: $150,000)
- **Gift Product**: Select product to give as gift
- **Role Requirement**: Optional restriction to distribuidor users

### VF Amélie Plugin Configuration

#### 1. Access Settings
```
WordPress Admin > Vane France > Amélie Chat
```

#### 2. API Configuration
- **API Base URL**: Your Amélie Flask backend URL
- **API Secret**: Optional authentication key (keep secure)
- **Endpoint**: Plugin posts to `/api/ai/chat`

#### 3. Chat Customization
- **Show Floating Chat**: Enable sitewide floating button
- **Position**: Choose button placement (bottom-right recommended)
- **Colors**: Match Vane France brand colors
- **Messages**: Customize welcome and placeholder text

## 🏪 Business Information

### Contact Details
**Automatically included in theme footer:**

#### Addresses
- Cl. 12 #13-99 a 13, 1, Bogotá
- Cl. 12 #13-69 Local 102, Bogotá

#### Phone
- 319 3605666

#### Business Hours
- **Monday-Saturday**: 9:00 AM - 7:00 PM
- **Sunday**: Closed

## 🛍️ E-commerce Setup

### Product Configuration

#### 1. Create Product Tags
```
WordPress Admin > Products > Tags
```
- Create tag: `emprendedor` (for special pricing)
- Create tag: `cliente` (for regular customers)
- Create tag: `featured` (for homepage display)

#### 2. Product Categories
Create categories for better organization:
- Eau de Parfum
- Eau de Toilette
- Perfume
- Cologne
- Accessories

#### 3. Product Setup
- Add products with appropriate tags
- Products tagged "emprendedor" will show blue "Especial" badges
- Set product images, descriptions, and pricing

### User Roles

#### Distribuidor Role
- Automatically created on VF Extras activation
- Access to special pricing on "emprendedor" products
- Eligible for gift program (if configured)
- Support section in My Account

#### Customer Role
- Standard WordPress customer role
- Access to all "cliente" tagged products
- Standard pricing and features

## 📱 Features Overview

### Theme Features
- **French Flag Colors**: Navy blue (#002395), red (#ed2939), white
- **Dual Catalog System**: Separate pages for emprendedor/cliente
- **WooCommerce Integration**: Full e-commerce functionality
- **Blog with Sidebar**: Professional blog layout with right sidebar
- **WhatsApp Integration**: Floating button on product pages
- **Responsive Design**: Mobile-first approach
- **SEO Optimized**: Clean markup and performance optimizations

### VF Extras Features
- **CPT Ofertas**: Custom post type for managing special offers
- **Admin Dashboard**: Sales overview and business metrics
- **Reports**: Chart.js powered analytics and insights
- **Stock Management**: Quick inventory update tools
- **Gift Program**: Automatic gift product addition
- **Support Integration**: My Account support section

### VF Amélie Features
- **AI Chat Widget**: Diamond-themed floating chat button
- **Flask Integration**: Connects to existing Amélie backend
- **Shortcode Support**: `[amelie_chat]` for embedded chat
- **Secure Communication**: No exposed API keys
- **Message History**: Local storage of conversations
- **Customizable Appearance**: Brand color matching

## 🔧 Technical Specifications

### Requirements
- **WordPress**: 5.0 or higher
- **PHP**: 7.4 or higher
- **MySQL**: 5.6 or higher
- **WooCommerce**: 4.0 or higher

### Browser Support
- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
- iOS Safari 12+
- Android Chrome 60+

### Performance Features
- Optimized CSS and JavaScript
- Conditional script loading
- Image optimization support
- Caching compatibility
- CDN ready assets

## 📄 Usage Examples

### Product Catalog Shortcodes
```
[vf_product_catalog tag="emprendedor" limit="12"]
[vf_product_catalog tag="cliente" limit="8"]
[vf_product_catalog tag="featured" limit="6"]
```

### Offers Shortcode
```
[vf_offers limit="6" orderby="date" order="DESC"]
```

### Amélie Chat Shortcode
```
[amelie_chat]
[amelie_chat title="Consultas Especiales" height="500px"]
```

## 🔒 Security Features

### Theme Security
- Input sanitization and validation
- Nonce verification for forms
- Escaped output to prevent XSS
- No hardcoded credentials

### Plugin Security
- Server-side API requests only
- WordPress capability checks
- Prepared SQL statements
- Secure options storage

### Best Practices
- Regular updates recommended
- Use SSL certificate
- Strong admin passwords
- Keep WordPress core updated

## 🎨 Customization Options

### CSS Variables
The theme uses CSS custom properties for easy customization:

```css
:root {
  --vf-navy: #002395;
  --vf-red: #ed2939;
  --vf-white: #ffffff;
  --vf-light-gray: #f8f9fa;
  --vf-dark-gray: #222222;
}
```

### Hooks and Filters
Both plugins provide numerous hooks for developers:

```php
// Theme hooks
add_action('vane_france_before_header', 'custom_function');
add_filter('vane_france_product_badge', 'custom_badge_filter');

// VF Extras hooks
add_action('vf_extras_offer_created', 'custom_offer_handler');
add_filter('vf_extras_gift_minimum', 'custom_gift_amount');

// VF Amélie hooks
add_action('amelie_chat_message_sent', 'track_chat_usage');
```

## 🐛 Troubleshooting

### Common Issues

#### Theme Not Loading Properly
1. Check if all files were uploaded correctly
2. Verify WordPress meets minimum requirements
3. Deactivate other plugins to check for conflicts
4. Switch to default theme temporarily to isolate issue

#### Products Not Showing Badges
1. Ensure products have correct tags (emprendedor/cliente)
2. Check WooCommerce is properly activated
3. Verify theme functions are working
4. Clear any caching plugins

#### Chat Not Connecting
1. Verify Amélie backend URL is correct
2. Check API endpoint is accessible
3. Test API key authentication if used
4. Review WordPress error logs

#### Gift Program Not Working
1. Check gift program is enabled in settings
2. Verify minimum amount and gift product are set
3. Ensure user meets role requirements
4. Test with different cart amounts

### Getting Help
- **Documentation**: Check individual README files for each component
- **Phone Support**: 319 3605666 (Mon-Sat 9AM-7PM)
- **Business Locations**: See contact information above

## 📊 Analytics and Tracking

### Built-in Analytics
- **VF Extras**: Product view tracking, sales reports, popular products
- **VF Amélie**: Chat usage analytics, message statistics

### Google Analytics Integration
Both plugins support Google Analytics event tracking:
- E-commerce events
- Chat interactions
- User engagement metrics

### Privacy Compliance
- No external tracking by default
- Local storage for chat history
- GDPR considerations implemented
- User data protection measures

## 🔄 Updates and Maintenance

### Theme Updates
- Monitor WordPress updates for compatibility
- Test on staging site before production updates
- Backup customizations before major updates

### Plugin Updates
- Check for WordPress and WooCommerce compatibility
- Update dependencies as needed
- Monitor security releases

### Regular Maintenance Tasks
- **Weekly**: Check for plugin/theme updates
- **Monthly**: Review sales reports and analytics
- **Quarterly**: Backup site and review security
- **Annually**: Performance audit and optimization

## 📞 Support and Contact

### Technical Support
- **Primary Contact**: 319 3605666
- **Business Hours**: Monday-Saturday, 9:00 AM - 7:00 PM
- **Emergency**: Use WhatsApp for urgent issues

### Business Locations
Visit us at either location:
- **Location 1**: Cl. 12 #13-99 a 13, 1, Bogotá
- **Location 2**: Cl. 12 #13-69 Local 102, Bogotá

### Online Support
- **Chat**: Use Amélie chat widget on website
- **Social Media**: Follow on Instagram, Facebook, TikTok
- **Email**: Contact through website contact forms

## ⚖️ License Information

### Theme License
- **License**: GPL v2 or later
- **Commercial Use**: Permitted
- **Modifications**: Allowed
- **Distribution**: Allowed under GPL terms

### Plugin Licenses
- **VF Extras**: GPL v2 or later
- **VF Amélie**: GPL v2 or later
- **Third-party Libraries**: Chart.js (MIT License)

### Usage Rights
You are free to:
- Use commercially
- Modify and customize
- Distribute (under GPL terms)
- Create derivative works

## 🏆 Credits and Acknowledgments

### Development Team
- **Vane France**: Business requirements and design direction
- **WordPress Community**: Core platform and plugins
- **WooCommerce**: E-commerce functionality
- **Chart.js**: Analytics visualization

### Special Thanks
- French perfumery industry for inspiration
- WordPress developer community
- Open source contributors
- Beta testers and early adopters

---

**Vane France WordPress Platform** - Complete e-commerce solution for French perfumery with AI integration and business management tools.

*Version 1.0.0 - 2024*