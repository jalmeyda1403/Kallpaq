<template>
    <div class="modal fade" id="concluirModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" v-if="accion">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Concluir Acción</h5>
                    <button type="button" class="close" @click="closeModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                     <div v-if="isReadOnly" class="alert alert-info">
                        Esta acción ya ha sido {{ accion.accion_estado }} y no puede ser modificada.
                    </div>
                    <!-- File Upload Section -->
                    <div class="form-group">
                        <label class="font-weight-bold">Adjuntar Evidencia de Conclusión</label>
                        <small class="form-text text-muted mb-2">Debe adjuntar al menos un archivo de evidencia para poder concluir la acción.</small>
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
                    <div v-if="existingFiles.length > 0" class="mt-3">
                        <hr>
                        <label class="font-weight-bold">Documentos Existentes:</label>
                        <ul class="list-group">
                            <li v-for="(file, index) in existingFiles" :key="index" class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ file.name }}</span>
                                <div>
                                    <a :href="getFileUrl(file.path)" target="_blank" class="btn btn-info btn-sm mr-2" title="Ver"><i class="fas fa-eye"></i></a>
                                    <button @click="deleteEvidencia(file.path, index)" class="btn btn-danger btn-sm" title="Eliminar" :disabled="isReadOnly"><i class="fas fa-trash"></i></button>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
                    <button type="button" class="btn btn-primary" @click="concluirAccion" :disabled="isConcluirDisabled || isReadOnly">Concluir</button>
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
import { ref, onMounted, onUnmounted, computed } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import { Modal } from 'bootstrap';

const emit = defineEmits(['accion-concluida']);

const accion = ref(null);
const modalEl = ref(null);
const modal = ref(null);
const fileInput = ref(null);
const isDragging = ref(false);
const filesToUpload = ref([]);
let fileCounter = 0;

const existingFiles = ref([]);

const isReadOnly = computed(() => {
    return accion.value && ['desestimada', 'finalizada'].includes(accion.value.accion_estado);
});

const isConcluirDisabled = computed(() => {
    if (isReadOnly.value) return true;
    return existingFiles.value.length === 0;
});

const handleOpenModal = (event) => {
    accion.value = event.detail;
    
    if (accion.value && accion.value.accion_ruta_evidencia) {
        try {
            const files = JSON.parse(accion.value.accion_ruta_evidencia);
            existingFiles.value = Array.isArray(files) ? files : (files ? [files] : []);
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
    modalEl.value = document.getElementById('concluirModal');
    modal.value = new Modal(modalEl.value, { backdrop: 'static', keyboard: false });
    document.addEventListener('open-concluir-modal', handleOpenModal);
});

onUnmounted(() => {
    document.removeEventListener('open-concluir-modal', handleOpenModal);
});

const handleFileSelect = (e) => {
    if (isReadOnly.value) return;
    startUpload(Array.from(e.target.files));
};

const openFileDialog = () => { 
    if (isReadOnly.value) return;
    fileInput.value.click(); 
};
const onDragEnter = () => { 
    if (isReadOnly.value) return;
    isDragging.value = true; 
};
const onDragLeave = () => { 
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

const concluirAccion = async () => {
    if (!accion.value || isConcluirDisabled.value) return;

    try {
        await axios.post(route('acciones.concluir', { accion: accion.value.id }));
        emit('accion-concluida');
        closeModal();
    } catch (error) {
        if (error.response && error.response.status === 422) {
            alert(error.response.data.message);
        } else {
            console.error('Error al concluir la acción:', error);
        }
    }
};
</script>
