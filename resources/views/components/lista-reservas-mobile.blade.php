<div id="reservas-wrapper" data-view="lista">
    @props([
    'reservas',
    'configEditar' => null,
    'ids' => null,
    'ubicacion' => null,
    'mostrarAcciones'
    ])

    <ul id="contenedor-reservas-listas" class="space-y-4">
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
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                    {{ $reserva->estado_reserva->estado == 'APROBADA'  ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                    {{ $reserva->estado_reserva->estado == 'EN_CURSO'  ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                    {{ $reserva->estado_reserva->estado == 'SOLICITADA'? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                    {{ $reserva->estado_reserva->estado == 'CANCELADA' ? 'bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200' : '' }}
                    {{ $reserva->estado_reserva->estado == 'FINALIZADA'? 'bg-gray-300 text-gray-900 dark:bg-gray-100 dark:text-gray-900' : '' }}
                    {{ $reserva->estado_reserva->estado == 'RECHAZADA' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                ">
                    {{ $reserva->estado_reserva->estado }}
                </span>
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

                @if(!in_array($reserva->estado_reserva->estado, ['CANCELADA','RECHAZADA','FINALIZADA']))
                @role('Operativo')
                @can('asignar_conductor_suplente')
                <a href="{{ route('operativo.editar-conductor', $reserva->id) }}" data-id="{{$reserva->id}}"
                    class="inline-flex items-center gap-1 rounded-md border border-yellow-600 px-3 py-2 text-yellow-600 hover:bg-yellow-600 hover:text-white dark:border-yellow-400 dark:text-yellow-400 dark:hover:bg-yellow-500"
                    title="Editar conductor">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
                @endcan
                @endrole
                @endif

                @role('Operativo')
                @if($reserva->estado_reserva->estado === 'APROBADA')
                <form method="POST" action="{{ route('operativo.viajes.comenzar', $reserva->id) }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-1 rounded-md border border-green-600 px-3 py-2 text-green-600 hover:bg-green-600 hover:text-white dark:border-green-400 dark:text-green-400 dark:hover:bg-green-500"
                        title="Iniciar viaje">
                        <i class="fas fa-route"></i>
                    </button>
                </form>
                @endif
                @endrole

                {{-- Devolver vehículo: reserva APROBADA sin viaje activo --}}
                @if($reserva->estado_reserva->estado === 'APROBADA' && !$reserva->viajeActivo)
                <form method="POST" action="{{ route('reservas.devolver', $reserva->id) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit"
                        class="inline-flex items-center gap-1 rounded-md border border-teal-600 px-3 py-2 text-teal-600 hover:bg-teal-600 hover:text-white dark:border-teal-400 dark:text-teal-400 dark:hover:bg-teal-500"
                        title="Devolver vehículo / Cerrar reserva">
                        <i class="fas fa-parking"></i>
                    </button>
                </form>
                @endif

                @canany(['cancelar_reserva_interna', 'cancelar_prestamo'])
                @if(!in_array($reserva->estado_reserva->estado, ['CANCELADA','RECHAZADA','FINALIZADA']))
                <button command="show-modal" commandfor="dialog-cancelar" data-id="{{$reserva->id}}"
                    class="btn-cancelar inline-flex items-center gap-1 rounded-md border border-red-600 px-3 py-2 text-red-600 hover:bg-red-600 hover:text-white dark:border-red-400 dark:text-red-400 dark:hover:bg-red-500"
                    title="Cancelar">
                    <i class="fa fa-times"></i>
                </button>
                @endif
                @endcanany

                {{-- Aprobar reserva interna --}}
                @canany(['autorizar_reservas_internas'])
                @if(!in_array($reserva->estado_reserva->estado, ['APROBADA','CANCELADA','RECHAZADA','FINALIZADA','EN_CURSO']))
                <button data-id="{{$reserva->id}}"
                    class="btn-aprobar-reserva inline-flex items-center gap-1 rounded-md border border-green-600 px-3 py-2 text-green-600 hover:bg-green-600 hover:text-white dark:border-green-400 dark:text-green-400 dark:hover:bg-green-500"
                    title="Aprobar reserva"
                   >
                    <i class="fa-solid fa-circle-check"></i>
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
                <button command="show-modal" commandfor="dialog-autorizar" data-id="{{$reserva->id}}"
                    class="btn-autorizar m-1 inline-block rounded-md border border-green-600 px-2 py-2 text-green-600 hover:bg-green-700 hover:text-white dark:border-green-400 dark:text-green-400 dark:hover:bg-green-500 dark:hover:text-white"
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
                    title="Rechazar préstamo">
                    <i class="fa fa-times"></i>
                </button>
                @endcan

            </div>
            @endif

        </li>
        @endforeach
    </ul>
</div>
