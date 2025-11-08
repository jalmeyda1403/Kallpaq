<?php

namespace App\Livewire\Requerimiento;
use Livewire\Component;
use App\Models\RequerimientoAvance;
use App\Models\Requerimiento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequerimientoAvanceModal extends Component
{
    public $requerimiento_id;

    public $estadoAvance = []; // ['contexto' => true, ...]
    public $comentarios = []; // ['contexto' => '...', ...]
    public $avance_registrado = 0;
    public $etapas = [
        'contexto' => ['titulo' => 'Análisis de contexto y objetivos', 'descripcion' => 'Define el marco contextual, necesidades y normativa.', 'peso' => 15],
        'levantamiento' => ['titulo' => 'Definición de actividades y flujo', 'descripcion' => 'Se recopila información y define actividades y flujo del proceso', 'peso' => 20],
        'caracterizacion' => ['titulo' => 'Descripción detallada del documento', 'descripcion' => 'Describe al detalle las actividades del proceso y su relación con otros procedimientos', 'peso' => 25],
        'formatos' => ['titulo' => 'Diseño de formatos y anexos', 'descripcion' => 'Elaboración de formatos, anexos e instrumentos', 'peso' => 15],
        'revision_interna' => ['titulo' => 'Revisión interna', 'descripcion' => 'Revisión con área solicitante o equipo de trabajo', 'peso' => 10],
        'revision_tecnica' => ['titulo' => 'Revisión técnica', 'descripcion' => 'Revisión técnica de pares por Modernización', 'peso' => 5],
        'firma' => ['titulo' => 'Firma o aprobación', 'descripcion' => 'Aprobación del documento', 'peso' => 5],
        'publicacion' => ['titulo' => 'Publicación', 'descripcion' => 'Publicación del documento', 'peso' => 5],
    ];
    protected $listeners = ['mostrarAvance' => 'mostrarAvance'];
    public function mostrarAvance($id)
    {
        $this->requerimiento_id = $id;
        $avance = RequerimientoAvance::where('requerimiento_id', $id)->first();
        if ($avance) {
            foreach ($this->etapas as $key => $info) {
                $this->estadoAvance[$key] = (bool) $avance->$key;
                $this->comentarios[$key] = $avance->{'comentario_' . $key} ?? '';
            }
        }

        $this->avance_registrado = $avance->avance_registrado ?? 0;
    }

    public function updatedEstadoAvance()
    {
        $this->avance_registrado = $this->calcularAvance();
    }


    public function calcularAvance()
    {
        $avance = 0;

        foreach ($this->etapas as $key => $data) {
            if (!empty($this->estadoAvance[$key])) {
                $avance += $data['peso'];
            }
        }

        return $avance;
    }

    public function guardarAvance()
    {
        $estado = "";
        $requerimiento = Requerimiento::findOrFail($this->requerimiento_id);

        $avance = RequerimientoAvance::firstOrNew([
            'requerimiento_id' => $this->requerimiento_id,
        ]);

        foreach ($this->etapas as $key => $info) {
            $avance->$key = $this->estadoAvance[$key] ?? false;
            $avance->{'comentario_' . $key} = $this->comentarios[$key] ?? null;
        }
        try {
            DB::beginTransaction();
            $avance->avance_registrado = $this->calcularAvance();
            $avance->save();
            $estado = ($avance->avance_registrado == 100) ? 'finalizado' : 'en proceso';
            $datos = ['estado' => $estado];

            if ($estado === 'finalizado') {
                $datos['fecha_fin'] = now();
            }
            $requerimiento?->update($datos);
            $requerimiento?->movimientos()->create([
                'estado' => $estado,
                'user_id' => Auth::id(), // Asegúrate que esté autenticado
                'comentario' => 'Se ha registrado un avance de ' . $this->avance_registrado . '%',
            ]);


            $this->dispatch('AvanceGuardado');
            session()->flash('message', 'Avance actualizado correctamente.');
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack(); // ❌ Algo falló, se revierte todo
            Log::error('Error al asignar requerimiento: ' . $e->getMessage());

            // Opcional: muestra mensaje o lanza excepción
            $this->dispatch('error', ['message' => 'Error al guardar. Intente nuevamente.']);
        }
    }
    public function render()
    {
        return view('livewire.requerimiento.requerimiento-avance-modal');
    }
}
