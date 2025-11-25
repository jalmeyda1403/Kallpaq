<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function uploadFile(Request $request)
    {
        // Validar que se haya enviado un archivo
        $request->validate([
            'file' => 'required|file|max:10240' // Máximo 10MB
        ]);

        $file = $request->file('file');

        // Definir el directorio donde se almacenarán las evidencias
        $path = $file->store('evidencias/salidas-nc', 'public');

        return response()->json([
            'path' => $path,
            'message' => 'Archivo subido exitosamente'
        ]);
    }
}
