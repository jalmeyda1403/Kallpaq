@extends('facilitador.layout.master')
@section('title', 'SIG')


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                        <h3 class="card-title">Lista de Procesos</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('procesos.create') }}" class="btn btn-primary mb-3">Añadir Proceso</a>
                        <table id= "procesos" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Código del Proceso</th>
                                    <th>Nombre del Proceso</th>
                                    <th>Tipo del Proceso</th>
                                    <th>Proceso Padre</th>
                                    <th>Nivel Proceso</th>
                                    <th style="width: 20%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($procesos as $proceso)
                                    <tr>
                                        <td>{{ $proceso->cod_proceso }}</td>
                                        <td>{{ $proceso->nombre }}</td>
                                        <td>{{ $proceso->tipo_proceso }}</td>
                                        <td>{{ $proceso->cod_proceso_padre }}</td>
                                        <td>{{ $proceso->nivel }}</td>
                               
                                        <td>
                                            <a href="#" class="btn btn-warning btn-sm" data-toggle="tooltip" title="Ver Riesgos">
                                                <i class="fas fa-exclamation-triangle"></i> 
                                            </a>
                                            <a href="#" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Ver Hallazgos">
                                                <i class="fas fa-fire"></i> 
                                            </a>                                            
                                            <a href="{{ route('indicadores.listar', ['proceso_id' => $proceso->id]) }}" class="btn btn-info btn-sm" data-toggle="tooltip" title="Ver Indicadores">
                                                <i class="fas fa-chart-bar"></i> 
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                               
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
<script>
$('#procesos').DataTable();
</script>

@endsection
