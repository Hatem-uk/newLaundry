/**
 * Admin View Forms - Unified JavaScript
 * Provides common functionality for all admin view/show pages
 */

class AdminViewForm {
    constructor(options = {}) {
        this.options = {
            enableStatusChange: true,
            enableImagePreview: true,
            enableTableSorting: true,
            enableRatingDisplay: true,
            ...options
        };
        
        this.init();
    }

    init() {
        if (this.options.enableStatusChange) {
            this.setupStatusChange();
        }
        
        if (this.options.enableImagePreview) {
            this.setupImagePreview();
        }
        
        if (this.options.enableTableSorting) {
            this.setupTableSorting();
        }
        
        if (this.options.enableRatingDisplay) {
            this.setupRatingDisplay();
        }
        
        this.setupGeneralEnhancements();
    }

    /**
     * Setup status change functionality
     */
    setupStatusChange() {
        const statusSelects = document.querySelectorAll('.status-select');
        
        statusSelects.forEach(select => {
            select.addEventListener('change', (e) => {
                const newStatus = e.target.value;
                const entityId = e.target.getAttribute('data-entity-id');
                const entityType = e.target.getAttribute('data-entity-type');
                
                if (newStatus && entityId && entityType) {
                    this.changeEntityStatus(entityId, newStatus, entityType);
                }
            });
        });
    }

    /**
     * Change entity status
     */
    changeEntityStatus(entityId, newStatus, entityType) {
        if (!newStatus) {
            this.showNotification('يرجى اختيار حالة.', 'warning');
            return;
        }

        const statusText = this.getStatusText(newStatus);
        
        if (confirm(`هل أنت متأكد من تغيير حالة ${entityType} إلى "${statusText}"?`)) {
            this.submitStatusChange(entityId, newStatus, entityType);
        }
    }

    /**
     * Submit status change form
     */
    submitStatusChange(entityId, newStatus, entityType) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = this.getStatusChangeRoute(entityType, entityId);
        form.style.display = 'none';

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = this.getCsrfToken();
        form.appendChild(csrfInput);

        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = newStatus;
        form.appendChild(statusInput);

        document.body.appendChild(form);
        form.submit();
    }

    /**
     * Get status change route
     */
    getStatusChangeRoute(entityType, entityId) {
        const routes = {
            'laundry': `/admin/laundries/${entityId}/status`,
            'user': `/admin/users/${entityId}/status`,
            'agent': `/admin/agents/${entityId}/status`,
            'service': `/admin/services/${entityId}/status`,
            'package': `/admin/packages/${entityId}/status`,
            'city': `/admin/cities/${entityId}/status`
        };
        
        return routes[entityType] || '#';
    }

    /**
     * Get CSRF token
     */
    getCsrfToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        return metaTag ? metaTag.getAttribute('content') : '';
    }

    /**
     * Get status text in Arabic
     */
    getStatusText(status) {
        const statusMap = {
            'approved': 'نشط',
            'pending': 'في الانتظار',
            'rejected': 'مرفوض',
            'suspended': 'معلق',
            'active': 'نشط',
            'inactive': 'غير نشط'
        };
        
        return statusMap[status] || status;
    }

    /**
     * Setup image preview functionality
     */
    setupImagePreview() {
        const images = document.querySelectorAll('.profile-image, .service-image');
        
        images.forEach(img => {
            img.addEventListener('click', (e) => {
                this.showImageModal(e.target.src, e.target.alt);
            });
            
            // Add cursor pointer to indicate clickable
            img.style.cursor = 'pointer';
        });
    }

    /**
     * Show image modal
     */
    showImageModal(src, alt) {
        const modal = document.createElement('div');
        modal.className = 'image-modal';
        modal.innerHTML = `
            <div class="image-modal-content">
                <span class="image-modal-close">&times;</span>
                <img src="${src}" alt="${alt}" class="image-modal-img">
                <div class="image-modal-caption">${alt}</div>
            </div>
        `;

        // Add modal styles
        this.addModalStyles();

        document.body.appendChild(modal);

        // Close modal on click
        modal.addEventListener('click', (e) => {
            if (e.target === modal || e.target.classList.contains('image-modal-close')) {
                modal.remove();
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                modal.remove();
            }
        });
    }

    /**
     * Add modal styles
     */
    addModalStyles() {
        if (document.getElementById('image-modal-styles')) return;

        const style = document.createElement('style');
        style.id = 'image-modal-styles';
        style.textContent = `
            .image-modal {
                position: fixed;
                z-index: 9999;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                animation: fadeIn 0.3s ease;
            }

            .image-modal-content {
                position: relative;
                max-width: 90%;
                max-height: 90%;
                text-align: center;
            }

            .image-modal-close {
                position: absolute;
                top: -40px;
                right: 0;
                color: white;
                font-size: 35px;
                font-weight: bold;
                cursor: pointer;
                z-index: 10000;
            }

            .image-modal-close:hover {
                color: #ccc;
            }

            .image-modal-img {
                max-width: 100%;
                max-height: 100%;
                border-radius: 8px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            }

            .image-modal-caption {
                color: white;
                margin-top: 15px;
                font-size: 16px;
                font-weight: 500;
            }

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
        `;

        document.head.appendChild(style);
    }

    /**
     * Setup table sorting functionality
     */
    setupTableSorting() {
        const tables = document.querySelectorAll('.data-table');
        
        tables.forEach(table => {
            const headers = table.querySelectorAll('th[data-sortable]');
            
            headers.forEach(header => {
                header.addEventListener('click', () => {
                    this.sortTable(table, header);
                });
                
                // Add sort indicator
                header.style.cursor = 'pointer';
                header.innerHTML += ' <i class="fas fa-sort"></i>';
            });
        });
    }

    /**
     * Sort table by column
     */
    sortTable(table, header) {
        const columnIndex = Array.from(header.parentElement.children).indexOf(header);
        const rows = Array.from(table.querySelectorAll('tbody tr'));
        const isAscending = header.classList.contains('sort-asc');
        
        // Remove existing sort classes
        header.parentElement.querySelectorAll('th').forEach(th => {
            th.classList.remove('sort-asc', 'sort-desc');
        });
        
        // Add sort class
        header.classList.add(isAscending ? 'sort-desc' : 'sort-asc');
        
        // Update sort icon
        const icon = header.querySelector('i');
        icon.className = isAscending ? 'fas fa-sort-down' : 'fas fa-sort-up';
        
        // Sort rows
        rows.sort((a, b) => {
            const aValue = a.children[columnIndex]?.textContent || '';
            const bValue = b.children[columnIndex]?.textContent || '';
            
            if (isAscending) {
                return bValue.localeCompare(aValue, 'ar');
            } else {
                return aValue.localeCompare(bValue, 'ar');
            }
        });
        
        // Reorder rows
        const tbody = table.querySelector('tbody');
        rows.forEach(row => tbody.appendChild(row));
    }

    /**
     * Setup rating display functionality
     */
    setupRatingDisplay() {
        const ratingItems = document.querySelectorAll('.rating-item');
        
        ratingItems.forEach(item => {
            const comment = item.querySelector('.rating-comment');
            if (comment && comment.textContent.length > 100) {
                this.truncateComment(comment);
            }
        });
    }

    /**
     * Truncate long comments
     */
    truncateComment(commentElement) {
        const fullText = commentElement.textContent;
        const truncatedText = fullText.substring(0, 100) + '...';
        
        commentElement.innerHTML = `
            <span class="comment-preview">${truncatedText}</span>
            <span class="comment-full" style="display: none;">${fullText}</span>
            <button class="comment-toggle" onclick="this.parentElement.querySelector('.comment-preview').style.display='none'; this.parentElement.querySelector('.comment-full').style.display='inline'; this.style.display='none';">
                عرض المزيد
            </button>
        `;
    }

    /**
     * Setup general enhancements
     */
    setupGeneralEnhancements() {
        // Add loading states to buttons
        this.setupButtonLoading();
        
        // Add confirmation to destructive actions
        this.setupConfirmations();
        
        // Add tooltips
        this.setupTooltips();
        
        // Add smooth scrolling
        this.setupSmoothScrolling();
    }

    /**
     * Setup button loading states
     */
    setupButtonLoading() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                    
                    // Re-enable button after 10 seconds as fallback
                    setTimeout(() => {
                        submitBtn.classList.remove('loading');
                        submitBtn.disabled = false;
                    }, 10000);
                }
            });
        });
    }

    /**
     * Setup confirmations for destructive actions
     */
    setupConfirmations() {
        const destructiveButtons = document.querySelectorAll('.btn-danger, .btn-warning');
        
        destructiveButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const action = btn.textContent.trim();
                if (!confirm(`هل أنت متأكد من ${action}؟`)) {
                    e.preventDefault();
                }
            });
        });
    }

    /**
     * Setup tooltips
     */
    setupTooltips() {
        const elementsWithTooltip = document.querySelectorAll('[data-tooltip]');
        
        elementsWithTooltip.forEach(element => {
            element.addEventListener('mouseenter', (e) => {
                this.showTooltip(e.target, e.target.getAttribute('data-tooltip'));
            });
            
            element.addEventListener('mouseleave', () => {
                this.hideTooltip();
            });
        });
    }

    /**
     * Show tooltip
     */
    showTooltip(element, text) {
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.textContent = text;
        
        // Add tooltip styles
        this.addTooltipStyles();
        
        document.body.appendChild(tooltip);
        
        // Position tooltip
        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
        
        // Store reference
        element.tooltip = tooltip;
    }

    /**
     * Hide tooltip
     */
    hideTooltip() {
        const tooltip = document.querySelector('.tooltip');
        if (tooltip) {
            tooltip.remove();
        }
    }

    /**
     * Add tooltip styles
     */
    addTooltipStyles() {
        if (document.getElementById('tooltip-styles')) return;

        const style = document.createElement('style');
        style.id = 'tooltip-styles';
        style.textContent = `
            .tooltip {
                position: fixed;
                z-index: 10000;
                background: #333;
                color: white;
                padding: 8px 12px;
                border-radius: 4px;
                font-size: 12px;
                pointer-events: none;
                animation: fadeIn 0.2s ease;
            }

            .tooltip::after {
                content: '';
                position: absolute;
                top: 100%;
                left: 50%;
                margin-left: -5px;
                border: 5px solid transparent;
                border-top-color: #333;
            }
        `;

        document.head.appendChild(style);
    }

    /**
     * Setup smooth scrolling
     */
    setupSmoothScrolling() {
        const links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(link => {
            link.addEventListener('click', (e) => {
                const targetId = link.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        // Style the notification
        Object.assign(notification.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            padding: '15px 20px',
            borderRadius: '5px',
            color: 'white',
            zIndex: '9999',
            animation: 'slideIn 0.3s ease'
        });
        
        // Set background color based on type
        const colors = {
            success: '#28a745',
            error: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8'
        };
        
        notification.style.backgroundColor = colors[type] || colors.info;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    /**
     * Export data to CSV
     */
    exportToCSV(data, filename) {
        const csvContent = this.convertToCSV(data);
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        
        if (link.download !== undefined) {
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }

    /**
     * Convert data to CSV format
     */
    convertToCSV(data) {
        if (!Array.isArray(data) || data.length === 0) return '';
        
        const headers = Object.keys(data[0]);
        const csvRows = [headers.join(',')];
        
        data.forEach(row => {
            const values = headers.map(header => {
                const value = row[header];
                return typeof value === 'string' ? `"${value}"` : value;
            });
            csvRows.push(values.join(','));
        });
        
        return csvRows.join('\n');
    }

    /**
     * Refresh page data
     */
    refreshData() {
        const refreshButtons = document.querySelectorAll('.btn-refresh, [data-refresh]');
        
        refreshButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                btn.classList.add('loading');
                location.reload();
            });
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize admin view form with default options
    window.adminViewForm = new AdminViewForm({
        enableStatusChange: true,
        enableImagePreview: true,
        enableTableSorting: true,
        enableRatingDisplay: true
    });
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .btn.loading {
        position: relative;
        color: transparent;
    }

    .btn.loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid transparent;
        border-top: 2px solid currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .comment-toggle {
        background: none;
        border: none;
        color: #667eea;
        cursor: pointer;
        font-size: 12px;
        text-decoration: underline;
        margin-left: 5px;
    }

    .comment-toggle:hover {
        color: #4facfe;
    }
`;

document.head.appendChild(style);

// Export for use in other scripts
window.AdminViewForm = AdminViewForm;
