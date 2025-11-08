<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequerimientoEvidencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'requerimiento_id',
        'file_name',
        'file_path',
    ];

    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }
}
