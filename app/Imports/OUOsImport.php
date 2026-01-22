<?php

namespace App\Imports;

use App\Models\OUO;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class OUOsImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        // Primer paso: Crear o actualizar registros básicos (sin el padre técnico para evitar fallos de FK)
        foreach ($rows as $row) {
            if (empty($row['ouo_codigo']))
                continue;

            $data = [
                'ouo_codigo' => $row['ouo_codigo'],
                'ouo_nombre' => $row['ouo_nombre'],
                'ouo_sigla' => $row['ouo_sigla'] ?? null,
                'ouo_cod_padre' => $row['ouo_cod_padre'] ?? null,
                'nivel_jerarquico' => $row['nivel_jerarquico'] ?? 1,
                'doc_vigencia_alta' => $row['doc_vigencia_alta'] ?? null,
                'fecha_vigencia_inicio' => $this->transformDate($row['fecha_vigencia_inicio'] ?? null),
                'estado' => 1,
            ];

            if (!empty($row['id'])) {
                OUO::updateOrCreate(['id' => $row['id']], $data);
            } else {
                // Si no hay ID, buscamos por código para actualizar si ya existe, evitando duplicados
                OUO::updateOrCreate(['ouo_codigo' => $row['ouo_codigo']], $data);
            }
        }

        // Segundo paso: Vincular las jerarquías una vez que todos los registros existen
        foreach ($rows as $row) {
            if (empty($row['ouo_codigo']))
                continue;

            $ouo = OUO::where('ouo_codigo', '=', $row['ouo_codigo'], 'and')->first();
            if (!$ouo)
                continue;

            $parentId = null;

            // 1. Intentar por ID técnico si se proporcionó y es válido
            if (!empty($row['ouo_padre']) && is_numeric($row['ouo_padre'])) {
                if (OUO::where('id', '=', $row['ouo_padre'], 'and')->exists()) {
                    $parentId = $row['ouo_padre'];
                }
            }

            // 2. Si no hay ID o no fue válido, intentar buscar por ouo_cod_padre
            if (!$parentId && !empty($row['ouo_cod_padre'])) {
                $parent = OUO::where('ouo_codigo', '=', $row['ouo_cod_padre'], 'and')->first();
                if ($parent) {
                    $parentId = $parent->id;
                }
            }

            // Actualizar solo el campo padre
            $ouo->update(['ouo_padre' => $parentId]);
        }
    }

    private function transformDate($value)
    {
        if (empty($value))
            return Carbon::now();

        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            }
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return Carbon::now();
        }
    }

    public function rules(): array
    {
        return [
            'ouo_codigo' => 'required',
            'ouo_nombre' => 'required',
            'nivel_jerarquico' => 'required|integer',
        ];
    }
}
