<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditoriaChecklist extends Model
{
    use HasFactory;

    protected $table = 'auditoria_checklists';

    protected $fillable = [
        'agenda_id',
        'norma_referencia',
        'requisito_referencia',
        'requisito_contenido',
        'pregunta',
        'criterio_auditoria',
        'evidencia_esperada',
        'estado_cumplimiento', // 'Sin Evaluar', 'Conforme', 'No Conforme', 'OM', 'No Aplica'
        'evidencia_registrada',
        'hallazgo_detectado',
        'hallazgo_redaccion',
        'hallazgo_resumen',
        'criterio_redaccion',
        'evidencia_redaccion',
        'hallazgo_clasificacion',
        'tipo_hallazgo',
        'comentarios',
        'ai_generated'
    ];

    protected $casts = [
        'ai_generated' => 'boolean'
    ];

    public function agenda()
    {
        return $this->belongsTo(AuditoriaAgenda::class, 'agenda_id');
    }
}
