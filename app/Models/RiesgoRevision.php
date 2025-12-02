<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiesgoRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'riesgo_id',
        'rr_fecha',
        'rr_responsable_id',
        'rr_resultado',
        'rr_comentario',
        'rr_ciclo',
    ];

    public function riesgo()
    {
        return $this->belongsTo(Riesgo::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'rr_responsable_id');
    }
}
