<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key') ?? env('OPENAI_API_KEY');
    }

    /**
     * Filtra una lista de normas usando IA según criterios específicos.
     * Criterios:
     * 1. Excluir designaciones/nombramientos.
     * 2. Incluir normas relacionadas con Contraloría.
     * 3. Incluir normas con obligaciones explícitas para la entidad.
     *
     * @param array $normas Lista de normas (título, resumen, texto)
     * @return array Lista de índices de normas relevantes
     */
    public function filtrarNormasRelevantes(array $normas): array
    {
        if (!$this->apiKey) {
            Log::warning('OpenAI API Key not found. Skipping AI filtering.');
            return array_keys($normas); // Devolver todas si no hay API
        }

        // Preparar data para el prompt (enviamos índice y texto clave para ahorrar tokens)
        $itemsParaAnalisis = [];
        foreach ($normas as $index => $norma) {
            $texto = "Título: " . $norma['titulo'] . ". Resumen: " . ($norma['resumen_ia'] ?? '') . ". Texto: " . substr($norma['texto_completo'] ?? '', 0, 300);
            $itemsParaAnalisis[] = ["id" => $index, "content" => $texto];
        }

        // Dividir en chunks si son muchas (ej. 20 por request) para no saturar
        $chunks = array_chunk($itemsParaAnalisis, 20);
        $indicesRelevantes = [];

        foreach ($chunks as $chunk) {
            try {
                $indices = $this->analizarChunk($chunk);
                $indicesRelevantes = array_merge($indicesRelevantes, $indices);
            } catch (\Exception $e) {
                Log::error("Error analizando chunk de normas: " . $e->getMessage());
                // En caso de error, podríamos decidir incluir todas o ninguna. 
                // Por seguridad, incluimos todas las de este chunk para no perder info importante.
                foreach ($chunk as $item) {
                    $indicesRelevantes[] = $item['id'];
                }
            }
        }

        return $indicesRelevantes;
    }

    protected function analizarChunk(array $items): array
    {
        $prompt = "Analiza las siguientes normas legales y selecciona SOLO aquellas que cumplan estos criterios:\n";
        $prompt .= "1. NO es una designación, nombramiento, renuncia o encargatura de puesto (DESCARTAR estas).\n";
        $prompt .= "2. Involucra a la 'Contraloría General de la República' O establece una obligación/regulación explícita para entidades públicas.\n";
        $prompt .= "3. Si es una norma puramente administrativa irrelevante (ej. fe de erratas menor, aprobación de viajes), DESCARTAR.\n\n";
        $prompt .= "Devuelve un JSON con una lista de IDs de las normas relevantes. Ejemplo: {\"relevantes\": [1, 4, 5]}\n\n";
        $prompt .= "Normas:\n" . json_encode($items, JSON_UNESCAPED_UNICODE);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Eres un asistente legal experto en filtrar normatividad peruana. Tu objetivo es identificar normas relevantes para gestión de riesgos y cumplimiento, descartando ruido administrativo (designaciones, viajes). Responde SOLO en JSON.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.1,
                    'response_format' => ['type' => 'json_object']
                ]);

        if ($response->successful()) {
            $content = $response->json('choices.0.message.content');
            $data = json_decode($content, true);
            return $data['relevantes'] ?? [];
        } else {
            throw new \Exception('OpenAI Error: ' . $response->status());
        }
    }
}
