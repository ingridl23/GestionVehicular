import './bootstrap';

// Importar Alpine.js
import collapse from '@alpinejs/collapse';
import Alpine from 'alpinejs';

// Registrar plugin collapse
Alpine.plugin(collapse);

// Hacer Alpine disponible globalmente
window.Alpine = Alpine;

// Iniciar Alpine
Alpine.start();

console.log('✅ Alpine.js cargado');



// Dark mode persistence
document.addEventListener('alpine:init', () => {
    Alpine.store('darkMode', {
        on: localStorage.getItem('darkMode') === 'true',

        toggle() {
            this.on = !this.on;
            localStorage.setItem('darkMode', this.on);
        },

        init() {
            this.on = localStorage.getItem('darkMode') === 'true';
        }
    });
});

// Sidebar state persistence
if (localStorage.getItem('sidebarOpen') === null) {
    localStorage.setItem('sidebarOpen', 'true');
}
