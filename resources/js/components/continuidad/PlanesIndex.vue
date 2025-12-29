<template>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Continuidad de Negocio</li>
                <li class="breadcrumb-item active">Planes de Continuidad</li>
            </ol>
        </nav>

        <div v-if="successMessage" class="alert alert-success alert-dismissible fade show">
            {{ successMessage }}
            <button type="button" class="close" @click="successMessage = ''">×</button>
        </div>

        <div class="card">
            <div class="card-header bg-success text-white">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-file-medical mr-2"></i>
                            Planes de Continuidad
                        </h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <button class="btn btn-light" @click="showModal = true">
                            <i class="fas fa-plus"></i> Nuevo Plan
                        </button>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <select v-model="filtros.tipo_plan" class="form-control" @change="buscar">
                            <option value="">Todos los tipos</option>
                            <option v-for="(label, value) in tiposPlan" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select v-model="filtros.estado" class="form-control" @change="buscar">
                            <option value="">Todos los estados</option>
                            <option value="borrador">Borrador</option>
                            <option value="en_revision">En Revisión</option>
                            <option value="aprobado">Aprobado</option>
                            <option value="obsoleto">Obsoleto</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Empty -->
                <EmptyState v-if="!isLoading && planes.length === 0" title="No hay planes de continuidad"
                    description="Crea planes BCP, DRP o de respuesta a incidentes" icon="fas fa-shield-alt"
                    action-text="Nuevo Plan" @action="showModal = true" />

                <div v-else>
                    <DataTable :value="planes" :loading="isLoading" :paginator="true" :rows="10"
                        responsiveLayout="scroll">
                        <Column field="codigo" header="Código">
                            <template #body="{ data }">
                                <strong>{{ data.codigo }}</strong>
                            </template>
                        </Column>
                        <Column field="tipo_plan" header="Tipo">
                            <template #body="{ data }">
                                {{ tiposPlan[data.tipo_plan] }}
                            </template>
                        </Column>
                        <Column field="nombre" header="Nombre"></Column>
                        <Column field="version" header="Versión">
                            <template #body="{ data }">
                                <span class="badge badge-light">{{ data.version }}</span>
                            </template>
                        </Column>
                        <Column field="responsable.name" header="Responsable">
                            <template #body="{ data }">
                                {{ data.responsable?.name || '-' }}
                            </template>
                        </Column>
                        <Column field="fecha_aprobacion" header="Aprobado">
                            <template #body="{ data }">
                                {{ formatDate(data.fecha_aprobacion) }}
                            </template>
                        </Column>
                        <Column header="Última Prueba">
                            <template #body="{ data }">
                                {{ data.ultima_prueba ? formatDate(data.ultima_prueba.fecha_ejecucion) : 'Sin probar' }}
                            </template>
                        </Column>
                        <Column header="Estado">
                            <template #body="{ data }">
                                <span class="badge"
                                    :class="'badge-' + (data.necesita_revision ? 'warning' : 'success')">
                                    {{ data.necesita_revision ? 'Requiere Revisión' : 'OK' }}
                                </span>
                            </template>
                        </Column>
                        <Column header="Acciones">
                            <template #body="{ data }">
                                <Button icon="pi pi-eye" class="p-button-rounded p-button-info p-button-text mr-1"
                                    @click="verDetalle(data)" />
                                <Button icon="pi pi-pencil" class="p-button-rounded p-button-warning p-button-text"
                                    @click="editar(data)" />
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>

        <PlanContinuidadModal v-if="showModal" :plan="planEditar" @saved="onSaved" @close="cerrarModal" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useContinuidadStore } from '@/stores/continuidadStore';
import EmptyState from '@/components/generales/EmptyState.vue';
import PlanContinuidadModal from './PlanContinuidadModal.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';

const router = useRouter();
const store = useContinuidadStore();

const showModal = ref(false);
const planEditar = ref(null);
const successMessage = ref('');
const filtros = reactive({ tipo_plan: '', estado: '' });

const isLoading = computed(() => store.isLoading);
const planes = computed(() => store.planes);
const tiposPlan = computed(() => store.tiposPlan);

const buscar = async () => {
    await store.fetchPlanes(filtros);
};

const editar = (plan) => {
    planEditar.value = plan;
    showModal.value = true;
};

const verDetalle = (plan) => {
    router.push({ name: 'continuidad.plan.detalle', params: { id: plan.id } });
};

const cerrarModal = () => {
    showModal.value = false;
    planEditar.value = null;
};

const onSaved = async (msg) => {
    successMessage.value = msg;
    cerrarModal();
    await buscar();
    setTimeout(() => { successMessage.value = ''; }, 5000);
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('es-PE');
};

onMounted(async () => {
    await store.fetchTiposPlan();
    await buscar();
});
</script>

<style scoped>
/* Custom loader styles */
.p-datatable-loading-overlay {
    background: rgba(255, 255, 255, 0) !important;
}

.p-datatable-loading-icon {
    color: red !important;
    font-size: 2rem !important;
}
</style>
