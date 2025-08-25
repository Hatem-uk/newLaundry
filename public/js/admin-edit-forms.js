/**
 * Admin Edit Forms - Unified JavaScript
 * Provides common functionality for all admin edit forms
 */

class AdminEditForm {
    constructor(formSelector, options = {}) {
        this.form = document.querySelector(formSelector);
        this.options = {
            autoSave: true,
            validatePassword: true,
            showLoadingStates: true,
            ...options
        };
        
        if (this.form) {
            this.init();
        }
    }

    init() {
        this.setupFormValidation();
        this.setupPasswordValidation();
        this.setupAutoSave();
        this.setupFormSubmission();
        this.setupImagePreview();
        this.setupFormEnhancements();
    }

    /**
     * Setup form validation
     */
    setupFormValidation() {
        if (!this.form) return;

        this.form.addEventListener('submit', (e) => {
            if (!this.form.checkValidity()) {
                e.preventDefault();
                this.form.reportValidity();
            }
        });

        // Real-time validation
        const inputs = this.form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });
    }

    /**
     * Setup password validation
     */
    setupPasswordValidation() {
        if (!this.options.validatePassword) return;

        const password = this.form.querySelector('#password');
        const confirmPassword = this.form.querySelector('#password_confirmation');

        if (password && confirmPassword) {
            const validatePassword = () => {
                if (password.value && confirmPassword.value && password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('كلمات المرور غير متطابقة');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            };

            password.addEventListener('change', validatePassword);
            confirmPassword.addEventListener('keyup', validatePassword);
        }
    }

    /**
     * Setup auto-save functionality
     */
    setupAutoSave() {
        if (!this.options.autoSave) return;

        const inputs = this.form.querySelectorAll('input, textarea, select');
        const formName = this.form.getAttribute('action')?.split('/').pop() || 'form';

        inputs.forEach(input => {
            input.addEventListener('change', () => {
                this.saveFormDraft(formName);
            });
        });

        // Load draft data if available
        this.loadFormDraft(formName);
    }

    /**
     * Save form data to localStorage as draft
     */
    saveFormDraft(formName) {
        try {
            const formData = new FormData(this.form);
            const draftData = {};
            
            for (let [key, value] of formData.entries()) {
                draftData[key] = value;
            }
            
            localStorage.setItem(`${formName}_draft`, JSON.stringify(draftData));
            this.showNotification('تم حفظ المسودة تلقائياً', 'success');
        } catch (e) {
            console.error('Error saving draft:', e);
        }
    }

    /**
     * Load draft data from localStorage
     */
    loadFormDraft(formName) {
        try {
            const draftData = localStorage.getItem(`${formName}_draft`);
            if (draftData) {
                const draft = JSON.parse(draftData);
                Object.keys(draft).forEach(key => {
                    const input = this.form.querySelector(`[name="${key}"]`);
                    if (input && !input.value) {
                        input.value = draft[key];
                    }
                });
                
                if (Object.keys(draft).length > 0) {
                    this.showNotification('تم تحميل المسودة المحفوظة', 'info');
                }
            }
        } catch (e) {
            console.error('Error loading draft:', e);
        }
    }

    /**
     * Setup form submission handling
     */
    setupFormSubmission() {
        if (!this.form) return;

        this.form.addEventListener('submit', () => {
            // Clear draft on successful submission
            const formName = this.form.getAttribute('action')?.split('/').pop() || 'form';
            localStorage.removeItem(`${formName}_draft`);
            
            if (this.options.showLoadingStates) {
                this.showLoadingState();
            }
        });
    }

    /**
     * Setup image preview functionality
     */
    setupImagePreview() {
        const fileInputs = this.form.querySelectorAll('input[type="file"]');
        
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    this.previewImage(input, file);
                }
            });
        });
    }

    /**
     * Preview image before upload
     */
    previewImage(input, file) {
        const reader = new FileReader();
        const previewContainer = input.parentElement.querySelector('.current-image') || 
                                this.createImagePreviewContainer(input);

        reader.onload = (e) => {
            const img = previewContainer.querySelector('img') || document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '100px';
            img.style.height = 'auto';
            img.style.marginBottom = '10px';
            
            if (!previewContainer.querySelector('img')) {
                previewContainer.appendChild(img);
            }
        };

        reader.readAsDataURL(file);
    }

    /**
     * Create image preview container
     */
    createImagePreviewContainer(input) {
        const container = document.createElement('div');
        container.className = 'current-image';
        container.style.textAlign = 'center';
        container.style.marginBottom = '15px';
        container.style.padding = '10px';
        container.style.border = '1px solid #ddd';
        container.style.borderRadius = '5px';
        container.style.backgroundColor = '#f9f9f9';
        
        const label = document.createElement('p');
        label.textContent = 'معاينة الصورة';
        label.style.margin = '5px 0 0 0';
        label.style.fontSize = '12px';
        label.style.color = '#666';
        
        container.appendChild(label);
        input.parentElement.insertBefore(container, input);
        
        return container;
    }

    /**
     * Setup form enhancements
     */
    setupFormEnhancements() {
        // Add character counter for textareas
        const textareas = this.form.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            this.addCharacterCounter(textarea);
        });

        // Add confirmation for form changes
        this.setupFormChangeConfirmation();
    }

    /**
     * Add character counter to textarea
     */
    addCharacterCounter(textarea) {
        const counter = document.createElement('div');
        counter.className = 'char-counter';
        counter.style.fontSize = '12px';
        counter.style.color = '#6c757d';
        counter.style.textAlign = 'left';
        counter.style.marginTop = '5px';
        
        const updateCounter = () => {
            const maxLength = textarea.getAttribute('maxlength');
            const currentLength = textarea.value.length;
            
            if (maxLength) {
                counter.textContent = `${currentLength}/${maxLength}`;
                
                if (currentLength > maxLength * 0.9) {
                    counter.style.color = '#dc3545';
                } else if (currentLength > maxLength * 0.7) {
                    counter.style.color = '#ffc107';
                } else {
                    counter.style.color = '#6c757d';
                }
            }
        };
        
        textarea.addEventListener('input', updateCounter);
        textarea.parentElement.appendChild(counter);
        updateCounter();
    }

    /**
     * Setup form change confirmation
     */
    setupFormChangeConfirmation() {
        let formChanged = false;
        const inputs = this.form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('change', () => {
                formChanged = true;
            });
        });
        
        // Warn user if they try to leave with unsaved changes
        window.addEventListener('beforeunload', (e) => {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = 'لديك تغييرات غير محفوظة. هل أنت متأكد من أنك تريد المغادرة؟';
            }
        });
    }

    /**
     * Show loading state on submit button
     */
    showLoadingState() {
        const submitBtn = this.form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            // Re-enable button after 10 seconds as fallback
            setTimeout(() => {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            }, 10000);
        }
    }

    /**
     * Validate individual field
     */
    validateField(field) {
        if (field.checkValidity()) {
            this.clearFieldError(field);
            field.classList.add('success');
        } else {
            this.showFieldError(field);
            field.classList.remove('success');
        }
    }

    /**
     * Show field error
     */
    showFieldError(field) {
        this.clearFieldError(field);
        
        const error = document.createElement('span');
        error.className = 'error-message';
        error.textContent = field.validationMessage;
        
        field.parentElement.appendChild(error);
        field.classList.add('error');
    }

    /**
     * Clear field error
     */
    clearFieldError(field) {
        const error = field.parentElement.querySelector('.error-message');
        if (error) {
            error.remove();
        }
        field.classList.remove('error');
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
     * Reset form
     */
    resetForm() {
        if (this.form) {
            this.form.reset();
            this.clearAllErrors();
            this.showNotification('تم إعادة تعيين النموذج', 'info');
        }
    }

    /**
     * Clear all field errors
     */
    clearAllErrors() {
        const errors = this.form.querySelectorAll('.error-message');
        errors.forEach(error => error.remove());
        
        const errorFields = this.form.querySelectorAll('.form-input.error');
        errorFields.forEach(field => field.classList.remove('error'));
    }

    /**
     * Get form data as object
     */
    getFormData() {
        const formData = new FormData(this.form);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        return data;
    }

    /**
     * Set form data from object
     */
    setFormData(data) {
        Object.keys(data).forEach(key => {
            const input = this.form.querySelector(`[name="${key}"]`);
            if (input) {
                input.value = data[key];
            }
        });
    }
}

// Initialize forms when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all edit forms
    const editForms = document.querySelectorAll('.edit-form');
    editForms.forEach(form => {
        new AdminEditForm(`#${form.id}`, {
            autoSave: true,
            validatePassword: true,
            showLoadingStates: true
        });
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
`;
document.head.appendChild(style);

// Export for use in other scripts
window.AdminEditForm = AdminEditForm;
