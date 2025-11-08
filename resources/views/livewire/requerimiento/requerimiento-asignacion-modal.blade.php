<div wire:ignore.self class="modal fade" id="AsignarRequerimientoModal" tabindex="-1" role="dialog" aria-hidden="true"
    data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Asignar Especialista</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div wire:loading class="loading-spinner">
                    <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
                <div wire:loading.remove>
                    <h7> <strong>Instrucciones: </strong></h7>

                    <p class="small">
                        Asigne este requerimiento a un especialista.
                    </p>
    
    
                    @if ($mensaje)
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>{{ $mensaje }}</span>
                        </div>
                    @else
                        <div class="form-group">
                      
                            <select wire:model="especialista_id" class="form-control" id="especialista_id">
                                <option value="">Seleccione Especialista</option>
                                @foreach ($especialistas as $id => $nombre)
                                    <option value="{{ $id }}">{{ $nombre }}</option>
                                @endforeach
                            </select>
                            @error('especialista_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        @if ($especialistaSeleccionado)
                            <div class="mt-2">
                                 <label>Actualmente asignado a:</label>
                                <p class="small mb-0">
                                    {{ $especialistaSeleccionado->name }}
                                </p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <div class="modal-footer">
                <div wire:loading wire:target="mostrarAsignacion">
                    <span class="text-muted">Cargando opciones...</span>
                </div>
                <div wire:loading.remove wire:target="mostrarAsignacion">
                    @if ($mensaje)
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    @else
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" wire:click="guardarSeleccion">
                            {{ $modoReasignacion ? 'Reasignar' : 'Asignar' }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
