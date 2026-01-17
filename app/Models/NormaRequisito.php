<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NormaRequisito extends Model
{
    use HasFactory;

    protected $table = 'norma_requisitos';
    protected $primaryKey = 'nr_id';

    protected $fillable = [
        'nr_norma_id',
        'nr_numeral',
        'nr_denominacion',
        'nr_detalle'
    ];

    public function norma()
    {
        return $this->belongsTo(NormaAuditable::class, 'nr_norma_id');
    }
}
