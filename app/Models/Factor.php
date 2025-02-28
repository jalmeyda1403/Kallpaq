<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factor extends Model
{
    use HasFactory;
    protected $table = 'factores';
    protected $fillable = ['nombre', 'valor', 'estado', 'inactivate_at'];
    public function riesgos()
    {
        return $this->hasMany(Riesgo::class);
    }

}
