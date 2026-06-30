<?php
namespace App\Http\Controllers;


/**
 * @class HomeController
 * @brief Controlador principal encargado de gestionar las vistas iniciales del sistema.
 *
 * Administra:
 * - Vista pública de inicio (login)
 * - Acceso autenticado al sistema
 * - Vista de reseteo de contraseña
 *
 * Controla el acceso mediante middlewares:
 * - guest → Solo usuarios no autenticados
 * - auth  → Solo usuarios autenticados
 *
 * @package App\Http\Controllers
 * @author Ingrid Ledesma
 * @version 1.0
 * @since 2026
 */

class HomeController extends Controller
{
    /**
 * Constructor del controlador.
 *
 * Configura los middlewares de acceso:
 * - 'guest' aplicado únicamente al método inicio().
 * - 'auth' aplicado a todos los métodos excepto inicio().
 *
 * Garantiza separación entre vistas públicas y privadas.
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

    /**
 * Muestra la vista de bienvenida del sistema.
 *
 * Accesible únicamente para usuarios no autenticados.
 *
 * @return \Illuminate\View\View Vista welcome.
 */

    public function inicio(){
        return View('welcome');
    }
    /**
 * Muestra la vista de restablecimiento de contraseña.
 *
 * Permite al usuario ingresar una nueva contraseña
 * mediante el flujo de recuperación.
 *
 * @return \Illuminate\View\View Vista de reset de contraseña.
 */

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
