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
        'proceso_id',
        'area_compliance_id',
        'documento_tecnico_normativo',
        'obligacion_principal',
        'obligacion_controles',
        'consecuencia_incumplimiento',
        'documento_deroga',
        'estado_obligacion'
    ];

    /**
     * Relación con la tabla 'areas_compliance'.
     * Una obligación pertenece a un área de compliance.
     */
    public function area_compliance()
    {
        return $this->belongsTo(AreaCompliance::class, 'area_compliance_id');
    }

    /**
     * Relación con la tabla 'procesos'.
     * Una obligación pertenece a un proceso.
     */
    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }

    /**
     * Relación con los riesgos a través de la tabla intermedia 'obligacion_riesgo'.
     * Una obligación tiene muchos riesgos.
     */
    public function riesgos()
    {
        return $this->belongsToMany(Riesgo::class, 'obligacion_riesgo');
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
        return $this->belongsTo(Documento::class);
    }

}