<template>
    <div class="tratamiento-container">

        <div v-if="!criticidad" class="alert alert-warning">
            <i class="fas fa-exclamation-circle mr-2"></i>
            Debe realizar la <strong>Evaluación de Criticidad</strong> antes de definir el tratamiento.
        </div>

        <div v-else>
            <!-- Header del Tipo de Tratamiento -->
            <div class="alert mb-4" :class="esCriticidadAlta ? 'alert-danger' : 'alert-success'">
                <h5 class="alert-heading font-weight-bold mb-1">
                    {{ esCriticidadAlta ? 'TRATAMIENTO REFORZADO (Riesgos)' : 'TRATAMIENTO ESTÁNDAR (Controles)' }}
                </h5>
                <p class="mb-0 small">
                    Segun la criticidad <strong>{{ criticidad.toUpperCase() }}</strong>, corresponde:
                    {{ esCriticidadAlta
                        ? 'Identificar Riesgos, establecer Controles y Planes de Acción.'
                        : 'Establecer Controles Directos y Acciones de Mantenimiento.' }}
                </p>
            </div>

            <!-- SECCIÓN 1: RIESGOS (Solo Alta/Muy Alta) -->
            <div v-if="esCriticidadAlta" class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h6 class="font-weight-bold text-dark mb-0">1. Evaluación de Riesgos</h6>
                    <button class="btn btn-sm btn-outline-danger" @click="$emit('open-riesgos')">
                        <i class="fas fa-plus mr-1"></i> Gestionar Riesgos
                    </button>
                </div>

                <div v-if="riesgos.length === 0" class="text-center py-3 bg-light rounded text-muted small">
                    No se han identificado riesgos. Es obligatorio para este nivel de criticidad.
                </div>
                <div v-else class="list-group">
                    <div v-for="riesgo in riesgos" :key="riesgo.id"
                        class="list-group-item border-left-danger py-2 mb-1 border-0 shadow-sm">
                        <div class="d-flex justify-content-between">
                            <span class="font-weight-bold">{{ riesgo.riesgo_nombre }}</span>
                            <span class="badge badge-light">{{ riesgo.riesgo_valoracion }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 2: CONTROLES -->
            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h6 class="font-weight-bold text-dark mb-0">
                        {{ esCriticidadAlta ? '2. Controles de Mitigación' : '1. Controles Existentes' }}
                    </h6>
                </div>

                <ControlSelector v-model="localControlesIds"
                    :label="esCriticidadAlta ? 'Controles vinculados a Riesgos y Directos' : 'Controles Directos del Proceso'"
                    @update:modelValue="updateControles" />

                <!-- Mostrar controles heredados de riesgos si es alta -->
                <div v-if="esCriticidadAlta && controlesRiesgos.length > 0" class="mt-3">
                    <h6 class="small text-muted font-weight-bold">Controles provenientes de Riesgos:</h6>
                    <ul class="list-unstyled small pl-3 border-left">
                        <li v-for="ctrl in controlesRiesgos" :key="ctrl.id" class="text-secondary mb-1">
                            <i class="fas fa-link mr-1"></i> {{ ctrl.nombre }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- SECCIÓN 3: PLAN DE ACCIÓN -->
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h6 class="font-weight-bold text-dark mb-0">
                        {{ esCriticidadAlta ? '3. Plan de Acción (Para reducir Riesgo Residual)' :
                            '2. Acciones Directas / Mejoras' }}
                    </h6>
                    <button class="btn btn-sm btn-danger shadow-sm" @click="$emit('open-acciones')">
                        <i class="fas fa-plus mr-1"></i> Nueva Acción
                    </button>
                </div>

                <div v-if="acciones.length === 0" class="text-center py-3 bg-light rounded text-muted small">
                    No hay acciones registradas.
                </div>
                <table v-else class="table table-sm table-hover small">
                    <thead class="bg-light">
                        <tr>
                            <th>Acción</th>
                            <th>Responsable</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="accion in acciones" :key="accion.id">
                            <td>{{ accion.accion_descripcion }}</td>
                            <td>{{ accion.responsable?.name }}</td>
                            <td>{{ accion.accion_fecha_fin_planificada }}</td>
                            <td>
                                <span class="badge" :class="getEstadoBadge(accion.accion_estado)">
                                    {{ accion.accion_estado }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import ControlSelector from '../controles/ControlSelector.vue';

const props = defineProps({
    obligacion: Object,
    riesgos: { type: Array, default: () => [] },
    acciones: { type: Array, default: () => [] },
    controlesRiesgos: { type: Array, default: () => [] } // Controles que vienen de los riesgos
});

const emit = defineEmits(['update:controles', 'open-riesgos', 'open-acciones']);

const localControlesIds = ref([]);

const criticidad = computed(() => {
    return props.obligacion?.evaluacion_actual?.oe_nivel_criticidad || null;
});

const esCriticidadAlta = computed(() => {
    return ['alta', 'muy_alta'].includes(criticidad.value);
});

// Sync local state with props
watch(() => props.obligacion?.controles_ids, (newVal) => {
    if (newVal) localControlesIds.value = [...newVal];
}, { immediate: true });

const updateControles = (ids) => {
    emit('update:controles', ids);
};

const getEstadoBadge = (estado) => {
    const map = {
        'abierta': 'badge-warning',
        'en_progreso': 'badge-primary',
        'implementada': 'badge-success',
        'cerrada': 'badge-secondary'
    };
    return map[estado] || 'badge-light';
};
</script>

<style scoped>
.border-left-danger {
    border-left: 3px solid #dc3545;
}
</style>
