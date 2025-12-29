<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanContinuidad extends Model
{
    use HasFactory;

    protected $table = 'planes_continuidad';

    protected $fillable = [
        'codigo',
        'nombre',
        'objetivo',
        'tipo_plan',
        'escenario_id',
        'proceso_id',
        'responsable_id',
        'alcance',
        'equipo_respuesta',
        'procedimientos_activacion',
        'procedimientos_recuperacion',
        'recursos_necesarios',
        'comunicacion_crisis',
        'documento_path',
        'version',
        'fecha_aprobacion',
        'proxima_revision',
        'estado',
        'created_by',
    ];

    protected $casts = [
        'equipo_respuesta' => 'array',
        'fecha_aprobacion' => 'date',
        'proxima_revision' => 'date',
    ];

    /**
     * Tipos de plan
     */
    public static function getTiposPlan()
    {
        return [
            'bcp' => 'Plan de Continuidad de Negocio (BCP)',
            'drp' => 'Plan de Recuperación ante Desastres (DRP)',
            'irp' => 'Plan de Respuesta a Incidentes (IRP)',
            'crmp' => 'Plan de Gestión de Crisis (CRMP)',
        ];
    }

    /**
     * Genera código automáticamente
     */
    protected static function booted()
    {
        static::creating(function ($plan) {
            if (empty($plan->codigo)) {
                $prefijo = strtoupper($plan->tipo_plan ?? 'BCP');
                $count = static::where('tipo_plan', $plan->tipo_plan)->count() + 1;
                $plan->codigo = $prefijo . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Escenario asociado
     */
    public function escenario()
    {
        return $this->belongsTo(EscenarioContinuidad::class, 'escenario_id');
    }

    /**
     * Proceso cubierto
     */
    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }

    /**
     * Responsable del plan
     */
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Usuario que creó el plan
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Estrategias del plan
     */
    public function estrategias()
    {
        return $this->hasMany(EstrategiaContinuidad::class, 'plan_id');
    }

    /**
     * Pruebas realizadas
     */
    public function pruebas()
    {
        return $this->hasMany(PruebaContinuidad::class, 'plan_id');
    }

    /**
     * Incidentes donde se activó este plan
     */
    public function incidentes()
    {
        return $this->hasMany(IncidenteContinuidad::class, 'plan_activado_id');
    }

    /**
     * Verifica si el plan necesita revisión
     */
    public function getNecesitaRevisionAttribute()
    {
        if (!$this->proxima_revision) return false;
        return $this->proxima_revision->isPast();
    }

    /**
     * Color del estado
     */
    public function getEstadoColorAttribute()
    {
        return match ($this->estado) {
            'borrador' => 'secondary',
            'en_revision' => 'warning',
            'aprobado' => 'success',
            'obsoleto' => 'danger',
            default => 'primary',
        };
    }

    /**
     * Última prueba realizada
     */
    public function getUltimaPruebaAttribute()
    {
        return $this->pruebas()
            ->where('estado', 'completada')
            ->orderBy('fecha_ejecucion', 'desc')
            ->first();
    }
}
