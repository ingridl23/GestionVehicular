console.log('vehiculos.js cargado correctamente');

document.addEventListener('DOMContentLoaded', function() {
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
        console.warn('No se encontró el elemento #vehiculos-grid');
        return;
    }

    // Función principal de carga
    function loadVehiculos(page) {
        page = page || 1;
        var params = new URLSearchParams();
        params.append('page', page);
        params.append('search', searchTerm);
        params.append('dependencia_id', filterDependencia);
        params.append('estado_vehiculo_id', filterEstado);

        var url = '/buscar-vehiculos?' + params.toString();

        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        var headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
        }

        fetch(url, { headers: headers })
            .then(function(response) {
                if (!response.ok) throw new Error('Error ' + response.status);
                return response.json();
            })
            .then(function(data) {
                vehiculos = data.data || [];
                currentPage = data.current_page || 1;
                totalPages = data.last_page || 1;
                renderVehiculos();
                updatePagination();
            })
            .catch(function(error) {
                console.error('Error:', error);
                grid.innerHTML = '<div class="col-span-full text-center py-12 text-red-500">Error al cargar datos</div>';
            });
    }

    function renderVehiculos() {
        if (!vehiculos.length) {
            grid.innerHTML = '<div class="col-span-full text-center py-12 text-gray-500">No se encontraron vehículos</div>';
            return;
        }

        var html = vehiculos.map(function(v) {
            // Manejo de relaciones de forma ultra segura
            var nombreMarca = v.marca || 'N/A';
            var nombreModelo = v.modelo || '';
            var dominio = v.dominio || 'N/A';
            var anio = v.anio || 'N/A';

            // Acceso seguro a objetos anidados sin usar ?.
            var estadoStr = (v.estado_vehiculo && v.estado_vehiculo.estado) ? v.estado_vehiculo.estado : 'N/A';
            var depStr = (v.dependencia_duena && v.dependencia_duena.nombre) ? v.dependencia_duena.nombre : 'N/A';

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

        grid.innerHTML = html;
    }

    function updatePagination() {
        if (paginationInfo) paginationInfo.textContent = currentPage + ' / ' + totalPages;
        if (prevButton) prevButton.disabled = (currentPage <= 1);
        if (nextButton) nextButton.disabled = (currentPage >= totalPages);
    }

    // Event listeners
    btnFiltrar.addEventListener('click', function() {
        searchTerm = searchInput ? searchInput.value.trim() : '';
        filterDependencia = depSelect ? depSelect.value : '';
        filterEstado = estSelect ? estSelect.value : '';
        loadVehiculos(1);
    });


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

    // Carga inicial
    loadVehiculos(1);
});