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
         $this->middleware('auth')->except(['inicio']);
    }

    public function inicio(){
        return View('welcome');
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
