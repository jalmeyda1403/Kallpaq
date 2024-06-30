@extends('facilitador.layout.master')
@section('title', 'SIG')
@section('css')

    <style>
        .selected {
            background-color: #ECECEC;
            /* Light gray background for selected row */
        }
        .collapse.show {
        transition: height 0.2s ease;
    }
    </style>


@endsection
@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"> <a href="{{ route('smp.index', ['clasificacion' => $breadcrumb['codigo']]) }}">{{$breadcrumb['nombre']}}</a>
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
                        <h3 class="card-title">{{$breadcrumb['nombre']}}</h3>
                        <div class="card-tools">
                            <a href="{{ route('smp.create',['clasificacion' => $breadcrumb['codigo']]) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Crear
                            </a>
                        </div>

                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <!-- Zona de Filtros -->
                        <div class="card border-0 shadow-none">
                            <div class="card-header">
                                <h6 class="card-title">Buscar por:</h6>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                        class="fas fa-plus"></i></button>                                    
                                </div>
                            </div>
                            <div class="card-body collapse" id="collapseFilter">
                                <form method="GET" action="{{route('smp.index',['clasificacion' => $breadcrumb['codigo']]) }}">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <select name="sig" id="sig" class="form-control" placeholder="Seleccione Sistema Gestión">
                                                <option value="">Seleccione Sistema Gestión</option>
                                                @foreach (config('opciones.sig') as $clave => $valor)
                                                    <option value="{{ $clave }}"
                                                        {{ request('sig') == $clave ? 'selected' : '' }}>{{ $valor }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">

                                            <input type="text" name="informe_id" id="informe_id" class="form-control"
                                                value="{{ request('informe_id') }}" placeholder="Ingrese informe">
                                        </div>
                                        <div class="col-md-3">

                                            <select name="year" id="year" class="form-control">
                                                <option value="">Seleccione Año</option>
                                                @for ($i = date('Y'); $i >= 2015; $i--)
                                                    <option value="{{ $i }}"
                                                        {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end">
                                            <button type="submit" class="btn btn-danger">Aplicar Filtros</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <hr>
                        <!-- Zona de Datatable -->
                        @if ($hallazgos->isEmpty())
                            <p>No hay solicitudes de mejora de procesos registrados.</p>
                        @else
                            <table id="smp" class="table  table-bordered table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Proceso</th>
                                        <th style="width:10%">SMP</th>
                                        <th>Resumen</th>
                                        <th>Clasificación</th>
                                        <th>Estado</th>
                                        <th>Especialista Asignado</th>
                                        <th>Fecha Solicitud</th>
                                        <th>Fecha Cierre</th>
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
                                            <td>{{ $hallazgo->proceso->nombre }}</td>
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
                                            <td>{{ $hallazgo->fecha_cierre_acciones }}</td>
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
@stop
@section('js')

    <script>
        setTimeout(function() {
            $('#success-alert').fadeOut('slow');
        }, 2000);

        $('#smp').DataTable();
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
                console.log(id);
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
@endsection
