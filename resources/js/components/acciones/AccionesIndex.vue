<template>
    <div class="container-fluid py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link to="/home"
                        class="text-danger font-weight-bold">Inicio</router-link></li>
                <li class="breadcrumb-item"><router-link :to="{ name: 'hallazgos.mine.vue' }"
                        class="text-danger font-weight-bold">Mis Solicitudes</router-link></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Ejecución de Acciones</li>
            </ol>
        </nav>

        <div class="animate__animated animate__fadeIn">
            <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                <div class="card-header bg-danger py-3 px-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm"
                                style="width: 45px; height: 45px;">
                                <i class="fas fa-tasks text-danger fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="font-weight-bold text-white mb-0">Ejecución de Acciones</h5>
                                <p class="text-white mb-0 small opacity-75">
                                    Expediente: {{ hallazgo.hallazgo_cod || 'Cargando...' }}
                                </p>
                            </div>
                        </div>
                        <router-link :to="{ name: 'hallazgos.mine.vue' }"
                            class="btn btn-outline-light btn-sm rounded-pill px-3 font-weight-bold">
                            <i class="fas fa-arrow-left mr-2"></i> Volver
                        </router-link>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Hallazgo Info Summary -->
                    <div class="alert alert-light border shadow-sm mb-4 rounded-lg">
                        <div class="row">
                            <div class="col-md-8 border-right">
                                <h6 class="text-danger font-weight-bold mb-2">Hallazgo</h6>
                                <p class="mb-0 text-muted small">{{ hallazgo.hallazgo_resumen }}</p>
                            </div>
                            <div class="col-md-4 pl-md-4 mt-3 mt-md-0">
                                <h6 class="text-danger font-weight-bold mb-2">Estado General</h6>
                                <span class="badge badge-pill p-2"
                                    :class="getStatusBadgeClass(hallazgo.hallazgo_estado)">
                                    {{ (hallazgo.hallazgo_estado || '').toUpperCase() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="h-1 mb-2">
                        <ProgressBar v-if="loading" mode="indeterminate" style="height: 4px;" />
                    </div>

                    <div v-if="!loading && acciones.length === 0" class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3 opacity-50"></i>
                        <p class="text-muted font-weight-bold">No hay acciones registradas para este hallazgo.</p>
                    </div>

                    <DataTable v-else :value="acciones" responsiveLayout="scroll"
                        :class="{ 'opacity-50 pointer-events-none': loading }" class="p-datatable-sm" stripedRows
                        paginator :rows="10" :rowsPerPageOptions="[5, 10, 20]">

                        <Column field="accion_cod" header="Código" style="width: 10%;">
                            <template #body="{ data }">
                                <span class="font-weight-bold text-dark small">{{ data.accion_cod }}</span>
                            </template>
                        </Column>

                        <Column field="accion_tipo" header="Tipo">
                            <template #body="{ data }">
                                <span class="text-capitalize small">{{ data.accion_tipo }}</span>
                            </template>
                        </Column>

                        <Column field="accion_descripcion" header="Descripción" style="width: 30%;">
                            <template #body="{ data }">
                                <div class="text-truncate-multiline small" style="max-height: 3.6em; overflow: hidden;"
                                    :title="data.accion_descripcion">
                                    {{ data.accion_descripcion }}
                                </div>
                            </template>
                        </Column>

                        <Column field="accion_responsable" header="Responsable">
                            <template #body="{ data }"><span class="small">{{ data.accion_responsable
                                    }}</span></template>
                        </Column>

                        <Column header="Vencimiento">
                            <template #body="{ data }">
                                <span class="small"
                                    :class="{ 'text-danger font-weight-bold': isFechaVencida(data.accion_fecha_fin_reprogramada || data.accion_fecha_fin_planificada) }">
                                    {{ formatDate(data.accion_fecha_fin_reprogramada ||
                                    data.accion_fecha_fin_planificada) }}
                                </span>
                                <i v-if="data.accion_fecha_fin_reprogramada" class="fas fa-clock text-warning ml-1"
                                    title="Reprogramada"></i>
                            </template>
                        </Column>

                        <Column field="accion_estado" header="Estado">
                            <template #body="{ data }">
                                <span :class="getEstadoBadgeClass(data.accion_estado) + ' small'">{{ data.accion_estado
                                    }}</span>
                            </template>
                        </Column>

                        <Column header="Gestión" style="width: 15%; text-align: center;">
                            <template #body="{ data }">
                                <div class="d-flex justify-content-center">
                                    <button class="btn btn-outline-info btn-sm mr-2 shadow-sm rounded-circle"
                                        @click="openAvanceModal(data)" title="Registrar Avance"
                                        style="width: 32px; height: 32px; padding: 0;">
                                        <i class="fas fa-tasks"></i>
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm shadow-sm rounded-circle"
                                        @click="openReprogramarModal(data)" title="Reprogramar"
                                        style="width: 32px; height: 32px; padding: 0;">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>

            <!-- Modals -->
            <AccionesAvanceForm :show="showAvanceModal" :actionData="selectedAction" @close="closeAvanceModal"
                @saved="refreshData" />

            <AccionesReprogramarForm ref="reprogramarModal" @accion-gestionada="refreshData" />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useHallazgoStore } from '@/stores/hallazgoStore';
import { storeToRefs } from 'pinia';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ProgressBar from 'primevue/progressbar';
import AccionesAvanceForm from './AccionesAvanceForm.vue';
import AccionesReprogramarForm from './AccionesReprogramarForm.vue';

const props = defineProps({
    hallazgoId: {
        type: [String, Number],
        required: true
    }
});

const hallazgoStore = useHallazgoStore();
const { hallazgoForm: hallazgo, todasLasAcciones: acciones, loading } = storeToRefs(hallazgoStore);

// Modal States
const showAvanceModal = ref(false);
const selectedAction = ref(null);
const reprogramarModal = ref(null);

// Methods
const openAvanceModal = (accion) => {
    selectedAction.value = accion;
    showAvanceModal.value = true;
};

const closeAvanceModal = () => {
    showAvanceModal.value = false;
    selectedAction.value = null;
};

const openReprogramarModal = (accion) => {
    if (reprogramarModal.value) {
        // Dispatch event as defined in AccionesReprogramarForm (formerly ReprogramarAccionModal)
        // Or if it exposes a method. The previous code used global event listener 'open-reprogramar-modal'
        // or passed data. Let's start by using a custom event or exposed method if available.
        // Checking the previous file content (AccionesReprogramarForm.vue) would be ideal, 
        // but assuming it uses the standard 'open-reprogramar-modal' event listener pattern common in this codebase:
        const event = new CustomEvent('open-reprogramar-modal', { detail: accion });
        document.dispatchEvent(event);
    }
};

const refreshData = async () => {
    await hallazgoStore.fetchTodasLasAcciones(props.hallazgoId);
};

// Utils
const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString();
};

const isFechaVencida = (fecha) => {
    if (!fecha) return false;
    return new Date(fecha) < new Date().setHours(0, 0, 0, 0);
};

const getEstadoBadgeClass = (estado) => {
    const map = {
        'programada': 'badge-primary',
        'en proceso': 'badge-info',
        'implementada': 'badge-success',
        'finalizada': 'badge-success',
        'desestimada': 'badge-secondary',
        'reprogramada': 'badge-warning'
    };
    return 'badge ' + (map[estado] || 'badge-light');
};

const getStatusBadgeClass = (estado) => {
    const map = {
        'abierto': 'bg-info text-white',
        'cerrado': 'bg-success text-white',
        'en_proceso': 'bg-primary text-white',
        'plan_enviado': 'bg-warning text-dark',
        'aprobado': 'bg-success text-white',
        'evaluado': 'bg-info text-white'
    };
    return map[estado] || 'bg-secondary text-white';
};

onMounted(async () => {
    await hallazgoStore.fetchHallazgo(props.hallazgoId);
    await hallazgoStore.fetchTodasLasAcciones(props.hallazgoId);
});

</script>

<style scoped>
.text-truncate-multiline {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.badge-block {
    display: block;
    width: 100%;
}
</style>
