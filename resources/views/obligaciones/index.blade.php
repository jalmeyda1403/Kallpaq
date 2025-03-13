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
                <li class="breadcrumb-item active" aria-current="page">Listado Obligaciones</li>
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
                        <h3 class="card-title">Lista de Obligaciones</h3>
                        <div class="card-tools">
                            <!-- Botón para agregar una nueva obligación con ícono -->
                            <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" id="addObligacionBtn"
                                data-target="#addObligacionModal" data-toggle="tooltip" title="Nueva Obligación">
                                <i class="fas fa-plus-circle"></i> Agregar
                            </a>

                            <a href="#" class="btn btn-danger btn-sm" id="deleteObligacionBtn" disabled
                                data-toggle="tooltip" title="Dar de Baja">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </a>
                        </div>

                    </div>

                    <div class="card-body">
                        <!-- Listado de obligaciones -->
                        <div class="row">
                            <table class="table table-hover table-sm" id="obligaciones">
                                <thead>
                                    <tr>
                                        <th class="align-top">Item</th>
                                        <th style="display: none;">Id</th>
                                        <th style="width: 12%" class="align-top">Proceso</th>
                                        <th class="align-top">Documento Técnico</th>
                                        <th class="align-top">Obligación Principal</th>
                                        <th class="align-top" style="width: 25%">Controles Identificados</th>
                                        <th class="align-top">Consecuencia del Incumplimiento</th>
                                    
                                        <th class="align-top" style="width: 7%">Estado</th>                         
                                        <th class="align-top"style="width:7%">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $item = 1;@endphp

                                    @foreach ($obligaciones as $obligacion)
                                        <tr class="clickable-row">
                                            <td>{{ $item++ }}</td>
                                            <td style="display: none;">{{ $obligacion->id }}</td>
                                            <td>{{ $obligacion->proceso->proceso_nombre }}</td>
                                            <td>{{ $obligacion->documento_tecnico_normativo }}</td>
                                            <td>{{ $obligacion->obligacion_principal }}</td>

                                            <td>{!! nl2br(e($obligacion->obligacion_controles)) !!}</td>
                                            <td>{{ $obligacion->consecuencia_incumplimiento }}</td>
                                        
                                            <td><span class="badge {{ $obligacion->estadoClass }}">
                                                    {{ ucfirst($obligacion->estado_obligacion) }}
                                                </span>
                                            </td>
                                            <td>

                                                <a href="#" class="btn btn-danger btn-sm verRiesgosBtn"
                                                    data-toggle="modal" data-id="{{ $obligacion->id }}"
                                                    data-target="#verRiesgosModal" data-toggle="tooltip"
                                                    title="Ver Riesgos">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </a>
                                                <a href="#" class="btn btn-warning btn-sm editObligacionBtn"
                                                    data-toggle="modal" data-id="{{ $obligacion->id }}"
                                                    data-target="#addObligacionModal" data-toggle="tooltip"
                                                    title="Editar Obligación" disabled>
                                                    <i class="fas fa-pencil-alt" aria-hidden="true"></i>
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


    <!-- Modal para agregar o editar obligación -->
    <div class="modal fade" id="addObligacionModal" tabindex="-1" role="dialog" aria-labelledby="addObligacionModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">

        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="addObligacionModalLabel">Agregar Nueva Obligación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="obligacionForm"method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="method" value="POST">
                    <input type="hidden" name="obligacion_id" id="obligacion_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col -md-6">
                                <!-- Campos del formulario -->
                                <div class="form-group">
                                    <label for="proceso_id"> Proceso</label>
                                    <div class="input-group">
                                        <!-- Campo oculto para el ID del proceso -->
                                        <input type="hidden" name="proceso_id" id="proceso_id" required>

                                        <!-- Campo de texto para el nombre del proceso -->
                                        <input type="text" class="form-control" id="proceso_nombre"
                                            name="proceso_nombre" required>

                                        <!-- Botón para abrir el modal -->
                                        <a href="#" class="btn btn-primary" data-toggle="modal"
                                            data-target="#procesoModal"><i class="fas fa-search"></i></a>
                                    </div>
                                </div>

                                <x-modal-busqueda :ruta="route('procesos.buscar', ['proceso_id' => $proceso->id])" campo-id="proceso_id" campo-nombre="proceso_nombre"
                                    modal-titulo="Proceso" modal-id="procesoModal">
                                </x-modal-busqueda>
                            </div>
                            <div class="col -md-6">
                                <div class="form-group">
                                    <label for="area_compliance_id">Area Compliance</label>
                                    <div class="input-group">
                                        <!-- Campo oculto para el ID del proceso -->
                                        <input type="hidden" name="area_compliance_id" id="area_compliance_id" required>

                                        <!-- Campo de texto para el nombre del proceso -->
                                        <input type="text" class="form-control" id="area_compliance_nombre"
                                            name="area_compliance_nombre" required>

                                        <!-- Botón para abrir el modal -->
                                        <a href="#" class="btn btn-primary" data-toggle="modal"
                                            data-target="#acModal"><i class="fas fa-search"></i></a>
                                    </div>
                                </div>

                                <x-modal-busqueda :ruta="route('areaCompliance.buscar')" campo-id="area_compliance_id"
                                    campo-nombre="area_compliance_nombre" modal-titulo="Area Compliance"
                                    modal-id="acModal">
                                </x-modal-busqueda>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col -md-6">
                                <div class="form-group">
                                    <label for="documento_tecnico_normativo">Documento Técnico Normativo</label>
                                    <textarea class="form-control" id="documento_tecnico_normativo" rows=5 name="documento_tecnico_normativo" required></textarea>
                                </div>
                            </div>
                            <div class="col -md-6">
                                <div class="form-group">
                                    <label for="obligacion_principal">Obligación Principal</label>
                                    <textarea class="form-control" id="obligacion_principal" rows=5 name="obligacion_principal" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col -md-6">
                                <div class="form-group">
                                    <label for="obligacion_controles">Controles Identificados</label>
                                    <textarea class="form-control" rows=6 id="obligacion_controles" name="obligacion_controles" required></textarea>
                                </div>
                            </div>
                            <div class="col -md-6">
                                <div class="form-group">
                                    <label for="consecuencia_incumplimiento">Consecuencia del Incumplimiento</label>
                                    <textarea class="form-control" rows=6 id="consecuencia_incumplimiento" name="consecuencia_incumplimiento" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="documento_deroga">Documento Deroga (opcional)</label>
                            <input type="text" class="form-control" id="documento_deroga" name="documento_deroga">
                        </div>
                        <div class="form-group">
                            <label for="estado_obligacion">Estado de la Obligación</label>
                            <select class="form-control" id="estado_obligacion" name="estado_obligacion" required>
                                <option value="pendiente">Pendiente</option>
                                <option value="mitigada">Mitigada</option>
                                <option value="controlada">Controlada</option>
                                <option value="vencida">Vencida</option>
                                <option value="inactiva">Inactiva</option>
                                <option value="suspendida">Suspendida</option>

                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitObligacion">Guardar
                            Obligación</button>

                    </div>


                </form>
            </div>
        </div>
    </div>
    <!-- Modal para mostrar los riesgos de la obligacion -->
    <div class="modal fade" id="verRiesgosModal" tabindex="-1" role="dialog" aria-labelledby="verRiesgosModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="verRiesgosModalLabel">Riesgos Asociados</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Spinner que será visible durante la carga -->
                    <div id="loading-spinner" style="display: none; text-align: center;">
                        <i class="fa fa-spinner fa-spin" style="font-size: 50px;"></i> Cargando...
                    </div>

                    <table class="table table-hover table-sm table-riesgos">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Tipo</th>
                                <th style="width: 300%" class="align-top">Nombre Riesgo</th>

                                <th>Factor</th>
                                <th>Probabilidad</th>
                                <th>Impacto</th>
                                <th>Valoración</th>
                                <th>Accioones</th>
                            </tr>
                        </thead>
                        <tbody id="listaRiesgos">
                            <!-- Los riesgos se agregarán aquí dinámicamente -->
                        </tbody>
                    </table>


                    <div class="text-right">
                        <!-- Botón para mostrar el formulario de agregar nuevo riesgo -->
                        <a href class="btn btn bg-navy" type="button" id="nuevoRiesgoBtn">
                            <i class="fas fa-plus-circle"></i> Nuevo Riesgo
                        </a>
                    </div>
                    <!-- Tarejte para agregar un nuevo riesgo -->
                    <div class="collapse mt-3" id="formRiesgo">
                        <div class="card card-navy">
                            <div class="card-header">
                                <h3 class="card-title">Registrar nuevo riesgo</h3>
                            </div>
                            <div class="card-body">
                                <form id="formAddRiesgo" method="POST">
                                    @csrf
                                    <input type="hidden" id="riesgo_id" name="riesgo_id">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="riesgo_nombre">Nombre del Riesgo</label>
                                                <textarea rows="3" class="form-control" id="riesgo_nombre" placeholder="Ingrese el nombre del riesgo"
                                                    required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="riesgo_tipo">Tipo de Riesgo</label>
                                                <select class="form-control" id="riesgo_tipo" required>
                                                    <option value="Riesgo">Riesgo</option>
                                                    <option value="Oportunidad">Oportunidad</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="factor_id">Factor</label>
                                                <select class="form-control" id="factor_id" required>
                                                    <option value="4">Cumplimiento</option>

                                                    <!-- Agregar más opciones según sea necesario -->
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="probabilidad">Probabilidad</label>
                                                <select class="form-control" id="probabilidad" required>
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
                                                <select class="form-control" id="impacto" required>
                                                    <option value="4">Bajo</option>
                                                    <option value="6">Medio</option>
                                                    <option value="8">Alto</option>
                                                    <option value="10">Muy Alto</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="card-footer">

                                <button type="submit" class="btn btn-primary" id="btnSubmitRiesgo">Agregar
                                    Riesgo</button>
                                </form>


                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>


@endsection
@section('js')
    <script>
        $(document).ready(function() {
            const $obligacionesTable = $('#obligaciones');
            const $formAddRiesgo = $('#formAddRiesgo');
            const $verRiesgosModal = $('#verRiesgosModal');
            const $loadingSpinner = $('#loading-spinner');
            const $listaRiesgos = $('#listaRiesgos');
            const $tableRiesgos = $('.table-riesgos');
            const $successAlert = $('#success-alert');
            const $errorAlert = $('#error-alert');
            let ObligacionId = null;

            // Inicializar DataTable
            $obligacionesTable.DataTable();

            // Delegar el clic en las filas para manejar selección
            $(document).on('click', '.clickable-row', function() {
                // Remover clase "selected" de todas las filas
                $('.clickable-row').removeClass('selected');

                // Añadir la clase "selected" a la fila clickeada
                $(this).addClass('selected').find('td:not(:last-child)').css('opacity', 0.8);

                // Obtener el ID de la fila seleccionada (de la celda oculta)
                const id = $(this).find('td:eq(1)').text().trim();

                // Habilitar el botón de eliminar y almacenar el ID en un atributo de datos
                $('#deleteObligacionBtn').prop('disabled', false).data('id', id);
            });

            // Editar obligación
            $(document).on('click', '.editObligacionBtn', function() {
                $loadingSpinner.show();
                const _obligacionId = $(this).data('id');
                const _url_obligaciones = `{{ route('obligaciones.show', ':id') }}`.replace(':id',
                    _obligacionId);
                const _url = `{{ route('obligaciones.update', ':id') }}`.replace(':id', _obligacionId);

                $.ajax({
                    url: _url_obligaciones,
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Llenar los campos con los datos de la obligación
                        $('#obligacion_id').val(_obligacionId);
                        $('#proceso_id').val(data.proceso_id);
                        $('#proceso_nombre').val(data.proceso_nombre);
                        $('#area_compliance_id').val(data.area_compliance_id);
                        $('#area_compliance_nombre').val(data.area_compliance_nombre);
                        $('#documento_tecnico_normativo').val(data.documento_tecnico_normativo);
                        $('#obligacion_principal').val(data.obligacion_principal);
                        $('#obligacion_controles').val(data.obligacion_controles);
                        $('#consecuencia_incumplimiento').val(data.consecuencia_incumplimiento);
                        $('#documento_deroga').val(data.documento_deroga);
                        $('#estado_obligacion').val(data.estado_obligacion);

                        // Cambiar la acción y método del formulario
                        $('#obligacionForm').attr('action', _url);
                        $('#method').val('PUT'); // Cambiar método a PUT
                        $('#btnSubmitObligacion').text('Actualizar Obligación');

                        // Ocultar spinner y abrir el modal
                        $loadingSpinner.hide();
                        $('#addObligacionModal').modal('show');
                    },
                    error: function(xhr, status, error) {

                        alert('Ocurrió un error al cargar la obligación.');
                        $loadingSpinner.hide();
                    }
                });
            });
            $(document).on('click', '#deleteObligacionBtn', function(e) {
                e.preventDefault();

                // Obtener el ID almacenado en el botón
                const obligacionId = $(this).data('id');

                if (!obligacionId) {
                    alert('Por favor, selecciona una obligación antes de eliminar.');
                    return;
                }

                if (!confirm(
                        '¿Estás seguro de que deseas eliminar esta obligación? Esta acción no se puede deshacer.'
                    )) {
                    return;
                }

                $.ajax({
                    url: `{{ route('obligaciones.destroy', ':id') }}`.replace(':id', obligacionId),
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        alert(response.success);
                        location.reload(); // Recargar la tabla para reflejar los cambios
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON.error || 'Error al eliminar la obligación.');
                    }
                });
            });

            $(document).on('click', '.editRiesgoBtn', function() {
                event.preventDefault();
                const riesgoId = $(this).data('id'); // Obtener el ID del riesgo
                const url = `{{ route('riesgos.show', ':id') }}`.replace(':id', riesgoId);

                // Realizar la solicitud para obtener los datos del riesgo
                $.get(url, function(data) {
                    // Llenar los campos del formulario con los datos del riesgo
                    $('#riesgo_id').val(riesgoId);
                    $('#riesgo_nombre').val(data.riesgo_nombre);
                    $('#riesgo_tipo').val(data.riesgo_tipo);
                    $('#factor_id').val(data.factor_id);
                    $('#impacto').val(data.impacto);
                    $('#probabilidad').val(data.probabilidad);

                    // Cambiar la acción del formulario a la ruta de actualización
                    const updateUrl = `{{ route('riesgos.update', ':id') }}`.replace(
                        ':id',
                        riesgoId);
                    $('#formAddRiesgo').attr('action', updateUrl);
                    $('#formAddRiesgo').attr('method', 'POST');

                    // Cambiar el texto del botón
                    $('#btnSubmitRiesgo').text('Guardar Riesgo');

                    // Mostrar el formulario
                    $('#formRiesgo').collapse('show');
                });
            });
            $(document).on('click', '.deleteRiesgoBtn', function() {
                event.preventDefault();
                const riesgoId = $(this).data('id');

                const obligacionId = $('.clickable-row.selected .verRiesgosBtn').data('id');
                const url = `{{ route('riesgos.destroy', ':id') }}`.replace(':id', riesgoId);

                if (confirm(
                        '¿Estás seguro de que deseas eliminar este riesgo? Esta acción no se puede deshacer.'
                    )) {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            obtenerRiesgos(obligacionId); // Recargar la tabla
                        },
                        error: function(xhr) {
                            alert(xhr.responseJSON.error ||
                                'Error al eliminar el riesgo.');
                        }
                    });
                }
            });
            $(document).on('click', '#nuevoRiesgoBtn', function() {
                event.preventDefault();
                // Mostrar el formulario
                // Resetear todos los campos del formulario
                $('#formAddRiesgo').trigger('reset');
                $('#riesgo_id').val(''); // Eliminar el ID (es un nuevo registro)

                // Cambiar la acción del formulario a la ruta de creación
                const createUrl = `{{ route('riesgos.store') }}`;
                $('#formAddRiesgo').attr('action', createUrl);
                $('#formAddRiesgo').attr('method', 'POST');

                // Cambiar el texto del botón
                $('#btnSubmitRiesgo').text('Agregar Riesgo');
                $('#formRiesgo').collapse('show');


            });
            // Agregar nueva obligación
            $('#addObligacionBtn').click(function() {
                $('#obligacionForm')[0].reset();
                $('#obligacion_id').val('');

                // Cambiar la acción y método del formulario
                const createUrl = `{{ route('obligaciones.store') }}`;
                $('#obligacionForm').attr('action', createUrl);
                $('#method').val('POST'); // Método POST para crear
                $('#btnSubmitObligacion').text('Guardar Obligación');

                // Abrir el modal
                $('#addObligacionModal').modal('show');
            });

            // Configuración global de AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Evento para abrir el modal y cargar los riesgos
            $(document).on('click', '.verRiesgosBtn', function() {
                const obligacionId = $(this).data('id');
                $('#formNuevoRiesgo').collapse('hide');
                $verRiesgosModal.modal('show');
                obtenerRiesgos(obligacionId);
            });

            // Obtener riesgos
            function obtenerRiesgos(obligacionId) {
                $loadingSpinner.show();
                $tableRiesgos.hide();

                const urlObligacionesRiesgos =
                    `{{ route('obligaciones.listariesgos', ['obligacion_id' => ':obligacion_id']) }}`
                    .replace(
                        ':obligacion_id', obligacionId);

                $.ajax({
                    url: urlObligacionesRiesgos,
                    method: 'GET',
                    success: function(response) {
                        $loadingSpinner.hide();
                        $tableRiesgos.show();
                        actualizarTablaRiesgos(response);
                    },
                    error: function(error) {
                        console.error('Error al obtener los riesgos:', error);
                        $loadingSpinner.hide();
                        $tableRiesgos.show();

                    }
                });
            }

            // Actualizar la tabla de riesgos
            function actualizarTablaRiesgos(riesgos) {
                $listaRiesgos.empty(); // Limpiar la lista de riesgos

                if (riesgos.length > 0) {
                    riesgos.forEach(function(riesgo) {
                    $listaRiesgos.append(`
                    <tr>
                        <td>${riesgo.codigo || "S/C"}</td>
                        <td>${riesgo.riesgo_tipo}</td>
                        <td>${riesgo.riesgo_nombre}</td>
                        <td style="text-align:center">${riesgo.factor ? riesgo.factor.nombre : 'No asignado'}</td>
                        <td style="text-align:center">${riesgo.probabilidad}</td>
                        <td style="text-align:center">${riesgo.impacto}</td>
                        <td style="text-align:center">
                        <span class="valoracion-circle badge-${riesgo.semaforo}"></span>
                        ${riesgo.riesgo_valoracion}
                        </td>
                        <td><a href="#" class="btn btn-warning btn-sm editRiesgoBtn" data-id="${riesgo.id}" data-target="#formNuevoRiesgo">      
                        <i class="fas fa-pencil-alt" aria-hidden="true"></i></a>
                        <a href="#" class="btn btn-danger btn-sm deleteRiesgoBtn" data-id="${riesgo.id}">
                         <i class="fas fa-trash-alt"></i></td>
                    </tr>
                `);
                    });
                } else {
                    $listaRiesgos.append(
                        '<tr><td colspan="7">No hay riesgos asociados a esta obligación.</td></tr>');
                }
            }



            // Evento para manejar el envío del formulario riesgo
            $formAddRiesgo.submit(function(e) {
                e.preventDefault(); // Prevenir el envío tradicional del formulario
                const _url = $formAddRiesgo.attr('action');
                const _method = $formAddRiesgo.attr('method');
                const formData = {
                    obligacion_id: $('.clickable-row.selected .verRiesgosBtn').data('id'),
                    riesgo_nombre: $('#riesgo_nombre').val(),
                    factor_id: $('#factor_id').val(),
                    riesgo_tipo: $('#riesgo_tipo').val(),
                    impacto: $('#impacto').val(),
                    probabilidad: $('#probabilidad').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                // Realizar la solicitud AJAX para enviar los datos
                $.ajax({
                    url: _url,
                    method: _method,
                    data: formData,
                    success: function(response) {
                        $('#formRiesgo').collapse('hide');
                        obtenerRiesgos(formData.obligacion_id);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al agregar el riesgo:', error);
                        alert('Ocurrió un error al agregar el riesgo.');
                    }
                });
            });

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
