<?php

namespace App\Http\Controllers;
use App\Models\Alerta;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{

public function operador(){
    return View('ui.operadordashboard');
}


public function dashboard()
{
    $user = Auth::user();

    return view('admin.auditoria.index', compact('user'));
}


}
