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
            background-color: #293241;
            color: #fff;

        }

        .bg-acciones:hover {
            color: #fff;
        }

        #filterFuenteDropdown {
            display: none;
            /* Ocultamos el filtro al inicio */
            position: absolute;
            background-color: white;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 5px;
            font-size: 12x;
        }

        .dropdown-menu {
            max-height: 200px;
            overflow-y: auto;
            padding: 0;
            border-radius: 5px;
            width: 100%;
            font-size: 12x;
        }

        .dropdown-item {
            padding: 8px 15px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 12x;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        /* Estilos para el botón de filtro */
        #dropdownMenuButton {
            background-color: #f8f9fa;
            border-color: #ced4da;
            color: #495057;
            border-radius: 5px;
            font-size: 12x;
            font-weight: 600;
            padding: 5px 15px;
        }

        #dropdownMenuButton:focus {
            box-shadow: none;
        }

        #dropdownMenuButton.btn-primary {
            background-color: #007bff;
            /* Azul Bootstrap */
            border-color: #007bff;
            color: white;
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
                <form action="{{ route('documento.buscar') }}" method="GET">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Título de la lista de procesos -->
                        <div class="col-md-5 text-md-left">
                            <h4 class="card-title">Documentación del Proceso</h4>
                           
                        </div>
                        <div class="col-md-7 text-md-right">                            
                          
                            <div class="input-group">
                                <input type="text" name="buscar_documento" id="buscar_documento" class="form-control form-control-sm me-2"
                                placeholder="Buscar por nombre documento" style="margin-right: 5px">
                                <input type="text" name="buscar_proceso" id="buscar_proceso" class="form-control form-control-sm me-2"
                                    placeholder="Buscar por Proceso">

                                <!-- Botón de Filtrar -->
                                <button type="submit" class="btn bg-black btn-sm">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                                <a href="#" class="btn-primary btn-sm" data-toggle="modal"
                                onclick="Livewire.dispatchTo('documento-modal','nuevoDocumento')"
                                data-target="#documentoModal">
                                    <i class="fas fa-plus-circle"></i> Agregar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 text-md-right">
                       
                    </div>
                </div>

                <table class="table table-bordered table-hover table-sm table-procesos" id="documentosTable">
                    <thead class="table-header">
                        <tr>
                            <th style="width:18%">Proceso</th>
                            <th style="width:15%">Código Documento</th>
                            <th>Nombre Documento</th>
                            <th class=" text-nowrap">Tipo de Documento</th>
                            <th class="text-center text-nowrap">Fuente
                                <i class="fas fa-filter  fa-xs" style="cursor: pointer; margin-left: 5px;"
                                    id="filterIcon"></i>
                                <div class="dropdown" id="filterFuenteDropdown" style="display:none;">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        Seleccionar
                                    </button>
                                    <div class="dropdown-menu" id="filterFuenteMenu">
                                        <!-- Filtros dinámicos se añadirán aquí -->
                                    </div>
                                </div>
                            </th>
                            <th>Versión</th>
                            <th class="text-center text-nowrap">Actualización</th>
                            <th class="text-center">Enlace</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documentos as $documento)
                            <tr class="clickable-row">
                                <td>{{ $documento->proceso->cod_proceso }} - {{ $documento->proceso->proceso_nombre }}</td>
                                <td>{{ $documento->cod_documento }}</td>
                                <td>{{ $documento->nombre }}</td>
                                <td>{{ $documento->tipo_documento->nombre }}</td>
                                <td class="text-center">{{ $documento->fuente }}</td>
                                <td class="text-center">
                                    {{ str_pad($documento->ultimaVersion->version ?? '0', 2, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="text-center text-nowrap">
                                    {{ $documento->ultimaVersion->fecha_publicacion ?? '' }}
                                </td>
                                <td class="text-center">
                                    <a href="#" class="px-1 btnVerDocumento" data-toggle="modal"
                                        onclick="Livewire.dispatchTo('pdf-modal','openPdfModal', { path: '{{ optional($documento->ultimaVersion)->archivo_path ?? '' }}' })"
                                        data-target="#pdfModal">
                                        <i class="fas fa-file-pdf fa-lg text-danger"></i>
                                    </a>

                                </td>

                                <td class="text-center">
                                    <div class="d-flex align-items-center">

                                        <a href="#" class="px-1 btnEditarDocumento" data-toggle="modal"
                                            data-id="{{ $documento->id }}"
                                            onclick="Livewire.dispatchTo('documento-modal','verDocumento', { id: 1 })"
                                            data-target="#documentoModal">
                                            <i class="fas fa-pencil-alt text-dark"></i>
                                        </a>
                                        <a href="#" class="px-3">
                                            <i class="fas fa-trash-alt text-danger"></i>
                                        </a>

                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    @livewire('documento-modal')
    @livewire('pdf-modal')
@endsection
@push('scripts')
    <script>
        let documentoId = null;
        $(document).ready(function() {
            var table = $('#documentosTable').DataTable({
                "columnDefs": [{
                        "orderable": false,
                        "targets": [0, 1, 2, 3, 4, 5, 6, 7]
                    }, // Deshabilitar la ordenación en todas las columnas excepto la columna "Fuente"
                    {
                        "orderable": true,
                        "targets": [4]
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

            // Crear los filtros de la columna Fuente
            var uniqueFuente = [];
            table.column(4).data().unique().each(function(value) {
                if (uniqueFuente.indexOf(value) === -1) {
                    uniqueFuente.push(value);
                    $('#filterFuenteMenu').append('<a class="dropdown-item" href="#" data-value="' + value +
                        '">' + value + '</a>');
                }
            });

            // Mostrar el dropdown de filtro solo cuando se hace foco
            $('#documentosTable th:nth-child(5)').on('click', function() {
                $('#filterFuenteDropdown').toggle(); // Mostrar/ocultar el dropdown
            });

            // Filtro de la columna Fuente cuando se selecciona una opción
            $('#filterFuenteMenu').on('click', 'a', function() {
                var selectedFuente = $(this).data('value');

                // Si el valor seleccionado es el mismo que el filtro aplicado, limpiamos el filtro
                var currentFilter = table.column(4).search();
                if (currentFilter === selectedFuente) {
                    table.column(4).search('').draw(); // Limpiar el filtro
                    $('#dropdownMenuButton').text('Seleccionar Fuente'); // Resetear el texto del botón
                    $('#dropdownMenuButton').removeClass('btn-primary').addClass(
                        'btn-outline-secondary'); // Cambiar color del botón a estado normal
                } else {
                    table.column(4).search(selectedFuente).draw(); // Aplicar el filtro
                    $('#dropdownMenuButton').text(
                        selectedFuente); // Actualizar el texto del botón con la opción seleccionada
                    $('#dropdownMenuButton').removeClass('btn-outline-secondary').addClass(
                        'btn-primary'); // Cambiar color del botón a azul
                }
            });
        });


        // Delegar el clic en las filas para manejar selección

        $(document).on('click', '.clickable-row', function() {
            $('.clickable-row').removeClass('selected').find('td').css('opacity', 1);
            $(this).addClass('selected').find('td:not(:last-child)').css('opacity', 0.8);

        });


        $(document).on('click', '.btnEditarDocumento', function(e) {
            e.preventDefault();
            var documentoId = $(this).data('id'); // Obtener el ID del botón
            Livewire.dispatchTo('documento-modal', 'verDocumento', {
                id: documentoId
            });
        });
    </script>
@endpush
