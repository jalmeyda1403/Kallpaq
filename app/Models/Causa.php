<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Causa extends Model
{
    use HasFactory;
    protected $table = 'hallazgos_causas';   
    protected $fillable = [
        'hallazgo_id',       
        'metodo',
        'mano_obra',
        'metodologias',
        'materiales',
        'maquinas',
        'medicion',
        'medio_ambiente',
        'por_que_1',
        'por_que_2',
        'por_que_3',
        'por_que_4',
        'por_que_5',
        'resultado',
    ];

    public function hallazgo()
    {
        return $this->belongsTo(Hallazgo::class, 'hallazgo_id', 'id');
    }
}
