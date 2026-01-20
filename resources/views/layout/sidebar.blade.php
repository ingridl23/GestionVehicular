<!-- resources/views/layout/sidebar.blade.php -->
<aside
    :class="sidebarOpen ? 'w-64' : 'w-20'"
    class="bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 flex flex-col"
>
    <!-- Logo & Toggle -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-gray-200 dark:border-gray-700">
        <div x-show="sidebarOpen" class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-car text-white text-sm"></i>
            </div>
            <span class="font-semibold text-gray-900 dark:text-white text-lg">VMS</span>
        </div>
        <button
            @click="sidebarOpen = !sidebarOpen"
            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300"
        >
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

        <!-- Dashboard -->
        @can('ver_auditoria')
            <x-nav-item
                icon="fa-gauge"
                label="Dashboard"
                route="auditoria.index"
                :active="request()->routeIs('auditoria.index')"
            />
        @endcan

        <!-- Vehículos -->
        @can('ver_vehiculos')
            <x-nav-item
                icon="fa-car"
                label="Vehículos"
                route="vehiculos.index"
                :active="request()->routeIs('vehiculos.*')"
            />
        @endcan

        <!-- Personal -->
        @can('ver_personal_dependencia')
            <x-nav-item
                icon="fa-id-card"
                label="Personal"
                route="personal.index"
                :active="request()->routeIs('personal.*')"
            />
        @endcan

        <!-- Reservas (con submenú) -->
        @can('ver_reservas_internas')
            <div x-data="{ open: {{ request()->routeIs('reservas.*') ? 'true' : 'false' }} }">
                <button
                    @click="open = !open"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                >
                    <i class="fas fa-calendar-check w-5 text-center"></i>
                    <span x-show="sidebarOpen" class="flex-1 text-left text-sm font-medium">Reservas</span>
                    <i x-show="sidebarOpen" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas text-xs"></i>
                </button>

                <div x-show="open && sidebarOpen" x-collapse class="ml-8 mt-1 space-y-1">
                    @can('ver_reservas_internas')
                    <a href="{{ route('reservas.internas') }}"
                       class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reservas.internas') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        Internas
                    </a>
                    @endcan
                    @can('ver_reservas_prestamos')
                        <a href="{{ route('reservas.prestamos') }}"
                        class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reservas.prestamos') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            Préstamos
                        </a>
                    @endcan
                    @can('autorizar_prestamos')
                        <a href="{{ route('reservas.autorizar-prestamos') }}"
                        class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('reservas.prestamos') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            Autorizar préstamos
                        </a>
                    @endcan
                </div>
            </div>
        @endcan

        <!-- Reportes -->
        @can('ver_reportes_dependencia')
            <x-nav-item
                icon="fa-chart-line"
                label="Reportes"
                route="reportes.index"
                :active="request()->routeIs('reportes.*')"
            />
        @endcan

        <!-- Divider -->
        <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>

        <!-- Dependencias -->
        @can('ver_dependencias')
            <x-nav-item
                icon="fa-building"
                label="Dependencias"
                route="dependencias.index"
                :active="request()->routeIs('dependencias.*')"
            />
        @endcan

        <!-- Usuarios -->
        @can('ver_todos_usuarios')
            <x-nav-item
                icon="fa-users"
                label="Usuarios"
                route="usuarios.index"
                :active="request()->routeIs('usuarios.*')"
            />
        @endcan

        <!-- Alertas -->
        @can('ver_auditoria')
            <x-nav-item
                icon="fa-bell"
                label="Alertas"
                route="alertas.index"
                :active="request()->routeIs('alertas.*')"
            />
        @endcan

    </nav>

    <!-- User Profile (bottom) -->
    <div x-show="sidebarOpen" class="border-t border-gray-200 dark:border-gray-700 p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                    {{ auth()->user()->name }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                    {{ auth()->user()->email }}
                </p>
            </div>
        </div>
    </div>

</aside>
