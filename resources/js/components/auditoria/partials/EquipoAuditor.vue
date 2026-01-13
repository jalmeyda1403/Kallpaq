<template>
    <div>
        <div class="header-container mb-4 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <span class="text-dark">Equipo Auditor y Recursos</span>
            </h6>
            <button class="btn btn-sm btn-outline-secondary" @click="loadData" title="Recargar Datos">
                <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }"></i> Refrescar
            </button>
        </div>

        <div class="card border-0 shadow-none">
            <div class="card-body p-0">
                <div class="d-flex align-items-center mb-4">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0"><i
                                    class="fas fa-user-plus"></i></span>
                        </div>
                        <select v-model="newMember.auditor_id" class="form-control border-left-0">
                            <option :value="null">Seleccione un Auditor...</option>
                            <option v-for="aud in auditorsList" :key="aud.id" :value="aud.user_id">
                                {{ aud.user?.name || 'Desconocido' }}
                            </option>
                        </select>
                        <select v-model="newMember.aeq_rol" class="form-control mx-2">
                            <option v-for="rol in roles" :key="rol" :value="rol">{{ rol }}</option>
                        </select>
                        <div class="input-group-append">
                            <button @click="addMember" class="btn btn-danger" :disabled="!newMember.auditor_id">
                                <i class="fas fa-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-overlay-container">
                    <div v-if="loading" class="loading-overlay">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light text-secondary small font-weight-bold">
                                <tr>
                                    <th>Nombre del Auditor</th>
                                    <th>Rol</th>
                                    <th class="text-center" style="width: 120px;">Horas Plan.</th>
                                    <th class="text-center" style="width: 120px;">Horas Ejec.</th>
                                    <th class="text-center" style="width: 80px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <tr v-for="(member, index) in team" :key="index">
                                    <td>
                                        <div class="font-weight-bold">{{ getAuditorName(member.auditor_id) }}</div>
                                    </td>
                                    <td>
                                        <select v-model="member.aeq_rol"
                                            class="form-control form-control-sm border-0 bg-light">
                                            <option v-for="rol in roles" :key="rol" :value="rol">{{ rol }}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.5" v-model="member.aeq_horas_planificadas"
                                            class="form-control form-control-sm text-center border-0 bg-light" />
                                    </td>
                                    <td>
                                        <input type="number" step="0.5" v-model="member.aeq_horas_ejecutadas"
                                            class="form-control form-control-sm text-center border-0 bg-white font-weight-bold show-readonly"
                                            readonly title="Calculado automáticamente desde la Agenda" />
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-link text-danger p-0" @click="removeMember(index)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="team.length === 0">
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No hay miembros asignados al equipo auditor.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer justify-content-center bg-light border-top mt-5 p-3"
                    style="margin: 0 -1.5rem -1rem -1.5rem; border-bottom-right-radius: 0.3rem;">
                    <button class="btn btn-danger btn-sm px-5 shadow-sm" @click="save" :disabled="saving">
                        <i v-if="saving" class="fas fa-spinner fa-spin mr-1"></i>
                        <i v-else class="fas fa-save mr-1"></i>
                        Guardar Equipo
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm ml-2 px-4 shadow-sm" @click="$emit('close')">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, defineProps, defineEmits } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

const props = defineProps(['auditId']);
const emit = defineEmits(['saved']);
const toast = useToast();
const loading = ref(false);
const saving = ref(false);

const team = ref([]);
const auditorsList = ref([]);
const roles = ['Auditor Líder', 'Auditor', 'Especialista', 'Observador'];
const newMember = ref({ auditor_id: null, aeq_rol: 'Auditor', aeq_horas_planificadas: 0, aeq_horas_ejecutadas: 0 });

const loadData = async () => {
    loading.value = true;
    try {
        const [resAuditors, resAudit] = await Promise.all([
            axios.get('/api/auditores'),
            axios.get(`/api/auditorias/${props.auditId}`)
        ]);
        auditorsList.value = resAuditors.data;
        if (resAudit.data.equipo) {
            team.value = resAudit.data.equipo;
        }
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo cargando equipo' });
    } finally {
        loading.value = false;
    }
};

const getAuditorName = (userId) => {
    const aud = auditorsList.value.find(a => a.user_id === userId);
    return aud?.user?.name || 'Desconocido';
};

const addMember = () => {
    if (!newMember.value.auditor_id) return;

    // Check if already in team
    if (team.value.some(m => m.auditor_id === newMember.value.auditor_id)) {
        toast.add({ severity: 'warn', summary: 'Duplicado', detail: 'El auditor ya está en el equipo' });
        return;
    }

    team.value.push({ ...newMember.value });
    newMember.value = { auditor_id: null, aeq_rol: 'Auditor Interno', aeq_horas_planificadas: 0, aeq_horas_ejecutadas: 0 };
};

const removeMember = (index) => {
    team.value.splice(index, 1);
};

const save = async () => {
    saving.value = true;
    try {
        await axios.put(`/api/auditorias/${props.auditId}/equipo`, { equipo: team.value });
        toast.add({ severity: 'success', summary: 'Guardado', detail: 'Equipo actualizado' });
        emit('saved');
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo guardando' });
    } finally {
        saving.value = false;
    }
};

onMounted(() => {
    loadData();
});
</script>

<style scoped>
.form-overlay-container {
    position: relative;
    min-height: 150px;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 5;
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
</style>
