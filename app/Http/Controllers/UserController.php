<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
Use App\Models\Proceso;
Use App\Models\Especialista;
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
        return view('admin.user.index', compact('users'));
    }

    public function create()
    {
        // Código para cargar los datos necesarios para la creación
        
        return view('admin.user.create');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.usuarios.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $user->syncRoles($request->input('roles', []));
        return redirect()->route('admin.usuarios.index')->with('success', 'Roles actualizados exitosamente.');
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
        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }
    
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado exitosamente.');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $user->password = Hash::make('nuevacontraseña');
        $user->save();
        
        $user->notify(new ResetPasswordNotification());
        return redirect()->route('usuarios.index')->with('success', 'Contraseña reiniciada exitosamente. Se ha enviado un correo electrónico al usuario.');
    }
//Asignar Procesos
    public function asignarProcesos($id)
    {
        $user = User::findOrFail($id);
        // Aquí puedes obtener los procesos asignados al usuario y pasarlos a la vista
        $procesosAsignados = $user->procesos;
 
        $procesosDisponibles = Proceso::whereNotIn('id', $procesosAsignados->pluck('id'))
        ->paginate(15);
        
        return view('admin.user.asignarprocesos', compact('user', 'procesosAsignados', 'procesosDisponibles'));
        
    }

    public function listarProcesos($id)
    {
        $user = User::findOrFail($id);
      
        $procesosAsignados = $user->procesos;

              
        if ($user->hasRole('facilitador')) {
             return view('procesos.listarprocesos', compact('user','procesosAsignados'));
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

    
    return view('admin.user.asignarroles', compact('user', 'rolesAsignados', 'rolesDisponibles'));  
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

    
    return view('admin.user.asignarpermisos', compact('user', 'permisosAsignados', 'permisosDisponibles'));  
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
public function showEspecialistas()
{
   // $role = Role::findById(2);
    //$especialistas = $role->users()->get();
    $especialistas = Especialista::all();
    return response()->json($especialistas);
}

}
