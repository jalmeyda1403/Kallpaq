<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaEspecifica;
use App\Models\AuditoriaAgenda;
use App\Models\AuditoriaEquipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditoriaEspecificaController extends Controller
{
    public function index($pa_id)
    {
        // Listar auditorías de un programa específico
        $auditorias = AuditoriaEspecifica::where('pa_id', $pa_id)
            ->with(['equipo.usuario', 'agenda'])
            ->get();
        return response()->json($auditorias);
    }

    public function show($id)
    {
        $auditoria = AuditoriaEspecifica::with(['equipo.usuario', 'agenda', 'proceso'])->findOrFail($id);
        return response()->json($auditoria);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pa_id' => 'required|exists:programa_auditoria,id',
            'ae_codigo' => 'required',
            'ae_objetivo' => 'required',
            'ae_fecha_inicio' => 'required|date',
            'ae_fecha_fin' => 'required|date|after_or_equal:ae_fecha_inicio',
        ]);

        $auditoria = AuditoriaEspecifica::create($request->all());
        return response()->json($auditoria, 201);
    }

    public function update(Request $request, $id)
    {
        $auditoria = AuditoriaEspecifica::findOrFail($id);
        $auditoria->update($request->all());
        return response()->json($auditoria);
    }

    public function destroy($id)
    {
        AuditoriaEspecifica::destroy($id);
        return response()->json(['message' => 'Eliminado correctamente']);
    }

    // Métodos para Agenda
    public function updateAgenda(Request $request, $ae_id)
    {
        // Recibe un array de items de agenda
        $items = $request->input('agenda', []);

        // Estrategia simple: borrar y recrear (o usar updateOrCreate si tienen ID)
        // Para Gantt/Timeline suele ser más fácil manejar todo el set.

        DB::transaction(function () use ($ae_id, $items) {
            AuditoriaAgenda::where('ae_id', $ae_id)->delete();

            foreach ($items as $item) {
                AuditoriaAgenda::create([
                    'ae_id' => $ae_id,
                    'aea_fecha' => $item['aea_fecha'],
                    'aea_hora_inicio' => $item['aea_hora_inicio'],
                    'aea_hora_fin' => $item['aea_hora_fin'],
                    'aea_actividad' => $item['aea_actividad'],
                    'aea_auditado' => $item['aea_auditado'] ?? null,
                    'aea_auditor' => $item['aea_auditor'] ?? null,
                    'aea_requisito' => $item['aea_requisito'] ?? null,
                    'aea_lugar' => $item['aea_lugar'] ?? null,
                ]);
            }
        });

        return response()->json(['message' => 'Agenda actualizada']);
    }

    // Métodos para Equipo
    public function updateEquipo(Request $request, $ae_id)
    {
        $equipo = $request->input('equipo', []);

        DB::transaction(function () use ($ae_id, $equipo) {
            AuditoriaEquipo::where('ae_id', $ae_id)->delete();

            foreach ($equipo as $miembro) {
                AuditoriaEquipo::create([
                    'ae_id' => $ae_id,
                    'auditor_id' => $miembro['auditor_id'],
                    'aeq_rol' => $miembro['aeq_rol'],
                    'aeq_horas_planificadas' => $miembro['aeq_horas_planificadas'] ?? 0,
                ]);
            }
        });

        return response()->json(['message' => 'Equipo actualizado']);
    }
}
