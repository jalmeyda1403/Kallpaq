<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light py-2 px-3 rounded">
                <li class="breadcrumb-item"><router-link :to="{ name: 'requerimientos.index' }">Inicio</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ isEditMode ? 'Editar' : 'Crear Nuevo' }} Requerimiento</li>
            </ol>
        </nav>
        <div class="card shadow-sm">
            <div class="card-header bg-danger">
                <h4 class="card-title text-white mb-0">
                    <i class="fas fa-file-alt mr-2"></i>
                    {{ isEditMode ? 'Editar' : 'Generar' }} Requerimiento
                </h4>
            </div>
            <div class="card-body">
                <div v-if="loading" class="text-center p-5">
                    <div class="spinner-border text-danger" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-3 text-muted">Cargando requerimiento...</p>
                </div>
                <div v-else class="row">
                    <!-- Stepper Navigation (Left Column) -->
                    <div class="col-md-3 stepper-sidebar">
                        <div class="stepper-wrapper">
                            <div class="stepper-item" 
                                :class="{ completed: currentStep > 1, active: currentStep === 1 }"
                                @click="goToStep(1)">
                                <div class="step-counter">
                                    <i v-if="currentStep > 1" class="fas fa-check"></i>
                                    <span v-else>1</span>
                                </div>
                                <div class="step-info">
                                    <div class="step-name">Información Básica</div>
                                    <small class="step-desc">Datos generales del requerimiento</small>
                                </div>
                            </div>
                            <div class="stepper-item" 
                                :class="{ completed: currentStep > 2, active: currentStep === 2 }"
                                @click="goToStep(2)">
                                <div class="step-counter">
                                    <i v-if="currentStep > 2" class="fas fa-check"></i>
                                    <span v-else>2</span>
                                </div>
                                <div class="step-info">
                                    <div class="step-name">Evaluación</div>
                                    <small class="step-desc">Complejidad del requerimiento</small>
                                </div>
                            </div>
                            <div class="stepper-item" 
                                :class="{ completed: currentStep > 3, active: currentStep === 3 }"
                                @click="goToStep(3)">
                                <div class="step-counter">
                                    <i v-if="currentStep > 3" class="fas fa-check"></i>
                                    <span v-else>3</span>
                                </div>
                                <div class="step-info">
                                    <div class="step-name">Documentos</div>
                                    <small class="step-desc">Archivos y firma</small>
                                </div>
                            </div>
                        </div>

                        <!-- Complexity Summary (visible when step 2 is completed) -->
                        <div v-if="complejidad.nivel" class="complexity-summary mt-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-3">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-chart-bar mr-2"></i>Resumen
                                    </h6>
                                    <div class="d-flex align-items-center">
                                        <span class="badge" :class="getComplejidadBadgeClass">
                                            {{ complejidad.nivel.toUpperCase() }}
                                        </span>
                                        <span class="ml-2 text-muted small">({{ complejidad.valor }} pts)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stepper Content (Right Column) -->
                    <div class="col-md-9">
                        <div class="bs-stepper-content">
                            <form @submit.prevent="">
                                <!-- Step 1: Basic Information -->
                                <div id="step-basic-info" class="step-content" :class="{ active: currentStep === 1 }">
                                    <div class="step-header mb-4">
                                        <h5 class="text-dark mb-1">
                                            <i class="fas fa-info-circle text-danger mr-2"></i>
                                            Información Básica
                                        </h5>
                                        <p class="text-muted small mb-0">Complete los datos generales del requerimiento.</p>
                                    </div>

                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="font-weight-bold custom-label">Asunto <span class="text-danger">*</span></label>
                                            <small class="text-muted">{{ form.asunto ? form.asunto.length : 0 }}/100</small>
                                        </div>
                                        <input type="text" class="form-control" v-model="form.asunto" 
                                            placeholder="Ingrese el asunto del requerimiento" maxlength="100" required>
                                    </div>

                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="font-weight-bold custom-label">Descripción <span class="text-danger">*</span></label>
                                            <small class="text-muted">{{ form.descripcion ? form.descripcion.length : 0 }}/500</small>
                                        </div>
                                        <textarea class="form-control" v-model="form.descripcion" rows="4"
                                            placeholder="Describa detalladamente el requerimiento..." maxlength="500" required></textarea>
                                    </div>

                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="font-weight-bold custom-label">Justificación <span class="text-danger">*</span></label>
                                            <small class="text-muted">{{ form.justificacion ? form.justificacion.length : 0 }}/500</small>
                                        </div>
                                        <textarea class="form-control" v-model="form.justificacion" rows="4"
                                            placeholder="Indique la justificación del requerimiento..." maxlength="500" required></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Proceso <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" :value="form.proceso_nombre"
                                                readonly placeholder="Seleccione un proceso...">
                                            <div class="input-group-append">
                                                <button class="btn btn-dark" type="button" @click="openProcesoModal">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                <button class="btn btn-danger" type="button" v-if="form.proceso_id" @click="clearProceso">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="step-actions mt-4 pt-3 border-top">
                                        <button class="btn btn-danger" @click="nextStep">
                                            Siguiente <i class="fas fa-arrow-right ml-2"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Step 2: Evaluación de Complejidad -->
                                <div id="step-evaluation" class="step-content" :class="{ active: currentStep === 2 }">
                                    <div class="step-header mb-4">
                                        <h5 class="text-dark mb-1">
                                            <i class="fas fa-tasks text-danger mr-2"></i>
                                            Evaluación de Complejidad
                                        </h5>
                                        <p class="text-muted small mb-0">Seleccione una opción para cada criterio para determinar el nivel de complejidad.</p>
                                    </div>

                                    <!-- Pregunta 1 -->
                                    <div class="evaluation-card">
                                        <div class="evaluation-header">
                                            <span class="evaluation-number">1</span>
                                            <div class="evaluation-title">
                                                <label class="font-weight-bold custom-label mb-0">¿Cuántas actividades principales componen el requerimiento?</label>
                                                <small class="text-muted d-block">Evaluar la cantidad de pasos o tareas clave necesarias.</small>
                                            </div>
                                        </div>
                                        <div class="evaluation-options">
                                            <div class="custom-control custom-radio" v-for="option in options.actividades" :key="'act_' + option.value">
                                                <input type="radio" :id="'actividades_' + option.value"
                                                    :value="option.value" v-model.number="form.eval_actividades"
                                                    class="custom-control-input">
                                                <label class="custom-control-label" :for="'actividades_' + option.value">{{ option.description }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pregunta 2 -->
                                    <div class="evaluation-card">
                                        <div class="evaluation-header">
                                            <span class="evaluation-number">2</span>
                                            <div class="evaluation-title">
                                                <label class="font-weight-bold custom-label mb-0">¿Cuántas unidades orgánicas participan?</label>
                                                <small class="text-muted d-block">Determinar el grado de coordinación institucional necesaria.</small>
                                            </div>
                                        </div>
                                        <div class="evaluation-options">
                                            <div class="custom-control custom-radio" v-for="option in options.areas" :key="'area_' + option.value">
                                                <input type="radio" :id="'areas_' + option.value" :value="option.value"
                                                    v-model.number="form.eval_areas" class="custom-control-input">
                                                <label class="custom-control-label" :for="'areas_' + option.value">{{ option.description }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pregunta 3 -->
                                    <div class="evaluation-card">
                                        <div class="evaluation-header">
                                            <span class="evaluation-number">3</span>
                                            <div class="evaluation-title">
                                                <label class="font-weight-bold custom-label mb-0">¿Qué nivel de requisitos normativos aplica?</label>
                                                <small class="text-muted d-block">Medir la complejidad de normas involucradas.</small>
                                            </div>
                                        </div>
                                        <div class="evaluation-options">
                                            <div class="custom-control custom-radio" v-for="option in options.requisitos" :key="'req_' + option.value">
                                                <input type="radio" :id="'requisitos_' + option.value"
                                                    :value="option.value" v-model.number="form.eval_requisitos"
                                                    class="custom-control-input">
                                                <label class="custom-control-label" :for="'requisitos_' + option.value">{{ option.description }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pregunta 4 -->
                                    <div class="evaluation-card">
                                        <div class="evaluation-header">
                                            <span class="evaluation-number">4</span>
                                            <div class="evaluation-title">
                                                <label class="font-weight-bold custom-label mb-0">¿Qué nivel de documentación requiere?</label>
                                                <small class="text-muted d-block">Evaluar la cantidad de documentos a elaborar o modificar.</small>
                                            </div>
                                        </div>
                                        <div class="evaluation-options">
                                            <div class="custom-control custom-radio" v-for="option in options.documentacion" :key="'doc_' + option.value">
                                                <input type="radio" :id="'documentacion_' + option.value"
                                                    :value="option.value" v-model.number="form.eval_documentacion"
                                                    class="custom-control-input">
                                                <label class="custom-control-label" :for="'documentacion_' + option.value">{{ option.description }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pregunta 5 -->
                                    <div class="evaluation-card">
                                        <div class="evaluation-header">
                                            <span class="evaluation-number">5</span>
                                            <div class="evaluation-title">
                                                <label class="font-weight-bold custom-label mb-0">¿Qué nivel de impacto tiene en otros procesos?</label>
                                                <small class="text-muted d-block">Evalúa la influencia respecto a otros procedimientos.</small>
                                            </div>
                                        </div>
                                        <div class="evaluation-options">
                                            <div class="custom-control custom-radio" v-for="option in options.impacto" :key="'imp_' + option.value">
                                                <input type="radio" :id="'impacto_' + option.value"
                                                    :value="option.value" v-model.number="form.eval_impacto"
                                                    class="custom-control-input">
                                                <label class="custom-control-label" :for="'impacto_' + option.value">{{ option.description }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="step-actions mt-4 pt-3 border-top d-flex justify-content-between">
                                        <button class="btn btn-outline-secondary" @click="prevStep">
                                            <i class="fas fa-arrow-left mr-2"></i> Anterior
                                        </button>
                                        <button class="btn btn-danger" @click="nextStep">
                                            Siguiente <i class="fas fa-arrow-right ml-2"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Step 3: Documents and Files -->
                                <div id="step-documents" class="step-content" :class="{ active: currentStep === 3 }">
                                    <div class="step-header mb-4">
                                        <h5 class="text-dark mb-1">
                                            <i class="fas fa-file-pdf text-danger mr-2"></i>
                                            Documentos y Archivos
                                        </h5>
                                        <p class="text-muted small mb-0">Genere el PDF para firma y adjunte el documento firmado.</p>
                                    </div>

                                    <!-- Print Section -->
                                    <div class="document-card mb-4">
                                        <div class="document-card-header">
                                            <i class="fas fa-print text-primary"></i>
                                            <h6 class="mb-0">Imprimir Requerimiento para Firma</h6>
                                        </div>
                                        <div class="document-card-body">
                                            <p class="text-muted small mb-3">
                                                Genere la versión PDF oficial de su requerimiento. Este documento es necesario para el proceso de firma.
                                            </p>
                                            <button class="btn btn-primary" @click.prevent="printRequerimiento"
                                                :disabled="!requerimientoId">
                                                <i class="fas fa-file-pdf mr-2"></i> Imprimir PDF
                                            </button>
                                            <div v-if="!requerimientoId" class="alert alert-info mt-3 mb-0 p-2 small">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                Primero debe guardar el requerimiento para poder imprimir.
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Upload Section -->
                                    <div class="document-card">
                                        <div class="document-card-header">
                                            <i class="fas fa-upload text-success"></i>
                                            <h6 class="mb-0">Adjuntar Requerimiento Firmado</h6>
                                        </div>
                                        <div class="document-card-body">
                                            <p class="text-muted small mb-3">
                                                Por favor, adjunte aquí el archivo PDF del requerimiento una vez que haya sido firmado y los documentos complementarios de su requerimiento.
                                            </p>

                                            <div class="drop-zone"
                                                @dragenter.prevent="onDragEnter"
                                                @dragleave.prevent="onDragLeave"
                                                @dragover.prevent
                                                @drop.prevent="onDrop"
                                                :class="{ 'drag-over': isDragging, 'disabled': !requerimientoId }"
                                                @click="requerimientoId && openFileDialog()">
                                                <input type="file" ref="fileInput" class="d-none"
                                                    @change="handleFileUpload($event, 'signed_requerimiento')"
                                                    accept=".pdf" multiple />
                                                <div class="text-center">
                                                    <i class="fas fa-cloud-upload-alt fa-3x" :class="requerimientoId ? 'text-muted' : 'text-secondary'"></i>
                                                    <p class="mb-0 mt-2">Arrastra y suelta el archivo aquí, o haz clic para seleccionar.</p>
                                                    <small class="text-muted">(Máx. 5 MB. Formato permitido: PDF)</small>
                                                </div>
                                            </div>

                                            <div v-if="uploadProgress > 0" class="progress mt-3" style="height: 20px;">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger"
                                                    role="progressbar"
                                                    :style="{ width: uploadProgress + '%' }"
                                                    :aria-valuenow="uploadProgress"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                    {{ uploadProgress }}%
                                                </div>
                                            </div>

                                            <div v-if="uploadedFiles.length > 0" class="mt-3">
                                                <ul class="list-group">
                                                    <li v-for="(file, index) in uploadedFiles" :key="index" 
                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <i class="fas fa-file-pdf text-danger mr-2"></i>
                                                            <a :href="file.path" target="_blank">{{ file.name }}</a>
                                                        </div>
                                                        <button class="btn btn-sm btn-outline-danger" @click="deleteFile(index)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div v-else-if="!uploadProgress && requerimientoId" class="alert alert-warning mt-3 mb-0 p-2 small">
                                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                                Documento pendiente de adjuntar.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="step-actions mt-4 pt-3 border-top d-flex justify-content-between">
                                        <button class="btn btn-outline-secondary" @click="prevStep">
                                            <i class="fas fa-arrow-left mr-2"></i> Anterior
                                        </button>
                                        <div>
                                            <button class="btn btn-dark mr-2" @click.prevent="saveAllData" :disabled="saving">
                                                <i class="fas fa-save mr-2"></i>
                                                {{ saving ? 'Guardando...' : 'Guardar' }}
                                            </button>
                                            <button class="btn btn-danger" @click.prevent="submitRequerimiento"
                                                :disabled="!canSubmit || saving">
                                                <i class="fas fa-paper-plane mr-2"></i> Enviar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <ModalHijo ref="modalHijoProceso" :fetch-url="procesoFetchUrl" target-id="proceso_id" target-desc="proceso_nombre"
        @update-target="handleProcesoSelected"></ModalHijo>
    
    <!-- Print Modal -->
    <RequerimientoPrintModal 
        ref="printModalRef"
        :requerimiento="printRequerimientoData"
        :complejidad="complejidad"
        @close="showPrintModal = false"
    />
</template>

<style scoped>
/* Stepper Sidebar */
.stepper-sidebar {
    background-color: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1.5rem;
}

.stepper-wrapper {
    position: relative;
}

.stepper-item {
    position: relative;
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    margin-bottom: 0.5rem;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.stepper-item:hover {
    background-color: #e9ecef;
}

.stepper-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 2rem;
    top: 3.5rem;
    width: 2px;
    height: calc(100% - 1rem);
    background-color: #dee2e6;
}

.step-counter {
    width: 36px;
    height: 36px;
    min-width: 36px;
    border-radius: 50%;
    background-color: #dee2e6;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
    z-index: 1;
    transition: all 0.3s ease;
}

.step-info {
    margin-left: 1rem;
}

.step-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
    transition: all 0.3s ease;
}

.step-desc {
    font-size: 0.75rem;
    color: #6c757d;
}

.stepper-item.active .step-counter {
    background-color: #dc3545;
    color: white;
    box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.2);
}

.stepper-item.active .step-name {
    color: #dc3545;
}

.stepper-item.completed .step-counter {
    background-color: #28a745;
    color: white;
}

.stepper-item.completed:not(:last-child)::before {
    background-color: #28a745;
}

.stepper-item.completed .step-name {
    color: #28a745;
}

/* Complexity Summary */
.complexity-summary .badge {
    font-size: 0.8rem;
    padding: 0.5rem 0.75rem;
}

/* Step Content */
.step-content {
    display: none;
    animation: fadeIn 0.3s ease;
}

.step-content.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.step-header {
    border-bottom: 2px solid #f8f9fa;
    padding-bottom: 1rem;
}

/* Custom Label Style */
.custom-label {
    font-size: 0.85rem !important;
    font-weight: 600 !important;
    color: #495057 !important;
}

/* Form Controls */
.form-control:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
}

/* Evaluation Cards */
.evaluation-card {
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    padding: 1.25rem;
    margin-bottom: 1rem;
    background-color: #fff;
    transition: all 0.2s ease;
}

.evaluation-card:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.evaluation-header {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.evaluation-number {
    width: 28px;
    height: 28px;
    min-width: 28px;
    border-radius: 50%;
    background-color: #dc3545;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.8rem;
    margin-right: 0.75rem;
}

.evaluation-title {
    flex: 1;
}

.evaluation-options {
    padding-left: 2.5rem;
}

.evaluation-options .custom-control {
    margin-bottom: 0.5rem;
}

.evaluation-options .custom-control-label {
    font-size: 0.8rem;
    color: #495057;
    line-height: 1.5;
    cursor: pointer;
}

.custom-control-input:checked ~ .custom-control-label::before {
    background-color: #dc3545;
    border-color: #dc3545;
}

/* Document Cards */
.document-card {
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    overflow: hidden;
}

.document-card-header {
    background-color: #f8f9fa;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    border-bottom: 1px solid #e9ecef;
}

.document-card-header i {
    font-size: 1.25rem;
}

.document-card-body {
    padding: 1.25rem;
}

/* Drop Zone */
.drop-zone {
    border: 2px dashed #ced4da;
    border-radius: 0.5rem;
    padding: 2rem;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
}

.drop-zone:hover:not(.disabled) {
    border-color: #dc3545;
    background-color: #fff5f5;
}

.drop-zone.drag-over {
    background-color: #fff5f5;
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
}

.drop-zone.disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

/* Buttons */
.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover:not(:disabled) {
    background-color: #c82333;
    border-color: #bd2130;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    color: white;
}

/* Step Actions */
.step-actions {
    background-color: #f8f9fa;
    margin: 0 -1rem -1rem -1rem;
    padding: 1rem !important;
    border-radius: 0 0 0.5rem 0.5rem;
}
</style>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import { useRouter } from 'vue-router';
import ModalHijo from '@/components/generales/ModalHijo.vue';
import RequerimientoPrintModal from './RequerimientoPrintModal.vue';

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
        case 'baja': return 'badge-success';
        case 'media': return 'badge-info';
        case 'alta': return 'badge-warning';
        case 'muy alta': return 'badge-danger';
        default: return 'badge-secondary';
    }
});

const canSubmit = computed(() => {
    return requerimientoId.value && uploadedFiles.value.length > 0;
});

const procesoFetchUrl = computed(() => route('procesos.buscar'));

const printRequerimientoData = computed(() => ({
    id: requerimientoId.value,
    asunto: form.asunto,
    descripcion: form.descripcion,
    justificacion: form.justificacion,
    proceso_nombre: form.proceso_nombre,
    eval_actividades: form.eval_actividades,
    eval_areas: form.eval_areas,
    eval_requisitos: form.eval_requisitos,
    eval_documentacion: form.eval_documentacion,
    eval_impacto: form.eval_impacto,
    fecha_creacion: new Date().toISOString(),
    estado: 'Borrador'
}));

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

// Navigation functions - free navigation without saving
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

// Validate all steps
const validateStep1 = () => {
    if (!form.asunto || !form.descripcion || !form.justificacion || !form.proceso_id) {
        return { valid: false, message: 'Complete todos los campos de Información Básica.' };
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
        return { valid: false, message: 'Complete todas las preguntas de evaluación.' };
    }
    return { valid: true };
};

// Save all data at once
const saveAllData = async () => {
    // Validate Step 1
    const step1 = validateStep1();
    if (!step1.valid) {
        alert(step1.message);
        currentStep.value = 1;
        return false;
    }

    // Validate Step 2
    const step2 = validateStep2();
    if (!step2.valid) {
        alert(step2.message);
        currentStep.value = 2;
        return false;
    }

    saving.value = true;

    try {
        // Save basic info
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

        // Save evaluation
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
        
        alert('Requerimiento guardado con éxito.');
        return true;
    } catch (error) {
        console.error('Error al guardar:', error);
        alert(error.response?.data?.message || 'Error al guardar el requerimiento.');
        return false;
    } finally {
        saving.value = false;
    }
};

const printRequerimiento = async () => {
    // Abrir el modal de impresión
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
                form.ruta_archivo_requerimiento = JSON.stringify(uploadedFiles.value);
            }
            setTimeout(() => {
                uploadProgress.value = 0;
            }, 1500);
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
        form.ruta_archivo_requerimiento = uploadedFiles.value.length > 0 ? JSON.stringify(uploadedFiles.value) : '';
    } catch (error) {
        console.error('Error al eliminar el documento:', error);
        alert('Error al eliminar el documento.');
    }
};

const submitRequerimiento = async () => {
    if (!requerimientoId.value) {
        alert('Primero debe guardar el requerimiento.');
        return;
    }
    if (uploadedFiles.value.length === 0) {
        alert('Debe adjuntar el requerimiento firmado antes de enviar.');
        return;
    }

    try {
        const response = await axios.post(route('requerimientos.submitForEvaluation', { id: requerimientoId.value }));
        alert(response.data.message);
        router.push({ name: 'requerimientos.index' });
    } catch (error) {
        console.error('Error al enviar el requerimiento:', error);
        alert('Error al enviar el requerimiento.');
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
                if (typeof data.ruta_archivo_requerimiento === 'string') {
                    const fileName = data.ruta_archivo_requerimiento.split('/').pop();
                    const filePath = data.ruta_archivo_requerimiento.startsWith('/storage/') ? data.ruta_archivo_requerimiento : '/storage/' + data.ruta_archivo_requerimiento;
                    uploadedFiles.value = [{ name: fileName, path: filePath }];
                }
            }
        }

        if (data.evaluacion) {
            formData.eval_actividades = data.evaluacion.num_actividades ? Number(data.evaluacion.num_actividades) : null;
            formData.eval_areas = data.evaluacion.num_areas ? Number(data.evaluacion.num_areas) : null;
            formData.eval_requisitos = data.evaluacion.num_requisitos ? Number(data.evaluacion.num_requisitos) : null;
            formData.eval_documentacion = data.evaluacion.nivel_documentacion ? Number(data.evaluacion.nivel_documentacion) : null;
            formData.eval_impacto = data.evaluacion.impacto_requerimiento ? Number(data.evaluacion.impacto_requerimiento) : null;
        }

        Object.assign(form, formData);
    } catch (error) {
        console.error('Error fetching requerimiento details:', error);
        alert('Error al cargar los detalles del requerimiento.');
        router.push({ name: 'requerimientos.index' });
    }
};
</script>