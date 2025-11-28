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
        'riesgo_tipo',
        'factor_id',
        'controles',
        'probabilidad',
        'impacto',
        'riesgo_valor',
        'riesgo_valoracion',
        'riesgo_tratamiento',
        'fecha_valoracion_rr',
        'probabilidad_rr',
        'impacto_rr',
        'evaluacion_rr',
        'riesgo_estado_rr',
        'estado',  // Estado: 'pendiente', 'abierto', 'cerrado'
    ];

    /**
     * Relación con las obligaciones a través de la tabla intermedia 'obligacion_riesgo'.
     * Un riesgo puede estar asociado a muchas obligaciones.
     */
    public function obligacion()
    {
        return $this->belongsToMany(Obligacion::class, 'obligacion_riesgo');
    }

    public function acciones()
    {
        return $this->hasMany(RiesgoAccion::class, 'riesgo_cod');
    }
    public function calcularRiesgoValor()
    {
        return $this->probabilidad * $this->impacto;
    }
    public function factor()
    {
        return $this->belongsTo(Factor::class);
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }


    // Método para asignar la valoración del riesgo
    public function calcularRiesgoValoracion()
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
            // Calcular riesgo_valor y riesgo_valoracion antes de crear
            $riesgo->riesgo_valor = $riesgo->calcularRiesgoValor();
            $riesgo->riesgo_valoracion = $riesgo->calcularRiesgoValoracion();
            $riesgo->estado = 'proyecto';
        });

        static::updating(function ($riesgo) {
            // Recalcular en caso de actualización
            $riesgo->riesgo_valor = $riesgo->calcularRiesgoValor();
            $riesgo->riesgo_valoracion = $riesgo->calcularRiesgoValoracion();
        });
    }
}
