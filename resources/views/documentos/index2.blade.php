@extends('layout.master')
@section('title', 'SIG')
@push('styles')
    <style>
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

        /* Estilo general de opciones */
        .select2-container--default .select2-results__option {
            font-size: 12px;
        }

        /* Hover en opciones */
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #6c757d !important;
            /* grey */
            color: white !important;
        }

        /* Estilo de etiquetas seleccionadas */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #dc3545 !important;
            /* danger */
            border-color: #dc3545 !important;
            color: white !important;
            font-size: 12px;
        }

        /* Estilo del botón de cerrar en etiquetas */
        .select2-container--default .select2-selection__choice__remove {
            color: white !important;
            font-size: 12px;
        }

        .select2-container--default .select2-selection__choice__remove:hover {
            color: #ffcccc !important;
        }

        /* Estilo del campo de búsqueda interno (placeholder y texto) */
        .select2-container--default .select2-selection--multiple .select2-search__field {
            font-size: 13px !important;
        }

        select:invalid {
            color: #94939a;
        }

        .select2-hidden-init {
            visibility: hidden;
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
                        <h3 class="card-title mb-0">Documentación del Proceso</h3>
                    </div>
                    @if (Auth::check())
                        <div class="col-md-6 text-md-right">
                            <a href="#" class="btn btn-primary btn-sm" data-toggle="modal"
                                onclick="Livewire.dispatchTo('documento.documento-modal','nuevoDocumento')"
                                data-target="#documentoModal">
                                <i class="fas fa-plus-circle"></i> Agregar
                            </a>
                            <button class="btn btn-warning btn-sm" id="btnEditar" disabled
                                onclick="Livewire.dispatchTo('documento.documento-modal','verDocumento', { id: $(this).data('id') })"
                                data-target="#documentoModal" data-toggle="modal">
                                <i class="fas fa-pencil-alt"></i> Editar
                            </button>

                            <button class="btn btn-danger btn-sm" id="btnEliminar" disabled
                                onclick="confirmarEliminacion($(this).data('id'))">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        </div>
                    @endif
                </div>
                <hr>
                <!-- Filtros del Proceso-->
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ route('documento.buscar') }}" method="GET">
                            <div class="d-flex justify-content-between align-items-center">

                                <input type="text" name="buscar_documento" id="buscar_documento"
                                    value="{{ request('buscar_documento') }}" class="form-control me-2"
                                    placeholder="Buscar por nombre documento" style="margin-right: 5px">

                                <input type="text" name="buscar_proceso" id="buscar_proceso"
                                    value="{{ request('buscar_proceso') }}" class="form-control me-2"
                                    placeholder="Buscar por Proceso" style="margin-right: 5px">

                                <select name="fuente" id="fuente" class="form-control me-2" style="margin-right: 5px">
                                    <option value="">Buscar por fuente</option>
                                    <option value="interno" {{ request('fuente') == 'interno' ? 'selected' : '' }}>Fuente
                                        Interna</option>
                                    <option value="externo" {{ request('fuente') == 'externo' ? 'selected' : '' }}>Fuente
                                        Externa</option>
                                </select>

                                <div class="input-group">
                                    <select name="tipo_documento[]"
                                        id="tipo_documento"class="form-control me-2 select2-hidden-init" multiple>
                                        @foreach (config('opciones.tipos_documento') as $codigo => $nombre)
                                            <option value="{{ $codigo }}"
                                                {{ collect(request('tipo_documento'))->contains($codigo) ? 'selected' : '' }}>
                                                {{ $nombre }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <!-- Botón de Filtrar -->
                                    <button type="submit" class="btn bg-black btn-sm">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>

                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover table-sm table-procesos" id="documentosTable">
                    <thead class="table-header">
                        <tr>
                            <th style="width:18%">Proceso</th>
                            <th style="width:15%">Código Documento</th>
                            <th>Nombre Documento</th>
                            <th class=" text-nowrap">Tipo de Documento</th>
                            <th class="text-center text-nowrap">Fuente
                            </th>
                            <th>Versión</th>
                            <th>Estado</th>
                            <th class="text-center text-nowrap">Publicación</th>
                            <th class="text-center"><i class="fas fa-info"><i></th>
                            <th class="text-center">Ver</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documentos as $documento)
                            <tr class="clickable-row" data-id="{{ $documento->id }}">
                                <td>{{ $documento->proceso->cod_proceso }} - {{ $documento->proceso->proceso_nombre }}
                                </td>
                                <td>{{ $documento->cod_documento }}</td>
                                <td>{{ $documento->nombre }}</td>
                                <td>{{ $documento->tipo_documento->nombre }}</td>
                                <td class="text-center">{{ $documento->fuente }}</td>
                                <td class="text-center">
                                    {{ str_pad($documento->ultimaVersion->version ?? '0', 2, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="text-center text-nowrap">
                                    {{ $documento->estado->nombre() }}
                                </td>
                                <td class="text-center text-nowrap">
                                    {{ $documento->ultimaVersion->fecha_publicacion ?? '' }}
                                </td>


                                <td class="text-center">
                                    @if (optional($documento->ultimaVersion)->enlace_valido === 1)
                                        <i class="fas fa-check-circle text-success" title="Enlace válido"></i>
                                    @else
                                        <i class="fas fa-exclamation-circle text-warning" title="Enlace no válido"></i>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    <a href="#" class="px-1 btnVerDocumento" data-toggle="modal"
                                        onclick="Livewire.dispatchTo('documento.documento-pdf-modal','openPdfModal', { path: '{{ optional($documento->ultimaVersion)->archivo_path ?? '' }}' })"
                                        data-target="#pdfModal" title="Abrir Pdf">
                                        <i class="fas fa-file-pdf fa-lg text-danger"></i>
                                    </a>


                                    <a href="#" class="px-1 btnverHijos" data-toggle="modal"
                                        title="Ver Documentos Relacionados"
                                        onclick="Livewire.dispatchTo('documento.documento-hijos-modal','mostrarHijos', { id: {{ $documento->id }} })"
                                        data-target="#documentoHijosModal">
                                        <i class="fas fa-copy"></i>
                                    </a>


                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    @livewire('documento.documento-hijos-modal')
    @livewire('documento.documento-modal')
    @livewire('documento.documento-pdf-modal')
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            const $select = $('#tipo_documento');
            $select.select2({
                placeholder: "Buscar por Tipo de documento",
                allowClear: true
            });

        });
        
       

        $(document).ready(function() {
            var table = $('#documentosTable').DataTable({
                dom: "<'row'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end'B>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [{
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Descargar',
                        className: 'btn btn-success btn-sm'
                    },

                ],
                "columnDefs": [{
                        "orderable": false,
                        "targets": [0, 1, 2, 4, 5, 6, 7, 8]
                    }, // Deshabilitar la ordenación en todas las columnas excepto la columna "Fuente"
                    {
                        "orderable": true,
                        "targets": [3]
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

        });



        // Delegar el clic en las filas para manejar selección

        $(document).on('click', '.clickable-row', function() {
            $('.clickable-row').removeClass('selected').find('td').css('opacity', 1);
            $(this).addClass('selected').find('td:not(:last-child)').css('opacity', 0.8);
            var editButton = $('#btnEditar');
            var deleteButton = $('#btnEliminar');
            procesoId = $(this).data('id');
            console.log(procesoId); // Asegurarse de que estamos capturando el ID
            // Establecer el atributo data-id
            editButton.prop('disabled', false).data('id', procesoId);
            deleteButton.prop('disabled', false).data('id', procesoId);

        });
    </script>
@endpush
