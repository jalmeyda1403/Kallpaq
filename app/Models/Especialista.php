<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Especialista extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'cargo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hallazgos()
    {
        return $this->belongsToMany(Hallazgo::class)
                    ->withPivot('motivo_asignacion', 'fecha_asignacion');
                 
    }
}
