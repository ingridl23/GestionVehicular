
<x-mail::message>

# Sistema de Gestión Vehicular

Hola {{ $usuario }}

{{ $mensaje }}

<x-mail::button :url="$url">
Ver en el sistema
</x-mail::button>

Ante cualquier inconveniente informatico comuníquese con **centro de computos**.

Saludos
Municipalidad de Tres Arroyos

</x-mail::message>
