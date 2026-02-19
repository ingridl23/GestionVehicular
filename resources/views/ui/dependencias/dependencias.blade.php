@extends('layout.app')

@push('scripts')
<script type="module" src="{{ Vite::asset('resources/js/filtros/filtrosDependencias.js') }}"></script>
<script type="module" src="{{ Vite::asset('resources/js/accionesDependencias.js') }}"></script>
@endpush


@section('content')
<section class="py-10 lg:py-[0px]">


  <div class="flex items-end justify-between">
    <button id="mostrarFiltrosDependencia" type="button"
      class="rounded-md bg-blue-600 px-2 py-2 mb-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
      Filtros
    </button>

    @can('crear_dependencias')
    <a href="{{ route('dependencias.create') }}"
      class="inline-block rounded-md bg-blue-600 px-2 py-2 mb-2 text-sm font-medium text-center text-white
                        hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
      Crear dependencia
    </a>
    @endcan
  </div>

  @if($dependencias->isEmpty())
  <p class="text-center text-gray-600 ">No hay dependencias cargadas</p>
  @else

  <div class="hidden opacity-0 -translate-y-4 transition-all duration-300 ease-out" id="filtros">

    <x-filtros-dependencias-fields :dependencias="$dependencias_filtros" :localidades="$localidades" />

  </div>

  <p id="mensajeNoHayDependencias" class="hidden text-center text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900 border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-4"></p>

  <div class="mx-auto px-0" id="contenedor-general">
    <div class="-mx-4 flex flex-wrap">
      <div class="w-full">
        <div class="max-w-full overflow-x-auto">
          <div class="hidden md:block">
            <x-tabla-dependencia-desktop :dependencias="$dependencias" />
          </div>

          <div class="block md:hidden">
            <x-lista-dependencia-mobile :dependencias="$dependencias" />
          </div>

        </div>
      </div>
    </div>

    <el-dialog>
      <dialog id="dialog-cancelar" class="p-0 backdrop:bg-black/50 rounded-lg">

        <div class="bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-auto">

          <div class="p-6">
            <div class="flex items-center gap-4">

              <!-- Icono -->
              <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-500/10">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.5"
                    class="h-6 w-6 text-red-400">
                  <path d="M12 9v3.75m0 3.75h.007M4.93 19h14.14c1.54 0 
                          2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 
                          0L3.2 16c-.77 1.33.19 3 1.73 3Z"
                        stroke-linecap="round"
                        stroke-linejoin="round"/>
                </svg>
              </div>

              <h3 class="text-lg font-semibold text-white">
                Eliminar Dependencia
              </h3>

            </div>

            <p class="mt-4 text-sm text-gray-400">
              Atención: esta dependencia no se eliminará si tiene dependencias hijas.
              Elimine primero las hijas para continuar.
            </p>
          </div>

          <div class="flex justify-end gap-3 bg-gray-700/30 px-6 py-4 rounded-b-lg">

            <button type="button"
                    onclick="this.closest('dialog').close()"
                    class="px-4 py-2 text-sm font-medium text-white bg-white/10 hover:bg-white/20 rounded-md">
              Cancelar
            </button>

            <button type="button"
                    class="botonCancelarDependencia px-4 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-400 rounded-md">
              Eliminar
            </button>

          </div>

        </div>

      </dialog>
    </el-dialog>

    <el-dialog>
      <dialog id="confirmDialog" class="p-0 backdrop:bg-black/50 rounded-lg">

        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl w-full max-w-md mx-auto">
          <div class="p-6">
            <div class="flex items-center gap-4">
              <!-- Icono -->
              <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-500/10">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                  class="h-6 w-6 text-blue-600 dark:text-blue-400">
                    <path d="M9 12.75 11.25 15 15 9.75" stroke-linecap="round" stroke-linejoin="round" />
                    <circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>

              <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Confirmar acción
              </h2>
            </div>

            <p id="dialogText" class="mt-4 text-sm text-gray-600 dark:text-gray-300">
              <!-- texto dinámico -->
            </p>
          </div>

          <div class="flex justify-end gap-3 bg-gray-100 dark:bg-gray-800 px-6 py-4 rounded-b-lg">
            <button id="cancelBtn"
              class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md">
              Cancelar
            </button>

            <button id="confirmBtn"
              class="botonAutorizar px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
              Confirmar
            </button>
          </div>
        </div>
      </dialog>
    </el-dialog>


    <div class="contenedor-servidor flex flex-col items-center justify-center mt-6">
      {{ $dependencias->links('vendor.pagination.simple-pagination') }}
    </div>

    <div id="contenedor-js" style="display:none;">
      <div id="lista-dependencias"></div>
      <div id="paginacion"></div>
    </div>
  </div>
  @endif
</section>


<script>
  window.DEPENDENCIAS_CONFIG = {
    permissions: {
      ver: @json(auth() -> user() -> can('ver_dependencias')),
      editar: @json(auth() -> user() -> can('editar_dependencias')),
      eliminar: @json(auth() -> user() -> can('eliminar_dependencias')),
    },
    routes: {
      ver: "{{ route('dependencias.show', ':id') }}",
      editar: "{{ route('dependencias.edit', ':id') }}",
    },
    puedeCambiarActiva: @json(
      auth() -> user() -> hasAnyRole(['Administrador General', 'Administrador de Dependencia'])
    )
  };
</script>

@endsection