<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Proceso;
use Illuminate\Support\Facades\Log;

class ProcesoModal extends Component
{

    public $proceso_id, $cod_proceso, $nombre, $objetivo, $tipo_proceso, $nivel_proceso, $estado, $cod_proceso_padre,$proceso_nombre;
    public $modalTitle, $actionRoute;
    public $isMacroproceso = false;
    public $charCountNombre = 0;
    public $charCountObjetivo = 0;
    protected $listeners = ['refreshComponent' => '$refresh', 'syncData' => 'syncData'];

    public function updatedNombre($value)
    {

        // Actualiza el contador de caracteres de 'nombre'
        $this->charCountNombre = mb_strlen($value);
       
    }

    // Método para actualizar el contador de caracteres cuando 'objetivo' cambie
    public function updatedObjetivo($value)
    {
        // Actualiza el contador de caracteres de 'objetivo'
        $this->charCountObjetivo = mb_strlen($value);
    }
    public function updatedNivelProceso()
    {
        // Si el valor de nivel_proceso es '0' (Macroproceso), deshabilitamos los campos
        $this->isMacroproceso = $this->nivel_proceso == 0;
        if ($this->isMacroproceso) {
            $this->proceso_nombre = '';
        }
    
    }
    // Método que inicializa el componente, ya sea para editar o crear un proceso
    public function mount($proceso = null)
    {
        if ($proceso) {
            $this->modalTitle = 'Editar Proceso';
            $this->actionRoute = route('procesos.update', $proceso->id);
            $this->proceso_id = $proceso->id;
            $this->cod_proceso = $proceso->cod_proceso;
            $this->nombre = $proceso->nombre;
            $this->objetivo = $proceso->objetivo;
            $this->tipo_proceso = $proceso->tipo_proceso;
            $this->nivel_proceso = $proceso->nivel_proceso;
            $this->estado = $proceso->estado;
        } else {
            $this->modalTitle = 'Crear Proceso';
            $this->actionRoute = route('procesos.store');
        }
    }

    public function save()
    {

        try {
            if ($this->proceso_id) {
                // Editar un proceso existente
                $proceso = Proceso::find($this->proceso_id);
                $proceso->update([
                    'cod_proceso' => $this->cod_proceso,
                    'nombre' => $this->nombre,
                    'objetivo' => $this->objetivo,
                    'tipo_proceso' => $this->tipo_proceso,
                    'nivel_proceso' => $this->nivel_proceso,
                    'estado' => $this->estado,
                ]);
            } else {
                // Crear un nuevo proceso
                Proceso::create([
                    'cod_proceso' => $this->cod_proceso,
                    'nombre' => $this->nombre,
                    'objetivo' => $this->objetivo,
                    'tipo_proceso' => $this->tipo_proceso,
                    'nivel_proceso' => $this->nivel_proceso,
                    'estado' => $this->estado,
                ]);
            }

            $this->dispatchBrowserEvent('close-modal');
        } catch (\Exception $e) {
            // Log error or handle exception
            session()->flash('error', 'Hubo un problema al guardar el proceso: ' . $e->getMessage());
        }
    }
    public function closeModal()
    {
        $this->emit('closeModal');  // Emitimos un evento para cerrar el modal desde JavaScript
    }
    public function syncData()
    {
        // Sincronizar datos cuando el modal se abre
    }

    public function clearForm()
    {
        $this->reset(['cod_proceso', 'nombre', 'objetivo']); // Limpia el formulario
        $this->emit('openModal'); // Abre el modal
    }
    public function render()
    {
        return view('livewire.proceso-modal');
    }

}