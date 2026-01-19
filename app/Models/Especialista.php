<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Especialista extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cargo',
        'estado',
        'inactived_at',
    ];

    protected $appends = ['nombres'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getNombresAttribute()
    {
        // Esto automáticamente usa la relación 'user' y obtiene
        // el campo 'name' de la tabla 'users'.
        return $this->user->name;
    }

    public function hallazgos()
    {
        return $this->belongsToMany(Hallazgo::class)
            ->withPivot('motivo_asignacion', 'fecha_asignacion');

    }
}
