<?php

namespace App\Http\Controllers;

use App\Models\RevisionDireccion;
use App\Models\RevisionEntrada;
use App\Models\RevisionSalida;
use App\Models\RevisionCompromiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class RevisionDireccionController extends Controller
{
    /**
     * Listar todas las revisiones
     */
    public function index(Request $request)
    {
        $query = RevisionDireccion::with(['responsable', 'compromisos'])
            ->orderBy('fecha_programada', 'desc');

        // Filtros
        if ($request->filled('anio')) {
            $query->where('anio', $request->anio);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('codigo', 'like', "%{$request->buscar}%")
                    ->orWhere('titulo', 'like', "%{$request->buscar}%");
            });
        }

        // Filtro por Sistema de Gestión (JSON)
        if ($request->filled('sistema_gestion')) {
            $sistemas = $request->sistema_gestion;
            if (is_array($sistemas)) {
                $query->where(function ($q) use ($sistemas) {
                    foreach ($sistemas as $sistema) {
                        $q->orWhereJsonContains('sistemas_gestion', $sistema);
                    }
                });
            } else {
                $query->whereJsonContains('sistemas_gestion', $sistemas);
            }
        }

        $revisiones = $query->get()->map(function ($revision) {
            $revision->compromisos_pendientes_count = $revision->compromisosPendientes()->count();
            return $revision;
        });

        return response()->json($revisiones);
    }

    /**
     * Crear nueva revisión
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha_programada' => 'required|date',
            'periodo' => 'required|string|max:20',
            'anio' => 'required|integer|min:2020|max:2100',
            'participantes' => 'nullable|string',
            'agenda' => 'nullable|string',
            'responsable_id' => 'required|exists:users,id',
            'sistemas_gestion' => 'nullable|array',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['estado'] = 'programada';

        $revision = RevisionDireccion::create($validated);

        return response()->json([
            'message' => 'Revisión creada exitosamente',
            'data' => $revision->load('responsable')
        ], 201);
    }

    /**
     * Mostrar una revisión específica con todos sus datos
     */
    public function show($id)
    {
        $revision = RevisionDireccion::with([
            'responsable',
            'creador',
            'entradas',
            'salidas.compromisos.responsable',
            'compromisos.responsable',
            'compromisos.seguimientos.usuario'
        ])->findOrFail($id);

        return response()->json($revision);
    }

    /**
     * Actualizar revisión
     */
    public function update(Request $request, $id)
    {
        $revision = RevisionDireccion::findOrFail($id);

        $validated = $request->validate([
            'titulo' => 'sometimes|string|max:255',
            'fecha_programada' => 'sometimes|date',
            'fecha_reunion' => 'nullable|date',
            'periodo' => 'sometimes|string|max:20',
            'participantes' => 'nullable|string',
            'agenda' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'estado' => 'sometimes|in:programada,en_preparacion,realizada,cancelada',
            'responsable_id' => 'sometimes|exists:users,id',
            'sistemas_gestion' => 'nullable|array',
        ]);

        $revision->update($validated);

        return response()->json([
            'message' => 'Revisión actualizada exitosamente',
            'data' => $revision->load('responsable')
        ]);
    }

    /**
     * Subir acta de la reunión
     */
    public function subirActa(Request $request, $id)
    {
        $revision = RevisionDireccion::findOrFail($id);

        $request->validate([
            'acta' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($revision->acta_path) {
            Storage::disk('public')->delete($revision->acta_path);
        }

        $path = $request->file('acta')->store('revisiones/actas', 'public');
        $revision->update(['acta_path' => $path]);

        return response()->json([
            'message' => 'Acta subida exitosamente',
            'path' => $path
        ]);
    }

    /**
     * Eliminar revisión
     */
    public function destroy($id)
    {
        $revision = RevisionDireccion::findOrFail($id);

        if ($revision->acta_path) {
            Storage::disk('public')->delete($revision->acta_path);
        }

        $revision->delete();

        return response()->json(['message' => 'Revisión eliminada exitosamente']);
    }

    // ========== ENTRADAS ==========

    /**
     * Agregar entrada a la revisión
     */
    public function storeEntrada(Request $request, $revisionId)
    {
        $revision = RevisionDireccion::findOrFail($revisionId);

        $validated = $request->validate([
            'tipo_entrada' => 'required|string',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'datos_soporte' => 'nullable|array',
            'conclusion' => 'nullable|string',
        ]);

        $entrada = $revision->entradas()->create($validated);

        return response()->json([
            'message' => 'Entrada agregada exitosamente',
            'data' => $entrada
        ], 201);
    }

    /**
     * Actualizar entrada
     */
    public function updateEntrada(Request $request, $id)
    {
        $entrada = RevisionEntrada::findOrFail($id);

        $validated = $request->validate([
            'titulo' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string',
            'datos_soporte' => 'nullable|array',
            'conclusion' => 'nullable|string',
            'estado' => 'sometimes|in:pendiente,revisado,aprobado',
        ]);

        $entrada->update($validated);

        return response()->json([
            'message' => 'Entrada actualizada exitosamente',
            'data' => $entrada
        ]);
    }

    /**
     * Eliminar entrada
     */
    public function destroyEntrada($id)
    {
        RevisionEntrada::findOrFail($id)->delete();
        return response()->json(['message' => 'Entrada eliminada exitosamente']);
    }

    /**
     * Obtener tipos de entrada disponibles
     */
    public function getTiposEntrada()
    {
        return response()->json(RevisionEntrada::getTiposEntrada());
    }

    // ========== SALIDAS ==========

    /**
     * Agregar salida/decisión a la revisión
     */
    public function storeSalida(Request $request, $revisionId)
    {
        $revision = RevisionDireccion::findOrFail($revisionId);

        $validated = $request->validate([
            'tipo_salida' => 'required|string',
            'descripcion' => 'required|string',
            'justificacion' => 'nullable|string',
        ]);

        $salida = $revision->salidas()->create($validated);

        return response()->json([
            'message' => 'Salida agregada exitosamente',
            'data' => $salida
        ], 201);
    }

    /**
     * Obtener tipos de salida disponibles
     */
    public function getTiposSalida()
    {
        return response()->json(RevisionSalida::getTiposSalida());
    }

    /**
     * Actualizar salida/decisión
     */
    public function updateSalida(Request $request, $id)
    {
        $salida = RevisionSalida::findOrFail($id);

        $validated = $request->validate([
            'tipo_salida' => 'sometimes|string',
            'descripcion' => 'sometimes|string',
            'justificacion' => 'nullable|string',
        ]);

        $salida->update($validated);

        return response()->json([
            'message' => 'Salida actualizada exitosamente',
            'data' => $salida
        ]);
    }

    /**
     * Eliminar salida/decisión
     */
    public function destroySalida($id)
    {
        RevisionSalida::findOrFail($id)->delete();
        return response()->json(['message' => 'Salida eliminada exitosamente']);
    }

    // ========== COMPROMISOS ==========

    /**
     * Agregar compromiso a la revisión
     */
    public function storeCompromiso(Request $request, $revisionId)
    {
        $revision = RevisionDireccion::findOrFail($revisionId);

        $validated = $request->validate([
            'salida_id' => 'nullable|exists:revision_salidas,id',
            'descripcion' => 'required|string',
            'responsable_id' => 'required|exists:users,id',
            'fecha_limite' => 'required|date|after:today',
            'recursos_necesarios' => 'nullable|string|max:1000',
            'observaciones' => 'nullable|string|max:500',
            'sistemas_gestion' => 'nullable|array',
        ]);

        $compromiso = $revision->compromisos()->create($validated);

        return response()->json([
            'message' => 'Compromiso agregado exitosamente',
            'data' => $compromiso->load('responsable')
        ], 201);
    }

    /**
     * Actualizar compromiso
     */
    public function updateCompromiso(Request $request, $id)
    {
        $compromiso = RevisionCompromiso::findOrFail($id);

        $validated = $request->validate([
            'descripcion' => 'sometimes|string',
            'responsable_id' => 'sometimes|exists:users,id',
            'fecha_limite' => 'sometimes|date',
            'estado' => 'sometimes|in:pendiente,en_proceso,completado,cancelado',
            'avance' => 'sometimes|integer|min:0|max:100',
            'recursos_necesarios' => 'nullable|string|max:1000',
            'observaciones' => 'nullable|string|max:500',
            'sistemas_gestion' => 'nullable|array',
        ]);

        // Si hay cambio de estado o avance, registrar seguimiento
        if (isset($validated['avance']) || isset($validated['estado'])) {
            $compromiso->registrarSeguimiento(
                Auth::id(),
                $request->input('comentario_seguimiento', 'Actualización del compromiso'),
                $validated['avance'] ?? null,
                $validated['estado'] ?? null
            );
            unset($validated['avance'], $validated['estado']);
        }

        if (!empty($validated)) {
            $compromiso->update($validated);
        }

        return response()->json([
            'message' => 'Compromiso actualizado exitosamente',
            'data' => $compromiso->load(['responsable', 'seguimientos.usuario'])
        ]);
    }

    /**
     * Registrar seguimiento de compromiso
     */
    public function registrarSeguimiento(Request $request, $id)
    {
        $compromiso = RevisionCompromiso::findOrFail($id);

        $validated = $request->validate([
            'comentario' => 'required|string',
            'avance' => 'nullable|integer|min:0|max:100',
            'estado' => 'nullable|in:pendiente,en_proceso,completado,cancelado',
        ]);

        $seguimiento = $compromiso->registrarSeguimiento(
            Auth::id(),
            $validated['comentario'],
            $validated['avance'] ?? null,
            $validated['estado'] ?? null
        );

        return response()->json([
            'message' => 'Seguimiento registrado exitosamente',
            'data' => $seguimiento->load('usuario'),
            'compromiso' => $compromiso->fresh(['responsable', 'seguimientos.usuario'])
        ]);
    }

    /**
     * Subir evidencia de compromiso
     */
    public function subirEvidencia(Request $request, $id)
    {
        $compromiso = RevisionCompromiso::findOrFail($id);

        $request->validate([
            'evidencia' => 'required|file|max:10240',
        ]);

        $path = $request->file('evidencia')->store('revisiones/evidencias', 'public');

        $evidencias = $compromiso->evidencia_path ?? [];
        $evidencias[] = [
            'path' => $path,
            'nombre' => $request->file('evidencia')->getClientOriginalName(),
            'fecha' => Carbon::now()->toDateTimeString(),
        ];

        $compromiso->update(['evidencia_path' => $evidencias]);

        return response()->json([
            'message' => 'Evidencia subida exitosamente',
            'evidencias' => $evidencias
        ]);
    }

    /**
     * Dashboard de compromisos pendientes
     */
    public function dashboardCompromisos()
    {
        $compromisos = RevisionCompromiso::with(['revision', 'responsable'])
            ->whereIn('estado', ['pendiente', 'en_proceso', 'vencido'])
            ->orderBy('fecha_limite')
            ->get();

        $resumen = [
            'total' => $compromisos->count(),
            'pendientes' => $compromisos->where('estado', 'pendiente')->count(),
            'en_proceso' => $compromisos->where('estado', 'en_proceso')->count(),
            'vencidos' => $compromisos->where('estado', 'vencido')->count(),
            'por_vencer' => $compromisos->filter(fn($c) => $c->dias_restantes !== null && $c->dias_restantes <= 7 && $c->dias_restantes > 0)->count(),
        ];

        return response()->json([
            'resumen' => $resumen,
            'compromisos' => $compromisos
        ]);
    }

    /**
     * Obtener datos para generar entradas automáticamente
     */
    public function getDatosParaEntradas()
    {
        // Recopilar datos de otros módulos del sistema
        $datos = [
            'indicadores' => [
                'total' => \App\Models\Indicador::count(),
                'con_desviacion' => \App\Models\Indicador::whereRaw('COALESCE(indicador_resultado, 0) < COALESCE(indicador_meta, 0)')->count(),
            ],
            'hallazgos' => [
                'abiertos' => \App\Models\Hallazgo::whereIn('hallazgo_estado', ['creado', 'asignado', 'en proceso'])->count(),
                'cerrados_periodo' => \App\Models\Hallazgo::where('hallazgo_estado', 'cerrado')
                    ->where('updated_at', '>=', Carbon::now()->subMonths(6))
                    ->count(),
            ],
            'riesgos' => [
                'total' => \App\Models\Riesgo::count(),
                'altos' => \App\Models\Riesgo::whereIn('riesgo_nivel', ['Alto', 'Muy Alto'])->count(),
            ],
            'obligaciones' => [
                'total' => \App\Models\Obligacion::count(),
                'pendientes' => \App\Models\Obligacion::where('estado_obligacion', 'pendiente')->count(),
            ],
        ];

        return response()->json($datos);
    }
}
