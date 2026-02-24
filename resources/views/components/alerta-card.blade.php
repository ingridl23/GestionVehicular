<div
    class="flex items-baseline gap-3
           bg-{{$alerta->color }}-50
           border-l-4 border-{{ $color }}-500
           rounded-md p-3 mb-4">

    <span class="text-{{ $alerta->color }}-600 text-lg">
      <i class="fas {{ $alerta->icono }}"></i>
    </span>

    <div class="text-sm">
        <div class="font-semibold text-{{ $alerta->color }}-800">
            {{ $titulo }}!
        </div>

        <p class="text-{{ $color }}-700">
            {{ $alerta->mensaje }}
        </p>
    </div>
</div>
