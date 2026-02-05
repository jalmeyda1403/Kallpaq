<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObligacionEvaluacion extends Model
{
    use HasFactory;

    protected $table = 'obligacion_evaluacion';

    protected $fillable = [
        'obligacion_id',
        'oe_puntaje_total',
        'oe_nivel_criticidad', // baja, media, alta, muy_alta
        'oe_fecha_evaluacion',
        'oe_criterios_json'
    ];

    protected $casts = [
        'oe_fecha_evaluacion' => 'date',
        'oe_criterios_json' => 'array',
        'oe_puntaje_total' => 'decimal:2'
    ];

    public function obligacion()
    {
        return $this->belongsTo(Obligacion::class);
    }
}
