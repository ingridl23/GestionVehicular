@push('scripts')
  <script type="module" src="{{ Vite::asset('resources/js/reservas/accionesAutorizarPrestamo.js') }}"></script>
@endpush

<div id="reservas-wrapper" data-view="tabla">
    @props([
        'reservas',
        'configEditar' => null,
        'ids' => null,
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

                      @if($ids && $reserva->id_dependencia_solicitante && in_array($reserva->id_dependencia_solicitante, $ids))
                        @can($configEditar['can'])
                          @if(!in_array($reserva->estado_reserva->estado, ['CANCELADA','RECHAZADA','FINALIZADA']))
                            <a href="{{ $configEditar['route'] }}" data-id="{{$reserva->id}}"
                              class="btn-editar m-1 inline-block rounded-md border border-yellow-600 px-2 py-2 text-yellow-600 hover:bg-yellow-600 hover:text-white dark:border-yellow-400 dark:text-yellow-400 dark:hover:bg-yellow-500 dark:hover:text-white"
                              title="Editar">
                              <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            @endif
                        @endcan
                      @endif

                      @if(!in_array($reserva->estado_reserva->estado, ['CANCELADA','RECHAZADA','FINALIZADA']))
                            @role('Operativo')
                                @can('asignar_conductor_suplente')
                                <a href="{{ route('operativo.editar-conductor', $reserva->id) }}" data-id="{{$reserva->id}}"
                                    class="m-1 inline-block rounded-md border border-yellow-600 px-2 py-2 text-yellow-600 hover:bg-yellow-600 hover:text-white dark:border-yellow-400 dark:text-yellow-400 dark:hover:bg-yellow-500 dark:hover:text-white"
                                    title="Editar conductor">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                @endcan
                            @endrole
                      @endif

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
                              <button command="show-modal" commandfor="dialog-autorizar" data-id="{{$reserva->id}}"
                                  class="btn-autorizar rounded-md border border-green-600 px-2 py-2 text-green-600 hover:bg-green-700 hover:text-white dark:border-green-400 dark:text-green-400 dark:hover:bg-green-500 dark:hover:text-white"
                                      title="Autorizar préstamo">
                                      <i class="fa-solid fa-circle-check"></i>
                              </button>
                            @endcan
                            
                            @if(!in_array($reserva->estado_reserva->estado, ['CANCELADA','RECHAZADA','FINALIZADA']))
                                  @role('Operativo')
                                      @can('asignar_conductor_suplente')
                                      <a href="{{ route('operativo.editar-conductor', $reserva->id) }}" data-id="{{$reserva->id}}"
                                          class="m-1 inline-block rounded-md border border-yellow-600 px-2 py-2 text-yellow-600 hover:bg-yellow-600 hover:text-white dark:border-yellow-400 dark:text-yellow-400 dark:hover:bg-yellow-500 dark:hover:text-white"
                                          title="Editar conductor">
                                          <i class="fa-solid fa-pen-to-square"></i>
                                      </a>
                                      @endcan
                                  @endrole
                            @endif

                            @can('rechazar_prestamos')
                              <button command="show-modal" commandfor="dialog-rechazar" data-id="{{$reserva->id}}"
                                  class="btn-rechazar m-1 inline-block rounded-md border border-red-600 px-2 py-2 text-red-600 hover:bg-red-600 hover:text-white dark:border-red-400 dark:text-red-400 dark:hover:bg-red-500 dark:hover:text-white"
                                  title="Rechazar préstamo" >
                                  <i class="fa fa-times"></i>
                              </button>
                            @endcan
                    </td>
                    <el-dialog>
            <dialog id="dialog-rechazar" aria-labelledby="dialog-title" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
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
                        <h3 id="dialog-title" class="text-base font-semibold text-white">Rechazar préstamo</h3>
                        <div class="mt-2">
                          <p class="text-sm text-gray-400">¿Está seguro de que desea rechazar este préstamo? Al confirmarlo, el día, horario y vehículo quedarán disponibles nuevamente 
                                                            y esta acción no podrá deshacerse.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="bg-gray-700/25 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" command="close" commandfor="dialog" class="botonRechazar inline-flex w-full justify-center rounded-md bg-red-500 px-3 py-2 text-sm font-semibold text-white hover:bg-red-400 sm:ml-3 sm:w-auto">Rechazar préstamo</button>
                    <button type="button" command="close" commandfor="dialog-rechazar" class="mt-3 inline-flex w-full justify-center rounded-md bg-white/10 px-3 py-2 text-sm font-semibold text-white inset-ring inset-ring-white/5 hover:bg-white/20 sm:mt-0 sm:w-auto">Cancelar</button>
                  </div>
                </el-dialog-panel>
              </div>
            </dialog>
          </el-dialog>

          <el-dialog>
            <dialog id="dialog-autorizar" class="rounded-lg p-0 backdrop:bg-black/50">
              <div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl w-full max-w-md">
                <div class="p-6">
                  <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Confirmar acción
                  </h2>

                  <p id="dialogText" class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    ¿Está seguro de que desea autorizar este préstamo? 
                      Al confirmarlo, la solicitud quedará aprobada y el vehículo quedará asignado 
                      para la fecha y horario indicados.
                  </p>
                </div>

                <div class="flex justify-end gap-3 bg-gray-100 dark:bg-gray-800 px-6 py-4 rounded-b-lg">
                  <button type="button" command="close" commandfor="dialog-autorizar" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md">Cancelar</button>
                  <button type="button" command="close" commandfor="dialog-autorizar" class="botonAutorizar px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">Autorizar préstamo</button>
                </div>
              </div>
            </dialog>
          </el-dialog>
                  @endif
                </tr>
                @endforeach
              </tbody>
            </table>
</div>