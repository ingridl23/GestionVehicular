<p style="text-align:center;">
<img src="{{ asset('assets/iconos/logo.png') }}" width="180">
</p>

<x-mail::message>

# Nueva reserva registrada

Se ha registrado una reserva de vehículo en el sistema **Gestión Vehicular**.

**Vehículo:** {{ $reserva->vehiculo->dominio }}
**Fecha inicio:** {{ $reserva->fecha_inicio_reserva }}
**Fecha fin:** {{ $reserva->fecha_fin_reserva }}

<x-mail::button :url="url('/reservas/'.$reserva->id)">
Ver reserva
</x-mail::button>

Ante cualquier inconveniente informatico comuníquese con **centro de computos**.

Saludos
Municipalidad de Tres Arroyos

</x-mail::message>
