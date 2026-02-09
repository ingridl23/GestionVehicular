console.log('vehiculos.js cargado correctamente - VERSION DEBUG');

document.addEventListener('DOMContentLoaded', function() {
    // ===== CSRF =====
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    console.log('✅ CSRF Token:', csrfToken ? 'Encontrado' : '❌ NO ENCONTRADO');

    // Variables de estado
    var currentPage = 1;
    var totalPages = 1;
    var vehiculos = [];
    var searchTerm = '';
    var filterDependencia = '';
    var filterEstado = '';

    // Elementos del DOM
    var grid = document.getElementById('vehiculos-grid');
    var searchInput = document.getElementById('search-vehiculos');
    var prevButton = document.getElementById('prev-page');
    var nextButton = document.getElementById('next-page');
    var paginationInfo = document.getElementById('pagination-info');
    var btnFiltrar = document.getElementById('btn-filtrar');
    var depSelect = document.getElementById('filter-dependencia');
    var estSelect = document.getElementById('filter-estado');

    if (!grid) {
        console.error('❌ ERROR: No se encontró el elemento #vehiculos-grid');
        return;
    }

    console.log('✅ Grid encontrado');

    // Función principal de carga CON DEBUGGING
    function loadVehiculos(page) {
        page = page || 1;
        var params = new URLSearchParams();
        params.append('page', page);
        params.append('search', searchTerm);
        params.append('dependencia_id', filterDependencia);
        params.append('estado_vehiculo_id', filterEstado);

        var url = '/buscar-vehiculos?' + params.toString();

        console.log('🔍 Haciendo petición a:', url);

        var headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken;
        }

        fetch(url, { headers: headers })
            .then(function(response) {
                console.log('📡 Respuesta recibida:', {
                    status: response.status,
                    ok: response.ok,
                    statusText: response.statusText
                });

                if (!response.ok) {
                    console.error('❌ Respuesta no OK:', response.status);
                    throw new Error('Error ' + response.status);
                }
                return response.json();
            })
            .then(function(data) {
                console.log('📦 Datos parseados:', data);
                console.log('📊 Estructura de data:', {
                    tiene_data: !!data.data,
                    es_array: Array.isArray(data.data),
                    cantidad: data.data ? data.data.length : 0,
                    current_page: data.current_page,
                    last_page: data.last_page
                });

                if (data.data && data.data.length > 0) {
                    console.log('🚗 Primer vehículo:', data.data[0]);
                    console.log('🔑 Keys del primer vehículo:', Object.keys(data.data[0]));
                }

                vehiculos = data.data || [];
                currentPage = data.current_page || 1;
                totalPages = data.last_page || 1;

                console.log('✅ Variables actualizadas:', {
                    vehiculos_count: vehiculos.length,
                    currentPage,
                    totalPages
                });

                renderVehiculos();
                updatePagination();
            })
            .catch(function(error) {
                console.error('❌ ERROR en fetch:', error);
                console.error('Stack trace:', error.stack);
                grid.innerHTML = '<div class="col-span-full text-center py-12 text-red-500">Error al cargar datos: ' + error.message + '</div>';
            });
    }

    function renderVehiculos() {
        console.log('🎨 Renderizando vehículos, cantidad:', vehiculos.length);

        if (!vehiculos.length) {
            console.warn('⚠️ No hay vehículos para renderizar');
            grid.innerHTML = '<div class="col-span-full text-center py-12 text-gray-500">No se encontraron vehículos</div>';
            return;
        }

        var html = vehiculos.map(function(v, index) {
            console.log(`🚗 Procesando vehículo ${index}:`, {
                id: v.id,
                marca: v.marca,
                modelo: v.modelo,
                dominio: v.dominio,
                tiene_estado_vehiculo: !!v.estado_vehiculo,
                estado_vehiculo_valor: v.estado_vehiculo
            });

            // Manejo de relaciones de forma ultra segura
            var nombreMarca = v.marca || 'N/A';
            var nombreModelo = v.modelo || '';
            var dominio = v.dominio || 'N/A';
            var anio = v.anio || 'N/A';

            // Acceso seguro a objetos anidados
            var estadoStr = 'N/A';
            if (v.estado_vehiculo) {
                if (v.estado_vehiculo.estado) {
                    estadoStr = v.estado_vehiculo.estado;
                } else {
                    console.warn('⚠️ estado_vehiculo existe pero no tiene .estado:', v.estado_vehiculo);
                }
            } else {
                console.warn('⚠️ No existe estado_vehiculo en el vehículo:', v);
            }

            var depStr = 'N/A';
            if (v.dependencia_duena && v.dependencia_duena.nombre) {
                depStr = v.dependencia_duena.nombre;
            }

            var colorClase = 'bg-gray-100 text-gray-800';

            if (estadoStr === 'DISPONIBLE') {
                colorClase = 'bg-green-100 text-green-800';
            } else if (estadoStr === 'EN_USO') {
                colorClase = 'bg-blue-100 text-blue-800';
            } else if (estadoStr === 'EN_MANTENIMIENTO') {
                colorClase = 'bg-yellow-100 text-yellow-800';
            } else if (estadoStr === 'BAJA') {
                colorClase = 'bg-red-100 text-red-800';
            }

            return '<div onclick="window.location.href=\'/vehiculos/' + v.id + '\'" class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg cursor-pointer transition">' +
                '<div class="bg-gray-200 dark:bg-gray-700 h-40 flex items-center justify-center">' +
                '<i class="fa-solid fa-car text-5xl text-gray-400"></i>' +
                '</div>' +
                '<div class="p-4">' +
                '<h3 class="font-semibold text-lg text-gray-900 dark:text-white">' + nombreMarca + ' ' + nombreModelo + '</h3>' +
                '<p class="text-sm text-gray-600 dark:text-gray-400">Dominio: ' + dominio + '</p>' +
                '<p class="text-sm mt-1"><span class="px-2 py-1 rounded text-xs ' + colorClase + '">' + estadoStr + '</span></p>' +
                '<button onclick="event.stopPropagation(); window.location.href=\'/vehiculos/' + v.id + '\'" class="mt-3 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded text-sm transition">Ver detalle</button>' +
                '</div>' +
                '</div>';
        }).join('');

        console.log('✅ HTML generado, longitud:', html.length);
        grid.innerHTML = html;
        console.log('✅ Grid actualizado');
    }

    function updatePagination() {
        if (paginationInfo) paginationInfo.textContent = currentPage + ' / ' + totalPages;
        if (prevButton) prevButton.disabled = (currentPage <= 1);
        if (nextButton) nextButton.disabled = (currentPage >= totalPages);
    }

    // Event listeners
    if (btnFiltrar) {
        btnFiltrar.addEventListener('click', function() {
            searchTerm = searchInput ? searchInput.value.trim() : '';
            filterDependencia = depSelect ? depSelect.value : '';
            filterEstado = estSelect ? estSelect.value : '';
            console.log('🔍 Filtros aplicados:', { searchTerm, filterDependencia, filterEstado });
            loadVehiculos(1);
        });
    }

    if (prevButton) {
        prevButton.addEventListener('click', function() {
            if (currentPage > 1) loadVehiculos(currentPage - 1);
        });
    }

    if (nextButton) {
        nextButton.addEventListener('click', function() {
            if (currentPage < totalPages) loadVehiculos(currentPage + 1);
        });
    }

    // ===== Evento Guardar Cambios =====
    const btnAlta = document.getElementById('btn-Alta');

    if (btnAlta) {
        btnAlta.addEventListener('click', async(e) => {
            e.preventDefault();
            console.log('Click en agregar vehiculo');

            const form = document.getElementById('vehiculo-form');
            if (!form.checkValidity()) {
                alert('Por favor complete todos los campos obligatorios');
                form.reportValidity();
                return;
            }

            if (!confirm('¿Desea agregar este vehículo?')) return;

            const formData = {
                id_direccion_actual: document.getElementById('id_direccion_actual').value,
                id_dependencia_duena: document.getElementById('id_dependencia_duena').value,
                habilitado_prestamo: document.getElementById('habilitado_prestamo').checked ? 1 : 0,
                control_satelital: document.getElementById('control_satelital').checked ? 1 : 0,
                condiciones_prestamo: document.getElementById('condiciones_prestamo').value,
                dominio: document.getElementById('dominio').value,
                marca: document.getElementById('marca').value,
                modelo: document.getElementById('modelo').value,
                anio: document.getElementById('anio').value,
                kilometros: document.getElementById('kilometros').value,
                VTV: document.getElementById('VTV').value,
                id_estado_vehiculo: document.getElementById('id_estado_vehiculo').value,
                id_estado_nafta: document.getElementById('id_estado_nafta').value,
            };

            console.log('Datos a enviar:', formData);

            try {
                const response = await fetch(`/vehiculos`, {
                    method: 'POST',
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
                    if (data.errors) {
                        const errores = Object.values(data.errors).flat().join('\n');
                        alert('Errores de validación:\n' + errores);
                    } else {
                        alert(data.message || 'Error al actualizar el vehículo');
                    }
                    return;
                }

                alert('Vehículo añadido con éxito');
                closeModal();
                loadVehiculos(currentPage);

            } catch (error) {
                console.error('Error en la petición:', error);
                alert('Error al procesar la solicitud');
            }
        });
    }

    // ===== ABRIR MODAL =====
    const btnAbrirModal = document.getElementById('btn-abrir');

    if (btnAbrirModal) {
        btnAbrirModal.addEventListener('click', function() {
            console.log('Abriendo modal para crear vehículo');
            openModalCrear();
        });
    }

    function openModalCrear() {
        const modal = document.getElementById('vehiculo-modal');
        const form = document.getElementById('vehiculo-form');

        if (!modal || !form) {
            console.error('Modal o formulario no encontrado');
            return;
        }

        if (!window.CATALOGOS) {
            console.error('window.CATALOGOS no está definido');
            alert('Error: No se pudieron cargar los catálogos');
            return;
        }

        form.reset();

        fillSelect('id_dependencia_duena', window.CATALOGOS.dependencias, 'id', 'nombre');
        fillSelect('id_direccion_actual', window.CATALOGOS.direcciones, 'id', 'nombre');
        fillSelect('id_estado_vehiculo', window.CATALOGOS.estadosVehiculo, 'id', 'estado');
        fillSelect('id_estado_nafta', window.CATALOGOS.estadosNafta, 'id', 'estado');

        modal.classList.remove('hidden');
    }

    function fillSelect(selectId, items, valueKey, textKey, selected = null) {
        const select = document.getElementById(selectId);
        if (!select) {
            console.error('Select no encontrado:', selectId);
            return;
        }

        select.innerHTML = '<option value="">Seleccione...</option>';

        if (!items || !Array.isArray(items)) {
            console.error('Items no es un array válido:', selectId, items);
            return;
        }

        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item[valueKey];

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

    // Carga inicial
    console.log('🚀 Iniciando carga inicial de vehículos...');
    loadVehiculos(1);
});

// Función global para cerrar modal
window.closeModal = function() {
    const modal = document.getElementById('vehiculo-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
};
