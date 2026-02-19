document.addEventListener("DOMContentLoaded", ()=>{

    let reservaIdActiva = null;
    let reservaIdAutorizar = null;


        document.querySelectorAll('.btn-rechazar').forEach(btn => {
            btn.addEventListener('click', function () {
                reservaIdActiva = this.dataset.id;
                 document.getElementById('dialog-rechazar').showModal();
            });
        });
        


        document.querySelectorAll('.btn-autorizar').forEach(btn => {
            btn.addEventListener('click', function () {
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