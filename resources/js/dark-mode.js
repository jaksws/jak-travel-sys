/**
 * Dark mode functionality for RTLA v2.0
 * System for managing light/dark theme preferences
 */
document.addEventListener('DOMContentLoaded', function() {
    const darkModeEnabled = window.darkModeSettings?.enabled || false;
    
    if (!darkModeEnabled) {
        return;
    }
    
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    const htmlElement = document.documentElement;
    
    // Initialize dark mode from saved preference or system preference
    function initDarkMode() {
        const savedTheme = localStorage.getItem('theme');
        
        if (savedTheme) {
            applyTheme(savedTheme);
        } else if (window.darkModeSettings?.default === 'system') {
            // Check system preference
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                applyTheme('dark');
            } else {
                applyTheme('light');
            }
            
            // Listen for system preference changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
                if (localStorage.getItem('theme') === 'system') {
                    applyTheme(event.matches ? 'dark' : 'light', false);
                }
            });
        } else {
            // Apply default theme
            applyTheme(window.darkModeSettings?.default || 'light');
        }
    }
    
    // Apply specified theme
    function applyTheme(theme, savePreference = true) {
        if (theme === 'dark') {
            htmlElement.classList.add('dark');
            htmlElement.classList.add('dark-theme');
        } else if (theme === 'light') {
            htmlElement.classList.remove('dark');
            htmlElement.classList.remove('dark-theme');
        } else if (theme === 'system') {
            // Apply theme based on system preference
            const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (isDarkMode) {
                htmlElement.classList.add('dark');
                htmlElement.classList.add('dark-theme');
            } else {
                htmlElement.classList.remove('dark');
                htmlElement.classList.remove('dark-theme');
            }
        }
        
        // Update toggle button if it exists
        if (darkModeToggle) {
            const isDarkActive = htmlElement.classList.contains('dark');
            darkModeToggle.setAttribute('aria-checked', isDarkActive);
            
            // Update icon if exists
            const themeIcon = document.getElementById('theme-icon');
            if (themeIcon) {
                themeIcon.className = isDarkActive ? 'fas fa-sun' : 'fas fa-moon';
            }
        }
        
        // Save theme preference
        if (savePreference) {
            localStorage.setItem('theme', theme);
        }
    }
    
    // Toggle between dark and light mode
    function toggleDarkMode() {
        const isDark = htmlElement.classList.contains('dark');
        applyTheme(isDark ? 'light' : 'dark');
        
        // Send theme preference to server if user is logged in
        if (window.userId) {
            fetch('/api/user/preferences', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    theme: localStorage.getItem('theme')
                })
            });
        }
    }
    
    // Setup dark mode toggle if present
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', toggleDarkMode);
    }
    
    // Setup theme selector if exists
    const themeSelector = document.getElementById('theme-selector');
    if (themeSelector) {
        themeSelector.addEventListener('change', function() {
            applyTheme(this.value);
            
            // Send theme preference to server if user is logged in
            if (window.userId) {
                fetch('/api/user/preferences', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        theme: this.value
                    })
                });
            }
        });
        
        // Set initial value
        const currentTheme = localStorage.getItem('theme') || window.darkModeSettings?.default || 'light';
        themeSelector.value = currentTheme;
    }
    
    // Initialize dark mode
    initDarkMode();
    
    // Make functions available globally
    window.darkModeManager = {
        applyTheme,
        toggleDarkMode
    };
});
