# Vane France - WordPress Deliverable

Complete WordPress platform for Vane France perfumery featuring modern e-commerce functionality, AI chat assistant, and French-inspired design.

## 📋 Package Contents

This repository contains a complete WordPress solution with:

### 🎨 Theme: Vane France
**File:** `dist/vane-france-theme.zip`
- WooCommerce-ready theme with French flag color palette
- Dual catalog system for entrepreneurs and clients
- Blog with right sidebar layout
- WhatsApp integration
- Responsive design with animations

### 🔧 Plugin: VF Extras
**File:** `dist/vf-extras.zip`
- Custom dashboard with analytics
- Product management tools
- Gift system automation
- Stock management
- Custom "Ofertas" post type

### 🤖 Plugin: VF Amelie AI Chat
**File:** `dist/vf-amelie.zip`
- Diamond-themed AI chat assistant
- Floating widget and shortcode support
- Secure Flask API integration
- Multiple theme options

## 🚀 Quick Installation

### Prerequisites
- WordPress 5.0+ 
- PHP 7.4+
- WooCommerce plugin
- Wompi payment gateway (for Colombian payments)

### Installation Steps

1. **Install Theme**
   ```
   WordPress Admin → Appearance → Themes → Add New → Upload Theme
   Upload: dist/vane-france-theme.zip
   Activate: Vane France theme
   ```

2. **Install Plugins**
   ```
   WordPress Admin → Plugins → Add New → Upload Plugin
   Upload: dist/vf-extras.zip
   Upload: dist/vf-amelie.zip
   Activate both plugins
   ```

3. **Configure Settings**
   - Go to **Appearance → Customize** to set WhatsApp number
   - Configure **Vane France** dashboard settings
   - Set up **Amelie Chat** API connection

## ⚙️ Configuration Guide

### Theme Configuration

#### WhatsApp Integration
1. Navigate to **Appearance → Customize → Contact Settings**
2. Enter WhatsApp number with country code: `573193605666`
3. Save changes

#### Menu Setup
1. Go to **Appearance → Menus**
2. Create primary menu with:
   - Inicio (Home)
   - Plan Emprendedor 
   - Cliente
   - Blog
   - Contacto

#### Catalog Pages
The theme automatically creates:
- **Catálogo Emprendedor**: Products tagged "emprendedor"
- **Catálogo Cliente**: Products tagged "cliente"

### WooCommerce Setup

#### Product Configuration
1. **Create Product Tags**:
   - `emprendedor` - for entrepreneur discounts
   - `cliente` - for general customers

2. **Product Categories**:
   - Set up perfume categories
   - Upload category images

3. **Featured Products**:
   - Mark products as "featured" for homepage display

#### Payment Gateway
- Install Wompi plugin for Colombian payments
- Configure payment methods in WooCommerce settings

### VF Extras Plugin

#### Dashboard Access
- Navigate to **Vane France** in admin menu
- View sales analytics and reports
- Manage stock and offers

#### Gift System Setup
1. Go to **Vane France → Ajustes**
2. Set minimum gift threshold (default: 100,000 COP)
3. Select gift product
4. Choose required user role (optional)

### VF Amelie Chat Setup

#### API Configuration
1. Go to **Settings → Amelie Chat**
2. Enter API Base URL (your Flask server)
3. Add API secret if required
4. Test connection

#### Chat Customization
- Enable/disable floating chat
- Set welcome message
- Choose position and theme
- Configure auto-show behavior

## 📊 Business Information

The platform includes complete Vane France business details:

### Store Locations
- **Dirección 1**: Cl. 12 #13-99 a 13, 1, Bogotá
- **Dirección 2**: Cl. 12 #13-69 Local 102, Bogotá

### Contact Information
- **Teléfono**: 319 3605666
- **WhatsApp**: Configurable in theme settings

### Business Hours
- **Lunes a Sábado**: 9:00 AM - 7:00 PM
- **Domingo**: Cerrado

## 🎯 Key Features

### E-commerce Functionality
- **Dual Catalog System**: Separate products for entrepreneurs vs. clients
- **Special Badges**: "Especial" badges for entrepreneur products
- **Gift Automation**: Automatic gift addition based on cart value
- **Stock Management**: Quick inventory updates
- **Analytics Dashboard**: Sales and product performance tracking

### User Experience
- **WhatsApp Integration**: Direct customer support
- **AI Chat Assistant**: 24/7 automated customer service
- **Responsive Design**: Optimized for all devices
- **French Design**: Navy blue, white, and red color scheme
- **Smooth Animations**: Modern interaction effects

### Admin Features
- **Custom Dashboard**: Vane France admin panel
- **Reports & Analytics**: Revenue and order tracking
- **Stock Tools**: Bulk inventory management
- **Offer Management**: Special promotions system
- **Chat Settings**: AI assistant configuration

## 🛠️ Customization

### Color Scheme
The theme uses CSS custom properties for easy customization:
```css
:root {
  --vf-navy: #002395;
  --vf-white: #ffffff;
  --vf-red: #ed2939;
}
```

### Adding Custom Styles
1. Use **Appearance → Customize → Additional CSS**
2. Or create a child theme for extensive modifications

### Shortcodes Available
- `[vf_catalog type="emprendedor"]` - Entrepreneur products
- `[vf_catalog type="cliente"]` - Client products  
- `[vf_offers limit="6"]` - Display offers
- `[amelie_chat]` - Embed AI chat

## 🔧 API Requirements

### Amelie Chat Backend
The AI chat requires a Flask server with this endpoint:

```
POST /api/ai/chat
Content-Type: application/json
X-API-KEY: [optional]

Request: {"message": "user text"}
Response: {"reply": "AI response"}
```

Example Flask implementation:
```python
@app.route('/api/ai/chat', methods=['POST'])
def chat():
    data = request.json
    message = data.get('message', '')
    
    # Process with your AI model
    response = process_ai_message(message)
    
    return jsonify({'reply': response})
```

## 📱 Mobile Optimization

- **Responsive Grid**: Products adapt to screen size
- **Touch-Friendly**: Large buttons and touch targets
- **Mobile Menu**: Collapsible navigation
- **Mobile Chat**: Full-screen chat on small devices
- **Fast Loading**: Optimized images and code

## 🔒 Security Features

- **Input Sanitization**: All user inputs are cleaned
- **Nonce Verification**: WordPress security tokens
- **API Security**: Optional authentication for chat
- **SQL Injection Protection**: Prepared statements
- **XSS Prevention**: Output escaping

## 📈 Analytics Integration

### Google Analytics
The theme is ready for Google Analytics integration:
```javascript
gtag('event', 'purchase', {
  'transaction_id': 'ORDER_ID',
  'value': 'ORDER_VALUE',
  'currency': 'COP'
});
```

### Built-in Analytics
- Sales tracking in VF Extras dashboard
- Product view counting
- Chat interaction analytics
- User behavior insights

## 🐛 Troubleshooting

### Common Issues

#### Theme Not Loading Properly
- Check if all plugins are activated
- Verify PHP version compatibility
- Clear any caching plugins

#### Products Not Showing in Catalogs
- Ensure products have correct tags ("emprendedor" or "cliente")
- Check product visibility settings
- Verify WooCommerce is properly configured

#### Chat Not Working
- Test API connection in Amelie settings
- Check JavaScript console for errors
- Verify API server is accessible

#### WhatsApp Button Missing
- Configure WhatsApp number in Customizer
- Check theme options are saved
- Verify number format includes country code

### Performance Optimization
- Install caching plugin (W3 Total Cache)
- Optimize images for web
- Use CDN for better loading times
- Enable GZIP compression

## 📞 Support

For technical support or customization requests:
- Review plugin documentation in README files
- Check WordPress.org support forums
- Contact development team for custom modifications

## 📄 License

All components are licensed under GPL v2 or later, compatible with WordPress licensing.

## 🏆 Credits

- **Developed for**: Vane France Perfumery
- **Design**: French flag inspired color palette
- **Built with**: WordPress, WooCommerce, PHP, JavaScript
- **AI Integration**: Secure Flask API communication
- **Mobile**: Responsive design for all devices

---

**Version**: 1.0.0  
**Last Updated**: December 2024  
**WordPress Tested**: 6.4  
**PHP Required**: 7.4+