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
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles {
        hasPermissionTo as hasPermissionToOriginal;
        getAllPermissions as getAllPermissionsOriginal;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_iniciales',
        'user_cod_personal',
        'user_foto_url',
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
            ->withPivot('role_in_ouo', 'activo');
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
    // TODO: Descomentar cuando se cree el modelo HallazgoAsignacion
    // public function asignacionesRealizadas()
    // {
    //     // Corregido: Ahora apunta al modelo 'HallazgoAsignacion' y a la clave foránea correcta.
    //     return $this->hasMany(HallazgoAsignacion::class, 'user_asigna_id');
    // }


    /**
     * Relación con permisos denegados (Blacklist).
     */
    public function deniedPermissions(): BelongsToMany
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Permission::class, 'denied_permissions', 'user_id', 'permission_id');
    }

    /**
     * Override: Verifica si el usuario tiene un permiso, considerando la blacklist.
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        // 1. Check Blacklist FIRST
        if (is_string($permission)) {
            $permission = \Spatie\Permission\Models\Permission::findByName($permission, $guardName ?? $this->getDefaultGuardName());
        }

        if (is_int($permission)) {
            $permission = \Spatie\Permission\Models\Permission::findById($permission, $guardName ?? $this->getDefaultGuardName());
        }

        if ($this->deniedPermissions()->where('id', $permission->id)->exists()) {
            return false;
        }

        // 2. Fallback to default Spatie logic (Roles + Direct Permissions)
        return $this->hasPermissionToOriginal($permission, $guardName);
    }

    /**
     * Override: Obtiene todos los permisos, excluyendo los denegados.
     */
    public function getAllPermissions(): \Illuminate\Support\Collection
    {
        $permissions = $this->getAllPermissionsOriginal();
        $denied = $this->deniedPermissions()->pluck('id')->toArray();

        return $permissions->reject(function ($permission) use ($denied) {
            return in_array($permission->id, $denied);
        });
    }

    /**
     * Convierte el usuario a un array incluyendo sus roles.
     *
     * @return array
     */
    public function toArrayWithRoles(): array
    {
        return array_merge(
            $this->only(['id', 'name', 'user_iniciales', 'user_cod_personal', 'user_foto_url', 'email']),
            [
                'roles' => $this->getRoleNames(),
                'permissions' => $this->getAllPermissions()->pluck('name')
            ]
        );
    }
    /**
     * Obtiene los IDs de los procesos asociados a las OUOs del usuario donde la OUO es Propietaria o Delegada.
     * Útil para la lógica de "Facilitador".
     */
    public function getProcesosAsociadosIds(): array
    {
        // 1. Obtener las OUOs activas del usuario
        $userOuus = $this->ouos()->wherePivot('activo', 1)->get();

        $processIds = [];

        foreach ($userOuus as $ouo) {
            // 2. Para cada OUO, obtener los procesos donde es Propietario o Delegado
            $procesos = $ouo->procesos()
                ->wherePivot('propietario', 1)
                ->orWherePivot('delegado', 1)
                ->pluck('procesos.id')
                ->toArray();
            
            $processIds = array_merge($processIds, $procesos);
        }

        return array_unique($processIds);
    }

    /**
     * Obtiene los IDs de TODOS los procesos asociados a las OUOs del usuario.
     * Sin filtrar por rol (propietario/delegado).
     */
    public function getAllProcesosAsociadosIds(): array
    {
        $userOuus = $this->ouos()->wherePivot('activo', 1)->get();
        $processIds = [];

        foreach ($userOuus as $ouo) {
            $procesos = $ouo->procesos()
                ->pluck('procesos.id')
                ->toArray();
            
            $processIds = array_merge($processIds, $procesos);
        }

        return array_unique($processIds);
    }
}
