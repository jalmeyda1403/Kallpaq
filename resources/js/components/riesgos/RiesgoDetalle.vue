<template>
    <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
    </div>
    <div v-else-if="riesgo" class="card shadow-sm">
        <div class="card-header bg-white border-bottom-0">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0 text-primary">
                    <i class="fas fa-exclamation-triangle mr-2"></i>{{ riesgo.riesgo_cod }} - {{ riesgo.nombre }}
                </h4>
                <span :class="['badge', getBadgeClass(riesgo.riesgo_valoracion)]">{{ riesgo.riesgo_valoracion }}</span>
            </div>
            <p class="text-muted mt-2 mb-0">{{ riesgo.descripcion }}</p>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs mb-4" id="riesgoTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="evaluacion-tab" data-toggle="tab" href="#evaluacion" role="tab">
                        <i class="fas fa-chart-bar mr-2"></i>Evaluación
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tratamiento-tab" data-toggle="tab" href="#tratamiento" role="tab">
                        <i class="fas fa-medkit mr-2"></i>Plan de Tratamiento
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="verificacion-tab" data-toggle="tab" href="#verificacion" role="tab">
                        <i class="fas fa-check-double mr-2"></i>Verificación de Eficacia
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="riesgoTabsContent">
                <!-- Tab Evaluación -->
                <div class="tab-pane fade show active" id="evaluacion" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Probabilidad (1-10)</label>
                                <input type="number" class="form-control" v-model.number="formEvaluacion.probabilidad" min="1" max="10">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Impacto (1-10)</label>
                                <input type="number" class="form-control" v-model.number="formEvaluacion.impacto" min="1" max="10">
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <strong>Nivel de Riesgo Calculado:</strong> {{ formEvaluacion.probabilidad * formEvaluacion.impacto }}
                    </div>
                    <button class="btn btn-primary" @click="guardarEvaluacion" :disabled="saving">
                        <i class="fas fa-save mr-1"></i> Guardar Evaluación
                    </button>
                </div>

                <!-- Tab Tratamiento -->
                <div class="tab-pane fade" id="tratamiento" role="tabpanel">
                    <div class="form-group">
                        <label>Estrategia de Tratamiento</label>
                        <select class="form-control" v-model="formTratamiento.riesgo_tratamiento">
                            <option value="Reducir">Reducir</option>
                            <option value="Aceptar">Aceptar</option>
                            <option value="Trasladar">Trasladar</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Controles / Acciones</label>
                        <textarea class="form-control" rows="4" v-model="formTratamiento.controles"></textarea>
                    </div>
                    <button class="btn btn-primary" @click="guardarTratamiento" :disabled="saving">
                        <i class="fas fa-save mr-1"></i> Guardar Plan
                    </button>
                </div>

                <!-- Tab Verificación -->
                <div class="tab-pane fade" id="verificacion" role="tabpanel">
                    <h5 class="mb-3">Evaluación de Riesgo Residual</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nueva Probabilidad</label>
                                <input type="number" class="form-control" v-model.number="formVerificacion.probabilidad_rr" min="1" max="10">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nuevo Impacto</label>
                                <input type="number" class="form-control" v-model.number="formVerificacion.impacto_rr" min="1" max="10">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fecha Valoración</label>
                                <input type="date" class="form-control" v-model="formVerificacion.fecha_valoracion_rr">
                            </div>
                        </div>
                    </div>
                    <div class="alert" :class="eficaciaClass">
                        <strong>Resultado:</strong> {{ eficaciaLabel }} ({{ formVerificacion.probabilidad_rr * formVerificacion.impacto_rr }})
                    </div>
                    <button class="btn btn-primary" @click="guardarVerificacion" :disabled="saving">
                        <i class="fas fa-save mr-1"></i> Guardar Verificación
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRiesgoStore } from '../../stores/riesgoStore';
import Swal from 'sweetalert2';

const props = defineProps({
    riesgoId: {
        type: [Number, String],
        required: true
    }
});

const store = useRiesgoStore();
const riesgo = computed(() => store.riesgoActual);
const loading = computed(() => store.loading);
const saving = ref(false);

const formEvaluacion = ref({ probabilidad: 0, impacto: 0 });
const formTratamiento = ref({ riesgo_tratamiento: '', controles: '' });
const formVerificacion = ref({ probabilidad_rr: 0, impacto_rr: 0, fecha_valoracion_rr: '' });

const loadData = async () => {
    await store.fetchRiesgoCompleto(props.riesgoId);
    if (riesgo.value) {
        formEvaluacion.value = {
            probabilidad: riesgo.value.probabilidad,
            impacto: riesgo.value.impacto
        };
        formTratamiento.value = {
            riesgo_tratamiento: riesgo.value.tratamiento_riesgo || 'Reducir',
            controles: riesgo.value.controles || '' // Asumiendo que 'controles' es el campo correcto, verificar modelo
        };
        formVerificacion.value = {
            probabilidad_rr: riesgo.value.probabilidad_rr || 0,
            impacto_rr: riesgo.value.impacto_rr || 0,
            fecha_valoracion_rr: riesgo.value.fecha_valoracion_rr || new Date().toISOString().split('T')[0]
        };
    }
};

onMounted(loadData);

// Watch para actualizar formularios si cambia el riesgo (ej. navegación)
watch(() => props.riesgoId, loadData);

const getBadgeClass = (valoracion) => {
    if (valoracion === 'Muy Alto') return 'badge-danger';
    if (valoracion === 'Alto') return 'badge-warning';
    if (valoracion === 'Medio') return 'badge-info';
    return 'badge-success';
};

const eficaciaLabel = computed(() => {
    const valor = formVerificacion.value.probabilidad_rr * formVerificacion.value.impacto_rr;
    const valorInicial = formEvaluacion.value.probabilidad * formEvaluacion.value.impacto;
    return valor < valorInicial ? 'Con Eficacia' : 'Sin Eficacia';
});

const eficaciaClass = computed(() => {
    return eficaciaLabel.value === 'Con Eficacia' ? 'alert-success' : 'alert-warning';
});

const guardarEvaluacion = async () => {
    saving.value = true;
    try {
        await store.updateEvaluacion(props.riesgoId, formEvaluacion.value);
        Swal.fire('Éxito', 'Evaluación actualizada correctamente', 'success');
    } catch (e) {
        Swal.fire('Error', 'No se pudo guardar la evaluación', 'error');
    } finally {
        saving.value = false;
    }
};

const guardarTratamiento = async () => {
    saving.value = true;
    try {
        await store.updateTratamiento(props.riesgoId, formTratamiento.value);
        Swal.fire('Éxito', 'Plan de tratamiento actualizado', 'success');
    } catch (e) {
        Swal.fire('Error', 'No se pudo guardar el plan', 'error');
    } finally {
        saving.value = false;
    }
};

const guardarVerificacion = async () => {
    saving.value = true;
    try {
        await store.updateVerificacion(props.riesgoId, formVerificacion.value);
        Swal.fire('Éxito', 'Verificación registrada', 'success');
    } catch (e) {
        Swal.fire('Error', 'No se pudo registrar la verificación', 'error');
    } finally {
        saving.value = false;
    }
};
</script>
