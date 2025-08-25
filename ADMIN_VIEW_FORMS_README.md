# Admin View Forms - Unified CSS and JavaScript

This document describes the unified CSS and JavaScript files for admin view/show pages in the laundry management system.

## Files

### CSS
- **`public/css/admin-view-forms.css`** - Unified styles for all admin view pages

### JavaScript
- **`public/js/admin-view-forms.js`** - Unified functionality for all admin view pages

## Features

### CSS Features
- **Page Headers**: Consistent styling for page titles and breadcrumbs
- **Alerts**: Success, error, warning, and info alert styles
- **Section Containers**: Standardized section layouts with proper spacing
- **Info Sections**: Profile information display with consistent formatting
- **Info Grids**: Responsive grid layouts for displaying information
- **Status Badges**: Color-coded status indicators
- **Role Badges**: User role display badges
- **Social Links**: Social media link styling
- **Stats Grids**: Statistics display with icons and numbers
- **Action Sections**: Button groups and action areas
- **Buttons**: Consistent button styles across all view pages
- **Status Dropdowns**: Status change select elements
- **Profile Headers**: Laundry, user, and agent profile header layouts
- **Rating Stars**: Star rating display system
- **Services Grid**: Service information grid layout
- **Orders Table**: Order data table styling
- **Ratings Section**: Rating display and comment sections
- **No Data Styling**: Empty state displays
- **Responsive Design**: Mobile-friendly layouts

### JavaScript Features

#### Core Functionality
- **Status Change Management**: Handle entity status updates
- **Image Preview**: Click to enlarge images with modal
- **Table Sorting**: Sortable table columns with indicators
- **Rating Display**: Enhanced rating and comment display
- **General Enhancements**: Loading states, confirmations, tooltips

#### Status Change System
- Automatic form submission for status changes
- Confirmation dialogs before status updates
- Support for multiple entity types (laundry, user, agent, service, package, city)
- CSRF token handling
- Arabic status text mapping

#### Image Preview System
- Clickable images with cursor pointer
- Modal display for enlarged images
- Keyboard support (Escape to close)
- Responsive modal sizing

#### Table Sorting
- Clickable table headers with sort indicators
- Arabic text sorting support
- Visual feedback for sort direction
- Maintains table structure during sorting

#### Rating Display Enhancement
- Automatic comment truncation for long text
- Expandable comment display
- Toggle buttons for full text viewing

#### General Enhancements
- **Button Loading States**: Visual feedback during form submission
- **Confirmation Dialogs**: Safety confirmations for destructive actions
- **Tooltips**: Hover tooltips for additional information
- **Smooth Scrolling**: Smooth navigation to page sections
- **Notifications**: Toast-style notifications for user feedback
- **CSV Export**: Data export functionality
- **Data Refresh**: Page data refresh capabilities

## Usage

### Basic Implementation
```html
@extends('layouts.admin')

@section('content')
    <!-- Your view content here -->
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-view-forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/admin-view-forms.js') }}"></script>
@endpush
```

### Customizing Options
```javascript
// Initialize with custom options
window.adminViewForm = new AdminViewForm({
    enableStatusChange: true,
    enableImagePreview: true,
    enableTableSorting: true,
    enableRatingDisplay: true
});
```

### Status Change Elements
```html
<select class="status-select" 
        data-entity-id="{{ $entity->id }}" 
        data-entity-type="laundry">
    <option value="approved">نشط</option>
    <option value="pending">في الانتظار</option>
    <option value="suspended">معلق</option>
</select>
```

### Sortable Tables
```html
<table class="data-table">
    <thead>
        <tr>
            <th data-sortable>الاسم</th>
            <th data-sortable>التاريخ</th>
            <th data-sortable>الحالة</th>
        </tr>
    </thead>
    <tbody>
        <!-- Table rows -->
    </tbody>
</table>
```

### Image Preview
```html
<img src="{{ $entity->image }}" 
     alt="{{ $entity->name }}" 
     class="profile-image">
```

### Tooltips
```html
<button data-tooltip="معلومات إضافية">زر</button>
```

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- ES6+ JavaScript features
- CSS Grid and Flexbox
- CSS Custom Properties (variables)

## Dependencies

- Font Awesome (for icons)
- Laravel Blade templating
- CSRF token support

## Notes

- All text is in Arabic for the user interface
- Responsive design for mobile and desktop
- Accessibility features included
- Performance optimized with event delegation
- Modular architecture for easy customization

## Maintenance

When updating the unified files:
1. Test all view pages to ensure compatibility
2. Update this README with new features
3. Maintain backward compatibility
4. Test responsive behavior across devices
