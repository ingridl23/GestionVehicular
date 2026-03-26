{{-- resources/views/ui/viajes/show.blade.php --}}
@extends('layout.app')

@section('content')

<div class="max-w-4xl mx-auto p-6">

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">

        <h2 class="text-xl font-bold mb-4">
            Viaje ID #{{ $viaje->id }}

        </h2>

        <div class="grid grid-cols-2 gap-4 mb-6">

            <div>
                <p class="text-sm text-gray-500">Vehículo</p>
                <p class="font-semibold">{{ $viaje->vehiculo->dominio }}</p>
            </div>

              <div>
                <p class="text-sm text-gray-500">El vehículo Pertenece a :</p>
                <p class="font-semibold">{{ $viaje->vehiculo->dependenciaDuena->nombre }}</p>
            </div>

           <div>
                <p class="text-sm text-gray-500">Conductor Asignado</p>
                <p class="font-semibold">{{ $viaje->reserva?->usuario?->name}} {{ $viaje->reserva?->usuario?->lastname}}</p>
            </div>


            <div>
                <p class="text-sm text-gray-500">Fecha Inicio</p>
                <p class="font-semibold">
                    {{ $viaje->fecha_inicio->format('d/m/Y H:i') }}
                </p>
            </div>

              <div>
                <p class="text-sm text-gray-500">Fecha Fin</p>
                <p class="font-semibold">
                   {{ $viaje->fecha_fin_formateada }}
                </p>
            </div>


            <div>
                <p class="text-sm text-gray-500">KM Inicial Registrado En El Vehiculo</p>
                <p class="font-semibold">{{ $viaje->kilometros_inicio }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">KM Final Registrado En El Vehiculo</p>
                <p class="font-semibold">{{ $viaje->kilometros_fin ?? 'Viaje en curso o no iniciado' }} </p>
            </div>




            <div>
                <p class="text-sm text-gray-500">Estado</p>
                <p class="font-semibold">
                    {{ $viaje->fecha_fin ? 'Finalizado' : 'En curso' }}
                </p>
            </div>

        </div>

        @if(!$viaje->fecha_fin)

        <form method="POST"
              action="{{ route('operativo.viajes.finalizar', $viaje->id) }}">
            @csrf

            <div class="space-y-4">

                <div>
                    <label class="block text-sm">Kilómetros finales</label>
                    <input type="number"
                           name="kilometros_fin"
                           required
                           class="w-full border rounded-lg p-2">
                </div>

                <div>
<label class="block text-sm">Estado nafta final</label>
 <select name="id_estado_nafta_fin" class="form-control" required>
    <option value="">Seleccione estado</option>

    @foreach($estadosNafta as $estado)
        <option value="{{ $estado->id }}">
            {{ $estado->estado }}
        </option>
    @endforeach
</select>

                </div>

                <div>
                    <label class="block text-sm">Observaciones</label>
                    <textarea name="observaciones"
                              class="w-full border rounded-lg p-2"></textarea>
                </div>


                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                    Finalizar viaje
                </button>

            </div>

        </form>

        @endif

    </div>

</div>
<a href="{{ url()->previous() }}"
         class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
     <-
</a>
@endsection
<script>
    window.VIAJE_DATA = {
        id: @json($viaje->id),
        finalizar_url: @json(route('operativo.viajes.finalizar', $viaje->id))
    };
</script>
