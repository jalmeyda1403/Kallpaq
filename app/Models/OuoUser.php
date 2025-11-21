<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot; // Import Pivot
use Illuminate\Database\Eloquent\SoftDeletes; // Import SoftDeletes

class OuoUser extends Pivot // Extend Pivot
{
    use HasFactory;

    protected $table = 'ouo_user'; // Specify the pivot table name

    protected $fillable = [
        'ouo_id',
        'user_id',
        'role_in_ouo',
        'activo', // Add activo to fillable
    ];

    /**
     * Get the user that owns the OuoUser.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the OUO that owns the OuoUser.
     */
    public function ouo()
    {
        return $this->belongsTo(OUO::class);
    }
}
