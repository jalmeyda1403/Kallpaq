<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisito extends Model
{
    use HasFactory;
    protected $fillable = ['salida_id', 'requisito','documento','fecha_requisito'];

    public function salida()
    {
        return $this->belongsTo(Salida::class);
    }
}
