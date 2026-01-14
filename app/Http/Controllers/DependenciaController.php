<?php

namespace App\Http\Controllers;
use App\Models\Dependencia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;
use function PHPUnit\Framework\isEmpty;

class DependenciaController extends Controller{


public function dashboard(){
    return View('layouts.dashboardGeneral');
}
}
