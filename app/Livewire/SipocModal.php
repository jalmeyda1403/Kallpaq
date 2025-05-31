<?php

namespace App\Livewire;
use App\Models\Proceso;
use Livewire\Component;
use App\Models\Sipoc;


class SipocModal extends Component
{

    public $proceso_id, $proceso_nombre, $proveedores, $entradas, $clientes;
    public $modalTitle, $btnName, $actionRoute, $method;
    public $salidas = [];
    public $modoEdicion = false;

    protected $rules = [
        'proveedores' => 'required|string',
        'entradas' => 'required|string',
        'clientes' => 'required|string',
    ];
    protected $listeners = [
        'editarSipoc' => 'editarSipoc',  // Evento de edición
        'nuevoSipoc' => 'nuevoSipoc' // Evento de creación de nuevo proceso
    ];
    public function mount()
    {
        $this->actionRoute = route('sipoc.store');
        $this->method = "POST";
        $this->proceso_id = "";
        $this->modalTitle = 'Crear SIPOC';
        $this->btnName = "Guardar";
        $this->proveedores = "";
        $this->entradas = "";
        $this->clientes = "";
        $this->salidas = [];

    }
    public function nuevoSipoc($id)
    {
        // Restablece los valores a los configurados en mount
        $proceso = Proceso::findOrFail($id);
        $this->proceso_id = $id;
        $this->proceso_nombre = $proceso->proceso_nombre;
        $this->salidas = $proceso->descendiente_salidas();
    }

    public function editarSipoc($id)
    {
        $this->modalTitle = 'Actualizar SIPOC';
        $this->btnName = "Actualizar";
        $this->method = "PUT";

        $sipoc = Sipoc::where('proceso_id', $id)->first();
      
        $this->actionRoute = route('sipoc.update', $sipoc->id);
        $this->proceso_id = $id;
        $this->proceso_nombre = $sipoc->proceso->proceso_nombre;

        $this->proveedores = $sipoc->proveedores;
        $this->entradas = $sipoc->entradas;
        $this->clientes = $sipoc->clientes;
        $this->obtenerSalidas($id);

    }
    public function obtenerSalidas($id = null) 
{ 
    $procesoId = $id ?? $this->proceso_id;

    $proceso = Proceso::findOrFail($procesoId);
    $this->salidas = $proceso->descendiente_salidas();
}

    public function render()
    {
        return view('livewire.sipoc-modal');
    }
}
