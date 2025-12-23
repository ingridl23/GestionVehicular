<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\Viaje;
use Illuminate\Http\Request;
class GastoController extends Controller{


    //tener relacion con viaje y auto

    //si hubo un viaje traer combustible y kilometros.
    //calcular gasto del viaje segun precio combustible actual
    //guardar o crear ese gasto del viaje registrado vinculado a una fecha y un viaje con un auto y un usuario
    //en una vista pasar cada viaje y sus gastos
    public function indexGasto(){
        $gastos = Gasto::all();
        $viajes = Viaje::all();
        return view("ui.gastos")->with('viajes',$viajes)->with('gasto',$gastos);
    }


    //seleccionar y eliminar un gasto registrado
    public function deleteGasto($id){

        //el ususario selecciona un gasto
        //se encuentra en el sistema
        //y se elimina
        //se actualiza la vista correspondiente a los gastos

    }

    }
