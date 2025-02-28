@extends('layout.master')
@section('title', 'SIG')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Listado de Procesos asignados a {{ $user->name }}</h3>
                        
                    </div>
                    <div class="card-body">
                        @if (count($procesosAsignados) > 0)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Código del Proceso</th>
                                    <th>Nombre del Proceso</th>
                                    <th>Tipo del Proceso</th>
                                    <th style="width: 30%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($procesosAsignados as $proceso)
                                    <tr>
                                        <td>{{ $proceso->cod_proceso }}</td>
                                        <td>{{ $proceso->proceso_nombre }}</td>
                                        <td>{{ $proceso->proceso_tipo }}</td>
                               
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
                               
                        @else
                            <p>No tiene procesos asignados.</p>
                        @endif
                    </div>
                   
                </div>
            </div>
        </div>
   

    
</div>
@stop

