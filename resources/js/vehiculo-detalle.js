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
    btnEditar.addEventListener('click', () => {
        openModal(window.VEHICULO);
    });


    if (btnEliminar) {
        btnEliminar.addEventListener('click', async() => {
            if (!confirm('¿Seguro que quieres dar de baja este vehículo?')) return;

            const res = await fetch(`/vehiculos/${vehiculoId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) {
                const error = await res.json();
                alert(error.message || 'Error al modificar estado del vehiculo');
                return;
            }

            alert('Vehículo dado de baja correctamente');
            window.location.href = '/vehiculos';
        });
    }






    const btnGuardar = document.getElementById('btnGuardarCambios');

    if (btnGuardar) {
        btnGuardar.addEventListener('click', function() {
            alert('CLICK REAL');
            console.log('CLICK REAL');
        });
    }
    /*
        document.getElementById('btnGuardarCambios')
            .addEventListener('click', function() {

                console.log('CLICK GUARDAR');

                const vehiculoId = window.VEHICULO.id; //

                fetch('/vehiculos/' + vehiculoId, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            id_direccion_actual: document.getElementById('id_direccion_actual').value,
                            id_dependencia_duena: document.getElementById('id_dependencia_duena').value,
                            habilitado_prestamo: document.getElementById('habilitado_prestamo').checked ? 1 : 0,
                            control_satelital: document.getElementById('control_satelital').checked ? 1 : 0,
                            condiciones_prestamo: document.getElementById('condiciones_prestamo').value
                        })
                    })
                    .then(function(res) {
                        return res.json();
                    })
                    .then(function(data) {
                        console.log('Respuesta backend:', data);

                        // actualizar objeto global
                        window.VEHICULO = data.vehiculo;

                        // refrescar vista
                        renderVehiculo(window.VEHICULO);

                        closeModal();
                    })
                    .catch(function(err) {
                        console.error('ERROR:', err);
                    });
            });
    */
    // ===== Render =====
    function renderVehiculo(v) {
        const estadoVehiculo = buscarNombre(
            window.CATALOGOS.estadosVehiculo,
            v.id_estado_vehiculo,
            'estado'
        );

        const estadoNafta = buscarNombre(
            window.CATALOGOS.estadosNafta,
            v.id_estado_nafta,
            'estado'
        );

        const dependencia = buscarNombre(
            window.CATALOGOS.dependencias,
            v.id_dependencia_duena,
            'nombre'
        );

        var direccion = buscarDireccion(
            window.CATALOGOS.direcciones,
            v.id_direccion_actual
        );


        descripcion.innerHTML = `
        <strong>Dominio:</strong> ${v.dominio}<br>
        <strong>Marca:</strong> ${v.marca}<br>
        <strong>Modelo:</strong> ${v.modelo}<br>
        <strong>Año:</strong> ${v.anio}<br>
        <strong>Kilómetros:</strong> ${formatNumber(v.kilometros)} km<br>
        <strong>VTV:</strong> ${formatDate(v.VTV)}<br>
        <strong>Control Satelital:</strong> ${v.control_satelital ? 'Sí' : 'No'}<br>
        <strong>Habilitación De Prestamo:</strong> ${v.habilitado_prestamo ? 'Sí' : 'No'}<br>
        <strong>Condición Para Prestamo:</strong> ${v.condiciones_prestamo}<br>
        <strong>Estado Actual Del Vehiculo:</strong> ${estadoVehiculo}<br>
        <strong>Estado Del Combustible:</strong> ${estadoNafta}<br>
        <strong>Dependencia De Origen:</strong> ${dependencia}<br>
        <strong>Dirección Actual:</strong> ${direccion}<br>
    `;
    }


    // ===== Utils =====
    function getEstadoColor(estado) {
        return {
            'DISPONIBLE': 'bg-green-200 text-green-800',
            'EN_USO': 'bg-blue-200 text-blue-800',
            'EN_MANTENIMIENTO': 'bg-yellow-200 text-yellow-800',
            'BAJA': 'bg-red-200 text-red-800'
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

    function buscarNombre(lista, id, campo = 'nombre') {
        for (let i = 0; i < lista.length; i++) {
            if (lista[i].id == id) {
                return lista[i][campo];
            }
        }
        return 'N/A';
    }

    function buscarDireccion(lista, id) {
        for (var i = 0; i < lista.length; i++) {
            if (lista[i].id == id) {
                var d = lista[i];

                var texto = d.calle;

                if (d.altura && d.altura != 0) {
                    texto += ' ' + d.altura;
                }

                if (d.ciudad) {
                    texto += ' - ' + d.ciudad;
                }

                return texto;
            }
        }
        return 'N/A';
    }


    function openModal(vehiculo) {
        var modal = document.getElementById('vehiculo-modal');
        var form = document.getElementById('vehiculo-form');

        if (!modal || !form) {
            console.error('Modal o formulario no encontrado');
            return;
        }

        fillSelect('id_dependencia_duena', CATALOGOS.dependencias, 'id', 'nombre', vehiculo.id_dependencia_duena);
        fillSelect('id_direccion_actual', CATALOGOS.direcciones, 'id', 'nombre', vehiculo.id_direccion_actual);
        fillSelect('id_estado_vehiculo', CATALOGOS.estadosVehiculo, 'id', 'estado', vehiculo.id_estado_vehiculo);
        fillSelect('id_estado_nafta', CATALOGOS.estadosNafta, 'id', 'estado', vehiculo.id_estado_nafta);


        form.dominio.value = vehiculo.dominio ? vehiculo.dominio : '';
        form.marca.value = vehiculo.marca ? vehiculo.marca : '';
        form.modelo.value = vehiculo.modelo ? vehiculo.modelo : '';
        form.anio.value = vehiculo.anio ? vehiculo.anio : '';
        form.kilometros.value = vehiculo.kilometros ? vehiculo.kilometros : '';
        form.VTV.value = vehiculo.VTV ? vehiculo.VTV.split('T')[0] : '';

        form.control_satelital.checked = vehiculo.control_satelital ? true : false;
        form.habilitado_prestamo.checked = vehiculo.habilitado_prestamo ? true : false;

        form.condiciones_prestamo.value = vehiculo.condiciones_prestamo ?
            vehiculo.condiciones_prestamo :
            '';

        modal.classList.remove('hidden');
    }


    function fillSelect(selectId, items, valueKey, textKey, selected = null) {
        const select = document.getElementById(selectId);
        select.innerHTML = '<option value="">Seleccione...</option>';

        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item[valueKey];
            option.textContent = item[textKey];

            if (selected && selected == item[valueKey]) {
                option.selected = true;
            }

            select.appendChild(option);
        });
    }


    renderVehiculo(window.VEHICULO);



});
window.closeModal = function() {
    const modal = document.getElementById('vehiculo-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
};
