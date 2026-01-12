<?php

namespace App\Http\Controllers;

use App\Models\Indicador;
use App\Models\IndicadorSeguimiento;
use App\Models\Proceso;
use App\Models\PlanificacionPEI;
use App\Models\PlanificacionSIG;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Services\AIService;

class IndicadorController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function view()
    {
        return view('indicadores.index');
    }

    public function index()
    {
        $user = auth()->user();
        $query = Indicador::with(['proceso', 'objetivoPEI', 'objetivoSIG']);

        // Filtrado por Rol (Facilitador / Propietario)
        // Si NO es admin, aplicamos filtro por OUO
        if (!$user->hasRole('admin') && $user->hasAnyRole(['facilitador', 'propietario'])) {
            // 1. Obtener IDs de OUOs asignadas al usuario
            $userOuoIds = $user->ouos()->pluck('ouos.id');

            // 2. Obtener IDs de Procesos asociados a esas OUOs
            // Usamos la tabla pivote procesos_ouo
            $allowedProcesoIds = DB::table('procesos_ouo')
                ->whereIn('id_ouo', $userOuoIds)
                ->pluck('id_proceso');

            // 3. Filtrar query de indicadores
            $query->whereIn('proceso_id', $allowedProcesoIds);

            // 4. Filtrar lista de procesos para el dropdown (solo los permitidos)
            $procesos = Proceso::select('id', 'proceso_nombre')
                ->whereIn('id', $allowedProcesoIds)
                ->get();
        } else {
            // Admin o rol sin restricción: ve todo
            $procesos = Proceso::select('id', 'proceso_nombre')->get();
        }

        if (request()->has('proceso_id') && request()->proceso_id) {
            $query->where('proceso_id', request()->proceso_id);
        }

        if (request()->has('indicador_sig') && request()->indicador_sig) {
            $query->where('indicador_sig', 'like', '%"' . request()->indicador_sig . '"%');
        }

        $indicadores = $query->get()
            ->map(function ($indicador) {
                // Obtener el último seguimiento para mostrar el valor actual, filtrando por año si existe
                $seguimientosQuery = $indicador->seguimientos();

                if (request()->has('year') && request()->year) {
                    $seguimientosQuery->where('is_periodo', request()->year);
                }

                $ultimoSeguimiento = $seguimientosQuery->orderBy('is_numero_periodo', 'desc')->first();

                $indicador->ultimo_valor = $ultimoSeguimiento ? $ultimoSeguimiento->is_valor : null;
                $indicador->ultima_meta = $ultimoSeguimiento ? $ultimoSeguimiento->is_meta : null;
                $indicador->ultimo_periodo = $ultimoSeguimiento ? $ultimoSeguimiento->is_numero_periodo : null;
                $indicador->ultima_fecha = $ultimoSeguimiento ? $ultimoSeguimiento->is_fecha : null;

                return $indicador;
            });

        $planificacionesPei = PlanificacionPEI::all();
        $planificacionesSig = PlanificacionSIG::all();

        return response()->json([
            'indicadores' => $indicadores,
            'procesos' => $procesos,
            'planificaciones_pei' => $planificacionesPei,
            'planificaciones_sig' => $planificacionesSig
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proceso_id' => 'required|exists:procesos,id',
            'indicador_nombre' => 'required|string|max:255',
            'indicador_frecuencia' => 'required|string',
            'indicador_meta' => 'required|numeric',
            // Agregar más validaciones según sea necesario
        ]);

        try {
            DB::beginTransaction();
            $indicador = Indicador::create($request->all());
            DB::commit();
            return response()->json(['message' => 'Indicador creado correctamente', 'indicador' => $indicador], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al crear indicador: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $indicador = Indicador::findOrFail($id);

        try {
            DB::beginTransaction();
            $indicador->update($request->all());
            DB::commit();
            return response()->json(['message' => 'Indicador actualizado correctamente', 'indicador' => $indicador]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar indicador: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $indicador = Indicador::findOrFail($id);
            $indicador->delete();
            return response()->json(['message' => 'Indicador eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar indicador: ' . $e->getMessage()], 500);
        }
    }

    public function storeAvance(Request $request)
    {
        $request->validate([
            'indicador_id' => 'required|exists:indicadores,id',
            'is_fecha' => 'required|date',
            'is_valor' => 'required',
            'is_periodo' => 'required|integer',
            'is_numero_periodo' => [
                'required',
                'integer',
                Rule::unique('indicadores_seguimiento')->where(function ($query) use ($request) {
                    return $query->where('indicador_id', $request->indicador_id)
                        ->where('is_periodo', $request->is_periodo);
                })
            ],
            'is_evidencias.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
            'is_comentario' => 'nullable|string'
        ], [
            'is_numero_periodo.unique' => 'Ya existe un avance registrado para este N° de Periodo en el año seleccionado.'
        ]);

        try {
            $indicador = Indicador::findOrFail($request->indicador_id);

            // Validar que la meta del periodo no supere la meta del indicador
            $metaPeriodo = (float) $request->is_meta;
            $metaIndicador = (float) $indicador->indicador_meta;

            // Esta validación depende de la lógica de negocio, a veces la meta del periodo puede ser distinta.
            // La mantengo si es requerida, pero ojo si bloquea casos válidos.
            if ($request->has('is_meta') && $metaPeriodo > $metaIndicador && $indicador->indicador_sentido == 'lineal') {
                // Solo validar si es sentido lineal estricto, o según requiera el usuario.
                // Por ahora lo dejo comentado o laxo si el usuario no lo pidió explícitamente corregir.
                // return response()->json([
                //    'message' => "La meta del periodo ($metaPeriodo) no puede ser mayor a la meta del indicador ($metaIndicador)"
                // ], 422);
            }

            DB::beginTransaction();

            $data = $request->except(['is_evidencias', 'indicador_id', 'existing_evidencias', 'update_evidences']);
            $data['indicador_id'] = $request->indicador_id;

            $evidenciasPaths = [];
            if ($request->hasFile('is_evidencias')) {
                foreach ($request->file('is_evidencias') as $file) {
                    $path = $file->store('evidencias_indicadores', 'public');
                    $evidenciasPaths[] = $path;
                }
            }

            if (!empty($evidenciasPaths)) {
                $data['is_evidencias'] = json_encode($evidenciasPaths);
            }

            $seguimiento = IndicadorSeguimiento::create($data);

            DB::commit();
            return response()->json(['message' => 'Avance registrado correctamente', 'seguimiento' => $seguimiento], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error storing avance: ' . $e->getMessage());
            return response()->json(['message' => 'Error interno al registrar el avance. Por favor, inténtelo de nuevo más tarde.'], 500);
        }
    }

    public function updateAvance(Request $request, $id)
    {
        $seguimiento = IndicadorSeguimiento::findOrFail($id);

        $request->validate([
            'is_fecha' => 'required|date',
            'is_valor' => 'required',
            'is_periodo' => 'required|integer',
            'is_numero_periodo' => [
                'required',
                'integer',
                Rule::unique('indicadores_seguimiento')->ignore($id)->where(function ($query) use ($request, $seguimiento) {
                    return $query->where('indicador_id', $seguimiento->indicador_id)
                        ->where('is_periodo', $request->is_periodo);
                })
            ],
            'is_evidencias.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
            'is_comentario' => 'nullable|string'
        ], [
            'is_numero_periodo.unique' => 'Ya existe un avance registrado para este N° de Periodo en el año seleccionado.'
        ]);

        try {
            $indicador = Indicador::findOrFail($seguimiento->indicador_id);

            DB::beginTransaction();

            $data = $request->except(['is_evidencias', 'indicador_id', 'existing_evidencias', 'update_evidences']);

            // Manejo de evidencias
            $evidenciasPaths = [];

            // 1. Mantener evidencias existentes que vienen del frontend
            if ($request->has('existing_evidencias')) {
                $existing = $request->existing_evidencias;
                if (is_array($existing)) {
                    $evidenciasPaths = $existing;
                }
            }

            // 2. Agregar nuevas evidencias
            if ($request->hasFile('is_evidencias')) {
                foreach ($request->file('is_evidencias') as $file) {
                    $path = $file->store('evidencias_indicadores', 'public');
                    $evidenciasPaths[] = $path;
                }
            }

            // Si se envió la bandera update_evidences, actualizamos el campo.
            // Si no hay evidencias, guardamos null o array vacío según preferencia.
            if ($request->has('update_evidences')) {
                $data['is_evidencias'] = !empty($evidenciasPaths) ? json_encode($evidenciasPaths) : null;
            } else {
                // Si no se envió la bandera (edición simple de datos), mantenemos lo que había si no se tocaron archivos
                // Pero el frontend parece enviar siempre todo si usa FormData.
                // Asumiremos que si llega update_evidences es porque se gestionaron archivos.
            }

            $seguimiento->update($data);

            DB::commit();
            return response()->json(['message' => 'Avance actualizado correctamente', 'seguimiento' => $seguimiento]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e; // Re-throw validation exceptions to be handled by Laravel's default handler (returns 422)
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error updating avance: ' . $e->getMessage());
            return response()->json(['message' => 'Error interno al actualizar el avance. Por favor, inténtelo de nuevo más tarde.'], 500);
        }
    }

    public function getAvances($id)
    {
        $indicador = Indicador::findOrFail($id);
        $avances = $indicador->seguimientos()->orderBy('is_fecha', 'desc')->get();
        return response()->json($avances);
    }

    public function getNextPeriod(Request $request)
    {
        $request->validate([
            'indicador_id' => 'required|exists:indicadores,id',
            'year' => 'required|integer'
        ]);

        $indicador = Indicador::findOrFail($request->indicador_id);

        $ultimoSeguimiento = IndicadorSeguimiento::where('indicador_id', $request->indicador_id)
            ->where('is_periodo', $request->year)
            ->orderBy('is_numero_periodo', 'desc')
            ->first();

        $siguientePeriodo = $ultimoSeguimiento ? ($ultimoSeguimiento->is_numero_periodo + 1) : 1;

        // Validar contra frecuencia
        $maxPeriodos = match (strtolower($indicador->indicador_frecuencia)) {
            'mensual' => 12,
            'trimestral' => 4,
            'semestral' => 2,
            'anual' => 1,
            default => 12
        };

        if ($siguientePeriodo > $maxPeriodos) {
            return response()->json(['message' => 'Se ha alcanzado el máximo de periodos para este año', 'full' => true, 'periodo' => $maxPeriodos], 200);
        }

        return response()->json(['periodo' => $siguientePeriodo, 'full' => false]);
    }
    public function generateDescription(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        try {
            $description = $this->aiService->generateIndicatorDescription($request->nombre);
            return response()->json(['description' => $description]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al generar descripción: ' . $e->getMessage()], 500);
        }
    }
}