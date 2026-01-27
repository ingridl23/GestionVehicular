import 'alpinejs';
import './bootstrap';

// Importar Alpine.js
import collapse from '@alpinejs/collapse';
import Alpine from 'alpinejs';
import './vehiculo';


// Registrar plugin de collapse
Alpine.plugin(collapse);

// Inicializar Alpine
window.Alpine = Alpine;
Alpine.start();

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