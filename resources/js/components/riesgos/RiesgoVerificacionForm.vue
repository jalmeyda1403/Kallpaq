<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div>
                    <!-- Encabezado -->
                    <div class="header-container">
                        <h6 class="mb-0 d-flex align-items-center">
                            <span class="text-dark">{{ formatBreadcrumbId(store.riesgoForm.id) }}</span>
                            <span class="mx-2 text-secondary"><i class="fas fa-chevron-right fa-xs"></i></span>
                            <span class="text-dark">Verificar Eficacia</span>
                        </h6>
                    </div>
                    <div class="text-left mb-4">
                        <h6 class="mb-1" style="font-weight: bold;">VERIFICACIÓN DE EFICACIA</h6>
                        <p class="mb-3 text-muted" style="font-size: 0.875rem;">
                            Registre la revisión de la eficacia de los controles y acciones implementadas.
                        </p>
                    </div>

                    <form @submit.prevent="saveVerificacion">
                        <div class="card-body">
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
                                            <option value="Sin Eficacia">Sin Eficacia</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="form.rr_resultado === 'Sin Eficacia'">
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
                        </div>
                    </form>

                    <!-- Historial de Revisiones -->
                    <div class="mt-4"
                        v-if="store.riesgoActual && store.riesgoActual.revisiones && store.riesgoActual.revisiones.length > 0">
                        <h6 class="mb-3" style="font-weight: bold;">HISTORIAL DE REVISIONES</h6>
                        <div class="table-responsive">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRiesgoStore } from '@/stores/riesgoStore';
import Swal from 'sweetalert2';

const store = useRiesgoStore();

const form = ref({
    rr_fecha: new Date().toISOString().substr(0, 10),
    rr_resultado: '',
    rr_comentario: ''
});

const formatBreadcrumbId = (id) => {
    if (!id) return 'Nuevo Riesgo';
    return `R${id.toString().padStart(6, '0')}`;
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
    return new Date(dateString).toLocaleDateString('es-ES', options);
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
        // Reset form but keep date
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
