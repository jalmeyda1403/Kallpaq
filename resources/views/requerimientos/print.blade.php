<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha de Requerimiento - RQ - {{ str_pad($requerimiento->id, 3, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            size: portrait;
            margin: 1cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #333;
            margin-bottom: 20px;
        }
        .header-table td {
            padding: 10px;
            vertical-align: middle;
        }
        .logo {
            width: 150px;
        }
        .document-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
        .document-code {
            text-align: center;
            font-size: 14px;
            border: 1px solid #333;
            padding: 5px;
            border-radius: 5px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            background-color: #e9ecef;
            padding: 8px;
            border-left: 4px solid #dc3545; /* Red accent */
            margin-bottom: 15px;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
        }
        .content-table th, .content-table td {
            border: 1px solid #ccc;
            padding: 8px;
            vertical-align: top;
        }
        .content-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 25%;
            text-align: left;
        }
        .content-table td {
            width: 75%;
        }
        .evaluation-table td:nth-child(2) {
            text-align: center;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
        }
        .signature-line {
            width: 300px;
            border-top: 1px solid #333;
            margin: 40px auto 0 auto;
            padding-top: 5px;
        }
        @media print {
            body {
                margin: 0;
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 20%;">
                    @php
                        $logoPath = public_path('images/logo.png');
                        if (file_exists($logoPath)) {
                            $logoData = base64_encode(file_get_contents($logoPath));
                            echo '<img src="data:image/png;base64,' . $logoData . '" alt="Logo" class="logo">';
                        }
                    @endphp
                </td>
                <td style="width: 60%;" class="document-title">
                    FICHA DE REQUERIMIENTO
                </td>
                <td style="width: 20%;">
                    <div class="document-code">
                        RQ - {{ str_pad($requerimiento->id, 3, '0', STR_PAD_LEFT) }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Información General -->
        <div class="section">
            <div class="section-title">1. INFORMACIÓN GENERAL DEL REQUERIMIENTO</div>
            <table class="content-table">
                <tbody>
                    <tr>
                        <th>Proceso</th>
                        <td>{{ $requerimiento->proceso->proceso_nombre ?? 'N/A' }} ({{ $requerimiento->proceso->cod_proceso ?? 'N/A' }})</td>
                    </tr>
                    <tr>
                        <th>Asunto del Requerimiento</th>
                        <td>{{ $requerimiento->asunto }}</td>
                    </tr>
                    <tr>
                        <th>Descripción</th>
                        <td>{!! nl2br(e($requerimiento->descripcion)) !!}</td>
                    </tr>
                    <tr>
                        <th>Justificación</th>
                        <td>{!! nl2br(e($requerimiento->justificacion)) !!}</td>
                    </tr>
                    <tr>
                        <th>Solicitante</th>
                        <td>{{ $requerimiento->solicitante->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Fecha de Creación</th>
                        <td>{{ $requerimiento->created_at->format('d/m/Y') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Evaluación de Complejidad -->
        @if($requerimiento->evaluacion)
        <div class="section">
            <div class="section-title">2. EVALUACIÓN PRELIMINAR DE COMPLEJIDAD</div>
            @php
                $options = \App\Helpers\RequerimientoHelper::getEvaluationOptions();
            @endphp
            <table class="content-table evaluation-table">
                <thead>
                    <tr>
                        <th style="width: 80%; text-align: left;">Criterio Evaluado</th>
                        <th style="width: 20%; text-align: center;">Puntaje</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>Cantidad de actividades del proceso</strong>
                            <div style="font-size: 10px; color: #555; margin-top: 5px;">
                                {{ $options['actividades'][$requerimiento->evaluacion->num_actividades] ?? 'N/A' }}
                            </div>
                        </td>
                        <td>{{ $requerimiento->evaluacion->num_actividades }}</td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Cantidad de unidades orgánicas involucradas</strong>
                            <div style="font-size: 10px; color: #555; margin-top: 5px;">
                                {{ $options['areas'][$requerimiento->evaluacion->num_areas] ?? 'N/A' }}
                            </div>
                        </td>
                        <td>{{ $requerimiento->evaluacion->num_areas }}</td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Requisitos normativos aplicables</strong>
                            <div style="font-size: 10px; color: #555; margin-top: 5px;">
                                {{ $options['requisitos'][$requerimiento->evaluacion->num_requisitos] ?? 'N/A' }}
                            </div>
                        </td>
                        <td>{{ $requerimiento->evaluacion->num_requisitos }}</td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Nivel de documentación requerida</strong>
                            <div style="font-size: 10px; color: #555; margin-top: 5px;">
                                {{ $options['documentacion'][$requerimiento->evaluacion->nivel_documentacion] ?? 'N/A' }}
                            </div>
                        </td>
                        <td>{{ $requerimiento->evaluacion->nivel_documentacion }}</td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Impacto del procedimiento</strong>
                            <div style="font-size: 10px; color: #555; margin-top: 5px;">
                                {{ $options['impacto'][$requerimiento->evaluacion->impacto_requerimiento] ?? 'N/A' }}
                            </div>
                        </td>
                        <td>{{ $requerimiento->evaluacion->impacto_requerimiento }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: right;">PUNTAJE TOTAL</th>
                        <td style="background-color: #f8f9fa;">{{ $requerimiento->evaluacion->complejidad_valor }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: right;">NIVEL DE COMPLEJIDAD</th>
                        <td style="background-color: #f8f9fa; text-transform: uppercase;">{{ $requerimiento->evaluacion->complejidad_nivel }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="signature-line">
                Firma del Solicitante
            </div>
        </div>

       
    </div>
</body>
</html>
