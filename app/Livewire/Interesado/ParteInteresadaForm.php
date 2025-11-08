<?php

namespace App\Livewire\Interesado;
use App\Models\ParteInteresada;
use Livewire\Component;

class ParteInteresadaForm extends Component
{
    public $id, $parteInteresadaId, $pi_nombre, $pi_tipo, $pi_nivel_influencia, $pi_nivel_interes, $pi_descripcion;
    public $mensaje_cuadrante = "";
    // Propiedades para controlar el estado del modal y la interfaz
    public $modalTitle = 'Crear Nueva Parte Interesada';
    public $btnName = 'Guardar';
    public $actionRoute; // La ruta a donde se enviará el formulario
    public $method;      // El método HTTP (POST o PUT)

    protected $listeners = [
        'verParteInteresada' => 'verParteInteresada',
        'nuevoParteInteresada' => 'nuevoParteInteresada',
        'resetParteInteresada' => 'resetParteInteresada',
    ];

    // Reglas de validación (usadas para feedback en tiempo real)
    protected $rules = [
        'pi_nombre' => 'required|string|max:255',
        'pi_tipo' => 'required|string|max:255',
        'pi_nivel_influencia' => 'required|in:bajo,medio,alto',
        'pi_nivel_interes' => 'required|in:bajo,medio,alto',
        'pi_descripcion' => 'required|string|max:1000',
    ];


    public function nuevoParteInteresada()
    {
        $this->resetParteInteresada();
        $this->actionRoute = route('partes.store'); // Ruta para crear
        // También puedes definir valores por defecto si lo deseas
        $this->modalTitle = 'Crear Nueva Parte Interesada';
        $this->btnName = 'Guardar';
        $this->method = 'POST';

    }
    public function resetParteInteresada()
    {
        $this->reset([
            'parteInteresadaId',
            'pi_nombre',
            'pi_tipo',
            'pi_nivel_influencia',
            'pi_nivel_interes',
            'pi_descripcion',
            'modalTitle',
            'btnName',
            'actionRoute',
            'method',
            'mensaje_cuadrante'
        ]);
        $this->resetValidation();
        $this->resetErrorBag();
    }

    public function verParteInteresada($id)
    {
        $this->id = $id;
        $this->modalTitle = 'Editar Parte Interesada';
        $this->btnName = 'Actualizar';
        $parteInteresada = ParteInteresada::select('id', 'pi_nombre', 'pi_tipo', 'pi_nivel_influencia', 'pi_nivel_interes', 'pi_descripcion')
            ->where('id', $id)
            ->findOrFail($id);

        $this->parteInteresadaId = $parteInteresada->id;
        $this->pi_nombre = $parteInteresada->pi_nombre;
        $this->pi_tipo = $parteInteresada->pi_tipo;
        $this->pi_nivel_influencia = $parteInteresada->pi_nivel_influencia;
        $this->pi_nivel_interes = $parteInteresada->pi_nivel_interes;
        $this->pi_descripcion = $parteInteresada->pi_descripcion;
        $this->mensaje_cuadrante = $parteInteresada->mensaje;
        $this->actionRoute = route('partes.update', $id); // Ruta para actualizar
        $this->method = 'PUT';
    }

    public function render()
    {
        return view('livewire.interesado.parte-interesada-form');
    }
}
