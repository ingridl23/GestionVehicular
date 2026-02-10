document.addEventListener("DOMContentLoaded", ()=>{

    let dialogDelete = document.getElementById("dialog-cancelar");
    let reservaIdActiva = null;

        document.addEventListener('click', e => {
            const btn = e.target.closest('.btn-cancelar');
            if (!btn) return;

            reservaIdActiva = btn.dataset.id;
            
        });

        document.querySelector('.botonCancelarDependencia').addEventListener('click', (e) => {
            e.preventDefault();
            cancelarReserva(reservaIdActiva);
        });

    

    async function cancelarReserva(id) {
        try {
            const res = await fetch(`/admin/dependencias/${id}`, {
                method: "DELETE",
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": window.csrfToken,
                },
            });

            if (!res.ok) {
                const error = await res.json();
                alert(error.errors?.dependencia ?? "No se pudo eliminar");
                dialogDelete.close();
                return;
            }

            const data = await res.json();

            if (data.success) {
                window.location.href = "/admin/dependencias";
            }
        } catch (err) {
            console.error(err);
        }
    }



    const dialog = document.getElementById('confirmDialog');
    const dialogText = document.getElementById('dialogText');
    const confirmBtn = document.getElementById('confirmBtn');
    const cancelBtn = document.getElementById('cancelBtn');

    let currentCheckbox = null;
    let nuevoEstado = null;

    document.querySelectorAll('.toggle-activa').forEach(toggle => {
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            currentCheckbox = this;
            nuevoEstado = this.checked;

            const nombre = this.dataset.nombre;

            dialogText.textContent = nuevoEstado
                ? `¿Querés activar la dependencia "${nombre}"?`
                : `¿Seguro que querés desactivar la dependencia "${nombre}"? No podrá ser utilizada pero en caso de tener dependencias hijas no podrá ser desactivada.`;

            dialog.showModal();
        });
    });

    cancelBtn.addEventListener('click', () => {
        currentCheckbox.checked = !nuevoEstado;
        dialog.close();
    });

    confirmBtn.addEventListener('click', () => {
        fetch(`/admin/dependencias/${currentCheckbox.dataset.id}/activa`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify({ activa: nuevoEstado })
        })
        .then(async res => {
            const data = await res.json();

            if (!res.ok || !data.ok) {
                throw new Error(data.message);
            }

            currentCheckbox.checked = nuevoEstado;
            alert(data.message);
            dialog.close();
        })

        .catch(err => {
            currentCheckbox.checked = !nuevoEstado;
            dialog.close();
            alert(err.message);
        });
    });

})