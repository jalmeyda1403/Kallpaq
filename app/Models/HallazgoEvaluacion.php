<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HallazgoEvaluacion extends Model
{
    use HasFactory;

    protected $table = 'hallazgo_evaluaciones';

    protected $fillable = [
        'hallazgo_id',
        'evaluador_id',
        'resultado',        // 'con eficacia' o 'sin eficacia'
        'observaciones',
        'fecha_evaluacion',
    ];

    protected $casts = [
        'fecha_evaluacion' => 'date',
    ];

    /**
     * Get the hallazgo that owns the evaluation.
     */
    public function hallazgo()
    {
        return $this->belongsTo(Hallazgo::class);
    }

    /**
     * Get the user who performed the evaluation.
     */
    public function evaluador()
    {
        return $this->belongsTo(User::class, 'evaluador_id');
    }

       public function setResultadoAttribute($value)
    {
        $this->attributes['resultado'] = $value;

        // If resultado is 'sin eficacia', set fecha_evaluacion to null
        if ($value === 'sin eficacia') {
            $this->attributes['fecha_evaluacion'] = null;
        }
    }
}
