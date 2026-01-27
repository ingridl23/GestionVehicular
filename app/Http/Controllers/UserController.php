<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Dependencia;
use App\Models\Alerta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{



/** ********************     REDIRECCIONES   *********************** */
   public function dashboard()
    {
        $user = Auth::user();
   $stats = [
        'total'        => 0,
        'licencias'    => 0,
        'vencimientos' => 0,
        'multas'       => 0,
        'mantenimiento' => 0,
        'vehiculos' => 0,
    ];

    // luego, cuando tengas datos reales:
    // $stats['licencias'] = Licencia::vencidas()->count()
        $alertas = Alerta::latest()->paginate(10);
        if ($user->hasRole('Operativo')) {
            return redirect()->route('operativo.dashboard');
        }

        if ($user->hasRole('Administrador General')) {
            return view('admin.auditoria.index', compact('user'));
        }

        if ($user->hasAnyRole(['Administrador de Dependencia', 'Jefe de Area'])) {
            return view('admin.auditoria.index', compact('user','stats','alertas'));
        }

        abort(403);
    }

    public function dashboard2()
    {
        $stats = [
        'total'        => 0,
        'licencias'    => 0,
        'vencimientos' => 0,
        'multas'       => 0,
        'mantenimiento'=>0,
    ];

    // luego, cuando tengas datos reales:
    // $stats['licencias'] = Licencia::vencidas()->count
        $user = Auth::user();
            $alertas = Alerta::latest()->paginate(10);
        return view('ui.operadordashboard', compact('user','stats','alertas'));
    }


    /**
     * Crear usuario
     */

    public function createUser(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'lastname' => 'required|string',
            'legajo' => 'nullable|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'id_dependencia' => 'required|exists:dependencias,id',
            'role' => 'required|string'
        ]);

        // AUTORIZACIÓN (antes de crear)
        Gate::authorize('createUsers', User::class);

        $user = User::create([
            'name' => $data['name'],
            'lastname' => $data['lastname'],
            'legajo' => $data['legajo'] ?? null,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'id_dependencia' => $data['id_dependencia'],
        ]);

        // Asignar rol (misma regla de seguridad)
        Gate::authorize('updateUsers', $user);
        $user->assignRole($data['role']);

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'user' => $user
        ], 201);
    }

    /**
     * Actualizar usuario
     */

    public function updateUser(Request $request, User $user)
    {
        Gate::authorize('updateUsers', $user);

        $data = $request->validate([
            'name' => 'sometimes|string',
            'lastname' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
        ]);

        $user->update($data);

        return response()->json(['message' => 'Usuario actualizado']);
    }

    /**
     * Eliminar usuario
     */

    public function destroyUser(User $user)
    {
        Gate::authorize('deleteUser', $user);

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado']);
    }

    /**
     * Listar usuarios por dependencia
     */

    public function index(Dependencia $dependencia)
    {
        Gate::authorize('showUsers', User::class);

        return response()->json(
            $dependencia->usuarios
        );
    }



}
