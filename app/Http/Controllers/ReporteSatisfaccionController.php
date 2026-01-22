<?php

namespace App\Http\Controllers;

use App\Models\ReporteSatisfaccion;
use App\Models\Proceso;
use App\Models\EncuestaSatisfaccion;
use App\Models\Sugerencia;
use App\Models\SalidaNoConforme;
use App\Services\AIService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Language;
use PhpOffice\PhpWord\Style\Cell;

class ReporteSatisfaccionController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        $reportes = ReporteSatisfaccion::with(['proceso', 'user'])->orderBy('created_at', 'desc')->get();
        return response()->json($reportes);
    }

    public function show($id)
    {
        $reporte = ReporteSatisfaccion::with(['proceso', 'user'])->findOrFail($id);
        return response()->json($reporte);
    }

    public function getData(Request $request)
    {
        $request->validate([
            'proceso_id' => 'required',
            'anio' => 'required|integer',
            'trimestre' => 'required|integer|min:1|max:4'
        ]);

        $procesoId = $request->proceso_id;
        $anio = $request->anio;
        $trimestre = $request->trimestre;

        // Definir rango de fechas del trimestre
        $startMonth = ($trimestre - 1) * 3 + 1;
        $startDate = Carbon::createFromDate($anio, $startMonth, 1)->startOfDay();
        $endDate = $startDate->copy()->addMonths(3)->subDay()->endOfDay();

        \Illuminate\Support\Facades\Log::info('ReporteSatisfaccion getData', [
            'proceso_id' => $procesoId,
            'anio' => $anio,
            'trimestre' => $trimestre,
            'start' => $startDate->toDateTimeString(),
            'end' => $endDate->toDateTimeString()
        ]);

        // 2.1 Encuestas: Traer el ÚLTIMO resultado registrado para el proceso
        $encuestas = EncuestaSatisfaccion::where('proceso_id', '=', $procesoId, 'and')
            ->orderBy('es_anio', 'desc')
            ->orderBy('es_numero_periodo', 'desc')
            ->with('detalles')
            ->first();

        if ($encuestas) {
            $periodoEncuesta = $encuestas->es_periodo ?? ($encuestas->es_numero_periodo ? "Trimestre " . $encuestas->es_numero_periodo : "Anual");
            $conductores = $encuestas->detalles->map(function ($d) {
                return "{$d->esd_categoria}: {$d->esd_puntaje}";
            })->implode(', ');

            $resumenEncuestas = "Se consideró los resultados de la medición correspondiente al periodo {$periodoEncuesta} del año {$encuestas->es_anio}. " .
                "Se obtuvo un índice de satisfacción (Score Global) de {$encuestas->es_score}% y un NPS de {$encuestas->es_nps_score}. " .
                "La medición se realizó sobre una muestra de {$encuestas->es_cantidad} encuestados. " .
                "Los resultados por conductor fueron: {$conductores}.";
        } else {
            $resumenEncuestas = "No se cuenta con líneas base ni resultados históricos de encuestas de satisfacción para este proceso.";
        }

        // 2.2 Sugerencias
        $sugerencias = Sugerencia::where('proceso_id', '=', $procesoId, 'and')
            ->whereBetween('sugerencia_fecha_ingreso', [$startDate, $endDate])
            ->get();

        if ($sugerencias->count() > 0) {
            $estados = $sugerencias->groupBy('sugerencia_estado')->map(function ($group, $key) {
                return "{$key} ({$group->count()})";
            })->implode(', ');

            $tratadas = $sugerencias->whereNotNull('sugerencia_tratamiento')->count();

            $resumenSugerencias = "Durante el periodo {$trimestre} del año {$anio}, se recibieron un total de {$sugerencias->count()} sugerencias. " .
                "El estado actual de las mismas es: {$estados}. " .
                "Del total recibido, {$tratadas} han sido analizadas y cuentan con tratamiento definido.";
        } else {
            $resumenSugerencias = "Durante el presente trimestre no se han recibido sugerencias por parte de los clientes/usuarios del proceso.";
        }

        // 2.4 Salidas No Conformes
        $snc = SalidaNoConforme::where('proceso_id', '=', $procesoId, 'and')
            ->whereBetween('snc_fecha_deteccion', [$startDate, $endDate])
            ->get();

        if ($snc->count() > 0) {
            $cerradas = $snc->where('snc_estado', 'Cerrado')->count();
            $abiertas = $snc->where('snc_estado', '!=', 'Cerrado')->count();

            $resumenSnc = "Se identificaron {$snc->count()} salidas no conformes en el periodo. " .
                "De las cuales, {$cerradas} se encuentran cerradas y {$abiertas} permanecen en proceso de tratamiento/cierre.";
        } else {
            $resumenSnc = "No se reportaron Salidas No Conformes en el periodo evaluado.";
        }

        \Illuminate\Support\Facades\Log::info('Data Found', [
            'encuestas' => $encuestas ? 'Si' : 'No',
            'sugerencias' => $sugerencias->count(),
            'snc' => $snc->count()
        ]);

        return response()->json([
            'encuestas_data' => $encuestas,
            'sugerencias_count' => $sugerencias->count(),
            'snc_count' => $snc->count(),
            'resumen_encuestas' => $resumenEncuestas,
            'resumen_sugerencias' => $resumenSugerencias,
            'resumen_snc' => $resumenSnc,
            'periodo_texto' => "$startMonth/01/$anio - " . $endDate->format('m/d/Y')
        ]);
    }

    public function generateDraft(Request $request)
    {
        $data = $request->all();
        // data expects: proceso_nombre, resumen_encuestas, resumen_sugerencias, reclamos, resumen_snc

        // Si proceso_nombre no viene, buscarlo
        if (!isset($data['proceso_nombre']) && isset($data['proceso_id'])) {
            $proceso = Proceso::find($data['proceso_id'], ['*']);
            $data['proceso_nombre'] = $proceso ? $proceso->proceso_nombre : 'Proceso Desconocido';
        }

        $analisis = $this->aiService->generateQuarterlyReportAnalysis($data);

        return response()->json($analisis); // { oportunidades, conclusiones }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'anio' => 'required',
            'trimestre' => 'required',
            'proceso_id' => 'required',
            'fecha_generacion' => 'required|date',
            'resumen_encuestas' => 'nullable|string',
            'resumen_sugerencias' => 'nullable|string',
            'reclamos' => 'nullable|string',
            'resumen_snc' => 'nullable|string',
            'oportunidades_mejora' => 'nullable|string',
            'conclusiones' => 'nullable|string',
            'estado' => 'required'
        ]);

        $validated['user_id'] = auth()->id();

        $reporte = ReporteSatisfaccion::create($validated);

        return response()->json(['message' => 'Reporte guardado correctamente', 'id' => $reporte->id]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'anio' => 'sometimes|integer',
            'trimestre' => 'sometimes|integer',
            'proceso_id' => 'sometimes|integer',
            'resumen_encuestas' => 'nullable|string',
            'resumen_sugerencias' => 'nullable|string',
            'reclamos' => 'nullable|string',
            'resumen_snc' => 'nullable|string',
            'oportunidades_mejora' => 'nullable|string',
            'conclusiones' => 'nullable|string',
            'estado' => 'required'
        ]);

        $reporte = ReporteSatisfaccion::findOrFail($id);

        // Update generation date if it was draft
        if ($reporte->estado == 'borrador' && $request->estado == 'generado') {
            $validated['fecha_generacion'] = now();
        }

        $reporte->update($validated);

        return response()->json(['message' => 'Reporte actualizado correctamente', 'id' => $reporte->id]);
    }

    public function destroy($id)
    {
        ReporteSatisfaccion::destroy($id);

        return response()->json(['message' => 'Reporte eliminado correctamente']);
    }

    public function uploadFirma(Request $request, $id)
    {
        $validated = $request->validate([
            'archivo_path' => 'required|file|mimes:pdf|max:10240' // Max 10MB
        ]);

        $reporte = ReporteSatisfaccion::findOrFail($id);

        // Solo permitir si está en estado 'generado'
        if ($reporte->estado !== 'generado') {
            return response()->json(['message' => 'Solo se puede firmar reportes generados'], 400);
        }

        // Eliminar archivo anterior si existe
        if ($reporte->archivo_path && \Storage::disk('public')->exists($reporte->archivo_path)) {
            \Storage::disk('public')->delete($reporte->archivo_path);
        }

        // Guardar nuevo archivo
        $path = $request->file('archivo_path')->store("satisfaccion/reporte_trimestral/{$reporte->proceso_id}", 'public');

        // Actualizar reporte
        $reporte->update([
            'archivo_path' => $path,
            'estado' => 'firmado'
        ]);

        return response()->json([
            'message' => 'Archivo firmado subido correctamente',
            'archivo_url' => asset('storage/' . $path)
        ]);
    }

    public function downloadWord($id)
    {
        $reporte = ReporteSatisfaccion::with('proceso')->findOrFail($id);

        $phpWord = new PhpWord();
        $phpWord->getSettings()->setThemeFontLang(new Language(Language::ES_ES));

        // Estilos
        $sectionStyle = ['marginTop' => 1134, 'marginLeft' => 1134, 'marginRight' => 1134, 'marginBottom' => 1134]; // ~2cm
        $headerStyle = ['bold' => true, 'size' => 11, 'name' => 'Arial'];
        $bodyStyle = ['size' => 10, 'name' => 'Arial', 'align' => 'both'];
        $titleStyle = ['bold' => true, 'size' => 12, 'name' => 'Arial', 'color' => '000000', 'allCaps' => true];

        // Table styles
        $tableStyle = ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 50, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER];
        $phpWord->addTableStyle('EstiloTabla', $tableStyle);
        $headerCellStyle = ['bgColor' => 'D9D9D9', 'valign' => 'center']; // Gris claro para headers tabla
        $headerRedCellStyle = ['bgColor' => 'C00000', 'valign' => 'center']; // Rojo oscuro para headers especiales
        $fontWhite = ['bold' => true, 'color' => 'FFFFFF', 'size' => 10, 'name' => 'Arial'];

        $section = $phpWord->addSection($sectionStyle);

        // Add logo at the top center
        $logoPath = public_path('images/logo.png');
        if (file_exists($logoPath)) {
            $section->addImage($logoPath, [
                'width' => 120,  // Reduced from 150
                'height' => 48,  // Reduced from 60
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
            ]);
            $section->addTextBreak(1);
        }

        // Title
        $titleStyle = ['bold' => true, 'size' => 14, 'name' => 'Arial'];
        $section->addText(
            "REPORTE TRIMESTRAL DE SATISFACCIÓN AL CLIENTE",
            $titleStyle,
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );
        $section->addTextBreak(1);

        // I. DATOS GENERALES
        $section->addText("I. Datos generales", $headerStyle);
        $table = $section->addTable('EstiloTabla');

        $table->addRow();
        $table->addCell(2000, $headerCellStyle)->addText('Proceso', ['bold' => true, 'size' => 9]);
        $table->addCell(4000)->addText($reporte->proceso->proceso_nombre, ['size' => 9]);
        $table->addCell(1500, $headerCellStyle)->addText('Tipo', ['bold' => true, 'size' => 9]);
        $table->addCell(2500)->addText($reporte->proceso->tipo_proceso ?? 'Misional', ['size' => 9]); // Asumiendo campo tipo en proceso

        $section->addTextBreak(1);

        // Tabla Mecanismos (Hardcoded structure based on image, but logical)
        $table2 = $section->addTable('EstiloTabla');
        $table2->addRow();
        $table2->addCell(2000, $headerCellStyle)->addText('Cliente', ['bold' => true, 'size' => 9]);
        $table2->addCell(1000, $headerCellStyle)->addText('Tipo', ['bold' => true, 'size' => 9]);
        $table2->addCell(3000, $headerCellStyle)->addText('Mecanismo de evaluación', ['bold' => true, 'size' => 9]);
        $table2->addCell(2000, $headerCellStyle)->addText('Frecuencia', ['bold' => true, 'size' => 9]);
        $table2->addCell(2000, $headerCellStyle)->addText('Periodo', ['bold' => true, 'size' => 9]);

        $table2->addRow();
        $table2->addCell(2000)->addText("Ciudadanos, Entidades", ['size' => 9]);
        $table2->addCell(1000)->addText("Ex", ['size' => 9]);
        $table2->addCell(3000)->addText("ENCUESTA DE SATISFACCIÓN / RECLAMOS / SUGERENCIAS", ['size' => 9]);
        $table2->addCell(2000)->addText("Trimestral / Permanente", ['size' => 9]);
        $table2->addCell(2000)->addText("Trimestral", ['size' => 9]);

        $section->addTextBreak(1);

        // II. RESULTADOS
        $section->addText("II. Resultados de la medición de los mecanismos de satisfacción", $headerStyle);
        $section->addTextBreak(1);

        // 2.1 Encuestas
        $section->addText("2.1 Encuesta de satisfacción al cliente", ['bold' => true, 'size' => 10, 'name' => 'Arial']);
        $section->addText($reporte->resumen_encuestas, $bodyStyle);
        $section->addTextBreak(1);

        // 2.2 Sugerencias
        $section->addText("2.2 Sugerencias", ['bold' => true, 'size' => 10, 'name' => 'Arial']);
        $section->addText($reporte->resumen_sugerencias, $bodyStyle);
        $section->addTextBreak(1);

        // 2.3 Reclamos
        $section->addText("2.3 Reclamos", ['bold' => true, 'size' => 10, 'name' => 'Arial']);
        $section->addText($reporte->reclamos ?? 'No se registraron reclamos.', $bodyStyle);
        $section->addTextBreak(1);

        // 2.4 Salidas No Conformes
        $section->addText("2.4 Quejas por defecto de tramitación / SNC", ['bold' => true, 'size' => 10, 'name' => 'Arial']);
        $section->addText($reporte->resumen_snc, $bodyStyle);
        $section->addTextBreak(1);

        // III. MEJORAS
        $section->addText("III. Oportunidades de mejora identificadas", $headerStyle);
        // Strip tags allows clean text, or use Html::addHtml for advanced but tricky in PHPWord
        // For stability, strip_tags + manual breaks is safer
        $section->addText($reporte->oportunidades_mejora, $bodyStyle);
        $section->addTextBreak(1);

        // IV. CONCLUSIONES
        $section->addText("IV. Conclusiones", $headerStyle);
        $section->addText($reporte->conclusiones, $bodyStyle);
        $section->addTextBreak(2);

        // Signature
        $section->addTextBreak(2);
        $section->addText('_________________________________', ['size' => 10], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addText('Firma del Propietario del Proceso', ['size' => 10, 'bold' => true], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addText($reporte->proceso->proceso_nombre, ['size' => 10], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        // Guardar a temp
        $fileName = 'Reporte_Satisfaccion_' . $reporte->anio . '_T' . $reporte->trimestre . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord');
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function uploadSigned(Request $request, $id)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:10240'
        ]);

        $reporte = ReporteSatisfaccion::findOrFail($id);

        if ($request->hasFile('archivo')) {
            $path = $request->file('archivo')->store('reportes_satisfaccion', 'public');
            $reporte->archivo_path = $path;
            $reporte->save();
        }

        return response()->json(['message' => 'Archivo firmado subido correctamente', 'path' => $reporte->archivo_path]);
    }
}
