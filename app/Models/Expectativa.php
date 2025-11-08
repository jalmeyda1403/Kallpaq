<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expectativa extends Model
{
    protected $table = 'expectativas';
    protected $fillable = [
        'id',
        'parte_interesada_id',
        'exp_tipo',
        'exp_descripcion',
        'sig',
        'proceso_id',
        'exp_observaciones',
        'exp_criticidad',
        'exp_viabilidad',
        'exp_prioridad',
        'exp_nivel_prioridad' // Add if you want to mass assign this as well
    ];

    // Ensure exp_criticidad and exp_viabilidad are cast to integer if needed
    protected $casts = [
        'sig' => 'array',
        'exp_criticidad' => 'integer',
        'exp_viabilidad' => 'integer',
        'exp_prioridad' => 'double',
    ];

    public function parteInteresada()
    {
        return $this->belongsTo(ParteInteresada::class);
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    public function getPrioridadAttribute()
    {
        $criticidad = (int) $this->exp_criticidad;
        $viabilidad = (int) $this->exp_viabilidad;
        $exp_tipo = strtolower($this->getAttribute('exp_tipo'));
        // Mapear obligatoriedad: expectativa = 2, necesidad = 3
        $obligatoriedad = match ($exp_tipo) {
            'expectativa' => 2,
            'necesidad' => 4,
            default => 0,
        };
        $cuadrantePeso = match ($this->parteInteresada->pi_cuadrante) {
            'I' => 1.0,
            'II' => 0.8,
            'III' => 0.6,
            'IV' => 0.4,
            default => 0.4, // Por si acaso
        };
        // Pesos
        $pesoCriticidad = 0.4;
        $pesoViabilidad = 0.4;
        $pesoObligatoriedad = 0.2;


        // Cálculo ponderado
        $prioridadBase = ($criticidad * $pesoCriticidad)
            + ($viabilidad * $pesoViabilidad)
            + ($obligatoriedad * $pesoObligatoriedad);

        $prioridadFinal = $prioridadBase * $cuadrantePeso;

        return round($prioridadFinal, 2);
    }
    public function getNivelPrioridadAttribute()
    {
        return match (true) {
            $this->exp_prioridad >= 3.2 => 'Muy Alta',
            $this->exp_prioridad >= 2.5 => 'Alta',
            $this->exp_prioridad >= 1.5 => 'Media',
            default => 'Baja',
        };
    }
    protected static function booted()
    {
        static::saving(function ($expectativa) {
            $expectativa->exp_prioridad = $expectativa->prioridad;
            $expectativa->exp_nivel_prioridad = $expectativa->nivel_prioridad;
        });
    }
}