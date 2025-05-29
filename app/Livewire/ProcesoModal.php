<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Proceso;
use Illuminate\Support\Facades\Log;

class ProcesoModal extends Component
{

    public $proceso_id, $cod_proceso, $proceso_tipo, $proceso_estado, $cod_proceso_padre, $proceso_nombre_padre,  $planificacion_pei_id, $planificacion_pei_nombre;
    public $modalTitle, $actionRoute, $btnName, $method;
    public $isMacroproceso = true;
    public $charCountNombre = 0;
    public $charCountObjetivo = 0;
    public $charCountSigla = 0;

    public $proceso_nombre = "";
    public $proceso_sigla = "";

    public $proceso_objetivo = "";
    public $proceso_nivel = "0";

      
    protected $listeners = [
        'verProceso' => 'verProceso',  // Evento de edición
        'nuevoProceso' => 'nuevoProceso' // Evento de creación de nuevo proceso
    ];
    protected $rules = [
        'cod_proceso' => 'required|max:13',
        'proceso_sigla' => 'required|max:5',
        'proceso_nombre' => 'required|max:200',
        'proceso_objetivo' => 'required|max:1000',
        'proceso_tipo' => 'required',
        'proceso_nivel' => 'required',
        'proceso_estado' => 'required',
    ];

    protected $messages = [
        'cod_proceso.required' => 'El código de proceso es obligatorio.',
        'proceso_sigla.required' => 'La sigla es obligatoria.',
        'proceso_nombre.required' => 'El nombre del proceso es obligatorio.',
        'proceso_objetivo.required' => 'El objetivo es obligatorio.',
        'proceso_tipo.required' => 'El tipo de proceso es obligatorio.',
        'proceso_nivel.required' => 'El nivel de proceso es obligatorio.',
        'proceso_estado.required' => 'El estado es obligatorio.',
    ];

    public function updatedProcesoNombre()
    {
        $this->charCountNombre = strlen($this->proceso_nombre);
    }
    public function updatedProcesoObjetivo()
    {
        $this->charCountObjetivo = strlen($this->proceso_objetivo);
    }
    public function updatedProcesoSigla()
    {
        $this->sigla = strtoupper($this->proceso_sigla);
        $this->charCountSigla = strlen($this->proceso_sigla);
    }
    public function updatedProcesoNivel($value)
    {
        $this->isMacroproceso = $value == 0;
    }

    public function verProceso($id)
    {
        $this->modalTitle = 'Editar Proceso';      
        $this->btnName = "Actualizar";
        $this->method = "PUT";
        
        $proceso = Proceso::find($id);

        if ($proceso) {
            $this->actionRoute = route('proceso.update', $id);
            $this->cod_proceso = $proceso->cod_proceso;
            $this->proceso_sigla = $proceso->proceso_sigla;
            $this->proceso_nombre = $proceso->proceso_nombre;
            $this->proceso_objetivo = $proceso->proceso_objetivo;
            $this->proceso_tipo = $proceso->proceso_tipo;
            $this->proceso_nivel = $proceso->proceso_nivel;
            $this->proceso_estado = $proceso->proceso_estado;
            $this->cod_proceso_padre = $proceso->cod_proceso_padre;
            $this->planificacion_pei_id = $proceso->planificacion_pei_id;
            $this->planificacion_pei_nombre = $proceso->planificacion_pei->planificacion_pei_nombre;

          
            // Verificar si el proceso tiene un padre
            if ($this->cod_proceso_padre && $proceso->procesoPadre) {
                $this->proceso_nombre_padre = $proceso->procesoPadre->proceso_nombre;
            } else {
                $this->proceso_nombre_padre = ''; // Dejar vacío si no tiene padre
            }

            // Establecer el título y la ruta de acción para la edición
            
        }
      
    }

    // Método que inicializa el componente, ya sea para editar o crear un proceso
    public function mount()
    {
        $this->modalTitle = 'Crear Proceso';
        $this->btnName= 'Guardar';
        $this->nivel_proceso = '0';
        $this->isMacroproceso = true;
        $this->cod_proceso = "";
        $this->proceso_nombre = "";
        $this->cod_proceso_padre = "";
        $this->proceso_nombre_padre= "";
        $this->proceso_objetivo = "";
        $this->proceso_sigla = "";
        $this->actionRoute = route('procesos.store');
        $this->method = "POST";

    }
    public function nuevoProceso()
    {
        // Restablece los valores a los configurados en mount
        
        $this->mount();
       
    }

    public function render()
    {
        return view('livewire.proceso-modal');
    }

}