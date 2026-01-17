<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class NormaRequisitoImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        return $rows->map(function ($row) {
            return [
                'nr_numeral' => (string) ($row['numeral'] ?? $row['nr_numeral'] ?? ''),
                'nr_denominacion' => (string) ($row['denominacion'] ?? $row['nr_denominacion'] ?? ''),
                'nr_detalle' => (string) ($row['detalle'] ?? $row['nr_detalle'] ?? ''),
            ];
        });
    }
}
