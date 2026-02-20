<?php
namespace App\Http\Controllers;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Dependencia;
use App\Models\Alerta;
use App\Models\Reserva;
use App\Models\Vehiculo;
use App\Models\Viaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

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

     //  ESTO ES LO QUE FALTABA
    $ultimosVehiculos = Vehiculo::with('estadoVehiculo')
        ->orderBy('created_at', 'desc')
        ->take(4)
        ->get();

    $vehiculosStats = [
    $total = Vehiculo::count(),
    $disponibles = Vehiculo::whereHas('estadoVehiculo', fn($q) =>
        $q->where('estado', 'DISPONIBLE')
    )->count(),
    $reservados = Vehiculo::whereHas('estadoVehiculo', fn($q) =>
        $q->where('estado', 'EN_USO')
    )->count(),
    $mantenimiento = Vehiculo::whereHas('estadoVehiculo', fn($q) =>
        $q->where('estado', 'EN_MANTENIMIENTO')
    )->count(),
    $baja = Vehiculo::whereHas('estadoVehiculo', fn($q) =>
        $q->where('estado', 'BAJA'))->count()
];
    // luego, cuando tengas datos reales:
    // $stats['licencias'] = Licencia::vencidas()->count()
        $alertas = Alerta::latest()->paginate(10);
        if ($user->hasRole('Operativo')) {
            return redirect()->route('operativo.dashboard2');
        }

        if ($user->hasRole('Administrador General')) {
            return view('admin.auditoria.index', compact('user','ultimosVehiculos','disponibles','total','reservados','mantenimiento','baja'));
        }

        if ($user->hasAnyRole(['Administrador de Dependencia', 'Jefe de Area'])) {
            return view('admin.auditoria.index', compact('user','stats','alertas','ultimosVehiculos','disponibles','total','reservados','mantenimiento','baja'));
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

    $vehiculosStats = [
    $total = Vehiculo::count(),
    $disponibles = Vehiculo::whereHas('estadoVehiculo', fn($q) =>
        $q->where('estado', 'DISPONIBLE')
    )->count(),
    $reservados = Vehiculo::whereHas('estadoVehiculo', fn($q) =>
        $q->where('estado', 'EN_USO')
    )->count(),
     ];


      $alertas = Alerta::latest()->take(10)->get();
    // luego, cuando tengas datos reales:
    // $stats['licencias'] = Licencia::vencidas()->count
        $user = Auth::user();
            //$alertas = Alerta::latest()->paginate(10);
       return view('operativo.dashboard', compact(
    'user',
    'stats',
    'alertas',
    'disponibles',
    'reservados'
));

    }

   /**
     * Listar usuarios con filtros
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Query base
        $query = User::with(['dependencia', 'roles']);

        // Filtro por dependencia (solo para admins de dependencia)
        if ($user->hasRole('Administrador de Dependencia') && !$user->hasRole('Administrador General')) {
            $query->where('id_dependencia', $user->id_dependencia);
        } elseif ($request->has('dependencia_id') && $request->dependencia_id != '') {
            $query->where('id_dependencia', $request->dependencia_id);
        }

        // Filtro por búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('lastname', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('legajo', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por rol
        if ($request->has('rol') && $request->rol != '') {
            $query->role($request->rol);
        }

        // Ordenar
        $query->orderBy('lastname', 'asc')->orderBy('name', 'asc');

        // Paginar
        $usuarios = $query->paginate(20)->withQueryString();

        // Obtener datos para los filtros
        $dependencias = Dependencia::orderBy('nombre')->get();
        $roles = Role::orderBy('name')->get();

        return view('admin.auditoria.usuarios', compact('usuarios', 'dependencias', 'roles'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        Gate::authorize('createUsers', User::class);

        $dependencias = Dependencia::orderBy('nombre')->get();
        $roles = Role::orderBy('name')->get();

        return view('admin.usuarios.create', compact('dependencias', 'roles'));
    }

    /**
     * Crear usuario
     */
    public function store(Request $request)
    {
        Gate::authorize('createUser', User::class);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'legajo' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'id_dependencia' => 'required|exists:dependencias,id',
            'role' => 'required|string|exists:roles,name'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'lastname' => $data['lastname'],
            'legajo' => $data['legajo'] ?? null,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'id_dependencia' => $data['id_dependencia'],
            'enabled' => true,
        ]);

        $user->assignRole($data['role']);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario creado correctamente');
    }

    /**
     * Mostrar un usuario específico (perfil público)
     */
    public function show(User $usuario)
    {
        $user = Auth::user();

        // Verificar permisos
        $esAdmin = $user->hasRole('Administrador General');
        $esAdminDependencia = $user->hasRole('Administrador de Dependencia');
        $esJefeArea = $user->hasRole('Jefe de Area');
        $esPropietario = $user->id === $usuario->id;

        // Admin general puede ver todos
        if (!$esAdmin) {
            // Admin de dependencia solo de su dependencia
            if ($esAdminDependencia && $user->id_dependencia !== $usuario->id_dependencia) {
                abort(403, 'No tenés permisos para ver este perfil.');
            }
            // Jefe de área solo de su dependencia
            if ($esJefeArea && $user->id_dependencia !== $usuario->id_dependencia && !$esPropietario) {
                abort(403, 'No tenés permisos para ver este perfil.');
            }
            // Operativo solo su propio perfil
            if (!$esAdminDependencia && !$esJefeArea && !$esPropietario) {
                abort(403, 'No tenés permisos para ver este perfil.');
            }
        }

        $puedeEditar = $esAdmin || $esPropietario;

        // Cargar relaciones necesarias
        $usuario->load(['dependencia', 'roles']);

        // Obtener listas
        $dependencias = $esAdmin ? Dependencia::all() : collect();

        return view('auth.profile', compact(
            'usuario',
            'puedeEditar',
            'esAdmin',
            'dependencias'
        ));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(User $usuario)
    {
        Gate::authorize('update', $usuario);

        $dependencias = Dependencia::orderBy('nombre')->get();
        $roles = Role::orderBy('name')->get();

        return view('admin.usuarios.edit', compact('usuario', 'dependencias', 'roles'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, User $usuario)
    {
        Gate::authorize('update', $usuario);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $usuario->id,
            'legajo' => [
            'nullable',
            'string',
            'max:20',
             Rule::unique('users', 'legajo')->ignore($usuario->id),
    ],
            'id_dependencia' => 'required|exists:dependencias,id',
            'role' => 'required|string|exists:roles,name',
            'password' => 'nullable|min:8'
        ]);

        // Actualizar datos básicos
        $updateData = [
            'name' => $data['name'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'legajo' => $data['legajo'] ?? null,
            'id_dependencia' => $data['id_dependencia'],
        ];

        // Solo actualizar contraseña si se proporciona
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $usuario->update($updateData);

        // Actualizar rol
        $usuario->syncRoles([$data['role']]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Eliminar usuario
     */
    public function destroy(User $usuario)
    {
        Gate::authorize('deleteUser', $usuario);

        // No permitir eliminar el propio usuario
        if ($usuario->id === Auth::id()) {
            return back()->withErrors('No podés eliminar tu propio usuario');
        }

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado correctamente');
    }

    /**
     * Ver mi perfil (usuario logueado)
     */
    public function myProfile()
    {
        $usuario = Auth::user();
        $usuario->load(['dependencia', 'roles']);

        // Todos pueden ver su propio perfil
        $puedeEditar = true;
        $esAdmin = $usuario->hasRole('Administrador General');
        $dependencias = $esAdmin ? Dependencia::all() : collect();
        $esConductorBase = $usuario->hasRole('Operativo');
        if($esAdmin){

            return view('auth.profile', compact('usuario', 'puedeEditar', 'esAdmin', 'dependencias'));
        }

        if($esConductorBase){
            return view('auth.profileOperativo', compact('usuario','dependencias','esAdmin','puedeEditar'));
        }
    }

    /**
     * Actualizar mi perfil (usuario logueado) o si el admin selecciona un usuario
     */
    public function updateProfile(Request $request)
    {
       $usuario = Auth::user();

    $validated = $request->validate([
        'name' => ['required', 'string', 'max:100'],
        'lastname' => ['required', 'string', 'max:100'],
        'email' => [
            'required',
            'email',
            Rule::unique('users', 'email')->ignore($usuario->id),
        ],
        'legajo' => [
            'nullable',
            'string',
            'max:20',
            Rule::unique('users', 'legajo')->ignore($usuario->id),
        ],
    ]);




        // Solo admin puede cambiar dependencia
        if ($usuario->hasRole('Administrador General')) {
            $rules['id_dependencia'] = 'sometimes|exists:dependencias,id';
        }

        $validated = $request->validate($rules);

        // Si no es admin, remover campo de dependencia si viene
        if (!$usuario->hasRole('Administrador General')) {
            unset($validated['id_dependencia']);
        }

        // Actualizar usuario
        $usuario->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Perfil actualizado correctamente');
    }

    /**
     * Listar usuarios por dependencia
     */
    public function usuariosPorDependencia(Request $request)
    {
        $user = Auth::user();

        $query = User::with(['dependencia', 'roles'])
            ->where('id_dependencia', $user->id_dependencia);

        // Aplicar búsqueda si existe
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('lastname', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $usuarios = $query->orderBy('lastname')->paginate(20);
        $dependencias = Dependencia::where('id', $user->id_dependencia)->get();
        $roles = Role::orderBy('name')->get();

        return view('admin.auditoria.personal', compact('usuarios', 'dependencias', 'roles'));
    }

    /**
     * Cambiar estado habilitado/deshabilitado
     */
    public function toggleEnabled(User $usuario)
    {
        Gate::authorize('updateUsers', $usuario);

        $usuario->update([
            'enabled' => !$usuario->enabled
        ]);

        return back()->with('success',
            $usuario->enabled ? 'Usuario habilitado correctamente' : 'Usuario deshabilitado correctamente'
        );
    }


}
