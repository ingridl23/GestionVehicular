@extends('layout.app')

@section('page-title', 'Auditoria')
@section('page-description', 'Resumen general del sistema de gestión vehicular')

@section('content')

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">

    <x-stat-card
        title="Vehículos Activos"
        value="{{$reservados}}"
        icon="fa-car"

        color="blue"
    />
<!--agregar contadores que faltan aca  -->
    <x-stat-card
        title="Reservas Activas"
           value="{{$reservascount}}"
        icon="fa-calendar-check"

        color="green"
    />

    <x-stat-card
        title="Reportes Pendientes"
          value="{{$reportesp}}"
        icon="fa-file-alt"


        color="yellow"
    />

        <x-stat-card
        title="Reportes Abiertos"
          value="{{$reportesA}}"
        icon="fa-file-alt"

        :trend-up="true"
        color="yellow"
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
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{$disponibles}}</p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Disponibles</p>
        </div>

         <!-- En total -->
        <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/10 rounded-lg">
            <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-road text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{$total}}</p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">En Total</p>
        </div>

        <!-- Reservado -->
        <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/10 rounded-lg">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-calendar text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{$reservados}}</p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Reservados</p>
        </div>


        <!-- Mantenimiento -->
        <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/10 rounded-lg">
            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-wrench text-orange-600 dark:text-orange-400 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{$mantenimiento}}</p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Mantenimiento</p>
        </div>

        <!-- Fuera de Servicio -->
        <div class="text-center p-4 bg-red-50 dark:bg-red-900/10 rounded-lg">
            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-times-circle text-red-600 dark:text-red-400 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{$baja}}</p>
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
@php
    $estado = strtoupper($vehiculo->estadoVehiculo?->estado ?? '');
@endphp

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
            <a href="   {{ route('reservas.internas') }} "
               class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                Ver todas
            </a>
        </div>

        <div class="space-y-3">
            @forelse($ultimasReservas as $reserva)
                <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50">

                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 font-semibold text-sm">
                      {{ strtoupper(substr($reserva->usuario?->name ?? 'N', 0, 1)) }}
                    </div>

                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                              {{ $reserva->vehiculo?->marca }} {{ $reserva->vehiculo?->modelo }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                             {{ $reserva->fecha_inicio_reserva?->format('d/m/Y H:i') }}
                        </p>
                    </div>
@php
    $estado = strtoupper($reserva->estado_reserva?->estado ?? '');
@endphp

              <span class="px-3 py-1 text-xs font-medium rounded-full
                @if($reserva->estado_reserva?->estado === 'PENDIENTE')
                    bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400
                @elseif($reserva->estado_reserva?->estado === 'EN_CURSO')
                    bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400
                @else
                    bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400
                @endif">
                {{ $reserva->estado_reserva?->estado ?? 'N/A' }}
            </span>


                </div>
        @empty
        <p class="text-sm text-gray-500 dark:text-gray-400">
            No hay reservas recientes
        </p>
                @endforelse
            </div>
        </div>

    </div>

{{-- aca va otra fila de resumen para cambios de conductores --}}

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    <!-- Recent Conductors -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Conductores Asignados recientemente</h2>
            <a href="   {{ route('reservas.internas') }} "  class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                Ver todos
            </a>
        </div>


        <div class="space-y-3">
            @forelse($ultimosConductores as $conductor)
                <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50">

                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-car text-gray-600 dark:text-gray-400"></i>
                    </div>

                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ strtoupper(substr($conductor->usuario?->name ?? 'N', 0, 1)) }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $conductor->vehiculo->marca }} {{ $conductor->vehiculo->modelo }}
                        </p>
                           <p class="text-xs text-gray-500 dark:text-gray-400">
                             {{ $conductor->reserva->fecha_inicio_reserva->format('d/m/Y H:i') }}
                        </p>
                    </div>

                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    No hay conductores modificados recientemente
                </p>
            @endforelse
        </div>
    </div>


    <!-- Recent System for Users -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Usuarios registrados recientemente</h2>
            <a href="   {{ route('admin.usuarios.index') }} "  class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                Ver todos
            </a>
        </div>


        <div class="space-y-3">
            @forelse($ultimosUsuarios as $usuario)
                <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50">

                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-car text-gray-600 dark:text-gray-400"></i>
                    </div>

                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ strtoupper(substr($usuario->name ?? 'N', 0, 1)) }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $usuario->getRoleNames()->implode(', ') }} {{ $usuario->dependencia->nombre ?? 'sin dependencia'}}
                        </p>
                           <p class="text-xs text-gray-500 dark:text-gray-400">
                             {{ $usuario->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    No hay usuarios añadidos recientemente
                </p>
            @endforelse
        </div>
    </div>

@endsection

