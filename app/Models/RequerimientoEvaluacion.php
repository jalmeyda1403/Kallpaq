<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequerimientoEvaluacion extends Model
{
    protected $table = 'requerimiento_evaluaciones';

    protected $fillable = [
        'requerimiento_id',
        'num_actividades',
        'num_areas',
        'num_requisitos',
        'nivel_documentacion',
        'impacto_requerimiento',
        'complejidad_valor',
        'complejidad_nivel',
        'fecha_evaluacion',
    ];

    public function requerimiento()
    {
        return $this->belongsTo(Requerimiento::class);
    }
}
