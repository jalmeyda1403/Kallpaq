<template>
    <div class="container-fluid py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" v-if="!embedded">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link to="/home" class="text-danger font-weight-bold">Inicio</router-link></li>
                <li class="breadcrumb-item"><router-link :to="{ name: 'hallazgos.mine.vue' }" class="text-danger font-weight-bold">Mis Solicitudes</router-link></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Gestión del Plan de Acción</li>
            </ol>
        </nav>

        <div class="animate__animated animate__fadeIn">
            <!-- MAIN CARD (Wizard Structure) -->
            <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                <!-- HEADER -->
                <div class="card-header bg-danger py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <!-- Circular Icon -->
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 40px; height: 40px; min-width: 40px;">
                                    <i class="fas fa-folder-open text-danger" style="font-size: 0.9rem;"></i>
                                </div>
                                <div>
                                    <h5 class="font-weight-bold text-white mb-0">
                                        Expediente: {{ hallazgo.hallazgo_cod || 'Cargando...' }}
                                    </h5>
                                    <p class="text-white mb-0" style="opacity: 0.9; font-size: 0.75rem;">
                                        Gestionar Plan de Acción - {{ hallazgo.procesos?.[0]?.proceso_nombre || 'Proceso' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-right mt-2 mt-md-0">
                            <router-link v-if="!embedded" :to="{ name: 'hallazgos.mine.vue' }" class="btn btn-link text-white text-decoration-none mr-3 px-0 btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a Bandeja
                            </router-link>
                        </div>
                    </div>
                </div>

                <!-- BODY -->
                <div class="card-body p-0">
                    <div class="row no-gutters">
                        <!-- SIDEBAR (Left) -->
                        <div class="col-md-3 bg-light border-right min-vh-75">
                            <div class="p-4">
                                <div class="stepper-wrapper">
                                    <!-- Step 1: Análisis de Causa -->
                                    <div class="stepper-item" :class="{ completed: currentStep > 1, active: currentStep === 1 }"
                                        @click="goToStep(1)">
                                        <div class="step-counter">
                                            <i v-if="currentStep > 1" class="fas fa-check"></i>
                                            <span v-else>1</span>
                                        </div>
                                        <div class="step-info">
                                            <div class="step-name">Análisis de Causa</div>
                                            <small class="step-desc">Identificar raíz del problema</small>
                                        </div>
                                    </div>

                                    <!-- Step 2: Plan de Acción -->
                                    <div class="stepper-item" :class="{ completed: currentStep > 2, active: currentStep === 2 }"
                                        @click="goToStep(2)">
                                        <div class="step-counter">
                                            <i v-if="currentStep > 2" class="fas fa-check"></i>
                                            <span v-else>2</span>
                                        </div>
                                        <div class="step-info">
                                            <div class="step-name">Plan de Acción</div>
                                            <small class="step-desc">Definir acciones correctivas</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Step 3: Revisión e Impresión -->
                                    <div class="stepper-item" :class="{ completed: currentStep > 3, active: currentStep === 3 }"
                                        @click="goToStep(3)">
                                        <div class="step-counter">
                                            <i v-if="currentStep > 3" class="fas fa-check"></i>
                                            <span v-else>3</span>
                                        </div>
                                        <div class="step-info">
                                            <div class="step-name">Revisión e Impresión</div>
                                            <small class="step-desc">Generar documento PDF</small>
                                        </div>
                                    </div>

                                    <!-- Step 4: Firma y Envío -->
                                    <div class="stepper-item" :class="{ completed: hallazgo.hallazgo_estado === 'aprobado', active: currentStep === 4 }"
                                        @click="goToStep(4)">
                                        <div class="step-counter">
                                            <i v-if="hallazgo.hallazgo_estado === 'aprobado'" class="fas fa-check"></i>
                                            <span v-else>4</span>
                                        </div>
                                        <div class="step-info">
                                            <div class="step-name">Firma y Envío</div>
                                            <small class="step-desc">Adjuntar y finalizar</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Widget -->
                                <div class="mt-5 fade-in" v-if="hallazgo.hallazgo_estado">
                                    <div class="card border-0 shadow-sm bg-white p-3 rounded-lg text-center">
                                        <h6 class="text-uppercase text-muted extra-small font-weight-bold mb-2">Estado del Hallazgo</h6>
                                        <span class="badge badge-pill badge-block p-2" 
                                            :class="getStatusBadgeClass(hallazgo.hallazgo_estado)">
                                            {{ hallazgo.hallazgo_estado.toUpperCase() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CONTENT (Right) -->
                        <div class="col-md-9 bg-white">
                            <div class="p-4 p-lg-5">
                                <!-- STEP 1: Análisis de Causa Raíz -->
                                <div v-show="currentStep === 1" class="step-pane fade-in">
                                    <div class="pane-header mb-4 border-bottom pb-3">
                                        <h4 class="text-dark font-weight-bold">1. Análisis de Causa Raíz</h4>
                                        <p class="text-muted">Describa la metodología utilizada y determine la causa raíz del hallazgo.</p>
                                    </div>

                                    <div class="card shadow-none border bg-light mb-4 rounded-lg">
                                        <div class="card-body p-4">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0 text-danger font-weight-bold">Detalle del Análisis</h6>
                                                <button :disabled="!hallazgoStore.accionesPermitidas"
                                                    class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm font-weight-bold"
                                                    @click="causaRaizRef.enableEdit()"
                                                    :title="!hallazgoStore.accionesPermitidas ? 'No se puede editar en este estado' : ''">
                                                    <i class="fas fa-edit mr-1"></i> Editar Análisis
                                                </button>
                                            </div>
                                            <CausaRaiz ref="causaRaizRef" :hallazgoId="hallazgoId" :embedded="true" :hideTitle="true" :hideEditButton="true" />
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-3 border-top text-right">
                                        <button class="btn btn-outline-danger px-4 shadow-sm rounded-pill font-weight-bold" @click="goToStep(2)">
                                            Siguiente: Plan de Acción <i class="fas fa-arrow-right ml-2"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- STEP 2: Plan de Acción -->
                                <div v-show="currentStep === 2" class="step-pane fade-in">
                                    <div class="pane-header mb-4 border-bottom pb-3">
                                        <h4 class="text-dark font-weight-bold">2. Plan de Acción</h4>
                                        <p class="text-muted">Defina las acciones correctivas necesarias para mitigar la causa identificada.</p>
                                    </div>

                                    <!-- Causa Raíz Reference (Read Only) -->
                                    <div class="card bg-white border shadow-sm rounded-lg mb-4">
                                        <div class="card-body py-3 px-3 border-left-danger">
                                            <h6 class="font-weight-bold text-dark mb-2">
                                                <i class="fas fa-search text-danger mr-2"></i>Referencia: Causa Raíz Identificada
                                            </h6>
                                            <p class="mb-0 text-secondary" style="white-space: pre-wrap; font-size: 0.9rem;">
                                                {{ causaRaiz?.hc_resultado || 'No se ha registrado un análisis de causa raíz. Por favor complete el paso anterior.' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="card shadow-none border rounded-lg">
                                        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                                            <h6 class="mb-0 text-danger font-weight-bold">Listado de Acciones</h6>
                                            <button :disabled="!hallazgoStore.accionesPermitidas"
                                                class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm font-weight-bold ml-auto" @click="openModal()"
                                                :title="!hallazgoStore.accionesPermitidas ? 'No se pueden crear acciones en este estado' : ''">
                                                <i class="fas fa-plus mr-1"></i> Nueva Acción
                                            </button>
                                        </div>
                                        <div class="card-body p-0">
                                            <div v-if="acciones.length === 0" class="text-center py-5 text-muted">
                                                <i class="fas fa-clipboard-list fa-3x mb-3 opacity-50"></i>
                                                <p class="mb-2 font-weight-bold">No hay acciones registradas</p>
                                                <small>Defina las acciones necesarias para mitigar la causa raíz mostrada arriba.</small>
                                            </div>

                                            <DataTable v-else :value="sortedAcciones" responsiveLayout="scroll" :loading="isLoading && !isPageLoading"
                                                class="p-datatable-sm" stripedRows paginator :rows="5" :rowsPerPageOptions="[5, 10, 20]">
                                                <Column field="accion_cod" header="Código" style="width: 10%;">
                                                    <template #body="{ data }">
                                                        <span class="font-weight-bold text-dark small">{{ data.accion_cod }}</span>
                                                    </template>
                                                </Column>
                                                <Column field="accion_tipo" header="Tipo" style="width: 15%;">
                                                    <template #body="{ data }">
                                                        <span class="badge badge-light border text-uppercase small">{{ data.accion_tipo || 'N/A' }}</span>
                                                    </template>
                                                </Column>
                                                <Column field="accion_descripcion" header="Descripción" style="width: 35%;">
                                                    <template #body="{ data }">
                                                        <div class="text-truncate-multiline small" style="max-height: 3.6em; overflow: hidden;">
                                                            {{ data.accion_descripcion }}
                                                        </div>
                                                    </template>
                                                </Column>
                                                <Column field="accion_responsable" header="Responsable" style="width: 15%;">
                                                    <template #body="{ data }"><span class="small">{{ data.accion_responsable }}</span></template>
                                                </Column>
                                                <Column header="Vencimiento" style="width: 15%;">
                                                    <template #body="{ data }">
                                                        <span class="small" :class="{ 'text-danger font-weight-bold': isFechaVencida(data.accion_fecha_fin_planificada) }">
                                                            {{ formatDate(data.accion_fecha_fin_reprogramada || data.accion_fecha_fin_planificada) }}
                                                        </span>
                                                    </template>
                                                </Column>
                                                <Column field="accion_estado" header="Estado" style="width: 10%;">
                                                    <template #body="{ data }">
                                                        <span :class="getEstadoBadgeClass(data.accion_estado) + ' small'">{{ data.accion_estado }}</span>
                                                    </template>
                                                </Column>
                                                <Column header="" :exportable="false" style="width: 15%; text-align: right;">
                                                    <template #body="{ data }">
                                                        <button @click.prevent="canEditActions && openModal(data)" 
                                                                class="btn btn-xs btn-link p-0 mr-2" 
                                                                :class="canEditActions ? 'text-warning' : 'text-muted'"
                                                                :style="!canEditActions ? 'cursor: not-allowed;' : ''"
                                                                :disabled="!canEditActions"
                                                                title="Editar">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </button>
                                                        <button @click.prevent="canEditActions && confirmDelete(data)" 
                                                                class="btn btn-xs btn-link p-0" 
                                                                :class="canEditActions ? 'text-danger' : 'text-muted'"
                                                                :style="!canEditActions ? 'cursor: not-allowed;' : ''"
                                                                :disabled="!canEditActions"
                                                                title="Eliminar">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </template>
                                                </Column>
                                            </DataTable>
                                        </div>
                                    </div>

                                    <div class="step-actions mt-5 pt-3 border-top d-flex justify-content-between">
                                        <button class="btn btn-light px-4 py-2 font-weight-bold text-muted border rounded-pill" @click="goToStep(1)">
                                            <i class="fas fa-chevron-left mr-2"></i> Anterior
                                        </button>
                                        <button class="btn btn-outline-danger px-4 py-2 font-weight-bold rounded-pill" @click="goToStep(3)">
                                            Siguiente: Impresión <i class="fas fa-arrow-right ml-2"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- STEP 3: Revisión e Impresión -->
                                <div v-show="currentStep === 3" class="step-pane fade-in">
                                    <div class="pane-header mb-4 border-bottom pb-3">
                                        <h4 class="text-dark font-weight-bold">3. Revisión e Impresión</h4>
                                        <p class="text-muted">Genere la versión PDF oficial del plan de acción para su firma y aprobación.</p>
                                    </div>

                                    <div class="card shadow-sm border-0 mb-4 bg-light rounded-lg text-center py-5">
                                        <div class="card-body">
                                            <div class="mb-4">
                                                <div class="bg-white rounded-circle shadow-sm d-inline-flex p-4">
                                                    <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                                </div>
                                            </div>
                                            <h5 class="font-weight-bold mb-2 text-dark">Imprimir Plan de Acción</h5>
                                            <p class="text-muted mb-4 px-5 mx-auto" style="max-width: 600px;">
                                                Este documento consolidado incluye el análisis de causa raíz y las acciones planificadas. 
                                                Debe ser firmado por el responsable.
                                            </p>
                                            <button class="btn btn-danger px-5 py-2 shadow-sm rounded-pill font-weight-bold" @click="openPrintModal">
                                                <i class="fas fa-print mr-2"></i> Generar PDF
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="step-actions mt-5 pt-3 border-top d-flex justify-content-between">
                                        <button class="btn btn-light px-4 py-2 font-weight-bold text-muted border rounded-pill" @click="goToStep(2)">
                                            <i class="fas fa-chevron-left mr-2"></i> Anterior
                                        </button>
                                        <button class="btn btn-outline-danger px-4 py-2 font-weight-bold rounded-pill" @click="goToStep(4)">
                                            Siguiente: Firma y Envío <i class="fas fa-arrow-right ml-2"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- STEP 4: Firma y Envío -->
                                <div v-show="currentStep === 4" class="step-pane fade-in">
                                    <div class="pane-header mb-4 border-bottom pb-3">
                                        <h4 class="text-dark font-weight-bold">4. Firma y Envío</h4>
                                        <p class="text-muted">Adjunte el plan de acción firmado y finalice el proceso.</p>
                                    </div>

                                    <div class="doc-upload-section">
                                        <div v-if="hallazgo.ruta_plan_accion" class="mb-4 animate__animated animate__fadeInDown">
                                            <div class="card border-0 shadow-sm bg-success-light">
                                                <div class="card-body p-3 d-flex align-items-center">
                                                    <div class="icon-circle bg-success text-white mr-3 shadow-sm">
                                                        <i class="fas fa-check"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0 font-weight-bold text-success-dark">Documento Cargado Exitosamente</h6>
                                                        <a :href="getAssetUrl(hallazgo.ruta_plan_accion)" target="_blank" class="text-success small text-decoration-underline">
                                                            Ver Plan de Acción Firmado.pdf
                                                        </a>
                                                    </div>
                                                    <button class="btn btn-sm btn-white text-danger font-weight-bold shadow-sm" 
                                                        @click="$refs.fileInput.click()"
                                                        v-if="['creado', 'evaluado'].includes(hallazgo.hallazgo_estado)">
                                                        <i class="fas fa-sync-alt mr-1"></i> Reemplazar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="drop-zone-premium shadow-sm border-2 rounded-lg py-5 text-center bg-white" 
                                             @click="triggerFileInput" 
                                             v-if="['creado', 'evaluado'].includes(hallazgo.hallazgo_estado)"
                                             :class="{'dragging': isDragging}"
                                             @dragover.prevent="isDragging = true" 
                                             @dragleave.prevent="isDragging = false"
                                             @drop.prevent="handleFileDrop">
                                            
                                            <input type="file" ref="fileInput" class="d-none" accept=".pdf" @change="handleFileSelect">
                                            
                                            <div v-if="uploading">
                                                 <div class="spinner-border text-danger" role="status"></div>
                                                 <p class="mt-2 text-muted font-weight-bold">Subiendo archivo...</p>
                                            </div>
                                            <div v-else>
                                                <i class="fas fa-cloud-upload-alt fa-3x text-danger mb-3 opacity-50"></i>
                                                <h5 class="font-weight-bold text-dark mb-1">Arrastre aquí su documento firmado</h5>
                                                <p class="text-muted small">o haga clic para seleccionar desde tu computadora (PDF máximo 10MB)</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="step-actions mt-5 pt-3 border-top d-flex justify-content-between align-items-center">
                                        <button class="btn btn-light px-4 py-2 font-weight-bold text-muted border rounded-pill" @click="goToStep(3)">
                                            <i class="fas fa-chevron-left mr-2"></i> Anterior
                                        </button>
                                        
                                        <div class="text-right">
                                            <span v-if="hallazgo.hallazgo_estado === 'aprobado'" class="text-success mr-3 font-weight-bold small">
                                                <i class="fas fa-check-circle"></i> ESTADO: APROBADO
                                            </span>
                                            <button class="btn btn-success px-5 py-2 font-weight-bold shadow rounded-pill" 
                                                    @click="enviarPlan" 
                                                    :disabled="!hallazgo.ruta_plan_accion || uploading || hallazgo.hallazgo_estado === 'aprobado'">
                                                <i class="fas fa-paper-plane mr-2"></i> {{ hallazgo.hallazgo_estado === 'aprobado' ? 'Plan Enviado' : 'Enviar Plan' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modals -->
            <AccionesReprogramarForm @accion-gestionada="refreshAcciones" />
            <AccionesForm :show="showActionModal" :actionData="selectedAction" :hallazgoId="hallazgoId"
                :procesos="hallazgo.procesos" @close="closeModal" @saved="onActionSaved" />
            <AccionesAvanceForm :show="showAvanceModal" :actionData="selectedAvanceAction" @close="closeAvanceModal"
                @saved="onAvanceSaved" />
            <AccionPrintModal ref="accionPrintModalRef" />
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { storeToRefs } from 'pinia';
import Swal from 'sweetalert2';
import axios from 'axios';
import { route } from 'ziggy-js';

// Components
import AccionesReprogramarForm from './AccionesReprogramarForm.vue';
import AccionesForm from './AccionesForm.vue';
import AccionesAvanceForm from './AccionesAvanceForm.vue';
import CausaRaiz from './CausaRaiz.vue';
import AccionPrintModal from './AccionPrintModal.vue';
import { useHallazgoStore } from '@/stores/hallazgoStore';

const props = defineProps({
    hallazgoId: { type: [Number, String], required: true },
    embedded: { type: Boolean, default: false }
});

const hallazgoStore = useHallazgoStore();
const { todasLasAcciones: acciones, hallazgoForm: hallazgo, causaRaiz, loading: isLoading } = storeToRefs(hallazgoStore);

const sortedAcciones = computed(() => {
    if (!acciones.value) return [];
    return [...acciones.value].sort((a, b) => {
        const typeA = (a.accion_tipo || '').toLowerCase();
        const typeB = (b.accion_tipo || '').toLowerCase();
        // 'corrección' first
        if (typeA.includes('corrección') && !typeB.includes('corrección')) return -1;
        if (!typeA.includes('corrección') && typeB.includes('corrección')) return 1;
        return 0;
    });
});

const isPageLoading = ref(true);
const currentStep = ref(1);
const uploading = ref(false);
const isDragging = ref(false);

const canEditActions = computed(() => {
    return ['creado', 'evaluado'].includes(hallazgo.value.hallazgo_estado);
});

const causaRaizRef = ref(null);
const accionPrintModalRef = ref(null);
const fileInput = ref(null);



// Modal states
const showActionModal = ref(false);
const showAvanceModal = ref(false);
const selectedAction = ref(null);
const selectedAvanceAction = ref(null);

// Helper Methods
const getAssetUrl = (path) => `/storage/${path}`;
const formatDate = (date) => date ? new Date(date).toLocaleDateString() : '';

const isFechaVencida = (fecha) => {
    if (!fecha) return false;
    return new Date(fecha) < new Date().setHours(0,0,0,0);
};

const getEstadoBadgeClass = (estado) => {
    const map = {
        'programada': 'badge-primary',
        'en proceso': 'badge-info',
        'implementada': 'badge-success',
        'finalizada': 'badge-success',
        'desestimada': 'badge-secondary',
        'reprogramada': 'badge-warning'
    };
    return 'badge ' + (map[estado] || 'badge-light');
};

const getStatusBadgeClass = (estado) => {
     const map = {
        'abierto': 'bg-info text-white',
        'cerrado': 'bg-success text-white',
        'en_proceso': 'bg-primary text-white',
        'plan_enviado': 'bg-warning text-dark',
        'aprobado': 'bg-success text-white',
        'evaluado': 'bg-info text-white'
    };
    return map[estado] || 'bg-secondary text-white';
};

// Actions
const goToStep = (step) => {
    currentStep.value = step;
};

const openPrintModal = () => {
    if(accionPrintModalRef.value) {
        accionPrintModalRef.value.open(
            hallazgo.value,
            acciones.value,
            hallazgoStore.causaRaiz
        );
    }
};

const triggerFileInput = () => {
    if (!['creado', 'evaluado'].includes(hallazgo.value.hallazgo_estado)) return;
    fileInput.value.click();
};

// File Upload
const handleFileSelect = (event) => uploadFile(event.target.files[0]);
const handleFileDrop = (event) => {
    if (!['creado', 'evaluado'].includes(hallazgo.value.hallazgo_estado)) return;
    isDragging.value = false;
    uploadFile(event.dataTransfer.files[0]);
}

const uploadFile = async (file) => {
    if (!file) return;
    if (file.type !== 'application/pdf') {
        Swal.fire({
            title: 'Formato inválido',
            text: 'Solo se permiten archivos en formato PDF.',
            icon: 'error',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Entendido'
        });
        return;
    }
    
    uploading.value = true;
    const formData = new FormData();
    formData.append('file', file);
    
    try {
        const response = await axios.post(route('hallazgo.plan-accion.upload', { hallazgo: props.hallazgoId }), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        hallazgo.value.ruta_plan_accion = response.data.path;
        Swal.fire({
            title: 'Éxito', text: 'Archivo subido correctamente.', icon: 'success', toast: true, position: 'top-end', timer: 3000, showConfirmButton: false
        });
    } catch (error) {
        Swal.fire({
            title: 'Error de carga',
            text: 'No se pudo subir el archivo. Intente nuevamente.',
            icon: 'error',
            confirmButtonColor: '#dc3545'
        });
        console.error(error);
    } finally {
        uploading.value = false;
    }
};

const enviarPlan = async () => {
    try {
        const result = await Swal.fire({
            title: '¿Confirmar Envío?',
            text: "El plan de acción será enviado para revisión. Una vez enviado (Aprobado), no podrá modificarlo hasta recibir respuesta.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, Enviar Plan',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            const response = await axios.post(route('hallazgo.plan-accion.enviar', { hallazgo: props.hallazgoId }));
            hallazgo.value.hallazgo_estado = response.data.estado;
            Swal.fire({
                title: '¡Enviado!',
                text: 'El plan de acción ha sido enviado y aprobado correctamente.',
                icon: 'success',
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Aceptar'
            });
        }
    } catch (error) {
        Swal.fire({
            title: 'Error',
            text: error.response?.data?.error || 'Ocurrió un error al enviar el plan.',
            icon: 'error',
            confirmButtonColor: '#dc3545'
        });
    }
};

// CRUD Acciones
const openModal = (accion = null) => { selectedAction.value = accion; showActionModal.value = true; };
const closeModal = () => { showActionModal.value = false; selectedAction.value = null; };
const onActionSaved = async () => await refreshAcciones();
const refreshAcciones = async () => await hallazgoStore.fetchTodasLasAcciones(props.hallazgoId);

const closeAvanceModal = () => { showAvanceModal.value = false; selectedAvanceAction.value = null; };
const onAvanceSaved = async () => await refreshAcciones();

const confirmDelete = (accion) => {
    Swal.fire({
        title: '¿Eliminar?', text: "Esta acción no se puede deshacer.", icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Sí, eliminar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await hallazgoStore.deleteAccion(accion.id);
                await refreshAcciones();
                Swal.fire({
                    title: 'Eliminado',
                    text: 'La acción ha sido eliminada correctamente.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (e) {
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo eliminar la acción.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            }
        }
    });
};

onMounted(async () => {
    try {
        await Promise.all([
            hallazgoStore.fetchHallazgo(props.hallazgoId),
            hallazgoStore.fetchTodasLasAcciones(props.hallazgoId),
            hallazgoStore.fetchCausaRaiz(props.hallazgoId)
        ]);
    } catch (e) {
        console.error(e);
    } finally {
        isPageLoading.value = false;
    }
});
</script>

<style scoped>
.min-vh-75 { min-height: 75vh; }
.cursor-pointer { cursor: pointer; }
.border-2 { border-width: 2px !important; }
.bg-danger-light { background-color: rgba(220, 53, 69, 0.08); }
.bg-success-light { background-color: #f0fff4; }
.text-success-dark { color: #22543d; }
.icon-circle { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }

/* STEPPER STYLES (MATCHING WIZARD) */
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
.step-desc { color: #adb5bd; line-height: 1.2; display: block; font-size: 0.8rem; }

/* UPLOAD ZONE */
.drop-zone-premium { border: 2px dashed #e2e8f0; transition: all 0.3s ease; }
.drop-zone-premium:hover { border-color: #dc3545; background-color: #fff5f5; }
.drop-zone-premium.dragging { background-color: #fcebeb; border-color: #dc3545; }

/* ANIMATIONS */
.fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

/* UTILS */
.rounded-lg { border-radius: 0.75rem !important; }
.extra-small { font-size: 0.65rem; }
.breadcrumb { font-size: 0.85rem; }
.breadcrumb-item + .breadcrumb-item::before { content: "\f105"; font-family: "Font Awesome 5 Free"; font-weight: 900; color: #adb5bd; }
.border-left-danger { border-left: 4px solid #dc3545 !important; }
</style>
