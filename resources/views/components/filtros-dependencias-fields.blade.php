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
                   class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                   <i class="fas fa-search mr-1"></i>
                Buscar
            </label>
            <input type="text" name="nombre" id="nombre-filtro" placeholder="Nombre de la dependencia"
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                           bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                           focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <!-- Ciudad -->

        <div class="flex flex-col">
            <label for="ciudad-filtro"
                   class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                   <i class="fas fa-city mr-1"></i>
                Ciudad
            </label>
            <select name="ciudad-filtro" id="ciudad-filtro"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                        bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                        focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="default">Todas</option>
                @foreach ($localidades as $localidad)
                    <option value="{{ $localidad->ciudad }}">{{ $localidad->ciudad }}</option>
                @endforeach
            </select>
        </div>

        <!-- Calle -->
        <div class="flex flex-col">
            <label for="calle-filtro"
                   class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                   <i class="fas fa-road mr-1"></i>
                Calle
            </label>
            <input type="text" name="calle-filtro" id="calle-filtro" placeholder="Nombre de la calle"
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                           bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                           focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>


        <!-- Activa -->
        <div class="flex flex-col">
            <label for="activa-filtro"
                   class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                   <i class="fas fa-toggle-on mr-1"></i>
                Activa
            </label>
            <select name="activa-filtro" id="activa-filtro" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                        bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                        focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Todas</option>
                <option value="1">Si</option>
                <option value="0">No</option>
            </select>
        </div>

        <!-- Dependencia padre -->
        <div class="flex flex-col">
            <label for="id_dependencia_padre"
                   class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                   <i class="fas fa-building mr-1"></i>
                Dependencia padre
            </label>
            <select name="id_dependencia_padre" id="id_dependencia_padre"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                        bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                        focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="default">Todas</option>
                @foreach ($dependencias as $dependencia)
                    <option value="{{ $dependencia->id }}">{{ $dependencia->nombre }}</option>
                @endforeach
            </select>
        </div>



        <!-- Boton -->
        
        <div class="flex flex-col md:flex-row md:justify-end md:items-end gap-2">

            <button id="busquedaFiltrosDependencias" type="submit"
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