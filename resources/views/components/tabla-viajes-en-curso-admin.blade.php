@props(['viajes'])

<div class="mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 border border-gray-100 dark:border-gray-700">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Viajes en curso
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Viajes iniciados pendientes de finalización
            </p>
        </div>

        <span class="px-3 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">
            {{ $viajes->count() }} activos
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="py-3 px-4">ID</th>
                    <th class="py-3 px-4">Vehículo</th>
                    <th class="py-3 px-4">Estado</th>
                    <th class="py-3 px-4 text-center">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @foreach($viajes as $viaje)
                    <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="py-4 px-4">#{{ $viaje->id }}</td>

                        <td class="py-4 px-4">
                            {{ $viaje->vehiculo?->dominio ?? '—' }}
                        </td>

                        <td class="py-4 px-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-700 font-medium">
                                EN CURSO
                            </span>
                        </td>

                        <td class="py-4 px-4 text-center">
                            <a href="{{ route('viajes.show', $viaje->id) }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-sm font-medium">
                                <i class="fas fa-flag-checkered"></i>
                                Finalizar
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
