<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaEspecifica;
use App\Models\AuditoriaAgenda;
use App\Models\AuditoriaEquipo;
use App\Models\AuditoriaEvaluacion;
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
        $auditoria = AuditoriaEspecifica::with(['equipo.usuario', 'agenda', 'procesos', 'evaluaciones.evaluador', 'evaluaciones.evaluado'])->findOrFail($id);
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

        $data = $request->all();
        // Default cycle is 1
        $data['ae_ciclo'] = 1;

        $auditoria = AuditoriaEspecifica::create($data);

        // Sync processes if provided
        if ($request->has('procesos')) {
            $auditoria->procesos()->sync($request->input('procesos', []));
        }

        return response()->json($auditoria, 201);
    }

    public function update(Request $request, $id)
    {
        $auditoria = AuditoriaEspecifica::with('programa')->findOrFail($id);

        // Versioning Logic
        if (in_array($auditoria->programa->pa_estado, ['Aprobada', 'Ejecutada'])) {
            // Increment Program Version
            $auditoria->programa->increment('pa_version');

            // Increment Cycle if not explicitly set (or logic to just bump it)
            // We only bump cycle if this is a "Change" to the audit content, which update() implies.
            // We avoid double bumping if the request already sends the new cycle? 
            // Better to handle it server side.
            $auditoria->ae_ciclo = $auditoria->ae_ciclo + 1;
        }

        $auditoria->update($request->except(['ae_ciclo'])); // Prevent manual override of cycle if we handle it here? Or allow it.
        // Actually, update() call above might overwrite ae_ciclo if in request. 
        // Let's force the incremented value if versioning triggered.
        if (in_array($auditoria->programa->pa_estado, ['Aprobada', 'Ejecutada'])) {
            $auditoria->save(); // Save the cycle change
        }

        // Sync processes if provided
        if ($request->has('procesos')) {
            $auditoria->procesos()->sync($request->input('procesos', []));
        }

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
                    'aea_tipo' => $item['aea_tipo'] ?? 'ejecucion',
                ]);
            }
        });

        $this->updateCalculatedHours($ae_id);

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
                    'aeq_horas_ejecutadas' => $miembro['aeq_horas_ejecutadas'] ?? 0,
                ]);
            }
        });

        return response()->json(['message' => 'Equipo actualizado']);
    }

    private function updateCalculatedHours($ae_id)
    {
        $audit = AuditoriaEspecifica::with(['agenda', 'equipo.usuario'])->find($ae_id);
        if (!$audit)
            return;

        // Map to store hours per auditor_id
        // We use the ID of the AuditoriaEquipo record or the auditor_id? 
        // The team member is unique by auditor_id in a specific audit.
        $hoursMap = []; // auditor_id => hours

        foreach ($audit->equipo as $member) {
            $hoursMap[$member->auditor_id] = 0;
        }

        foreach ($audit->agenda as $item) {
            if (!$item->aea_hora_inicio || !$item->aea_hora_fin)
                continue;

            $start = \Carbon\Carbon::parse($item->aea_hora_inicio);
            $end = \Carbon\Carbon::parse($item->aea_hora_fin);

            // Ensure positive duration
            $minutes = $end->diffInMinutes($start);
            $duration = $minutes / 60;

            if (in_array($item->aea_tipo, ['apertura', 'cierre', 'gabinete'])) {
                // Add to all members
                foreach ($audit->equipo as $member) {
                    $hoursMap[$member->auditor_id] += $duration;
                }
            } else {
                // Execution
                if ($item->aea_auditor) {
                    foreach ($audit->equipo as $member) {
                        $memberName = $member->usuario->name ?? '';
                        if (stripos($item->aea_auditor, $memberName) !== false) {
                            $hoursMap[$member->auditor_id] += $duration;
                        }
                    }
                }
            }
        }

        foreach ($audit->equipo as $member) {
            $newHours = $hoursMap[$member->auditor_id] ?? 0;
            if ($member->aeq_horas_ejecutadas != $newHours) {
                $member->aeq_horas_ejecutadas = $newHours;
                $member->save();
            }
        }
    }

    // Evaluación de Auditores
    public function storeEvaluacion(Request $request, $ae_id)
    {
        $request->validate([
            'evaluador_id' => 'required|exists:users,id',
            'evaluado_id' => 'required|exists:users,id',
            'aev_rol_evaluado' => 'required',
            'aev_promedio' => 'required|numeric',
        ]);

        $evaluacion = AuditoriaEvaluacion::create([
            'ae_id' => $ae_id,
            'evaluador_id' => $request->evaluador_id,
            'evaluado_id' => $request->evaluado_id,
            'aev_rol_evaluado' => $request->aev_rol_evaluado,
            'aev_criterios' => $request->aev_criterios, // JSON
            'aev_promedio' => $request->aev_promedio,
            'aev_comentario' => $request->aev_comentario,
        ]);

        return response()->json($evaluacion, 201);
    }
    public function getNextSequence(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $type = $request->input('type', 'Interna');

        $count = AuditoriaEspecifica::where('ae_tipo', $type)
            ->whereYear('ae_fecha_inicio', $year)
            ->count();

        return response()->json(['count' => $count]);
    }
}
