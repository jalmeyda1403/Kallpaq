@extends('layout.master')
@section('title', 'SIG')
@push('styles')
   
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

                    <div class="col-md-6 text-md-right">


                        <!-- Botones Nuevo, Editar y Eliminar Proceso-->
                        <a href="#" class="btn btn-primary btn-sm" id="btnNuevo" title="Nuevo Proceso">
                            <i class="fas fa-plus-circle"></i> Agregar
                        </a>

                        <button class="btn btn-warning btn-sm" id="btnEditar" disabled data-id="">
                            <i class="fas fa-pencil-alt"></i> Editar
                        </button>

                        <button class="btn btn-danger btn-sm" id="btnEliminar" disabled data-id="">
                            <i class="fas fa-trash-alt"></i> Eliminar
                        </button>

                        <div id="app">
                            <proceso-modal></proceso-modal>
                        </div>
                    </div>

                    @routes
                    @vite(['resources/js/app.js'])


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
                <table class="table table-hover table-striped table-sm table-procesos" id="procesos">
                    <thead class="table-header">
                        <tr>
                            <th>Cod Proceso</th>
                            <th style="display: none;">Id</th>
                            <th style="width: 20%">Nombre</th>
                            <th>Tipo</th>
                            <th>Sigla</th>
                            <th>Nivel</th>
                            <th style="width: 20%">Responsable</th>
                            <th class="text-center">OUOs</th>
                            <th class="text-center">Subprocesos</th>
                            <th class="text-center"></th>
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

                                <td class="text-center">
                                    <a href ="#"
                                        onclick="Livewire.dispatchTo('asignarouo-modal', 'obtenerOUO', {id:{{ $proceso->id }}})"
                                        data-target="#ModalOUO" data-toggle="modal">
                                        {{ $proceso->ouos->count() }}</a>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('procesos.subprocesos', $proceso->id) }}"
                                        class="text-primary text-decoration-underline" title="Ver Subprocesos">
                                        {{ $proceso->subprocesos->count() }}
                                    </a>
                                </td>

                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm dropdown-toggle" type="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-link"></i>  Asociaciones
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">

                                            <a class="dropdown-item"
                                                href="{{ route('procesos.caracterizacion', $proceso->id) }}">
                                                <i class="fas fa-file-alt fa-fw mr-2"></i>Documentación
                                            </a>
                                           <!-- 
                                            <a class="dropdown-item"
                                                href="{{ route('documentos.validar.enlaces', ['proceso_id' => $proceso->id]) }}">
                                                <i class="fa fa-exclamation-circle"></i> Enlaces
                                            </a>
                                            -->
                                            <a class="dropdown-item"
                                                href="{{ route('indicadores.listar', ['proceso_id' => $proceso->id]) }}">
                                                <i class="fas fa-chart-bar fa-fw mr-2"></i>Indicadores
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('obligaciones.listar', ['proceso_id' => $proceso->id]) }}">
                                                <i class="fas fa-list-ul fa-fw mr-2"></i>Obligaciones
                                            </a>
                                            <a class="dropdown-item"
                                                href="{{ route('riesgos.listar', ['proceso_id' => $proceso->id]) }}">
                                                <i class="fas fa-exclamation-triangle fa-fw mr-2"></i>Riesgos
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="fas fa-fire fa-fw mr-2"></i>Hallazgos
                                            </a>


                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        let procesoId = null;
        var table = $('#procesos').DataTable({
            dom: "<'row'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end'B>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [{
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Descargar',
                        className: 'btn btn-success btn-sm'
                    },

                ],
            "pageLength": 25,
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

        $(document).on('click', '.clickable-row', function() {
            $('.clickable-row').removeClass('selected').find('td').css('opacity', 1);
            $(this).addClass('selected').find('td:not(:last-child)').css('opacity', 0.8);

            let id = $(this).data('id');

            $('#btnEditar').prop('disabled', false).data('id', id);
            $('#btnEliminar').prop('disabled', false).data('id', id);
        });

        $('#btnNuevo').on('click', function(e) {
            e.preventDefault();
            window.dispatchEvent(new Event('abrirProcesoModal'));
        });

        $('#btnEditar').on('click', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            if (id) {
                window.dispatchEvent(new CustomEvent('editarProceso', {
                    detail: {
                        id
                    }
                }));
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const successAlert = document.getElementById('success-alert');

            if (successAlert) {
                setTimeout(function() {
                    successAlert.style.display = 'none';
                }, 2000); //  milisegundos = 1 segundo
            }
        });

       
    </script>
@endpush
