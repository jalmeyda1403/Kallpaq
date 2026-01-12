<template>
    <div class="modal fade" id="reporteSatisfaccionEnviarModal" tabindex="-1" role="dialog"
        aria-labelledby="reporteSatisfaccionEnviarModalLabel" aria-hidden="true" ref="modalRef">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="reporteSatisfaccionEnviarModalLabel">
                        <i class="fas fa-file-signature mr-2"></i>Enviar Reporte Firmado
                    </h5>
                    <button type="button" class="close text-white" @click="close" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted mb-4">
                        Suba el archivo PDF firmado del reporte. Una vez subido, el estado cambiará a
                        <strong>"Firmado"</strong> y
                        no podrá ser editado.
                    </p>

                    <!-- Drag & Drop Zone -->
                    <div class="upload-zone mb-3"
                        :class="{ 'dragging': isDragging, 'has-file': selectedFile, 'error': error }"
                        @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
                        @drop.prevent="handleDrop" @click="triggerFileInput">
                        <input type="file" class="d-none" ref="fileInput" accept=".pdf" @change="handleFileChange" />

                        <div v-if="!selectedFile" class="text-center py-4">
                            <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-muted"></i>
                            <h6 class="font-weight-bold text-dark">Arrastre su archivo aquí</h6>
                            <p class="text-muted small mb-0">o haga clic para seleccionar</p>
                            <p class="text-muted small mt-2">(Solo archivos PDF, Máx. 10MB)</p>
                        </div>

                        <div v-else class="text-center py-4">
                            <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                            <h6 class="font-weight-bold text-dark mb-1">{{ selectedFile.name }}</h6>
                            <p class="text-muted small mb-3">{{ formatFileSize(selectedFile.size) }}</p>
                            <button class="btn btn-sm btn-outline-danger" @click.stop="resetForm">
                                <i class="fas fa-trash-alt mr-1"></i>Cambiar archivo
                            </button>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div v-if="uploading" class="progress mb-3" style="height: 20px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger"
                            role="progressbar" :style="{ width: uploadProgress + '%' }" :aria-valuenow="uploadProgress"
                            aria-valuemin="0" aria-valuemax="100">
                            {{ uploadProgress }}%
                        </div>
                    </div>

                    <div v-if="error" class="alert alert-danger shadow-sm shake-animation">
                        <i class="fas fa-exclamation-triangle mr-2"></i>{{ error }}
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary px-4" @click="close" :disabled="uploading">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-danger px-4" @click="uploadFile"
                        :disabled="!selectedFile || uploading">
                        <span v-if="uploading" class="spinner-border spinner-border-sm mr-2"></span>
                        <i v-else class="fas fa-paper-plane mr-2"></i>
                        {{ uploading ? 'Subiendo...' : 'Enviar Reporte' }}
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
const uploadProgress = ref(0);
const error = ref('');
const isDragging = ref(false);

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

const triggerFileInput = () => {
    if (!uploading.value) {
        fileInput.value.click();
    }
};

const resetForm = () => {
    selectedFile.value = null;
    error.value = '';
    uploadProgress.value = 0;
    uploading.value = false;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const handleDrop = (event) => {
    isDragging.value = false;
    const files = event.dataTransfer.files;
    if (files.length > 0) {
        validateAndSetFile(files[0]);
    }
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        validateAndSetFile(file);
    }
};

const validateAndSetFile = (file) => {
    error.value = '';

    // Validar tipo
    if (file.type !== 'application/pdf') {
        error.value = 'Solo se permiten archivos PDF';
        return;
    }

    // Validar tamaño (10MB)
    if (file.size > 10 * 1024 * 1024) {
        error.value = 'El archivo no debe superar los 10MB';
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
    uploadProgress.value = 0;
    error.value = '';

    try {
        const formData = new FormData();
        formData.append('archivo', selectedFile.value); // Backend expects 'archivo' based on uploadSigned

        // Note: The backend route is likely 'uploadSigned' based on web.php, but let's check the controller usage in the previous version
        // The previous version used 'upload-firma' which maps to 'uploadFirma' in controller.
        // However, web.php line 813 says: Route::post('/{id}/upload-signed', ...)->name('uploadSigned');
        // And line 811 says: Route::post('/{id}/upload-firma', ...)->name('upload-firma');
        // Let's stick to what was working or what the controller expects. 
        // Controller 'uploadFirma' uses 'archivo_path'. Controller 'uploadSigned' uses 'archivo'.
        // The previous component sent 'archivo_path' to 'upload-firma'. 
        // I will use 'upload-firma' and 'archivo_path' to be consistent with the controller method `uploadFirma`.

        // RE-READING CONTROLLER:
        // public function uploadFirma(Request $request, $id) { $request->validate(['archivo_path' => ...]); ... $request->file('archivo_path') ... }

        // So I must use 'archivo_path' and the route for 'uploadFirma'.

        formData.delete('archivo'); // remove incorrect key
        formData.append('archivo_path', selectedFile.value);

        const response = await axios.post(`/api/reportes-satisfaccion/${props.reporteId}/upload-firma`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            onUploadProgress: (progressEvent) => {
                const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                uploadProgress.value = percentCompleted;
            }
        });

        close();

        await Swal.fire({
            title: '¡Éxito!',
            text: 'El archivo firmado se ha subido correctamente.',
            icon: 'success',
            confirmButtonColor: '#dc3545',
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
.upload-zone {
    border: 2px dashed #dee2e6;
    border-radius: 10px;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
}

.upload-zone:hover,
.upload-zone.dragging {
    border-color: #dc3545;
    background-color: #fff1f2;
}

.upload-zone.has-file {
    border-style: solid;
    border-color: #dee2e6;
    background-color: white;
}

.upload-zone.error {
    border-color: #dc3545;
}

.shake-animation {
    animation: shake 0.5s cubic-bezier(.36, .07, .19, .97) both;
}

@keyframes shake {

    10%,
    90% {
        transform: translate3d(-1px, 0, 0);
    }

    20%,
    80% {
        transform: translate3d(2px, 0, 0);
    }

    30%,
    50%,
    70% {
        transform: translate3d(-4px, 0, 0);
    }

    40%,
    60% {
        transform: translate3d(4px, 0, 0);
    }
}
</style>
