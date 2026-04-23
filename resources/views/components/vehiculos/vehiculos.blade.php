@extends('layout.app')

@section('page-title', 'Listado de vehículos')
@section('page-description', 'Gestión de vehículos del sistema')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-wrap gap-4 mb-6 items-center">
            <div class="relative flex-grow md:flex-grow-0">
                <input type="text" id="search-vehiculos" placeholder="Buscar por dominio, marca..."
                    class="w-64 pl-10 pr-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-white"
            >
  <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>

            </div>

            <select id="filter-dependencia" class="rounded-lg border-gray-300 dark:bg-gray-700">
                <option value="">Todas las dependencias</option>
                @foreach($dependencias as $dep)
                    <option value="{{ $dep->id }}">{{ $dep->nombre }}</option>
                @endforeach
            </select>

            <select id="filter-estado" class="rounded-lg border-gray-300 dark:bg-gray-700">
                <option value="">Todos los estados</option>
                @foreach($estados as $estado)
                    <option value="{{ $estado->id }}">{{ $estado->estado }}</option>
                @endforeach
            </select>

            <button id="btn-filtrar" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition shadow-sm">
                <i class="fa-solid fa-filter mr-2"></i>Filtrar
            </button>
              <button id="btn-abrir" class="bg-green-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition shadow-sm">
                <i class="fa-solid fa-car mr-2"></i>+
            </button>
        </div>

        <div id="vehiculos-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse ($vehiculos as $vehiculo)
                <div onclick="window.location.href='{{ url('/vehiculos/'.$vehiculo->id) }}'"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg cursor-pointer transition">

                    <div class="bg-gray-200 dark:bg-gray-700 h-40 flex items-center justify-center">
                        <i class="fa-solid fa-car text-5xl text-gray-400"></i>
                    </div>

                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500">No hay vehículos cargados</p>
                </div>
            @endforelse
        </div>

        <div class="flex justify-center items-center gap-2 mt-8">
            <button id="prev-page" class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 transition">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <span id="pagination-info" class="px-4 py-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                1 / 1
            </span>
            <button id="next-page" class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 transition">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

@include('components.vehiculos.vehiculo-modal-crear')
 <div class="contenedor_loader">
        <div class="loader"></div>
    </div>

@push('scripts')
<script>
    // Catálogos para el modal
    window.CATALOGOS = {
        dependencias: @json($dependencias),
        direcciones: @json($direcciones),
        estadosVehiculo: @json($estados),
        estadosNafta: @json($estadosNafta),
    };
</script>

@vite(['resources/js/vehiculo.js'])
@endpush
@endsection
