@extends('layout.master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Editar Proceso</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('procesos.update', $proceso) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <label for="cod_proceso">Código del Proceso</label>
                                <input type="text" name="cod_proceso" id="cod_proceso" class="form-control" value="{{ $proceso->cod_proceso }}" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label for="nombre">Nombre del Proceso</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" value="{{ $proceso->nombre }}">
                            </div>
                            
                            <div class="form-group">
                                <label for="tipo_proceso">Tipo de Proceso</label>
                                <select name="tipo_proceso" id="tipo_proceso" class="form-control">
                                    <option value="Misional" {{ $proceso->tipo_proceso === 'Misional' ? 'selected' : '' }}>Misional</option>
                                    <option value="Estratégico" {{ $proceso->tipo_proceso === 'Estratégico' ? 'selected' : '' }}>Estratégico</option>
                                    <option value="Apoyo" {{ $proceso->tipo_proceso === 'Apoyo' ? 'selected' : '' }}>Apoyo</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="cod_proceso_padre">Proceso Padre</label>
                                <div class="input-group">
                                    <input type="hidden" name="cod_proceso_padre" id="cod_proceso_padre" value="{{ $proceso->cod_proceso_padre }}">
                                    <input type="text" name="nombre_proceso_padre" id="nombre_proceso_padre" class="form-control" value="{{ $proceso->padre ? $proceso->padre->nombre : '-' }}" readonly>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalProcesoPadre">Seleccionar</button>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Modal -->
<div class="modal fade" id="selectProcesoPadreModal" tabindex="-1" role="dialog" aria-labelledby="selectProcesoPadreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="selectProcesoPadreModalLabel">Seleccionar Proceso Padre</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <select id="procesoPadreSelect" class="form-control" style="width: 100%;">
                        <option value="">Seleccionar proceso padre</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<script>
  $(document).ready(function () {
    $('#procesoPadreSelect').select2({
        placeholder: 'Seleccionar proceso padre',
        ajax: {
            url: '/buscar-procesos',
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });
})
</script>