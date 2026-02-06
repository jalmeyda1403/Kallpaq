<template>
    <teleport to="body">
        <div v-if="show" class="modal fade show" role="dialog" style="display: block; overflow-y: auto; z-index: 1060;"
            aria-labelledby="tratamientoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="tratamientoModalLabel">Tratamiento de Salida No Conforme</h5>
                        <button type="button" class="close text-white" @click="close" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="submitForm">
                        <div class="modal-body">
                            <!-- Alerta cuando está cerrada -->
                            <div v-if="form.snc_estado === 'cerrada'" class="alert alert-purple mb-3">
                                <small class="text-white">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Salida No Conforme cerrada el {{ formatDate(form.snc_fecha_cierre) }}
                                </small>
                            </div>

                            <!-- Alerta de Observación -->
                            <div v-if="form.snc_estado === 'observada'" class="alert alert-warning mb-3">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>¡Atención! Esta Salida NC ha sido observada</strong>
                                </div>
                                <hr class="my-2" style="border-top-color: rgba(0,0,0,0.1);">
                                <p class="mb-0 text-dark">
                                    {{ snc.snc_observacion || 'Sin motivo de observación registrado.' }}
                                </p>
                            </div>

                            <!-- Row 1: Tratamiento | Requiere Accion | Estado -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark custom-label">Tratamiento</label>
                                        <select v-model="form.snc_tratamiento" class="form-control"
                                            :disabled="form.snc_estado === 'cerrada'">
                                            <option value="">Selecciona...</option>
                                            <option value="corrección">Corrección</option>
                                            <option value="concesion o aceptación">Concesión o Aceptación</option>
                                            <option value="rechazo">Rechazo</option>
                                            <option value="sustitucion">Sustitución</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark custom-label">Requiere Acción
                                            Correctiva</label>
                                        <select v-model="form.snc_requiere_accion_correctiva" class="form-control"
                                            :disabled="form.snc_estado === 'cerrada'">
                                            <option :value="null">Seleccionar...</option>
                                            <option :value="true">Sí</option>
                                            <option :value="false">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark custom-label">Estado</label>
                                        <select v-model="form.snc_estado" class="form-control" @change="onEstadoChange"
                                            :disabled="form.snc_estado === 'cerrada'">
                                            <option value="identificada">Identificada</option>
                                            <option value="en análisis">En Análisis</option>
                                            <option value="en tratamiento">En Tratamiento</option>
                                            <option value="tratada">Tratada</option>
                                            <option value="cerrada" disabled>Cerrada</option>
                                            <option value="observada" disabled>Observada</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2: Responsable -->
                            <div class="form-group">
                                <label class="font-weight-bold text-dark custom-label">Responsable <span
                                        class="text-danger">*</span></label>
                                <input type="text" v-model="form.snc_responsable" class="form-control" required
                                    :disabled="form.snc_estado === 'cerrada'"
                                    placeholder="Ingrese el nombre del responsable...">
                            </div>

                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="font-weight-bold text-dark custom-label">Descripción del
                                        Tratamiento</label>
                                    <small class="text-muted">
                                        {{ form.snc_descripcion_tratamiento ? form.snc_descripcion_tratamiento.length :
                                            0 }}/500
                                    </small>
                                </div>
                                <textarea v-model="form.snc_descripcion_tratamiento" class="form-control" rows="3"
                                    maxlength="500"
                                    :disabled="form.snc_tratamiento === 'concesion o aceptación' || form.snc_estado === 'cerrada'"
                                    placeholder="Ingrese la descripción del tratamiento aplicado..."></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark custom-label">Fecha de Tratamiento
                                            (Prog.)</label>
                                        <input type="date" v-model="form.snc_fecha_fecha_fin_prog" class="form-control"
                                            :disabled="form.snc_estado === 'cerrada'">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark custom-label">Costo Estimado</label>
                                        <input type="number" v-model="form.snc_costo_estimado" class="form-control"
                                            min="0" step="0.01" placeholder="Ingrese el costo estimado..."
                                            :disabled="form.snc_estado === 'cerrada'">
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="form.snc_estado === 'tratada' || form.snc_estado === 'cerrada'">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-dark custom-label">Fecha Fin Real</label>
                                        <input type="date" v-model="form.snc_fecha_fin_real" class="form-control"
                                            readonly disabled>
                                    </div>
                                </div>
                            </div>



                            <!-- Sección de Evidencias (Multi-archivo) -->
                            <div class="form-group">
                                <label class="font-weight-bold file-list-label">Evidencias del Tratamiento</label>
                                <small class="form-text text-muted mb-2">Adjunte archivos de evidencia del tratamiento
                                    si los tiene.</small>
                                <div class="drop-zone" @dragenter.prevent="onDragEnter" @dragleave.prevent="onDragLeave"
                                    @dragover.prevent @drop.prevent="onDrop"
                                    :class="{ 'drag-over': isDragging, 'disabled': form.snc_estado === 'cerrada' }"
                                    @click="form.snc_estado !== 'cerrada' && openFileDialog()">
                                    <input type="file" ref="fileInput" class="d-none" @change="handleFileSelect"
                                        multiple :disabled="form.snc_estado === 'cerrada'">
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
                                    <label class="font-weight-bold file-list-label">Evidencias Existentes:</label>
                                    <ul class="list-group">
                                        <li v-for="(file, index) in existingFiles" :key="index"
                                            class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="text-truncate" style="max-width: 85%;">
                                                <i class="fas fa-paperclip mr-2 text-muted"></i>
                                                <a :href="`/storage/${file.path}`" target="_blank"
                                                    class="text-decoration-none text-dark">
                                                    {{ file.name }}
                                                </a>
                                            </div>
                                            <button type="button" @click="removeExistingFile(index)"
                                                class="btn btn-danger btn-sm" title="Eliminar archivo"
                                                v-if="form.snc_estado !== 'cerrada'">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Sección Historial de Movimientos -->
                            <div v-if="snc && snc.movimientos && snc.movimientos.length > 0" class="mt-4">
                                <h6 class="font-weight-bold mb-3"><i class="fas fa-history mr-2"></i>Historial de
                                    Movimientos</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-striped"
                                        style="font-size: 0.85rem;">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 15%;">Fecha</th>
                                                <th style="width: 15%;">Estado</th>
                                                <th style="width: 20%;">Responsable</th>
                                                <th style="width: 50%;">Comentarios / Observaciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="mov in snc.movimientos" :key="mov.id">
                                                <td>{{ formatDateDateTime(mov.fecha_movimiento) }}</td>
                                                <td>
                                                    <span :class="getEstadoBadgeClass(mov.estado)">
                                                        {{ capitalizeFirstLetter(mov.estado) }}
                                                    </span>
                                                </td>
                                                <td>{{ mov.user ? mov.user.name : 'Sistema' }}</td>
                                                <td>{{ mov.observacion || '-' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer d-flex justify-content-end">
                            <div class="mr-auto"
                                v-if="canClose && snc && (snc.snc_estado === 'tratada' || snc.snc_estado === 'cerrada')">
                                <!-- Mostrar botón cerrar solo si es tratada, si es cerrada ya no se puede hacer mucho pero podría querer ver historial -->
                                <button type="button" class="btn bg-purple text-white" @click="openEvaluacionModal"
                                    v-if="snc.snc_estado === 'tratada'">
                                    <i class="fas fa-check-double mr-1"></i> Cerrar / Observar SNC
                                </button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-secondary" @click="close">
                                    <i class="fas fa-times mr-1"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-danger ml-2"
                                    :disabled="!isValid || form.snc_estado === 'cerrada'">
                                    <i class="fas fa-save mr-1"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal de Evaluación (Validación/Cierre) -->
        <SNCEvaluacionModal :show="showEvaluacionModal" :snc-id="snc?.id" :snc-data="snc" @close="closeEvaluacionModal"
            @validated="onSNCValidated" />

        <div v-if="show" class="modal-backdrop fade show" style="z-index: 1055;"></div>
    </Teleport>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, computed, nextTick } from 'vue';
import { useSalidasNCStore } from '@/stores/salidasNCStore';
import { useAuthStore } from '@/stores/authStore';
import { Modal } from 'bootstrap';
import SNCEvaluacionModal from './SNCEvaluacionModal.vue';

import Swal from 'sweetalert2';

const props = defineProps({
    show: Boolean,
    snc: Object, // objeto de salida no conforme
});

const emit = defineEmits(['update:show', 'saved']);

const salidasNCStore = useSalidasNCStore();
const authStore = useAuthStore();

const form = ref({
    snc_responsable: '',
    snc_tratamiento: '',
    snc_descripcion_tratamiento: '',
    snc_fecha_fecha_fin_prog: null,
    snc_fecha_fin_real: null,
    snc_costo_estimado: null,
    snc_requiere_accion_correctiva: null,
    snc_fecha_cierre: null,
    snc_evidencias: null,
    snc_estado: 'identificada'
});

const modalRef = ref(null);
const modalInstance = ref(null);
const existingFiles = ref([]);
const fileInput = ref(null);

// Variables para la subida de archivos de evidencia
const isDragging = ref(false);
const filesToUpload = ref([]);
let fileCounter = 0;

onMounted(() => {
    // Cargar datos iniciales si es edición
});

const onEstadoChange = () => {
    if (form.value.snc_estado === 'tratada') {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        form.value.snc_fecha_fin_real = `${year}-${month}-${day}`;
    } else {
        form.value.snc_fecha_fin_real = null;
    }
};

// Watch para tratamiento
watch(() => form.value.snc_tratamiento, (newVal) => {
    if (newVal === 'concesion o aceptación') {
        // Solo cambiar el estado a 'tratada' si no está cerrada
        if (form.value.snc_estado !== 'cerrada') {
            form.value.snc_estado = 'tratada';
            onEstadoChange(); // Actualizar fecha fin real
        }
        form.value.snc_descripcion_tratamiento = 'No aplica por concesión o aceptación';
    } else if (form.value.snc_descripcion_tratamiento === 'No aplica por concesión o aceptación') {
        form.value.snc_descripcion_tratamiento = '';
    }
});

// Función para formatear fecha para input de tipo date
const formatDateForInput = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

watch(() => props.snc, (newVal) => {
    if (newVal) {
        // Cargar datos del tratamiento desde la SNC
        form.value = {
            snc_responsable: newVal.snc_responsable || '', // Cargar responsable
            snc_tratamiento: newVal.snc_tratamiento || '',
            snc_descripcion_tratamiento: newVal.snc_descripcion_tratamiento || '',
            snc_fecha_fecha_fin_prog: newVal.snc_fecha_fecha_fin_prog ? formatDateForInput(newVal.snc_fecha_fecha_fin_prog) : null,
            snc_fecha_fin_real: newVal.snc_fecha_fin_real ? formatDateForInput(newVal.snc_fecha_fin_real) : null,
            snc_costo_estimado: newVal.snc_costo_estimado || null,
            snc_requiere_accion_correctiva: newVal.snc_requiere_accion_correctiva,
            snc_fecha_cierre: newVal.snc_fecha_cierre ? formatDateForInput(newVal.snc_fecha_cierre) : null,
            snc_evidencias: newVal.snc_evidencias || null,
            snc_estado: newVal.snc_estado || 'identificada'
        };

        // Cargar archivos existentes
        existingFiles.value = [];
        if (newVal.snc_evidencias) {
            // El backend ya devuelve un array (gracias al cast 'array' del modelo)
            if (Array.isArray(newVal.snc_evidencias)) {
                existingFiles.value = newVal.snc_evidencias.map(item => {
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
            } else if (typeof newVal.snc_evidencias === 'string') {
                // Fallback: si por alguna razón viene como string
                existingFiles.value = [{
                    path: newVal.snc_evidencias,
                    name: newVal.snc_evidencias.split('/').pop()
                }];
            }
        }
    }
}, { immediate: true });

watch(() => props.show, async (newVal) => {
    if (newVal) {
        // When show becomes true, we wait for the next tick to ensure DOM is updated
        await nextTick();
        if (modalRef.value) {
            // Create new modal instance if one doesn't already exist
            if (!modalInstance.value) {
                modalInstance.value = new Modal(modalRef.value, {
                    backdrop: 'static', // Prevent closing by clicking outside
                    keyboard: false     // Prevent closing by pressing ESC
                });
            }
            modalInstance.value.show();
        }
    } else {
        // When show becomes false, hide the modal if it's open
        if (modalInstance.value) {
            modalInstance.value.hide();
        }

        // Limpiar archivos al cerrar
        filesToUpload.value = [];
        existingFiles.value = [];
    }
}, { immediate: true });

// Handle the modal hidden event to sync the show prop
onMounted(() => {
    if (modalRef.value) {
        modalRef.value.addEventListener('hidden.bs.modal', () => {
            emit('update:show', false);
        });
    }
});

// Remove the modal instance when component is unmounted to prevent memory leaks
onUnmounted(() => {
    if (modalInstance.value) {
        modalInstance.value.dispose();
        modalInstance.value = null;
    }
});

const close = () => {
    // Remove focus from the button to prevent ARIA warnings when modal hides
    if (document.activeElement instanceof HTMLElement) {
        document.activeElement.blur();
    }

    if (modalInstance.value) {
        modalInstance.value.hide();
    } else {
        emit('update:show', false);
    }
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

const isValid = computed(() => {
    return true; // Validación personalizada según sea necesario
});

const canClose = computed(() => {
    return authStore.hasRole('admin') || authStore.hasRole('especialista');
});

const showEvaluacionModal = ref(false);

const openEvaluacionModal = () => {
    showEvaluacionModal.value = true;
};

const closeEvaluacionModal = () => {
    showEvaluacionModal.value = false;
};

const onSNCValidated = () => {
    emit('saved');
    close();
};

const getEstadoBadgeClass = (estado) => {
    const classes = {
        'identificada': 'badge badge-secondary',
        'en análisis': 'badge badge-info',
        'en tratamiento': 'badge badge-primary',
        'tratada': 'badge badge-success',
        'cerrada': 'badge badge-purple',
        'observada': 'badge badge-warning'
    };
    return classes[estado] || 'badge badge-light';
};

const capitalizeFirstLetter = (string) => {
    if (!string) return '';
    return string.charAt(0).toUpperCase() + string.slice(1);
};

// Funciones auxiliares para la tabla de historial
const formatDateDateTime = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleString();
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const getInitials = (name) => {
    if (!name) return 'S';
    return name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
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
                    if (key === 'snc_requiere_accion_correctiva') {
                        // Manejo explícito de booleanos
                        const boolValue = form.value[key] === true || form.value[key] === '1' || form.value[key] === 1;
                        filesFormData.append(key, boolValue ? '1' : '0');
                    } else if (typeof form.value[key] === 'object' && !(form.value[key] instanceof Date)) {
                        filesFormData.append(key, JSON.stringify(form.value[key]));
                    } else {
                        filesFormData.append(key, form.value[key]);
                    }
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
                    filesFormData.append('snc_evidencias[]', fileEntry.file, fileEntry.file.name);
                });
            }

            // Añadir indicación de que se están actualizando evidencias de tratamiento
            filesFormData.append('update_treatment', '1');

            // Actualizar la SNC con la información de tratamiento
            await salidasNCStore.updateTratamiento(props.snc.id, filesFormData);
        } else {
            // Asegurar que el campo snc_requiere_accion_correctiva sea un valor booleano adecuado
            if (form.value.snc_requiere_accion_correctiva !== undefined && form.value.snc_requiere_accion_correctiva !== null) {
                const boolValue = form.value.snc_requiere_accion_correctiva === true ||
                    form.value.snc_requiere_accion_correctiva === 'true' ||
                    form.value.snc_requiere_accion_correctiva === '1' ||
                    form.value.snc_requiere_accion_correctiva === 1 ||
                    form.value.snc_requiere_accion_correctiva === 'on';
                form.value.snc_requiere_accion_correctiva = boolValue ? 1 : 0;
            }

            // Asegurar que si el estado no es 'tratada', la fecha fin real sea null
            if (form.value.snc_estado !== 'tratada') {
                form.value.snc_fecha_fin_real = null;
            }

            // Actualizar la SNC con la información de tratamiento
            await salidasNCStore.updateTratamiento(props.snc.id, form.value);
        }

        // Show success feedback
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: 'Tratamiento actualizado exitosamente',
            timer: 2000,
            showConfirmButton: false
        });
        emit('saved');
        close();
    } catch (e) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo guardar: ' + (e.response?.data?.message || e.message)
        });
        console.error(e);
    }
};
</script>

<style scoped>
.custom-label {
    font-size: 0.9em !important;
    font-weight: 600 !important;
    color: #495057 !important;
}

/* Smaller font size for labels of file lists */
.file-list-label {
    font-size: 0.85em !important;
    font-weight: 600 !important;
    color: #6c757d !important;
    margin-bottom: 0.5rem !important;
}

/* Improved drop zone styles */
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

/* File list items */
.list-group-item {
    transition: all 0.15s ease-in-out;
}

.list-group-item:hover {
    background-color: #f8f9fa;
    transform: translateY(-1px);
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.bg-purple {
    background-color: #605ca8 !important;
}

.avatar-circle {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: bold;
}

.alert-purple {
    background-color: #605ca8;
    color: white;
    border-color: #4f4c8b;
}

.alert-purple .text-white {
    color: white !important;
}
</style>
```