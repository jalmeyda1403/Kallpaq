<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditoriaEspecifica extends Model
{
    use HasFactory;

    protected $table = 'auditoria_especifica';

    protected $fillable = [
        'pa_id',
        'ae_codigo',
        'ae_objetivo',
        'ae_criterios',
        'ae_alcance',
        'ae_fecha_inicio',
        'ae_fecha_fin',
        'ae_estado',
        'ae_lugar',
        'ae_direccion',
        'ae_reunion_apertura',
        'ae_reunion_cierre',
        'proceso_id'
    ];

    protected $casts = [
        'ae_fecha_inicio' => 'date',
        'ae_fecha_fin' => 'date',
        'ae_reunion_apertura' => 'datetime',
        'ae_reunion_cierre' => 'datetime',
    ];

    public function programa()
    {
        return $this->belongsTo(ProgramaAuditoria::class, 'pa_id');
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }

    public function equipo()
    {
        // Using custom pivot model for extra fields
        return $this->hasMany(AuditoriaEquipo::class, 'ae_id');
    }

    public function agenda()
    {
        return $this->hasMany(AuditoriaAgenda::class, 'ae_id');
    }

    public function evaluaciones()
    {
        return $this->hasMany(AuditoriaEvaluacion::class, 'ae_id');
    }
}
