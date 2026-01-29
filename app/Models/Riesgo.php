<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Riesgo extends Model
{
    use HasFactory;

    protected $fillable = [
        'riesgo_cod',
        'proceso_id',
        'riesgo_nombre',
        'riesgo_consecuencia',
        'riesgo_tipo',
        'factor_id',
        'riesgo_controles',
        'riesgo_probabilidad',
        'riesgo_impacto',
        'riesgo_valor',
        'riesgo_nivel',
        'riesgo_matriz',
        'riesgo_tratamiento',
        'riesgo_estado',
        'riesgo_fecha_valoracion_rr',
        'riesgo_probabilidad_rr',
        'riesgo_impacto_rr',
        'riesgo_valor_rr',
        'riesgo_nivel_rr',
        'riesgo_estado_rr',
        'riesgo_ciclo',
        'especialista_id',
    ];

    public function especialista()
    {
        return $this->belongsTo(User::class, 'especialista_id');
    }

    /**
     * Relación con las obligaciones a través de la tabla intermedia 'obligacion_riesgo'.
     * Un riesgo puede estar asociado a muchas obligaciones.
     */
    public function obligacion()
    {
        return $this->belongsToMany(Obligacion::class, 'obligacion_riesgo');
    }

    /**
     * Relación con los controles (Refactorizado ISO 37301).
     * Muchos a Muchos con la tabla controles.
     */
    public function controles()
    {
        return $this->belongsToMany(Control::class, 'control_riesgo', 'riesgo_id', 'control_id')
            ->withPivot(['eficacia', 'fecha_ultima_evaluacion', 'fecha_revaluacion', 'observaciones'])
            ->withTimestamps();
    }
    public function calcularRiesgoValor()
    {
        return $this->riesgo_probabilidad * $this->riesgo_impacto;
    }
    public function factor()
    {
        return $this->belongsTo(Factor::class);
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }

    public function acciones()
    {
        return $this->hasMany(Accion::class, 'accion_riesgo_id');
    }

    public function revisiones()
    {
        return $this->hasMany(RiesgoRevision::class, 'riesgo_id');
    }


    // Método para asignar la valoración del riesgo
    public function calcularRiesgoNivel()
    {
        $riesgo_valor = $this->calcularRiesgoValor();

        if ($riesgo_valor >= 80) {
            return 'Muy Alto';
        } elseif ($riesgo_valor >= 48) {
            return 'Alto';
        } elseif ($riesgo_valor >= 32) {
            return 'Medio';
        } else {
            return 'Bajo';
        }
    }

    // Evento Eloquent que se ejecuta antes de guardar el modelo
    protected static function booted()
    {
        static::creating(function ($riesgo) {
            // Calcular riesgo_valor y riesgo_nivel antes de crear
            $riesgo->riesgo_valor = $riesgo->calcularRiesgoValor();
            $riesgo->riesgo_nivel = $riesgo->calcularRiesgoNivel();
            if (empty($riesgo->riesgo_estado)) {
                $riesgo->riesgo_estado = 'proyecto';
            }
            if (empty($riesgo->riesgo_ciclo)) {
                $riesgo->riesgo_ciclo = 1;
            }
        });

        static::updating(function ($riesgo) {
            // Recalcular en caso de actualización
            $riesgo->riesgo_valor = $riesgo->calcularRiesgoValor();
            $riesgo->riesgo_nivel = $riesgo->calcularRiesgoNivel();
        });
    }
}
