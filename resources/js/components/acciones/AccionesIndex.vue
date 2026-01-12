<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light py-2 px-3 rounded">
                <li class="breadcrumb-item"><router-link to="/home" class="text-secondary">Inicio</router-link></li>
                <li class="breadcrumb-item"><router-link :to="{ name: 'hallazgos.mine.vue' }" class="text-secondary">Mis
                        Solicitudes de Mejora</router-link></li>
                <li class="breadcrumb-item active text-danger" aria-current="page">Gestión del Plan de Acción</li>
            </ol>
        </nav>

        <div class="animate__animated animate__fadeIn">
            <div class="row">
                <!-- Stepper Sidebar -->
                <div class="col-md-3 mb-4">
                    <div class="stepper-sidebar sticky-top" style="top: 20px; z-index: 1;">
                        <div class="card border-0 shadow-sm bg-danger text-white mb-3">
                            <div class="card-body p-2 text-center">
                                <h6 class="font-weight-bold mb-0 text-white">
                                    <i class="fas fa-folder-open mr-2"></i>Expediente: {{ hallazgo.hallazgo_cod || '...' }}
                                </h6>
                            </div>
                        </div>

                        <div class="stepper-wrapper">
                            <!-- Step 1: Planificación -->
                            <div class="stepper-item" :class="{ completed: currentStep > 1, active: currentStep === 1 }"
                                @click="goToStep(1)">
                                <div class="step-counter">
                                    <i v-if="currentStep > 1" class="fas fa-check"></i>
                                    <span v-else>1</span>
                                </div>
                                <div class="step-info">
                                    <div class="step-name">Planificación</div>
                                    <small class="step-desc">Causa Raíz y Acciones</small>
                                </div>
                            </div>
                            
                            <!-- Step 2: Revisión e Impresión -->
                            <div class="stepper-item" :class="{ completed: currentStep > 2, active: currentStep === 2 }"
                                @click="goToStep(2)">
                                <div class="step-counter">
                                    <i v-if="currentStep > 2" class="fas fa-check"></i>
                                    <span v-else>2</span>
                                </div>
                                <div class="step-info">
                                    <div class="step-name">Revisión e Impresión</div>
                                    <small class="step-desc">Generar documento PDF</small>
                                </div>
                            </div>

                            <!-- Step 3: Firma y Envío -->
                            <div class="stepper-item" :class="{ completed: hallazgo.hallazgo_estado === 'plan_enviado', active: currentStep === 3 }"
                                @click="goToStep(3)">
                                <div class="step-counter">
                                    <i v-if="hallazgo.hallazgo_estado === 'plan_enviado'" class="fas fa-check"></i>
                                    <span v-else>3</span>
                                </div>
                                <div class="step-info">
                                    <div class="step-name">Firma y Envío</div>
                                    <small class="step-desc">Adjuntar y finalizar</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="col-md-9">
                    
                    <!-- STEP 1: Planificación (Causa Raíz + Acciones) -->
                    <div v-show="currentStep === 1" class="step-content px-1 animate__animated animate__fadeIn">
                         <!-- Header Step 1 -->
                        <div class="step-header mb-4 pb-2 border-bottom">
                            <h4 class="text-dark"><i class="fas fa-tasks text-danger mr-2"></i> Planificación</h4>
                            <p class="text-muted small">Realice el análisis de causa raíz y defina las acciones correctivas o preventivas.</p>
                        </div>

                        <!-- Análisis de Causa Raíz -->
                        <div class="card shadow-sm mb-4 border-0">
                            <div class="card-header bg-white border-bottom py-2 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-danger font-weight-bold">1.1 Análisis de Causa Raíz</h5>
                                <div class="ml-auto" v-if="hallazgoStore.causaRaiz && hallazgoStore.causaRaiz.id">
                                    <button :disabled="!hallazgoStore.accionesPermitidas"
                                        class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm"
                                        @click="causaRaizRef.enableEdit()"
                                        :title="!hallazgoStore.accionesPermitidas ? 'No se puede editar en este estado' : ''">
                                        <i class="fas fa-edit mr-1"></i> Editar
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <CausaRaiz ref="causaRaizRef" :hallazgoId="hallazgoId" :embedded="true" :hideTitle="true" :hideEditButton="true" />
                            </div>
                        </div>

                        <!-- Planes de Acción Tabla -->
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-bottom py-2 d-flex align-items-center justify-content-between">
                                <h5 class="mb-0 text-danger font-weight-bold">1.2 Plan de Acción</h5>
                                <div class="ml-auto">
                                    <button :disabled="!hallazgoStore.accionesPermitidas"
                                        class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm" @click="openModal()"
                                        :title="!hallazgoStore.accionesPermitidas ? 'No se pueden crear acciones en este estado' : ''">
                                        <i class="fas fa-plus mr-1"></i> Nueva Acción
                                    </button>
                                </div>

                            </div>
                            <div class="card-body">
                                <div class="alert alert-secondary small mb-3 py-2 px-3" role="alert">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Defina las acciones necesarias para mitigar la causa raíz. Debe registrar al menos una acción para continuar.
                                </div>

                                <DataTable :value="acciones" responsiveLayout="scroll" :loading="isLoading && !isPageLoading"
                                    class="p-datatable-sm" stripedRows paginator :rows="5" :rowsPerPageOptions="[5, 10, 20]">
                                    <template #empty>
                                        <div class="text-center py-4 text-muted">
                                            <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                                            <p class="mb-0">No hay acciones registradas.</p>
                                        </div>
                                    </template>
                                    <Column field="accion_cod" header="Código" style="width: 10%;">
                                        <template #body="{ data }">
                                            <span class="font-weight-bold text-dark">{{ data.accion_cod }}</span>
                                        </template>
                                    </Column>
                                    <Column field="accion_descripcion" header="Descripción" style="width: 30%;">
                                        <template #body="{ data }">
                                            <div class="text-truncate-multiline" style="max-height: 3.6em; overflow: hidden;">
                                                {{ data.accion_descripcion }}
                                            </div>
                                        </template>
                                    </Column>
                                    <Column field="accion_responsable" header="Responsable" style="width: 15%;"></Column>
                                    <Column field="accion_fecha_inicio" header="Inicio" style="width: 100px;">
                                        <template #body="{ data }">{{ formatDate(data.accion_fecha_inicio) }}</template>
                                    </Column>
                                    <Column header="Fin" style="width: 100px;">
                                        <template #body="{ data }">
                                            <span :class="{ 'text-danger font-weight-bold': isFechaVencida(data.accion_fecha_fin_planificada) }">
                                                {{ formatDate(data.accion_fecha_fin_reprogramada || data.accion_fecha_fin_planificada) }}
                                            </span>
                                        </template>
                                    </Column>
                                    <Column field="accion_estado" header="Estado">
                                        <template #body="{ data }">
                                            <span :class="getEstadoBadgeClass(data.accion_estado)">{{ data.accion_estado }}</span>
                                        </template>
                                    </Column>
                                    <Column header="Acciones" :exportable="false" style="text-align: center; min-width: 120px;">
                                        <template #body="{ data }">
                                            <div class="btn-group" role="group">
                                                <button @click.prevent="openModal(data)" class="btn btn-sm btn-outline-secondary" title="Editar">
                                                    <i class="fas fa-pencil-alt text-warning"></i>
                                                </button>
                                                <button @click.prevent="confirmDelete(data)" class="btn btn-sm btn-outline-secondary" title="Eliminar">
                                                    <i class="fas fa-trash-alt text-danger"></i>
                                                </button>
                                            </div>
                                        </template>
                                    </Column>
                                </DataTable>

                                <div class="text-right mt-4 pt-3 border-top">
                                    <button class="btn btn-outline-danger px-4 shadow-sm" @click="goToStep(2)">
                                        Siguiente: Impresión <i class="fas fa-arrow-right ml-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: Revisión e Impresión -->
                    <div v-show="currentStep === 2" class="step-content px-1 animate__animated animate__fadeIn">
                        
                        <div class="step-header mb-4 pb-2 border-bottom">
                            <h4 class="text-dark"><i class="fas fa-print text-danger mr-2"></i> Revisión e Impresión</h4>
                            <p class="text-muted small">Genere la versión PDF oficial del plan de acción para su firma y aprobación.</p>
                        </div>

                        <!-- Card 1: Print -->
                         <div class="card shadow-sm border-0 mb-4 document-card">
                            <div class="card-body p-5">
                                <div class="d-flex align-items-center justify-content-center flex-column text-center">
                                    <div class="mb-4">
                                        <i class="fas fa-file-pdf fa-4x text-danger opacity-75"></i>
                                    </div>
                                    <h5 class="font-weight-bold mb-2">Imprimir Plan de Acción para Firma</h5>
                                    <p class="text-muted mb-4 px-5">
                                        Genere la versión PDF oficial de su Plan de Acción. Este documento es necesario para el proceso de firma y aprobación por parte del responsable.
                                    </p>
                                    <button class="btn btn-danger btn-lg px-5 shadow-sm rounded-pill" @click="openPrintModal">
                                        <i class="fas fa-print mr-2"></i> Imprimir PDF
                                    </button>
                                </div>
                                
                                <div class="step-actions mt-5 pt-3 border-top d-flex justify-content-between align-items-center">
                                    <button class="btn btn-outline-secondary" @click="goToStep(1)">
                                        <i class="fas fa-arrow-left mr-2"></i> Anterior
                                    </button>
                                    <button class="btn btn-outline-danger px-4" @click="goToStep(3)">
                                        Siguiente: Firma y Envío <i class="fas fa-arrow-right ml-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- STEP 3: Firma y Envío -->
                    <div v-show="currentStep === 3" class="step-content px-1 animate__animated animate__fadeIn">
                         <div class="step-header mb-4 pb-2 border-bottom">
                            <h4 class="text-dark"><i class="fas fa-file-signature text-danger mr-2"></i> Firma y Envío</h4>
                            <p class="text-muted small">Adjunte el plan de acción firmado y finalice el proceso.</p>
                        </div>

                        <!-- Card 2: Upload -->
                        <div class="card shadow-sm border-0 mb-4 document-card">
                             <div class="card-header bg-white border-bottom py-3">
                                <h6 class="mb-0 font-weight-bold text-dark"><i class="fas fa-upload text-success mr-2"></i> Adjuntar Plan Firmado</h6>
                            </div>
                            <div class="card-body p-4">
                                
                                <p class="text-muted small mb-3">
                                    Por favor, adjunte aquí el archivo PDF del Plan de Acción una vez que haya sido firmado.
                                </p>

                                <!-- Current File Check -->
                                <div v-if="hallazgo.ruta_plan_accion" class="mb-4">
                                    <div class="d-flex align-items-center bg-light p-3 rounded border border-success">
                                        <i class="fas fa-check-circle text-success fa-2x mr-3"></i>
                                        <div>
                                            <a :href="getAssetUrl(hallazgo.ruta_plan_accion)" target="_blank" class="font-weight-bold text-dark text-decoration-none">
                                                Plan de Acción Firmado.pdf
                                            </a>
                                            <div class="small text-success">Documento cargado correctamente</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Upload Area -->
                                <div class="upload-area">
                                    <div class="drop-zone p-5 text-center border-dashed rounded bg-light" 
                                         @click="$refs.fileInput.click()" 
                                         :class="{'hover-active': !uploading}"
                                         @dragover.prevent @drop.prevent="handleFileDrop">
                                        
                                        <input type="file" ref="fileInput" class="d-none" accept=".pdf" @change="handleFileSelect">
                                        
                                        <div v-if="uploading" class="py-3">
                                            <div class="spinner-border text-danger role='status'"></div>
                                            <p class="mt-2 text-muted">Subiendo archivo...</p>
                                        </div>
                                        <div v-else>
                                            <i class="fas fa-cloud-upload-alt fa-3x text-secondary mb-3 opacity-50"></i>
                                            <h5 class="text-secondary font-weight-normal">Arrastre su archivo aquí o haga clic para seleccionar</h5>
                                            <p class="text-muted small mb-0">(Solo formato PDF - Máx. 10MB)</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Send Action -->
                                <div class="step-actions mt-5 pt-3 border-top d-flex justify-content-between align-items-center">
                                    <button class="btn btn-outline-secondary" @click="goToStep(2)">
                                        <i class="fas fa-arrow-left mr-2"></i> Anterior
                                    </button>
                                    
                                    <div class="text-right">
                                        <span v-if="hallazgo.hallazgo_estado === 'plan_enviado'" class="text-success mr-3 font-weight-bold small">
                                            <i class="fas fa-check-circle"></i> ESTADO: PLAN ENVIADO
                                        </span>
                                        <button class="btn btn-success px-5 shadow" 
                                                @click="enviarPlan" 
                                                :disabled="!hallazgo.ruta_plan_accion || uploading || hallazgo.hallazgo_estado === 'plan_enviado'">
                                            <i class="fas fa-paper-plane mr-2"></i> {{ hallazgo.hallazgo_estado === 'plan_enviado' ? 'Reenviar Plan' : 'Enviar Plan Finalizado' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Modals -->
            <ReprogramarAccionModal @accion-gestionada="refreshAcciones" />
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
import ReprogramarAccionModal from './ReprogramarAccionModal.vue';
import AccionesForm from './AccionesForm.vue';
import AccionesAvanceForm from './AccionesAvanceForm.vue';
import CausaRaiz from './CausaRaiz.vue';
import AccionPrintModal from './AccionPrintModal.vue';
import { useHallazgoStore } from '@/stores/hallazgoStore';

const props = defineProps({
    hallazgoId: { type: [Number, String], required: true }
});

const hallazgoStore = useHallazgoStore();
const { todasLasAcciones: acciones, hallazgoForm: hallazgo, loading: isLoading } = storeToRefs(hallazgoStore);

const isPageLoading = ref(true);
const currentStep = ref(1);
const uploading = ref(false);

const causaRaizRef = ref(null);
const accionPrintModalRef = ref(null);

// Modal states
const showActionModal = ref(false);
const showAvanceModal = ref(false);
const selectedAction = ref(null);
const selectedAvanceAction = ref(null);

// Computed Navigation guards - not strict anymore but good for visual cue if needed
const canGoToStep2 = computed(() => acciones.value && acciones.value.length > 0);

// Helper Methods
const getAssetUrl = (path) => `/storage/${path}`;
const formatDate = (date) => date ? new Date(date).toLocaleDateString() : '';

const isFechaVencida = (fecha) => {
    if (!fecha) return false;
    return new Date(fecha) < new Date().setHours(0,0,0,0);
};

const getEstadoBadgeClass = (estado) => {
    switch (estado) {
        case 'programada': return 'badge badge-primary';
        case 'en proceso': return 'badge badge-info';
        case 'implementada': return 'badge badge-success';
        case 'finalizada': return 'badge badge-success';
        case 'desestimada': return 'badge badge-secondary';
        case 'reprogramada': return 'badge badge-warning';
        default: return 'badge badge-light';
    }
};

// Actions
const goToStep = (step) => {
    currentStep.value = step;
};

const openPrintModal = () => {
    // Pass full data objects to the client-side print modal
    if(accionPrintModalRef.value) {
        accionPrintModalRef.value.open(
            hallazgo.value,
            acciones.value,
            hallazgoStore.causaRaiz // Access directly from store state
        );
    }
};

// File Upload
const handleFileSelect = (event) => uploadFile(event.target.files[0]);
const handleFileDrop = (event) => uploadFile(event.dataTransfer.files[0]);

const uploadFile = async (file) => {
    if (!file) return;
    if (file.type !== 'application/pdf') {
        Swal.fire('Error', 'Solo se permiten archivos PDF.', 'error');
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
        Swal.fire('Error', 'Error al subir el archivo.', 'error');
        console.error(error);
    } finally {
        uploading.value = false;
    }
};

const enviarPlan = async () => {
    try {
        const result = await Swal.fire({
            title: '¿Confirmar Envío?',
            text: "El plan de acción será enviado para revisión. Asegúrese de que el documento firmado corresponde a las acciones registradas.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, Enviar Plan',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            const response = await axios.post(route('hallazgo.plan-accion.enviar', { hallazgo: props.hallazgoId }));
            hallazgo.value.hallazgo_estado = response.data.estado;
            Swal.fire('Enviado', 'El plan de acción ha sido enviado correctamente.', 'success');
        }
    } catch (error) {
        Swal.fire('Error', error.response?.data?.error || 'No se pudo enviar el plan.', 'error');
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
                Swal.fire('Eliminado', 'La acción ha sido eliminada.', 'success');
            } catch (e) {
                Swal.fire('Error', 'No se pudo eliminar.', 'error');
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
/* Stepper Sidebar Styles */
.stepper-sidebar {
    background-color: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1.5rem;
}

.stepper-wrapper {
    position: relative;
    padding-left: 0.5rem;
}

.stepper-item {
    position: relative;
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    margin-bottom: 0.5rem;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.stepper-item:hover {
    background-color: #e9ecef;
}

/* Line connector */
.stepper-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: calc(1rem + 13px); 
    top: 3.5rem;
    width: 2px;
    height: calc(100% - 20px);
    background-color: #dee2e6;
    z-index: 0;
}

.stepper-item.completed:not(:last-child)::before {
    background-color: #28a745;
}

.step-counter {
    width: 28px;
    height: 28px;
    min-width: 28px;
    border-radius: 50%;
    background-color: #dee2e6;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.8rem;
    z-index: 1;
    margin-right: 1rem;
    transition: all 0.3s;
    border: 2px solid #fff; 
    box-shadow: 0 0 0 2px #dee2e6;
}

.stepper-item.active .step-counter {
    background-color: #dc3545;
    color: white;
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.2);
}

.stepper-item.completed .step-counter {
    background-color: #28a745;
    color: white;
    box-shadow: none;
}

.step-info {
    flex: 1;
}

.step-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
}

.step-desc {
    font-size: 0.75rem;
    color: #6c757d;
}

.stepper-item.active .step-name {
    color: #dc3545;
}

.stepper-item.completed .step-name {
    color: #28a745;
}

/* Document Cards */
.document-card {
    border: 1px solid #e9ecef;
    transition: all 0.3s;
}
.document-card:hover {
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}

/* Dropzone */
.drop-zone {
    border: 2px dashed #ced4da;
    transition: all 0.3s;
}
.drop-zone.hover-active:hover {
    border-color: #dc3545;
    background-color: #fff8f8 !important;
}

.loading-spinner-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    min-height: 400px;
}
</style>
