document.addEventListener('DOMContentLoaded', () => {

    // ===== CSRF =====
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    // ===== ID =====
    const vehiculoId = window.location.pathname.split('/').pop();

    // ===== DOM =====
    const title = document.getElementById('vehiculo-title');
    const descripcion = document.getElementById('vehiculo-descripcion');
    const tableBody = document.getElementById('vehiculo-table-body');
    const btnEditar = document.getElementById('btn-editar');
    const btnEliminar = document.getElementById('btn-eliminar');

    // ===== Eventos =====
    if (btnEditar) {
        btnEditar.addEventListener('click', () => {
            window.location.href = `/vehiculos/${vehiculoId}/update`;
        });
    }

    if (btnEliminar) {
        btnEliminar.addEventListener('click', async() => {
            if (!confirm('¿Seguro que querés eliminar este vehículo?')) return;

            const res = await fetch(`/vehiculos/${vehiculoId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) {
                const error = await res.json();
                alert(error.message || 'Error al eliminar');
                return;
            }

            alert('Vehículo dado de baja correctamente');
            window.location.href = '/vehiculos';
        });
    }


    // ===== Render =====
    function renderVehiculo(v) {
        title.textContent = `${v.marca} ${v.modelo} - ${v.dominio}`;

        descripcion.innerHTML = `
            <strong>Dominio:</strong> ${v.dominio}<br>
            <strong>Marca:</strong> ${v.marca}<br>
            <strong>Modelo:</strong> ${v.modelo}<br>
            <strong>Año:</strong> ${v.anio}<br>
            <strong>Kilómetros:</strong> ${formatNumber(v.kilometros)} km<br>
            <strong>VTV:</strong> ${formatDate(v.VTV)}<br>
            <strong>Control Satelital:</strong> ${v.control_satelital ? 'Sí' : 'No'}<br>
            <strong>Estado:</strong>
            <span class="px-2 py-1 rounded text-xs ${getEstadoColor(v.estado_vehiculo)}">
                ${v.estado_vehiculo}
            </span>
        `;

        tableBody.innerHTML = `
            <tr>
                <td>${v.dependencia_duena ?? 'N/A'}</td>
                <td>${v.direccion_actual ?? 'N/A'}</td>
                <td>${v.habilitado_prestamo ? 'Sí' : 'No'}</td>
                <td>${v.anio}</td>
                <td>${v.nafta ?? 'N/A'}</td>
                <td>${v.control_satelital ? 'GPS Activo' : 'Sin GPS'}</td>
            </tr>
        `;
    }

    // ===== Utils =====
    function getEstadoColor(estado) {
        return {
            'Disponible': 'bg-green-200 text-green-800',
            'En uso': 'bg-blue-200 text-blue-800',
            'Mantenimiento': 'bg-yellow-200 text-yellow-800',
            'Fuera de servicio': 'bg-red-200 text-red-800'
        }[estado] || 'bg-gray-200 text-gray-800';
    }


    function formatNumber(num) {
        return new Intl.NumberFormat('es-AR').format(num);
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        return new Date(dateString).toLocaleDateString('es-AR');
    }

    function showError(message) {
        descripcion.innerHTML = `<p class="text-red-500">${message}</p>`;
        tableBody.innerHTML = '';
    }


});
