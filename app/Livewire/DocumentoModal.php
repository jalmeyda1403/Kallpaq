<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Documento;
Use App\Models\TipoDocumento;
use Livewire\WithFileUploads;

class DocumentoModal extends Component
{
    use WithFileUploads;

    public $documento_id, $modalTitle, $actionRoute, $btnName, $method, $archivo,$tiposDocumento;

    public $cod_documento, $tipo_documento_id, $proceso_id, $proceso_nombre, $version, $nombre, $fuente, $enlace, $estado;

    protected $listeners = [
        'verDocumento' => 'verDocumento',   // Evento para cargar documento en edición
        'nuevoDocumento' => 'nuevoDocumento' // Evento para crear nuevo documento
    ];

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
        $this->version = "";
        $this->nombre = "";
        $this->fuente = "";
        $this->enlace = "";
        $this->estado = "";
       
    }

    public function nuevoDocumento()
    {
        $this->mount(); // Reiniciar valores a valores por defecto
    }

    public function verDocumento($id)
    {
        $this->modalTitle = 'Editar Documento';
        $this->btnName = 'Actualizar';
        $this->method = "PUT";

        $documento = Documento::find($id);

        if ($documento) {
            $this->documento_id = $documento->id;
            $this->actionRoute = route('documentos.update', $id);
            $this->cod_documento = $documento->cod_documento;
            $this->tipo_documento_id = $documento->tipo_documento_id;
            $this->proceso_id = $documento->proceso_id;
            $this->proceso_nombre = $documento->proceso->proceso_nombre;
            $this->version = $documento->version;
            $this->nombre = $documento->nombre;
            $this->fuente = $documento->fuente;
            $this->enlace = $documento->enlace;
            $this->estado = $documento->estado;                  
        }
    }

    public function render()
    {
        return view('livewire.documento-modal');
    }

}
