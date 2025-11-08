<?php

namespace App\Livewire\Requerimiento;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Requerimiento;
use App\Models\Proceso;
use App\Models\Documento;
use App\Models\TipoDocumento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class RequerimientoFormulario extends Component
{
    use WithFileUploads;

    public $proceso_id, $asunto, $descripcion, $justificacion;
    public $ruta_archivo_requerimiento;
    public $documentos = [];
    public $documentos_seleccionados = [];
    public $requerimiento_id;
    public $modalTitle;
    public $archivo; // archivo temporal
    public $documentos_nuevos = [];
    public $documentos_existentes = [];

    public $procesos;
    public $tipos_documento;
    protected $listeners = ['nuevoRequerimiento'];
    public function mount()
    {
        $this->procesos = Proceso::All();
        $this->tipos_documento = TipoDocumento::All();
    }

    public function nuevoRequerimiento()
    {
        $this->modalTitle = "Nuevo requerimiento";
        $this->procesos = Proceso::select('id', 'proceso_nombre')
            ->orderBy('proceso_nombre')
            ->get();
        $this->tipos_documento = TipoDocumento::select('id', 'nombre')
            ->orderBy('nombre')
            ->get();
    }


    public function updatedProcesoId()
    {
        $this->documentos_existentes = Documento::where('proceso_id', $this->proceso_id)->get();
    }

    public function guardar()
    {
        $this->validate([
            'proceso_id' => 'required',
            'asunto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'justificacion' => 'nullable|string'
        ]);

        $rutaArchivo = null;
        if ($this->archivo) {
            $rutaArchivo = $this->archivo->store('requerimientos', 'public');
        }

        $requerimiento = Requerimiento::create([
            'proceso_id' => $this->proceso_id,
            'user_id' => Auth::id(),
            'asunto' => $this->asunto,
            'descripcion' => $this->descripcion,
            'justificacion' => $this->justificacion,
            'ruta_archivo_requerimiento' => $rutaArchivo,
            'estado' => 'creado',
        ]);

        // Asociar documentos seleccionados
        foreach ($this->documentos_seleccionados as $docId) {
            $requerimiento->documentos()->attach($docId);
        }

        // Guardar referencias a documentos nuevos (por crear/actualizar/eliminar)
        foreach ($this->documentos_nuevos as $doc) {
            if (!empty($doc['tipo']) && !empty($doc['nombre']) && !empty($doc['accion'])) {
                $requerimiento->documentosPropuestos()->create($doc);
            }
        }

        session()->flash('message', 'Requerimiento registrado correctamente.');
        $this->dispatch('cerrarModalRequerimiento'); // para cerrar modal desde JS
    }

    public function agregarDocumentoNuevo()
    {
        $this->documentos_nuevos[] = ['tipo' => '', 'nombre' => '', 'accion' => ''];
    }

    public function eliminarDocumentoNuevo($index)
    {
        unset($this->documentos_nuevos[$index]);
        $this->documentos_nuevos = array_values($this->documentos_nuevos);
    }
    public function render()
    {
        return view('livewire.requerimiento.requerimiento-formulario');
    }
}
