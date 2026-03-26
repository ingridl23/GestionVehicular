/*document.addEventListener('DOMContentLoaded', () => {

    const btnFinalizar = document.getElementById('btnFinalizar');
    const modal = document.getElementById('modalFinalizar');
    const confirmar = document.getElementById('confirmarFinalizar');

    if (btnFinalizar) {
        btnFinalizar.addEventListener('click', () => {
            modal.classList.remove('hidden');
        });
    }

    if (confirmar) {
        confirmar.addEventListener('click', async() => {

            const km = document.getElementById('kmFinal').value;
            const estado = document.getElementById('estadoNafta').value;
            const obs = document.getElementById('observaciones').value;

            await fetch(window.VIAJE_DATA.finalizar_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    kilometros_fin: km,
                    id_estado_nafta_fin: estado,
                    observaciones: obs
                })
            });

            location.reload();
        });
    }

});*/
