document.addEventListener('DOMContentLoaded', () => {
    const openBtn = document.getElementById('openUserBtn');
    const closeBtn = document.getElementById('closeUserBtn');

    if (openBtn) {
        openBtn.addEventListener('click', openUserCreateModal);
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeUserModal);
    }



});



export function openUserCreateModal() {
    document.getElementById('modalTitle').textContent = 'Crear Usuario';
    document.getElementById('userForm').action = `/admin/usuarios`;
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('passwordField').required = true;
    document.getElementById('userForm').reset();
    document.getElementById('userModal').classList.remove('hidden');
}

export function openEditModal(userId) {
    // Aquí deberías cargar los datos del usuario vía AJAX
    document.getElementById('modalTitle').textContent = 'Editar Usuario';
    document.getElementById('userForm').action = `/admin/usuarios/${userId}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('passwordField').required = false;
    document.getElementById('userModal').classList.remove('hidden');
}

export function closeUserModal() {
    document.getElementById('userModal').classList.add('hidden');
}

export function confirmDelete(userId) {
    if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/usuarios/${userId}`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}
