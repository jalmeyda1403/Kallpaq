@extends('layout.master')
@section('title', 'Kallpaq')
@push('styles')
    <style>
        .selected {
            background-color: #ECECEC;
            /* Light gray background for selected row */
        }

        .collapse.show {
            transition: height 0.2s ease;
        }

        .table-smp {
            font-size: 13px;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"> <a
                        href="{{ route('smp.index', ['clasificacion' => $breadcrumb['codigo']]) }}">{{ $breadcrumb['nombre'] }}</a>
                </li>
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
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-md-left">
                                <h3 class="card-title mb-0">{{ $breadcrumb['nombre'] }}</h3>
                            </div>
                            <div class="col-md-6 text-md-right">

                                <a href="{{ route('smp.create', ['clasificacion' => $breadcrumb['codigo']]) }}"
                                    class="btn btn-primary btn-sm">
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
                            <!-- Filtros del Proceso-->
                            <div class="card-body">
                                <form method="GET"
                                    action="{{ route('smp.index', ['clasificacion' => $breadcrumb['codigo']]) }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <select name="sig" id="sig" class="form-control me-2"
                                            style="margin-right: 5px" placeholder="Seleccione Sistema Gestión">
                                            <option value="">Buscar por Sistema de Gestión</option>
                                            @foreach (config('opciones.sig') as $clave => $valor)
                                                <option value="{{ $clave }}"
                                                    {{ request('sig') == $clave ? 'selected' : '' }}>{{ $valor }}
                                                </option>
                                            @endforeach
                                        </select>


                                        <input type="text" name="informe_id" id="informe_id" class="form-control me-2"
                                            style="margin-right: 5px" value="{{ request('informe_id') }}"
                                            placeholder="Buscar por informe">

                                        <div class="input-group">
                                            <select name="year" id="year" class="form-control me-2">
                                                <option value="">Buscar por Año</option>
                                                @for ($i = date('Y'); $i >= 2015; $i--)
                                                    <option value="{{ $i }}"
                                                        {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}
                                                    </option>
                                                @endfor
                                            </select>

                                            <button type="submit" class="btn btn-sm btn-dark">
                                                <i class="fas fa-search"></i> Filtrar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- Zona de Filtros -->

                        <!-- Zona de Datatable -->
                        @if ($hallazgos->isEmpty())
                            <p>No hay solicitudes de mejora de procesos registrados.</p>
                        @else
                            <table class="table table-bordered table-hover table-sm table-smp" id="smpTable">
                                <thead class="table-header">
                                    <tr>
                                        <th>ID</th>
                                        <th>Proceso</th>
                                        <th style="width:10%">SMP</th>
                                        <th>Resumen</th>
                                        <th>Clasificación</th>
                                        <th>Estado</th>
                                        <th>Especialista</th>
                                        <th class="text-center text-nowrap">Fecha Solicitud</th>
                                        <th class="text-center text-nowrap">Fecha Cierre</th>
                                        <th>Avance</th>
                                        <th colspan="80px">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $item = 1;
                                    @endphp
                                    @foreach ($hallazgos as $hallazgo)
                                        <tr>
                                            <td>{{ $item++ }}</td>
                                            <td>{{ $hallazgo->proceso->proceso_nombre }}</td>
                                            <td>{{ $hallazgo->smp_cod }}</td>
                                            <td>{{ $hallazgo->resumen }}</td>
                                            <td>{{ $hallazgo->clasificacion }}</td>
                                            <td>{{ $hallazgo->estado }}</td>
                                            <td>
                                                @if ($hallazgo->especialistas->isNotEmpty())
                                                    {{-- Ordenar la colección de especialistas por fecha de asignación de manera descendente --}}
                                                    @php
                                                        $especialistasSorted = $hallazgo->especialistas
                                                            ->sortByDesc('pivot.fecha_asignacion')
                                                            ->first();
                                                    @endphp
                                                    {{ $especialistasSorted->nombres }}
                                                    {{ $especialistasSorted->apellido_paterno }}
                                                    {{ $especialistasSorted->apellido_materno }}
                                                @else
                                                    Sin asignar
                                                @endif
                                            </td>
                                            <td>{{ $hallazgo->fecha_solicitud }}</td>
                                            <td class="text-center text-nowrap">{{ $hallazgo->fecha_cierre_acciones }}</td>
                                            <td>{{ number_format($hallazgo->avance, 2) }}%</td>

                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('smp.show', $hallazgo->id) }}"
                                                        class="btn btn-warning btn-sm" data-toggle="tooltip"
                                                        title="Editar SMP">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-primary btn-sm btnAsignar"
                                                        data-toggle="modal" data-target="#especialistaModal"
                                                        title="Asignar Especialista" data-id="{{ $hallazgo->id }}">
                                                        <i class="fas fa-user"></i>
                                                    </a>
                                                    <a href="{{ route('smp.plan', $hallazgo->id) }}"
                                                        class="btn btn-success btn-sm" title="Elaborar Plan de Acción">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <a href="{{ route('smp.acciones.seguimiento', $hallazgo->id) }}"
                                                        class="btn btn-dark btn-sm" title="Seguimiento Acciones">
                                                        <i class="fas fa-tasks"></i>
                                                    </a>
                                                </div>


                                                <!-- Agrega más acciones según sea necesario -->
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- Formulario Modal para mostrar y asignar el especialista -->
        <div class="modal fade" id="especialistaModal" tabindex="-1" role="dialog"
            aria-labelledby="especialistaModalLabel" aria-hidden="true" data-backdrop="static">>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="especialistaModalLabel">Buscar Especialista</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="asignarEspecialistaForm" method="POST" action="">
                        @csrf

                        <div class="modal-body">
                            <!-- Coloca aquí el contenido del formulario para seleccionar el proceso -->
                            <input type="hidden" id="hallazgo_id" name="hallazgo_id">
                            <input type="hidden" id="especialista_id" name="especialista_id">
                            <input type="text" id="especialistaSearch" class="form-control"
                                placeholder="Buscar especialista...">
                            <div id="especialistaResults"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Seleccionar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#smpTable').DataTable({
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
                        "targets": [0, 1, 2, 3, 4, 6, 7, 8, 9, 10]
                    }, // Deshabilitar la ordenación en todas las columnas excepto la columna "Fuente"
                    {
                        "orderable": true,
                        "targets": [5]
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

        setTimeout(function() {
            $('#success-alert').fadeOut('slow');
        }, 2000);


        var especialistas = [];

        $(document).ready(function() {
            loadEspecialistas();
            $('#especialistaSearch').on('input', function() {
                var inputText = $(this).val().toLowerCase();
                var filteredEspecialistas = especialistas.filter(function(especialistas) {
                    return especialistas.name.toLowerCase().includes(inputText);
                });
                displayEspecialistas(filteredEspecialistas);
            });

            $('#especialistaResults').on('click', 'input[type=radio]', function() {
                var id = $(this).data('id');
                $('#especialista_id').val(id);
            });

            $('.btnAsignar').click(function() {
                var id = $(this).data('id');
                $('#hallazgo_id').val(id);
            });

            $('#asignarEspecialistaForm').submit(function(e) {
                e.preventDefault();
                var hallazgoId = $('#hallazgo_id').val();
                var formAction = '{{ route('smp.asignarEspecialista', ':hallazgoId') }}';
                formAction = formAction.replace(':hallazgoId', hallazgoId);
                $(this).attr('action', formAction);
                $(this).unbind('submit').submit()
            });
        });


        function loadEspecialistas() {
            $.ajax({
                url: '/usuarios/especialistas', // Reemplaza por la URL correcta para obtener los procesos
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    especialistas = data;
                    console.log(data);
                    displayEspecialistas(especialistas);
                },
                error: function(error) {
                    console.log('Error al cargar los especialistas:', error);
                }
            });
        }

        function displayEspecialistas(filteredEspecialistas) {
            var $especialistaResults = $('#especialistaResults');
            $especialistaResults.empty();

            filteredEspecialistas.forEach(function(especialista) {
                var html = '<div class="form-check">';
                html += '<input class="form-check-input" type="radio" name="opt_especialista_id" data-id="' +
                    especialista.id +
                    '" data-name="' + especialista.name + '">';
                html += '<label class="form-check-label" for="' + especialista.id + '">' + especialista.id +
                    ' - ' + especialista.nombres + ' ' + especialista.apellido_paterno + ' ' + especialista
                    .apellido_materno + '</label>';
                html += '</div>';
                $especialistaResults.append(html);
            });

        }
    </script>
@endpush
