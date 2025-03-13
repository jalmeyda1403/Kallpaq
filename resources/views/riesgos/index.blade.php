@extends('layout.master')
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
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('procesos.index') }}">Procesos</a></li>
                <li class="breadcrumb-item"><a href="#">{{ $proceso->proceso_nombre }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Listado de Riesgos</li>
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
                        <h3 class="card-title">Lista de Riesgos</h3>
                        <div class="card-tools">
                            <!-- Botón para agregar una nueva obligación con ícono -->
                            <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" id="nuevoRiesgoBtn"
                                data-target="#verRiesgosModal" data-toggle="tooltip" title="Agergar Riesgo">
                                <i class="fas fa-plus-circle"></i> Agregar
                            </a>

                            <a href="#" class="btn btn-danger btn-sm" id="deleteRiesgoBtn" disabled
                                data-toggle="tooltip" title="Dar de Baja">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </a>
                        </div>

                    </div>

                    <div class="card-body">
                        <!-- Listado de Riesgos -->
                        <div class="row">
                            <table class="table table-hover table-sm" id="riesgos">
                                <thead>
                                    <tr>
                                        <th class="align-top">Item</th>
                                        <th style="display: none;">Id</th>
                                        <th class="align-top">Clasificacion</th>                                     
                                        <th class="align-top" style="text-align:center">Codigo</th>
                                        <th style="width: 10%" class="align-top">Proceso</th>
                                        <th class="align-top" style="width:20%">Riesgo</th>

                                        <th class="align-top" style="text-align:center">Tipo</th>
                                        <th class="align-top" style="text-align:center; width: 10%">Controles</th>
                                        <th class="align-top" style="text-align:center">Probabilidad</th>

                                        <th class="align-top" style="text-align:center">Impacto</th>

                                        <th class="align-top" style="text-align:center;width:7% ">Nivel</th>
                                        <th class="align-top" style="width:7%">Acciones</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @php $item = 1;@endphp

                                    @foreach ($riesgos as $riesgo)
                                        <tr class="clickable-row">
                                            <td>{{ $item++ }}</td>
                                            <td style="display: none;">{{ $riesgo->id }}</td>
                                            <td>{{ $riesgo->riesgo_tipo }}</td>
                                            <td style="text-align:center">{{ $riesgo->cod ?? 'S/C' }}</td>
                                            <td>{{ $riesgo->proceso->proceso_nombre }}</td>
                                            <td>{{ $riesgo->riesgo_nombre }}</td>
                                            <td style="text-align:center">{{ $riesgo->factor->nombre }}</td>
                                            <td style="text-align:center">{{ $riesgo->controles ?? 'Sin controles' }}</td>
                                            <td style="text-align:center">{{ $riesgo->probabilidad }}</td>
                                            <td style="text-align:center">{{ $riesgo->impacto }}</td>

                                            <td style="text-align:center"> <span
                                                    class="valoracion-circle badge-{{ $riesgo->semaforo }}"></span>
                                                {{ $riesgo->riesgo_valoracion }}
                                            </td>
                                            <td>


                                                <a href="#" class="btn btn-warning btn-sm editRiesgoBtn"
                                                    data-toggle="modal" data-id="{{ $riesgo->id }}"
                                                    data-target="#verRiesgosModal" data-toggle="tooltip"
                                                    title="Editar Obligación" disabled>
                                                    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
                                                </a>
                                                <a href="#" class="btn btn-primary btn-sm addAccion"
                                                    data-toggle="modal" data-id="{{ $riesgo->id }}"
                                                    data-target="#addAccionModal" data-toggle="tooltip"
                                                    title="Agregar Accion" disabled>
                                                    <i class="fas fa-plus" aria-hidden="true"></i>
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
    </div>


    <!-- Modal para mostrar los riesgos-->
    <div class="modal fade" id="verRiesgosModal" tabindex="-1" role="dialog" aria-labelledby="verRiesgosModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="verRiesgosModalLabel">Gestionar Riesgo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="riesgoForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="method" value="POST">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="proceso_id">Proceso</label>
                                    <div class="input-group">
                                        <input type="hidden" name="proceso_id" id="proceso_id" required>
                                        <input type="text" class="form-control" id="proceso_nombre"
                                            name="proceso_nombre" required>
                                        <a href="#" class="btn btn-primary" data-toggle="modal"
                                            data-target="#procesoModal">
                                            <i class="fas fa-search"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <x-modal-busqueda :ruta="route('procesos.buscar', ['proceso_id' => $proceso->id])" campo-id="proceso_id" campo-nombre="proceso_nombre"
                                modal-titulo="Proceso" modal-id="procesoModal">
                            </x-modal-busqueda>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo">Clasificacion</label>
                                    <select class="form-control" id="riesgo_tipo" name="riesgo_tipo" required>
                                        <option value="Riesgo">Riesgo</option>
                                        <option value="Oportunidad">Oportunidad</option>

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cod_riesgo">Codigo del Riesgo</label>
                                    <input type="text" class="form-control" id="cod_riesgo" name="cod_riesgo"
                                        rows="1" disabled></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="factor">Tipo de Riesgo</label>
                                    <select class="form-control" id="factor_id" name="factor_id" required>
                                        <option value="1">Estratégico</option>
                                        <option value="2">Operacional</option>
                                        <option value="3">Corrupción</option>
                                        <option value="4">Cumplimiento</option>
                                        <option value="5">Reputacional</option>
                                        <option value="6">Ambiental</option>
                                        <option value="7">Seguridad</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="riesgo">Descripción del Riesgo</label>
                                    <textarea class="form-control" id="riesgo_nombre" name="riesgo_nombre" rows="5" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="controles">Controles</label>
                                    <textarea class="form-control" id="controles" name="controles" rows="5"required></textarea>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="probabilidad">Probabilidad</label>
                                    <select class="form-control" id="probabilidad" name="probabilidad" required>
                                        <option value="4">Bajo</option>
                                        <option value="6">Medio</option>
                                        <option value="8">Alto</option>
                                        <option value="10">Muy Alto</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="impacto">Impacto</label>
                                    <select class="form-control" id="impacto" name="impacto" required>
                                        <option value="4">Bajo</option>
                                        <option value="6">Medio</option>
                                        <option value="8">Alto</option>
                                        <option value="10">Muy Alto</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="riesgo_valoracion">Nivel</label>
                                    <input type="text" class="form-control" id="riesgo_valoracion"
                                        name="riesgo_valoracion" rows="1" disabled></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="riesgo_tratamiento">Tratamiento</label>
                                    <input type="text" class="form-control" id="riesgo_tratamiento"
                                        name="riesgo_tratamiento" rows="1" disabled></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitRiesgo">Guardar Riesgo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



@endsection
@section('js')
    <script>
        $(document).ready(function() {



            const $loadingSpinner = $('#loading-spinner');
            const $verRiesgosModal = $('#verRiesgosModal');
            const $tableRiesgos = $('#riesgos');
            const $listaRiesgos = $tableRiesgos.find('tbody');
            const $formAddRiesgo = $('#riesgoForm');
            const $successAlert = $('#success-alert');
            const $errorAlert = $('#error-alert');
            const procesoId = {{ $proceso->id }};

            let RiesgosId = null;

            // Inicializar DataTable
            $tableRiesgos.DataTable();

            // Delegar el clic en las filas para manejar selección
            $(document).on('click', '.clickable-row', function() {
                // Remover clase "selected" de todas las filas
                $('.clickable-row').removeClass('selected');

                // Añadir la clase "selected" a la fila clickeada
                $(this).addClass('selected').find('td:not(:last-child)').css('opacity', 0.8);

                // Obtener el ID de la fila seleccionada (de la celda oculta)
                const id = $(this).find('td:eq(1)').text().trim();

                // Habilitar el botón de eliminar y almacenar el ID en un atributo de datos
                $('#deleteRiesgoBtn').prop('disabled', false).data('id', id);
            });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Editar Riesgo
            $(document).on('click', '.editRiesgoBtn', function() {
                event.preventDefault();
                const riesgoId = $(this).data('id'); // Obtener el ID del riesgo
                const url = `{{ route('riesgos.show', ':id') }}`.replace(':id', riesgoId);


                $.get(url, function(data) {
                    // Llenar los campos del formulario con los datos del riesgo
                    $('#proceso_id').val(data.proceso_id);
                    $('#proceso_nombre').val(data.proceso.proceso_nombre);
                    $('#riesgo_id').val(riesgoId);
                    $('#riesgo_cod').val(data.riesgo_cod);
                    $('#riesgo_nombre').val(data.riesgo_nombre);
                    $('#controles').val(data.controles);
                    $('#riesgo_tipo').val(data.riesgo_tipo);
                    $('#factor_id').val(data.factor_id);
                    $('#impacto').val(data.impacto);
                    $('#probabilidad').val(data.probabilidad);

                    // Cambiar la acción del formulario a la ruta de actualización
                    const updateUrl = `{{ route('riesgos.update', ':id') }}`.replace(
                        ':id',
                        riesgoId);
                    $formAddRiesgo.attr('action', updateUrl);
                    $formAddRiesgo.attr('method', 'POST');

                    // Cambiar el texto del botón
                    $('#btnSubmitRiesgo').text('Guardar Riesgo');
                });
            });

            // Enviar formualario para actualizar o agregar el riesgo
            $formAddRiesgo.on('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(this);

                // Enviar la solicitud AJAX para actualizar el riesgo
                $.ajax({
                    url: $formAddRiesgo.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(procesoId);
                        // Actualizar la fila correspondiente en la tabla
                        obtenerRiesgos(procesoId);
                        // Cerrar el modal
                        $('#verRiesgosModal').modal('hide');
                        // Resetear el formulario después de la actualización
                        $formAddRiesgo.trigger('reset');
                    },
                    error: function(xhr) {
                        alert(
                            'Hubo un error al actualizar el riesgo. Por favor, inténtelo de nuevo.'
                        );
                    }
                });
            });
            // Eliminar Riesgos
            $(document).on('click', '#deleteRiesgoBtn', function(e) {
                e.preventDefault();

                // Obtener el ID almacenado en el botón
                const RiesgosId = $(this).data('id');
             
                if (!RiesgosId) {
                    alert('Por favor, selecciona un Riesgo antes de eliminar.');
                    return;
                }

                if (!confirm(
                        '¿Estás seguro de que deseas eliminar este Riesgo? Esta acción no se puede deshacer.'
                    )) {
                    return;
                }

                $.ajax({
                    url: `{{ route('riesgos.destroy', ':id') }}`.replace(':id', RiesgosId),
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                       
                        location.reload(); // Recargar la tabla para reflejar los cambios
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.error || 'Error al eliminar la obligación.');
                    }
                });
            });


            // Nuevo Riesgo
            $(document).on('click', '#nuevoRiesgoBtn', function() {

                event.preventDefault();
                // Mostrar el formulario
                // Resetear todos los campos del formulario
                $formAddRiesgo.trigger('reset');
                $('#riesgo_id').val(''); // Eliminar el ID (es un nuevo registro)          
                // Cambiar la acción del formulario a la ruta de creación
                const createUrl = `{{ route('riesgos.store') }}`;
                $formAddRiesgo.attr('action', createUrl);
                $formAddRiesgo.attr('method', 'POST');
                // Cambiar el texto del botón
                $('#btnSubmitRiesgo').text('Agregar Riesgo');

            });

            // Función para actualizar la tabla después de editar un riesgo
            function obtenerRiesgos(procesoId) {
                $loadingSpinner.show();
                $tableRiesgos.hide(); // Ocultar la tabla mientras cargamos

                const urlRiesgos = `{{ route('riesgos.listar', ':proceso_id') }}`.replace(':proceso_id',
                    procesoId); // Ruta ajustada para pasar el proceso_id

                $.ajax({
                    url: urlRiesgos,
                    method: 'GET',
                    success: function(response) {
                        $loadingSpinner.hide();
                        $tableRiesgos.show(); // Mostrar la tabla nuevamente

                        // Ahora la respuesta es HTML, extraemos el tbody de la tabla renderizada
                        const newTableBody = $(response).find(
                        '#riesgos tbody'); // Buscar el tbody dentro del HTML renderizado

                        // Limpiamos la tabla actual y agregamos el nuevo contenido
                        $listaRiesgos.empty();
                        $listaRiesgos.append(newTableBody
                    .html()); // Añadir los nuevos riesgos al tbody actual
                    },
                    error: function(error) {
                        console.error('Error al obtener los riesgos:', error);
                        $loadingSpinner.hide();
                        $tableRiesgos.show(); // Mostrar la tabla aunque haya error
                    }
                });
            }
            // Alertas de éxito y error
            if ($successAlert.length) {
                setTimeout(() => $successAlert.fadeOut(), 2000);
            }

            if ($errorAlert.length) {
                setTimeout(() => $errorAlert.fadeOut(), 2000);
            }
        });
    </script>
@stop
