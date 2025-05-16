<?php

namespace App\Livewire;
use App\Models\Proceso;
use Livewire\Component;

class AsignarOUOModal extends Component
{
    public $proceso_id, $modalTitle;
    public $ouo_id, $ouos;
    public $ouo_nombre;
    public $proceso;



    protected $listeners = [
        'obtenerOUO' => 'obtenerOUO'
    ];

    // Método mount para inicialización
    public function mount()
    {
        $this->modalTitle = 'Asignar OUO';

    }

    public function obtenerOUO($id)
    {
        try {
            // Validar que el ID del proceso no esté vacío
            if (empty($id)) {
                throw new \Exception("El ID del proceso no puede estar vacío.");
            }

            // Encuentra el proceso con las unidades orgánicas asociadas
            $this->proceso = Proceso::with('ouos')->find($id);
            $this->proceso_id = $id;
            $this->ouos = $this->proceso->ouos;


        } catch (\Exception $e) {
            // Manejar la excepción, por ejemplo, mostrando un mensaje de error
            $this->emit('error', $e->getMessage());
        }

    }

    public function asociarOUO()
    {
        if (!empty($this->ouo_id) && !empty($this->proceso_id) ){
            $this->proceso = Proceso::find($this->proceso_id);
            $this->proceso->ouos()->attach($this->ouo_id);
            $this->obtenerOUO($this->proceso_id);
        }
    }

    public function desasociarOUO($ouoId)
    {
        // Encuentra el proceso y disocia la unidad orgánica
        $this->proceso = Proceso::find($this->proceso_id);
        $this->proceso->ouos()->detach($ouoId);

        // Actualiza la lista de unidades orgánicas asociadas
        $this->obtenerOUO($this->proceso_id);
    }

     // Función para actualizar responsable
     public function actualizarResponsable($ouoId)
     {
         $this->proceso = Proceso::find($this->proceso_id);
         $responsable = $this->proceso->ouos()->where('id_ouo', $ouoId)->first()->pivot->responsable;
 
         // Cambiar responsable si es 0, poner 1
         $newResponsable = ($responsable == 0) ? 1 : 0;
 
         // Actualizar el valor del responsable en la tabla pivote
         $this->proceso->ouos()->updateExistingPivot($ouoId, ['responsable' => $newResponsable]);
 
         // Refrescar la lista de unidades orgánicas
         $this->obtenerOUO($this->proceso_id);
     }
 
     // Función para actualizar delegada
     public function actualizarDelegada($ouoId)
     {
         $this->proceso = Proceso::find($this->proceso_id);
         $delegada = $this->proceso->ouos()->where('id_ouo', $ouoId)->first()->pivot->delegada;
 
         // Cambiar delegada si es 0, poner 1
         $newDelegada = ($delegada == 0) ? 1 : 0;
 
         // Actualizar el valor de delegada en la tabla pivote
         $this->proceso->ouos()->updateExistingPivot($ouoId, ['delegada' => $newDelegada]);
 
         // Refrescar la lista de unidades orgánicas
         $this->obtenerOUO($this->proceso_id);
     }
 
    public function render()
    {
        return view('livewire.asignarouo-modal');
    }
}
