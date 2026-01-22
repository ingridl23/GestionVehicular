@extends('layout.app')

@push('scripts')
    <script type="module" src="{{ Vite::asset('resources/js/filtros/filtrosReservas.js') }}"></script>
@endpush

@section('content')
<section class="bg-gray-100 dark:bg-gray-900 py-10 lg:py-[0px]">
    @if($reservas->isEmpty())
        <p class="text-center text-gray-600">No hay reservas</p>
    @else
    <div class="flex items-end">
            <button id="mostrarFiltros" type="button"
                    class="rounded-md bg-blue-600 px-4 py-2 mb-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Filtros
            </button>
    </div>
    <div class="hidden opacity-0 -translate-y-4 transition-all duration-300 ease-out" id="filtros">
            @include('ui.reservas.components.filtro')
    </div>

    <div class="mx-auto px-0">
      <div class="-mx-4 flex flex-wrap">
        <div class="w-full">
          <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto border-collapse">
              <thead>
                  <tr class="bg-blue-600 dark:bg-blue-800 text-center">

                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white bg-blue-600 dark:bg-blue-800 lg:px-4 lg:py-7">
                    Inicio de uso
                  </th>
                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white bg-blue-600 dark:bg-blue-800 lg:px-4 lg:py-7">
                    Fin de uso
                  </th>
                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white bg-blue-600 dark:bg-blue-800 lg:px-4 lg:py-7">
                    Estado
                  </th>
                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white bg-blue-600 dark:bg-blue-800 lg:px-4 lg:py-7">
                    Oficina solicitante
                  </th>
                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white bg-blue-600 dark:bg-blue-800 lg:px-4 lg:py-7">
                    Conductor
                  </th>
                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white bg-blue-600 dark:bg-blue-800 lg:px-4 lg:py-7">
                    Vehículo
                  </th>

                  @canany(['ver_reservas_internas', 'actualizar_reserva_interna', 'cancelar_reserva_interna'])
                  <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white bg-blue-600 dark:bg-blue-800 lg:px-4 lg:py-7">
                    Acciones
                  </th>
                  @endcanany
                </tr>
              </thead>

              <tbody id="contenedor-reservas">
                @foreach ($reservas as $reserva)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">

                  <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">
                    {{ $reserva->fecha_inicio_reserva->format('d/m/Y H:i') }}
                  </td>

                  <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">
                    {{ $reserva->fecha_fin_reserva->format('d/m/Y H:i') }}
                  </td>

                  <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">
                    {{ $reserva->estado_reserva->estado }}
                  </td>

                  <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">
                    {{ $reserva->dependencia_solicitante->nombre }}
                  </td>

                  <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">
                    {{ $reserva->usuario->name }} - {{ $reserva->usuario->lastname }}
                  </td>

                  <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">
                    {{ $reserva->vehiculo->dominio }} - {{ $reserva->vehiculo->marca }} - {{ $reserva->vehiculo->anio }}
                  </td>

                  <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">
                    
                    @canany(['ver_reservas_internas', 'ver_reservas_prestamos'])
                    <a href="{{ route('reservas.reserva', $reserva->id) }}"
                      class="m-1 inline-block rounded-md border border-blue-600 px-2 py-2 text-blue-600 hover:bg-blue-600 hover:text-white dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white"
                       title="Ver detalles">
                      <i class="fa-solid fa-eye"></i>
                    </a>
                    @endcanany

                    @can('actualizar_reserva_interna')
                    <a href="{{ route('reservas.editar', $reserva->id) }}"
                       class="m-1 inline-block rounded-md border border-yellow-600 px-2 py-2 text-yellow-600 hover:bg-yellow-600 hover:text-white dark:border-yellow-400 dark:text-yellow-400 dark:hover:bg-yellow-500 dark:hover:text-white"
                       title="Editar">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    @endcan

                    @can('cancelar_reserva_interna')
                    <a href="{{ route('reservas.cancelar', $reserva->id) }}"
                       class="m-1 inline-block rounded-md border border-red-600 px-2 py-2 text-red-600 hover:bg-red-600 hover:text-white dark:border-red-400 dark:text-red-400 dark:hover:bg-red-500 dark:hover:text-white"
                       title="Cancelar">
                      <i class="fa fa-times"></i>
                    </a>
                    @endcan

                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    @endif
</section>


    <div class="contenedor-servidor">
        {{ $reservas->links() }}
    </div>

<script>
    window.RESERVAS_CONFIG = {
        permissions: {
            ver: @json(auth()->user()->can('ver_reservas_internas')),
            editar: @json(auth()->user()->can('actualizar_reserva_interna')),
            cancelar: @json(auth()->user()->can('cancelar_reserva_interna')),
        },
        routes: {
            ver: "{{ route('reservas.reserva', ':id') }}",
            editar: "{{ route('reservas.editar', ':id') }}",
            cancelar: "{{ route('reservas.cancelar', ':id') }}",
        }
    };
</script>
@endsection


