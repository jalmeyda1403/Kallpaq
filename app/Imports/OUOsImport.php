<?php

namespace App\Imports;

use App\Models\OUO;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class OUOsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new OUO([
            'ouo_codigo' => $row['codigo'],
            'ouo_nombre' => $row['nombre'],
            'nivel_jerarquico' => $row['nivel'],
            'fecha_vigencia_inicio' => isset($row['fecha_inicio']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_inicio']) : Carbon::now(),
            'estado' => 1,
        ]);
    }

    public function rules(): array
    {
        return [
            'codigo' => 'required|unique:ouos,ouo_codigo',
            'nombre' => 'required',
            'nivel' => 'required|integer',
        ];
    }
}
