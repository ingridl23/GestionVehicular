<form action="api/filtrar-dependencias" method="get" id="formFiltros">
    <div>


        <div>
            <label for="nombre">Buscar por nombre: </label>
            <input type="text" name="nombre" id="nombre-filtro">
        </div>
        
        <div>
            <label for="activa">Dependencia activa: </label>
            <select name="activa" id="activa-filtro">
                <option value="default">Seleccionar</option>
                <option value="1">Si</option>
                <option value="0">No</option>
            </select>
        </div>
        
        <div>
            <label for="dependencias-filtros">Dependencia padre: </label>
            <select name="dependencias-filtros" id="dependencias-filtros">
                <option value="default">Seleccionar</option>
                @foreach ($dependencias_filtros as $dependencia)
                <option value="{{$dependencia->id}}">{{$dependencia->nombre}}</option> 
                @endforeach
            </select>
        </div>
        
        <div>
            <label for="localidad-filtro">Localidad donde se encuentra: </label>
            <select name="localidad-filtro" id="localidad-filtro">
                <option value="default">Seleccionar</option>
                @foreach ($localidades as $localidad)
                    <option value="{{$localidad->ciudad}}">{{$localidad->ciudad}}</option> 
                @endforeach
            </select>
        </div>

            <button id="busquedaFiltros">Buscar</button>
    </div>
    


</form>