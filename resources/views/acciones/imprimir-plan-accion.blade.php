<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Plan de Acción - {{ $hallazgo->hallazgo_cod }}</title>

    <!-- Importar estilos para dompdf -->
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
        }

        .encabezado {
            border-bottom: 2px solid #dc3545;
            padding-bottom: 10px;
        }

        .titulo-documento {
            color: #dc3545;
            font-weight: bold;
        }

        .linea-separadora {
            border-top: 2px solid #dc3545;
            margin: 10px 0;
        }

        .subtitulo {
            background-color: #f8f9fa;
            padding: 8px 12px;
            border-left: 4px solid #dc3545;
            margin: 0 0 10px 0;
            font-weight: bold;
            color: #495057;
        }

        .proceso-tag {
            background-color: #e9ecef;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            margin-right: 5px;
        }

        .causa-detalle {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
        }

        .table {
            font-size: 11px;
        }

        .table th,
        .table td {
            padding: 6px;
            vertical-align: middle;
        }

        .table th {
            background-color: #e9ecef;
            color: #495057;
            font-weight: bold;
        }

        .badge {
            font-size: 10px;
            padding: 4px 8px;
        }

        .estadistica-card {
            border: 1px solid #ced4da;
            border-radius: 8px;
            text-align: center;
        }

        .display-4 {
            font-size: 2.5rem;
            font-weight: 300;
            line-height: 1.2;
            margin: 0;
            color: #dc3545;
        }

        .firma-area {
            border-top: 1px solid #000;
            padding-top: 20px;
            margin-top: 40px;
        }

        .logo {
            max-width: 80px;
            height: auto;
        }

        /* Estilos específicos para dompdf */
        .container-fluid {
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        .col-3, .col-6 {
            position: relative;
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
        }

        .col-3 {
            flex: 0 0 25%;
            max-width: 25%;
        }

        .col-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }

        .mb-4 {
            margin-bottom: 1.5rem !important;
        }

        .mb-3 {
            margin-bottom: 1rem !important;
        }

        .mb-2 {
            margin-bottom: 0.5rem !important;
        }

        .mb-1 {
            margin-bottom: 0.25rem !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .mt-5 {
            margin-top: 3rem !important;
        }

        .mt-4 {
            margin-top: 1.5rem !important;
        }

        .mt-2 {
            margin-top: 0.5rem !important;
        }

        .pt-4 {
            padding-top: 1.5rem !important;
        }

        .p-2 {
            padding: 0.5rem !important;
        }

        .p-3 {
            padding: 1rem !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .font-weight-bold {
            font-weight: bold !important;
        }

        .bg-secondary {
            background-color: #6c757d !important;
        }

        .bg-danger {
            background-color: #dc3545 !important;
        }

        .bg-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        .bg-primary {
            background-color: #007bff !important;
        }

        .bg-info {
            background-color: #17a2b8 !important;
        }

        .bg-success {
            background-color: #28a745 !important;
        }

        .bg-light {
            background-color: #f8f9fa !important;
            color: #212529 !important;
        }

        .table-bordered {
            border: 1px solid #dee2e6;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }

        .table-light th {
            background-color: #e9ecef;
            color: #495057;
        }

        .border {
            border: 1px solid #dee2e6 !important;
        }

        .rounded {
            border-radius: 0.25rem !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Encabezado del documento -->
        <div class="encabezado mb-4">
            <div class="row">
                <div class="col-3 text-center">
                    @if(file_exists(public_path('/vendor/adminlte/dist/img/kallpaq_ico.png')))
                        <img src="{{ public_path('/vendor/adminlte/dist/img/kallpaq_ico.png') }}" alt="Logo" class="logo">
                    @endif
                </div>
                <div class="col-6 text-center">
                    <h3 class="titulo-documento mb-1">PLAN DE ACCIÓN</h3>
                    <h5 class="titulo-documento">HALLAZGO: {{ $hallazgo->hallazgo_cod }}</h5>
                </div>
                <div class="col-3 text-right">
                    <div class="codigo-version">
                        <p class="mb-1"><strong>Fecha:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
            <hr class="linea-separadora">
        </div>

        <!-- Información del hallazgo -->
        <div class="info-hallazgo mb-4">
            <div class="datos-generales mb-3">
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-1"><strong>Resumen:</strong> {{ $hallazgo->hallazgo_resumen }}</p>
                        <p class="mb-1"><strong>Descripción:</strong> {{ $hallazgo->hallazgo_descripcion }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1"><strong>Clasificación:</strong>
                            <span class="badge bg-secondary">{{ $hallazgo->hallazgo_clasificacion }}</span>
                        </p>
                        <p class="mb-1"><strong>Estado:</strong> {{ $hallazgo->hallazgo_estado }}</p>
                        <p class="mb-1"><strong>Fecha Identificación:</strong> {{ \Carbon\Carbon::parse($hallazgo->hallazgo_fecha_identificacion)->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="procesos-afectados mb-3">
                <h5 class="subtitulo mb-2">Procesos Afectados:</h5>
                <div class="procesos-lista">
                    @if($hallazgo->procesos && $hallazgo->procesos->count() > 0)
                        @foreach($hallazgo->procesos as $index => $proceso)
                            <span class="proceso-tag">
                                {{ $proceso->proceso_nombre }}@if($index < $hallazgo->procesos->count() - 1){{','}}@endif
                            </span>
                        @endforeach
                    @else
                        <span>No hay procesos afectados</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Análisis de causa raíz -->
        @if($causaRaiz && $causaRaiz->causa_resultado)
        <div class="causa-raiz mb-4">
            <h5 class="subtitulo mb-2">Análisis de Causa Raíz:</h5>
            <div class="causa-detalle p-3 border rounded">
                <p class="mb-1"><strong>Método aplicado:</strong>
                    {{ $causaRaiz->causa_metodo === 'cinco_porques' ? '5 Porqués' : ($causaRaiz->causa_metodo === 'ishikawa' ? 'Ishikawa (6M)' : $causaRaiz->causa_metodo) }}
                </p>
                <p class="mb-0"><strong>Causa Raíz:</strong> {{ $causaRaiz->causa_resultado }}</p>
            </div>
        </div>
        @endif

        <!-- Tabla de acciones -->
        <div class="acciones-listado mb-4">
            <h5 class="subtitulo mb-3">Acciones del Plan</h5>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">Código</th>
                        <th style="width: 10%;">Tipo</th>
                        <th style="width: 25%;">Descripción</th>
                        <th style="width: 15%;">Responsable</th>
                        <th style="width: 10%;">Inicio</th>
                        <th style="width: 10%;">Fin Planif.</th>
                        <th style="width: 10%;">Estado</th>
                        <th style="width: 10%;">Ciclo</th>
                    </tr>
                </thead>
                <tbody>
                    @if($acciones && $acciones->count() > 0)
                        @foreach($acciones as $accion)
                        <tr>
                            <td>{{ $accion->accion_cod }}</td>
                            <td>
                                @if($accion->accion_tipo)
                                    <span class="badge
                                        @if($accion->accion_tipo === 'inmediata') bg-danger
                                        @elseif($accion->accion_tipo === 'correctiva') bg-warning text-dark
                                        @else bg-secondary @endif">
                                        @if($accion->accion_tipo === 'inmediata') Inmediata
                                        @elseif($accion->accion_tipo === 'correctiva') Correctiva
                                        @else {{ $accion->accion_tipo }} @endif
                                    </span>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                            <td>{{ $accion->accion_descripcion }}</td>
                            <td>{{ $accion->accion_responsable }}</td>
                            <td>{{ $accion->accion_fecha_inicio ? \Carbon\Carbon::parse($accion->accion_fecha_inicio)->format('d/m/Y') : 'N/A' }}</td>
                            <td class="@if($accion->accion_fecha_fin_planificada && \Carbon\Carbon::parse($accion->accion_fecha_fin_planificada) < \Carbon\Carbon::now()) text-danger font-weight-bold @endif">
                                {{ $accion->accion_fecha_fin_planificada ? \Carbon\Carbon::parse($accion->accion_fecha_fin_planificada)->format('d/m/Y') : 'N/A' }}
                                @if($accion->accion_fecha_fin_planificada && \Carbon\Carbon::parse($accion->accion_fecha_fin_planificada) < \Carbon\Carbon::now())
                                    <i class="fas fa-exclamation-triangle ml-1 text-danger" title="Fecha vencida"></i>
                                @endif
                            </td>
                            <td>
                                <span class="badge
                                    @if($accion->accion_estado === 'programada') bg-primary
                                    @elseif($accion->accion_estado === 'en ejecucion') bg-info
                                    @elseif($accion->accion_estado === 'finalizada') bg-success
                                    @elseif($accion->accion_estado === 'desestimada') bg-secondary
                                    @elseif($accion->accion_estado === 'reprogramada') bg-warning text-dark
                                    @else bg-light text-dark @endif">
                                    {{ $accion->accion_estado }}
                                </span>
                            </td>
                            <td>{{ $accion->accion_ciclo }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center">No hay acciones registradas</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Estadísticas -->
        @if($acciones && $acciones->count() > 0)
        <div class="estadisticas mb-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="card estadistica-card text-center">
                        <div class="card-body p-2">
                            <h6 class="card-title">Total Acciones</h6>
                            <p class="card-text display-4">{{ $acciones->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card estadistica-card text-center">
                        <div class="card-body p-2">
                            <h6 class="card-title">Acciones Finalizadas</h6>
                            <p class="card-text display-4">{{ $acciones->where('accion_estado', 'finalizada')->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card estadistica-card text-center">
                        <div class="card-body p-2">
                            <h6 class="card-title">% de Avance</h6>
                            <p class="card-text display-4">{{ $acciones->count() > 0 ? round(($acciones->where('accion_estado', 'finalizada')->count() / $acciones->count()) * 100) : 0 }}%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Espacio para firma -->
        <div class="firma-section mt-5 pt-4">
            <div class="row">
                <div class="col-6 text-center">
                    <div class="firma-area">
                        <p class="mb-4">_________________________</p>
                        <p class="mb-0"><strong>{{ $hallazgo->especialista->name ?? 'Responsable' }}</strong></p>
                        <p class="mb-0">Responsable del Hallazgo</p>
                    </div>
                </div>
                <div class="col-6 text-center">
                    <div class="firma-area">
                        <p class="mb-4">_________________________</p>
                        <p class="mb-0"><strong>{{ $hallazgo->auditor->name ?? 'Auditor' }}</strong></p>
                        <p class="mb-0">Auditor</p>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <p class="mb-0"><small>Documento generado electrónicamente el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</small></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>