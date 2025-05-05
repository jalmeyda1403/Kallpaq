@extends('layout.master')
@section('title', 'SIG')
@push('styles')
    <style>
        .selected {
            background-color: #ECECEC;
            /* Light gray background for selected row */
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
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
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <!-- Título de la lista de procesos -->
                    <div class="col-md-6">
                        <h3 class="card-title mb-0">Lista de Procesos</h3>
                    </div>

                    <!-- Botones Nuevo y Editar -->
                    <div class="col-md-6 text-md-right">
                        <a href="#" class="btn btn-success btn-sm" data-toggle="modal" id="btnNuevo"
                            onclick="Livewire.dispatchTo('proceso-modal','nuevoProceso')" data-target="#procesoModal"
                            data-toggle="modal">
                            <i class="fas fa-plus"></i> Nuevo
                        </a>

                        <button class="btn btn-primary btn-sm" id="btnEditar"
                            onclick="Livewire.dispatchTo('proceso-modal','verProceso', { id: 31 })"
                            data-target="#procesoModal" data-toggle="modal">
                            <i class="fas fa-pencil-alt"></i> Editar
                        </button>

                        <button class="btn btn-danger btn-sm" id="btnEliminar" disabled
                            onclick="confirmarEliminacion($(this).data('id'))">
                            <i class="fas fa-trash-alt"></i> Eliminar
                        </button>
                    </div>
                    @livewire('proceso-modal')

                </div>
                <!-- Botón para añadir un nuevo proceso -->
                <div class="card-body">
                    <form method="GET" action="{{ route('procesos.index') }}">
                        <!-- Etiqueta y Select para Filtrar por Proceso Padre -->
                        <div class="input-group">
                            <select name="proceso_padre_id" id="proceso_padre_id" class="form-control">
                                <option value="">Selecciona un Proceso Padre</option>
                                @foreach ($procesos_padre as $procesoPadre)
                                    <option value="{{ $procesoPadre->id }}"
                                        {{ request('proceso_padre_id') == $procesoPadre->id ? 'selected' : '' }}>
                                        {{ $procesoPadre->cod_proceso }}. {{ $procesoPadre->proceso_nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm "><i class="fas fa-search"></i>
                                Filtrar</button>

                        </div>
                </div>
                </form>
            </div>

            <div class="card-body">

                <table class="table table-hover" id= "procesos">
                    <thead>
                        <tr>
                            <th>Cod Proceso</th>
                            <th style="display: none;">Id</th>
                            <th style="width: 20%">Nombre</th>
                            <th>Tipo</th>
                            <th>Nivel</th>
                            <th>Padre</th>
                            <th>OUOs</th>
                            <th style="width: 25%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($procesos as $proceso)
                            <tr class="clickable-row" data-id="{{ $proceso->id }}">
                                <td>{{ $proceso->cod_proceso }}</td>
                                <td style="display: none;">{{ $proceso->id }}</td>
                                <td>{{ $proceso->proceso_nombre }}</td>
                                <td>{{ $proceso->proceso_tipo }}</td>
                                <td>{{ $proceso->proceso_nivel }}</td>
                                <td>{{ $proceso->procesoPadre ? $proceso->procesoPadre->cod_proceso : '*' }}
                                </td>

                                <td>{{ $proceso->ouos->count() }}</td>

                                <td>
                                    <a href="#" class="btn btn-success btn-sm verOUOBtn" data-toggle="modal"
                                        data-id="{{ $proceso->id }}" data-target="#ModalOUO" title="Asociar OUOs">
                                        <i class="fas fa-link"></i>
                                    </a>


                                    <a href="{{ route('obligaciones.listar', ['proceso_id' => $proceso->id]) }}"
                                        class="btn btn-dark btn-sm" data-toggle="tooltip" title="Ver Obligaciones">
                                        <i class="fas fa-list"></i>
                                    </a>
                                    <a href="{{ route('riesgos.listar', ['proceso_id' => $proceso->id]) }}"
                                        class="btn bg-orange color-palette btn-sm" data-toggle="tooltip"
                                        title="Ver Riesgos">
                                        <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                                    </a>
                                    <a href="#" class="btn btn-danger btn-sm" data-toggle="tooltip"
                                        title="Ver Hallazgos">
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

    <!-- Modal para asociar/disociar OUOs -->
    <div class="modal fade" id="ModalOUO" tabindex="-1" role="dialog" aria-labelledby="verModalLabel" aria-hidden="true"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="margin-top: -12%">
            <div class="modal-content" style="border-radius: 15px">
                <div class="modal-header bg-olive color-palette"
                    style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                    <h5 class="modal-title" id="verRiesgosModalLabel">OUO Asociados</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Spinner que será visible durante la carga -->
                    <div id="loading-spinner" style="display: none; text-align: center;">
                        <i class="fa fa-spinner fa-spin" style="font-size: 50px;"></i> Cargando...
                    </div>
                    <form action="#" method="POST" id="formAddOUO">
                        @csrf

                        <div class="form-group">
                            <label for="ouos">Unidad Orgánica a Asociar</label>
                            <!-- Select2 con búsqueda y opción múltiple -->
                            <div class="input-group">
                                <!-- Campo oculto para el ID del proceso -->
                                <input type="hidden" name="ouo_id" id="ouo_id" required>

                                <!-- Campo de texto para el nombre del proceso -->
                                <input type="text" class="form-control" id="ouo_nombre" name="ouo_nombre" required
                                    disabled placeholder="Seleccione el Órgano o Undiad Orgánica a Asociar">

                                <!-- Botón para abrir el modal -->
                                <a href="#" class="btn bg-navy color-palette" data-toggle="modal"
                                    data-target="#ouooModal"><i class="fas fa-search"></i></a>
                            </div>
                        </div>
                        <x-modal-busqueda :ruta="route('ouos.listar')" campo-id="ouo_id" campo-nombre="ouo_nombre"
                            modal-titulo="OUO" modal-id="ouooModal" :modalBgcolor="'#001f3f'" :modalTxtcolor="'#FFFFFF'">
                        </x-modal-busqueda>

                        <button type="submit" class="btn btn-success" style="border-radius: 25px; padding: 10px 20px; ">
                            <i class="fas fa-link"></i> Asociar </i>
                        </button>
                    </form>


                    <hr>

                    <!-- Tabla de OUOs asociadas -->

                    <table class="table table-hover table-sm table-ouos">
                        <thead>
                            <tr>
                                <th>Código OUO</th>
                                <th>Nombre OUO</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="listaOUOs">
                            <!-- Las OUO se agregarán aquí dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let procesoId = null;
            const $loadingSpinner = $('#loading-spinner');
            const $tableOUOs = $('.table-ouos');
            const $listaOUOs = $('#listaOUOs');
            const $verModal = $('#ModalOUO');
            const $formAddOUO = $('#formAddOUO');
            $('#procesos').DataTable();

            // Delegar el clic en las filas para manejar selección


            $(document).on('click', '.clickable-row', function() {
                $('.clickable-row').removeClass('selected').find('td').css('opacity', 1);
                $(this).addClass('selected').find('td:not(:last-child)').css('opacity', 0.8);

                procesoId = $(this).data('id');
                console.log(procesoId); // Asegurarse de que estamos capturando el ID
                $('#btnEditar').prop('disabled', false).data('id', procesoId);
                $('#btnEliminar').prop('disabled', false).data('id', procesoId);
            });

            //al momento de cargar el modal
            $(document).on('click', '.verOUOBtn', function() {
                procesoId = $(this).data('id');
                $verModal.modal('show');
                obtenerOUO(procesoId);
            });



            function obtenerOUO(procesoId) {
                $loadingSpinner.show();
                $tableOUOs.hide();

                const url =
                    `{{ route('procesos.listarOUO', ['proceso_id' => ':proceso_id']) }}`
                    .replace(
                        ':proceso_id', procesoId);

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        $loadingSpinner.hide();
                        $tableOUOs.show();
                        actualizarTablaOUOs(response);
                    },
                    error: function(error) {
                        console.error('Error al obtener las OUO:', error);
                        $loadingSpinner.hide();
                        $tableOUOs.show();

                    }
                });
            }

            // Actualizar la tabla de OUOS
            function actualizarTablaOUOs(ouos) {
                $listaOUOs.empty(); // Limpiar la lista de OUOS

                if (ouos.length > 0) {
                    ouos.forEach(function(ouo) {
                        $listaOUOs.append(`
                    <tr data-proceso-id="${ouo.proceso_id}"> <!-- Aquí se pasa el proceso_id -->
                        <td>${ouo.ouo_codigo || "S/C"}</td>
                        <td>${ouo.ouo_nombre}</td>  
                        <td><a href="#" class="btn btn-danger btn-sm deleteOUOBtn" data-id="${ouo.id}"  data-proceso-id="${ouo.proceso_id}">
                         <i class="fas fa-trash-alt"></i></td>
                    </tr>
                `);
                    });
                } else {
                    $listaOUOs.append(
                        '<tr><td colspan="7">No hay OUO asociados a esta Proceso.</td></tr>');
                }
            }
            //Disociar OUO
            $(document).on('click', '.deleteOUOBtn', function() {
                event.preventDefault();
                const ouoId = $(this).data('id');
                const procesoId = $(this).data('proceso-id');
                const url =
                    `{{ route('procesos.disociarOUO', ['proceso_id' => '__PROCESO_ID__', 'ouo_id' => '__OUO_ID__']) }}`
                    .replace('__PROCESO_ID__', procesoId)
                    .replace('__OUO_ID__', ouoId);
                if (confirm(
                        '¿Estás seguro de que deseas disociar esta OUO? Esta acción no se puede deshacer.'
                    )) {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            obtenerOUO(procesoId); // Recargar la tabla
                        },
                        error: function(xhr) {
                            alert(xhr.responseJSON.error ||
                                'Error al disociar la OUO.');
                        }
                    });
                }
            });
            //Asociar OUO
            $formAddOUO.submit(function(e) {
                e.preventDefault(); // Prevenir el envío tradicional del formulario

                const _url = `{{ route('procesos.asociarOUO', ':proceso_id') }}`.replace(
                    ':proceso_id',
                    procesoId); // Obtener la URL del formulario


                // Crear un objeto con los datos del formulario
                const formData = {
                    ouo_id: $('#ouo_id').val(), // ID de la OUO seleccionada
                    ouo_nombre: $('#ouo_nombre').val(), // Nombre de la OUO
                    _token: $('meta[name="csrf-token"]').attr('content') // Incluir el token CSRF
                };

                // Realizar la solicitud AJAX para enviar los datos
                $.ajax({
                    url: _url, // La URL donde se enviarán los datos
                    method: 'POST', // El método HTTP (POST)
                    data: formData, // Los datos que se enviarán
                    success: function(response) {
                        $('#ouooModal').modal('hide'); // Cerrar el modal de búsqueda
                        $formAddOUO[0]
                            .reset(); // Limpiar los campos del formulario (si es necesario)
                        obtenerOUO(procesoId);

                    },
                    error: function(xhr, status, error) {
                        // En caso de error, puedes mostrar un mensaje
                        console.error('Error al asociar la OUO:', error);
                        alert('Ocurrió un error al asociar la OUO.');
                    }
                });
            });
            // Escuchar cuando el modal se muestra y emitir el evento para refrescar el componente Livewire
        });
        document.addEventListener("DOMContentLoaded", function() {
            // Seleccionamos todas las filas
            const rows = document.querySelectorAll('.clickable-row');
            const editButton = document.getElementById('btnEditar');

            rows.forEach(row => {
                row.addEventListener('click', function() {
                    // Obtenemos el id de la fila seleccionada
                    const procesoId = this.getAttribute('data-id');
                    console.log(procesoId);

                    // Habilitamos el botón "Editar" y le pasamos el id del proceso
                    editButton.disabled = false;
                    editButton.setAttribute('data-id', procesoId);
                    editButton.setAttribute('onclick',
                        `Livewire.dispatchTo('proceso-modal', 'verProceso', { id: ${procesoId} })`
                    );

                });
            });
        });

        function confirmarEliminacion(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este proceso?')) {
                // Enviar una solicitud DELETE a la ruta del controlador usando la función route
                $.ajax({
                    url: "{{ route('proceso.eliminar', ['id' => '__id__']) }}".replace('__id__', id),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}', // Asegúrate de incluir el token CSRF
                    },
                    success: function(response) {
                        // Manejar la respuesta exitosa, por ejemplo, recargar la página o actualizar la vista
                        alert('Proceso eliminado exitosamente.');
                        location.reload();
                    }
                });
            }
        }
    </script>
@endpush
