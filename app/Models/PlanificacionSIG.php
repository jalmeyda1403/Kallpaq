<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanificacionSIG extends Model
{
    protected $table = 'planificacion_sig';
    protected $fillable = [
        'id', 'objetivo', 'sistema', 'nombre_objetivo'
    ];

    public function indicadores()
    {
        return $this->hasMany(Indicador::class, 'planificacion_sig_id', 'id');
    } 
}
	
	
