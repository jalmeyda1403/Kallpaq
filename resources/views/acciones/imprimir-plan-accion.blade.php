<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plan de Acción - {{ $hallazgo->hallazgo_cod }}</title>
    <!-- AdminLTE and FontAwesome Styles -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <style>
        .invoice {
            border: none;
            padding: 20px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .invoice {
                padding: 0;
                border: none;
            }

            .card {
                box-shadow: none !important;
                border: none !important;
            }

            body {
                background-color: white !important;
            }
        }

        .text-danger-custom {
            color: #dc3545 !important;
        }

        .bg-light-danger {
            background-color: #fce8e8;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(220, 53, 69, 0.05);
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body invoice p-5 bg-white">
                <!-- Header -->
                <div class="row mb-5 pb-3 border-bottom position-relative">
                    <div class="position-absolute" style="right: 15px; top: 0;">
                        <span class="text-muted small font-weight-bold">Versión:
                            {{ str_pad($hallazgo->hallazgo_ciclo ?? 1, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="col-12 text-center">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Institucional" style="max-height: 80px;"
                            class="mb-3">
                        <h5 class="font-weight-bold text-uppercase text-dark mb-1" style="letter-spacing: 1px;">Sistema
                            Integrado de Gestión</h5>
                        <h3 class="font-weight-bold text-danger-custom mb-2">PLAN DE ACCIÓN SMP</h3>
                        <h5 class="text-muted font-weight-bold">{{ $hallazgo->hallazgo_cod }}</h5>
                    </div>
                </div>

                <!-- Hallazgo Info -->
                <div class="row invoice-info mb-4">
                    <div class="col-sm-4 invoice-col">
                        <strong class="text-secondary text-uppercase small">Datos Generales</strong>
                        <address>
                            <strong>Estado: </strong><span
                                class="badge badge-light border">{{ $hallazgo->hallazgo_estado }}</span><br>
                            <strong>Identificado: </strong>
                            {{ \Carbon\Carbon::parse($hallazgo->hallazgo_fecha_identificacion)->format('d/m/Y') }}<br>
                            <strong>Fuente: </strong> {{ $hallazgo->hallazgo_origen }}
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <strong class="text-secondary text-uppercase small">Procesos Afectados</strong>
                        <address>
                            @foreach ($hallazgo->procesos as $proceso)
                                <div class="mb-1"><i class="fas fa-check-circle text-danger-custom mr-1"
                                        style="font-size: 0.8em"></i> {{ $proceso->proceso_nombre }}</div>
                            @endforeach
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <strong class="text-secondary text-uppercase small">Responsables</strong>
                        <address>
                            <div class="mb-1"><strong>Especialista:</strong>
                                {{ optional($hallazgo->especialista)->name ?? 'No asignado' }}</div>
                            <div class="mb-1"><strong>Auditor:</strong>
                                {{ optional($hallazgo->auditor)->name ?? 'No asignado' }}</div>
                        </address>
                    </div>
                </div>

                <!-- Description -->
                <div class="row mb-5">
                    <div class="col-12">
                        <h5 class="text-danger-custom font-weight-bold mb-3">Descripción del Hallazgo</h5>
                        <div class="p-3 bg-light rounded text-muted text-justify">
                            {{ $hallazgo->hallazgo_descripcion ?? $hallazgo->hallazgo_resumen }}
                        </div>
                    </div>
                </div>

                <!-- Criteria & Evidence -->
                <div class="row mb-5">
                    <div class="col-6">
                        <h6 class="text-danger-custom font-weight-bold mb-2">Criterio / Referencia</h6>
                        <div class="p-3 bg-light rounded text-muted text-justify small" style="min-height: 80px;">
                            {{ $hallazgo->hallazgo_criterio ?? 'No registrado' }}
                        </div>
                    </div>
                    <div class="col-6">
                        <h6 class="text-danger-custom font-weight-bold mb-2">Evidencia Objetiva</h6>
                        <div class="p-3 bg-light rounded text-muted text-justify small" style="min-height: 80px;">
                            {{ $hallazgo->hallazgo_evidencia ?? 'No registrada' }}
                        </div>
                    </div>
                </div>

                <!-- Causa Raíz -->
                @if ($causaRaiz)
                    <div class="row mb-5">
                        <div class="col-12">
                            <h5 class="text-danger-custom font-weight-bold mb-3">1. Análisis de Causa Raíz</h5>
                            <div class="bg-light-danger p-4 rounded border-left border-danger"
                                style="border-left-width: 5px !important;">
                                <div class="mb-3">
                                    <strong class="text-danger text-uppercase small">Metodología Aplicada: </strong>
                                    <span
                                        class="badge badge-white border text-danger ml-2 px-3 py-1">{{ $causaRaiz->hc_metodo == 'cinco_porques' ? '5 Porqués' : 'Ishikawa (6M)' }}</span>
                                </div>

                                <!-- Detailed Method Fields -->
                                <div class="mb-4 pt-3 border-top border-danger"
                                    style="border-top-style: dashed !important; border-width: 1px !important; border-color: rgba(220, 53, 69, 0.3) !important;">
                                    @if ($causaRaiz->hc_metodo == 'cinco_porques')
                                        <h6 class="font-weight-bold text-danger mb-3 pl-2 border-left border-danger">
                                            Desarrollo de los 5 Porqués:</h6>
                                        <ul class="list-unstyled mb-0">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @php $campo = 'hc_por_que'.$i; @endphp
                                                @if (!empty($causaRaiz->$campo))
                                                    <li class="mb-2 d-flex">
                                                        <span class="badge badge-danger mr-2"
                                                            style="height: fit-content; margin-top: 3px;">{{ $i }}°</span>
                                                        <span
                                                            class="text-dark font-italic">"{{ $causaRaiz->$campo }}"</span>
                                                    </li>
                                                @endif
                                            @endfor
                                        </ul>
                                    @else
                                        <h6 class="font-weight-bold text-danger mb-3 pl-2 border-left border-danger">
                                            Análisis de Causas (6M):</h6>
                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <small class="text-muted text-uppercase font-weight-bold">Mano de
                                                    Obra</small>
                                                <div class="bg-white p-2 rounded border border-light">
                                                    {{ $causaRaiz->hc_mano_obra ?? 'N/A' }}</div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <small
                                                    class="text-muted text-uppercase font-weight-bold">Métodos</small>
                                                <div class="bg-white p-2 rounded border border-light">
                                                    {{ $causaRaiz->hc_metodologias ?? 'N/A' }}</div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <small
                                                    class="text-muted text-uppercase font-weight-bold">Materiales</small>
                                                <div class="bg-white p-2 rounded border border-light">
                                                    {{ $causaRaiz->hc_materiales ?? 'N/A' }}</div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <small
                                                    class="text-muted text-uppercase font-weight-bold">Maquinaria</small>
                                                <div class="bg-white p-2 rounded border border-light">
                                                    {{ $causaRaiz->hc_maquinas ?? 'N/A' }}</div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <small
                                                    class="text-muted text-uppercase font-weight-bold">Medición</small>
                                                <div class="bg-white p-2 rounded border border-light">
                                                    {{ $causaRaiz->hc_medicion ?? 'N/A' }}</div>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <small class="text-muted text-uppercase font-weight-bold">Medio
                                                    Ambiente</small>
                                                <div class="bg-white p-2 rounded border border-light">
                                                    {{ $causaRaiz->hc_medio_ambiente ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <strong class="text-danger d-block mb-2 text-uppercase small">Causa Raíz
                                        Identificada:</strong>
                                    <p class="mb-0 text-dark font-weight-normal" style="font-size: 1.1em;">
                                        {{ $causaRaiz->hc_resultado }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Acciones -->
                <div class="row">
                    <div class="col-12 table-responsive">
                        <h5 class="text-danger-custom font-weight-bold mb-3">2. Acciones Definidas</h5>
                        <table class="table table-hover">
                            <thead class="bg-light text-secondary">
                                <tr>
                                    <th style="width: 15%">Código</th>
                                    <th style="width: 40%">Acción / Descripción</th>
                                    <th style="width: 25%">Responsable</th>
                                    <th style="width: 20%">Planificación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($acciones as $accion)
                                    <tr>
                                        <td class="align-middle font-weight-bold">{{ $accion->accion_cod }}</td>
                                        <td class="align-middle">
                                            <span
                                                class="badge {{ $accion->accion_tipo == 'correctiva' ? 'badge-danger' : 'badge-warning' }} mb-1">{{ ucfirst($accion->accion_tipo) }}</span>
                                            <div class="small">{{ $accion->accion_descripcion }}</div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="font-weight-bold">
                                                {{ optional($accion->responsable)->name ?? $accion->accion_responsable }}
                                            </div>
                                            @if (isset($accion->responsable->ouos) && $accion->responsable->ouos->count() > 0)
                                                <small
                                                    class="text-muted">{{ $accion->responsable->ouos->first()->ouo_nombre }}</small>
                                            @endif
                                        </td>
                                        <td class="align-middle small">
                                            <div><strong>Inicio:</strong>
                                                {{ \Carbon\Carbon::parse($accion->accion_fecha_inicio)->format('d/m/Y') }}
                                            </div>
                                            <div><strong>Fin:</strong>
                                                {{ \Carbon\Carbon::parse($accion->accion_fecha_fin_planificada)->format('d/m/Y') }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-info-circle mr-2"></i> No se han registrado acciones para
                                            este hallazgo.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Signatures -->
                <div class="row mt-5 pt-5">
                    <div class="col-8 offset-2 text-center">
                        <div class="pt-2 border-top border-dark" style="border-top-width: 1px !important;">
                            <div class="font-weight-bold text-dark">Firma del Propietario del Proceso</div>
                            <div class="text-muted small mt-1">&nbsp;</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
