<div id="dependnecias-wrapper" data-view="lista">
    @props([
        'dependencias',
    ])

    <ul id="contenedor-dependencias-listas" class="space-y-4 ">
        @foreach ($dependencias as $dependencia)
            <li class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 p-4">

                <div >
                    <div class="mt-2 text-sm dark:text-white">
                        <span class="font-semibold">Nombre:</span>
                        {{ $dependencia->nombre }}
                    </div>

                    <div class="mt-2 text-sm dark:text-white">
                        <span class="font-semibold">Calle:</span>
                        {{ $dependencia->direccion->calle }}
                    </div>
                </div>

                <div class="mt-2 text-sm dark:text-white">
                    <span class="font-semibold">Altura:</span>
                    {{ $dependencia->direccion->altura }}
                </div>

                <div class="mt-1 text-sm dark:text-white">
                    <span class="font-semibold">Ciudad:</span>
                    {{ $dependencia->direccion->ciudad }}
                </div>

                <div  class="mt-1 text-sm dark:text-white mt-3 flex">
                    <span class="font-semibold">Activa:</span>

                    @role('Administrador General|Administrador de Dependencia')
                        <label class="relative inline-flex w-11 h-6 cursor-pointer items-center ml-2">
                            <input type="checkbox"
                                class="peer sr-only toggle-activa"
                                id="check-activa-{{ $dependencia->id }}"
                                {{ $dependencia->activa ? 'checked' : '' }}
                                data-id="{{ $dependencia->id }}"
                                data-nombre="{{ $dependencia->nombre }}">

                            <span class="absolute inset-0 rounded-full bg-gray-400 transition-colors peer-checked:bg-blue-600"></span>
                            <span class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-5"></span>
                        </label>
                    @else
                        <span class="ml-1">
                            {{ $dependencia->activa ? 'Sí' : 'No' }}
                        </span>
                    @endrole
                </div>


                
                <div class="mt-4 flex flex-wrap gap-2">

                    @can('ver_dependencias')
                        <a href="{{ route('dependencias.show', $dependencia->id) }}"
                        class="m-1 inline-block rounded-md border border-blue-600 px-2 py-2 text-blue-600 hover:bg-blue-600 hover:text-white dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white"
                        title="Ver detalles">
                        <i class="fa-solid fa-eye"></i>
                        </a>
                    @endcan

                    @can('editar_dependencias')
                      <a href="{{ route('dependencias.edit', $dependencia->id) }}" data-id="{{$dependencia->id}}"
                        class="btn-editar m-1 inline-block rounded-md border border-yellow-600 px-2 py-2 text-yellow-600 hover:bg-yellow-600 hover:text-white dark:border-yellow-400 dark:text-yellow-400 dark:hover:bg-yellow-500 dark:hover:text-white"
                        title="Editar">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </a>
                    @endcan

                    @can('eliminar_dependencias')
                        <button command="show-modal" commandfor="dialog-cancelar" data-id="{{$dependencia->id}}"
                            class="btn-cancelar m-1 inline-block rounded-md border border-red-600 px-2 py-2 text-red-600 hover:bg-red-600 hover:text-white dark:border-red-400 dark:text-red-400 dark:hover:bg-red-500 dark:hover:text-white"
                            title="eliminar" >
                            <i class="fa fa-times"></i>
                        </button>
                    @endcan

                </div>
            </li>
        @endforeach
    </ul>
</div>