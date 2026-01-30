document.addEventListener("DOMContentLoaded", ()=>{

    let reservaIdActiva = null;

        document.addEventListener('click', e => {
            const btn = e.target.closest('.btn-cancelar');
            if (!btn) return;

            reservaIdActiva = btn.dataset.id;
            
        });

        document.querySelector('.botonCancelar').addEventListener('click', () => {
            cancelarReserva(reservaIdActiva);
        });

    

    async function cancelarReserva(id){

        try {
            const res = await fetch(`/cancelar-reserva/${id}`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": window.csrfToken,
                },
            });
            const data = await res.json();
            
            if (data.success) {
                if (window.APP_CONFIG.ubicacion == "interna") {
                    window.location.href = "/listado-reservas";
                }
                else{
                    window.location.href = "/listado-prestamos";
                }
            }

        } catch (err) {
            console.error(err);
        }
    }

})