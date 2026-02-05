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
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Clasificación <span
                                                class="text-danger">*</span></label>
                                        <select v-model="form.sugerencia_clasificacion" class="form-control" required>
                                            <option value="" disabled>Selecciona...</option>
                                            <option value="MP">Mejora de procesos y servicios (MP)</option>
                                            <option value="MT">Mejora tecnológica (MT)</option>
                                            <option value="AC">Atención al cliente y trato del personal (AC)</option>
                                            <option value="MF">Mejora de infraestructura física (MF)</option>
                                            <option value="CF">Capacitación y formación (CF)</option>
                                            <option value="CT">Comunicación y transparencia (CT)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Procedencia <span
                                                class="text-danger">*</span></label>
                                        <select v-model="form.sugerencia_procedencia" class="form-control" required>
                                            <option value="" disabled>Selecciona...</option>
                                            <option value="virtual">Virtual</option>
                                            <option value="fisico">Físico</option>
                                            <option value="entrevista">Entrevista</option>
                                            <option value="encuesta">Encuesta</option>
                                            <option value="otros">Otros</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="font-weight-bold custom-label">Detalle de la Sugerencia <span
                                            class="text-danger">*</span></label>
                                    <small class="form-text text-muted">
                                        {{ form.sugerencia_detalle ? form.sugerencia_detalle.length : 0 }}/500
                                    </small>
                                </div>
                                <textarea v-model="form.sugerencia_detalle" class="form-control" rows="5" required
                                    placeholder="Describe la sugerencia..." maxlength="500"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Fecha de Ingreso <span
                                                class="text-danger">*</span></label>
                                        <input type="date" v-model="form.sugerencia_fecha_ingreso" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Proceso <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" v-model="processName" class="form-control" readonly
                                                placeholder="Seleccionar proceso..." :required="!form.proceso_id">
                                            <div class="input-group-append">
                                                <button class="btn btn-dark" type="button" @click="openProcessModal">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                                <button class="btn btn-danger" type="button" v-if="form.proceso_id"
                                                    @click="clearProcess">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección de Evidencias -->
                            <div class="form-group">
                                <label class="font-weight-bold custom-label">Evidencias de Implementación</label>
                                <div class="drop-zone" @dragenter.prevent="onDragEnter" @dragleave.prevent="onDragLeave"
                                    @dragover.prevent @drop.prevent="onDrop" :class="{ 'drag-over': isDragging }"
                                    @click="openFileDialog">
                                    <input type="file" ref="fileInput" class="d-none" @change="handleFileSelect"
                                        multiple>
                                    <div class="text-center">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                                        <p class="mb-0 mt-2">Arrastra archivos aquí o haz clic para seleccionar.</p>
                                    </div>
                                </div>

                                <!-- New Files List -->
                                <ul v-if="filesToUpload.length > 0" class="list-group mt-2">
                                    <li v-for="file in filesToUpload" :key="file.id"
                                        class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ file.file.name }}</span>
                                        <button type="button" class="btn btn-sm btn-danger"
                                            @click="removeFile(file.id)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </li>
                                </ul>

                                <!-- Existing Files List -->
                                <div v-if="existingFiles.length > 0" class="mt-2">
                                    <label class="small font-weight-bold">Archivos Existentes:</label>
                                    <ul class="list-group">
                                        <li v-for="(file, index) in existingFiles" :key="index"
                                            class="list-group-item d-flex justify-content-between align-items-center">
                                            <a :href="file.path.startsWith('http') ? file.path : `/storage/${file.path}`"
                                                target="_blank">{{ file.name }}</a>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                @click="removeExistingFile(index)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Estado hidden default -->
                            <input type="hidden" v-model="form.sugerencia_estado">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="close">
                                <i class="fa fa-times mr-1"></i> Cancelar</button>
                            <button type="submit" class="btn btn-danger"> <i class="fa fa-save mr-1"></i>
                                Guardar</button>
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
import { ref, watch, onMounted, onUnmounted, nextTick, computed } from 'vue';
import { useSugerenciasStore } from '@/stores/sugerenciasStore';
import { useAuthStore } from '@/stores/authStore';
import { Modal } from 'bootstrap';
import ModalHijo from '@/components/generales/ModalHijo.vue';

const props = defineProps({
    show: Boolean,
    sugerenciaId: Number, // If null, create mode
});

const emit = defineEmits(['close', 'saved']);

const sugerenciasStore = useSugerenciasStore();
const authStore = useAuthStore();

const modalRef = ref(null);
const modalInstance = ref(null);
const modalTitle = ref('Nueva Sugerencia');
const processName = ref('');
const processModal = ref(null); // Referencia al ModalHijo
const fileInput = ref(null);

const processRoute = route('procesos.buscar');

const form = ref({
    sugerencia_clasificacion: '',
    sugerencia_detalle: '',
    sugerencia_fecha_ingreso: '',
    sugerencia_procedencia: '',
    proceso_id: null,
    sugerencia_estado: 'identificada', // Default state
    sugerencia_evidencia: null // For file/s
});

// Variables para archivos
const isDragging = ref(false);
const filesToUpload = ref([]);
const existingFiles = ref([]);
let fileCounter = 0;

const formatDateForInput = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const loadSugerencia = async (id) => {
    try {
        const data = await sugerenciasStore.fetchSugerenciaById(id);
        form.value = {
            sugerencia_clasificacion: data.sugerencia_clasificacion,
            sugerencia_detalle: data.sugerencia_detalle,
            sugerencia_fecha_ingreso: formatDateForInput(data.sugerencia_fecha_ingreso),
            sugerencia_procedencia: data.sugerencia_procedencia,
            proceso_id: data.proceso_id,
            sugerencia_estado: data.sugerencia_estado,
            sugerencia_evidencia: null
        };

        // Cargar archivos existentes
        existingFiles.value = [];
        if (data.sugerencia_evidencia) {
            if (Array.isArray(data.sugerencia_evidencia)) {
                existingFiles.value = data.sugerencia_evidencia.map(item => {
                    if (typeof item === 'object' && item.path) return item;
                    return { path: item, name: item.split('/').pop() };
                });
            } else if (typeof data.sugerencia_evidencia === 'string') {
                try {
                    const parsed = JSON.parse(data.sugerencia_evidencia);
                    if (Array.isArray(parsed)) {
                        existingFiles.value = parsed.map(item => {
                            if (typeof item === 'object' && item.path) return item;
                            return { path: item, name: item.split('/').pop() };
                        });
                    } else {
                        existingFiles.value = [{ path: parsed, name: parsed.split('/').pop() }];
                    }
                } catch (e) {
                    existingFiles.value = [{
                        path: data.sugerencia_evidencia,
                        name: data.sugerencia_evidencia.split('/').pop()
                    }];
                }
            }
        }

        if (data.proceso) {
            processName.value = data.proceso.proceso_nombre || data.proceso.nombre || data.proceso.descripcion || data.proceso.proceso_nombre_corto;
        }

        modalTitle.value = 'Editar Sugerencia';
    } catch (error) {
        console.error('Error loading sugerencia:', error);
    }
};

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

// File handling logic
const handleFileSelect = (e) => startUpload(Array.from(e.target.files));
const openFileDialog = () => fileInput.value.click();
const onDragEnter = () => isDragging.value = true;
const onDragLeave = () => isDragging.value = false;
const onDrop = (event) => {
    isDragging.value = false;
    startUpload(Array.from(event.dataTransfer.files));
};

const startUpload = (files) => {
    const maxFileSize = 10 * 1024 * 1024;
    files.forEach(file => {
        if (file.size > maxFileSize) {
            alert(`El archivo '${file.name}' supera el límite de 10MB.`);
            return;
        }
        filesToUpload.value.push({ id: fileCounter++, file: file, progress: 0 });
    });
};

const removeFile = (id) => {
    filesToUpload.value = filesToUpload.value.filter(f => f.id !== id);
};

const removeExistingFile = (index) => {
    existingFiles.value.splice(index, 1);
};

const canCloseSugerencia = computed(() => {
    if (!props.sugerenciaId || form.value.sugerencia_estado === 'cerrada') return false;
    // Check for admin or especialista role
    return authStore.hasRole('admin') || authStore.hasRole('especialista');
});

const closeSugerencia = () => {
    if (confirm('¿Está seguro de cerrar esta sugerencia?')) {
        form.value.sugerencia_estado = 'cerrada';
        submitForm();
    }
};

watch(() => props.show, async (newVal) => {
    if (newVal) {
        filesToUpload.value = [];
        existingFiles.value = [];
        if (props.sugerenciaId) {
            await loadSugerencia(props.sugerenciaId);
        } else {
            form.value = {
                sugerencia_clasificacion: '',
                sugerencia_detalle: '',
                sugerencia_fecha_ingreso: new Date().toISOString().split('T')[0],
                sugerencia_procedencia: '',
                proceso_id: null,
                sugerencia_estado: 'identificada',
                sugerencia_evidencia: null
            };
            processName.value = '';
            modalTitle.value = 'Nueva Sugerencia';
        }

        await nextTick();
        if (modalRef.value && !modalInstance.value) {
            modalInstance.value = new Modal(modalRef.value, { backdrop: 'static', keyboard: false });
        }
        modalInstance.value?.show();
    } else {
        modalInstance.value?.hide();
    }
});

const close = () => {
    if (document.activeElement instanceof HTMLElement) document.activeElement.blur();
    emit('close');
};

const submitForm = async () => {
    try {
        // Logic to determine status
        if (form.value.sugerencia_estado !== 'cerrada') {
            const hasEvidence = filesToUpload.value.length > 0 || existingFiles.value.length > 0;

            // Logic Simplified per user request: 'identificada' default.
            // If evidence is added, maybe we don't change status here explicitly unless requested.
            // Keeping it simple as 'identificada' if it's new.
        }

        // Prepare plain object for the store
        const submitData = { ...form.value };

        // Remove 'sugerencia_evidencia' (singular) as we use plural for upload
        delete submitData.sugerencia_evidencia;

        // Add files
        submitData.sugerencia_evidencias = filesToUpload.value.map(f => f.file);
        submitData.existing_evidencias = existingFiles.value.map(f => f.path);

        if (props.sugerenciaId) {
            await sugerenciasStore.updateSugerencia(props.sugerenciaId, submitData);
        } else {
            await sugerenciasStore.createSugerencia(submitData);
        }
        emit('saved');
        close();
    } catch (error) {
        console.error('Error saving:', error);
        alert('Error: ' + (error.response?.data?.message || error.message));
    }
};

onMounted(() => {
    if (modalRef.value) modalRef.value.addEventListener('hidden.bs.modal', close);
});

onUnmounted(() => {
    modalInstance.value?.dispose();
    if (modalRef.value) modalRef.value.removeEventListener('hidden.bs.modal', close);
});

</script>

<style scoped>
.custom-label {
    font-size: 0.9em !important;
    font-weight: 600 !important;
    color: #495057 !important;
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
</style>
