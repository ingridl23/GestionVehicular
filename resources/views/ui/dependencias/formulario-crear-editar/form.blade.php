

        <label for="nombre">Nombre: </label>
        <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $dependencia_base_datos?->nombre) }}" required>

        @error('nombre')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror

        <label for="activa">Marque si estará activa: </label>
        <input type="radio" id="opcion-true" name="activa" value="true" @checked(old('activa', $dependencia_base_datos?->activa) == 1)> <label for="activa">Si</label>
        <input type="radio" id="opcion-false" name="activa" value="false" @checked(old('activa', $dependencia_base_datos?->activa) == 0)> <label for="activa">No</label><br>
         @error('activa')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

        <label for="dependencias">Ingrese la dependencia padre: </label>
        <select name="id_dependencia_padre" id="dependencias">
            <option value="">No tiene padre</option>
            @foreach ($dependencias as $dependencia)
                <option value="{{$dependencia->id}}" @selected(old('id_dependencia_padre', $dependencia_base_datos?->id_dependencia_padre) == $dependencia->id)> {{$dependencia->nombre}}</option>
            @endforeach
        </select>


        <label for="direcciones">Ingrese la direccion</label>
        <select name="id_direccion" id="direcciones" onchange="toggleNuevaDireccion()">
            @foreach ($direcciones as $direccion)
                <option value="{{$direccion->id}}" @selected(old('id_direccion', $dependencia_base_datos?->id_direccion) == $direccion->id)>{{$direccion->calle}} {{$direccion->altura}} - {{$direccion->ciudad}}</option>
            @endforeach
            <option value="nueva" @selected(old('id_direccion') === 'nueva')>Agregar la dirección</option>
        </select>
        <div class="transition-all mt-3" style="display: none;" id="nueva-direccion">
            <label for="calle">Calle: </label>
            <input type="text" name="calle" value="{{old('calle')}}">
            @error('calle')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

            <label for="altura">Altura: </label>
            <input type="number" name="altura" id="altura" value="{{ old('altura') }}">
            @error('altura')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror

            <label for="ciudad">Ciudad: </label>
            <input type="text" name="ciudad" value="{{ old('ciudad') }}">
            @error('ciudad')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>


