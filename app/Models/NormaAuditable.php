<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NormaAuditable extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'normas_auditables';

    protected $fillable = [
        'na_nombre',
        'na_descripcion',
        'na_sistema'
    ];

    public function requisitos()
    {
        return $this->hasMany(NormaRequisito::class, 'nr_norma_id');
    }
}
