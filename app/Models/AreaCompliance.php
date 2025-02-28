<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaCompliance extends Model
{
    use HasFactory;
    protected $table = 'areas_compliance';

    // Definimos los campos que se pueden llenar de forma masiva
    protected $fillable = [
        'area_compliance_nombre',
        'area_compliance_descripcion',
    ];

    /**
     * Relación con las obligaciones.
     * Un área de compliance puede tener muchas obligaciones asociadas.
     */
    public function obligaciones()
    {
        return $this->hasMany(Obligacion::class, 'area_compliance_id');
    }
}