@extends('adminlte::page')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lista de Procesos</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('procesos.create') }}" class="btn btn-primary mb-3">Añadir Proceso</a>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Proceso Padre</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($procesos as $proceso)
                                    <tr>
                                        <td>{{ $proceso->id }}</td>
                                        <td>{{ $proceso->cod_proceso }}</td>
                                        <td>{{ $proceso->nombre }}</td>
                                        <td>{{ $proceso->tipo_proceso }}</td>
                                        <td>{{ $proceso->padre->nombre ?? '-' }}</td>
                                        <td>{{ $proceso->estado ? 'Activo' : 'Inactivo' }}</td>
                                        <td>
                                            <a href="{{ route('procesos.edit', $proceso) }}" class="btn btn-primary">Editar</a>
                                            <a href="{{ route('indicadores.index', $proceso) }}" class="btn btn-primary">Indicadores</a>
                                            <form action="{{ route('procesos.destroy', $proceso) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este proceso?')">Eliminar</button>
                                            </form>
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
