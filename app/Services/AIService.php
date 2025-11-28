<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.openai.com/v1';

    protected $knowledgeService;

    public function __construct(ChatbotKnowledgeService $knowledgeService)
    {
        $this->apiKey = config('services.openai.api_key') ?? env('OPENAI_API_KEY');
        $this->knowledgeService = $knowledgeService;
    }

    public function processChat(string $message, int $userId): array
    {
        // 1. Identify Intent
        $intentData = $this->identifyIntent($message);
        $intent = $intentData['intent'] ?? 'qa';

        // 2. Handle Navigation / Actions
        if ($intent === 'navigation') {
            return [
                'message' => $intentData['response_text'] ?? 'Te estoy redirigiendo...',
                'action' => $intentData['action'] ?? null
            ];
        }

        // 3. Handle Redaction Improvement
        if ($intent === 'redaction') {
            return [
                'message' => $this->improveRedaction($message)
            ];
        }

        // 4. Handle Q&A / Examples (RAG)
        $context = $this->knowledgeService->getRelevantContext($message);

        // Add DB context if requested (simplified for now)
        if ($intent === 'example') {
            // Fetch some examples from DB logic could go here
            // For now, we rely on the prompt to generate examples or use the docs
        }

        return [
            'message' => $this->generateResponse($message, $context)
        ];
    }

    protected function identifyIntent(string $message): array
    {
        $prompt = "Analyze the user message and identify the intent.
        User Message: \"$message\"
        
        Possible Intents:
        - navigation: User wants to go to a specific module.
        - redaction: User asks to improve text.
        - qa: General question.

        If navigation, specify the action using ONLY these valid URLs (Vue Router):
        - '/vue/documentos' (Documentos) [Params: buscar_proceso, buscar_documento]
        - '/vue/mejora' (Hallazgos/Mejora)
        - '/vue/mis-hallazgos' (Mis Hallazgos)
        - '/vue/requerimientos/index' (Requerimientos)
        - '/vue/mis-requerimientos' (Mis Requerimientos)
        - '/vue/obligaciones' (Obligaciones)
        - '/vue/mis-obligaciones' (Mis Obligaciones)
        - '/vue/radar-obligaciones' (Radar Normativo)
        - '/vue/riesgos/index' (Riesgos)
        - '/vue/riesgos/mis-riesgos' (Mis Riesgos)
        - '/vue/indicadores' (Indicadores)
        - '/vue/encuestas-satisfaccion' (Encuestas de Satisfacción)
        - '/vue/encuestas-satisfaccion/dashboard' (Dashboard Encuestas)
        - '/vue/salidas-nc' (Salidas No Conformes)
        - '/vue/sugerencias' (Sugerencias)
        - '/procesos' (Procesos - Blade View, NO /vue/)

        If the user wants to search for documents of a specific process, include \"parameters\": { \"buscar_proceso\": \"...\" } in the action.

        Return JSON: { \"intent\": \"...\", \"action\": { \"type\": \"navigate\", \"url\": \"...\", \"parameters\": { ... } }, \"response_text\": \"...\" }
        IMPORTANT: The 'response_text' MUST be in Spanish.";

        $response = $this->callOpenAI($prompt, true);
        return json_decode($response, true) ?? ['intent' => 'qa'];
    }

    protected function improveRedaction(string $message): string
    {
        $prompt = "Act as an expert in ISO 9001 and ISO 37001. The user wants to improve the redaction of a Risk or Obligation.
        User Input: \"$message\"
        
        Analyze the text. If it's a Risk, ensure it follows syntax (Cause -> Risk -> Effect). If it's an Obligation, ensure clarity and compliance.
        Provide a revised version and a brief explanation. Keep it under 500 chars.
        IMPORTANT: Respond ONLY in Spanish.";

        return $this->callOpenAI($prompt);
    }

    protected function generateResponse(string $message, string $context): string
    {
        $prompt = "You are Jaris, an AI assistant for the Kallpaq system (ISO 9001/37001).
        Context from documents:
        $context
        
        User Question: \"$message\"
        
        Answer the question based on the context or your general knowledge of ISO standards.
        Keep the answer professional, concise, and UNDER 500 CHARACTERS.
        If the context doesn't have the answer, say so but try to help.
        IMPORTANT: Respond ONLY in Spanish.";

        return $this->callOpenAI($prompt);
    }

    protected function callOpenAI(string $prompt, bool $jsonMode = false): string
    {
        $params = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are Jaris, a helpful AI assistant. You MUST always answer in Spanish.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.3,
        ];

        if ($jsonMode) {
            $params['response_format'] = ['type' => 'json_object'];
        }

        $response = Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', $params);

        if ($response->successful()) {
            return $response->json('choices.0.message.content');
        }

        throw new \Exception('OpenAI Error: ' . $response->status());
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
        $prompt .= "4. Si es una norma del sector privado o público que no involucra a la Contraloría General de la República o recursos del estado, DESCARTAR.\n\n";
        $prompt .= "Devuelve un JSON con una lista de IDs de las normas relevantes. Ejemplo: {\"relevantes\": [1, 4, 5]}\n\n";
        $prompt .= "Normas:\n" . json_encode($items, JSON_UNESCAPED_UNICODE);

        return json_decode($this->callOpenAI($prompt, true), true)['relevantes'] ?? [];
    }
}
