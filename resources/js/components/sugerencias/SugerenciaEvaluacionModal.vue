<template>
    <teleport to="body">
        <div v-if="show" class="modal fade show" tabindex="-1" role="dialog" style="display: block; overflow-y: auto;"
            aria-labelledby="evaluacionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="evaluacionModalLabel">Evaluación de Sugerencia</h5>
                        <button type="button" class="close text-white" @click="$emit('close')">
                            <span class="text-white">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="sugerencia.sugerencia_estado !== 'concluida'" class="alert alert-warning">
                            <strong>Advertencia:</strong> Esta sugerencia no está en estado 'concluida' y no puede
                            ser validada.
                        </div>
                        <div v-else>
                            <div class="form-group">
                                <label for="sugerencia_detalle" class="font-weight-bold custom-label">Detalle de la
                                    Sugerencia</label>
                                <textarea id="sugerencia_detalle" class="form-control" rows="3"
                                    v-model="sugerencia.sugerencia_detalle" disabled></textarea>
                            </div>

                            <div class="form-group">
                                <label for="sugerencia_tratamiento" class="font-weight-bold custom-label">Tratamiento
                                    Realizado</label>
                                <textarea id="sugerencia_tratamiento" class="form-control" rows="3"
                                    v-model="sugerencia.sugerencia_tratamiento" disabled></textarea>
                            </div>

                            <!-- Archivos de evidencia existentes -->
                            <div v-if="existingFiles && existingFiles.length > 0" class="form-group">
                                <label class="font-weight-bold custom-label">Evidencias del Tratamiento:</label>
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
                                        <a :href="`/storage/${file.path}`" target="_blank"
                                            class="btn btn-primary btn-sm">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="form-group">
                                <label for="evaluacion_estado" class="font-weight-bold custom-label">Acción a
                                    Tomar</label>
                                <select id="evaluacion_estado" class="form-control" v-model="evaluacion.estado">
                                    <option value="cerrada">Cerrar Sugerencia</option>
                                    <option value="observada">Observar Sugerencia</option>
                                </select>
                            </div>

                            <div class="form-group" v-if="evaluacion.estado === 'observada'">
                                <label for="evaluacion_observacion"
                                    class="font-weight-bold custom-label">Observación</label>
                                <textarea id="evaluacion_observacion" class="form-control" rows="3"
                                    v-model="evaluacion.observacion"
                                    placeholder="Ingrese la observación o comentario sobre por qué se observa la sugerencia..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="$emit('close')">Cancelar</button>
                        <button type="button" class="btn btn-danger" @click="validarSugerencia"
                            :disabled="!sugerencia || sugerencia.sugerencia_estado !== 'concluida' || (evaluacion.estado === 'observada' && !evaluacion.observacion.trim())">
                            {{ evaluacion.estado === 'cerrada' ? 'Cerrar Sugerencia' : 'Observar Sugerencia' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="show" class="modal-backdrop fade show" style="display: block;"></div>
    </teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useSugerenciasStore } from '@/stores/sugerenciasStore';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    sugerenciaId: {
        type: [Number, String],
        required: false
    }
});

const emit = defineEmits(['close', 'validated']);

const sugerenciasStore = useSugerenciasStore();
const sugerencia = ref(null);
const existingFiles = ref([]);
const evaluacion = ref({
    estado: 'cerrada', // Default to 'cerrada'
    observacion: ''
});

// Load the sugerencia whenever the sugerenciaId changes
watch(() => props.sugerenciaId, async (newId) => {
    if (newId) {
        try {
            const data = await sugerenciasStore.fetchSugerenciaById(newId);
            sugerencia.value = data;

            // Cargar archivos existentes
            existingFiles.value = [];
            if (data.sugerencia_evidencias) {
                // El backend ya devuelve un array (gracias al cast 'array' del modelo)
                if (Array.isArray(data.sugerencia_evidencias)) {
                    existingFiles.value = data.sugerencia_evidencias.map(item => {
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
                } else if (typeof data.sugerencia_evidencias === 'string') {
                    // Fallback: si por alguna razón viene como string
                    existingFiles.value = [{
                        path: data.sugerencia_evidencias,
                        name: data.sugerencia_evidencias.split('/').pop()
                    }];
                }
            }

            console.log('Archivos existentes cargados (Evaluación):', existingFiles.value);

            // Reset evaluation form
            evaluacion.value.estado = 'cerrada';
            evaluacion.value.observacion = '';
        } catch (error) {
            console.error('Error loading sugerencia:', error);
        }
    } else {
        sugerencia.value = null;
    }
}, { immediate: true });

// Watch for changes in show prop to reset form when modal opens
watch(() => props.show, (newShow) => {
    if (newShow) {
        // Reset evaluation form when modal opens
        evaluacion.value.estado = 'cerrada';
        evaluacion.value.observacion = '';
    } else {
        // Clear data when modal closes
        sugerencia.value = null;
        existingFiles.value = [];
    }
});

const validarSugerencia = async () => {
    if (!sugerencia.value) return;

    if (sugerencia.value.sugerencia_estado !== 'concluida') {
        alert('Esta sugerencia no está en estado concluida y no puede ser validada.');
        return;
    }

    if (evaluacion.value.estado === 'observada' && !evaluacion.value.observacion.trim()) {
        alert('Por favor ingrese una observación.');
        return;
    }

    try {
        // Prepare the update data
        const updateData = {
            sugerencia_estado: evaluacion.value.estado
        };

        // Add observation if the estado is 'observado'
        if (evaluacion.value.estado === 'observada') {
            updateData.sugerencia_observacion = evaluacion.value.observacion;
            updateData.sugerencia_fecha_observacion = new Date().toISOString().split('T')[0];
        } else if (evaluacion.value.estado === 'cerrada') {
            updateData.sugerencia_fecha_cierre = new Date().toISOString().split('T')[0];
        }

        // Validate the sugerencia
        await sugerenciasStore.validateSugerencia(sugerencia.value.id, updateData);

        // Emit the validated event
        emit('validated', {
            id: sugerencia.value.id,
            estado: evaluacion.value.estado,
            observacion: evaluacion.value.observacion
        });

        // Close the modal
        emit('close');
    } catch (error) {
        console.error('Error validating suggestion:', error);

        // Handle specific error messages
        if (error.response && error.response.status === 400) {
            alert('Error: ' + (error.response.data.message || 'La sugerencia no puede ser validada'));
        } else {
            alert('Error al validar la sugerencia: ' + error.message);
        }
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

/* Smaller font size for labels of file lists */
.file-list-label {
    font-size: 0.85em !important;
    font-weight: 600 !important;
    color: #6c757d !important;
    margin-bottom: 0.5rem !important;
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
.alert-warning {
    color: #856404;
    background-color: #fff3cd;
    border-color: #ffeaa7;
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
