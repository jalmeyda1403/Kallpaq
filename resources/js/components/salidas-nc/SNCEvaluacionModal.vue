<template>
    <teleport to="body">
        <div v-if="show" class="modal fade show" role="dialog" style="display: block; overflow-y: auto; z-index: 2050;"
            aria-labelledby="evaluacionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document" style="pointer-events: auto;">
                <div class="modal-content">
                    <div class="modal-header bg-purple text-white">
                        <h5 class="modal-title" id="evaluacionModalLabel">Evaluación de Salida No Conforme</h5>
                        <button type="button" class="close text-white" @click="$emit('close')">
                            <span class="text-white">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="snc && snc.snc_estado && snc.snc_estado.toLowerCase() !== 'tratada'"
                            class="alert alert-warning">
                            <strong>Advertencia:</strong> Esta Salida NC debe estar en estado 'Tratada' para ser
                            validada (estado actual: {{ snc.snc_estado }}).
                        </div>
                        <div v-else>
                            <div class="form-group">
                                <label for="snc_descripcion" class="font-weight-bold custom-label">Descripción de la
                                    SNC</label>
                                <textarea id="snc_descripcion" class="form-control" rows="3"
                                    v-model="snc.snc_descripcion" disabled></textarea>
                            </div>

                            <div class="form-group">
                                <label for="snc_descripcion_tratamiento"
                                    class="font-weight-bold custom-label">Tratamiento
                                    Realizado</label>
                                <textarea id="snc_descripcion_tratamiento" class="form-control" rows="3"
                                    v-model="snc.snc_descripcion_tratamiento" disabled></textarea>
                            </div>

                            <!-- Archivos de evidencia existentes -->
                            <div v-if="existingFiles && existingFiles.length > 0" class="form-group">
                                <label class="font-weight-bold custom-label">Evidencias del Tratamiento:</label>
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
                                        <a :href="`/storage/${file.path}`" target="_blank"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="form-group">
                                <label for="evaluacion_estado" class="font-weight-bold custom-label">Acción a
                                    Tomar</label>
                                <select id="evaluacion_estado" class="form-control" v-model="evaluacion.estado">
                                    <option value="cerrada">Cerrar SNC</option>
                                    <option value="observada">Observar SNC</option>
                                </select>
                            </div>

                            <div class="form-group" v-if="evaluacion.estado === 'observada'">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="evaluacion_observacion"
                                        class="font-weight-bold custom-label">Observación</label>
                                    <small class="text-muted">{{ evaluacion.observacion ?
                                        evaluacion.observacion.length : 0
                                    }}/500</small>
                                </div>
                                <textarea id="evaluacion_observacion" class="form-control" rows="4"
                                    v-model="evaluacion.observacion" maxlength="500" autofocus
                                    placeholder="Ingrese la observación o comentario sobre por qué se observa la SNC..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="$emit('close')">Cancelar</button>
                        <button type="button" class="btn bg-purple text-white" @click="validarSNC"
                            :disabled="!snc || !snc.snc_estado || snc.snc_estado.toLowerCase() !== 'tratada' || (evaluacion.estado === 'observada' && !evaluacion.observacion.trim())">
                            {{ evaluacion.estado === 'cerrada' ? 'Cerrar SNC' : 'Observar SNC' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="show" class="modal-backdrop fade show" style="display: block; z-index: 2040;"></div>
    </teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import Swal from 'sweetalert2';
import { useSalidasNCStore } from '@/stores/salidasNCStore';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    sncId: {
        type: [Number, String],
        required: false
    },
    sncData: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close', 'validated']);

const salidasNCStore = useSalidasNCStore();
const snc = ref(null);
const existingFiles = ref([]);
const evaluacion = ref({
    estado: 'cerrada', // Default to 'cerrada'
    observacion: ''
});

// Load the snc 
watch(() => props.sncId, async (newId) => {
    if (newId) {
        try {
            // Optimization: If sncData is provided, use it directly
            if (props.sncData) {
                processSNCData(props.sncData);
            } else {
                // Otherwise fetch it
                await salidasNCStore.fetchSNCById(newId);
                processSNCData(salidasNCStore.getCurrentSNC);
            }
        } catch (error) {
            console.error('Error loading SNC:', error);
        }
    } else {
        snc.value = null;
    }
}, { immediate: true });

// Helper function to process data regardless of source
const processSNCData = (data) => {
    snc.value = data;

    // Cargar archivos existentes (Evidencias)
    existingFiles.value = [];
    if (data.snc_evidencias) {
        // El backend ya devuelve un array (gracias al cast 'array' del modelo)
        if (Array.isArray(data.snc_evidencias)) {
            existingFiles.value = data.snc_evidencias.map(item => {
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
        }
    }

    // Reset evaluation form
    evaluacion.value.estado = 'cerrada';
    evaluacion.value.observacion = '';
};

// Watch for changes in show prop to reset form when modal opens
watch(() => props.show, (newShow) => {
    if (newShow) {
        // Reset evaluation form when modal opens
        evaluacion.value.estado = 'cerrada';
        evaluacion.value.observacion = '';
    }
});

const validarSNC = async () => {
    if (!snc.value) return;

    if (!snc.value.snc_estado || snc.value.snc_estado.toLowerCase() !== 'tratada') {
        Swal.fire({
            icon: 'warning',
            title: 'Advertencia',
            text: 'Esta Salida NC no está en estado tratada y no puede ser evaluada (Actual: ' + snc.value.snc_estado + ')'
        });
        return;
    }

    if (evaluacion.value.estado === 'observada' && !evaluacion.value.observacion.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Campo requerido',
            text: 'Por favor ingrese una observación.'
        });
        return;
    }

    try {
        const updateData = {
            snc_estado: evaluacion.value.estado
        };

        if (evaluacion.value.estado === 'observada') {
            updateData.snc_observacion = evaluacion.value.observacion;
        }

        await salidasNCStore.updateSNC(snc.value.id, updateData);

        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: 'Evaluación registrada exitosamente',
            timer: 2000,
            showConfirmButton: false
        });

        emit('validated');
        emit('close');
    } catch (error) {
        console.error('Error validating SNC:', error);

        // Mejora el mensaje de error para mostrar lo que devuelve el backend si existe
        const errorMsg = error.response?.data?.message || error.message || 'Error desconocido';

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al evaluar la SNC: ' + errorMsg
        });
    }
}
</script>

<style scoped>
.custom-label {
    font-size: 0.9em !important;
    font-weight: 600 !important;
    color: #495057 !important;
}

.bg-purple {
    background-color: #605ca8 !important;
}

.list-group-item {
    transition: all 0.15s ease-in-out;
}

.list-group-item:hover {
    background-color: #f8f9fa;
    transform: translateY(-1px);
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
</style>
