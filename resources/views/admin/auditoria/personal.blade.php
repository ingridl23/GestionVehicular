
@section('content')
@extends('layout.app')

@section('page-title', 'Administración de Usuarios')
@section('page-description', 'Gestión de Usuarios de la Dependencia')

@section('content')

<div class="container mx-auto px-4 py-6">

    <!-- Header con botón de crear -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Usuarios del Sistema</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                Total de usuarios: <span class="font-semibold">{{ $usuarios->total() }}</span>
            </p>
        </div>

        @can('crear_usuario')
        <button
            onclick="openCreateModal()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
        >
            <i class="fas fa-plus"></i>
            Crear Usuario
        </button>
        @endcan
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-6">
        <form method="GET" action="{{ route('admin.usuarios.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <!-- Búsqueda -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-search mr-1"></i> Buscar
                </label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Nombre, apellido, email o legajo..."
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                           bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                           focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>



            <!-- Filtro por Rol -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-user-tag mr-1"></i> Rol
                </label>
                <select
                    name="rol"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg
                           bg-white dark:bg-gray-700 text-gray-900 dark:text-white
                           focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">Todos</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->name }}" {{ request('rol') == $rol->name ? 'selected' : '' }}>
                            {{ $rol->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Botones -->
            <div class="md:col-span-4 flex gap-2 justify-end">
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center gap-2 transition-colors"
                >
                    <i class="fas fa-filter"></i>
                    Filtrar
                </button>
                <a
                    href="{{ route('admin.usuarios.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg flex items-center gap-2 transition-colors"
                >
                    <i class="fas fa-times"></i>
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de usuarios -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Usuario
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Legajo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Dependencia
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Rol
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($usuarios as $usuario)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($usuario->name, 0, 1) . substr($usuario->lastname, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $usuario->name }} {{ $usuario->lastname }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $usuario->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $usuario->legajo ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $usuario->dependencia->nombre ?? 'Sin asignar' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($usuario->roles->isNotEmpty())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $usuario->hasRole('Administrador General') ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                    {{ $usuario->hasRole('Administrador de Dependencia') ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}
                                    {{ $usuario->hasRole('Jefe de Area') ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                    {{ $usuario->hasRole('Operativo') ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                ">
                                    {{ $usuario->roles->first()->name }}
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Sin rol
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $usuario->enabled ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ $usuario->enabled ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <a
                                    href="{{ route('admin.usuarios.show', $usuario->id) }}"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                    title="Ver perfil">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @can('editar_usuarios')
                                <button
                                    onclick="openEditModal({{ $usuario->id }})"
                                    class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @endcan

                                @can('eliminar_usuarios')
                                <button
                                    onclick="confirmDelete({{ $usuario->id }})"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-users text-4xl mb-2"></i>
                            <p>No se encontraron usuarios</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $usuarios->links() }}
        </div>
    </div>

</div>

<!-- Modal para crear/editar usuario -->
<div id="userModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4" id="modalTitle">
                Crear Usuario
            </h3>
            <form id="userForm" method="POST">
                @csrf
                <input type="hidden" name="_method" value="POST" id="formMethod">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                        <input type="text" name="name" required
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellido</label>
                        <input type="text" name="lastname" required
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" name="email" required
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Legajo</label>
                        <input type="text" name="legajo"
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña</label>
                        <input type="password" name="password" id="passwordField"
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="text-xs text-gray-500 mt-1">Dejar en blanco para mantener la actual</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dependencia</label>
                        <select name="id_dependencia" required
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Seleccionar...</option>
                            @foreach($dependencias as $dep)
                                <option value="{{ $dep->id }}">{{ $dep->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rol</label>
                        <select name="role" required
                            class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Seleccionar...</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->name }}">{{ $rol->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Crear Usuario';
    document.getElementById('userForm').action = '{{ route("admin.usuarios.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('passwordField').required = true;
    document.getElementById('userForm').reset();
    document.getElementById('userModal').classList.remove('hidden');
}

function openEditModal(userId) {
    // Aquí deberías cargar los datos del usuario vía AJAX
    document.getElementById('modalTitle').textContent = 'Editar Usuario';
    document.getElementById('userForm').action = `/admin/usuarios/${userId}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('passwordField').required = false;
    document.getElementById('userModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('userModal').classList.add('hidden');
}

function confirmDelete(userId) {
    if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/usuarios/${userId}`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>



@endsection
