<?php

namespace App\Livewire;
use Livewire\WithFileUploads;
use Livewire\Component;
use App\Models\Documento;
use App\Models\DocumentoVersion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;

class DocumentoVersionModal extends Component
{
    use WithFileUploads;

    public $id, $documento_id, $archivo_path, $version, $control_cambios,  $fecha_aprobacion, $fecha_publicacion;
    public $modalTitle, $btnName, $method, $actionRoute;
    public $archivo;
    public $documento_version;

    protected $listeners = [
        'mostrarVersion' => 'mostrarVersion',
        'nuevaVersion' => 'nuevaVersion',
    ];

    public function mostrarVersion($id)
    {
        $this->modalTitle = 'Editar Version';
        $this->btnName = 'Actualizar';
        $this->method = "PUT";
        $documento_version = DocumentoVersion::findOrFail($id);
        if ($documento_version) {
            $this->actionRoute = "Actualizar";
            $this->id = $documento_version->id;
            $this->archivo_path = $documento_version->archivo_path;
            $this->control_cambios = $documento_version->control_cambios;
            $this->fecha_aprobacion = Carbon::parse($documento_version->fecha_aprobacion)->toDateString();
            $this->fecha_publicacion = Carbon::parse($documento_version->fecha_publicacion)->toDateString();
            $this->version = $documento_version->version;
        }

    }
    public function nuevaVersion($id)
    {
        $this->documento_id = $id;
        $this->fecha_publicacion = Carbon::now()->toDateString();
        $this->mount();

    }

    public function mount()
    {
        $this->modalTitle = 'Añadir Version';
        $this->btnName = 'Guardar';
        $this->method = "POST";
        $this->actionRoute = "Insertar";
        $this->version = "0";
        $this->reset(['archivo', 'control_cambios', 'fecha_aprobacion']);
    }



    public function update()
    {
        $documentoVersion = DocumentoVersion::find($this->id);
        $documento = $documentoVersion ? $documentoVersion->documento : Documento::find($this->documento_id);
        $proceso = $documento->proceso;

        // Determinar la versión según la acción
        $ultimaVersion = ($this->actionRoute === "Insertar")
            ? (($documento->ultimaVersion->version ?? 0) + 1)
            : $this->version;

        $data = [
            'fecha_publicacion' => Carbon::now()->toDateString(),
            'control_cambios' => $this->control_cambios,
            'version' => $ultimaVersion,
            'archivo_path' => $this->archivo_path, // valor por defecto, se puede sobreescribir
        ];

        // Procesar archivo si hay archivo subido
        if ($this->archivo) {
            $codProceso = $proceso->cod_proceso;
            $tipoDocumento = $documento->tipo_documento->nombre;
            $carpeta = $codProceso . '/' . $tipoDocumento;

            if (!Storage::disk('documentos')->exists($carpeta)) {
                Storage::disk('documentos')->makeDirectory($carpeta);
            }

            $versionString = 'v' . str_pad($ultimaVersion, 2, '0', STR_PAD_LEFT);
            $extension = $this->archivo->getClientOriginalExtension();
            $nombreArchivo = $documento->cod_documento . '-' . $versionString . '.' . $extension;

            $path = $this->archivo->storeAs($carpeta, $nombreArchivo, 'documentos');

            $data['archivo_path'] = $path;
        }

        if ($this->actionRoute === "Insertar") {
            $data['fecha_aprobacion'] = $this->fecha_aprobacion ?? Carbon::now()->toDateString();
            $data['documento_id'] = $documento->id;

            DocumentoVersion::create($data);
        } else {
            if ($documentoVersion) {
                $documentoVersion->update($data);
            } else {
                // Opcional: manejar error o crear nuevo registro
            }
        }

        $this->dispatch('versionActualizada', [], 'documento-modal');
    }



    public function render()
    {
        return view('livewire.documento-version-modal');
    }
}
