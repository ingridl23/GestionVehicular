<div class="bg-white p-4 rounded-xl shadow mb-4">
    <form class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <!-- BUSCADOR -->
        <input type="text" name="search" placeholder="Buscar por dominio, marca, modelo..."
            class="border rounded-lg px-3 py-2 w-full">

        <!-- DEPENDENCIA -->
        @if ($user->hasRole('AdminGeneral'))
            <select name="dependencia_id" class="border rounded-lg px-3 py-2 w-full">
                <option value="">Todas las dependencias</option>
                @foreach ($dependencias as $d)
                    <option value="{{ $d->id }}">{{ $d->nombre }}</option>
                @endforeach
            </select>
        @endif

        <!-- ESTADO VEHICULO -->
        <select name="estado_vehiculo_id" class="border rounded-lg px-3 py-2 w-full">
            <option value="">Todos los estados</option>
            @foreach ($estadosVehiculo as $ev)
                <option value="{{ $ev->id }}">{{ $ev->estado }}</option>
            @endforeach
        </select>

        <!-- ESTADO VTV -->
        <select name="vtv_filter" class="border rounded-lg px-3 py-2 w-full">
            <option value="">VTV</option>
            <option value="al_dia">Al día</option>
            <option value="por_vencer">Por vencer</option>
            <option value="vencida">Vencida</option>
        </select>

    </form>
    <div class="grid grid-cols-3 gap-4 mb-4">
        <div class="p-3 rounded-lg bg-green-100 text-green-800 text-center">
            Al día: {{ $resumen['al_dia'] }}
        </div>
        <div class="p-3 rounded-lg bg-yellow-100 text-yellow-800 text-center">
            Por vencer: {{ $resumen['por_vencer'] }}
        </div>
        <div class="p-3 rounded-lg bg-red-100 text-red-800 text-center">
            Vencida: {{ $resumen['vencida'] }}
        </div>
    </div>


    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 text-gray-700 text-sm">
                <tr>
                    <th class="p-3 text-left">Dominio</th>
                    <th class="p-3 text-left">Vehículo</th>
                    <th class="p-3 text-left">Dependencia</th>
                    <th class="p-3 text-left">Estado</th>
                    <th class="p-3 text-left">VTV</th>
                    <th class="p-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach ($vehiculos as $v)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 font-semibold">{{ $v->dominio }}</td>
                        <td class="p-3">{{ $v->marca }} {{ $v->modelo }} ({{ $v->anio }})</td>
                        <td class="p-3">{{ $v->dependencia->nombre }}</td>
                        <td class="p-3">
                            <span
                                class="px-2 py-1 rounded text-xs
                            @if ($v->estado_vehiculo->estado == 'EN_USO') bg-blue-100 text-blue-800
                            @elseif($v->estado_vehiculo->estado == 'DISPONIBLE') bg-green-100 text-green-800
                            @elseif($v->estado_vehiculo->estado == 'BAJA') bg-gray-300 text-gray-700
                            @else bg-yellow-100 text-yellow-800 @endif">
                                {{ $v->estado_vehiculo->estado }}
                            </span>
                        </td>
                        <td class="p-3">
                            @php
                                $hoy = now();
                                $dias = $hoy->diffInDays($v->VTV, false);
                            @endphp

                            <span
                                class="px-2 py-1 rounded text-xs
                            @if ($dias < 0) bg-red-100 text-red-800
                            @elseif($dias <= 30) bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800 @endif">
                                {{ $v->VTV->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <a href="{{ route('vehiculo.show', $v) }}" class="text-blue-600 hover:underline">Ver</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="flex justify-end my-3">
        <a href="{{ route('vehiculo.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 text-sm">
            + Agregar vehículo
        </a>
    </div>
    <div class="mt-4">
        {{ $vehiculos->links() }}
    </div>

</div>
