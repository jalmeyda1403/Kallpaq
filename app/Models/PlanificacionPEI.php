<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanificacionPEI extends Model
{
    protected $table = 'planificacion_pei';
    protected $fillable = [
        'pp_cod',
        'pp_nombre',
        'pp_alcance',
        'pp_documento_aprueba',
        'pp_fecha_aprueba',
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
        return "{$this->pp_cod} - {$this->pp_nombre}";
    }  
}
	
	
