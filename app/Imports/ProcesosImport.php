<?php

namespace App\Imports;

use App\Models\Proceso;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class ProcesosImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $nivel;

    public function __construct($nivel)
    {
        $this->nivel = $nivel;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Proceso([
            'cod_proceso' => $row['cod_proceso'],
            'proceso_nombre' => $row['proceso_nombre'],
            'proceso_sigla' => $row['proceso_sigla'] ?? null,
            'proceso_tipo' => $row['proceso_tipo'] ?? 'Misional',
            'proceso_objetivo' => $row['proceso_objetivo'] ?? null,
            'proceso_producto' => $row['proceso_producto'] ?? null,
            'proceso_nivel' => $this->nivel, // Keep using the selected level from modal for safety/consistency
            'cod_proceso_padre' => $row['cod_proceso_padre'] ?? null,

            // Boolean fields
            'sgc' => isset($row['sgc']) ? ($row['sgc'] == 1 || $row['sgc'] == 'yes' ? 1 : 0) : 0,
            'sgas' => isset($row['sgas']) ? ($row['sgas'] == 1 || $row['sgas'] == 'yes' ? 1 : 0) : 0,
            'sgcm' => isset($row['sgcm']) ? ($row['sgcm'] == 1 || $row['sgcm'] == 'yes' ? 1 : 0) : 0,
            'sgsi' => isset($row['sgsi']) ? ($row['sgsi'] == 1 || $row['sgsi'] == 'yes' ? 1 : 0) : 0,
            'sgco' => isset($row['sgco']) ? ($row['sgco'] == 1 || $row['sgco'] == 'yes' ? 1 : 0) : 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'cod_proceso' => 'required|unique:procesos,cod_proceso',
            'proceso_nombre' => 'required',
            'cod_proceso_padre' => Rule::requiredIf($this->nivel > 0),
        ];
    }
}
