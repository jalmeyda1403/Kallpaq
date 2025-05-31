@extends('layout.master')
@section('title', 'SIG')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Listado de Roles asignados a {{ $user->name }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#rolModal">
                              [+] Roles
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (count($rolesAsignados) > 0)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nombre del Rol</th>
                                    <th>Descripción</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rolesAsignados as $rol)
                                    <tr>
                                        <td>{{ $rol->id }}</td>
                                        <td>{{ $rol->name }}</td>
                                        <td>{{ $rol->descripcion }}</td>
                               
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                               
                        @else
                            <p>No tiene roles asignados.</p>
                        @endif
                    </div>
                   
                </div>
            </div>
        </div>
   

        <!-- Modal -->
        <div id="rolModal" class="modal fade" tabindex="-1" role="dialog" >
            <div class="modal-dialog  modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Seleccionar Rol</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form action="{{ route('usuarios.guardar-roles', ['id' => $user->id]) }}" method="POST">
                        @csrf
                    <div class="modal-body" id="modal-body">
                       
                        <div id="rolModalContent" >
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 20%">Seleccionar</th>
                                        <th style="width: 20%">Id</th>
                                        <th style="width: 30%">Nombre Rol</th>
                                        <th style="width: 100%">Descripcíon</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rolesDisponibles as $rol)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="rol-id[]" value="{{ $rol->id }}"
                                                 @if ($rolesAsignados->contains('id', $rol->id)) checked @endif>
                                            </td>
                                            <td>{{ $rol->id }}</td>
                                            <td>{{ $rol->name }}</td>
                                            <td>{{ $rol->descripcion }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
      
                            </div>
                    </div>
                  
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="agregarRoles">
                                Sincronizar Roles
                            </button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
@stop
@section('js')
<style>
      /* Estilos para hacer la tabla y el texto más compactos */
      #rolModalContent table {
        margin: 0;
        table-layout: fixed; /* Fijar el ancho de las columnas */
      
    }
    
    #rolModalContent table th,
    #rolModalContent table td {
        padding: 5px;
        font-size: 12px; /* Tamaño de fuente menor */
        overflow: hidden; /* Evitar que el contenido se desborde */
        
       
    }
</style>

@endsection
