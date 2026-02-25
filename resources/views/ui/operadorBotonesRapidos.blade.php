{{-- ALERTAS --}}
@foreach ($alertas as $alerta)
    <x-alerta-card :alerta="$alerta" />
@endforeach

{{-- BOTONES RÁPIDOS --}}
<section class="flex flex-col gap-10 mb-10 ">
    <a class="btn-rapido text-center" action="{{ route('admin.reservas.form.agregar') }}" >Iniciar reserva</a>

    <a class="btn-rapido  text-center"
        onclick="window.location='{{ route('operativo.reportes.create') }}'">
        Comenzar reporte
</a>

<a  action="{{ isset($reservaActiva) ? route('operativo.editar-conductor', $reservaActiva->id): '#' }}" class="btn-rapido  text-center">Asignar conductor</a>
</section>



{{-- BOTONES DE VIAJE --}}
<section class="flex gap-6">

    {{-- INICIAR VIAJE --}}
    <form method="POST"
          action="{{ isset($reservaActiva) ? route('viajes.iniciar', $reservaActiva->id) : '#' }}"
          class="flex-1">
        @csrf
        <button
            type="submit"
            class="btn-iniciar w-full {{ isset($reservaActiva) ? '' : 'opacity-50 cursor-not-allowed' }}"
            {{ isset($reservaActiva) ? '' : 'disabled' }}>
            Iniciar viaje
        </button>
    </form>

    {{-- FINALIZAR VIAJE --}}
    <button
        type="button"
        id="btnFinalizar"
        class="btn-finalizar flex-1 {{ auth()->user()->viajeActivo ? '' : 'opacity-50 cursor-not-allowed' }}"
        {{ auth()->user()->viajeActivo ? '' : 'disabled' }}>
        Finalizar viaje
    </button>

</section>

<div id="modalFinalizar" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-96">
        <h2 class="text-lg font-bold mb-4">Finalizar viaje</h2>

        <input type="number" id="kmFinal" placeholder="Kilómetros finales" class="input w-full mb-3">

        <select id="estadoNafta" class="input w-full mb-3">
            @foreach($estadosNafta as $estado)
                <option value="{{ $estado->id }}">{{ $estado->estado }}</option>
            @endforeach
        </select>

        <textarea id="observaciones" class="input w-full mb-3" placeholder="Observaciones"></textarea>

        <button id="confirmarFinalizar" class="btn-finalizar w-full">
            Confirmar
        </button>
    </div>
</div>
<script>
    window.viajeId = {{ auth()->user()->viajeActivo->id ?? 'null' }};
</script>

@vite('resources/js/viajeModal.js')
