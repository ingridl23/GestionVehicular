<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class ReservaController extends Controller{


public function reservas(){
    return View ('ui.reservas');
}
public function prestamos(){
    return View ('ui.prestamos');
}
}
