<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetFactory;
use PhpOffice\PhpWord\IOFactory as WordFactory;

class ObligacionIAController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Extrae una obligación de un documento existente en la base de datos.
     */
    public function extractFromDocument(Request $request)
    {
        $request->validate([
            'documento_id' => 'required|exists:documentos,id'
        ]);

        try {
            $documento = Documento::with('ultimaVersion')->findOrFail($request->documento_id);
            $path = $documento->archivo_path_documento ?? ($documento->ultimaVersion ? $documento->ultimaVersion->archivo_path : null);

            if (!$path || !Storage::disk('documentos')->exists($path)) {
                return response()->json(['error' => 'El documento no tiene un archivo asociado o el archivo no existe.'], 404);
            }

            // Parsear contenido del archivo
            $content = $this->parseDocumentFile($path);

            if (!$content) {
                return response()->json(['error' => 'No se pudo leer el contenido del documento.'], 422);
            }

            // Extraer obligación usando IA
            $obligacion = $this->aiService->extractObligacionFromText(substr($content, 0, 8000));

            return response()->json([
                'obligacion' => $obligacion,
                'documento_nombre' => $documento->nombre_documento
            ]);

        } catch (\Exception $e) {
            Log::error('Error extrayendo obligación de documento: ' . $e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al procesar el documento con IA.'], 500);
        }
    }

    /**
     * Extrae una obligación de un archivo subido temporalmente.
     */
    public function analyzeUploadedFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240'
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->store('temp_ai_analysis');
            $fullPath = Storage::path($tempPath);

            // Parsear el archivo
            $content = $this->parseUploadedFile($fullPath, $extension);

            // Limpiar archivo temporal
            Storage::delete($tempPath);

            if (!$content) {
                return response()->json(['error' => 'No se pudo procesar el archivo subido.'], 422);
            }

            // Limpiar caracteres UTF-8 mal codificados
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
            $content = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $content);

            $obligacion = $this->aiService->extractObligacionFromText(substr($content, 0, 8000));

            return response()->json(['obligacion' => $obligacion]);

        } catch (\Exception $e) {
            Log::error('Error analizando archivo subido: ' . $e->getMessage());
            return response()->json(['error' => 'Error al analizar el archivo: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mejora o sugiere consecuencias para una obligación.
     */
    public function improveConsequence(Request $request)
    {
        $request->validate([
            'obligacion_principal' => 'required|string|max:1000',
            'current_consequence' => 'nullable|string|max:1000'
        ]);

        try {
            $improved = $this->aiService->improveObligacionConsequence(
                $request->obligacion_principal,
                $request->current_consequence
            );

            return response()->json(['improved' => $improved]);

        } catch (\Exception $e) {
            Log::error('Error mejorando consecuencia: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudo procesar la solicitud con IA.'], 500);
        }
    }

    /**
     * Parsea un archivo del disco 'documentos'.
     */
    private function parseDocumentFile(string $path): ?string
    {
        $fullPath = Storage::disk('documentos')->path($path);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return $this->parseFileContent($fullPath, $extension);
    }

    /**
     * Parsea un archivo subido temporalmente.
     */
    private function parseUploadedFile(string $fullPath, string $extension): ?string
    {
        return $this->parseFileContent($fullPath, strtolower($extension));
    }

    /**
     * Parsea el contenido de un archivo según su extensión.
     */
    private function parseFileContent(string $fullPath, string $extension): ?string
    {
        try {
            if (!file_exists($fullPath)) {
                Log::error("File does not exist: " . $fullPath);
                return null;
            }

            switch ($extension) {
                case 'pdf':
                    $parser = new PdfParser;
                    $pdf = $parser->parseFile($fullPath);
                    return $pdf->getText();

                case 'txt':
                case 'csv':
                    return file_get_contents($fullPath);

                case 'docx':
                case 'doc':
                    if (class_exists(WordFactory::class)) {
                        $phpWord = WordFactory::load($fullPath);
                        $text = '';
                        foreach ($phpWord->getSections() as $section) {
                            foreach ($section->getElements() as $element) {
                                if (method_exists($element, 'getText')) {
                                    $text .= $element->getText() . "\n";
                                }
                            }
                        }
                        return $text;
                    }
                    Log::error("WordFactory class not found");
                    return null;

                default:
                    Log::error("Unsupported file extension: " . $extension);
                    return null;
            }
        } catch (\Exception $e) {
            Log::error("Error parsing file: " . $e->getMessage());
            return null;
        }
    }
}
