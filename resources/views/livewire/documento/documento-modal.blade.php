<div wire:ignore.self class="modal fade" id="documentoModal1" tabindex="-1" role="document"
    aria-labelledby="documentoModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h6 class="modal-title">{{ $modalTitle }}</h6>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div wire:loading class="loading-spinner">
                    <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
                {{-- tarjeta: formulario --}}
                <div class="card">
                    <form method="POST" action="{{ $actionRoute }}" enctype="multipart/form-data">
                        @csrf
                        @if ($method === 'PUT')
                            @method('PUT')
                        @endif
                        <div class="card-header bg-navy">
                            <!-- Título de la lista de procesos -->
                            <div class="col-md-6 text-md-left">
                                <h6>Datos del documento </h6>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fa fa-info-circle"></i>
                                    <strong>Información General</strong>
                                </h6>
                                <div class="text-end">
                                    <strong>ID:</strong> {{ $documento_id }}
                                </div>
                            </div>
                            <hr>
                            <p>
                            <div class="row">

                                <div class="form-group small col-md-6">
                                    <label for="proceso_id">Proceso </label>
                                    <input type="hidden" id="proceso_id" wire:model.live="proceso_id" name="proceso_id"
                                        value="{{ $proceso_id }}">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="proceso_nombre"
                                            wire:model.live="proceso_nombre" readonly>
                                        <div class="input-group-prepend">
                                            <button type="button" class="btn btn-dark" data-toggle="modal"
                                                onclick="Livewire.dispatchTo('busqueda-procesos-modal', 'cargarModal')"
                                                data-target="#busquedaModal">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>



                                <div class="form-group small col-md-3">
                                    <label for="tipo_documento_id">Tipo Documento</label>
                                    <select id="tipo_documento_id" wire:model.live="tipo_documento_id"
                                        name="tipo_documento_id" class="form-control">
                                        <option value="">Seleccione un tipo</option>
                                        @foreach ($tiposDocumento as $tipo)
                                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_documento_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group small col-md-3">
                                    <label for="fuente">Fuente</label>
                                    <select id="fuente" wire:model="fuente" name="fuente" class="form-control"
                                        required>
                                        <option value="">Seleccione</option>
                                        <option value="1">Interno</option>
                                        <option value="0">Externo</option>
                                    </select>
                                    @error('fuente')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>


                            </div>

                            <div class="row">
                                <div class="form-group small col-md-6">
                                    <label for="cod_documento">Código Documento</label>
                                    <div class="input-group">
                                        <input type="text" id="cod_documento" wire:model="cod_documento"
                                            name="cod_documento" class="form-control" maxlength="255" required>
                                        <div class="input-group-prepend">
                                            <button type="button" class="btn btn-dark" wire:click="generarCodigo">
                                                <i class="fas fa-magic"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @error('cod_documento')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>




                                <div class="form-group small col-md-3">
                                    <label for="usa_versiones">¿Usa versiones?</label>
                                    <select wire:model.live="usa_versiones" class="form-control" name="usa_versiones">
                                        <option value="0">No</option>
                                        <option value="1">Si</option>
                                    </select>
                                </div>



                                <div class="form-group small col-md-3">
                                    <label for="estado">Estado</label>
                                    <select wire:model="estado" class="form-control" id="estado" name="estado">
                                        @foreach ($estadoOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="form-group small">
                                    <label for="nombre">Nombre</label>
                                    <textarea rows="2" id="nombre" wire:model="nombre" name="nombre" maxlength="250" class="form-control"></textarea>
                                    @error('nombre')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="form-group small">
                                    <label for="resumen">Resumen</label>
                                    <textarea rows="4" id="resumen" wire:model="resumen" name="resumen" maxlength="600" class="form-control"></textarea>
                                    @error('resumen')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group small col-md-6">
                                    <label for="instrumento_aprueba">Instrumento que aprueba</label>
                                    <input type="text" wire:model="instrumento_aprueba" name="instrumento_aprueba"
                                        class="form-control">
                                </div>
                                <div class="form-group small col-md-6">
                                    <label for="fecha_aprobacion">Fecha de aprobación</label>
                                    <input type="date" wire:model="fecha_aprobacion" name="fecha_aprobacion"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="form-group small">
                                    <label for="documento_padre_id"> Documento Padre
                                        Asociado
                                        (opcional)</label>
                                    <label class="text-primary">{{ $documento_padre_nombre }}</label>
                                    <select id="documento_padre_id" wire:model="documento_padre_id"
                                        name="documento_padre_id" class="form-control">
                                        <option value="">Ninguno</option>
                                        @foreach ($documentosDisponibles as $doc)
                                            <option value="{{ $doc->id }}">{{ $doc->cod_documento }} -
                                                {{ Str::limit($doc->nombre, 50) }}</option>
                                        @endforeach
                                    </select>
                                    @error('documento_padre_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <h8> <i class="fa fa-paperclip"></i><strong> Archivo y enlace</strong>
                            </h8>
                            <hr>
                            <div class="row">
                                <div class="form-group small  col-md-6">
                                    <label for="archivo">Archivo (opcional)</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="archivo"
                                                name="archivo" wire:model.live="archivo"
                                                {{ $usa_versiones ? 'disabled' : '' }}>
                                            <label class="custom-file-label"
                                                for="archivo">{{ $archivo?->getClientOriginalName() ?? 'Seleccionar archivo...' }}</label>
                                        </div>
                                        @if ($archivo)
                                            <button type="button" class="btn btn-sm btn-danger"
                                                wire:click="$set('archivo', null)">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Mostrar spinner de carga mientras sube --}}
                                    <div wire:loading wire:target="archivo">Subiendo archivo...</div>
                                </div>
                                <div class="form-group small  col-md-6">
                                    <label for="archivo_path">Enlace (puede ser URL externa o ruta del
                                        archivo
                                        subido)</label>

                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                        </div>
                                        <input type="text" id="archivo_path" name="archivo_path"
                                            wire:model.live="archivo_path" class="form-control"
                                            {{ $usa_versiones || $archivo ? 'disabled' : '' }} required>

                                        @error('enlace')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                </div>
                            </div>

                            <div class="row">

                                <div class="form-group small  col-md-6">
                                    <label>Fecha Vigencia</label>
                                    <input type="date" id="fecha_vigencia" name= "fecha_vigencia"
                                        wire:model.defer="fecha_vigencia" class="form-control"
                                        {{ $usa_versiones ? 'disabled' : '' }} required>

                                    @error('fecha_vigencia')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror


                                </div>

                                <div class="form-group small col-md-6">
                                    <label for="frecuencia_revision">Frecuencia de
                                        Actualización</label>


                                    <select wire:model="frecuencia_revision" class="form-control"
                                        name="frecuencia_revision">
                                        <option value="0.5">Semestral</option>
                                        <option value="1">Anual</option>
                                        <option value="2">Bianual</option>
                                    </select>

                                </div>


                            </div>
                            <h8> <i class="fa fa-th-list"></i><strong> Metadatos</strong>
                            </h8>
                            <hr>
                            <div class="row">

                                <div class=" form-group small col-md-6 mb-1">
                                    <label for="area_compliance_id">Area Temática</label>
                                    <select wire:model.live="area_compliance_id" name="area_compliance_id"
                                        class="form-control" required>
                                        <option value="">Seleccione Area...</option>
                                        @foreach ($areaCompliance as $ac)
                                            <option value="{{ $ac->id }}">{{ $ac->area_compliance_nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group small  col-md-6">
                                    <label for="palabras_clave">Palabras clave (separadas por
                                        coma)</label>
                                    <div class="input-group">

                                        <input type="text" name="palabras_clave" class="form-control"
                                            wire:model="palabras_clave"
                                            placeholder="ej: planeamiento, pei, estrategia" required>
                                    </div>

                                </div>
                                <div class=" form-group small col-md-6 mb-1">
                                    <label for="subarea_compliance_id">Subcategoria Temática</label>
                                    <select wire:model.live="subarea_compliance_id" name="subarea_compliance_id"
                                        class="form-control" required>
                                        <option value="">Seleccione SubCategoria...</option>
                                        @foreach ($subareaCompliance as $sac)
                                            <option value="{{ $sac->id }}">{{ $sac->subarea_compliance_nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group small col-md-6 mb-1">
                                    <div wire:ignore>
                                        <label for="tags-select">Etiquetas (Tags)</label>
                                        <select name="tags[]" id="tags-select" class="form-control me-2" multiple>
                                            @foreach ($tagsDisponibles as $tag)
                                                <<option value="{{ $tag->nombre }}"
                                                    {{ in_array($tag->nombre, $tagsSeleccionados ?? []) ? 'selected' : '' }}>
                                                    {{ $tag->nombre }}
                                                    </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card-footer text-md-right">
                            <button type="submit" class="btn bg-primary btn-sm">
                                {{ $btnName }}</button>
                            @if ($documento_id)
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                    data-target="#modalDerogar">
                                    <i class="fas fa-ban"></i> Derogar
                                </button>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- tarjeta: relacionados e impactados --}}
                <div class="card">
                    <div class="card-header bg-navy text-white py-2">
                        <h6 class="mb-0"><i class="fas fa-project-diagram me-1"></i> Documentos Relacionados</h6>
                    </div>

                    <div class="card-body">
                        <div class="row">

                            {{-- Impacta A --}}
                            <div class="form-group col-md-6">
                                <label class="form-label fw-bold">Impacta a:</label>

                                <div class="input-group mb-2">
                                    <select wire:model="documentoImpactadoId" class="form-control form-control-sm">
                                        <option value="">Seleccione</option>
                                        @foreach ($documentosDisponibles as $doc)
                                            <option value="{{ $doc->id }}">{{ $doc->cod_documento }} -
                                                {{ $doc->nombre }}</option>
                                        @endforeach
                                    </select>
                                    {{-- Tipo de impacto --}}
                                    <select wire:model="tipoImpactoRelacionado" class="form-control  form-control-sm">
                                        <option value="">Tipo</option>
                                        <option value="impacta">Impacta</option>
                                        <option value="deroga">Deroga</option>
                                        <option value="modifica">Modifica</option>
                                    </select>

                                    <button class="btn btn-sm btn-primary" wire:click="agregarDocumentoImpactado">
                                        <i class="fas fa-plus-circle"></i> Agregar
                                    </button>
                                </div>

                                {{-- Lista de documentos que este impacta --}}
                                <ul class="list-group">
                                    @foreach ($documentosImpactados as $imp)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                              
                                                {{ $imp['cod_documento'] ?? '' }} - {{ $imp['nombre'] ?? '' }} 
                                                  <strong>[{{ ucfirst($imp['tipo_relacion']) }}]</strong>
                                            </span>
                                            <a href="#" class="text-danger"
                                                wire:click.prevent="eliminarDocumentoImpactado({{ $imp['id'] }})">
                                                <i class="fas fa-times-circle fa-lg"></i>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Impactado por --}}
                            <div class="form-group col-md-6">
                                <label class="form-label fw-bold">Impactado por:</label>

                                <div class="input-group mb-2">
                                    <select wire:model="documentoRelacionadoId" class="form-control form-control-sm">
                                        <option value="">Seleccione</option>
                                        @foreach ($documentosDisponibles as $doc)
                                            <option value="{{ $doc->id }}">{{ $doc->cod_documento }} -
                                                {{ $doc->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-primary" wire:click="agregarDocumentoRelacionado">
                                        <i class="fas fa-plus-circle"></i> Agregar
                                    </button>
                                </div>

                                {{-- Lista de documentos que impactan a este --}}
                                <ul class="list-group">
                                    @foreach ($documentosRelacionados as $rel)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $rel['cod_documento'] ?? '' }} -
                                                {{ $rel['nombre'] ?? '' }}</span>

                                            <a href="#" class="text-danger"
                                                wire:click="eliminarDocumentoRelacionado({{ $rel['id'] }})">
                                                <i class="fas fa-times-circle fa-lg"></i>
                                            </a>

                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
                {{-- tarjeta: versiones --}}
                @if ($usa_versiones)
                    <div class="card">
                        <div class="card-header bg-navy small">
                            <!-- Título de la lista de procesos -->
                            <div class="col-md-6 text-md-left">
                                <h6>Versiones</h6>
                            </div>

                        </div>

                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <a href="#" class="px-1 btn btn-primary btn-sm" data-toggle="modal"
                                    onclick="Livewire.dispatchTo('documento.documento-version-modal', 'nuevaVersion', { id: {{ $documento_id }} })"
                                    data-target="#versionesModal">
                                    <i class="fas fa-plus-circle"></i> Agregar Versión
                                </a>
                            </div>
                            <table class="table table-bordered table-hover table-versiones table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Versión</th>
                                        <th>Fecha Aprobación</th>
                                        <th>Fecha Vigencia</th>
                                        <th>Cambios</th>
                                        <th>Archivo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($versiones as $ver)
                                        <tr>
                                            <td class="d-none">{{ $ver->id }}</td>
                                            <td>{{ $ver->version }}</td>
                                            <td>{{ $ver->fecha_aprobacion }}</td>
                                            <td>{{ $ver->fecha_vigencia }}</td>
                                            <td>{{ $ver->control_cambios }}</td>
                                            <td>
                                                <a href="#" class="px-1 btnVerDocumento" data-toggle="modal"
                                                    onclick="Livewire.dispatchTo('pdf-modal','openPdfModal', { path: '{{ optional($ver)->archivo_path ?? '' }}' })"
                                                    data-target="#pdfModal">
                                                    <i class="fas fa-file-pdf fa-lg text-danger"></i>
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center">
                                                    <a href="#" class="px-1 btnEditarVersion"
                                                        data-toggle="modal"
                                                        onclick="Livewire.dispatchTo('documento.documento-version-modal', 'mostrarVersion', { id: {{ $ver->id }} })"
                                                        data-target="#versionesModal">
                                                        <i class="fas fa-pencil-alt text-dark"></i>
                                                    </a>
                                                    <a href="#" class="px-3 btnEliminarVersion"
                                                        wire:click="eliminarVersion({{ $ver->id }})">
                                                        <i class="fas fa-trash-alt text-danger"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if (count($versiones) == 0)
                                        <tr>
                                            <td colspan="6" class="text-center">No hay versiones aún
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @livewire('documento.documento-version-modal')
    @livewire('busqueda-procesos-modal', ['eventoRetorno' => 'procesoSeleccionado'])
    {{-- Modal Derogar --}}
    <div wire:ignore.self class="modal fade" id="modalDerogar" tabindex="-1" role="dialog"
        aria-labelledby="modalDerogarLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title">Derogar Documento</h5>
                    <button type="button" class="close" aria-label="Cerrar" id="ModalDerogarClose">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>

                <form wire:submit.prevent="derogarDocumento">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="fecha_derogacion">Fecha de Derogación</label>
                            <input type="date" wire:model="fecha_derogacion" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="instrumento_deroga">Instrumento que Deroga</label>
                            <input type="text" wire:model="instrumento_deroga" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="observacion_deroga">Observación</label>
                            <textarea wire:model="observacion_deroga" rows="2" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm"
                            data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger btn-sm">Confirmar Derogación</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .table-versiones {
            font-size: 12px;
        }

        .list-group-item {
            font-size: 12px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        Livewire.on('procesoSeleccionado', ({
            id,
            nombre
        }) => {
            @this.set('proceso_id', id);
            @this.set('proceso_nombre', nombre);
        });

        Livewire.on('versionActualizada', () => {
            $('#versionesModal').modal('hide');
            @this.call('obtenerVersiones');
        });

        Livewire.on('limpiar-versiones', () => {
            $('.table-versiones tbody').empty();
        });

        Livewire.on('cerrar-modal-derogar', () => {
            $('#modalDerogar').modal('hide');
        });

        $(document).on('hidden.bs.modal', function() {
            // Verificamos si queda otro modal abierto
            if ($('.modal.show').length > 0) {
                $('body').addClass('modal-open');
            }
        });

        $('#ModalDerogarClose').on('click', function() {
            $('#modalDerogar').modal('hide');
        });

        Livewire.on('refreshSelect2', (data) => {
            const $tagSelect = $('#tags-select');


            // Re-inicializar select2
            $tagSelect.select2({
                placeholder: 'Seleccione o escriba etiquetas...',
                tags: true,
                width: '100%'
            });

            // Asignar los valores desde el atributo data-selected-tags
            const tagObjects = data[0]?.tags ?? [];
            const selectedTag = tagObjects.map(tag => tag.nombre);
            if (Array.isArray(selectedTag)) {
                $tagSelect.val(selectedTag).trigger('change');
            }
        });
    </script>
@endpush
