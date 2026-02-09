document.addEventListener("DOMContentLoaded", ()=>{

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

    
    // let enlaces = document.querySelectorAll(".btn-editar");
    // enlaces.forEach(a => {
    //     const id = a.dataset.id;
    //     const url = window.RESERVAS_CONFIG.routes.editar.replace(':id', id);
    //     a.href = url;
    //     console.log(url);
    // });


})