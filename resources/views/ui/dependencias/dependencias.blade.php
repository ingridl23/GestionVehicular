@extends('layout.app')

@push('scripts')
<script type="module" src="{{ Vite::asset('resources/js/filtros/filtrosDependencias.js') }}"></script>
<script type="module" src="{{ Vite::asset('resources/js/accionesDependencias.js') }}"></script>
@endpush


@section('content')
<section class="bg-gray-100 dark:bg-gray-900 py-10 lg:py-[0px]">


  @if($dependencias->isEmpty())
  <p class="text-center text-gray-600 ">No hay dependencias cargadas</p>
  @else
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
      <dialog id="dialog-cancelar" aria-labelledby="dialog-title" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
        <el-dialog-backdrop class="fixed inset-0 bg-gray-900/50 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in"></el-dialog-backdrop>

        <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
          <el-dialog-panel class="relative transform overflow-hidden rounded-lg bg-gray-800 text-left shadow-xl outline -outline-offset-1 outline-white/10 transition-all data-closed:translate-y-4 data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in sm:my-8 sm:w-full sm:max-w-lg data-closed:sm:translate-y-0 data-closed:sm:scale-95">
            <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-500/10 sm:mx-0 sm:size-10">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 text-red-400">
                    <path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                  <h3 id="dialog-title" class="text-base font-semibold text-white">Eliminar Dependencia</h3>
                  <div class="mt-2">
                    <p class="text-sm text-gray-400">Atención: esta dependencia no se eliminará si tiene dependencias hijas.
                      Eliminá primero las hijas para continuar.</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-700/25 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
              <button type="button" command="close" commandfor="dialog" class="botonCancelarDependencia inline-flex w-full justify-center rounded-md bg-red-500 px-3 py-2 text-sm font-semibold text-white hover:bg-red-400 sm:ml-3 sm:w-auto">Eliminar</button>
              <button type="button" command="close" commandfor="dialog-cancelar" class="mt-3 inline-flex w-full justify-center rounded-md bg-white/10 px-3 py-2 text-sm font-semibold text-white inset-ring inset-ring-white/5 hover:bg-white/20 sm:mt-0 sm:w-auto">Cancelar</button>
            </div>
          </el-dialog-panel>
        </div>
      </dialog>
    </el-dialog>

    <el-dialog>
      <dialog id="confirmDialog" class="rounded-lg p-0 backdrop:bg-black/50">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl w-full max-w-md">
          <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
              Confirmar acción
            </h2>

            <p id="dialogText" class="mt-2 text-sm text-gray-600 dark:text-gray-300">
              <!-- texto dinámico -->
            </p>
          </div>

          <div class="flex justify-end gap-3 bg-gray-100 dark:bg-gray-800 px-6 py-4 rounded-b-lg">
            <button id="cancelBtn"
              class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md">
              Cancelar
            </button>

            <button id="confirmBtn"
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
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
      auth()->user()->hasAnyRole(['Administrador General', 'Administrador de Dependencia'])
    )
  };
</script>

@endsection