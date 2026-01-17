<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditoriaInforme extends Model
{
    use HasFactory;

    protected $table = 'auditoria_informes';

    protected $fillable = [
        'ae_id',
        'codigo',
        'estado',
        'resumen_ejecutivo',
        'alcance_criterios',
        'hallazgos_conformidad',
        'hallazgos_no_conformidad',
        'oportunidades_mejora',
        'procesos_auditados',
        'auditados',
        'conclusiones',
        'recomendaciones',
        'fecha_emision',
        'elaborado_por',
        'aprobado_por'
    ];

    protected $casts = [
        'hallazgos_conformidad' => 'array',
        'hallazgos_no_conformidad' => 'array',
        'oportunidades_mejora' => 'array',
        'procesos_auditados' => 'array',
        'auditados' => 'array',
        'fecha_emision' => 'date'
    ];

    public function auditoria()
    {
        return $this->belongsTo(AuditoriaEspecifica::class, 'ae_id');
    }

    public function elaboradoPor()
    {
        return $this->belongsTo(User::class, 'elaborado_por');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }
}
