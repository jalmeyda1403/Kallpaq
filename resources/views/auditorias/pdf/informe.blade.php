<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Informe de Auditoría</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px solid #dc3545;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .logo {
            max-height: 50px;
        }

        .title {
            font-size: 14pt;
            font-weight: bold;
            color: #dc3545;
            text-align: center;
            margin: 0;
        }

        .meta {
            font-size: 8pt;
            text-align: right;
        }

        .section-title {
            background-color: #f8f9fa;
            padding: 5px;
            font-weight: bold;
            font-size: 11pt;
            border: 1px solid #dee2e6;
            margin-top: 20px;
            text-transform: uppercase;
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .content-table th,
        .content-table td {
            border: 1px solid #dee2e6;
            padding: 5px;
            vertical-align: top;
        }

        .content-table th {
            background-color: #f2f2f2;
            text-align: left;
            font-weight: bold;
            font-size: 10pt;
        }

        .content-box {
            border: 1px solid #dee2e6;
            padding: 10px;
            font-size: 10pt;
            white-space: pre-wrap;
            margin-bottom: 10px;
        }

        .sub-section-title {
            font-weight: bold;
            border-bottom: 1px solid #eee;
            margin: 10px 0 5px 0;
            font-size: 10pt;
            color: #444;
        }

        .hallazgos-list {
            padding-left: 20px;
            margin: 0 0 10px 0;
        }

        .hallazgos-list li {
            margin-bottom: 5px;
            font-size: 9pt;
        }

        .text-center {
            text-align: center;
        }

        .signatures {
            margin-top: 50px;
            width: 100%;
        }

        .sig-box {
            width: 40%;
            border-top: 1px solid black;
            text-align: center;
            padding-top: 5px;
            font-size: 9pt;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td width="20%"><img src="{{ public_path('images/logo.png') }}" class="logo"></td>
            <td width="60%" class="title">Informe de Auditoría {{ $informe->codigo }}</td>
            <td width="20%" class="meta">
                F. Emisión: {{ $informe->fecha_emision ? $informe->fecha_emision->format('d/m/Y') : date('d/m/Y') }}<br>
                Versión: 01
            </td>
        </tr>
    </table>

    <div class="section-title">1. Resumen Ejecutivo</div>
    <div class="content-box">{{ $informe->resumen_ejecutivo }}</div>

    <div class="section-title">2. Alcance y Criterios</div>
    <div class="content-box">{{ $informe->alcance_criterios }}</div>

    @if (!empty($informe->procesos_auditados))
        <div class="section-title">3. Procesos Auditados</div>
        <table class="content-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>Proceso</th>
                    <th width="15%">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($informe->procesos_auditados as $index => $proceso)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $proceso['nombre'] ?? 'N/A' }}</td>
                        <td>{{ $proceso['fecha_auditoria'] ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if (!empty($informe->auditados))
        <div class="section-title">4. Lista de Auditados</div>
        <table class="content-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>Nombre</th>
                    <th>Cargo</th>
                    <th>Proceso</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($informe->auditados as $index => $auditado)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $auditado['nombre'] ?? 'N/A' }}</td>
                        <td>{{ $auditado['cargo'] ?? 'N/A' }}</td>
                        <td>{{ $auditado['proceso'] ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="page-break"></div>

    <div class="section-title">5. Hallazgos de Conformidad</div>
    @if (!empty($informe->hallazgos_conformidad))
        @foreach ($informe->hallazgos_conformidad as $grupo)
            <div class="sub-section-title">{{ $grupo['proceso'] }}</div>
            <ul class="hallazgos-list">
                @foreach ($grupo['items'] as $item)
                    <li><strong>{{ $item['norma'] }} - Req. {{ $item['requisito'] }}:</strong>
                        {{ $item['evidencia'] }}</li>
                @endforeach
            </ul>
        @endforeach
    @else
        <div class="content-box">No se registraron hallazgos de conformidad.</div>
    @endif

    <div class="section-title">6. Oportunidades de Mejora</div>
    @if (!empty($informe->oportunidades_mejora))
        @foreach ($oportunidades as $grupo)
            <div class="sub-section-title">{{ $grupo['proceso'] }}</div>
            <table class="content-table" style="margin-bottom: 15px;">
                <thead>
                    <tr>
                        <th width="15%">Sistema</th>
                        <th width="10%">Req.</th>
                        <th>Oportunidad de Mejora / Evidencia</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($grupo['items'] as $item)
                        <tr>
                            <td>{{ $item['norma'] ?? 'General' }}</td>
                            <td>{{ $item['requisito'] ?? 'N/A' }}</td>
                            <td>
                                <div>{{ $item['hallazgo'] }}</div>
                                <div style="margin-top: 5px; font-style: italic; color: #555;">
                                    <small>Evidencia: {{ $item['evidencia'] }}</small>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @else
        <div class="content-box">No se registraron oportunidades de mejora.</div>
    @endif

    <div class="section-title">7. Hallazgos de No Conformidad</div>
    @if (!empty($informe->hallazgos_no_conformidad))
        @foreach ($informe->hallazgos_no_conformidad as $grupo)
            <div class="sub-section-title">{{ $grupo['proceso'] }}</div>
            <table class="content-table">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="15%">Proceso</th>
                        <th width="10%">Sistema</th>
                        <th width="10%">Requisito</th>
                        <th width="35%">Descripción del Hallazgo</th>
                        <th width="5%">Clasif.</th>
                        <th width="20%">Evidencia</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($grupo['items'] as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $grupo['proceso'] }}</td>
                            <td>{{ $item['norma'] ?? '' }}</td>
                            <td>{{ $item['requisito'] ?? '' }}</td>
                            <td class="text-left">{{ $item['hallazgo'] }}</td>
                            <td class="text-center"><strong>{{ $item['hallazgo_clasificacion'] ?? '' }}</strong></td>
                            <td class="text-left">{{ $item['evidencia'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @else
        <div class="content-box">No se registraron no conformidades.</div>
    @endif

    <div class="section-title">8. Conclusiones</div>
    <div class="content-box">{{ $informe->conclusiones }}</div>

    <div class="section-title">9. Recomendaciones</div>
    <div class="content-box">{{ $informe->recomendaciones }}</div>

    <table class="signatures">
        <tr>
            <td align="center">
                <div class="sig-box">
                    <strong>{{ $informe->elaboradoPor->name ?? 'Auditor Líder' }}</strong><br>
                    Auditor Líder
                </div>
            </td>
            <td align="center">
                <div class="sig-box">
                    <strong>{{ $informe->aprobadoPor->name ?? 'Responsable del SIG' }}</strong><br>
                    Aprobado por
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
