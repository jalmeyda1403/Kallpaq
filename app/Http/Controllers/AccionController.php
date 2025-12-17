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

class AccionController extends Controller
{
    public function getAccionesPorHallazgo(Hallazgo $hallazgo)
    {
        $acciones = $hallazgo->acciones()
            ->with([
                'responsable:id,name,email',
                'responsable.ouos:id,ouo_nombre',
                'hallazgoProceso.proceso:id,proceso_nombre,proceso_sigla',
                'reprogramaciones' // Eager load reprogramaciones
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
                'responsable:id,name,email',
                'responsable.ouos:id,ouo_nombre',
                'hallazgoProceso.proceso:id,proceso_nombre,cod_proceso',
                'reprogramaciones' // Eager load reprogramaciones
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
        $request->validate([
            'actionType' => 'required|string|in:reprogramar,desestimar',
            'accion_justificacion' => 'required|string',
            'accion_fecha_fin_reprogramada' => 'required_if:actionType,reprogramar|date',
        ]);

        $accion->accion_justificacion = $request->accion_justificacion;

        if ($request->actionType === 'reprogramar') {
            // Guardar historial de reprogramación
            $accion->reprogramaciones()->create([
                'ar_fecha_anterior' => $accion->accion_fecha_fin_reprogramada ?? $accion->accion_fecha_fin_planificada,
                'ar_fecha_nueva' => $request->accion_fecha_fin_reprogramada,
                'ar_justificacion' => $request->accion_justificacion,
                'ar_usuario_id' => auth()->id(),
            ]);

            $accion->accion_fecha_fin_reprogramada = $request->accion_fecha_fin_reprogramada;
        } else { // desestimar
            $accion->accion_estado = 'desestimada';
        }

        $accion->save();

        $this->updateHallazgoAvance($accion->hallazgo);

        return response()->json(['message' => 'Acción gestionada con éxito.']);
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

        $accion->accion_estado = 'finalizada';
        $accion->accion_fecha_fin_real = Carbon::now();
        $accion->save();

        $this->updateHallazgoAvance($accion->hallazgo);

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

    public function uploadEvidencia(Request $request, Accion $accion)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,png,xlsx,xls|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        $hallazgoCod = $accion->hallazgo->hallazgo_cod;
        $accionCod = $accion->accion_cod;
        $pathPrefix = "hallazgos/{$hallazgoCod}/acciones/{$accionCod}";

        $path = $file->store($pathPrefix, 'public');

        $newFile = [
            'path' => $path,
            'name' => $originalName,
        ];

        $existingFiles = json_decode($accion->accion_ruta_evidencia, true) ?: [];
        if (!is_array($existingFiles)) {
            $existingFiles = [];
        }

        $existingFiles[] = $newFile;

        $accion->accion_ruta_evidencia = json_encode($existingFiles);
        $accion->save();

        return response()->json($newFile);
    }

    public function deleteEvidencia(Request $request, Accion $accion)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $pathToDelete = $request->input('path');

        // 1. Delete from storage
        if (Storage::disk('public')->exists($pathToDelete)) {
            Storage::disk('public')->delete($pathToDelete);
        }

        // 2. Update the JSON column
        $existingFiles = json_decode($accion->accion_ruta_evidencia, true) ?: [];
        if (!is_array($existingFiles)) {
            $existingFiles = [];
        }

        $updatedFiles = array_filter($existingFiles, function ($file) use ($pathToDelete) {
            return $file['path'] !== $pathToDelete;
        });

        // Re-index the array to prevent it from becoming an object on empty
        $accion->accion_ruta_evidencia = json_encode(array_values($updatedFiles));
        $accion->save();

        return response()->json(['message' => 'Archivo eliminado con éxito.']);
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
        $acciones = Accion::where('hallazgo_id', $hallazgo->id)
            ->whereHas('hallazgoProceso', function ($query) use ($proceso) {
                $query->where('proceso_id', $proceso->id);
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

        return response()->json($causa);
    }

    public function updateAccion(Request $request, Accion $accion)
    {
        // Validar el estado del hallazgo asociado a la acción
        $this->validarEstadoHallazgo($accion->hallazgo);

        $validatedData = $request->validate([
            'accion_tipo' => 'required|in:inmediata,correctiva',
            'accion_descripcion' => 'required|string',
            'accion_responsable' => 'required|string',
            'accion_responsable_correo' => 'nullable|email',
            'accion_fecha_inicio' => 'required|date',
            'accion_fecha_fin_planificada' => 'required|date',
            'accion_estado' => 'required|string',
            'accion_comentario' => 'nullable|string',
        ]);

        $accion->update($validatedData);

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
            'accion_fecha_fin_real' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png,xlsx,xls|max:10240',
        ]);

        $accion->accion_estado = $request->accion_estado;
        $accion->accion_comentario = $request->accion_comentario;

        if ($request->accion_fecha_fin_real) {
            $accion->accion_fecha_fin_real = $request->accion_fecha_fin_real;
        }

        // Handle file upload if present
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();

            $hallazgoCod = $accion->hallazgo->hallazgo_cod;
            $accionCod = $accion->accion_cod;
            $pathPrefix = "hallazgos/{$hallazgoCod}/acciones/{$accionCod}";

            $path = $file->store($pathPrefix, 'public');

            $newFile = [
                'path' => $path,
                'name' => $originalName,
            ];

            $existingFiles = json_decode($accion->accion_ruta_evidencia, true) ?: [];
            if (!is_array($existingFiles)) {
                $existingFiles = [];
            }

            $existingFiles[] = $newFile;
            $accion->accion_ruta_evidencia = json_encode($existingFiles);
        }

        $accion->save();

        $this->updateHallazgoAvance($accion->hallazgo);

        return response()->json($accion);
    }

    public function storeAccion(Request $request, Hallazgo $hallazgo, Proceso $proceso)
    {
        // Validar el estado del hallazgo
        $this->validarEstadoHallazgo($hallazgo);

        $validatedData = $request->validate([
            'accion_tipo' => 'required|in:inmediata,correctiva',
            'accion_descripcion' => 'required|string',
            'accion_responsable' => 'required|string',
            'accion_responsable_correo' => 'nullable|email',
            'accion_fecha_inicio' => 'required|date',
            'accion_fecha_fin_planificada' => 'required|date',
        ]);

        // Find the corresponding hallazgo_proceso pivot record
        $hallazgoProceso = HallazgoProceso::where('hallazgo_id', $hallazgo->id)
            ->where('proceso_id', $proceso->id)
            ->firstOrFail();

        // Generate accion_cod
        $ultimoAccion = Accion::where('hallazgo_id', $hallazgo->id)->latest('id')->first();
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

        return response()->json($accion, 201);
    }

    public function destroyAccion(Accion $accion)
    {
        // Validar el estado del hallazgo asociado a la acción
        $this->validarEstadoHallazgo($accion->hallazgo);

        $accion->delete();

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
                'causa_metodo',
                'causa_por_que1',
                'causa_por_que2',
                'causa_por_que3',
                'causa_por_que4',
                'causa_por_que5',
                'causa_mano_obra',
                'causa_metodologias',
                'causa_materiales',
                'causa_maquinas',
                'causa_medicion',
                'causa_medio_ambiente',
                'causa_resultado'
            ])
            ->first();

        return view('acciones.imprimir-plan-accion', compact('hallazgo', 'acciones', 'causaRaiz'));
    }
}