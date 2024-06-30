@extends('adminlte::page')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Crear Proceso</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('procesos.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="cod_proceso">Código de Proceso</label>
                                <input type="text" name="cod_proceso" id="cod_proceso" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="tipo_proceso">Tipo de Proceso</label>
                                <select name="tipo_proceso" id="tipo_proceso" class="form-control" required>
                                    <option value="Misional">Misional</option>
                                    <option value="Estratégico">Estratégico</option>
                                    <option value="Apoyo">Apoyo</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="cod_proceso_padre">Proceso Padre</label>
                                <select name="cod_proceso_padre" id="cod_proceso_padre" class="form-control">
                                    <option value="">Seleccionar</option>
                                    @foreach($procesos as $proceso)
                                        <option value="{{ $proceso->id }}">{{ $proceso->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select name="estado" id="estado" class="form-control" required>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                                <a href="{{ route('procesos.index') }}" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
