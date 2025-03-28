@extends('layout.master')
@section('title', 'SIG')
@push('style')
    <style type="text/css">
        /* Cambiar el color de fondo y el borde de los elementos seleccionados */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #e74c3c !important;
            /* Cambia el color de fondo */
            border: 1px solid #e74c3c !important;
            /* Cambia el borde */
            color: white !important;
            /* Cambia el color del texto */
        }

        /* Cambiar el color del icono de eliminar (x) */
        .select2-selection__choice__remove {
            color: white !important;
            /* Cambia el color del icono de eliminar */
        }

        .select2-checkbox {
            margin-right: 10px;
        }

        .selected {
            background-color: #ECECEC;
            /* Light gray background for selected row */
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <!-- Título de la lista de procesos -->
                    <div class="col-md-6">
                        <h3 class="card-title mb-0">Lista de Procesos</h3>
                    </div>

                    <!-- Botones Nuevo y Editar -->
                    <div class="col-md-6 text-md-right">
                        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#procesoModal"
                            wire:click="clearForm" id="openModalButton">
                            <i class="fas fa-plus"></i> Nuevo
                        </button>
                        <a href="#" class="btn btn-warning btn-sm" title="Editar Proceso" id="btnEditar"
                            data-id="">
                            <i class="fas fa-pencil-alt"></i> Editar
                        </a>
                    </div>
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
                            <th>Código del Proceso</th>
                            <th style="display: none;">Id</th>
                            <th>Nombre del Proceso</th>
                            <th>Tipo del Proceso</th>
                            <th>Proceso Padre</th>
                            <th>Nivel Proceso</th>
                            <th>OUO Asociadas</th>
                            <th style="width: 20%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($procesos as $proceso)
                            <tr class="clickable-row">
                                <td>{{ $proceso->cod_proceso }}</td>
                                <td style="display: none;">{{ $proceso->id }}</td>
                                <td>{{ $proceso->proceso_nombre }}</td>
                                <td>{{ $proceso->proceso_tipo }}</td>
                                <td>{{ $proceso->procesoPadre ? $proceso->procesoPadre->cod_proceso : 'N/A' }}
                                </td>
                                <td>{{ $proceso->proceso_nivel }}</td>
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
    <!-- Componente Modal Procesos -->
    <livewire:proceso-modal /> <!-- Este es el componente Livewire -->

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
        let procesoId = null;
        const $loadingSpinner = $('#loading-spinner');
        const $tableOUOs = $('.table-ouos');
        const $listaOUOs = $('#listaOUOs');
        const $verModal = $('#ModalOUO');
        const $formAddOUO = $('#formAddOUO');
        $('#procesos').DataTable();

        // Delegar el clic en las filas para manejar selección
        $(document).on('click', '.clickable-row', function() {
            // Remover clase "selected" de todas las filas
            $('.clickable-row').removeClass('selected').find('td').css('opacity', 1);


            // Añadir la clase "selected" a la fila clickeada
            $(this).addClass('selected').find('td:not(:last-child)').css('opacity', 0.8);

            // Obtener el ID de la fila seleccionada (de la celda oculta)
            const id = $(this).find('td:eq(1)').text().trim();

            // Habilitar el botón de eliminar y almacenar el ID en un atributo de datos
            $('#btnEditar').prop('disabled', false).data('id', id);
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
                    $formAddOUO[0].reset(); // Limpiar los campos del formulario (si es necesario)
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
             
    </script>

@endpush
