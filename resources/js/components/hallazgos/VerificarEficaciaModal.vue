<template>
    <div class="modal fade" id="verificarEficaciaModal" tabindex="-1" role="dialog"
        aria-labelledby="verificarEficaciaModalLabel" aria-hidden="true" ref="modalRef">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="verificarEficaciaModalLabel">Verificación de Eficacia</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
                        @click="cerrarModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2">
                        <strong>Hallazgo:</strong> {{ hallazgo?.hallazgo_cod }} - {{ hallazgo?.hallazgo_resumen }}
                    </div>

                    <ul class="nav nav-tabs mb-3" id="verificationTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="evidencias-tab" data-toggle="tab" href="#evidencias"
                                role="tab" aria-controls="evidencias" aria-selected="true">
                                <i class="fas fa-paperclip mr-1"></i> Evidencias
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="evaluacion-tab" data-toggle="tab" href="#evaluacion" role="tab"
                                aria-controls="evaluacion" aria-selected="false">
                                <i class="fas fa-check-circle mr-1"></i> Evaluación
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" id="verificationTabsContent">
                        <!-- TAB 1: EVIDENCIAS -->
                        <div class="tab-pane fade show active" id="evidencias" role="tabpanel"
                            aria-labelledby="evidencias-tab">

                            <!-- Lista de archivos ya subidos -->
                            <div v-if="evidencias.length > 0" class="mb-4">
                                <h6 class="font-weight-bold text-dark d-flex align-items-center mb-3">
                                    <i class="fas fa-archive text-danger mr-2"></i> Documentos Subidos:
                                </h6>
                                <div class="list-group shadow-sm rounded-lg">
                                    <div v-for="(file, index) in evidencias" :key="index"
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 border-left-danger">
                                        <a :href="file.path" target="_blank"
                                            class="text-decoration-none text-dark text-truncate"
                                            style="max-width: 85%;">
                                            <i class="fas fa-file-pdf mr-2 text-danger"></i> {{ file.name }}
                                        </a>
                                        <span class="badge badge-light badge-pill">Existente</span>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="mb-4 text-center py-4 bg-light rounded-lg border border-dashed">
                                <i class="fas fa-info-circle text-muted fa-2x mb-2 d-xl-block"></i>
                                <p class="text-muted mb-0 small">No hay documentos de evidencia subidos aún.</p>
                            </div>

                            <!-- Zona de Carga de Archivos -->
                            <div class="card border-0 shadow-sm rounded-lg">
                                <div class="card-body p-0">
                                    <div class="drop-zone" :class="{ 'drag-over': isDragging }"
                                        @dragenter.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
                                        @dragover.prevent @drop.prevent="onDrop" @click="$refs.fileInput.click()">

                                        <input type="file" ref="fileInput" class="d-none" multiple
                                            @change="handleFileUpload"
                                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">

                                        <div class="text-center py-2">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-danger opacity-50"></i>
                                            <h6 class="mt-3 font-weight-bold text-dark">Subir Nuevas Evidencias</h6>
                                            <p class="text-muted small mb-0">Arrastre archivos aquí o haga clic para
                                                seleccionar</p>
                                            <small class="text-muted mt-2 d-block" style="font-size: 0.7rem;">Formatos
                                                aceptados: PDF, Imágenes, Office (Máx 10MB por archivo)</small>
                                        </div>
                                    </div>

                                    <!-- Lista de archivos seleccionados pendientes de subir -->
                                    <div v-if="selectedFiles.length > 0" class="p-3 bg-white border-top">
                                        <h6 class="small font-weight-bold text-secondary mb-3">Archivos seleccionados
                                            ({{ selectedFiles.length }}):</h6>
                                        <ul class="list-group list-group-flush mb-3">
                                            <li v-for="(file, index) in selectedFiles" :key="index"
                                                class="list-group-item px-0 d-flex justify-content-between align-items-center py-2">
                                                <div class="d-flex align-items-center overflow-hidden">
                                                    <i class="fas fa-file-alt text-muted mr-3"></i>
                                                    <div class="text-truncate">
                                                        <span class="small font-weight-bold d-block text-truncate">{{
                                                            file.name }}</span>
                                                        <span class="text-muted" style="font-size: 0.65rem;">{{
                                                            (file.size / 1024).toFixed(1) }} KB</span>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-outline-danger btn-xs border-0"
                                                    @click.stop="removeSelectedFile(index)">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </li>
                                        </ul>
                                        <div class="text-right">
                                            <button type="button"
                                                class="btn btn-danger btn-sm rounded-pill px-4 shadow-sm"
                                                @click="subirArchivos" :disabled="loadingUpload">
                                                <span v-if="loadingUpload" class="spinner-border spinner-border-sm mr-2"
                                                    role="status"></span>
                                                <i class="fas fa-upload mr-2" v-else></i> Subir Seleccionados
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: EVALUACIÓN -->
                        <div class="tab-pane fade" id="evaluacion" role="tabpanel" aria-labelledby="evaluacion-tab">
                            <form @submit.prevent="guardarEvaluacion" class="p-2">
                                <div class="form-group mb-4">
                                    <label class="small font-weight-bold text-dark">Resultado de la Eficacia <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm rounded">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i
                                                    class="fas fa-poll text-danger"></i></span>
                                        </div>
                                        <select v-model="form.he_resultado" class="form-control border-left-0" required>
                                            <option value="" disabled>Seleccione un resultado</option>
                                            <option value="con eficacia">Con Eficacia (Cierra el hallazgo)</option>
                                            <option value="sin eficacia">Sin Eficacia (Solicita nuevo plan de acción)
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="small font-weight-bold text-dark">Fecha de Evaluación <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group shadow-sm rounded">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i
                                                    class="fas fa-calendar-alt text-danger"></i></span>
                                        </div>
                                        <input type="date" v-model="form.he_fecha" class="form-control border-left-0"
                                            required>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="small font-weight-bold text-dark">Observaciones / Conclusión <span
                                            class="text-danger">*</span></label>
                                    <div class="shadow-sm rounded bg-white p-1">
                                        <textarea v-model="form.he_comentario" class="form-control border-0" rows="5"
                                            required
                                            placeholder="Describa los detalles de la verificación y la conclusión final..."></textarea>
                                    </div>
                                </div>

                                <div class="text-right mt-4 pt-2">
                                    <button type="submit" class="btn btn-danger btn-lg rounded-pill px-5 shadow"
                                        :disabled="loadingForm">
                                        <span v-if="loadingForm" class="spinner-border spinner-border-sm mr-2"
                                            role="status"></span>
                                        <i class="fas fa-save mr-2" v-else></i> Guardar Verificación
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { route } from 'ziggy-js';
import { Modal } from 'bootstrap';

const props = defineProps({
    visible: Boolean,
    hallazgo: Object,
    initialTab: {
        type: String,
        default: 'evidencias' // 'evidencias' or 'evaluacion'
    }
});

const emit = defineEmits(['cerrar', 'guardado', 'archivos-subidos']);

const modalRef = ref(null);
let modalInstance = null;

// Upload State
const loadingUpload = ref(false);
const evidencias = ref([]);
const selectedFiles = ref([]);
const fileInput = ref(null);
const isDragging = ref(false);

// Form State
const loadingForm = ref(false);
const form = reactive({
    he_resultado: '',
    he_fecha: new Date().toISOString().split('T')[0],
    he_comentario: ''
});

// --- METHODS FOR UPLOAD ---
const fetchEvaluacion = async () => {
    if (!props.hallazgo) return;
    try {
        const response = await axios.get(route('hallazgo.evaluacion.get', props.hallazgo.id));
        if (response.data && response.data.evidencias) {
            evidencias.value = response.data.evidencias;
        } else {
            evidencias.value = [];
        }
    } catch (error) {
        console.error('Error al cargar evaluación:', error);
        evidencias.value = [];
    }
};

const handleFileUpload = (event) => {
    const files = Array.from(event.target.files);
    addFiles(files);
};

const onDrop = (event) => {
    isDragging.value = false;
    const files = Array.from(event.dataTransfer.files);
    addFiles(files);
};

const addFiles = (files) => {
    const maxFileSize = 10 * 1024 * 1024; // 10MB
    files.forEach(file => {
        if (file.size > maxFileSize) {
            Swal.fire({
                icon: 'warning',
                title: 'Archivo demasiado grande',
                text: `El archivo "${file.name}" supera el límite de 10MB.`,
            });
            return;
        }
        selectedFiles.value.push(file);
    });
};

const removeSelectedFile = (index) => {
    selectedFiles.value.splice(index, 1);
};

const subirArchivos = async () => {
    if (selectedFiles.value.length === 0) return;

    loadingUpload.value = true;
    const formData = new FormData();
    selectedFiles.value.forEach(file => {
        formData.append('files[]', file);
    });

    try {
        const response = await axios.post(route('hallazgo.evaluacion.upload', props.hallazgo.id), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        evidencias.value = response.data.evidencias;
        selectedFiles.value = [];
        if (fileInput.value) fileInput.value.value = ''; // Reset input

        Swal.fire({
            icon: 'success',
            title: 'Subido',
            text: 'Documentos subidos correctamente.',
            timer: 1500,
            showConfirmButton: false
        });

        emit('archivos-subidos');
    } catch (error) {
        console.error('Error al subir archivos:', error);
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron subir los archivos.' });
    } finally {
        loadingUpload.value = false;
    }
};

// --- METHODS FOR FORM ---
const guardarEvaluacion = async () => {
    if (!form.he_resultado || !form.he_comentario || !form.he_fecha) return;

    loadingForm.value = true;
    try {
        await axios.post(route('hallazgo.evaluacion.store', props.hallazgo.id), {
            resultado: form.he_resultado,
            fecha_evaluacion: form.he_fecha,
            observaciones: form.he_comentario
        });

        Swal.fire({
            icon: 'success',
            title: 'Evaluación Guardada',
            text: `El hallazgo ha sido marcado como ${form.he_resultado}.`,
            timer: 2000,
            showConfirmButton: false
        });

        emit('guardado');
        cerrarModal();
    } catch (error) {
        console.error('Error al guardar evaluación:', error);
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo guardar la evaluación.' });
    } finally {
        loadingForm.value = false;
    }
};

// --- MODAL CONTROLS ---
const cerrarModal = () => {
    emit('cerrar');
    if (modalInstance) modalInstance.hide();
};

const abrirModal = () => {
    if (modalInstance) {
        modalInstance.show();
        // Set active tab based on prop or default
        setTimeout(() => {
            // Activate tab manually using JQuery/Bootstrap logic since we are using bootstrap tabs
            const tabName = props.initialTab === 'evaluacion' ? '#evaluacion-tab' : '#evidencias-tab';
            $(tabName).tab('show');
        }, 100);
    }
};

onMounted(() => {
    modalInstance = new Modal(modalRef.value, {
        backdrop: 'static',
        keyboard: false
    });
});

watch(() => props.visible, (newVal) => {
    if (newVal) {
        fetchEvaluacion(); // Load evidences always when opening
        // Reset form
        form.he_resultado = '';
        form.he_fecha = new Date().toISOString().split('T')[0];
        form.he_comentario = '';
        selectedFiles.value = [];
        abrirModal();
    } else {
        cerrarModal();
    }
});
</script>

<style scoped>
.custom-file-label {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.nav-tabs .nav-link.active {
    color: #dc3545;
    font-weight: bold;
    border-top: 3px solid #dc3545;
    background-color: transparent !important;
}

.nav-tabs .nav-link {
    color: #6c757d;
    border: none;
    padding: 0.8rem 1.2rem;
    transition: all 0.2s;
}

.nav-tabs .nav-link:hover {
    color: #dc3545;
    background-color: rgba(220, 53, 69, 0.05);
}

.drop-zone {
    border: 2px dashed #dee2e6;
    border-radius: 12px;
    padding: 30px 20px;
    transition: all 0.3s ease;
    background-color: #fdfdfd;
    cursor: pointer;
    margin: 10px;
}

.drop-zone:hover,
.drop-zone.drag-over {
    border-color: #dc3545;
    background-color: #fff5f5;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.1);
}

.drop-zone i {
    transition: transform 0.3s ease;
}

.drop-zone:hover i {
    transform: translateY(-5px);
}

.border-left-danger {
    border-left: 4px solid #dc3545 !important;
}

.btn-xs {
    padding: 0.2rem 0.4rem;
    font-size: 0.75rem;
}

.shadow-sm {
    box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
}

.rounded-lg {
    border-radius: .5rem !important;
}

.border-dashed {
    border-style: dashed !important;
}
</style>
