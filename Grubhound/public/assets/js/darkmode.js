/**
 * Dark Mode Manager
 * Handles dark mode toggle, persistence, and initialization
 */

class DarkModeManager {
    constructor() {
        this.darkModeKey = 'pawsplace-darkmode';
        this.init();
    }

    /**
     * Initialize dark mode on page load
     */
    init() {
        // Check localStorage for saved preference
        const isDarkMode = localStorage.getItem(this.darkModeKey) === 'true';
        
        // Apply saved preference
        if (isDarkMode) {
            this.enableDarkMode();
        }

        // Create and attach toggle button
        this.createToggleButton();
    }

    /**
     * Create the dark mode toggle button
     */
    createToggleButton() {
        // Check if button already exists
        if (document.getElementById('dark-mode-toggle')) {
            return;
        }

        const button = document.createElement('button');
        button.id = 'dark-mode-toggle';
        button.className = 'dark-mode-toggle';
        button.title = 'Toggle Dark Mode';
        button.innerHTML = this.isDarkMode() ? '☀️' : '🌙';
        button.setAttribute('aria-label', 'Toggle Dark Mode');

        // Add click handler
        button.addEventListener('click', (e) => {
            e.preventDefault();
            this.toggle();
        });

        // Append to body
        document.body.appendChild(button);
    }

    /**
     * Check if dark mode is currently enabled
     */
    isDarkMode() {
        return document.body.classList.contains('dark-mode');
    }

    /**
     * Enable dark mode
     */
    enableDarkMode() {
        document.body.classList.add('dark-mode');
        localStorage.setItem(this.darkModeKey, 'true');
        
        // Update button icon
        const button = document.getElementById('dark-mode-toggle');
        if (button) {
            button.innerHTML = '☀️';
            button.title = 'Disable Dark Mode';
        }
    }

    /**
     * Disable dark mode (Light mode)
     */
    disableDarkMode() {
        document.body.classList.remove('dark-mode');
        localStorage.setItem(this.darkModeKey, 'false');
        
        // Update button icon
        const button = document.getElementById('dark-mode-toggle');
        if (button) {
            button.innerHTML = '🌙';
            button.title = 'Enable Dark Mode';
        }
    }

    /**
     * Toggle dark mode on/off
     */
    toggle() {
        if (this.isDarkMode()) {
            this.disableDarkMode();
        } else {
            this.enableDarkMode();
        }
    }

    /**
     * Get current theme
     */
    getTheme() {
        return this.isDarkMode() ? 'dark' : 'light';
    }

    /**
     * Set theme directly
     */
    setTheme(theme) {
        if (theme === 'dark') {
            this.enableDarkMode();
        } else if (theme === 'light') {
            this.disableDarkMode();
        }
    }
}

// Initialize dark mode on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.darkMode = new DarkModeManager();
    });
} else {
    window.darkMode = new DarkModeManager();
}
