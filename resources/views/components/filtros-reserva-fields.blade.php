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
                   class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                Buscar por conductor u oficina
            </label>
            <input type="text" name="nombre" id="nombre-filtro"
                   class="rounded-md border border-gray-300 dark:border-gray-600
                          bg-white dark:bg-gray-700
                          px-3 py-2 text-sm text-gray-700 dark:text-gray-200
                          focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Fecha inicio -->
        <div class="flex flex-col">
            <label for="fecha-inicio"
                   class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                Fecha inicio
            </label>
            <input type="date" name="fecha_inicio" id="fecha-inicio"
                   class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                          px-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Fecha fin -->
        <div class="flex flex-col">
            <label for="fecha-fin"
                   class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                Fecha fin
            </label>
            <input type="date" name="fecha_fin" id="fecha-fin"
                   class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                          px-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Estado -->
        <div class="flex flex-col">
            <label for="estado-filtro"
                   class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                Estado
            </label>
            <select name="estado" id="estado-filtro"
                    class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                           px-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="default">Seleccionar</option>
                @foreach ($estados_filtros as $estado)
                    <option value="{{ $estado->id }}">{{ $estado->estado }}</option>
                @endforeach
            </select>
        </div>

        <!-- Vehículo -->
        <div class="flex flex-col">
            <label for="vehiculo-filtro"
                   class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                Vehículo
            </label>
            <select name="vehiculo" id="vehiculo-filtro"
                    class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                           px-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="default">Seleccionar</option>
                @foreach ($vehiculos_filtros as $vehiculo)
                    <option value="{{ $vehiculo->id }}">{{ $vehiculo->dominio }}</option>
                @endforeach
            </select>
        </div>

        <!-- Boton -->
        <div class="flex items-end">
            <button id="busquedaFiltrosReservas" type="submit"
                    class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white
                           hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Buscar
            </button>
        </div>

    </div>
</form>
</div>