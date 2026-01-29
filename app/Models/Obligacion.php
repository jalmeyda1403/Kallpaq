<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obligacion extends Model
{
    use HasFactory;

    // Especificamos el nombre de la tabla en caso de que no siga la convención predeterminada
    protected $table = 'obligaciones';

    protected $fillable = [
        'area_compliance_id',
        'subarea_compliance_id',
        'documento_tecnico_normativo',
        'obligacion_principal',
        'consecuencia_incumplimiento',
        'documento_deroga',
        'estado_obligacion',
        'radar_id',
        'documento_id',
        'tipo_obligacion',
        'frecuencia_revision'
    ];

    /**
     * Relación con la tabla 'areas_compliance'.
     * Una obligación pertenece a un área de compliance.
     */
    public function area_compliance()
    {
        return $this->belongsTo(AreaCompliance::class, 'area_compliance_id');
    }

    public function subarea_compliance()
    {
        return $this->belongsTo(SubAreaCompliance::class, 'subarea_compliance_id');
    }

    /**
     * Relación con la tabla 'procesos' (Muchos a Muchos).
     */
    public function procesos()
    {
        return $this->belongsToMany(Proceso::class, 'obligacion_proceso', 'obligacion_id', 'proceso_id');
    }

    /**
     * Relación con los riesgos a través de la tabla intermedia 'obligacion_riesgo'.
     * Una obligación tiene muchos riesgos.
     */
    public function riesgos()
    {
        return $this->belongsToMany(Riesgo::class, 'obligacion_riesgo');
    }

    /**
     * Relación con los controles directos.
     */
    public function controles()
    {
        return $this->belongsToMany(Control::class, 'obligacion_control');
    }


    public function asociarRiesgo(Riesgo $riesgo)
    {
        $this->riesgos()->attach($riesgo->id);
    }

    /**
     * Cambiar el estado de la obligación, por ejemplo, cuando se cumpla o se dé de baja.
     * 
     * @param string $estado
     * @return void
     */
    public function cambiarEstado($estado)
    {
        $this->update(['estado' => $estado]);
    }
    public function cantidadRiesgos()
    {
        return $this->riesgos()->count();  // Devuelve la cantidad de riesgos asociados
    }

    public function getEstadoClassAttribute()
    {
        return match ($this->estado_obligacion) {
            'pendiente' => 'bg-warning text-dark',
            'controlada' => 'bg-success',
            'inactiva' => 'bg-secondary',
            'suspendida' => 'bg-danger',
            default => 'bg-light text-dark',
        };
    }

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }

    public function radar()
    {
        return $this->belongsTo(RadarNormativo::class, 'radar_id');
    }

    public function acciones()
    {
        return $this->hasMany(Accion::class, 'accion_obligacion_id');
    }
}