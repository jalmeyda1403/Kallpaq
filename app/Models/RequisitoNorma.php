<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitoNorma extends Model
{
    use HasFactory;

    protected $table = 'requisitos_norma';

    protected $fillable = [
        'norma_id',
        'numeral',
        'denominacion',
        'detalle'
    ];

    public function norma()
    {
        return $this->belongsTo(NormaAuditable::class, 'norma_id');
    }
}
