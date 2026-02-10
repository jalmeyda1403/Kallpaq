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
        'aeq_horas_programadas'
    ];

    public function auditoria()
    {
        return $this->belongsTo(AuditoriaEspecifica::class, 'ae_id');
    }

    public function auditor()
    {
        return $this->belongsTo(Auditor::class, 'auditor_id');
    }

    // Helper to get user directly if needed
    public function usuario()
    {
        return $this->hasOneThrough(User::class, Auditor::class, 'id', 'id', 'auditor_id', 'user_id');
    }
}
