@extends('layout.master')
@section('title', 'SIG')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Listado de Procesos asignados a {{ $user->name }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#procesosModal">
                              [+] Procesos
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (count($procesosAsignados) > 0)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Código del Proceso</th>
                                    <th>Nombre del Proceso</th>
                                    <th>Tipo del Proceso</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($procesosAsignados as $proceso)
                                    <tr>
                                        <td>{{ $proceso->cod_proceso }}</td>
                                        <td>{{ $proceso->proceso_nombre }}</td>
                                        <td>{{ $proceso->proceso_tipo }}</td>
                               
                                        <td>
                                            <a href="{{ route('usuarios.eliminar-proceso', ['id' => $user->id, 'proceso_id' => $proceso->id]) }}" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> 
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
   

    <!-- Modal -->
    <div id="procesosModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="procesosModalLabel">
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Seleccionar Procesos</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{ route('usuarios.guardar-procesos', ['id' => $user->id]) }}" method="POST">
                    @csrf
                <div class="modal-body" id="modal-body">
                    @section('pagination_url', route('usuario.listar-procesos',$user->id) )
                    @include('user.listarprocesos')
                </div>
              
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="agregarProcesos">
                            Agregar Procesos
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
      #procesosModalContent table {
        margin: 0;
        table-layout: fixed; /* Fijar el ancho de las columnas */
      
    }
    
    #procesosModalContent table th,
    #procesosModalContent table td {
        padding: 5px;
        font-size: 12px; /* Tamaño de fuente menor */
        overflow: hidden; /* Evitar que el contenido se desborde */
        
       
    }
</style>
<script>
 
    let selectedProcesos = [];

    $(document).ready(function () {
        // Manejar el cambio de página en la paginación
        $(document).on('click', '#procesosModalPagination a', function(event) {
            event.preventDefault();
            var url = $(this).attr('href');
            $.get(url, function(data) {
                $('#procesosModalContent').html($(data).find('#procesosModalContent').html());
                $('#procesosModalPagination').html($(data).find('#procesosModalPagination').html());

                // Restaurar los procesos seleccionados
                selectedProcesos.forEach(procesoId => {
                    $('#proceso-' + procesoId).prop('checked', true);
                });
            });
        });

        // Manejar la selección de procesos
        $(document).on('change', '.proceso-checkbox', function() {
            let procesoId = $(this).data('proceso-id');
            if ($(this).prop('checked')) {
                selectedProcesos.push(procesoId);
            } else {
                selectedProcesos = selectedProcesos.filter(id => id !== procesoId);
            }
        });

        // Manejar el clic en el botón Agregar Procesos
        $('#agregarProcesos').click(function () {
            // Aquí puedes hacer lo que necesites con los procesos seleccionados
            console.log(selectedProcesos);
            // Cerrar el modal
            $('#procesosModal').modal('hide');
        });
    });


// Actualizar la paginación dentro del modal sin cerrarlo
$(document).on('click', '#procesosModalPagination a', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        $.get(url, function(data) {
            $('#procesosModalContent').html($(data).find('#procesosModalContent').html());
            $('#procesosModalPagination').html($(data).find('#procesosModalPagination').html());
        });
    });
</script>
@endsection
