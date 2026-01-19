<template>
    <div>
        <Teleport to="body">
            <div class="modal fade" tabindex="-1" ref="modalRef" aria-hidden="true" style="z-index: 1060;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title">Registrar Avance</h5>
                            <button type="button" class="close text-white" @click="closeModal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="submitForm">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="font-weight-bold custom-label">Estado <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" v-model="form.accion_estado" required>
                                        <option value="programada">Programada</option>
                                        <option value="desestimada">Desestimada</option>
                                        <option value="en proceso">En Proceso</option>
                                        <option value="implementada">Implementada</option>
                                        <option value="finalizada">Finalizada</option>
                                    </select>
                                </div>

                                <div class="form-group"
                                    v-if="['implementada', 'finalizada'].includes(form.accion_estado)">
                                    <label class="font-weight-bold custom-label">Fecha Real de Fin <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" v-model="form.accion_fecha_fin_real"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold custom-label">
                                        Evidencia 
                                        <span v-if="['implementada', 'finalizada'].includes(form.accion_estado)" class="text-danger">*</span>
                                        <span v-else class="text-muted font-weight-normal">(Opcional)</span>
                                    </label>
                                    
                                     <div v-if="existingEvidence" class="alert alert-secondary d-flex justify-content-between align-items-center p-2 mb-2">
                                        <small class="text-truncate" style="max-width: 80%;">
                                            <i class="fas fa-file-alt mr-1"></i> Evidencia cargada
                                        </small>
                                        <span class="badge badge-success">Existente</span>
                                    </div>

                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="evidenciaFile"
                                            @change="handleFileUpload" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                        <label class="custom-file-label" for="evidenciaFile" data-browse="Elegir">
                                            {{ fileName || 'Seleccionar archivo...' }}
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Formatos permitidos: PDF, Word, Imágenes. Máx: 10MB.
                                    </small>
                                </div>

                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="font-weight-bold custom-label">Comentario</label>
                                        <small class="text-muted">Caracteres: {{ form.accion_comentario.length }} /
                                            500</small>
                                    </div>
                                    <textarea class="form-control" v-model="form.accion_comentario" rows="4"
                                        :maxlength="500"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="closeModal">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-info" :disabled="saving">
                                    <span v-if="saving" class="spinner-border spinner-border-sm mr-1" role="status"
                                        aria-hidden="true"></span>
                                    Guardar Avance
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted, onUnmounted, computed } from 'vue';
import { useHallazgoStore } from '@/stores/hallazgoStore';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';

const props = defineProps({
    show: Boolean,
    actionData: Object
});

const emit = defineEmits(['close', 'saved']);

const store = useHallazgoStore();
const modalRef = ref(null);
const modalInstance = ref(null);
const saving = ref(false);
const fileName = ref('');
const file = ref(null);
const existingEvidence = ref(null); // To store existing evidence path/name

const form = reactive({
    id: null,
    accion_estado: 'programada',
    accion_fecha_fin_real: '',
    accion_comentario: ''
});

watch(() => props.show, (newVal) => {
    if (newVal) {
        if (props.actionData) {
            form.id = props.actionData.id;
            form.accion_estado = props.actionData.accion_estado;
            form.accion_comentario = props.actionData.accion_comentario || '';
            form.accion_fecha_fin_real = props.actionData.accion_fecha_fin_real
                ? props.actionData.accion_fecha_fin_real.split('T')[0]
                : new Date().toISOString().split('T')[0];
            
            // Check for existing evidence
            // Assuming actionData has a field for evidence path, e.g., 'accion_ruta_evidencia'
            if (props.actionData.accion_ruta_evidencia) {
                 existingEvidence.value = props.actionData.accion_ruta_evidencia;
            } else {
                existingEvidence.value = null;
            }
        }
        fileName.value = '';
        file.value = null;

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

const handleFileUpload = (event) => {
    const selectedFile = event.target.files[0];
    if (selectedFile) {
        if (selectedFile.size > 10 * 1024 * 1024) {
            Swal.fire('Error', 'El archivo es demasiado grande (Máx 10MB)', 'error');
            event.target.value = '';
            return;
        }
        file.value = selectedFile;
        fileName.value = selectedFile.name;
    }
};

const closeModal = () => {
    if (modalInstance.value) {
        modalInstance.value.hide();
    }
    emit('close');
};

const submitForm = async () => {
    // Validation: Require evidence for 'implementada' or 'finalizada'
    if (['implementada', 'finalizada'].includes(form.accion_estado)) {
        if (!file.value && !existingEvidence.value) {
             Swal.fire('Atención', 'Debe adjuntar una evidencia para concluir la acción.', 'warning');
             return;
        }
    }

    saving.value = true;
    try {
        const formData = new FormData();
        formData.append('accion_estado', form.accion_estado);
        formData.append('accion_comentario', form.accion_comentario);

        if (['implementada', 'finalizada'].includes(form.accion_estado)) {
            formData.append('accion_fecha_fin_real', form.accion_fecha_fin_real);
        }

        if (file.value) {
            formData.append('file', file.value);
        }

        await store.saveAccionAvance(form.id, formData);

        Swal.fire('Guardado', 'El avance ha sido registrado.', 'success');
        emit('saved');
        closeModal();
    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'Hubo un problema al guardar el avance.', 'error');
    } finally {
        saving.value = false;
    }
};
</script>

<style scoped>
.custom-label {
    font-size: 0.9em !important;
    font-weight: 600 !important;
    color: #495057 !important;
}
</style>
