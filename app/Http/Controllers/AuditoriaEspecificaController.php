<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaEspecifica;
use App\Models\AuditoriaAgenda;
use App\Models\AuditoriaEquipo;
use App\Models\AuditoriaEvaluacion;
use App\Traits\CalculatesAuditHours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class AuditoriaEspecificaController extends Controller
{
    use CalculatesAuditHours;

    public function index($pa_id)
    {
        // Listar auditorías de un programa específico
        $auditorias = AuditoriaEspecifica::where('pa_id', $pa_id)
            ->with(['equipo.auditor.user', 'agenda'])
            ->get();
        return response()->json($auditorias);
    }

    public function show($id)
    {
        $auditoria = AuditoriaEspecifica::with([
            'equipo.auditor.user' => function ($q) {
                $q->select('id', 'name', 'email');
            },
            'agenda.auditor.user' => function ($q) {
                $q->select('id', 'name', 'email');
            },
            'procesos',
            'evaluaciones.evaluador:id,name',
            'evaluaciones.evaluado:id,name'
        ])->findOrFail($id);
        return response()->json($auditoria);
    }

    public function getEquipo($id)
    {
        $auditoria = AuditoriaEspecifica::with([
            'equipo.auditor.user' => function ($q) {
                $q->select('id', 'name', 'email');
            }
        ])->select('id')->findOrFail($id);

        return response()->json(['equipo' => $auditoria->equipo]);
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
        return DB::transaction(function () use ($request, $id) {
            $auditoria = AuditoriaEspecifica::with('programa')->findOrFail($id);

            // Versioning Logic
            if (in_array($auditoria->programa->pa_estado, ['Aprobada', 'Ejecutada'])) {
                // Increment Program Version
                $auditoria->programa->increment('pa_version', 1, []);

                // Increment Cycle if not explicitly set
                $auditoria->ae_ciclo = $auditoria->ae_ciclo + 1;
            }

            $auditoria->update($request->except(['ae_ciclo']));

            // Force cycle save if versioning triggered (since update might not cover it if not in request)
            if (in_array($auditoria->programa->pa_estado, ['Aprobada', 'Ejecutada'])) {
                $auditoria->save();
            }

            // Sync processes if provided
            // Laravel's sync() is "smart" - it keeps existing IDs and only adds/removes differences.
            if ($request->has('procesos')) {
                $auditoria->procesos()->sync($request->input('procesos', []));
            }

            return response()->json($auditoria);
        });
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

        // DEBUG: Log payload to see what we are receiving
        file_put_contents(public_path('debug_agenda_payload.txt'), print_r($items, true));

        // Extract IDs to keep to perform cleanup
        $keepIds = [];
        foreach ($items as $item) {
            if (isset($item['id']) && $item['id']) {
                $keepIds[] = $item['id'];
            }
        }

        try {
            DB::beginTransaction();
            // 1. Delete items not in payload (cleanup removed slots)
            AuditoriaAgenda::where('ae_id', $ae_id)
                ->whereNotIn('id', $keepIds)
                ->delete();

            // 2. Update or Create items
            foreach ($items as $item) {
                $data = [
                    'ae_id' => $ae_id,
                    'proceso_id' => $item['proceso_id'] ?? null,
                    'aea_fecha' => $item['aea_fecha'],
                    'aea_hora_inicio' => $item['aea_hora_inicio'],
                    'aea_hora_fin' => $item['aea_hora_fin'],
                    'aea_actividad' => $item['aea_actividad'],
                    'auditor_id' => $item['auditor_id'] ?? null,
                    'observador_id' => $item['observador_id'] ?? null,
                    'aea_requisito' => $item['aea_requisito'] ?? null,
                    'aea_lugar' => $item['aea_lugar'] ?? null,
                    'aea_tipo' => $item['aea_tipo'] ?? 'ejecucion',
                ];

                if (isset($item['id']) && $item['id']) {
                    $agendaItem = AuditoriaAgenda::find($item['id']);
                    if ($agendaItem) {
                        $agendaItem->fill($data);
                        $agendaItem->save();
                    }
                } else {
                    AuditoriaAgenda::create($data);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            \Illuminate\Support\Facades\Log::error("Error updating agenda $ae_id: " . $e->getMessage());
            file_put_contents(public_path('debug_error_agenda.txt'), $e->getMessage());
            return response()->json(['message' => 'Error al guardar agenda', 'error' => $e->getMessage()], 500);
        }

        $this->updateCalculatedHours($ae_id);

        return response()->json(['message' => 'Agenda actualizada correctamente']);
    }

    // Métodos para Equipo
    public function updateEquipo(Request $request, $ae_id)
    {
        $equipo = $request->input('equipo', []);

        try {
            DB::beginTransaction();

            // 1. Get existing members for this audit
            $existingMembers = AuditoriaEquipo::where('ae_id', '=', $ae_id, 'and')->get();
            $existingMap = $existingMembers->keyBy('auditor_id'); // Key by auditor_id (unique per audit)

            // 2. Identify IDs to keep (from the request)
            $incomingAuditorIds = [];

            foreach ($equipo as $miembro) {
                if (!isset($miembro['auditor_id']))
                    continue;

                $auditorId = $miembro['auditor_id'];
                $incomingAuditorIds[] = $auditorId;

                $data = [
                    'ae_id' => $ae_id,
                    'auditor_id' => $auditorId,
                    'aeq_rol' => $miembro['aeq_rol'] ?? 'Auditor',
                    // Horas programadas se calculan automáticamente desde la agenda
                ];

                if ($existingMap->has($auditorId)) {
                    // Update existing record (Preserves ID)
                    $existingMap->get($auditorId)->update($data);
                } else {
                    // Create new record
                    AuditoriaEquipo::create($data);
                }
            }

            // 3. Delete members not in the request
            // Use auditor_id to identify which ones to remove
            if (!empty($incomingAuditorIds)) {
                AuditoriaEquipo::where('ae_id', '=', $ae_id, 'and')
                    ->whereNotIn('auditor_id', $incomingAuditorIds)
                    ->delete();
            } else {
                // If empty request, clear all
                AuditoriaEquipo::where('ae_id', '=', $ae_id, 'and')->delete();
            }

            DB::commit();
            return response()->json(['message' => 'Equipo actualizado']);
        } catch (\Exception $e) {
            DB::rollback();
            \Illuminate\Support\Facades\Log::error("Error updating equipo auditoria $ae_id: " . $e->getMessage());
            file_put_contents(public_path('debug_error.txt'), $e->getMessage());
            return response()->json(['message' => 'Error al guardar equipo', 'error' => $e->getMessage()], 500);
        }
    }



    public function cancelarActividad($id)
    {
        $agendaItem = AuditoriaAgenda::with('auditoria')->findOrFail($id);
        $agendaItem->estado = 'Cancelada';
        $agendaItem->save();

        if ($agendaItem->auditoria) {
            $this->updateCalculatedHours($agendaItem->auditoria->id);
        }

        return response()->json(['message' => 'Actividad cancelada', 'item' => $agendaItem]);
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

    public function getRequisitosDisponibles($id)
    {
        $auditoria = AuditoriaEspecifica::findOrFail($id);
        $sistemas = $auditoria->ae_sistema ?? [];

        if (empty($sistemas)) {
            return response()->json([]);
        }

        // Map system keys to partial norm names
        $map = [
            'sgc' => 'ISO 9001',
            'sgas' => 'ISO 37001',
            'sgco' => 'ISO 21001',
            'sgsi' => 'ISO 27001',
            'sgcm' => 'ISO 37301'
        ];

        $terms = [];
        foreach ($sistemas as $sys) {
            if (isset($map[$sys])) {
                $terms[] = $map[$sys];
            }
        }

        if (empty($terms)) {
            return response()->json([]);
        }

        // Find Normas matching the terms
        $normas = \App\Models\NormaAuditable::with('requisitos')
            ->where(function ($query) use ($terms) {
                foreach ($terms as $term) {
                    $query->orWhere('nombre', 'like', "%{$term}%");
                }
            })
            ->get();

        return response()->json($normas);
    }
    public function downloadPlanPdf($id)
    {
        $auditoria = AuditoriaEspecifica::with(['agenda', 'equipo.usuario'])->findOrFail($id);

        $pdf = PDF::loadView('auditorias.pdf.plan', compact('auditoria'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download("Plan_Auditoria_{$auditoria->ae_codigo}.pdf");
    }
}
