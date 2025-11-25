<?php

namespace App\Http\Controllers;

use App\Models\EncuestaSatisfaccion;
use App\Models\EncuestaSatisfaccionDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EncuestaSatisfaccionController extends Controller
{
    public function index(Request $request)
    {

        $query = EncuestaSatisfaccion::with(['proceso', 'detalles']);

        // Filter by es_periodo if provided and not empty
        if ($request->filled('es_periodo')) {
            $query->where('es_periodo', $request->es_periodo);
        }

        // Filter by es_anio if provided and not empty
        if ($request->filled('es_anio')) {
            $query->where('es_anio', $request->es_anio);
        }


        // Filter by proceso_nombre if provided and not empty
        if ($request->filled('proceso_nombre')) {
            $query->whereHas('proceso', function ($q) use ($request) {
                $q->where('proceso_nombre', 'like', '%' . $request->proceso_nombre . '%');
            });
        }

        // Ordenar por año y periodo (esto último es string, así que cuidado)
        $encuestas = $query->orderBy('es_anio', 'desc')->orderBy('created_at', 'desc')->get();

        return response()->json($encuestas);
    }

    public function store(Request $request)
    {
        $request->validate([
            'proceso_id' => 'required|exists:procesos,id',
            'es_periodo' => 'required|string',
            'es_numero_periodo' => 'required|integer|min:1',
            'es_anio' => 'required|integer',
            'es_nps_score' => 'nullable|numeric|min:-100|max:100',
            'es_cantidad' => 'nullable|integer',
            'informe' => 'nullable|file|mimes:pdf,xlsx,xls|max:10240', // 10MB max
            'detalles' => 'required|string' // JSON string
        ]);

        DB::beginTransaction();
        try {
            // Calculate average score from details if provided
            $detalles = json_decode($request->detalles, true);
            $es_score = null;

            if (is_array($detalles) && count($detalles) > 0) {
                $total_puntaje = 0;
                $count = 0;

                foreach ($detalles as $detalle) {
                    if (isset($detalle['puntaje']) && is_numeric($detalle['puntaje'])) {
                        $total_puntaje += $detalle['puntaje'];
                        $count++;
                    }
                }

                $es_score = $count > 0 ? round($total_puntaje / $count, 2) : null;
            }

            $data = $request->only(['proceso_id', 'es_periodo', 'es_numero_periodo', 'es_anio', 'es_nps_score', 'es_cantidad']);
            $data['es_score'] = $es_score; // Override any frontend-provided value with calculated value

            if ($request->hasFile('informe')) {
                $file = $request->file('informe');
                $path = $file->store('satisfaccion/' . $request->proceso_id, 'public');
                $fileData = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName()
                ];
                $data['es_informe_path'] = json_encode($fileData);
            }

            $encuesta = EncuestaSatisfaccion::create($data);

            if (is_array($detalles)) {
                foreach ($detalles as $detalle) {
                    $encuesta->detalles()->create([
                        'esd_categoria' => $detalle['categoria'],
                        'esd_puntaje' => $detalle['puntaje']
                    ]);
                }
            }

            DB::commit();
            return response()->json($encuesta->load('detalles'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating encuesta: ' . $e->getMessage());
            return response()->json(['message' => 'Error al guardar la encuesta'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $encuesta = EncuestaSatisfaccion::findOrFail($id);

        $request->validate([
            'proceso_id' => 'required|exists:procesos,id',
            'es_periodo' => 'required|string',
            'es_numero_periodo' => 'required|integer|min:1',
            'es_anio' => 'required|integer',
            'es_nps_score' => 'nullable|numeric',
            'es_cantidad' => 'nullable|integer',
            'informe' => 'nullable|file|mimes:pdf,xlsx,xls|max:10240',
            'detalles' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Calculate average score from details if provided
            $detalles = null;
            if ($request->has('detalles')) {
                $detalles = json_decode($request->detalles, true);
            }

            $es_score = null;

            if (is_array($detalles) && count($detalles) > 0) {
                $total_puntaje = 0;
                $count = 0;

                foreach ($detalles as $detalle) {
                    if (isset($detalle['puntaje']) && is_numeric($detalle['puntaje'])) {
                        $total_puntaje += $detalle['puntaje'];
                        $count++;
                    }
                }

                $es_score = $count > 0 ? round($total_puntaje / $count, 2) : null;
            }

            $data = $request->only(['proceso_id', 'es_periodo', 'es_numero_periodo', 'es_anio', 'es_nps_score', 'es_cantidad']);
            $data['es_score'] = $es_score; // Override any frontend-provided value with calculated value

            if ($request->hasFile('informe')) {
                $file = $request->file('informe');
                $path = $file->store('satisfaccion/' . $request->proceso_id, 'public');
                $fileData = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName()
                ];
                $data['es_informe_path'] = json_encode($fileData);
            } elseif ($request->has('remove_informe') && $request->remove_informe === '1') {
                // Remove existing file if requested
                if ($encuesta->es_informe_path) {
                    // Parse the file data to get the path for deletion
                    $fileData = json_decode($encuesta->es_informe_path, true);
                    if ($fileData && isset($fileData['path'])) {
                        $pathToDelete = $fileData['path'];
                        if (Storage::disk('public')->exists($pathToDelete)) {
                            Storage::disk('public')->delete($pathToDelete);
                        }
                    } else {
                        // Fallback for old format if it's just a string path
                        if (Storage::disk('public')->exists($encuesta->es_informe_path)) {
                            Storage::disk('public')->delete($encuesta->es_informe_path);
                        }
                    }
                    $data['es_informe_path'] = null;
                }
            }

            $encuesta->update($data);

            if ($request->has('detalles')) {
                // Sync details: delete old and create new
                $encuesta->detalles()->delete();

                if (is_array($detalles)) {
                    foreach ($detalles as $detalle) {
                        $encuesta->detalles()->create([
                            'esd_categoria' => $detalle['categoria'],
                            'esd_puntaje' => $detalle['puntaje']
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json($encuesta->load('detalles'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating encuesta: ' . $e->getMessage());
            return response()->json(['message' => 'Error al actualizar la encuesta'], 500);
        }
    }

    public function destroy($id)
    {
        $encuesta = EncuestaSatisfaccion::findOrFail($id);

        if ($encuesta->es_informe_path) {
            Storage::disk('public')->delete($encuesta->es_informe_path);
        }

        $encuesta->delete(); // Cascade delete handles details
        return response()->json(['message' => 'Encuesta eliminada']);
    }

    public function dashboard(Request $request)
    {
        // Logic for dashboard charts
        // NPS evolution
        $npsTrend = EncuestaSatisfaccion::select('es_periodo', 'es_anio', DB::raw('AVG(es_nps_score) as avg_nps'))
            ->groupBy('es_anio', 'es_periodo')
            ->orderBy('es_anio')
            ->orderBy('es_periodo') // This might need better sorting logic for periods
            ->get();

        // Category averages
        $categoryAverages = EncuestaSatisfaccionDetalle::select('esd_categoria', DB::raw('AVG(esd_puntaje) as avg_score'))
            ->groupBy('esd_categoria')
            ->get();

        return response()->json([
            'nps_trend' => $npsTrend,
            'category_averages' => $categoryAverages
        ]);
    }
}
