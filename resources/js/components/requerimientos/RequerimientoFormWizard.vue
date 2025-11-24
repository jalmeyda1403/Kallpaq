<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><router-link :to="{ name: 'requerimientos.index' }">Inicio</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Crear Nuevo Requerimiento</li>
            </ol>
        </nav>
        <div class="card">
            <div class="card-header bg-danger">
                <h4 class="card-title text-white">Generar Requerimiento</h4>
            </div>
            <div class="card-body">
                <div v-if="loading" class="text-center p-5">
                    <p>Cargando requerimiento...</p>
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                </div>
                <div v-else class="row">
                    <!-- Stepper Content (Left Column) -->
                    <div class="col-md-9">
                        <div class="bs-stepper-content">
                            <form @submit.prevent="">
                                <!-- Step 1: Basic Information -->
                                <div id="step-basic-info" class="content" role="tabpanel"
                                    aria-labelledby="step-basic-info-trigger" :class="{ active: currentStep === 1 }">
                                    <div class="form-group">
                                        <label for="asunto">Asunto</label>
                                        <input type="text" class="form-control" id="asunto" v-model="form.asunto"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="descripcion">Descripción</label>
                                        <textarea class="form-control" id="descripcion" v-model="form.descripcion"
                                            rows="3" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="justificacion">Justificación</label>
                                        <textarea class="form-control" id="justificacion" v-model="form.justificacion"
                                            rows="3" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="proceso_id">Proceso</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" :value="form.proceso_nombre"
                                                readonly placeholder="Seleccione un proceso" required>
                                            <div class="input-group-append">
                                                <button class="btn bg-dark" type="button" @click="openProcesoModal">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="hidden" v-model="form.proceso_id" required>
                                    </div>
                                    <button class="btn btn-primary btn-sm" @click="saveAndNext">Siguiente</button>
                                </div>

                                <!-- Step 2: Evaluación de Complejidad -->
                                <div id="step-evaluation" class="content" role="tabpanel"
                                    aria-labelledby="step-evaluation-trigger" :class="{ active: currentStep === 2 }">
                                    <h3 class="step-title">Evaluación de Complejidad del Requerimiento</h3>
                                    <p class="step-intro">Seleccione una opción para cada uno de los cinco criterios
                                        para determinar el nivel de complejidad del requerimiento.</p>

                                    <!-- Pregunta 1 -->
                                    <div class="evaluation-question-group">
                                        <label class="evaluation-question-title">1. ¿Cuántas actividades principales
                                            componen el requerimiento?</label>
                                        <small class="form-text text-muted">Evaluar la cantidad de pasos o tareas clave
                                            necesarias para atender el requerimiento.</small>
                                        <div class="evaluation-options">
                                            <div class="custom-control custom-radio"
                                                v-for="option in options.actividades">
                                                <input type="radio" :id="'actividades_' + option.value"
                                                    :value="option.value" v-model.number="form.eval_actividades"
                                                    class="custom-control-input">
                                                <label class="custom-control-label"
                                                    :for="'actividades_' + option.value">{{ option.description
                                                    }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pregunta 2 -->
                                    <div class="evaluation-question-group">
                                        <label class="evaluation-question-title">2. ¿Cuántas unidades orgánicas
                                            participan en la atención del requerimiento?</label>
                                        <small class="form-text text-muted">Determinar el grado de coordinación
                                            institucional necesaria entre áreas.</small>
                                        <div class="evaluation-options">
                                            <div class="custom-control custom-radio" v-for="option in options.areas">
                                                <input type="radio" :id="'areas_' + option.value" :value="option.value"
                                                    v-model.number="form.eval_areas" class="custom-control-input">
                                                <label class="custom-control-label" :for="'areas_' + option.value">{{
                                                    option.description }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pregunta 3 -->
                                    <div class="evaluation-question-group">
                                        <label class="evaluation-question-title">3. ¿Qué nivel de requisitos normativos
                                            aplica al requerimiento?</label>
                                        <small class="form-text text-muted">Medir la cantidad y complejidad de normas o
                                            directivas involucradas en su cumplimiento.</small>
                                        <div class="evaluation-options">
                                            <div class="custom-control custom-radio"
                                                v-for="option in options.requisitos">
                                                <input type="radio" :id="'requisitos_' + option.value"
                                                    :value="option.value" v-model.number="form.eval_requisitos"
                                                    class="custom-control-input">
                                                <label class="custom-control-label"
                                                    :for="'requisitos_' + option.value">{{
                                                        option.description }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pregunta 4 -->
                                    <div class="evaluation-question-group">
                                        <label class="evaluation-question-title">4. ¿Qué nivel de documentación requiere
                                            el requerimiento?</label>
                                        <small class="form-text text-muted">Evaluar la cantidad y complejidad de
                                            documentos a elaborar, revisar o modificar.</small>
                                        <div class="evaluation-options">
                                            <div class="custom-control custom-radio"
                                                v-for="option in options.documentacion">
                                                <input type="radio" :id="'documentacion_' + option.value"
                                                    :value="option.value" v-model.number="form.eval_documentacion"
                                                    class="custom-control-input">
                                                <label class="custom-control-label"
                                                    :for="'documentacion_' + option.value">{{ option.description
                                                    }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pregunta 5 -->
                                    <div class="evaluation-question-group">
                                        <label class="evaluation-question-title">5. ¿Qué nivel de impacto tiene el
                                            requerimiento en otros procedimientos o procesos?</label>
                                        <small class="form-text text-muted">Evalúa el nivel de influencia del
                                            requerimiento respecto a otros procedimientos.</small>
                                        <div class="evaluation-options">
                                            <div class="custom-control custom-radio" v-for="option in options.impacto">
                                                <input type="radio" :id="'impacto_' + option.value"
                                                    :value="option.value" v-model.number="form.eval_impacto"
                                                    class="custom-control-input">
                                                <label class="custom-control-label" :for="'impacto_' + option.value">{{
                                                    option.description }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <button class="btn btn-secondary btn-sm ml-1" @click="prevStep">Anterior</button>
                                    <button class="btn btn-primary btn-sm ml-1"
                                        @click="saveEvaluationAndNext">Siguiente</button>
                                </div>

                                <!-- Step 3: Documents and Files -->
                                <div id="step-documents" class="content" role="tabpanel"
                                    aria-labelledby="step-documents-trigger" :class="{ active: currentStep === 3 }">
                                    <div class="form-group mb-4 p-3 border rounded shadow-sm bg-light">
                                        <h5 class="mb-2">
                                            <i class="fas fa-print mr-2"></i> Imprimir Requerimiento para Firma
                                        </h5>
                                        <p class="text-muted small">
                                            Genere la versión PDF oficial de su requerimiento. Este documento es
                                            necesario para el proceso de firma.
                                        </p>

                                        <div class="mt-3">
                                            <button class="btn btn-primary" @click.prevent="printRequerimiento"
                                                :disabled="!requerimientoId">
                                                <i class="fas fa-file-pdf mr-2"></i> Imprimir PDF
                                            </button>
                                        </div>

                                        <div v-if="!requerimientoId" class="text-danger small mt-2">
                                            Aún no es posible imprimir. Guarde o cree el requerimiento primero.
                                        </div>
                                    </div>
                                 

                                    <div class="form-group mb-4 p-3 border rounded border-success-subtle bg-light">
                                        <h5 class="text-success mb-2 d-flex align-items-center">
                                            <i class="fas fa-upload mr-2"></i> Adjuntar Requerimiento Firmado
                                        </h5>
                                        <p class="text-muted small">
                                            Por favor, adjunte aquí el archivo PDF del requerimiento **una vez que haya
                                            sido firmado** por las partes correspondientes.
                                        </p>

                                        <div
                                            class="drop-zone"
                                            @dragenter.prevent="onDragEnter"
                                            @dragleave.prevent="onDragLeave"
                                            @dragover.prevent
                                            @drop.prevent="onDrop"
                                            :class="{ 'drag-over': isDragging }"
                                            @click="openFileDialog"
                                        >
                                            <input
                                                type="file"
                                                ref="fileInput"
                                                class="d-none"
                                                @change="handleFileUpload($event, 'signed_requerimiento')"
                                                accept=".pdf"
                                                multiple
                                            />
                                            <div class="text-center">
                                                <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                                                <p class="mb-0 mt-2">Arrastra y suelta el archivo aquí, o haz clic para seleccionar.</p>
                                                <small class="text-muted">(Máx. 5 MB. Formato permitido: PDF)</small>
                                            </div>
                                        </div>

                                        <div v-if="uploadProgress > 0" class="progress mt-3" style="height: 20px;">
                                            <div
                                                class="progress-bar progress-bar-striped progress-bar-animated"
                                                role="progressbar"
                                                :style="{ width: uploadProgress + '%' }"
                                                :aria-valuenow="uploadProgress"
                                                aria-valuemin="0"
                                                aria-valuemax="100"
                                            >
                                                {{ uploadProgress }}%
                                            </div>
                                        </div>

                                        <div v-if="uploadedFiles.length > 0" class="mt-3">
                                            <ul class="list-group">
                                                <li v-for="(file, index) in uploadedFiles" :key="index" class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-file-pdf mr-2"></i>
                                                        <a :href="file.path" target="_blank">{{ file.name }}</a>
                                                    </div>
                                                    <button class="btn btn-sm btn-danger" @click="deleteFile(index)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>

                                        <div v-else-if="!uploadProgress" class="alert alert-warning mt-3 p-2">
                                            <i class="fas fa-info-circle mr-2"></i> Documento pendiente de adjuntar.
                                        </div>
                                    </div>
                                    <!-- Submission button moved here -->
                                    <button class="btn btn-secondary btn-sm ml-1" @click="prevStep">Anterior</button>
                                    <button class="btn btn-primary btn-sm ml-1" @click.prevent="submitRequerimiento"
                                        :disabled="!form.ruta_archivo_requerimiento">Enviar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Stepper Navigation (Right Column) -->
                    <div class="col-md-3 stepper-bg">
                        <div class="stepper-wrapper">
                            <div class="stepper-item"
                                :class="{ completed: currentStep > 1, active: currentStep === 1 }">
                                <div class="step-counter">1</div>
                                <div class="step-name">Información Básica</div>
                            </div>
                            <div class="stepper-item"
                                :class="{ completed: currentStep > 2, active: currentStep === 2 }">
                                <div class="step-counter">2</div>
                                <div class="step-name">Evaluación de Complejidad</div>
                            </div>
                            <div class="stepper-item"
                                :class="{ completed: currentStep > 3, active: currentStep === 3 }">
                                <div class="step-counter">3</div>
                                <div class="step-name">Documentos y Archivos</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <ModalHijo ref="modalHijoProceso" :fetch-url="procesoFetchUrl" target-id="proceso_id" target-desc="proceso_nombre"
        @update-target="handleProcesoSelected"></ModalHijo>
</template>

<style scoped>
.stepper-bg {
    background-color: #f8f9fa;
    /* Light smoke color */
    border-radius: 0.25rem;
    padding-top: 20px;
    padding-bottom: 20px;
}

.stepper-wrapper {
    position: relative;
    padding-left: 40px;
}

.stepper-item {
    position: relative;
    display: flex;
    align-items: center;
    margin-bottom: 40px;
}

.stepper-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 35px;
    width: 2px;
    height: 100%;
    background-color: #e0e0e0;
}

.step-counter {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: #e0e0e0;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    z-index: 1;
    transition: all 0.3s ease;
}

.step-name {
    margin-left: 15px;
    font-size: 15px;
    color: #6a6c6d;
    transition: all 0.3s ease;
}

.stepper-item.active .step-counter {
    background-color: #e94152;
    /* Red for active */
    border: 2px solid #e94152;
}

.stepper-item.active .step-name {
    color: #dc3545;

}

.stepper-item.completed .step-counter {
    background-color: #1a1919;
    /* Gray for completed */
    border: 2px solid #1a1919;
}

.stepper-item.completed .step-name {
    color: #3a3a3a;
    font-weight: 500;
}

.stepper-item.completed:not(:last-child)::before {
    background-color: #6c757d;
}

.bs-stepper-content .content {
    display: none;
    /* Hide inactive content */
}

.bs-stepper-content .content.active {
    display: block;
    /* Show active content */
}

/* Evaluation Step Styles */
.step-title {
    font-size: 1.25rem;
    /* Reduced size */
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #333;
}

.step-intro {
    margin-bottom: 2rem;
    font-size: 0.9rem;
    /* Reduced size */
    color: #6c757d;
}

.evaluation-question-group {
    margin-bottom: 1rem;
    /* Reduced margin */
    padding: 1.5rem;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    background-color: #fff;
}

.evaluation-question-title {
    font-size: 12px;
    /* As requested */
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.25rem;
    /* Closer to help text */
}

.evaluation-question-group .form-text {
    margin-bottom: 1rem;
    /* More space between help text and options */
}

.evaluation-options .custom-control {
    margin-bottom: 0.75rem;
}

.evaluation-options .custom-control-label {
    font-size: 12px;
    /* As requested */
    font-weight: 400;
    /* Not bold */
    color: #495057;
    line-height: 1.5;
}

.drop-zone {
    border: 2px dashed #ccc;
    border-radius: 10px;
    padding: 40px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.drop-zone.drag-over {
    background-color: #f0f0f0;
    border-color: #aaa;
}
</style>
<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import { useRouter } from 'vue-router';
import ModalHijo from '@/components/generales/ModalHijo.vue';

const router = useRouter();

const props = defineProps({
    requerimientoId: {
        type: [String, Number],
        default: null
    }
});

const currentStep = ref(1);
const requerimientoId = ref(null); // Keep this ref for internal use and assignment from prop
const procesos = ref([]); // Kept for getProcesoName in review step if needed elsewhere
const modalHijoProceso = ref(null); // Ref for ModalHijo component
const loading = ref(true); // New loading state
const isDragging = ref(false);
const uploadProgress = ref(0);
const uploadedFileName = ref('');
const uploadedFiles = ref([]);
const fileInput = ref(null);

const openFileDialog = () => {
    fileInput.value.click();
};


const form = reactive({
    asunto: '',
    descripcion: '',
    justificacion: '',
    proceso_id: '',
    proceso_nombre: '',
    eval_actividades: null,
    eval_areas: null,
    eval_requisitos: null,
    eval_documentacion: null,
    eval_impacto: null,
    ruta_archivo_requerimiento: '',
});

const options = {
    actividades: [
        { value: 1, description: 'El proceso es lineal y sencillo, compuesto por menos de 5 actividades principales.' },
        { value: 2, description: 'El proceso tiene complejidad media, compuesto por 5 a 8 actividades principales, con 1-2 decisiones simples.' },
        { value: 3, description: 'El proceso es complejo, compuesto por 9 a 15 actividades principales, con múltiples puntos de decisión y bifurcaciones.' },
        { value: 4, description: 'El proceso es muy extenso y complejo, compuesto por más de 15 actividades, con bucles o subprocesos anidados.' },
    ],
    areas: [
        { value: 1, description: '1 unidad orgánica involucrada (el requerimiento es interno a una sola área).' },
        { value: 2, description: '2 a 3 unidades orgánicas involucradas. La coordinación es manejable.' },
        { value: 3, description: '4 a 5 unidades orgánicas involucradas. Requiere gestión formal de múltiples partes interesadas.' },
        { value: 4, description: 'Más de 5 unidades orgánicas involucradas, o es transversal a toda la organización.' },
    ],
    requisitos: [
        { value: 1, description: 'No hay requisitos normativos directos, o solo aplican políticas internas no críticas.' },
        { value: 2, description: 'Afectado por 1-2 normativas o regulaciones conocidas, con requisitos claros y poca ambigüedad.' },
        { value: 3, description: 'Afectado por 3 o más normativas, o por regulaciones de alta rigurosidad (ej. legales, financieras) que requieren revisión constante.' },
        { value: 4, description: 'Afectado por un marco regulatorio complejo, cambiante y/o contradictorio. Requiere validación y auditoría externa.' },
    ],
    documentacion: [
        { value: 1, description: 'Solo se requieren adecuar las actividades de un procedimiento o adecuar algún formato (ej. un acta o un email simple).' },
        { value: 2, description: 'Requiere la elaboración o modificación de más de un documento estándar (ej. un manual de usuario o una política simple).' },
        { value: 3, description: 'Requiere la elaboración de un nuevo manual o procedimientos completos, múltiples instructivos, o modificación de documentos críticos.' },
        { value: 4, description: 'Requiere la creación de un paquete documental formal y extenso, incluyendo documentación técnica, legal, de capacitación y aprobación a múltiples niveles.' },
    ],
    impacto: [
        { value: 1, description: 'El resultado afecta solo al área que lo ejecuta, o a un grupo muy reducido de usuarios.' },
        { value: 2, description: 'El resultado sirve de entrada o salida para 1-2 procedimientos adyacentes de bajo impacto.' },
        { value: 3, description: 'El resultado afecta un proceso clave de la organización o tiene una influencia directa en el servicio/producto principal.' },
        { value: 4, description: 'La modificación implica un cambio significativo en la forma de operar de la organización (afecta a clientes externos, impacta la misión de la empresa, genera cascadas de cambios).' },
    ],
};

const complejidad = computed(() => {
    const scores = [
        form.eval_actividades,
        form.eval_areas,
        form.eval_requisitos,
        form.eval_documentacion,
        form.eval_impacto
    ];
    if (scores.some(score => score === null)) {
        return { valor: 0, nivel: '' };
    }
    const total = scores.reduce((acc, value) => acc + (Number(value) || 0), 0);
    let nivel = '';
    if (total >= 5 && total <= 8) {
        nivel = 'baja';
    } else if (total >= 9 && total <= 12) {
        nivel = 'media';
    } else if (total >= 13 && total <= 16) {
        nivel = 'alta';
    } else if (total >= 17) {
        nivel = 'muy alta';
    }
    return { valor: total, nivel };
});

const procesoFetchUrl = computed(() => route('procesos.listar'));

const fetchProcesos = async () => {
    try {
        // This might not be needed if ModalHijo handles fetching for all processes
        // However, keeping it if getProcesoName is used for review step
        const response = await axios.get(route('procesos.listar'));
        procesos.value = response.data; // This will hold all processes in {id, descripcion} format
    } catch (error) {
        console.error('Error fetching procesos:', error);
    }
};

const getProcesoName = (id) => {
    const proceso = procesos.value.find(p => p.id === id);
    return proceso ? proceso.descripcion : 'N/A'; // Use .descripcion now
};

const openProcesoModal = () => {
    modalHijoProceso.value.open();
};

const handleProcesoSelected = (data) => {
    form.proceso_id = data.idValue;
    form.proceso_nombre = data.descValue;
};

const onDragEnter = () => {
    isDragging.value = true;
};

const onDragLeave = () => {
    isDragging.value = false;
};

const onDrop = (event) => {
    isDragging.value = false;
    const files = event.dataTransfer.files;
    if (files.length > 0) {
        const fakeEvent = { target: { files: files } };
        handleFileUpload(fakeEvent, 'signed_requerimiento');
    }
};

const nextStep = async () => {
    if (currentStep.value < 3) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const saveAndNext = async () => {
    if (!form.asunto || !form.descripcion || !form.justificacion || !form.proceso_id) {
        alert('Por favor, complete todos los campos requeridos en Información Básica.');
        return;
    }
    await saveBasicRequerimiento();
    if (requerimientoId.value) {
        nextStep();
    }
};

const saveEvaluationAndNext = async () => {
    const evaluationFields = [
        form.eval_actividades,
        form.eval_areas,
        form.eval_requisitos,
        form.eval_documentacion,
        form.eval_impacto
    ];
    if (evaluationFields.some(field => field === null)) {
        alert('Por favor, complete todas las preguntas de evaluación.');
        return;
    }
    const success = await saveEvaluation();
    if (success) {
        nextStep();
    }
};

const saveBasicRequerimiento = async () => {
    try {
        let response;
        const payload = {
            asunto: form.asunto,
            descripcion: form.descripcion,
            justificacion: form.justificacion,
            proceso_id: form.proceso_id,
        };

        if (requerimientoId.value) {
            // Update existing requerimiento
            response = await axios.put(route('requerimientos.update', { id: requerimientoId.value }), payload);
        } else {
            // Create new requerimiento
            response = await axios.post(route('requerimientos.store'), payload);
        }
        requerimientoId.value = response.data.requerimiento_id;
    } catch (error) {
        console.error('Error al guardar el requerimiento básico:', error);
        alert('Error al guardar el requerimiento.');
    }
};

const saveEvaluation = async () => {
    try {
        const payload = {
            actividades: form.eval_actividades,
            areas: form.eval_areas,
            requisitos: form.eval_requisitos,
            documentacion: form.eval_documentacion,
            impacto: form.eval_impacto,
            complejidad_valor: complejidad.value.valor,
            complejidad_nivel: complejidad.value.nivel,
            from_wizard: true,
        };

        const response = await axios.post(route('requerimiento.grabarEvaluacion', { id: requerimientoId.value }), payload);
        alert(response.data.message || 'Evaluación guardada con éxito.');
        return true;
    } catch (error) {
        console.error('Error al guardar la evaluación:', error);
        alert(error.response?.data?.message || 'Ocurrió un error inesperado al guardar la evaluación.');
        return false;
    }
};

const printRequerimiento = () => {
    if (requerimientoId.value) {
        window.open(`/vue/requerimientos/imprimir/${requerimientoId.value}`, '_blank');
    } else {
        alert('Primero debe guardar la información básica del requerimiento.');
    }
};

const handleFileUpload = async (event, documentType) => {
    const files = event.target.files;
    if (!files.length) return;

    if (!requerimientoId.value) {
        alert('Primero debe guardar la información básica del requerimiento.');
        return;
    }

    for (const file of files) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('document_type', documentType);

        try {
            const response = await axios.post(route('requerimientos.uploadDocument', { id: requerimientoId.value }), formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                onUploadProgress: (progressEvent) => {
                    uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                },
            });
            if (documentType === 'signed_requerimiento') {
                const newFile = {
                    name: response.data.file.name,
                    path: response.data.file.path.startsWith('/storage/') ? response.data.file.path : '/storage/' + response.data.file.path
                };
                uploadedFiles.value.push(newFile);
            }
            alert(response.data.message);
            setTimeout(() => {
                uploadProgress.value = 0;
            }, 2000);
        } catch (error) {
            console.error('Error al subir el documento:', error);
            alert('Error al subir el documento.');
            uploadProgress.value = 0;
        }
    }
};

const deleteFile = async (index) => {
    const fileToDelete = uploadedFiles.value[index];
    if (!fileToDelete) return;

    try {
        await axios.post(route('requerimientos.deleteDocument', { id: requerimientoId.value }), {
            path: fileToDelete.path.startsWith('/storage/') ? fileToDelete.path.substring(8) : fileToDelete.path
        });
        uploadedFiles.value.splice(index, 1);
        alert('Documento eliminado con éxito.');
    } catch (error) {
        console.error('Error al eliminar el documento:', error);
        alert('Error al eliminar el documento.');
    }
};

const submitRequerimiento = async () => {
    if (!requerimientoId.value) {
        alert('El requerimiento no ha sido guardado.');
        return;
    }
    if (!form.ruta_archivo_requerimiento) {
        alert('Debe adjuntar el requerimiento firmado antes de enviar.');
        return;
    }

    try {
        const response = await axios.post(route('requerimientos.submitForEvaluation', { id: requerimientoId.value }));
        alert(response.data.message);
        router.push({ name: 'requerimientos.index' }); // Redirect to monitoring inbox
    } catch (error) {
        console.error('Error al enviar el requerimiento para evaluación:', error);
        alert('Error al enviar el requerimiento.');
    }
};

onMounted(async () => {
    loading.value = true; // Start loading
    await fetchProcesos(); // Wait for processes to be loaded
    if (props.requerimientoId) {
        requerimientoId.value = props.requerimientoId; // Assign prop value to ref
        await fetchRequerimientoDetails(props.requerimientoId);
    }
    loading.value = false; // End loading
});

const fetchRequerimientoDetails = async (id) => {
    try {
        const response = await axios.get(route('requerimientos.show', { id: id }));
        const data = response.data;

        // Prepare an object with all the data to update
        const formData = {
            asunto: data.asunto,
            descripcion: data.descripcion,
            justificacion: data.justificacion,
            proceso_id: data.proceso_id,
            ruta_archivo_requerimiento: data.ruta_archivo_requerimiento || '',
        };

        // Handle process name
        if (data.proceso_id) {
            const proceso = procesos.value.find(p => p.id === data.proceso_id);
            if (proceso) {
                formData.proceso_nombre = proceso.descripcion;
            }
        }

        if (data.ruta_archivo_requerimiento) {
            try {
                const files = JSON.parse(data.ruta_archivo_requerimiento);
                if (Array.isArray(files)) {
                    uploadedFiles.value = files.map(file => ({
                        name: file.name,
                        path: file.path.startsWith('/storage/') ? file.path : '/storage/' + file.path
                    }));
                }
            } catch (error) {
                // Fallback for single file path
                if (typeof data.ruta_archivo_requerimiento === 'string') {
                     const fileName = data.ruta_archivo_requerimiento.split('/').pop();
                     const filePath = data.ruta_archivo_requerimiento.startsWith('/storage/') ? data.ruta_archivo_requerimiento : '/storage/' + data.ruta_archivo_requerimiento;
                     uploadedFiles.value = [{ name: fileName, path: filePath }];
                }
                console.error('Error parsing ruta_archivo_requerimiento:', error);
            }
        }

        // Handle evaluation data, ensuring values are numbers
        if (data.evaluacion) {
            formData.eval_actividades = data.evaluacion.num_actividades ? Number(data.evaluacion.num_actividades) : null;
            formData.eval_areas = data.evaluacion.num_areas ? Number(data.evaluacion.num_areas) : null;
            formData.eval_requisitos = data.evaluacion.num_requisitos ? Number(data.evaluacion.num_requisitos) : null;
            formData.eval_documentacion = data.evaluacion.nivel_documentacion ? Number(data.evaluacion.nivel_documentacion) : null;
            formData.eval_impacto = data.evaluacion.impacto_requerimiento ? Number(data.evaluacion.impacto_requerimiento) : null;
        }

        // Use Object.assign to update the reactive form object
        Object.assign(form, formData);

    } catch (error) {
        console.error('Error fetching requerimiento details:', error);
        alert('Error al cargar los detalles del requerimiento.');
        router.push({ name: 'requerimientos.index' });
    }
};
</script>