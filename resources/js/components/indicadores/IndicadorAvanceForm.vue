<template>
    <Teleport to="body">
        <div ref="modalRef" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title font-weight-bold">
                            Registrar Avance <br>
                            <small class="text-muted text-white">{{ indicador?.indicador_nombre }}</small>
                        </h5>
                        <button type="button" class="close text-white" @click="close" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="submitForm">
                        <div class="modal-body bg-light-soft px-4">
                            <!-- SECTION 1: Configuración de Periodo -->
                            <div class="section-header mb-3">
                                <span class="badge badge-danger-soft text-danger mr-2"><i
                                        class="fas fa-calendar-alt"></i></span>
                                <span
                                    class="text-uppercase font-weight-bold small text-muted letter-spacing-1">Configuración
                                    de Periodo</span>
                            </div>

                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-body py-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group mb-0">
                                                <label class="custom-label mb-1">Frecuencia de Medición</label>
                                                <div class="form-control bg-light text-capitalize text-muted font-weight-bold"
                                                    style="opacity: 0.8;">
                                                    <i class="fas fa-clock mr-2 small"></i> {{
                                                    indicador?.indicador_frecuencia || 'No def.' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-0">
                                                <label class="custom-label mb-1">Año Fiscal <span
                                                        class="text-danger">*</span></label>
                                                <select v-model="form.is_periodo" class="form-control" required>
                                                    <option v-for="year in years" :key="year" :value="year">{{ year }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-0">
                                                <label class="custom-label mb-1">Periodo Correspondiente</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-light border-right-0"><i
                                                                class="fas fa-hashtag text-muted small"></i></span>
                                                    </div>
                                                    <input type="text" v-model="form.is_numero_periodo"
                                                        class="form-control bg-light" readonly>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-info border-left-0" type="button"
                                                            @click="fetchNextPeriod" :disabled="isLoadingPeriod"
                                                            title="Siguiente periodo">
                                                            <i class="fas"
                                                                :class="isLoadingPeriod ? 'fa-spinner fa-spin' : 'fa-sync-alt'"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION 2: Centro de Cálculo -->
                            <div class="section-header mb-3" v-if="indicador?.indicador_formula">
                                <span class="badge badge-danger-soft text-danger mr-2"><i
                                        class="fas fa-calculator"></i></span>
                                <span class="text-uppercase font-weight-bold small text-muted letter-spacing-1">Centro
                                    de Procesamiento</span>
                            </div>

                            <div class="card border-0 shadow-sm mb-4" v-if="indicador?.indicador_formula">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <!-- Bloque Izquierdo: Lógica y Proyección -->
                                        <div class="col-md-5 border-right">
                                            <div class="mb-3">
                                                <label
                                                    class="custom-label-small text-muted text-uppercase d-block mb-2">Fórmula
                                                    Activa</label>
                                                <div class="bg-danger-soft p-2 rounded text-center border-danger-light">
                                                    <span class="text-monospace font-weight-bold text-danger h5 mb-0">{{
                                                        indicador.indicador_formula }}</span>
                                                </div>
                                            </div>

                                            <div class="evaluated-box p-3 text-center rounded bg-light border">
                                                <small class="text-muted text-uppercase d-block mb-2 font-weight-bold"
                                                    style="font-size: 0.65rem;">Proyección del Cálculo (Live)</small>
                                                <div v-html="evaluatedFormula"
                                                    class="h5 mb-0 text-dark font-weight-bold"></div>
                                            </div>
                                        </div>

                                        <!-- Bloque Derecho: Entrada de Variables -->
                                        <div class="col-md-7">
                                            <label
                                                class="custom-label-small text-muted text-uppercase d-block mb-3 ml-2">Construcción
                                                de Variables</label>
                                            <div class="row no-gutters mx-n2">
                                                <div v-for="(v, index) in activeVariables" :key="v.n" :class="[
                                                    'px-2 mb-3',
                                                    (activeVariables.length % 2 !== 0 && index === activeVariables.length - 1) ? 'col-md-12' : 'col-md-6'
                                                ]">
                                                    <div class="variable-field p-2 rounded border-light-2 shadow-xs has-hover-info"
                                                        :class="{ 'mx-auto': activeVariables.length % 2 !== 0 && index === activeVariables.length - 1 }"
                                                        :style="activeVariables.length % 2 !== 0 && index === activeVariables.length - 1 ? 'max-width: 50%;' : ''">
                                                        <label
                                                            class="custom-label-small text-muted text-uppercase d-flex justify-content-between mb-1">
                                                            <span><span class="text-danger font-weight-bold">V{{ v.n
                                                            }}</span></span>
                                                            <i class="fas fa-info-circle text-info"></i>
                                                        </label>
                                                        <div
                                                            class="variable-info-overlay small text-info font-weight-bold mb-1">
                                                            {{ v.name }}
                                                        </div>
                                                        <input type="number" v-model="form['is_var' + v.n]"
                                                            class="form-control form-control-sm border-0 bg-transparent text-right font-weight-bold px-0 h-auto"
                                                            style="font-size: 1.1rem;" placeholder="0.00">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Barra de Resultados (Footer) -->
                                <div
                                    class="card-footer bg-danger text-white border-top-0 d-flex align-items-center py-3 rounded-bottom">
                                    <div class="flex-grow-1 pr-3">
                                        <label class="custom-label-white mb-0 text-uppercase d-block"
                                            style="font-size: 0.65rem; opacity: 0.8;">Meta del
                                            Periodo</label>
                                        <input type="text" v-model="form.is_meta"
                                            class="form-control-plaintext text-white font-weight-bold py-0"
                                            style="font-size: 1.2rem;" :readonly="isMetaReadonly">
                                    </div>

                                    <div class="text-center px-4 border-left border-white-50">
                                        <label class="custom-label-white mb-0 text-uppercase d-block"
                                            style="font-size: 0.65rem; opacity: 0.8;">Valor
                                            Obtenido</label>
                                        <div class="h4 mb-0 font-weight-bold">
                                            {{ form.is_valor || '0.00' }} <small>%</small>
                                        </div>
                                    </div>

                                    <div class="pl-4">
                                        <button
                                            class="btn btn-light btn-sm font-weight-bold text-danger ripple px-4 shadow-sm"
                                            type="button" @click="calculateValue">
                                            <i class="fas fa-bolt mr-1"></i> CALCULAR
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION 3: Evidencias y Notas -->
                            <div class="section-header mb-3">
                                <span class="badge badge-danger-soft text-danger mr-2"><i
                                        class="fas fa-paperclip"></i></span>
                                <span class="text-uppercase font-weight-bold small text-muted letter-spacing-1">Soportes
                                    y Justificación</span>
                            </div>

                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-7 border-right">
                                            <div class="form-group mb-0">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <label class="custom-label mb-0">Justificación / Comentario</label>
                                                    <small class="text-muted font-weight-bold"
                                                        :class="{ 'text-danger': (form.is_comentario?.length >= 500) }">
                                                        {{ form.is_comentario?.length || 0 }}/500
                                                    </small>
                                                </div>
                                                <textarea v-model="form.is_comentario"
                                                    class="form-control bg-light-soft border-0" rows="5" maxlength="500"
                                                    placeholder="Redacte una justificación sobre el resultado obtenido..."></textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-5">
                                            <div class="form-group mb-0">
                                                <label class="custom-label">Evidencias del Avance</label>
                                                <div class="drop-zone ripple-light py-4"
                                                    @dragenter.prevent="onDragEnter" @dragleave.prevent="onDragLeave"
                                                    @dragover.prevent @drop.prevent="onDrop"
                                                    :class="{ 'drag-over': isDragging }" @click="openFileDialog">
                                                    <input type="file" ref="fileInput" class="d-none"
                                                        @change="handleFileSelect" multiple>
                                                    <div class="text-center">
                                                        <i
                                                            class="fas fa-cloud-upload-alt fa-2x text-danger opacity-5 mb-2"></i>
                                                        <p class="mb-0 small font-weight-bold text-muted">Arrastra
                                                            archivos
                                                            aquí</p>
                                                        <small class="text-muted" style="font-size: 0.6rem;">Máximo
                                                            10MB</small>
                                                    </div>
                                                </div>

                                                <!-- Lista de archivos -->
                                                <div v-if="filesToUpload.length > 0 || existingFiles.length > 0"
                                                    class="mt-2" style="max-height: 120px; overflow-y: auto;">
                                                    <div
                                                        class="list-group list-group-flush border rounded overflow-hidden">
                                                        <!-- Combinados -->
                                                        <div v-for="file in filesToUpload" :key="file.id"
                                                            class="list-group-item bg-light-soft d-flex align-items-center py-1">
                                                            <i class="fas fa-file-alt text-primary mr-2 small"></i>
                                                            <span
                                                                class="small font-weight-bold flex-grow-1 text-truncate">{{
                                                                    file.file.name }}</span>
                                                            <button @click.stop="removeFile(file.id)"
                                                                class="btn btn-link text-danger p-0 ml-1">
                                                                <i class="fas fa-times-circle small"></i>
                                                            </button>
                                                        </div>
                                                        <div v-for="(file, index) in existingFiles" :key="index"
                                                            class="list-group-item d-flex align-items-center py-1">
                                                            <i class="fas fa-paperclip text-muted mr-2 small"></i>
                                                            <a :href="`/storage/${file.path}`" target="_blank"
                                                                class="small text-dark flex-grow-1 text-truncate">
                                                                {{ file.name }}
                                                            </a>
                                                            <button type="button"
                                                                @click.stop="removeExistingFile(index)"
                                                                class="btn btn-link text-danger p-0 ml-1">
                                                                <i class="fas fa-trash-alt small"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION 4: Historial de Avances -->
                            <div class="section-header mb-3">
                                <span class="badge badge-info-soft text-info mr-2"><i class="fas fa-history"></i></span>
                                <span
                                    class="text-uppercase font-weight-bold small text-muted letter-spacing-1">Trazabilidad
                                    de Registro</span>
                            </div>

                            <div class="card border-0 shadow-sm">
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm mb-0">
                                        <thead class="table-header-gray">
                                            <tr>
                                                <th class="text-center font-weight-bold p-2 border-0">Periodo</th>
                                                <th class="text-center font-weight-bold p-2 border-0">Meta</th>
                                                <th class="text-center font-weight-bold p-2 border-0">Logro</th>
                                                <th class="font-weight-bold p-2 border-0 text-center">Comentario</th>
                                                <th class="text-center font-weight-bold p-2 border-0">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="isLoadingHistory">
                                                <td colspan="5" class="text-center py-5">
                                                    <div class="spinner-border text-danger spinner-border-sm"
                                                        role="status"></div>
                                                    <p class="text-muted mt-2 mb-0 small">Sincronizando historial...</p>
                                                </td>
                                            </tr>
                                            <tr v-else-if="avances.length === 0">
                                                <td colspan="5" class="text-center text-muted py-4">No hay registros
                                                    previos.</td>
                                            </tr>
                                            <tr v-else v-for="avance in avances" :key="avance.id">
                                                <td class="text-center align-middle">
                                                    <span class="badge badge-light p-2">{{ avance.is_periodo }}-{{
                                                        avance.is_numero_periodo
                                                        }}</span>
                                                </td>
                                                <td class="text-center align-middle font-weight-bold">{{ avance.is_meta
                                                    }}</td>
                                                <td class="text-center align-middle">
                                                    <span class="text-success font-weight-bold">{{ avance.is_valor
                                                        }}%</span>
                                                </td>
                                                <td class="align-middle">
                                                    <small class="text-muted text-truncate d-block"
                                                        style="max-width: 180px;" :title="avance.is_comentario">
                                                        {{ avance.is_comentario || '-' }}
                                                    </small>
                                                </td>
                                                <td v-if="isAdmin" class="text-center align-middle">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-warning rounded-circle"
                                                        style="width: 32px; height: 32px; padding: 0;"
                                                        @click="editAvance(avance)">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-between">
                            <div></div> <!-- Empty div for spacing -->
                            <div>
                                <button type="button" class="btn btn-secondary px-4" @click="close">
                                    <i class="fas fa-times mr-1"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-danger px-4 ml-2 shadow-sm"
                                    :disabled="isSaveDisabled">
                                    <i class="fas fa-save mr-1"></i> {{ editingAvanceId ? 'Actualizar' : 'Guardar' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, nextTick, computed } from 'vue';
import axios from 'axios';
import { Modal } from 'bootstrap';
import Swal from 'sweetalert2';

const props = defineProps({
    visible: Boolean,
    indicador: Object
});

const emit = defineEmits(['close', 'saved']);

const modalRef = ref(null);
const modalInstance = ref(null);
const fileInput = ref(null);

// Evaluación de la fórmula en tiempo real
const evaluatedFormula = computed(() => {
    if (!props.indicador?.indicador_formula) return '';
    let formula = props.indicador.indicador_formula;
    formula = formula.replace(/x/gi, '*');
    const getVarValue = (val, placeholder) => (val !== undefined && val !== null && val !== '') ? `<span class="text-danger border-bottom border-danger">${val}</span>` : `<span class="text-muted">[${placeholder}]</span>`;
    const variables = {
        'V1': getVarValue(form.value.is_var1, 'V1'),
        'V2': getVarValue(form.value.is_var2, 'V2'),
        'V3': getVarValue(form.value.is_var3, 'V3'),
        'V4': getVarValue(form.value.is_var4, 'V4'),
        'V5': getVarValue(form.value.is_var5, 'V5'),
        'V6': getVarValue(form.value.is_var6, 'V6'),
    };
    for (const [key, value] of Object.entries(variables)) {
        const regex = new RegExp(`\\b${key}\\b`, 'gi');
        formula = formula.replace(regex, value);
    }
    return formula;
});

const activeVariables = computed(() => {
    const active = [];
    if (!props.indicador) return active;
    for (let i = 1; i <= 6; i++) {
        const varName = props.indicador[`indicador_var${i}`];
        if (varName || props.indicador.indicador_formula.toUpperCase().includes(`V${i}`)) {
            active.push({
                n: i,
                name: varName || `Variable ${i}`
            });
        }
    }
    return active;
});

// Variables para la subida de archivos de evidencia
const isDragging = ref(false);
const filesToUpload = ref([]);
let fileCounter = 0;
const existingFiles = ref([]);
const editingAvanceId = ref(null);

const avances = ref([]); // Historial de avances
const isLoadingPeriod = ref(false);
const isLoadingHistory = ref(false);
const isPeriodFull = ref(false);

// Generar lista de años (actual +/- 1)
const years = computed(() => {
    const currentYear = new Date().getFullYear();
    return [currentYear - 1, currentYear, currentYear + 1].sort((a, b) => b - a);
});

// TODO: Implementar lógica real de permisos. Por ahora asumimos true o lo dejamos pendiente.
const isAdmin = ref(true);

const form = ref({
    is_periodo: new Date().getFullYear(),
    is_numero_periodo: '',
    is_fecha: new Date().toISOString().substr(0, 10),
    is_meta: '',
    is_valor: '',
    is_var1: '',
    is_var2: '',
    is_var3: '',
    is_var4: '',
    is_var5: '',
    is_var6: '',
    is_comentario: '',
});

console.log('IndicadorAvanceForm initialized', form.value);

const isMetaReadonly = computed(() => {
    return props.indicador?.indicador_sentido === 'lineal';
});

const isSaveDisabled = computed(() => {
    // Disable if periods are full AND we are NOT editing (creating new)
    if (isPeriodFull.value && !editingAvanceId.value) {
        return true;
    }
    return false;
});

const fetchNextPeriod = async () => {
    if (!props.indicador?.id || !form.value.is_periodo) return;
    isLoadingPeriod.value = true;
    try {
        const response = await axios.post('/api/indicadores-gestion/next-period', {
            indicador_id: props.indicador.id,
            year: form.value.is_periodo
        });
        form.value.is_numero_periodo = response.data.periodo;
        if (response.data.full) {
            isPeriodFull.value = true;
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: 'Máximo de periodos alcanzado',
                text: 'Se ha completado el número de periodos para este año.',
                showConfirmButton: false,
                timer: 4000
            });
        } else {
            isPeriodFull.value = false;
        }
    } catch (error) {
        console.error("Error fetching next period", error);
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: 'Error',
            text: "Error al obtener periodo: " + (error.response?.data?.message || error.message),
            timer: 3000
        });
    } finally {
        isLoadingPeriod.value = false;
    }
};

const fetchHistory = async () => {
    if (!props.indicador?.id) return;
    isLoadingHistory.value = true;
    try {
        const response = await axios.get(`/api/indicadores-gestion/${props.indicador.id}/avances`);
        avances.value = response.data;
    } catch (error) {
        console.error("Error fetching history", error);
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: 'Error',
            text: 'No se pudo cargar el historial.'
        });
    } finally {
        isLoadingHistory.value = false;
    }
};

const calculateValue = () => {
    if (!props.indicador?.indicador_formula) {
        alert('El indicador no tiene una fórmula definida.');
        return;
    }

    let formula = props.indicador.indicador_formula;

    // Normalizar la fórmula: reemplazar 'x' por '*' para multiplicación
    formula = formula.replace(/x/gi, '*');

    const variables = {
        'V1': parseFloat(form.value.is_var1) || 0,
        'V2': parseFloat(form.value.is_var2) || 0,
        'V3': parseFloat(form.value.is_var3) || 0,
        'V4': parseFloat(form.value.is_var4) || 0,
        'V5': parseFloat(form.value.is_var5) || 0,
        'V6': parseFloat(form.value.is_var6) || 0,
    };

    try {
        // Reemplazar variables en la fórmula
        for (const [key, value] of Object.entries(variables)) {
            // Usar regex para reemplazar palabra completa (ej: V1 pero no V10), case-insensitive
            const regex = new RegExp(`\\b${key}\\b`, 'gi');
            formula = formula.replace(regex, value);
        }

        // Limpiar la fórmula de caracteres no permitidos, pero permitir espacios
        // Permitimos: dígitos, puntos, operadores (+, -, *, /), paréntesis y espacios
        if (!/^[\d\.\+\-\*\/\(\)\s]+$/.test(formula)) {
            console.error("Fórmula inválida tras reemplazo:", formula);
            throw new Error("La fórmula contiene caracteres no válidos.");
        }

        const result = eval(formula);

        if (!isFinite(result) || isNaN(result)) {
            throw new Error("El resultado no es un número válido (posible división por cero).");
        }

        form.value.is_valor = parseFloat(result.toFixed(2)); // Redondear a 2 decimales
    } catch (error) {
        console.error("Error al calcular:", error);
        alert("Error al calcular la fórmula: " + error.message);
    }
};

const close = () => {
    // Remove focus from the button to prevent ARIA warnings when modal hides
    if (document.activeElement instanceof HTMLElement) {
        document.activeElement.blur();
    }
    emit('close');
};

// Funciones para manejar archivos (Drag & Drop + Input)
const handleFileSelect = (e) => {
    startUpload(Array.from(e.target.files));
};

const openFileDialog = () => {
    fileInput.value.click();
};

const onDragEnter = () => {
    isDragging.value = true;
};

const onDragLeave = () => {
    isDragging.value = false;
};

const onDrop = (event) => {
    isDragging.value = false;
    startUpload(Array.from(event.dataTransfer.files));
};

const startUpload = (files) => {
    const maxFileSize = 10 * 1024 * 1024; // 10MB

    files.forEach(file => {
        if (file.size > maxFileSize) {
            alert(`El archivo '${file.name}' supera el límite de 10MB y no será subido.`);
            return; // Skip this file
        }

        const fileEntry = { id: fileCounter++, file: file, progress: 0 };
        filesToUpload.value.push(fileEntry);
    });
};

const removeFile = (fileId) => {
    filesToUpload.value = filesToUpload.value.filter(f => f.id !== fileId);
};

const removeExistingFile = (index) => {
    existingFiles.value.splice(index, 1);
};

// Watchers
watch(() => form.value.is_periodo, () => {
    fetchNextPeriod();
});

// Limpiar valor si cambian las variables
watch(() => [form.value.is_var1, form.value.is_var2, form.value.is_var3, form.value.is_var4, form.value.is_var5, form.value.is_var6], () => {
    if (form.value.is_valor) {
        form.value.is_valor = '';
    }
});

const editAvance = (avance) => {
    editingAvanceId.value = avance.id;
    form.value.is_periodo = avance.is_periodo;
    form.value.is_numero_periodo = avance.is_numero_periodo;
    form.value.is_fecha = avance.is_fecha || new Date().toISOString().substr(0, 10);
    form.value.is_meta = avance.is_meta;
    form.value.is_valor = avance.is_valor;
    form.value.is_var1 = avance.is_var1;
    form.value.is_var2 = avance.is_var2;
    form.value.is_var3 = avance.is_var3;
    form.value.is_var4 = avance.is_var4;
    form.value.is_var5 = avance.is_var5;
    form.value.is_var6 = avance.is_var6;
    form.value.is_comentario = avance.is_comentario || '';

    existingFiles.value = [];
    if (avance.evidencias_list && Array.isArray(avance.evidencias_list)) {
        existingFiles.value = avance.evidencias_list;
    } else if (avance.is_evidencias && typeof avance.is_evidencias === 'string') {
        try {
            const parsed = JSON.parse(avance.is_evidencias);
            if (Array.isArray(parsed)) {
                existingFiles.value = parsed.map(path => ({ path: path, name: path.split('/').pop() }));
            }
        } catch (e) {
            existingFiles.value = [{ path: avance.is_evidencias, name: avance.is_evidencias.split('/').pop() }];
        }
    }

    if (modalRef.value) {
        modalRef.value.querySelector('.modal-body').scrollTop = 0;
    }
};

const submitForm = async () => {
    try {
        // Verificar si hay cambios en los archivos (nuevos o eliminados)
        const hasNewFiles = filesToUpload.value.length > 0;
        const hasFileChanges = hasNewFiles || existingFiles.value.length > 0;

        // Siempre usamos FormData si hay archivos o si estamos editando (para consistencia)
        // Ojo: Laravel PUT con FormData a veces requiere _method: PUT

        const filesFormData = new FormData();

        // Agregar todos los campos del formulario al FormData
        for (const key in form.value) {
            if (form.value[key] !== null && form.value[key] !== undefined && form.value[key] !== '') {
                filesFormData.append(key, form.value[key]);
            }
        }

        // Agregar el ID del indicador
        filesFormData.append('indicador_id', props.indicador.id);

        // Agregar archivos existentes que se mantienen
        if (existingFiles.value.length > 0) {
            existingFiles.value.forEach((file) => {
                filesFormData.append('existing_evidencias[]', file.path);
            });
        }

        // Agregar archivos nuevos para subir
        if (hasNewFiles) {
            filesToUpload.value.forEach((fileEntry) => {
                filesFormData.append('is_evidencias[]', fileEntry.file, fileEntry.file.name);
            });
        }

        // Añadir indicación de que se están actualizando evidencias
        // Esto es útil tanto para create como update si queremos lógica unificada
        if (hasFileChanges) {
            filesFormData.append('update_evidences', '1');
        }

        if (editingAvanceId.value) {
            // UPDATE
            // Para soportar archivos en update, usamos POST con _method = PUT o simplemente POST si la ruta lo permite.
            // Hemos definido la ruta update como POST /avance/{id} para simplificar.
            await axios.post(`/api/indicadores-gestion/avance/${editingAvanceId.value}`, filesFormData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
        } else {
            // CREATE
            await axios.post('/api/indicadores-gestion/avance', filesFormData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
        }

        emit('saved');
        await fetchHistory(); // Refresh history table
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: editingAvanceId.value ? 'Avance actualizado correctamente' : 'Avance registrado correctamente',
            timer: 1500,
            showConfirmButton: false
        });

        // Optional: clear form logic if creating new? 
        // For now, leaving form as is allows user to see what they saved or make minor adjustments for another entry if needed.
        // If it was editing, we might want to exit edit mode?
        if (editingAvanceId.value) {
            editingAvanceId.value = null;
            // Reset form fields to default for new entry? 
            // Or just leave it. The user said "not close". Usually implies "add another" or "review".
            // I'll leave it but maybe resetting the form would be better UX if they want to add another.
            // However, keeping values (like year/period) might be useful. 
            // I'll stick to minimum changes: don't close.
        }

    } catch (error) {
        console.error('Error saving avance:', error);
        let msg = error.response?.data?.message || error.message;

        if (error.response?.status === 422 && error.response?.data?.errors) {
            const errors = error.response.data.errors;
            // Si hay un error específico de validación, lo mostramos
            if (errors.is_numero_periodo) {
                msg = errors.is_numero_periodo[0];
            } else {
                // Sino, mostramos el primer error que encontremos
                const firstError = Object.values(errors)[0];
                msg = Array.isArray(firstError) ? firstError[0] : firstError;
            }
        }

        Swal.fire('Error', 'Error al guardar el avance: ' + msg, 'error');
    }
};

// ... (watchers)

watch(() => props.visible, async (newVal) => {
    if (newVal) {
        editingAvanceId.value = null; // Reset edit mode

        // Reset form when modal opens
        form.value.is_periodo = new Date().getFullYear();
        form.value.is_fecha = new Date().toISOString().substr(0, 10);
        form.value.is_meta = '';
        form.value.is_valor = '';
        form.value.is_var1 = '';
        form.value.is_var2 = '';
        form.value.is_var3 = '';
        form.value.is_var4 = '';
        form.value.is_var5 = '';
        form.value.is_var6 = '';
        form.value.is_numero_periodo = '';
        form.value.is_comentario = '';

        // Clear file lists
        filesToUpload.value = [];
        existingFiles.value = [];

        // Set meta if lineal
        if (props.indicador?.indicador_sentido === 'lineal') {
            form.value.is_meta = props.indicador.indicador_meta;
        }

        // Show modal immediately
        await nextTick();
        if (modalRef.value) {
            if (!modalInstance.value) {
                modalInstance.value = new Modal(modalRef.value, {
                    backdrop: 'static',
                    keyboard: false
                });
            }
            modalInstance.value.show();
        }

        // Fetch data asynchronously after showing modal
        Promise.all([fetchNextPeriod(), fetchHistory()]).catch(console.error);

    } else {
        if (modalInstance.value) {
            modalInstance.value.hide();
        }
        // Limpiar archivos cuando se cierra el modal
        filesToUpload.value = [];
        existingFiles.value = [];
        editingAvanceId.value = null;
    }
}, { immediate: true });




onMounted(() => {
    if (modalRef.value) {
        modalRef.value.addEventListener('hidden.bs.modal', close);
    }
});

onUnmounted(() => {
    modalInstance.value?.dispose();
});
</script>

<style scoped>
.border-bottom-red {
    border-bottom: 3px solid #dc3545 !important;
}

.bg-danger-soft {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.bg-light-soft {
    background-color: #fbfcfd !important;
}

.alert-danger-soft {
    background-color: rgba(220, 53, 69, 0.05) !important;
    color: #dc3545;
}

.badge-danger-soft {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    padding: 0.5rem;
    border-radius: 8px;
}

.badge-info-soft {
    background-color: rgba(23, 162, 184, 0.1);
    color: #17a2b0;
    padding: 0.5rem;
    border-radius: 8px;
}

.header-icon {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
}

.letter-spacing-1 {
    letter-spacing: 1px;
}

.section-header {
    display: flex;
    align-items: center;
}

.variable-field {
    background-color: #fff;
    border: 1px solid #edf2f7;
    transition: all 0.2s;
}

.variable-field:focus-within {
    border-color: #dc3545;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.evaluated-box {
    background-color: #f8f9fa;
    border: 1px dashed #dee2e6;
}

.custom-label {
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    color: #718096 !important;
}

.custom-label-small {
    font-size: 0.65rem !important;
    font-weight: 700 !important;
}

.custom-label-white {
    font-weight: 700 !important;
}

.ripple {
    position: relative;
    overflow: hidden;
}

.drop-zone {
    border: 2px dashed #e2e8f0;
    border-radius: 10px;
    background-color: #fcfcfd;
    cursor: pointer;
    transition: all 0.2s;
}

.drop-zone:hover {
    border-color: #dc3545;
    background-color: #fffafa;
}

.shadow-sm-up {
    box-shadow: 0 -0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.border-light-2 {
    border: 1px solid #f1f5f9;
}

.table-header-gray {
    background-color: #f1f5f9 !important;
    border-bottom: 2px solid #e2e8f0 !important;
}

.table-header-gray th {
    color: #475569 !important;
    font-size: 0.7rem !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.font-weight-medium {
    font-weight: 500;
}

.variable-field {
    min-height: 75px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
}

.variable-info-overlay {
    opacity: 0;
    height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
    font-size: 0.65rem;
    line-height: 1.1;
    margin-bottom: 0;
}

.has-hover-info:hover .variable-info-overlay {
    opacity: 1;
    height: auto;
    margin-bottom: 5px;
}

.has-hover-info:hover {
    border-color: #dc3545;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.btn-danger:focus {
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.5);
}

.btn-danger:not(:disabled):not(.disabled):active,
.btn-danger:not(:disabled):not(.disabled).active {
    background-color: #bd2130;
    border-color: #b21f2d;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
    transform: translateY(-1px);
    box-shadow: 0 0.125rem 0.25rem rgba(108, 117, 125, 0.3);
}

.btn-secondary:focus {
    box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.5);
}

/* Modal header */
.modal-header {
    background-color: #dc3545;
    color: white;
    border-top-left-radius: calc(0.3rem - 1px);
    border-top-right-radius: calc(0.3rem - 1px);
}

.modal-header .close {
    color: white;
    opacity: 1;
    font-size: 1.5rem;
}

.modal-header .close:hover {
    color: #e9ecef;
    opacity: 0.8;
}

/* Card styling */
.card {
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    background-color: #ffffff;
}

.card-body {
    padding: 0.75rem;
}

/* Input group styling */
.input-group .btn {
    border: 1px solid #ced4da;
    background-color: #e9ecef;
    color: #495057;
    transition: all 0.15s ease-in-out;
}

.input-group .btn:hover {
    background-color: #dcdcdc;
    border-color: #adb5bd;
    color: #212529;
}

.input-group .btn-danger {
    border: 1px solid #dc3545;
    background-color: #dc3545;
    color: white;
}

.input-group .btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
    color: white;
}

/* Form group styling */
.form-group {
    margin-bottom: 1.1rem;
}

/* Modal footer */
.modal-footer {
    background-color: #f8f9fa;
    padding: 1rem;
    border-bottom-right-radius: calc(0.3rem - 1px);
    border-bottom-left-radius: calc(0.3rem - 1px);
}

/* Textarea styling */
textarea.form-control {
    resize: vertical;
}

/* Section headers */
h6.font-weight-bold {
    color: #212529;
    font-weight: 700;
}

/* Input group text */
.input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
}

/* Progress bar styling */
.progress {
    background-color: #e9ecef;
    border-radius: 1rem;
}

.progress-bar {
    background-color: #dc3545;
}

/* File list items */
.list-group-item {
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    margin-bottom: 0.25rem;
    transition: all 0.15s ease-in-out;
}

.list-group-item:hover {
    background-color: #f8f9fa;
    transform: translateY(-1px);
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
</style>
