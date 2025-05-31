<div wire:ignore.self class="modal fade" id="salidaModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document" >
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h6 class="modal-title">{{ $modalTitle }}</h6>
                <button type="button" class="close" id="closeSearchModal">
                    <span style="color: white">&times;</span>
                </button>
            </div>
            <div class="modal-body d-flex" style="gap: 20px; flex-direction: row;">
                <!-- Panel principal -->
                <div style="flex: 1; overflow-y: auto;">
                    <form wire:submit.prevent="guardarSalida">
                        <div class="modal-body">
                            @csrf
                            <div class="form-group small">
                                <label>Proceso</label>
                                <input type="hidden" id="salida_proceso_id" name="salida_proceso_id" wire:model="salida_proceso_id">
                                <div class="input-group">
                                    <input type="text" class="form-control" wire:model.live="salida_proceso_nombre" id="salida_proceso_nombre" name="salida_proceso_nombre" readonly>
                                    <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#procesosearchModal">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <x-modal-busqueda :ruta="route('procesos.buscar')" campo-id="salida_proceso_id" campo-nombre="salida_proceso_nombre" modal-titulo="Proceso" modal-id="procesosearchModal" :modalBgcolor="'#001f3f'" :modalTxtcolor="'#FFFFFF'">
                            </x-modal-busqueda>

                            <div class="form-group small">
                                <label>Salida</label>
                                <input type="text" class="form-control" wire:model="salida" required>
                                @error('salida')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group small">
                                <label>Tipo</label>
                                <select class="form-control" wire:model="tipo" required>
                                    <option value="" selected disabled>Seleccione un tipo</option>
                                    <option value="producto">Producto</option>
                                    <option value="servicio">Servicio</option>
                                </select>
                                @error('tipo')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary btn-sm">{{ $btnName }}</button>
                            <button type="button" class="btn btn-secondary btn-sm" id="btncloseModal">Cancelar</button>
                        </div>
                    </form>

                    @if ($salida_id)
                        <hr>
                        <h6>Requisitos</h6>
                        <table class="table table-bordered table-hover table-versiones table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>id</th>
                                    <th style="width:50%">Descripción</th>
                                    <th style="width:25%">Documento</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requisitos as $requisito)
                                    <tr>
                                        <td>{{ $requisito->id }}</td>
                                        <td>{{ $requisito->requisito }}</td>
                                        <td>{{ $requisito->documento }}</td>
                                        <td>{{ $requisito->fecha_requisito }}</td>
                                        <td>
                                            <a href="#" class="px-1 btnEditar" wire:click.prevent="editarRequisito({{ $requisito->id }})">
                                                <i class="fas fa-pencil-alt text-dark"></i>
                                            </a>
                                            <a href="#" class="px-2 btnEliminar" wire:click.prevent="removeRequisito({{ $requisito->id }})">
                                                <i class="fas fa-trash-alt text-danger"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="addRequisito">+ Agregar Requisito</button>
                    @endif
                </div>
                @if ($mostrarPanelRequisito)
                    <div style="width: 450px; border-left: 1px solid #ddd; padding: 20px; overflow-y: auto;">
                        <div class="card">
                            <div class="card-header bg-dark">
                                <h6>Requisito: {{ $requisito_id ?? 'Nuevo requisito' }}</h6>
                            </div>
                            <div class="card-body">
                                <form wire:submit.prevent="guardarRequisito" enctype="multipart/form-data">
                                    <div class="form-group small">
                                        <label>Descripción</label>
                                        <textarea rows="5" class="form-control" wire:model.defer="requisito" required></textarea>
                                    </div>
                                    <div class="form-group small">
                                        <label>Documento</label>
                                        <textarea rows="5" class="form-control" wire:model.defer="documento"></textarea>
                                    </div>
                                    <div class="form-group small">
                                        <label>Fecha</label>
                                        <input type="date" class="form-control" wire:model.defer="fecha_requisito">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                                    <button type="button" class="btn btn-secondary btn-sm" wire:click="cancelarEdicion">Cerrar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $('#closeSearchModal').on('click', function() {
            $('#salidaModal').modal('hide');
        });

        $('#btncloseModal').on('click', function() {
            $('#salidaModal').modal('hide');
        });

        $('#btnGuardar').on('click', function() {
            $('#salidaModal').modal('hide');
        });
    </script>
@endpush
