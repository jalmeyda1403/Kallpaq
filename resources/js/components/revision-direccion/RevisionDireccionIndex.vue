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
                <!-- Empty State -->
                <EmptyState v-if="!isLoading && revisiones.length === 0" title="No hay revisiones registradas"
                    description="Comienza programando una nueva revisión por la dirección" icon="fas fa-calendar-plus"
                    action-text="Nueva Revisión" @action="openCreateModal" />

                <!-- Tabla de Revisiones -->
                <div v-else>
                    <DataTable :value="revisiones" :loading="isLoading" :paginator="true" :rows="10"
                        responsiveLayout="scroll">
                        <Column field="codigo" header="Código">
                            <template #body="{ data }">
                                <strong>{{ data.codigo }}</strong>
                            </template>
                        </Column>
                        <Column field="titulo" header="Título"></Column>
                        <Column field="fecha_programada" header="Fecha Programada">
                            <template #body="{ data }">
                                {{ formatDate(data.fecha_programada) }}
                            </template>
                        </Column>
                        <Column field="periodo" header="Periodo"></Column>
                        <Column header="Estado">
                            <template #body="{ data }">
                                <span class="badge" :class="'badge-' + data.estado_color">
                                    {{ getEstadoLabel(data.estado) }}
                                </span>
                            </template>
                        </Column>
                        <Column header="Compromisos" class="text-center">
                            <template #body="{ data }">
                                <span class="badge badge-secondary">
                                    {{ data.compromisos?.length || 0 }}
                                </span>
                                <span v-if="data.compromisos_pendientes_count > 0" class="badge badge-warning ml-1">
                                    {{ data.compromisos_pendientes_count }} pendientes
                                </span>
                            </template>
                        </Column>
                        <Column header="Avance">
                            <template #body="{ data }">
                                <div class="progress progress-sm" style="height: 10px;">
                                    <div class="progress-bar bg-info" :style="{ width: data.avance_general + '%' }">
                                    </div>
                                </div>
                                <small class="text-muted">{{ data.avance_general }}%</small>
                            </template>
                        </Column>
                        <Column header="Acciones">
                            <template #body="{ data }">
                                <Button icon="pi pi-eye" class="p-button-rounded p-button-info p-button-text mr-1"
                                    @click="verDetalle(data)" title="Ver detalle" />
                                <Button icon="pi pi-pencil" class="p-button-rounded p-button-warning p-button-text mr-1"
                                    @click="editarRevision(data)" title="Editar" />
                                <Button icon="pi pi-trash" class="p-button-rounded p-button-danger p-button-text"
                                    @click="confirmarEliminar(data)" title="Eliminar" />
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>

        <!-- Modal de Creación/Edición -->
        <RevisionDireccionModal v-if="modalVisible" @saved="onRevisionSaved" @close="closeModal" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useRevisionDireccionStore } from '@/stores/revisionDireccionStore';
import EmptyState from '@/components/generales/EmptyState.vue';
import RevisionDireccionModal from './RevisionDireccionModal.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';

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

/* Custom loader styles */
.p-datatable-loading-overlay {
    background: rgba(255, 255, 255, 0) !important;
}

.p-datatable-loading-icon {
    color: red !important;
    font-size: 2rem !important;
}
</style>
