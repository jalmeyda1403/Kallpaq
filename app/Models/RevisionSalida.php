<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevisionSalida extends Model
{
    use HasFactory;

    protected $table = 'revision_salidas';

    protected $fillable = [
        'revision_id',
        'tipo_salida',
        'descripcion',
        'justificacion',
    ];

    /**
     * Tipos de salida según ISO 9001 §9.3.3
     */
    public static function getTiposSalida()
    {
        return [
            'decision_mejora' => 'Decisiones de mejora continua',
            'necesidad_cambio_sgc' => 'Necesidad de cambios en el SGC',
            'necesidad_recursos' => 'Necesidades de recursos',
            'actualizacion_riesgos' => 'Actualización de riesgos y oportunidades',
            'actualizacion_objetivos' => 'Actualización de objetivos de calidad',
            'otros' => 'Otros',
        ];
    }

    /**
     * Revisión a la que pertenece
     */
    public function revision()
    {
        return $this->belongsTo(RevisionDireccion::class, 'revision_id');
    }

    /**
     * Compromisos derivados de esta salida
     */
    public function compromisos()
    {
        return $this->hasMany(RevisionCompromiso::class, 'salida_id');
    }

    /**
     * Obtiene la etiqueta del tipo de salida
     */
    public function getTipoSalidaLabelAttribute()
    {
        return self::getTiposSalida()[$this->tipo_salida] ?? $this->tipo_salida;
    }
}
