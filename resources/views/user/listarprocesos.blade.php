<div id="procesosModalContent" >
<table class="table">
    <thead>
        <tr>
            <th style="width: 20%">Seleccionar</th>
            <th style="width: 20%">ID</th>
            <th style="width: 30%">Código de Proceso</th>
            <th style="width: 100%">Nombre</th>
        </tr>
    </thead>
    <tbody>
        @foreach($procesosDisponibles as $proceso)
            <tr>
                <td>
                    
                    <input type="checkbox" class="proceso-checkbox" data-proceso-id="{{ $proceso->id }}" id="proceso-{{ $proceso->id }}" name="proceso-id[]" value="{{ $proceso->id }}">
                </td>
                <td>{{ $proceso->id }}</td>
                <td>{{ $proceso->cod_proceso }}</td>
                <td>{{ $proceso->nombre }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<div id="procesosModalPagination">
    <ul class="pagination pagination-sm m-0 float-right">
        {{ $procesosDisponibles->links() }}
    </ul>   
</div>
</div>