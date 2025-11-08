<div wire:ignore.self class="modal fade" id="ModalEvidencias" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Evidencias del Requerimiento</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <div class="modal-body">
                @if (session()->has('message'))
                    <div class="alert alert-success small">{{ session('message') }}</div>
                @endif

                <div class="form-group">
                    <label><strong>Subir Evidencias</strong></label>
                    <input type="file" id="input-evidencias" multiple wire:model.defer="archivos"
                        class="form-control-file" key="{{ $randomId }}">
                    <button type="button" wire:click="subirArchivos" class="btn btn-primary mt-2">
                        <i class="fas fa-upload"></i> Subir Evidencias
                    </button>
                </div>

                <hr>

                <h6 class="text-muted">Archivos existentes:</h6>
                <ul class="list-group">
                    @forelse($archivosExistentes as $archivo)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ $archivo['url'] }}" target="_blank" class="text-muted">
                                 {{ $archivo['nombre'] }}
                            </a>
                            <button class="btn btn-danger btn-sm"
                                wire:click="eliminarArchivo('{{ $archivo['nombre'] }}')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">No hay archivos registrados.</li>
                    @endforelse
                </ul>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>
@push('scripts')
    <script>
        Livewire.on('archivos-subidos', () => {
            const input = document.getElementById('input-evidencias');
            if (input) {
                input.value = ''; // 🔁 Limpia el input visualmente
            }
        });
    </script>
@endpush
