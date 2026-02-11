<div id="reservas-wrapper" data-view="lista">
     @props([
        'reservas',
        'configEditar' => null,
        'mostrarAcciones'
    ])

    <ul id="contenedor-reservas-listas" class="space-y-4 ">
        @foreach ($reservas as $reserva)
            <li class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 p-4">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-700 dark:text-gray-400">
                    <div>
                        <span class="font-semibold">Inicio de uso:</span>
                        {{ $reserva->fecha_inicio_reserva->format('d/m/Y H:i') }}
                    </div>

                    <div>
                        <span class="font-semibold">Fin de uso:</span>
                        {{ $reserva->fecha_fin_reserva->format('d/m/Y H:i') }}
                    </div>
                </div>

                <div class="mt-2 text-sm dark:text-white">
                    <span class="font-semibold">Estado:</span>
                    {{ $reserva->estado_reserva->estado }}
                </div>

                <div class="mt-1 text-sm dark:text-white">
                    <span class="font-semibold">Oficina solicitante:</span>
                    {{ $reserva->dependencia_solicitante->nombre }}
                </div>

                <div class="mt-1 text-sm dark:text-white">
                    <span class="font-semibold">Conductor:</span>
                    {{ $reserva->usuario->name }} - {{ $reserva->usuario->lastname }}
                </div>

                <div class="mt-1 text-sm dark:text-white">
                    <span class="font-semibold">Vehículo:</span>
                    {{ $reserva->vehiculo->dominio }} - {{ $reserva->vehiculo->marca }} - {{ $reserva->vehiculo->anio }}
                </div>

                @if($mostrarAcciones)
                    @canany(['ver_reservas_internas', 'actualizar_reserva_interna', 'cancelar_reserva_interna', 'cancelar_prestamo'])
                        <div class="mt-4 flex flex-wrap gap-2">

                            @canany(['ver_reservas_internas', 'ver_reservas_prestamos'])
                                <a href="{{ route('reservas.reserva', $reserva->id) }}"
                                class="inline-flex items-center gap-1 rounded-md border border-blue-600 px-3 py-2 text-blue-600 hover:bg-blue-600 hover:text-white dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-500"
                                title="Ver detalles">
                                    <i class="fa-solid fa-eye"></i>
                                    
                                </a>
                            @endcanany

                            @can($configEditar['can'])
                                @if(!in_array($reserva->estado_reserva->estado, ['CANCELADA','RECHAZADA','FINALIZADA']))
                                    <a href="{{ $configEditar['route'] }}"
                                    data-id="{{ $reserva->id }}"
                                    class="btn-editar inline-flex items-center gap-1 rounded-md border border-yellow-600 px-3 py-2 text-yellow-600 hover:bg-yellow-600 hover:text-white dark:border-yellow-400 dark:text-yellow-400 dark:hover:bg-yellow-500"
                                    title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    
                                    </a>
                                @endif
                            @endcan

                        @canany(['cancelar_reserva_interna', 'cancelar_prestamo'])
                        @if(!in_array($reserva->estado_reserva->estado, ['CANCELADA','RECHAZADA','FINALIZADA']))
                            <button command="show-modal" commandfor="dialog-cancelar" data-id="{{$reserva->id}}"
                                class="btn-cancelar inline-flex items-center gap-1 rounded-md border border-red-600 px-3 py-2 text-red-600 hover:bg-red-600 hover:text-white dark:border-red-400 dark:text-red-400 dark:hover:bg-red-500"
                                    title="Cancelar">
                                <i class="fa fa-times"></i>
                            </button>
                        @endif
                        @endcanany

                        </div>
                    @endcanany
                @else
                    <div class="mt-4 flex flex-wrap gap-2">
                            @can('ver_solicitudes_prestamos')
                              <a href="{{ route('reservas.reserva', $reserva->id) }}"
                                class="m-1 inline-block rounded-md border border-blue-600 px-2 py-2 text-blue-600 hover:bg-blue-600 hover:text-white dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white"
                                title="Ver detalles">
                                <i class="fa-solid fa-eye"></i>
                              </a>
                            @endcan
                            @can('autorizar_prestamos')
                                <a href="{{ route('admin.reservas.autorizar', $reserva->id) }}"
                                  class="m-1 inline-block rounded-md border border-blue-600 px-2 py-2 text-blue-600 hover:bg-blue-600 hover:text-white dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white"
                                  title="Autorizar prestamo">
                                    <i class="fa-solid fa-circle-check text-green-600"></i>
                                    
                                </a>
                            @endcan
                           @can('rechazar_prestamos')
                                <a href="#"
                                  class="m-1 inline-block rounded-md border border-blue-600 px-2 py-2 text-blue-600 hover:bg-blue-600 hover:text-white dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white"
                                  title="Rechazar prestamo">
                                    <i class="fa-solid fa-circle-xmark text-red-600"></i>
                                    
                                </a>
                            @endcan
                        </div>
                @endif

            </li>
        @endforeach
    </ul>
</div>