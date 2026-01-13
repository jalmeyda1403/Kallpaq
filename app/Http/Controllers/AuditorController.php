<?php

namespace App\Http\Controllers;

use App\Models\Auditor;
use App\Models\User;
use Illuminate\Http\Request;

class AuditorController extends Controller
{
    public function index(Request $request)
    {
        $query = Auditor::with('user');

        if ($request->has('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('user', function ($q) use ($buscar) {
                $q->where('name', 'like', "%$buscar%")
                    ->orWhere('email', 'like', "%$buscar%");
            });
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:auditores,user_id',
        ], [
            'user_id.unique' => 'Este usuario ya está registrado como auditor.',
        ]);

        $auditor = Auditor::create([
            'user_id' => $request->user_id,
        ]);

        return response()->json($auditor->load('user'), 201);
    }

    public function show($id)
    {
        $auditor = Auditor::with('user')->findOrFail($id);

        $historial = \App\Models\AuditoriaEquipo::where('auditor_id', $auditor->user_id)
            ->with(['auditoria.procesos'])
            ->get()
            ->map(function ($equipo) {
                return [
                    'id' => $equipo->auditoria->id,
                    'codigo' => $equipo->auditoria->ae_codigo,
                    'objetivo' => $equipo->auditoria->ae_objetivo,
                    'rol' => $equipo->aeq_rol,
                    'fecha_inicio' => $equipo->auditoria->ae_fecha_inicio,
                    'fecha_fin' => $equipo->auditoria->ae_fecha_fin,
                    'procesos' => $equipo->auditoria->procesos->pluck('proceso_nombre')->implode(', ')
                ];
            });

        return response()->json([
            'auditor' => $auditor,
            'historial' => $historial
        ]);
    }

    public function update(Request $request, $id)
    {
        $auditor = Auditor::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id|unique:auditores,user_id,' . $id,
        ], [
            'user_id.unique' => 'Este usuario ya está registrado como auditor.',
        ]);

        $auditor->update([
            'user_id' => $request->user_id,
        ]);

        return response()->json($auditor->load('user'));
    }

    public function destroy($id)
    {
        $auditor = Auditor::findOrFail($id);
        $auditor->delete();

        return response()->json(['message' => 'Auditor eliminado (soft delete)']);
    }

    public function getAvailableUsers(Request $request)
    {
        $excludeId = $request->query('exclude_auditor_id');

        $query = Auditor::query();
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        $auditorUserIds = $query->pluck('user_id')->toArray();

        $users = User::whereNotIn('id', $auditorUserIds)->get();

        return response()->json($users);
    }
}
