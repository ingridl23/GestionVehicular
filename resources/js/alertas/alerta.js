// Resolver una alerta individual
console.log('se carga alertas');
window.resolver = async function(id) {
    if (!confirm('¿Marcar esta alerta como resuelta?')) return;

    try {
        const response = await fetch(`/alertas/${id}/resolver`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.success) {
            location.reload();
        }

    } catch (error) {
        console.error(error);
    }
};

// Ver detalle
window.verDetalle = async function(id) {
    try {
        const response = await fetch(`/alertas/${id}`);
        const alerta = await response.json();

        document.getElementById('detalleMensaje').innerText = alerta.mensaje;
        document.getElementById('detalleFecha').innerText = alerta.fecha;
        document.getElementById('detalleDependencia').textContent = alerta.dependencia;

        document.getElementById('modalAlerta').classList.remove('hidden');

    } catch (error) {
        console.error(error);
    }
};

// Cerrar modal
window.cerrarModal = function() {
    document.getElementById('modalAlerta').classList.add('hidden');
};

// Resolver múltiples
window.resolverSeleccionadas = async function() {
    const ids = Array.from(document.querySelectorAll('.alerta-checkbox:checked'))
        .map(el => el.value);

    if (ids.length === 0) {
        alert('Seleccioná al menos una alerta');
        return;
    }

    if (!confirm('¿Resolver alertas seleccionadas?')) return;

    try {
        await fetch('/alertas/resolver-multiples', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ ids })
        });

        location.reload();

    } catch (error) {
        console.error(error);
    }
};