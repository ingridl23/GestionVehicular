<div id="reservas-wrapper" data-view="tabla">
    @props([
        'reservas',
        'configEditar' => null,
        'mostrarAcciones'
    ])
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
                  @if($mostrarAcciones)
                    @canany(['ver_reservas_internas', 'actualizar_reserva_interna', 'cancelar_reserva_interna', 'cancelar_prestamo'])
                    <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white bg-blue-600 dark:bg-blue-800 lg:px-4 lg:py-7">
                      Acciones
                    </th>
                    @endcanany
                  @else
                    @canany(['ver_solicitudes_prestamos','autorizar_prestamos', 'rechazar_prestamos'])
                    <th class="w-1/6 min-w-[160px] px-3 py-4 text-lg font-medium text-white bg-blue-600 dark:bg-blue-800 lg:px-4 lg:py-7">
                      Acciones
                    </th>
                    @endcanany
                  @endif
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
                  @if($mostrarAcciones)
                    <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">
                      
                      @canany(['ver_reservas_internas', 'ver_reservas_prestamos'])
                      <a href="{{ route('reservas.reserva', $reserva->id) }}"
                        class="m-1 inline-block rounded-md border border-blue-600 px-2 py-2 text-blue-600 hover:bg-blue-600 hover:text-white dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white"
                        title="Ver detalles">
                        <i class="fa-solid fa-eye"></i>
                      </a>
                      @endcanany

                      @can($configEditar['can'])
                      @if(!in_array($reserva->estado_reserva->estado, ['CANCELADA','RECHAZADA','FINALIZADA']))
                        <a href="{{ $configEditar['route'] }}" data-id="{{$reserva->id}}"
                          class="btn-editar m-1 inline-block rounded-md border border-yellow-600 px-2 py-2 text-yellow-600 hover:bg-yellow-600 hover:text-white dark:border-yellow-400 dark:text-yellow-400 dark:hover:bg-yellow-500 dark:hover:text-white"
                          title="Editar">
                          <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        @endif
                      @endcan

                      @canany(['cancelar_reserva_interna', 'cancelar_prestamo'])
                        @if(!in_array($reserva->estado_reserva->estado, ['CANCELADA','RECHAZADA','FINALIZADA']))
                          <button command="show-modal" commandfor="dialog-cancelar" data-id="{{$reserva->id}}"
                              class="btn-cancelar m-1 inline-block rounded-md border border-red-600 px-2 py-2 text-red-600 hover:bg-red-600 hover:text-white dark:border-red-400 dark:text-red-400 dark:hover:bg-red-500 dark:hover:text-white"
                              title="Cancelar" >
                              <i class="fa fa-times"></i>
                          </button>
                        @endif
                      @endcanany

                    </td>
                  @else
                    <td class="border-b border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 px-2 py-5 text-center text-base font-medium text-gray-700 dark:text-gray-200">
                            @can('ver_solicitudes_prestamos')
                              <a href="{{ route('reservas.reserva', $reserva->id) }}"
                                class="m-1 inline-block rounded-md border border-blue-600 px-2 py-2 text-blue-600 hover:bg-blue-600 hover:text-white dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white"
                                title="Ver detalles">
                                <i class="fa-solid fa-eye"></i>
                              </a>
                            @endcan
                            @can('autorizar_prestamos')
                              <form action="{{ route('admin.reservas.autorizar', $reserva->id) }}" 
                                    method="POST" 
                                    class="inline-block m-1">
                                  @csrf
                                  @method('PATCH')

                                  <button type="submit"
                                      class="rounded-md border border-blue-600 px-2 py-2 text-blue-600 hover:bg-blue-600 hover:text-white dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white"
                                      title="Autorizar préstamo">
                                      <i class="fa-solid fa-circle-check text-green-600"></i>
                                  </button>
                              </form>
                            @endcan
                           @can('rechazar_prestamos')
                                <a href="{{ route('admin.reservas.rechazar', $reserva->id) }}"
                                  class="m-1 inline-block rounded-md border border-blue-600 px-2 py-2 text-blue-600 hover:bg-blue-600 hover:text-white dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white"
                                  title="Rechazar prestamo">
                                    <i class="fa-solid fa-circle-xmark text-red-600"></i>
                                    
                                </a>
                            @endcan
                    </td>
                  @endif
                </tr>
                @endforeach
              </tbody>
            </table>
</div>