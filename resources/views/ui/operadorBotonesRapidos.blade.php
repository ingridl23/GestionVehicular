{{-- ALERTAS --}}
@foreach ($alertas as $alerta)
    <x-alerta-card :alerta="$alerta" />
@endforeach

{{-- BOTONES RÁPIDOS --}}
<section class="flex flex-col gap-10 mb-10">
    <button class="btn-rapido" action="{{ route('admin.reservas.form.agregar') }}" >Iniciar reserva</button>

    <button class="btn-rapido"
        onclick="window.location='{{ route('operativo.reportes.create') }}'">
        Comenzar reporte
    </button>

    <button  action="{{ route('operativo.editar-conductor','$id') }}" class="btn-rapido">Asignar conductor</button>
</section>

{{-- BOTONES DE VIAJE --}}

<section class="flex gap-6">
 <form method="POST" action="{{ route('viajes.iniciar', $reservaActiva->id ?? '') }}">
    @csrf
    <button
        class="btn-iniciar flex-1 {{ isset($reservaActiva) ? '' : 'opacity-50 cursor-not-allowed' }}"
        {{ isset($reservaActiva) ? '' : 'disabled' }}>
        Iniciar viaje
    </button>
</form>

<form method="POST" action="{{ route('viajes.iniciar', $reservaActiva->id ?? '') }}">
    @csrf
        class="btn-finalizar flex-1 {{ auth()->user()->viajeActivo ? '' : 'opacity-50 cursor-not-allowed' }}"
      {{ auth()->user()->viajeActivo ? '' : 'disabled' }}>
        Finalizar viaje
    </button>
</form>

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
@vite('js/viajeModal.js');
