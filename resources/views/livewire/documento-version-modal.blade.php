<div wire:ignore.self class="modal fade" id="versionesModal" tabindex="-1" role="dialog" aria-hidden="true"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h6 class="modal-title">{{ $modalTitle }}</h6>
                <button type="button" class="close" aria-label="Close" id="closeSearchModal">
                    <span aria-hidden="true" style="color: white">&times;</span>
                </button>
            </div>
            <form wire:submit.prevent="update" enctype="multipart/form-data">
                <div class="modal-body">

                    @csrf
                    @if ($method === 'PUT')
                        @method('PUT')
                    @endif
                    <div class="form-group small">
                        <label>Version: {{ str_pad($version, 2, '0', STR_PAD_LEFT) }}</label>

                    </div>
                    <div class="form-group small">
                        <label for="archivo">Archivo (opcional)</label>
                        <div class="input-group">
                            <input type="file" name="archivo" id="archivo" wire:model.live="archivo"
                                class="form-control">
                            @if ($archivo)
                                <button type="button" class="btn btn-sm btn-danger" wire:click="$set('archivo', null)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            @endif
                        </div>

                        {{-- Mostrar spinner de carga mientras sube --}}
                        <div wire:loading wire:target="archivo">Subiendo archivo...</div>
                    </div>

                    <div class="form-group small">
                        <label for="archivo_path">Enlace (puede ser URL externa o ruta del archivo subido)</label>
                        <input type="text" id="archivo_path" name="archivo_path" wire:model.live="archivo_path"
                            class="form-control" placeholder="Ingresa enlace o deja vacío si subes archivo"
                            @if ($archivo || ($archivo_path && Str::startsWith($archivo_path, asset('storage')))) disabled @endif>
                        @error('enlace')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group small">
                        <label>Fecha Aprobación</label>
                        <input type="date" id="fecha_aprobacion" name= "fecha_aprobacion"
                            wire:model.live="fecha_aprobacion" class="form-control" required>
                        @error('fecha_aprobacion')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group small">
                        <label>Fecha Publicación</label>
                        <input type="date" name= "fecha_publicacion" wire:model.live="fecha_publicacion"
                            class="form-control" required readonly>
                        @error('fecha_publicacion')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group small">
                        <label for="control_cambios">Control de cambios</label>
                        <textarea rows="3" id="control_cambios" name="control_cambios" wire:model.live="control_cambios"
                            class="form-control" required>
                         @error('control_cambios')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                    </textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm" id="btnGuardar">{{ $btnName }}</button>
                    <button type="button" class="btn btn-secondary btn-sm" id="btncloseModal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    @push('scripts')
        <script>
            $('#closeSearchModal').on('click', function() {
                $('#versionesModal').modal('hide');
            });
            $('#btncloseModal').on('click', function() {
                $('#versionesModal').modal('hide');
            });
           
        </script>
    @endpush
