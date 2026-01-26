<?php

namespace App\Http\Controllers;

use App\Models\Hallazgo;
use App\Models\Proceso;
use App\Models\Accion;
use App\Models\Causa; // Import the Causa model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\HallazgoProceso;
use App\Models\AccionReprogramacion;

class AccionController extends Controller
{
    public function getAccionesPorHallazgo(Hallazgo $hallazgo)
    {
        $acciones = $hallazgo->acciones()
            ->with([
                'responsable:id,name,email',
                'responsable.ouos:id,ouo_nombre',
                'hallazgoProceso.proceso:id,proceso_nombre,proceso_sigla',
                'reprogramaciones',
                'movimientos.usuario:id,name',
                'avances.usuario:id,name'
            ])
            ->select([
                'id',
                'hallazgo_id',
                'hallazgo_proceso_id',
                'accion_cod',
                'accion_tipo',
                'accion_descripcion',
                'accion_responsable',
                'accion_fecha_inicio',
                'accion_fecha_fin_planificada',
                'accion_fecha_fin_reprogramada',
                'accion_estado',
                'accion_estado',
                'created_at',
                'updated_at'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($acciones);
    }

    /**
     * Obtiene todos los datos necesarios para la vista de Planes de Acción en una sola llamada
     * Esto reduce la latencia al evitar múltiples peticiones HTTP
     */
    /**
     * Obtiene todos los datos necesarios para la vista de Planes de Acción en una sola llamada
     * Optimizado para máximo rendimiento con selects específicos y eager loading eficiente
     */
    public function getPlanesAccionCompleto(Hallazgo $hallazgo)
    {
        // Cargar el hallazgo con sus relaciones usando selects específicos
        $hallazgo->load([
            'procesos:id,proceso_nombre,cod_proceso',
            'especialista:id,name,email',
            'auditor:id,name,email'
        ]);

        // Seleccionar solo los campos necesarios del hallazgo para la respuesta
        $hallazgoData = $hallazgo->only([
            'id',
            'hallazgo_cod',
            'hallazgo_resumen',
            'hallazgo_descripcion',
            'hallazgo_clasificacion',
            'hallazgo_origen',
            'hallazgo_estado',
            'hallazgo_fecha_identificacion',
            'hallazgo_fecha_asignacion',
            'hallazgo_avance',
            'hallazgo_sig'
        ]);

        // Añadir las relaciones cargadas
        $hallazgoData['procesos'] = $hallazgo->procesos;
        $hallazgoData['especialista'] = $hallazgo->especialista;
        $hallazgoData['auditor'] = $hallazgo->auditor;

        // Obtener las acciones con sus relaciones
        $acciones = $hallazgo->acciones()
            ->with([
                'responsable.ouos:id,ouo_nombre',
                'hallazgoProceso.proceso:id,proceso_nombre,proceso_sigla',
                'reprogramaciones',
                'movimientos.usuario:id,name',
                'avances.usuario:id,name'  // Load advances with user
            ])
            ->select([
                'id',
                'hallazgo_id',
                'hallazgo_proceso_id',
                'accion_cod',
                'accion_tipo',
                'accion_descripcion',
                'accion_responsable',
                'accion_fecha_inicio',
                'accion_fecha_fin_planificada',
                'accion_fecha_fin_reprogramada',
                'accion_estado',
                'accion_estado',
                'created_at',
                'updated_at'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        // Obtener la causa raíz
        $causaRaiz = $hallazgo->causa()
            ->select([
                'id',
                'hallazgo_id',
                'hc_metodo',
                'hc_por_que1',
                'hc_por_que2',
                'hc_por_que3',
                'hc_por_que4',
                'hc_por_que5',
                'hc_mano_obra',
                'hc_metodologias',
                'hc_materiales',
                'hc_maquinas',
                'hc_medicion',
                'hc_medio_ambiente',
                'hc_resultado'
            ])
            ->first();

        // Devolver todo en una sola respuesta optimizada
        return response()->json([
            'hallazgo' => $hallazgoData,
            'acciones' => $acciones,
            'causaRaiz' => $causaRaiz
        ]);
    }


    public function reprogramar(Request $request, Accion $accion)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Usuario no autenticado.'], 401);
        }

        $request->validate([
            'actionType' => 'required|string|in:reprogramar,desestimar',
            'accion_justificacion' => 'required|string',
            'accion_fecha_fin_reprogramada' => 'required_if:actionType,reprogramar|date',
        ]);

        $accion->accion_justificacion = $request->accion_justificacion;

        if ($request->actionType === 'reprogramar') {
            // Guardar historial de reprogramación con estado solicitado
            $accion->reprogramaciones()->create([
                'ar_fecha_anterior' => $accion->accion_fecha_fin_reprogramada ?? $accion->accion_fecha_fin_planificada,
                'ar_fecha_nueva' => $request->accion_fecha_fin_reprogramada,
                'ar_justificacion' => $request->accion_justificacion,
                'ar_usuario_id' => auth()->id() ?? $accion->hallazgo->especialista_id, // Fallback to specialist if somehow null
                'ar_estado' => 'solicitado',
            ]);

            // No actualizamos la fecha reprogramada hasta que sea aprobada
            // $accion->accion_fecha_fin_reprogramada = $request->accion_fecha_fin_reprogramada;

            // Registrar movimiento
            $accion->movimientos()->create([
                'user_id' => auth()->id() ?? $accion->hallazgo->especialista_id,
                'estado' => $accion->accion_estado,
                'comentario' => "Solicitud de Reprogramación: " . $request->accion_justificacion,
            ]);

            return response()->json(['message' => 'Solicitud de reprogramación enviada con éxito. Pendiente de aprobación.']);

        } else { // desestimar
            $accion->accion_estado = 'desestimada';
            $accion->save();

            // Registrar movimiento
            $accion->movimientos()->create([
                'user_id' => auth()->id() ?? $accion->hallazgo->especialista_id,
                'estado' => $accion->accion_estado,
                'comentario' => "Acción desestimada: " . $request->accion_justificacion,
            ]);

            $this->updateHallazgoAvance($accion->hallazgo);
            return response()->json(['message' => 'Acción desestimada con éxito.']);
        }
    }

    public function aprobarReprogramacion(Request $request, Accion $accion, AccionReprogramacion $reprogramacion)
    {
        // Solo el especialista puede aprobar
        if (auth()->id() !== $accion->hallazgo->especialista_id) {
            return response()->json(['error' => 'Solo el especialista asignado puede aprobar reprogramaciones.'], 403);
        }

        // Verify state
        /* if ($reprogramacion->ar_estado !== 'solicitado') {
             return response()->json(['error' => 'Esta solicitud no está en estado solicitado.'], 400);
        } */

        $reprogramacion->update(['ar_estado' => 'aprobado']);

        // Assign the new date to the action
        // Ensure we are getting the value correctly.
        $accion->accion_fecha_fin_reprogramada = $reprogramacion->ar_fecha_nueva;
        $accion->accion_estado = 'reprogramada';
        $accion->save();

        $accion->movimientos()->create([
            'user_id' => auth()->id(),
            'estado' => 'reprogramada',
            'comentario' => 'Reprogramación aprobada. Nueva fecha: ' . $reprogramacion->ar_fecha_nueva->format('d/m/Y'),
        ]);

        return response()->json(['message' => 'Reprogramación aprobada con éxito.']);
    }

    public function rechazarReprogramacion(Request $request, Accion $accion, AccionReprogramacion $reprogramacion)
    {
        // Solo el especialista puede rechazar
        if (auth()->id() !== $accion->hallazgo->especialista_id) {
            return response()->json(['error' => 'Solo el especialista asignado puede rechazar reprogramaciones.'], 403);
        }

        $reprogramacion->update(['ar_estado' => 'rechazado']);

        $accion->accion_estado = 'observado';
        $accion->save();

        $accion->movimientos()->create([
            'user_id' => auth()->id(),
            'estado' => 'observado',
            'comentario' => 'Reprogramación rechazada/observada: ' . $request->motivo,
        ]);

        return response()->json(['message' => 'Reprogramación observada con éxito.']);
    }

    public function concluir(Request $request, Accion $accion)
    {
        // Files are now uploaded via uploadEvidencia.
        // This method just finalizes the action.

        // Optional: Check if there is at least one evidence file before concluding.
        $existingFiles = json_decode($accion->accion_ruta_evidencia, true) ?: [];
        if (empty($existingFiles)) {
            return response()->json(['message' => 'Debe adjuntar al menos un archivo de evidencia para poder concluir la acción.'], 422);
        }

        $accion->accion_estado = 'implementada';
        $accion->accion_fecha_fin_real = Carbon::now();
        $accion->save();

        $this->updateHallazgoAvance($accion->hallazgo);
        $accion->hallazgo->verificarYActualizarEstado();

        return response()->json(['message' => 'Acción concluida con éxito.']);
    }

    private function updateHallazgoAvance(Hallazgo $hallazgo)
    {
        $totalAcciones = $hallazgo->acciones()->count();
        if ($totalAcciones === 0) {
            $hallazgo->hallazgo_avance = 0;
        } else {
            $accionesCompletadas = $hallazgo->acciones()
                ->whereIn('accion_estado', ['finalizada', 'desestimada'])
                ->count();
            $hallazgo->hallazgo_avance = ($accionesCompletadas / $totalAcciones) * 100;
        }
        $hallazgo->save();
    }

    public function downloadEvidencia($path)
    {
        // Ensure the path is secure and exists
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found.');
        }

        return response()->download(storage_path('app/public/' . $path));
    }


    public function listarCausaRaiz(Hallazgo $hallazgo)
    {
        $causa = $hallazgo->causa()
            ->select([
                'id',
                'hallazgo_id',
                'hc_metodo',
                'hc_por_que1',
                'hc_por_que2',
                'hc_por_que3',
                'hc_por_que4',
                'hc_por_que5',
                'hc_mano_obra',
                'hc_metodologias',
                'hc_materiales',
                'hc_maquinas',
                'hc_medicion',
                'hc_medio_ambiente',
                'hc_resultado'
            ])
            ->first();

        return response()->json($causa);
    }

    public function listarAcciones(Hallazgo $hallazgo, Proceso $proceso)
    {
        // Fetch actions related to the specific hallazgo and process
        // Eager load necessary relationships for the frontend display
        $acciones = Accion::where('hallazgo_id', '=', $hallazgo->id, 'and')
            ->whereHas('hallazgoProceso', function ($query) use ($proceso) {
                $query->where('proceso_id', '=', $proceso->id, 'and');
            })
            ->with('responsable.ouos', 'hallazgoProceso.proceso')
            ->get();

        return response()->json($acciones);
    }

    public function storeOrUpdateCausaRaiz(Request $request, Hallazgo $hallazgo)
    {
        // Validar el estado del hallazgo
        $this->validarEstadoHallazgo($hallazgo);

        $validatedData = $request->validate([
            'hc_metodo' => 'required|string',
            'hc_por_que1' => 'nullable|string',
            'hc_por_que2' => 'nullable|string',
            'hc_por_que3' => 'nullable|string',
            'hc_por_que4' => 'nullable|string',
            'hc_por_que5' => 'nullable|string',
            'hc_mano_obra' => 'nullable|string',
            'hc_metodologias' => 'nullable|string',
            'hc_materiales' => 'nullable|string',
            'hc_maquinas' => 'nullable|string',
            'hc_medicion' => 'nullable|string',
            'hc_medio_ambiente' => 'nullable|string',
            'hc_resultado' => 'nullable|string',
        ]);

        // Find existing Causa or create a new one
        $causa = Causa::firstOrNew(['hallazgo_id' => $hallazgo->id]);

        // Fill and save the data
        $causa->fill($validatedData);
        $causa->hallazgo_id = $hallazgo->id; // Ensure hallazgo_id is set
        $causa->save();

        // Auto-transition to 'evaluado' if currently 'creado'
        if ($hallazgo->hallazgo_estado === 'creado') {
            $hallazgo->update(['hallazgo_estado' => 'evaluado']);
        }

        return response()->json($causa);
    }

    public function updateAccion(Request $request, Accion $accion)
    {
        // Bloquear al especialista
        if (auth()->id() === $accion->hallazgo->especialista_id) {
            return response()->json(['error' => 'No tiene permisos para editar acciones (Rol: Especialista).'], 403);
        }
        // Validar el estado del hallazgo asociado a la acción
        $this->validarEstadoHallazgo($accion->hallazgo);

        $validatedData = $request->validate([
            'accion_tipo' => 'required|in:inmediata,correctiva,corrección,acción correctiva',
            'accion_descripcion' => 'required|string',
            'accion_responsable' => 'required|string',
            'accion_responsable_correo' => 'nullable|email',
            'accion_fecha_inicio' => 'required|date',
            'accion_fecha_fin_planificada' => 'required|date',
            'accion_estado' => 'required|string',
            'accion_comentario' => 'nullable|string',
        ]);

        $accion->update($validatedData);
        $accion->hallazgo->verificarYActualizarEstado();

        return response()->json($accion);
    }

    public function registrarAvance(Request $request, Accion $accion)
    {
        // Validar el estado del hallazgo asociado a la acción
        // Permitimos registrar avance en estados donde se permite gestión
        // $this->validarEstadoHallazgo($accion->hallazgo);

        $validatedData = $request->validate([
            'accion_estado' => 'required|string',
            'accion_comentario' => 'nullable|string',
            'accion_avance_porcentaje' => 'required|integer|min:0|max:100',
            'accion_fecha_fin_real' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png,xlsx,xls|max:10240',
        ]);

        // Update Parent Action State
        $accion->accion_estado = $request->accion_estado;
        // $accion->accion_comentario = $request->accion_comentario; // We now store comments in history

        if ($request->accion_fecha_fin_real) {
            $accion->accion_fecha_fin_real = $request->accion_fecha_fin_real;
        }

        // Handle file upload if present
        $evidencePathJson = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();

            $hallazgoCod = $accion->hallazgo->hallazgo_cod;
            $accionCod = $accion->accion_cod;
            // Use timestamp to allow same filename multiple times in different advances
            $timestamp = time();
            $pathPrefix = "hallazgos/{$hallazgoCod}/acciones/{$accionCod}/avances";

            $path = $file->store($pathPrefix, 'public');

            $newFile = [
                'path' => $path,
                'name' => $originalName,
                'uploaded_at' => now()->toIso8601String()
            ];

            // Store as JSON because the column anticipates strict structure, or array of files
            // Ideally we support multiple files per advance, but input is single 'file'.
            // Storing as array for future proofing.
            $evidencePathJson = json_encode([$newFile]);
        }

        $accion->save();

        // Create Valid History Record
        $accion->avances()->create([
            'accion_avance_porcentaje' => $request->input('accion_avance_porcentaje', 0),
            'accion_avance_comentario' => $request->accion_comentario,
            'accion_avance_estado' => $request->accion_estado,
            'accion_avance_evidencia' => $evidencePathJson,
            'user_id' => auth()->id()
        ]);

        // Also log movement for general compatibility
        $accion->movimientos()->create([
            'user_id' => auth()->id(),
            'estado' => $accion->accion_estado,
            'comentario' => ($request->accion_comentario ?: "Registro de avance") . " (" . $request->input('accion_avance_porcentaje', 0) . "%)",
        ]);

        $this->updateHallazgoAvance($accion->hallazgo);
        $accion->hallazgo->verificarYActualizarEstado();

        return response()->json($accion);
    }

    public function storeAccion(Request $request, Hallazgo $hallazgo, Proceso $proceso)
    {
        // Validar el estado del hallazgo
        $this->validarEstadoHallazgo($hallazgo);

        $validatedData = $request->validate([
            'accion_tipo' => 'required|in:inmediata,correctiva,corrección,acción correctiva',
            'accion_descripcion' => 'required|string',
            'accion_responsable' => 'required|string',
            'accion_responsable_correo' => 'nullable|email',
            'accion_fecha_inicio' => 'required|date',
            'accion_fecha_fin_planificada' => 'required|date',
        ]);

        // Find the corresponding hallazgo_proceso pivot record
        $hallazgoProceso = HallazgoProceso::where('hallazgo_id', '=', $hallazgo->id, 'and')
            ->where('proceso_id', '=', $proceso->id, 'and')
            ->firstOrFail();

        // Generate accion_cod
        $ultimoAccion = Accion::where('hallazgo_id', '=', $hallazgo->id, 'and')->latest('id')->first();
        if ($ultimoAccion) {
            $parts = explode('-', $ultimoAccion->accion_cod);
            $correlativo = (int) end($parts) + 1;
        } else {
            $correlativo = 1;
        }
        $accionCod = $hallazgo->hallazgo_cod . '-' . sprintf('%03d', $correlativo);

        $validatedData['hallazgo_id'] = $hallazgo->id;
        $validatedData['hallazgo_proceso_id'] = $hallazgoProceso->id;
        $validatedData['accion_cod'] = $accionCod;
        $validatedData['accion_estado'] = 'programada'; // Set initial state to 'programada'
        $validatedData['accion_ciclo'] = $hallazgo->hallazgo_ciclo ?? 0; // Copy hallazgo_ciclo to accion_ciclo

        $accion = Accion::create($validatedData);

        // Auto-transition to 'evaluado' if currently 'creado'
        if ($hallazgo->hallazgo_estado === 'creado') {
            $hallazgo->update(['hallazgo_estado' => 'evaluado']);
        }

        $hallazgo->verificarYActualizarEstado();

        return response()->json($accion, 201);
    }

    public function destroyAccion(Accion $accion)
    {
        // Validar el estado del hallazgo asociado a la acción
        $this->validarEstadoHallazgo($accion->hallazgo);

        Accion::destroy($accion->id);

        return response()->json(['message' => 'Acción eliminada con éxito.']);
    }

    /**
     * Valida que el estado del hallazgo permita realizar acciones
     *
     * @param Hallazgo $hallazgo
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @return void
     */
    private function validarEstadoHallazgo(Hallazgo $hallazgo)
    {
        $estadosPermitidos = ['creado', 'modificado', 'evaluado'];
        if (!in_array($hallazgo->hallazgo_estado, $estadosPermitidos)) {
            abort(403, 'No se pueden crear o modificar acciones en este estado de hallazgo. El hallazgo debe estar en estado \'creado\', \'modificado\' o \'evaluado\'.');
        }
    }

    /**
     * Genera un PDF del plan de acción
     *
     * @param Hallazgo $hallazgo
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function imprimirPlanAccion(Hallazgo $hallazgo)
    {
        // Cargar las relaciones necesarias
        $hallazgo->load([
            'procesos:id,proceso_nombre,cod_proceso,proceso_sigla',
            'especialista:id,name,email',
            'auditor:id,name,email'
        ]);

        // Obtener las acciones del hallazgo
        $acciones = $hallazgo->acciones()
            ->with([
                'responsable:id,name,email',
                'responsable.ouos:id,ouo_nombre',
                'hallazgoProceso.proceso:id,proceso_nombre,cod_proceso'
            ])
            ->select([
                'id',
                'hallazgo_id',
                'hallazgo_proceso_id',
                'accion_cod',
                'accion_tipo',
                'accion_descripcion',
                'accion_responsable',
                'accion_fecha_inicio',
                'accion_fecha_fin_planificada',
                'accion_fecha_fin_reprogramada',
                'accion_estado',
                'accion_ruta_evidencia',
                'accion_ciclo',
                'created_at',
                'updated_at'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        // Obtener la causa raíz
        $causaRaiz = $hallazgo->causa()
            ->select([
                'id',
                'hallazgo_id',
                'hc_metodo',
                'hc_por_que1',
                'hc_por_que2',
                'hc_por_que3',
                'hc_por_que4',
                'hc_por_que5',
                'hc_mano_obra',
                'hc_metodologias',
                'hc_materiales',
                'hc_maquinas',
                'hc_medicion',
                'hc_medio_ambiente',
                'hc_resultado'
            ])
            ->first();

        return view('acciones.imprimir-plan-accion', compact('hallazgo', 'acciones', 'causaRaiz'));
    }
    public function descargarPdfPlanAccion(Hallazgo $hallazgo)
    {
        $acciones = $hallazgo->acciones;

        // Obtener la causa raíz (reusing query logic)
        $causaRaiz = $hallazgo->causa()
            ->select([
                'id',
                'hallazgo_id',
                'hc_metodo',
                'hc_por_que1',
                'hc_por_que2',
                'hc_por_que3',
                'hc_por_que4',
                'hc_por_que5',
                'hc_mano_obra',
                'hc_metodologias',
                'hc_materiales',
                'hc_maquinas',
                'hc_medicion',
                'hc_medio_ambiente',
                'hc_resultado'
            ])
            ->first();

        // Generate PDF using DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('acciones.imprimir-plan-accion', compact('hallazgo', 'acciones', 'causaRaiz'));

        // Optional: Set options if needed, e.g., enable remote images (like the logo)
        $pdf->setOptions(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true]);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Plan_Accion_' . $hallazgo->hallazgo_cod . '.pdf');
    }
}