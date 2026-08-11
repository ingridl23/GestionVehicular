
 @extends('layout.appOperativo')

@section('page-title', 'Perfil de Usuario')
@section('page-description', 'Información del usuario')

@section('content')

 <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS & Alpine.js -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
<div class="container mx-auto px-4 py-6">

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            @foreach($errors->all() as $error)
                <span class="block sm:inline">{{ $error }}</span>
            @endforeach
        </div>
    @endif

    <!-- Profile Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">

        <!-- Profile Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 py-8 px-6">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-6">

        <!-- Avatar -->

        <form id="formFoto"
      action="{{ route('operativo.usuario.editarImagen', $usuario->id) }}"
      method="POST"
      enctype="multipart/form-data">

    @csrf

    <div class="relative w-24 h-24">

        @if($usuario->imagenProfile)
            <img src="{{ $usuario->imagenProfile->url_photo_profile }}"
                 class="w-24 h-24 rounded-full object-cover shadow-lg">

        @else
            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-lg">
                <span class="text-4xl text-blue-600 font-bold">
                    {{ strtoupper(substr($usuario->name, 0, 1) . substr($usuario->lastname, 0, 1)) }}
                </span>
            </div>
       @endif

        @if($puedeEditarFoto)

              @if(!$usuario->imagenProfile)
            <!-- SUBIR FOTO -->
            <label class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full cursor-pointer hover:bg-blue-700">
                <i class="fas fa-camera"></i>

                <input type="file"
                       name="foto"
                       class="hidden"
                       onchange="this.form.submit()">
            </label>

            @else
        <!-- BOTÓN ELIMINAR FOTO -->
        <button type="button"
                onclick="document.getElementById('confirmDialog').showModal()"
                class="absolute bottom-0 right-0 bg-red-600 text-white p-2 rounded-full hover:bg-red-700">
            <i class="fas fa-trash"></i>
        </button>

    @endif
        @endif

    </div>
</form>

<!-- *************************** dialog de confirmacion eliminar foto ****************************************-->
<dialog id="confirmDialog" class="rounded-lg p-6 bg-white dark:bg-gray-800">
    <p class="text-lg text-gray-900 dark:text-white">¿Estás seguro de que deseas eliminar la foto de perfil?</p>
    <div class="flex justify-end gap-4 mt-4">
        <button type="button" onclick="document.getElementById('confirmDialog').close()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500">
            Cancelar
        </button>
        <button type="button" onclick="eliminarFoto()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
            Eliminar
        </button>
    </div>
</dialog>

<!--********************* dialog de eliminacion exitosa ********************************************* -->
<dialog id="successDialog" class="rounded-lg p-6 bg-white dark:bg-gray-800">
    <p class="text-lg text-gray-900 dark:text-white">Foto de perfil eliminada exitosamente.</p>
    <div class="flex justify-end mt-4">
        <button type="button" onclick="document.getElementById('successDialog').close()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Aceptar
        </button>
    </div>
</dialog>

{{--######################  SCRIPT DE ELIMINAR LA IMAGEN DEL PERFIL  ###########################--}}
<script>

    // Manejar eliminación de foto -->

    function eliminarFoto() {

        fetch("{{ route('operativo.usuario.eliminarImagen', $usuario->id) }}", {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })

        const successDialog = document.getElementById('successDialog');
        successDialog.showModal();

        successDialog.addEventListener('close', () => {
                location.reload();
            })
            .then(() => location.reload());
    }
    </script>

{{-- ************************************************************************************************************* --}}

                <!-- Info -->
                <div class="flex-1 text-center md:text-left">
                  <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-2 break-words text-center md:text-left">
                        {{ $usuario->name }} {{ $usuario->lastname }}
                    </h2>

                    @if($usuario->roles->isNotEmpty())
                        <p class="text-blue-100 text-lg mb-3 break-words">
                            {{ $usuario->roles->first()->name }}
                        </p>
                    @endif

                    <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                        @if($esAdmin)
                            <span class="px-3 py-1 bg-yellow-500 text-white text-sm rounded-full flex items-center gap-1">
                                <i class="fas fa-crown"></i> Administrador
                            </span>
                        @endif


                    </div>
                </div>
            </div>
        </div>

        <!-- ########### Profile Body ##################-->
        <form id="formPerfil" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data"  class="p-6">
            @csrf
            @method('PUT')

            <!--********** Información Personal ***********-->
            <div class="mb-8">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-blue-600"></i>
                    Información Personal
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">



                    <!--** Nombre **-->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nombre
                        </label>
                        @if($puedeEditar)
                            <input type="text" name="name" value="{{ old('name', $usuario->name) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @else
                            <p class="text-gray-900 dark:text-white break-words">{{ $usuario->name }} </p>
                        @endif
                    </div>

                    <!--** Apellido **-->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Apellido
                        </label>
                        @if($puedeEditar)
                            <input type="text" name="lastname" value="{{ old('lastname', $usuario->lastname) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @else
                            <p class="text-gray-900 dark:text-white break-words">{{ $usuario->lastname }}</p>
                        @endif
                    </div>

                    <!--** Email **-->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email
                        </label>
                        @if($puedeEditar)
                            <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @else
                            <p class="text-gray-900 dark:text-white break-words">{{ $usuario->email }}</p>
                        @endif
                    </div>

                    <!--** Legajo **-->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Legajo
                        </label>
                        @if($puedeEditar)
                            <input type="number" name="legajo" value="{{ old('legajo', $usuario->legajo) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @else
                            <p class="text-gray-900 dark:text-white break-words">{{ $usuario->legajo ?? 'N/A' }}</p>
                        @endif
                    </div>


                      <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Licencia de Conducir
                        </label>

                        @if($puedeEditar)
                            <input type="date" name="fecha_emision" value="{{ old('fecha_emision', $usuario->carnet?->fecha_emision) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @else
                            <p class="text-gray-900 dark:text-white break-words">{{ $usuario->carnet?->fecha_emision->format('d/m/Y') ?? 'N/A' }}</p>

                        @endif

                          @if($puedeEditar)
                         <input type="date" name="fecha_vencimiento"
                            value="{{ old('fecha_vencimiento', optional($usuario->carnet)->fecha_vencimiento ? \Carbon\Carbon::parse($usuario->carnet->fecha_vencimiento)->format('Y-m-d') : '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @else
                            <p class="text-gray-900 dark:text-white">{{ $usuario->carnet?->fecha_vencimiento->format('d/m/Y') ?? 'N/A' }}</p>
                        @endif
                          <p class="text-gray-900 dark:text-white">{{ $usuario->carnet->vigente ?? 'N/A' }}</p>


                    </div>


                </div>
            </div>

            <!-- Información Laboral -->
            <div class="mb-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-briefcase text-blue-600"></i>
                    Información Laboral
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Dependencia -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Dependencia
                        </label>
                        @if($esAdmin && $dependencias->isNotEmpty())
                            <select name="id_dependencia" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @foreach($dependencias as $dep)
                                    <option value="{{ $dep->id }}" {{ old('id_dependencia', $usuario->id_dependencia) == $dep->id ? 'selected' : '' }}>
                                        {{ $dep->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <p class="text-gray-900 dark:text-white">
                                {{ $usuario->dependencia->nombre ?? 'Sin asignar' }}
                            </p>
                        @endif
                    </div>

                    <!-- Rol -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Rol
                        </label>
                        <p class="text-gray-900 dark:text-white">
                            {{ $usuario->roles->first()->name ?? 'Sin rol asignado' }}
                        </p>
                    </div>

                </div>
            </div>

            <!-- Estadísticas -->
            <div class="mb-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-line text-blue-600"></i>
                    Estadísticas
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar-check text-white text-xl"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white break-words">

                                    {{$reservas_count ?? 0 }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400  break-words">Reservas</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-car text-white text-xl"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white break-words">
                                    {{ $viajes_count ?? 0 }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Viajes</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-white text-xl"></i>
                            </div>
                            <div>
                                <p class="text-1xl font-bold text-gray-900 dark:text-white break-words">
                                    {{ $usuario->created_at->format('d/m/Y') }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Miembro desde</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex gap-3 justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ url()->previous() }}"
                   class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver
                </a>

                @if($puedeEditar)
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Guardar Cambios
                    </button>
                @endif
            </div>

        </form>

    </div>

</div>

@endsection

