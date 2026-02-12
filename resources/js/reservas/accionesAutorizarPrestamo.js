document.addEventListener("DOMContentLoaded", ()=>{

    let reservaIdActiva = null;
    let reservaIdAutorizar = null;


        document.addEventListener('click', e => {
            const btnRechazar = e.target.closest('.btn-rechazar');
            if (btnRechazar) {
                reservaIdActiva = btnRechazar.dataset.id;
            }

            const btnAutorizar = e.target.closest('.btn-autorizar');
            if (btnAutorizar) {
                reservaIdAutorizar = btnAutorizar.dataset.id;
            }
        });

        document.querySelector('.botonRechazar').addEventListener('click', (e) => {
            e.preventDefault();
            rechazarPrestamo(reservaIdActiva);
        });

        document.querySelector('.botonAutorizar').addEventListener('click', (e) => {
            e.preventDefault();
            autorizarPrestamo(reservaIdAutorizar);
        });


    async function rechazarPrestamo(id){
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
            
            if (data.success) {
                alert(data.message);
                window.location.href = "/admin/autorizar-prestamos";

            }

        } catch (err) {
            console.error(err);
        }
    }



    async function autorizarPrestamo(id){
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
            console.log(data.message);
            if (data.success || data.errors) {
                    alert(data.message);
                    window.location.href = "/admin/autorizar-prestamos";

            }

        } catch (err) {
            console.error(err);
        }
    }

    
})