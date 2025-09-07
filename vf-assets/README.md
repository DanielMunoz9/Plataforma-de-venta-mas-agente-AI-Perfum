# Vane France Landing Page

This directory contains the assets and template for the Vane France landing page.

## File Structure

```
vf-assets/
├── assets/
│   ├── css/
│   │   └── vf-style.css          # Main styles with vf- prefixed classes
│   └── js/
│       └── vf-script.js          # JavaScript with video controls and animations
├── snippets/
│   └── vf-functions-snippet.php  # Functions to include in theme's functions.php
├── images/                       # Placeholder for images
└── videos/                       # Placeholder for video files

page-vanefrance.php               # Main page template
```

## Installation

1. **Copy the assets**: Place the `vf-assets` folder in your theme directory or website root.

2. **Add the template**: Copy `page-vanefrance.php` to your active theme directory.

3. **Include functions**: Copy the contents of `vf-assets/snippets/vf-functions-snippet.php` into your theme's `functions.php` file.

## Asset Loading

- **CSS**: Contains vf- prefixed classes to avoid conflicts
- **JavaScript**: Handles video autoplay/mute behavior and scroll animations
- **Conditional Loading**: By default, assets load only on pages using the Vane France template

## Video Setup

Add your video files to `vf-assets/videos/`:
- `hero-video.mp4` - Background video for hero section
- `hero-video.webm` - WebM format for better browser compatibility
- `showcase-video.mp4` - Feature video
- `showcase-video.webm` - WebM format

## Image Setup

Add your images to `vf-assets/images/`:
- `hero-fallback.jpg` - Fallback image if video doesn't load
- `video-poster.jpg` - Poster image for showcase video
- `collection-1.jpg` - Collection showcase image 1
- `collection-2.jpg` - Collection showcase image 2

## Features

- Responsive design with mobile-first approach
- Video autoplay with fallback handling
- Scroll-triggered animations
- Smooth scrolling navigation
- Social media integration
- SEO optimized meta tags
- Accessibility features

## Browser Compatibility

- Modern browsers with HTML5 video support
- Fallback handling for autoplay restrictions
- Progressive enhancement for older browsers

## Customization

All styles use the `vf-` prefix and can be easily customized by modifying `vf-style.css`. The JavaScript is modular and can be extended as needed.