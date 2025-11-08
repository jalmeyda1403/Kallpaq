@push('styles')
    <style>
       
        /* Estilo general de opciones */
        .select2-container--default .select2-results__option {
            font-size: 12px;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #6c757d !important;
            /* grey */
            color: white !important;
        }

        /* Estilo de etiquetas seleccionadas */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #dc3545 !important;
            /* danger */
            border-color: #dc3545 !important;
            color: white !important;
            font-size: 12px;
        }

        /* Estilo del botón de cerrar en etiquetas */
        .select2-container--default .select2-selection__choice__remove {
            color: white !important;
            font-size: 12px;
        }

        .select2-container--default .select2-selection__choice__remove:hover {
            color: #ffcccc !important;
        }

        /* Estilo del campo de búsqueda interno (placeholder y texto) */
        .select2-container--default .select2-selection--multiple .select2-search__field {
            font-size: 13px !important;
        }

        select:invalid {
            color: #94939a;
        }

        .select2-hidden-init {
            visibility: hidden;
        }
    </style>
@endpush
<div wire:ignore.self class="modal fade" id="RequerimientoModal" tabindex="-1" role="dialog"
    aria-labelledby="verModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger color-palette">
                <h5 class="modal-title">{{ $modalTitle }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group" wire:ignore>
                    <label>Proceso</label>
                    <select id="select_proceso" class="form-control form-control-sm select2">
                        <option value="">Seleccione...</option>
                        @foreach ($procesos as $proceso)
                            <option value="{{ $proceso->id }}" @if ($proceso_id == $proceso->id) selected @endif>
                                {{ $proceso->cod_proceso }} - {{ $proceso->proceso_nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Asunto</label>
                    <input type="text" wire:model.defer="asunto" class="form-control">
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea wire:model.defer="descripcion" class="form-control" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label>Justificación</label>
                    <textarea wire:model.defer="justificacion" class="form-control" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label>Borrador (opcional)</label>
                    <input type="file" wire:model="archivo" class="form-control-file">
                    @if ($ruta_archivo_requerimiento)
                        <a href="{{ Storage::url($ruta_archivo_requerimiento) }}" target="_blank"
                            class="btn btn-sm btn-success mt-2">
                            <i class="fas fa-download"></i> Ver archivo
                        </a>
                    @endif
                </div>

                <h6 class="mt-4 font-weight-bold">Documentos a intervenir</h6>
                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Tipo de documento</th>
                            <th>Nombre del documento</th>
                            <th class="text-center">Crear</th>
                            <th class="text-center">Actualizar</th>
                            <th class="text-center">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documentos_nuevos as $index => $doc)
                            <tr>
                                <td>
                                    <select wire:model.defer="documentos_nuevos.{{ $index }}.tipo"
                                        class="form-control">
                                        <option value="">Seleccione...</option>
                                        @foreach ($tipos_documento as $tipo)
                                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="text"
                                        wire:model.defer="documentos_nuevos.{{ $index }}.nombre"
                                        class="form-control"></td>
                                <td class="text-center"><input type="radio"
                                        wire:model.defer="documentos_nuevos.{{ $index }}.accion" value="C">
                                </td>
                                <td class="text-center"><input type="radio"
                                        wire:model.defer="documentos_nuevos.{{ $index }}.accion" value="A">
                                </td>
                                <td class="text-center"><input type="radio"
                                        wire:model.defer="documentos_nuevos.{{ $index }}.accion" value="E">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button wire:click="agregarDocumentoNuevo" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-plus"></i> Agregar documento
                </button>

                <h6 class="mt-4 font-weight-bold">Documentos existentes del proceso</h6>
                <table class="table table-sm table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Codigo</th>
                            <th>Tipo</th>
                            <th>Nombre</th>
                            <th class="text-center">Seleccionar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documentos_existentes as $doc)
                            <tr>
                                <td>{{ $doc->cod_documento }}</td>
                                <td>{{ $doc->tipo_documento->nombre }}</td>
                                <td>{{ $doc->nombre }}</td>
                                <td class="text-center">
                                    <input type="checkbox" wire:model.defer="documentos_relacionados"
                                        value="{{ $doc->id }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm" wire:click="guardar">Guardar y Enviar</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        // Inicializa Select2
        $('#select_proceso').select2({
            dropdownParent: $('#RequerimientoModal') // si estás en un modal
        });

        // Detecta cambios y actualiza Livewire
        $('#select_proceso').on('change', function(e) {
            let selectedValue = $(this).val();
            @this.set('proceso_id', selectedValue);
        });

        // En caso el modal se abra varias veces, reinicia Select2
        Livewire.on('reiniciarSelectProceso', () => {
            $('#select_proceso').val(@this.get('proceso_id')).trigger('change');
        });
    </script>
@endpush
