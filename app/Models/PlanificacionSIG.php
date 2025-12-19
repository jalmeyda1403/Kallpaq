<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanificacionSIG extends Model
{
    protected $table = 'planificacion_sig';
    protected $fillable = [
        'id',
        'objetivo_sig_cod',
        'sistema',
        'objetivo_sig_nombre'
    ];
    protected $appends = ['descripcion'];

    public function indicadores()
    {
        return $this->hasMany(Indicador::class, 'planificacion_sig_id', 'id');
    }

    public function getDescripcionAttribute()
    {
        return "{$this->objetivo_sig_cod} - {$this->objetivo_sig_nombre}";
    }
}


