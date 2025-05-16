<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanificacionPEI extends Model
{
    protected $table = 'planificacion_pei';
    protected $fillable = [
        'id', 'cod_objetivo', 'objetivo_nombre'
    ];

    public function indicadores()
    {
        return $this->hasMany(Indicador::class, 'planificacion_pei_id', 'id');
    }
    public function procesos()
    {
        return $this->hasMany(Proceso::class, 'objetivo_pei', 'id');
    }  
}
	
	
