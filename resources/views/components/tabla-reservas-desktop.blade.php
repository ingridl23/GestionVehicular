<div id="reservas-wrapper" data-view="tabla" class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
  @props([
  'reservas',
  'configEditar' => null,
  'ids' => null,
  'ubicacion' => null,
  'mostrarAcciones'
  ])

  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
      <thead class="bg-gray-50 dark:bg-gray-900">
        <tr>

          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Inicio de uso
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Fin de uso
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Estado
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Oficina solicitante
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Conductor
          </th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Vehículo
          </th>
          @if($mostrarAcciones)
          @canany(['ver_reservas_internas', 'actualizar_reserva_interna', 'cancelar_reserva_interna', 'cancelar_prestamo'])
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Acciones
          </th>
          @endcanany
          @else
          @canany(['ver_solicitudes_prestamos','autorizar_prestamos', 'rechazar_prestamos'])
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
            Acciones
          </th>
          @endcanany
          @endif
        </tr>
      </thead>

      <tbody id="contenedor-reservas" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        @foreach ($reservas as $reserva)
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">

          <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">
            {{ $reserva->fecha_inicio_reserva->format('d/m/Y H:i') }}
          </td>

          <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">
            {{ $reserva->fecha_fin_reserva->format('d/m/Y H:i') }}
          </td>

          <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                {{ $reserva->estado_reserva->estado == 'APROBADA' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                {{ $reserva->estado_reserva->estado == 'EN CURSO' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                {{ $reserva->estado_reserva->estado == 'PENDIENTE' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                {{ $reserva->estado_reserva->estado == 'CANCELADA' ? 'bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200' : '' }}
                {{ $reserva->estado_reserva->estado == 'FINALIZADA' ? 'bg-gray-300 text-gray-900 dark:bg-gray-100 dark:text-gray-900' : '' }}
                {{ $reserva->estado_reserva->estado == 'RECHAZADA' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
            ">
            {{ $reserva->estado_reserva->estado }} </span>
          </td>

          <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">
            {{ $reserva->dependencia_solicitante->nombre }}
          </td>

          <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">
            {{ $reserva->usuario->name }} - {{ $reserva->usuario->lastname }}
          </td>

          <td class="px-6 py-8 whitespace-nowrap text-sm text-gray-900 dark:text-white">
            {{ $reserva->vehiculo->dominio }} - {{ $reserva->vehiculo->marca }} - {{ $reserva->vehiculo->anio }}
          </td>





          @if($mostrarAcciones)
          <td class="px-6 py-8 whitespace-nowrap text-gray-900 dark:text-white">
            <div class="flex justify-start gap-4">
            @canany(['ver_reservas_internas', 'ver_reservas_prestamos'])
            <a href="{{ route('reservas.reserva', $reserva->id) }}"
              class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
              title="Ver detalles">
              <i class="fas fa-eye"></i>
            </a>
            @endcanany

            @if(($ubicacion === "interna" || ($ids && in_array($reserva->id_dependencia_solicitante, $ids))) && $reserva->id_dependencia_solicitante)
              @can($configEditar['can'])
                @if(!in_array($reserva->estado_reserva->estado, ['CANCELADA','RECHAZADA','FINALIZADA']))
                <a href="{{ $configEditar['route'] }}" data-id="{{$reserva->id}}"
                  class="btn-editar text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                  title="Editar">
                  <i class="fas fa-edit"></i>
                </a>
                @endif
              @endcan


              @if(!in_array($reserva->estado_reserva->estado, ['CANCELADA','RECHAZADA','FINALIZADA']))
                @role('Operativo')
                  @can('asignar_conductor_suplente')
                  <a href="{{ route('operativo.editar-conductor', $reserva->id) }}" data-id="{{$reserva->id}}"
                    class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                    title="Editar conductor">
                    <i class="fas fa-edit"></i>
                  </a>
                  @endcan
                @endrole
              @endif
@role('Operativo')
@if($reserva->estado_reserva->estado === 'APROBADA')
<form method="POST"
      action="{{ route('operativo.viajes.comenzar', $reserva->id) }}"
      class="inline">
    @csrf
    <button type="submit"
        class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
        title="Iniciar viaje">
        <i class="fas fa-route"></i>
    </button>
</form>
@endif
@endrole

            @canany(['cancelar_reserva_interna', 'cancelar_prestamo'])
            @if(!in_array($reserva->estado_reserva->estado, ['CANCELADA','RECHAZADA','FINALIZADA']))
            <button command="show-modal" commandfor="dialog-cancelar" data-id="{{$reserva->id}}"
              class="btn-cancelar text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
              title="Cancelar">
              <i class="fas fa-times"></i>
            </button>
            @endif
            @endcanany
            @endif


            </div>
          </td>
          @else
          <td class="px-6 py-8 whitespace-nowrap text-gray-900 dark:text-white">
            <div class="flex justify-start gap-4">
            @can('ver_solicitudes_prestamos')
            <a href="{{ route('reservas.reserva', $reserva->id) }}"
              class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
              title="Ver detalles">
              <i class="fas fa-eye"></i>
            </a>
            @endcan
            @can('autorizar_prestamos')
            <button command="show-modal" commandfor="dialog-autorizar" data-id="{{$reserva->id}}"
              class="btn-autorizar text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
              title="Autorizar préstamo">
              <i class="fa-solid fa-circle-check"></i>
            </button>
            @endcan

            @can('rechazar_prestamos')
            <button command="show-modal" commandfor="dialog-rechazar" data-id="{{$reserva->id}}"
              class="btn-rechazar text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
              title="Rechazar préstamo">
              <i class="fas fa-times"></i>
            </button>
            @endcan
            </div>
          </td>

          @endif
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
