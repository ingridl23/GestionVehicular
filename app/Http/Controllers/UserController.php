<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{

public function operador(){
    return View('layouts/operadordashboard');
}
}
