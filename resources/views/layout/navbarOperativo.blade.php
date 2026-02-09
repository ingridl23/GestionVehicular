<!-- NAV SUPERIOR -->
<nav class="fixed top-0 left-0 w-full h-12 bg-blue-600 text-white z-50">
    <div class="max-w-md mx-auto h-full flex items-center justify-between px-3">

        <!-- LOGO / TÍTULO -->
        <span class="font-semibold text-sm whitespace-nowrap">
            Gestión Vehicular
        </span>

        <!-- ICONOS -->
        <div class="flex items-center gap-4">

            <!-- NOTIFICACIONES -->
        <div class="relative">

    <!-- BOTÓN -->
    <button id="notifBtn"
        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700
               text-gray-600 dark:text-gray-300 relative">

        <i class="far fa-bell text-lg"></i>

        <!-- PUNTO ROJO -->
        <span id="notifDot"
      class="{{ $alertas->count() ? '' : 'hidden' }}
             absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full">
</span>

    </button>

    <!-- POPUP -->
 <div id="notifPopup" class="space-y-2">
    @forelse($alertas as $alerta)
        <div class="flex items-start gap-2 text-xs">
            <i class="fas {{ $alerta->icono ?? 'fa-bell' }} text-{{ $alerta->color ?? 'blue' }}-500"></i>
            <span>{{ $alerta->mensaje }}</span>
        </div>
    @empty
        <p class="text-gray-400">Sin notificaciones</p>
    @endforelse
</div>


</div>


            <!-- PERFIL -->
            <div class="relative">
                <button id="userBtn" class="focus:outline-none">
                    <i class="fas fa-user"></i> =
                </button>

                <div id="userMenu"
                    class="hidden absolute right-0 top-8 bg-white text-gray-800 w-40 rounded shadow text-sm">
                    <a href="{{ route('profile.show') }}" class="block px-3 py-2 hover:bg-gray-100">Perfil</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="block w-full text-left px-3 py-2 hover:bg-gray-100">
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</nav>

<!-- BOTÓN MENÚ OPERATIVO -->
<div class="bg-white border-b px-4 py-2">
    <button id="menuOperativoBtn" class="text-sm font-medium">
        ☰ Menú
    </button>
</div>

<!-- MENÚ OPERATIVO DESPLEGABLE -->

<div id="menuOperativo"
     class="hidden bg-white shadow-sm border-b text-sm">

<div x-data="{ open: false }">
    <button
        @click="open = !open"
        class="w-full flex items-center justify-between gap-3 px-4 py-2 hover:bg-gray-100 rounded-lg"
    >
        <div class="flex items-center gap-3">
            <i class="fas fa-calendar-check text-blue-600"></i>
            <span>Mis reservas</span>
        </div>

        <i
            class="fas fa-chevron-down text-xs transition-transform duration-200"
            :class="{ 'rotate-180': open }"
        ></i>
    </button>

    <div
        x-show="open"
        x-collapse
        class="ml-8 mt-1 space-y-1"
    >
        @can('ver_reservas_internas')
        <a href="{{ route('reservas.internas') }}"
           class="block px-3 py-2 rounded-lg text-sm
           {{ request()->routeIs('reservas.internas')
                ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400'
                : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            Internas
        </a>
        @endcan

        @can('ver_reservas_prestamos')
        <a href="{{ route('reservas.prestamos') }}"
           class="block px-3 py-2 rounded-lg text-sm
           {{ request()->routeIs('reservas.prestamos')
                ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400'
                : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
            Préstamos
        </a>
        @endcan
    </div>
</div>




    <a href="{{ route('operativo.reportes.index') }}"
       class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
        <i class="fas fa-file-alt text-green-600"></i>
        <span>Mis reportes</span>
    </a>

    <a href="#"
       class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
        <i class="fas fa-route text-purple-600"></i>
        <span>Mis viajes</span>
    </a>

</div>
@vite(['resources/js/scriptsOperativo.js'])
