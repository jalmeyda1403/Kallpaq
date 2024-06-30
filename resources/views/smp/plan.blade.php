@extends('facilitador.layout.master')
@section('title', 'SIG')
@section('css')

    <style>
        .selected {
            background-color: #ECECEC;
            /* Light gray background for selected row */
        }

        .ishikawa-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            position: relative;
        }

        .ishikawa-container::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            border-top: 2px solid #000;
            z-index: -1;
        }

        .ishikawa-cause {
            flex: 1 1 30%;
            padding: 10px;
            box-sizing: border-box;
            position: relative;
        }

        .ishikawa-cause::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 20px;
            border-top: 2px solid #000;
            z-index: -1;
        }

        .ishikawa-cause:first-child::before {
            display: none;
        }

        .ishikawa-result {
            flex: 10%;
            padding: 10px;
            text-align: center;
            position: relative;
        }



        .ishikawa-top,
        .ishikawa-bottom {
            display: flex;
            width: 100%;
            justify-content: space-between;
        }

        .ishikawa-bottom {
            flex-direction: row-reverse;
        }

        .ishikawa-top .ishikawa-cause::after,
        .ishikawa-bottom .ishikawa-cause::after {
            content: '';
            position: absolute;
            top: 50%;
            right: 0;
            width: 0;
            height: 0;
            border-top: 2px solid #000;
            border-right: 2px solid #000;
            transform: rotate(135deg);
            transform-origin: center;
        }

        .ishikawa-bottom .ishikawa-cause::after {
            top: auto;
            bottom: 50%;
            transform: rotate(-135deg);
        }


        .ishikawa-result::before {
            content: '';
            position: absolute;
            top: 50%;
            left: -20px;
            width: 20px;

        }
    </style>



@endsection
@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"> <a href="{{ route('smp.index', ['clasificacion' => $breadcrumb['codigo']]) }}">{{$breadcrumb['nombre']}}</a>
                    <li class="breadcrumb-item active" aria-current="page">{{ $hallazgo->smp_cod }}</li>
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
        <div id="contenidoPDF" class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-right bg-navy">
                        <div class="mr-auto">
                            <h5 class="card-title"> I. Solicitud de Mejora de Procesos</h5>
                        </div>
                        <div class="ml-auto">
                            <h5 class="card-title">{{ $hallazgo->smp_cod }}</h5>
                            <!-- Collapse Button -->
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>

                    </div>


                    <div class="card-body">
                        <!-- Sección 1: Datos del Proceso -->
                        <div class="row">
                            <h5 class="text-left"><b>1.1. Identificación de la Mejora</b></h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Campo para el origen -->
                                <div class="form-group">
                                    <label for="origen">Origen</label>
                                    <select class="form-control" id="origen" name="origen" required disabled>
                                        @foreach (config('opciones.origen') as $clave => $valor)
                                            <option value="{{ $clave }}"
                                                {{ $hallazgo->origen === $clave ? 'selected' : '' }}>
                                                {{ $valor }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Campo para el ID del proceso -->
                                <div class="form-group">
                                    <label for="proceso_id">Proceso</label>
                                    <div class="input-group">
                                        <input type="hidden" name="proceso_id" id="proceso_id"
                                            value="{{ $hallazgo->proceso_id }}" required disabled>
                                        <input type="text" class="form-control" id="proceso_nombre" name="proceso_nombre"
                                            value="{{ $hallazgo->proceso->nombre }}" required readonly>
                                        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#"><i
                                                class="fas fa-search"></i></a>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <!-- Campo para la clasificación -->
                                <div class="form-group">
                                    <label for="clasificacion">Clasificación </label>
                                    <select class="form-control" id="clasificacion" name="clasificacion" required disabled>
                                        @foreach (config('opciones.clasificacion') as $clave => $valor)
                                            <option value="{{ $clave }}"
                                                {{ $hallazgo->clasificacion === $clave ? 'selected' : '' }}>
                                                {{ $valor }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Campo para Sistema de Gestión Impactado -->
                                <div class="form-group">
                                    <label for="sig">Sistema de Gestión Impactado</label>
                                    <select class="form-control" id="sig" name="sig" required disabled>
                                        @foreach (config('opciones.sig') as $clave => $valor)
                                            <option value="{{ $clave }}"
                                                {{ $hallazgo->sig === $clave ? 'selected' : '' }}>
                                                {{ $valor }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>



                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Campo para el ID del informe -->
                                <div class="form-group">
                                    <label for="auditor">Auditor o colaborador que identificó la mejora</label>
                                    <input type="text" class="form-control" id="auditor" name="auditor"
                                        value="{{ $hallazgo->auditor }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Campo para el ID del informe -->
                                <div class="form-group">
                                    <label for="informe_id">Informe</label>
                                    <textarea class="form-control" id="informe_id" name="informe_id" rows="1" readonly>{{ $hallazgo->informe_id }}</textarea>
                                </div>

                            </div>
                        </div>
                        <hr>
                        <!-- Sección 2: Detalle de la Mejora -->
                        <div class="row">
                            <h5 class="text-left"><b>1.2. Detalle de la Mejora</b></h5>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="descripcion">Resumen SMP (Breve y concisa)</label>
                                    <textarea class="form-control" id="resumen" name="resumen" rows="3" readonly>{{ $hallazgo->resumen }}</textarea>
                                </div>
                            </div>
                        </div>
                 

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Campo para la descripción -->
                                <div class="form-group">
                                    <label for="descripcion">Descripción /Problemática</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="10" readonly>{{ $hallazgo->descripcion }}</textarea>
                                </div>
                                <!-- Campo para el criterio -->
                                <div class="form-group">
                                    <label for="criterio">Criterio (Referencia)</label>
                                    <textarea class="form-control" id="criterio" name="criterio" rows="3" readonly>{{ $hallazgo->criterio }}</textarea>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <!-- Campo para la evidencia -->
                                <div class="form-group">
                                    <label for="evidencia">Evidencia</label>
                                    <textarea class="form-control" id="evidencia" name="evidencia" rows="10" readonly>{{ $hallazgo->evidencia }}</textarea>
                                </div>
                                <!-- Campo para la fecha de solicitud -->
                                <div class="form-group">
                                    <label for="fecha_solicitud">Fecha de la solicitud</label>
                                    <input type="date" class="form-control" id="fecha_solicitud"
                                        name="fecha_solicitud" value="{{ $hallazgo->fecha_solicitud }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-right bg-navy">
                        <div class="mr-auto">
                            <h5 class="card-title"> II. Acciones Correctivas</h5>
                        </div>
                        <div class="ml-auto">

                            <!-- Collapse Button -->
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <a href="{{ route('acciones.create') }}" class="btn btn-primary new-accion" data-toggle="modal"
                            data-target="#addActionModal" data-correctiva="1">
                            <i class="fas fa-plus"></i> Agregar Acción
                        </a>
                        <p></p>
                        @if ($correctivas > 0)

                            <table class="table table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 40%">Acción</th>
                                        <th>Fecha de Inicio</th>
                                        <th>Fecha de Fin</th>
                                        <th>Responsable</th>
                                        <th>Estado</th>
                                        <th class="col-1">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($planesAccion as $plan)
                                        <tr>
                                            @if ($plan->es_correctiva)
                                                <td>{{ $plan->accion }}</td>
                                                <td>{{ $plan->fecha_inicio }}</td>
                                                <td>{{ $plan->fecha_fin }}</td>
                                                <td>{{ $plan->responsable_id }}</td>
                                                <td>{{ $plan->estado }}</td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="Acciones">
                                                        <a href="#" class="btn btn-sm btn-primary edit-accion"
                                                            data-accion_id="{{ $plan->id }}" data-correctiva="1">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        <form action="{{ route('acciones.destroy', $plan->id) }}"
                                                            method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                onclick="return confirm('¿Estás seguro de que quieres eliminar este plan de acción?')"><i
                                                                    class="fas fa-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-left">
                                "No se ha registrado Acciones"
                            </div>
                        @endif

                    </div>
                </div>
            </div>
            @if ($hallazgo->clasificacion === 'NCM' || $hallazgo->clasificacion === 'Ncme')

                <div class="col-md-10">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-right bg-navy">
                            <div class="mr-auto">
                                <h5 class="card-title"> III. Identificación de la causa raíz y plan de acción</h5>
                            </div>
                            <div class="ml-auto">

                                <!-- Collapse Button -->
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                        class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <h5><b>3.1 Analisis de la Causa Raíz</b></h5>
                            @if (!$hallazgo->causa)
                                <a href="{{ route('analisis.create', ['hallazgo_id' => $hallazgo->id]) }}"
                                    class="btn btn-primary" data-toggle="modal" data-target="#addCausaModal">
                                    <i class="fas fa-plus"></i> Agregar Causa Raíz
                                </a>
                            @endif
                            <p></p>
                            @if ($hallazgo->causa)
                                <table class="table table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Análisis</th>
                                            <th class="col-1">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <tr>
                                            <td>{{ $hallazgo->causa->resultado }}</td>
                                            <td>
                                                <div class="btn-group" role="group" aria-label="Acciones">
                                                    <a href="#" class="btn btn-sm btn-primary edit-causa"
                                                        data-causa_id="{{ $hallazgo->causa->id }}"
                                                        data-hallazgo_id="{{ $hallazgo->id }}">
                                                        <i class="fas fa-pencil-alt"></i></a>
                                                    <form
                                                        action="{{ route('analisis.destroy', ['hallazgo_id' => $hallazgo->id, 'id' => $hallazgo->causa->id]) }}"
                                                        method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('¿Estás seguro de que quieres eliminar el análisis de causas?')"><i
                                                                class="fas fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>


                                </table>
                            @else
                                <div class="text-left">
                                    "No se ha registrado Causas"
                                </div>
                            @endif
                            <p></p>
                            <h5><b>3.2 Plan de Acción</b></h5>
                            <a href="{{ route('acciones.create') }}" class="btn btn-primary new-accion"
                                data-toggle="modal" data-target="#addActionModal" data-correctiva="0">
                                <i class="fas fa-plus"></i> Agregar Acción
                            </a>
                            </a>
                            @if ($preventivas > 0)
                                <table class="table table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 40%">Acción</th>
                                            <th>Fecha de Inicio</th>
                                            <th>Fecha de Fin</th>
                                            <th>Responsable</th>
                                            <th>Estado</th>
                                            <th class="col-1">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($planesAccion as $plan)
                                            @if (!$plan->es_correctiva)
                                                <tr>
                                                    <td>{{ $plan->accion }}</td>
                                                    <td>{{ $plan->fecha_inicio }}</td>
                                                    <td>{{ $plan->fecha_fin }}</td>
                                                    <td>{{ $plan->responsable_id }}</td>
                                                    <td>{{ $plan->estado }}</td>
                                                    <td>
                                                        <div class="btn-group" role="group" aria-label="Acciones">
                                                            <a href="#" class="btn btn-sm btn-primary edit-accion"
                                                                data-accion_id="{{ $plan->id }}" data-correctiva="0">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            <form action="{{ route('acciones.destroy', $plan->id) }}"
                                                                method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('¿Estás seguro de que quieres eliminar este plan de acción?')"><i
                                                                        class="fas fa-trash"></i></button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-left">
                                    "No se ha registrado Acciones"
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            @endif

            <div class="col-md-10">
                <div class="card">
                    <div class="card-footer">
                        @if ($hallazgo->estado === 'Abierto')
                            <form action="{{ route('smp.aprobar', $hallazgo->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">Aprobar</button>
                            </form>
                        @endif

                        @if ($hallazgo->estado == 'Aprobado')
                            <form id= "frmImprimir" action="{{ route('smp.imprimir', ['id' => $hallazgo->id]) }}"
                                method="POST">
                                @csrf
                                <input type="hidden" name="html" id="html" value="">
                                <button type="button" id="btn-imprimir" class="btn btn-primary">Imprimir</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>


        </div>

        <!-- Modal para agregar acción -->
        <div class="modal fade" id="addActionModal" tabindex="-1" role="dialog" aria-labelledby="addActionModalLabel"
            data-backdrop="static" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h5 class="modal-title" id="addActionModalLabel">Agregar Acción</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="addActionForm" method="POST" action="{{ route('acciones.store') }}">
                        @csrf
                        <input type="hidden" name="_method" value="POST">
                        <div class="modal-body">
                            <input type="hidden" name="hallazgo_id" value="{{ $hallazgo->id }}">
                            <div class="form-group">
                                <label for="accion">Acción</label>
                                <textarea class="form-control" id="accion" name="accion" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="fecha_inicio">Fecha de Inicio</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_fin">Fecha de Fin</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                            </div>

                            <div class="form-group">
                                <label for="comentario">Comentario</label>
                                <textarea class="form-control" id="comentario" name="comentario" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <input type="hidden" name="estado" value="Programada">
                                <select class="form-control" id="estado" name="estado" required disabled>
                                    @foreach (config('opciones.estado_acciones') as $clave => $valor)
                                        <option value="{{ $clave }}">
                                            {{ $valor }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="es_correctiva">Es Correctiva</label>
                                <input type="hidden" name="es_correctiva" id="es_correctiva">
                                <select class="form-control" id="select_correctiva" name="select_correctiva" required
                                    disabled>
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="responsable_id">Responsable</label>
                                <input type="text" class="form-control" id="responsable_id" name="responsable_id"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="responsable_correo">Email Responsable</label>
                                <input type="email" class="form-control" id="responsable_correo"
                                    name="responsable_correo" required>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Acción</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para agregar causa -->
        <div class="modal fade" id="addCausaModal" tabindex="-1" role="dialog" aria-labelledby="addCausaModalLabel"
            data-backdrop="static" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-navy">
                        <h5 class="modal-title" id="addCausanModalLabel">Analisis de Causas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('analisis.store', $hallazgo->id) }}">
                        @csrf
                        <input type="hidden" name="_method" value="POST">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required readonly>{{ $hallazgo->descripcion }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="metodo">Método</label>
                                <select class="form-control" id="metodo" name="metodo" required>
                                    <option value="ishikawa">Diagrama de Ishikawa</option>
                                    <option value="cinco_porques">Los Cinco Porqués</option>
                                </select>
                            </div>
                            <!-- Contenido dinámico según el método seleccionado -->
                            <div id="metodo_ishikawa" class="metodo-content">
                                <div class="ishikawa-container">
                                    <!-- Causas arriba -->
                                    <div class="ishikawa-top">
                                        <div class="ishikawa-cause">
                                            <div class="form-group">
                                                <label for="mano_obra"><i class="fas fa-users"></i> Mano de
                                                    obra</label>
                                                <textarea class="form-control" id="mano_obra" name="mano_obra" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="ishikawa-cause">
                                            <div class="form-group">
                                                <label for="metodologias"><i class="fas fa-project-diagram"></i>
                                                    Métodologías</label>
                                                <textarea class="form-control" id="metodologias" name="metodologias" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="ishikawa-cause">
                                            <div class="form-group">
                                                <label for="materiales"><i class="fas fa-box-open"></i>
                                                    Materiales</label>
                                                <textarea class="form-control" id="materiales" name="materiales" rows="3"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Causas abajo -->
                                    <div class="ishikawa-bottom">
                                        <div class="ishikawa-cause">
                                            <div class="form-group">
                                                <label for="maquinas"><i class="fas fa-industry"></i>
                                                    Máquinas</label>
                                                <textarea class="form-control" id="maquinas" name="maquinas" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="ishikawa-cause">
                                            <div class="form-group">
                                                <label for="medicion"><i class="fas fa-tachometer-alt"></i>
                                                    Medición</label>
                                                <textarea class="form-control" id="medicion" name="medicion" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="ishikawa-cause">
                                            <div class="form-group">
                                                <label for="medio_ambiente"><i class="fas fa-leaf"></i> Medio
                                                    ambiente</label>
                                                <textarea class="form-control" id="medio_ambiente" name="medio_ambiente" rows="3"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Contenido dinámico según el método Cinco Porques -->
                            <div id="metodo_cinco_porques" class="metodo-content d-none">
                                <!-- Aquí puedes agregar el contenido específico para los Cinco Porqués -->
                                <div class="form-group">
                                    <label for="por_que_1">¿Por qué 1?</label>
                                    <textarea class="form-control" id="por_que_1" name="por_que_1" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="por_que_2">¿Por qué 2?</label>
                                    <textarea class="form-control" id="por_que_2" name="por_que_2" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="por_que_3">¿Por qué 3?</label>
                                    <textarea class="form-control" id="por_que_3" name="por_que_3" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="por_que_4">¿Por qué 4?</label>
                                    <textarea class="form-control" id="por_que_4" name="por_que_4" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="por_que_5">¿Por qué 5?</label>
                                    <textarea class="form-control" id="por_que_5" name="por_que_5" rows="3"></textarea>
                                </div>

                            </div>
                            <div class="form-group">
                                <label for="resultado">Resultado</label>
                                <textarea class="form-control" id="resultado" name="resultado" rows="3" required></textarea>
                            </div>

                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Acción</button>
                        </div>
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


        document.addEventListener('DOMContentLoaded', function() {
            var metodoSelect = document.getElementById('metodo');
            var metodoIshikawa = document.getElementById('metodo_ishikawa');
            var metodoCincoPorques = document.getElementById('metodo_cinco_porques');

            metodoSelect.addEventListener('change', function() {
                if (this.value === 'ishikawa') {
                    metodoIshikawa.classList.remove('d-none');
                    metodoCincoPorques.classList.add('d-none');
                } else if (this.value === 'cinco_porques') {
                    metodoIshikawa.classList.add('d-none');
                    metodoCincoPorques.classList.remove('d-none');
                }
            });
        });

        $('.edit-causa').click(function() {
            var causaId = $(this).data('causa_id');
            var hallazgoId = $(this).data('hallazgo_id');
            var metodoIshikawa = document.getElementById('metodo_ishikawa');
            var metodoCincoPorques = document.getElementById('metodo_cinco_porques');
            var updateUrlTemplate =
                "{{ route('analisis.update', ['hallazgo_id' => ':hallazgo_id', 'id' => ':id']) }}"
            var updateAction = updateUrlTemplate
                .replace(':hallazgo_id', hallazgoId)
                .replace(':id', causaId);

            $.ajax({
                url: '/hallazgos/' + hallazgoId + '/analisis/' + causaId +
                    '/edit', // Asegúrate de que esta URL es correcta
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var modal = $('#addCausaModal');

                    // Rellenar todos los campos en el modal basados en los datos recibidos
                    modal.find('#metodo').val(data.metodo);
                    modal.find('#mano_obra').val(data.mano_obra);
                    modal.find('#metodologias').val(data.metodologias);
                    modal.find('#materiales').val(data.materiales);
                    modal.find('#maquinas').val(data.maquinas);
                    modal.find('#medicion').val(data.medicion);
                    modal.find('#medio_ambiente').val(data.medio_ambiente);
                    modal.find('#por_que_1').val(data.por_que_1);
                    modal.find('#por_que_2').val(data.por_que_2);
                    modal.find('#por_que_3').val(data.por_que_3);
                    modal.find('#por_que_4').val(data.por_que_4);
                    modal.find('#por_que_5').val(data.por_que_5);
                    modal.find('#resultado').val(data.resultado);

                    // Cambiar la acción del formulario para actualizar

                    modal.find('form').attr('action', updateAction);
                    modal.find('input[name="_method"]').val(
                        'PUT'); // Asegurar que el método PUT está configurado

                    // Mostrar el div correspondiente al método seleccionado
                    if (data.metodo === 'ishikawa') {
                        metodoIshikawa.classList.remove('d-none');
                        metodoCincoPorques.classList.add('d-none');
                    } else {
                        metodoIshikawa.classList.add('d-none');
                        metodoCincoPorques.classList.remove('d-none');
                    }

                    // Mostrar el modal
                    modal.modal('show');
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar datos: " + error);
                    alert('Error al cargar información de la causa');
                }
            });
        });
        $('.edit-accion').click(function() {
            var accionId = $(this).data('accion_id');
            var esCorrectiva = $(this).data('correctiva');
            // Reemplaza los marcadores de posición en la URL de plantilla
            var updateAction = "{{ route('acciones.update', ':id') }}";
            updateAction = updateAction.replace(':id', accionId);

            $.ajax({
                url: '/acciones/' + accionId + '/edit',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var modal = $('#addActionModal');

                    // Aquí deberías actualizar los campos del formulario basándote en `data`
                    modal.find('#accion').val(data.accion);
                    modal.find('#fecha_inicio').val(data.fecha_inicio);
                    modal.find('#fecha_fin').val(data.fecha_fin);
                    modal.find('#comentario').val(data.comentario);
                    modal.find('#estado').val(data.estado);
                    modal.find('#responsable_id').val(data.responsable_id);
                    modal.find('#responsable_correo').val(data.responsable_correo);
                    modal.find('#es_correctiva').val(esCorrectiva);
                    modal.find('#select_correctiva').val(esCorrectiva);

                    // Configura la acción del formulario para el método de actualización
                    modal.find('form').attr('action', updateAction);
                    modal.find('input[name="_method"]').val('PUT'); // Cambia el método a PUT

                    // Mostrar el modal
                    modal.modal('show');
                },
                error: function(xhr, status, error) {
                    console.error("Error al cargar datos: " + error);
                    alert('Error al cargar información de la acción');
                }
            });
        });

        $('.new-accion').click(function() {
            var esCorrectiva = $(this).data('correctiva');
            var modal = $('#addActionModal');
            modal.find('#es_correctiva').val(esCorrectiva);
            modal.find('#select_correctiva').val(esCorrectiva);
        });

        var contenidoPDF = document.getElementById('contenidoPDF');

        $('#btn-imprimir').click(function() {
            var htmlPersonalizado = contenidoPDF.innerHTML;
            var form = document.getElementById('frmImprimir');
            var htmlInput = $('#html');
            // Encrypt the HTML content using CryptoJS


            htmlInput.val(htmlPersonalizado); // Set the value of the input field
            form.setAttribute('target', '_blank'); // Abre el formulario en una nueva pestaña
            form.submit(); // Envía el formulario

        });
    </script>
@endsection
