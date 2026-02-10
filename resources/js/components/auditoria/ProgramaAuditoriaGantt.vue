<template>
    <div class="container-fluid">
        <!-- Skeleton Loading State -->
        <div v-if="loading" class="skeleton-container">
            <div class="skeleton-header mb-4 p-4 bg-white rounded-lg shadow-sm">
                <div class="row">
                    <div class="col-md-9">
                        <div class="skeleton-text skeleton-h3 mb-3" style="width: 60%"></div>
                        <div class="skeleton-text skeleton-small mb-4" style="width: 20%"></div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="skeleton-info-item"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="skeleton-info-item"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="skeleton-info-item"></div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="col-md-3 bg-light rounded-right p-4 d-flex flex-column align-items-center justify-content-center">
                        <div class="skeleton-circle mb-3"></div>
                        <div class="skeleton-button"></div>
                    </div>
                </div>
            </div>
            <div class="skeleton-body card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white p-3 d-flex justify-content-between">
                    <div class="skeleton-text" style="width: 30%"></div>
                    <div class="skeleton-button"></div>
                </div>
                <div class="card-body p-0">
                    <div v-for="i in 5" :key="i" class="skeleton-row p-3 border-bottom d-flex align-items-center">
                        <div class="skeleton-text mr-3" style="width: 5%"></div>
                        <div class="skeleton-text mr-3" style="width: 15%"></div>
                        <div class="skeleton-text mr-3" style="width: 40%"></div>
                        <div class="skeleton-text mr-3" style="width: 10%"></div>
                        <div class="skeleton-text" style="width: 15%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Header Design as per User Request -->
        <div v-else class="bg-white shadow-sm rounded-lg overflow-hidden mb-4">
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

                        <Column header="Código" headerStyle="width: 140px;" class="font-weight-bold">
                            <template #body="{ data }">
                                <span class="text-danger font-weight-bold">{{ data.ae_codigo || 'SIN CÓDIGO' }}</span>
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
                                <div v-if="data.proceso" class="font-weight-bold text-dark">
                                    {{ data.proceso.proceso_nombre || data.proceso.nombre }}
                                </div>
                                <div v-else-if="data.procesos && data.procesos.length > 0"
                                    class="font-weight-bold text-dark">
                                    <div v-for="p in data.procesos" :key="p.id" class="small">
                                        • {{ p.proceso_nombre || p.nombre }}
                                    </div>
                                </div>
                                <div v-else class="text-muted small">General / SGC</div>

                                <small class="text-muted d-block text-truncate" style="max-width: 200px;"
                                    v-if="data.ae_alcance" :title="data.ae_alcance">
                                    {{ data.ae_alcance }}
                                </small>
                            </template>
                        </Column>

                        <Column field="ae_sistema" header="Sistema de Gestión">
                            <template #body="{ data }">
                                <span class="badge badge-light border text-wrap"
                                    v-if="data.ae_sistema && data.ae_sistema.length > 0">
                                    {{ getSistemasNombres(data.ae_sistema) }}
                                </span>
                                <span v-else class="text-muted small">-</span>
                            </template>
                        </Column>

                        <Column field="ae_ciclo" header="Ciclo" class="text-center">
                            <template #body="{ data }">
                                <span class="badge badge-secondary">{{ data.ae_ciclo || 1 }}</span>
                            </template>
                        </Column>

                        <Column field="ae_horas_hombre" header="HH" class="text-center">
                            <template #body="{ data }">
                                <span class="font-weight-bold">{{ data.ae_horas_hombre || 0 }}h</span>
                            </template>
                        </Column>

                        <Column header="Fecha Programada">
                            <template #body="{ data }">
                                <span class="small font-weight-bold">
                                    {{ formatDate(data.ae_fecha_inicio) }} - {{ formatDate(data.ae_fecha_fin) }}
                                </span>
                            </template>
                        </Column>

                        <Column field="ae_estado" header="Estado">
                            <template #body="{ data }">
                                <span class="badge px-3 py-1 rounded-pill" :class="getEstadoBadge(data.ae_estado)">
                                    {{ data.ae_estado }}
                                </span>
                            </template>
                        </Column>

                        <Column header="Avance" headerStyle="width: 120px;">
                            <template #body="{ data }">
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 mr-2" style="height: 6px; border-radius: 10px;">
                                        <div class="progress-bar"
                                            :class="(data.ae_avance || 0) == 100 ? 'bg-success' : 'bg-primary'"
                                            :style="{ width: (data.ae_avance || 0) + '%' }"></div>
                                    </div>
                                    <span class="small font-weight-bold text-muted">{{ data.ae_avance || 0
                                    }}%</span>
                                </div>
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

        <!-- Modal for Specific Audit (New Component) -->
        <PlanAuditoriaModal v-if="auditModalVisible" v-model:visible="auditModalVisible" :audit-id="selectedAuditId"
            :programa-id="programa.id" :audit-status="selectedAuditStatus" @refresh="loadData" />
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useRoute, useRouter } from 'vue-router';
import { useProgramaAuditoriaStore } from '@/stores/programaAuditoriaStore';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Knob from 'primevue/knob';
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';
import PlanAuditoriaModal from './PlanAuditoriaModal.vue';

const route = useRoute();
const router = useRouter();
const toast = useToast();
const store = useProgramaAuditoriaStore();

const loading = computed(() => store.loading);
const programa = ref({});
const auditorias = ref([]);
const auditModalVisible = ref(false);
const selectedAuditId = ref(null);
const selectedAuditStatus = ref(null);

// Computed for Compliance
const compliancePercentage = computed(() => {
    return programa.value.compliance || 0;
});

onMounted(async () => {
    await loadData();
});

const loadData = async () => {
    try {
        const paId = route.params.id;
        // Use the store action which handles loading state and currentPrograma
        await store.fetchProgramaById(paId);

        if (store.currentPrograma) {
            programa.value = store.currentPrograma;
            auditorias.value = store.currentPrograma.auditorias_especificas || [];
        }
    } catch (e) {
        console.error('Error loading data:', e);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Error al cargar los datos', life: 3000 });
    }
};

const getEstadoBadge = (estado) => {
    switch (estado) {
        case 'Ejecutada':
        case 'Realizada':
        case 'Cerrada': return 'badge-success';
        case 'Programada': return 'badge-warning';
        case 'Cancelada': return 'badge-danger';
        default: return 'badge-secondary';
    }
};

const formatDate = (d) => {
    if (!d) return '';
    return new Date(d).toLocaleDateString('es-PE', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const getSistemasNombres = (sistemas) => {
    if (!sistemas || !Array.isArray(sistemas)) return '-';
    const map = {
        'sgc': 'ISO 9001',
        'sgas': 'ISO 37001',
        'sgco': 'ISO 21001',
        'sgsi': 'ISO 27001',
        'sgcm': 'ISO 37301'
    };
    return sistemas.map(s => map[s] || s.toUpperCase()).join(', ');
};

const openAuditModal = () => {
    selectedAuditId.value = null;
    selectedAuditStatus.value = null;
    auditModalVisible.value = true;
};

const editAudit = (data) => {
    selectedAuditId.value = data.id;
    selectedAuditStatus.value = data.ae_estado;
    auditModalVisible.value = true;
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

/* Skeleton Loading CSS */
.skeleton-text {
    height: 1rem;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 4px;
}

.skeleton-h3 {
    height: 2rem;
    border-radius: 8px;
}

.skeleton-small {
    height: 0.75rem;
}

.skeleton-info-item {
    height: 3rem;
    background: #f8f9fa;
    border-radius: 8px;
    animation: shimmer 1.5s infinite;
}

.skeleton-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #e0e0e0;
    animation: shimmer 1.5s infinite;
}

.skeleton-button {
    height: 2.5rem;
    width: 120px;
    border-radius: 50px;
    background: #e0e0e0;
    animation: shimmer 1.5s infinite;
}

.skeleton-row {
    height: 50px;
}

@keyframes shimmer {
    0% {
        background-position: 200% 0;
    }

    100% {
        background-position: -200% 0;
    }
}

.btn-light-primary {
    background-color: #e7f5ff;
    color: #1c7ed6;
    border: none;
    transition: all 0.2s;
}

.btn-light-primary:hover {
    background-color: #d0ebff;
    transform: translateY(-1px);
}
</style>
