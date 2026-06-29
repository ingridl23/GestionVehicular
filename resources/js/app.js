import './bootstrap';
// Importar Alpine.js
import collapse from '@alpinejs/collapse';
//import '@fortawesome/fontawesome-free/css/all.min.css';
import Alpine from 'alpinejs';

import './alerta.js';
import './calculadora';

import './reportes';
import {
    closeUserModal,
    openEditModal,
    openUserCreateModal
} from './users-modal';

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