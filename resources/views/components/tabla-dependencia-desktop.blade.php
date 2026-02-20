<div data-view="tabla" class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
  @props([
  'dependencias',
  ])
  <div class="overflow-x-auto">

    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
      
      <thead class="bg-gray-50 dark:bg-gray-900">
        <tr>

          <th class="w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Nombre
          </th>
          <th class="w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Calle
          </th>
          <th class="w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Altura
          </th>
          <th class="w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Ciudad
          </th>
          <th class="w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Activa
          </th>

          @canany(['crear_dependencias', 'editar_dependencias', 'eliminar_dependencias', 'ver_dependencias'])
          <th class="w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Acciones
          </th>
          @endcanany
        </tr>
      </thead>

      <tbody id="contenedor-dependencias" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        @foreach ($dependencias as $dependencia)
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">

          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
            {{ $dependencia->nombre }}
          </td>

          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
            {{ $dependencia->direccion->calle}}
          </td>

          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
            {{ $dependencia->direccion->altura}}
          </td>

          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
            {{ $dependencia->direccion->ciudad }}
          </td>

          @role('Administrador General|Administrador de Dependencia')
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
            <label class="relative inline-flex w-11 h-6 cursor-pointer items-center">

              <input type="checkbox" class="peer sr-only toggle-activa" id="check-activa-{{ $dependencia->id }}"
                {{ $dependencia->activa ? 'checked' : '' }}
                data-id="{{ $dependencia->id }}"
                data-nombre="{{ $dependencia->nombre }}">

              <span class="absolute inset-0 rounded-full bg-gray-400 transition-colors peer-checked:bg-blue-600"></span>

              <span class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-5"></span>

            </label>
          </td>
          @else
            <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                  {{ $dependencia->activa ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                  {{ $dependencia->activa ? 'Activa' : 'Inactiva' }}
                </span>
            </td>
          @endrole


          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right font-medium">
            <div class="flex justify-start gap-4">
            @can('ver_dependencias')
            <a href="{{ route('dependencias.show', $dependencia->id) }}"
              class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
              title="Ver detalles">
              <i class="fas fa-eye"></i>
            </a>
            @endcan

            @can('editar_dependencias')
            <a href="{{ route('dependencias.edit', $dependencia->id) }}" data-id="{{$dependencia->id}}"
              class="btn-editar text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
              title="Editar">
              <i class="fas fa-edit"></i>
            </a>
            @endcan

            @can('eliminar_dependencias')
            <button command="show-modal" commandfor="dialog-cancelar" data-id="{{$dependencia->id}}"
              class="btn-cancelar text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
              title="eliminar">
              <i class="fas fa-trash"></i>
            </button>
            @endcan
                          
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

  </div>
</div>

<script>
  function toggleDependencia(btn) {
    const activa = btn.dataset.activa === '1';

    // alternar colores
    btn.classList.toggle('bg-blue-600', !activa);
    btn.classList.toggle('bg-gray-400', activa);

    const dot = btn.querySelector('span');
    dot.classList.toggle('translate-x-6', !activa);
    dot.classList.toggle('translate-x-1', activa);

    // actualizar estado
    btn.dataset.activa = activa ? 0 : 1;

  }
</script>