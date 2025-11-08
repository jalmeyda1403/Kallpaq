<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;
    protected $table = 'inventarios';
    protected $fillable = [
        'nombre',
        'descripcion',
        'documento_aprueba',
        'vigencia',
        'enlace',
        'estado'
    ];

    // Si quieres que el campo 'vigencia' sea tratado como una fecha
    protected $casts = [
        'vigencia' => 'datetime',
    ];

    // Si la tabla tiene un campo 'created_at' y 'updated_at', puedes usar las fechas automáticamente
    public $timestamps = true;

}
