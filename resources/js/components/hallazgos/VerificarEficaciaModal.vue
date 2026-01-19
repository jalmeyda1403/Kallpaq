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
                        <!-- TAB 1: EVIDENCIAS (Antes EvaluacionArchivosModal) -->
                        <div class="tab-pane fade show active" id="evidencias" role="tabpanel"
                            aria-labelledby="evidencias-tab">
                            <div v-if="evidencias.length > 0" class="mb-4">
                                <h6 class="font-weight-bold">Documentos Subidos:</h6>
                                <ul class="list-group">
                                    <li v-for="(file, index) in evidencias" :key="index"
                                        class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <a :href="file.path" target="_blank text-truncate" style="max-width: 85%;">
                                            <i class="fas fa-file-alt mr-2 text-muted"></i> {{ file.name }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div v-else class="mb-4 text-muted small">
                                <i class="fas fa-info-circle mr-1"></i> No hay documentos de evidencia subidos aún.
                            </div>

                            <div class="card bg-light border-0">
                                <div class="card-body p-3">
                                    <form @submit.prevent="subirArchivos">
                                        <div class="form-group">
                                            <label for="files" class="font-weight-bold ml-1">Subir Nuevas
                                                Evidencias:</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="files" multiple
                                                    @change="handleFileUpload"
                                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                                <label class="custom-file-label" for="files" data-browse="Elegir">
                                                    {{ selectedFiles.length > 0 ? `${selectedFiles.length} archivos seleccionados` : 'Seleccionar archivos...' }}
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">Formatos: PDF, Imágenes, Office.</small>
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary btn-sm"
                                                :disabled="loadingUpload || selectedFiles.length === 0">
                                                <span v-if="loadingUpload" class="spinner-border spinner-border-sm"
                                                    role="status" aria-hidden="true"></span>
                                                <i class="fas fa-upload mr-1" v-else></i> Subir Archivos
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: EVALUACIÓN (Antes HallazgoEvaluacionModal) -->
                        <div class="tab-pane fade" id="evaluacion" role="tabpanel" aria-labelledby="evaluacion-tab">
                            <form @submit.prevent="guardarEvaluacion">
                                <div class="form-group">
                                    <label for="resultado" class="font-weight-bold">Resultado de la Eficacia <span
                                            class="text-danger">*</span></label>
                                    <select v-model="form.he_resultado" class="form-control" required>
                                        <option value="" disabled>Seleccione un resultado</option>
                                        <option value="con eficacia">Con Eficacia (Cierra el hallazgo)</option>
                                        <option value="sin eficacia">Sin Eficacia (Solicita nuevo plan de acción)
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="fecha_evaluacion" class="font-weight-bold">Fecha de Evaluación <span
                                            class="text-danger">*</span></label>
                                    <input type="date" v-model="form.he_fecha" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="observaciones" class="font-weight-bold">Observaciones / Conclusión <span
                                            class="text-danger">*</span></label>
                                    <textarea v-model="form.he_comentario" class="form-control" rows="4" required
                                        placeholder="Describa los detalles de la verificación y la conclusión final..."></textarea>
                                </div>

                                <div class="text-right mt-3">
                                    <button type="submit" class="btn btn-danger" :disabled="loadingForm">
                                        <span v-if="loadingForm" class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                        <i class="fas fa-save mr-1" v-else></i> Guardar Verificación
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
    selectedFiles.value = Array.from(event.target.files);
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
        document.getElementById('files').value = ''; // Reset input

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
}
.nav-tabs .nav-link {
    color: #6c757d;
}
</style>
