<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagramaContexto extends Model
{
    use HasFactory;

    // Definir la tabla asociada al modelo
    protected $table = 'diagrama_contexto';

    // Los campos que son asignables en masa
    protected $fillable = [
        'proceso_id',
        'archivo', 
        'version', 
        'vigencia', 
        'estado',
        'inactive_at'
    ];

   

    // Método para obtener la ruta completa del archivo almacenado
    public function getArchivoUrlAttribute()
    {
        return asset('storage/' . $this->archivo);
    }
}
