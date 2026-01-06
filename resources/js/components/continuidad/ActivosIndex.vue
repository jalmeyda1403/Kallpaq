<template>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light py-2 px-3 rounded">
                <li class="breadcrumb-item"><router-link to="/home">Inicio</router-link></li>
                <li class="breadcrumb-item">Continuidad de Negocio</li>
                <li class="breadcrumb-item active">Activos Críticos</li>
            </ol>
        </nav>

        <!-- Alert -->
        <div v-if="successMessage" class="alert alert-success alert-dismissible fade show">
            {{ successMessage }}
            <button type="button" class="close" @click="successMessage = ''">×</button>
        </div>

        <!-- Card Principal -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-layer-group mr-2"></i>
                            Activos Críticos
                        </h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <button class="btn btn-light" @click="showModal = true">
                            <i class="fas fa-plus"></i> Nuevo Activo
                        </button>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="row mt-3">
                    <div class="col-md-3">
                        <input type="text" v-model="filtros.buscar" class="form-control" placeholder="Buscar..."
                            @input="buscarDebounced">
                    </div>
                    <div class="col-md-3">
                        <select v-model="filtros.tipo" class="form-control" @change="buscar">
                            <option value="">Todos los tipos</option>
                            <option v-for="(label, value) in tiposActivo" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Empty -->
                <EmptyState v-if="!isLoading && activos.length === 0" title="No hay activos críticos"
                    description="Comienza identificando los activos críticos de tu organización" icon="fas fa-cubes"
                    action-text="Nuevo Activo" @action="showModal = true" />

                <!-- Tabla -->
                <div v-else>
                    <DataTable :value="activos" :loading="isLoading" :paginator="true" :rows="10"
                        responsiveLayout="scroll">
                        <Column field="codigo" header="Código">
                            <template #body="slotProps">
                                <strong>{{ slotProps.data.codigo }}</strong>
                            </template>
                        </Column>
                        <Column field="nombre" header="Nombre"></Column>
                        <Column header="Tipo">
                            <template #body="slotProps">
                                <span class="badge badge-secondary">
                                    {{ tiposActivo[slotProps.data.tipo] || slotProps.data.tipo }}
                                </span>
                            </template>
                        </Column>
                        <Column header="Proceso">
                            <template #body="slotProps">
                                {{ slotProps.data.proceso?.proceso_nombre || '-' }}
                            </template>
                        </Column>
                        <Column header="Criticidad">
                            <template #body="slotProps">
                                <span class="badge"
                                    :class="'badge-' + getCriticidadColor(slotProps.data.nivel_criticidad)">
                                    {{ slotProps.data.nivel_criticidad }}
                                </span>
                            </template>
                        </Column>
                        <Column header="RTO">
                            <template #body="slotProps">
                                {{ formatRTO(slotProps.data.rto) }}
                            </template>
                        </Column>
                        <Column header="Responsable">
                            <template #body="slotProps">
                                {{ slotProps.data.responsable?.name || '-' }}
                            </template>
                        </Column>
                        <Column header="Acciones">
                            <template #body="slotProps">
                                <Button icon="pi pi-pencil" class="p-button-rounded p-button-info p-button-text mr-1"
                                    @click="editar(slotProps.data)" />
                                <Button icon="pi pi-trash" class="p-button-rounded p-button-danger p-button-text"
                                    @click="eliminar(slotProps.data)" />
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <ActivoCriticoModal v-if="showModal" :activo="activoEditar" @saved="onSaved" @close="cerrarModal" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue';
import { useContinuidadStore } from '@/stores/continuidadStore';
import EmptyState from '@/components/generales/EmptyState.vue';
import ActivoCriticoModal from './ActivoCriticoModal.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';

const store = useContinuidadStore();

const showModal = ref(false);
const activoEditar = ref(null);
const successMessage = ref('');
const filtros = reactive({ buscar: '', tipo: '' });

let debounceTimer = null;

const isLoading = computed(() => store.isLoading);
const activos = computed(() => store.activos);
const tiposActivo = computed(() => store.tiposActivo);

const buscar = async () => {
    await store.fetchActivos(filtros);
};

const buscarDebounced = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => buscar(), 300);
};

const editar = (activo) => {
    activoEditar.value = activo;
    showModal.value = true;
};

const cerrarModal = () => {
    showModal.value = false;
    activoEditar.value = null;
};

const onSaved = async (msg) => {
    successMessage.value = msg;
    cerrarModal();
    await buscar();
    setTimeout(() => { successMessage.value = ''; }, 5000);
};

const eliminar = async (activo) => {
    if (confirm(`¿Eliminar el activo ${activo.codigo}?`)) {
        await store.deleteActivo(activo.id);
        successMessage.value = 'Activo eliminado';
    }
};

const getCriticidadColor = (nivel) => {
    const colores = { bajo: 'success', medio: 'warning', alto: 'orange', critico: 'danger' };
    return colores[nivel] || 'secondary';
};

const formatRTO = (horas) => {
    if (!horas) return '-';
    if (horas < 24) return `${horas}h`;
    return `${Math.round(horas / 24)}d`;
};

onMounted(async () => {
    await store.fetchTiposActivo();
    await buscar();
});
</script>

<style scoped>
.badge-orange {
    background-color: #fd7e14;
    color: white;
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
