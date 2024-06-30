@extends('facilitador.layout.master')
@section('title', 'Seguimiento de Acciones')
@section('css')
    <style>

    </style>
@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('smp.index') }}">Solicitudes de Mejora de Procesos</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Seguimiento de Acciones</li>
            </ol>
        </nav>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Seguimiento de Acciones - SMP: {{ $hallazgo->smp_cod }}</h5>
                    </div>
                    <div class="card-body">
                        <table id="acciones" class="table  table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width:12%">Código</th>
                                    <th style="width:25%">Acción </th>
                                    <th>Tipo</th>
                                    <th>Fecha de Inicio</th>
                                    <th>Fecha de Fin</th>
                                    <th>Responsable</th>
                                    <th>Estado</th>
                                    <th>Alerta</th>
                                    <th>Evidencias</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($acciones as $accion)
                                    <tr>
                                        <td>{{ $accion->accion_cod }}</td>
                                        <td>{{ $accion->accion }}</td>
                                        @if ($accion->es_correctiva)
                                            <td>Correctiva</td>
                                        @else
                                            <td>Preventiva</td>
                                        @endif
                                        <td>{{ $accion->fecha_inicio }}</td>
                                        <td>{{ $accion->fecha_fin }}</td>
                                        <td>{{ $accion->responsable_id }}</td>
                                        <td>{{ $accion->estado }}</td>
                                        <td>
                                            @if ($accion->estado == 'Programada')
                                                <i class="fas fa-circle text-secondary"></i>
                                            @elseif ($accion->estado == 'Pendiente')
                                                <i class="fas fa-circle text-danger"></i>
                                            @elseif ($accion->estado == 'En implementación')
                                                <i class="fas fa-circle text-warning"></i>
                                            @elseif ($accion->estado == 'Completada')
                                                <i class="fas fa-circle text-success"></i>
                                            @elseif ($accion->estado == 'Cerrada')
                                                <i class="fas fa-circle text-primary"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-danger edit-accion" data-toggle="modal"
                                                data-target="#editActionModal" data-id="{{ $accion->id }}"
                                                data-hallazgo_id="{{ $accion->hallazgo_id }}">
                                                <i class="fas fa-edit"></i></a>

                                            <a href="#" class="btn btn-warning btn-sm btnVerArchivos"
                                                title="Ver Archivos" data-id="{{ $accion->id }}"
                                                data-hallazgo_id="{{ $accion->hallazgo_id }}">
                                                <i class="fas fa-folder"></i>
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

        <!-- Modal para editar acción -->
        <div class="modal fade" id="editActionModal" tabindex="-1" role="dialog" data-backdrop="static"
            aria-labelledby="editActionModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h5 class="modal-title" id="editActionModalLabel">Editar Acción</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="editActionForm" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="accion">accion</label>
                                <textarea class="form-control" id="accion" name="accion" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="comentario">Comentario</label>
                                <textarea class="form-control" id="comentario" name="comentario" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select class="form-control" id="estado" name="estado" required>
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="En implementación">En implementación</option>
                                    <option value="Completada">Completada</option>
                                    <option value="Cerrada">Cerrada</option>
                                </select>
                            </div>
                            <!-- Div funcional subida simple
                                                                <div class="form-group">
                                                                    <label for="evidencia">Subir Evidencia</label>
                                                                    <input type="file" name="archivos[]" id="archivos" multiple>
                                                                    <small class="form-text text-muted">El tamaño de cada archivo no debe superar los
                                                                        20MB.</small>
                                                                </div>

                                                                !-->
                            <div class="form-group">
                                <label for="archivos">Subir Evidencia</label>
                                <div class="dropzone" id="archivosDropzone">
                                    <div class="dz-message">
                                        Arrastra y suelta tus archivos aquí o haz click para subirlos.
                                        <br>
                                        <small class="form-text text-muted">El tamaño de cada archivo no debe superar los
                                            20MB.</small>
                                    </div>
                                </div>
                                <input type="file" name="archivos[]" id="hiddenFileInput" multiple
                                    style="display: none;">
                            </div>


                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="button" id="submit-all" class="btn btn-primary">Guardar Cambios</button>

                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- Modal para ver archivos -->
        <div class="modal fade" id="archivosModal" tabindex="-1" role="dialog" aria-labelledby="archivosModalLabel"
            aria-hidden="true">
            <div class="modal-dialog  modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h5 class="modal-title" id="archivosModalLabel">Lista de Documentos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped table-bordered" id="archivosTable">
                            <thead>
                                <tr>
                                    <th>Nombre del Archivo</th>
                                    <th>Fecha de Carga</th>
                                    <th>Tamaño</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Se llenará dinámicamente con JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@section('js')
    <script>
        Dropzone.autoDiscover = false;
        var myDropzone;


        setTimeout(function() {
            $('#success-alert').fadeOut('slow');
        }, 2000);

        $('#acciones').DataTable();

        $('.edit-accion').click(function() {
            var accionId = $(this).data('id');
            var hallazgoId = $(this).data('hallazgo_id');
            var form = document.getElementById("editActionForm");


            var updateUrlTemplate =
                "{{ route('smp.acciones.update', ['hallazgo_id' => ':hallazgo_id', 'id' => ':id']) }}";

            var updateAction = updateUrlTemplate
                .replace(':hallazgo_id', hallazgoId)
                .replace(':id', accionId);

            var url = updateAction;

            $.ajax({
                url: '/acciones/' + accionId + '/edit',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var modal = $('#editActionModal');
                    modal.find('#accion').val(data.accion);
                    modal.find('#comentario').val(data.comentario);
                    modal.find('#estado').val(data.estado);

                    // Configura la acción del formulario para el método de actualización
                    modal.find('form').attr('action', updateAction);

                    // Mostrar el modal
                    modal.modal('show');
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar datos: " + error);
                    alert('Error al cargar información de la acción');
                }
            });

            if (myDropzone) {
                myDropzone.destroy();
            }

            // Crear una nueva instancia de Dropzone
            myDropzone = new Dropzone("#archivosDropzone", {
                url: updateAction,
                paramName: "file[]",
                maxFilesize: 20,
                addRemoveLinks: true,
                autoProcessQueue: false,
                uploadMultiple: true,
                parallelUploads: 100,
                dictDefaultMessage: "Arrastra y suelta tus archivos aquí o haz click para subirlos.",
                dictFileTooBig: "El archivo es demasiado grande. El tamaño máximo permitido es 20MB.",
                init: function() {
                    var dz = this;

                    var submitButton = document.querySelector("#submit-all");
                    submitButton.addEventListener("click", function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        var fileInput = document.getElementById("hiddenFileInput");
                        var files = dz.getAcceptedFiles();

                        if (files.length > 0) {
                            var dataTransfer = new DataTransfer();
                            files.forEach(function(file) {
                                dataTransfer.items.add(file);
                            });
                            fileInput.files = dataTransfer.files;
                        }

                        if (dz.getQueuedFiles().length > 0) {
                            dz.processQueue();
                        } else {
                            form.submit();
                        }                       
                    });

                    dz.on("sendingmultiple", function(file, xhr, formData) {
                        var data = $(form).serializeArray();
                        $.each(data, function(key, el) {
                            formData.append(el.name, el.value);
                        });
                    });

                    dz.on("queuecomplete", function() {
                        form.submit();
                    });

                    dz.on("error", function(file, message) {
                        if (file.size > (20 * 1024 * 1024)) {
                            document.getElementById('fileSizeError').style.display = 'block';
                            dz.removeFile(file);
                        }
                    });


                }
            });
        });


        $('.btnVerArchivos').click(function() {
            var accionId = $(this).data('id');
            var hallazgoId = $(this).data('hallazgo_id');

            $.ajax({
                url: '/smp/' + hallazgoId + '/acciones/' + accionId + '/archivos',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var archivosTable = $('#archivosTable tbody');
                    archivosTable.empty();

                    if (data.length === 0) {
                        archivosTable.append(
                            '<tr><td colspan="4">No se han encontrado archivos.</td></tr>');
                    } else {
                        $.each(data, function(index, file) {
                            var date = new Date(file.lastModified * 1000);
                            var formattedDate = date.toLocaleString();
                            var sizeInKB = (file.size / 1024).toFixed(2);

                            var row = '<tr>' +
                                '<td><a href="' + file.url + '" target="_blank">' + file.name +
                                '</a></td>' +
                                '<td>' + formattedDate + '</td>' +
                                '<td>' + sizeInKB + ' KB</td>' +
                                '<td>' +
                                '<form action="{{ route('smp.acciones.eliminarArchivo', [':hallazgo_id', ':accion_id']) }}" method="POST" class="deleteForm" style="display:inline;">' +
                                '@csrf' +
                                '@method('DELETE')' +
                                '<input type="hidden" name="fileUrl" value="' + file.url +
                                '">' +
                                '<button type="submit" class="btn btn-danger btn-sm confirmDelete">Eliminar</button>' +
                                '</form>' +
                                '</td>' +
                                '</tr>';

                            // Reemplazar los marcadores de posición en la URL de la ruta
                            row = row.replace(':hallazgo_id', hallazgoId);
                            row = row.replace(':accion_id', accionId);

                            archivosTable.append(row);
                        });
                    }

                    $('#archivosModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar los archivos: " + error);
                    alert('Error al cargar los archivos');
                }
            });
        });
        $(document).on('click', '.confirmDelete', function(e) {
            e.preventDefault(); // Prevenir la acción predeterminada del formulario

            var form = $(this).closest('form'); // Encontrar el formulario al que pertenece este botón

            // Mostrar cuadro de diálogo de confirmación
            if (confirm('¿Estás seguro de que deseas eliminar este archivo? Esta acción no se puede deshacer.')) {
                form.submit(); // Enviar formulario si el usuario confirma
            }
        });
    </script>

@endsection
