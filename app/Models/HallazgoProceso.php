<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class HallazgoProceso extends Pivot
{
    
    protected $table = 'hallazgo_proceso';

    public $incrementing = true;


    protected $fillable = [
        'hallazgo_id',
        'proceso_id'
    ];

    public function hallazgo()
    {
        return $this->belongsTo(Hallazgo::class);
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    public function acciones()
    {
        return $this->hasMany(Accion::class, 'hallazgo_proceso_id');
    }
}
