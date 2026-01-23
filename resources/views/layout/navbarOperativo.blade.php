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
                <button id="notifBtn" class="focus:outline-none">
                    <i class="fas fa-bell"></i>
                </button>

                <div id="notifPopup"
                    class="hidden absolute right-0 top-8 w-56 bg-white text-gray-800 rounded shadow p-3 text-xs">
                    <p class="font-semibold mb-1">Notificaciones</p>
                    <p>Hace 2m — Nueva reserva</p>
                </div>
            </div>

            <!-- PERFIL -->
            <div class="relative">
                <button id="userBtn" class="focus:outline-none">
                    <i class="fas fa-user"></i> =
                </button>

                <div id="userMenu"
                    class="hidden absolute right-0 top-8 bg-white text-gray-800 w-40 rounded shadow text-sm">
                    <a href="#" class="block px-3 py-2 hover:bg-gray-100">Perfil</a>

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

    <a href="#"
       class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100">
        <i class="fas fa-calendar-check text-blue-600"></i>
        <span>Mis reservas</span>
    </a>

    <a href="#"
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
