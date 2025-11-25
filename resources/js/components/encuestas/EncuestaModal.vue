<template>
    <Teleport to="body">
        <div ref="modalRef" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">{{ modalTitle }}</h5>
                        <button type="button" class="close text-white" @click="close" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="submitForm">
                        <div class="modal-body">
                            <!-- Datos Generales -->
                            <h6 class="font-weight-bold text-primary mb-3">Datos Generales</h6>

                            <!-- Fila 1: Proceso (ancho completo) -->
                            <div class="row mb-3">
                                <div class="col-md-12">
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
                                </div>
                            </div>

                            <!-- Fila 2: Año, Periodo y Número -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Año <span
                                                class="text-danger">*</span></label>
                                        <input type="number" v-model="form.es_anio" class="form-control" required
                                            min="2020" max="2100">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Periodo <span
                                                class="text-danger">*</span></label>
                                        <select v-model="form.es_periodo" class="form-control" required
                                            @change="onPeriodoChange">
                                            <option value="" disabled>Selecciona...</option>
                                            <option value="Bimestral">Bimestral</option>
                                            <option value="Trimestral">Trimestral</option>
                                            <option value="Semestral">Semestral</option>
                                            <option value="Anual">Anual</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Número <span
                                                class="text-danger">*</span></label>
                                        <select v-model="form.es_numero_periodo" class="form-control" required
                                            :disabled="!form.es_periodo">
                                            <option value="" disabled>Selecciona...</option>
                                            <option v-for="n in getPeriodoOptions()" :key="n" :value="n">{{ n }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Fila 3: Cantidad de Encuestas, NPS Score y Score -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Cantidad</label>
                                        <input type="number" v-model="form.es_cantidad" class="form-control"
                                            min="0">
                                        <small class="text-muted">Solo números</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">NPS Score <span
                                                class="text-danger">*</span></label>
                                        <input type="number" v-model="form.es_nps_score" class="form-control" required
                                            step="0.01" min="-100" max="100">
                                        <small class="text-muted">Valor entre -100 y 100</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Score</label>
                                        <input type="number" :value="averageScore" class="form-control" readonly
                                            step="0.01" min="0" max="5">
                                        <small class="text-muted">Promedio de conductores: {{ averageScore || 'N/A' }}</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Conductores / Categorías -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="font-weight-bold text-primary mb-3">
                                        Conductores de Satisfacción
                                        <small class="text-muted font-weight-normal ml-2">(Seleccione al menos 5)</small>
                                    </h6>
                                    <div class="alert alert-warning py-2" v-if="selectedCategoriesCount < 5">
                                        <small>
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Debe seleccionar al menos 5
                                            conductores y asignarles un puntaje.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6" v-for="(cat, index) in availableCategories" :key="index">
                                    <div class="card mb-2 border-light shadow-sm">
                                        <div class="card-body p-2 d-flex align-items-center justify-content-between">
                                            <div class="custom-control custom-checkbox flex-grow-1">
                                                <input type="checkbox" class="custom-control-input" :id="'cat-' + index"
                                                    :value="cat" v-model="selectedCategories">
                                                <label class="custom-control-label font-weight-bold"
                                                    :for="'cat-' + index">{{ cat }}</label>
                                            </div>
                                            <div v-if="selectedCategories.includes(cat)" class="ml-2" style="min-width: 120px;">
                                                <input type="number" v-model="categoryScores[cat]"
                                                    class="form-control form-control-sm" placeholder="Puntaje"
                                                    step="0.01" min="0" max="5" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Fila 4: Informe PDF (ancho completo para mejor visibilidad) -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Informe (PDF/Excel)</label>
                                        <div class="drop-zone" @dragenter.prevent="onDragEnter" @dragleave.prevent="onDragLeave"
                                            @dragover.prevent @drop.prevent="onDrop" :class="{ 'drag-over': isDragging }"
                                            @click="openFileDialog">
                                            <input type="file" ref="fileInput" class="d-none" @change="handleFileSelect"
                                                accept=".pdf,.xlsx,.xls">
                                            <div class="text-center">
                                                <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                                                <p class="mb-0 mt-2">Arrastra y suelta el archivo aquí, o haz clic para
                                                    seleccionar.</p>
                                                <small class="text-muted">(PDF o Excel, máximo 10MB)</small>
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
                                        <div v-if="existingFile" class="mt-3">
                                            <label class="font-weight-bold">Archivo Existente:</label>
                                            <div class="list-group">
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="text-truncate" style="max-width: 85%;">
                                                        <i class="fas fa-paperclip mr-2 text-muted"></i>
                                                        <a :href="`/storage/${existingFile.path || existingFile}`" target="_blank" class="text-decoration-none text-dark">
                                                            {{ existingFile.name }}
                                                        </a>
                                                    </div>
                                                    <button type="button" @click="removeCurrentFile" class="btn btn-danger btn-sm" title="Eliminar archivo">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="close">Cancelar</button>
                            <button type="submit" class="btn btn-danger"
                                :disabled="selectedCategoriesCount < 5 || loading">
                                <span v-if="loading" class="spinner-border spinner-border-sm mr-1" role="status"
                                    aria-hidden="true"></span>
                                Guardar
                            </button>
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
import { useEncuestasStore } from '@/stores/encuestasStore';
import { Modal } from 'bootstrap';
import ModalHijo from '@/components/generales/ModalHijo.vue';

const props = defineProps({
    show: Boolean,
    encuestaId: Number, // If null, create mode
});

const emit = defineEmits(['close', 'saved']);

const encuestasStore = useEncuestasStore();
const modalRef = ref(null);
const modalInstance = ref(null);
const processModal = ref(null); // Referencia al ModalHijo
const modalTitle = ref('Nuevo Resultado de Encuesta');
const processName = ref('');
const loading = ref(false);

const processRoute = route('procesos.buscar');

const availableCategories = [
    'Oportunidad', 'Tiempo', 'Trato', 'Calidad', 'Accesibilidad',
    'Claridad', 'Expertise', 'Seguridad', 'Relevancia', 'Equidad'
];

const form = ref({
    proceso_id: null,
    es_periodo: '',
    es_numero_periodo: null,
    es_anio: new Date().getFullYear(),
    es_nps_score: null,
    es_cantidad: null,
    es_score: null,
    informe: null  // This will be handled separately for file upload
});

const selectedCategories = ref([]);
const categoryScores = ref({});
const existingFile = ref(null);

const selectedCategoriesCount = computed(() => selectedCategories.value.length);

const averageScore = computed(() => {
    if (selectedCategories.value.length === 0) {
        return null;
    }

    const scores = selectedCategories.value
        .map(cat => parseFloat(categoryScores.value[cat]))
        .filter(score => !isNaN(score));

    if (scores.length === 0) {
        return null;
    }

    const avg = scores.reduce((sum, score) => sum + score, 0) / scores.length;
    return parseFloat(avg.toFixed(2)); // Round to 2 decimal places
});

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

// Variables para la subida de archivos
const fileInput = ref(null);
const isDragging = ref(false);
const filesToUpload = ref([]);
let fileCounter = 0;

const handleFileSelect = (e) => {
    const files = Array.from(e.target.files);
    startUpload(files);
};

const openFileDialog = () => {
    if (fileInput.value) {
        fileInput.value.click();
    }
};

const onDragEnter = () => {
    isDragging.value = true;
};

const onDragLeave = () => {
    isDragging.value = false;
};

const onDrop = (event) => {
    isDragging.value = false;
    const files = Array.from(event.dataTransfer.files);
    startUpload(files);
};

const startUpload = (files) => {
    const maxFileSize = 10 * 1024 * 1024; // 10MB

    // Limit to 1 file since this is for single file upload
    const file = files[0];

    if (!file) return;

    if (file.size > maxFileSize) {
        alert(`El archivo '${file.name}' supera el límite de 10MB.`);
        return;
    }

    // Clear any existing files since we only allow one file
    filesToUpload.value = [];

    const fileEntry = { id: fileCounter++, file: file, progress: 0 };
    filesToUpload.value.push(fileEntry);
};

const removeFile = (fileId) => {
    filesToUpload.value = filesToUpload.value.filter(f => f.id !== fileId);
};

const removeCurrentFile = () => {
    existingFile.value = null;
};

const getPeriodoOptions = () => {
    const options = {
        'Bimestral': 6,
        'Trimestral': 4,
        'Semestral': 2,
        'Anual': 1
    };
    const max = options[form.value.es_periodo] || 0;
    return Array.from({ length: max }, (_, i) => i + 1);
};

const onPeriodoChange = () => {
    // Reset numero_periodo when periodo changes
    form.value.es_numero_periodo = null;
};

const loadEncuesta = async (id) => {
    // We can assume the store has the data or fetch it.
    // For simplicity, let's find it in the store's list first, or fetch if needed.
    // Ideally the store should have a 'getById' or we fetch from API.
    // Let's assume we pass the full object or fetch it.
    // Since the store logic I wrote fetches all, let's try to find it there.
    const encuesta = encuestasStore.encuestas.find(e => e.id === id);

    if (encuesta) {
        form.value = {
            proceso_id: encuesta.proceso_id,
            es_periodo: encuesta.es_periodo,
            es_numero_periodo: encuesta.es_numero_periodo,
            es_anio: encuesta.es_anio,
            es_nps_score: encuesta.es_nps_score,
            es_cantidad: encuesta.es_cantidad,
            es_score: encuesta.es_score, // This is the stored value, but we'll override with calculated average when needed
            informe: null // Reset file input
        };

        if (encuesta.proceso) {
            processName.value = encuesta.proceso.proceso_nombre || encuesta.proceso.nombre;
        }

        // Format existing file as object with path and name
        if (encuesta.es_informe_path) {
            try {
                // Try to parse as JSON (new format)
                const fileData = JSON.parse(encuesta.es_informe_path);
                if (typeof fileData === 'object' && fileData.path) {
                    // New format: {path: "...", name: "..."}
                    existingFile.value = fileData;
                } else {
                    // Fallback for old format: just the path string
                    existingFile.value = {
                        path: encuesta.es_informe_path,
                        name: encuesta.es_informe_path.split('/').pop()
                    };
                }
            } catch (e) {
                // If parsing fails, handle as old format
                existingFile.value = {
                    path: encuesta.es_informe_path,
                    name: encuesta.es_informe_path.split('/').pop()
                };
            }
        } else {
            existingFile.value = null;
        }

        // Load details
        selectedCategories.value = [];
        categoryScores.value = {};
        if (encuesta.detalles) {
            encuesta.detalles.forEach(d => {
                selectedCategories.value.push(d.esd_categoria);
                categoryScores.value[d.esd_categoria] = d.esd_puntaje;
            });
        }

        modalTitle.value = 'Editar Resultado de Encuesta';
    }
};

const resetForm = () => {
    form.value = {
        proceso_id: null,
        es_periodo: '',
        es_numero_periodo: null,
        es_anio: new Date().getFullYear(),
        es_nps_score: null,
        es_cantidad: null,
        es_score: null,
        informe: null
    };
    processName.value = '';
    selectedCategories.value = [];
    categoryScores.value = {};
    existingFile.value = null;
    modalTitle.value = 'Nuevo Resultado de Encuesta';
};

watch(() => props.show, async (newVal) => {
    if (newVal) {
        if (props.encuestaId) {
            await loadEncuesta(props.encuestaId);
        } else {
            resetForm();
        }

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
    }
});

const close = () => {
    // Remove focus from any focused elements before closing
    if (document.activeElement instanceof HTMLElement) {
        document.activeElement.blur();
    }

    emit('close');
};

const submitForm = async () => {
    if (selectedCategoriesCount.value < 5) {
        alert('Debe seleccionar al menos 5 conductores.');
        return;
    }

    loading.value = true;
    try {
        const formData = new FormData();
        formData.append('proceso_id', form.value.proceso_id);
        formData.append('es_periodo', form.value.es_periodo);
        formData.append('es_numero_periodo', form.value.es_numero_periodo);
        formData.append('es_anio', form.value.es_anio);
        formData.append('es_nps_score', form.value.es_nps_score);
        if (form.value.es_cantidad !== null) formData.append('es_cantidad', form.value.es_cantidad);
        if (averageScore.value !== null) formData.append('es_score', averageScore.value);

        // Handle file upload
        if (filesToUpload.value.length > 0) {
            formData.append('informe', filesToUpload.value[0].file, filesToUpload.value[0].file.name);
        } else if (existingFile.value && existingFile.value.path) {
            // If there's an existing file that wasn't removed, send its path
            formData.append('es_informe_path', existingFile.value.path);
        } else if (props.encuestaId && !existingFile.value) {
            // If we're updating an existing record and there's no file (was deleted), remove the existing file
            formData.append('remove_informe', '1');
        }

        // Prepare details array
        const detalles = selectedCategories.value.map(cat => ({
            categoria: cat,
            puntaje: categoryScores.value[cat]
        }));
        formData.append('detalles', JSON.stringify(detalles));

        if (props.encuestaId) {
            // For update, we might need to spoof PUT if using FormData with files
            // But my store handles it with POST and the controller expects POST for update if needed or PUT
            // Let's use the store's update method which uses POST
            formData.append('_method', 'POST'); // Actually the store uses POST for update URL, so this might be redundant or needed depending on Laravel route definition.
            // Wait, I defined route as POST /{id} for update in web.php, so no need for _method=PUT spoofing unless I want to be standard REST.
            // But I defined it as POST in routes. So standard POST is fine.
            await encuestasStore.updateEncuesta(props.encuestaId, formData);
        } else {
            await encuestasStore.createEncuesta(formData);
        }

        emit('saved');
        close();
    } catch (error) {
        console.error(error);
        alert('Error al guardar: ' + (error.response?.data?.message || error.message));
    } finally {
        loading.value = false;
    }
};

// Asegurarnos de restaurar el scroll cuando el ModalHijo se cierra
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

    if (modalRef.value) {
        modalRef.value.addEventListener('hidden.bs.modal', close);
    }

    // Cleanup: remover el listener cuando el componente se desmonte
    onUnmounted(() => {
        document.removeEventListener('hidden.bs.modal', handleModalHidden);
        modalInstance.value?.dispose();
    });
});

// No need for getOriginalFileName anymore since we store original name separately
// The function is not needed as the original filename is now stored in the 'name' property
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
    text-align: center;
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
