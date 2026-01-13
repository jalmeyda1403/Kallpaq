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
        $this->apiKey = config('services.openai.api_key') ?? env('OPENAI_API_KEY') ?? '';
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
        $prompt = "Act as an expert in ISO 9001, ISO 37301, ISO 31000 and ISO 37001. The user wants to improve the redaction of a Risk or Obligation.
        User Input: \"$message\"
        
        Analyze the text. If it's a Risk, ensure it follows syntax (Cause -> Risk -> Effect). If it's an Obligation, ensure clarity and compliance.
        Provide a revised version and a brief explanation. Keep it under 500 chars.
        IMPORTANT: Respond ONLY in Spanish.";

        return $this->callOpenAI($prompt);
    }

    public function improveRiskDescription(string $text): string
    {
        $prompt = "Actúa como un experto en gestión de riesgos ISO 9001, ISO 37301, ISO 31000 and ISO 37001.
        Tu tarea es evaluar primero el riesgo (ISO 31000, un riesgo es el efecto de la incertidumbre sobre los objetivos,  Es decir, es la probabilidad de que ocurra un evento que tenga un impacto en los objetivos de la organización) e indicar si es o no un riesgo, si es un riesgo, mejorar la redacción de la siguiente descripción de riesgo para que cumpla estrictamente con la estructura:
        '[EVENTO], debido a [CAUSA]'. (Los eventos no deben ser factores externos o internos, deben ser eventos internos de la organización).

        
        Texto original: \"$text\"
        
        Ejemplos de buena redacción:
        - 'Favorecimiento indebido al solicitante, debido al procedimiento desactualizado y a la atención mediante canales personales.'
        - 'Mayor cobertura en la atención de solicitudes, debido a la directiva de atención y a la disponibilidad de equipos informáticos.'
        
        Instrucciones:
        1. Identifica el efecto (consecuencia) y la causa (origen) en el texto original.
        2. Si falta información, infiere lo más lógico basado en el contexto de gestión pública o empresarial.
        3. Devuelve SOLO el texto reescrito, sin explicaciones adicionales ni comillas.
        4. Mantén un lenguaje formal y técnico.
        5. Longitud máxima: 500 caracteres.";

        return $this->callOpenAI($prompt);
    }

    public function improveRiskConsequence(string $riskDescription, ?string $currentConsequence = null): string
    {
        $prompt = "Actúa como un experto en gestión de riesgos ISO 9001, ISO 37301, ISO 31000 e ISO 37001.
        
        Contexto (Descripción del Riesgo): \"$riskDescription\"
        
        Tarea:
        ";

        if (empty($currentConsequence)) {
            $prompt .= "La 'Consecuencia' está vacía. Sugiere 2 posibles consecuencias directas y coherentes con la descripción del riesgo proporcionada dando un enfoque en la afectación o impacto en la continuidad del proceso o servicio.
            Formato de respuesta:
            - Opción 1: [Consecuencia sugerida]
            - Opción 2: [Consecuencia sugerida]";
        } else {
            $prompt .= "Analiza la siguiente 'Consecuencia' actual: \"$currentConsequence\".
            Mejora su redacción para que sea más clara, técnica y tenga una relación lógica directa con la descripción del riesgo.
            Mantén un tono formal.";
        }

        $prompt .= "\n\nIMPORTANTE: Devuelve SOLO el texto de la respuesta (las opciones o la redacción mejorada), sin introducciones ni explicaciones adicionales. Longitud máxima: 500 caracteres.";

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
    public function generateIndicatorDescription(string $name): string
    {
        $prompt = "Actúa como un experto en gestión de calidad y procesos (ISO 9001).
        Tu tarea es generar una descripción corta y precisa para un indicador de gestión basado en su nombre.
        
        Nombre del Indicador: \"$name\"
        
        Instrucciones:
        1. La descripción debe explicar qué mide este indicador de forma clara.
        2. Mantén un tono técnico y formal.
        3. Longitud máxima: 300 caracteres (para asegurar que quepa en el campo).
        4. Inicia directamente con la descripción (ej: 'Mide el porcentaje de...', 'Calcula la cantidad de...').
        
        Ejemplos:
        - Nombre: 'Satisfacción del Cliente' -> Descripción: 'Mide el nivel de satisfacción de los clientes respecto al servicio recibido a través de encuestas.'
        - Nombre: 'Tiempo de Respuesta' -> Descripción: 'Calcula el tiempo promedio transcurrido desde la recepción de una solicitud hasta su atención final.'
        
        Devuelve SOLO la descripción generada.";

        return $this->callOpenAI($prompt);
    }
    public function improveDocumentSummary(string $summary): string
    {
        $prompt = "Actúa como un experto en gestión documental y normas ISO.
        Tu tarea es mejorar la redacción del siguiente 'Resumen del Documento' para que sea más claro, conciso y profesional.
        Debe explicar brevemente el propósito y alcance del documento.
        
        Texto original: \"$summary\"
        
        Instrucciones:
        1. Mantén un tono formal y técnico.
        2. Corrige errores gramaticales o de sintaxis.
        3. Si el texto es muy corto, expande ligeramente para darle contexto (asumiendo un contexto corporativo estándar).
        4. Longitud máxima: 400 caracteres.
        5. Devuelve SOLO el texto mejorado.
        
        Ejemplo entrada: 'manual para vender mejor'
        Ejemplo salida: 'Este documento establece los lineamientos y técnicas fundamentales para optimizar el proceso de ventas, asegurando un enfoque estandarizado y efectivo en la atención al cliente.'";

        return $this->callOpenAI($prompt);
    }

    public function generateQuarterlyReportAnalysis(array $data): array
    {
        $prompt = "Actúa como un analista de satisfacción del cliente para una institución pública peruana.

        Datos del trimestre {$data['trimestre']} del año {$data['anio']} para el proceso '{$data['proceso_nombre']}':

        1. Resultados Encuestas (NPS/Satisfacción): {$data['resumen_encuestas']}
        2. Sugerencias recibidas: {$data['resumen_sugerencias']}
        3. Reclamos reportados: {$data['reclamos']}
        4. Salidas No Conformes (SNC): {$data['resumen_snc']}

        Genera dos secciones de redacción profesional, técnica y ejecutiva, actuando como el Propietario del Proceso que presenta su informe trimestral a la Alta Dirección.
        
        Instrucciones de Redacción:
        - Tono: Formal, objetivo, orientado a la mejora continua y basado en evidencia.
        - Formato: TEXTO PLANO, sin HTML ni Markdown. Usa saltos de línea para separar párrafos y guiones (-) para listas.
        
        Secciones requeridas:
        1. 'oportunidades': Basado en las debilidades o áreas de mejora (bajas encuestas, quejas, SNCs), propón 2-3 acciones concretas. Si los resultados son excelentes, propón acciones para la sostenibilidad o innovación. Usa guiones para cada punto.
        2. 'conclusiones': Redacta 2-3 párrafos analizando el desempeño integral del trimestre. Conecta los puntos (ej. \"A pesar del aumento en quejas, el NPS se mantuvo alto debido a...\"). Evita frases genéricas.

        Devuelve un objeto JSON con las claves exactas: { \"oportunidades\": \"...\", \"conclusiones\": \"...\" }";

        return json_decode($this->callOpenAI($prompt, true), true) ?? ['oportunidades' => 'No se pudo generar análisis.', 'conclusiones' => 'No se pudo generar conclusiones.'];
    }
    public function generateNormaRequirements(string $normName): array
    {
        $prompt = "Actúa como un experto en normas ISO (ISO 9001, 37001, 37301, 45001, etc.).
        Tu tarea es generar la lista completa de requisitos auditables para la norma \"$normName\".

        Instrucciones:
        1. Devuelve una lista de objetos JSON.
        2. Cada objeto debe tener: 'numeral' (ej. 4.1), 'denominacion' (ej. Comprensión de la organización), 'detalle' (breve descripción del debe).
        3. Cubre desde el capítulo 4 en adelante.
        4. Omitir introducción y definiciones.
        5. Devuelve SOLO UN ARRAY JSON puro: [{\"numeral\": \"...\", \"denominacion\": \"...\", \"detalle\": \"...\"}, ...]
        Si la norma no es conocida, intenta inferir una estructura de alto nivel (HLS) o indica error.";

        $response = $this->callOpenAI($prompt, true);
        return json_decode($response, true)['requisitos'] ?? json_decode($response, true) ?? [];
    }
}
