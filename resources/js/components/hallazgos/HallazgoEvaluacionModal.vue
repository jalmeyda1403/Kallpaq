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
                            <select v-model="form.he_resultado" class="form-control" required>
                                <option value="" disabled>Seleccione un resultado</option>
                                <option value="con eficacia">Con Eficacia (Cierra el hallazgo)</option>
                                <option value="sin eficacia">Sin Eficacia (Solicita nuevo plan de acción)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fecha_evaluacion">Fecha de Evaluación <span class="text-danger">*</span></label>
                            <input type="date" v-model="form.he_fecha" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="observaciones">Observaciones / Evidencia <span
                                    class="text-danger">*</span></label>
                            <textarea v-model="form.he_comentario" class="form-control" rows="4" required
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
import { ref, reactive, watch, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { route } from 'ziggy-js';
import { Modal } from 'bootstrap';

const props = defineProps({
    visible: Boolean,
    hallazgo: Object
});

const emit = defineEmits(['cerrar', 'guardado']);

const modalRef = ref(null);
let modalInstance = null;
const loading = ref(false);
const form = reactive({
    he_resultado: '',
    he_fecha: new Date().toISOString().split('T')[0],
    he_comentario: ''
});

const guardarEvaluacion = async () => {
    if (!form.he_resultado || !form.he_comentario || !form.he_fecha) return;

    loading.value = true;
    try {
        await axios.post(route('hallazgo.evaluacion.store', props.hallazgo.id), {
            resultado: form.he_resultado,
            fecha_evaluacion: form.he_fecha,
            observaciones: form.he_comentario
        });

        Swal.fire({
            icon: 'success',
            title: 'Evaluación Guardada',
            text: `El hallazgo ha sido marcado como ${form.he_resultado}.`,
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
    if (modalInstance) modalInstance.hide();
};

const abrirModal = () => {
    if (modalInstance) modalInstance.show();
};

onMounted(() => {
    modalInstance = new Modal(modalRef.value, {
        backdrop: 'static',
        keyboard: false
    });
});

watch(() => props.visible, (newVal) => {
    if (newVal) {
        // Reset form
        form.he_resultado = '';
        form.he_fecha = new Date().toISOString().split('T')[0];
        form.he_comentario = '';
        abrirModal();
    } else {
        cerrarModal();
    }
});
</script>
