@extends('layout.app')

@section('page-title', 'Listado de vehiculos')
@section('page-description', 'Gestión de vehiculos del sistema')

@section('content')


    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Grid de tarjetas de vehículos -->
         <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

@forelse ($vehiculos as $vehiculo)
    <div
        onclick="window.location.href='{{ url('/vehiculos/'.$vehiculo->id) }}'"
        class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg cursor-pointer transition">

        <div class="bg-gray-200 dark:bg-gray-700 h-40 flex items-center justify-center">
            <i class="fa-solid fa-car text-5xl text-gray-400"></i>
        </div>

        <div class="p-4">
            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
            </h3>

            <p class="text-sm text-gray-600 dark:text-gray-400">
                Dominio: {{ $vehiculo->dominio }}
            </p>

            <p class="text-sm mt-1">
                <span class="px-2 py-1 rounded text-xs
                    {{ $vehiculo->estadoVehiculo->estado === 'Disponible'
                        ? 'bg-green-100 text-green-800'
                        : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $vehiculo->estadoVehiculo->estado }}
                </span>
            </p>

            <button
                onclick="event.stopPropagation(); window.location.href='{{ url('/vehiculos/'.$vehiculo->id) }}'"
                class="mt-3 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded text-sm">
                Ver detalle
            </button>
        </div>
    </div>
@empty
    <p class="col-span-full text-center text-gray-500">
        No hay vehículos cargados
    </p>
@endforelse

</div>


            <!-- Paginación -->
            <div class="flex justify-center items-center gap-2 mt-8">
                <button id="prev-page" class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50">
                    &lt;
                </button>
                <span id="pagination-info" class="px-4 py-1 text-gray-700 dark:text-gray-300"></span>
                <button id="next-page" class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50">
                    &gt;
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/vehiculos.js'])
    @endpush

@endsection
