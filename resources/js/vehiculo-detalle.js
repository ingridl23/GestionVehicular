// resources/js/vehiculo-detalle.js

document.addEventListener('DOMContentLoaded', function() {
            // Obtener ID del vehículo de la URL
            const pathParts = window.location.pathname.split('/');
            const vehiculoId = pathParts[pathParts.length - 1];

            // Elementos del DOM
            const title = document.getElementById('vehiculo-title');
            const descripcion = document.getElementById('vehiculo-descripcion');
            const tableBody = document.getElementById('vehiculo-table-body');
            if (btnEditar) {
                btnEditar.addEventListener('click', () => {
                    window.location.href = `/vehiculos/${vehiculoId}/edit`;
                });
            }

            if (btnEliminar) {
                btnEliminar.addEventListener('click', async() => {
                    // delete
                });
            }

            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    document.documentElement.classList.toggle('dark');
                });
            }


            // Cargar datos del vehículo
            async function loadVehiculo() {
                try {
                    const response = await fetch(`/api/vehiculos/${vehiculoId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (!response.ok) throw new Error('Error al cargar el vehículo');

                    const vehiculo = await response.json();
                    renderVehiculo(vehiculo);
                } catch (error) {
                    console.error('Error:', error);
                    showError('No se pudo cargar la información del vehículo');
                }
            }

            // Renderizar información del vehículo
            function renderVehiculo(vehiculo) {
                // Actualizar título
                title.textContent = `${vehiculo.marca} ${vehiculo.modelo} - ${vehiculo.dominio}`;

                // Actualizar descripción
                descripcion.innerHTML = `
            <strong>Dominio:</strong> ${vehiculo.dominio}<br>
            <strong>Marca:</strong> ${vehiculo.marca}<br>
            <strong>Modelo:</strong> ${vehiculo.modelo}<br>
            <strong>Año:</strong> ${vehiculo.anio}<br>
            <strong>Kilómetros:</strong> ${formatNumber(vehiculo.kilometros)} km<br>
            <strong>VTV:</strong> ${formatDate(vehiculo.VTV)}<br>
            <strong>Control Satelital:</strong> ${vehiculo.control_satelital ? 'Sí' : 'No'}<br>
            <strong>Estado:</strong> <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getEstadoColor(vehiculo.estado_vehiculo)}">${vehiculo.estado_vehiculo}</span>
        `;

                // Renderizar tabla
                tableBody.innerHTML = `
            <tr>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    ${vehiculo.dependencia_duena || 'N/A'}
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    ${vehiculo.direccion_actual || 'N/A'}
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    ${vehiculo.habilitado_prestamo ? 
                        `<span class="text-green-600 dark:text-green-400">Habilitado</span>` : 
                        `<span class="text-red-600 dark:text-red-400">No habilitado</span>`
                    }
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    ${vehiculo.anio}
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getNaftaColor(vehiculo.nafta)}">
                        ${vehiculo.nafta || 'N/A'}
                    </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    ${vehiculo.control_satelital ? 'GPS Activo' : 'Sin GPS'}
                </td>
            </tr>
        `;

        // Si hay condiciones de préstamo, agregarlas a la descripción
        if (vehiculo.condiciones_prestamo) {
            descripcion.innerHTML += `<br><strong>Condiciones de préstamo:</strong> ${vehiculo.condiciones_prestamo}`;
        }
    }

    // Obtener color del badge según el estado del vehículo
    function getEstadoColor(estado) {
        const estados = {
            'Disponible': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'En uso': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'Mantenimiento': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'Fuera de servicio': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
        };
        return estados[estado] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
    }

    // Obtener color del badge según el estado de nafta
    function getNaftaColor(nafta) {
        const estados = {
            'Lleno': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'Medio': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'Bajo': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            'Vacío': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
        };
        return estados[nafta] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
    }

    // Formatear números
    function formatNumber(num) {
        return new Intl.NumberFormat('es-AR').format(num);
    }

    // Formatear fechas
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('es-AR').format(date);
    }

    // Botón editar
    btnEditar.addEventListener('click', async () => {
        // Aquí puedes abrir un modal o redirigir a una página de edición
        window.location.href = `/vehiculos/${vehiculoId}/edit`;
    });

    // Botón eliminar
    btnEliminar.addEventListener('click', async () => {
        if (!confirm('¿Está seguro que desea eliminar este vehículo?')) {
            return;
        }

        try {
            const response = await fetch(`/api/vehiculos/${vehiculoId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) throw new Error('Error al eliminar el vehículo');

            alert('Vehículo eliminado correctamente');
            window.location.href = '/vehiculos';
        } catch (error) {
            console.error('Error:', error);
            alert('No se pudo eliminar el vehículo. Puede que tenga reservas o viajes asociados.');
        }
    });

    // Toggle tema
    themeToggle.addEventListener('click', () => {
        document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
    });

    // Aplicar tema guardado
    if (localStorage.getItem('theme') === 'dark') {
        document.documentElement.classList.add('dark');
    }

    // Mostrar error
    function showError(message) {
        descripcion.innerHTML = `<p class="text-red-500 dark:text-red-400">${message}</p>`;
        tableBody.innerHTML = '';
    }

    // Cargar vehículo al iniciar
    loadVehiculo();
});