<template>
    <div class="container-fluid">
        <!-- Alertas -->
        <div v-if="successMessage" class="alert alert-success alert-dismissible fade show">
            {{ successMessage }}
            <button type="button" class="close" @click="successMessage = ''">×</button>
        </div>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Alta Dirección</li>
                <li class="breadcrumb-item active">Revisión por la Dirección</li>
            </ol>
        </nav>

        <!-- Card Principal -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-users-cog mr-2"></i>
                            Revisiones por la Dirección
                        </h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <button class="btn btn-primary" @click="openCreateModal">
                            <i class="fas fa-plus-circle mr-1"></i> Nueva Revisión
                        </button>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="row mt-3">
                    <div class="col-md-3">
                        <input type="text" v-model="filtros.buscar" class="form-control"
                            placeholder="Buscar por código o título...">
                    </div>
                    <div class="col-md-2">
                        <select v-model="filtros.anio" class="form-control">
                            <option value="">Todos los años</option>
                            <option v-for="a in aniosDisponibles" :key="a" :value="a">{{ a }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select v-model="filtros.estado" class="form-control">
                            <option value="">Todos los estados</option>
                            <option value="programada">Programada</option>
                            <option value="en_preparacion">En Preparación</option>
                            <option value="realizada">Realizada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-secondary" @click="buscar">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Loading Skeleton -->
                <SkeletonLoader v-if="isLoading" type="table" :rows="5" :columns="6" />

                <!-- Empty State -->
                <EmptyState 
                    v-else-if="!isLoading && revisiones.length === 0"
                    title="No hay revisiones registradas"
                    description="Comienza programando una nueva revisión por la dirección"
                    icon="fas fa-calendar-plus"
                    action-text="Nueva Revisión"
                    @action="openCreateModal"
                />

                <!-- Tabla de Revisiones -->
                <div v-else class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Código</th>
                                <th>Título</th>
                                <th>Fecha Programada</th>
                                <th>Periodo</th>
                                <th>Estado</th>
                                <th>Compromisos</th>
                                <th>Avance</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="revision in revisiones" :key="revision.id">
                                <td>
                                    <strong>{{ revision.codigo }}</strong>
                                </td>
                                <td>{{ revision.titulo }}</td>
                                <td>{{ formatDate(revision.fecha_programada) }}</td>
                                <td>{{ revision.periodo }}</td>
                                <td>
                                    <span class="badge" :class="'badge-' + revision.estado_color">
                                        {{ getEstadoLabel(revision.estado) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-secondary">
                                        {{ revision.compromisos?.length || 0 }}
                                    </span>
                                    <span v-if="revision.compromisos_pendientes_count > 0" 
                                          class="badge badge-warning ml-1">
                                        {{ revision.compromisos_pendientes_count }} pendientes
                                    </span>
                                </td>
                                <td>
                                    <div class="progress progress-sm" style="height: 10px;">
                                        <div class="progress-bar bg-info" 
                                             :style="{ width: revision.avance_general + '%' }">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ revision.avance_general }}%</small>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info mr-1" 
                                            @click="verDetalle(revision)" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning mr-1" 
                                            @click="editarRevision(revision)" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" 
                                            @click="confirmarEliminar(revision)" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal de Creación/Edición -->
        <RevisionDireccionModal 
            v-if="modalVisible"
            @saved="onRevisionSaved"
            @close="closeModal"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useRevisionDireccionStore } from '@/stores/revisionDireccionStore';
import SkeletonLoader from '@/components/generales/SkeletonLoader.vue';
import EmptyState from '@/components/generales/EmptyState.vue';
import RevisionDireccionModal from './RevisionDireccionModal.vue';

const router = useRouter();
const store = useRevisionDireccionStore();

const successMessage = ref('');
const filtros = reactive({
    buscar: '',
    anio: '',
    estado: ''
});

// Computed
const isLoading = computed(() => store.isLoading);
const revisiones = computed(() => store.revisionesOrdenadas);
const modalVisible = computed(() => store.modalVisible);

const aniosDisponibles = computed(() => {
    const currentYear = new Date().getFullYear();
    return [currentYear + 1, currentYear, currentYear - 1, currentYear - 2];
});

// Métodos
const buscar = async () => {
    await store.fetchRevisiones(filtros);
};

const openCreateModal = () => {
    store.openModal('create');
};

const editarRevision = (revision) => {
    store.openModal('edit', revision);
};

const verDetalle = (revision) => {
    router.push({ name: 'revision-direccion.detalle', params: { id: revision.id } });
};

const closeModal = () => {
    store.closeModal();
};

const onRevisionSaved = async (mensaje) => {
    successMessage.value = mensaje;
    await buscar();
    setTimeout(() => { successMessage.value = ''; }, 5000);
};

const confirmarEliminar = async (revision) => {
    if (confirm(`¿Está seguro de eliminar la revisión ${revision.codigo}?`)) {
        try {
            await store.deleteRevision(revision.id);
            successMessage.value = 'Revisión eliminada exitosamente';
        } catch (err) {
            alert('Error al eliminar la revisión');
        }
    }
};

const formatDate = (date) => {
    if (!date) return '-';
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

// Lifecycle
onMounted(() => {
    buscar();
});
</script>

<style scoped>
.progress-sm {
    height: 8px;
}
.badge {
    font-size: 0.85em;
}
</style>
