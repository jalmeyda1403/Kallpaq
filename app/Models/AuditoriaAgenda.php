<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditoriaAgenda extends Model
{
    use HasFactory;

    protected $table = 'auditoria_agenda';

    protected $fillable = [
        'ae_id',
        'proceso_id',
        'aea_fecha',
        'aea_hora_inicio',
        'aea_hora_fin',
        'aea_actividad',
        'auditor_id',
        'observador_id',
        'aea_requisito',
        'aea_lugar',
        'aea_tipo',
        'estado',
        'aea_archivo'
    ];

    protected $casts = [
        'aea_requisito' => 'array',
    ];

    public function auditoria()
    {
        return $this->belongsTo(AuditoriaEspecifica::class, 'ae_id');
    }

    public function auditor()
    {
        return $this->belongsTo(Auditor::class, 'auditor_id');
    }

    public function observador()
    {
        return $this->belongsTo(Auditor::class, 'observador_id');
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }

    public function checklists()
    {
        return $this->hasMany(AuditoriaChecklist::class, 'agenda_id');
    }

    public function auditados()
    {
        return $this->hasMany(AuditoriaAuditado::class, 'agenda_id');
    }
}

