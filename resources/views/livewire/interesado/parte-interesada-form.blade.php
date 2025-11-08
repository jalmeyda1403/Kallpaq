<div wire:ignore.self class="modal fade" id="parteInteresadaModal" tabindex="-1" role="dialog"
    aria-labelledby="parteInteresadaModalLabel" aria-hidden="false" data-backdrop="static" data-keyboard="false">

    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="parteInteresadaModalLabel">{{ $modalTitle }}</h5>
                {{-- Usar data-bs-dismiss para Bootstrap 5 --}}
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                {{-- Indicador de carga (spinner) --}}
                <div wire:loading class="loading-spinner">
                    <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
                <div>
                    <form method="POST" action="{{ $actionRoute }}" id="formPartes">
                        @csrf {{-- ¡Crucial para formularios tradicionales! --}}
                        {{-- Simular el método PUT/PATCH con un campo oculto --}}
                        @if ($method === 'PUT')
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-6 form-group small">
                                <label for="pi_nombre" class="required-field">Nombre</label>
                                <div class="d-flex align-items-center"> {{-- Ajuste para alinear contador --}}
                                    <input type="text" wire:model.defer="pi_nombre" name="pi_nombre" id="pi_nombre"
                                        class="form-control form-control-sm @error('pi_nombre') is-invalid @enderror"
                                        placeholder="Ej: Ministerio de Economía y Finanzas" maxlength="255" required>

                                </div>
                                @error('pi_nombre')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 form-group small">
                                <label for="pi_tipo" class="required-field">Tipo</label>
                                <select wire:model.defer="pi_tipo" name="pi_tipo" id="pi_tipo"
                                    class="form-control form-control-sm @error('pi_tipo') is-invalid @enderror"
                                    required>
                                    <option value="">-- Seleccione el tipo --</option>
                                    <option value="interna">Interna</option>
                                    <option value="externa">Externa</option>
                                    <option value="cliente">Cliente</option>
                                    <option value="proveedor">Proveedor</option>
                                    <option value="regulador">Regulador</option>
                                </select>
                                @error('pi_tipo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 form-group small">
                                <label for="pi_descripcion" class="required-field">Descripción</label>
                                <div class="d-flex align-items-center"> {{-- Ajuste para alinear contador --}}
                                    <textarea wire:model.defer="pi_descripcion" name="pi_descripcion" id="pi_descripcion"
                                        class="form-control form-control-sm @error('pi_descripcion') is-invalid @enderror" rows="4"
                                        placeholder="Detalles sobre la parte interesada, su rol o relación..." maxlength="1000" required></textarea>

                                </div>
                                @error('pi_descripcion')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row justify-content-center">


                            <div class="col-md-6 form-group small">
                                <label for="pi_nivel_interes" class="required-field">Nivel de Interes</label>
                                <select wire:model.defer="pi_nivel_interes" name="pi_nivel_interes"
                                    id="pi_nivel_interes"
                                    class="form-control form-control-sm @error('pi_nivel_interes') is-invalid @enderror"
                                    required>
                                    <option value="">-- Seleccione el nivel --</option>
                                    <option value="bajo">Bajo</option>
                                    <option value="medio">Medio</option>
                                    <option value="alto">Alto</option>
                                </select>
                                @error('pi_nivel_interes')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group small">
                                <label for="pi_nivel_influencia" class="required-field">Nivel de Influencia</label>
                                <select wire:model.defer="pi_nivel_influencia" name="pi_nivel_influencia"
                                    id="pi_nivel_influencia"
                                    class="form-control form-control-sm @error('pi_nivel_influencia') is-invalid @enderror"
                                    required>
                                    <option value="">-- Seleccione el nivel --</option>
                                    <option value="bajo">Bajo</option>
                                    <option value="medio">Medio</option>
                                    <option value="alto">Alto</option>
                                </select>
                                @error('pi_nivel_influencia')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="text-muted mt-3">
                            <small>(*) Es obligatorio completar los campos.</small>
                        </div>
                        <div class="text-muted mt-3" id="mensaje_cuadrante">
                            <small>{{ $mensaje_cuadrante }}</small>
                        </div>
                        <div class="modal-footer justify-content-center w-100 mt-4">
                            <button type="submit" class="btn bg-dark btn-sm">{{ $btnName }}</button>
                            <button type="button" class="btn btn-secondary btn-sm"
                                data-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    {{-- Script para controlar el modal de Bootstrap con Livewire --}}
    <script>
        $(document).ready(function() {
            $('#parteInteresadaModal').on('hidden.bs.modal', function() {
                const form = $(this).find('form')[0];
                if (form) {
                    form.reset();
                    $('#mensaje_cuadrante').html(''); // limpia todo el contenido del div
                }
                // Quitar el foco de cualquier elemento dentro del modal
                const triggerButton = document.getElementById('addParteInteresadaButton');
                if (triggerButton) {
                    triggerButton.focus();
                }
            });
            $('#parteInteresadaModal').on('hide.bs.modal', function() {
                // Quitar el foco de cualquier elemento dentro del modal
                const focusedElement = this.querySelector(':focus');
                if (focusedElement) {
                    focusedElement.blur();
                }
            });
        });
        
    </script>
@endpush
