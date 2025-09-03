# VF Amélie Plugin

AI chat integration for Vane France with diamond-themed floating chat widget and shortcode support.

## Features

### Diamond-Themed Chat Widget
- **Floating Chat Button**: Elegant diamond-shaped button with shimmer animations
- **Position Options**: Bottom-right, bottom-left, top-right, or top-left positioning
- **Responsive Design**: Adapts to mobile devices and different screen sizes
- **Custom Colors**: Configurable primary and accent colors matching Vane France brand

### AI Chat Integration
- **Flask Backend Connection**: Connects to existing Amélie Flask backend from PR #1
- **Secure API Communication**: Optional API key authentication via X-API-KEY header
- **Real-time Messaging**: AJAX-powered chat with typing indicators
- **Message History**: Local storage of chat conversations

### Floating Chat Features
- **Auto-show Option**: Configurable sitewide floating chat display
- **Minimize/Close**: Users can minimize or close the chat window
- **Notification Indicator**: Visual notification dot when minimized
- **Welcome Message**: Customizable greeting from Amélie
- **Typing Indicators**: Shows when Amélie is responding

### Shortcode Support
- **Embedded Chat**: `[amelie_chat]` shortcode for inserting chat anywhere
- **Customizable Appearance**: Configurable title, height, and width
- **Multiple Instances**: Support for multiple chat widgets on same page
- **New Chat Button**: Reset conversation functionality

### Security Features
- **No Hardcoded Secrets**: All API keys stored securely in WordPress options
- **Server-side Requests**: API calls made from WordPress server, not frontend
- **Nonce Protection**: WordPress nonce verification for all AJAX requests
- **Input Sanitization**: All user inputs properly sanitized and validated

### Admin Interface
- **Settings Page**: Comprehensive configuration under Vane France menu
- **Visual Customization**: Color picker for brand matching
- **API Configuration**: Easy setup of backend connection
- **Usage Documentation**: Built-in help and examples

## Installation

### Quick Install
1. Download `vf-amelie.zip` from the `/dist` directory
2. Go to **Plugins > Add New > Upload Plugin** in WordPress admin
3. Upload the ZIP file and activate the plugin

### Manual Install
1. Extract files to `/wp-content/plugins/vf-amelie/`
2. Activate the plugin in **Plugins** menu

## Configuration

### API Setup
1. **Access Settings**: Go to **Vane France > Amélie Chat** (or standalone menu if vf-extras not active)
2. **Configure API**:
   - **API Base URL**: Enter your Amélie backend URL (e.g., `https://your-api.com`)
   - **API Secret**: Optional authentication key for secure communication
3. **Test Connection**: The plugin will POST to `[URL]/api/ai/chat` with JSON `{"message": "user text"}`

### Chat Configuration
1. **Display Options**:
   - **Show Floating Chat**: Toggle sitewide floating chat button
   - **Button Position**: Choose from 4 corner positions
   
2. **Customization**:
   - **Chat Title**: Default "Amélie - Asistente Virtual"
   - **Welcome Message**: Amélie's greeting message
   - **Placeholder Text**: Input field placeholder
   
3. **Visual Settings**:
   - **Primary Color**: Main brand color (default: #002395)
   - **Accent Color**: Highlight color (default: #ed2939)

### Backend Requirements
Your Amélie Flask backend should accept:
- **Method**: POST
- **Endpoint**: `/api/ai/chat`
- **Content-Type**: `application/json`
- **Request Body**: `{"message": "user message text"}`
- **Optional Header**: `X-API-KEY: your-secret-key`

Expected response format:
```json
{
  "response": "Amélie's reply text"
}
```

Alternative response formats also supported:
- `{"message": "reply"}` 
- `{"reply": "reply"}`

## Usage

### Shortcode

#### Basic Usage
```
[amelie_chat]
```

#### With Parameters
```
[amelie_chat title="Consultas Especiales" height="500px" width="100%"]
```

**Available Parameters:**
- `title`: Custom chat title (default: from settings)
- `height`: Chat container height (default: "400px")
- `width`: Chat container width (default: "100%")

### JavaScript API

#### Global Functions
```javascript
// Send message in embedded chat
amelieSendMessage('chat-id');

// Start new conversation
amelieNewChat('chat-id');

// Access chat manager (for advanced usage)
window.AmelieChat.addMessage('Hello!', 'bot');
```

#### Event Tracking
The plugin tracks various events for analytics:
- `chat_opened` - When user opens floating chat
- `chat_closed` - When user closes chat
- `message_sent` - When user sends message
- `api_error` - When API request fails

## Customization

### CSS Variables
The plugin uses CSS custom properties for easy theming:

```css
:root {
  --amelie-primary: #002395;      /* Primary brand color */
  --amelie-accent: #ed2939;       /* Accent color */
  --amelie-white: #ffffff;        /* Background color */
  --amelie-light-gray: #f8f9fa;   /* Light backgrounds */
  --amelie-dark-gray: #333333;    /* Text color */
  --amelie-gradient: linear-gradient(135deg, var(--amelie-primary) 0%, var(--amelie-accent) 100%);
}
```

### Custom Styling
Override default styles by adding CSS to your theme:

```css
/* Customize chat button */
.amelie-chat-button {
  width: 70px;
  height: 70px;
}

/* Customize chat window */
.amelie-chat-window {
  width: 400px;
  height: 600px;
}

/* Customize message bubbles */
.amelie-bot-message .amelie-message-content {
  background: #f0f0f0;
  border: 1px solid #ddd;
}
```

### Message Formatting
Messages support basic formatting:
- **Line breaks**: Converted to `<br>` tags
- **URLs**: Automatically converted to clickable links
- **Email addresses**: Converted to mailto links
- **HTML escaping**: User input is safely escaped

## Developer Information

### Hooks & Filters

#### WordPress Hooks
- `amelie_chat_message_sent` - Fired when user sends message
- `amelie_chat_response_received` - Fired when bot responds
- `amelie_chat_error` - Fired on API errors

#### JavaScript Hooks
```javascript
// Listen for chat events
if (typeof wp !== 'undefined' && wp.hooks) {
  wp.hooks.addAction('amelie_chat_event', 'myPlugin', function(eventName, data) {
    console.log('Chat event:', eventName, data);
  });
}
```

### AJAX Endpoints

#### Chat Message Handler
- **Action**: `amelie_chat`
- **Method**: POST
- **Parameters**:
  - `message`: User message text
  - `nonce`: WordPress nonce for security
- **Response**: JSON with `success` and `data.message`

### Database Storage

#### WordPress Options
- `amelie_api_base_url`: Backend API base URL
- `amelie_api_secret`: API authentication secret
- `amelie_show_floating`: Boolean for floating chat display
- `amelie_chat_title`: Chat window title
- `amelie_welcome_message`: Amélie's welcome message
- `amelie_placeholder_text`: Input placeholder text
- `amelie_button_position`: Chat button position
- `amelie_primary_color`: Primary theme color
- `amelie_accent_color`: Accent theme color

#### Local Storage (Browser)
- `amelie_chat_history`: User's chat message history (JSON array)

### File Structure
```
vf-amelie/
├── amelie.php             # Main plugin file
├── README.md              # Documentation
├── assets/                # Plugin assets
│   ├── amelie.css        # Chat widget styles
│   └── amelie.js         # Chat functionality
└── languages/            # Translation files
    └── vf-amelie.pot     # Translation template
```

## Security

### API Security
- **Server-side Requests**: API calls made from WordPress server, not user browser
- **No Exposed Secrets**: API keys never sent to frontend or exposed in HTML
- **Header Authentication**: Optional X-API-KEY header for backend authentication
- **Input Validation**: All user inputs sanitized before API transmission

### WordPress Security
- **Nonce Verification**: All AJAX requests protected with WordPress nonces
- **Capability Checks**: Admin functions require appropriate user permissions
- **Data Sanitization**: All stored options and user inputs properly sanitized
- **XSS Prevention**: Message content escaped to prevent script injection

### Privacy Considerations
- **Local Storage**: Chat history stored locally in user's browser
- **No External Tracking**: No third-party analytics or tracking by default
- **Optional Analytics**: Event tracking only if Google Analytics present
- **Data Retention**: No persistent server-side storage of conversations

## Accessibility

### WCAG Compliance
- **Keyboard Navigation**: Full keyboard accessibility for all interactions
- **Screen Reader Support**: Proper ARIA labels and semantic HTML
- **High Contrast**: Support for high contrast display preferences
- **Reduced Motion**: Respects `prefers-reduced-motion` setting
- **Focus Management**: Proper focus handling for modal-style chat

### Mobile Accessibility
- **Touch Targets**: Minimum 44px touch targets for mobile devices
- **Responsive Text**: Text scaling support for accessibility preferences
- **Voice Input**: Compatible with voice input and speech recognition
- **Swipe Gestures**: No essential functionality requires specific gestures

## Performance

### Optimization Features
- **Conditional Loading**: Assets only loaded when needed
- **Lightweight**: Minimal CSS and JavaScript footprint
- **Local Storage**: Message history cached locally to reduce API calls
- **Efficient DOM**: Minimal DOM manipulation and efficient event handling
- **CDN Ready**: Assets can be served from CDN for better performance

### Browser Support
- **Modern Browsers**: Chrome 60+, Firefox 55+, Safari 12+, Edge 79+
- **Mobile Browsers**: iOS Safari 12+, Android Chrome 60+
- **Graceful Degradation**: Fallback for older browsers
- **Progressive Enhancement**: Core functionality works without JavaScript

## Troubleshooting

### Common Issues

#### Chat Not Appearing
1. Check if "Show Floating Chat" is enabled in settings
2. Verify API Base URL is configured
3. Check browser console for JavaScript errors
4. Ensure plugin is activated and assets are loading

#### API Connection Issues
1. Verify backend URL is correct and accessible
2. Check API endpoint returns proper JSON response
3. Test API key authentication if configured
4. Review WordPress error logs for detailed messages

#### Styling Issues
1. Check for CSS conflicts with theme or other plugins
2. Use browser developer tools to inspect styling
3. Verify custom colors are properly formatted
4. Test with default theme to isolate issues

### Debug Mode
Enable WordPress debug mode for detailed error information:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

### Support
For technical support:
- **Phone**: 319 3605666
- **Hours**: Monday-Saturday 9 AM-7 PM
- **Email**: Available through contact form

## License

This plugin is licensed under the GPL v2 or later. You are free to modify and distribute it according to the GPL license terms.

---

**VF Amélie Plugin** - AI-powered chat integration with diamond-themed interface for Vane France perfumery platform.