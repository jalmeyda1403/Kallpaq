<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div>
                    <!-- Encabezado -->
                    <div class="header-container">
                        <h6 class="mb-0 d-flex align-items-center">
                            <span class="text-dark">{{ formatBreadcrumbId(store.riesgoForm.id) }}</span>
                            <span class="mx-2 text-secondary"><i class="fas fa-chevron-right fa-xs"></i></span>
                            <span class="text-dark">Evaluación y Tratamiento</span>
                        </h6>
                    </div>
                    <div class="text-left mb-4">
                        <h6 class="mb-1" style="font-weight: bold;">EVALUACIÓN Y TRATAMIENTO</h6>
                        <p class="mb-3 text-muted" style="font-size: 0.875rem;">
                            Defina la probabilidad, impacto, controles y estrategia de tratamiento para el riesgo.
                        </p>
                    </div>

                    <form @submit.prevent="saveRiesgo">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Descripción del Riesgo</label>
                                        <textarea class="form-control" rows="8" readonly
                                            :value="store.riesgoForm.riesgo_nombre"
                                            style="background-color: #e9ecef; resize: none;"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Consecuencia</label>
                                        <textarea class="form-control" rows="8" readonly
                                            :value="store.riesgoForm.riesgo_consecuencia"
                                            style="background-color: #e9ecef; resize: none;"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="riesgo_probabilidad">Probabilidad (1-10) <i
                                                class="fas fa-info-circle text-info cursor-pointer"
                                                @click="showProbabilidadInfo = true"></i></label>
                                        <select class="form-control" id="riesgo_probabilidad"
                                            v-model="store.riesgoForm.riesgo_probabilidad"
                                            :class="{ 'is-invalid': store.errors.riesgo_probabilidad }">
                                            <option value="4">4 - Bajo</option>
                                            <option value="6">6 - Medio</option>
                                            <option value="8">8 - Alto</option>
                                            <option value="10">10 - Muy Alto</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="store.errors.riesgo_probabilidad">
                                            {{ store.errors.riesgo_probabilidad[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="riesgo_impacto">Impacto (1-10) <i
                                                class="fas fa-info-circle text-info cursor-pointer"
                                                @click="showImpactoInfo = true"></i></label>
                                        <select class="form-control" id="riesgo_impacto"
                                            v-model="store.riesgoForm.riesgo_impacto"
                                            :class="{ 'is-invalid': store.errors.riesgo_impacto }">
                                            <option value="4">4 - Bajo</option>
                                            <option value="6">6 - Medio</option>
                                            <option value="8">8 - Alto</option>
                                            <option value="10">10 - Muy Alto</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="store.errors.riesgo_impacto">
                                            {{ store.errors.riesgo_impacto[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="riesgo_nivel">Nivel <span v-if="riesgoValor">({{ riesgoValor }})
                                            </span>
                                            <i class="fas fa-info-circle text-info cursor-pointer"
                                                @click="showImpactoInfo = true"></i></label>
                                        <input type="text" class="form-control text-center font-weight-bold"
                                            id="riesgo_nivel" v-model="store.riesgoForm.riesgo_nivel" readonly
                                            :class="store.riesgoForm.riesgo_nivel_badge || getBadgeClassForNivel(store.riesgoForm.riesgo_nivel)"
                                            placeholder="Calculando...">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <ControlSelector v-model="store.riesgoForm.controles_ids"
                                            label="Controles Actuales" />
                                        <div class="invalid-feedback d-block" v-if="store.errors.controles_ids">
                                            {{ store.errors.controles_ids[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="riesgo_tratamiento">Estrategia de Tratamiento</label>
                                        <select class="form-control" id="riesgo_tratamiento"
                                            v-model="store.riesgoForm.riesgo_tratamiento"
                                            :class="{ 'is-invalid': store.errors.riesgo_tratamiento }"
                                            :disabled="isTratamientoDisabled">
                                            <option value="">Seleccione estrategia</option>
                                            <option v-for="option in treatmentOptions" :key="option.value"
                                                :value="option.value">
                                                {{ option.label }}
                                            </option>
                                        </select>
                                        <div class="invalid-feedback" v-if="store.errors.riesgo_tratamiento">
                                            {{ store.errors.riesgo_tratamiento[0] }}
                                        </div>
                                        <small v-if="isTratamientoDisabled" class="form-text text-muted">
                                            Para riesgos bajos, la estrategia se establece automáticamente como
                                            "Aceptar".
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer justify-content-center w-100">
                                <button type="submit" class="btn btn-danger" :disabled="store.loading">
                                    <span v-if="store.loading" class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                    {{ store.isEditing ? 'Actualizar' : 'Grabar' }}
                                </button>
                                <button type="button" class="btn btn-secondary ml-2" @click="store.closeModal">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Modals -->
            <Dialog v-model:visible="showProbabilidadInfo" header="Criterios para estimar la Probabilidad" :modal="true"
                :style="{ width: '600px' }" class="riesgo-modal">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="bg-info">
                            <tr>
                                <th class="text-center">Índice</th>
                                <th class="text-center">Nivel</th>
                                <th>Probabilidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center font-weight-bold">10</td>
                                <td class="text-center"><span class="badge badge-danger-custom">MUY ALTO</span></td>
                                <td>Es seguro que el riesgo se materialice de manera inmediata.</td>
                            </tr>
                            <tr>
                                <td class="text-center font-weight-bold">8</td>
                                <td class="text-center"><span class="badge badge-high">ALTO</span></td>
                                <td>Existe la posibilidad que el riesgo se materialice en el corto plazo.</td>
                            </tr>
                            <tr>
                                <td class="text-center font-weight-bold">6</td>
                                <td class="text-center"><span class="badge badge-medium">MEDIO</span></td>
                                <td>Existe la posibilidad que el riesgo se materialice en el mediano plazo.</td>
                            </tr>
                            <tr>
                                <td class="text-center font-weight-bold">4</td>
                                <td class="text-center"><span class="badge badge-low">BAJO</span></td>
                                <td>Hay una posibilidad leve que el riesgo pueda materializarse en el largo plazo.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </Dialog>

            <Dialog v-model:visible="showImpactoInfo" header="Criterios para determinar el nivel de Impacto"
                :modal="true" :style="{ width: '90vw', maxWidth: '1200px' }" class="riesgo-modal">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="bg-info text-center text-white">
                            <tr>
                                <th rowspan="2" class="align-middle" style="width: 50px;">Índice</th>
                                <th rowspan="2" class="align-middle" style="width: 100px;">Nivel</th>
                                <th rowspan="2" class="align-middle">Estratégico</th>
                                <th rowspan="2" class="align-middle">Reputacional</th>
                                <th colspan="2" class="text-center">Corrupción</th>
                                <th rowspan="2" class="align-middle">Compliance</th>
                                <th rowspan="2" class="align-middle">Operativo</th>
                            </tr>
                            <tr>
                                <th>Valoración</th>
                                <th>Efecto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center font-weight-bold">10</td>
                                <td class="text-center"><span class="badge badge-danger-custom">MUY ALTO</span></td>
                                <td>Impedimento para el logro de algún objetivo estratégico</td>
                                <td>Continua exposición negativa regional o nacional en los medios de comunicación.</td>
                                <td>La materialización del riesgo podría suponer de dos a más efectos, incluyendo el
                                    quinto
                                    efecto</td>
                                <td>5. Posible interrupción de la actual gestión de la entidad.</td>
                                <td>Muy alta responsabilidad legal para la institución, sus funcionarios o frente a
                                    terceros.
                                    Muy grave incumplimiento de las obligaciones.</td>
                                <td>Impedimento para el logro de algún objetivo operacional.</td>
                            </tr>
                            <tr>
                                <td class="text-center font-weight-bold">8</td>
                                <td class="text-center"><span class="badge badge-high">ALTO</span></td>
                                <td>Afecta considerablemente para el logro de algún objetivo estratégico.</td>
                                <td>Continua exposición negativa local en los medios de comunicación.</td>
                                <td>La materialización del riesgo podría suponer de tres a cuatro efectos</td>
                                <td>
                                    1. Afectación de derechos de los públicos de interés.<br>
                                    2. Afectación de servicios.<br>
                                    3. Pérdida o desvío de recursos y bienes de la entidad.
                                </td>
                                <td>Alta responsabilidad legal para la institución, sus funcionarios o frente a
                                    terceros.
                                    Grave incumplimiento de las obligaciones.</td>
                                <td>Afecta considerablemente el logro de algún objetivo operacional.</td>
                            </tr>
                            <tr>
                                <td class="text-center font-weight-bold">6</td>
                                <td class="text-center"><span class="badge badge-medium">MEDIO</span></td>
                                <td>Afecta moderadamente el logro de algún objetivo estratégico</td>
                                <td>Limitada exposición negativa local en los medios de comunicación.</td>
                                <td>La materialización del riesgo podría suponer dos de los cuatro efectos</td>
                                <td>4. Afectación del patrimonio o recursos de los usuarios.</td>
                                <td>Alguna responsabilidad legal para la institución, sus funcionarios o frente a
                                    terceros.
                                    Moderado incumplimiento de las obligaciones.</td>
                                <td>Afecta moderadamente el logro de algún objetivo operacional.</td>
                            </tr>
                            <tr>
                                <td class="text-center font-weight-bold">4</td>
                                <td class="text-center"><span class="badge badge-low">BAJO</span></td>
                                <td>Afecta levemente el cumplimiento de algún objetivo estratégico.</td>
                                <td>Sin cobertura por los medios de comunicación.</td>
                                <td>La materialización del riesgo podría suponer uno de los cuatro efectos</td>
                                <td>-</td>
                                <td>Mínima o nula responsabilidad legal para la institución, sus funcionarios o frente a
                                    terceros. Leve incumplimiento de las obligaciones.</td>
                                <td>Afecta levemente el cumplimiento de algún objetivo operacional.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </Dialog>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { useRiesgoStore } from '@/stores/riesgoStore';
import Dialog from 'primevue/dialog';
import ControlSelector from '../controles/ControlSelector.vue';

const store = useRiesgoStore();

const formatBreadcrumbId = (id) => {
    if (!id) return 'Nuevo Riesgo';
    return `R${id.toString().padStart(6, '0')}`;
};

const showProbabilidadInfo = ref(false);
const showImpactoInfo = ref(false);

const saveRiesgo = async () => {
    try {
        await store.saveRiesgo();
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: 'Evaluación guardada correctamente',
            timer: 1500,
            showConfirmButton: false
        });
    } catch (error) {
        console.error(error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al guardar la evaluación.',
        });
    }
};

// Calculate risk level based on probability x impact
const calculateRiskLevel = (probabilidad, impacto) => {
    if (!probabilidad || !impacto) return { nivel: '', badgeClass: '' };

    const valor = parseInt(probabilidad) * parseInt(impacto);

    if (valor === 80 || valor === 100) {
        return { nivel: 'Muy Alto', badgeClass: 'badge-danger-custom' };
    } else if (valor === 60 || valor === 64 || valor === 48) {
        return { nivel: 'Alto', badgeClass: 'badge-high' };
    } else if (valor === 32 || valor === 36 || valor === 40) {
        return { nivel: 'Medio', badgeClass: 'badge-medium' };
    } else if (valor === 16 || valor === 24) {
        return { nivel: 'Bajo', badgeClass: 'badge-low' };
    } else {
        return { nivel: '', badgeClass: '' }; // Unknown value
    }
};

// Helper function to get badge class based on nivel text
const getBadgeClassForNivel = (nivel) => {
    if (nivel === 'Muy Alto') return 'badge-danger-custom';
    if (nivel === 'Alto') return 'badge-high';
    if (nivel === 'Medio') return 'badge-medium';
    if (nivel === 'Bajo') return 'badge-low';
    return 'badge-secondary';
};

// Function to update tratamiento based on business rules
const updateTratamientoBasedOnRules = () => {
    const tipo = store.riesgoForm.riesgo_tipo;
    const nivel = store.riesgoForm.riesgo_nivel;

    // Apply rules based on tipo and nivel
    if (tipo === 'Riesgo') {
        if (nivel === 'Bajo') {
            // For low risk, automatically set treatment to "aceptar" and lock
            store.riesgoForm.riesgo_tratamiento = 'aceptar';
        } else {
            // For other risk levels, ensure treatment is valid
            if (!['reducir', 'compartir'].includes(store.riesgoForm.riesgo_tratamiento)) {
                store.riesgoForm.riesgo_tratamiento = null;
            }
        }
    } else if (tipo === 'Oportunidad') {
        // For opportunities, ensure treatment is valid
        if (!['compartir', 'aprovechar'].includes(store.riesgoForm.riesgo_tratamiento)) {
            store.riesgoForm.riesgo_tratamiento = null;
        }
    }
};

// Watch for changes in probability and impact to update the risk level
watch([() => store.riesgoForm.riesgo_probabilidad, () => store.riesgoForm.riesgo_impacto],
    ([newProb, newImpact]) => {
        if (newProb && newImpact) {
            const { nivel, badgeClass } = calculateRiskLevel(newProb, newImpact);
            // Store the calculated level and badge class in the store
            store.riesgoForm.riesgo_nivel = nivel;
            store.riesgoForm.riesgo_nivel_badge = badgeClass || 'badge-secondary';

            // Apply business rules for tratamiento based on tipo and nivel
            updateTratamientoBasedOnRules();
        } else {
            // If either value is empty, reset the calculated values
            store.riesgoForm.riesgo_nivel = '';
            store.riesgoForm.riesgo_nivel_badge = 'badge-secondary';
        }
    },
    { immediate: true }
);

// Watch for changes in risk type to update tratamiento options
watch(() => store.riesgoForm.riesgo_tipo, () => {
    updateTratamientoBasedOnRules();
});

// Computed property to determine treatment options based on rules
const treatmentOptions = computed(() => {
    const tipo = store.riesgoForm.riesgo_tipo;
    const nivel = store.riesgoForm.riesgo_nivel;

    if (tipo === 'Riesgo') {
        if (nivel === 'Bajo') {
            // For low risk, only "aceptar" is available and it's locked
            return [{ value: 'aceptar', label: 'Aceptar' }];
        } else {
            // For other risk levels, show "reducir" and "compartir"
            return [
                { value: 'reducir', label: 'Reducir' },
                { value: 'compartir', label: 'Compartir' }
            ];
        }
    } else if (tipo === 'Oportunidad') {
        // For opportunities, show "compartir" and "aprovechar"
        return [
            { value: 'compartir', label: 'Compartir' },
            { value: 'aprovechar', label: 'Aprovechar' }
        ];
    }

    // Default options
    return [
        { value: 'reducir', label: 'Reducir' },
        { value: 'aceptar', label: 'Aceptar' },
        { value: 'compartir', label: 'Compartir' },
        { value: 'aprovechar', label: 'Aprovechar' }
    ];
});

// Computed property to determine if tratamiento should be disabled
const isTratamientoDisabled = computed(() => {
    const tipo = store.riesgoForm.riesgo_tipo;
    const nivel = store.riesgoForm.riesgo_nivel;

    // Disable treatment selection only when it's a low risk (which must be accepted)
    return tipo === 'Riesgo' && nivel === 'Bajo';
});

const riesgoValor = computed(() => {
    if (!store.riesgoForm.riesgo_probabilidad || !store.riesgoForm.riesgo_impacto) return '';
    return parseInt(store.riesgoForm.riesgo_probabilidad) * parseInt(store.riesgoForm.riesgo_impacto);
});
</script>

<style scoped>
.form-group label {
    font-weight: bold;
    font-size: 0.9rem;
}

.cursor-pointer {
    cursor: pointer;
}

.text-xs {
    font-size: 0.75rem;
}

.badge-orange {
    background-color: #fd7e14;
    color: white;
}

/* Improved badge styling */
.badge {
    font-size: 0.75em;
    padding: 0.25em 0.5em;
    font-weight: 600;
    border-radius: 0.25rem;
    display: inline-block;
    text-align: center;
    vertical-align: middle;
    min-width: 60px;
}

.badge-danger-custom {
    background-color: #dc3545 !important;
    color: white !important;
    border: 1px solid #dc3545;
}

.badge-high {
    background-color: #fd7e14 !important;
    color: white !important;
    border: 1px solid #fd7e14;
}

.badge-medium {
    background-color: #ffc107 !important;
    color: #212529 !important;
    border: 1px solid #ffc107;
}

.badge-low {
    background-color: #28a745 !important;
    color: white !important;
    border: 1px solid #28a745;
}

.badge-secondary {
    background-color: #6c757d !important;
    color: white !important;
    border: 1px solid #6c757d;
}

/* Modal styling */
.riesgo-modal .p-dialog-header {
    background-color: #dc3545;
    color: white;
    border-top-left-radius: calc(0.3rem - 1px);
    border-top-right-radius: calc(0.3rem - 1px);
}

.riesgo-modal .p-dialog-header .p-dialog-title {
    font-weight: 600;
}

.riesgo-modal .p-dialog-header .p-dialog-header-icon {
    color: white;
    background-color: transparent;
    border: 0;
    margin-left: 0.5rem;
}

.riesgo-modal .p-dialog-content {
    padding: 1.5rem;
}

.riesgo-modal .p-dialog-footer {
    border-top: 1px solid #e9ecef;
    padding: 1rem 1.5rem;
    background-color: #f8f9fa;
    border-bottom-left-radius: calc(0.3rem - 1px);
    border-bottom-right-radius: calc(0.3rem - 1px);
}

/* Table styling */
.table {
    margin-bottom: 0;
    font-size: 0.8rem;
}

.table th {
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
    vertical-align: middle !important;
    padding: 0.5rem;
}

.table td {
    vertical-align: middle !important;
    padding: 0.5rem;
    line-height: 1.3;
    font-size: 0.7rem;
    font-weight: 400;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.table-bordered th,
.table-bordered td {
    border: 1px solid #dee2e6;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}


/* Header styling */
thead tr th {
    border-top: 0;
    border-bottom-width: 2px;
    text-align: center;
}

/* Badge styling in table cells */
.table td .badge {
    min-width: 60px;
    padding: 0.25em 0.4em;
    font-size: 0.75em;
}

/* Responsive improvements */
@media (min-width: 992px) {
    .table-responsive {
        overflow-x: visible;
    }
}

/* Specific table adjustments */
.table thead th {
    vertical-align: middle;
    border-bottom: 2px solid #dee2e6;
}
</style>
<style scoped>
.header-container {
    padding: 0.75rem;
    margin-bottom: 1.5rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #dc3545;
    display: flex;
    align-items: center;
}
</style>
