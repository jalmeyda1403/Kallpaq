<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioProceso extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'inventario_procesos';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'id_inventario',
        'id_proceso',
        'id_ouo_propietario',
        'id_ouo_delegado',
        'id_ouo_ejecutor',
        'estado',
        'inactive_at',
    ];

    // Asegurar que se manejen las fechas created_at y updated_at
    public $timestamps = true;

}