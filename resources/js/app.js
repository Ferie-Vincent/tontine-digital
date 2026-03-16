import './bootstrap';
import './push';

// Theme management
function initTheme() {
    const saved = localStorage.getItem('theme');
    if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}

window.toggleTheme = function() {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
};

initTheme();

// Alpine.js est charge par Livewire automatiquement

// Fonctions utilitaires globales
window.formatMoney = function(amount) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'decimal',
        minimumFractionDigits: 0,
    }).format(amount) + ' FCFA';
};

window.copyToClipboard = function(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Feedback visuel si necessaire
    });
};

// Enregistrement du Service Worker PWA
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('./service-worker.js')
            .catch(() => {
                // Service worker non disponible en dev
            });
    });
}
