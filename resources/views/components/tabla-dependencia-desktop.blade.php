<div data-view="tabla">
    @props([
        'dependencias',
    ])
   <table class="w-full table-auto border-collapse">
              <thead>
                  <tr class="bg-blue-600 dark:bg-blue-800 text-center">

                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white bg-blue-600 dark:bg-blue-800 lg:px-4 lg:py-7">
                    Nombre
                  </th>
                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white bg-blue-600 dark:bg-blue-800 lg:px-4 lg:py-7">
                    Calle
                  </th>
                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white bg-blue-600 dark:bg-blue-800 lg:px-4 lg:py-7">
                    Altura
                  </th>
                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white bg-blue-600 dark:bg-blue-800 lg:px-4 lg:py-7">
                    Ciudad
                  </th>
                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white bg-blue-600 dark:bg-blue-800 lg:px-4 lg:py-7">
                    Activa
                  </th>

                  @canany(['crear_dependencias', 'editar_dependencias', 'eliminar_dependencias', 'ver_dependencias'])
                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white bg-blue-600 dark:bg-blue-800 lg:px-4 lg:py-7">
                    Acciones
                  </th>
                  @endcanany
                </tr>
              </thead>

              <tbody>
                @foreach ($dependencias as $dependencia)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">

                  <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">
                    {{ $dependencia->nombre }}
                  </td>

                  <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">
                    {{ $dependencia->direccion->calle}}
                  </td>

                  <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">
                    {{ $dependencia->direccion->altura}}
                  </td>

                  <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">
                    {{ $dependencia->direccion->ciudad }}
                  </td>

                  <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center">
                      <label class="relative inline-flex w-11 h-6 cursor-pointer items-center">
                          <input type="checkbox" class="peer sr-only" {{$dependencia->activa ? 'checked' : ''}}>

                          <span class="absolute inset-0 rounded-full bg-gray-400 transition-colors peer-checked:bg-blue-600">
                          </span>

                          <span class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-5">
                          </span>
                      </label>
                  </td>


                  <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">
                    
                    @can('ver_dependencias')
                    <a href="{{ route('dependencia.show', $dependencia->id) }}"
                      class="m-1 inline-block rounded-md border border-blue-600 px-2 py-2 text-blue-600 hover:bg-blue-600 hover:text-white dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white"
                       title="Ver detalles">
                      <i class="fa-solid fa-eye"></i>
                    </a>
                    @endcan

                    @can('editar_dependencias')
                      <a href="#" data-id="{{$dependencia->id}}"
                        class="btn-editar m-1 inline-block rounded-md border border-yellow-600 px-2 py-2 text-yellow-600 hover:bg-yellow-600 hover:text-white dark:border-yellow-400 dark:text-yellow-400 dark:hover:bg-yellow-500 dark:hover:text-white"
                        title="Editar">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </a>
                    @endcan

                    @can('eliminar_dependencias')
                        <button command="show-modal" commandfor="dialog-cancelar" data-id="{{$dependencia->id}}"
                            class="btn-cancelar m-1 inline-block rounded-md border border-red-600 px-2 py-2 text-red-600 hover:bg-red-600 hover:text-white dark:border-red-400 dark:text-red-400 dark:hover:bg-red-500 dark:hover:text-white"
                            title="Cancelar" >
                            <i class="fa fa-times"></i>
                        </button>
                    @endcan

                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
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

    // acá después podés meter fetch / axios para guardar en BD
    // fetch(`/dependencias/${btn.dataset.id}/toggle`, { method: 'POST' })
}
</script>