<?php

namespace App\Observers;
use Illuminate\Support\Facades\Auth;
use App\Models\DocumentoVersion;
use App\Models\DocumentoMovimiento;

class DocumentoVersionObserver
{
    /**
     * Handle the DocumentoVersion "created" event.
     */
    public function created(DocumentoVersion $version): void
    {
        DocumentoMovimiento::create([
            // CRÍTICO: El movimiento se registra contra el ID del documento padre.
            'documento_id' => $version->documento_id,
            'usuario_id' => Auth::id(),
            'accion' => 'NUEVA VERSIÓN',
            'descripcion' => "Se creó y asoció la Versión '{$version->dv_version}'.",
        ]);
    }

    /**
     * Se ejecuta DESPUÉS de que una Versión de Documento es ACTUALIZADA.
     * (Opcional: solo si permites editar los detalles de una versión ya creada)
     */
    public function updated(DocumentoVersion $version): void
    {
        // Puedes añadir una lógica similar al DocumentoObserver si es necesario
        // para registrar qué campos de la versión cambiaron.
      
        DocumentoMovimiento::create([
            'documento_id' => $version->documento_id,
            'usuario_id' => Auth::id(),
            'accion' => 'ACTUALIZACIÓN DE VERSIÓN',
            'descripcion' => "Se actualizaron los detalles de la Versión '{$version->dv_version}'.",
        ]);
    }

    /**
     * Se ejecuta ANTES de que una Versión de Documento sea ELIMINADA.
     * (Opcional: solo si permites eliminar versiones)
     */
    public function deleting(DocumentoVersion $version): void
    {
        DocumentoMovimiento::create([
            'documento_id' => $version->documento_id,
            'usuario_id' => Auth::id(),
            'accion' => 'ELIMINACIÓN DE VERSIÓN',
            'descripcion' => "Se eliminó la Versión '{$version->dv_version}'.",
        ]);
    }
}
