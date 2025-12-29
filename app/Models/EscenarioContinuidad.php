<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EscenarioContinuidad extends Model
{
    use HasFactory;

    protected $table = 'escenarios_continuidad';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'categoria',
        'probabilidad',
        'impacto',
        'nivel_riesgo',
        'activos_afectados',
        'procesos_afectados',
        'controles_existentes',
        'activo',
    ];

    protected $casts = [
        'activos_afectados' => 'array',
        'procesos_afectados' => 'array',
        'activo' => 'boolean',
    ];

    /**
     * Categorías de escenarios
     */
    public static function getCategorias()
    {
        return [
            'desastre_natural' => 'Desastre natural',
            'falla_tecnologica' => 'Falla tecnológica',
            'ciberataque' => 'Ciberataque',
            'pandemia' => 'Pandemia/Emergencia sanitaria',
            'incidente_seguridad' => 'Incidente de seguridad',
            'falla_proveedor' => 'Falla de proveedor crítico',
            'falla_infraestructura' => 'Falla de infraestructura',
            'otro' => 'Otro',
        ];
    }

    /**
     * Escala de probabilidad
     */
    public static function getProbabilidades()
    {
        return [
            'muy_baja' => ['label' => 'Muy Baja', 'valor' => 1],
            'baja' => ['label' => 'Baja', 'valor' => 2],
            'media' => ['label' => 'Media', 'valor' => 3],
            'alta' => ['label' => 'Alta', 'valor' => 4],
            'muy_alta' => ['label' => 'Muy Alta', 'valor' => 5],
        ];
    }

    /**
     * Escala de impacto
     */
    public static function getImpactos()
    {
        return [
            'insignificante' => ['label' => 'Insignificante', 'valor' => 1],
            'menor' => ['label' => 'Menor', 'valor' => 2],
            'moderado' => ['label' => 'Moderado', 'valor' => 3],
            'mayor' => ['label' => 'Mayor', 'valor' => 4],
            'catastrofico' => ['label' => 'Catastrófico', 'valor' => 5],
        ];
    }

    /**
     * Genera código y calcula nivel de riesgo
     */
    protected static function booted()
    {
        static::creating(function ($escenario) {
            if (empty($escenario->codigo)) {
                $count = static::count() + 1;
                $escenario->codigo = 'ESC-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            }
            $escenario->calcularNivelRiesgo();
        });

        static::updating(function ($escenario) {
            $escenario->calcularNivelRiesgo();
        });
    }

    /**
     * Calcula el nivel de riesgo
     */
    public function calcularNivelRiesgo()
    {
        $probabilidades = self::getProbabilidades();
        $impactos = self::getImpactos();

        $valProb = $probabilidades[$this->probabilidad]['valor'] ?? 3;
        $valImp = $impactos[$this->impacto]['valor'] ?? 3;

        $this->nivel_riesgo = $valProb * $valImp;
    }

    /**
     * Planes asociados a este escenario
     */
    public function planes()
    {
        return $this->hasMany(PlanContinuidad::class, 'escenario_id');
    }

    /**
     * Incidentes de este escenario
     */
    public function incidentes()
    {
        return $this->hasMany(IncidenteContinuidad::class, 'escenario_id');
    }

    /**
     * Color del nivel de riesgo
     */
    public function getNivelRiesgoColorAttribute()
    {
        if ($this->nivel_riesgo >= 20) return 'danger';
        if ($this->nivel_riesgo >= 12) return 'orange';
        if ($this->nivel_riesgo >= 6) return 'warning';
        return 'success';
    }

    /**
     * Etiqueta del nivel de riesgo
     */
    public function getNivelRiesgoLabelAttribute()
    {
        if ($this->nivel_riesgo >= 20) return 'Muy Alto';
        if ($this->nivel_riesgo >= 12) return 'Alto';
        if ($this->nivel_riesgo >= 6) return 'Medio';
        return 'Bajo';
    }
}
