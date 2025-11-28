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
                            <!-- Alert when suggestion is closed -->
                            <div v-if="form.sugerencia_estado === 'cerrada'" class="alert alert-info mb-3">
                                <small>
                                    <i class="fa fa-info-circle mr-1"></i>
                                    Sugerencia cerrada el {{ formatDate(form.sugerencia_fecha_cierre) }}
                                </small>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold text-dark custom-label">Análisis Inicial</label>
                                <textarea v-model="form.sugerencia_analisis" class="form-control" rows="3"
                                    :disabled="form.sugerencia_estado === 'cerrada'"
                                    placeholder="Análisis de la sugerencia..."></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark custom-label">Viabilidad</label>
                                        <select v-model="form.sugerencia_viabilidad" class="form-control"
                                            :disabled="form.sugerencia_estado === 'cerrada'">
                                            <option value="">Selecciona...</option>
                                            <option value="viable">Viable</option>
                                            <option value="no viable">No Viable</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark custom-label">Estado</label>
                                        <select v-model="form.sugerencia_estado" class="form-control" required
                                            :disabled="form.sugerencia_estado === 'cerrada'">
                                            <option value="abierta">Abierta</option>
                                            <option value="en progreso">En Progreso</option>
                                            <option value="concluida">Concluida</option>
                                            <option v-if="form.sugerencia_estado === 'observada' ||
                                                form.sugerencia_estado === 'observada'" value="observada">Observada
                                            </option>
                                            <option v-if="form.sugerencia_estado === 'cerrada' ||
                                                form.sugerencia_estado === 'cerrada'" value="cerrada">Cerrada
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold text-dark custom-label">Tratamiento / Acción a Tomar</label>
                                <textarea v-model="form.sugerencia_tratamiento" class="form-control" rows="3"
                                    :disabled="form.sugerencia_estado === 'cerrada'"
                                    placeholder="Descripción del tratamiento..."></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark custom-label">Fecha Fin Programada</label>
                                        <input type="date" v-model="form.sugerencia_fecha_fin_prog" class="form-control"
                                            :disabled="form.sugerencia_estado === 'cerrada'">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark custom-label">Fecha Fin Real</label>
                                        <input type="date" v-model="form.sugerencia_fecha_fin_real" class="form-control"
                                            :disabled="form.sugerencia_estado === 'cerrada'">
                                    </div>
                                </div>
                            </div>

                            <!-- Sección de Evidencias (Multi-archivo) -->
                            <div class="form-group">
                                <label class="font-weight-bold custom-label">Evidencias del Tratamiento</label>
                                <small class="form-text text-muted mb-2">Adjunte archivos de evidencia del tratamiento
                                    si los tiene.</small>
                                <div class="drop-zone" @dragenter.prevent="onDragEnter" @dragleave.prevent="onDragLeave"
                                    @dragover.prevent @drop.prevent="onDrop" :class="{ 'drag-over': isDragging }"
                                    @click="openFileDialog">
                                    <input type="file" ref="fileInput" class="d-none" @change="handleFileSelect"
                                        multiple>
                                    <div class="text-center">
                                        <i class="fa fa-cloud-upload fa-3x text-muted"></i>
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
                                                <i class="fa fa-file mr-2 text-muted"></i>
                                                <span>{{ file.file.name }}</span>
                                                <div class="progress mt-1" style="height: 8px;">
                                                    <div class="progress-bar" role="progressbar"
                                                        :style="{ width: file.progress + '%' }"
                                                        :aria-valuenow="file.progress" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                            <button @click="removeFile(file.id)" class="btn btn-danger btn-sm">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </li>
                                </ul>

                                <!-- Archivos actualmente almacenados -->
                                <div v-if="existingFiles.length > 0" class="mt-3">
                                    <label class="font-weight-bold">Evidencias Existentes:</label>
                                    <ul class="list-group">
                                        <li v-for="(file, index) in existingFiles" :key="index"
                                            class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="text-truncate" style="max-width: 85%;">
                                                <i class="fa fa-paperclip mr-2 text-muted"></i>
                                                <a :href="`/storage/${file.path}`" target="_blank"
                                                    class="text-decoration-none text-dark">
                                                    {{ file.name }}
                                                </a>
                                            </div>
                                            <button type="button" @click="removeExistingFile(index)"
                                                class="btn btn-danger btn-sm" title="Eliminar archivo">
                                                <i class="fa fa-trash"></i>
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
                                    <i class="fa fa-times mr-1"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-danger ml-2"
                                    :disabled="form.sugerencia_estado === 'cerrada'">
                                    <i class="fa fa-save mr-1"></i> Guardar
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

const formatDate = (dateString) => {
    if (!dateString) return '';
    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
    return new Date(dateString).toLocaleDateString(undefined, options);
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
            sugerencia_fecha_fin_real: formatDateForInput(data.sugerencia_fecha_fin_real),
            sugerencia_fecha_cierre: data.sugerencia_fecha_cierre
        };

        // Cargar archivos existentes
        existingFiles.value = [];
        if (data.sugerencia_evidencias) {
            // El backend ya devuelve un array (gracias al cast 'array' del modelo)
            if (Array.isArray(data.sugerencia_evidencias)) {
                existingFiles.value = data.sugerencia_evidencias.map(item => {
                    // Normalizar la estructura
                    if (typeof item === 'object' && item.path) {
                        return {
                            path: item.path,
                            name: item.name || item.path.split('/').pop()
                        };
                    } else if (typeof item === 'string') {
                        return {
                            path: item,
                            name: item.split('/').pop()
                        };
                    }
                    return item;
                });
            } else if (typeof data.sugerencia_evidencias === 'string') {
                // Fallback: si por alguna razón viene como string
                existingFiles.value = [{
                    path: data.sugerencia_evidencias,
                    name: data.sugerencia_evidencias.split('/').pop()
                }];
            }
        }

        console.log('Archivos existentes cargados:', existingFiles.value);
    } catch (error) {
        console.error('Error loading sugerencia:', error);
    }
};

const close = () => {
    // Remove focus from the button to prevent ARIA warnings when modal hides
    if (document.activeElement instanceof HTMLElement) {
        document.activeElement.blur();
    }
    emit('close');
};

watch(() => props.show, async (newVal) => {
    if (newVal && props.sugerenciaId) {
        // Primero cargamos los datos
        await loadSugerencia(props.sugerenciaId);

        // Luego mostramos el modal (esto elimina el efecto de loader)
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
        // Limpiar archivos cuando se cierra el modal
        filesToUpload.value = [];
        existingFiles.value = [];
    }
});


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
        // Verificar si hay cambios en los archivos (nuevos o eliminados)
        const hasNewFiles = filesToUpload.value.length > 0;
        const hasFileChanges = hasNewFiles || existingFiles.value.length > 0;

        if (hasFileChanges) {
            // Creamos un FormData para manejar la subida de archivos
            const filesFormData = new FormData();

            // Agregar todos los campos del formulario al FormData
            for (const key in form.value) {
                if (form.value[key] !== null && form.value[key] !== undefined && form.value[key] !== '') {
                    filesFormData.append(key, form.value[key]);
                }
            }

            // Agregar archivos existentes que se mantienen
            if (existingFiles.value.length > 0) {
                existingFiles.value.forEach((file) => {
                    filesFormData.append('existing_evidencias[]', file.path);
                });
            }

            // Agregar archivos nuevos para subir
            if (hasNewFiles) {
                filesToUpload.value.forEach((fileEntry) => {
                    filesFormData.append('sugerencia_evidencias[]', fileEntry.file, fileEntry.file.name);
                });
            }

            // Añadir indicación de que se están actualizando evidencias
            filesFormData.append('update_evidences', '1');

            // Actualizar la sugerencia con la información de tratamiento
            await sugerenciasStore.updateSugerencia(props.sugerenciaId, filesFormData);
        } else {
            // Actualizar la sugerencia con la información de tratamiento (sin archivos)
            await sugerenciasStore.updateSugerencia(props.sugerenciaId, form.value);
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
    color: #495057 !important;
    letter-spacing: 0.2px !important;
}

/* Improved drop zone styles with red/gray variations */
.drop-zone {
    border: 2px dashed #ced4da;
    border-radius: 10px;
    padding: 40px;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
    text-align: center;
}

.drop-zone:hover {
    border-color: #dc3545;
    background-color: #fff5f5;
}

.drop-zone.drag-over {
    background-color: #fdf0f0;
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
}

.drop-zone.disabled {
    cursor: not-allowed;
    background-color: #e9ecef;
    opacity: 0.7;
}

/* Improved form controls */
.form-control {
    border: 1px solid #ced4da;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    border-radius: 0.375rem;
}

.form-control:focus {
    color: #495057;
    background-color: #fff;
    border-color: #dc3545;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* Button styles */
.btn {
    border-radius: 0.375rem;
    font-weight: 500;
    padding: 0.375rem 0.75rem;
    transition: all 0.15s ease-in-out;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
    transform: translateY(-1px);
    box-shadow: 0 0.125rem 0.25rem rgba(220, 53, 69, 0.3);
}

.btn-danger:focus {
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.5);
}

.btn-danger:not(:disabled):not(.disabled):active,
.btn-danger:not(:disabled):not(.disabled).active {
    background-color: #bd2130;
    border-color: #b21f2d;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
    transform: translateY(-1px);
    box-shadow: 0 0.125rem 0.25rem rgba(108, 117, 125, 0.3);
}

.btn-secondary:focus {
    box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.5);
}

/* Alert styling */
.alert-info {
    color: #0c5460;
    background-color: #d1ecf1;
    border-color: #bee5eb;
    border-radius: 0.375rem;
}

/* Modal header */
.modal-header {
    background-color: #dc3545;
    color: white;
    border-top-left-radius: calc(0.3rem - 1px);
    border-top-right-radius: calc(0.3rem - 1px);
}

.modal-header .close {
    color: white;
    opacity: 1;
    font-size: 1.5rem;
}

.modal-header .close:hover {
    color: #e9ecef;
    opacity: 0.8;
}

/* Card styling */
.card {
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    background-color: #ffffff;
}

.card-body {
    padding: 0.75rem;
}

/* Input group styling */
.input-group .btn {
    border: 1px solid #ced4da;
    background-color: #e9ecef;
    color: #495057;
    transition: all 0.15s ease-in-out;
}

.input-group .btn:hover {
    background-color: #dcdcdc;
    border-color: #adb5bd;
    color: #212529;
}

.input-group .btn-danger {
    border: 1px solid #dc3545;
    background-color: #dc3545;
    color: white;
}

.input-group .btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
    color: white;
}

/* Form group styling */
.form-group {
    margin-bottom: 1.1rem;
}

/* Modal footer */
.modal-footer {
    background-color: #f8f9fa;
    padding: 1rem;
    border-bottom-right-radius: calc(0.3rem - 1px);
    border-bottom-left-radius: calc(0.3rem - 1px);
}

/* Textarea styling */
textarea.form-control {
    resize: vertical;
}

/* Section headers */
h6.font-weight-bold {
    color: #212529;
    font-weight: 700;
}

/* Input group text */
.input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
}

/* Progress bar styling */
.progress {
    background-color: #e9ecef;
    border-radius: 1rem;
}

.progress-bar {
    background-color: #dc3545;
}

/* File list items */
.list-group-item {
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    margin-bottom: 0.25rem;
    transition: all 0.15s ease-in-out;
}

.list-group-item:hover {
    background-color: #f8f9fa;
    transform: translateY(-1px);
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

/* Style for disabled content */
.disabled-content {
    pointer-events: none;
    opacity: 0.6;
}

.disabled-dropzone {
    opacity: 0.6;
    pointer-events: none;
    cursor: not-allowed;
}
</style>
