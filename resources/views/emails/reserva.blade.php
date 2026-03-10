<p style="text-align:center;">
<img src="{{ asset('images/logo-muni-azul-claro.png') }}" width="180">
</p>

<x-mail::message>

# Sistema de Gestión Vehicular

Hola {{ $usuario }}

{{ $mensaje }}

<x-mail::button :url="$url">
Ver en el sistema
</x-mail::button>

Ante cualquier inconveniente comuníquese con el área de informática.

Saludos
Municipalidad de Tres Arroyos

</x-mail::message>
