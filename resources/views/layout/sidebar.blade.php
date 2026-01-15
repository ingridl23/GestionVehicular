<!-- resources/views/layout/sidebar.blade.php -->
<aside
    x-data="{ open: true }"
    :class="open ? 'w-64' : 'w-20'"
    class="transition-all duration-200 border-r bg-slate-100 dark:bg-slate-900">

    <div class="flex items-center justify-between p-4">
        <span class="font-semibold text-slate-800 dark:text-slate-200" x-show="open">
            Gestión Vehicular
        </span>
        <button @click="open = !open" class="text-slate-600 dark:text-slate-300">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <nav class="px-2 text-sm space-y-1">

        @can('ver_auditoria')
            <x-sidebar.item icon="fa-gauge" label="Auditoría" route="auditoria.index" />
        @endcan

        @can('ver_vehiculos')
            <x-sidebar.item icon="fa-car" label="Vehículos" route="vehiculos.index" />
        @endcan

        @can('ver_personal_dependencia')
            <x-sidebar.item icon="fa-id-card" label="Personal" route="personal.index" />
        @endcan

        @can('ver_reservas_internas')
            <x-sidebar.group icon="fa-calendar-check" label="Reservas">
                <x-sidebar.subitem label="Internas" route="reservas.internas" />
                <x-sidebar.subitem label="Préstamos" route="reservas.prestamos" />
            </x-sidebar.group>
        @endcan
<li class="text-white font-bold">TEST ITEM</li>

        @can('ver_reportes_dependencia')
            <x-sidebar.item icon="fa-chart-line" label="Reportes" route="reportes.index" />
        @endcan

        @can('ver_dependencias')
            <x-sidebar.item icon="fa-building" label="Dependencias" route="dependencias.index" />
        @endcan

        @can('ver_todos_usuarios')
            <x-sidebar.item icon="fa-users" label="Usuarios" route="usuarios.index" />
        @endcan

        @can('ver_auditoria')
            <x-sidebar.item icon="fa-bell" label="Alertas" route="alertas.index" />
        @endcan

    </nav>

</aside>
