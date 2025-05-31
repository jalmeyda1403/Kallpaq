@extends('layout.master')
@section('title', 'SIG')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Gestión de Usuarios</h3>
                        <div class="card-tools">
                            <a href="{{ route('usuarios.create') }}" class="btn btn-primary">Nuevo Usuario</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input type="text" id="buscarUsuario" class="form-control" placeholder="Buscar Usuario">
                            </div>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr class="fila-usuario">
                                        <td>{{ $user->id }}</td>
                                        <td class="nombre-usuario">{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach ($user->roles as $role)
                                                {{ $role->name }}
                                            @endforeach
                                        </td>
                                        <td>
                                            <a href="{{ route('usuarios.asignar-roles', $user->id) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-user-shield"></i> Rol
                                            </a>
                                            <a href="{{ route('usuarios.asignar-permisos', $user->id) }}" class="btn btn-success btn-sm">
                                                <i class="fas fa-lock"></i> Permisos
                                            </a>
                                             <a href="{{ route('usuarios.asignar-procesos', $user->id) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-cogs"></i> Procesos
                                            </a>
                                            <a href="{{ route('usuarios.reset-password', $user->id) }}" class="btn btn-danger btn-sm">
                                                <i class="fas fa-key"></i> 
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
@stop
@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputBusqueda = document.querySelector('#buscarUsuario');
        const filasUsuarios = document.querySelectorAll('.fila-usuario');

        inputBusqueda.addEventListener('keyup', function () {
            const busqueda = inputBusqueda.value.toLowerCase();

            filasUsuarios.forEach(fila => {
                const nombreUsuario = fila.querySelector('.nombre-usuario').textContent.toLowerCase();

                if (nombreUsuario.includes(busqueda)) {
                    fila.style.display = 'table-row';
                } else {
                    fila.style.display = 'none';
                }
            });
        });
    });
</script>
@endsection
