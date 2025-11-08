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
        'causa_metodo',
        'causa_por_que1',
        'causa_por_que2',
        'causa_por_que3',
        'causa_por_que4',
        'causa_por_que5',
        'causa_mano_obra',
        'causa_metodologias',
        'causa_materiales',
        'causa_maquinas',
        'causa_medicion',
        'causa_medio_ambiente',
        'causa_resultado',
    ];

    public function hallazgo()
    {
        return $this->belongsTo(Hallazgo::class, 'hallazgo_id', 'id');
    }
}
