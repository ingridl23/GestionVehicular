@extends('layout.app')

@section('page-title', 'Auditoria')
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
            <a href="   {{ route('vehiculos.index') }} "  class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                Ver todos
            </a>
        </div>


        <div class="space-y-3">
            @forelse($ultimosVehiculos as $vehiculo)
                <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50">

                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-car text-gray-600 dark:text-gray-400"></i>
                    </div>

                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $vehiculo->dominio }}
                        </p>
                    </div>

                    <span class="px-3 py-1 text-xs font-medium rounded-full
                        @if($vehiculo->estadoVehiculo?->estado === 'Disponible')
                            bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400
                        @else
                            bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400
                        @endif">
                        {{ $vehiculo->estadoVehiculo->estado ?? 'N/A' }}
                    </span>

                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    No hay vehículos cargados recientemente
                </p>
            @endforelse
        </div>
    </div>

    <!-- Recent Reservations -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Reservas Recientes
            </h2>
            <a href="#"
               class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
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
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            Reunión Cliente {{ $i }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ now()->addDays($i)->format('d M Y') }}
                        </p>
                    </div>

                    <span class="px-3 py-1 text-xs font-medium rounded-full
                        {{ $i == 1
                            ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400'
                            : 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400' }}">
                        {{ $i == 1 ? 'Pendiente' : 'Aprobada' }}
                    </span>

                </div>
            @endfor
        </div>
    </div>

</div>





@endsection

