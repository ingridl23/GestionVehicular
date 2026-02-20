@extends('layout.app')

@push('scripts')
  <script type="module" src="{{ Vite::asset('resources/js/filtros/filtrosReservas.js') }}"></script>
  <script type="module" src="{{ Vite::asset('resources/js/reservas/accionesReserva.js') }}"></script>
@endpush

@php
$configAgregar = $ubicacion == 'interna'
? [
'can' => 'solicitar_reserva_interna',
'route' => route('admin.reservas.form.agregar'),
'text' => 'Agregar reserva',
]
: [
'can' => 'solicitar_prestamo',
'route' => route('admin.prestamo.form.agregar'),
'text' => 'Agregar préstamo',
];

$configEditar = $ubicacion == 'interna'
? [
'can' => 'actualizar_reserva_interna',
'route' => route('admin.reservas.form.editar', ':id'),
]
: [
'can' => 'actualizar_prestamo',
'route' => route('admin.prestamo.form.editar', ':id'),
];
@endphp


@section('content')
<section class="bg-gray-100 dark:bg-gray-900 py-10 lg:py-[0px]">


  <div class="flex items-end justify-between">
    <button id="mostrarFiltros" type="button"
      class="rounded-md bg-blue-600 px-2 py-2 mb-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
      Filtros
    </button>


    @can($configAgregar['can'])
    <a href="{{ $configAgregar['route'] }}"
      class="inline-block rounded-md bg-blue-600 px-2 py-2 mb-2 text-sm font-medium text-center text-white
                        hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
      {{ $configAgregar['text'] }}
    </a>
    @endcan
  </div>

  @if($reservas->isEmpty())
  <p class="text-center text-gray-600 ">No hay reservas</p>
  @else
  
  <div class="hidden opacity-0 -translate-y-4 transition-all duration-300 ease-out" id="filtros">
    <x-filtros-reserva-fields :vehiculos_filtros="$vehiculos_filtros" :estados_filtros="$estados_filtros" :ubicacion="$ubicacion" />

  </div>

  <p id="mensajeNoHayReservas" class="hidden text-center text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900 border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-4"></p>

  <div class="mx-auto px-0" id="contenedor-general">
    <div class="-mx-4 flex flex-wrap">
      <div class="w-full">
        <div class="max-w-full overflow-x-auto">
          <div class="hidden md:block">
            <x-tabla-reservas-desktop :reservas="$reservas" :ubicacion="$ubicacion" :ids="$ids" :configEditar="$configEditar" :mostrarAcciones="$mostrarAcciones" />
          </div>

          <div class="block md:hidden">
            <x-lista-reservas-mobile :reservas="$reservas" :ubicacion="$ubicacion" :ids="$ids" :configEditar="$configEditar" :mostrarAcciones="$mostrarAcciones" />
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
                        <h3 id="dialog-title" class="text-base font-semibold text-white">Cancelar reserva</h3>
                        <div class="mt-2">
                          <p class="text-sm text-gray-400">¿Esta seguro de cancelar esta reserva? Al hacerlo, el día, horario y vehiculo se liberarán y podrían no estar disponibles nuevamente.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="bg-gray-700/25 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" command="close" commandfor="dialog" class="botonCancelar inline-flex w-full justify-center rounded-md bg-red-500 px-3 py-2 text-sm font-semibold text-white hover:bg-red-400 sm:ml-3 sm:w-auto">Desactivar</button>
                    <button type="button" command="close" commandfor="dialog-cancelar" class="mt-3 inline-flex w-full justify-center rounded-md bg-white/10 px-3 py-2 text-sm font-semibold text-white inset-ring inset-ring-white/5 hover:bg-white/20 sm:mt-0 sm:w-auto">Cancelar</button>
                  </div>
                </el-dialog-panel>
              </div>
            </dialog>
          </el-dialog>

          


        </div>
      </div>
    </div>

    <div id="contenedor-js" style="display:none;">
      <div id="lista-reservas"></div>
      <div id="paginacion"></div>
    </div>

    <div class="contenedor-servidor flex flex-col items-center justify-center mt-6">
      {{ $reservas->links('vendor.pagination.simple-pagination') }}
    </div>

  </div>
  @endif
</section>




<script>
  window.RESERVAS_CONFIG = {
    permissions: {
      ver: @json(auth() -> user() -> can('ver_reservas_internas')),
      editar: @json(auth() -> user() -> can($configEditar['can'])),
      cancelar: @json(auth() -> user() -> can('cancelar_reserva_interna')),
      mostrarAcciones: "{{$mostrarAcciones}}",
    },
    routes: {
      ver: "{{ route('reservas.reserva', ':id') }}",
      editar: "{{ $configEditar['route'] }}",
    }
  };

  window.APP_CONFIG = {
    ubicacion: @json($ubicacion),
  };
</script>
@endsection