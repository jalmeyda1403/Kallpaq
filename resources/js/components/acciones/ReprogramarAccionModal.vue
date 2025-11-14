<template>
    <div class="modal fade" id="reprogramarModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" v-if="accion">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Gestionar Acción</h5>
                    <button type="button" class="close" @click="closeModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div v-if="isReadOnly" class="alert alert-info">
                        Esta acción ya ha sido {{ accion.accion_estado }} y no puede ser modificada.
                    </div>
                    <form @submit.prevent="guardarCambios">
                        <!-- Action Type and Justification -->
                        <div class="form-group">
                            <label class="font-weight-bold">Acción a Realizar</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="reprogramar" value="reprogramar" v-model="form.actionType" :disabled="isReadOnly">
                                    <label class="form-check-label" for="reprogramar">Reprogramar</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="desestimar" value="desestimar" v-model="form.actionType" :disabled="isReadOnly">
                                    <label class="form-check-label" for="desestimar">Desestimar</label>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <!-- Justification -->
                        <div class="form-group">
                            <label for="justificacion" class="font-weight-bold">Justificación</label>
                            <textarea class="form-control" id="justificacion" v-model.trim="form.accion_justificacion" rows="5" required maxlength="300" :readonly="isReadOnly"></textarea>
                            <div class="d-flex justify-content-between">
                                <small class="form-text text-muted">Explique el motivo de la reprogramación o desestimación.</small>
                                <small class="form-text text-muted">{{ form.accion_justificacion.length }} / 300</small>
                            </div>
                        </div>

                        <!-- Dates Section -->
                        <div v-if="form.actionType === 'reprogramar'" class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_fin_planificada" class="font-weight-bold">Fecha Fin Programada (Original)</label>
                                    <input type="text" class="form-control" id="fecha_fin_planificada" :value="formattedOriginalDate" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_fin_reprogramada" class="font-weight-bold">Nueva Fecha Fin</label>
                                    <input type="date" class="form-control" id="fecha_fin_reprogramada" v-model="form.accion_fecha_fin_reprogramada" required :readonly="isReadOnly">
                                </div>
                            </div>
                        </div>
                        <hr>

                        <!-- File Upload Section -->
                        <div class="form-group">
                            <label class="font-weight-bold">Adjuntar Evidencia (Opcional)</label>
                            <div class="drop-zone" @dragenter.prevent="onDragEnter" @dragleave.prevent="onDragLeave" @dragover.prevent @drop.prevent="onDrop" :class="{ 'drag-over': isDragging, 'disabled': isReadOnly }" @click="openFileDialog">
                                <input type="file" ref="fileInput" class="d-none" @change="handleFileSelect" multiple :disabled="isReadOnly">
                                <div class="text-center">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                                    <p class="mb-0 mt-2">Arrastra y suelta archivos aquí, o haz clic para seleccionar.</p>
                                    <small class="text-muted">(Peso máximo por archivo: 10MB)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Files Lists -->
                        <ul v-if="filesToUpload.length > 0" class="list-group mt-3">
                            <li v-for="file in filesToUpload" :key="file.id" class="list-group-item">
                                <div>{{ file.file.name }}</div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar" role="progressbar" :style="{ width: file.progress + '%' }"></div>
                                </div>
                            </li>
                        </ul>
                        <ul v-if="existingFiles.length > 0" class="list-group mt-3">
                             <li v-for="(file, index) in existingFiles" :key="index" class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ file.name }}</span>
                                <div>
                                    <a :href="getFileUrl(file.path)" target="_blank" class="btn btn-info btn-sm mr-2" title="Ver"><i class="fas fa-eye"></i></a>
                                    <button @click="deleteEvidencia(file.path, index)" class="btn btn-danger btn-sm" title="Eliminar" :disabled="isReadOnly"><i class="fas fa-trash"></i></button>
                                </div>
                            </li>
                        </ul>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
                    <button type="button" class="btn btn-primary" @click="guardarCambios" :disabled="isSaveDisabled || isReadOnly">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
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
.drop-zone.disabled {
    cursor: not-allowed;
    background-color: #f8f9fa;
    opacity: 0.7;
}
</style>

<script setup>
import { ref, reactive, onMounted, onUnmounted, computed } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import { Modal } from 'bootstrap';

const emit = defineEmits(['accion-gestionada']);

const accion = ref(null);
const modalEl = ref(null);
const modal = ref(null);
const fileInput = ref(null);
const isDragging = ref(false);
const filesToUpload = ref([]);
let fileCounter = 0;

const form = reactive({
    actionType: 'reprogramar',
    accion_justificacion: '',
    accion_fecha_fin_reprogramada: '',
});

const existingFiles = ref([]);

const isReadOnly = computed(() => {
    return accion.value && ['desestimada', 'finalizada'].includes(accion.value.accion_estado);
});

const isSaveDisabled = computed(() => {
    if (isReadOnly.value) return true;
    if (!form.accion_justificacion) return true;
    if (form.actionType === 'reprogramar' && !form.accion_fecha_fin_reprogramada) return true;
    return false;
});

const formattedOriginalDate = computed(() => {
    if (accion.value && accion.value.accion_fecha_fin_planificada) {
        const date = new Date(accion.value.accion_fecha_fin_planificada);
        // Adjust for timezone offset
        const userTimezoneOffset = date.getTimezoneOffset() * 60000;
        const adjustedDate = new Date(date.getTime() + userTimezoneOffset);
        const day = String(adjustedDate.getDate()).padStart(2, '0');
        const month = String(adjustedDate.getMonth() + 1).padStart(2, '0');
        const year = adjustedDate.getFullYear();
        return `${day}/${month}/${year}`;
    }
    return 'N/A';
});

const handleOpenModal = (event) => {
    accion.value = event.detail;
    form.actionType = 'reprogramar';
    form.accion_justificacion = accion.value.accion_justificacion || '';
    form.accion_fecha_fin_reprogramada = '';
    
    if (accion.value && accion.value.accion_ruta_evidencia) {
        try {
            existingFiles.value = JSON.parse(accion.value.accion_ruta_evidencia) || [];
        } catch (e) {
            existingFiles.value = [];
        }
    } else {
        existingFiles.value = [];
    }

    filesToUpload.value = [];
    if (fileInput.value) fileInput.value.value = '';
    
    modal.value.show();
};

const closeModal = () => {
    modal.value.hide();
};

onMounted(() => {
    modalEl.value = document.getElementById('reprogramarModal');
    modal.value = new Modal(modalEl.value, { backdrop: 'static', keyboard: false });
    document.addEventListener('open-reprogramar-modal', handleOpenModal);
});

onUnmounted(() => {
    document.removeEventListener('open-reprogramar-modal', handleOpenModal);
});

const handleFileSelect = (e) => {
    if (isReadOnly.value) return;
    startUpload(Array.from(e.target.files));
};

const openFileDialog = () => { 
    if (isReadOnly.value) return;
    fileInput.value.click(); 
};
const onDragEnter = (e) => { 
    if (isReadOnly.value) return;
    isDragging.value = true; 
};
const onDragLeave = (e) => { 
    if (isReadOnly.value) return;
    isDragging.value = false; 
};
const onDrop = (event) => {
    if (isReadOnly.value) return;
    isDragging.value = false;
    startUpload(Array.from(event.dataTransfer.files));
};

const startUpload = (files) => {
    const maxFileSize = 10 * 1024 * 1024; // 10MB

    files.forEach(file => {
        if (file.size > maxFileSize) {
            alert(`El archivo '${file.name}' supera el límite de 10MB y no será subido.`);
            return; // Skip this file
        }

        const fileEntry = { id: fileCounter++, file: file, progress: 0 };
        filesToUpload.value.push(fileEntry);
        uploadFile(fileEntry);
    });
};

const uploadFile = async (fileEntry) => {
    const formData = new FormData();
    formData.append('file', fileEntry.file);
    try {
        const response = await axios.post(route('acciones.upload-evidencia', { accion: accion.value.id }), formData, {
            onUploadProgress: (progressEvent) => {
                fileEntry.progress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
            },
        });
        existingFiles.value.push(response.data);
        filesToUpload.value = filesToUpload.value.filter(f => f.id !== fileEntry.id);
    } catch (error) {
        console.error('Error al subir el archivo:', error);
        fileEntry.progress = 0;
    }
};

const deleteEvidencia = async (filePath, index) => {
    if (isReadOnly.value) return;
    if (!confirm('¿Está seguro de que desea eliminar este archivo?')) return;
    try {
        await axios.post(route('acciones.delete-evidencia', { accion: accion.value.id }), { path: filePath });
        existingFiles.value.splice(index, 1);
    } catch (error) {
        console.error('Error al eliminar el archivo:', error);
        alert('No se pudo eliminar el archivo.');
    }
};

const getFileUrl = (filePath) => `/storage/${filePath}`;

const guardarCambios = async () => {
    if (!accion.value || isSaveDisabled.value) return;

    const formData = new FormData();
    formData.append('actionType', form.actionType);
    formData.append('accion_justificacion', form.accion_justificacion);
    if (form.actionType === 'reprogramar') {
        formData.append('accion_fecha_fin_reprogramada', form.accion_fecha_fin_reprogramada);
    }

    try {
        await axios.post(route('acciones.reprogramar', { accion: accion.value.id }), formData);
        emit('accion-gestionada');
        closeModal();
    } catch (error) {
        if (error.response && error.response.status === 422) {
            console.error('Validation errors:', error.response.data.errors);
            alert('Error de validación: ' + JSON.stringify(error.response.data.errors));
        } else {
            console.error('Error al guardar los cambios:', error);
        }
    }
};
</script>
