<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth; // Para obtener el usuario autenticado
use Illuminate\Support\Facades\DB; // Para transacciones
use Illuminate\Http\Request;
use App\Models\OUO;
use App\Models\OuoUser;
use App\Models\OuoUserMovimiento; // Importar el modelo OuoUserMovimiento
use App\Models\User;
use App\Models\Proceso;


class OUOController extends Controller
{
    public function buscar()
    {
        $ouos = OUO::select('id', 'ouo_nombre AS descripcion')->get();
        return response()->json($ouos);
    }

    /**
     * Lista todas las OUOs.
     * Utilizado por el componente Vue OuoUserAssignment.vue
     */
    public function listar()
    {
        $ouos = OUO::all();
        return response()->json($ouos);
    }

    /**
     * Lista los usuarios asignados a una OUO específica.
     */
    public function listOuoUsers(OUO $ouo)
    {
        $ouoUsers = $ouo->users()->with('user')->get()->map(function ($ouoUser) {
            return [
                'id' => $ouoUser->pivot->id, // Acceder al ID de la tabla pivote
                'user_id' => $ouoUser->id,
                'name' => $ouoUser->name,
                'email' => $ouoUser->email,
                'role_in_ouo' => $ouoUser->pivot->role_in_ouo,
            ];
        });
        return response()->json($ouoUsers);
    }

    /**
     * Asigna un usuario a una OUO con un rol específico.
     */
    public function attachOuoUser(Request $request, OUO $ouo)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_in_ouo' => 'required|in:titular,suplente,facilitador,miembro',
        ]);

        $userId = $request->input('user_id');
        $roleInOuo = $request->input('role_in_ouo');
        $cambiadoPor = Auth::id(); // Usuario autenticado

        // Verificar si la asignación ya existe
        $existingOuoUser = OuoUser::where('ouo_id', $ouo->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingOuoUser) {
            return response()->json(['message' => 'El usuario ya está asignado a esta OUO.'], 409);
        }

        DB::transaction(function () use ($ouo, $userId, $roleInOuo, $cambiadoPor) {
            // Crear la asignación en la tabla pivote ouo_user
            $ouo->users()->attach($userId, ['role_in_ouo' => $roleInOuo]);

            // Obtener el ID del registro recién creado en ouo_user
            $newOuoUser = OuoUser::where('ouo_id', $ouo->id)
                ->where('user_id', $userId)
                ->first();

            // Registrar el movimiento
            OuoUserMovimiento::create([
                'ouo_user_id' => $newOuoUser->id,
                'cambiado_por' => $cambiadoPor,
                'tipo_anterior' => null, // Es una nueva asignación
                'tipo_nuevo' => $roleInOuo,
                'motivo' => 'Asignación inicial de usuario a OUO',
                'fecha_cambio' => now(),
            ]);
        });

        return response()->json(['message' => 'Usuario asignado con éxito.']);
    }

    /**
     * Desvincula un usuario de una OUO.
     */
    public function detachOuoUser(OUO $ouo, User $user)
    {
        $cambiadoPor = Auth::id(); // Usuario autenticado

        $ouoUser = OuoUser::where('ouo_id', $ouo->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        DB::transaction(function () use ($ouo, $user, $ouoUser, $cambiadoPor) {
            // Registrar el movimiento antes de eliminar
            OuoUserMovimiento::create([
                'ouo_user_id' => $ouoUser->id,
                'cambiado_por' => $cambiadoPor,
                'tipo_anterior' => $ouoUser->role_in_ouo,
                'tipo_nuevo' => null, // Es una desvinculación
                'motivo' => 'Desvinculación de usuario de OUO',
                'fecha_cambio' => now(),
            ]);

            // Eliminar la asignación
            $ouo->users()->detach($user->id);
        });

        return response()->json(['message' => 'Usuario desvinculado con éxito.']);
    }

    /**
     * Actualiza el rol de un usuario en una OUO.
     */
    public function updateOuoUserRole(Request $request, OUO $ouo, User $user)
    {
        $request->validate([
            'role_in_ouo' => 'required|in:titular,suplente,facilitador,miembro',
        ]);

        $newRole = $request->input('role_in_ouo');
        $cambiadoPor = Auth::id();

        $ouoUser = OuoUser::where('ouo_id', $ouo->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $oldRole = $ouoUser->role_in_ouo;

        if ($oldRole === $newRole) {
            return response()->json(['message' => 'El rol no ha cambiado.'], 200);
        }

        DB::transaction(function () use ($ouoUser, $oldRole, $newRole, $cambiadoPor) {
            $ouoUser->update(['role_in_ouo' => $newRole]);

            OuoUserMovimiento::create([
                'ouo_user_id' => $ouoUser->id,
                'cambiado_por' => $cambiadoPor,
                'tipo_anterior' => $oldRole,
                'tipo_nuevo' => $newRole,
                'motivo' => 'Actualización de rol de usuario en OUO',
                'fecha_cambio' => now(),
            ]);
        });

        return response()->json(['message' => 'Rol de usuario actualizado con éxito.']);
    }

    /**
     * Lista todas las OUOs con detalles de padre y conteo de procesos para asignación.
     */
    public function indexForAssignment(Request $request)
    {
        $query = OUO::with('ouoPadre')
            ->withCount('procesos')
            ->withCount('users'); // Add this line

        // Filtering
        if ($request->has('search') && $request->input('search') != '') {
            $searchTerm = $request->input('search');
            $query->where('ouo_nombre', 'like', '%' . $searchTerm . '%');
        }

        if ($request->has('ouo_padre_id') && $request->input('ouo_padre_id') != '') {
            $query->where('ouo_padre', $request->input('ouo_padre_id'));
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $ouos = $query->paginate($perPage);

        return response()->json([
            'data' => $ouos->map(function ($ouo) {
                return [
                    'id' => $ouo->id,
                    'ouo_nombre' => $ouo->ouo_nombre,
                    'ouo_padre_nombre' => $ouo->ouoPadre ? $ouo->ouoPadre->ouo_nombre : 'N/A',
                    'procesos_count' => $ouo->procesos_count,
                    'users_count' => $ouo->users_count, // Add this line
                ];
            })->values(),
            'current_page' => $ouos->currentPage(),
            'last_page' => $ouos->lastPage(),
            'total' => $ouos->total(),
        ]);
    }

    /**
     * Obtiene una lista de OUOs con nivel_jerarquico 1 o 2 para el dropdown de padres.
     */
    public function getOuoPadresForDropdown()
    {
        $ouoPadres = OUO::whereIn('nivel_jerarquico', [1, 2])
            ->select('id', 'ouo_nombre')
            ->get();

        return response()->json($ouoPadres);
    }

    /**
     * Obtiene usuarios con el rol 'gestor' para un dropdown.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGestorUsersForDropdown()
    {
        $gestorRole = \Spatie\Permission\Models\Role::where('name', 'gestor')->first();

        if (!$gestorRole) {
            return response()->json([], 404); // Or handle as appropriate
        }

        $users = $gestorRole->users()->select('id', 'name', 'email')->orderBy('name', 'asc')->get();

        // Transform the users to match ModalHijo's expected format
        $formattedUsers = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'descripcion' => $user->name . ' - ' . $user->email,
            ];
        });

        return response()->json($formattedUsers);
    }

    public function getAssignedUsers(OUO $ouo)
    {
        $users = $ouo->users()->withPivot('role_in_ouo', 'activo')->wherePivot('deleted_at', null)->get();

        return response()->json($users);
    }

    /**
     * Obtiene los usuarios soft-deleted asignados a una OUO específica.
     *
     * @param  \App\Models\OUO  $ouo
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSoftDeletedUsers(OUO $ouo)
    {
        $ouoUsers = OuoUser::onlyTrashed()
            ->where('ouo_id', $ouo->id)
            ->with('user') // Eager load the related User model
            ->get();

        // Transform the results to match the expected format in the frontend
        return response()->json($ouoUsers->map(function ($ouoUser) {
            return [
                'id' => $ouoUser->user->id, // The user's ID
                'name' => $ouoUser->user->name,
                'email' => $ouoUser->user->email,
                'pivot' => [
                    'role_in_ouo' => $ouoUser->role_in_ouo,
                    'activo' => $ouoUser->activo,
                    'deleted_at' => $ouoUser->deleted_at,
                ],
            ];
        }));
    }

    public function updateUserPivot(Request $request, OUO $ouo, User $user)
    {
        $request->validate([
            'role_in_ouo' => 'required|in:titular,suplente,facilitador,miembro',
            'activo' => 'nullable|boolean',
        ]);

        $ouo->users()->updateExistingPivot($user->id, [
            'role_in_ouo' => $request->input('role_in_ouo'),
            'activo' => $request->boolean('activo'),
        ]);

        return response()->json(['message' => 'Asignación de usuario a OUO actualizada con éxito.']);
    }


    public function detachUser(OUO $ouo, User $user)
    {
        $ouoUser = OuoUser::where('ouo_id', $ouo->id)
            ->where('user_id', $user->id)
            ->firstOrFail();
        $ouoUser->update(
            ['activo' => 0,]
        );

        $ouoUser->delete(); // Perform soft delete

        return response()->json(['message' => 'Asignación de usuario a OUO eliminada (soft delete) con éxito.']);
    }


    public function attachUser(Request $request, OUO $ouo)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_in_ouo' => 'required|in:titular,suplente,facilitador,miembro',
            'activo' => 'nullable|boolean',
        ]);

        // Check if the user is already assigned (even if soft-deleted)
        $existingOuoUser = OuoUser::withTrashed()
            ->where('ouo_id', $ouo->id)
            ->where('user_id', $request->input('user_id'))
            ->first();

        if ($existingOuoUser) {
            // If exists and is soft-deleted, restore it
            if ($existingOuoUser->trashed()) {
                $existingOuoUser->restore();
                $existingOuoUser->update([
                    'role_in_ouo' => $request->input('role_in_ouo'),
                    'activo' => $request->boolean('activo', true), // Default to true if not provided
                ]);
                // Register the movement
                OuoUserMovimiento::create([
                    'ouo_user_id' => $existingOuoUser->id,
                    'cambiado_por' => Auth::id(),
                    'tipo_nuevo' => $request->input('role_in_ouo'),
                    'motivo' => 'Asignación de usuario a OUO restaurada y actualizada',
                    'fecha_cambio' => now(),
                ]);
                return response()->json(['message' => 'Asignación de usuario a OUO restaurada y actualizada con éxito.']);
            } else {
                return response()->json(['message' => 'El usuario ya está asignado a esta OUO.'], 409);
            }
        }

        $ouo->users()->attach($request->input('user_id'), [
            'role_in_ouo' => $request->input('role_in_ouo'),
            'activo' => $request->boolean('activo', true), // Default to true if not provided
        ]);

        // Get the newly created OuoUser pivot record
        $newOuoUser = OuoUser::where('ouo_id', $ouo->id)
            ->where('user_id', $request->input('user_id'))
            ->first();

        // Register the movement
        OuoUserMovimiento::create([
            'ouo_user_id' => $newOuoUser->id,
            'cambiado_por' => Auth::id(),
            'tipo_nuevo' => $request->input('role_in_ouo'),
            'motivo' => 'Asignación inicial de usuario a OUO',
            'fecha_cambio' => now(),
        ]);
        return response()->json(['message' => 'Usuario asignado a OUO con éxito.']);
    }
    public function updateProcessPivot(Request $request, OUO $ouo, Proceso $proceso)
    {
        $request->validate([
            'propietario' => 'nullable|boolean',
            'delegado' => 'nullable|boolean',
            'ejecutor' => 'nullable|boolean',
            'sgc' => 'nullable|boolean',
            'sgas' => 'nullable|boolean',
            'sgcm' => 'nullable|boolean',
            'sgsi' => 'nullable|boolean',
            'sgco' => 'nullable|boolean',
        ]);

        $ouo->procesos()->updateExistingPivot($proceso->id, [
            'propietario' => $request->boolean('propietario'),
            'delegado' => $request->boolean('delegado'),
            'ejecutor' => $request->boolean('ejecutor'),
            'sgc' => $request->boolean('sgc'),
            'sgas' => $request->boolean('sgas'),
            'sgcm' => $request->boolean('sgcm'),
            'sgsi' => $request->boolean('sgsi'),
            'sgco' => $request->boolean('sgco'),
        ]);

        return response()->json(['message' => 'Proceso de OUO actualizado con éxito.']);
    }

    public function getAssignedProcesses(OUO $ouo)
    {
        $procesos = $ouo->procesos()->select('procesos.id', 'procesos.cod_proceso', 'procesos.proceso_nombre')
            ->withPivot('propietario', 'delegado', 'ejecutor', 'sgc', 'sgas', 'sgcm', 'sgsi', 'sgco')
            ->get();

        return response()->json($procesos);
    }        /**
             * Sincroniza los procesos asignados a una OUO.
             * Esto incluye adjuntar nuevos, desvincular los eliminados y actualizar los existentes.
             *
             * @param  \Illuminate\Http\Request  $request
             * @param  \App\Models\OUO  $ouo
             * @return \Illuminate\Http\JsonResponse
             */
    public function syncProcesses(Request $request, OUO $ouo)
    {
        $request->validate([
            'procesos' => 'array',
            'procesos.*.id' => 'required|exists:procesos,id',
            'procesos.*.propietario' => 'nullable|boolean',
            'procesos.*.delegado' => 'nullable|boolean',
            'procesos.*.ejecutor' => 'nullable|boolean',
            'procesos.*.sgc' => 'nullable|boolean',
            'procesos.*.sgas' => 'nullable|boolean',
            'procesos.*.sgcm' => 'nullable|boolean',
            'procesos.*.sgsi' => 'nullable|boolean',
            'procesos.*.sgco' => 'nullable|boolean',
        ]);

        $procesosToSync = [];
        foreach ($request->input('procesos', []) as $procesoData) {
            $procesosToSync[$procesoData['id']] = [
                'propietario' => $procesoData['propietario'] ?? false,
                'delegado' => $procesoData['delegado'] ?? false,
                'ejecutor' => $procesoData['ejecutor'] ?? false,
                'sgc' => $procesoData['sgc'] ?? false,
                'sgas' => $procesoData['sgas'] ?? false,
                'sgcm' => $procesoData['sgcm'] ?? false,
                'sgsi' => $procesoData['sgsi'] ?? false,
                'sgco' => $procesoData['sgco'] ?? false,
            ];
        }

        $ouo->procesos()->sync($procesosToSync);
        return response()->json(['message' => 'Procesos de OUO sincronizados con éxito.']);
    }
}
