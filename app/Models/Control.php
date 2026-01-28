<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Control extends Model
{
    use HasFactory;

    protected $table = 'controles';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo', // Preventivo, Detectivo
        'frecuencia',
        'responsable',
        'responsable_id',
        'fecha_implementacion',
        'estado'
    ];

    /**
     * Risks associated with this control.
     */
    public function riesgos()
    {
        return $this->belongsToMany(Riesgo::class, 'control_riesgo')
            ->withPivot(['eficacia', 'fecha_ultima_evaluacion', 'fecha_revaluacion', 'observaciones'])
            ->withTimestamps();
    }

    public function obligaciones()
    {
        return $this->belongsToMany(Obligacion::class, 'obligacion_control');
    }
}
