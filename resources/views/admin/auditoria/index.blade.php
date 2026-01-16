@extends('layout.app')

@section('page-title', 'Dashboard')
@section('page-description', 'Resumen general del sistema de gestión vehicular')

@section('content')

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">

    <x-stat-card
        title="Vehículos Activos"
        value="42"
        icon="fa-car"
        trend="+3 este mes"
        :trend-up="true"
        color="blue"
    />

    <x-stat-card
        title="Reservas Activas"
        value="8"
        icon="fa-calendar-check"
        trend="2 pendientes"
        :trend-up="false"
        color="green"
    />

    <x-stat-card
        title="Reportes Abiertos"
        value="5"
        icon="fa-file-alt"
        trend="-2 desde ayer"
        :trend-up="true"
        color="yellow"
    />

    <x-stat-card
        title="Alertas Activas"
        value="3"
        icon="fa-bell"
        trend="Requieren atención"
        :trend-up="false"
        color="red"
    />

</div>

<!-- Vehicle Status Overview -->
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Estado de Vehículos</h2>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">

        <!-- Disponible -->
        <div class="text-center p-4 bg-green-50 dark:bg-green-900/10 rounded-lg">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-check text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">24</p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Disponibles</p>
        </div>

        <!-- Reservado -->
        <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/10 rounded-lg">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-calendar text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">8</p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Reservados</p>
        </div>

        <!-- En Uso -->
        <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/10 rounded-lg">
            <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-road text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">6</p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">En Uso</p>
        </div>

        <!-- Mantenimiento -->
        <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/10 rounded-lg">
            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-wrench text-orange-600 dark:text-orange-400 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">3</p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Mantenimiento</p>
        </div>

        <!-- Fuera de Servicio -->
        <div class="text-center p-4 bg-red-50 dark:bg-red-900/10 rounded-lg">
            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-times-circle text-red-600 dark:text-red-400 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">1</p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Fuera de Servicio</p>
        </div>

    </div>
</div>

<!-- Two Column Layout -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    <!-- Recent Vehicles -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Vehículos Recientes</h2>
            <a href="{{ route('vehiculos.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                Ver todos
            </a>
        </div>

        <div class="space-y-3">
            @for($i = 1; $i <= 4; $i++)
            <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-car text-gray-600 dark:text-gray-400"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Toyota Corolla 202{{ $i }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">AA-{{ 100 + $i }}-BB</p>
                </div>
                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $i % 2 == 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' }}">
                    {{ $i % 2 == 0 ? 'Disponible' : 'Reservado' }}
                </span>
            </div>
            @endfor
        </div>
    </div>

    <!-- Recent Reservations -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Reservas Recientes</h2>
            <a href="{{ route('reservas.internas') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                Ver todas
            </a>
        </div>

        <div class="space-y-3">
            @for($i = 1; $i <= 4; $i++)
            <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 font-semibold text-sm">
                    JD
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Reunión Cliente {{ $i }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ now()->addDays($i)->format('d M Y') }}</p>
                </div>
                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $i == 1 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400' : 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400' }}">
                    {{ $i == 1 ? 'Pendiente' : 'Aprobada' }}
                </span>
            </div>
            @endfor
        </div>
    </div>

</div>

<!-- Alerts Section -->
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Alertas y Notificaciones</h2>
        <a href="{{ route('alertas.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
            Ver todas
        </a>
    </div>

    <div class="space-y-3">

        <!-- Alert Item -->
        <div class="flex items-start gap-4 p-4 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900/20 rounded-lg">
            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-900 dark:text-white">Vehículo requiere mantenimiento</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Toyota Corolla (AA-101-BB) - Vencimiento en 3 días</p>
            </div>
            <span class="text-xs text-gray-500 dark:text-gray-400">Hace 2h</span>
        </div>

        <!-- Warning Item -->
        <div class="flex items-start gap-4 p-4 bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-900/20 rounded-lg">
            <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-info-circle text-yellow-600 dark:text-yellow-400"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-900 dark:text-white">Reserva pendiente de aprobación</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Juan Pérez - Honda Civic para mañana</p>
            </div>
            <span class="text-xs text-gray-500 dark:text-gray-400">Hace 4h</span>
        </div>

        <!-- Info Item -->
        <div class="flex items-start gap-4 p-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/20 rounded-lg">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-bell text-blue-600 dark:text-blue-400"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-900 dark:text-white">Nuevo vehículo agregado al sistema</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Nissan Sentra 2024 (AA-105-BB)</p>
            </div>
            <span class="text-xs text-gray-500 dark:text-gray-400">Ayer</span>
        </div>

    </div>
</div>

@endsection
