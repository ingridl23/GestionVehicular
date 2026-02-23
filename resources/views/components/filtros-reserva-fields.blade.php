<div >
    @props([
        'vehiculos_filtros',
        'estados_filtros' ,
        'ubicacion',
    ])

    <form action="/filtrar-reservas" data-busqueda="{{$ubicacion}}" method="get" id="formFiltrosReservas" class="mb-6 rounded-lg bg-white dark:bg-gray-800 shadow-md p-4">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

        <!-- Buscar -->
        <div class="flex flex-col">
            <label for="nombre-filtro"
                   class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                   <i class="fas fa-search mr-1"></i>
                Buscar
            </label>
            <input type="text" name="nombre" id="nombre-filtro" placeholder="Conductor u oficina solicitante"

                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                           bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                           focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <!-- Fecha inicio -->
        <div class="flex flex-col">
            <label for="fecha-inicio"
                   class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                   <i class="fas fa-calendar-alt mr-1"></i>
                Fecha inicio
            </label>
            
            <input type="date" name="fecha_inicio" id="fecha-inicio"
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                           bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                           focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <!-- Fecha fin -->
        <div class="flex flex-col">
            <label for="fecha-fin"
                   class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                   <i class="fas fa-calendar-alt mr-1"></i>
                Fecha fin
            </label>
            <input type="date" name="fecha_fin" id="fecha-fin"
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                           bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                           focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <!-- Estado -->
        <div class="flex flex-col">
            <label for="estado-filtro"
                   class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                   <i class="fas fa-tasks mr-1"></i>
                Estado
            </label>
            <select name="estado" id="estado-filtro"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                            bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                            focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="default">Todos</option>
                @foreach ($estados_filtros as $estado)
                    <option value="{{ $estado->id }}">{{ $estado->estado }}</option>
                @endforeach
            </select>
        </div>

        <!-- Vehículo -->
        <div class="flex flex-col">
            <label for="vehiculo-filtro"
                   class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                   <i class="fas fa-car mr-1"></i>
                Vehículo
            </label>
            <select name="vehiculo" id="vehiculo-filtro"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                        bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                        focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="default">Todos</option>
                @foreach ($vehiculos_filtros as $vehiculo)
                    <option value="{{ $vehiculo->id }}">{{ $vehiculo->dominio }}</option>
                @endforeach
            </select>
        </div>

        <!-- Boton -->

        <div class="flex flex-col md:flex-row md:justify-end md:items-end gap-2">

            <button id="busquedaFiltrosReservas" type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg 
                    flex items-center gap-2 transition-colors justify-center">
                <i class="fas fa-filter"></i>
                Filtrar
            </button>

            <button id="limpiarFiltros" type="button"
                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg 
                    flex items-center gap-2 transition-colors justify-center">
                <i class="fas fa-times"></i>
                Limpiar
            </button>

        </div>

    </div>
</form>
</div>