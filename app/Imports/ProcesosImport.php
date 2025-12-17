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
            'cod_proceso' => $row['codigo'],
            'proceso_nombre' => $row['nombre'],
            'proceso_sigla' => $row['sigla'] ?? null,
            'proceso_tipo' => $row['tipo'] ?? 'Misional', // Valor por defecto
            'proceso_nivel' => $this->nivel,
            'cod_proceso_padre' => $row['codigo_padre'] ?? null,
            // Agrega otros campos según tu modelo
        ]);
    }

    public function rules(): array
    {
        return [
            'codigo' => 'required|unique:procesos,cod_proceso',
            'nombre' => 'required',
            'codigo_padre' => Rule::requiredIf($this->nivel > 0),
        ];
    }
}
