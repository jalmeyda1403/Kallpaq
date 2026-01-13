<template>
    <div>
        <div class="header-container mb-4">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">Evaluación Auditores</span>
            </h6>
        </div>

        <div class="card border-0 shadow-none">
            <div class="card-body p-0">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <p class="text-muted small m-0 italic">Consolidado de las evaluaciones realizadas a los miembros del
                        equipo auditor.</p>
                    <button class="btn btn-danger btn-sm" @click="openNewEvaluation">
                        <i class="fas fa-plus"></i> Nueva Evaluación
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="bg-light text-secondary small font-weight-bold">
                            <tr>
                                <th>Evaluado</th>
                                <th>Evaluador</th>
                                <th>Rol Desempeñado</th>
                                <th class="text-center" style="width: 100px;">Calificación</th>
                                <th class="text-center" style="width: 100px;">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <tr v-for="ev in evaluations" :key="ev.id">
                                <td>
                                    <div class="font-weight-bold text-dark">{{ ev.evaluado?.name || 'Desconocido' }}
                                    </div>
                                </td>
                                <td>{{ ev.evaluador?.name || 'Desconocido' }}</td>
                                <td>{{ ev.aev_rol_evaluado }}</td>
                                <td class="text-center">
                                    <span :class="'badge badge-' + getBadgeClass(ev.aev_promedio)">
                                        {{ ev.aev_promedio }}
                                    </span>
                                </td>
                                <td class="text-center">{{ new Date(ev.created_at).toLocaleDateString() }}</td>
                            </tr>
                            <tr v-if="evaluations.length === 0">
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No se han registrado evaluaciones para esta auditoría.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- MANUAL MODAL OVERLAY (Bootstrap Style) -->
        <div v-if="dialogVisible" class="custom-modal-backdrop" @click.self="dialogVisible = false">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-danger text-white py-2">
                        <h5 class="modal-title font-weight-bold" style="font-size: 1rem;">Registrar Evaluación</h5>
                        <button type="button" class="close text-white" @click="dialogVisible = false">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-group mb-0">
                                    <label class="form-label text-danger font-weight-bold p-0 m-0 small">Auditor
                                        Evaluado</label>
                                    <select v-model="form.evaluado_id" class="form-control form-control-sm">
                                        <option :value="null">Seleccione...</option>
                                        <option v-for="m in teamMembers" :key="m.id" :value="m.id">{{ m.label }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-group mb-0">
                                    <label class="form-label text-danger font-weight-bold p-0 m-0 small">Rol
                                        Desempeñado</label>
                                    <select v-model="form.aev_rol_evaluado" class="form-control form-control-sm">
                                        <option value="Auditor Líder">Auditor Líder</option>
                                        <option value="Auditor Interno">Auditor Interno</option>
                                        <option value="Experto Técnico">Experto Técnico</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group mb-0">
                                    <label class="form-label text-danger font-weight-bold p-0 m-0 small">Calificación
                                        (0-20)</label>
                                    <input type="number" v-model="form.aev_promedio"
                                        class="form-control form-control-sm" min="0" max="20" />
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-0">
                                    <label
                                        class="form-label text-danger font-weight-bold p-0 m-0 small">Comentarios/Observaciones</label>
                                    <textarea v-model="form.aev_comentario" rows="3"
                                        class="form-control form-control-sm"
                                        placeholder="Escriba sus observaciones..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center bg-light py-2">
                        <button class="btn btn-danger btn-sm px-4 shadow-sm" @click="saveEvaluation" :disabled="saving">
                            <i v-if="saving" class="fas fa-spinner fa-spin mr-1"></i>
                            <i v-else class="fas fa-save mr-1"></i>
                            Guardar Evaluación
                        </button>
                        <button class="btn btn-secondary btn-sm px-4 ml-2 shadow-sm" @click="dialogVisible = false">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, defineProps, computed } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

const props = defineProps(['auditId']);
const toast = useToast();
const evaluations = ref([]);
const teamMembers = ref([]);
const dialogVisible = ref(false);
const saving = ref(false);

const form = ref({
    evaluado_id: null,
    aev_rol_evaluado: 'Auditor Interno',
    aev_promedio: 20,
    aev_comentario: ''
});

const currentUserId = ref(window.Laravel?.user?.id || 1);

const loadTeamforDropdown = async () => {
    if (!props.auditId) return;
    try {
        const response = await axios.get(`/api/auditorias/${props.auditId}`);
        if (response.data.equipo) {
            teamMembers.value = response.data.equipo.map(m => ({
                id: m.auditor_id,
                label: m.usuario?.name || 'Unknown'
            }));
        }
        if (response.data.evaluaciones) {
            evaluations.value = response.data.evaluaciones;
        }
    } catch (e) { }
};

const openNewEvaluation = () => {
    form.value = {
        evaluado_id: null,
        aev_rol_evaluado: 'Auditor Interno',
        aev_promedio: 20,
        aev_comentario: ''
    };
    dialogVisible.value = true;
};

const saveEvaluation = async () => {
    if (!form.value.evaluado_id) {
        toast.add({ severity: 'warn', summary: 'Incompleto', detail: 'Seleccione un evaluado' });
        return;
    }
    saving.value = true;
    try {
        await axios.post(`/api/auditorias/${props.auditId}/evaluacion`, {
            ...form.value,
            evaluador_id: currentUserId.value,
            aev_criterios: JSON.stringify({})
        });
        toast.add({ severity: 'success', summary: 'Registrado', detail: 'Evaluación guardada' });
        dialogVisible.value = false;
        loadTeamforDropdown();
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo al guardar' });
    } finally {
        saving.value = false;
    }
};

const getBadgeClass = (score) => {
    if (score >= 18) return 'success';
    if (score >= 14) return 'warning';
    return 'danger';
};

onMounted(() => {
    loadTeamforDropdown();
});
</script>

<style scoped>
.italic {
    font-style: italic;
}

.header-container {
    padding: 0.75rem 1rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #ff851b;
    display: flex;
    align-items: center;
}

.custom-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
</style>
