<template>
    <div class="cumplimiento-container">

        <div v-if="!readOnly" class="mb-4">
            <p class="text-muted small mb-4">
                Registre el estado de cumplimiento de la obligación.
                <span class="text-danger font-weight-bold">Nota:</span> El registro actualizará automáticamente el
                estado de la obligación.
            </p>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold text-dark">Estado de Cumplimiento</label>
                    <select class="form-control" v-model="localCumplimiento.cumplimiento">
                        <option value="pendiente">Pendiente (Sin evaluar)</option>
                        <option value="cumplida">CUMPLIDA (100%)</option>
                        <option value="parcialmente_cumplida">PARCIALMENTE CUMPLIDA</option>
                        <option value="no_cumplida">NO CUMPLIDA</option>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold text-dark">Fecha de Evaluación</label>
                    <input type="date" class="form-control" v-model="localCumplimiento.fecha_cumplimiento">
                </div>
            </div>

            <!-- Feedback visual del cambio de estado -->
            <div v-if="estadoProyectado" class="alert fade show my-3" :class="alertClass">
                <i class="fas" :class="alertIcon"></i>
                <span class="ml-2 font-weight-bold">{{ alertTitle }}</span>
                <p class="mb-0 small mt-1 pl-4">{{ alertMessage }}</p>
            </div>

            <div class="form-group">
                <label class="font-weight-bold text-dark">Comentarios / Evidencia</label>
                <textarea class="form-control" rows="3" v-model="localCumplimiento.comentario_cumplimiento"
                    placeholder="Describa la evidencia del cumplimiento o las razones del incumplimiento..."></textarea>
            </div>

            <div class="text-right">
                <button class="btn btn-danger shadow-sm" @click="saveCumplimiento" :disabled="saving">
                    <i class="fas fa-save mr-1"></i>
                    {{ saving ? 'Guardando...' : 'Registrar Cumplimiento' }}
                </button>
            </div>
        </div>

        <div v-else class="alert alert-light border">
            <h6 class="alert-heading font-weight-bold">Registro de Cumplimiento</h6>
            <hr>
            <div class="d-flex justify-content-between align-items-center">
                <span>Estado Registrado:</span>
                <span class="badge p-2 text-uppercase" :class="badgeClass">
                    {{ localCumplimiento.cumplimiento?.replace('_', ' ') }}
                </span>
            </div>
            <p class="mt-3 small text-muted font-italic">"{{ localCumplimiento.comentario_cumplimiento }}"</p>
            <small class="text-muted d-block text-right">Fecha: {{ localCumplimiento.fecha_cumplimiento }}</small>
        </div>

    </div>
</template>

<script setup>
import { reactive, computed, ref, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
    obligacion: { type: Object, required: true },
    readOnly: { type: Boolean, default: false }
});

const emit = defineEmits(['saved']);
const loading = ref(false);

const form = reactive({
    cumplimiento: 'pendiente',
    fecha_cumplimiento: new Date().toISOString().substr(0, 10),
    comentario_cumplimiento: ''
});

// Inicializar form con props
watch(() => props.obligacion, (val) => {
    if (val) {
        form.cumplimiento = val.cumplimiento || 'pendiente';
        form.fecha_cumplimiento = val.fecha_cumplimiento || new Date().toISOString().substr(0, 10);
        form.comentario_cumplimiento = val.comentario_cumplimiento || '';
    }
}, { immediate: true });

const puedeEvaluar = computed(() => {
    return props.obligacion?.id && props.obligacion?.evaluacion_actual;
});

const mensajeEstado = computed(() => {
    if (form.cumplimiento === 'cumplida') return "Al guardar, el estado cambiará automáticamente a CONTROLADA.";
    if (form.cumplimiento === 'no_cumplida') return "Al guardar, el estado cambiará automáticamente a NO CONTROLADA.";
    if (form.cumplimiento === 'parcialmente_cumplida') return "La obligación requerirá acciones adicionales.";
    return "";
});

const alertaClase = computed(() => {
    const map = {
        'cumplida': 'alert-success',
        'parcialmente_cumplida': 'alert-warning',
        'no_cumplida': 'alert-danger',
        'pendiente': 'alert-secondary'
    };
    return map[props.obligacion.cumplimiento] || 'alert-light';
});

const guardarCumplimiento = async () => {
    if (form.cumplimiento === 'pendiente') {
        Swal.fire('Atención', 'Seleccione un resultado de cumplimiento válido.', 'warning');
        return;
    }

    loading.value = true;
    try {
        const response = await axios.post(`/api/obligaciones/${props.obligacion.id}/cumplimiento`, form);

        Swal.fire({
            icon: 'success',
            title: 'Cumplimiento Registrado',
            text: `Estado actualizado a: ${response.data.obligacion.obligacion_estado.toUpperCase().replace('_', ' ')}`,
            timer: 2000,
            showConfirmButton: false
        });

        emit('saved', response.data.obligacion);

    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'No se pudo registrar el cumplimiento.', 'error');
    } finally {
        loading.value = false;
    }
};
</script>
