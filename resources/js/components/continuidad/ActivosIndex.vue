<template>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
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
                        <input type="text" v-model="filtros.buscar" class="form-control"
                            placeholder="Buscar..." @input="buscarDebounced">
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
                <!-- Loading -->
                <SkeletonLoader v-if="isLoading" type="table" :rows="5" :columns="7" />

                <!-- Empty -->
                <EmptyState 
                    v-else-if="!isLoading && activos.length === 0"
                    title="No hay activos críticos"
                    description="Comienza identificando los activos críticos de tu organización"
                    icon="fas fa-cubes"
                    action-text="Nuevo Activo"
                    @action="showModal = true"
                />

                <!-- Tabla -->
                <div v-else class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Proceso</th>
                                <th>Criticidad</th>
                                <th>RTO</th>
                                <th>Responsable</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="activo in activos" :key="activo.id">
                                <td><strong>{{ activo.codigo }}</strong></td>
                                <td>{{ activo.nombre }}</td>
                                <td>
                                    <span class="badge badge-secondary">
                                        {{ tiposActivo[activo.tipo] || activo.tipo }}
                                    </span>
                                </td>
                                <td>{{ activo.proceso?.proceso_nombre || '-' }}</td>
                                <td>
                                    <span class="badge" :class="'badge-' + getCriticidadColor(activo.nivel_criticidad)">
                                        {{ activo.nivel_criticidad }}
                                    </span>
                                </td>
                                <td>{{ formatRTO(activo.rto) }}</td>
                                <td>{{ activo.responsable?.name || '-' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info mr-1" @click="editar(activo)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" @click="eliminar(activo)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <ActivoCriticoModal 
            v-if="showModal"
            :activo="activoEditar"
            @saved="onSaved"
            @close="cerrarModal"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue';
import { useContinuidadStore } from '@/stores/continuidadStore';
import SkeletonLoader from '@/components/generales/SkeletonLoader.vue';
import EmptyState from '@/components/generales/EmptyState.vue';
import ActivoCriticoModal from './ActivoCriticoModal.vue';

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
</style>
