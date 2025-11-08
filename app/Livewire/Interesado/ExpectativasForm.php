<?php

namespace App\Livewire\Interesado;
use Livewire\Component;
use App\Models\ParteInteresada;
use App\Models\Proceso;
use App\Models\Expectativa;

class ExpectativasForm extends Component
{
    public $parte_interesada_id,  $parte_interesada_nombre, $exp_descripcion, $proceso_id, $exp_observaciones,$exp_prioridad, $exp_criticidad,$exp_viabilidad;
    public $expectativas, $partes, $procesos;
    public $exp_tipo = 'necesidad';
    public $expectativa_id;
    public $sig = [];

    public $sistemas = ['ISO 9001', 'ISO 37001', 'ISO 37301', 'ISO 27001', 'ISO 21001'];

    protected $listeners = ['mostrarExpectativas'];

    public function mount()
    {

        $this->partes = ParteInteresada::select('id', 'pi_nombre')->where('pi_activo', 1)->get();
        $this->procesos = Proceso::select('id', 'cod_proceso', 'proceso_nombre')->where('proceso_nivel', 0)->orderBy('cod_proceso')->get();
        $this->expectativas = [];

    }

    public function mostrarExpectativas($id)
    {   $parte_interesada = ParteInteresada::select('id', 'pi_nombre')->where('id', $id)->first();
        $this->parte_interesada_id = $parte_interesada->id;
        $this->parte_interesada_nombre = $parte_interesada->pi_nombre;
        $this->expectativas = Expectativa::where('parte_interesada_id', $id)->with('parteInteresada', 'proceso')->get();
        
        $this->dispatch('refreshSelect2');
    }

    public function guardar()
    {
        Expectativa::updateOrCreate(
            ['id' => $this->expectativa_id],
            [
                'parte_interesada_id' => $this->parte_interesada_id,
                'exp_tipo' => $this->exp_tipo,
                'exp_descripcion' => $this->exp_descripcion,
                'sig' => $this->sig,
                'proceso_id' => $this->proceso_id,
                'exp_observaciones' => $this->exp_observaciones,
                'exp_criticidad' => $this->exp_criticidad,
                'exp_viabilidad' => $this->exp_viabilidad,
            ]
        );

        $this->resetCampos();
        $this->expectativas = Expectativa::with('parteInteresada', 'proceso')->get();
        session()->flash('message', 'Registro guardado correctamente.');
        $this->dispatch('cerrar-expectativafrm');
    }

    public function editar($id)
    {

        $expectativa = Expectativa::find($id);
        
        if ($expectativa) {
            $this->expectativa_id = $expectativa->id;
            $this->parte_interesada_id = $expectativa->parte_interesada_id;
            $this->exp_tipo = $expectativa->exp_tipo;
            $this->exp_descripcion = $expectativa->exp_descripcion;
            $this->sig = $expectativa->sig;
            $this->proceso_id = $expectativa->proceso_id;
            $this->exp_observaciones = $expectativa->exp_observaciones;
            $this->exp_criticidad = $expectativa->exp_criticidad;
            $this->exp_viabilidad = $expectativa->exp_viabilidad;

            $this->dispatch('refreshSelect2');
        } else {
            session()->flash('error', 'Expectativa no encontrada.');
        }
    }
    public function eliminar($id)
    {
        Expectativa::destroy($id);
        $this->expectativas = Expectativa::with('parteInteresada', 'proceso')->get();
        session()->flash('message', 'Registro eliminado correctamente.');
    }
    public function resetCampos()
    {
        $this->reset(['exp_tipo', 'exp_descripcion', 'sig', 'proceso_id', 'exp_observaciones']);
    }
    public function render()
    {
        return view('livewire.interesado.expectativas-form');
    }
}
