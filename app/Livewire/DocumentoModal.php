<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use App\Models\Documento;
use App\Models\DocumentoVersion;
use App\Models\TipoDocumento;
use Livewire\WithFileUploads;


class DocumentoModal extends Component
{
    use WithFileUploads;

    public $documento_id, $modalTitle, $actionRoute, $btnName, $method, $archivo, $tiposDocumento;
    public $archivo_version, $fecha_aprobacion, $fecha_publicacion;

    public $versiones = []; // para cargar versiones relacionadas

    public $cod_documento, $tipo_documento_id, $proceso_id, $proceso_nombre, $version, $nombre, $fuente, $estado;

    protected $listeners = [
        'verDocumento' => 'verDocumento',   // Evento para cargar documento en edición
        'nuevoDocumento' => 'nuevoDocumento',
        'recargarVersiones' => 'recargarVersiones',// Evento para crear nuevo documento
        'eliminarVersion' => 'eliminarVersion',
        'actualizarProceso' => 'actualizarProceso', // Evento para eliminar una versión
        'procesoSeleccionado' => 'procesoSeleccionado',
    ];
    public function procesoSeleccionado($datos)
    {
        $this->proceso_id = $datos['id'];
        $this->proceso_nombre = $datos['nombre'];
    }

    public function actualizarProceso($id)
    {
        $this->proceso_id = $id;
    }
    public function mount($procesoId = null)
    {
        $this->modalTitle = 'Crear Documento';
        $this->btnName = 'Guardar';
        $this->method = "POST";
        $this->actionRoute = route('documentos.store');
        $this->tiposDocumento = TipoDocumento::all();
        // Valores por defecto
        $this->documento_id = null;
        $this->cod_documento = "";
        $this->tipo_documento_id = null;
        $this->proceso_id = $procesoId;
        $this->nombre = "";
        $this->fuente = "";
        $this->estado = "";
        $this->versiones = [];

    }

    public function nuevoDocumento()
    {
        $this->reset([
            'documento_id',
            'cod_documento',
            'tipo_documento_id',
            'nombre',
            'fuente',
            'estado',
            'versiones',
            'version',
        ]);

        // Si quieres, asigna valores por defecto explícitos

        $this->modalTitle = 'Crear Documento';
        $this->btnName = 'Guardar';
        $this->method = "POST";
        $this->actionRoute = route('documentos.store');
        $this->dispatch('limpiar-versiones');
    }

    public function verDocumento($id)
    {
        $this->modalTitle = 'Editar Documento';
        $this->btnName = 'Actualizar';
        $this->method = "PUT";

        $documento = Documento::find($id);
        if (!$documento)
            return;

        $this->documento_id = $documento->id;
        $this->actionRoute = route('documentos.update', $id);
        $this->cod_documento = $documento->cod_documento;
        $this->tipo_documento_id = $documento->tipo_documento_id;
        $this->proceso_id = $documento->proceso_id;
        $this->proceso_nombre = $documento->proceso->proceso_nombre;
        $this->nombre = $documento->nombre;
        $this->fuente = $documento->fuente;
        $this->estado = $documento->estado;

        // cargar versiones
        $this->obtenerVersiones();

        // Calcular siguiente versión incremental
        $lastVersion = collect($this->versiones)->max('version');
        $this->version = is_null($lastVersion) ? 0 : $lastVersion + 1;


    }
    public function obtenerVersiones()
    {

        if (is_null($this->documento_id)) {
            $this->versiones = [];
            return;
        }
        $documento = Documento::find($this->documento_id);
        if (!$documento) {
            $this->versiones = [];
            return;
        }
        $this->versiones = $documento->versiones->sortBy('version')->values()->all();
    }
    public function render()
    {
        return view('livewire.documento-modal');
    }

    public function eliminarVersion($id)
    {
        $documentoVersion = DocumentoVersion::find($id);

        $documento = $documentoVersion->documento;

        // Verificar si es la última versión
        $ultimaVersion = $documento->ultimaVersion; // Asumo que existe esta relación y propiedad
        if ($ultimaVersion->id == $documentoVersion->id) {
            if ($documentoVersion->archivo_path && Storage::disk('documentos')->exists($documentoVersion->archivo_path)) {
                Storage::disk('documentos')->delete($documentoVersion->archivo_path);

            }
            $documentoVersion->delete();
            $this->dispatch('versionEliminada');
            $this->obtenerVersiones();
        }
    }

}
