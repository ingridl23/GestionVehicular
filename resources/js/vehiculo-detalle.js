document.addEventListener('DOMContentLoaded', () => {

    // ===== CSRF =====
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    // ===== ID =====
    const vehiculoId = window.location.pathname.split('/').pop();

    // ===== DOM =====
    const descripcion = document.getElementById('vehiculo-descripcion');
    const btnEditar = document.getElementById('btn-editar');
    const btnEliminar = document.getElementById('btn-eliminar');

    // ===== Evento Editar =====
    if (btnEditar) {
        btnEditar.addEventListener('click', () => {
            openModal(window.VEHICULO);
        });
    }

    // ===== Evento Eliminar =====

    if (btnEliminar) {


        btnEliminar.addEventListener('click', async() => {

            //  1. CONFIRMAR (con tu dialog lindo)
            const ok = await abrirDialogoConfirmacion('¿Dar de baja este vehículo?');
            if (!ok) return;

            try {
                //  2. RECIÉN ACÁ eliminás
                const res = await fetch(`/vehiculos/${vehiculoId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();

                if (!res.ok) {
                    mostrarToast(data.message);
                    return;
                }

                //  3. REDIRECCIÓN
                window.location.href = '/vehiculos';

            } catch (error) {
                console.error('Error:', error);
                mostrarToast(error);
            }
        });
    }
    // ===== Evento Guardar Cambios =====
    const btnGuardar = document.getElementById('btnGuardarCambios');

    if (btnGuardar) {
        btnGuardar.addEventListener('click', async(e) => {
            e.preventDefault();
            console.log('Click en guardar cambios');

            const ok = await abrirDialogoConfirmacion('¿Modificar este vehículo?');
            if (!ok) return;

            // Recopilar datos del formulario
            const formData = {
                id_direccion_actual: document.getElementById('id_direccion_actual').value,
                id_dependencia_duena: document.getElementById('id_dependencia_duena').value,
                habilitado_prestamo: document.getElementById('habilitado_prestamo').checked ? 1 : 0,
                control_satelital: document.getElementById('control_satelital').checked ? 1 : 0,
                condiciones_prestamo: document.getElementById('condiciones_prestamo').value,
                dominio: document.getElementById('dominio').value,
                marca: document.getElementById('marca').value,
                unidad: document.getElementById('unidad').value,
                modelo: document.getElementById('modelo').value,
                anio: document.getElementById('anio').value,
                kilometros: document.getElementById('kilometros').value,
                VTV: document.getElementById('VTV').value,
                id_estado_vehiculo: document.getElementById('id_estado_vehiculo').value,
                id_estado_nafta: document.getElementById('id_estado_nafta').value,
            };

            console.log('Datos a enviar:', formData);

            try {
                const response = await fetch(`/vehiculos/${vehiculoId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();
                console.log('Respuesta del servidor:', data);

                if (!response.ok) {
                    // Si hay errores de validación
                    if (data.errors) {
                        const error = Object.values(data.errors).flat().join('\n');
                        mostrarToast(error);
                    } else {
                        mostrarToast(data.message || 'Error al modificar estado del vehiculo');
                    }
                    return;
                }

                // Actualizar objeto global
                window.VEHICULO = data.vehiculo;

                // Refrescar vista
                renderVehiculo(window.VEHICULO);

                // Cerrar modal
                closeModal();


            } catch (error) {
                console.error('Error en la petición:', error);
                mostrarToast(error);
            }
        });
    }

    function mostrarToast(mensaje) {
        const toast = document.getElementById('toast');

        toast.textContent = mensaje;
        toast.classList.remove('hidden');

        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }

    function abrirDialogoConfirmacion(texto) {
        return new Promise((resolve) => {
            const dialog = document.getElementById('dialog-confirmar-vehiculo');
            const textoEl = document.getElementById('dialog-texto');
            const btnConfirmar = document.getElementById('dialog-confirmar-btn');

            textoEl.textContent = texto;

            let confirmado = false;

            const onConfirm = () => {
                confirmado = true;
                dialog.close();
            };

            btnConfirmar.onclick = onConfirm;

            dialog.addEventListener('close', () => {
                resolve(confirmado);
            }, { once: true });

            dialog.showModal();
        });
    }

    // ===== Función Render =====
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

        const direccion = buscarDireccion(
            window.CATALOGOS.direcciones,
            v.id_direccion_actual
        );

        descripcion.innerHTML = `
            <strong>Dominio:</strong> ${v.dominio}<br>
            <strong>Marca:</strong> ${v.marca}<br>
            <strong>Modelo:</strong> ${v.modelo}<br>
            <strong>Unidad:</strong> ${v.unidad}<br>
            <strong>Año:</strong> ${v.anio}<br>
            <strong>Kilómetros:</strong> ${formatNumber(v.kilometros)} km<br>
            <strong>VTV:</strong> ${formatDate(v.vtv)}<br>
            <strong>Control Satelital:</strong> ${v.control_satelital ? 'Sí' : 'No'}<br>
            <strong>Habilitación De Prestamo:</strong> ${v.habilitado_prestamo ? 'Sí' : 'No'}<br>
            <strong>Condición Para Prestamo:</strong> ${v.condiciones_prestamo || 'N/A'}<br>
            <strong>Estado Actual Del Vehiculo:</strong> ${estadoVehiculo}<br>
            <strong>Estado Del Combustible:</strong> ${estadoNafta}<br>
            <strong>Dependencia De Origen:</strong> ${dependencia}<br>
            <strong>Dirección Actual:</strong> ${direccion}<br>
        `;
    }

    // ===== Utils =====
    function formatNumber(num) {
        return new Intl.NumberFormat('es-AR').format(num);
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        return new Date(dateString).toLocaleDateString('es-AR');
    }

    function buscarNombre(lista, id, campo = 'nombre') {
        const item = lista.find(i => i.id == id);
        return item ? item[campo] : 'N/A';
    }

    function buscarDireccion(lista, id) {
        const d = lista.find(i => i.id == id);
        if (!d) return 'N/A';

        let texto = d.calle || '';

        if (d.altura && d.altura != 0) {
            texto += ' ' + d.altura;
        }

        if (d.ciudad) {
            texto += ' - ' + d.ciudad;
        }

        return texto || d.nombre || 'N/A';
    }

    // ===== Modal Functions =====
    function openModal(vehiculo) {
        const modal = document.getElementById('vehiculo-modal');
        const form = document.getElementById('vehiculo-form');

        if (!modal || !form) {
            console.error('Modal o formulario no encontrado');
            return;
        }

        // Llenar selects
        fillSelect('id_dependencia_duena', window.CATALOGOS.dependencias, 'id', 'nombre', vehiculo.id_dependencia_duena);
        fillSelect('id_direccion_actual', window.CATALOGOS.direcciones, 'id', 'nombre', vehiculo.id_direccion_actual);
        fillSelect('id_estado_vehiculo', window.CATALOGOS.estadosVehiculo, 'id', 'estado', vehiculo.id_estado_vehiculo);
        fillSelect('id_estado_nafta', window.CATALOGOS.estadosNafta, 'id', 'estado', vehiculo.id_estado_nafta);

        // Llenar campos
        form.dominio.value = vehiculo.dominio || '';
        form.marca.value = vehiculo.marca || '';
        form.unidad.value = vehiculo.unidad || '';
        form.modelo.value = vehiculo.modelo || '';
        form.anio.value = vehiculo.anio || '';
        form.kilometros.value = vehiculo.kilometros || '';
        form.VTV.value = vehiculo.VTV ? vehiculo.VTV.split('T')[0] : '';
        form.control_satelital.checked = Boolean(vehiculo.control_satelital);
        form.habilitado_prestamo.checked = Boolean(vehiculo.habilitado_prestamo);
        form.condiciones_prestamo.value = vehiculo.condiciones_prestamo || '';

        modal.classList.remove('hidden');
    }

    function fillSelect(selectId, items, valueKey, textKey, selected = null) {
        const select = document.getElementById(selectId);
        if (!select) {
            console.error('Select no encontrado:', selectId);
            return;
        }

        select.innerHTML = '<option value="">Seleccione...</option>';

        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item[valueKey];

            // Para direcciones, construir el texto completo
            if (selectId === 'id_direccion_actual') {
                let texto = item.calle || item.nombre || '';
                if (item.altura && item.altura != 0) {
                    texto += ' ' + item.altura;
                }
                if (item.ciudad) {
                    texto += ' - ' + item.ciudad;
                }
                option.textContent = texto || item[textKey];
            } else {
                option.textContent = item[textKey];
            }

            if (selected && selected == item[valueKey]) {
                option.selected = true;
            }

            select.appendChild(option);
        });
    }

    // Renderizar vehículo inicial
    renderVehiculo(window.VEHICULO);
});

// Función global para cerrar modal
window.closeModal = function() {
    const modal = document.getElementById('vehiculo-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
};