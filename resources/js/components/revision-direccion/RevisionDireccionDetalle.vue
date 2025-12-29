<template>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <router-link to="/home">Home</router-link>
                </li>
                <li class="breadcrumb-item">
                    <router-link to="/revision-direccion">Revisiones por la Dirección</router-link>
                </li>
                <li class="breadcrumb-item active">{{ revision?.codigo }}</li>
            </ol>
        </nav>

        <!-- Alert de éxito -->
        <div v-if="successMessage" class="alert alert-success alert-dismissible fade show">
            {{ successMessage }}
            <button type="button" class="close" @click="successMessage = ''">×</button>
        </div>

        <!-- Loading State -->
        <LoadingState v-if="isLoading" text="Cargando revisión..." />

        <!-- Error State -->
        <ErrorState 
            v-else-if="error" 
            :message="error" 
            @retry="cargarRevision" 
        />

        <!-- Contenido Principal -->
        <template v-else-if="revision">
            <!-- Header Card -->
            <div class="card">
                <div class="card-header" :class="'bg-' + getEstadoColor(revision.estado)">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="card-title text-white mb-0">
                                <i class="fas fa-calendar-check mr-2"></i>
                                {{ revision.codigo }} - {{ revision.titulo }}
                            </h3>
                            <small class="text-white-50">
                                {{ getEstadoLabel(revision.estado) }} | 
                                Periodo: {{ revision.periodo }}-{{ revision.anio }}
                            </small>
                        </div>
                        <div class="col-md-4 text-md-right">
                            <button class="btn btn-light btn-sm mr-1" @click="editarRevision">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button class="btn btn-light btn-sm" @click="subirActa" v-if="revision.estado === 'realizada'">
                                <i class="fas fa-file-upload"></i> Subir Acta
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Fecha Programada:</strong><br>
                            {{ formatDate(revision.fecha_programada) }}
                        </div>
                        <div class="col-md-3">
                            <strong>Fecha Reunión:</strong><br>
                            {{ formatDate(revision.fecha_reunion) || 'Pendiente' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Responsable:</strong><br>
                            {{ revision.responsable?.name || '-' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Avance General:</strong><br>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-info" 
                                     :style="{ width: (revision.avance_general || 0) + '%' }">
                                    {{ revision.avance_general || 0 }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-if="revision.participantes" class="mt-3">
                        <strong>Participantes:</strong>
                        <p class="mb-0">{{ revision.participantes }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabs de Contenido -->
            <ul class="nav nav-tabs mt-3">
                <li class="nav-item">
                    <a class="nav-link" :class="{ active: activeTab === 'entradas' }" 
                       @click="activeTab = 'entradas'" href="#">
                        <i class="fas fa-sign-in-alt mr-1"></i> Entradas
                        <span class="badge badge-secondary">{{ revision.entradas?.length || 0 }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" :class="{ active: activeTab === 'salidas' }" 
                       @click="activeTab = 'salidas'" href="#">
                        <i class="fas fa-sign-out-alt mr-1"></i> Salidas/Decisiones
                        <span class="badge badge-secondary">{{ revision.salidas?.length || 0 }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" :class="{ active: activeTab === 'compromisos' }" 
                       @click="activeTab = 'compromisos'" href="#">
                        <i class="fas fa-tasks mr-1"></i> Compromisos
                        <span class="badge badge-warning">{{ compromisosPendientes.length }}</span>
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content border border-top-0 p-3 bg-white">
                <!-- Tab Entradas -->
                <div v-if="activeTab === 'entradas'">
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Entradas de la Revisión (ISO 9001 §9.3.2)</h5>
                        <button class="btn btn-primary btn-sm" @click="showEntradaModal = true">
                            <i class="fas fa-plus"></i> Agregar Entrada
                        </button>
                    </div>

                    <div v-if="!revision.entradas?.length" class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-3x mb-2"></i>
                        <p>No hay entradas registradas</p>
                        <button class="btn btn-outline-primary" @click="showEntradaModal = true">
                            <i class="fas fa-plus mr-1"></i> Agregar primera entrada
                        </button>
                    </div>

                    <div v-else class="list-group">
                        <div v-for="entrada in revision.entradas" :key="entrada.id" 
                             class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <i :class="getEntradaIcon(entrada.tipo_entrada)" class="mr-2 text-primary"></i>
                                        {{ entrada.titulo }}
                                    </h6>
                                    <small class="text-muted">{{ tiposEntrada[entrada.tipo_entrada] || entrada.tipo_entrada }}</small>
                                    <p class="mb-0 mt-2">{{ entrada.descripcion }}</p>
                                    <div v-if="entrada.conclusion" class="mt-2 p-2 bg-light rounded">
                                        <strong>Conclusión:</strong> {{ entrada.conclusion }}
                                    </div>
                                </div>
                                <span class="badge" :class="getEntradaEstadoClass(entrada.estado)">
                                    {{ entrada.estado }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Salidas -->
                <div v-if="activeTab === 'salidas'">
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Salidas de la Revisión (ISO 9001 §9.3.3)</h5>
                        <button class="btn btn-success btn-sm" @click="showSalidaModal = true">
                            <i class="fas fa-plus"></i> Agregar Salida
                        </button>
                    </div>

                    <div v-if="!revision.salidas?.length" class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-3x mb-2"></i>
                        <p>No hay salidas registradas</p>
                        <button class="btn btn-outline-success" @click="showSalidaModal = true">
                            <i class="fas fa-plus mr-1"></i> Agregar primera salida
                        </button>
                    </div>

                    <div v-else class="row">
                        <div v-for="salida in revision.salidas" :key="salida.id" class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white py-2">
                                    <small>{{ tiposSalida[salida.tipo_salida] || salida.tipo_salida }}</small>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ salida.descripcion }}</p>
                                    <div v-if="salida.recursos_necesarios" class="mt-2 small text-muted">
                                        <strong>Recursos:</strong> {{ salida.recursos_necesarios }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Compromisos -->
                <div v-if="activeTab === 'compromisos'">
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Compromisos de la Dirección</h5>
                        <button class="btn btn-warning btn-sm" @click="abrirCompromisoModal(null)">
                            <i class="fas fa-plus"></i> Agregar Compromiso
                        </button>
                    </div>

                    <div v-if="!revision.compromisos?.length" class="text-center py-4 text-muted">
                        <i class="fas fa-check-circle fa-3x mb-2"></i>
                        <p>No hay compromisos registrados</p>
                        <button class="btn btn-outline-warning" @click="abrirCompromisoModal(null)">
                            <i class="fas fa-plus mr-1"></i> Agregar primer compromiso
                        </button>
                    </div>

                    <div v-else class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Descripción</th>
                                    <th>Responsable</th>
                                    <th>Fecha Límite</th>
                                    <th>Estado</th>
                                    <th>Avance</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="compromiso in revision.compromisos" :key="compromiso.id"
                                    :class="{ 'table-danger': compromiso.estado === 'vencido' }">
                                    <td><strong>{{ compromiso.codigo }}</strong></td>
                                    <td>{{ compromiso.descripcion }}</td>
                                    <td>{{ compromiso.responsable?.name || '-' }}</td>
                                    <td>
                                        {{ formatDate(compromiso.fecha_limite) }}
                                        <span v-if="compromiso.dias_restantes !== null && compromiso.dias_restantes <= 7 && compromiso.dias_restantes >= 0" 
                                              class="badge badge-warning ml-1">
                                            {{ compromiso.dias_restantes }} días
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge" :class="getCompromisoEstadoClass(compromiso.estado)">
                                            {{ compromiso.estado }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress" style="width: 80px; height: 8px;">
                                            <div class="progress-bar bg-info" 
                                                 :style="{ width: (compromiso.avance || 0) + '%' }"></div>
                                        </div>
                                        <small>{{ compromiso.avance || 0 }}%</small>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info mr-1" 
                                                @click="verSeguimientos(compromiso)" title="Seguimiento">
                                            <i class="fas fa-history"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" 
                                                @click="abrirCompromisoModal(compromiso)" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </template>

        <!-- Modales -->
        <EntradaModal 
            v-if="showEntradaModal"
            :revision-id="revision?.id"
            @saved="onEntradaSaved"
            @close="showEntradaModal = false"
        />

        <SalidaModal 
            v-if="showSalidaModal"
            :revision-id="revision?.id"
            @saved="onSalidaSaved"
            @close="showSalidaModal = false"
        />

        <CompromisoModal 
            v-if="showCompromisoModal"
            :revision-id="revision?.id"
            :compromiso="compromisoEditar"
            @saved="onCompromisoSaved"
            @close="cerrarCompromisoModal"
        />

        <SeguimientoModal 
            v-if="showSeguimientoModal"
            :compromiso="compromisoSeguimiento"
            @updated="cargarRevision"
            @close="showSeguimientoModal = false"
        />

        <!-- Modal para editar revisión -->
        <RevisionDireccionModal 
            v-if="showRevisionModal"
            :revision="revision"
            @saved="onRevisionSaved"
            @close="showRevisionModal = false"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useRevisionDireccionStore } from '@/stores/revisionDireccionStore';
import LoadingState from '@/components/generales/LoadingState.vue';
import ErrorState from '@/components/generales/ErrorState.vue';
import EntradaModal from './EntradaModal.vue';
import SalidaModal from './SalidaModal.vue';
import CompromisoModal from './CompromisoModal.vue';
import SeguimientoModal from './SeguimientoModal.vue';
import RevisionDireccionModal from './RevisionDireccionModal.vue';

const route = useRoute();
const router = useRouter();
const store = useRevisionDireccionStore();

const activeTab = ref('entradas');
const successMessage = ref('');

// Estados de modales
const showEntradaModal = ref(false);
const showSalidaModal = ref(false);
const showCompromisoModal = ref(false);
const showSeguimientoModal = ref(false);
const showRevisionModal = ref(false);

// Datos para modales
const compromisoEditar = ref(null);
const compromisoSeguimiento = ref(null);

// Computed
const isLoading = computed(() => store.isLoading);
const error = computed(() => store.error);
const revision = computed(() => store.revisionActual);
const tiposEntrada = computed(() => store.tiposEntrada);
const tiposSalida = computed(() => store.tiposSalida);

const compromisosPendientes = computed(() => {
    if (!revision.value?.compromisos) return [];
    return revision.value.compromisos.filter(c => 
        ['pendiente', 'en_proceso', 'vencido'].includes(c.estado)
    );
});

// Métodos
const cargarRevision = async () => {
    await store.fetchRevision(route.params.id);
    store.fetchTiposEntrada();
    store.fetchTiposSalida();
};

const editarRevision = () => {
    showRevisionModal.value = true;
};

const subirActa = () => {
    alert('Subir acta - Por implementar');
};

const abrirCompromisoModal = (compromiso) => {
    compromisoEditar.value = compromiso;
    showCompromisoModal.value = true;
};

const cerrarCompromisoModal = () => {
    showCompromisoModal.value = false;
    compromisoEditar.value = null;
};

const verSeguimientos = (compromiso) => {
    compromisoSeguimiento.value = compromiso;
    showSeguimientoModal.value = true;
};

// Eventos
const onEntradaSaved = (msg) => {
    successMessage.value = msg;
    showEntradaModal.value = false;
    setTimeout(() => { successMessage.value = ''; }, 5000);
};

const onSalidaSaved = (msg) => {
    successMessage.value = msg;
    showSalidaModal.value = false;
    setTimeout(() => { successMessage.value = ''; }, 5000);
};

const onCompromisoSaved = (msg) => {
    successMessage.value = msg;
    cerrarCompromisoModal();
    cargarRevision(); // Recargar para obtener datos actualizados
    setTimeout(() => { successMessage.value = ''; }, 5000);
};

const onRevisionSaved = () => {
    successMessage.value = 'Revisión actualizada exitosamente';
    showRevisionModal.value = false;
    cargarRevision();
    setTimeout(() => { successMessage.value = ''; }, 5000);
};

// Helpers
const formatDate = (date) => {
    if (!date) return null;
    return new Date(date).toLocaleDateString('es-PE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const getEstadoLabel = (estado) => {
    const labels = {
        programada: 'Programada',
        en_preparacion: 'En Preparación',
        realizada: 'Realizada',
        cancelada: 'Cancelada'
    };
    return labels[estado] || estado;
};

const getEstadoColor = (estado) => {
    const colors = {
        programada: 'info',
        en_preparacion: 'warning',
        realizada: 'success',
        cancelada: 'secondary'
    };
    return colors[estado] || 'primary';
};

const getEntradaIcon = (tipo) => {
    const icons = {
        estado_acciones_anteriores: 'fas fa-history',
        cambios_contexto_externo: 'fas fa-globe',
        cambios_contexto_interno: 'fas fa-building',
        retroalimentacion_partes_interesadas: 'fas fa-comments',
        desempeno_procesos: 'fas fa-chart-line',
        no_conformidades_acciones_correctivas: 'fas fa-exclamation-triangle',
        resultados_auditorias: 'fas fa-clipboard-check',
        satisfaccion_cliente: 'fas fa-smile',
        oportunidades_mejora: 'fas fa-lightbulb',
        recursos: 'fas fa-cubes',
        eficacia_acciones_riesgos: 'fas fa-shield-alt',
        desempeno_proveedores: 'fas fa-truck',
    };
    return icons[tipo] || 'fas fa-file-alt';
};

const getEntradaEstadoClass = (estado) => {
    return {
        pendiente: 'badge-warning',
        revisado: 'badge-info',
        aprobado: 'badge-success'
    }[estado] || 'badge-secondary';
};

const getCompromisoEstadoClass = (estado) => {
    return {
        pendiente: 'badge-warning',
        en_proceso: 'badge-info',
        completado: 'badge-success',
        vencido: 'badge-danger',
        cancelado: 'badge-secondary'
    }[estado] || 'badge-secondary';
};

// Lifecycle
onMounted(() => {
    cargarRevision();
});
</script>

<style scoped>
.nav-tabs .nav-link {
    cursor: pointer;
}
.nav-tabs .nav-link.active {
    font-weight: 600;
}
</style>

