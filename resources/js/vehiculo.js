// resources/js/vehiculo.js
console.log('vehiculos.js cargado');

document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let totalPages = 1;
    let vehiculos = [];
    let searchTerm = '';

    // ===============================
    // Elementos del DOM
    // ===============================
    const grid = document.getElementById('vehiculos-grid');
    if (!grid) {
        console.warn('vehiculos-grid no existe en esta vista');
        return;
    }

    const searchInput = document.getElementById('search-vehiculos');
    const prevButton = document.getElementById('prev-page');
    const nextButton = document.getElementById('next-page');
    const paginationInfo = document.getElementById('pagination-info');
    const themeToggle = document.getElementById('theme-toggle');

    // ===============================
    // Cargar vehículos
    // ===============================
    async function loadVehiculos(page = 1) {
        try {
            const response = await fetch(`/api/vehiculos?page=${page}&search=${searchTerm}`, {
                headers: {
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });


            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();

            vehiculos = data.data || data;
            currentPage = data.current_page || page;
            totalPages = data.last_page || 1;

            console.log('Vehículos recibidos:', vehiculos);

            renderVehiculos();
            updatePagination();
        } catch (error) {
            console.error('Error cargando vehículos:', error);
            showError('No se pudieron cargar los vehículos');
        }
    }

    // ===============================
    // Renderizar tarjetas
    // ===============================
    function renderVehiculos() {
        if (!vehiculos || vehiculos.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 dark:text-gray-400">
                        No se encontraron vehículos
                    </p>
                </div>
            `;
            return;
        }

        grid.innerHTML = vehiculos.map(vehiculo => `
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer"
                 onclick="window.location.href='/vehiculos/${vehiculo.id}'">

                <div class="bg-gray-200 dark:bg-gray-700 h-48 flex items-center justify-center">
                    <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>

                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        ${vehiculo.marca ?? 'N/A'} ${vehiculo.modelo ?? ''}
                    </h3>

                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                        <span class="font-medium">Dominio:</span> ${vehiculo.dominio ?? 'N/A'}
                    </p>

                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                        <span class="font-medium">Año:</span> ${vehiculo.anio ?? 'N/A'}
                    </p>

                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                        <span class="font-medium">Estado:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getEstadoColor(vehiculo.estado_vehiculo)}">
                            ${vehiculo.estado_vehiculo ?? 'N/A'}
                        </span>
                    </p>

                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        <span class="font-medium">Dependencia:</span> ${vehiculo.dependencia_duena ?? 'N/A'}
                    </p>

                    <button
                        onclick="event.stopPropagation(); window.location.href='/vehiculos/${vehiculo.id}'"
                        class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Ver detalle
                    </button>
                </div>
            </div>
        `).join('');
    }

    // ===============================
    // Badge estado
    // ===============================
    function getEstadoColor(estado) {
        const estados = {
            'Disponible': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'En uso': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'Mantenimiento': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'Fuera de servicio': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
        };

        return estados[estado] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
    }

    // ===============================
    // Paginación (protegida)
    // ===============================
    function updatePagination() {
        if (!paginationInfo || !prevButton || !nextButton) return;

        paginationInfo.textContent = `${currentPage} / ${totalPages}`;
        prevButton.disabled = currentPage <= 1;
        nextButton.disabled = currentPage >= totalPages;
    }

    if (prevButton && nextButton) {
        prevButton.addEventListener('click', () => {
            if (currentPage > 1) {
                loadVehiculos(--currentPage);
            }
        });

        nextButton.addEventListener('click', () => {
            if (currentPage < totalPages) {
                loadVehiculos(++currentPage);
            }
        });
    }

    // ===============================
    // Buscador
    // ===============================
    if (searchInput) {
        searchInput.addEventListener('input', debounce((e) => {
            searchTerm = e.target.value;
            currentPage = 1;
            loadVehiculos(currentPage);
        }, 500));
    }

    // ===============================
    // Tema oscuro
    // ===============================

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem(
                'theme',
                document.documentElement.classList.contains('dark') ? 'dark' : 'light'
            );
        });
    }

    // ===============================
    // Utils
    // ===============================
    function debounce(func, wait) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => func(...args), wait);
        };
    }

    function showError(message) {
        grid.innerHTML = `
            <div class="col-span-full text-center py-12">
                <p class="text-red-500 dark:text-red-400">${message}</p>
            </div>
        `;
    }

    // ===============================
    // Init
    // ===============================
    loadVehiculos();
});
