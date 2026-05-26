import './bootstrap';
// Importar Alpine.js
import collapse from '@alpinejs/collapse';
import '@fortawesome/fontawesome-free/css/all.min.css';
import Alpine from 'alpinejs';


//import './alertas/alerta.js'; hace loop en loader  por eso lo comento
import './calculadora';
import './loader.js';
import './reportes';
import './simulador';
import {
    closeUserModal,
    openEditModal,
    openUserCreateModal
} from './users-modal';
import './vehiculo';

window.openUserCreateModal = openUserCreateModal;
window.openEditModal = openEditModal;
window.closeUserModal = closeUserModal;
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
