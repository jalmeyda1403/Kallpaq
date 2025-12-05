<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccionReprogramacion extends Model
{
    use HasFactory;

    protected $table = 'accion_reprogramaciones';

    protected $fillable = [
        'accion_id',
        'ar_fecha_anterior',
        'ar_fecha_nueva',
        'ar_justificacion',
        'ar_usuario_id',
    ];

    protected $casts = [
        'ar_fecha_anterior' => 'date',
        'ar_fecha_nueva' => 'date',
    ];

    public function accion()
    {
        return $this->belongsTo(Accion::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'ar_usuario_id');
    }
}
