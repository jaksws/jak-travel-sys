/**
 * إدارة الوضع الليلي/المظلم
 */
class DarkModeManager {
    constructor() {
        this.darkModeEnabled = config('V1_features.dark_mode.enabled', false);
        this.defaultMode = config('V1_features.dark_mode.default', 'light');
        this.init();
    }

    init() {
        if (!this.darkModeEnabled) return;
        
        // استرجاع إعدادات المستخدم المحفوظة
        const userPreference = localStorage.getItem('theme') || this.defaultMode;
        
        // تطبيق الثيم الافتراضي
        if (userPreference === 'dark' || (userPreference === 'system' && 
            window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark-theme');
        }
        
        // إضافة مستمعي الأحداث
        this.setupEventListeners();
    }

    setupEventListeners() {
        // مراقبة زر تبديل المظهر
        const themeToggler = document.getElementById('theme-toggle');
        if (themeToggler) {
            themeToggler.addEventListener('click', () => this.toggleDarkMode());
        }
        
        // مراقبة تغييرات تفضيلات النظام
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (localStorage.getItem('theme') === 'system') {
                this.setTheme(e.matches ? 'dark' : 'light', false);
            }
        });
    }

    toggleDarkMode() {
        const isDark = document.documentElement.classList.contains('dark-theme');
        this.setTheme(isDark ? 'light' : 'dark', true);
    }

    setTheme(theme, savePreference = true) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark-theme');
        } else {
            document.documentElement.classList.remove('dark-theme');
        }
        
        if (savePreference) {
            localStorage.setItem('theme', theme);
        }
        
        // تحديث أيقونة الزر
        const themeIcon = document.getElementById('theme-icon');
        if (themeIcon) {
            themeIcon.className = theme === 'dark' ? 
                'fas fa-sun' : 'fas fa-moon';
        }
    }
}

// تهيئة إدارة المظهر
document.addEventListener('DOMContentLoaded', () => {
    window.darkModeManager = new DarkModeManager();
});
