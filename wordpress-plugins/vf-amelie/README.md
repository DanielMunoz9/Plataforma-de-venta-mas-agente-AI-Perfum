# VF Amélie - AI Chat Assistant

AI-powered chat assistant plugin for Vane France with diamond-themed design, floating widget, and secure API integration.

## Features

### 🤖 AI Chat Integration
- Secure API communication with Flask backend
- Configurable API endpoints and authentication
- Real-time chat responses
- Error handling and retry mechanisms

### 💎 Diamond-Themed Design
- Premium diamond visual theme
- French flag color palette integration
- Smooth animations and transitions
- Responsive design for all devices

### 🚀 Multiple Chat Interfaces
- **Floating Chat Widget**: Always accessible bottom-corner chat
- **Shortcode Integration**: `[amelie_chat]` for embedded chat
- **Customizable Positioning**: 4 corner positions available
- **Theme Variations**: Diamond, Modern, and Classic themes

### 🔒 Security & Privacy
- Secure AJAX requests with nonce verification
- Optional API key authentication (X-API-KEY header)
- No hardcoded secrets in frontend code
- Input sanitization and validation

### ⚙️ Admin Features
- Complete settings panel in WordPress admin
- API connection testing
- Chat enable/disable controls
- Welcome message customization
- Position and theme selection

## Installation

1. Upload the `vf-amelie` folder to `/wp-content/plugins/`
2. Activate the plugin through WordPress admin
3. Go to **Settings > Amelie Chat** to configure
4. Set up your Flask API backend (see API Requirements below)

## Configuration

### Admin Settings

Navigate to **Settings > Amelie Chat** to configure:

#### API Settings
- **API Base URL**: Your Flask server URL (e.g., `https://api.vane-france.com`)
- **API Secret**: Optional authentication key
- **Connection Test**: Verify API connectivity

#### Chat Settings
- **Enable Chat**: Turn chat system on/off
- **Auto-show**: Display floating button automatically
- **Welcome Message**: Customize greeting message
- **Position**: Choose corner position (bottom-right, bottom-left, etc.)
- **Theme**: Select visual theme (Diamond, Modern, Classic)

### API Requirements

Your Flask backend must provide:

```
POST /api/ai/chat
Content-Type: application/json
X-API-KEY: [optional secret]

Request Body:
{
  "message": "user message text"
}

Response:
{
  "reply": "AI assistant response"
}
```

### Usage Examples

#### Shortcode Implementation
```php
// Basic chat window
[amelie_chat]

// Custom dimensions
[amelie_chat height="500px" width="400px"]

// Specific theme
[amelie_chat theme="modern"]
```

#### JavaScript API
```javascript
// Open floating chat
Amelie.api.open();

// Send message programmatically
Amelie.api.sendMessage("Hello Amélie!");

// Check if chat is open
if (Amelie.api.isOpen()) {
    console.log("Chat is currently open");
}

// Clear chat history
Amelie.api.clearHistory();
```

## Features in Detail

### Floating Chat Widget
- **Smart Positioning**: Adapts to screen size and position setting
- **Notification Badge**: Shows unread message indicator
- **Keyboard Shortcut**: Ctrl+Shift+A to toggle chat
- **Click Outside**: Auto-close when clicking outside
- **Session Persistence**: Remembers chat state during session

### Chat Interface
- **Real-time Messaging**: Instant message display
- **Typing Indicators**: Shows when AI is responding
- **Message Timestamps**: Time display for each message
- **Character Counter**: Prevents overly long messages
- **Error Handling**: Graceful error display with retry options
- **Auto-scroll**: Keeps latest messages visible

### Responsive Design
- **Mobile Optimized**: Full-screen chat on small devices
- **Touch Friendly**: Large touch targets
- **Accessibility**: ARIA labels and keyboard navigation
- **High Contrast**: Support for accessibility preferences
- **Reduced Motion**: Respects user motion preferences

### Security Measures
- **Nonce Verification**: WordPress security tokens
- **Input Sanitization**: All user inputs cleaned
- **Rate Limiting**: Prevents API abuse
- **Secure Headers**: Proper CORS and security headers
- **No Secret Exposure**: API keys never sent to frontend

## Customization

### CSS Classes
```css
.amelie-floating-chat { /* Main container */ }
.amelie-chat-window { /* Chat window */ }
.amelie-message { /* Individual message */ }
.amelie-user-message { /* User messages */ }
.amelie-bot-message { /* AI responses */ }
.amelie-theme-diamond { /* Diamond theme */ }
.amelie-theme-modern { /* Modern theme */ }
.amelie-theme-classic { /* Classic theme */ }
```

### Custom Events
```javascript
// Listen for chat events
$(document).on('amelie:opened', function() {
    console.log('Chat opened');
});

$(document).on('amelie:message:added', function(e, data) {
    console.log('Message added:', data);
});
```

### Theme Creation
To create a custom theme, add CSS rules:
```css
.amelie-theme-custom .amelie-float-button {
    background: your-gradient;
}
```

## API Backend Setup

### Flask Example
```python
from flask import Flask, request, jsonify

app = Flask(__name__)

@app.route('/api/ai/chat', methods=['POST'])
def chat():
    # Verify API key if configured
    api_key = request.headers.get('X-API-KEY')
    if api_key and api_key != 'your-secret-key':
        return jsonify({'error': 'Invalid API key'}), 401
    
    data = request.json
    user_message = data.get('message', '')
    
    # Process with your AI model
    ai_response = process_with_ai(user_message)
    
    return jsonify({'reply': ai_response})

def process_with_ai(message):
    # Your AI implementation here
    return "Hello! I'm Amélie, how can I help you?"
```

### Security Recommendations
- Use HTTPS for all API communication
- Implement rate limiting
- Validate and sanitize all inputs
- Use secure API authentication
- Log chat interactions for monitoring

## Troubleshooting

### Common Issues

#### Chat Not Showing
- Check if plugin is activated
- Verify chat is enabled in settings
- Check for JavaScript console errors

#### API Connection Failed
- Verify API URL is correct and accessible
- Check API key configuration
- Test endpoint manually with curl/Postman
- Verify CORS headers on API server

#### Messages Not Sending
- Check WordPress AJAX functionality
- Verify nonce generation
- Check API server logs
- Test with minimal message

#### Styling Issues
- Check for theme conflicts
- Verify CSS is loading
- Check browser developer tools
- Try different theme options

### Debug Mode
Enable WordPress debug mode to see detailed error messages:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## Performance Optimization

### Frontend Optimization
- CSS/JS files are minified in production
- Chat history limited to 50 messages
- Debounced resize events
- Efficient DOM manipulation

### Backend Optimization
- Implement caching for AI responses
- Use connection pooling
- Add request queuing for high traffic
- Monitor API response times

## Analytics Integration

### Google Analytics
```javascript
// Track chat events
gtag('event', 'chat_opened', {
    'event_category': 'engagement',
    'event_label': 'amelie_chat'
});
```

### Custom Analytics
The plugin fires events that can be tracked:
- `chat_opened`
- `message_sent`
- `message_received`
- `chat_closed`

## Changelog

### Version 1.0.0
- Initial release
- Floating chat widget
- Shortcode integration
- Admin settings panel
- Diamond theme design
- Secure API integration
- Mobile responsive design
- Accessibility features

## Support

For technical support or customization requests, contact the Vane France development team.

## License

GPL v2 or later

## Credits

- Developed for Vane France perfumery
- Diamond theme inspired by luxury jewelry design
- Built with WordPress best practices
- Secure API integration following OWASP guidelines