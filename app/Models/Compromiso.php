<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compromiso extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'expectativa_compromisos';

    protected $fillable = [
        'expectativa_id',
        'ec_descripcion',
        'ec_responsable_id',
        'ec_fecha_limite',
        'ec_estado',
        'ec_avance',
    ];

    protected $dates = ['ec_fecha_limite', 'deleted_at'];

    public function expectativa()
    {
        return $this->belongsTo(Expectativa::class, 'expectativa_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'ec_responsable_id');
    }
}
