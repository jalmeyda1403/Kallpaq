<template>
    <div class="container-fluid py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link to="/home"
                        class="text-danger font-weight-bold">Inicio</router-link></li>
                <li class="breadcrumb-item"><router-link :to="backRoute" class="text-danger font-weight-bold">{{
                    backLabel }}</router-link></li>
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
                        <div class="d-flex">
                            <button v-if="canManageActions" @click="openActionModal()"
                                class="btn btn-outline-light btn-sm rounded-pill px-3 font-weight-bold mr-2">
                                <i class="fas fa-plus mr-2"></i> Nueva Acción
                            </button>
                            <router-link :to="backRoute"
                                class="btn btn-link text-white text-decoration-none px-3 btn-sm font-weight-bold">
                                <i class="fas fa-arrow-left mr-2"></i> Volver a {{ backLabel }}
                            </router-link>
                        </div>
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

                        <Column header="Vencimiento" style="width: 15%;">
                            <template #body="{ data }">
                                <!-- Caso 1: Reprogramación Aprobada (Ya aplicada) -->
                                <div v-if="data.accion_fecha_fin_reprogramada">
                                    <span class="badge badge-warning text-wrap d-block mb-1 mx-auto"
                                        style="font-size: 90%; width: fit-content;"
                                        :class="{ 'badge-danger': isFechaVencida(data.accion_fecha_fin_reprogramada) }">
                                        {{ formatDate(data.accion_fecha_fin_reprogramada) }}
                                        <i class="fas fa-clock ml-1"></i>
                                    </span>
                                    <small class="text-muted d-block text-center"
                                        style="text-decoration: line-through;">
                                        {{ formatDate(data.accion_fecha_fin_planificada) }}
                                    </small>
                                </div>
                                <!-- Caso 2: Solicitud Pendiente (Mostrar fecha solicitada) -->
                                <div v-else-if="hasPendingReprogramming(data)">
                                    <span class="small d-block text-center mb-1">
                                        {{ formatDate(data.accion_fecha_fin_planificada) }}
                                    </span>
                                    <span class="badge badge-warning d-block mx-auto"
                                        style="font-size: 90%; width: fit-content;"
                                        title="Solicitud de Reprogramación Pendiente">
                                        {{ formatDate(getPendingReprogramming(data).ar_fecha_nueva) }}
                                    </span>
                                </div>
                                <!-- Caso 3: Normal -->
                                <div v-else>
                                    <span class="small font-weight-bold d-block text-center"
                                        :class="{ 'text-danger': isFechaVencida(data.accion_fecha_fin_planificada) }">
                                        {{ formatDate(data.accion_fecha_fin_planificada) }}
                                    </span>
                                </div>
                            </template>
                        </Column>

                        <Column field="accion_estado" header="Estado" style="width: 10%;">
                            <template #body="{ data }">
                                <span :class="getEstadoBadgeClass(data.accion_estado) + ' small'">{{ data.accion_estado
                                }}</span>
                                <div v-if="hasPendingReprogramming(data)"
                                    @click="reviewPendingRequest(getPendingReprogramming(data))"
                                    class="badge badge-warning font-weight-normal mt-1 d-block cursor-pointer"
                                    style="cursor: pointer;" title="Clic para revisar solicitud">
                                    <i class="fas fa-clock mr-1"></i> Solicitud Pend.
                                </div>
                            </template>
                        </Column>

                        <Column header="Avance" style="width: 15%;">
                            <template #body="{ data }">
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1" style="height: 12px; background-color: #e9ecef;">
                                        <div class="progress-bar progress-bar-striped" role="progressbar"
                                            :style="{ width: getPorcentajeAvance(data) + '%' }"
                                            :class="{ 'bg-success': getPorcentajeAvance(data) === 100, 'bg-info': getPorcentajeAvance(data) < 100 }"
                                            :aria-valuenow="getPorcentajeAvance(data)" aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                    <span class="small ml-2 font-weight-bold text-muted">{{ getPorcentajeAvance(data)
                                        }}%</span>
                                </div>
                            </template>
                        </Column>

                        <Column header="Gestión" style="width: 15%; text-align: center;">
                            <template #body="{ data }">
                                <div class="d-flex justify-content-center align-items-center">
                                    <template v-if="isReadOnly && hasPendingReprogramming(data)">
                                        <button class="btn btn-xs btn-success mr-2"
                                            @click="aprobarReprogramacion(getPendingReprogramming(data))"
                                            title="Aprobar Reprogramación">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-xs btn-danger"
                                            @click="rechazarReprogramacion(getPendingReprogramming(data))"
                                            title="Observar/Rechazar Reprogramación">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </template>
                                    <template v-else>
                                        <button class="btn btn-xs btn-link p-0 text-info" @click="openAvanceModal(data)"
                                            title="Registrar Avance">
                                            <i class="fas fa-tasks fa-lg"></i>
                                        </button>
                                        <template v-if="canManageActions">
                                            <button class="btn btn-xs btn-link p-0 ml-3 text-warning"
                                                @click="openActionModal(data)" title="Editar Acción">
                                                <i class="fas fa-pencil-alt fa-lg"></i>
                                            </button>
                                            <button class="btn btn-xs btn-link p-0 ml-3 text-danger"
                                                @click="confirmDelete(data)" title="Eliminar Acción">
                                                <i class="fas fa-trash-alt fa-lg"></i>
                                            </button>
                                        </template>
                                    </template>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>

            <!-- Modals -->
            <AccionesAvanceForm :show="showAvanceModal" :actionData="selectedAction" :initialTab="activeTab"
                :readonly="isReadOnly" @close="closeAvanceModal" @saved="refreshData" />

            <AccionesForm :show="showActionModal" :actionData="selectedAction" :hallazgoId="hallazgoId"
                :procesos="hallazgo.procesos" @close="closeActionModal" @saved="refreshData" />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useHallazgoStore } from '@/stores/hallazgoStore';
import { storeToRefs } from 'pinia';
import axios from 'axios';
import { route } from 'ziggy-js';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ProgressBar from 'primevue/progressbar';
import AccionesAvanceForm from './AccionesAvanceForm.vue';
import AccionesForm from './AccionesForm.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    hallazgoId: {
        type: [String, Number],
        required: true
    }
});

const routeData = useRoute();
const hallazgoStore = useHallazgoStore();
const { hallazgoForm: hallazgo, todasLasAcciones: acciones, loading } = storeToRefs(hallazgoStore);

// Navigation Logic
const backRoute = computed(() => {
    if (routeData.query.from) {
        return { name: routeData.query.from };
    }
    return { name: 'hallazgos.mine.vue' };
});

const backLabel = computed(() => {
    const name = backRoute.value.name;
    if (name === 'hallazgos.index') return 'Solicitudes de Mejora';
    if (name === 'hallazgos.mine.vue') return 'Mis Solicitudes';
    if (name === 'hallazgos.eficacia') return 'Bandeja de Eficacia';
    return 'Mis Solicitudes';
});

// Permissions
const canManageActions = computed(() => {
    const estado = (hallazgo.value.hallazgo_estado || '').toLowerCase();
    return ['creado', 'evaluado'].includes(estado);
});

// Modal States
const showAvanceModal = ref(false);
const showActionModal = ref(false);
const selectedAction = ref(null);
const activeTab = ref('avance');
const isReadOnly = computed(() => routeData.query.from === 'hallazgos.eficacia');

// Methods
const openAvanceModal = (accion, tab = 'avance') => {
    selectedAction.value = accion;
    activeTab.value = tab;
    showAvanceModal.value = true;
};

const closeAvanceModal = () => {
    showAvanceModal.value = false;
    selectedAction.value = null;
    activeTab.value = 'avance';
};

const openActionModal = (accion = null) => {
    selectedAction.value = accion;
    showActionModal.value = true;
};

const closeActionModal = () => {
    showActionModal.value = false;
    selectedAction.value = null;
};

const refreshData = async () => {
    await hallazgoStore.fetchTodasLasAcciones(props.hallazgoId, true);
};

const confirmDelete = (accion) => {
    Swal.fire({
        title: '¿Está seguro?',
        text: "¡No podrá revertir esta acción!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await hallazgoStore.deleteAccion(accion.id);
                await refreshData();
                Swal.fire('Eliminado', 'La acción ha sido eliminada.', 'success');
            } catch (error) {
                Swal.fire('Error', 'No se pudo eliminar la acción.', 'error');
            }
        }
    });
};

const hasPendingReprogramming = (accion) => {
    return accion.reprogramaciones && accion.reprogramaciones.some(r => r.ar_estado === 'solicitado');
};

const getPendingReprogramming = (accion) => {
    return accion.reprogramaciones.find(r => r.ar_estado === 'solicitado');
};

const executeAprobarReprogramacion = async (reprogramacion) => {
    try {
        await axios.post(route('acciones.reprogramar.aprobar', { accion: reprogramacion.accion_id, reprogramacion: reprogramacion.id }));
        Swal.fire('Aprobado', 'La reprogramación ha sido aprobada.', 'success');
        await refreshData();
    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'No se pudo aprobar la solicitud.', 'error');
    }
};

const executeRechazarReprogramacion = async (reprogramacion, motivo) => {
    try {
        await axios.post(route('acciones.reprogramar.rechazar', { accion: reprogramacion.accion_id, reprogramacion: reprogramacion.id }), { motivo: motivo });
        Swal.fire('Observado', 'La solicitud ha sido observada.', 'success');
        await refreshData();
    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'No se pudo rechazar la solicitud.', 'error');
    }
};

const aprobarReprogramacion = async (reprogramacion) => {
    const result = await Swal.fire({
        title: '¿Aprobar Reprogramación?',
        text: `Nueva fecha solicitada: ${formatDate(reprogramacion.ar_fecha_nueva)}. Justificación: ${reprogramacion.ar_justificacion}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, Aprobar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745'
    });

    if (result.isConfirmed) {
        await executeAprobarReprogramacion(reprogramacion);
    }
};

const rechazarReprogramacion = async (reprogramacion) => {
    const result = await Swal.fire({
        title: 'Observar/Rechazar Solicitud',
        input: 'text',
        inputLabel: 'Motivo de la observación',
        inputPlaceholder: 'Ingrese el motivo...',
        showCancelButton: true,
        confirmButtonText: 'Observar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545',
        inputValidator: (value) => {
            if (!value) {
                return 'Debe ingresar un motivo!';
            }
        }
    });

    if (result.isConfirmed) {
        await executeRechazarReprogramacion(reprogramacion, result.value);
    }
};

const reviewPendingRequest = async (reprogramacion) => {
    if (!isReadOnly.value) return; // Sólo lectura (Especialista) puede revisar

    if (!reprogramacion) return;

    const result = await Swal.fire({
        title: 'Revisión de Reprogramación',
        html: `
            <div class="text-left">
                <p><strong>Fecha Actual:</strong> ${formatDate(reprogramacion.ar_fecha_anterior)}</p>
                <p><strong>Nueva Fecha Solicitada:</strong> <span class="text-danger font-weight-bold">${formatDate(reprogramacion.ar_fecha_nueva)}</span></p>
                <p><strong>Solicitante:</strong> ${reprogramacion.usuario ? reprogramacion.usuario.name : 'Usuario'}</p>
                <p><strong>Justificación:</strong></p>
                <div class="alert alert-secondary small p-2">${reprogramacion.ar_justificacion}</div>
            </div>
        `,
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check"></i> Aprobar',
        denyButtonText: '<i class="fas fa-times"></i> Observar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745',
        denyButtonColor: '#dc3545'
    });

    if (result.isConfirmed) {
        await executeAprobarReprogramacion(reprogramacion);
    } else if (result.isDenied) {
        const motifResult = await Swal.fire({
            title: 'Motivo de la observación',
            input: 'text',
            inputPlaceholder: 'Ingrese el motivo...',
            showCancelButton: true,
            confirmButtonText: 'Observar',
            cancelButtonText: 'Cancelar',
            inputValidator: (value) => {
                if (!value) return 'Debe ingresar un motivo';
            }
        });

        if (motifResult.isConfirmed) {
            await executeRechazarReprogramacion(reprogramacion, motifResult.value);
        }
    }
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

const getPorcentajeAvance = (accion) => {
    if (!accion.avances || accion.avances.length === 0) return 0;
    // Assuming advances are ordered by ID or created_at desc/asc. 
    // In Controller I used ->get() which typically returns default order (asc id).
    // Let's assume the last one is the latest.
    const last = accion.avances[accion.avances.length - 1];
    return last.accion_avance_porcentaje || 0;
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
