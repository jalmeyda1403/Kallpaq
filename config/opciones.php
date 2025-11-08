<?php
return [
    //Indicadores
    'frecuencias' => [
        'mensual' => 'Mensual',
        'trimestral' => 'Trimestral',
        'semestral' => 'Semestral',
        'anual' => 'Anual',
    ],
    'parametro_medida' => [
        'ratio' => 'Ratio',
        'porcentaje' => 'Porcentaje',
        'numero' => 'Número',
        'indice' => 'Indice',
        'tasa' => 'Tasa',
    ],
    'tipos_indicador' => [
        'Producto' => 'Producto',
        'Servicio' => 'Servicio',
        'Resultado' => 'Resultado',
        'Calidad' => 'Calidad',
    ],
    'tipos_agregacion' => [
        'acumulada' => 'Acumulada',
        'no acumulada' => 'No acumulada',
    ],
    'sentido' => [
        'ascendente' => 'Ascendente',
        'descendente' => 'Descendente',
    ],
    'periodo' => ['actual' => '2023'],
    //Hallazgos

    'clasificacion' => [
        'NCM' => 'No conformidad mayor',
        'Ncme' => 'No conformidad menor',
        'Obs' => 'Observación',
        'Odm' => 'Oportunidad de Mejora'
    ],

    'origen' => [
        'IN' => 'Auditoría Interna',
        'EX' => 'Auditoría Externa',
        'SN' => 'Salida No Conforme',
        'RI' => 'Resultado Análisis Indicadores',
        'GR' => 'Resultados Gestión de Riesgos',
        'CL' => 'Reclamos y quejas de los clientes',
        'HA' => 'Hallazgos de personal',
        'ACAL' => 'Aseguramiento',
        'HOF' => 'Hallazgos de Oficio',
        'OT' => 'Otros'
    ],

    'estado_hallazgo' => [
        'Abierto' => 'Abierto',
        'En implementación' => 'En implementación',
        'Pendiente' => 'Pendiente',
        'Cerrado' => 'Cerrado'
    ],

    'estado_final_hallazgo' => [
        'Sin Efacia' => 'Sin Efacia',
        'Con Eficacia' => 'Con Eficacia'
    ],
    'sig' => [
        'SGC' => 'Gestión de la Calidad',
        'SGAS' => 'Gestión Antisborno',
        'SGCM' => 'Gestión de Compliance',
        'SGSI' => 'Seguridad de la Información',
        'SGCE' => 'Gestión de la Calidad Educativa'
    ],

    'estado_acciones' => [
        'Programada' => 'Programada',
        'Pendiente' => 'Pendiente',
        'En implementación' => 'En implementación',
        'Cancelada' => 'Cancelada',
        'Completada' => 'Completada',
        'Cerrada' => 'Cerrada'
    ],


    'auditor_tipo' => [
        'auditor interno' => 'auditor interno',
        'auditor externo' => 'auditor externo',
        'colaborador' => 'colaborador'
    ],
//Tipo_documento
    'tipos_documento' => [
        '1' => 'Manual de Gestión',
        '2' => 'Manual',
        '3' => 'Plan de la Calidad',
        '4' => 'Procedimiento',
        '5' => 'Guía',
        '6' => 'Instructivo',
        '7' => 'Directriz',
        '8' => 'Protocolo',
        '9' => 'Formato',
        '10' => 'Ley',
        '11' => 'Directiva',
        '12' => 'Reglamento',
        '13' => 'Política',
        '14' => 'Normas Generales',
        '15' => 'Código',
    ],

    //Estado
    'estado' => [
        '0' => 'inactivo',
        '1' => 'activo'
    ],
];


