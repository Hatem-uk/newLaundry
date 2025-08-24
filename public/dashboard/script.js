// Mobile menu functionality
document.addEventListener('DOMContentLoaded', function() {
     

    // Add sidebar toggle button
    const sidebarToggle = document.createElement('button');
    sidebarToggle.className = 'sidebar-toggle';
    sidebarToggle.innerHTML = '<img src="/dashboard/logo.png" alt="Toggle Sidebar" class="toggle-logo">';
    sidebarToggle.setAttribute('aria-label', 'Toggle Sidebar');
    document.body.appendChild(sidebarToggle);

    // Add mobile menu toggle button
    const mobileToggle = document.createElement('button');
    mobileToggle.className = 'mobile-menu-toggle';
    mobileToggle.innerHTML = '<img src="/dashboard/logo.png" alt="Toggle Menu" class="toggle-logo">';
    mobileToggle.setAttribute('aria-label', 'Toggle Menu');
    document.body.appendChild(mobileToggle);

    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    // Toggle sidebar
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');

        // Rotate logo for visual feedback
        if (sidebar.classList.contains('collapsed')) {
            sidebarToggle.querySelector('.toggle-logo').style.transform = 'rotate(180deg)';
        } else {
            sidebarToggle.querySelector('.toggle-logo').style.transform = 'rotate(0deg)';
        }
    });

    mobileToggle.addEventListener('click', function() {
        sidebar.classList.toggle('open');
        // Rotate logo for mobile toggle
        if (sidebar.classList.contains('open')) {
            mobileToggle.querySelector('.toggle-logo').style.transform = 'rotate(90deg)';
        } else {
            mobileToggle.querySelector('.toggle-logo').style.transform = 'rotate(0deg)';
        }
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
            sidebar.classList.remove('open');
            mobileToggle.querySelector('.toggle-logo').style.transform = 'rotate(0deg)';
        }
    });

    // Close mobile menu on window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('open');
            mobileToggle.querySelector('.toggle-logo').style.transform = 'rotate(0deg)';
        }
    });

    // Add fade-in animation to cards
    const cards = document.querySelectorAll('.card, .stats-card, .orders-card');
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, observerOptions);

    cards.forEach(card => {
        observer.observe(card);
    });

    // Add hover effects to navigation items
    const navItems = document.querySelectorAll('.nav-item:not(.logout)');
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // Remove active class from all items
            navItems.forEach(nav => nav.classList.remove('active'));
            
            // Add active class to clicked item
            this.classList.add('active');
            
            // Close mobile menu after navigation
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('open');
                mobileToggle.querySelector('.toggle-logo').style.transform = 'rotate(0deg)';
            }
            
            // Allow navigation to work normally
            // The link will navigate to the new page
        });
    });

    // Add loading animation to numbers
    const numbers = document.querySelectorAll('.number');
    numbers.forEach(number => {
        const finalValue = parseInt(number.textContent);
        if (!isNaN(finalValue)) {
            let currentValue = 0;
            const increment = finalValue / 50;
            const timer = setInterval(() => {
                currentValue += increment;
                if (currentValue >= finalValue) {
                    number.textContent = finalValue;
                    clearInterval(timer);
                } else {
                    number.textContent = Math.floor(currentValue);
                }
            }, 30);
        }
    });

    // Add real-time updates simulation
    setInterval(() => {
        const todayOrders = document.querySelector('.card:nth-child(1) .number');
        if (todayOrders) {
            const currentValue = parseInt(todayOrders.textContent);
            if (!isNaN(currentValue)) {
                const newValue = currentValue + Math.floor(Math.random() * 3) - 1;
                if (newValue >= 0) {
                    todayOrders.textContent = newValue;
                }
            }
        }
    }, 10000); // Update every 10 seconds

    // Add smooth scrolling for better UX
    const smoothScroll = (target, duration) => {
        const targetPosition = target.getBoundingClientRect().top;
        const startPosition = window.pageYOffset;
        const distance = targetPosition - startPosition;
        let startTime = null;

        function animation(currentTime) {
            if (startTime === null) startTime = currentTime;
            const timeElapsed = currentTime - startTime;
            const run = ease(timeElapsed, startPosition, distance, duration);
            window.scrollTo(0, run);
            if (timeElapsed < duration) requestAnimationFrame(animation);
        }

        function ease(t, b, c, d) {
            t /= d / 2;
            if (t < 1) return c / 2 * t * t + b;
            t--;
            return -c / 2 * (t * (t - 2) - 1) + b;
        }

        requestAnimationFrame(animation);
    };

    // Add keyboard navigation support
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            sidebar.classList.remove('open');
            mobileToggle.querySelector('.toggle-logo').style.transform = 'rotate(0deg)';
        }
    });

    // Add touch gesture support for mobile
    let touchStartX = 0;
    let touchEndX = 0;

    document.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    });

    document.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });

    function handleSwipe() {
        const swipeThreshold = 50;
        const swipeDistance = touchEndX - touchStartX;
        
        if (Math.abs(swipeDistance) > swipeThreshold) {
            if (swipeDistance > 0 && window.innerWidth <= 768) {
                // Swipe right - open menu
                sidebar.classList.add('open');
                mobileToggle.querySelector('.toggle-logo').style.transform = 'rotate(90deg)';
            } else if (swipeDistance < 0 && window.innerWidth <= 768) {
                // Swipe left - close menu
                sidebar.classList.remove('open');
                mobileToggle.querySelector('.toggle-logo').style.transform = 'rotate(0deg)';
            }
        }
    }

    // Add accessibility improvements
    const focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
    const firstFocusableElement = sidebar.querySelector(focusableElements);
    const focusableContent = sidebar.querySelectorAll(focusableElements);
    const lastFocusableElement = focusableContent[focusableContent.length - 1];

    // Trap focus within sidebar when open
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Tab' && sidebar.classList.contains('open')) {
            if (e.shiftKey) {
                if (document.activeElement === firstFocusableElement) {
                    lastFocusableElement.focus();
                    e.preventDefault();
                }
            } else {
                if (document.activeElement === lastFocusableElement) {
                    firstFocusableElement.focus();
                    e.preventDefault();
                }
            }
        }
    });

    // Add ARIA labels and roles for better accessibility
    sidebar.setAttribute('role', 'navigation');
    sidebar.setAttribute('aria-label', 'Main Navigation');
    
    navItems.forEach((item, index) => {
        item.setAttribute('role', 'menuitem');
        const spanElement = item.querySelector('span');
        if (spanElement) {
            item.setAttribute('aria-label', spanElement.textContent);
        }
    });

    // Add performance optimization
    let ticking = false;
    
    function updateLayout() {
        if (!ticking) {
            requestAnimationFrame(() => {
                // Update any layout-dependent calculations here
                ticking = false;
            });
            ticking = true;
        }
    }

    window.addEventListener('resize', updateLayout);
    window.addEventListener('orientationchange', updateLayout);
});
