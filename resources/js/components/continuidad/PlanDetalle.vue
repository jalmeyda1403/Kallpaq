<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light py-2 px-3 rounded">
                <li class="breadcrumb-item"><router-link to="/home">Inicio</router-link></li>
                <li class="breadcrumb-item">Continuidad</li>
                <li class="breadcrumb-item">
                    <router-link to="/continuidad/planes">Planes</router-link>
                </li>
                <li class="breadcrumb-item active">{{ plan?.codigo }}</li>
            </ol>
        </nav>

        <LoadingState v-if="isLoading" text="Cargando plan..." />

        <template v-else-if="plan">
            <!-- Header -->
            <div class="card">
                <div class="card-header" :class="'bg-' + plan.estado_color + ' text-white'">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-0">{{ plan.codigo }} - {{ plan.nombre }}</h3>
                            <small>{{ tiposPlan[plan.tipo_plan] }} | v{{ plan.version }}</small>
                        </div>
                        <div class="col-md-4 text-md-right">
                            <span class="badge badge-light mr-2">{{ plan.estado }}</span>
                            <button class="btn btn-light btn-sm" @click="editar">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Objetivo</h5>
                            <p>{{ plan.objetivo }}</p>
                            
                            <h5 v-if="plan.alcance">Alcance</h5>
                            <p v-if="plan.alcance">{{ plan.alcance }}</p>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-info"><i class="fas fa-user"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Responsable</span>
                                    <span class="info-box-number">{{ plan.responsable?.name || '-' }}</span>
                                </div>
                            </div>
                            <div v-if="plan.escenario" class="info-box bg-light">
                                <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Escenario</span>
                                    <span class="info-box-number">{{ plan.escenario.nombre }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs mt-3">
                <li class="nav-item">
                    <a class="nav-link" :class="{ active: activeTab === 'procedimientos' }" 
                       @click="activeTab = 'procedimientos'" href="#">
                        <i class="fas fa-list-ol mr-1"></i> Procedimientos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" :class="{ active: activeTab === 'estrategias' }" 
                       @click="activeTab = 'estrategias'" href="#">
                        <i class="fas fa-chess mr-1"></i> Estrategias
                        <span class="badge badge-secondary">{{ plan.estrategias?.length || 0 }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" :class="{ active: activeTab === 'pruebas' }" 
                       @click="activeTab = 'pruebas'" href="#">
                        <i class="fas fa-vial mr-1"></i> Pruebas
                        <span class="badge badge-secondary">{{ plan.pruebas?.length || 0 }}</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content border border-top-0 p-3 bg-white">
                <!-- Tab Procedimientos -->
                <div v-if="activeTab === 'procedimientos'">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-warning">
                                    <i class="fas fa-play mr-1"></i> Procedimientos de Activación
                                </div>
                                <div class="card-body">
                                    <pre class="mb-0" style="white-space: pre-wrap;">{{ plan.procedimientos_activacion || 'No definido' }}</pre>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <i class="fas fa-sync mr-1"></i> Procedimientos de Recuperación
                                </div>
                                <div class="card-body">
                                    <pre class="mb-0" style="white-space: pre-wrap;">{{ plan.procedimientos_recuperacion || 'No definido' }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header"><i class="fas fa-tools mr-1"></i> Recursos Necesarios</div>
                                <div class="card-body">
                                    <pre class="mb-0" style="white-space: pre-wrap;">{{ plan.recursos_necesarios || 'No definido' }}</pre>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header"><i class="fas fa-bullhorn mr-1"></i> Comunicación de Crisis</div>
                                <div class="card-body">
                                    <pre class="mb-0" style="white-space: pre-wrap;">{{ plan.comunicacion_crisis || 'No definido' }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Estrategias -->
                <div v-if="activeTab === 'estrategias'">
                    <div v-if="!plan.estrategias?.length" class="text-center py-4 text-muted">
                        No hay estrategias definidas para este plan
                    </div>
                    <div v-else class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Activo</th>
                                    <th>Tipo</th>
                                    <th>Prioridad</th>
                                    <th>Tiempo Impl.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="est in plan.estrategias" :key="est.id">
                                    <td>{{ est.nombre }}</td>
                                    <td>{{ est.activo?.nombre || '-' }}</td>
                                    <td>{{ est.tipo_estrategia }}</td>
                                    <td><span class="badge" :class="'badge-' + est.prioridad_color">{{ est.prioridad }}</span></td>
                                    <td>{{ est.tiempo_implementacion ? est.tiempo_implementacion + 'h' : '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab Pruebas -->
                <div v-if="activeTab === 'pruebas'">
                    <div v-if="!plan.pruebas?.length" class="text-center py-4 text-muted">
                        No hay pruebas registradas para este plan
                    </div>
                    <div v-else class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Calificación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="prueba in plan.pruebas" :key="prueba.id">
                                    <td>{{ prueba.codigo }}</td>
                                    <td>{{ prueba.nombre }}</td>
                                    <td>{{ prueba.tipo_prueba }}</td>
                                    <td>{{ formatDate(prueba.fecha_ejecucion || prueba.fecha_programada) }}</td>
                                    <td><span class="badge" :class="'badge-' + prueba.estado_color">{{ prueba.estado }}</span></td>
                                    <td>
                                        <span v-if="prueba.calificacion">
                                            <i v-for="i in 5" :key="i" class="fas fa-star" 
                                               :class="i <= prueba.calificacion ? 'text-warning' : 'text-muted'"></i>
                                        </span>
                                        <span v-else>-</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useContinuidadStore } from '@/stores/continuidadStore';
import LoadingState from '@/components/generales/LoadingState.vue';

const route = useRoute();
const store = useContinuidadStore();

const activeTab = ref('procedimientos');

const isLoading = computed(() => store.isLoading);
const plan = computed(() => store.planActual);
const tiposPlan = computed(() => store.tiposPlan);

const editar = () => {
    // TODO: Implementar edición
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('es-PE');
};

onMounted(async () => {
    await store.fetchTiposPlan();
    await store.fetchPlan(route.params.id);
});
</script>

<style scoped>
.nav-tabs .nav-link { cursor: pointer; }
</style>
