@extends('layout.app')

@section('content')
<section class="max-w-6xl mx-auto p-6 space-y-6">

    <!-- Fechas -->
    <div class="bg-white rounded-xl shadow p-6 mt-1 mb-4">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
            Fechas de la reservación
        </h2>

        <div class="space-y-2 text-gray-700 mt-1 mb-4">
            <p><span class="font-medium">Fecha de registro:</span> {{ $reserva->getRawOriginal('fecha_reserva') }}</p>
            <p><span class="font-medium">Inicio de uso:</span> {{ $reserva->getRawOriginal('fecha_inicio_reserva') }}</p>
            <p><span class="font-medium">Fin de uso:</span> {{ $reserva->getRawOriginal('fecha_fin_reserva') }}</p>
        </div>
    </div>

    <!-- Datos del vehículo -->
    <div class="bg-white rounded-xl shadow p-6 mt-1 mb-4">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
            Datos del vehículo
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-700 mt-1 mb-4">
            <p><span class="font-medium">Dominio:</span> {{ $reserva->vehiculo->dominio }}</p>
            <p><span class="font-medium">Marca:</span> {{ $reserva->vehiculo->marca }}</p>
            <p><span class="font-medium">Modelo:</span> {{ $reserva->vehiculo->modelo }}</p>
            <p><span class="font-medium">Año:</span> {{ $reserva->vehiculo->anio }}</p>

            <p>
                <span class="font-medium">VTV vigente:</span>
                <span class="ml-1 px-2 py-1 rounded-full font-semibold
                    {{ $vtv ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $vtv ? 'Sí' : 'No' }}
                </span>
            </p>

            <p><span class="font-medium">Kilómetros:</span> {{ $reserva->vehiculo->kilometros }}</p>

            <p class="sm:col-span-2">
                <span class="font-medium">Condiciones de préstamo:</span>
                {{ $reserva->vehiculo->condiciones_prestamo ?? 'No tiene condiciones de préstamo' }}
            </p>

            <p>
                <span class="font-medium">Estado de la nafta:</span>
                {{ $reserva->vehiculo->nafta->estado }}
            </p>
        </div>
    </div>

    <!-- Estado de la reserva --> 
    <div class="bg-white rounded-xl shadow p-6 mt-1 mb-4">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
            Estado de la reserva
        </h2>

        <p class="text-gray-700">
            <span class="font-medium">Estado:</span>
            <span class="ml-2 px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold">
                {{ $reserva->estado_reserva->estado }}
            </span>
        </p>
    </div>

    <!-- Dependencia dueña -->
    <div class="bg-white rounded-xl shadow p-6 mt-1 mb-4">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
            Dependencia dueña del vehículo
        </h2>

        <div class="space-y-2 text-gray-700">
            <p><span class="font-medium">Nombre:</span> {{ $reserva->dependencia_duena->nombre }}</p>

            <p>
                <span class="font-medium">Está activa:</span>
                <span class="ml-1 px-2 py-1 rounded-full text-sm font-semibold
                    {{ $reserva->dependencia_duena->activa ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $reserva->dependencia_duena->activa ? 'Sí' : 'No' }}
                </span>
            </p>

            <p>
                <span class="font-medium">Dirección:</span>
                {{ $reserva->dependencia_duena->direccion->calle }}
                {{ $reserva->dependencia_duena->direccion->altura }} -
                {{ $reserva->dependencia_duena->direccion->ciudad }}
            </p>
        </div>
    </div>

    <!-- Dependencia solicitante -->
    <div class="bg-white rounded-xl shadow p-6 mt-1 mb-4">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
            Dependencia solicitante de la reserva
        </h2>

        <div class="space-y-2 text-gray-700">
            <p><span class="font-medium">Nombre:</span> {{ $reserva->dependencia_solicitante->nombre }}</p>

            <p>
                <span class="font-medium">Está activa:</span>
                <span class="ml-1 px-2 py-1 rounded-full text-sm font-semibold
                    {{ $reserva->dependencia_solicitante->activa ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $reserva->dependencia_solicitante->activa ? 'Sí' : 'No' }}
                </span>
            </p>

            <p>
                <span class="font-medium">Dirección:</span>
                {{ $reserva->dependencia_solicitante->direccion->calle }}
                {{ $reserva->dependencia_solicitante->direccion->altura }} -
                {{ $reserva->dependencia_solicitante->direccion->ciudad }}
            </p>
        </div>
    </div>

    <!-- Usuario designado -->
    <div class="bg-white rounded-xl shadow p-6 mt-1 mb-4">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">
            Usuario designado para manejar
        </h2>

        <div class="space-y-2 text-gray-700">
            <p><span class="font-medium">Nombre:</span> {{ $reserva->usuario->name }}</p>
            <p><span class="font-medium">Apellido:</span> {{ $reserva->usuario->lastname }}</p>
            <p><span class="font-medium">Email:</span> {{ $reserva->usuario->email }}</p>
            <p>
                <span class="font-medium">Tiene el carnet de conducir vigente:</span>
                <span class="ml-1 px-2 py-1 rounded-full text-sm font-semibold
                    {{ $carnet_vigente ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $carnet_vigente ? 'Sí' : 'No' }}
                </span></p>

        </div>
    </div>

</section>
@endsection