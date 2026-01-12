<template>
    <div class="container-fluid">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light py-2 px-3 rounded mb-3">
                <li class="breadcrumb-item"><router-link to="/home">Inicio</router-link></li>
                <li class="breadcrumb-item"><router-link :to="{ name: 'reportes-satisfaccion.index' }">Reportes de
                        Satisfacción</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">{{ isEditing ? 'Editar' : 'Nuevo' }} Reporte</li>
            </ol>
        </nav>

        <div class="card shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h3 class="card-title mb-0">
                    <i class="fas fa-file-alt text-primary mr-2"></i>
                    Generar Reporte Trimestral de Satisfacción
                </h3>
            </div>
            <div class="card-body">
                <div class="animate__animated animate__fadeIn">
                    <div class="row">
                        <!-- Stepper Sidebar -->
                        <div class="col-md-3 mb-4">
                            <div class="stepper-sidebar sticky-top" style="top: 20px; z-index: 1;">
                                <div class="stepper-wrapper">
                                    <!-- Step 1: Configuración -->
                                    <div class="stepper-item"
                                        :class="{ completed: currentStep > 1, active: currentStep === 1 }"
                                        @click="goToStep(1)">
                                        <div class="step-counter">
                                            <i v-if="currentStep > 1" class="fas fa-check"></i>
                                            <span v-else>1</span>
                                        </div>
                                        <div class="step-info">
                                            <div class="step-name">Configuración</div>
                                            <small class="step-desc">Selección de Periodo y Proceso</small>
                                        </div>
                                    </div>

                                    <!-- Step 2: Resultados -->
                                    <div class="stepper-item"
                                        :class="{ completed: currentStep > 2, active: currentStep === 2 }"
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

                                    <!-- Step 3: Conclusiones -->
                                    <div class="stepper-item"
                                        :class="{ completed: currentStep > 3, active: currentStep === 3 }"
                                        @click="goToStep(3)">
                                        <div class="step-counter">
                                            <i v-if="currentStep > 3" class="fas fa-check"></i>
                                            <span v-else>3</span>
                                        </div>
                                        <div class="step-info">
                                            <div class="step-name">Conclusiones</div>
                                            <small class="step-desc">Oportunidades y Conclusiones</small>
                                        </div>
                                    </div>

                                    <!-- Step 4: Finalización -->
                                    <div class="stepper-item"
                                        :class="{ completed: currentStep > 4, active: currentStep === 4 }"
                                        @click="goToStep(4)">
                                        <div class="step-counter">
                                            <i v-if="currentStep > 4" class="fas fa-check"></i>
                                            <span v-else>4</span>
                                        </div>
                                        <div class="step-info">
                                            <div class="step-name">Finalización</div>
                                            <small class="step-desc">Revisar y Guardar</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Main Content Area -->
                        <div class="col-md-9">
                            <div class="card card-primary card-outline shadow-sm border-0">
                                <div class="card-body">
                                    <!-- Readonly Alert -->
                                    <div v-if="isReadonly" class="alert alert-warning border-warning shadow-sm mb-4">
                                        <i class="fas fa-lock mr-2"></i>
                                        <strong>Modo Solo Lectura:</strong> Este reporte ya está firmado y no puede ser
                                        modificado.
                                    </div>

                                    <!-- PASO 1: Configuración -->
                                    <div v-if="currentStep === 1"
                                        class="step-content animate__animated animate__fadeIn">
                                        <div class="step-header mb-4 pb-2 border-bottom">
                                            <h4 class="text-dark"><i class="fas fa-cog text-primary mr-2"></i>
                                                Configuración del Reporte</h4>
                                            <p class="text-muted small">Seleccione el periodo y el proceso para el cual
                                                desea generar el reporte.</p>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Periodo (Año)</label>
                                                    <select v-model="form.anio" class="form-control"
                                                        :disabled="isReadonly">
                                                        <option v-for="year in [2024, 2025, 2026]" :key="year"
                                                            :value="year">{{ year }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Trimestre</label>
                                                    <select v-model="form.trimestre" class="form-control"
                                                        :disabled="isReadonly">
                                                        <option :value="1">I Trimestre</option>
                                                        <option :value="2">II Trimestre</option>
                                                        <option :value="3">III Trimestre</option>
                                                        <option :value="4">IV Trimestre</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Proceso</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" :value="procesoNombre" readonly
                                                    :disabled="isReadonly" placeholder="Seleccione un proceso..."
                                                    style="background-color: white;">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-primary" type="button"
                                                        @click="openProcesoModal" :disabled="isReadonly">
                                                        <i class="fas fa-search"></i> Buscar
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted">Utilice el botón de búsqueda para seleccionar el
                                                proceso.</small>
                                        </div>

                                        <div class="text-right mt-4">
                                            <button class="btn btn-primary px-4 shadow-sm" @click="fetchDataAndNext"
                                                :disabled="!form.proceso_id || isReadonly">
                                                Siguiente <i class="fas fa-arrow-right ml-2"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- PASO 2: Resultados Preeliminares -->
                                    <div v-if="currentStep === 2"
                                        class="step-content animate__animated animate__fadeIn">
                                        <div
                                            class="step-header mb-4 pb-2 border-bottom d-flex justify-content-between align-items-center">
                                            <div>
                                                <h4 class="text-dark mb-1"><i
                                                        class="fas fa-chart-bar text-primary mr-2"></i> Resultados del
                                                    Trimestre</h4>
                                                <p class="text-muted small mb-0">Revise los datos consolidados del
                                                    periodo: <strong>{{ periodoTexto }}</strong></p>
                                            </div>
                                            <button @click="refreshQuarterData" class="btn btn-primary btn-sm shadow"
                                                :disabled="loading || isReadonly">
                                                <i class="fas fa-sync-alt mr-1" :class="{ 'fa-spin': loading }"></i>
                                                {{ loading ? 'Actualizando...' : 'Actualizar Datos' }}
                                            </button>
                                        </div>

                                        <div class="card mb-3 border-light shadow-sm">
                                            <div class="card-header bg-light"><strong>2.1 Encuestas de
                                                    Satisfacción</strong></div>
                                            <div class="card-body p-2">
                                                <textarea v-model="form.resumen_encuestas" class="form-control" rows="5"
                                                    placeholder="Resumen de encuestas..."
                                                    :disabled="isReadonly"></textarea>
                                            </div>
                                        </div>

                                        <div class="card mb-3 border-light shadow-sm">
                                            <div class="card-header bg-light"><strong>2.2 Sugerencias</strong></div>
                                            <div class="card-body p-2">
                                                <textarea v-model="form.resumen_sugerencias" class="form-control"
                                                    rows="5" placeholder="Resumen de sugerencias..."
                                                    :disabled="isReadonly"></textarea>
                                            </div>
                                        </div>

                                        <div class="card mb-3 border-warning shadow-sm"
                                            style="border-left: 4px solid #ffc107;">
                                            <div class="card-header bg-white"><strong>2.3 Reclamos (Ingreso
                                                    Manual)</strong></div>
                                            <div class="card-body p-2">
                                                <textarea v-model="form.reclamos" class="form-control" rows="5"
                                                    placeholder="Reclamos reportados..."
                                                    :disabled="isReadonly"></textarea>
                                            </div>
                                        </div>

                                        <div class="card mb-3 border-light shadow-sm">
                                            <div class="card-header bg-light"><strong>2.4 Salidas No Conformes
                                                    (SNC)</strong></div>
                                            <div class="card-body p-2">
                                                <textarea v-model="form.resumen_snc" class="form-control" rows="5"
                                                    placeholder="Resumen de SNCs..." :disabled="isReadonly"></textarea>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between mt-4">
                                            <button class="btn btn-outline-secondary" @click="prevStep">
                                                <i class="fas fa-arrow-left mr-2"></i> Atrás
                                            </button>
                                            <button class="btn btn-primary px-4 shadow-sm" @click="nextStep">
                                                Siguiente <i class="fas fa-arrow-right ml-2"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- PASO 3: Conclusiones -->
                                    <div v-if="currentStep === 3"
                                        class="step-content animate__animated animate__fadeIn">
                                        <div
                                            class="step-header mb-4 pb-2 border-bottom d-flex justify-content-between align-items-center">
                                            <div>
                                                <h4 class="text-dark mb-1"><i
                                                        class="fas fa-lightbulb text-warning mr-2"></i> Conclusiones
                                                </h4>
                                                <p class="text-muted small mb-0">Genere oportunidades de mejora y
                                                    conclusiones utilizando IA.</p>
                                            </div>
                                            <button @click="generateAIAnalysis" class="btn btn-purple btn-sm shadow"
                                                :disabled="generatingAI || isReadonly">
                                                <i class="fas fa-magic mr-1"></i>
                                                {{ generatingAI ? 'Generando...' : 'Generar con IA' }}
                                                <span v-if="generatingAI"
                                                    class="spinner-border spinner-border-sm ml-1"></span>
                                            </button>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">III. Oportunidades de Mejora
                                                Identificadas</label>
                                            <textarea v-model="form.oportunidades_mejora" class="form-control" rows="10"
                                                placeholder="Escriba las oportunidades de mejora..."
                                                :disabled="isReadonly"></textarea>
                                            <small class="text-muted">Use guiones (-) para listas y saltos de línea para
                                                separar párrafos</small>
                                        </div>

                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">IV. Conclusiones</label>
                                            <textarea v-model="form.conclusiones" class="form-control" rows="10"
                                                placeholder="Escriba las conclusiones..."
                                                :disabled="isReadonly"></textarea>
                                            <small class="text-muted">Use saltos de línea para separar párrafos</small>
                                        </div>

                                        <div class="d-flex justify-content-between mt-4">
                                            <button class="btn btn-outline-secondary" @click="prevStep">
                                                <i class="fas fa-arrow-left mr-2"></i> Atrás
                                            </button>
                                            <button class="btn btn-primary px-4 shadow-sm" @click="nextStep">
                                                Siguiente <i class="fas fa-arrow-right ml-2"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- PASO 4: Confirmación -->
                                    <div v-if="currentStep === 4"
                                        class="step-content animate__animated animate__fadeIn">
                                        <div class="step-header mb-4 pb-2 border-bottom">
                                            <h4 class="text-dark"><i class="fas fa-check-circle text-success mr-2"></i>
                                                Finalización</h4>
                                            <p class="text-muted small">Revise la información y genere el reporte
                                                oficial.</p>
                                        </div>

                                        <div class="alert alert-light border shadow-sm">
                                            <h5><i class="fas fa-info-circle text-info mr-2"></i> Resumen del Reporte
                                            </h5>
                                            <ul class="mt-2 mb-0">
                                                <li><strong>Proceso:</strong> {{ procesoNombre }}</li>
                                                <li><strong>Periodo:</strong> {{ form.anio }} - Trimestre {{
                                                    form.trimestre }}</li>
                                                <li><strong>Encuestas:</strong> {{ form.resumen_encuestas ?
                                                    'Registradas' : 'Sin datos' }}</li>
                                                <li><strong>Análisis IA:</strong> {{ form.oportunidades_mejora ?
                                                    'Generado' : 'Pendiente' }}</li>
                                            </ul>
                                        </div>

                                        <div class="text-center py-4">
                                            <button v-if="!isReadonly && !reporteSaved" @click="saveDraft"
                                                class="btn btn-danger btn-md px-4 shadow mr-3">
                                                <i class="fas fa-save mr-2"></i> Guardar y Generar Reporte
                                            </button>
                                            <button v-if="reporteSaved" @click="downloadWord"
                                                class="btn btn-primary btn-md px-4 shadow">
                                                <i class="fas fa-download mr-2"></i> Descargar Word
                                            </button>
                                        </div>

                                        <div class="d-flex justify-content-between mt-4 border-top pt-3">
                                            <button class="btn btn-secondary" @click="prevStep">
                                                <i class="fas fa-arrow-left mr-2"></i> Atrás
                                            </button>
                                            <router-link :to="{ name: 'reportes-satisfaccion.index' }"
                                                class="btn btn-outline-secondary">
                                                <i class="fas fa-list mr-2"></i> Volver al Listado
                                            </router-link>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading Overlay -->
            <div v-if="loading" class="overlay">
                <div class="spinner-container text-center">
                    <i class="fas fa-3x fa-sync-alt fa-spin text-white"></i>
                    <p class="text-white mt-2 font-weight-bold">Procesando...</p>
                </div>
            </div>


            <!-- Modal Hijo para Selección de Proceso -->
            <ModalHijo ref="procesoModal" :fetch-url="procesoFetchUrl" target-id="proceso_id"
                target-desc="proceso_nombre" @update-target="handleProcesoSeleccionado" />
        </div> <!-- /card -->
    </div> <!-- /container-fluid -->
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useReporteSatisfaccionStore } from '../../stores/reporteSatisfaccionStore';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import ModalHijo from '../generales/ModalHijo.vue';
import { route } from 'ziggy-js';
import axios from 'axios'; // Import Axios

const props = defineProps({
    id: {
        type: [String, Number],
        default: null
    }
});

const store = useReporteSatisfaccionStore();
const router = useRouter();

const currentStep = ref(1);
const loading = computed(() => store.loading);
const generatingAI = ref(false);
const editorRefreshKey = ref(0);
const procesoModal = ref(null);

const periodoTexto = ref('');
const procesoNombre = ref('');

const isEditing = computed(() => !!props.id);
const isReadonly = computed(() => form.value.estado === 'firmado');
const reporteSaved = ref(false);
const procesoFetchUrl = computed(() => route('procesos.buscar'));

const form = ref({
    id: null, // Add ID for update handling
    anio: new Date().getFullYear(),
    trimestre: 4,
    proceso_id: null,
    resumen_encuestas: '',
    resumen_sugerencias: '',
    reclamos: 'No se registraron reclamos en este periodo.',
    resumen_snc: '',
    oportunidades_mejora: '',
    conclusiones: '',
    estado: 'borrador',
    fecha_generacion: null
});

onMounted(async () => {
    if (isEditing.value) {
        try {
            console.log('Fetching single report:', props.id);
            const reporte = await store.fetchReporte(props.id);

            console.log('Reporte fetched:', reporte);

            if (reporte) {
                form.value = { ...reporte };

                if (reporte.proceso) {
                    console.log('Proceso loaded:', reporte.proceso);
                    procesoNombre.value = reporte.proceso.proceso_nombre;
                } else {
                    procesoNombre.value = 'Proceso no encontrado (ID: ' + reporte.proceso_id + ')';
                }

                // Set period text
                periodoTexto.value = `${form.value.anio} - Trimestre ${form.value.trimestre}`;

                // Force editor refresh
                editorRefreshKey.value++;
            }
        } catch (e) {
            console.error('Error fetching report:', e);
            Swal.fire('Error', 'No se pudo cargar el reporte.', 'error').then(() => {
                router.push({ name: 'reportes-satisfaccion.index' });
            });
        }
    }
});

const openProcesoModal = () => {
    if (procesoModal.value) {
        procesoModal.value.open();
    }
};

const handleProcesoSeleccionado = (data) => {
    form.value.proceso_id = data.idValue;
    procesoNombre.value = data.descValue;
};

const goToStep = (step) => {
    // Basic validation to prevent jumping ahead without data
    if (step > 1 && !form.value.proceso_id) {
        Swal.fire('Atención', 'Debe seleccionar un proceso primero.', 'warning');
        return;
    }

    // If jumping to step 2 or more, ensure we fetched data if coming from step 1
    if (currentStep.value === 1 && step > 1) {
        fetchDataAndNext(); // This handles the jump inside
        return;
    }

    currentStep.value = step;
};

const nextStep = () => currentStep.value++;
const prevStep = () => currentStep.value--;

const fetchDataAndNext = async () => {
    if (!form.value.proceso_id) return;

    try {
        const data = await store.getQuarterData({
            proceso_id: form.value.proceso_id,
            anio: form.value.anio,
            trimestre: form.value.trimestre
        });

        // Populate form if empty or update (logic can be refined to not overwrite user edits)
        if (!form.value.resumen_encuestas) form.value.resumen_encuestas = data.resumen_encuestas;
        if (!form.value.resumen_sugerencias) form.value.resumen_sugerencias = data.resumen_sugerencias;
        if (!form.value.resumen_snc) form.value.resumen_snc = data.resumen_snc;
        periodoTexto.value = data.periodo_texto;

        currentStep.value = 2;
    } catch (e) {
        console.error(e);
        Swal.fire('Error', 'No se pudieron cargar los datos del trimestre.', 'error');
    }
};

const refreshQuarterData = async () => {
    if (!form.value.proceso_id) return;

    try {
        const data = await store.getQuarterData({
            proceso_id: form.value.proceso_id,
            anio: form.value.anio,
            trimestre: form.value.trimestre
        });

        // Overwrite all data with fresh values
        form.value.resumen_encuestas = data.resumen_encuestas;
        form.value.resumen_sugerencias = data.resumen_sugerencias;
        form.value.resumen_snc = data.resumen_snc;
        periodoTexto.value = data.periodo_texto;

        Swal.fire({
            title: '¡Datos Actualizados!',
            text: 'Los datos del trimestre se han actualizado correctamente.',
            icon: 'success',
            timer: 2000
        });
    } catch (e) {
        console.error(e);
        Swal.fire('Error', 'No se pudieron actualizar los datos del trimestre.', 'error');
    }
};

const generateAIAnalysis = async () => {
    generatingAI.value = true;
    try {
        const payload = {
            ...form.value,
            proceso_nombre: procesoNombre.value
        };
        const analysis = await store.generateDraft(payload);

        console.log('AI Analysis Result:', analysis);

        // Ensure reactivity
        form.value.oportunidades_mejora = analysis.oportunidades || form.value.oportunidades_mejora;
        form.value.conclusiones = analysis.conclusiones || form.value.conclusiones;

        // Force editor refresh
        editorRefreshKey.value++;

        // Force editor refresh if needed (usually v-model handles it, but just in case)
        // If keys are missing in JSON, show alert
        if (!analysis.oportunidades && !analysis.conclusiones) {
            Swal.fire('Atención', 'La IA no devolvió contenido válido. Por favor intente nuevamente.', 'warning');
        }

        Swal.fire({
            title: '¡Análisis Generado!',
            text: 'La IA ha propuesto oportunidades y conclusiones. Revísalas y edítalas si es necesario.',
            icon: 'success',
            timer: 3000
        });
    } catch (e) {
        console.error(e);
        Swal.fire('Error', 'Falló la generación IA. Intente nuevamente.', 'error');
    } finally {
        generatingAI.value = false;
    }
};

const saveDraft = async () => {
    if (!form.value.proceso_id) {
        Swal.fire('Error', 'Debe seleccionar un proceso.', 'error');
        return;
    }

    try {
        if (isEditing.value && form.value.id) {
            await store.updateReporte(form.value.id, form.value);
        } else {
            const result = await store.createReporte(form.value);
            form.value.id = result.id;
        }

        reporteSaved.value = true;

        Swal.fire({
            title: '¡Éxito!',
            text: 'El reporte ha sido guardado correctamente. Ahora puede descargarlo en formato Word.',
            icon: 'success',
            timer: 3000
        });
    } catch (e) {
        console.error(e);
        Swal.fire('Error', 'No se pudo guardar el reporte.', 'error');
    }
};

const downloadWord = async () => {
    if (!form.value.id) return;

    try {
        const response = await axios.get(`/api/reportes-satisfaccion/${form.value.id}/download`, {
            responseType: 'blob',
            headers: {
                'Accept': 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            }
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const a = document.createElement('a');
        a.href = url;
        a.download = `Reporte_Satisfaccion_${form.value.anio}_T${form.value.trimestre}.docx`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    } catch (error) {
        console.error('Error downloading:', error);
        Swal.fire('Error', 'No se pudo descargar el reporte. Verifique que haya iniciado sesión.', 'error');
    }
};
</script>

<style scoped>
/* Stepper Styles copied and adapted */
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
    background-color: #007bff;
    /* Primary Blue */
    color: white;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
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
    color: #007bff;
}

.stepper-item.completed .step-name {
    color: #28a745;
}

/* Custom colors */
.text-purple {
    color: #6f42c1 !important;
}

.btn-purple {
    background-color: #6f42c1;
    color: white;
}

.btn-purple:hover {
    background-color: #5a32a3;
    color: white;
}

.overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
