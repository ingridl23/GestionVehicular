<!-- resources/views/layout/bottomnav.blade.php -->
<nav
    class="fixed bottom-0 inset-x-0 z-40 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex items-stretch justify-around"
    style="padding-bottom: env(safe-area-inset-bottom);"
>
    <a href="{{ route('operativo.dashboard2') }}"
       class="flex-1 flex flex-col items-center justify-center gap-1 py-2 text-xs
       {{ request()->routeIs('operativo.dashboard2') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
        <i class="fas fa-gauge text-lg"></i>
        <span>Inicio</span>
    </a>

    <a href="{{ route('operativo.mis-reservas') }}"
       class="flex-1 flex flex-col items-center justify-center gap-1 py-2 text-xs
       {{ request()->routeIs('operativo.mis-reservas') || request()->routeIs('operativo.reservas-form') || request()->routeIs('operativo.reservas.reserva') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
        <i class="fas fa-calendar-check text-lg"></i>
        <span>Reservas</span>
    </a>

    <a href="{{ route('operativo.viajes.index') }}"
       class="flex-1 flex flex-col items-center justify-center gap-1 py-2 text-xs
       {{ request()->routeIs('operativo.viajes.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
        <i class="fas fa-route text-lg"></i>
        <span>Viajes</span>
    </a>

    <a href="{{ route('operativo.reportes.mis') }}"
       class="flex-1 flex flex-col items-center justify-center gap-1 py-2 text-xs
       {{ request()->routeIs('operativo.reportes.*') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
        <i class="fas fa-chart-line text-lg"></i>
        <span>Reportes</span>
    </a>

    <a href="{{ route('profile.show') }}"
       class="flex-1 flex flex-col items-center justify-center gap-1 py-2 text-xs
       {{ request()->routeIs('profile.show') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
        <i class="fas fa-user-circle text-lg"></i>
        <span>Perfil</span>
    </a>
</nav>
