<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        try {
            $userId = Auth::id() ?? 0;
            $response = $this->aiService->processChat($request->message, $userId);

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Chatbot Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Lo siento, ocurrió un error al procesar tu solicitud. Por favor intenta de nuevo.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
