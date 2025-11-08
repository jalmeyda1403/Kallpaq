<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoMovimiento extends Model
{
    use HasFactory;
    protected $table = 'documento_movimientos';

    protected $fillable = [
        'documento_id',
        'accion',
        'descripcion',
        'usuario_id',
    ];

    protected $casts = [
        'fecha_movimiento' => 'datetime',
    ];

    // Documento principal
    public function documento()
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
