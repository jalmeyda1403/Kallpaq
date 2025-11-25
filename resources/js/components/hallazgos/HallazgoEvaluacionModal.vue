<template>
    <div class="modal fade" id="evaluacionEficaciaModal" tabindex="-1" role="dialog"
        aria-labelledby="evaluacionEficaciaModalLabel" aria-hidden="true" ref="modalRef">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="evaluacionEficaciaModalLabel">Verificación de Eficacia</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
                        @click="cerrarModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Hallazgo:</strong> {{ hallazgo?.hallazgo_cod }} - {{ hallazgo?.hallazgo_resumen }}
                    </div>
                    <form @submit.prevent="guardarEvaluacion">
                        <div class="form-group">
                            <label for="resultado">Resultado de la Eficacia <span class="text-danger">*</span></label>
                            <select v-model="form.resultado" class="form-control" required>
                                <option value="" disabled>Seleccione un resultado</option>
                                <option value="con eficacia">Con Eficacia (Cierra el hallazgo)</option>
                                <option value="sin eficacia">Sin Eficacia (Solicita nuevo plan de acción)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fecha_evaluacion">Fecha de Evaluación <span class="text-danger">*</span></label>
                            <input type="date" v-model="form.fecha_evaluacion" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="observaciones">Observaciones / Evidencia <span
                                    class="text-danger">*</span></label>
                            <textarea v-model="form.observaciones" class="form-control" rows="4" required
                                placeholder="Describa los detalles de la verificación y la evidencia revisada..."></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                @click="cerrarModal">Cancelar</button>
                            <button type="submit" class="btn btn-danger" :disabled="loading">
                                <span v-if="loading" class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                                Guardar Verificación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { route } from 'ziggy-js';

const props = defineProps({
    visible: Boolean,
    hallazgo: Object
});

const emit = defineEmits(['cerrar', 'guardado']);

const modalRef = ref(null);
const loading = ref(false);
const form = reactive({
    resultado: '',
    fecha_evaluacion: new Date().toISOString().split('T')[0],
    observaciones: ''
});

const guardarEvaluacion = async () => {
    if (!form.resultado || !form.observaciones || !form.fecha_evaluacion) return;

    loading.value = true;
    try {
        await axios.post(route('hallazgo.evaluacion.store', props.hallazgo.id), form);

        Swal.fire({
            icon: 'success',
            title: 'Evaluación Guardada',
            text: `El hallazgo ha sido marcado como ${form.resultado}.`,
            timer: 2000,
            showConfirmButton: false
        });

        emit('guardado');
        cerrarModal();
    } catch (error) {
        console.error('Error al guardar evaluación:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo guardar la evaluación.'
        });
    } finally {
        loading.value = false;
    }
};

const cerrarModal = () => {
    emit('cerrar');
    $(modalRef.value).modal('hide');
};

const abrirModal = () => {
    $(modalRef.value).modal('show');
};

watch(() => props.visible, (newVal) => {
    if (newVal) {
        // Reset form
        form.resultado = '';
        form.fecha_evaluacion = new Date().toISOString().split('T')[0];
        form.observaciones = '';
        abrirModal();
    } else {
        cerrarModal();
    }
});
</script>
