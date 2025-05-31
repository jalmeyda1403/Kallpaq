<?php

namespace App\Livewire;

use App\Models\Requisito;
use App\Models\Salida;

use Carbon\Carbon;

use Livewire\Component;


class SipocSalidaModal extends Component
{

    public $salida_id, $salida_proceso_id, $tipo, $salida_proceso_nombre;
    public $modalTitle, $btnName, $method;

    public $requisito_id, $requisito_cod, $requisito, $documento, $fecha_requisito;
    public $salida;
    public $requisitos = [];

    public $mostrarPanelRequisito = false;
    protected $listeners = [
        'nuevaSalida' => 'nuevaSalida',
        'editarSalida' => 'editarSalida',
        'editarRequisito' => 'editarRequisito',
        'removeRequisito' => 'removeRequisito',
        'guardarRequisito' => 'guardarRequisito',
        'guardarSalida' => 'guardarSalida'
        
    ];

    public function mount($salida = null)
    {
        if ($salida) {
            $this->salida_id = $salida->id;
            $this->salida_proceso_id = $salida->proceso_id;
            $this->salida = $salida->salida;
            $this->tipo = $salida->tipo;
            $this->salida_proceso_nombre = $salida->proceso->proceso_nombre;
            $this->requisitos = $salida->requisitos;
            $this->method = 'PUT';
            $this->cargarRequisitos($salida->id);

        }
    }
    public function guardarSalida()
    {
           $salida = Salida::updateOrCreate(
            ['id' => $this->salida_id],
            [
                'salida' => $this->salida,
                'tipo' => $this->tipo,
                'proceso_id' => $this->salida_proceso_id
            ]
        );
        $this->dispatch('salidaGuardada','sipoc-modal');

    }

    public function nuevaSalida()
    {
        $this->reset(['salida_id', 'salida', 'tipo', 'proceso_id', 'proceso_nombre', 'requisitos']);
        $this->modalTitle = 'Nueva Salida';
        $this->btnName = 'Guardar';
        $this->method = 'POST';
    }

    public function editarSalida($id)
    {
        $salida = Salida::with('requisitos', 'proceso')->findOrFail($id);

        $this->salida_id = $salida->id;
        $this->salida_proceso_id = $salida->proceso_id;
        $this->salida_proceso_nombre = $salida->proceso->proceso_nombre ?? '';
        $this->salida = $salida->salida;
        $this->tipo = $salida->tipo;
        $this->requisitos = $salida->requisitos;
        $this->modalTitle = 'Editar Salida';
        $this->btnName = 'Actualizar';
        $this->method = 'PUT';
    }
    public function cargarRequisitos($salida_id)
    {
        $salida = Salida::with('requisitos', 'proceso')->findOrFail(id: $salida_id);
        $this->requisitos = $salida->requisitos;
        
    }
    
    public function guardarRequisito()
    {
        
        if ($this->requisito_id) {
       
            // Actualizar el requisito existente
            $requisito = Requisito::find($this->requisito_id);
            $requisito->update([
                'requisito' => $this->requisito,
                'documento' => $this->documento,
                'fecha_requisito' => $this->fecha_requisito,
            ]);
        } else {
            // Crear un nuevo requisito
            Requisito::create([
                'requisito' => $this->requisito,
                'documento' => $this->documento,
                'fecha_requisito' => $this->fecha_requisito,
                'salida_id' => $this->salida_id,
            ]);
        }

        $this->cargarRequisitos($requisito->salida->id);

    }

    public function addRequisito()
    {

    }

    public function removeRequisito($id)
    {
        $requisito = Requisito::findOrFail($id);
        $requisito->delete();
        
        $this->cargarRequisitos($requisito->salida->id);
    }
    public function editarRequisito($id)
    {
        $requisito = Requisito::findOrFail($id);
        $this->requisito_id = $id;
        $this->requisito_cod = $requisito->requisito_cod;
        $this->requisito = $requisito->requisito;
        $this->documento = $requisito->documento;
        $this->fecha_requisito = $requisito->fecha_requisito;
        $this->mostrarPanelRequisito = true;
    }
    public function cancelarEdicion()
    {
        $this->mostrarPanelRequisito = false;

    }   

    public function render()
    {
        return view('livewire.sipoc-salida-modal');
    }
}
