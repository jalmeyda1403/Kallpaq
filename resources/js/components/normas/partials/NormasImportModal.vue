<template>
    <div ref="modalRef" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-file-excel mr-2"></i>Importar Requisitos
                    </h5>
                    <button type="button" class="close text-white" @click="close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <p class="text-muted">Descarga la plantilla, completa los datos y súbela nuevamente.</p>
                        <a :href="`/api/auditoria/normas/template${normaId ? '?norma_id=' + normaId : ''}`"
                            class="btn btn-outline-success btn-sm px-4 rounded-pill shadow-sm">
                            <i class="fas fa-download mr-1"></i> Descargar Plantilla
                        </a>
                    </div>

                    <div class="upload-zone p-4 border rounded text-center position-relative"
                        :class="{ 'border-success bg-light': isDragging }" @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false" @drop.prevent="handleDrop">

                        <input type="file" ref="fileInput"
                            class="position-absolute w-100 h-100 opacity-0 cursor-pointer" style="top: 0; left: 0;"
                            @change="handleFileSelect" accept=".xlsx,.xls,.csv" />

                        <div v-if="!selectedFile">
                            <i class="fas fa-cloud-upload-alt fa-3x text-success mb-3"></i>
                            <h6 class="font-weight-bold">Arrastra el archivo aquí</h6>
                            <p class="small text-muted mb-0">o haz clic para buscarlo</p>
                        </div>
                        <div v-else class="text-success">
                            <i class="fas fa-file-excel fa-3x mb-3"></i>
                            <h6 class="font-weight-bold">{{ selectedFile.name }}</h6>
                            <button class="btn btn-link btn-sm text-danger"
                                @click.stop="resetSelection">Cambiar</button>
                        </div>
                    </div>

                    <div v-if="uploading" class="progress mt-3" style="height: 10px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                            role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
                <div class="modal-footer bg-light justify-content-between">
                    <button type="button" class="btn btn-secondary px-4" @click="close">Cerrar</button>
                    <button type="button" class="btn btn-success px-4 shadow-sm" @click="submitImport"
                        :disabled="!selectedFile || uploading">
                        <i class="fas fa-check-circle mr-1"></i> {{ uploading ? 'Importando...' : 'Importar Datos' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
    normaId: {
        type: [Number, String],
        default: null
    }
});

const emit = defineEmits(['imported']);

const modalRef = ref(null);
let modalInstance = null;
const selectedFile = ref(null);
const uploading = ref(false);
const isDragging = ref(false);
const fileInput = ref(null);

const open = () => {
    selectedFile.value = null;
    if (fileInput.value) fileInput.value.value = ''; // Reset input value
    if (!modalInstance) {
        modalInstance = new bootstrap.Modal(modalRef.value, { backdrop: 'static', keyboard: false });
    }
    modalInstance.show();
};

const close = () => {
    modalInstance?.hide();
};

const handleFileSelect = (e) => {
    selectedFile.value = e.target.files[0];
};

const handleDrop = (e) => {
    isDragging.value = false;
    selectedFile.value = e.dataTransfer.files[0];
};

const resetSelection = () => {
    selectedFile.value = null;
    if (fileInput.value) fileInput.value.value = '';
};

const submitImport = async () => {
    if (!selectedFile.value) return;

    const formData = new FormData();
    formData.append('file', selectedFile.value);

    uploading.value = true;
    try {
        const response = await axios.post('/api/auditoria/normas/import', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        emit('imported', response.data);
        Swal.fire({
            icon: 'success',
            title: '¡Importación Exitosa!',
            text: `Se han procesado ${response.data.length} requisitos.`,
            confirmButtonColor: '#28a745'
        });
        close();
    } catch (e) {
        console.error("Error importing", e);
        Swal.fire({
            icon: 'error',
            title: 'Error de Importación',
            text: e.response?.data?.message || 'No se pudo procesar el archivo.',
            confirmButtonColor: '#dc3545'
        });
    } finally {
        uploading.value = false;
    }
};

defineExpose({ open, modalRef });
</script>

<style scoped>
.upload-zone {
    border: 2px dashed #dee2e6;
    transition: all 0.3s ease;
    background: #fdfdfd;
}

.upload-zone:hover {
    border-color: #28a745;
    background-color: #f8fff9;
}

.cursor-pointer {
    cursor: pointer;
}

.opacity-0 {
    opacity: 0;
}
</style>
