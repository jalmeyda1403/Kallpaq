<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubAreaCompliance extends Model
{
    use HasFactory;
    protected $table = 'subareas_compliance';

    // Definimos los campos que se pueden llenar de forma masiva
    protected $fillable = [
        'area_compliance_id',
        'subarea_compliance_nombre',
        'subarea_compliance_descripcion',
    ];

    /**
     * Relación con las obligaciones.
     * Un área de compliance puede tener muchas obligaciones asociadas.
     */
    public function area_compliance()
    {
        return $this->hasMany(AreaCompliance::class, 'area_compliance_id');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'subarea_compliance_id');
    }
 
}