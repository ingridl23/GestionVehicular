<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Aplica el middleware "guest" SOLO al método inicio (login)
        // Si el usuario está logueado, no lo deja ver el welcome
        $this->middleware('guest')->only(['inicio']);

        // Aplica el middleware "auth" a TODOS los métodos EXCEPTO al inicio (login)
        // Obliga a estar autenticado para acceder al resto
        $this->middleware('auth')->except(['inicio']);
    }

    public function inicio(){
        return View('welcome');
    }

public function reset(){
    return View('auth.passwords.reset');
}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
  /*  public function dashboard()
    {
        return view('admin.auditoria.index');
    }*/
}
