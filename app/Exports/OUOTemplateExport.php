<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OUOTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function headings(): array
    {
        return [
            'id',
            'ouo_codigo',
            'ouo_nombre',
            'ouo_sigla',
            'ouo_cod_padre',
            'ouo_padre',
            'nivel_jerarquico',
            'doc_vigencia_alta',
            'fecha_vigencia_inicio'
        ];
    }

    public function array(): array
    {
        return [
            [
                '', // id
                'GG', // ouo_codigo
                'GERENCIA GENERAL', // ouo_nombre
                'GG', // ouo_sigla
                '', // ouo_cod_padre
                '', // ouo_padre
                '1', // nivel_jerarquico
                'R.G. N° 001-2024', // doc_vigencia_alta
                '2024-01-01' // fecha_vigencia_inicio
            ]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
