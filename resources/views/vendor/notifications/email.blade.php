<p style="text-align:center;">
<img src="{{ asset('images/logo-muni-azul-claro.png') }}" width="180">
</p>
<x-mail::message>

Sistema de Gestión Vehicular
Municipalidad de Tres Arroyos
Área de Informática

{{ $slot }}

<x-mail::button :url="$actionUrl">
{{ $actionText }}
</x-mail::button>

Ante cualquier inconveniente comuníquese con el área de informática.

Saludos
Municipalidad de Tres Arroyos

</x-mail::message>
