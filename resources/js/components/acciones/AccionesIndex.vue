
<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent pl-0">
                <li class="breadcrumb-item"><a href="/home" class="text-secondary">Home</a></li>
                <li class="breadcrumb-item"><router-link :to="{ name: 'hallazgos.mine.vue' }" class="text-secondary">Mis Hallazgos</router-link></li>
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
                <div
                    v-if="!hallazgoStore.accionesPermitidas"
                    class="alert alert-warning m-3 mb-0 small"
                    role="alert">
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
                            <label class="text-muted small font-weight-bold text-uppercase">Descripción Detallada</label>
                            <div class="bg-light p-3 rounded text-secondary border-left-secondary" style="border-left: 3px solid #6c757d;">
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
                            <div class="text-dark"><i class="far fa-calendar-alt mr-1 text-danger"></i> {{ formatDate(hallazgo.hallazgo_fecha_identificacion) }}</div>
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
                        <button
                            type="button"
                            class="btn btn-outline-light btn-sm rounded-pill px-3 mr-2"
                            @click="imprimirPlanAccion"
                            title="Imprimir Plan de Acción">
                            <i class="fas fa-print mr-1"></i> Imprimir
                        </button>
                        <button
                            :disabled="!hallazgoStore.accionesPermitidas"
                            class="btn btn-outline-light btn-sm rounded-pill px-3"
                            @click="openCreateModal"
                            :title="!hallazgoStore.accionesPermitidas ? 'No se pueden crear acciones en este estado de hallazgo' : ''">
                            <i class="fas fa-plus mr-1"></i> Nueva Acción
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-light border-left-danger mb-4" role="alert" style="border-left: 4px solid #dc3545;">
                        <i class="fas fa-info-circle text-danger mr-2"></i>
                        En esta sección se detallan las acciones planificadas para abordar la causa raíz identificada. Gestione, monitoree y actualice el estado de cada acción para asegurar la mejora continua.
                    </div>
                    
                    <DataTable :value="acciones" responsiveLayout="scroll" :loading="isLoading && !isPageLoading" 
                               class="p-datatable-sm" stripedRows
                               paginator :rows="5" :rowsPerPageOptions="[5, 10, 20]">
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
                        <Column field="accion_descripcion" header="Descripción de la Acción" style="width: 25%;"></Column>
                        <Column field="accion_tipo" header="Tipo de Acción" style="width: 10%;">
                            <template #body="{ data }">
                                <span v-if="data.accion_tipo" :class="getTipoAccionClass(data.accion_tipo)" class="badge">
                                    {{ getTipoAccionLabel(data.accion_tipo) }}
                                </span>
                                <span v-else class="text-muted">N/A</span>
                            </template>
                        </Column>
                        <Column field="accion_responsable" header="Responsable" style="width: 15%;">
                            <template #body="{ data }">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle d-flex justify-content-center align-items-center mr-2 text-danger font-weight-bold" style="width: 30px; height: 30px;">
                                        {{ data.accion_responsable.charAt(0).toUpperCase() }}
                                    </div>
                                    <span>{{ data.accion_responsable }}</span>
                                </div>
                            </template>
                        </Column>
                        <Column field="accion_fecha_inicio" header="Inicio">
                            <template #body="{ data }">
                                {{ formatDate(data.accion_fecha_inicio) }}
                            </template>
                        </Column>
                        <Column field="accion_fecha_fin_planificada" header="Fin Prog." style="width: 120px;">
                            <template #body="{ data }">
                                <div class="d-flex align-items-center">
                                    <span :class="{ 'text-danger font-weight-bold': isFechaVencida(data.accion_fecha_fin_planificada) }">
                                        {{ formatDate(data.accion_fecha_fin_planificada) }}
                                    </span>
                                    <i v-if="isFechaVencida(data.accion_fecha_fin_planificada)"
                                       class="fas fa-exclamation-triangle ml-1 text-danger"
                                       title="Fecha vencida"></i>
                                </div>
                            </template>
                        </Column>
                        <Column field="accion_estado" header="Estado">
                            <template #body="{ data }">
                                <span :class="getEstadoBadgeClass(data.accion_estado)">{{ data.accion_estado }}</span>
                            </template>
                        </Column>
                        <Column header="Acciones" :exportable="false" style="width: 150px;">
                            <template #body="{ data }">
                                <div class="d-flex justify-content-center">
                                    <button @click.prevent="openDetalleModal(data)"
                                            class="btn btn-sm btn-light text-primary mr-1"
                                            title="Ver Detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button @click.prevent="openReprogramarModal(data)"
                                            :class="['btn btn-sm btn-light text-warning mr-1', { 'disabled': isAccionTerminada(data) || !hallazgoStore.gestionAccionesPermitida }]"
                                            :title="!hallazgoStore.gestionAccionesPermitida ? 'No se pueden gestionar acciones en este estado de hallazgo' : (isAccionTerminada(data) ? 'Acción terminada' : 'Gestionar / Reprogramar')">
                                        <i class="fas fa-calendar-alt" :class="{ 'text-muted': !hallazgoStore.gestionAccionesPermitida }"></i>
                                    </button>
                                    <button @click.prevent="openConcluirModal(data)"
                                            :class="['btn btn-sm btn-light text-success', { 'disabled': isAccionTerminada(data) || !hallazgoStore.gestionAccionesPermitida }]"
                                            :title="!hallazgoStore.gestionAccionesPermitida ? 'No se pueden concluir acciones en este estado de hallazgo' : (isAccionTerminada(data) ? 'Acción terminada' : 'Concluir Acción')">
                                        <i class="fas fa-check-circle" :class="{ 'text-muted': !hallazgoStore.gestionAccionesPermitida }"></i>
                                    </button>
                                </div>
                            </template>
                        </Column>
                    </DataTable>

                    <!-- Modal de Detalle de Acción -->
                    <div class="modal fade" :class="{ 'show': detalleModalVisible }"
                         :style="{ display: detalleModalVisible ? 'block' : 'none' }"
                         tabindex="-1"
                         role="dialog"
                         data-backdrop="static"
                         data-keyboard="false"
                         style="background-color: rgba(0,0,0,0.5);">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Detalles de la Acción - {{ accionDetalle.accion_cod }}
                                    </h5>
                                    <button type="button" class="close text-white" @click="cerrarDetalleModal">
                                        <span class="text-white">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="font-weight-bold text-muted small">Tipo de Acción</label>
                                            <div class="bg-light p-2 rounded">
                                                <span :class="getTipoAccionClass(accionDetalle.accion_tipo)" class="badge">
                                                    {{ getTipoAccionLabel(accionDetalle.accion_tipo) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="font-weight-bold text-muted small">Proceso Afectado</label>
                                            <div class="bg-light p-2 rounded">
                                                {{ accionDetalle.hallazgo_proceso?.proceso?.proceso_nombre || 'No asignado' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold text-muted small">Descripción de la Acción</label>
                                        <div class="bg-light p-3 rounded">
                                            {{ accionDetalle.accion_descripcion }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="font-weight-bold text-muted small">Responsable</label>
                                            <div class="bg-light p-2 rounded">
                                                {{ accionDetalle.accion_responsable }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="font-weight-bold text-muted small">Estado</label>
                                            <div class="bg-light p-2 rounded">
                                                <span :class="getEstadoBadgeClass(accionDetalle.accion_estado)" class="badge">
                                                    {{ accionDetalle.accion_estado }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label class="font-weight-bold text-muted small">Fecha Inicio</label>
                                            <div class="bg-light p-2 rounded">
                                                {{ formatDate(accionDetalle.accion_fecha_inicio) }}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="font-weight-bold text-muted small">Fecha Fin Planificada</label>
                                            <div class="bg-light p-2 rounded">
                                                <span :class="{ 'text-danger font-weight-bold': isFechaVencida(accionDetalle.accion_fecha_fin_planificada) }">
                                                    {{ formatDate(accionDetalle.accion_fecha_fin_planificada) }}
                                                    <i v-if="isFechaVencida(accionDetalle.accion_fecha_fin_planificada)"
                                                       class="fas fa-exclamation-triangle ml-1 text-danger"
                                                       title="Fecha vencida"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="font-weight-bold text-muted small">Fecha Fin Real</label>
                                            <div class="bg-light p-2 rounded">
                                                {{ formatDate(accionDetalle.accion_fecha_fin_real) }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group" v-if="accionDetalle.accion_justificacion">
                                        <label class="font-weight-bold text-muted small">Justificación</label>
                                        <div class="bg-light p-3 rounded">
                                            {{ accionDetalle.accion_justificacion }}
                                        </div>
                                    </div>

                                    <!-- Sección de Evidencias -->
                                    <div class="card mt-4 border">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 font-weight-bold"><i class="fas fa-file-alt mr-2 text-primary"></i>Evidencias</h6>
                                        </div>
                                        <div class="card-body">
                                            <div v-if="!evidenciasAccion || evidenciasAccion.length === 0" class="text-center text-muted py-3">
                                                No hay evidencias adjuntas
                                            </div>
                                            <div v-else class="list-group">
                                                <div v-for="(evidencia, index) in evidenciasAccion" :key="index" class="list-group-item">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="mb-1">{{ evidencia.name }}</h6>
                                                        <small class="text-muted">{{ formatFileSize(evidencia.size) }}</small>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                                        <small class="text-muted">{{ formatDateFromPath(evidencia.path) }}</small>
                                                        <div>
                                                            <a :href="getDownloadUrl(evidencia.path)"
                                                               class="btn btn-outline-primary btn-sm mr-2"
                                                               target="_blank">
                                                                <i class="fas fa-download"></i> Descargar
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" @click="cerrarDetalleModal">
                                        <i class="fas fa-times mr-1"></i>Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modals -->
            <ReprogramarAccionModal @accion-gestionada="refreshAcciones" />
            <ConcluirAccionModal @accion-concluida="refreshAcciones" />
            <AccionCreateModal :hallazgoId="hallazgoId" :procesos="hallazgo.procesos" @accion-creada="refreshAcciones" />
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { storeToRefs } from 'pinia'; 

// Import modals and components
import ReprogramarAccionModal from './ReprogramarAccionModal.vue';
import ConcluirAccionModal from './ConcluirAccionModal.vue';
import AccionCreateModal from './AccionCreateModal.vue';
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

const isAccionTerminada = (accion) => {
    return ['desestimada', 'finalizada'].includes(accion.accion_estado);
};

const isFechaVencida = (fecha) => {
    if (!fecha) return false;
    const fechaFin = new Date(fecha);
    const hoy = new Date();
    // Establecemos la hora a 00:00:00 para comparar solo las fechas
    hoy.setHours(0, 0, 0, 0);
    fechaFin.setHours(0, 0, 0, 0);
    return fechaFin < hoy;
};

const getEstadoBadgeClass = (estado) => {
    switch (estado) {
        case 'programada': return 'badge badge-primary';
        case 'en ejecucion': return 'badge badge-info';
        case 'finalizada': return 'badge badge-success';
        case 'desestimada': return 'badge badge-secondary';
        case 'reprogramada': return 'badge badge-warning';
        default: return 'badge badge-light';
    }
};

const openReprogramarModal = (accion) => {
    document.dispatchEvent(new CustomEvent('open-reprogramar-modal', { detail: accion }));
};

const openConcluirModal = (accion) => {
    document.dispatchEvent(new CustomEvent('open-concluir-modal', { detail: accion }));
};

const openCreateModal = () => {
    document.dispatchEvent(new CustomEvent('open-create-accion-modal'));
};

// Variables y métodos para el modal de detalle
const detalleModalVisible = ref(false);
const accionDetalle = ref({});
const evidenciasAccion = ref([]);

const openDetalleModal = async (accion) => {
    accionDetalle.value = accion;

    // Procesar las evidencias si existen
    if (accion.accion_ruta_evidencia) {
        try {
            const decodedEvidencias = JSON.parse(accion.accion_ruta_evidencia);
            if (Array.isArray(decodedEvidencias)) {
                evidenciasAccion.value = decodedEvidencias.map(e => {
                    // Obtener el tamaño aproximado del archivo (en bytes)
                    // En una implementación real, esto podría venir del backend
                    return {
                        ...e,
                        size: e.name ? e.name.length * 1000 : 0 // Valor estimado
                    };
                });
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
    // Extraer la fecha del path si está disponible en el formato
    // Por ejemplo, si el path contiene fecha, extraerla
    return path ? new Date().toLocaleDateString() : 'N/A'; // En una implementación real, extraer de la ruta
};

const getDownloadUrl = (path) => {
    // Enviar al endpoint de descarga
    // Este endpoint debe configurarse en el backend
    return `/acciones/evidencia/${path}`;
};

const refreshAcciones = async () => {
    await hallazgoStore.fetchTodasLasAcciones(props.hallazgoId);
};

const imprimirPlanAccion = () => {
    // Abrir una nueva ventana/tab con el componente de impresión
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
        // Cargar datos en paralelo para mejor rendimiento
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
    min-height: 400px; /* Altura mínima para centrar verticalmente en la pantalla */
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
.p-datatable .p-datatable-tbody > tr > td {
    vertical-align: middle;
}

/* Indicador de vencimiento */
.text-vencido {
    color: #dc3545 !important;
    font-weight: bold;
}
</style>
