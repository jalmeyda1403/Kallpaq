<template>
    <div>
        <Teleport to="body">
            <div class="modal fade" ref="modalRef" aria-hidden="true" style="z-index: 1060;">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">Gestión de Reprogramaciones</h5>
                            <button type="button" class="close text-white" @click="closeModal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Formulario de Solicitud -->
                            <div class="card mb-3">
                                <div class="card-header bg-light font-weight-bold">
                                    Nueva Solicitud
                                </div>
                                <div class="card-body">
                                    <form @submit.prevent="submitReprogramacion">
                                        <div class="form-group">
                                            <label class="font-weight-bold custom-label">Tipo de Acción <span
                                                    class="text-danger">*</span></label>
                                            <select v-model="form.actionType" class="form-control" required>
                                                <option value="reprogramar">Reprogramar Fecha</option>
                                                <option value="desestimar">Desestimar Acción</option>
                                            </select>
                                        </div>
                                        <div class="form-group" v-if="form.actionType === 'reprogramar'">
                                            <label class="font-weight-bold custom-label">Nueva Fecha Fin <span
                                                    class="text-danger">*</span></label>
                                            <input v-model="form.accion_fecha_fin_reprogramada" type="date"
                                                class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="font-weight-bold custom-label">Justificación <span
                                                        class="text-danger">*</span></label>
                                                <small class="text-muted">Caracteres: {{ form.accion_justificacion.length }}
                                                    / 500</small>
                                            </div>
                                            <textarea v-model="form.accion_justificacion" class="form-control" rows="5"
                                                :maxlength="500" required></textarea>
                                        </div>
                                        <!-- Sección de Evidencias (Multi-archivo) -->
                                        <div class="form-group">
                                            <label class="font-weight-bold custom-label">Evidencia (Opcional)</label>
                                            <small class="form-text text-muted mb-2">Adjunte archivos de evidencia si
                                                los tiene.</small>
                                            <div class="drop-zone" @dragenter.prevent="onDragEnter"
                                                @dragleave.prevent="onDragLeave" @dragover.prevent
                                                @drop.prevent="onDrop" :class="{ 'drag-over': isDragging }"
                                                @click="openFileDialog">
                                                <input type="file" ref="fileInput" class="d-none"
                                                    @change="handleFileSelect" multiple>
                                                <div class="text-center">
                                                    <i class="fa fa-cloud-upload fa-3x text-muted"></i>
                                                    <p class="mb-0 mt-2">Arrastra y suelta archivos aquí, o haz clic
                                                        para
                                                        seleccionar.</p>
                                                    <small class="text-muted">(Peso máximo por archivo: 10MB)</small>
                                                </div>
                                            </div>

                                            <!-- Lista de archivos seleccionados para subir -->
                                            <ul v-if="filesToUpload.length > 0" class="list-group mt-3">
                                                <li v-for="file in filesToUpload" :key="file.id"
                                                    class="list-group-item">
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
                                                        <button @click="removeFile(file.id)"
                                                            class="btn btn-danger btn-sm">
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
                                        <div class="modal-footer d-flex justify-content-between">
                                            <div></div> <!-- Empty div for spacing -->
                                            <div>
                                                <button type="button" class="btn btn-secondary" @click="closeModal">
                                                    <i class="fa fa-times mr-1"></i> Cancelar
                                                </button>
                                                <button type="submit" class="btn btn-danger ml-2" :disabled="saving">
                                                    <i class="fa fa-save mr-1"></i>
                                                    <span v-if="saving" class="spinner-border spinner-border-sm mr-1"
                                                        role="status" aria-hidden="true"></span>
                                                    Enviar Solicitud
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Historial de Reprogramaciones -->
                            <div class="card">
                                <div class="card-header bg-light font-weight-bold">
                                    Historial de Solicitudes
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Fecha Solicitada</th>
                                                    <th>Justificación</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="rep in sortedReprogramaciones" :key="rep.id">
                                                    <td>{{ formatDate(rep.rar_fecha_nueva) }}</td>
                                                    <td>{{ rep.rar_justificacion }}</td>
                                                    <td>
                                                        <span class="badge"
                                                            :class="getReprogramacionBadge(rep.rar_estado)">
                                                            {{ rep.rar_estado }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div v-if="rep.ar_estado === 'solicitado'">
                                                            <button class="btn btn-xs btn-success mr-1"
                                                                @click="aprobar(rep)" title="Aprobar">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button class="btn btn-xs btn-danger" @click="rechazar(rep)"
                                                                title="Rechazar">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                        <div v-if="rep.ar_evidencia">
                                                            <a :href="`/storage/${rep.ar_evidencia}`" target="_blank"
                                                                class="btn btn-xs btn-info" title="Ver Evidencia">
                                                                <i class="fas fa-paperclip"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr v-if="sortedReprogramaciones.length === 0">
                                                    <td colspan="4" class="text-center text-muted">No hay historial de
                                                        reprogramaciones.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted, onUnmounted, computed } from 'vue';
import { useRiesgoStore } from '@/stores/riesgoStore';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';

const props = defineProps({
    show: Boolean,
    actionData: Object
});

const emit = defineEmits(['close', 'updated']);

const store = useRiesgoStore();
const modalRef = ref(null);
const modalInstance = ref(null);
const fileInput = ref(null);
const saving = ref(false);

// Variables para la subida de archivos de evidencia
const isDragging = ref(false);
const filesToUpload = ref([]);
let fileCounter = 0;
const existingFiles = ref([]);

const form = reactive({
    id: null,
    actionType: 'reprogramar',
    accion_fecha_fin_reprogramada: '',
    accion_justificacion: '',
    accion_evidencia: null
});

const sortedReprogramaciones = computed(() => {
    if (!props.actionData || !props.actionData.reprogramaciones) return [];
    return [...props.actionData.reprogramaciones].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
});

watch(() => props.show, (newVal) => {
    if (newVal) {
        if (props.actionData) {
            form.id = props.actionData.id;
            // Cargar archivos existentes si están disponibles en actionData
            loadExistingFiles();
        }
        resetForm();

        if (modalInstance.value) {
            modalInstance.value.show();
        }
    } else {
        if (modalInstance.value) {
            modalInstance.value.hide();
        }
    }
});

onMounted(() => {
    if (modalRef.value) {
        modalInstance.value = new Modal(modalRef.value);
        modalRef.value.addEventListener('hidden.bs.modal', () => {
            emit('close');
        });
    }
});

onUnmounted(() => {
    if (modalInstance.value) {
        modalInstance.value.dispose();
    }
});

const loadExistingFiles = () => {
    // Cargar archivos existentes si están disponibles
    existingFiles.value = [];
    if (props.actionData?.ra_evidencia) {
        // Asumiendo que actionData.ra_evidencia puede ser un string o un array
        if (Array.isArray(props.actionData.ra_evidencia)) {
            existingFiles.value = props.actionData.ra_evidencia.map(item => {
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
        } else if (typeof props.actionData.ra_evidencia === 'string') {
            existingFiles.value = [{
                path: props.actionData.ra_evidencia,
                name: props.actionData.ra_evidencia.split('/').pop()
            }];
        }
    }
};

const resetForm = () => {
    form.actionType = 'reprogramar';
    form.accion_fecha_fin_reprogramada = '';
    form.accion_justificacion = '';
    form.accion_evidencia = null;
    // Reset file input manually if possible, or use a key to force re-render
    // Limpiar archivos
    filesToUpload.value = [];
    existingFiles.value = [];
};

const closeModal = () => {
    if (modalInstance.value) {
        modalInstance.value.hide();
    }
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

const submitReprogramacion = async () => {
    saving.value = true;
    try {
        // Verificar si hay cambios en los archivos (nuevos o eliminados)
        const hasNewFiles = filesToUpload.value.length > 0;
        const hasFileChanges = hasNewFiles || existingFiles.value.length > 0;

        if (hasFileChanges) {
            // Creamos un FormData para manejar la subida de archivos
            const filesFormData = new FormData();

            // Agregar todos los campos del formulario al FormData
            filesFormData.append('actionType', form.actionType);
            filesFormData.append('accion_justificacion', form.accion_justificacion);
            if (form.actionType === 'reprogramar') {
                filesFormData.append('accion_fecha_fin_reprogramada', form.accion_fecha_fin_reprogramada);
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
                    filesFormData.append('accion_evidencia[]', fileEntry.file, fileEntry.file.name);
                });
            }

            // Añadir indicación de que se están actualizando evidencias
            filesFormData.append('update_evidences', '1');

            const response = await store.reprogramarAccion(form.id, filesFormData);

            Swal.fire('Éxito', 'Solicitud enviada correctamente', 'success');
            emit('updated', response.accion);
            resetForm();
        } else {
            // Enviar la información sin archivos
            const formData = new FormData();
            formData.append('actionType', form.actionType);
            formData.append('accion_justificacion', form.accion_justificacion);
            if (form.actionType === 'reprogramar') {
                formData.append('accion_fecha_fin_reprogramada', form.accion_fecha_fin_reprogramada);
            }

            const response = await store.reprogramarAccion(form.id, formData);

            Swal.fire('Éxito', 'Solicitud enviada correctamente', 'success');
            emit('updated', response.accion);
            resetForm();
        }
    } catch (error) {
        console.error('Error submitting reprogramming:', error);
        Swal.fire('Error', 'No se pudo enviar la solicitud: ' + (error.response?.data?.message || error.message), 'error');
    } finally {
        saving.value = false;
    }
};

const aprobar = async (reprogramacion) => {
    const { value: comentario } = await Swal.fire({
        target: modalRef.value,
        title: 'Aprobar Reprogramación',
        input: 'text',
        inputLabel: 'Comentario de Aprobación',
        showCancelButton: true,
        inputValidator: (value) => {
            if (!value) {
                return 'Debes escribir un comentario'
            }
        }
    });

    if (comentario) {
        try {
            const response = await store.aprobarReprogramacion(reprogramacion.id, comentario);
            Swal.fire({
                target: modalRef.value,
                title: 'Aprobar Reprogramación',
                text: 'La reprogramación ha sido aprobada.',
                icon: 'success'
            });
            emit('updated', response.accion);
        } catch (error) {
            Swal.fire({
                target: modalRef.value,
                title: 'Error',
                text: 'No se pudo aprobar la reprogramación.',
                icon: 'error'
            });
        }
    }
};

const rechazar = async (reprogramacion) => {
    const { value: comentario } = await Swal.fire({
        target: modalRef.value,
        title: 'Rechazar Reprogramación',
        input: 'text',
        inputLabel: 'Motivo del Rechazo',
        showCancelButton: true,
        inputValidator: (value) => {
            if (!value) {
                return 'Debes escribir un motivo'
            }
        }
    });

    if (comentario) {
        try {
            const response = await store.rechazarReprogramacion(reprogramacion.id, comentario);
            Swal.fire({
                target: modalRef.value,
                title: 'Rechazado',
                text: 'La reprogramación ha sido rechazada.',
                icon: 'success'
            });
            emit('updated', response.accion);
        } catch (error) {
            Swal.fire({
                target: modalRef.value,
                title: 'Error',
                text: 'No se pudo rechazar la reprogramación.',
                icon: 'error'
            });
        }
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString();
};

const getReprogramacionBadge = (status) => {
    const badges = {
        'pendiente': 'badge badge-warning',
        'aprobado': 'badge badge-success',
        'rechazado': 'badge badge-danger'
    };
    return badges[status] || 'badge badge-secondary';
};
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

/* Table styling */
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
}

.table {
    margin-bottom: 0;
}

.table td {
    vertical-align: middle;
}

.table .btn-xs {
    padding: 0.25rem 0.4rem;
    font-size: 0.75rem;
    line-height: 0.5;
    border-radius: 0.2rem;
}

.table .badge {
    padding: 0.3em 0.6em;
    font-size: 0.75rem;
}
</style>
