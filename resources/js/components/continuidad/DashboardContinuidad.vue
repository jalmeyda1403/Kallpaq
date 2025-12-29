<template>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active">Dashboard Continuidad</li>
            </ol>
        </nav>

        <LoadingState v-if="isLoading" text="Cargando dashboard..." />

        <template v-else-if="dashboard">
            <!-- Resumen Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ dashboard.activos?.total || 0 }}</h3>
                            <p>Activos Críticos</p>
                        </div>
                        <div class="icon"><i class="fas fa-layer-group"></i></div>
                        <router-link to="/continuidad/activos" class="small-box-footer">
                            Ver más <i class="fas fa-arrow-circle-right"></i>
                        </router-link>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ dashboard.escenarios?.alto_riesgo || 0 }}</h3>
                            <p>Escenarios Alto Riesgo</p>
                        </div>
                        <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <router-link to="/continuidad/escenarios" class="small-box-footer">
                            Ver más <i class="fas fa-arrow-circle-right"></i>
                        </router-link>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ dashboard.planes?.aprobados || 0 }}</h3>
                            <p>Planes Aprobados</p>
                        </div>
                        <div class="icon"><i class="fas fa-file-medical"></i></div>
                        <router-link to="/continuidad/planes" class="small-box-footer">
                            Ver más <i class="fas fa-arrow-circle-right"></i>
                        </router-link>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ dashboard.pruebas?.completadas_anio || 0 }}</h3>
                            <p>Pruebas este Año</p>
                        </div>
                        <div class="icon"><i class="fas fa-vial"></i></div>
                        <router-link to="/continuidad/pruebas" class="small-box-footer">
                            Ver más <i class="fas fa-arrow-circle-right"></i>
                        </router-link>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Activos por Tipo -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-pie mr-2"></i> Activos por Tipo</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li v-for="item in dashboard.activos?.por_tipo" :key="item.tipo" 
                                    class="list-group-item d-flex justify-content-between">
                                    <span>{{ getTipoActivoLabel(item.tipo) }}</span>
                                    <span class="badge badge-info">{{ item.total }}</span>
                                </li>
                            </ul>
                            <div v-if="!dashboard.activos?.por_tipo?.length" class="text-center text-muted py-3">
                                No hay activos registrados
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Planes por Tipo -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-file-alt mr-2"></i> Planes por Tipo</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li v-for="item in dashboard.planes?.por_tipo" :key="item.tipo_plan" 
                                    class="list-group-item d-flex justify-content-between">
                                    <span>{{ getTipoPlanLabel(item.tipo_plan) }}</span>
                                    <span class="badge badge-success">{{ item.total }}</span>
                                </li>
                            </ul>
                            <div v-if="!dashboard.planes?.por_tipo?.length" class="text-center text-muted py-3">
                                No hay planes registrados
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alertas -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="fas fa-bell mr-2"></i> Alertas</h5>
                        </div>
                        <div class="card-body">
                            <div v-if="dashboard.planes?.necesitan_revision" class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>{{ dashboard.planes?.necesitan_revision }}</strong> planes necesitan revisión
                            </div>
                            <div v-if="dashboard.incidentes?.activos" class="alert alert-danger">
                                <i class="fas fa-fire mr-2"></i>
                                <strong>{{ dashboard.incidentes?.activos }}</strong> incidentes activos
                            </div>
                            <div v-if="!dashboard.planes?.necesitan_revision && !dashboard.incidentes?.activos" 
                                 class="text-center text-success py-3">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <p class="mb-0">Sin alertas activas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Próximas Pruebas -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-calendar mr-2"></i> Próximas Pruebas Programadas</h5>
                        </div>
                        <div class="card-body">
                            <div v-if="dashboard.pruebas?.proximas?.length" class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Plan</th>
                                            <th>Tipo</th>
                                            <th>Fecha Programada</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="prueba in dashboard.pruebas.proximas" :key="prueba.id">
                                            <td>{{ prueba.codigo }}</td>
                                            <td>{{ prueba.nombre }}</td>
                                            <td>{{ prueba.plan?.codigo }}</td>
                                            <td>{{ prueba.tipo_prueba }}</td>
                                            <td>{{ formatDate(prueba.fecha_programada) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else class="text-center text-muted py-3">
                                No hay pruebas programadas
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useContinuidadStore } from '@/stores/continuidadStore';
import LoadingState from '@/components/generales/LoadingState.vue';

const store = useContinuidadStore();

const isLoading = computed(() => store.isLoading);
const dashboard = computed(() => store.dashboard);

const getTipoActivoLabel = (tipo) => {
    const tipos = {
        personal: 'Personal clave',
        tecnologia: 'Tecnología',
        informacion: 'Información',
        infraestructura: 'Infraestructura',
        proveedor: 'Proveedor',
        proceso: 'Proceso',
        otro: 'Otro'
    };
    return tipos[tipo] || tipo;
};

const getTipoPlanLabel = (tipo) => {
    const tipos = {
        bcp: 'BCP',
        drp: 'DRP',
        irp: 'IRP',
        crmp: 'CRMP'
    };
    return tipos[tipo] || tipo;
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('es-PE');
};

onMounted(() => {
    store.fetchDashboard();
});
</script>
