<template>
    <Teleport to="body">
        <div ref="modalRef" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header  bg-danger text-white">
                        <h5 class="modal-title">Tratamiento de Sugerencia</h5>
                        <button type="button" class="close text-white" @click="close" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="submitForm">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Análisis Inicial</label>
                                <textarea v-model="form.sugerencia_analisis" class="form-control" rows="3"
                                    placeholder="Análisis de la sugerencia..."></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Viabilidad</label>
                                        <select v-model="form.sugerencia_viabilidad" class="form-control">
                                            <option value="">Selecciona...</option>
                                            <option value="viable">Viable</option>
                                            <option value="no viable">No Viable</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Estado</label>
                                        <select v-model="form.sugerencia_estado" class="form-control" required>
                                            <option value="abierta">Abierta</option>
                                            <option value="en progreso">En Progreso</option>
                                            <option value="cerrada">Cerrada</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Tratamiento / Acción a Tomar</label>
                                <textarea v-model="form.sugerencia_tratamiento" class="form-control" rows="3"
                                    placeholder="Descripción del tratamiento..."></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Fecha Fin Programada</label>
                                        <input type="date" v-model="form.sugerencia_fecha_fin_prog"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Fecha Fin Real</label>
                                        <input type="date" v-model="form.sugerencia_fecha_fin_real"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>

                            <!-- Sección de Evidencias (Multi-archivo) -->
                            <div class="form-group">
                                <label class="font-weight-bold custom-label">Evidencias del Tratamiento</label>
                                <small class="form-text text-muted mb-2">Adjunte archivos de evidencia del tratamiento si los tiene.</small>
                                <div class="drop-zone" @dragenter.prevent="onDragEnter" @dragleave.prevent="onDragLeave"
                                    @dragover.prevent @drop.prevent="onDrop" :class="{ 'drag-over': isDragging }"
                                    @click="openFileDialog">
                                    <input type="file" ref="fileInput" class="d-none" @change="handleFileSelect"
                                        multiple>
                                    <div class="text-center">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                                        <p class="mb-0 mt-2">Arrastra y suelta archivos aquí, o haz clic para
                                            seleccionar.</p>
                                        <small class="text-muted">(Peso máximo por archivo: 10MB)</small>
                                    </div>
                                </div>

                                <!-- Lista de archivos seleccionados para subir -->
                                <ul v-if="filesToUpload.length > 0" class="list-group mt-3">
                                    <li v-for="file in filesToUpload" :key="file.id" class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-file mr-2 text-muted"></i>
                                                <span>{{ file.file.name }}</span>
                                                <div class="progress mt-1" style="height: 8px;">
                                                    <div class="progress-bar" role="progressbar"
                                                        :style="{ width: file.progress + '%' }"
                                                        :aria-valuenow="file.progress" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                            <button @click="removeFile(file.id)" class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </li>
                                </ul>

                                <!-- Archivos actualmente almacenados -->
                                <div v-if="existingFiles.length > 0" class="mt-3">
                                    <label class="font-weight-bold">Evidencias Existentes:</label>
                                    <ul class="list-group">
                                        <li v-for="(file, index) in existingFiles" :key="index" class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="text-truncate" style="max-width: 85%;">
                                                <i class="fas fa-paperclip mr-2 text-muted"></i>
                                                <a :href="`/storage/${file.path}`" target="_blank" class="text-decoration-none text-dark">
                                                    {{ file.name }}
                                                </a>
                                            </div>
                                            <button type="button" @click="removeExistingFile(index)" class="btn btn-danger btn-sm" title="Eliminar archivo">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-between">
                            <div></div> <!-- Empty div for spacing -->
                            <div>
                                <button type="button" class="btn btn-secondary" @click="close">
                                    <i class="fas fa-times mr-1"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-danger ml-2">
                                    <i class="fas fa-save mr-1"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue';
import { useSugerenciasStore } from '@/stores/sugerenciasStore';
import { Modal } from 'bootstrap';

const props = defineProps({
    show: Boolean,
    sugerenciaId: Number
});

const emit = defineEmits(['close', 'saved']);

const sugerenciasStore = useSugerenciasStore();

const modalRef = ref(null);
const modalInstance = ref(null);
const fileInput = ref(null);

// Variables para la subida de archivos de evidencia
const isDragging = ref(false);
const filesToUpload = ref([]);
let fileCounter = 0;
const existingFiles = ref([]);

const form = ref({
    sugerencia_analisis: '',
    sugerencia_viabilidad: '',
    sugerencia_tratamiento: '',
    sugerencia_estado: '',
    sugerencia_fecha_fin_prog: '',
    sugerencia_fecha_fin_real: ''
});

const formatDateForInput = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toISOString().split('T')[0];
};

const loadSugerencia = async (id) => {
    try {
        // Cargamos la sugerencia específica en el store
        const data = await sugerenciasStore.fetchSugerenciaById(id);
        form.value = {
            sugerencia_analisis: data.sugerencia_analisis || '',
            sugerencia_viabilidad: data.sugerencia_viabilidad || '',
            sugerencia_tratamiento: data.sugerencia_tratamiento || '',
            sugerencia_estado: data.sugerencia_estado,
            sugerencia_fecha_fin_prog: formatDateForInput(data.sugerencia_fecha_fin_prog),
            sugerencia_fecha_fin_real: formatDateForInput(data.sugerencia_fecha_fin_real)
        };

        // Parsear sugerencia_evidencias para preservar archivos existentes
        existingFiles.value = [];
        if (data.sugerencia_evidencias) {
            try {
                let parsed = JSON.parse(data.sugerencia_evidencias);
                if (!Array.isArray(parsed)) parsed = [parsed];
                existingFiles.value = parsed.map(item => {
                    if (typeof item === 'string') return { path: item, name: item.split('/').pop() };
                    return item;
                });
            } catch (e) {
                existingFiles.value = [{ path: data.sugerencia_evidencias, name: data.sugerencia_evidencias.split('/').pop() }];
            }
        }
    } catch (error) {
        console.error('Error loading sugerencia:', error);
    }
};

watch(() => props.show, async (newVal) => {
    if (newVal && props.sugerenciaId) {
        await loadSugerencia(props.sugerenciaId);
        await nextTick();
        if (modalRef.value) {
            if (!modalInstance.value) {
                modalInstance.value = new Modal(modalRef.value, {
                    backdrop: 'static',
                    keyboard: false
                });
            }
            modalInstance.value.show();
        }
    } else {
        if (modalInstance.value) {
            modalInstance.value.hide();
        }
        filesToUpload.value = [];
    }
});

const close = () => {
    emit('close');
};

// Funciones para manejar archivos (Drag & Drop + Input)
const handleFileSelect = (e) => {
    startUpload(Array.from(e.target.files));
};

const openFileDialog = () => {
    fileInput.value.click();
};

const onDragEnter = () => {
    isDragging.value = true;
};

const onDragLeave = () => {
    isDragging.value = false;
};

const onDrop = (event) => {
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
    });
};

const removeFile = (fileId) => {
    filesToUpload.value = filesToUpload.value.filter(f => f.id !== fileId);
};

const removeExistingFile = (index) => {
    existingFiles.value.splice(index, 1);
};

const submitForm = async () => {
    try {
        // Preparamos los datos para enviar
        const formData = { ...form.value };

        // Agregar archivos existentes que se mantienen
        if (existingFiles.value.length > 0) {
            formData.existing_evidencias = existingFiles.value.map(file => file.path);
        }

        // Agregar archivos nuevos para subir
        if (filesToUpload.value.length > 0) {
            // Creamos un FormData para manejar la subida de archivos
            const filesFormData = new FormData();

            // Agregar todos los campos del formulario al FormData
            for (const key in formData) {
                if (formData[key] !== null && formData[key] !== undefined) {
                    filesFormData.append(key, formData[key]);
                }
            }

            // Agregar archivos nuevos para subir
            filesToUpload.value.forEach((fileEntry) => {
                filesFormData.append('sugerencia_evidencias[]', fileEntry.file, fileEntry.file.name);
            });

            // Actualizar la sugerencia con la información de tratamiento
            await sugerenciasStore.updateSugerencia(props.sugerenciaId, filesFormData);
        } else {
            // Actualizar la sugerencia con la información de tratamiento
            await sugerenciasStore.updateSugerencia(props.sugerenciaId, formData);
        }

        emit('saved');
        close();
    } catch (error) {
        console.error('Error saving tratamiento:', error);
        alert('Error al guardar el tratamiento: ' + (error.response?.data?.message || error.message));
    }
};

onMounted(() => {
    if (modalRef.value) {
        modalRef.value.addEventListener('hidden.bs.modal', close);
    }
});

onUnmounted(() => {
    modalInstance.value?.dispose();
});
</script>

<style scoped>
.custom-label {
    font-size: 0.9em !important;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif !important;
    font-weight: 600 !important;
    color: #070707 !important;
    letter-spacing: 0.2px !important;
}

.drop-zone {
    border: 2px dashed #ccc;
    border-radius: 10px;
    padding: 40px;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #f9f9f9;
}

.drop-zone:hover {
    border-color: #999;
}

.drop-zone.drag-over {
    background-color: #f0f0f0;
    border-color: #666;
}
</style>
