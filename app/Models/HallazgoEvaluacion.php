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
        'he_responsable_id',
        'he_resultado',        // 'con eficacia' o 'sin eficacia'
        'he_comentario',
        'he_fecha',
        'he_evidencias',
        'he_ciclo',
    ];

    protected $casts = [
        'he_fecha' => 'date',
        'he_evidencias' => 'array',
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
        return $this->belongsTo(User::class, 'he_responsable_id');
    }

    public function setResultadoAttribute($value)
    {
        $this->attributes['he_resultado'] = $value;

        // If resultado is 'sin eficacia', set he_fecha to null
        if ($value === 'sin eficacia') {
            $this->attributes['he_fecha'] = null;
        }
    }
}
