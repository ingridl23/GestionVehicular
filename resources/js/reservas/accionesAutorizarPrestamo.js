document.addEventListener("DOMContentLoaded", () => {

    let reservaIdActiva = null;
    let reservaIdAutorizar = null;


    // let dialogRechazar = document.getElementById("dialog-cancelar");
    document.querySelectorAll('.btn-rechazar').forEach(btn => {
        btn.addEventListener('click', function() {
            reservaIdActiva = this.dataset.id;
            document.getElementById('dialog-rechazar').showModal();
        });
    });



    document.querySelectorAll('.btn-autorizar').forEach(btn => {
        btn.addEventListener('click', function() {
            reservaIdAutorizar = this.dataset.id;
        });
    });


    document.querySelector('.botonRechazar').addEventListener('click', (e) => {
        e.preventDefault();
        if (!reservaIdActiva) return;

        rechazarPrestamo(reservaIdActiva);
    });

    document.querySelector('.botonAutorizar').addEventListener('click', (e) => {
        e.preventDefault();
        if (!reservaIdAutorizar) return;

        autorizarPrestamo(reservaIdAutorizar);
    });


    async function rechazarPrestamo(id) {
        try {
            const res = await fetch(`/admin/rechazar-prestamo/${id}`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": window.csrfToken,
                },
            });
            const data = await res.json();

            const dialog = document.getElementById('dialog-confirm-rechazo');
            const parrafo = document.getElementById('parrafo-rechazo');

            if (dialog && parrafo) {
                parrafo.textContent = data.message;
                dialog.showModal();

                dialog.addEventListener('close', () => {
                    window.location.href = "/admin/autorizar-prestamos";
                }, { once: true });
            }


        } catch (err) {
            console.error(err);
            console.error(err);

            const dialog = document.getElementById('dialog-confirm-cancelacion');
            const parrafo = document.getElementById('parrafo-cancelacion');

            if (dialog && parrafo) {
                parrafo.textContent = "Ocurrió un error al rechazar el préstamo";
                dialog.showModal();
            }
        }
    }



    async function autorizarPrestamo(id) {
        try {
            const res = await fetch(`/admin/autorizar-prestamo/${id}`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": window.csrfToken,
                },
            });
            const data = await res.json();
            const dialog = document.getElementById('dialog-confirm-aceptacion');
            const parrafo = document.getElementById('parrafo-aceptacion');

            if (dialog && parrafo) {
                parrafo.textContent = data.message;
                dialog.showModal();

                dialog.addEventListener('close', () => {
                    window.location.href = "/admin/autorizar-prestamos";
                }, { once: true });
            }

        } catch (err) {
            console.error(err);
            const dialog = document.getElementById('dialog-confirm-aceptacion');
            const parrafo = document.getElementById('parrafo-aceptacion');

            if (dialog && parrafo) {
                parrafo.textContent = "Ocurrió un error al autorizar el préstamo";
                dialog.showModal();
            }
        }
    }


})
