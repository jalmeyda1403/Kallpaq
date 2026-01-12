<template>
    <div class="container-fluid">
        <!-- Skeleton Loading State -->
        <!-- Custom Header Design as per User Request -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-4">
            <div class="row no-gutters">
                <!-- Left Side: Information -->
                <div class="col-md-9 p-4">
                    <div class="d-flex align-items-center mb-2">
                        <h3 class="font-weight-bold text-dark mb-0 mr-3">
                            Programa Anual de Auditoría {{ programa.pa_anio }}
                        </h3>
                        <i class="fas fa-check text-danger fa-lg" v-if="programa.pa_estado === 'Aprobada'"
                            title="Aprobado"></i>
                    </div>

                    <div class="d-flex align-items-center mb-4">
                        <span class="text-muted small mr-2"><i class="fas fa-fingerprint"></i> v{{ programa.pa_version
                        }}</span>
                        <span class="badge badge-info rounded-pill px-3">{{ programa.pa_estado }}</span>
                    </div>

                    <div class="row">
                        <div class="col-md-4 border-right">
                            <div class="d-flex align-items-start">
                                <div class="bg-light rounded-circle p-2 mr-3 text-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-muted"></i>
                                </div>
                                <div>
                                    <h6 class="text-uppercase text-muted font-weight-bold small mb-1">RESPONSABLE</h6>
                                    <!-- Placeholder or Real Data -->
                                    <p class="font-weight-bold text-dark mb-0">Juan Almeyda Requejo</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 border-right">
                            <div class="d-flex align-items-start">
                                <div class="bg-light rounded-circle p-2 mr-3 text-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-calendar-alt text-muted"></i>
                                </div>
                                <div>
                                    <h6 class="text-uppercase text-muted font-weight-bold small mb-1">PROGRAMADO</h6>
                                    <p class="font-weight-bold text-dark mb-0">{{
                                        formatDate(programa.pa_fecha_aprobacion) || '-' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <div class="bg-light rounded-circle p-2 mr-3 text-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-clock text-muted"></i>
                                </div>
                                <div>
                                    <h6 class="text-uppercase text-muted font-weight-bold small mb-1">PERIODO FISCAL
                                    </h6>
                                    <p class="font-weight-bold text-dark mb-0">A - {{ programa.pa_anio }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Compliance Panel -->
                <div class="col-md-3 bg-dark text-white p-4">
                    <div class="d-flex flex-column align-items-center justify-content-center h-100">
                        <h6 class="text-uppercase text-white-50 font-weight-bold small mb-3">AVANCE DEL CUMPLIMIENTO
                        </h6>

                        <div class="mb-3">
                            <Knob v-model="compliancePercentage" :size="100" valueTemplate="{value}%" :readonly="true"
                                rangeColor="#424242" valueColor="#ffc107" textColor="#ffffff" />
                        </div>

                        <button class="btn btn-outline-light rounded-pill px-4 btn-sm" @click="goBack">
                            <i class="fas fa-chevron-left mr-1"></i> Volver
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Card -->
        <Transition name="fade" appear>
            <div v-if="!loading" class="card shadow-sm border-0 rounded-lg mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold text-dark mb-0">Cronograma de Auditorías</h5>
                    <button class="btn btn-dark rounded-pill px-4 shadow-sm" @click="openAuditModal">
                        <i class="fas fa-plus-circle mr-1"></i> Agendar Auditoría
                    </button>
                </div>

                <div class="card-body p-0">
                    <DataTable :value="auditorias" :loading="loading" responsiveLayout="scroll"
                        class="p-datatable-sm p-datatable-striped" :rowHover="true">

                        <Column header="N°" headerStyle="width: 50px;" class="text-center">
                            <template #body="slotProps">
                                {{ slotProps.index + 1 }}
                            </template>
                        </Column>

                        <Column field="ae_tipo" header="Tipo de Auditoría">
                            <template #body="{ data }">
                                <span class="badge px-3 py-1 rounded-pill"
                                    :class="data.ae_tipo === 'Interna' ? 'badge-info' : 'badge-warning'">
                                    {{ data.ae_tipo }}
                                </span>
                            </template>
                        </Column>

                        <Column header="Procesos">
                            <template #body="{ data }">
                                <div class="font-weight-bold text-dark">{{ getNombreProceso(data.proceso_id) }}</div>
                                <small class="text-muted d-block text-truncate" style="max-width: 200px;"
                                    v-if="data.ae_alcance" :title="data.ae_alcance">
                                    {{ data.ae_alcance }}
                                </small>
                            </template>
                        </Column>

                        <Column field="ae_sistema_kallpaq" header="Sistema de Gestión">
                            <template #body="{ data }">
                                <span class="badge badge-light border text-wrap" v-if="data.ae_sistema_kallpaq">
                                    {{ data.ae_sistema_kallpaq }}
                                </span>
                                <span v-else class="text-muted small">-</span>
                            </template>
                        </Column>

                        <Column field="ae_equipo_auditor" header="Auditor">
                            <template #body="{ data }">
                                <div v-if="data.ae_equipo_auditor">
                                    <i class="fas fa-user-tie text-muted mr-1"></i> {{ data.ae_equipo_auditor }}
                                </div>
                                <span v-else class="text-muted small">Por definir</span>
                            </template>
                        </Column>

                        <Column field="ae_auditado" header="Auditado">
                            <template #body="{ data }">
                                {{ data.ae_auditado || '-' }}
                            </template>
                        </Column>
                        <Column field="ae_fecha_inicio" header="Fecha Prog.">
                            <template #body="{ data }">
                                {{ formatDate(data.ae_fecha_inicio) }}
                            </template>
                        </Column>

                        <Column field="ae_estado" header="Estado">
                            <template #body="{ data }">
                                <span class="badge px-3 py-1 rounded-pill" :class="getEstadoBadge(data.ae_estado)">
                                    {{ data.ae_estado }}
                                </span>
                            </template>
                        </Column>

                        <Column header="Acciones" headerStyle="width: 100px" bodyStyle="text-align: center">
                            <template #body="{ data }">
                                <button class="btn btn-sm btn-light-primary mr-1" @click="editAudit(data)"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </Transition>

        <!-- Modal for Specific Audit -->
        <Dialog v-model:visible="auditModalVisible" :style="{ width: '50vw' }" :modal="true"
            :header="isEdit ? 'Editar Auditoría' : 'Agendar Nueva Auditoría'" :draggable="false">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold small">Tipo de Auditoría</label>
                        <select v-model="form.ae_tipo" class="form-control">
                            <option value="Interna">Interna</option>
                            <option value="Externa">Externa</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold small">Sistema de Gestión</label>
                        <input type="text" v-model="form.ae_sistema_kallpaq" class="form-control"
                            placeholder="Ej: ISO 9001, ISO 37001">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="font-weight-bold small">Proceso</label>
                <select v-model="form.proceso_id" class="form-control">
                    <option value="" disabled>Seleccione un proceso...</option>
                    <option v-for="proc in procesos" :key="proc.id" :value="proc.id">
                        {{ proc.proceso_nombre || proc.nombre }}
                    </option>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold small">Equipo Auditor</label>
                        <input type="text" v-model="form.ae_equipo_auditor" class="form-control"
                            placeholder="Nombre del auditor o equipo">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold small">Auditado</label>
                        <input type="text" v-model="form.ae_auditado" class="form-control"
                            placeholder="Rol o nombre del auditado">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">Fecha Inicio</label>
                        <input type="date" v-model="form.ae_fecha_inicio" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">Fecha Fin</label>
                        <input type="date" v-model="form.ae_fecha_fin" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="font-weight-bold small">Estado</label>
                        <select v-model="form.ae_estado" class="form-control">
                            <option value="Programada">Programada</option>
                            <option value="Realizada">Realizada</option>
                            <option value="Cancelada">Cancelada</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="font-weight-bold small">Objetivo / Alcance</label>
                <textarea v-model="form.ae_alcance" class="form-control" rows="3"
                    placeholder="Detalles adicionales..."></textarea>
            </div>

            <template #footer>
                <button class="btn btn-secondary" @click="auditModalVisible = false">Cancelar</button>
                <button class="btn btn-primary ml-2" @click="saveAudit">Guardar</button>
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useRoute, useRouter } from 'vue-router';
import { useProgramaAuditoriaStore } from '@/stores/programaAuditoriaStore';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import Knob from 'primevue/knob'; // Imported Knob
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';

const route = useRoute();
const router = useRouter();
const toast = useToast();
const store = useProgramaAuditoriaStore();

const loading = ref(true);
const programa = ref({});
const auditorias = ref([]);
const procesos = ref([]);
const auditModalVisible = ref(false);
const isEdit = ref(false);

const form = ref({
    id: null,
    pa_id: null,
    ae_tipo: 'Interna',
    proceso_id: '',
    ae_sistema_kallpaq: '',
    ae_equipo_auditor: '',
    ae_auditado: '',
    ae_estado: 'Programada',
    ae_fecha_inicio: '',
    ae_fecha_fin: '',
    ae_alcance: ''
});

// Computed for Compliance
const compliancePercentage = computed(() => {
    if (!auditorias.value.length) return 0;
    const completed = auditorias.value.filter(a => a.ae_estado === 'Realizada').length;
    return Math.round((completed / auditorias.value.length) * 100);
});

onMounted(async () => {
    await loadData();
});

const loadData = async () => {
    loading.value = true;
    try {
        const paId = route.params.id;

        // Check if we have the program in store
        if (store.currentPrograma && store.currentPrograma.id == paId) {
            programa.value = store.currentPrograma;
            auditorias.value = store.currentPrograma.auditoriasEspecificas || store.currentPrograma.auditorias_especificas || [];
            // If data is from store, processes might need fetching if not global, but for now we fetch processes always to be safe or parallel
            // Optimization: Fetch only processes
            const resProc = await axios.get('/api/procesos');
            procesos.value = Array.isArray(resProc.data) ? resProc.data : (resProc.data.data || []);
        } else {
            // Fallback: Fetch everything if direct access
            const [resProg, resProc] = await Promise.all([
                store.fetchProgramaById(paId), // Use store action
                axios.get('/api/procesos')
            ]);

            if (resProg) {
                programa.value = resProg;
                auditorias.value = resProg.auditoriasEspecificas || resProg.auditorias_especificas || [];
            }
            procesos.value = Array.isArray(resProc.data) ? resProc.data : (resProc.data.data || []);
        }

    } catch (e) {
        console.error('Error loading data:', e);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Error al cargar los datos', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const getNombreProceso = (id) => {
    if (!id) return 'General / SGC';
    const p = procesos.value.find(proc => proc.id === id);
    return p ? (p.proceso_nombre || p.nombre) : 'Desconocido';
};

const getEstadoBadge = (estado) => {
    switch (estado) {
        case 'Realizada': return 'badge-success';
        case 'Programada': return 'badge-warning';
        case 'Cancelada': return 'badge-danger';
        default: return 'badge-secondary';
    }
};

const formatDate = (d) => {
    if (!d) return '';
    // Adjust for timezone if needed, usually just slicing for YYYY-MM-DD
    return new Date(d).toLocaleDateString('es-PE', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const openAuditModal = () => {
    isEdit.value = false;
    resetForm();
    form.value.pa_id = programa.value.id;
    auditModalVisible.value = true;
};

const editAudit = (data) => {
    isEdit.value = true;
    form.value = { ...data };
    auditModalVisible.value = true;
};

const resetForm = () => {
    form.value = {
        id: null,
        pa_id: programa.value.id,
        ae_tipo: 'Interna',
        proceso_id: '',
        ae_sistema_kallpaq: '',
        ae_equipo_auditor: '',
        ae_auditado: '',
        ae_estado: 'Programada',
        ae_fecha_inicio: '',
        ae_fecha_fin: '',
        ae_alcance: ''
    };
};

const saveAudit = async () => {
    try {
        const url = isEdit.value ? `/api/auditoria/especifica/${form.value.id}` : '/api/auditoria/especifica';
        const method = isEdit.value ? 'put' : 'post';

        await axios[method](url, form.value);

        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Auditoría guardada', life: 3000 });
        auditModalVisible.value = false;
        await loadData();

    } catch (e) {
        console.error(e);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo guardar', life: 3000 });
    }
};

const goBack = () => {
    router.push({ name: 'programa.index' });
};
</script>

<style scoped>
.rounded-lg {
    border-radius: 0.5rem !important;
}

.no-gutters {
    margin-right: 0;
    margin-left: 0;
}

.no-gutters>.col,
.no-gutters>[class*="col-"] {
    padding-right: 0;
    padding-left: 0;
}

::v-deep(.p-datatable .p-datatable-thead > tr > th) {
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
}

/* Transition Styles */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
