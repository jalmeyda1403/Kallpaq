<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Causa extends Model
{
    use HasFactory;
    protected $table = 'hallazgo_causas';
    protected $fillable = [
        'hallazgo_id',
        'hc_metodo',
        'hc_por_que1',
        'hc_por_que2',
        'hc_por_que3',
        'hc_por_que4',
        'hc_por_que5',
        'hc_mano_obra',
        'hc_metodologias',
        'hc_materiales',
        'hc_maquinas',
        'hc_medicion',
        'hc_medio_ambiente',
        'hc_resultado',
    ];

    public function hallazgo()
    {
        return $this->belongsTo(Hallazgo::class, 'hallazgo_id', 'id');
    }
}
