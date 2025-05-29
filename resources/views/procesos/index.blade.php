@extends('layout.master')
@section('title', 'SIG')
@push('styles')
    <style>
        .selected {
            background-color: #ECECEC;
            /* Light gray background for selected row */
        }

        .table-procesos {
            font-size: 13px;
        }

        .bg-acciones {
            background-color: #949292;
            color: #fff;

        }

        .bg-acciones:hover {
            color: #fff;
            background-color: #666565;            
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
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('procesos.index') }}">Procesos</a></li>

            </ol>
        </nav>
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <!-- Título de la lista de procesos -->
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title mb-0">Lista de Procesos</h3>
                    </div>

                    <!-- Botones Nuevo, Editar y Eliminar Proceso-->
                    <div class="col-md-6 text-md-right">
                        <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" id="btnNuevo"
                            onclick="Livewire.dispatchTo('proceso-modal','nuevoProceso')" data-target="#procesoModal"
                            data-toggle="modal">
                            <i class="fas fa-plus-circle"></i> Agregar
                        </a>

                        <button class="btn btn-warning btn-sm" id="btnEditar" disabled
                            onclick="Livewire.dispatchTo('proceso-modal','verProceso', { id: $(this).data('id') })"
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
                <!-- Filtros del Proceso-->
                <div class="card-body">
                    <form method="GET" action="{{ route('procesos.index') }}">
                        <!-- Filtros en la misma línea -->
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Filtro por Proceso Padre -->

                            <input type="text" name="buscar_proceso" id="buscar_proceso" class="form-control me-2"
                                placeholder="Buscar por Proceso" style="margin-right: 5px">

                            <!-- Filtro por Tipo de Proceso -->
                            <div class="input-group">
                                <select name="proceso_padre_id" id="proceso_padre_id" class="form-control me-2">
                                    <option value="">Selecciona un macro proceso</option>
                                    @foreach ($procesos_padre as $procesoPadre)
                                        <option value="{{ $procesoPadre->id }}"
                                            {{ request('proceso_padre_id') == $procesoPadre->id ? 'selected' : '' }}>
                                            {{ $procesoPadre->cod_proceso }}. {{ $procesoPadre->proceso_nombre }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Botón de Filtrar -->
                                <button type="submit" class="btn bg-dark btn-sm">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-bordered table-hover table-sm table-procesos" id="procesos">
                    <thead class="table-header">
                        <tr>
                            <th>Cod Proceso</th>
                            <th style="display: none;">Id</th>
                            <th style="width: 20%">Nombre</th>
                            <th>Tipo</th>
                            <th>Sigla</th>
                            <th>Nivel</th>
                            <th>Responsable</th>
                            <th class="text-center">OUOs</th>
                            <th class="text-center">Subprocesos</th>
                            <th style="width: 14%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($procesos as $proceso)
                            <tr class="clickable-row" data-id="{{ $proceso->id }}">
                                <td>{{ $proceso->cod_proceso }}</td>
                                <td style="display: none;">{{ $proceso->id }}</td>
                                <td>{{ $proceso->proceso_nombre }}</td>
                                <td>{{ $proceso->proceso_tipo }}</td>
                                <td>{{ $proceso->proceso_sigla }}</td>
                                <td>{{ $proceso->proceso_nivel }}</td>
                                <td>
                                    {{ $proceso->ouo_responsable() ?? 'No hay responsable' }}

                                </td>

                                <td class="text-center">{{ $proceso->ouos->count() }}</td>
                                <td class="text-center">{{ $proceso->subprocesos->count() }}</td>

                                <td class="d-flex justify-content-between align-items-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm dropdown-toggle bg-acciones" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            Acciones
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <button class="dropdown-item verOUOBtn"
                                                onclick="Livewire.dispatchTo('asignarouo-modal', 'obtenerOUO', {id:{{ $proceso->id }}})"
                                                data-target="#ModalOUO" data-toggle="modal">
                                                <i class="fas fa-link"></i> Asignar OUO
                                            </button>
                                            <a class="dropdown-item"
                                                href="{{ route('caracterizacion.index', ['proceso_id' => $proceso->id]) }}">
                                                <i class="fas fa-file-pdf"></i> Documentación
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('documentos.validar.enlaces', ['proceso_id' => $proceso->id]) }}">
                                                <i class="fa fa-exclamation-circle"></i> Enlaces
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('indicadores.listar', ['proceso_id' => $proceso->id]) }}">
                                                <i class="fas fa-chart-bar"></i> Indicadores
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('obligaciones.listar', ['proceso_id' => $proceso->id]) }}">
                                                <i class="fas fa-list"></i> Obligaciones
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('riesgos.listar', ['proceso_id' => $proceso->id]) }}">
                                                <i class="fas fa-exclamation-circle"></i> Riesgos
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="fas fa-fire"></i> Hallazgos
                                            </a>


                                        </div>
                                    </div>
                                    <a href="{{ route('procesos.subprocesos', $proceso->id) }}"
                                        class="btn btn-sm bg-primary" type="button" aria-haspopup="true"
                                        aria-expanded="false">
                                        Procesos
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
              
            </div>
        </div>
    </div>
    @livewire('asignarouo-modal')
@endsection
@push('scripts')
    <script>
        let procesoId = null;
        document.addEventListener('DOMContentLoaded', function() {

            const $loadingSpinner = $('#loading-spinner');
            const $tableOUOs = $('.table-ouos');
            const $listaOUOs = $('#listaOUOs');
            const $verModal = $('#ModalOUO');
            const $formAddOUO = $('#formAddOUO');

            var table = $('#procesos').DataTable({
                "columnDefs": [{
                        "orderable": false,
                        "targets": [1, 3, 4, 5, 6, 7, 8, 9]
                    }, // Deshabilitar la ordenación en todas las columnas excepto la columna "Fuente"
                    {
                        "orderable": true,
                        "targets": [2]
                    } // Habilitar la ordenación en la columna "Fuente" (columna 5)
                ],
                "language": {
                    "search": "Buscar:",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "paginate": {
                        "first": "Primero", // Botón de la primera página
                        "last": "Último", // Botón de la última página
                        "next": "Siguiente", // Botón para la siguiente página
                        "previous": "Anterior" // Botón para la página anterior
                    },
                    "info": "",
                }

            });


            // Delegar el clic en las filas para manejar selección

            $(document).on('click', '.clickable-row', function() {
                var editButton = $('#btnEditar');
                var deleteButton = $('#btnEliminar');


                $('.clickable-row').removeClass('selected').find('td').css('opacity', 1);
                $(this).addClass('selected').find('td:not(:last-child)').css('opacity', 0.8);
                procesoId = $(this).data('id');
                console.log(procesoId); // Asegurarse de que estamos capturando el ID
                // Establecer el atributo data-id
                editButton.prop('disabled', false).data('id', procesoId);
                deleteButton.prop('disabled', false).data('id', procesoId);

                // Adjuntar el evento click
                editButton.on('click', function() {
                    var id = $(this).data('id'); // Obtener el ID del botón
                    Livewire.dispatchTo('proceso-modal', 'verProceso', {
                        id: id
                    });
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
                        // Manejar la respuesta exitosa
                        const successAlert = document.getElementById('success-alert');
                        if (successAlert) {
                            successAlert.innerHTML = 'Proceso eliminado exitosamente.';
                            successAlert.style.display =
                                'block'; // Asegúrate de que el alert esté visible

                            // Ocultar el mensaje después de 3 segundos
                            setTimeout(function() {
                                successAlert.style.display = 'none';
                                location.reload();
                            }, 2000);
                        }

                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const successAlert = document.getElementById('success-alert');

            if (successAlert) {
                setTimeout(function() {
                    successAlert.style.display = 'none';
                }, 2000); //  milisegundos = 1 segundo
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Escucha el evento para actualizar la vista index
            window.addEventListener('updateIndexView', function() {
                // Recarga la vista index
                location.reload();
            });
        });
    </script>
@endpush
