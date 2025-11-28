<?php

namespace App\Http\Controllers;

use App\Models\RadarNormativo;
use App\Models\Obligacion;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\NormasElPeruanoService;
use App\Services\RadarAnalisisService;
use Carbon\Carbon;

class RadarController extends Controller
{
    protected $radarService;

    public function __construct(RadarAnalisisService $radarService)
    {
        $this->radarService = $radarService;
    }

    public function index()
    {
        $normas = RadarNormativo::orderBy('fecha_publicacion', 'desc')->get();
        return response()->json($normas);
    }

    public function scan()
    {
        try {
            // 1. Obtener normas filtradas y procesadas por el servicio orquestador
            $nuevasNormas = $this->radarService->procesarNormasDelDia();

            $count = 0;
            if ($nuevasNormas && is_array($nuevasNormas)) {
                foreach ($nuevasNormas as $norma) {
                    // Evitar duplicados simples
                    $exists = RadarNormativo::where('numero_norma', $norma['numero_norma'])
                        ->orWhere('titulo', $norma['titulo']) // Doble check
                        ->exists();

                    if (!$exists) {
                        RadarNormativo::create($norma);
                        $count++;
                    }
                }
            }

            return response()->json(['message' => 'Escaneo completado', 'count' => $count]);

        } catch (\Exception $e) {
            Log::error("Error en RadarController::scan: " . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function approve(Request $request, $id)
    {
        \Log::info('Radar approve called', [
            'id' => $id,
            'request_data' => $request->all()
        ]);

        $request->validate([
            'proceso_id' => 'required|exists:procesos,id',
            'area_compliance_id' => 'required|exists:areas_compliance,id',
            'cod_documento' => 'nullable|string',
            'estado_documento' => 'required|in:vigente,derogado',
            'analisis_humano' => 'required|string'
        ]);

        $radar = RadarNormativo::findOrFail($id);

        DB::beginTransaction();
        try {
            // 1. Crear el Documento (Externo)
            $documento = Documento::create([
                'nombre_documento' => $radar->titulo . ($radar->numero_norma ? ' (' . $radar->numero_norma . ')' : ''),
                'cod_documento' => $request->input('cod_documento', $radar->numero_norma),
                'tipo_documento_id' => 1, // Asumiendo 1 es 'Norma' o similar
                'fuente_documento' => 'externo',
                'estado_documento' => $request->input('estado_documento', 'vigente'),
                'resumen_documento' => $radar->resumen_ia,
                'fecha_aprobacion_documento' => $radar->fecha_publicacion,
                'proceso_id' => $request->input('proceso_id'),
                'area_compliance_id' => $request->input('area_compliance_id'),
                'subarea_compliance_id' => $request->input('subarea_compliance_id'),
                'usa_versiones_documento' => 0,
                'archivo_path_documento' => $radar->url_fuente // Guardar URL como path
            ]);

            // 2. Crear la Obligación vinculada
            $obligacion = Obligacion::create([
                'radar_id' => $radar->id,
                'documento_id' => $documento->id,
                'documento_tecnico_normativo' => $request->input('cod_documento', $radar->numero_norma),
                'obligacion_principal' => $request->input('obligacion_principal', $radar->resumen_ia ?? 'Sin descripción'),
                'obligacion_controles' => $request->input('obligacion_controles'),
                'consecuencia_incumplimiento' => $request->input('consecuencia_incumplimiento'),
                'estado_obligacion' => 'pendiente',
                'tipo_obligacion' => $request->input('tipo_obligacion', 'Legal'),
                'nivel_riesgo_inherente' => $request->input('nivel_riesgo_inherente', 'Alto'),
                'proceso_id' => $request->input('proceso_id'),
                'area_compliance_id' => $request->input('area_compliance_id'),
            ]);

            // 3. Actualizar Radar
            $radar->update([
                'estado' => 'Aplicable',
                'analisis_humano' => $request->input('analisis_humano')
            ]);

            DB::commit();
            return response()->json(['message' => 'Norma aprobada y obligación creada', 'obligacion_id' => $obligacion->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al aprobar norma: ' . $e->getMessage()], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        $radar = RadarNormativo::findOrFail($id);
        $radar->update([
            'estado' => 'No Aplicable',
            'analisis_humano' => $request->input('analisis_humano')
        ]);

        return response()->json(['message' => 'Norma marcada como No Aplicable']);
    }
}
