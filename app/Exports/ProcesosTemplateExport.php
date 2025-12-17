<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProcesosTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $nivel;

    public function __construct($nivel)
    {
        $this->nivel = $nivel;
    }

    public function headings(): array
    {
        $headers = [
            'codigo',
            'nombre',
            'sigla',
            'tipo',
        ];

        if ($this->nivel > 0) {
            $headers[] = 'codigo_padre';
        }

        return $headers;
    }

    public function array(): array
    {
        $example = [
            'P-01',
            'Nombre del Proceso',
            'SIGLA',
            'Misional',
        ];

        if ($this->nivel > 0) {
            $example[] = 'COD-PADRE-EJEMPLO';
        }

        return [$example];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}
