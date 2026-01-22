<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Proceso;
use App\Models\Especialista;
use App\Models\Auditor;
use App\Models\OUO; // Added OUO model
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password; // Added Password facade
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;


class UserController extends Controller
{
    use HasRoles;
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        $users = User::all();
        return view('user.index', compact('users'));
    }

    public function create()
    {
        // Código para cargar los datos necesarios para la creación

        return view('user.create');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $user->syncRoles($request->input('roles', []));
        return redirect()->route('user.index')->with('success', 'Roles actualizados exitosamente.');
    }

    public function store(Request $request)
    {
        // Validación de los datos del formulario
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:6',
        ]);

        // Crear el nuevo usuario
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        // Redireccionar a la vista de gestión de usuarios
        return redirect()->route('user.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function destroy(User $user)
    {
        User::where('id', '=', $user->id, 'and')->delete();
        return redirect()->route('user.index')->with('success', 'Usuario eliminado exitosamente.');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $user->password = Hash::make('nuevacontraseña');
        $user->save();

        $user->notify(new ResetPasswordNotification());
        return redirect()->route('user.index')->with('success', 'Contraseña reiniciada exitosamente. Se ha enviado un correo electrónico al usuario.');
    }
    //Asignar Procesos
    public function asignarProcesos($id)
    {
        $user = User::findOrFail($id);
        // Aquí puedes obtener los procesos asignados al usuario y pasarlos a la vista
        $procesosAsignados = $user->procesos;

        $procesosDisponibles = Proceso::whereNotIn('id', $procesosAsignados->pluck('id'), 'and', false)
            ->paginate(15);

        return view('user.asignarprocesos', compact('user', 'procesosAsignados', 'procesosDisponibles'));

    }

    public function listarProcesos($id)
    {
        $user = User::findOrFail($id);

        $procesosAsignados = $user->procesos;


        if ($user->hasRole('facilitador')) {
            return view('user.listarprocesos', compact('user', 'procesosAsignados'));
        }



    }

    public function guardarProcesos(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $procesosSeleccionados = $request->input('proceso-id', []);

        // Asignar los procesos seleccionados al usuario
        $user->procesos()->attach($procesosSeleccionados);

        return redirect()->back()->with('success', 'Procesos asignados correctamente.');
    }

    public function eliminarProceso($id, $proceso_id)
    {
        $user = User::findOrFail($id);
        $user->procesos()->detach($proceso_id);
        return redirect()->back()->with('success', 'Proceso eliminado correctamente.');
    }

    //Asignar Roles
    public function asignarRoles($id)
    {
        $user = User::findOrFail($id);
        // Aquí puedes obtener los procesos asignados al usuario y pasarlos a la vista
        $rolesAsignados = $user->roles;
        $rolesDisponibles = Role::all();


        return view('user.asignarroles', compact('user', 'rolesAsignados', 'rolesDisponibles'));
    }

    public function guardarRoles(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $roles = $request->input('rol-id', []);

        // Asignar los procesos seleccionados al usuario
        $user->syncRoles($roles);

        return redirect()->back()->with('success', 'Roles asignados correctamente.');
    }

    //Asignar Permisos
    public function asignarPermisos($id)
    {
        $user = User::findOrFail($id);
        // Aquí puedes obtener los procesos asignados al usuario y pasarlos a la vista
        $permisosAsignados = $user->permissions;
        $permisosDisponibles = Permission::all();


        return view('user.asignarpermisos', compact('user', 'permisosAsignados', 'permisosDisponibles'));
    }

    public function guardarPermisos(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $permisos = $request->input('permiso-id', []);
        // Asignar los procesos seleccionados al usuario
        $user->syncPermissions($permisos);

        return redirect()->back()->with('success', 'Permisos asignados correctamente.');
    }

    //Mostrar Especialistas

    public function showAuditores()
    {
        // Obtener todos los auditores con su información de usuario
        $auditores = Auditor::with('user')->get();

        $auditores = $auditores->map(function ($auditor) {
            return [
                'id' => $auditor->user_id,
                'descripcion' => $auditor->user ? $auditor->user->name : 'Usuario no encontrado', // Usar el nombre del usuario
            ];
        });

        return response()->json($auditores);
    }

    public function listUsers(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            if ($request->role === 'propietario') {
                // Filtrar usuarios que tienen el rol de 'owner' O 'propietario' en alguna OUO usando JOIN explícito
                $query->join('ouo_user', 'users.id', '=', 'ouo_user.user_id')
                    ->where(function ($q) {
                        $q->where('ouo_user.role_in_ouo', 'owner')
                            ->orWhere('ouo_user.role_in_ouo', 'propietario');
                    });
            } else {
                $query->role($request->role);
            }
        }

        // Seleccionar id y name como descripcion para compatibilidad con ModalHijo
        $users = $query->select('users.id', 'users.name as descripcion')
            ->distinct()
            ->orderBy('users.name')
            ->get();

        return response()->json($users);
    }

    /**
     * API endpoint for fetching paginated user data with roles and permissions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiIndex(Request $request)
    {
        $query = User::query();

        // Apply search filter
        if ($request->has('search') && $request->input('search') != '') {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        // Apply role filter
        if ($request->has('role') && $request->input('role') != '') {
            $query->role($request->input('role'));
        }

        // Sorting
        $sortField = $request->input('sort_field', 'id');
        $sortOrder = $request->input('sort_order', 'desc');

        // Validate sort field to prevent SQL injection
        $allowedSorts = ['id', 'name', 'email', 'user_cod_personal', 'user_iniciales'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->orderBy('id', 'desc');
        }

        // Eager load roles and permissions
        $query->with('roles', 'permissions');

        // Pagination
        $perPage = $request->input('per_page', 10);
        $users = $query->paginate($perPage);

        return response()->json([
            'data' => collect($users->items())->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name'),
                    'permissions' => $user->permissions->pluck('name'),
                    'user_cod_personal' => $user->user_cod_personal,
                    'user_iniciales' => $user->user_iniciales,
                    'user_foto_url' => $user->user_foto_url ? asset($user->user_foto_url) : null,
                ];
            })->values(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'total' => $users->total(),
        ]);
    }

    /**
     * List users assigned to a specific OUO.
     *
     * @param  \App\Models\OUO  $ouo
     * @return \Illuminate\Http\JsonResponse
     */
    public function listOuoUsers(OUO $ouo)
    {
        $ouoUsers = $ouo->users()->withPivot('role_in_ouo')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_in_ouo' => $user->pivot->role_in_ouo,
            ];
        });

        return response()->json($ouoUsers);
    }

    /**
     * Attach a user to an OUO with a specific role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OUO  $ouo
     * @return \Illuminate\Http\JsonResponse
     */
    public function attachOuoUser(Request $request, OUO $ouo)
    {
        $validatedData = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'role_in_ouo' => ['required', 'string', 'in:owner,titular,suplente,facilitador,member'], // Define your roles
        ]);

        // Check if the user is already attached to this OUO
        if ($ouo->users()->where('user_id', $validatedData['user_id'])->exists()) {
            return response()->json(['message' => 'El usuario ya está asociado a esta OUO.'], 409);
        }

        $ouo->users()->attach($validatedData['user_id'], [
            'role_in_ouo' => $validatedData['role_in_ouo'],
        ]);

        return response()->json(['message' => 'Usuario asociado a la OUO con éxito.'], 200);
    }

    /**
     * Detach a user from an OUO.
     *
     * @param  \App\Models\OUO  $ouo
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function detachOuoUser(OUO $ouo, User $user)
    {
        $ouo->users()->detach($user->id);

        return response()->json(['message' => 'Usuario desasociado de la OUO con éxito.'], 200);
    }

    public function storeApi(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'user_cod_personal' => 'nullable|string|unique:users',
            'user_iniciales' => 'nullable|string|max:10|unique:users',
            'user_foto_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'roles' => 'array' // If we want to assign roles on creation
        ]);

        $user = new User([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'user_cod_personal' => !empty($validatedData['user_cod_personal']) ? $validatedData['user_cod_personal'] : null,
            'user_iniciales' => !empty($validatedData['user_iniciales']) ? $validatedData['user_iniciales'] : null,
        ]);

        if ($request->hasFile('user_foto_url')) {
            $file = $request->file('user_foto_url');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('photo'), $filename);
            $user->user_foto_url = 'photo/' . $filename;
        }

        $user->save();

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return response()->json(['message' => 'Usuario creado correctamente', 'user' => $user], 201);
    }

    public function updateApi(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'user_cod_personal' => ['nullable', 'string', Rule::unique('users')->ignore($user->id)],
            'user_iniciales' => ['nullable', 'string', 'max:10', Rule::unique('users')->ignore($user->id)],
            'user_foto_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->user_cod_personal = $validatedData['user_cod_personal'];
        $user->user_iniciales = $validatedData['user_iniciales'];

        if ($request->hasFile('user_foto_url')) {
            // Delete old photo if exists
            if ($user->user_foto_url && file_exists(public_path($user->user_foto_url))) {
                unlink(public_path($user->user_foto_url));
            }

            $file = $request->file('user_foto_url');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('photo'), $filename);
            $user->user_foto_url = 'photo/' . $filename;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save();

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return response()->json(['message' => 'Usuario actualizado correctamente', 'user' => $user]);
    }

    public function destroyApi(User $user)
    {
        User::where('id', '=', $user->id, 'and')->delete();
        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new UsersImport, $request->file('file'));
            return response()->json(['message' => 'Usuarios importados correctamente']);
        } catch (\Exception $e) {
            // Log error
            return response()->json(['message' => 'Error al importar: ' . $e->getMessage()], 500);
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\UsersTemplateExport, 'usuarios_template.xlsx');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // We can use the Password broker to send the link
        $status = \Illuminate\Support\Facades\Password::sendResetLink(
            $request->only('email')
        );

        if ($status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)]);
        }

        return response()->json(['message' => __($status)], 422);
    }

    public function generateUniqueInitials(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        $name = $request->input('name');

        // Generate base initials
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        // Limit to reasonable length (e.g. 3 chars) if too long? 
        // User request didn't specify, but usually initials are short. 
        // Let's keep all first letters for now, or maybe max 4.
        $initials = substr($initials, 0, 4);

        $baseInitials = $initials;
        $counter = 1;

        // Check uniqueness and append number if needed
        while (User::where('user_iniciales', '=', $initials, 'and')->exists()) {
            $initials = $baseInitials . $counter;
            $counter++;
        }

        return response()->json(['initials' => $initials]);
    }

    public function getRolesApi()
    {
        $roles = Role::select(['id', 'name'])->get();
        return response()->json($roles);
    }

    public function assignRolesApi(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'array'
        ]);

        $user->syncRoles($request->input('roles', []));

        return response()->json(['message' => 'Roles asignados correctamente']);
    }

    /**
     * Get user specific permissions analysis.
     */
    public function getUserPermissions($id)
    {
        $user = User::findOrFail($id);
        $allPermissions = Permission::all();

        // Permisos otorgados por roles (sin filtrar blacklist aún)
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('id')->toArray();

        // Permisos otorgados directamente
        $directPermissions = $user->getDirectPermissions()->pluck('id')->toArray();

        // Permisos denegados explícitamente (blacklist)
        $deniedPermissions = $user->deniedPermissions()->pluck('id')->toArray();

        return response()->json([
            'all_permissions' => $allPermissions,
            'role_permissions' => $rolePermissions,
            'direct_permissions' => $directPermissions,
            'denied_permissions' => $deniedPermissions
        ]);
    }

    /**
     * Sync user specific permissions (Direct & Denied).
     */
    public function syncUserPermissions(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $desiredPermissions = $request->input('permissions', []); // Array of ID strings

        // Get permissions from current roles
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('name')->toArray();

        // Lists to sync
        $directToAdd = [];
        $deniedToAdd = [];

        // 1. Analyze what needs to be Direct (User wants it, but Role doesn't have it)
        $allSystemPermissions = Permission::all();

        foreach ($allSystemPermissions as $p) {
            $wants = in_array($p->name, $desiredPermissions);
            $hasByRole = in_array($p->name, $rolePermissions);

            if ($wants && !$hasByRole) {
                // Needs direct permission
                $directToAdd[] = $p->id;
            } elseif (!$wants && $hasByRole) {
                // User wants to Hide it, but Role gives it -> Blacklist it
                $deniedToAdd[] = $p->id;
            }
        }

        // Sync Direct Permissions
        // We use syncPermissions but ONLY for the ones identified as Needed Direct.
        // This clears previous direct permissions that are no longer needed.
        // However, Spatie syncPermissions replaces ALL direct permissions.
        // So directToAdd is exactly what we want.
        $user->syncPermissions($directToAdd);

        // Sync Denied Permissions (Blacklist)
        $user->deniedPermissions()->sync($deniedToAdd);

        return response()->json([
            'message' => 'Permisos de usuario actualizados correctamente.',
            'debug_direct' => $directToAdd,
            'debug_denied' => $deniedToAdd
        ]);
    }
    /**
     * Sync 'Especialista' table with users having 'Especialista' role.
     */
    public function syncSpecialists(Request $request)
    {
        try {
            // 1. Get all users with 'Especialista' role
            $especialistaUsers = User::role('especialista')->get();
            $especialistaUserIds = $especialistaUsers->pluck('id')->toArray();

            // 2. Active/Create specialists
            foreach ($especialistaUsers as $user) {
                // Check individually to avoid overwriting existing 'cargo' with a default if it exists
                $especialista = Especialista::where('user_id', '=', $user->id, 'and')->first();

                if ($especialista) {
                    $especialista->update([
                        'estado' => 1,
                        'inactived_at' => null
                    ]);
                } else {
                    Especialista::create([
                        'user_id' => $user->id,
                        'estado' => 1,
                        'cargo' => 'Especialista', // Default cargo for new specialists
                        'inactived_at' => null
                    ]);
                }
            }

            // 3. Soft delete (deactivate) specialists who no longer have the role
            // We find specialists whose user_id is NOT in the list of current specialist users
            Especialista::whereNotIn('user_id', $especialistaUserIds, 'and')
                ->update([
                    'estado' => 0,
                    'inactived_at' => now()
                ]);

            return response()->json([
                'message' => 'Sincronización de especialistas completada.',
                'count_active' => count($especialistaUserIds),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al sincronizar especialistas: ' . $e->getMessage()
            ], 500);
        }
    }
    public function storeMassiveApi(Request $request)
    {
        $request->validate([
            'users' => 'required|array|min:1',
            'users.*.name' => 'required|string|max:255',
            'users.*.email' => 'required|email|unique:users,email',
            'users.*.user_cod_personal' => 'nullable|string|unique:users,user_cod_personal',
            'users.*.user_iniciales' => 'nullable|string|max:10|unique:users,user_iniciales',
        ]);

        $createdUsers = [];
        $defaultPassword = Hash::make('password123');

        foreach ($request->users as $userData) {
            // Normalizar Nombre
            $name = collect(explode(' ', strtolower(trim($userData['name']))))
                ->map(fn($word) => ucfirst($word))
                ->filter()
                ->implode(' ');

            // Normalizar Email
            $email = strtolower(trim($userData['email']));

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $defaultPassword,
                'user_cod_personal' => $userData['user_cod_personal'] ?? null,
                'user_iniciales' => $userData['user_iniciales'] ?? null,
            ]);

            $createdUsers[] = $user;
        }

        return response()->json([
            'message' => count($createdUsers) . ' usuarios creados correctamente.',
            'users' => $createdUsers
        ], 201);
    }

    /**
     * Send a password reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLink(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // We can use the Password broker to send the link
        // We pass the email of the user we want to send the link to
        $status = Password::broker()->sendResetLink(
            ['email' => $user->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)]);
        }

        return response()->json(['message' => __($status)], 422);
    }
}

