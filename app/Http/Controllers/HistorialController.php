<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reportes;

class HistorialController extends Controller{



    public function resumen()
    {
        return Reportes::select('estado')
            ->selectRaw('count(*) as total')
            ->groupBy('estado')
            ->get();
    }
}
