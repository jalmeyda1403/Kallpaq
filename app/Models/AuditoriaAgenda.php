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
        'aea_fecha',
        'aea_hora_inicio',
        'aea_hora_fin',
        'aea_actividad',
        'aea_auditado',
        'aea_auditor',
        'aea_requisito',
        'aea_lugar'
    ];

    public function auditoria()
    {
        return $this->belongsTo(AuditoriaEspecifica::class, 'ae_id');
    }
}
