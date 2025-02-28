@extends('layout.master')
@section('title', 'SIG')


@section('content')
    <div class="container-fluid">

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <!-- Título de la lista de procesos -->
                    <div class="col-md-10">
                        <h3 class="card-title">Lista de Procesos</h3>
                    </div>

                    <!-- Botón para añadir un nuevo proceso -->
                    <div class="col-md-2">
                        <a href="{{ route('procesos.create') }}" class="btn btn-primary mb-3">Añadir Proceso</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('procesos.index') }}">
                    <div class="row align-items-center">
                        <!-- Etiqueta y Select para Filtrar por Proceso Padre -->
                        <div class="form-group col-md-12">
                            <label for="proceso_padre_id" class="mr-2">Filtrar por Proceso Padre</label>
                            <select name="proceso_padre_id" id="proceso_padre_id" class="form-control d-inline-block w-auto">
                                <option value="">Selecciona un Proceso Padre</option>
                                @foreach ($procesos_padre as $procesoPadre)
                                    <option value="{{ $procesoPadre->id }}"
                                        {{ request('proceso_padre_id') == $procesoPadre->id ? 'selected' : '' }}>
                                        {{ $procesoPadre->proceso_nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </div>
                        
                       
                    </div>
                </form>
         


            <table id= "procesos" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Código del Proceso</th>
                        <th>Nombre del Proceso</th>
                        <th>Tipo del Proceso</th>
                        <th>Proceso Padre</th>
                        <th>Nivel Proceso</th>
                        <th>OUO Asociadas</th>
                        <th style="width: 20%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($procesos as $proceso)
                        <tr>
                            <td>{{ $proceso->cod_proceso }}</td>
                            <td>{{ $proceso->proceso_nombre }}</td>
                            <td>{{ $proceso->proceso_tipo }}</td>
                            <td>{{ $proceso->procesoPadre ? $proceso->procesoPadre->cod_proceso : 'N/A' }}
                            </td>
                            <td>{{ $proceso->proceso_nivel }}</td>
                            <td>{{ $proceso->ouos->count() }}</td>

                            <td>
                                <a href="{{ route('procesos.listarOUO', ['proceso_id' => $proceso->id]) }}"
                                    class="btn btn-success btn-sm" data-toggle="tooltip" title="Asociar Proceso">
                                    <i class="fas fa-link"></i>
                                </a>
                                <a href="{{ route('obligaciones.listar', ['proceso_id' => $proceso->id])  }}" 
                                    class="btn btn-dark btn-sm" data-toggle="tooltip" title="Ver Obligaciones">
                                    <i class="fas fa-list"></i>
                                </a>
                                <a href="#" class="btn btn-warning btn-sm" data-toggle="tooltip" title="Ver Riesgos">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </a>
                                <a href="#" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Ver Hallazgos">
                                    <i class="fas fa-fire"></i>
                                </a>
                                
                               
                                <a href="{{ route('indicadores.listar', ['proceso_id' => $proceso->id]) }}"
                                    class="btn btn-info btn-sm" data-toggle="tooltip" title="Ver Indicadores">
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


@endsection
@section('js')
    <script>
        $('#procesos').DataTable();
    </script>

@endsection
