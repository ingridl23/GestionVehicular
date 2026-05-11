@props(['reservas'])


@foreach($reservas as $reserva)

<div class="flex items-center justify-between mb-4">

        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Reservas pendientes de iniciar
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Reservas aprobadas listas para comenzar recorrido
            </p>
        </div>

        <span class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
            {{ $reservas->count() }} pendientes
        </span>
    </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300">
                    <th class="py-3 px-4 font-semibold">ID</th>
                    <th class="py-3 px-4 font-semibold">Vehículo</th>
                    <th class="py-3 px-4 font-semibold">Estado</th>
                    <th class="py-3 px-4 font-semibold text-center">Acciones</th>
                </tr>
            </thead>
        <tbody>
            @endforeach
            @foreach($reservas as $reserva)
                  <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">

                        <td class="py-4 px-4 font-medium text-gray-900 dark:text-white">
                            #{{ $reserva->id }}
                        </td>

                        <td class="py-4 px-4 text-gray-700 dark:text-gray-300">
                            {{ $reserva->vehiculo?->dominio ?? '—' }}
                        </td>

                        <td class="py-4 px-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700 font-medium">
                                {{ $reserva->estado_reserva?->estado }}
                            </span>
                        </td>

                        <td class="py-4 px-4 text-center">
                            <form method="POST"
                                  action="{{ route('viajes.comenzar', $reserva->id) }}">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                                    <i class="fas fa-play"></i>
                                    Iniciar viaje
                                </button>
                            </form>
                        </td>

                    </tr>
            @endforeach
        </tbody>
    </table>
</div>
