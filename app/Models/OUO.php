<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OUO extends Model
{
    use HasFactory;
    protected $table = 'ouos';  // Define el nombre de la tabla

    protected $fillable = [
        'ouo_codigo',
        'ouo_nombre',       
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

    /**
     * The users that belong to the OUO.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ouo_user', 'ouo_id', 'user_id')
                    ->using(OuoUser::class)
                    ->withPivot('role_in_ouo', 'activo', 'deleted_at');
    }

    /**
     * Get the OuoUser records associated with the OUO.
     */
    public function ouoUsers()
    {
        return $this->hasMany(OuoUser::class);
    }

    // Relación con proceso (muchos a muchos)
    public function procesos(): BelongsToMany
    {
        // Assuming ProcesoOuo model exists for the 'procesos_ouo' pivot table
        return $this->belongsToMany(Proceso::class, 'procesos_ouo', 'id_ouo', 'id_proceso')
        ->using(ProcesoOuo::class) // Assuming ProcesoOuo model exists
        ->withPivot('propietario', 'delegado', 'ejecutor', 'sgc', 'sgas', 'sgcm', 'sgsi', 'sgco');
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
