<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
Use App\Models\Proceso;
Use App\Models\Especialista;
Use App\Models\Auditor;
use App\Models\OUO; // Added OUO model
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


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
        $user->delete();
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
 
        $procesosDisponibles = Proceso::whereNotIn('id', $procesosAsignados->pluck('id'))
        ->paginate(15);
        
        return view('user.asignarprocesos', compact('user', 'procesosAsignados', 'procesosDisponibles'));
        
    }

    public function listarProcesos($id)
    {
        $user = User::findOrFail($id);
      
        $procesosAsignados = $user->procesos;

              
        if ($user->hasRole('facilitador')) {
             return view('user.listarprocesos', compact('user','procesosAsignados'));
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
   // $role = Role::findById(2);
    //$especialistas = $role->users()->get();
    $auditores = Auditor::all();
     $auditores = $auditores->map(function ($auditor) {
            return [
                'id' => $auditor->user_id,
                'descripcion' => $auditor->nombres . '  ' . $auditor->apellido_paterno . '  ' . $auditor->apellido_materno,
            ];
        });
        return response()->json($auditores);
    }

    public function listUsers()
    {
        $users = User::select('id', 'name')->get();
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

        // Eager load roles and permissions
        $query->with('roles', 'permissions');

        // Pagination
        $perPage = $request->input('per_page', 10);
        $users = $query->paginate($perPage);

        return response()->json([
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name'),
                    'permissions' => $user->permissions->pluck('name'),
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
}

