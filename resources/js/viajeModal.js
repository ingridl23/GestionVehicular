document.getElementById('btnFinalizar') ? .addEventListener('click', () => {
    document.getElementById('modalFinalizar').classList.remove('hidden');
});

document.getElementById('confirmarFinalizar') ? .addEventListener('click', async() => {

    const km = document.getElementById('kmFinal').value;
    const estado = document.getElementById('estadoNafta').value;
    const obs = document.getElementById('observaciones').value;

    await fetch(`/viajes/finalizar/${window.viajeId}`, {
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
