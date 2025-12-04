<template>
    <Teleport to="body">
        <div ref="modalRef" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Registrar Avance: {{ indicador?.indicador_nombre }}</h5>
                        <button type="button" class="close text-white" @click="close" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="submitForm">
                        <div class="modal-body">
                            <!-- Row 1: Period Info -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Año (Periodo) <span
                                                class="text-danger">*</span></label>
                                        <select v-model="form.is_periodo" class="form-control" required>
                                            <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">N° Periodo</label>
                                        <div class="input-group">
                                            <input type="text" v-model="form.is_numero_periodo" class="form-control"
                                                readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-info" type="button" @click="fetchNextPeriod"
                                                    :disabled="isLoadingPeriod" title="Obtener siguiente periodo">
                                                    <i class="fas"
                                                        :class="isLoadingPeriod ? 'fa-spinner fa-spin' : 'fa-sync-alt'"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2: Formula -->
                            <div class="d-flex align-items-center mb-3 p-2 bg-light rounded border"
                                v-if="indicador?.indicador_formula">
                                <i class="fas fa-calculator text-muted mr-2"></i>
                                <span class="font-weight-bold text-dark mr-2">Fórmula del indicador:</span>
                                <span class="text-monospace text-secondary">{{ indicador.indicador_formula }}</span>
                            </div>

                            <!-- Row 3: Variables -->
                            <div class="row">
                                <div class="col-md-12" v-if="indicador?.indicador_var1">
                                    <div class="form-group row">
                                        <label
                                            class="col-sm-5 col-form-label text-right custom-label font-weight-bold">{{
                                                indicador.indicador_var1 }} (V1)</label>
                                        <div class="col-sm-7">
                                            <input type="text" v-model="form.is_var1"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" v-if="indicador?.indicador_var2">
                                    <div class="form-group row">
                                        <label
                                            class="col-sm-5 col-form-label text-right custom-label font-weight-bold">{{
                                                indicador.indicador_var2 }} (V2)</label>
                                        <div class="col-sm-7">
                                            <input type="text" v-model="form.is_var2"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" v-if="indicador?.indicador_var3">
                                    <div class="form-group row">
                                        <label
                                            class="col-sm-5 col-form-label text-right custom-label font-weight-bold">{{
                                                indicador.indicador_var3 }} (V3)</label>
                                        <div class="col-sm-7">
                                            <input type="text" v-model="form.is_var3"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" v-if="indicador?.indicador_var4">
                                    <div class="form-group row">
                                        <label
                                            class="col-sm-5 col-form-label text-right custom-label font-weight-bold">{{
                                                indicador.indicador_var4 }} (V4)</label>
                                        <div class="col-sm-7">
                                            <input type="text" v-model="form.is_var4"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" v-if="indicador?.indicador_var5">
                                    <div class="form-group row">
                                        <label
                                            class="col-sm-5 col-form-label text-right custom-label font-weight-bold">{{
                                                indicador.indicador_var5 }} (V5)</label>
                                        <div class="col-sm-7">
                                            <input type="text" v-model="form.is_var5"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" v-if="indicador?.indicador_var6">
                                    <div class="form-group row">
                                        <label
                                            class="col-sm-5 col-form-label text-right custom-label font-weight-bold">{{
                                                indicador.indicador_var6 }} (V6)</label>
                                        <div class="col-sm-7">
                                            <input type="text" v-model="form.is_var6"
                                                class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 4: Meta and Value -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Meta del Periodo <span
                                                class="text-danger">*</span></label>
                                        <input type="text" v-model="form.is_meta" class="form-control" required
                                            :readonly="isMetaReadonly">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Valor del Periodo <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" v-model="form.is_valor" class="form-control" readonly
                                                required>
                                            <div class="input-group-append">
                                                <button class="btn btn-warning" type="button" @click="calculateValue"
                                                    title="Calcular">
                                                    <i class="fas fa-calculator"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 5: Evidences -->
                            <div class="form-group mt-2">
                                <label class="font-weight-bold custom-label">Evidencias del Avance</label>
                                <small class="form-text text-muted mb-2">Adjunte archivos de evidencia del avance
                                    si los tiene.</small>
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
                                    <label class="font-weight-bold custom-label">Evidencias Existentes:</label>
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
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- History Table -->
                            <div class="mt-4">
                                <h6 class="font-weight-bold mb-3">Historial de Avances</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-sm">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="text-center">Periodo</th>
                                                <th class="text-center">N°</th>
                                                <th class="text-center">Meta</th>
                                                <th class="text-center">Valor</th>
                                                <th class="text-center">Evidencia</th>
                                                <th v-if="isAdmin" class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="avance in avances" :key="avance.id">
                                                <td class="text-center">{{ avance.is_periodo }}</td>
                                                <td class="text-center">{{ avance.is_numero_periodo }}</td>
                                                <td class="text-center">{{ avance.is_meta }}</td>
                                                <td class="text-center">{{ avance.is_valor }}</td>
                                                <td class="text-center">
                                                    <span v-if="avance.is_evidencias"
                                                        class="badge badge-success">Sí</span>
                                                    <span v-else class="badge badge-secondary">No</span>
                                                </td>
                                                <td v-if="isAdmin" class="text-center">
                                                    <!-- Placeholder for edit action -->
                                                    <button type="button" class="btn btn-sm btn-warning" title="Editar"
                                                        @click="editAvance(avance)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr v-if="avances.length === 0">
                                                <td colspan="6" class="text-center text-muted">No hay avances
                                                    registrados.</td>
                                            </tr>
                                        </tbody>
                                    </table>
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
                                    <i class="fas fa-save mr-1"></i> {{ editingAvanceId ? 'Actualizar' : 'Guardar' }}
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
import { ref, watch, onMounted, onUnmounted, nextTick, computed } from 'vue';
import axios from 'axios';
import { Modal } from 'bootstrap';

const props = defineProps({
    visible: Boolean,
    indicador: Object
});

const emit = defineEmits(['close', 'saved']);

const modalRef = ref(null);
const modalInstance = ref(null);
const fileInput = ref(null);

// Variables para la subida de archivos de evidencia
const isDragging = ref(false);
const filesToUpload = ref([]);
let fileCounter = 0;
const existingFiles = ref([]);
const editingAvanceId = ref(null);

const avances = ref([]); // Historial de avances
const isLoadingPeriod = ref(false);

// Generar lista de años (actual +/- 1)
const years = computed(() => {
    const currentYear = new Date().getFullYear();
    return [currentYear - 1, currentYear, currentYear + 1].sort((a, b) => b - a);
});

// TODO: Implementar lógica real de permisos. Por ahora asumimos true o lo dejamos pendiente.
const isAdmin = ref(true);

const form = ref({
    is_periodo: new Date().getFullYear(),
    is_numero_periodo: '',
    is_fecha: new Date().toISOString().substr(0, 10),
    is_meta: '',
    is_valor: '',
    is_var1: '',
    is_var2: '',
    is_var3: '',
    is_var4: '',
    is_var5: '',
    is_var6: '',
});

console.log('IndicadorAvanceForm initialized', form.value);

const isMetaReadonly = computed(() => {
    return props.indicador?.indicador_sentido === 'lineal';
});

const fetchNextPeriod = async () => {
    if (!props.indicador?.id || !form.value.is_periodo) return;
    isLoadingPeriod.value = true;
    try {
        const response = await axios.post('/api/indicadores-gestion/next-period', {
            indicador_id: props.indicador.id,
            year: form.value.is_periodo
        });
        form.value.is_numero_periodo = response.data.periodo;
        if (response.data.full) {
            // Opcional: Mostrar alerta de que ya se completaron los periodos
            alert('Se ha alcanzado el número máximo de periodos para este año según la frecuencia del indicador.');
        }
    } catch (error) {
        console.error("Error fetching next period", error);
        alert("Error al obtener el siguiente periodo: " + (error.response?.data?.message || error.message));
    } finally {
        isLoadingPeriod.value = false;
    }
};

const fetchHistory = async () => {
    if (!props.indicador?.id) return;
    try {
        const response = await axios.get(`/api/indicadores-gestion/${props.indicador.id}/avances`);
        avances.value = response.data;
    } catch (error) {
        console.error("Error fetching history", error);
    }
};

const calculateValue = () => {
    if (!props.indicador?.indicador_formula) {
        alert('El indicador no tiene una fórmula definida.');
        return;
    }

    let formula = props.indicador.indicador_formula;

    // Normalizar la fórmula: reemplazar 'x' por '*' para multiplicación
    formula = formula.replace(/x/gi, '*');

    const variables = {
        'V1': parseFloat(form.value.is_var1) || 0,
        'V2': parseFloat(form.value.is_var2) || 0,
        'V3': parseFloat(form.value.is_var3) || 0,
        'V4': parseFloat(form.value.is_var4) || 0,
        'V5': parseFloat(form.value.is_var5) || 0,
        'V6': parseFloat(form.value.is_var6) || 0,
    };

    try {
        // Reemplazar variables en la fórmula
        for (const [key, value] of Object.entries(variables)) {
            // Usar regex para reemplazar palabra completa (ej: V1 pero no V10), case-insensitive
            const regex = new RegExp(`\\b${key}\\b`, 'gi');
            formula = formula.replace(regex, value);
        }

        // Limpiar la fórmula de caracteres no permitidos, pero permitir espacios
        // Permitimos: dígitos, puntos, operadores (+, -, *, /), paréntesis y espacios
        if (!/^[\d\.\+\-\*\/\(\)\s]+$/.test(formula)) {
            console.error("Fórmula inválida tras reemplazo:", formula);
            throw new Error("La fórmula contiene caracteres no válidos.");
        }

        const result = eval(formula);

        if (!isFinite(result) || isNaN(result)) {
            throw new Error("El resultado no es un número válido (posible división por cero).");
        }

        form.value.is_valor = parseFloat(result.toFixed(2)); // Redondear a 2 decimales
    } catch (error) {
        console.error("Error al calcular:", error);
        alert("Error al calcular la fórmula: " + error.message);
    }
};

const close = () => {
    // Remove focus from the button to prevent ARIA warnings when modal hides
    if (document.activeElement instanceof HTMLElement) {
        document.activeElement.blur();
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

// Watchers
watch(() => form.value.is_periodo, () => {
    fetchNextPeriod();
});

// Limpiar valor si cambian las variables
watch(() => [form.value.is_var1, form.value.is_var2, form.value.is_var3, form.value.is_var4, form.value.is_var5, form.value.is_var6], () => {
    if (form.value.is_valor) {
        form.value.is_valor = '';
    }
});

const editAvance = (avance) => {
    editingAvanceId.value = avance.id;
    form.value.is_periodo = avance.is_periodo;
    form.value.is_numero_periodo = avance.is_numero_periodo;
    form.value.is_fecha = avance.is_fecha || new Date().toISOString().substr(0, 10);
    form.value.is_meta = avance.is_meta;
    form.value.is_valor = avance.is_valor;
    form.value.is_var1 = avance.is_var1;
    form.value.is_var2 = avance.is_var2;
    form.value.is_var3 = avance.is_var3;
    form.value.is_var4 = avance.is_var4;
    form.value.is_var5 = avance.is_var5;
    form.value.is_var6 = avance.is_var6;

    existingFiles.value = [];
    if (avance.evidencias_list && Array.isArray(avance.evidencias_list)) {
        existingFiles.value = avance.evidencias_list;
    } else if (avance.is_evidencias && typeof avance.is_evidencias === 'string') {
        try {
            const parsed = JSON.parse(avance.is_evidencias);
            if (Array.isArray(parsed)) {
                existingFiles.value = parsed.map(path => ({ path: path, name: path.split('/').pop() }));
            }
        } catch (e) {
            existingFiles.value = [{ path: avance.is_evidencias, name: avance.is_evidencias.split('/').pop() }];
        }
    }

    if (modalRef.value) {
        modalRef.value.querySelector('.modal-body').scrollTop = 0;
    }
};

const submitForm = async () => {
    try {
        // Verificar si hay cambios en los archivos (nuevos o eliminados)
        const hasNewFiles = filesToUpload.value.length > 0;
        const hasFileChanges = hasNewFiles || existingFiles.value.length > 0;

        // Siempre usamos FormData si hay archivos o si estamos editando (para consistencia)
        // Ojo: Laravel PUT con FormData a veces requiere _method: PUT

        const filesFormData = new FormData();

        // Agregar todos los campos del formulario al FormData
        for (const key in form.value) {
            if (form.value[key] !== null && form.value[key] !== undefined && form.value[key] !== '') {
                filesFormData.append(key, form.value[key]);
            }
        }

        // Agregar el ID del indicador
        filesFormData.append('indicador_id', props.indicador.id);

        // Agregar archivos existentes que se mantienen
        if (existingFiles.value.length > 0) {
            existingFiles.value.forEach((file) => {
                filesFormData.append('existing_evidencias[]', file.path);
            });
        }

        // Agregar archivos nuevos para subir
        if (hasNewFiles) {
            filesToUpload.value.forEach((fileEntry) => {
                filesFormData.append('is_evidencias[]', fileEntry.file, fileEntry.file.name);
            });
        }

        // Añadir indicación de que se están actualizando evidencias
        // Esto es útil tanto para create como update si queremos lógica unificada
        if (hasFileChanges) {
            filesFormData.append('update_evidences', '1');
        }

        if (editingAvanceId.value) {
            // UPDATE
            // Para soportar archivos en update, usamos POST con _method = PUT o simplemente POST si la ruta lo permite.
            // Hemos definido la ruta update como POST /avance/{id} para simplificar.
            await axios.post(`/api/indicadores-gestion/avance/${editingAvanceId.value}`, filesFormData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
        } else {
            // CREATE
            await axios.post('/api/indicadores-gestion/avance', filesFormData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
        }

        emit('saved');
        close();
    } catch (error) {
        console.error('Error saving avance:', error);
        let msg = error.response?.data?.message || error.message;

        if (error.response?.status === 422 && error.response?.data?.errors) {
            const errors = error.response.data.errors;
            // Si hay un error específico de validación, lo mostramos
            if (errors.is_numero_periodo) {
                msg = errors.is_numero_periodo[0];
            } else {
                // Sino, mostramos el primer error que encontremos
                const firstError = Object.values(errors)[0];
                msg = Array.isArray(firstError) ? firstError[0] : firstError;
            }
        }

        alert('Error al guardar el avance: ' + msg);
    }
};

// ... (watchers)

watch(() => props.visible, async (newVal) => {
    if (newVal) {
        editingAvanceId.value = null; // Reset edit mode

        // Reset form when modal opens
        form.value.is_periodo = new Date().getFullYear();
        form.value.is_fecha = new Date().toISOString().substr(0, 10);
        form.value.is_meta = '';
        form.value.is_valor = '';
        form.value.is_var1 = '';
        form.value.is_var2 = '';
        form.value.is_var3 = '';
        form.value.is_var4 = '';
        form.value.is_var5 = '';
        form.value.is_var6 = '';
        form.value.is_numero_periodo = '';

        // Clear file lists
        filesToUpload.value = [];
        existingFiles.value = [];

        // Fetch data
        await fetchNextPeriod();
        await fetchHistory();

        // Set meta if lineal
        if (props.indicador?.indicador_sentido === 'lineal') {
            form.value.is_meta = props.indicador.indicador_meta;
        }

        // Show modal
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
        editingAvanceId.value = null;
    }
}, { immediate: true });



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
