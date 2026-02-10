<div id="filtrosDependencia">
    @props([
        'dependencias',
        'localidades' ,
    ])

    <form action="/filtrar-dependencias" method="get" id="formFiltrosDependencias" class="mb-6 rounded-lg bg-white dark:bg-gray-800 shadow-md p-4">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

        <!-- Nombre -->
        <div class="flex flex-col">
            <label for="nombre-filtro"
                   class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                Buscar por nombre
            </label>
            <input type="text" name="nombre" id="nombre-filtro"
                   class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                          px-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Ciudad -->

        <div class="flex flex-col">
            <label for="ciudad-filtro"
                   class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                Ciudad
            </label>
            <select name="ciudad-filtro" id="ciudad-filtro"
                    class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                           px-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="default">Seleccionar</option>
                @foreach ($localidades as $localidad)
                    <option value="{{ $localidad->ciudad }}">{{ $localidad->ciudad }}</option>
                @endforeach
            </select>
        </div>

        <!-- Calle -->
        <div class="flex flex-col">
            <label for="calle-filtro"
                   class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                Calle
            </label>
            <input type="text" name="calle-filtro" id="calle-filtro"
                   class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                          px-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>


        <!-- Activa -->
        <div class="flex flex-col">
            <label for="activa-filtro"
                   class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                Activa
            </label>
            <select name="activa-filtro" id="activa-filtro">
                <option value="">Seleccionar</option>
                <option value="1">Si</option>
                <option value="0">No</option>
            </select>
        </div>

        <!-- Dependencia padre -->
        <div class="flex flex-col">
            <label for="id_dependencia_padre"
                   class="mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                Dependencia padre
            </label>
            <select name="id_dependencia_padre" id="id_dependencia_padre"
                    class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                           px-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="default">Seleccionar</option>
                @foreach ($dependencias as $dependencia)
                    <option value="{{ $dependencia->id }}">{{ $dependencia->nombre }}</option>
                @endforeach
            </select>
        </div>



        <!-- Boton -->
        
        <div class="flex items-end">
            <button id="limpiarFiltros" type="button"
                class="w-full mr-1 rounded-md bg-gray-400 px-4 py-2 text-sm font-medium text-white
                        hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:hover:bg-gray-900">
                Limpiar filtros
            </button>
            <button id="busquedaFiltrosDependencias" type="submit"
                    class="w-full ml-1 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white
                           hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Buscar
            </button>
            
        </div>

    </div>
</form>
</div>