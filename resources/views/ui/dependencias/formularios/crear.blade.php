
@extends('layout.app')

@section('content')
<section class="py-10 lg:py-0">
    <div class="mx-auto px-0">
        <div class="-mx-4 flex flex-wrap">
            <div class="w-full">
                <div class="max-w-full overflow-x-auto">
                    <form action="{{ route('dependencias.store') }}" method="post">
                        @csrf

                        <x-dependencia-form-fields :direcciones="$direcciones" :dependencia="$dependencia" :dependencias_arbol="$dependencias_arbol" />

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('dependencias.index') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg flex items-center gap-2 transition-colors">
                                Cancelar
                            </a>

                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center gap-2 transition-colors">
                                Crear
                            </button>
                        </div>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection
