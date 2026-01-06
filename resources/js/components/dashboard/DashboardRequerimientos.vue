<template>
    <div class="container-fluid">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb bg-light py-2 px-3 rounded">
                <li class="breadcrumb-item"><router-link to="/home">Inicio</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard de Requerimientos</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="d-flex align-items-center mb-4">
            <i class="fas fa-tachometer-alt fa-2x text-danger mr-3"></i>
            <div>
                <h4 class="mb-0 text-dark font-weight-bold">Dashboard de Requerimientos</h4>
                <small class="text-muted">Monitoreo y seguimiento del estado de los requerimientos</small>
            </div>
        </div>

        <!-- Resumen General (Stats Cards) -->
        <div class="row text-white mb-4">
            <div class="col-md-2 col-sm-6 mb-3">
                <div class="summary-card bg-dark text-white rounded shadow p-3 text-center">
                    <div v-if="isLoadingStats" class="spinner-border spinner-border-sm" role="status"></div>
                    <div v-else>
                        <div class="mb-2"><i class="fas fa-tasks fa-2x"></i></div>
                        <div class="d-flex justify-content-around align-items-center">
                            <div>
                                <h4 class="mb-0">{{ stats.total }} |</h4>
                                <small>Total | </small>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ stats.sin_asignar }}</h4>
                                <small>Sin asignar</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="summary-card bg-secondary">
                    <div v-if="isLoadingStats" class="spinner-border spinner-border-sm" role="status"></div>
                    <div v-else>
                        <i class="fas fa-ban"></i>
                        <h4>{{ stats.desestimados }}</h4>
                        <div class="small-box-footer">Desestimados</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="summary-card bg-green">
                    <div v-if="isLoadingStats" class="spinner-border spinner-border-sm" role="status"></div>
                    <div v-else>
                        <i class="fas fa-spinner"></i>
                        <h4>{{ stats.enProceso }}</h4>
                        <div class="small-box-footer">En Proceso</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="summary-card bg-danger">
                    <div v-if="isLoadingStats" class="spinner-border spinner-border-sm" role="status"></div>
                    <div v-else>
                        <i class="fas fa-exclamation-triangle"></i>
                        <h4>{{ stats.vencidos }}</h4>
                        <div class="small-box-footer">Vencidos</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="summary-card bg-primary">
                    <div v-if="isLoadingStats" class="spinner-border spinner-border-sm" role="status"></div>
                    <div v-else>
                        <i class="fas fa-check-circle"></i>
                        <h4>{{ stats.finalizados }}</h4>
                        <div class="small-box-footer">Finalizados</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="summary-card bg-purple">
                    <div v-if="isLoadingStats" class="spinner-border spinner-border-sm" role="status"></div>
                    <div v-else>
                        <i class="fas fa-check-double"></i>
                        <h4>{{ stats.eficacia }}%</h4>
                        <p>Eficacia</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Gráfico Mensual -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Evolución Mensual de Requerimientos</h6>
                        <select class="form-control form-control-sm ml-auto" style="width: auto;" v-model="chartYear" @change="fetchChartData">
                            <option v-for="year in chartYears" :key="year" :value="year">{{ year }}</option>
                        </select>
                    </div>
                    <div class="card-body chart-container">
                        <div v-if="isLoadingChart" class="d-flex align-items-center justify-content-center h-100">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                        <canvas v-show="!isLoadingChart" ref="chartCanvas"></canvas>
                    </div>
                </div>
            </div>

            <!-- Alertas -->
            <div class="col-md-6 mb-4">
                <div class="row h-100">
                    <!-- Vencidos -->
                    <div class="col-md-6">
                        <div class="card border-danger h-100">
                            <div class="card-header bg-danger text-white">Requerimientos Vencidos ({{ vencidos.total || 0 }})</div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush small">
                                    <li v-if="isLoadingVencidos" class="list-group-item text-center">
                                        <div class="spinner-border spinner-border-sm" role="status"></div>
                                    </li>
                                    <template v-else>
                                        <li v-for="r in vencidos.data" :key="'vencido-' + r.id" class="list-group-item">
                                            <strong>Req: {{ r.id }}</strong> - Avance: {{ r.avance ? r.avance.avance_registrado : 0 }}% <br>
                                            <strong>Inicio:</strong> {{ formatDate(r.fecha_asignacion) }} -
                                            <strong>Venció:</strong> {{ formatDate(r.fecha_limite) }}
                                        </li>
                                        <li v-if="vencidos.data.length === 0" class="list-group-item text-muted">Sin requerimientos vencidos</li>
                                    </template>
                                </ul>
                            </div>
                            <div class="card-footer d-flex justify-content-center" v-if="vencidos.last_page > 1">
                                <nav>
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item" :class="{ disabled: vencidos.current_page === 1 }">
                                            <a class="page-link" href="#" @click.prevent="fetchVencidos(vencidos.current_page - 1)">&laquo;</a>
                                        </li>
                                        <li v-for="page in vencidos.last_page" :key="page" class="page-item" :class="{ active: page === vencidos.current_page }">
                                            <a class="page-link" href="#" @click.prevent="fetchVencidos(page)">{{ page }}</a>
                                        </li>
                                        <li class="page-item" :class="{ disabled: vencidos.current_page === vencidos.last_page }">
                                            <a class="page-link" href="#" @click.prevent="fetchVencidos(vencidos.current_page + 1)">&raquo;</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <!-- En Riesgo -->
                    <div class="col-md-6">
                        <div class="card border-warning h-100">
                            <div class="card-header bg-warning">Requerimientos en Riesgo ({{ enRiesgo.total || 0 }})</div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush small">
                                    <li v-if="isLoadingEnRiesgo" class="list-group-item text-center">
                                        <div class="spinner-border spinner-border-sm" role="status"></div>
                                    </li>
                                    <template v-else>
                                        <li v-for="r in enRiesgo.data" :key="'riesgo-' + r.id" class="list-group-item">
                                            <strong>Req: {{ r.id }}</strong> - Avance: {{ r.avance ? r.avance.avance_registrado : 0 }}%<br>
                                            <strong>Asignado a:</strong> {{ r.especialista ? r.especialista.sigla : 'Sin asignar' }}<br>
                                            <strong>Fecha:</strong> {{ formatDate(r.fecha_asignacion) }} - {{ formatDate(r.fecha_limite) }}
                                        </li>
                                        <li v-if="enRiesgo.data.length === 0" class="list-group-item text-muted">Sin requerimientos en riesgo</li>
                                    </template>
                                </ul>
                            </div>
                            <div class="card-footer d-flex justify-content-center" v-if="enRiesgo.last_page > 1">
                                <nav>
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item" :class="{ disabled: enRiesgo.current_page === 1 }">
                                            <a class="page-link" href="#" @click.prevent="fetchEnRiesgo(enRiesgo.current_page - 1)">&laquo;</a>
                                        </li>
                                        <li v-for="page in enRiesgo.last_page" :key="page" class="page-item" :class="{ active: page === enRiesgo.current_page }">
                                            <a class="page-link" href="#" @click.prevent="fetchEnRiesgo(page)">{{ page }}</a>
                                        </li>
                                        <li class="page-item" :class="{ disabled: enRiesgo.current_page === enRiesgo.last_page }">
                                            <a class="page-link" href="#" @click.prevent="fetchEnRiesgo(enRiesgo.current_page + 1)">&raquo;</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen por Especialista -->
        <div class="card mb-4">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Resumen por Especialista</h6>
                <select v-model="especialistaAnio" class="form-control form-control-sm ml-auto" style="width: auto;">
                    <option v-for="anio in aniosDisponibles" :key="anio" :value="anio">{{ anio }}</option>
                </select>
            </div>
            <div class="card-body">
                <div v-if="isLoadingEspecialistas" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
                <div v-else class="table-responsive">
                    <table class="table table-sm table-striped table-hover table-bordered align-middle text-center">
                        <thead class="thead-light">
                            <tr>
                                <th>Especialista</th>
                                <th>Asignados</th>
                                <th>Vencidos</th>
                                <th>Finalizados</th>
                                <th>Desestimados</th>
                                <th>Avance Pendientes</th>
                                <th>Eficacia</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="esp in especialistas" :key="esp.id">
                                <td class="text-left">
                                    <img :src="esp.foto_url" alt="foto" class="img-circle elevation-2" width="40" height="40" style="margin-right: 10px;">
                                    {{ esp.nombre }} ({{ esp.sigla }})
                                </td>
                                <td>{{ esp.total_asignados }}</td>
                                <td><span :class="{'badge bg-danger': esp.total_vencidos > 0}">{{ esp.total_vencidos }}</span></td>
                                <td>{{ esp.total_finalizados }}</td>
                                <td><span :class="{'badge bg-danger': esp.total_desestimados > 0}">{{ esp.total_desestimados }}</span></td>
                                <td>
                                    <div class="text-center">
                                        <strong>{{ esp.promedioAvance }}%</strong>
                                        <div class="progress" style="height: 8px; margin-top: 5px;">
                                            <div class="progress-bar" :class="getProgressBarClass(esp.promedioAvance)" role="progressbar"
                                                :style="{ width: esp.promedioAvance + '%' }"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <strong>{{ esp.efectividad }}%</strong>
                                        <div class="progress" style="height: 8px; margin-top: 5px;">
                                            <div class="progress-bar" :class="getProgressBarClass(esp.efectividad)" role="progressbar"
                                                :style="{ width: esp.efectividad + '%' }"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="#" class="btn btn-outline-danger btn-sm" @click.prevent="openDetalleModal(esp)">
                                        <i class="fas fa-eye"></i> Ver detalle
                                    </a>
                                </td>
                            </tr>
                            <tr v-if="especialistas.length === 0">
                                <td colspan="8">No hay especialistas registrados para el año seleccionado.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Detalle Especialista -->
        <div class="modal fade" ref="detalleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Detalle de Requerimientos</h5>
                        <button type="button" class="close text-white" aria-label="Close" @click="closeDetalleModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="isLoadingDetalle" class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                        <div v-else>
                            <div class="d-flex align-items-center bg-light border rounded p-2 mb-3">
                                <p class="mb-0 small">
                                    <i class="fas fa-info-circle text-primary mr-2"></i>
                                    Requerimientos asignados a <strong class="text-dark">{{ detalleEspecialista.nombre }}</strong>
                                </p>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-bordered align-middle text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>ID</th>
                                            <th style="width: 30%">Asunto</th>
                                            <th>Estado</th>
                                            <th>Avance</th>
                                            <th style="width: 15%">Fecha Límite</th>
                                            <th>Días Restantes</th>
                                            <th>Fecha Fin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="req in detalleRequerimientos" :key="req.id">
                                            <td>{{ req.id }}</td>
                                            <td class="text-left">{{ req.asunto }}</td>
                                            <td>{{ req.estado }}</td>
                                            <td>{{ req.avance ? req.avance.avance_registrado : 0 }}%</td>
                                            <td>{{ formatDate(req.fecha_limite) }}</td>
                                            <td>
                                                <span v-if="req.estado === 'asignado'">{{ calcularDiasRestantes(req.fecha_limite) }}</span>
                                                <span v-else>N/A</span>
                                            </td>
                                            <td>{{ formatDate(req.fecha_fin) }}</td>
                                        </tr>
                                        <tr v-if="detalleRequerimientos.length === 0">
                                            <td colspan="7">No hay requerimientos para mostrar.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { route } from 'ziggy-js';
import { Ziggy } from '../../ziggy';
import Chart from 'chart.js/auto';
import { Modal } from 'bootstrap';

export default {
    name: 'DashboardRequerimientos',
    data() {
        return {
            // Stats
            isLoadingStats: false,
            stats: { total: 0, sin_asignar: 0, desestimados: 0, enProceso: 0, vencidos: 0, finalizados: 0, eficacia: 0 },
            
            // Chart
            isLoadingChart: false,
            chartInstance: null,
            chartYear: new Date().getFullYear(),
            chartYears: [],
            
            // Alertas
            isLoadingVencidos: false,
            isLoadingEnRiesgo: false,
            vencidos: { data: [], total: 0, current_page: 1, last_page: 1 },
            enRiesgo: { data: [], total: 0, current_page: 1, last_page: 1 },
            
            // Especialistas
            isLoadingEspecialistas: false,
            especialistas: [],
            aniosDisponibles: [],
            especialistaAnio: new Date().getFullYear(),
            
            // Modal Detalle
            detalleModal: null,
            isLoadingDetalle: false,
            detalleEspecialista: {},
            detalleRequerimientos: [],
        };
    },
    watch: {
        especialistaAnio() {
            this.fetchEspecialistas();
        }
    },
    methods: {
        // Data fetching
        fetchStats() {
            this.isLoadingStats = true;
            axios.get(route('dashboard.resumenGeneral', {}, false, Ziggy))
                .then(response => this.stats = response.data)
                .catch(error => console.error('Error al cargar stats:', error))
                .finally(() => this.isLoadingStats = false);
        },
        fetchChartData() {
            this.isLoadingChart = true;
            axios.get(route('dashboard.resumenGrafico', { year: this.chartYear }, false, Ziggy))
                .then(response => this.renderChart(response.data))
                .catch(error => console.error('Error al cargar gráfico:', error))
                .finally(() => this.isLoadingChart = false);
        },
        fetchVencidos(page = 1) {
            this.isLoadingVencidos = true;
            axios.get(route('dashboard.alertas', { type: 'vencidos', page }, false, Ziggy))
                .then(response => this.vencidos = response.data)
                .catch(error => console.error('Error al cargar vencidos:', error))
                .finally(() => this.isLoadingVencidos = false);
        },
        fetchEnRiesgo(page = 1) {
            this.isLoadingEnRiesgo = true;
            axios.get(route('dashboard.alertas', { type: 'enRiesgo', page }, false, Ziggy))
                .then(response => this.enRiesgo = response.data)
                .catch(error => console.error('Error al cargar en riesgo:', error))
                .finally(() => this.isLoadingEnRiesgo = false);
        },
        fetchEspecialistas() {
            this.isLoadingEspecialistas = true;
            axios.get(route('dashboard.resumenEspecialistas', { anio: this.especialistaAnio }, false, Ziggy))
                .then(response => {
                    this.especialistas = response.data.especialistas;
                    this.aniosDisponibles = response.data.aniosDisponibles;
                })
                .catch(error => console.error('Error al cargar especialistas:', error))
                .finally(() => this.isLoadingEspecialistas = false);
        },
        
        // Chart
        populateYears() {
            const currentYear = new Date().getFullYear();
            for (let i = currentYear; i >= currentYear - 5; i--) {
                this.chartYears.push(i);
            }
        },
        renderChart(data) {
            if (this.chartInstance) this.chartInstance.destroy();
            const ctx = this.$refs.chartCanvas.getContext('2d');
            this.chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        { label: 'Asignados', data: data.asignados, borderColor: 'rgba(108, 117, 125, 1)', backgroundColor: 'rgba(108, 117, 125, 0.2)', tension: 0.3, fill: true, pointRadius: 4 },
                        { label: 'Fin Planificado', data: data.programados, borderColor: 'rgba(80, 200, 162, 1)', backgroundColor: 'rgba(80, 200, 162, 0.2)', tension: 0.3, fill: true, pointRadius: 4 },
                        { label: 'Fin Real', data: data.finalizados, borderColor: 'rgba(0, 123, 255, 1)', backgroundColor: 'rgba(0, 123, 255, 0.2)', tension: 0.3, fill: true, pointRadius: 4 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
            });
        },
        
        // Helpers
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            return new Date(dateString).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
        },
        getProgressBarClass(value) {
            if (value >= 80) return 'bg-success';
            if (value >= 50) return 'bg-warning';
            return 'bg-danger';
        },
        calcularDiasRestantes(fechaLimite) {
            if (!fechaLimite) return 'N/A';
            const diffDays = Math.ceil((new Date(fechaLimite).getTime() - new Date().getTime()) / (1000 * 60 * 60 * 24));
            return diffDays < 0 ? `Vencido por ${Math.abs(diffDays)} días` : diffDays;
        },
        
        // Modal
        openDetalleModal(especialista) {
            this.isLoadingDetalle = true;
            this.detalleEspecialista = {};
            this.detalleRequerimientos = [];
            this.detalleModal?.show();
            
            axios.get(route('dashboard.detalleEspecialista', { especialista: especialista.id, anio: this.especialistaAnio }, false, Ziggy))
                .then(response => {
                    this.detalleEspecialista = response.data.especialista;
                    this.detalleRequerimientos = response.data.requerimientos;
                })
                .catch(error => console.error('Error al cargar detalle:', error))
                .finally(() => this.isLoadingDetalle = false);
        },
        closeDetalleModal() {
            this.detalleModal?.hide();
        }
    },
    mounted() {
        this.populateYears();
        this.fetchStats();
        this.fetchChartData();
        this.fetchVencidos();
        this.fetchEnRiesgo();
        this.fetchEspecialistas();
        
        if (this.$refs.detalleModal) {
            this.detalleModal = new Modal(this.$refs.detalleModal);
        }
    },
    beforeUnmount() {
        if (this.chartInstance) this.chartInstance.destroy();
        if (this.detalleModal) this.detalleModal.dispose();
    }
};
</script>

<style scoped>
.summary-card {
    border-radius: 5px;
    padding: 1rem;
    color: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 120px;
    text-align: center;
}
.summary-card i { font-size: 1.5rem; margin-bottom: 0.2rem; }
.summary-card h4 { margin: 0; font-size: 1.6rem; }
.summary-card p { margin: 0; font-size: 0.9rem; }
.breadcrumb { background-color: #f8f9fa; }
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}
</style>
