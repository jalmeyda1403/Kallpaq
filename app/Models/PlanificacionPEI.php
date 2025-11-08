<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanificacionPEI extends Model
{
    protected $table = 'planificacion_pei';
    protected $fillable = [
        'id', 'planificacion_pei_cod', 'planificacion_pei_nombre'
    ];
    protected $appends = ['descripcion']; 

    public function indicadores()
    {
        return $this->hasMany(Indicador::class, 'planificacion_pei_id', 'id');
    }
    public function procesos()
    {
        return $this->hasMany(Proceso::class, 'planificacion_pei_id', 'id');
    }
     public function getDescripcionAttribute()
    {
        return "{$this->planificacion_pei_cod} - {$this->planificacion_pei_nombre}";
    }  
}
	
	
