document.addEventListener("DOMContentLoaded", ()=>{
    let botonesCancelar = document.querySelectorAll(".botonCancelar");
    console.log(botonesCancelar);
    botonesCancelar.forEach(boton => {
        boton.addEventListener("click", ()=>{
            eliminarReserva(boton);
        });

    });
    

    async function eliminarReserva(boton){
        let id = boton.dataset.id;
        console.log(id);
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