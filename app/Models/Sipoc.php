<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sipoc extends Model
{
    use HasFactory;
    protected $fillable = ['proveedores', 'entradas',  'clientes', 'proceso_id'];

    
    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }
}
