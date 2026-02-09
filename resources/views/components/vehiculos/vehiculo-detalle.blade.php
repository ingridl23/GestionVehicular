@extends('layout.app')

@section('page-title', 'Listado de vehiculos')
@section('page-description', 'Gestión de vehiculos del sistema , detalles')

@section('content')


<div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-4">
                    <button onclick="window.history.back()" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </button>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white" id="vehiculo-title">Vehículo</h1>
                </div>
                </div>
            </div>

            <!-- Contenedor principal -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Imagen del vehículo -->
                    <div class="lg:w-1/3">
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-8 flex items-center justify-center aspect-square">
                            <svg class="w-32 h-32 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Información del vehículo -->
                    <div class="lg:w-2/3">
                        <div class="mb-6">
                            <p class="text-gray-600 dark:text-gray-400 mb-4" id="vehiculo-descripcion">
                                Información detallada del vehículo
                            </p>
                        </div>

                        <!-- Tabla de información -->
                        <div class="overflow-x-auto">
                            <table class="w-full">

                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="vehiculo-table-body">
                                    <!-- Se llenará dinámicamente -->
                                </tbody>
                            </table>
{{--
 <p class="text-gray-600 dark:text-gray-400 mb-4">
    <strong>Dominio:</strong> {{ $vehiculo->dominio }}<br>
    <strong>Marca:</strong> {{ $vehiculo->marca }}<br>
    <strong>Modelo:</strong> {{ $vehiculo->modelo }}<br>
    <strong>Año:</strong> {{ $vehiculo->anio }}<br>
    <strong>Kilómetros:</strong> {{ number_format($vehiculo->kilometros, 0, ',', '.') }} km<br>
    <strong>VTV:</strong> {{ \Carbon\Carbon::parse($vehiculo->VTV)->format('d/m/Y') }}<br>
    <strong>Control Satelital:</strong> {{ $vehiculo->control_satelital ? 'Sí' : 'No' }}<br>
    <strong>Habilitación De Prestamo:</strong> {{ $vehiculo->habilitado_prestamo ? 'Sí' : 'No' }}<br>
    <strong>Condicion Para Prestamo:</strong> {{ $vehiculo->condiciones_prestamo }}<br>
    <strong>Estado Actual Del Vehiculo:</strong> {{ $vehiculo->estadoVehiculo->estado }}<br>
    <strong>Estado Del Combustible:</strong> {{ $vehiculo->estadoNafta->estado }}<br>
    <strong>Dependencia De Origen:</strong> {{ $vehiculo->id_dependencia_duena }}<br>
    <strong>Direccion Actual:</strong> {{ $vehiculo->id_direccion_actual }}<br>
   </p>
  --}}
</div>

                        <!-- Botones de acción -->
                        <div class="flex justify-end gap-4 mt-8">
                            <button id="btn-editar" data-id= "{{ $vehiculo->id }}" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </button>




    <button type="button"  id="btn-eliminar" data-id="{{ $vehiculo->id }}"
        class="p-2 rounded-lg hover:bg-red-100 dark:hover:bg-red-900 transition-colors">
        <svg class="w-6 h-6 text-red-600 dark:text-red-400"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
    </button>


                        </div>
                    </div>
                </div>
            </div>
        </div>

@include('components.vehiculos.vehiculo-modal')

@push('scripts')
<script>
    window.VEHICULO = @json($vehiculo);
    window.CATALOGOS = {
        dependencias: @json($dependencias),
        direcciones: @json($direcciones),
        estadosVehiculo: @json($estadosVehiculo),
        estadosNafta: @json($estadosNafta),
    };
</script>

@vite(['resources/js/vehiculo-detalle.js'])
@endpush


@endsection
