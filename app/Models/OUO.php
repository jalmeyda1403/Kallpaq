<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OUO extends Model
{
    use HasFactory;
    protected $table = 'ouos';  // Define el nombre de la tabla

    protected $fillable = [
        'nombre',
        'codigo',
        'ouo_padre',
        'subgerente_id',
        'subgerente_condicion',
        'nivel_jerarquico',
        'doc_vigencia_alta',
        'fecha_vigencia_inicio',
        'doc_vigencia_baja',
        'fecha_vigencia_fin',
        'estado',
        'inactive_at',
    ];

    // Relación con proceso (muchos a muchos)
    public function procesos()
    {
        return $this->belongsToMany(Proceso::class, 'procesos_ouo', 'id_ouo', 'id_proceso');
    }

    // Relación con ouo_padre (uno a muchos)
    public function ouoPadre()
    {
        return $this->belongsTo(Ouo::class, 'ouo_padre');
    }

    // Relación con subgerente (dependiendo de la tabla que relaciona)
    public function subgerente()
    {
        return $this->belongsTo(User::class, 'subgerente_id');
    }
}
