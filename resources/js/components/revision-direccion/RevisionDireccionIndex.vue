<template>
    <div class="container-fluid">
        <!-- Alertas -->
        <div v-if="successMessage" class="alert alert-success alert-dismissible fade show">
            {{ successMessage }}
            <button type="button" class="close" @click="successMessage = ''">×</button>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-4">
                <li class="breadcrumb-item"><router-link :to="{ name: 'home' }">Inicio</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Revisión por la Dirección</li>
            </ol>
        </nav>

        <!-- Card Principal -->
        <div class="card shadow-sm border-0 rounded-xl mb-4">
            <div class="card-header bg-white border-0 py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="card-title font-weight-bold text-dark">
                            <i class="fas fa-users-cog mr-2 text-primary"></i>
                            Revisiones por la Dirección
                        </h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <button class="btn btn-primary rounded-pill px-4 shadow-sm" @click="openCreateModal">
                            <i class="fas fa-plus-circle mr-1"></i> Nueva Revisión
                        </button>
                    </div>
                </div>
                <hr class="my-3">
                <div class="row">
                    <div class="col-md-12">
                        <form @submit.prevent="buscar">
                            <div class="form-row">
                                <div class="col">
                                    <div class="input-group bg-light rounded-lg overflow-hidden border">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-transparent border-0"><i
                                                    class="fas fa-search text-muted"></i></span>
                                        </div>
                                        <input type="text" v-model="filtros.buscar"
                                            class="form-control border-0 bg-transparent"
                                            placeholder="Buscar por código o título...">
                                    </div>
                                </div>
                                <div class="col">
                                    <select v-model="filtros.anio" class="form-control bg-light border rounded-lg">
                                        <option value="">Todos los años</option>
                                        <option v-for="a in aniosDisponibles" :key="a" :value="a">{{ a }}</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select v-model="filtros.estado" class="form-control bg-light border rounded-lg">
                                        <option value="">Todos los estados</option>
                                        <option value="programada">Programada</option>
                                        <option value="en_preparacion">En Preparación</option>
                                        <option value="realizada">Realizada</option>
                                        <option value="cancelada">Cancelada</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn bg-dark btn-block shadow-sm rounded-lg">
                                        <i class="fas fa-filter mr-1"></i> Filtrar
                                    </button>
                                </div>
                            </div>
                        </form>
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
                        responsiveLayout="scroll" class="p-datatable-sm p-datatable-striped">
                        <Column field="codigo" header="Código">
                            <template #body="{ data }">
                                <strong class="text-primary">{{ data.codigo }}</strong>
                            </template>
                        </Column>
                        <Column field="titulo" header="Título"></Column>
                        <Column field="fecha_programada" header="Fecha Programada">
                            <template #body="{ data }">
                                <i class="far fa-calendar-alt mr-1 text-muted"></i>
                                {{ formatDate(data.fecha_programada) }}
                            </template>
                        </Column>
                        <Column field="periodo" header="Periodo"></Column>
                        <Column header="Estado">
                            <template #body="{ data }">
                                <span class="badge px-3 py-2 rounded-pill" :class="'badge-' + data.estado_color">
                                    {{ getEstadoLabel(data.estado) }}
                                </span>
                            </template>
                        </Column>
                        <Column header="Compromisos" class="text-center">
                            <template #body="{ data }">
                                <span class="badge badge-secondary">
                                    {{ data.compromisos?.length || 0 }} total
                                </span>
                                <span v-if="data.compromisos_pendientes_count > 0" class="badge badge-warning ml-1">
                                    {{ data.compromisos_pendientes_count }} pendientes
                                </span>
                                <span v-else-if="data.compromisos?.length > 0" class="badge badge-success ml-1">
                                    <i class="fas fa-check mr-1"></i> Todo al día
                                </span>
                            </template>
                        </Column>
                        <Column header="Avance">
                            <template #body="{ data }">
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 mr-2" style="height: 6px;">
                                        <div class="progress-bar rounded-pill"
                                            :class="'bg-' + getAvanceColor(data.avance_general)"
                                            :style="{ width: data.avance_general + '%' }">
                                        </div>
                                    </div>
                                    <span class="small font-weight-bold text-dark ml-2">{{ data.avance_general
                                        }}%</span>
                                </div>
                            </template>
                        </Column>
                        <Column header="Acciones" headerStyle="width: 120px" bodyStyle="text-align: center">
                            <template #body="{ data }">
                                <div class="d-flex justify-content-center">
                                    <button class="btn btn-sm btn-light-info mr-1" @click="verDetalle(data)"
                                        title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light-warning mr-1" @click="editarRevision(data)"
                                        title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" @click="confirmarEliminar(data)"
                                        title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
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
        aprobada: 'Aprobada',
        en_preparacion: 'En Preparación',
        realizada: 'Realizada',
        cancelada: 'Cancelada'
    };
    return labels[estado] || estado;
};

const getAvanceColor = (avance) => {
    if (avance < 30) return 'danger';
    if (avance < 70) return 'warning';
    return 'success';
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
