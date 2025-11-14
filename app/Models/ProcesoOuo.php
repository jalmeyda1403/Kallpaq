<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot; // Import Pivot

class ProcesoOuo extends Pivot // Extend Pivot
{
    use HasFactory;

    protected $table = 'procesos_ouo'; // Specify the pivot table name

    protected $fillable = [
        'id_ouo',
        'id_proceso',
        'propietario',
        'delegado',
        'ejecutor',
        'sgc',
        'sgas',
        'sgcm',
        'sgsi',
        'sgco',
    ];

    /**
     * Get the Proceso that owns the ProcesoOuo.
     */
    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'id_proceso');
    }

    /**
     * Get the OUO that owns the ProcesoOuo.
     */
    public function ouo()
    {
        return $this->belongsTo(OUO::class, 'id_ouo');
    }
}
