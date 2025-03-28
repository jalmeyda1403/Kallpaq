@push('styles')
    <style>
        /* Estilos específicos para el modal */
        .card-footer small {
            font-size: 0.875rem;
            color: #888;
        }

        .form-control {
            border-radius: 5px;

            width: 100%;
        }

        textarea::placeholder {
            color: #aaa;
        }
    </style>
@endpush

<div class="modal fade" id="procesoModal" tabindex="-1" role="dialog" aria-labelledby="procesoModalLabel"
    aria-hidden="false" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 15px">

            <div class="modal-header bg-olive color-palette"
                style="border-top-left-radius: 15px; border-top-right-radius: 15px;">

                <h5 class="modal-title" id="procesoModalLabel">{{ $modalTitle ?? 'Crear Proceso' }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="save">
                    <div class="form-group">
                        <label for="cod_proceso">
                            <h6>Código de Proceso (*)</h6>
                        </label>
                        <input type="text" wire:model="cod_proceso" id="cod_proceso" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>
                            <h6>Nombre del Proceso (*)</h6>
                        </label>

                        <textarea wire:model="nombre" id="nombre" class="form-control" rows="3" maxlength="400"
                            placeholder="Escribe el nombre del proceso en no más de 400 caracteres." required></textarea>
                        <small id="charCountNombre" class="form-text text-muted">{{ $charCountNombre }}/400</small>

                    </div>


                    <div class="form-group">
                        <label for="objetivo">
                            <h6>Objetivo (*)</h6>
                        </label>
                        <div>
                            <textarea wire:model="objetivo" id="objetivo" class="form-control" rows="3" maxlength="1000"
                                placeholder="Describe el objetivo del proceso en no más de 1000 caracteres." required></textarea>
                            <small id="charCountObjetivo"
                                class="form-text text-muted">{{ $charCountObjetivo }}/1000</small>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_proceso">
                                    <h6>Tipo (*)</h6>
                                </label>
                                <select wire:model="tipo_proceso" id="tipo_proceso" class="form-control" required>
                                    <option value="Misional">Misional</option>
                                    <option value="Estratégico">Estratégico</option>
                                    <option value="Apoyo">Apoyo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nivel_proceso">
                                    <h6>Nivel (*)</h6>
                                </label>
                                <select wire:model ="nivel_proceso" id="nivel_proceso" class="form-control" required>
                                    @for ($i = 0; $i <= 3; $i++)
                                        <option value="{{ $i }}">
                                            @if ($i == 0)
                                                Macroproceso
                                            @else
                                                Proceso nivel {{ $i }}
                                            @endif
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>
                            <h6>Proceso</h6>
                        </label>
                        <!-- Campo oculto para el ID del proceso -->
                        <input type="hidden" name="cod_proceso_padre" id="cod_proceso_padre" required disabled>
                        <div class="input-group">
                            <!-- Campo de texto para el nombre del proceso -->
                            <input type="text" class="form-control" id="proceso_nombre" name="proceso_nombre"
                                disabled readonly>
                            <!-- Botón para abrir el modal -->
                            <a href="#" class="btn btn-primary" data-toggle="modal"
                                data-target="#procesopadreModal" {{ $isMacroproceso ? 'disabled' : '' }}>
                                <i class="fas fa-search"></i>
                            </a>
                        </div>
                    </div>
                    <x-modal-busqueda :ruta="route('procesos.buscar')" campo-id="cod_proceso_padre" campo-nombre="proceso_nombre"
                        modal-titulo="Proceso" modal-id="procesopadreModal">
                    </x-modal-busqueda>

                    <div class="form-group">
                        <label for="estado">
                            <h6>Estado</h6>
                        </label>
                        <select wire:model="estado" id="estado" class="form-control" required>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="text-muted">
                        <small>(*) Es obligatorio completar los campos.</small>
                    </div>

                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

@push('scripts')
    <script>
      document.addEventListener('livewire:load', function () {
        Livewire.on('closeModal', () => {
            $('#procesoModal').modal('hide');
        });

        Livewire.on('openModal', () => {
            $('#procesoModal').modal('show');
        });

        $('#procesoModal').on('shown.bs.modal', function () {
            // Sincronizar datos cuando el modal se abre
            Livewire.emit('syncData');
        });
    });

    
   
    </script>
@endpush
