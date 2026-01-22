document.addEventListener("DOMContentLoaded", ()=>{
    let botonCancelar = document.getElementById("botonCancelar");
    botonCancelar.addEventListener("click", eliminarReserva);


    async function eliminarReserva(){
        let id = botonCancelar.dataset.idreserva;
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