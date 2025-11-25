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

                                <!-- Archivo actualmente almacenado -->
                                <div v-if="form.snc_evidencia && !filesToUpload.some(f => f.file.name === form.snc_evidencia)"
                                    class="mt-3">
                                    <label class="font-weight-bold">Archivo Existente:</label>
                                    <div class="list-group">
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ form.snc_evidencia }}</span>
                                            <button @click="removeCurrentFile"
                                                class="btn btn-danger btn-sm">Eliminar</button>
                                        </div>
                                    </div>
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
import axios from 'axios';
import { route } from 'ziggy-js';
import { Modal } from 'bootstrap';
import ModalHijo from '@/components/generales/ModalHijo.vue';

const props = defineProps({
    show: Boolean,
    snc: Object, // null for create, object for edit
});

const emit = defineEmits(['update:show', 'saved']);

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
    snc_evidencia: null
});

const processName = ref('');
const fileInput = ref(null);
const modalRef = ref(null);
const modalInstance = ref(null);
const processModal = ref(null);

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
            snc_evidencia: null
        };
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

const removeCurrentFile = () => {
    form.value.snc_evidencia = null;
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
            snc_evidencia: null
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

const submitForm = async () => {
    try {

        // Obtener el cookie CSRF para Sanctum antes de enviar la solicitud
        await axios.get('/sanctum/csrf-cookie');

        // Creamos un FormData para manejar la subida de archivos
        const formData = new FormData();

        // Agregamos todos los campos del formulario
        for (const key in form.value) {
            // Excluir snc_codigo al crear (se genera automáticamente en el backend)
            if (key === 'snc_codigo' && !props.snc) {
                continue; // No enviar snc_codigo en operaciones de creación
            }

            if (form.value[key] !== null && form.value[key] !== undefined) {
                // Manejar el campo de evidencia de forma especial
                if (key === 'snc_evidencia') {
                    // Solo agregamos la ruta existente si no hay nuevos archivos para subir
                    if (typeof form.value[key] === 'string' && filesToUpload.value.length === 0) {
                        formData.append(key, form.value[key]);
                    }
                }
                // Si es un objeto o array (pero no un File), lo convertimos a string
                else if (typeof form.value[key] === 'object' && !(form.value[key] instanceof Date) && !(form.value[key] instanceof File)) {
                    formData.append(key, JSON.stringify(form.value[key]));
                }
                // Para otros tipos (string, number, boolean, etc.), lo agregamos directamente
                else {
                    formData.append(key, form.value[key]);
                }
            }
        }

        // Agregar archivos nuevos para subir
        filesToUpload.value.forEach((fileEntry, index) => {
            // Agregar cada archivo como 'snc_evidencia[]' para que el backend lo reciba como array
            formData.append('snc_evidencia[]', fileEntry.file, fileEntry.file.name);
        });

        // Asegurar que el campo snc_requiere_accion_correctiva sea un valor booleano adecuado para Laravel
        if (form.value.snc_requiere_accion_correctiva !== undefined && form.value.snc_requiere_accion_correctiva !== null) {
            // Convertir a booleano y luego a string '1' o '0' como espera Laravel para campos tinyint
            const boolValue = form.value.snc_requiere_accion_correctiva === true ||
                             form.value.snc_requiere_accion_correctiva === 'true' ||
                             form.value.snc_requiere_accion_correctiva === '1' ||
                             form.value.snc_requiere_accion_correctiva === 1 ||
                             form.value.snc_requiere_accion_correctiva === 'on';
            formData.set('snc_requiere_accion_correctiva', boolValue ? '1' : '0');
        } else {
            // Si es nulo, establecer como '0' (falso) para que pase la validación
            formData.set('snc_requiere_accion_correctiva', '0');
        }

        // Configurar las cabeceras para la solicitud
        const config = {
            headers: {
                'Content-Type': 'multipart/form-data',
                'X-Requested-With': 'XMLHttpRequest'  // Asegura que sea reconocida como solicitud AJAX
            },
            withCredentials: true
        };

        // Agregar token CSRF si está disponible
        const token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) {
            config.headers['X-CSRF-TOKEN'] = token.content;
        }


        if (props.snc) {
            // Para actualizar, usamos POST con _method=PUT (Laravel Spoofing)
            formData.append('_method', 'PUT');
            const response = await axios.post(route('api.salidas-nc.update', props.snc.id), formData, config);
            console.log('[DEBUG] Respuesta update:', response);
        } else {
            const response = await axios.post(route('api.salidas-nc.store'), formData, config);
            console.log('[DEBUG] Respuesta store:', response);
        }

        console.log('[DEBUG] Petición exitosa mostrando alert y cerrando modal');

        // Show success feedback - using vanilla alert since toast was from PrimeVue
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
    color: #070707 !important;
    letter-spacing: 0.2px !important;
}

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

.drop-zone.disabled {
    cursor: not-allowed;
    background-color: #f8f9fa;
    opacity: 0.7;
}
</style>
