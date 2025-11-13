<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Requerimiento;
use App\Models\Especialista;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use App\Models\RequerimientoMovimiento;
use App\Models\RequerimientoEvaluacion;

use App\Models\RequerimientoAvance;
use Barryvdh\DomPDF\Facade\Pdf as PDF;




class RequerimientoController extends Controller
{


    // Método para mostrar la lista de requerimientos como Web API
    public function webApiIndex(Request $request)
    {
        $especialistas = Especialista::with('user')
            ->where('estado', 1)
            ->get();

        $allPossibleStatuses = ['creado', 'aprobado', 'evaluado', 'desestimado', 'asignado', 'atendido'];
        $statuses = ['aprobado', 'evaluado', 'desestimado', 'asignado', 'atendido']; // Default for admin view

        if ($request->filled('mine') && $request->mine === 'true') {
            $statuses = $allPossibleStatuses; // Include 'creado' for 'mine' view
        }

        $requerimientos = Requerimiento::with('proceso', 'especialista', 'avance', 'movimientos')
            ->whereIn('estado', $statuses)
            ->orderByRaw("FIELD(estado, 'creado', 'aprobado', 'desestimado', 'asignado', 'atendido')")
            ->when($request->filled('buscar_requerimiento'), function ($query) use ($request) {
                return $query->where('asunto', 'like', '%' . $request->buscar_requerimiento . '%');
            })
            ->when($request->filled('especialista_id'), function ($query) use ($request) {
                return $query->where('especialista_id', $request->especialista_id);
            })
            ->when($request->filled('estado'), function ($query) use ($request) {
                return $query->where('estado', $request->estado);
            })
            ->when($request->filled('mine') && $request->mine === 'true', function ($query) {
                return $query->where('facilitador_id', Auth::id());
            })
            ->get();

        return response()->json(compact('requerimientos', 'especialistas', 'statuses'));
    }

    // Método para mostrar la lista de requerimientos
    public function index(Request $request)
    {
        $especialistas = Especialista::with('user') // Carga la relación (para {{ especialista.nombres }})
            ->where('estado', 1)
            ->get();
        $statuses = ['aprobado', 'evaluado', 'desestimado', 'asignado', 'atendido'];
        $requerimientos = Requerimiento::whereIn('estado', ['aprobado', 'evaluado', 'desestimado', 'asignado', 'atendido'])
            ->orderByRaw("FIELD(estado, 'aprobado', 'desestimado', 'asignado', 'atendido')")
            ->when($request->filled('buscar_requerimiento'), function ($query) use ($request) {
                return $query->where('asunto', 'like', '%' . $request->buscar_requerimiento . '%');
            })
            ->when($request->filled('especialista_id'), function ($query) use ($request) {
                return $query->where('especialista_id', $request->especialista_id);
            })
            ->when($request->filled('estado'), function ($query) use ($request) {
                return $query->where('estado', $request->estado);
            })
            ->get();
        return view('requerimientos.index', compact('requerimientos', 'especialistas', 'statuses'));
    }


    // Método para almacenar un nuevo requerimiento
    public function store(Request $request)
    {
        try {
            $request->validate([
                'asunto' => 'required|string',
                'proceso_id' => 'required|exists:procesos,id',
                'justificacion' => 'required|string',
                'descripcion' => 'required|string'
            ]);
            // Crear un nuevo requerimiento
            $requerimiento = new Requerimiento();
            $requerimiento->asunto = $request->asunto;
            $requerimiento->proceso_id = $request->proceso_id;
            $requerimiento->facilitador_id = Auth::id();
            $requerimiento->justificacion = $request->justificacion;
            $requerimiento->descripcion = $request->descripcion;
            $requerimiento->estado = 'creado';
            $requerimiento->save();

            // Mail::to($propietario->email)->send(new RequerimientoCreado($requerimiento));

            return response()->json([
                'requerimiento_id' => $requerimiento->id,
                'message' => 'Requerimiento creado con éxito.'
            ], 201);

        } catch (\Exception $e) {
            // Registrar el error en laravel.log
            Log::error('Error al crear requerimiento', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            // Retornar un mensaje de error al frontend
            return response()->json([
                'message' => 'Ocurrió un error al crear el requerimiento.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Método para actualizar un requerimiento existente
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'asunto' => 'required|string',
                'proceso_id' => 'required|exists:procesos,id',
                'justificacion' => 'required|string',
                'descripcion' => 'required|string'
            ]);

            $requerimiento = Requerimiento::findOrFail($id);

            // Ensure only the facilitator who created it can update it (optional, but good for security)
            if ($requerimiento->facilitador_id !== Auth::id()) {
                return response()->json(['message' => 'No autorizado para actualizar este requerimiento.'], 403);
            }

            $requerimiento->asunto = $request->asunto;
            $requerimiento->proceso_id = $request->proceso_id;
            $requerimiento->justificacion = $request->justificacion;
            $requerimiento->descripcion = $request->descripcion;
            // Do not change status here, status changes are handled by specific actions like submitForEvaluation
            $requerimiento->save();

            return response()->json([
                'requerimiento_id' => $requerimiento->id,
                'message' => 'Requerimiento actualizado con éxito.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al actualizar requerimiento', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'message' => 'Ocurrió un error al actualizar el requerimiento.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadDocument(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
            'document_type' => 'required|string', // e.g., 'signed_requerimiento', 'other_document'
        ]);

        $requerimiento = Requerimiento::findOrFail($id);

        $path = $request->file('file')->store('requerimientos/' . $id . '/' . $request->document_type, 'public');
        $filename = $request->file('file')->getClientOriginalName();

        if ($request->document_type === 'signed_requerimiento') {
            $files = json_decode($requerimiento->ruta_archivo_requerimiento, true);
            if (!is_array($files)) {
                $files = [];
            }
            $newFile = ['path' => $path, 'name' => $filename];
            $files[] = $newFile;
            $requerimiento->ruta_archivo_requerimiento = json_encode($files);
        }

        $requerimiento->save();

        return response()->json([
            'message' => 'Documento subido con éxito.',
            'file' => [
                'path' => Storage::url($path),
                'name' => $filename,
            ]
        ]);
    }

    public function submitForEvaluation($id)
    {
        $requerimiento = Requerimiento::findOrFail($id);
        $requerimiento->estado = 'aprobado';
        $requerimiento->save();

        // TODO: Implement notification to Supervisor
        // Example: Notification::send($supervisorUsers, new RequerimientoSubmitted($requerimiento));

        return response()->json(['message' => 'Requerimiento enviado para evaluación con éxito.']);
    }

    public function printRequerimiento($id)
    {
        $requerimiento = Requerimiento::with('proceso', 'especialista.user', 'solicitante', 'evaluacion')->findOrFail($id);
        $pdf = PDF::loadView('requerimientos.print', compact('requerimiento'));
        return $pdf->stream('requerimiento-' . $id . '.pdf');
    }

    // Método para mostrar la lista de requerimientos

    public function asignados($rol)
    {
        $user = Auth::user();

        if ($rol === 'especialista') {
            $requerimientos = Requerimiento::where('especialista_id', $user->id)
                ->where('estado', 'en_proceso')->get();
        } elseif (in_array($rol, ['facilitador', 'subgerente'])) {
            $procesoIds = $user->procesos->pluck('id');
            $requerimientos = Requerimiento::whereIn('proceso_id', $procesoIds)
                ->whereIn('estado', ['asignado', 'en proceso'])->get();
        } elseif (in_array($rol, ['supervisor', 'admin'])) {
            $requerimientos = Requerimiento::whereIn('estado', ['asignado', 'en proceso'])->get();
        }

        return view('requerimientos.index', compact('requerimientos'));
    }
    public function atendidos($rol)
    {
        $user = Auth::user();

        if ($rol === 'especialista') {
            $requerimientos = Requerimiento::where('especialista_id', $user->id)
                ->where('estado', 'finalizado')->get();
        } elseif (in_array($rol, ['facilitador', 'subgerente'])) {
            $procesoIds = $user->procesos->pluck('id');
            $requerimientos = Requerimiento::whereIn('proceso_id', $procesoIds)
                ->where('estado', 'finalizado')->get();
        } elseif (in_array($rol, ['supervisor', 'admin'])) {
            $requerimientos = Requerimiento::where('estado', 'finalizado')->get();
        }

        return view('requerimientos.atendidos', compact('requerimientos'));
    }

    public function desestimar(Request $request, $id)
    {
        $request->validate([
            'comentario' => 'required|string',
            'file' => 'required|file|max:10240', // 10MB Max
        ]);

        $requerimiento = Requerimiento::findOrFail($id);
        $path = $request->file('file')->store('requerimientos/' . $id . '/deestimacion', 'public');
        $requerimiento->update([
            'estado' => 'desestimado',
            'fecha_fin' => now(),
            'comentario' => $request->comentario,
            'ruta_archivo_desistimacion' => $path,
        ]);

        return response()->json(['message' => 'Requerimiento desestimado con éxito.']);
    }

    public function finalizar($id)
    {
        $requerimiento = Requerimiento::with('avance')->findOrFail($id);

        if ($requerimiento->estado !== 'asignado') {
            return response()->json(['message' => 'El requerimiento no se puede finalizar porque no está en el estado "asignado".'], 422);
        }

        if (!$requerimiento->avance || $requerimiento->avance->avance_registrado < 100) {
            return response()->json(['message' => 'El requerimiento no se puede finalizar hasta que el avance sea del 100%.'], 422);
        }

        DB::beginTransaction();
        try {
            $requerimiento->update([
                'estado' => 'atendido',
                'fecha_fin' => now(),
            ]);

            $requerimiento->movimientos()->create([
                'estado' => 'atendido',
                'user_id' => Auth::id(),
                'comentario' => 'El requerimiento ha sido finalizado.',
            ]);

            DB::commit();

            return response()->json(['message' => 'Requerimiento finalizado con éxito.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al finalizar el requerimiento: ' . $e->getMessage());
            return response()->json(['message' => 'Error al finalizar el requerimiento. Intente nuevamente.'], 500);
        }
    }


    public function creados(Request $request)
    {
        $especialistas = User::role('Especialista')->get();
        $statuses = Requerimiento::select('estado')->distinct()->get();
        $requerimientos = Requerimiento::whereIn('estado', ['aprobado', 'evaluado'])
            ->when($request->especialista_id, function ($query, $especialista_id) {
                return $query->where('especialista_id', $especialista_id);
            })
            ->when($request->estado, function ($query, $estado) {
                return $query->where('estado', $estado);
            })
            ->get();
        return view('requerimientos.index', compact('requerimientos', 'especialistas', 'statuses'));
    }
    public function seguimiento()
    {
        $requerimientos = Requerimiento::all();
        return view('requerimientos.seguimiento', compact('requerimientos'));
    }

    public function asignarEspecialista(Request $request, $id)
    {
        $requerimiento = Requerimiento::findOrFail($id);
        // Regla de negocio: Solo permitir asignación en estados específicos
        $estadosPermitidos = ['evaluado', 'asignado'];
        if (!in_array($requerimiento->estado, $estadosPermitidos)) {
            return response()->json(['message' => 'La asignación no está permitida en el estado actual del requerimiento.'], 403);
        }

        $plazos = [
            'baja' => 30,
            'media' => 60,
            'alta' => 90,
            'muy alta' => 120,
        ];

        $nivel = strtolower($requerimiento->complejidad ?? 'media');
        $plazoDias = $plazos[$nivel];
        $plazoReasignacion = 20;

        try {
            DB::beginTransaction();
            if ($requerimiento->especialista_id) {
                // Reasignación
                $fechaLimite = $requerimiento->fecha_limite->addDays($plazoReasignacion);
                $requerimiento->update([
                    'especialista_id' => $request->especialista_id,
                    'fecha_asignacion' => now(),
                    'fecha_limite' => $fechaLimite,
                ]);
            } else {
                // Primera asignación
                $fechaLimite = now()->addDays($plazoDias);
                $requerimiento->update([
                    'estado' => 'asignado',
                    'especialista_id' => $request->especialista_id,
                    'fecha_asignacion' => now(),
                    'fecha_limite' => $fechaLimite,
                    'fecha_inicio' => now()
                ]);

                $requerimiento->avance()->updateOrCreate(
                    ['requerimiento_id' => $requerimiento->id],
                    ['avance_registrado' => 2]
                );
            }

            $requerimiento->movimientos()->create([
                'estado' => 'asignado',
                'user_id' => Auth::id(),
                'comentario' => 'Asignación del requerimiento al especialista ID: ' . $request->especialista_id,
            ]);

            DB::commit();

            return response()->json(['message' => 'Asignación guardada con éxito.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al asignar requerimiento: ' . $e->getMessage());
            return response()->json(['message' => 'Error al guardar. Intente nuevamente.'], 500);
        }
    }

    public function getAvance($id)
    {
        $requerimiento = Requerimiento::with('avance')->findOrFail($id);
        $avance = $requerimiento->avance;
        return response()->json($avance);
    }

    public function guardarAvance(Request $request, $id)
    {
        Log::info('guardarAvance request data: ', $request->all());
        $requerimiento = Requerimiento::findOrFail($id);

        $requerimiento->avance()->updateOrCreate(
            ['requerimiento_id' => $id],
            $request->all()
        );

        return response()->json(['message' => 'Avance guardado con éxito.']);
    }

    public function listarEvidencias($id)
    {
        $avance = RequerimientoAvance::where('requerimiento_id', $id)->first();

        if ($avance && $avance->ruta_evidencias) {
            return response()->json(json_decode($avance->ruta_evidencias));
        }

        return response()->json([]);
    }

    public function subirEvidencia(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB Max
        ]);

        $path = $request->file('file')->store('requerimientos/' . $id . '/evidencias', 'public');

        $avance = RequerimientoAvance::firstOrCreate(['requerimiento_id' => $id]);

        Log::info('ruta_evidencias from DB: ' . $avance->ruta_evidencias);

        $evidencias = $avance->ruta_evidencias ? json_decode($avance->ruta_evidencias, true) : [];
        $evidencias[] = [
            'name' => $request->file('file')->getClientOriginalName(),
            'path' => $path,
            'url' => asset('storage/' . $path),
        ];

        $avance->update(['ruta_evidencias' => json_encode($evidencias)]);

        return response()->json($evidencias);
    }

    public function eliminarEvidencia(Request $request, $id)
    {
        $request->validate([
            'file_path' => 'required|string',
        ]);

        $avance = RequerimientoAvance::where('requerimiento_id', $id)->firstOrFail();

        $evidencias = json_decode($avance->ruta_evidencias, true);
        $newEvidencias = array_filter($evidencias, function ($evidencia) use ($request) {
            return $evidencia['path'] !== $request->file_path;
        });

        Storage::disk('public')->delete($request->file_path);

        $avance->update(['ruta_evidencias' => json_encode(array_values($newEvidencias))]);

        return response()->json(['message' => 'Evidencia eliminada con éxito.']);
    }

    public function descargarEvidencia(Request $request, $id)
    {
        $request->validate([
            'file_path' => 'required|string',
        ]);

        if (Storage::disk('public')->exists($request->file_path)) {
            $contents = Storage::disk('public')->get($request->file_path);
            $filename = basename($request->file_path);
            return response($contents)
                ->header('Content-Type', mime_content_type(storage_path('app/public/' . $request->file_path)))
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }

        return abort(404);
    }


    public function getEvaluacion($id)
    {
        $evaluacion = RequerimientoEvaluacion::where('requerimiento_id', $id)->latest()->first();
        return response()->json($evaluacion);
    }

    public function storeEvaluacion(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $requerimiento = Requerimiento::findOrFail($id);
            $isFromWizard = $request->input('from_wizard', false);

            // Regla de negocio: Permitir evaluación en 'aprobado' o 'evaluado',
            // O si viene del wizard y el estado es 'creado'.
            $isWizardException = $isFromWizard && $requerimiento->estado === 'creado';
            $estadosPermitidos = ['aprobado', 'evaluado'];

            if (!in_array($requerimiento->estado, $estadosPermitidos) && !$isWizardException) {
                return response()->json(['message' => 'La evaluación no está permitida en el estado actual del requerimiento.'], 403);
            }

            $data = [
                'requerimiento_id' => $id,
                'num_actividades' => $request->actividades,
                'num_areas' => $request->areas,
                'num_requisitos' => $request->requisitos,
                'nivel_documentacion' => $request->documentacion,
                'impacto_requerimiento' => $request->impacto,
                'complejidad_valor' => $request->complejidad_valor,
                'complejidad_nivel' => $request->complejidad_nivel,
                'fecha_evaluacion' => now(),
            ];

            RequerimientoEvaluacion::updateOrCreate(['requerimiento_id' => $id], $data);

            // Solo actualiza el estado si no es la excepción del wizard
            // (en el wizard, el estado se cambia al final con 'submitForEvaluation')
            if (!$isWizardException) {
                $requerimiento->update([
                    'complejidad' => $request->complejidad_nivel,
                    'estado' => 'evaluado',
                ]);

                $requerimiento->movimientos()->create([
                    'estado' => 'evaluado',
                    'user_id' => Auth::id(),
                    'comentario' => 'Evaluación de complejidad registrada con puntaje: ' . $request->complejidad_valor . ' y nivel: ' . $request->complejidad_nivel,
                ]);
            } else {
                // Si es la excepción del wizard, solo actualizamos la complejidad sin cambiar el estado
                $requerimiento->update([
                    'complejidad' => $request->complejidad_nivel,
                ]);
            }


            DB::commit();

            return response()->json(['message' => 'Evaluación guardada con éxito.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al guardar evaluación del requerimiento: ' . $e->getMessage());

            return response()->json(['message' => 'Error al guardar la evaluación. Intente nuevamente.'], 500);
        }
    }

    public function show($id)
    {
        $requerimiento = Requerimiento::with(['avance', 'evaluacion'])->findOrFail($id);
        return response()->json($requerimiento);
    }

    public function deleteDocument(Request $request, $id)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $requerimiento = Requerimiento::findOrFail($id);

        $files = json_decode($requerimiento->ruta_archivo_requerimiento, true);
        if (!is_array($files)) {
            $files = [];
        }

        $pathToDelete = $request->path;

        $files = array_filter($files, function ($file) use ($pathToDelete) {
            return $file['path'] !== $pathToDelete;
        });

        Storage::disk('public')->delete($pathToDelete);

        $requerimiento->ruta_archivo_requerimiento = json_encode(array_values($files));
        $requerimiento->save();

        return response()->json(['message' => 'Documento eliminado con éxito.']);
    }
}
