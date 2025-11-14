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

    /**
     * The OUOs that belong to the user.
     */
    public function ouos(): BelongsToMany
    {
        return $this->belongsToMany(OUO::class, 'ouo_user', 'user_id', 'ouo_id')
                    ->using(OuoUser::class)
                    ->withPivot('role_in_ouo', 'activo', 'deleted_at');
    }

    /**
     * Get the OuoUser records associated with the user.
     */
    public function ouoUsers()
    {
        return $this->hasMany(OuoUser::class);
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
