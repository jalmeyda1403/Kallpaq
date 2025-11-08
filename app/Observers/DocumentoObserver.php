<?php

namespace App\Observers;
use Illuminate\Support\Facades\Auth;
use App\Models\DocumentoMovimiento;
use App\Models\Documento;

class DocumentoObserver
{
    public function created(Documento $documento): void
    {
        DocumentoMovimiento::create([
            'documento_id' => $documento->id,
            'usuario_id' => Auth::id(), // Obtiene el ID del usuario autenticado
            'accion' => 'CREACIÓN',
            'descripcion' => "Se creó el documento '{$documento->nombre_documento}' con código '{$documento->cod_documento}'.",
        ]);
    }

    /**
     * Se ejecuta DESPUÉS de que un Documento es ACTUALIZADO.
     */
    public function updated(Documento $documento): void
    {
      
        $cambios = $documento->getChanges();

        // No registramos nada si solo se actualizó el 'updated_at'
        if (count($cambios) === 1 && isset($cambios['updated_at'])) {
            return;
        }

        $descripcion = 'Se actualizaron los siguientes campos: ';
        foreach ($cambios as $campo => $valorNuevo) {
            if ($campo === 'updated_at')
                continue; // Ignoramos este campo

            // getOriginal() nos da el valor anterior del campo
            $valorAntiguo = $documento->getOriginal($campo);
            $descripcion .= "'{$campo}' de '{$valorAntiguo}' a '{$valorNuevo}'; ";
        }

        DocumentoMovimiento::create([
            'documento_id' => $documento->id,
            'usuario_id' => Auth::id(),
            'accion' => 'ACTUALIZACIÓN',
            'descripcion' => rtrim($descripcion, '; '), // Quita el último punto y coma
        ]);
    }

   
    public function deleting(Documento $documento): void
    {
        DocumentoMovimiento::create([
            'documento_id' => $documento->id,
            'usuario_id' => Auth::id(),
            'accion' => 'ELIMINACIÓN',
            'descripcion' => "Se eliminó el documento '{$documento->nombre_documento}' (Código: {$documento->cod_documento}).",
        ]);
    }
}
