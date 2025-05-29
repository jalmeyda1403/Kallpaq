@extends('admin.layout.master')
@section('title', 'SIG')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Listado de permisos asignados a {{ $user->name }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#rolModal">
                              [+] Permisos
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (count($permisosAsignados) > 0)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nombre del Permiso</th>
                                    <th>Descripción</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($permisosAsignados as $permiso)
                                    <tr>
                                        <td>{{ $permiso->id }}</td>
                                        <td>{{ $permiso->name }}</td>
                                        <td></td>
                               
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                               
                        @else
                            <p>No tiene permisos asignados.</p>
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
                        <h4 class="modal-title">Seleccionar Permisos</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form action="{{ route('usuarios.guardar-permisos', ['id' => $user->id]) }}" method="POST">
                        @csrf
                    <div class="modal-body" id="modal-body">
                       
                        <div id="permisoModalContent" >
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 20%">Seleccionar</th>
                                        <th style="width: 20%">Id</th>
                                        <th style="width: 30%">Nombre Permiso</th>
                                        <th style="width: 100%">Descripcíon</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permisosDisponibles as $permiso)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="permiso-id[]" value="{{ $permiso->id }}"
                                                @if ($permisosAsignados->contains('id', $permiso->id)) 
                                                checked 
                                                @endif>
                                            </td>
                                            <td>{{ $permiso->id }}</td>
                                            <td>{{ $permiso->name }}</td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
      
                            </div>
                    </div>
                  
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="agregarRoles">
                                Sincronizar Permisos
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
      #permisoModalContent table {
        margin: 0;
        table-layout: fixed; /* Fijar el ancho de las columnas */
      
    }
    
    #permisoModalContent table th,
    #permisoModalContent table td {
        padding: 5px;
        font-size: 12px; /* Tamaño de fuente menor */
        overflow: hidden; /* Evitar que el contenido se desborde */
        
       
    }
</style>

@endsection
