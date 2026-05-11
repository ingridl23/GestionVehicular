document.addEventListener("DOMContentLoaded", () => {

    // ── CANCELAR RESERVA ──────────────────────────────────────────────
    let reservaIdCancelar = null;

    document.addEventListener('click', e => {
        const btn = e.target.closest('.btn-cancelar');
        if (!btn) return;
        reservaIdCancelar = btn.dataset.id;
        const dialog = document.getElementById('dialog-cancelar');
        if (dialog) dialog.showModal();
    });

    const botonCancelar = document.querySelector('.botonCancelar');
    if (botonCancelar) {
        botonCancelar.addEventListener('click', (e) => {
            e.preventDefault();
            if (!reservaIdCancelar) return;
            //  cerrar el dialogo de confirmación
            const dialogCancelar = document.getElementById('dialog-cancelar');
            if (dialogCancelar) dialogCancelar.close();

            cancelarReserva(reservaIdCancelar);
        });
    }


    async function cancelarReserva(id) {
        try {
            const res = await fetch(`/admin/cancelar-reserva/${id}`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": window.csrfToken,
                },
            });
            const data = await res.json();

            //abro el dialogo de mensaje de confirmacion de cancelacion de la  reserva
            const dialog = document.getElementById('dialog-confirm-cancelacion');
            const ParrafoCancelacion = document.getElementById('parrafo-cancelacion');

            if (dialog) {
                ParrafoCancelacion.textContent = data.message;
                dialog.showModal();

                dialog.addEventListener('close', () => {
                    if (window.APP_CONFIG.ubicacion == "interna") {

                        window.location.href = "/listado-reservas";
                    } else {

                        window.location.href = "/listado-prestamos";
                    }

                }, { once: true });
            }

        } catch (err) {
            console.error(err);

            const dialog = document.getElementById('dialog-confirm-cancelacion');
            const ParrafoCancelacion = document.getElementById('parrafo-cancelacion');

            if (dialog && ParrafoCancelacion) {
                ParrafoCancelacion.textContent = "Ocurrió un error al cancelar la reserva";
                dialog.showModal();
            }
        }
    }

    // ── APROBAR RESERVA INTERNA ───────────────────────────────────────
    // El ID se guarda en window._reservaAprobarId desde el onclick del botón en el blade
    // ── APROBAR RESERVA INTERNA ───────────────────────────────────────
    let reservaIdAprobar = null;

    document.addEventListener('click', e => {
        const btn = e.target.closest('.btn-aprobar-reserva');
        if (!btn) return;

        reservaIdAprobar = btn.dataset.id;

        const dialog = document.getElementById('dialog-aceptar');
        if (dialog) {
            dialog.showModal();
        }
    });

    const botonAprobar = document.querySelector('.botonAprobarReserva');
    if (botonAprobar) {
        botonAprobar.addEventListener('click', async(e) => {
            e.preventDefault();
            if (!reservaIdAprobar) return;

            try {
                const url = window.RESERVAS_CONFIG.routes.aprobar.replace(':id', reservaIdAprobar);
                const res = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken,
                    },
                });

                const data = await res.json();
                document.getElementById('dialog-aceptar').close();
                //abro el dialogo de mensaje de confirmacion de reserva
                const dialog = document.getElementById('dialog-confirmacion');
                if (dialog) {
                    dialog.showModal();
                }
                //const botonAceptar = document.querySelector('.botonAprobarReserva');

                ConfirmReserva = document.getElementById('parrafo-confirmacion');
                if (data.success) {

                    ConfirmReserva.textContent = data.message;

                    window.location.reload();
                } else {
                    ConfirmReserva.textContent = data.message ? data.message : 'No se pudo aprobar la reserva.';
                }

            } catch (err) {
                console.error(err);
                ConfirmReserva('Error al procesar la solicitud.');
            }
        });
    }

    // ── EDITAR — armar href con el ID correcto ────────────────────────
    document.querySelectorAll(".btn-editar").forEach(a => {
        const id = a.dataset.id;
        const url = window.RESERVAS_CONFIG.routes.editar.replace(':id', id);
        a.href = url;
    });

});
