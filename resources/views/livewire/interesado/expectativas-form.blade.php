    <!-- Modal -->
    @push('styles')
        <style>
            .modal-full {
                width: 100vw;
                max-width: none;
                height: 90%;
                margin: 0;
            }

            .modal-full .modal-content {
                border: none;
                overflow-y: auto;
            }
        </style>
    @endpush
    <div wire:ignore.self class="modal fade  modal-full" id="expectativasModal" tabindex="-1" role="dialog"
        aria-labelledby="procesoModalLabel" data-backdrop="static" data-keyboard="false">

        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title"> Necesidades o Expectativas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>

                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-md-left">
                            <h3 class="card-title">Listado Necesidades o Expectativas: <span
                                    id="parteInteresadaNombre">{{ $parte_interesada_nombre }}</span>
                            </h3>
                        </div>
                        <div class="col-md-6 text-md-right">
                            {{-- Botón Modal --Agregar --}}

                            <a href="#" class="btn btn-primary btn-sm" data-toggle="modal"
                                id="addParteInteresadaButton" wire:click="resetCampos" data-target="#expectativasForm">
                                <i class="fas fa-plus-circle"></i> Agregar
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div wire:loading class="loading-spinner">
                        <div class="spinner-border text-success" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                    <table class="table table-bordered table-hover table-sm table-procesos" id="expectativasTable">
                        <thead class="thead-light">
                            <tr>
                                <th class="border px-3 py-2 text-left">Tipo</th>
                                <th class="border px-3 py-2 text-left">Descripción</th>
                                <th class="border px-3 py-2 text-left">Proceso</th>
                                <th class="border px-3 py-2 text-left">SIG</th>
                                <th class="border px-3 py-2 text-left">Prioridad</th>
                                <th class="border px-3 py-2 text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($expectativas as $exp)
                                <tr>
                                    <td class="border px-3 py-2">{{ ucfirst($exp->exp_tipo) }}</td>
                                    <td class="border px-3 py-2">{{ $exp->exp_descripcion }}</td>
                                    <td class="border px-3 py-2">{{ optional($exp->proceso)->proceso_nombre }}
                                    </td>

                                    <td class="border px-3 py-2">
                                        @foreach ($exp->sig as $sig)
                                            <span class="small">{{ $sig }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-{{ \Semaforo::getSemaforoColor($exp->exp_nivel_prioridad) }}">
                                            {{ $exp->exp_nivel_prioridad }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center">

                                            <a href="#" class="px-1" data-toggle="modal"
                                                wire:click="editar({{ $exp->id }})"
                                                data-target="#expectativasForm">
                                                <i class="fas fa-pencil-alt text-dark"></i>
                                            </a>

                                            <a href="#" class="px-3" wire:click="eliminar({{ $exp->id }})">
                                                <i class="fas fa-trash-alt text-danger"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        <!-- Modal FormExpectativas -->
        <div wire:ignore.self class="modal fade" id="expectativasForm" tabindex="-1" role="dialog"
            aria-labelledby="expectativasFormLabel" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    {{-- Encabezado --}}
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Registrar Expectativa / Necesidad</h5>
                        <button type="button" class="close text-white" id="btnCerrarexpectativasForm"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    {{-- Formulario --}}
                    <form wire:submit.prevent="guardar" id="formExpectativas">
                        <div class="modal-body">
                            <div wire:loading class="loading-spinner">
                                <div class="spinner-border text-success" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                            </div>
                            {{-- Primera fila --}}
                            <div wire:loading.class="blurred" class="contenido-formulario">
                                <div class="form-row">
                                    <div class="form-group small col-md-6">
                                        <label class="required-field">Parte Interesada</label>
                                        <select wire:model="parte_interesada_id" class="form-control">
                                            <option value="">Seleccione Parte Interesada</option>
                                            @foreach ($partes as $parte)
                                                <option value="{{ $parte->id }}">{{ $parte->pi_nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group small col-md-6">
                                        <label class="required-field">Tipo</label>
                                        <select wire:model="exp_tipo" class="form-control">
                                            <option value="">Seleccione Tipo</option>
                                            <option value="expectativa">Expectativa</option>
                                            <option value="necesidad">Necesidad</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Segunda fila --}}
                                <div class="form-row">
                                    <div class="form-group small col-md-6">
                                        <label>Proceso</label>
                                        <select wire:model="proceso_id" class="form-control">
                                            <option value="">Seleccione Proceso</option>
                                            @foreach ($procesos as $proceso)
                                                <option value="{{ $proceso->id }}">
                                                    {{ $proceso->cod_proceso }} - {{ $proceso->proceso_nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div wire:ignore class="form-group small col-md-6">
                                        <label>Sistemas de Gestión</label>
                                        <select wire:model="sig" multiple class="form-control select2"
                                            id="select-sig" data-placeholder="Seleccione uno o varios sistemas">
                                            @foreach ($sistemas as $s)
                                                <option value="{{ $s }}">{{ $s }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Descripción --}}
                                <div class="form-group small">
                                    <label class="required-field">Descripción de la Necesidad</label>
                                    <textarea wire:model="exp_descripcion" class="form-control" rows="4"
                                        placeholder="Registre la descripción de la necesidad (máx. 400 caracteres)" required></textarea>
                                </div>

                                {{-- Observaciones --}}
                                <div class="form-group small">
                                    <label>Observaciones</label>
                                    <textarea wire:model="exp_observaciones" class="form-control" rows="3"
                                        placeholder="Registre la observación (máx. 400 caracteres)"></textarea>
                                </div>

                                {{-- Evaluación --}}
                                <div class="form-row mt-3">
                                    <div class="form-group small col-md-6">
                                        <label>Criticidad <span class="text-danger">*</span>
                                            <i class="fas fa-question-circle text-primary" tabindex="0"
                                                role="button" data-toggle="popover" data-trigger="focus"
                                                data-html="true" title="¿Qué es criticidad?"
                                                data-content="
                                                            Es el nivel de urgencia y severidad con que se debe implementar la expectativa o requerimiento.<br><br>
                                                            <b>Muy Alta:</b> Afecta objetivos estratégicos o puede generar sanción<br>
                                                            <b>Alta:</b> Impacta procesos críticos o genera disconformidad fuerte<br>
                                                            <b>Media:</b> Afecta tareas importantes pero no críticas<br>
                                                            <b>Baja:</b> No tiene un impacto significativo
                                                        ">
                                            </i>
                                        </label>
                                        <select wire:model="exp_criticidad" class="form-control">
                                            <option value="">Seleccione</option>
                                            <option value="1">Baja</option>
                                            <option value="2">Media</option>
                                            <option value="3">Alta</option>
                                            <option value="4">Muy Alta</option>
                                        </select>
                                    </div>

                                    <div class="form-group small col-md-6">
                                        <label>Viabilidad <span class="text-danger">*</span>
                                            <i class="fas fa-question-circle text-primary" tabindex="0"
                                                role="button" data-toggle="popover" data-trigger="focus"
                                                data-html="true" title="¿Qué es viabilidad?"
                                                data-content="
           Es la posibilidad real de implementar esta expectativa según recursos, tecnología, normas y contexto:
           <ul style='padding-left: 1rem; margin: 0;'>
               <li><strong>Muy Alta:</strong> Fácil de atender, recursos disponibles</li>
               <li><strong>Alta:</strong> Se puede atender con esfuerzo moderado</li>
               <li><strong>Media:</strong> Requiere coordinación o recursos adicionales</li>
               <li><strong>Baja:</strong> Actualmente no es viable atender</li>
           </ul>">
                                            </i>
                                        </label>

                                        <select wire:model="exp_viabilidad" class="form-control">
                                            <option value="">Seleccione</option>
                                            <option value="1">Baja</option>
                                            <option value="2">Media</option>
                                            <option value="3">Alta</option>
                                            <option value="4">Muy Alta</option>
                                        </select>

                                    </div>
                                </div>
                                <div class="text-muted mt-3">
                                    <small>(*) Es obligatorio completar los campos.</small>
                                </div>
                            </div>
                        </div>

                        {{-- Footer FUERA del modal-body --}}
                        <div class="modal-footer justify-content-center">
                            <button type="submit" class="btn btn-dark btn-sm">
                                Guardar
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" id="btnCancelarexpectativasForm">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            let cerrarFormulario = false;
            document.addEventListener('DOMContentLoaded', function() {


                // Función para inicializar Select2
                function initializeSelect2() {
                    $('#select-sig').select2({
                        placeholder: $('#select-sig').data('placeholder'),
                        width: 'resolve',
                        theme: 'bootstrap4',
                        allowClear: true,
                        dropdownParent: $('#select-sig').closest('.modal') // Importante para modales
                    });
                }

                // Inicializar cuando el modal se abre
                $(document).on('shown.bs.modal', '.modal', function() {
                    setTimeout(function() {
                        initializeSelect2();
                    }, 100);
                });

                // Escuchar eventos de Livewire
                Livewire.on('refreshSelect2', () => {
                    setTimeout(function() {
                        // Destruir Select2 existente si existe
                        if ($('#select-sig').hasClass('select2-hidden-accessible')) {
                            $('#select-sig').select2('destroy');
                        }
                        initializeSelect2();
                    }, 100);
                });

                // Reinicializar después de actualizaciones de Livewire
                Livewire.hook('message.processed', (message, component) => {
                    setTimeout(function() {
                        if ($('#select-sig').length && !$('#select-sig').hasClass(
                                'select2-hidden-accessible')) {
                            initializeSelect2();
                        }
                    }, 100);
                });

                // Manejar cambios del Select2 hacia Livewire
                $(document).on('change', '#select-sig', function() {
                    let selectedValues = $(this).val();
                    @this.set('sig', selectedValues);
                });
            });

            $('#btnCancelarexpectativasForm, #btnCerrarexpectativasForm').on('click', function() {
                cerrarFormulario = true;
                $('#expectativasForm').modal('hide');
                resetCampos();
            });

            function resetCampos() {
                $('#formExpectativas').trigger('reset');
            }

            $(document).ready(function() {
                $('[data-toggle="popover"]').popover();
            });

            $('#expectativasModal').on('hide.bs.modal', function() {
                // Quitar el foco de cualquier elemento dentro del modal
                const focusedElement = this.querySelector(':focus');
                if (focusedElement) {
                    focusedElement.blur();
                }
            });

            $('#expectativasModal').on('hidden.bs.modal', function() {
                if (!cerrarFormulario) {
                    const tbody = document.querySelector('#expectativasTable tbody');
                    tbody.innerHTML = '';
                    document.getElementById('parteInteresadaNombre').textContent = '';
                }
                cerrarFormulario = false; // siempre se resetea
            });
        </script>
    @endpush
