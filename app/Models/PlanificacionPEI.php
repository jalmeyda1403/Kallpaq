<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanificacionPEI extends Model
{
    protected $table = 'planificacion_pei';
    protected $fillable = [
        'id', 'objetivo', 'nombre_objetivo'
    ];

    public function indicadores()
    {
        return $this->hasMany(Indicador::class, 'planificacion_pei_id', 'id');
    } 
}
	
	
