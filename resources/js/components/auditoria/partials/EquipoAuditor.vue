<template>
    <div>
        <div class="header-container mb-4">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">Equipo Auditor y Recursos</span>
            </h6>
        </div>

        <div class="card border-0 shadow-none">
            <div class="card-body p-0">
                <p class="text-muted small mb-4 italic">
                    Seleccione un auditor de la lista, asigne un rol y agréguelo al equipo.
                </p>

                <div class="d-flex align-items-center mb-4">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0"><i
                                    class="fas fa-user-plus"></i></span>
                        </div>
                        <select v-model="newMember.auditor_id" class="form-control border-left-0">
                            <option :value="null">Seleccione un Auditor...</option>
                            <option v-for="aud in availableAuditors" :key="aud.id" :value="aud.id">
                                {{ aud.user?.name || 'Desconocido' }}
                            </option>
                        </select>
                        <select v-model="newMember.aeq_rol" class="form-control mx-2">
                            <option v-for="rol in roles" :key="rol" :value="rol">{{ rol }}</option>
                        </select>
                        <div class="input-group-append">
                            <button @click="addMember" class="btn btn-danger" :disabled="!newMember.auditor_id">
                                <i class="fas fa-plus mr-1"></i> Agregar
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
                        <table class="table table-sm table-bordered table-hover">
                            <thead class="bg-light text-secondary small font-weight-bold">
                                <tr>
                                    <th class="pl-3">Nombre del Auditor</th>
                                    <th>Rol en el Equipo</th>
                                    <th class="text-center">H. Planificadas</th>
                                    <th class="text-center">H. Ejecutadas</th>
                                    <th class="text-center" style="width: 80px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <tr v-for="(member, index) in team" :key="member.auditor_id">
                                    <td class="pl-3 align-middle">
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="avatar-circle-sm mr-2 text-white bg-secondary font-weight-bold d-flex align-items-center justify-content-center">
                                                {{ getAuditorName(member.auditor_id).charAt(0) }}
                                            </div>
                                            <span class="font-weight-bold text-dark">{{
                                                getAuditorName(member.auditor_id) }}</span>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <select v-model="member.aeq_rol"
                                            class="form-control form-control-sm border-0 bg-transparent"
                                            style="min-width: 140px;">
                                            <option v-for="rol in roles" :key="rol" :value="rol">{{ rol }}</option>
                                        </select>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input type="number" step="0.5" v-model="member.aeq_horas_planificadas"
                                            class="form-control form-control-sm text-center border-0 bg-transparent p-0"
                                            style="max-width: 60px; margin: 0 auto;" />
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="font-weight-bold text-secondary">{{ member.aeq_horas_ejecutadas ||
                                            0 }}</span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button class="btn btn-link text-danger p-0" @click="removeMember(index)"
                                            title="Eliminar Miembro">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="team.length === 0">
                                    <td colspan="5" class="text-center py-5 bg-light">
                                        <i class="fas fa-users-slash fa-2x text-secondary opacity-50 mb-2"></i>
                                        <p class="text-muted small mb-0">No hay miembros asignados.</p>
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
import { ref, onMounted, defineProps, defineEmits, watch, computed } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

const props = defineProps(['auditId', 'auditData', 'auditStatus', 'loading']);
const emit = defineEmits(['saved']);
const toast = useToast();
const loading = ref(false);
const saving = ref(false);

const auditorsList = ref([]);
const team = ref([]);
const roles = ['Auditor Líder', 'Auditor Interno', 'Auditor', 'Especialista', 'Observador'];
const newMember = ref({ auditor_id: null, aeq_rol: 'Auditor', aeq_horas_planificadas: 0, aeq_horas_ejecutadas: 0 });

// Compute filtered auditors for the dropdown (exclude already in team)
const availableAuditors = computed(() => {
    const inTeamIds = new Set(team.value.map(m => m.auditor_id));
    return auditorsList.value.filter(aud => !inTeamIds.has(aud.id));
});

// Shared cache for auditors list to avoid refetching between components/instances
let cachedAuditors = null;

const loadData = async () => {
    // 1. Prioritize data from props
    if (props.auditData?.equipo && props.auditData.equipo.length > 0) {
        team.value = props.auditData.equipo;
    }

    // 2. Check cache for auditors list (Master List)
    if (cachedAuditors) {
        auditorsList.value = cachedAuditors;
    }

    // 3. Only show loader if we are missing critical data
    const needsAuditors = !cachedAuditors;
    const needsTeam = (!team.value || team.value.length === 0) && (!props.auditData || !props.auditData.equipo);

    if (needsAuditors || needsTeam) {
        loading.value = true;
    }

    try {
        const fetches = [];

        if (needsAuditors) {
            // Load only if not cached globally (implement simple cache or dependency injection later if needed)
            // Or better, check if store has it? For now, component local "cachedAuditors" variable (which is module scope) works.
            fetches.push(axios.get('/api/auditores').then(r => {
                cachedAuditors = r.data;
                auditorsList.value = r.data;
            }));
        } else {
            auditorsList.value = cachedAuditors;
        }

        if (needsTeam) {
            fetches.push(axios.get(`/api/auditorias/${props.auditId}`).then(r => {
                const fetchedTeam = r.data?.equipo || [];
                team.value = fetchedTeam;
            }));
        }

        if (fetches.length > 0) {
            await Promise.all(fetches);
        }
    } catch (e) {
        console.error("Error loading team data", e);
    } finally {
        loading.value = false;
    }
};

watch(() => props.auditData, (newVal) => {
    if (newVal && newVal.equipo) {
        team.value = newVal.equipo;
        loading.value = false; // Data is here, stop loading
    }
}, { immediate: true });

watch(() => props.loading, (newVal) => {
    // Only show loader if we don't have team data yet
    if (newVal && team.value.length === 0) {
        loading.value = true;
    } else if (!newVal) {
        loading.value = false;
    }
}, { immediate: true });

// O(1) lookup map for auditor names
const auditorsMap = computed(() => {
    const map = new Map();
    auditorsList.value.forEach(aud => {
        if (aud.id) map.set(aud.id, aud.user?.name || 'Desconocido');
    });
    return map;
});

const getAuditorName = (userId) => {
    return auditorsMap.value.get(userId) || 'Buscando...';
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
.italic {
    font-style: italic;
}

.avatar-circle-sm {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    font-size: 0.75rem;
}

.form-overlay-container {
    position: relative;
    min-height: 200px;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 5;
    backdrop-filter: blur(2px);
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
