<?php

namespace App\Http\Controllers;

use App\Models\ActivoCritico;
use App\Models\EscenarioContinuidad;
use App\Models\PlanContinuidad;
use App\Models\PruebaContinuidad;
use App\Models\IncidenteContinuidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContinuidadController extends Controller
{
    // ========== ACTIVOS CRÍTICOS ==========

    /**
     * Listar activos críticos
     */
    public function indexActivos(Request $request)
    {
        $query = ActivoCritico::with(['proceso', 'responsable'])
            ->where('activo', true)
            ->orderBy('nivel_criticidad', 'desc');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('proceso_id')) {
            $query->where('proceso_id', $request->proceso_id);
        }
        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', "%{$request->buscar}%")
                  ->orWhere('codigo', 'like', "%{$request->buscar}%");
            });
        }

        return response()->json($query->get());
    }

    /**
     * Crear activo crítico
     */
    public function storeActivo(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:personal,tecnologia,informacion,infraestructura,proveedor,proceso,otro',
            'proceso_id' => 'nullable|exists:procesos,id',
            'responsable_id' => 'nullable|exists:users,id',
            'nivel_criticidad' => 'required|in:bajo,medio,alto,critico',
            'rto' => 'nullable|integer|min:0',
            'rpo' => 'nullable|integer|min:0',
            'mtpd' => 'nullable|integer|min:0',
            'ubicacion' => 'nullable|string',
        ]);

        $activo = ActivoCritico::create($validated);

        return response()->json([
            'message' => 'Activo crítico creado exitosamente',
            'data' => $activo->load(['proceso', 'responsable'])
        ], 201);
    }

    /**
     * Mostrar activo
     */
    public function showActivo($id)
    {
        $activo = ActivoCritico::with(['proceso', 'responsable', 'estrategias'])
            ->findOrFail($id);
        return response()->json($activo);
    }

    /**
     * Actualizar activo
     */
    public function updateActivo(Request $request, $id)
    {
        $activo = ActivoCritico::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'sometimes|in:personal,tecnologia,informacion,infraestructura,proveedor,proceso,otro',
            'proceso_id' => 'nullable|exists:procesos,id',
            'responsable_id' => 'nullable|exists:users,id',
            'nivel_criticidad' => 'sometimes|in:bajo,medio,alto,critico',
            'rto' => 'nullable|integer|min:0',
            'rpo' => 'nullable|integer|min:0',
            'mtpd' => 'nullable|integer|min:0',
            'ubicacion' => 'nullable|string',
            'activo' => 'sometimes|boolean',
        ]);

        $activo->update($validated);

        return response()->json([
            'message' => 'Activo actualizado exitosamente',
            'data' => $activo->load(['proceso', 'responsable'])
        ]);
    }

    /**
     * Eliminar activo (soft)
     */
    public function destroyActivo($id)
    {
        $activo = ActivoCritico::findOrFail($id);
        $activo->update(['activo' => false]);
        return response()->json(['message' => 'Activo desactivado exitosamente']);
    }

    /**
     * Obtener tipos de activo
     */
    public function getTiposActivo()
    {
        return response()->json(ActivoCritico::getTipos());
    }

    // ========== ESCENARIOS ==========

    /**
     * Listar escenarios
     */
    public function indexEscenarios(Request $request)
    {
        $query = EscenarioContinuidad::where('activo', true)
            ->orderBy('nivel_riesgo', 'desc');

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        $escenarios = $query->get()->map(function ($e) {
            $e->nivel_riesgo_label = $e->nivel_riesgo_label;
            $e->nivel_riesgo_color = $e->nivel_riesgo_color;
            return $e;
        });

        return response()->json($escenarios);
    }

    /**
     * Crear escenario
     */
    public function storeEscenario(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'categoria' => 'required|string',
            'probabilidad' => 'required|in:muy_baja,baja,media,alta,muy_alta',
            'impacto' => 'required|in:insignificante,menor,moderado,mayor,catastrofico',
            'activos_afectados' => 'nullable|array',
            'procesos_afectados' => 'nullable|array',
            'controles_existentes' => 'nullable|string',
        ]);

        $escenario = EscenarioContinuidad::create($validated);

        return response()->json([
            'message' => 'Escenario creado exitosamente',
            'data' => $escenario
        ], 201);
    }

    /**
     * Actualizar escenario
     */
    public function updateEscenario(Request $request, $id)
    {
        $escenario = EscenarioContinuidad::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string',
            'categoria' => 'sometimes|string',
            'probabilidad' => 'sometimes|in:muy_baja,baja,media,alta,muy_alta',
            'impacto' => 'sometimes|in:insignificante,menor,moderado,mayor,catastrofico',
            'activos_afectados' => 'nullable|array',
            'procesos_afectados' => 'nullable|array',
            'controles_existentes' => 'nullable|string',
        ]);

        $escenario->update($validated);

        return response()->json([
            'message' => 'Escenario actualizado exitosamente',
            'data' => $escenario
        ]);
    }

    /**
     * Obtener categorías de escenario
     */
    public function getCategoriasEscenario()
    {
        return response()->json(EscenarioContinuidad::getCategorias());
    }

    // ========== PLANES DE CONTINUIDAD ==========

    /**
     * Listar planes
     */
    public function indexPlanes(Request $request)
    {
        $query = PlanContinuidad::with(['escenario', 'proceso', 'responsable'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('tipo_plan')) {
            $query->where('tipo_plan', $request->tipo_plan);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $planes = $query->get()->map(function ($p) {
            $p->estado_color = $p->estado_color;
            $p->necesita_revision = $p->necesita_revision;
            $p->ultima_prueba = $p->ultima_prueba;
            return $p;
        });

        return response()->json($planes);
    }

    /**
     * Crear plan
     */
    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'objetivo' => 'required|string',
            'tipo_plan' => 'required|in:bcp,drp,irp,crmp',
            'escenario_id' => 'nullable|exists:escenarios_continuidad,id',
            'proceso_id' => 'nullable|exists:procesos,id',
            'responsable_id' => 'required|exists:users,id',
            'alcance' => 'nullable|string',
            'equipo_respuesta' => 'nullable|array',
            'procedimientos_activacion' => 'nullable|string',
            'procedimientos_recuperacion' => 'nullable|string',
            'recursos_necesarios' => 'nullable|string',
            'comunicacion_crisis' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['estado'] = 'borrador';

        $plan = PlanContinuidad::create($validated);

        return response()->json([
            'message' => 'Plan creado exitosamente',
            'data' => $plan->load(['escenario', 'proceso', 'responsable'])
        ], 201);
    }

    /**
     * Mostrar plan con detalles
     */
    public function showPlan($id)
    {
        $plan = PlanContinuidad::with([
            'escenario',
            'proceso',
            'responsable',
            'estrategias.activo',
            'pruebas.responsable'
        ])->findOrFail($id);

        $plan->estado_color = $plan->estado_color;
        $plan->necesita_revision = $plan->necesita_revision;

        return response()->json($plan);
    }

    /**
     * Actualizar plan
     */
    public function updatePlan(Request $request, $id)
    {
        $plan = PlanContinuidad::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'objetivo' => 'sometimes|string',
            'tipo_plan' => 'sometimes|in:bcp,drp,irp,crmp',
            'escenario_id' => 'nullable|exists:escenarios_continuidad,id',
            'proceso_id' => 'nullable|exists:procesos,id',
            'responsable_id' => 'sometimes|exists:users,id',
            'alcance' => 'nullable|string',
            'equipo_respuesta' => 'nullable|array',
            'procedimientos_activacion' => 'nullable|string',
            'procedimientos_recuperacion' => 'nullable|string',
            'recursos_necesarios' => 'nullable|string',
            'comunicacion_crisis' => 'nullable|string',
            'estado' => 'sometimes|in:borrador,en_revision,aprobado,obsoleto',
            'version' => 'sometimes|string|max:10',
            'fecha_aprobacion' => 'nullable|date',
            'proxima_revision' => 'nullable|date',
        ]);

        $plan->update($validated);

        return response()->json([
            'message' => 'Plan actualizado exitosamente',
            'data' => $plan->load(['escenario', 'proceso', 'responsable'])
        ]);
    }

    /**
     * Subir documento del plan
     */
    public function subirDocumentoPlan(Request $request, $id)
    {
        $plan = PlanContinuidad::findOrFail($id);

        $request->validate([
            'documento' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($plan->documento_path) {
            Storage::disk('public')->delete($plan->documento_path);
        }

        $path = $request->file('documento')->store('continuidad/planes', 'public');
        $plan->update(['documento_path' => $path]);

        return response()->json([
            'message' => 'Documento subido exitosamente',
            'path' => $path
        ]);
    }

    /**
     * Obtener tipos de plan
     */
    public function getTiposPlan()
    {
        return response()->json(PlanContinuidad::getTiposPlan());
    }

    // ========== PRUEBAS ==========

    /**
     * Listar pruebas
     */
    public function indexPruebas(Request $request)
    {
        $query = PruebaContinuidad::with(['plan', 'responsable'])
            ->orderBy('fecha_programada', 'desc');

        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $pruebas = $query->get()->map(function ($p) {
            $p->estado_color = $p->estado_color;
            $p->esta_vencida = $p->esta_vencida;
            return $p;
        });

        return response()->json($pruebas);
    }

    /**
     * Crear prueba
     */
    public function storePrueba(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'plan_id' => 'required|exists:planes_continuidad,id',
            'tipo_prueba' => 'required|in:documental,walkthrough,simulacion,funcional,ejercicio_total',
            'fecha_programada' => 'required|date',
            'objetivo' => 'required|string',
            'alcance' => 'nullable|string',
            'participantes' => 'nullable|string',
            'escenario_prueba' => 'nullable|string',
            'responsable_id' => 'required|exists:users,id',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['estado'] = 'programada';

        $prueba = PruebaContinuidad::create($validated);

        return response()->json([
            'message' => 'Prueba programada exitosamente',
            'data' => $prueba->load(['plan', 'responsable'])
        ], 201);
    }

    /**
     * Registrar resultados de prueba
     */
    public function registrarResultados(Request $request, $id)
    {
        $prueba = PruebaContinuidad::findOrFail($id);

        $validated = $request->validate([
            'fecha_ejecucion' => 'required|date',
            'resultados' => 'required|string',
            'hallazgos' => 'nullable|string',
            'lecciones_aprendidas' => 'nullable|string',
            'acciones_mejora' => 'nullable|string',
            'calificacion' => 'required|integer|min:1|max:5',
            'estado' => 'required|in:completada,cancelada,postergada',
        ]);

        $prueba->update($validated);

        return response()->json([
            'message' => 'Resultados registrados exitosamente',
            'data' => $prueba
        ]);
    }

    /**
     * Obtener tipos de prueba
     */
    public function getTiposPrueba()
    {
        return response()->json(PruebaContinuidad::getTiposPrueba());
    }

    // ========== DASHBOARD ==========

    /**
     * Dashboard de continuidad
     */
    public function dashboard()
    {
        $data = [
            'activos' => [
                'total' => ActivoCritico::where('activo', true)->count(),
                'criticos' => ActivoCritico::where('activo', true)
                    ->whereIn('nivel_criticidad', ['alto', 'critico'])->count(),
                'por_tipo' => ActivoCritico::where('activo', true)
                    ->selectRaw('tipo, count(*) as total')
                    ->groupBy('tipo')
                    ->get(),
            ],
            'escenarios' => [
                'total' => EscenarioContinuidad::where('activo', true)->count(),
                'alto_riesgo' => EscenarioContinuidad::where('activo', true)
                    ->where('nivel_riesgo', '>=', 12)->count(),
            ],
            'planes' => [
                'total' => PlanContinuidad::count(),
                'aprobados' => PlanContinuidad::where('estado', 'aprobado')->count(),
                'necesitan_revision' => PlanContinuidad::where('estado', 'aprobado')
                    ->whereNotNull('proxima_revision')
                    ->where('proxima_revision', '<', now())
                    ->count(),
                'por_tipo' => PlanContinuidad::selectRaw('tipo_plan, count(*) as total')
                    ->groupBy('tipo_plan')
                    ->get(),
            ],
            'pruebas' => [
                'programadas' => PruebaContinuidad::where('estado', 'programada')->count(),
                'completadas_anio' => PruebaContinuidad::where('estado', 'completada')
                    ->whereYear('fecha_ejecucion', date('Y'))
                    ->count(),
                'proximas' => PruebaContinuidad::where('estado', 'programada')
                    ->orderBy('fecha_programada')
                    ->take(5)
                    ->get(),
            ],
            'incidentes' => [
                'total_anio' => IncidenteContinuidad::whereYear('fecha_inicio', date('Y'))->count(),
                'activos' => IncidenteContinuidad::whereNull('fecha_fin')->count(),
            ],
        ];

        return response()->json($data);
    }
}
