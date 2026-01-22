<template>
    <div class="container-fluid py-4">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item">
                    <router-link to="/home" class="text-danger font-weight-bold">Inicio</router-link>
                </li>
                <li class="breadcrumb-item">
                    <router-link :to="{ name: 'reportes-satisfaccion.index' }"
                        class="text-danger font-weight-bold">Reportes de Satisfacción</router-link>
                </li>
                <li class="breadcrumb-item active text-muted" aria-current="page">
                    {{ isEditing ? 'Editar' : 'Nuevo' }} Reporte
                </li>
            </ol>
        </nav>

        <div class="card shadow-sm border-0 mb-4">
            <!-- Header -->
            <div class="card-header bg-danger py-2 px-3">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <div class="d-flex align-items-center">
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm"
                                style="width: 40px; height: 40px; min-width: 40px;">
                                <i class="fas fa-file-alt text-danger" style="font-size: 0.9rem;"></i>
                            </div>
                            <div>
                                <h5 class="font-weight-bold text-white mb-0">
                                    Generar Reporte Trimestral de Satisfacción
                                </h5>
                                <p class="text-white mb-0" style="opacity: 0.9; font-size: 0.75rem;">
                                    Complete los pasos para generar el reporte consolidado del periodo.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-right mt-2 mt-md-0">
                        <button class="btn btn-link text-white text-decoration-none mr-3 px-0 btn-sm"
                            @click="router.push({ name: 'reportes-satisfaccion.index' })">
                            <i class="fas fa-arrow-left mr-1"></i> Volver al Listado
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
                                        <div class="step-name">Configuración</div>
                                        <small class="step-desc">Periodo y Proceso</small>
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
                                        <div class="step-name">Resultados</div>
                                        <small class="step-desc">Datos del Trimestre</small>
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
                                        <div class="step-name">Conclusiones</div>
                                        <small class="step-desc">Análisis con IA</small>
                                    </div>
                                </div>
                                <div class="stepper-item"
                                    :class="{ 'active': currentStep === 4, 'completed': currentStep > 4 }"
                                    @click="goToStep(4)">
                                    <div class="step-counter">
                                        <i v-if="currentStep > 4" class="fas fa-check"></i>
                                        <span v-else>4</span>
                                    </div>
                                    <div class="step-info">
                                        <div class="step-name">Vista Previa</div>
                                        <small class="step-desc">Revisión y Descarga</small>
                                    </div>
                                </div>
                                <div class="stepper-item"
                                    :class="{ 'active': currentStep === 5, 'completed': form.estado === 'firmado' }"
                                    @click="goToStep(5)">
                                    <div class="step-counter">
                                        <i v-if="form.estado === 'firmado'" class="fas fa-check"></i>
                                        <span v-else>5</span>
                                    </div>
                                    <div class="step-info">
                                        <div class="step-name">Firma y Envío</div>
                                        <small class="step-desc">Adjuntar y Finalizar</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content (Right) -->
                    <div class="col-md-9 bg-white">
                        <div class="p-4 p-lg-5">
                            <!-- Readonly Alert -->
                            <div v-if="isReadonly"
                                class="alert alert-danger-light border-0 shadow-sm mb-4 p-4 rounded-lg">
                                <div class="d-flex">
                                    <i class="fas fa-lock fa-2x text-danger mr-4"></i>
                                    <div>
                                        <h6 class="font-weight-bold text-danger mb-1">Modo Solo Lectura</h6>
                                        <p class="mb-0 text-dark small">Este reporte ya está firmado y no puede ser
                                            modificado.</p>
                                    </div>
                                </div>
                            </div>

                            <form @submit.prevent="">
                                <!-- Step 1: Configuración -->
                                <div v-show="currentStep === 1" class="step-pane fade-in">
                                    <div class="pane-header mb-4">
                                        <h4 class="text-dark font-weight-bold">1. Configuración del Reporte</h4>
                                        <p class="text-muted">Seleccione el periodo y el proceso para el cual desea
                                            generar el reporte.</p>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="font-weight-bold text-dark mb-2">Periodo (Año)</label>
                                                <select v-model="form.anio"
                                                    class="form-control form-control-lg border-2 shadow-none"
                                                    :disabled="isReadonly">
                                                    <option v-for="year in [2024, 2025, 2026]" :key="year"
                                                        :value="year">{{ year }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="font-weight-bold text-dark mb-2">Trimestre</label>
                                                <select v-model="form.trimestre"
                                                    class="form-control form-control-lg border-2 shadow-none"
                                                    :disabled="isReadonly">
                                                    <option :value="1">I Trimestre</option>
                                                    <option :value="2">II Trimestre</option>
                                                    <option :value="3">III Trimestre</option>
                                                    <option :value="4">IV Trimestre</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark mb-2">Proceso Relacionado <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light border-2 border-right-0"><i
                                                        class="fas fa-project-diagram"></i></span>
                                            </div>
                                            <input type="text"
                                                class="form-control form-control-lg border-2 border-left-0 shadow-none"
                                                :value="procesoNombre" readonly placeholder="Seleccione un proceso...">
                                            <div class="input-group-append">
                                                <button class="btn btn-danger font-weight-bold px-4" type="button"
                                                    @click="openProcesoModal" :disabled="isReadonly">
                                                    <i class="fas fa-search mr-1"></i> Buscar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2: Resultados -->
                                <div v-show="currentStep === 2" class="step-pane fade-in">
                                    <div class="pane-header mb-4 d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="text-dark font-weight-bold">2. Resultados del Trimestre</h4>
                                            <p class="text-muted">Revise los datos consolidados del periodo: <strong>{{
                                                periodoTexto }}</strong></p>
                                        </div>
                                        <button @click="refreshQuarterData"
                                            class="btn btn-outline-danger btn-sm font-weight-bold"
                                            :disabled="loading || isReadonly">
                                            <i class="fas fa-sync-alt mr-1" :class="{ 'fa-spin': loading }"></i>
                                            Actualizar Datos
                                        </button>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark mb-2">2.1 Encuestas de
                                            Satisfacción</label>
                                        <textarea v-model="form.resumen_encuestas"
                                            class="form-control border-2 shadow-none" rows="4"
                                            :disabled="isReadonly"></textarea>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark mb-2">2.2 Sugerencias</label>
                                        <textarea v-model="form.resumen_sugerencias"
                                            class="form-control border-2 shadow-none" rows="4"
                                            :disabled="isReadonly"></textarea>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark mb-2">2.3 Reclamos (Manual)</label>
                                        <textarea v-model="form.reclamos" class="form-control border-2 shadow-none"
                                            rows="4" :disabled="isReadonly"></textarea>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark mb-2">2.4 Salidas No Conformes
                                            (SNC)</label>
                                        <textarea v-model="form.resumen_snc" class="form-control border-2 shadow-none"
                                            rows="4" :disabled="isReadonly"></textarea>
                                    </div>
                                </div>

                                <!-- Step 3: Conclusiones -->
                                <div v-show="currentStep === 3" class="step-pane fade-in">
                                    <div class="pane-header mb-4 d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="text-dark font-weight-bold">3. Análisis y Conclusiones</h4>
                                            <p class="text-muted">Genere las oportunidades de mejora y conclusiones
                                                utilizando IA.</p>
                                        </div>
                                        <button @click="generateAIAnalysis"
                                            class="btn btn-danger font-weight-bold px-4 shadow-sm"
                                            :disabled="generatingAI || isReadonly">
                                            <i class="fas fa-magic mr-1"></i> {{ generatingAI ? 'Generando...' :
                                                'Generar con IA' }}
                                        </button>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark mb-2">III. Oportunidades de
                                            Mejora</label>
                                        <textarea v-model="form.oportunidades_mejora"
                                            class="form-control border-2 shadow-none" rows="8"
                                            :disabled="isReadonly"></textarea>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold text-dark mb-2">IV. Conclusiones</label>
                                        <textarea v-model="form.conclusiones" class="form-control border-2 shadow-none"
                                            rows="8" :disabled="isReadonly"></textarea>
                                    </div>
                                </div>

                                <!-- Step 4: Vista Previa -->
                                <div v-show="currentStep === 4" class="step-pane fade-in">
                                    <div class="pane-header mb-4">
                                        <h4 class="text-dark font-weight-bold">4. Revisión e Impresión</h4>
                                        <p class="text-muted">Genere la versión oficial del reporte para su firma.</p>
                                    </div>

                                    <div v-if="!form.id && !isReadonly"
                                        class="alert alert-warning shadow-sm border-0 rounded-lg py-3">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Primero debe <strong>Guardar el Borrador</strong> para habilitar las descargas.
                                    </div>

                                    <div class="card shadow-sm border-0 mb-4 bg-light rounded-lg text-center py-5">
                                        <div class="card-body">
                                            <div class="mb-4">
                                                <div class="bg-white rounded-circle shadow-sm d-inline-flex p-4">
                                                    <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                                </div>
                                            </div>
                                            <h5 class="font-weight-bold mb-2 text-dark">Imprimir Reporte Trimestral</h5>
                                            <p class="text-muted mb-4 px-5 mx-auto" style="max-width: 600px;">
                                                Documento consolidado para ser firmado por el responsable del proceso.
                                            </p>

                                            <div class="d-flex justify-content-center flex-wrap gap-2">
                                                <button
                                                    class="btn btn-outline-danger px-4 py-2 shadow-sm rounded-pill font-weight-bold m-1"
                                                    @click="openPrintModal">
                                                    <i class="fas fa-eye mr-2"></i> Vista Previa PDF
                                                </button>
                                                <button v-if="form.id"
                                                    class="btn btn-danger px-4 py-2 shadow-sm rounded-pill font-weight-bold m-1"
                                                    @click="downloadWord">
                                                    <i class="fas fa-file-word mr-2"></i> Descargar Word
                                                </button>
                                                <button v-else
                                                    class="btn btn-outline-primary px-4 py-2 shadow-sm rounded-pill font-weight-bold m-1"
                                                    @click="saveDraft" :disabled="loading">
                                                    <i class="fas fa-save mr-2"></i> Guardar Borrador
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 5: Firma y Envío -->
                                <div v-show="currentStep === 5" class="step-pane fade-in">
                                    <div class="pane-header mb-4 border-bottom pb-3">
                                        <h4 class="text-dark font-weight-bold">5. Firma y Envío</h4>
                                        <p class="text-muted">Adjunte el reporte firmado y finalice el proceso.</p>
                                    </div>

                                    <div class="doc-upload-section">
                                        <div v-if="form.archivo_path" class="mb-4">
                                            <div class="card border-0 shadow-sm bg-success-light">
                                                <div class="card-body p-3 d-flex align-items-center">
                                                    <div class="bg-success text-white rounded-circle mr-3 d-flex align-items-center justify-content-center"
                                                        style="width: 36px; height: 36px;">
                                                        <i class="fas fa-check"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0 font-weight-bold text-success-dark">Reporte
                                                            Firmado Cargado</h6>
                                                        <a :href="getAssetUrl(form.archivo_path)" target="_blank"
                                                            class="text-success small">Ver Archivo Firmado.pdf</a>
                                                    </div>
                                                    <button
                                                        class="btn btn-sm btn-white text-danger font-weight-bold shadow-sm"
                                                        @click="triggerFileInput" v-if="!isReadonly">
                                                        <i class="fas fa-sync-alt mr-1"></i> Reemplazar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="drop-zone-premium shadow-sm border-2 rounded-lg py-5 text-center bg-white"
                                            @click="triggerFileInput" v-if="!isReadonly"
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
                                                <h5 class="font-weight-bold text-dark mb-1">Arrastre aquí el reporte
                                                    firmado</h5>
                                                <p class="text-muted small">o haga clic para seleccionar archivo (PDF
                                                    máximo 10MB)</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-5 pt-3 border-top text-right ml-auto">
                                        <button
                                            class="btn btn-success px-5 py-2 font-weight-bold shadow-sm rounded-pill"
                                            @click="enviarReporte"
                                            :disabled="!form.archivo_path || uploading || isReadonly">
                                            <i class="fas fa-paper-plane mr-2"></i> {{ isReadonly ? 'Reporte Enviado'
                                                : 'Enviar Reporte' }}
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Navigation Footer -->
                            <div class="navigation-footer d-flex justify-content-between pt-5 mt-5 border-top">
                                <button v-if="currentStep > 1"
                                    class="btn btn-light px-4 py-2 font-weight-bold text-muted border"
                                    @click="prevStep">
                                    <i class="fas fa-chevron-left mr-2"></i> Anterior
                                </button>
                                <div v-else></div>

                                <div class="ml-auto">
                                    <button v-if="currentStep < 5"
                                        class="btn btn-danger px-5 py-2 font-weight-bold shadow-sm rounded-pill"
                                        @click="handleNextClick" :disabled="currentStep === 1 && !form.proceso_id">
                                        Siguiente <i class="fas fa-chevron-right ml-2"></i>
                                    </button>
                                    <router-link v-if="currentStep === 5" :to="{ name: 'reportes-satisfaccion.index' }"
                                        class="btn btn-light px-4 py-2 font-weight-bold text-muted border rounded-pill">
                                        <i class="fas fa-list mr-2"></i> Volver al Listado
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals -->
        <ModalHijo ref="procesoModal" :fetch-url="procesoFetchUrl" target-id="proceso_id" target-desc="proceso_nombre"
            @update-target="handleProcesoSeleccionado" />
        <ReporteSatisfaccionPrintModal ref="reportePrintModalRef" />
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useReporteSatisfaccionStore } from '../../stores/reporteSatisfaccionStore';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import ModalHijo from '../generales/ModalHijo.vue';
import ReporteSatisfaccionPrintModal from './ReporteSatisfaccionPrintModal.vue';
import { route } from 'ziggy-js';
import axios from 'axios';

const props = defineProps({
    id: { type: [String, Number], default: null }
});

const store = useReporteSatisfaccionStore();
const router = useRouter();

const currentStep = ref(1);
const loading = computed(() => store.loading);
const generatingAI = ref(false);
const editorRefreshKey = ref(0);
const procesoModal = ref(null);
const reportePrintModalRef = ref(null);
const fileInput = ref(null);
const uploading = ref(false);
const isDragging = ref(false);

const periodoTexto = ref('');
const procesoNombre = ref('');

const isEditing = computed(() => !!props.id);
const isReadonly = computed(() => form.value.estado === 'firmado');
const reporteSaved = ref(false);
const procesoFetchUrl = computed(() => route('procesos.buscar'));

const form = ref({
    id: null, anio: new Date().getFullYear(), trimestre: 1, proceso_id: null,
    resumen_encuestas: '', resumen_sugerencias: '', reclamos: 'No se registraron reclamos.',
    resumen_snc: '', oportunidades_mejora: '', conclusiones: '', estado: 'borrador', archivo_path: null
});

const loadReporte = async () => {
    if (!props.id) return;
    try {
        const reporte = await store.fetchReporte(props.id);
        if (reporte) {
            form.value = { ...reporte };
            if (reporte.proceso) procesoNombre.value = reporte.proceso.proceso_nombre;
            periodoTexto.value = `${form.value.anio} - Trimestre ${form.value.trimestre}`;
            reporteSaved.value = true;
            editorRefreshKey.value++;
        }
    } catch (e) {
        Swal.fire('Error', 'No se pudo cargar el reporte.', 'error');
    }
};

onMounted(loadReporte);
watch(() => props.id, (newId) => { if (newId) loadReporte(); });

const openProcesoModal = () => procesoModal.value?.open();
const handleProcesoSeleccionado = (data) => {
    form.value.proceso_id = data.idValue;
    procesoNombre.value = data.descValue;
};

const goToStep = (step) => {
    if (step > 1 && !form.value.proceso_id) {
        return Swal.fire('Atención', 'Debe seleccionar un proceso primero.', 'warning');
    }
    currentStep.value = step;
};

const nextStep = () => currentStep.value++;
const prevStep = () => currentStep.value--;
const handleNextClick = () => currentStep.value === 1 ? fetchDataAndNext() : nextStep();

const fetchDataAndNext = async () => {
    if (!form.value.proceso_id) return;
    try {
        const data = await store.getQuarterData({
            proceso_id: form.value.proceso_id, anio: form.value.anio, trimestre: form.value.trimestre
        });
        if (!form.value.resumen_encuestas) form.value.resumen_encuestas = data.resumen_encuestas;
        if (!form.value.resumen_sugerencias) form.value.resumen_sugerencias = data.resumen_sugerencias;
        if (!form.value.resumen_snc) form.value.resumen_snc = data.resumen_snc;
        periodoTexto.value = data.periodo_texto;
        currentStep.value = 2;
    } catch (e) {
        Swal.fire('Error', 'No se pudieron cargar los datos.', 'error');
    }
};

const refreshQuarterData = async () => {
    if (!form.value.proceso_id) return;
    try {
        const data = await store.getQuarterData({
            proceso_id: form.value.proceso_id, anio: form.value.anio, trimestre: form.value.trimestre
        });
        form.value.resumen_encuestas = data.resumen_encuestas;
        form.value.resumen_sugerencias = data.resumen_sugerencias;
        form.value.resumen_snc = data.resumen_snc;
        periodoTexto.value = data.periodo_texto;
        Swal.fire({ title: '¡Datos Actualizados!', icon: 'success', timer: 2000 });
    } catch (e) {
        Swal.fire('Error', 'No se pudo actualizar.', 'error');
    }
};

const generateAIAnalysis = async () => {
    generatingAI.value = true;
    try {
        const analysis = await store.generateDraft({ ...form.value, proceso_nombre: procesoNombre.value });
        form.value.oportunidades_mejora = analysis.oportunidades || form.value.oportunidades_mejora;
        form.value.conclusiones = analysis.conclusiones || form.value.conclusiones;
        editorRefreshKey.value++;
        Swal.fire({ title: '¡Análisis Generado!', icon: 'success', timer: 2000 });
    } catch (e) {
        Swal.fire('Error', 'Falló la IA.', 'error');
    } finally {
        generatingAI.value = false;
    }
};

const saveDraft = async () => {
    try {
        if (isEditing.value && form.value.id) await store.updateReporte(form.value.id, form.value);
        else {
            const result = await store.createReporte(form.value);
            form.value.id = result.id;
        }
        reporteSaved.value = true;
        Swal.fire({ title: '¡Guardado!', icon: 'success', timer: 1500 });
    } catch (e) {
        Swal.fire('Error', 'No se pudo guardar.', 'error');
    }
};

const downloadWord = async () => {
    try {
        const response = await axios.get(`/api/reportes-satisfaccion/${form.value.id}/download`, { responseType: 'blob' });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const a = document.createElement('a');
        a.href = url;
        a.download = `Reporte_${form.value.anio}_T${form.value.trimestre}.docx`;
        a.click();
    } catch (e) {
        Swal.fire('Error', 'Falla al descargar.', 'error');
    }
};

const openPrintModal = () => reportePrintModalRef.value?.open({ ...form.value, proceso: { proceso_nombre: procesoNombre.value } });
const triggerFileInput = () => fileInput.value?.click();
const handleFileSelect = (e) => uploadFile(e.target.files[0]);
const handleFileDrop = (e) => { isDragging.value = false; uploadFile(e.dataTransfer.files[0]); };

const uploadFile = async (file) => {
    if (!file || file.type !== 'application/pdf') return Swal.fire('Error', 'Solo PDF.', 'error');
    uploading.value = true;
    try {
        const resp = await store.uploadSigned(form.value.id, file);
        form.value.archivo_path = resp.path;
        Swal.fire({ title: 'Archivo Cargado', text: 'Ahora puede proceder a enviar el reporte.', icon: 'success', timer: 2000 });
    } catch (e) {
        Swal.fire('Error', 'Falla al subir.', 'error');
    } finally { uploading.value = false; }
};

const enviarReporte = async () => {
    const result = await Swal.fire({ title: '¿Enviar Reporte?', text: 'Se cerrará el reporte y no podrá ser modificado.', icon: 'question', showCancelButton: true });
    if (result.isConfirmed) {
        try {
            form.value.estado = 'firmado';
            await store.updateReporte(form.value.id, form.value);
            Swal.fire('¡Éxito!', 'Reporte enviado correctamente.', 'success');
        } catch (e) { Swal.fire('Error', 'Falla al enviar.', 'error'); }
    }
};

const getAssetUrl = (path) => `/storage/${path}`;
</script>

<style scoped>
.min-vh-75 {
    min-height: 75vh;
}

.bg-success-light {
    background-color: #f0fff4;
}

.text-success-dark {
    color: #22543d;
}

.drop-zone-premium {
    border: 2px dashed #e2e8f0;
    transition: all 0.3s ease;
    cursor: pointer;
}

.drop-zone-premium:hover,
.drop-zone-premium.dragging {
    border-color: #dc3545;
    background-color: #fff5f5;
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
    border-color: #28a745;
}

.stepper-item.completed .step-name {
    color: #28a745;
}

.step-counter {
    width: 34px;
    height: 34px;
    min-width: 34px;
    border-radius: 50%;
    border: 2px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.85rem;
    color: #adb5bd;
    margin-right: 1rem;
}

.stepper-item.active .step-counter {
    border-color: #dc3545;
    color: #dc3545;
    background-color: white;
    transform: scale(1.1);
}

.step-name {
    font-weight: 700;
    font-size: 0.95rem;
    color: #6c757d;
}

.stepper-item.active .step-name {
    color: #dc3545;
}

.step-desc {
    color: #adb5bd;
    line-height: 1.2;
    font-size: 0.75rem;
}

.fade-in {
    animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(5px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.breadcrumb {
    font-size: 0.85rem;
}

.alert-danger-light {
    background-color: #fef2f2;
    border: 1px solid #fee2e2;
}
</style>
