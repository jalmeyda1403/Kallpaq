<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditor extends Model
{
    use HasFactory;
    protected $table = 'auditores';
    protected $fillable = [
        'user_id',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
