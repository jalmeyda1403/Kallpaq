<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditoriaEvaluacion extends Model
{
    use HasFactory;

    protected $table = 'auditoria_evaluacion';

    protected $fillable = [
        'ae_id',
        'evaluador_id',
        'evaluado_id',
        'aev_rol_evaluado',
        'aev_criterios',
        'aev_promedio',
        'aev_comentario'
    ];

    protected $casts = [
        'aev_criterios' => 'array'
    ];

    public function auditoria()
    {
        return $this->belongsTo(AuditoriaEspecifica::class, 'ae_id');
    }

    public function evaluador()
    {
        return $this->belongsTo(User::class, 'evaluador_id');
    }

    public function evaluado()
    {
        return $this->belongsTo(User::class, 'evaluado_id');
    }
}
