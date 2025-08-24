# لوحة التحكم - موج (Mawj Admin Dashboard)

A responsive, modern Arabic admin dashboard built with HTML, CSS, and JavaScript.

## Features

- **Responsive Design**: Works perfectly on desktop, tablet, and mobile devices
- **Arabic RTL Support**: Right-to-left text direction with proper Arabic typography
- **Modern UI**: Clean, professional design with smooth animations
- **Interactive Elements**: Hover effects, smooth transitions, and real-time updates
- **Mobile-First**: Optimized for mobile devices with touch gestures
- **Accessibility**: ARIA labels, keyboard navigation, and screen reader support

## File Structure

```
├── index.html          # Main HTML structure (Dashboard Home)
├── users.html          # User Management page
├── agents.html         # Agent Management page
├── laundries.html      # Laundry Management page
├── services.html       # Service Management page
├── orders.html         # Orders page
├── tracking.html       # Order Tracking page
├── styles.css          # CSS styling and responsive design
├── script.js           # JavaScript functionality
└── README.md           # This file
```

## How to Use

1. **Open the Dashboard**: Simply open `index.html` in any modern web browser
2. **Navigation**: Use the sidebar navigation to explore different sections
3. **Sidebar Toggle**: Click the sidebar toggle button (top-right) to hide/show the sidebar
4. **Mobile Menu**: On mobile devices, tap the hamburger menu to open/close the sidebar
5. **Touch Gestures**: Swipe right to open menu, swipe left to close (mobile only)
6. **Page Navigation**: All pages are linked together through the sidebar navigation

## Responsive Breakpoints

- **Desktop**: 1200px and above
- **Tablet**: 768px - 1199px
- **Mobile**: Below 768px

## Browser Support

- Chrome (recommended)
- Firefox
- Safari
- Edge
- Mobile browsers (iOS Safari, Chrome Mobile)

## Customization

### Colors
The dashboard uses a modern color palette that can be easily customized in `styles.css`:

```css
:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    --accent-color: #4fc3f7;
    --success-color: #43e97b;
    --warning-color: #f093fb;
    --danger-color: #ff6b6b;
}
```

### Icons
Icons are powered by Font Awesome. You can change icons by modifying the `<i>` tags in the HTML.

### Content
All text content is in Arabic and can be easily modified in the HTML file.

## Features in Detail

### Dashboard Cards
- **Today's Orders**: Shows current day order count
- **Total Laundries**: Displays total number of laundry services
- **Total Agents**: Shows total number of agents
- **Total Users**: Displays total user count

### Statistics Section
- **Completed Orders**: Number of completed orders
- **Active Agents**: Currently active agents
- **Orders in Progress**: Orders being processed
- **Orders for Delivery**: Orders ready for delivery
- **Average Execution Time**: Average time to complete orders

### Recent Orders
- **Order Details**: Price, laundry name, order ID, and time
- **Status Indicators**: Color-coded status (Completed, In Progress, Cancelled)

## Mobile Features

- **Touch Gestures**: Swipe to open/close sidebar
- **Responsive Layout**: Cards stack vertically on small screens
- **Mobile Menu**: Collapsible sidebar with toggle button
- **Touch-Friendly**: Large touch targets for mobile users

## Accessibility Features

- **Keyboard Navigation**: Full keyboard support
- **Screen Reader**: ARIA labels and semantic HTML
- **Focus Management**: Proper focus trapping in mobile menu
- **High Contrast**: Clear visual hierarchy and contrast

## Performance Optimizations

- **CSS Grid**: Modern layout system for better performance
- **Intersection Observer**: Efficient animation triggering
- **RequestAnimationFrame**: Smooth animations
- **Touch Event Handling**: Optimized for mobile devices

## Troubleshooting

### Common Issues

1. **Sidebar not showing on mobile**: Make sure JavaScript is enabled
2. **Font Awesome icons not loading**: Check internet connection for CDN
3. **Layout issues**: Clear browser cache and refresh

### Browser Compatibility

If you experience issues in older browsers, consider:
- Using a modern browser
- Adding polyfills for CSS Grid
- Implementing fallback layouts

## License

This project is open source and available under the MIT License.

## Support

For questions or issues, please check the browser console for any error messages and ensure all files are in the same directory.
