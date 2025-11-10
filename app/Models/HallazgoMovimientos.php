<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HallazgoMovimientos extends Model
{
    use HasFactory;

    protected $table = 'hallazgo_movimientos';

    protected $fillable = [
        'hallazgo_id',
        'estado',
        'comentario',
        'user_id',
    ];

    public function hallazgo()
    {
        return $this->belongsTo(Hallazgo::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}