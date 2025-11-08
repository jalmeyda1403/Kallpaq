<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'sigla',
        'foto_url',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    //Obtiene los procesos del usuario
    public function procesos(): BelongsToMany
    {
        return $this->belongsToMany(Proceso::class);
    }
    //Obtiene los hallazgos donde este usuario es el especialista ACTUALMENTE asignado
    public function hallazgosAsignados()
    {
        return $this->hasMany(Hallazgo::class, 'especialista_id');
    }

    //Obtiene el historial de asignaciones que este usuario ha REALIZADO a otros
    public function asignacionesRealizadas()
    {
        // Corregido: Ahora apunta al modelo 'HallazgoAsignacion' y a la clave foránea correcta.
        return $this->hasMany(HallazgoAsignacion::class, 'user_asigna_id');
    }
}
