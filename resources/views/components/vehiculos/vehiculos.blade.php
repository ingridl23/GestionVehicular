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
        </div>

        <div id="vehiculos-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse ($vehiculos as $vehiculo)
                <div onclick="window.location.href='{{ url('/vehiculos/'.$vehiculo->id) }}'"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg cursor-pointer transition">

                    <div class="bg-gray-200 dark:bg-gray-700 h-40 flex items-center justify-center">
                        <i class="fa-solid fa-car text-5xl text-gray-400"></i>
                    </div>

                 <!--    <div class="p-4">
                        <h3 class="font-semibold text-lg text-gray-900 dark:text-white">
                             $vehiculo->marca }}  $vehiculo->modelo }}
                        </h3>

                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Dominio:  $vehiculo->dominio }}
                        </p>

                        <p class="text-sm mt-1">
                            <span class="px-2 py-1 rounded text-xs
                                 $vehiculo->estadoVehiculo->estado === 'Disponible' , 'bg-green-100 text-green-800'}}">
                                 $vehiculo->estadoVehiculo->estado }}
                            </span>
                        </p>

                        <button onclick="event.stopPropagation(); window.location.href='{ url('/vehiculos/'.$vehiculo->id) }}'"
                            class="mt-3 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded text-sm transition">
                            Ver detalle
                        </button>
                    </div> -->
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


@endsection
