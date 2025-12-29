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
                <SkeletonLoader v-if="isLoading" type="card" :count="4" />

                <EmptyState 
                    v-else-if="!isLoading && planes.length === 0"
                    title="No hay planes de continuidad"
                    description="Crea planes BCP, DRP o de respuesta a incidentes"
                    icon="fas fa-shield-alt"
                    action-text="Nuevo Plan"
                    @action="showModal = true"
                />

                <div v-else class="row">
                    <div v-for="plan in planes" :key="plan.id" class="col-md-6 mb-3">
                        <div class="card h-100" :class="{ 'border-warning': plan.necesita_revision }">
                            <div class="card-header d-flex justify-content-between align-items-center"
                                 :class="'bg-' + plan.estado_color">
                                <span class="text-white">
                                    <strong>{{ plan.codigo }}</strong> - {{ tiposPlan[plan.tipo_plan] }}
                                </span>
                                <span class="badge badge-light">{{ plan.version }}</span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ plan.nombre }}</h5>
                                <p class="card-text text-muted small">{{ plan.objetivo?.substring(0, 150) }}...</p>
                                
                                <div class="row text-center mt-3">
                                    <div class="col-4">
                                        <i class="fas fa-user text-primary"></i><br>
                                        <small>{{ plan.responsable?.name || '-' }}</small>
                                    </div>
                                    <div class="col-4">
                                        <i class="fas fa-calendar-check text-success"></i><br>
                                        <small>{{ formatDate(plan.fecha_aprobacion) }}</small>
                                    </div>
                                    <div class="col-4">
                                        <i class="fas fa-vial text-info"></i><br>
                                        <small>{{ plan.ultima_prueba ? formatDate(plan.ultima_prueba.fecha_ejecucion) : 'Sin probar' }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <button class="btn btn-sm btn-info mr-1" @click="verDetalle(plan)">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                                <button class="btn btn-sm btn-warning mr-1" @click="editar(plan)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <span v-if="plan.necesita_revision" class="badge badge-warning float-right">
                                    <i class="fas fa-exclamation-triangle"></i> Requiere revisión
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <PlanContinuidadModal 
            v-if="showModal"
            :plan="planEditar"
            @saved="onSaved"
            @close="cerrarModal"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useContinuidadStore } from '@/stores/continuidadStore';
import SkeletonLoader from '@/components/generales/SkeletonLoader.vue';
import EmptyState from '@/components/generales/EmptyState.vue';
import PlanContinuidadModal from './PlanContinuidadModal.vue';

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
