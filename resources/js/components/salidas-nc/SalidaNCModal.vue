<template>
    <Teleport to="body">
        <div ref="modalRef" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="salidaNCModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="salidaNCModalLabel">{{ modalTitle }}</h5>
                        <button type="button" class="close text-white" @click="close" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="submitForm">
                        <div class="modal-body">
                            <!-- Primera fila: Origen y Clasificación -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Origen <span
                                                class="text-danger">*</span></label>
                                        <select v-model="form.snc_origen" class="form-control" required>
                                            <option value="" disabled>Selecciona...</option>
                                            <option value="cliente">Cliente</option>
                                            <option value="auditoría interna">Auditoría Interna</option>
                                            <option value="auditoría externa">Auditoría Externa</option>
                                            <option value="otro">Otro</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Clasificación <span
                                                class="text-danger">*</span></label>
                                        <select v-model="form.snc_clasificacion" class="form-control" required>
                                            <option value="" disabled>Selecciona...</option>
                                            <option value="crítica">Crítica</option>
                                            <option value="mayor">Mayor</option>
                                            <option value="menor">Menor</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Segunda fila: Descripción -->
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="font-weight-bold custom-label">Descripción <span
                                            class="text-danger">*</span></label>
                                    <small class="form-text text-muted">
                                        {{ form.snc_descripcion ? form.snc_descripcion.length : 0 }}/500
                                    </small>
                                </div>
                                <textarea v-model="form.snc_descripcion" class="form-control" rows="5" required
                                    placeholder="Ingrese la descripción de la no conformidad..."
                                    @input="updateCharCount" maxlength="500"></textarea>
                            </div>

                            <!-- Tercera fila: Proceso -->
                            <div class="form-group">
                                <label class="font-weight-bold custom-label">Proceso <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" v-model="processName" class="form-control" readonly
                                        placeholder="Seleccionar proceso..." :required="!form.proceso_id">
                                    <div class="input-group-append">
                                        <button class="btn btn-dark" type="button" @click="openProcessModal">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <button class="btn btn-danger" type="button" v-if="form.proceso_id"
                                            @click="clearProcess">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Cuarta fila: Cantidad Afectada y Fecha Detección -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Cantidad Afectada</label>
                                        <input type="number" v-model="form.snc_cantidad_afectada" class="form-control"
                                            min="0" placeholder="Ingrese la cantidad afectada...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Fecha Detección <span
                                                class="text-danger">*</span></label>
                                        <input type="date" v-model="form.snc_fecha_deteccion" class="form-control"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <!-- Quinta fila: Responsable -->
                            <div class="form-group">
                                <label class="font-weight-bold custom-label">Responsable <span
                                        class="text-danger">*</span></label>
                                <input type="text" v-model="form.snc_responsable" class="form-control" required
                                    placeholder="Ingrese el nombre del responsable...">
                            </div>

                            <!-- Sexta fila: Evidencia -->
                            <div class="form-group">
                                <label class="font-weight-bold custom-label">Evidencia</label>
                                <small class="form-text text-muted mb-2">Adjunte archivos de evidencia si los
                                    tiene.</small>
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

                                <!-- Lista de archivos seleccionados -->
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
                                    <label class="font-weight-bold">Archivos Existentes:</label>
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
                                                class="btn btn-danger btn-sm" title="Eliminar archivo">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Séptima fila: Estado (oculto, valor por defecto) -->
                            <input type="hidden" v-model="form.snc_estado" value="registrada">
                        </div>
                        <div class="modal-footer d-flex justify-content-between">
                            <div></div> <!-- Empty div for spacing -->
                            <div>
                                <button type="button" class="btn btn-secondary" @click="close">
                                    <i class="fas fa-times mr-1"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-danger ml-2" :disabled="!isValid">
                                    <i class="fas fa-save mr-1"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ModalHijo para seleccionar procesos -->
        <ModalHijo ref="processModal" :fetch-url="processRoute" target-id="id" target-desc="proceso_nombre"
            @update-target="handleProcessSelected" />
    </Teleport>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, computed, nextTick } from 'vue';
import { useSalidasNCStore } from '@/stores/salidasNCStore';
import { Modal } from 'bootstrap';
import ModalHijo from '@/components/generales/ModalHijo.vue';

const props = defineProps({
    show: Boolean,
    snc: Object, // null for create, object for edit
});

const emit = defineEmits(['update:show', 'saved']);

const salidasNCStore = useSalidasNCStore();

const form = ref({
    snc_descripcion: '',
    snc_cantidad_afectada: null,
    snc_fecha_deteccion: null,
    snc_responsable: '',
    snc_origen: '',
    snc_clasificacion: '',
    snc_tratamiento: '',
    snc_descripcion_tratamiento: '',
    snc_fecha_tratamiento: null,
    snc_costo_estimado: null,
    snc_estado: 'registrada',
    snc_requiere_accion_correctiva: null,
    snc_fecha_cierre: null,
    snc_observaciones: '',
    proceso_id: null,
    snc_archivos: null
});

const processName = ref('');
const fileInput = ref(null);
const modalRef = ref(null);
const modalInstance = ref(null);
const processModal = ref(null);
const existingFiles = ref([]);

// Variables para la subida de archivos de evidencia
const isDragging = ref(false);
const filesToUpload = ref([]);
let fileCounter = 0;

const modalTitle = ref('Nueva Salida No Conforme');

const processRoute = route('procesos.buscar');

onMounted(() => {
    // No es necesario cargar usuarios ya que no se usa el campo 'detectado por'
});

watch(() => props.snc, (newVal) => {
    if (newVal) {
        modalTitle.value = 'Editar Salida No Conforme';
        form.value = {
            ...newVal,
            snc_producto_servicio: undefined,  // Remover campo no usado
            snc_tipo: undefined,  // Remover campo no usado
            snc_detectado_por: undefined,  // Remover campo no usado
            // Asegurarnos de que snc_requiere_accion_correctiva sea booleano
            snc_requiere_accion_correctiva: newVal.snc_requiere_accion_correctiva === true || newVal.snc_requiere_accion_correctiva === '1' || newVal.snc_requiere_accion_correctiva === 1
        };

        // Cargar archivos existentes
        existingFiles.value = [];
        if (newVal.snc_archivos) {
            // El backend ya devuelve un array (gracias al cast 'array' del modelo)
            if (Array.isArray(newVal.snc_archivos)) {
                existingFiles.value = newVal.snc_archivos.map(item => {
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
            } else if (typeof newVal.snc_archivos === 'string') {
                // Fallback: si por alguna razón viene como string
                existingFiles.value = [{
                    path: newVal.snc_archivos,
                    name: newVal.snc_archivos.split('/').pop()
                }];
            }
        }

        console.log('Archivos existentes cargados (SNC):', existingFiles.value);

        // Formatear la fecha de detección para el input de tipo date
        if (newVal.snc_fecha_deteccion) {
            form.value.snc_fecha_deteccion = formatDateForInput(newVal.snc_fecha_deteccion);
        }

        // Actualizar el nombre del proceso si está definido
        if (newVal.proceso && newVal.proceso.proceso_nombre) {
            processName.value = newVal.proceso.proceso_nombre;
        } else if (newVal.proceso_id) {
            // Si solo tenemos id, intentamos obtener el nombre
            loadProcessName(newVal.proceso_id);
        }
    } else {
        modalTitle.value = 'Nueva Salida No Conforme';
        form.value = {
            snc_codigo: '',
            snc_descripcion: '',
            snc_cantidad_afectada: null,
            snc_fecha_deteccion: null,
            snc_responsable: '',
            snc_origen: '',
            snc_clasificacion: '',
            snc_tratamiento: '',
            snc_descripcion_tratamiento: '',
            snc_fecha_tratamiento: null,
            snc_costo_estimado: null,
            snc_estado: 'registrada',
            snc_requiere_accion_correctiva: false, // Cambiado de null a false para que sea un booleano
            snc_fecha_cierre: null,
            snc_observaciones: '',
            proceso_id: null,
            snc_archivos: null
        };
        existingFiles.value = [];
        processName.value = '';
    }
});

// Cargar el nombre del proceso si solo tenemos el ID
const loadProcessName = async (processId) => {
    try {
        const response = await axios.get(route('procesos.show', { proceso: processId }));
        processName.value = response.data.proceso_nombre || response.data.descripcion;
    } catch (error) {
        console.error('Error al cargar el nombre del proceso:', error);
        processName.value = 'Proceso no encontrado';
    }
};

// Funciones para manejar el proceso
const openProcessModal = () => {
    processModal.value.open();
};

const handleProcessSelected = (data) => {
    form.value.proceso_id = data.idValue;
    processName.value = data.descValue;
};

const clearProcess = () => {
    form.value.proceso_id = null;
    processName.value = '';
};

// Función para actualizar el contador de caracteres
const updateCharCount = () => {
    // Esta función se ejecuta cada vez que se introduce texto en el campo de descripción
    // No necesita lógica adicional ya que Vue se encarga de actualizar el DOM automáticamente
};

// Asegurarnos de restaurar el scroll cuando el modal se cierra
onMounted(() => {
    const handleModalHidden = (e) => {
        // Verificar si el modal que se está ocultando es el ModalHijo
        // Usamos el evento 'hidden.bs.modal' que es disparado por Bootstrap
        if (e.target !== modalRef.value && e.target.classList.contains('modal')) {
            // Si el body no tiene la clase 'modal-open' pero nuestro modal padre está abierto,
            // restauramos la clase y el padding adecuado para el scroll
            if (!document.body.classList.contains('modal-open') &&
                modalInstance.value && modalInstance.value._isShown) {
                document.body.classList.add('modal-open');

                // También restauramos el padding derecho si fue modificado por Bootstrap
                const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;
                if (scrollBarWidth > 0) {
                    document.body.style.paddingRight = scrollBarWidth + 'px';
                }
            }
        }
    };

    // Añadir listener para el evento de ocultar modal
    document.addEventListener('hidden.bs.modal', handleModalHidden);

    // Cleanup: remover el listener cuando el componente se desmonte
    onUnmounted(() => {
        document.removeEventListener('hidden.bs.modal', handleModalHidden);
    });
});

// Funciones para manejar el archivo de evidencia (mejorado)
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
        // No subimos archivos inmediatamente, solo los marcamos para subida en submitForm
    });
};

// Función para formatear fecha para input de tipo date
const formatDateForInput = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const removeFile = (fileId) => {
    filesToUpload.value = filesToUpload.value.filter(f => f.id !== fileId);
};

const removeExistingFile = (index) => {
    existingFiles.value.splice(index, 1);
};

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

        // reset form when modal is closed
        form.value = {
            snc_descripcion: '',
            snc_cantidad_afectada: null,
            snc_fecha_deteccion: null,
            snc_responsable: '',
            snc_origen: '',
            snc_clasificacion: '',
            snc_tratamiento: '',
            snc_descripcion_tratamiento: '',
            snc_fecha_tratamiento: null,
            snc_costo_estimado: null,
            snc_estado: 'registrada',
            snc_requiere_accion_correctiva: null,
            snc_fecha_cierre: null,
            snc_observaciones: '',
            proceso_id: null,
            snc_archivos: null
        };
        modalTitle.value = props.snc ? 'Editar Salida No Conforme' : 'Nueva Salida No Conforme';
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

const isValid = computed(() => {
    return (
        form.value.snc_descripcion &&
        form.value.snc_fecha_deteccion &&
        form.value.snc_responsable &&
        form.value.proceso_id &&
        form.value.snc_origen &&
        form.value.snc_clasificacion &&
        form.value.snc_estado
    );
});

const removeCurrentFile = () => {
    form.value.snc_archivos = null;
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
                    filesFormData.append('existing_archivos[]', file.path);
                });
            }

            // Agregar archivos nuevos para subir
            if (hasNewFiles) {
                filesToUpload.value.forEach((fileEntry) => {
                    filesFormData.append('snc_archivos[]', fileEntry.file, fileEntry.file.name);
                });
            }

            // Añadir indicación de que se están actualizando archivos de registro
            filesFormData.append('update_registration', '1');

            if (props.snc) {
                // Actualizar la SNC existente
                await salidasNCStore.updateSNC(props.snc.id, filesFormData);
            } else {
                // Crear nueva SNC
                await salidasNCStore.createSNC(filesFormData);
            }
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

            if (props.snc) {
                // Actualizar la SNC existente
                await salidasNCStore.updateSNC(props.snc.id, form.value);
            } else {
                // Crear nueva SNC
                await salidasNCStore.createSNC(form.value);
            }
        }

        // Show success feedback
        alert('Operación completada exitosamente');
        emit('saved');
        close();
    } catch (e) {
        alert('No se pudo guardar: ' + (e.response?.data?.message || e.message));
    }
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
</style>
