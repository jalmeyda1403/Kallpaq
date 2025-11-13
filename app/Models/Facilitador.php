<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facilitador extends Model
{
    protected $table = 'facilitadores';

    protected $fillable = [
        'user_id',
        'cargo',
        'estado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function procesos()
    {
        return $this->belongsToMany(Proceso::class, 'proceso_facilitador')->using(ProcesoFacilitador::class);
    }
}
