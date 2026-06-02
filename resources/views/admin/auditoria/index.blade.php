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

                <div class="flex gap-3">
        <a href="{{ route('vehiculos.export') }}"
         title="Descargar formato Excel"
           class="text-sm text-green-600 dark:text-green-400 hover:underline flex items-center gap-1">

            <i class="fas fa-download"></i>

        </a>

        <a href="{{ route('vehiculos.index') }}"
           class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
           Ver todos
        </a>
    </div>
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
           <div class="flex gap-3">
        <a href="{{ route('reservas.export') }}"
             title="Descargar formato Excel ultimos 4 meses"
           class="text-sm text-green-600 dark:text-green-400 hover:underline flex items-center gap-1">

            <i class="fas fa-download"></i>
        </a>

        <a href="{{ route('reservas.internas') }}"
           class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
           Ver todas
        </a>
    </div>
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

          <div class="flex gap-3">
        <a href="{{ route('conductores.export') }}"
            title="Descargar formato Excel ultimos 4 meses "
           class="text-sm text-green-600 dark:text-green-400 hover:underline flex items-center gap-1">

            <i class="fas fa-download"></i>
        </a>
            <a href="   {{ route('reservas.internas') }} "  class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                Ver todos
            </a>
          </div>
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
                           {{ $conductor->fecha_inicio_reserva?->format('d/m/Y H:i') ?? 'Sin fecha' }}
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
          <div class="flex gap-3">
        <a href="{{ route('usuarios.export') }}"
           title="Descargar formato Excel "
           class="text-sm text-green-600 dark:text-green-400 hover:underline flex items-center gap-1">

            <i class="fas fa-download"></i>
        </a>

        <a href="{{ route('admin.usuarios.index') }}"
           class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
           Ver todos
        </a>
    </div>
        </div>


        <div class="space-y-3">
            @forelse($ultimosUsuarios as $usuario)
                <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50">

                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user text-gray-600 dark:text-gray-400"></i>
                    </div>

                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $usuario->name }} {{ $usuario->lastname }}
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




 <!-- Recent System for Reports -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Reportes registrados recientemente</h2>
          <div class="flex gap-3">
        <a href="{{ route('reportes.export') }}"
           title="Descargar ultimos 3 meses a formato Excel "
           class="text-sm text-green-600 dark:text-green-400 hover:underline flex items-center gap-1">

            <i class="fas fa-download"></i>
        </a>

        <a href="{{ route('admin.reportes.index') }}"
           class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
           Ver todos
        </a>
    </div>
        </div>


        <div class="space-y-3">
            @forelse($ultimosReportes as $reporte)
                <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50">

                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user text-gray-600 dark:text-gray-400"></i>
                    </div>

                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white"> {{ $reporte->titulo }} </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400"> {{ Str::limit($reporte->descripcion, 50) }} </p>
                         <p class="text-xs text-gray-400 mt-1"> {{ $reporte->usuario?->name }} • {{ $reporte->created_at->format('d/m/Y H:i') }} </p>

                    </div>

                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    No hay reportes enviados recientemente
                </p>
            @endforelse
        </div>
    </div>















</div>

{{-- calculadora de gastos del sistema --}}
{{--
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">


    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Calculadora de gastos en reservas efectuadas en el sistema </h2>

      <div class="flex gap-3">
    <a href="{{ route('gastos.export') }}"
        title="Descargar formato Excel ultimos 6 meses "
       class="text-sm text-green-600 dark:text-green-400 hover:underline flex items-center gap-1">

        <i class="fas fa-download"></i>
    </a>
      </div>



</div>
</div>

--}}

{{-- ============================================================
 CALCULADORA DE GASTOS - Sección para dashboard de Auditoría
 Incluir dentro de @section('content') en auditoria/index.blade.php
 Reemplaza el div vacío de "calculadora de gastos" existente
 ============================================================ --}}







<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 mb-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Calculadora de Gastos en Combustible
            </h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Estimá el costo de un viaje antes de registrarlo
            </p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Precio actual del combustible (se carga desde el backend si está disponible) --}}
            @if(isset($precioLitroActual) && $precioLitroActual)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-xs font-medium rounded-full border border-green-200 dark:border-green-800">
                    <i class="fas fa-gas-pump text-xs"></i>
                    Precio Estimativo Cargado: ${{ number_format($precioLitroActual, 2, ',', '.') }}/L
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 text-xs font-medium rounded-full border border-yellow-200 dark:border-yellow-800">
                    <i class="fas fa-exclamation-triangle text-xs"></i>
                    Precio no disponible
                </span>
            @endif

            <a href="{{ route('gastos.export') }}"
               title="Descargar gastos últimos 6 meses"
               class="text-sm text-green-600 dark:text-green-400 hover:underline flex items-center gap-1">
                <i class="fas fa-download"></i>
            </a>
        </div>
    </div>

    {{-- Grid: Formulario + Resultado + Resumen --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ---- COLUMNA 1: Formulario de cálculo manual ---- --}}
        <div class="lg:col-span-1 bg-gray-50 dark:bg-gray-700/40 rounded-xl p-5 border border-gray-100 dark:border-gray-700">

            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center gap-2">
                <i class="fas fa-calculator text-blue-500"></i>
                Cálculo manual
            </h3>

            <div class="space-y-4" id="calculadora-form">

                {{-- Litros consumidos --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                        Litros consumidos
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            id="calc-litros"
                            min="0"
                            step="0.1"
                            placeholder="Ej: 35.5"
                            class="w-full pl-3 pr-10 py-2.5 text-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        >
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">L</span>
                    </div>
                </div>

                {{-- Precio por litro --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                        Precio por litro
                        @if(isset($precioLitroActual) && $precioLitroActual)
                            <button
                                type="button"
                                onclick="usarPrecioActual()"
                                class="ml-2 text-blue-500 hover:text-blue-600 underline text-xs font-normal"
                            >
                                Usar precio actual
                            </button>
                        @endif
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">$</span>
                        <input
                            type="number"
                            id="calc-precio"
                            min="0"
                            step="0.01"
                            placeholder="Ej: 1450.00"
                            value="{{ isset($precioLitroActual) ? $precioLitroActual : '' }}"
                            class="w-full pl-7 pr-3 py-2.5 text-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        >
                    </div>
                </div>

                {{-- Kilómetros (opcional, informativo) --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                        Kilómetros recorridos
                        <span class="text-gray-400 font-normal">(opcional)</span>
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            id="calc-km"
                            min="0"
                            step="1"
                            placeholder="Ej: 180"
                            class="w-full pl-3 pr-10 py-2.5 text-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        >
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">km</span>
                    </div>
                </div>

                {{-- Botón calcular --}}
                <button
                    type="button"
                    onclick="calcularGasto()"
                    class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-medium rounded-lg transition flex items-center justify-center gap-2"
                >
                    <i class="fas fa-calculator"></i>
                    Calcular
                </button>

                <button
                    type="button"
                    onclick="limpiarCalculadora()"
                    class="w-full py-2 px-4 bg-transparent hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs rounded-lg transition border border-gray-200 dark:border-gray-600"
                >
                    Limpiar
                </button>

            </div>
        </div>

        {{-- ---- COLUMNA 2: Resultado del cálculo ---- --}}
        <div class="lg:col-span-1 flex flex-col gap-4">

            {{-- Resultado principal --}}
            <div id="resultado-container"
                 class="flex-1 bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800 rounded-xl p-5 flex flex-col items-center justify-center text-center min-h-[180px] transition-all duration-300">

                <div id="resultado-vacio" class="text-gray-400 dark:text-gray-500">
                    <i class="fas fa-gas-pump text-3xl mb-3 opacity-40"></i>
                    <p class="text-sm">Ingresá los datos para calcular el gasto estimado</p>
                </div>

                <div id="resultado-datos" class="hidden w-full">
                    <p class="text-xs text-blue-600 dark:text-blue-400 font-medium uppercase tracking-wider mb-1">Costo estimado</p>
                    <p id="resultado-monto" class="text-4xl font-bold text-blue-700 dark:text-blue-300 mb-1">$0</p>
                    <p id="resultado-detalle" class="text-xs text-gray-500 dark:text-gray-400"></p>

                    {{-- Info extra si se ingresaron km --}}
                    <div id="resultado-km-info" class="hidden mt-4 pt-4 border-t border-blue-200 dark:border-blue-800 grid grid-cols-2 gap-3 text-left">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Costo por km</p>
                            <p id="resultado-costo-km" class="text-sm font-semibold text-gray-700 dark:text-gray-300">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Consumo</p>
                            <p id="resultado-consumo" class="text-sm font-semibold text-gray-700 dark:text-gray-300">-</p>
                        </div>
                    </div>
                </div>

                <div id="resultado-error" class="hidden text-red-500 dark:text-red-400">
                    <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                    <p id="resultado-error-msg" class="text-sm"></p>
                </div>

            </div>

            {{-- Nota informativa --}}
            <div class="bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                <p class="text-xs text-yellow-700 dark:text-yellow-400 flex items-start gap-2">
                    <i class="fas fa-info-circle mt-0.5 flex-shrink-0"></i>
                    Este cálculo es estimativo. El gasto definitivo se genera automáticamente al finalizar el viaje dentro del sistema.
                </p>
                <br>
                <p class="text-xs text-yellow-700 dark:text-yellow-400 flex items-start gap-2">Para consultar el precio del combustible actual recomendamos visitar el siguiente enlace </p>
                <a class="text-xs text-blue-400 " href="https://surtidores.com.ar/precios/">click aqui</a>
            </div>

        </div>

        {{-- ---- COLUMNA 3: Estadísticas globales de gastos ---- --}}
        <div class="lg:col-span-1 bg-gray-50 dark:bg-gray-700/40 rounded-xl p-5 border border-gray-100 dark:border-gray-700">

            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-bar text-purple-500"></i>
                Resumen de gastos
            </h3>

            @if(isset($resumenGastos))
                <div class="space-y-3">

                    <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Total registrado</span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">
                            ${{ number_format($resumenGastos['gasto_total'] ?? 0, 2, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Promedio por viaje</span>
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            ${{ number_format($resumenGastos['gasto_promedio'] ?? 0, 2, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Gasto máximo</span>
                        <span class="text-sm font-semibold text-red-600 dark:text-red-400">
                            ${{ number_format($resumenGastos['max_gasto'] ?? 0, 2, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Gasto mínimo</span>
                        <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                            ${{ number_format($resumenGastos['min_gasto'] ?? 0, 2, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-2">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Viajes con gasto</span>
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            {{ $resumenGastos['cantidad_gastos'] ?? 0 }}
                        </span>
                    </div>

                </div>
            @else

                {{-- Placeholder si no se pasan datos desde el controller --}}
                <div class="space-y-3">
                    @foreach(['Total registrado', 'Promedio por viaje', 'Gasto máximo', 'Gasto mínimo', 'Viajes con gasto'] as $label)
                        <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-600 last:border-0">
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $label }}</span>
                            <span class="text-xs bg-gray-200 dark:bg-gray-600 text-gray-200 dark:text-gray-600 rounded px-8 py-1 animate-pulse">—</span>
                        </div>
                    @endforeach

        </div>
            @endif

        </div>

    </div>
</div>

<div class="contenedor_loader">
        <div class="loader"></div>
    </div>
<script>
    window.PRECIO_ACTUAL = {{ isset($precioLitroActual) && $precioLitroActual ? $precioLitroActual : 'null' }};
</script>


@endsection

