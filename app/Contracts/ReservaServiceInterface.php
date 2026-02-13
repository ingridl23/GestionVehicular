<?php

namespace App\Contracts;

use App\Models\User;

interface ReservaServiceInterface
{
    public function cancelarReserva(int $id);

    public function verReserva($id);
    public function verReservas();
    public function datosFiltros();
    public function datosParaFormEditar($id);
    public function datosParaFormCrear();
    public function crearReserva($request);
    public function editarReserva($request, $id);
    public function editarConductor($request, $id);
    public function user();
    public function rol();

    public function verReservasPendientes();
    

}
