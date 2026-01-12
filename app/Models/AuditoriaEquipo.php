<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditoriaEquipo extends Model
{
    use HasFactory;

    protected $table = 'auditoria_equipo';

    protected $fillable = [
        'ae_id',
        'auditor_id', // Links to User/Personal
        'aeq_rol',
        'aeq_horas_planificadas',
        'aeq_horas_ejecutadas'
    ];

    public function auditoria()
    {
        return $this->belongsTo(AuditoriaEspecifica::class, 'ae_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }
}
