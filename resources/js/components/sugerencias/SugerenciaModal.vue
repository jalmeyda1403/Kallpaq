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
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue';
import { useSugerenciasStore } from '@/stores/sugerenciasStore';
import { Modal } from 'bootstrap';
import ModalHijo from '@/components/generales/ModalHijo.vue';

const props = defineProps({
    show: Boolean,
    sugerenciaId: Number, // If null, create mode
});

const emit = defineEmits(['close', 'saved']);

const sugerenciasStore = useSugerenciasStore();

const modalRef = ref(null);
const modalInstance = ref(null);
const modalTitle = ref('Nueva Sugerencia');
const processName = ref('');
const processModal = ref(null); // Referencia al ModalHijo

const processRoute = route('procesos.buscar');

const form = ref({
    sugerencia_clasificacion: '',
    sugerencia_detalle: '',
    sugerencia_fecha_ingreso: '',
    sugerencia_procedencia: '',
    proceso_id: null, // Cambiado de '' a null para consistencia
    sugerencia_estado: 'abierta'
});

const formatDateForInput = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toISOString().split('T')[0];
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
            sugerencia_estado: data.sugerencia_estado
        };

        // Actualizar el nombre del proceso directamente desde la información que ya viene con la sugerencia
        if (data.proceso) {
            processName.value = data.proceso.proceso_nombre || data.proceso.nombre || data.proceso.descripcion || data.proceso.proceso_nombre_corto;
        }

        modalTitle.value = 'Editar Sugerencia';
    } catch (error) {
        console.error('Error loading sugerencia:', error);
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

watch(() => props.show, async (newVal) => {
    if (newVal) {
        if (props.sugerenciaId) {
            await loadSugerencia(props.sugerenciaId);
        } else {
            form.value = {
                sugerencia_clasificacion: '',
                sugerencia_detalle: '',
                sugerencia_fecha_ingreso: new Date().toISOString().split('T')[0],
                sugerencia_procedencia: '',
                proceso_id: null,
                sugerencia_estado: 'abierta'
            };
            processName.value = '';
            modalTitle.value = 'Nueva Sugerencia';
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
    // Remove focus from the button to prevent ARIA warnings when modal hides
    if (document.activeElement instanceof HTMLElement) {
        document.activeElement.blur();
    }
    emit('close');
};

const submitForm = async () => {
    try {
        if (props.sugerenciaId) {
            await sugerenciasStore.updateSugerencia(props.sugerenciaId, form.value);
        } else {
            await sugerenciasStore.createSugerencia(form.value);
        }
        emit('saved');
        close();
    } catch (error) {
        console.error('Error saving sugerencia:', error);
        alert('Error al guardar la sugerencia: ' + (error.response?.data?.message || error.message));
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
