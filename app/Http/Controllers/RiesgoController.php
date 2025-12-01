<?php

namespace App\Http\Controllers;
use App\Models\Riesgo;
use App\Models\Proceso;
use App\Models\Obligacion;
use Illuminate\Http\Request;
use App\Helpers\SemaforoHelper;

class RiesgoController extends Controller
{
    // Mostrar una lista de los riesgos
    public function index()
    {

        $riesgos = Riesgo::all();
        return view('riesgos.index', compact('riesgos'));
    }

    public function listar($proceso_id = null)
    {
        $proceso = Proceso::with('subprocesos.riesgos')->findOrFail($proceso_id);
        $riesgos = $proceso->riesgos->sortBy('id');
        // Función recursiva para obtener indicadores de subprocesos, nietos, etc.
        $riesgos = $this->obtenerRiesgosRecursivos($proceso, $riesgos);
        $riesgos = $riesgos->map(function ($riesgo) {
            $riesgo->semaforo = SemaforoHelper::getSemaforoColor($riesgo->riesgo_valoracion);
            return $riesgo;
        });
        return view('riesgos.index', compact('proceso', 'riesgos'));
    }

    private function obtenerRiesgosRecursivos($proceso, &$riesgos)
    {
        foreach ($proceso->subprocesos as $subproceso) {
            // Fusionar los indicadores del subproceso a la colección de indicadores
            $riesgos = $riesgos->merge($subproceso->riesgos);

            // Recursión para obtener indicadores de los subprocesos de los hijos (nietos, bisnietos...)
            $riesgos = $this->obtenerRiesgosRecursivos($subproceso, $riesgos);


        }
        return $riesgos;
    }


    // Obtener riesgos del usuario actual basados en sus OUOs y Procesos
    public function misRiesgos(Request $request)
    {
        $user = auth()->user();

        // Si es admin, devolver todos
        if ($user->hasRole('admin')) {
            $query = Riesgo::with(['proceso', 'factor']);
        } else {
            // Obtener IDs de procesos donde el usuario tiene acceso a través de sus OUOs
            // Asumiendo que la relación es User -> OUOs -> Procesos
            $procesosIds = $user->ouos()->with('procesos')->get()->pluck('procesos')->flatten()->pluck('id')->unique();

            // También incluir procesos donde el usuario esté asignado directamente (si existe esa relación)
            // $procesosDirectos = $user->procesos->pluck('id');
            // $procesosIds = $procesosIds->merge($procesosDirectos)->unique();

            $query = Riesgo::whereIn('proceso_id', $procesosIds)
                ->with(['proceso', 'factor']);
        }

        // Apply filters if provided
        // Apply filters if provided
        if ($request->has('codigo') && !empty($request->codigo)) {
            $term = $request->codigo;
            $query->where(function ($q) use ($term) {
                $q->where('riesgo_cod', 'like', '%' . $term . '%')
                    ->orWhereHas('proceso', function ($q2) use ($term) {
                        $q2->where('proceso_nombre', 'like', '%' . $term . '%');
                    });
            });
        }

        if ($request->has('nombre') && !empty($request->nombre)) {
            $query->where('riesgo_nombre', 'like', '%' . $request->nombre . '%');
        }

        if ($request->has('nivel') && !empty($request->nivel)) {
            $query->where('riesgo_nivel', $request->nivel);
        }

        if ($request->has('factor') && !empty($request->factor)) {
            $query->where('factor_id', $request->factor);
        }

        if ($request->has('tipo') && !empty($request->tipo)) {
            $query->where('riesgo_tipo', $request->tipo);
        }

        $riesgos = $query->get();

        return response()->json($riesgos);
    }

    // Obtener un riesgo con todas sus relaciones para la vista de detalle
    public function getRiesgoCompleto(Riesgo $riesgo)
    {
        $riesgo->load(['proceso', 'factor', 'obligacion']);
        return response()->json($riesgo);
    }

    // Actualizar Evaluación del Riesgo (Probabilidad e Impacto)
    public function updateEvaluacion(Request $request, Riesgo $riesgo)
    {
        $validated = $request->validate([
            'probabilidad' => 'required|integer',
            'impacto' => 'required|integer',
        ]);

        $riesgo->update([
            'probabilidad' => $validated['probabilidad'],
            'impacto' => $validated['impacto'],
            // El modelo calcula riesgo_valor y riesgo_valoracion automáticamente en el evento updating
        ]);

        return response()->json($riesgo);
    }

    // Actualizar Plan de Tratamiento
    public function updateTratamiento(Request $request, Riesgo $riesgo)
    {
        $validated = $request->validate([
            'riesgo_tratamiento' => 'required|string', // Estrategia: Reducir, Aceptar, etc.
            'controles' => 'nullable|string',
            // Aquí se podrían agregar campos para planes de acción específicos si se decide separar
        ]);

        $riesgo->update($validated);

        return response()->json($riesgo);
    }

    // Actualizar Verificación de Eficacia (Riesgo Residual)
    public function updateVerificacion(Request $request, Riesgo $riesgo)
    {
        $validated = $request->validate([
            'probabilidad_rr' => 'required|integer',
            'impacto_rr' => 'required|integer',
            'fecha_valoracion_rr' => 'required|date',
        ]);

        // Calcular evaluación residual (simple multiplicación por ahora, igual que el inherente)
        $evaluacion_rr = $validated['probabilidad_rr'] * $validated['impacto_rr'];

        // Determinar estado de eficacia (ejemplo simple)
        $estado_eficacia = ($evaluacion_rr < $riesgo->riesgo_valor) ? 'Con Eficacia' : 'Sin eficacia';

        $riesgo->update([
            'probabilidad_rr' => $validated['probabilidad_rr'],
            'impacto_rr' => $validated['impacto_rr'],
            'fecha_valoracion_rr' => $validated['fecha_valoracion_rr'],
            'evaluacion_rr' => $evaluacion_rr,
            'estado_riesgo_rr' => $estado_eficacia
        ]);

        return response()->json($riesgo);
    }

    public function store(Request $request)
    {
        // Crear el nuevo riesgo en la base de datos

        // Inicializar la variable de la obligación
        $obligacion = null;
        $proceso = null;
        $proceso_id = null;

        // Verificar si se recibe un `obligacion_id`
        if ($request->has('obligacion_id') && $request->obligacion_id) {
            // Obtener la obligación y su proceso_id
            $obligacion = Obligacion::find($request->obligacion_id);
            $proceso_id = $obligacion->proceso_id;
            $request->merge(['proceso_id' => $proceso_id]);
        } else {
            $proceso_id = $request->proceso_id;
        }
        // Crear el riesgo
        $riesgo = Riesgo::create($request->all());

        // Si hay una obligación asociada, asociamos el riesgo a la obligación
        if ($obligacion) {
            $obligacion->asociarRiesgo($riesgo);
            $riesgos = $obligacion->riesgos;

        } else {
            $proceso = Proceso::find($request->proceso_id);
            $riesgos = $proceso->riesgos;
        }

        $riesgos->load('proceso');

        // Si no hay obligación, solo devolvemos el riesgo creado
        return response()->json($riesgos);

    }

    // Mostrar un riesgo específico
    public function show(Riesgo $riesgo)
    {

        $riesgo->load('proceso');
        return response()->json($riesgo);
    }

    // Mostrar el formulario para editar un riesgo existente
    public function edit(Riesgo $riesgo)
    {
        return view('riesgos.edit', compact('riesgo'));
    }

    // Actualizar un riesgo existente en la base de datos
    public function update(Request $request, Riesgo $riesgo)
    {
        $riesgo->update($request->all());
        $riesgo->load('proceso');

        return response()->json($riesgo);
    }

    // Eliminar un riesgo de la base de datos
    public function destroy(Riesgo $riesgo)
    {
        $riesgo->delete();

        return response()->json($riesgo);
    }
    // Asignación de Especialistas
    public function listarAsignaciones(Riesgo $riesgo)
    {
        $riesgo->load('especialista');

        return response()->json([
            'actual' => $riesgo->especialista,
            'historial' => [] // Implementar historial si se crea la tabla riesgo_movimientos
        ]);
    }

    public function asignarEspecialista(Request $request, Riesgo $riesgo)
    {
        $request->validate([
            'especialista_id' => 'required|exists:users,id',
        ]);

        $riesgo->especialista_id = $request->especialista_id;
        $riesgo->save();

        $riesgo->load('especialista');

        return response()->json([
            'actual' => $riesgo->especialista,
            'historial' => []
        ]);
    }
}