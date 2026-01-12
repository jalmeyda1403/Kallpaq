<template>
    <div class="modal fade" id="reporteSatisfaccionEnviarModal" tabindex="-1" role="dialog" aria-labelledby="reporteSatisfaccionEnviarModalLabel" aria-hidden="true" ref="modalRef">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="reporteSatisfaccionEnviarModalLabel">
                        <i class="fas fa-file-signature mr-2"></i>Enviar Reporte Firmado
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-info mb-3">
                        <i class="fas fa-info-circle mr-2"></i>
                        <small>Suba el archivo PDF firmado del reporte. Una vez subido, el estado cambiará a "Firmado" y no podrá ser editado.</small>
                    </p>

                    <div class="form-group">
                        <label for="archivo" class="font-weight-bold">Archivo PDF Firmado *</label>
                        <div class="custom-file">
                            <input 
                                type="file" 
                                class="custom-file-input" 
                                id="archivo" 
                                ref="fileInput"
                                accept=".pdf"
                                @change="handleFileChange"
                            />
                            <label class="custom-file-label" for="archivo">
                                {{ selectedFile ? selectedFile.name : 'Seleccionar archivo...' }}
                            </label>
                        </div>
                        <small v-if="selectedFile" class="text-success d-block mt-2">
                            <i class="fas fa-check-circle"></i> {{ formatFileSize(selectedFile.size) }}
                        </small>
                        <small class="text-muted d-block mt-1">Tamaño máximo: 10MB. Solo archivos PDF.</small>
                    </div>

                    <div v-if="error" class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>{{ error }}
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" :disabled="uploading">
                        <i class="fas fa-times mr-1"></i>Cancelar
                    </button>
                    <button 
                        type="button" 
                        class="btn btn-danger" 
                        @click="uploadFile" 
                        :disabled="!selectedFile || uploading"
                    >
                        <span v-if="uploading" class="spinner-border spinner-border-sm mr-1"></span>
                        <i v-else class="fas fa-upload mr-1"></i>
                        {{ uploading ? 'Subiendo...' : 'Subir y Firmar' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Modal } from 'bootstrap';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
    reporteId: {
        type: Number,
        default: null
    }
});

const emit = defineEmits(['success', 'close']);

const modalRef = ref(null);
let modalInstance = null;
const selectedFile = ref(null);
const fileInput = ref(null);
const uploading = ref(false);
const error = ref('');

const open = () => {
    if (modalInstance) {
        modalInstance.show();
    }
    resetForm();
};

const close = () => {
    if (modalInstance) {
        modalInstance.hide();
    }
    emit('close');
};

const resetForm = () => {
    selectedFile.value = null;
    error.value = '';
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    error.value = '';
    
    if (!file) {
        selectedFile.value = null;
        return;
    }
    
    // Validar tipo
    if (file.type !== 'application/pdf') {
        error.value = 'Solo se permiten archivos PDF';
        selectedFile.value = null;
        return;
    }
    
    // Validar tamaño (10MB)
    if (file.size > 10 * 1024 * 1024) {
        error.value = 'El archivo no debe superar los 10MB';
        selectedFile.value = null;
        return;
    }
    
    selectedFile.value = file;
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

const uploadFile = async () => {
    if (!selectedFile.value || !props.reporteId) return;
    
    uploading.value = true;
    error.value = '';
    
    try {
        const formData = new FormData();
        formData.append('archivo_path', selectedFile.value);
        
        const response = await axios.post(`/api/reportes-satisfaccion/${props.reporteId}/upload-firma`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        
        close();
        
        await Swal.fire({
            title: '¡Éxito!',
            text: 'El archivo firmado se ha subido correctamente. El reporte ahora está firmado.',
            icon: 'success',
            timer: 3000
        });
        
        emit('success');
    } catch (err) {
        console.error('Error uploading file:', err);
        error.value = err.response?.data?.message || 'Error al subir el archivo. Intente nuevamente.';
    } finally {
        uploading.value = false;
    }
};

onMounted(() => {
    if (modalRef.value) {
        modalInstance = new Modal(modalRef.value, {
            backdrop: 'static',
            keyboard: false
        });
    }
});

defineExpose({ open, close });
</script>

<style scoped>
.modal-header.bg-danger {
    background-color: #dc3545 !important;
}

.custom-file-label::after {
    content: "Buscar";
}
</style>
