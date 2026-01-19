<template>
    <div class="container-fluid py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item">
                    <router-link :to="{ name: 'requerimientos.index' }" class="text-danger font-weight-bold">Inicio</router-link>
                </li>
                <li class="breadcrumb-item">
                    <router-link :to="{ name: 'requerimientos.mine' }" class="text-danger font-weight-bold">Mis Requerimientos</router-link>
                </li>
                <li class="breadcrumb-item active text-muted" aria-current="page">
                    {{ requerimientoId ? 'Editar' : 'Crear' }} Requerimiento
                </li>
            </ol>
        </nav>
        <!-- Header Style like UsuariosIndex -->
        <div class="card shadow-sm border-0 mb-4 overflow-hidden">
            <div class="card-header bg-danger py-2 px-3">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <div class="d-flex align-items-center">
                            <!-- Circular Icon Container (Extra Small) -->
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 40px; height: 40px; min-width: 40px;">
                                <i class="fas fa-file-signature text-danger" style="font-size: 0.9rem;"></i>
                            </div>
                            <div>
                                <h5 class="font-weight-bold text-white mb-0">
                                    {{ requerimientoId ? 'Editar Requerimiento' : 'Nuevo Requerimiento' }}
                                </h5>
                                <p class="text-white mb-0" style="opacity: 0.9; font-size: 0.75rem;">
                                    Complete los pasos para gestionar su solicitud.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-right mt-2 mt-md-0">
                        <button class="btn btn-link text-white text-decoration-none mr-3 px-0 btn-sm" @click="router.push({ name: 'requerimientos.mine' })">
                            <i class="fas fa-arrow-left mr-1"></i> Volver
                        </button>
                        <button v-if="requerimientoId" class="btn btn-light btn-xs px-3 shadow-sm font-weight-bold py-1" @click="printRequerimiento" style="font-size: 0.75rem;">
                            <i class="fas fa-print mr-1 text-danger"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="row no-gutters">
                    <!-- Sidebar Stepper (Left) -->
                    <div class="col-md-3 bg-light border-right min-vh-75">
                        <div class="p-4">
                            <div class="stepper-wrapper">
                                <div class="stepper-item" 
                                    :class="{ 'active': currentStep === 1, 'completed': currentStep > 1 }"
                                    @click="goToStep(1)">
                                    <div class="step-counter">
                                        <i v-if="currentStep > 1" class="fas fa-check"></i>
                                        <span v-else>1</span>
                                    </div>
                                    <div class="step-info">
                                        <div class="step-name">Información Básica</div>
                                        <small class="step-desc">Detalles del requerimiento</small>
                                    </div>
                                </div>
                                <div class="stepper-item" 
                                    :class="{ 'active': currentStep === 2, 'completed': currentStep > 2 }"
                                    @click="goToStep(2)">
                                    <div class="step-counter">
                                        <i v-if="currentStep > 2" class="fas fa-check"></i>
                                        <span v-else>2</span>
                                    </div>
                                    <div class="step-info">
                                        <div class="step-name">Evaluación</div>
                                        <small class="step-desc">Impacto y complejidad</small>
                                    </div>
                                </div>
                                <div class="stepper-item" 
                                    :class="{ 'active': currentStep === 3, 'completed': currentStep > 3 }"
                                    @click="goToStep(3)">
                                    <div class="step-counter">
                                        <i v-if="currentStep > 3" class="fas fa-check"></i>
                                        <span v-else>3</span>
                                    </div>
                                    <div class="step-info">
                                        <div class="step-name">Adjuntar y Enviar</div>
                                        <small class="step-desc">Documentación final</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Complexity Widget -->
                            <div v-if="complejidad.nivel" class="mt-5 fade-in">
                                <div class="card border-0 shadow-sm bg-white p-3 rounded-lg">
                                    <h6 class="text-uppercase text-muted small font-weight-bold mb-3">Nivel de Complejidad</h6>
                                    <div class="d-flex align-items-end justify-content-between mb-2">
                                        <span class="h4 mb-0 font-weight-bold text-dark">{{ complejidad.valor }}</span>
                                        <span class="text-muted small">Puntaje Total</span>
                                    </div>
                                    <div class="progress mb-3" style="height: 6px;">
                                        <div class="progress-bar" :class="getComplejidadBadgeClass" 
                                            :style="{ width: (complejidad.valor / 20 * 100) + '%' }"></div>
                                    </div>
                                    <span class="badge badge-pill badge-block p-2" :class="getComplejidadBadgeClass">
                                        {{ complejidad.nivel.toUpperCase() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content (Right) -->
                    <div class="col-md-9 bg-white">
                        <div class="p-4 p-lg-5">
                            <form @submit.prevent="">
                                <!-- Step 1: Basic Info -->
                                <div v-show="currentStep === 1" class="step-pane fade-in">
                                    <div class="pane-header mb-4">
                                        <h4 class="text-dark font-weight-bold">Información General</h4>
                                        <p class="text-muted">Proporcione los detalles fundamentales de su solicitud.</p>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark mb-2">Asunto / Título <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-lg border-2 shadow-none" 
                                            v-model="form.asunto" placeholder="Ej: Implementación de Módulo de Facturación" :disabled="saving">
                                    </div>

                                    <div class="form-group mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="font-weight-bold text-dark mb-0">Descripción Detallada <span class="text-danger">*</span></label>
                                            <small class="text-muted">{{ form.descripcion?.length || 0 }}/800</small>
                                        </div>
                                        <textarea class="form-control border-2 shadow-none" rows="4" 
                                            v-model="form.descripcion" maxlength="800"
                                            placeholder="Explique qué se requiere y cuál es el objetivo..." :disabled="saving"></textarea>
                                    </div>

                                    <div class="form-group mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="font-weight-bold text-dark mb-0">Justificación <span class="text-danger">*</span></label>
                                            <small class="text-muted">{{ form.justificacion?.length || 0 }}/500</small>
                                        </div>
                                        <textarea class="form-control border-2 shadow-none" rows="3" 
                                            v-model="form.justificacion" maxlength="500"
                                            placeholder="¿Por qué es necesario realizar este cambio?" :disabled="saving"></textarea>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark mb-2">Proceso Relacionado <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-2 border-right-0"><i class="fas fa-project-diagram"></i></span>
                                            </div>
                                            <input type="text" class="form-control border-2 border-left-0 shadow-none" 
                                                v-model="form.proceso_nombre" readonly placeholder="Busque y seleccione el proceso...">
                                            <div class="input-group-append">
                                                <button v-if="form.proceso_id" class="btn btn-outline-secondary border-2" type="button" @click="clearProceso">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <button class="btn btn-danger font-weight-bold px-4" type="button" @click="openProcesoModal" :disabled="saving">
                                                    <i class="fas fa-search mr-1"></i> Buscar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2: Evaluation -->
                                <div v-show="currentStep === 2" class="step-pane fade-in">
                                    <div class="pane-header mb-4">
                                        <h4 class="text-dark font-weight-bold">Evaluación de Impacto</h4>
                                        <p class="text-muted">Responda los criterios para determinar la complejidad del requerimiento.</p>
                                    </div>

                                    <div v-for="(q, key) in options" :key="key" class="evaluation-card mb-4">
                                        <div class="card border-0 shadow-sm bg-light rounded-lg">
                                            <div class="card-body p-3">
                                                <div class="d-flex mb-3">
                                                    <div class="avatar-sm bg-danger-light text-danger rounded-circle mr-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; min-width: 32px;">
                                                        <span class="font-weight-bold">?</span>
                                                    </div>
                                                    <h6 class="text-dark font-weight-bold mb-0">
                                                        {{ key === 'actividades' ? '¿Cuántas actividades principales componen el requerimiento?' : 
                                                           key === 'areas' ? '¿Cuántas unidades orgánicas participan?' :
                                                           key === 'requisitos' ? '¿Qué nivel de requisitos normativos aplica?' :
                                                           key === 'documentacion' ? '¿Qué nivel de documentación requiere?' :
                                                           '¿Qué nivel de impacto tiene en otros procesos?' }}
                                                    </h6>
                                                </div>
                                                <div class="pl-md-5">
                                                    <div v-for="opt in q" :key="opt.value" class="custom-control custom-radio mb-3">
                                                        <input type="radio" :id="key + opt.value" :value="opt.value" 
                                                            v-model="form['eval_' + key]" class="custom-control-input">
                                                        <label class="custom-control-label text-secondary cursor-pointer" :for="key + opt.value" style="font-size: 0.9rem;">
                                                            <span>{{ opt.value }} :</span> {{ opt.description }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 3: Deployment -->
                                <div v-show="currentStep === 3" class="step-pane fade-in">
                                    <div class="pane-header mb-4">
                                        <h4 class="text-dark font-weight-bold">Finalizar y Enviar</h4>
                                        <p class="text-muted">Siga los pasos finales para formalizar su solicitud.</p>
                                    </div>

                                    <div class="alert alert-danger-light border-0 shadow-sm mb-4 p-4 rounded-lg">
                                        <div class="d-flex">
                                            <i class="fas fa-exclamation-circle fa-2x text-danger mr-4"></i>
                                            <div>
                                                <h6 class="font-weight-bold text-danger mb-1">Pasos Críticos</h6>
                                                <p class="mb-0 text-dark small">1. Guarde su información. 2. Imprima el formato. 3. Fírmelo y escanéelo. 4. Adjúntelo aquí abajo.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-5">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <button class="btn btn-outline-dark btn-block py-3 border-dashed" @click="saveAllData" :disabled="saving">
                                                <i class="fas fa-save mb-2 fa-lg d-block"></i>
                                                <span class="font-weight-bold">1. Guardar Avance</span>
                                            </button>
                                        </div>
                                        <div class="col-md-6">
                                            <button class="btn btn-outline-danger btn-block py-3 border-dashed" @click="printRequerimiento" :disabled="!requerimientoId">
                                                <i class="fas fa-print mb-2 fa-lg d-block"></i>
                                                <span class="font-weight-bold">2. Imprimir Formato</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="doc-upload-section mt-5">
                                        <label class="font-weight-bold text-dark mb-3">3. Adjuntar Formato Firmado</label>
                                        
                                        <div v-if="uploadedFiles.length === 0" 
                                            class="drop-zone-premium shadow-sm border-2 rounded-lg py-5 text-center cursor-pointer"
                                            :class="{ 'bg-light': !requerimientoId, 'bg-white': requerimientoId, 'dragging': isDragging }"
                                            @click="requerimientoId ? openFileDialog() : null"
                                            @dragover.prevent="onDragEnter"
                                            @dragleave.prevent="onDragLeave"
                                            @drop.prevent="onDrop">
                                            
                                            <div v-if="uploadProgress > 0">
                                                <div class="progress mb-2 mx-5" style="height: 10px;">
                                                    <div class="progress-bar bg-danger progress-bar-striped progress-bar-animated" :style="{ width: uploadProgress + '%' }"></div>
                                                </div>
                                                <p class="text-muted mb-0">Subiendo archivo... {{ uploadProgress }}%</p>
                                            </div>
                                            <div v-else>
                                                <i class="fas fa-file-upload fa-3x text-danger mb-3 opacity-50"></i>
                                                <h5 class="font-weight-bold text-dark mb-1">Arraste aquí su documento firmado</h5>
                                                <p class="text-muted small">Haz clic aquí para seleccionar desde tu computadora (PDF máximo 10MB)</p>
                                            </div>
                                            <input type="file" ref="fileInput" class="d-none" accept=".pdf" @change="(e) => handleFileUpload(e, 'signed_requerimiento')">
                                        </div>

                                        <!-- File List -->
                                        <div v-else class="file-card mt-3 animate-up">
                                            <div v-for="(file, index) in uploadedFiles" :key="index" class="card border-0 shadow-sm bg-light mb-2">
                                                <div class="card-body p-3 d-flex align-items-center">
                                                    <div class="icon-circle bg-white shadow-sm text-danger mr-3" style="width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </div>
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <h6 class="mb-0 text-dark font-weight-bold text-truncate">{{ file.name }}</h6>
                                                        <small class="text-muted">Documento Firmado PDF</small>
                                                    </div>
                                                    <div class="actions ml-3">
                                                        <a :href="file.path" target="_blank" class="btn btn-sm btn-link text-primary"><i class="fas fa-eye fa-lg"></i></a>
                                                        <button class="btn btn-sm btn-link text-danger" @click="deleteFile(index)"><i class="fas fa-trash-alt fa-lg"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <!-- Navigation Footer -->
                            <div class="navigation-footer d-flex justify-content-between pt-5 mt-5 border-top">
                                <button v-if="currentStep > 1" class="btn btn-light px-4 py-2 font-weight-bold text-muted border" @click="prevStep">
                                    <i class="fas fa-chevron-left mr-2"></i> Anterior
                                </button>
                                <div v-else></div>

                                <div class="ml-auto">
                                    <button v-if="currentStep < 3" class="btn btn-danger px-5 py-2 font-weight-bold shadow-sm rounded-pill" @click="nextStep">
                                        Siguiente <i class="fas fa-chevron-right ml-2"></i>
                                    </button>
                                    <button v-if="currentStep === 3" class="btn btn-success px-5 py-2 font-weight-bold shadow-sm rounded-pill" 
                                        @click="submitRequerimiento" :disabled="saving || uploadedFiles.length === 0">
                                        <i class="fas fa-paper-plane mr-2"></i> Enviar Requerimiento
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Components -->
    <ModalHijo ref="modalHijoProceso" :fetch-url="procesoFetchUrl" target-id="proceso_id" target-desc="proceso_nombre" @update-target="handleProcesoSelected" />
    <RequerimientoPrintModal ref="printModalRef" :requerimientoId="requerimientoId" v-if="requerimientoId" />
</template>

<style scoped>
.min-vh-75 { min-height: 75vh; }
.cursor-pointer { cursor: pointer; }
.border-2 { border-width: 2px !important; }
.bg-danger-light { background-color: rgba(220, 53, 69, 0.08); }
.alert-danger-light { background-color: #fef2f2; border: 1px solid #fee2e2; }
.border-dashed { border-style: dashed !important; border-width: 2px !important; }

/* Stepper Original UX */
.stepper-item {
    display: flex;
    padding: 1rem;
    margin-bottom: 0.5rem;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    border-left: 4px solid transparent;
}
.stepper-item:hover { background-color: rgba(0,0,0,0.02); }
.stepper-item.active { 
    background-color: white; 
    box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
    border-left-color: #dc3545;
}
.stepper-item.completed .step-counter { background-color: #28a745; color: white; border-color: #28a745; }
.stepper-item.completed .step-name { color: #28a745; }

.step-counter {
    width: 34px; height: 34px; min-width: 34px;
    border-radius: 50%;
    border: 2px solid #dee2e6;
    display: flex; align-items: center; justify-content: center;
    font-weight: bold; font-size: 0.85rem; color: #adb5bd;
    margin-right: 1rem;
    transition: all 0.3s ease;
}
.stepper-item.active .step-counter { border-color: #dc3545; color: #dc3545; background-color: white; transform: scale(1.1); }
.step-name { font-weight: 700; font-size: 0.95rem; color: #6c757d; }
.stepper-item.active .step-name { color: #dc3545; }
.step-desc { color: #adb5bd; line-height: 1.2; display: block; }

/* Content animations */
.fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

/* Upload Area */
.drop-zone-premium { border: 2px dashed #e2e8f0; transition: all 0.3s ease; }
.drop-zone-premium:hover:not(.bg-light) { border-color: #dc3545; background-color: #fff5f5; }
.drop-zone-premium.dragging { background-color: #fcebeb; border-color: #dc3545; }

/* Form improvements */
.form-control-lg { border-radius: 0.6rem; }
.form-control:focus { border-color: #dc3545; box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.1) !important; }
.breadcrumb {
    font-size: 0.85rem;
}
.breadcrumb-item + .breadcrumb-item::before {
    content: "\f105";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    color: #adb5bd;
}
.rounded-lg { border-radius: 0.75rem !important; }
</style>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import { useRouter } from 'vue-router';
import ModalHijo from '@/components/generales/ModalHijo.vue';
import RequerimientoPrintModal from './RequerimientoPrintModal.vue';
import Swal from 'sweetalert2';

const router = useRouter();

const props = defineProps({
    requerimientoId: {
        type: [String, Number],
        default: null
    }
});

const currentStep = ref(1);
const requerimientoId = ref(null);
const procesos = ref([]);
const modalHijoProceso = ref(null);
const printModalRef = ref(null);
const showPrintModal = ref(false);
const loading = ref(true);
const saving = ref(false);
const isDragging = ref(false);
const uploadProgress = ref(0);
const uploadedFiles = ref([]);
const fileInput = ref(null);

const isEditMode = computed(() => !!props.requerimientoId);

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
        { value: 1, description: 'Menos de 5 actividades principales. Proceso lineal y sencillo.' },
        { value: 2, description: '5 a 8 actividades principales. Complejidad media con 1-2 decisiones simples.' },
        { value: 3, description: '9 a 15 actividades principales. Múltiples puntos de decisión y bifurcaciones.' },
        { value: 4, description: 'Más de 15 actividades. Bucles o subprocesos anidados.' },
    ],
    areas: [
        { value: 1, description: '1 unidad orgánica involucrada (interno a una sola área).' },
        { value: 2, description: '2 a 3 unidades orgánicas. Coordinación manejable.' },
        { value: 3, description: '4 a 5 unidades orgánicas. Requiere gestión formal.' },
        { value: 4, description: 'Más de 5 unidades o transversal a toda la organización.' },
    ],
    requisitos: [
        { value: 1, description: 'Sin requisitos normativos directos o solo políticas internas.' },
        { value: 2, description: '1-2 normativas conocidas con requisitos claros.' },
        { value: 3, description: '3+ normativas o regulaciones de alta rigurosidad.' },
        { value: 4, description: 'Marco regulatorio complejo, cambiante o contradictorio.' },
    ],
    documentacion: [
        { value: 1, description: 'Adecuar actividades de un procedimiento o formato simple.' },
        { value: 2, description: 'Elaborar o modificar más de un documento estándar.' },
        { value: 3, description: 'Nuevo manual, procedimientos completos o documentos críticos.' },
        { value: 4, description: 'Paquete documental extenso con aprobación multinivel.' },
    ],
    impacto: [
        { value: 1, description: 'Afecta solo al área que lo ejecuta o grupo reducido.' },
        { value: 2, description: 'Entrada/salida para 1-2 procedimientos de bajo impacto.' },
        { value: 3, description: 'Afecta proceso clave o servicio/producto principal.' },
        { value: 4, description: 'Cambio significativo en operación (clientes, misión, cascadas).' },
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

const getComplejidadBadgeClass = computed(() => {
    switch (complejidad.value.nivel) {
        case 'baja': return 'bg-success';
        case 'media': return 'bg-info';
        case 'alta': return 'bg-warning text-dark';
        case 'muy alta': return 'bg-danger';
        default: return 'bg-secondary';
    }
});

const canSubmit = computed(() => {
    return requerimientoId.value && uploadedFiles.value.length > 0;
});

const procesoFetchUrl = computed(() => route('procesos.buscar'));

const fetchProcesos = async () => {
    try {
        const response = await axios.get(route('procesos.buscar'));
        procesos.value = response.data;
    } catch (error) {
        console.error('Error fetching procesos:', error);
    }
};

const openProcesoModal = () => {
    modalHijoProceso.value.open();
};

const handleProcesoSelected = (data) => {
    form.proceso_id = data.idValue;
    form.proceso_nombre = data.descValue;
};

const clearProceso = () => {
    form.proceso_id = '';
    form.proceso_nombre = '';
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
    if (!requerimientoId.value) return;
    const files = event.dataTransfer.files;
    if (files.length > 0) {
        const fakeEvent = { target: { files: files } };
        handleFileUpload(fakeEvent, 'signed_requerimiento');
    }
};

const goToStep = (step) => {
    if (step >= 1 && step <= 3) {
        currentStep.value = step;
    }
};

const nextStep = () => {
    if (currentStep.value < 3) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const validateStep1 = () => {
    if (!form.asunto || !form.descripcion || !form.justificacion || !form.proceso_id) {
        return { valid: false, message: 'Complete todos los campos obligatorios (*).' };
    }
    return { valid: true };
};

const validateStep2 = () => {
    const evaluationFields = [
        form.eval_actividades,
        form.eval_areas,
        form.eval_requisitos,
        form.eval_documentacion,
        form.eval_impacto
    ];
    if (evaluationFields.some(field => field === null)) {
        return { valid: false, message: 'Responda todas las preguntas de evaluación.' };
    }
    return { valid: true };
};

const saveAllData = async () => {
    const step1 = validateStep1();
    if (!step1.valid) {
        Swal.fire({ icon: 'warning', title: 'Atención', text: step1.message });
        currentStep.value = 1;
        return false;
    }

    const step2 = validateStep2();
    if (!step2.valid) {
        Swal.fire({ icon: 'warning', title: 'Atención', text: step2.message });
        currentStep.value = 2;
        return false;
    }

    saving.value = true;

    try {
        const basicPayload = {
            asunto: form.asunto,
            descripcion: form.descripcion,
            justificacion: form.justificacion,
            proceso_id: form.proceso_id,
        };

        let response;
        if (requerimientoId.value) {
            response = await axios.put(route('requerimientos.update', { id: requerimientoId.value }), basicPayload);
        } else {
            response = await axios.post(route('requerimientos.store'), basicPayload);
        }
        requerimientoId.value = response.data.requerimiento_id;

        const evalPayload = {
            actividades: form.eval_actividades,
            areas: form.eval_areas,
            requisitos: form.eval_requisitos,
            documentacion: form.eval_documentacion,
            impacto: form.eval_impacto,
            complejidad_valor: complejidad.value.valor,
            complejidad_nivel: complejidad.value.nivel,
            from_wizard: true,
        };

        await axios.post(route('requerimiento.grabarEvaluacion', { id: requerimientoId.value }), evalPayload);
        
        Swal.fire({ icon: 'success', title: 'Guardado', text: 'Información guardada correctamente.' });
        return true;
    } catch (error) {
        console.error('Error al guardar:', error);
        Swal.fire({ icon: 'error', title: 'Error', text: error.response?.data?.message || 'Error al procesar la solicitud.' });
        return false;
    } finally {
        saving.value = false;
    }
};

const printRequerimiento = async () => {
    if (printModalRef.value) {
        printModalRef.value.open();
    }
};

const handleFileUpload = async (event, documentType) => {
    const files = event.target.files;
    if (!files.length) return;

    if (!requerimientoId.value) {
        const saved = await saveAllData();
        if (!saved) return;
    }

    for (const file of files) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('document_type', documentType);

        try {
            const response = await axios.post(route('requerimientos.uploadDocument', { id: requerimientoId.value }), formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
                onUploadProgress: (p) => {
                    uploadProgress.value = Math.round((p.loaded * 100) / p.total);
                },
            });
            if (documentType === 'signed_requerimiento') {
                const newFile = {
                    name: response.data.file.name,
                    path: response.data.file.path.startsWith('/storage/') ? response.data.file.path : '/storage/' + response.data.file.path
                };
                uploadedFiles.value.push(newFile);
                form.ruta_archivo_requerimiento = JSON.stringify(uploadedFiles.value);
            }
            setTimeout(() => { uploadProgress.value = 0; }, 1500);
        } catch (error) {
            console.error('Error al subir:', error);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error al subir el archivo.' });
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
        form.ruta_archivo_requerimiento = uploadedFiles.value.length > 0 ? JSON.stringify(uploadedFiles.value) : '';
    } catch (error) {
        console.error('Error al eliminar:', error);
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error al eliminar el archivo.' });
    }
};

const submitRequerimiento = async () => {
    if (!requerimientoId.value) {
        Swal.fire({ icon: 'warning', title: 'Atención', text: 'Primero guarde el requerimiento.' });
        return;
    }
    if (uploadedFiles.value.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Atención', text: 'Adjunte el requerimiento firmado.' });
        return;
    }

    try {
        const response = await axios.post(route('requerimientos.submitForEvaluation', { id: requerimientoId.value }));
        Swal.fire({ icon: 'success', title: 'Enviado', text: response.data.message }).then(() => {
             router.push({ name: 'requerimientos.mine' });
        });
    } catch (error) {
        console.error('Error al enviar:', error);
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error al enviar el requerimiento.' });
    }
};

const fetchRequerimientoDetails = async (id) => {
    try {
        const response = await axios.get(route('requerimientos.show', { id: id }));
        const data = response.data;
        const formData = {
            asunto: data.asunto,
            descripcion: data.descripcion,
            justificacion: data.justificacion,
            proceso_id: data.proceso_id,
            ruta_archivo_requerimiento: data.ruta_archivo_requerimiento || '',
        };
        if (data.proceso_id) {
            const proceso = procesos.value.find(p => p.id === data.proceso_id);
            if (proceso) formData.proceso_nombre = proceso.descripcion;
        }
        if (data.ruta_archivo_requerimiento) {
            try {
                const files = JSON.parse(data.ruta_archivo_requerimiento);
                if (Array.isArray(files)) {
                    uploadedFiles.value = files.map(f => ({
                        name: f.name,
                        path: f.path.startsWith('/storage/') ? f.path : '/storage/' + f.path
                    }));
                }
            } catch (e) {
                if (typeof data.ruta_archivo_requerimiento === 'string') {
                    const name = data.ruta_archivo_requerimiento.split('/').pop();
                    uploadedFiles.value = [{ name, path: '/storage/' + data.ruta_archivo_requerimiento }];
                }
            }
        }
        if (data.evaluacion) {
            form.eval_actividades = data.evaluacion.num_actividades ? Number(data.evaluacion.num_actividades) : null;
            form.eval_areas = data.evaluacion.num_areas ? Number(data.evaluacion.num_areas) : null;
            form.eval_requisitos = data.evaluacion.num_requisitos ? Number(data.evaluacion.num_requisitos) : null;
            form.eval_documentacion = data.evaluacion.nivel_documentacion ? Number(data.evaluacion.nivel_documentacion) : null;
            form.eval_impacto = data.evaluacion.impacto_requerimiento ? Number(data.evaluacion.impacto_requerimiento) : null;
        }
        Object.assign(form, formData);
    } catch (error) {
        console.error('Error details:', error);
        router.push({ name: 'requerimientos.index' });
    }
};

onMounted(async () => {
    loading.value = true;
    await fetchProcesos();
    if (props.requerimientoId) {
        requerimientoId.value = props.requerimientoId;
        await fetchRequerimientoDetails(props.requerimientoId);
    }
    loading.value = false;
});
</script>