<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div>
                    <!-- Encabezado -->
                    <div class="header-container">
                        <h6 class="mb-0 d-flex align-items-center justify-content-between w-100">
                            <div class="d-flex align-items-center">
                                <span class="text-dark">{{ formatBreadcrumbId(store.riesgoForm.id) }}</span>
                                <span class="mx-2 text-secondary"><i class="fas fa-chevron-right fa-xs"></i></span>
                                <span class="text-dark">Verificar Eficacia</span>
                            </div>
                            <span class="badge badge-info">Ciclo: {{ store.riesgoActual?.riesgo_ciclo || 1 }}</span>
                        </h6>
                    </div>
                    <div class="text-left mb-4">
                        <h6 class="mb-1" style="font-weight: bold;">VERIFICACIÓN DE EFICACIA</h6>
                        <p class="mb-3 text-muted" style="font-size: 0.875rem;">
                            Registre la revisión de la eficacia de los controles y acciones implementadas.
                        </p>
                    </div>

                    <!-- Comparativa de Riesgo -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="font-weight-bold mb-3 text-secondary">Comparativa de Riesgo</h6>
                            <div class="row">
                                <!-- Riesgo Inicial (Inherente) -->
                                <div class="col-md-6 border-right">
                                    <h6 class="text-center mb-3 text-muted">Riesgo Inicial (Inherente)</h6>
                                    <div class="row text-center">
                                        <div class="col-3">
                                            <small class="d-block text-muted">Probabilidad</small>
                                            <span class="font-weight-bold">{{ store.riesgoActual?.riesgo_probabilidad ||
                                                '-' }}</span>
                                        </div>
                                        <div class="col-3">
                                            <small class="d-block text-muted">Impacto</small>
                                            <span class="font-weight-bold">{{ store.riesgoActual?.riesgo_impacto || '-'
                                                }}</span>
                                        </div>
                                        <div class="col-3">
                                            <small class="d-block text-muted">Valor</small>
                                            <span class="font-weight-bold">{{ store.riesgoActual?.riesgo_valor || '-'
                                                }}</span>
                                        </div>
                                        <div class="col-3">
                                            <small class="d-block text-muted">Nivel</small>
                                            <span :class="getNivelBadgeClass(store.riesgoActual?.riesgo_nivel)">
                                                {{ store.riesgoActual?.riesgo_nivel || '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Riesgo Residual (Calculado) -->
                                <div class="col-md-6">
                                    <h6 class="text-center mb-3 text-primary">Riesgo Residual (Proyectado)</h6>
                                    <div class="row text-center">
                                        <div class="col-3">
                                            <small class="d-block text-muted">Probabilidad</small>
                                            <span class="font-weight-bold text-primary">{{ form.probabilidad_rr || '-'
                                                }}</span>
                                        </div>
                                        <div class="col-3">
                                            <small class="d-block text-muted">Impacto</small>
                                            <span class="font-weight-bold text-primary">{{ form.impacto_rr || '-'
                                                }}</span>
                                        </div>
                                        <div class="col-3">
                                            <small class="d-block text-muted">Valor</small>
                                            <span class="font-weight-bold">{{ calculatedValorRR || '-' }}</span>
                                        </div>
                                        <div class="col-3">
                                            <small class="d-block text-muted">Nivel</small>
                                            <span :class="getNivelBadgeClass(calculatedNivelRR)">
                                                {{ calculatedNivelRR || '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="saveVerificacion">
                        <div class="card-body">
                            <!-- Nueva Evaluación Residual -->
                            <h6 class="font-weight-bold mb-3 mt-2">Nueva Evaluación Residual</h6>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Probabilidad Residual <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" v-model.number="form.probabilidad_rr" required>
                                            <option value="" disabled>Seleccione...</option>
                                            <option value="4">4 - Bajo</option>
                                            <option value="6">6 - Medio</option>
                                            <option value="8">8 - Alto</option>
                                            <option value="10">10 - Muy Alto</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Impacto Residual <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" v-model.number="form.impacto_rr" required>
                                            <option value="" disabled>Seleccione...</option>
                                            <option value="4">4 - Bajo</option>
                                            <option value="6">6 - Medio</option>
                                            <option value="8">8 - Alto</option>
                                            <option value="10">10 - Muy Alto</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Datos de Verificación -->
                            <h6 class="font-weight-bold mb-3 mt-2">Datos de Verificación</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Fecha de Revisión <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" v-model="form.rr_fecha" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Resultado <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" v-model="form.rr_resultado" required>
                                            <option value="">Seleccione...</option>
                                            <option value="Con Eficacia">Con Eficacia</option>
                                            <option value="Sin eficacia">Sin eficacia</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="form.rr_resultado === 'Sin eficacia'">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Comentario / Observación <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control" rows="3" v-model="form.rr_comentario" required
                                            placeholder="Indique por qué no fue eficaz y qué medidas se tomarán..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-else>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Comentario (Opcional)</label>
                                        <textarea class="form-control" rows="3" v-model="form.rr_comentario"
                                            placeholder="Comentarios adicionales..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer justify-content-center w-100">
                                <button type="submit" class="btn btn-danger" :disabled="store.loading">
                                    <span v-if="store.loading" class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                    Guardar Verificación
                                </button>
                            </div>

                            <hr>
                            <table class="table table-bordered table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Responsable</th>
                                        <th>Ciclo</th>
                                        <th>Resultado</th>
                                        <th>Comentario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="revision in store.riesgoActual.revisiones" :key="revision.id">
                                        <td>{{ formatDate(revision.rr_fecha) }}</td>
                                        <td>{{ revision.responsable ? revision.responsable.name : 'N/A' }}</td>
                                        <td class="text-center">{{ revision.rr_ciclo }}</td>
                                        <td>
                                            <span
                                                :class="revision.rr_resultado === 'Con Eficacia' ? 'badge badge-success' : 'badge badge-danger'">
                                                {{ revision.rr_resultado }}
                                            </span>
                                        </td>
                                        <td>{{ revision.rr_comentario || '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRiesgoStore } from '@/stores/riesgoStore';
import Swal from 'sweetalert2';

const store = useRiesgoStore();

const form = ref({
    rr_fecha: new Date().toISOString().substr(0, 10),
    rr_resultado: '',
    rr_comentario: '',
    probabilidad_rr: null,
    impacto_rr: null
});

// Watch for changes in riesgoActual to initialize form with current RR values if they exist
watch(() => store.riesgoActual, (newVal) => {
    if (newVal) {
        form.value.probabilidad_rr = newVal.riesgo_probabilidad_rr || null;
        form.value.impacto_rr = newVal.riesgo_impacto_rr || null;
    }
}, { immediate: true });

const formatBreadcrumbId = (id) => {
    if (!id) return 'Nuevo Riesgo';
    return `R${id.toString().padStart(6, '0')}`;
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
    return new Date(dateString).toLocaleDateString('es-ES', options);
};

// Computed properties for Residual Risk Calculation
const calculatedValorRR = computed(() => {
    if (form.value.probabilidad_rr && form.value.impacto_rr) {
        return form.value.probabilidad_rr * form.value.impacto_rr;
    }
    return null;
});

const calculatedNivelRR = computed(() => {
    const valor = calculatedValorRR.value;
    if (valor === null) return null;

    if (valor >= 80) return 'Muy Alto';
    if (valor >= 48) return 'Alto';
    if (valor >= 32) return 'Medio';
    return 'Bajo';
});

const getNivelBadgeClass = (nivel) => {
    if (!nivel) return 'badge badge-secondary';
    switch (nivel) {
        case 'Muy Alto': return 'badge badge-danger';
        case 'Alto': return 'badge badge-warning text-dark';
        case 'Medio': return 'badge badge-info';
        case 'Bajo': return 'badge badge-success';
        default: return 'badge badge-secondary';
    }
};

const saveVerificacion = async () => {
    try {
        await store.saveVerificacion(form.value);
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: 'Verificación guardada correctamente',
            timer: 1500,
            showConfirmButton: false
        });
        // Reset form specific fields but keep date and RR values
        form.value.rr_resultado = '';
        form.value.rr_comentario = '';
    } catch (error) {
        console.error(error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al guardar la verificación.',
        });
    }
};
</script>

<style scoped>
.header-container {
    padding: 0.75rem;
    margin-bottom: 1.5rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #dc3545;
    display: flex;
    align-items: center;
}

.badge {
    font-size: 0.85em;
    padding: 0.35em 0.6em;
}
</style>
