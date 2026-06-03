document.addEventListener('DOMContentLoaded', () => {
    //  const openBtn = document.getElementById('openUserBtn');
    //   const closeBtn = document.getElementById('closeUserBtn');
    /*
        if (openBtn) {
            openBtn.addEventListener('click', openUserCreateModal);
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', closeUserModal);
        }

    */

});



export function openUserCreateModal() {
    window.openUserCreateModal = openUserCreateModal;
    document.getElementById('modalTitle').textContent = 'Crear Usuario';
    document.getElementById('userForm').action = window.USER_ROUTES.store;
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('passwordField').required = true;
    document.getElementById('userForm').reset();
    document.getElementById('userModal').classList.remove('hidden');

}

export function openEditModal(userId) {
    window.openEditModal = openEditModal;
    // Aquí deberías cargar los datos del usuario vía AJAX
    document.getElementById('modalTitle').textContent = 'Editar Usuario';
    document.getElementById('userForm').action = `${window.USER_ROUTES.updateBase}/${userId}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('passwordField').required = false;
    document.getElementById('userModal').classList.remove('hidden');
}

export function closeUserModal() {
    document.getElementById('userModal').classList.add('hidden');
}

let userToDelete = null;

export function confirmDelete(userId) {

    userToDelete = userId;

    document
        .getElementById('deleteModal')
        .classList.remove('hidden');
}

window.confirmDelete = confirmDelete;



document.addEventListener('DOMContentLoaded', () => {

    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const cancelBtn = document.getElementById('cancelDeleteBtn');

    if (confirmBtn) {

        confirmBtn.addEventListener('click', () => {

            if (!userToDelete) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `${window.USER_ROUTES.deleteBase}/${userToDelete}`;

            const csrfToken = document.querySelector(
                'meta[name="csrf-token"]'
            ).content;

            form.innerHTML = `
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="DELETE">
            `;

            document.body.appendChild(form);
            form.submit();
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            document
                .getElementById('deleteModal')
                .classList.add('hidden');

            userToDelete = null;
        });
    }

});


const cancelBtn = document.getElementById('cancelDeleteBtn');

if (cancelBtn) {
    cancelBtn.addEventListener('click', () => {
        document
            .getElementById('deleteModal')
            .classList.add('hidden');

        userToDelete = null;
    });

}
