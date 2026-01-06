<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light py-2 px-3 rounded">
                <li class="breadcrumb-item"><router-link to="/home" class="text-secondary">Inicio</router-link></li>
                <li class="breadcrumb-item"><router-link :to="{ name: 'hallazgos.mine.vue' }" class="text-secondary">Mis
                        Hallazgos</router-link></li>
                <li class="breadcrumb-item active text-danger" aria-current="page">Planes de Acción</li>
            </ol>
        </nav>

        <!-- Loader Spinner -->
        <div v-if="isPageLoading" class="loading-spinner-container">
            <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;">
                <span class="sr-only">Cargando...</span>
            </div>
            <div class="mt-3 text-muted font-weight-bold">Cargando Planes de Acción...</div>
        </div>

        <!-- Real Content -->
        <div v-else class="animate__animated animate__fadeIn">
            <!-- Expediente del Hallazgo (Detalles + Causa Raíz) -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-file-alt mr-2"></i>Expediente del Hallazgo: {{ hallazgo.hallazgo_cod }}
                    </h5>
                </div>
                <div v-if="!hallazgoStore.accionesPermitidas" class="alert alert-warning m-3 mb-0 small" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Las acciones están deshabilitadas</strong> para este estado de hallazgo
                </div>
                <div class="card-body">
                    <!-- Detalles Generales -->
                    <div class="row mb-4">
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small font-weight-bold text-uppercase">Resumen</label>
                            <div class="font-weight-bold text-dark h6">{{ hallazgo.hallazgo_resumen }}</div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small font-weight-bold text-uppercase">Descripción
                                Detallada</label>
                            <div class="bg-light p-3 rounded text-secondary border-left-secondary"
                                style="border-left: 3px solid #6c757d;">
                                {{ hallazgo.hallazgo_descripcion }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small font-weight-bold text-uppercase">Clasificación</label>
                            <div><span class="badge badge-secondary">{{ hallazgo.hallazgo_clasificacion }}</span></div>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small font-weight-bold text-uppercase">Origen</label>
                            <div class="text-dark">{{ hallazgo.hallazgo_origen }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small font-weight-bold text-uppercase">Fecha Identificación</label>
                            <div class="text-dark"><i class="far fa-calendar-alt mr-1 text-danger"></i> {{
                                formatDate(hallazgo.hallazgo_fecha_identificacion) }}</div>
                        </div>
                    </div>

                    <hr>

                    <!-- Análisis de Causa Raíz Integrado -->
                    <CausaRaiz :hallazgoId="hallazgoId" :embedded="true" />
                </div>
            </div>

            <!-- Planes de Acción -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-danger text-white py-3 d-flex align-items-center">
                    <h5 class="mb-0 font-weight-bold"><i class="fas fa-tasks mr-2"></i>Planes de Acción</h5>
                    <div class="ml-auto">
                        <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 mr-2"
                            @click="imprimirPlanAccion" title="Imprimir Plan de Acción">
                            <i class="fas fa-print mr-1"></i> Imprimir
                        </button>
                        <button :disabled="!hallazgoStore.accionesPermitidas"
                            class="btn btn-outline-light btn-sm rounded-pill px-3" @click="openModal()"
                            :title="!hallazgoStore.accionesPermitidas ? 'No se pueden crear acciones en este estado de hallazgo' : ''">
                            <i class="fas fa-plus mr-1"></i> Nueva Acción
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-light border-left-danger mb-4" role="alert"
                        style="border-left: 4px solid #dc3545;">
                        <i class="fas fa-info-circle text-danger mr-2"></i>
                        En esta sección se detallan las acciones planificadas para abordar la causa raíz identificada.
                        Gestione, monitoree y actualice el estado de cada acción para asegurar la mejora continua.
                    </div>

                    <DataTable :value="acciones" responsiveLayout="scroll" :loading="isLoading && !isPageLoading"
                        class="p-datatable-sm" stripedRows paginator :rows="5" :rowsPerPageOptions="[5, 10, 20]">
                        <template #empty>
                            No hay planes de acción registrados.
                        </template>
                        <Column field="accion_cod" header="Código" style="width: 10%;">
                            <template #body="{ data }">
                                <span class="font-weight-bold text-dark">{{ data.accion_cod }}</span>
                            </template>
                        </Column>
                        <Column field="Proceso" header="Proceso" style="width: 15%;">
                            <template #body="{ data }">
                                <span class="text-secondary">{{ data.hallazgo_proceso?.proceso?.proceso_nombre }}</span>
                            </template>
                        </Column>
                        <Column field="accion_descripcion" header="Descripción de la Acción" style="width: 25%;">
                        </Column>
                        <Column field="accion_tipo" header="Tipo de Acción" style="width: 10%;">
                            <template #body="{ data }">
                                <span v-if="data.accion_tipo" :class="getTipoAccionClass(data.accion_tipo)"
                                    class="badge">
                                    {{ getTipoAccionLabel(data.accion_tipo) }}
                                </span>
                                <span v-else class="text-muted">N/A</span>
                            </template>
                        </Column>
                        <Column field="accion_responsable" header="Responsable" style="width: 15%;">
                            <template #body="{ data }">
                                <div class="d-flex align-items-center">
                                    {{ data.accion_responsable }}
                                </div>
                            </template>
                        </Column>
                        <Column field="accion_fecha_inicio" header="Fecha Inicio" style="width: 120px;">
                            <template #body="{ data }">
                                {{ formatDate(data.accion_fecha_inicio) }}
                            </template>
                        </Column>
                        <Column header="Fecha Fin" style="width: 140px;">
                            <template #body="{ data }">
                                <div v-if="data.accion_fecha_fin_reprogramada">
                                    <span class="text-secondary" style="text-decoration: line-through;">
                                        {{ formatDate(data.accion_fecha_fin_planificada) }}
                                    </span>
                                    <br>
                                    <span class="text-success font-weight-bold">
                                        {{ formatDate(data.accion_fecha_fin_reprogramada) }}
                                    </span>
                                </div>
                                <div v-else>
                                    <span
                                        :class="{ 'text-danger font-weight-bold': isFechaVencida(data.accion_fecha_fin_planificada) }">
                                        {{ formatDate(data.accion_fecha_fin_planificada) }}
                                    </span>
                                </div>
                                <i v-if="isFechaVencida(data.accion_fecha_fin_reprogramada || data.accion_fecha_fin_planificada)"
                                    class="fas fa-exclamation-triangle ml-1 text-danger" title="Fecha vencida"></i>
                            </template>
                        </Column>
                        <Column field="accion_estado" header="Estado">
                            <template #body="{ data }">
                                <span :class="getEstadoBadgeClass(data.accion_estado)">{{ data.accion_estado }}</span>
                            </template>
                        </Column>
                        <Column header="Acciones" :exportable="false" style="text-align: center"
                            bodyStyle="text-align: center">
                            <template #body="{ data }">
                                <div class="d-flex justify-content-center">
                                    <button @click.prevent="openModal(data)" class="btn btn-sm btn-light text-warning"
                                        title="Editar" :disabled="!hallazgoStore.accionesPermitidas">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <button @click.prevent="openReprogramarModal(data)"
                                        class="btn btn-sm btn-light text-info" title="Reprogramar"
                                        :disabled="isAccionTerminada(data) || !hallazgoStore.gestionAccionesPermitida">
                                        <i class="fas fa-clock"></i>
                                    </button>
                                    <button @click.prevent="openAvanceModal(data)"
                                        class="btn btn-sm btn-light text-success" title="Registrar Avance"
                                        :disabled="isAccionTerminada(data) || !hallazgoStore.gestionAccionesPermitida">
                                        <i class="fas fa-tasks"></i>
                                    </button>

                                    <button @click.prevent="confirmDelete(data)"
                                        class="btn btn-sm btn-light text-danger" title="Eliminar"
                                        :disabled="!hallazgoStore.accionesPermitidas">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </template>
                        </Column>
                    </DataTable>


                </div>
            </div>

            <!-- Modals -->
            <ReprogramarAccionModal @accion-gestionada="refreshAcciones" />
            <AccionesForm :show="showActionModal" :actionData="selectedAction" :hallazgoId="hallazgoId"
                :procesos="hallazgo.procesos" @close="closeModal" @saved="onActionSaved" />
            <AccionesAvanceForm :show="showAvanceModal" :actionData="selectedAvanceAction" @close="closeAvanceModal"
                @saved="onAvanceSaved" />
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { storeToRefs } from 'pinia';
import Swal from 'sweetalert2';

// Import modals and components
import ReprogramarAccionModal from './ReprogramarAccionModal.vue';
import AccionesForm from './AccionesForm.vue';
import AccionesAvanceForm from './AccionesAvanceForm.vue';
import CausaRaiz from './CausaRaiz.vue';

// Import Store
import { useHallazgoStore } from '@/stores/hallazgoStore';

const props = defineProps({
    hallazgoId: {
        type: [Number, String],
        required: true
    }
});

// Initialize Store
const hallazgoStore = useHallazgoStore();
const { todasLasAcciones: acciones, hallazgoForm: hallazgo, loading: isLoading } = storeToRefs(hallazgoStore);

const isPageLoading = ref(true); // Estado de carga inicial de la página

// Modal states
const showActionModal = ref(false);
const showAvanceModal = ref(false);
const selectedAction = ref(null);
const selectedAvanceAction = ref(null);

// Detalle modal states (keeping it as extra feature)
const detalleModalVisible = ref(false);
const accionDetalle = ref({});
const evidenciasAccion = ref([]);

const isAccionTerminada = (accion) => {
    return ['desestimada', 'finalizada'].includes(accion.accion_estado);
};

const isFechaVencida = (fecha) => {
    if (!fecha) return false;
    const fechaFin = new Date(fecha);
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    fechaFin.setHours(0, 0, 0, 0);
    return fechaFin < hoy;
};

const getEstadoBadgeClass = (estado) => {
    switch (estado) {
        case 'programada': return 'badge badge-primary';
        case 'en proceso': return 'badge badge-info';
        case 'implementada': return 'badge badge-success';
        case 'finalizada': return 'badge badge-success';
        case 'desestimada': return 'badge badge-secondary';
        case 'reprogramada': return 'badge badge-warning';
        default: return 'badge badge-light';
    }
};

// Modal Handlers
const openModal = (accion = null) => {
    selectedAction.value = accion;
    showActionModal.value = true;
};

const closeModal = () => {
    showActionModal.value = false;
    selectedAction.value = null;
};

const onActionSaved = async () => {
    await refreshAcciones();
};

const openReprogramarModal = (accion) => {
    document.dispatchEvent(new CustomEvent('open-reprogramar-modal', { detail: accion }));
};

const openAvanceModal = (accion) => {
    selectedAvanceAction.value = accion;
    showAvanceModal.value = true;
};

const closeAvanceModal = () => {
    showAvanceModal.value = false;
    selectedAvanceAction.value = null;
};

const onAvanceSaved = async () => {
    await refreshAcciones();
};

const confirmDelete = (accion) => {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await hallazgoStore.deleteAccion(accion.id);
                Swal.fire('Eliminado!', 'La acción ha sido eliminada.', 'success');
                await refreshAcciones();
            } catch (error) {
                Swal.fire('Error', 'No se pudo eliminar la acción.', 'error');
            }
        }
    });
};

// Detalle Modal Logic
const openDetalleModal = async (accion) => {
    accionDetalle.value = accion;
    if (accion.accion_ruta_evidencia) {
        try {
            const decodedEvidencias = JSON.parse(accion.accion_ruta_evidencia);
            if (Array.isArray(decodedEvidencias)) {
                evidenciasAccion.value = decodedEvidencias.map(e => ({
                    ...e,
                    size: e.name ? e.name.length * 1000 : 0
                }));
            } else {
                evidenciasAccion.value = [];
            }
        } catch (e) {
            evidenciasAccion.value = [];
        }
    } else {
        evidenciasAccion.value = [];
    }
    detalleModalVisible.value = true;
};

const cerrarDetalleModal = () => {
    detalleModalVisible.value = false;
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const formatDateFromPath = (path) => {
    return path ? new Date().toLocaleDateString() : 'N/A';
};

const getDownloadUrl = (path) => {
    return `/acciones/evidencia/${path}`;
};

const refreshAcciones = async () => {
    await hallazgoStore.fetchTodasLasAcciones(props.hallazgoId);
};

const imprimirPlanAccion = () => {
    const routeUrl = `/vue/acciones/imprimir/${props.hallazgoId}`;
    window.open(routeUrl, '_blank');
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
};

const getTipoAccionClass = (tipo) => {
    switch (tipo) {
        case 'inmediata': return 'badge-danger';
        case 'correctiva': return 'badge-warning';
        default: return 'badge-secondary';
    }
};

const getTipoAccionLabel = (tipo) => {
    const labels = {
        'inmediata': 'Inmediata',
        'correctiva': 'Correctiva'
    };
    return labels[tipo] || tipo;
};

onMounted(async () => {
    isPageLoading.value = true;
    try {
        await Promise.all([
            hallazgoStore.fetchHallazgo(props.hallazgoId),
            hallazgoStore.fetchTodasLasAcciones(props.hallazgoId),
            hallazgoStore.fetchCausaRaiz(props.hallazgoId)
        ]);
    } catch (error) {
        console.error("Error loading page data:", error);
    } finally {
        isPageLoading.value = false;
    }
});
</script>

<style scoped>
.loading-spinner-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    min-height: 400px;
    /* Altura mínima para centrar verticalmente en la pantalla */
    width: 100%;
}

/* Animaciones de entrada suaves */
.animate__animated.animate__fadeIn {
    animation-duration: 0.4s;
}

/* Mejora visual de bordes */
.border-left-danger {
    border-left: 4px solid #dc3545 !important;
}

.border-left-secondary {
    border-left: 3px solid #6c757d !important;
}

/* Transiciones suaves para elementos interactivos */
.btn {
    transition: all 0.2s ease-in-out;
}

.card {
    transition: box-shadow 0.3s ease-in-out;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
}

/* Estilo para el modal de detalle */
.modal.show {
    display: block !important;
    background-color: rgba(0, 0, 0, 0.5);
}

/* Ajustes para la tabla */
.p-datatable .p-datatable-tbody>tr>td {
    vertical-align: middle;
}

/* Indicador de vencimiento */
.text-vencido {
    color: #dc3545 !important;
    font-weight: bold;
}
</style>
