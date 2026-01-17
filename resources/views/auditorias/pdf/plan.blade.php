<!DOCTYPE html>
<html>

<head>
    <title>Plan de Auditoría</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
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
            border-bottom: none;
            margin-top: 20px;
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
        }

        .content-table th,
        .content-table td {
            border: 1px solid #dee2e6;
            padding: 5px;
            vertical-align: top;
        }

        .content-table th {
            background-color: #fff;
            text-align: left;
            font-weight: bold;
        }

        .agenda-table {
            font-size: 9pt;
        }

        .agenda-table th {
            background-color: #dc3545;
            color: white;
            text-align: center;
        }

        .agenda-table td {
            font-size: 9pt;
        }

        .requisitos {
            font-size: 8pt !important;
        }

        .text-center {
            text-align: center;
        }

        .signatures {
            margin-top: 100px;
            width: 100%;
        }

        .sig-box {
            width: 40%;
            border-top: 1px solid black;
            text-align: center;
            padding-top: 5px;
            display: inline-block;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td width="20%"><img src="{{ public_path('images/logo.png') }}" class="logo"></td>
            <td width="60%" class="title">Plan de Auditoría {{ $auditoria->ae_codigo }}</td>
            <td width="20%" class="meta">
                F. Emisión: {{ date('d/m/Y') }}<br>
                Versión: 01
            </td>
        </tr>
    </table>

    <!-- 1. OBJETIVO Y ALCANCE -->
    <div class="section-title">1. OBJETIVO Y ALCANCE</div>
    <table class="content-table">
        <tr>
            <th width="25%">Objetivo</th>
            <td>{{ $auditoria->ae_objetivo }}</td>
        </tr>
        <tr>
            <th>Alcance</th>
            <td>{{ $auditoria->ae_alcance }}</td>
        </tr>
        <tr>
            <th>Criterios (Normas)</th>
            <td>
                @if (is_array($auditoria->ae_sistema))
                    {{ implode(', ', $auditoria->ae_sistema) }}
                @else
                    {{ $auditoria->ae_sistema }}
                @endif
            </td>
        </tr>
        <tr>
            <th>Fecha de Auditoría</th>
            <td>
                {{ $auditoria->ae_fecha_inicio ? \Carbon\Carbon::parse($auditoria->ae_fecha_inicio)->format('d/m/Y') : '' }}
                -
                {{ $auditoria->ae_fecha_fin ? \Carbon\Carbon::parse($auditoria->ae_fecha_fin)->format('d/m/Y') : '' }}
            </td>
        </tr>
    </table>

    <!-- 2. EQUIPO AUDITOR -->
    <div class="section-title">2. EQUIPO AUDITOR</div>
    <table class="content-table">
        <thead>
            <tr>
                <th width="25%">Rol</th>
                <th width="35%">Nombre</th>
                <th width="10%">Siglas</th>
                <th width="30%">Correo</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($auditoria->equipo as $member)
                <tr>
                    <td>{{ $member->aeq_rol }}</td>
                    <td>{{ $member->usuario ? $member->usuario->name : 'Desconocido' }}</td>
                    <td>
                        @php
                            $name = $member->usuario ? $member->usuario->name : '';
                            $initials = '';
                            $parts = explode(' ', $name);
                            foreach ($parts as $part) {
                                if (!empty($part)) {
                                    $initials .= strtoupper(substr($part, 0, 1));
                                }
                            }
                        @endphp
                        {{ $initials }}
                    </td>
                    <td>{{ $member->usuario ? $member->usuario->email : '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- 3. AGENDA (CRONOGRAMA) -->
    <div class="section-title">3. AGENDA (CRONOGRAMA)</div>
    <table class="content-table agenda-table">
        <thead>
            <tr>
                <th width="12%">Fecha</th>
                <th width="12%">Horario</th>
                <th>Actividad / Proceso</th>
                <th width="15%">Auditor</th>
                <th width="20%">Requisitos</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($auditoria->agenda->sortBy('aea_fecha')->sortBy('aea_hora_inicio') as $item)
                <tr>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->aea_fecha)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ substr($item->aea_hora_inicio, 0, 5) }} -
                        {{ substr($item->aea_hora_fin, 0, 5) }}</td>
                    <td>{{ $item->aea_actividad }}</td>
                    <td class="text-center">
                        @php
                            // Intento generar iniciales si es string, o mostrar tal cual
                            $auditorName = $item->aea_auditor;
                            if (is_string($auditorName)) {
                                $aParts = explode(' ', $auditorName);
                                $aInitials = '';
                                foreach ($aParts as $p) {
                                    if (!empty($p)) {
                                        $aInitials .= strtoupper(substr($p, 0, 1));
                                    }
                                }
                                echo $aInitials;
                            } else {
                                echo $auditorName; // Array or ID?
                            }

                        @endphp
                    </td>
                    <td class="requisitos">
                        @if (is_array($item->aea_requisito) && count($item->aea_requisito) > 0)
                            {{ implode(', ',array_map(function ($r) {return is_array($r) ? $r['numeral'] ?? '' : $r;}, $item->aea_requisito)) }}
                        @elseif(is_string($item->aea_requisito))
                            {{ $item->aea_requisito }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- SIGNATURES -->
    <table class="signatures">
        <tr>
            <td align="center">
                <div class="sig-box">Firma del Auditor Líder</div>
            </td>
            <td align="center">
                <div class="sig-box">Firma del Coordinador / Responsable SIG</div>
            </td>
        </tr>
    </table>
</body>

</html>
