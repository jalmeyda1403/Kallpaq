<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {

    }

    public function apiIndex(Request $request)
    {
        $query = Role::query();

        if ($request->has('search') && $request->input('search') != '') {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        // Sorting
        $sortField = $request->input('sort_field', 'id');
        $sortOrder = $request->input('sort_order', 'desc');
        $allowedSorts = ['id', 'name', 'created_at'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->orderBy('id', 'desc');
        }

        $perPage = $request->input('per_page', 10);
        $roles = $query->paginate($perPage);

        return response()->json($roles);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'guard_name' => ['nullable', 'string'],
        ]);

        $role = Role::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'] ?? null,
            'guard_name' => $validatedData['guard_name'] ?? 'web',
        ]);

        return response()->json(['message' => 'Rol creado correctamente', 'role' => $role], 201);
    }

    public function update(Request $request, Role $role)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $role->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'] ?? null,
        ]);

        return response()->json(['message' => 'Rol actualizado correctamente', 'role' => $role]);
    }

    public function destroy(Role $role)
    {
        Role::where('id', '=', $role->id, 'and')->delete();
        return response()->json(['message' => 'Rol eliminado correctamente']);
    }

    public function permissions()
    {
        $permissions = \Spatie\Permission\Models\Permission::all()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'description' => $p->description ?? $p->name
            ];
        });

        return response()->json($permissions);
    }

    public function rolePermissions($id)
    {
        $role = Role::findOrFail($id);
        return response()->json($role->permissions->pluck('name'));
    }

    public function syncPermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $permissions = $request->input('permissions', []);

        $role->syncPermissions($permissions);

        return response()->json(['message' => 'Permisos sincronizados correctamente']);
    }
}
