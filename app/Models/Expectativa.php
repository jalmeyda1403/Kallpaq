<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expectativa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'expectativas';
    
    protected $fillable = [
        'parte_interesada_id',
        'exp_descripcion',
        'exp_tipo',
        'exp_normas',
        'exp_criticidad',
        'exp_viabilidad',
        'exp_prioridad',
        'exp_estado',
        'exp_observaciones',
    ];

    protected $casts = [
        'exp_normas' => 'array', // JSON to Array
        'exp_criticidad' => 'integer',
        'exp_viabilidad' => 'integer',
        'exp_prioridad' => 'float',
    ];

    public function parteInteresada()
    {
        return $this->belongsTo(ParteInteresada::class);
    }

    public function procesos()
    {
        return $this->belongsToMany(Proceso::class, 'proceso_expectativa', 'expectativa_id', 'proceso_id');
    }

    public function compromisos()
    {
        return $this->hasMany(Compromiso::class, 'expectativa_id');
    }

    // Removed riesgos and obligaciones relations




    // Calculation logic
    public function getPrioridadAttribute()
    {
        // 1. Criticidad (1-5) 40%
        $criticidad = (int) $this->exp_criticidad;
        // 2. Viabilidad (1-5) 40% (Inversa: menos viable = más riesgo/atención?) 
        // No, usually viability implies easy to implement. let's assume Higher = More Viable.
        // But priority depends on "Importancia".
        // Let's stick to previous logic:
        
        $viabilidad = (int) $this->exp_viabilidad;
        
        // 3. Obligatoriedad (Tipo) 20%
        $obligatoriedad = ($this->exp_tipo === 'necesidad') ? 5 : 2;

        // Peso del Stakeholder
        $cuadrantePeso = match ($this->parteInteresada?->pi_cuadrante ?? 'III') { // Default low
            'I' => 1.0,   // Key Player
            'II' => 0.8,  // Keep Satisfied
            'III' => 0.5, // Monitor
            'IV' => 0.6, // Keep Informed
            default => 0.5,
        };

        // Pesos
        $score = ($criticidad * 0.4) + ($viabilidad * 0.4) + ($obligatoriedad * 0.2);
        
        // Ajuste por cuadrante
        $final = $score * $cuadrantePeso;

        return round($final, 2);
    }

    public function getNivelPrioridadAttribute()
    {
        // Max score = 5. Max final = 5 * 1.0 = 5.
        $val = $this->exp_prioridad;
        
        return match (true) {
            $val >= 4.0 => 'Crítica',
            $val >= 3.0 => 'Alta',
            $val >= 2.0 => 'Media',
            default => 'Baja',
        };
    }

    protected static function booted()
    {
        static::saving(function ($expectativa) {
            // Recalculate priority automatically on save
            $expectativa->exp_prioridad = $expectativa->prioridad;
        });
    }
}