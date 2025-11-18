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
        'estado',
        'estado_flujo', // Nuevo campo
        'inventario_cierre', // Nuevo campo
        'fecha_cierre' // Nuevo campo
    ];

    public function procesos()
    {
        return $this->belongsToMany(Proceso::class, 'inventario_procesos', 'id_inventario', 'id_proceso');
    }

    // Si quieres que el campo 'vigencia' sea tratado como una fecha
    protected $casts = [
        'vigencia' => 'datetime',
        'fecha_cierre' => 'datetime', // Añadir cast para fecha_cierre
    ];

    // Si la tabla tiene un campo 'created_at' y 'updated_at', puedes usar las fechas automáticamente
    public $timestamps = true;

    // Relación con InventarioProceso
    public function inventarioProcesos()
    {
        return $this->hasMany(InventarioProceso::class, 'id_inventario');
    }

    // Relación para obtener procesos asociados al inventario y sus asignaciones OUO en ese momento
    public function procesosConOuos()
    {
        return $this->belongsToMany(Proceso::class, 'inventario_procesos', 'id_inventario', 'id_proceso')
                    ->withPivot('id_ouo_propietario', 'id_ouo_delegado', 'id_ouo_ejecutor', 'estado', 'inactive_at', 'created_at', 'updated_at'); // Incluir los nuevos campos OUO y otros de inventario_procesos
                    // No se necesita join con procesos_ouo_historial ya que se eliminó
                    // No se usa select aquí para no mezclar campos de pivot con campos de proceso.
                    // Los campos de pivot se acceden vía 'proceso.pivot.campo'
    }

    // O simplemente la relación directa para la tabla historial
    public function ouosHistorial()
    {
        return $this->hasMany(ProcesosOuoHistorial::class, 'id_inventario');
    }

}
