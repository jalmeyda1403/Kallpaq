<template>
    <div class="container-fluid py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" v-if="!embedded">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link to="/home"
                        class="text-danger font-weight-bold">Inicio</router-link></li>
                <li class="breadcrumb-item"><router-link :to="backRoute" class="text-danger font-weight-bold">{{
                    backLabel }}</router-link></li>
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
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm"
                                    style="width: 40px; height: 40px; min-width: 40px;">
                                    <i class="fas fa-folder-open text-danger" style="font-size: 0.9rem;"></i>
                                </div>
                                <div>
                                    <h5 class="font-weight-bold text-white mb-0 d-flex align-items-center">
                                        Expediente: {{ hallazgo.hallazgo_cod || 'Cargando...' }}
                                        <span v-if="hallazgo.hallazgo_estado"
                                            class="badge badge-light ml-3 text-uppercase text-danger shadow-sm"
                                            style="font-size: 0.65rem; padding: 0.4em 0.8em;">
                                            <i class="fas fa-info-circle mr-1"></i> {{ hallazgo.hallazgo_estado }}
                                        </span>
                                    </h5>
                                    <p class="text-white mb-0" style="opacity: 0.9; font-size: 0.75rem;">
                                        Gestionar Plan de Acción - {{ hallazgo.procesos?.[0]?.proceso_nombre ||
                                            'Proceso' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-right mt-2 mt-md-0">
                            <button
                                v-if="['creado', 'evaluado', 'aprobado', 'en proceso'].includes(hallazgo.hallazgo_estado?.toLowerCase())"
                                @click="confirmarDesestimar"
                                class="btn btn-outline-light btn-sm mr-3 border-0 font-weight-bold">
                                <i class="fas fa-ban mr-1"></i> Desestimar
                            </button>
                            <router-link v-if="!embedded" :to="backRoute"
                                class="btn btn-link text-white text-decoration-none mr-3 px-0 btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a {{ backLabel }}
                            </router-link>
                        </div>
                    </div>
                </div>

                <!-- BODY -->
                <div class="card-body p-0 position-relative">
                    <div v-if="isPageLoading"
                        class="wizard-loader d-flex flex-column align-items-center justify-content-center bg-white"
                        style="height: 60vh; z-index: 10;">
                        <div class="spinner-border text-danger mb-3" style="width: 3rem; height: 3rem;"
                            role="status text-danger">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <h6 class="text-muted font-weight-bold animate__animated animate__pulse animate__infinite">
                            Preparando Plan de Acción...</h6>
                    </div>

                    <div v-else class="row no-gutters animate__animated animate__fadeIn">
                        <!-- SIDEBAR (Left) -->
                        <div class="col-md-3 bg-light border-right min-vh-75">
                            <div class="p-4">
                                <div class="stepper-wrapper">
                                    <div v-for="(step, index) in steps" :key="step.id" class="stepper-item"
                                        :class="{ completed: currentStep > step.id, active: currentStep === step.id }"
                                        @click="goToStep(step.id)">
                                        <div class="step-counter">
                                            <i v-if="currentStep > step.id || (step.id === 4 && (hallazgo.hallazgo_estado || '').toLowerCase() === 'aprobado')"
                                                class="fas fa-check"></i>
                                            <span v-else>{{ index + 1 }}</span>
                                        </div>
                                        <div class="step-info">
                                            <div class="step-name">{{ step.name }}</div>
                                            <small class="step-desc">{{ step.desc }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5 fade-in" v-if="hallazgo.hallazgo_estado">
                                    <div class="card border-0 shadow-sm bg-white p-3 rounded-lg text-center">
                                        <h6 class="text-uppercase text-muted extra-small font-weight-bold mb-2">Estado
                                            del Hallazgo</h6>
                                        <span class="badge badge-pill badge-block p-2"
                                            :class="getStatusBadgeClass(hallazgo.hallazgo_estado)">{{
                                                hallazgo.hallazgo_estado.toUpperCase() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CONTENT (Right) -->
                        <div class="col-md-9 bg-white">
                            <div class="p-4 p-lg-5">
                                <div v-if="showCausaStep" v-show="currentStep === 1" class="step-pane fade-in">
                                    <div class="pane-header mb-4 border-bottom pb-3">
                                        <h4 class="text-dark font-weight-bold">1. Análisis de Causa Raíz</h4>
                                        <p class="text-muted">Describa la metodología utilizada y determine la causa
                                            raíz del hallazgo.</p>
                                    </div>
                                    <div class="card shadow-none border bg-light mb-4 rounded-lg">
                                        <div class="card-body p-4">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0 text-danger font-weight-bold">Detalle del Análisis</h6>
                                                <button :disabled="!hallazgoStore.accionesPermitidas"
                                                    class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm font-weight-bold"
                                                    @click="causaRaizRef.enableEdit()">
                                                    <i class="fas fa-edit mr-1"></i> Editar Análisis
                                                </button>
                                            </div>
                                            <CausaRaiz ref="causaRaizRef" :hallazgoId="hallazgoId" :embedded="true"
                                                :hideTitle="true" :hideEditButton="true" />
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-3 border-top text-right">
                                        <button
                                            class="btn btn-outline-danger px-4 shadow-sm rounded-pill font-weight-bold"
                                            @click="goToStep(2)">Siguiente: Plan de Acción <i
                                                class="fas fa-arrow-right ml-2"></i></button>
                                    </div>
                                </div>

                                <div v-show="currentStep === 2" class="step-pane fade-in">
                                    <div class="pane-header mb-4 border-bottom pb-3">
                                        <h4 class="text-dark font-weight-bold">{{ showCausaStep ? '2.' : '1.' }} Plan de
                                            Acción</h4>
                                        <p class="text-muted">Defina las acciones correctivas necesarias para mitigar la
                                            situación identificada.</p>
                                    </div>
                                    <div v-if="showCausaStep" class="card bg-white border shadow-sm rounded-lg mb-4">
                                        <div class="card-body py-3 px-3 border-left-danger">
                                            <h6 class="font-weight-bold text-dark mb-2"><i
                                                    class="fas fa-search text-danger mr-2"></i>Referencia: Causa Raíz
                                                Identificada</h6>
                                            <p class="mb-0 text-secondary"
                                                style="white-space: pre-wrap; font-size: 0.9rem;">
                                                {{ causaRaiz?.hc_resultado ||
                                                    'No se ha registrado un análisis de causa raíz.' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="card shadow-none border rounded-lg">
                                        <div
                                            class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                                            <h6 class="mb-0 text-danger font-weight-bold">Listado de Acciones</h6>
                                            <button :disabled="!hallazgoStore.accionesPermitidas"
                                                class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm font-weight-bold ml-auto"
                                                @click="openModal()"><i class="fas fa-plus mr-1"></i> Nueva
                                                Acción</button>
                                        </div>
                                        <div class="card-body p-0">
                                            <div v-if="acciones.length === 0" class="text-center py-5 text-muted">
                                                <i class="fas fa-clipboard-list fa-3x mb-3 opacity-50"></i>
                                                <p class="mb-2 font-weight-bold">No hay acciones registradas</p>
                                                <small>{{ showCausaStep ?
                                                    'Defina las acciones necesarias para mitigar la causa raíz.' :
                                                    'Defina las acciones necesarias para este hallazgo.' }}</small>

                                            </div>
                                            <DataTable v-else :value="sortedAcciones" responsiveLayout="scroll"
                                                :loading="isLoading && !isPageLoading" class="p-datatable-sm"
                                                stripedRows paginator :rows="5" :rowsPerPageOptions="[5, 10, 20]">
                                                <Column field="accion_cod" header="Código" style="width: 10%;">
                                                    <template #body="{ data }"><span
                                                            class="font-weight-bold text-dark small">{{ data.accion_cod
                                                            }}</span></template>
                                                </Column>
                                                <Column field="accion_tipo" header="Tipo" style="width: 15%;">
                                                    <template #body="{ data }"><span
                                                            class="badge badge-light border text-uppercase small">{{
                                                                data.accion_tipo || 'N/A' }}</span></template>
                                                </Column>
                                                <Column field="accion_descripcion" header="Descripción"
                                                    style="width: 35%;">
                                                    <template #body="{ data }">
                                                        <div class="text-truncate-multiline small"
                                                            style="max-height: 3.6em; overflow: hidden;">{{
                                                                data.accion_descripcion }}</div>
                                                    </template>
                                                </Column>
                                                <Column field="accion_responsable" header="Responsable"
                                                    style="width: 15%;">
                                                    <template #body="{ data }"><span class="small">{{
                                                        data.accion_responsable }}</span></template>
                                                </Column>
                                                <Column header="Vencimiento" style="width: 15%;">
                                                    <template #body="{ data }"><span class="small"
                                                            :class="{ 'text-danger font-weight-bold': isFechaVencida(data.accion_fecha_fin_planificada) }">{{
                                                                formatDate(data.accion_fecha_fin_reprogramada ||
                                                                    data.accion_fecha_fin_planificada) }}</span></template>
                                                </Column>
                                                <Column field="accion_estado" header="Estado" style="width: 10%;">
                                                    <template #body="{ data }"><span
                                                            :class="getEstadoBadgeClass(data.accion_estado) + ' small'">{{
                                                                data.accion_estado }}</span></template>
                                                </Column>
                                                <Column header="" :exportable="false"
                                                    style="width: 15%; text-align: right;">
                                                    <template #body="{ data }">
                                                        <button
                                                            v-if="['desestimado', 'concluido', 'cerrado', 'aprobado', 'en proceso'].includes(hallazgo.hallazgo_estado?.toLowerCase())"
                                                            @click.prevent="openModal(data, true)"
                                                            class="btn btn-xs btn-link p-0 mr-2" title="Visualizar"><i
                                                                class="fas fa-eye text-dark"></i></button>
                                                        <button
                                                            v-if="['aprobado', 'en proceso'].includes(hallazgo.hallazgo_estado?.toLowerCase())"
                                                            @click.prevent="openAvanceModal(data)"
                                                            class="btn btn-xs btn-link p-0 mr-2"
                                                            title="Registrar Avance"><i
                                                                class="fas fa-tasks text-info"></i></button>
                                                        <button @click.prevent="canEditActions && openModal(data)"
                                                            class="btn btn-xs btn-link p-0 mr-2"
                                                            :class="canEditActions ? 'text-warning' : 'text-muted'"
                                                            :disabled="!canEditActions" v-if="canEditActions"
                                                            title="Editar"><i class="fas fa-pencil-alt"></i></button>
                                                        <button @click.prevent="canEditActions && confirmDelete(data)"
                                                            class="btn btn-xs btn-link p-0"
                                                            :class="canEditActions ? 'text-danger' : 'text-muted'"
                                                            :disabled="!canEditActions" v-if="canEditActions"
                                                            title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                                                    </template>
                                                </Column>
                                            </DataTable>
                                        </div>
                                    </div>
                                    <div class="step-actions mt-5 pt-3 border-top d-flex justify-content-between">
                                        <button v-if="showCausaStep"
                                            class="btn btn-light px-4 py-2 font-weight-bold text-muted border rounded-pill"
                                            @click="goToStep(1)"><i class="fas fa-chevron-left mr-2"></i>
                                            Anterior</button>
                                        <div v-else></div>
                                        <button class="btn btn-outline-danger px-4 py-2 font-weight-bold rounded-pill"
                                            @click="goToStep(3)">Siguiente:
                                            Impresión <i class="fas fa-arrow-right ml-2"></i></button>
                                    </div>
                                </div>

                                <div v-show="currentStep === 3" class="step-pane fade-in">
                                    <div class="pane-header mb-4 border-bottom pb-3">
                                        <h4 class="text-dark font-weight-bold">{{ showCausaStep ? '3.' : '2.' }}
                                            Revisión e Impresión</h4>
                                        <p class="text-muted">Genere la versión PDF oficial del plan de acción.</p>
                                    </div>
                                    <div class="card shadow-sm border-0 mb-4 bg-light rounded-lg text-center py-5">
                                        <div class="card-body">
                                            <div class="mb-4">
                                                <div class="bg-white rounded-circle shadow-sm d-inline-flex p-4"><i
                                                        class="fas fa-file-pdf fa-3x text-danger"></i></div>
                                            </div>
                                            <h5 class="font-weight-bold mb-2 text-dark">Imprimir Plan de Acción</h5>
                                            <p class="text-muted mb-4 px-5 mx-auto" style="max-width: 600px;">Este
                                                documento consolidado incluye {{
                                                    showCausaStep ? 'el análisis de causa raíz y ' : '' }} las acciones
                                                planificadas.</p>
                                            <button
                                                class="btn btn-danger px-5 py-2 shadow-sm rounded-pill font-weight-bold"
                                                @click="openPrintModal"><i class="fas fa-print mr-2"></i> Generar
                                                PDF</button>
                                        </div>
                                    </div>
                                    <div class="step-actions mt-5 pt-3 border-top d-flex justify-content-between">
                                        <button
                                            class="btn btn-light px-4 py-2 font-weight-bold text-muted border rounded-pill"
                                            @click="goToStep(2)"><i class="fas fa-chevron-left mr-2"></i>
                                            Anterior</button>
                                        <button class="btn btn-outline-danger px-4 py-2 font-weight-bold rounded-pill"
                                            @click="goToStep(4)">Siguiente:
                                            Firma y Envío <i class="fas fa-arrow-right ml-2"></i></button>
                                    </div>
                                </div>

                                <div v-show="currentStep === 4" class="step-pane fade-in">
                                    <div class="pane-header mb-4 border-bottom pb-3">
                                        <h4 class="text-dark font-weight-bold">{{ showCausaStep ? '4.' : '3.' }} Firma y
                                            Envío</h4>
                                        <p class="text-muted">Adjunte el plan de acción firmado y finalice el proceso.
                                        </p>
                                    </div>
                                    <div class="doc-upload-section">
                                        <div v-if="hallazgo.ruta_plan_accion"
                                            class="mb-4 animate__animated animate__fadeInDown">
                                            <div class="card border-0 shadow-sm bg-success-light"
                                                style="background-color: #f0fff4;">
                                                <div class="card-body p-3 d-flex align-items-center">
                                                    <div class="icon-circle bg-success text-white mr-3 shadow-sm"
                                                        style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-check"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0 font-weight-bold text-success-dark"
                                                            style="color: #22543d;">Documento Cargado
                                                        </h6>
                                                        <a :href="getAssetUrl(hallazgo.ruta_plan_accion)"
                                                            target="_blank"
                                                            class="text-success small text-decoration-underline">Ver
                                                            Plan de Acción Firmado.pdf</a>
                                                    </div>
                                                    <button
                                                        class="btn btn-sm btn-white text-danger font-weight-bold shadow-sm"
                                                        @click="$refs.fileInput.click()"
                                                        v-if="['creado', 'evaluado'].includes(hallazgo.hallazgo_estado?.toLowerCase())"><i
                                                            class="fas fa-sync-alt mr-1"></i> Reemplazar</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="drop-zone-premium shadow-sm border-2 rounded-lg py-5 text-center bg-white"
                                            @click="triggerFileInput"
                                            v-if="['creado', 'evaluado'].includes(hallazgo.hallazgo_estado?.toLowerCase())"
                                            :class="{ 'dragging': isDragging }" @dragover.prevent="isDragging = true"
                                            @dragleave.prevent="isDragging = false" @drop.prevent="handleFileDrop">
                                            <input type="file" ref="fileInput" class="d-none" accept=".pdf"
                                                @change="handleFileSelect">
                                            <div v-if="uploading">
                                                <div class="spinner-border text-danger" role="status"></div>
                                                <p class="mt-2 text-muted font-weight-bold">Subiendo archivo...</p>
                                            </div>
                                            <div v-else>
                                                <i
                                                    class="fas fa-cloud-upload-alt fa-3x text-danger mb-3 opacity-50"></i>
                                                <h5 class="font-weight-bold text-dark mb-1">Arrastre el documento
                                                    firmado</h5>
                                                <p class="text-muted small">o seleccione un PDF desde su computadora</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="step-actions mt-5 pt-3 border-top d-flex justify-content-between align-items-center">
                                        <button
                                            class="btn btn-light px-4 py-2 font-weight-bold text-muted border rounded-pill"
                                            @click="goToStep(3)"><i class="fas fa-chevron-left mr-2"></i>
                                            Anterior</button>
                                        <div class="text-right">
                                            <span v-if="hallazgo.hallazgo_estado === 'aprobado'"
                                                class="text-success mr-3 font-weight-bold small"><i
                                                    class="fas fa-check-circle"></i> ESTADO: APROBADO</span>
                                            <button
                                                class="btn btn-success px-5 py-2 font-weight-bold shadow rounded-pill"
                                                @click="enviarPlan"
                                                :disabled="!hallazgo.ruta_plan_accion || uploading || hallazgo.hallazgo_estado === 'aprobado'">
                                                <i class="fas fa-paper-plane mr-2"></i> {{ hallazgo.hallazgo_estado ===
                                                    'aprobado' ? 'Plan Enviado' :
                                                    'Enviar Plan' }}
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
            <AccionesForm :show="showActionModal" :actionData="selectedAction" :hallazgoId="hallazgoId"
                :procesos="hallazgo.procesos" :readonly="isFormReadOnly" @close="closeModal" @saved="onActionSaved" />
            <AccionesAvanceForm :show="showAvanceModal" :actionData="selectedAvanceAction" :initialTab="activeAvanceTab"
                @close="closeAvanceModal" @saved="onAvanceSaved" />
            <AccionPrintModal ref="accionPrintModalRef" />
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Swal from 'sweetalert2';
import axios from 'axios';
import { route } from 'ziggy-js';
import AccionesForm from './AccionesForm.vue';
import AccionesAvanceForm from './AccionesAvanceForm.vue';
import CausaRaiz from './CausaRaiz.vue';
import AccionPrintModal from './AccionPrintModal.vue';
import { useHallazgoStore } from '@/stores/hallazgoStore';

const props = defineProps({ hallazgoId: { type: [Number, String], required: true }, embedded: { type: Boolean, default: false } });
const routeData = useRoute();
const router = useRouter();

const backRoute = computed(() => {
    if (routeData.query.from) {
        return { name: routeData.query.from };
    }
    return { name: 'hallazgos.mine.vue' };
});

const backLabel = computed(() => {
    const name = backRoute.value.name;
    if (name === 'hallazgos.index') return 'Solicitudes de Mejora';
    if (name === 'hallazgos.mine.vue') return 'Mis Solicitudes';
    if (name === 'hallazgos.eficacia') return 'Bandeja de Eficacia';
    return 'Mis Solicitudes';
});

const hallazgoStore = useHallazgoStore();
const { todasLasAcciones: acciones, hallazgoForm: hallazgo, causaRaiz, loading: isLoading } = storeToRefs(hallazgoStore);

const showCausaStep = computed(() => {
    const clas = (hallazgo.value.hallazgo_clasificacion || '').toLowerCase();
    return ['ncm', 'ncme'].includes(clas);
});

const steps = computed(() => {
    const s = [];
    if (showCausaStep.value) s.push({ id: 1, name: 'Análisis de Causa', desc: 'Identificar raíz' });
    s.push({ id: 2, name: 'Plan de Acción', desc: 'Definir acciones' });
    s.push({ id: 3, name: 'Revisión e Impresión', desc: 'Generar PDF' });
    s.push({ id: 4, name: 'Firma y Envío', desc: 'Adjuntar y finalizar' });
    return s;
});

const sortedAcciones = computed(() => {
    if (!acciones.value) return [];
    return [...acciones.value].sort((a, b) => {
        const typeA = (a.accion_tipo || '').toLowerCase();
        const typeB = (b.accion_tipo || '').toLowerCase();
        if (typeA.includes('corrección') && !typeB.includes('corrección')) return -1;
        if (!typeA.includes('corrección') && typeB.includes('corrección')) return 1;
        return 0;
    });
});

const isPageLoading = ref(true);
const currentStep = ref(1);
const uploading = ref(false);
const isDragging = ref(false);
const isFormReadOnly = ref(false);
const canEditActions = computed(() => {
    const estado = (hallazgo.value.hallazgo_estado || '').toLowerCase();
    return ['creado', 'evaluado'].includes(estado);
});
const causaRaizRef = ref(null);
const accionPrintModalRef = ref(null);
const fileInput = ref(null);
const showActionModal = ref(false);
const showAvanceModal = ref(false);
const selectedAction = ref(null);
const selectedAvanceAction = ref(null);
const activeAvanceTab = ref('avance');

const getAssetUrl = (path) => `/storage/${path}`;
const formatDate = (date) => date ? new Date(date).toLocaleDateString() : '';
const isFechaVencida = (fecha) => fecha && new Date(fecha) < new Date().setHours(0, 0, 0, 0);

const getEstadoBadgeClass = (s) => {
    const map = { 'programada': 'badge-primary', 'en proceso': 'badge-info', 'implementada': 'badge-success', 'finalizada': 'badge-success', 'desestimada': 'badge-secondary', 'reprogramada': 'badge-warning' };
    return 'badge ' + (map[s] || 'badge-light');
};

const getStatusBadgeClass = (s) => {
    const map = { 'abierto': 'bg-info text-white', 'cerrado': 'bg-success text-white', 'en_proceso': 'bg-primary text-white', 'plan_enviado': 'bg-warning text-dark', 'aprobado': 'bg-success text-white', 'evaluado': 'bg-info text-white' };
    return map[s] || 'bg-secondary text-white';
};

const goToStep = (s) => currentStep.value = s;
const openPrintModal = () => { if (accionPrintModalRef.value) accionPrintModalRef.value.open(hallazgo.value, acciones.value, showCausaStep.value ? hallazgoStore.causaRaiz : null); };
const triggerFileInput = () => { if (['creado', 'evaluado'].includes(hallazgo.value.hallazgo_estado?.toLowerCase())) fileInput.value.click(); };
const handleFileSelect = (e) => uploadFile(e.target.files[0]);
const handleFileDrop = (e) => { if (['creado', 'evaluado'].includes(hallazgo.value.hallazgo_estado?.toLowerCase())) { isDragging.value = false; uploadFile(e.dataTransfer.files[0]); } };

const uploadFile = async (file) => {
    if (!file || file.type !== 'application/pdf') { Swal.fire({ title: 'Error', text: 'Solo PDF.', icon: 'error' }); return; }
    uploading.value = true;
    const fd = new FormData(); fd.append('file', file);
    try {
        const res = await axios.post(route('hallazgo.plan-accion.upload', { hallazgo: props.hallazgoId }), fd);
        hallazgo.value.ruta_plan_accion = res.data.path;
        Swal.fire({ title: 'Éxito', icon: 'success', toast: true, position: 'top-end', timer: 3000, showConfirmButton: false });
    } catch (e) { Swal.fire({ title: 'Error', icon: 'error' }); } finally { uploading.value = false; }
};

const enviarPlan = async () => {
    if ((await Swal.fire({ title: '¿Enviar?', icon: 'question', showCancelButton: true })).isConfirmed) {
        try {
            const res = await axios.post(route('hallazgo.plan-accion.enviar', { hallazgo: props.hallazgoId }));
            hallazgo.value.hallazgo_estado = res.data.estado;
            Swal.fire({ title: '¡Enviado!', icon: 'success' });
        } catch (e) { Swal.fire({ title: 'Error', text: e.response?.data?.error, icon: 'error' }); }
    }
};

const confirmarDesestimar = async () => {
    if ((await Swal.fire({ title: '¿Desestimar?', icon: 'warning', showCancelButton: true })).isConfirmed) {
        try {
            const res = await axios.post(route('hallazgo.desestimar', { hallazgo: props.hallazgoId }));
            hallazgo.value.hallazgo_estado = res.data.estado;
            Swal.fire({ title: 'Éxito', icon: 'success', timer: 2000, showConfirmButton: false });
        } catch (e) { Swal.fire({ title: 'Error', icon: 'error' }); }
    }
};

const openModal = (a = null, r = false) => { selectedAction.value = a; isFormReadOnly.value = r; showActionModal.value = true; };
const openAvanceModal = (a, t = 'avance') => { selectedAvanceAction.value = a; activeAvanceTab.value = t; showAvanceModal.value = true; };
const closeModal = () => { showActionModal.value = false; selectedAction.value = null; isFormReadOnly.value = false; };
const onActionSaved = () => refreshAcciones();
const refreshAcciones = () => hallazgoStore.fetchTodasLasAcciones(props.hallazgoId, true);
const closeAvanceModal = () => { showAvanceModal.value = false; selectedAvanceAction.value = null; };
const onAvanceSaved = () => refreshAcciones();

const confirmDelete = (a) => {
    Swal.fire({ title: '¿Eliminar?', icon: 'warning', showCancelButton: true }).then(async (r) => {
        if (r.isConfirmed) { try { await hallazgoStore.deleteAccion(a.id); refreshAcciones(); } catch (e) { Swal.fire({ title: 'Error', icon: 'error' }); } }
    });
};

onMounted(async () => {
    try {
        await Promise.all([hallazgoStore.fetchHallazgo(props.hallazgoId), hallazgoStore.fetchTodasLasAcciones(props.hallazgoId), hallazgoStore.fetchCausaRaiz(props.hallazgoId)]);
        if (!showCausaStep.value && currentStep.value === 1) currentStep.value = 2;
    } catch (e) { console.error(e); } finally { isPageLoading.value = false; }
});

watch(showCausaStep, (v) => { if (!v && currentStep.value === 1) currentStep.value = 2; });
</script>

<style scoped>
.min-vh-75 {
    min-height: 500px;
}

.wizard-loader {
    animation: fadeIn 0.3s ease-in-out;
}

.stepper-item {
    display: flex;
    padding: 1rem;
    margin-bottom: 0.5rem;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    border-left: 4px solid transparent;
}

.stepper-item:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.stepper-item.active {
    background-color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    border-left-color: #dc3545;
}

.stepper-item.completed .step-counter {
    background-color: #28a745;
    color: white;
}

.step-counter {
    width: 32px;
    height: 32px;
    background-color: #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-weight: bold;
    flex-shrink: 0;
}

.stepper-item.active .step-counter {
    background-color: #dc3545;
    color: white;
}

.step-name {
    font-weight: bold;
    font-size: 0.9rem;
}

.step-desc {
    font-size: 0.75rem;
    color: #6c757d;
}

.border-left-danger {
    border-left: 4px solid #dc3545;
}
</style>
