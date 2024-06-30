@extends('facilitador.layout.master')
@section('title', 'SIG')
@section('css')

    <style>
        .selected {
            background-color: #ECECEC;
            /* Light gray background for selected row */
        }
    </style>


@endsection
@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Gestión de Auditorias</a></li>
                <li class="breadcrumb-item active" aria-current="page">Programa Auditoría</li>
            </ol>
        </nav>
        <div id="successMessage"></div>

        @if (session('success'))
            <div class="alert alert-success" id="success-alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" id="error-alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header ui-sortable-handle">
                        <h3 class="card-title">Lista de Programas de Auditorías</h3>
                        <div class="card-tools">
                            <a href="{{ route('programa.create') }}" class="btn btn-primary" data-toggle="tooltip"
                                title="Nuevo Programa">Nuevo
                                Programa</a>
                        </div>
                    </div>


                    <div class="card-body">


                        @if ($programas->isEmpty())
                            <p>No hay programas de auditoría registrados.</p>
                        @else
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Periodo</th>
                                        <th>Versión</th>
                                        <th>Presupuesto</th>
                                        <th>Fecha de Aprobación</th>
                                        <th>Avance</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($programas as $programa)
                                        <tr>
                                            <td>{{ $programa->id }}</td>
                                            <td>{{ $programa->periodo }}</td>
                                            <td>{{ $programa->version }}</td>
                                            <td>{{ $programa->presupuesto }}</td>
                                            <td>{{ $programa->fecha_aprobacion }}</td>
                                            <td>{{ $programa->avance }}</td>
                                            <td>
                                                <a href="{{ route('programa.showHistory', $programa->id) }}"
                                                    class="btn btn-info btn-sm" data-toggle="tooltip" title="Ver Historial"><i class="fas fa-list"></i></a>
                                                <a href="{{ route('programa.edit', $programa->id) }}"
                                                    class="btn btn-success btn-sm" data-toggle="tooltip" title="Descargar PDF"><i class="fas fa-download"></i></a>
                                                <a href="{{ route('programa.edit', $programa->id) }}"
                                                    class="btn btn-warning btn-sm" data-toggle="tooltip" title="Editar Programa"><i class="fas fa-pencil-alt"></i></a>
                                                <a href="{{ route('programa.reprogramar', $programa->id) }}"
                                                    class="btn btn-danger btn-sm" data-toggle="tooltip" title="Reprogramar Programa"><i class="fas fa-history"></i></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
@section('js')
@stop
