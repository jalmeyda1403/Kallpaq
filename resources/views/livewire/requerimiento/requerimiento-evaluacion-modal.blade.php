<div wire:ignore.self class="modal fade" id="RequerimientoEvaluacionModal" tabindex="-1" role="dialog" aria-hidden="true"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title">Evaluación de Complejidad </h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <h7> <strong>Instrucciones: </strong></h7>

                <p class="small">
                    Selecciona una opción para cada criterio; el puntaje total y nivel de complejidad se calcularán
                    automáticamente.
                </p>


                {{-- Tabla de criterios --}}
                <table class="table table-bordered small mb-0">
                    <thead class="thead-light text-center">
                        <tr>
                            <th>Criterio</th>
                            <th>Baja (1)</th>
                            <th>Media (2)</th>
                            <th>Alta (3)</th>
                            <th>Muy Alta (4)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong>
                                    Cantidad de actividades del proceso
                                    <i class="fas fa-info-circle text-warning" data-toggle="tooltip"
                                        data-placement="top"
                                        title="Baja: ≤ 5 | Media: 6–10 | Alta: 11–20 | Muy Alta: > 20"></i>
                                </strong>
                                <div class="text-muted small mb-0">Evalúa cuántas actividades principales
                                    componen el
                                    requerimiento</div>
                            </td>
                            @for ($i = 1; $i <= 4; $i++)
                                <td class="text-center align-middle">
                                    <input type="radio" wire:model.live="actividades" value="{{ $i }}">
                                </td>
                            @endfor
                        </tr>

                        <tr>
                            <td>
                                <strong>
                                    Cantidad de unidades orgánicas involucradas
                                    <i class="fas fa-info-circle text-warning" data-toggle="tooltip"
                                        data-placement="top"
                                        title="Baja: 1 | Media: 2–3 | Alta: 4–5 | Muy Alta: > 5"></i>
                                </strong>
                                <div class="text-muted small mb-0">Evalúa cuántas unidades orgánicas participan
                                    en el
                                    requerimiento</div>
                            </td>
                            @for ($i = 1; $i <= 4; $i++)
                                <td class="text-center align-middle">
                                    <input type="radio" wire:model.live="areas" value="{{ $i }}">
                                </td>
                            @endfor
                        </tr>

                        <tr>
                            <td>
                                <strong>
                                    Requisitos normativos aplicables
                                    <i class="fas fa-info-circle text-warning" data-toggle="tooltip"
                                        data-placement="top"
                                        title="Baja: Ninguno o 1 | Media: 2–3 | Alta: 4–5 | Muy Alta: > 5 (estrictamente regulados)"></i>
                                </strong>
                                <div class="text-muted small mb-0">Mide la cantidad y rigurosidad de normas que
                                    afectan
                                    el proceso</div>
                            </td>
                            @for ($i = 1; $i <= 4; $i++)
                                <td class="text-center align-middle">
                                    <input type="radio" wire:model.live="requisitos" value="{{ $i }}">
                                </td>
                            @endfor
                        </tr>

                        <tr>
                            <td>
                                <strong>
                                    Nivel de documentación requerida
                                    <i class="fas fa-info-circle text-warning" data-toggle="tooltip"
                                        data-placement="top"
                                        title="Baja: Básico | Media: Moderado | Alta: Detallado (formatos, anexos) | Muy Alta: Exhaustivo (manuales, guías, reglamentos)"></i>
                                </strong>
                                <div class="text-muted small mb-0">Evalúa la profundidad y complejidad de los
                                    documentos
                                    a elaborar o modificar</div>
                            </td>
                            @for ($i = 1; $i <= 4; $i++)
                                <td class="text-center align-middle">
                                    <input type="radio" wire:model.live="documentacion" value="{{ $i }}">
                                </td>
                            @endfor
                        </tr>

                        <tr>
                            <td>
                                <strong>
                                    Impacto del procedimiento
                                    <i class="fas fa-info-circle text-warning" data-toggle="tooltip"
                                        data-placement="top"
                                        title="Baja: No impacta | Media: Impacta en el mismo proceso | Alta: Afecta a otros procesos | Muy Alta: Afecta a procesos misionales"></i>
                                </strong>
                                <div class="text-muted small mb-0">Evalúa el nivel de influencia del
                                    requerimiento
                                    respecto a otros procedimientos</div>
                            </td>
                            @for ($i = 1; $i <= 4; $i++)
                                <td class="text-center align-middle">
                                    <input type="radio" wire:model.live="impacto" value="{{ $i }}">
                                </td>
                            @endfor
                        </tr>
                    </tbody>
                </table>

                {{-- Resultado --}}
                <div class="mt-3">
                    {{-- Mensaje si ya existe evaluación --}}

                    <div class="alert alert-info small">
                        @if ($evaluacionExistente)
                            <strong>Este requerimiento ya fue evaluado</strong><br>
                        @endif
                        Nivel de Complejidad: <strong>{{ ucfirst($nivelComplejidad) }}</strong> |
                        Puntaje total: <strong>{{ $puntajeTotal }}</strong><br>

                    </div>


                </div>
            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>

                <button type="button" wire:click="guardarEvaluacion" class="btn btn-danger btn-sm"
                    {{ $puntajeTotal < 5 ? 'disabled' : '' }}>
                    Guardar Evaluación
                </button>

            </div>
        </div>
    </div>
</div>
