<template>
    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="card shadow-sm border-0 mb-4 fade-in-down">
            <div class="card-header bg-white p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-clipboard-check mr-2 text-danger"></i>Tablero de Gestión de Obligaciones
                        </h4>
                        <p class="text-muted small mb-0">Monitoreo de Cumplimiento Normativo y Acciones</p>
                    </div>
                    <div class="mt-2 mt-md-0 d-flex align-items-center">
                        <!-- Admin Toggle -->
                        <div v-if="esAdmin" class="mr-3 bg-light p-2 rounded shadow-sm d-flex align-items-center">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="mostrarTodosSwitch"
                                    v-model="mostrarTodos" @change="loadData">
                                <label class="custom-control-label font-weight-bold text-muted small user-select-none"
                                    for="mostrarTodosSwitch">Ver Todos</label>
                            </div>
                        </div>

                        <!-- Refresh Button (Always visible) -->
                        <div class="d-inline-flex align-items-center bg-light p-2 rounded shadow-sm ml-auto">
                            <button class="btn btn-sm btn-icon btn-light text-danger shadow-sm rounded-circle"
                                @click="loadData" title="Actualizar" style="width: 32px; height: 32px;">
                                <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-danger" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
        </div>

        <div v-else class="animated-fade-in">
            <!-- Metrics Grid -->
            <div class="row mb-5">
                <!-- Total Obligaciones -->
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden main-stat-card">
                        <div class="card-body p-4 d-flex flex-column justify-content-between position-relative z-1">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <h6 class="text-uppercase text-muted small font-weight-bold letter-spacing-1 mb-1">
                                        Total Obligaciones</h6>
                                    <h2 class="font-weight-bolder text-dark mb-0 display-4">{{ stats.total }}</h2>
                                </div>
                                <div class="icon-circle bg-light-danger text-danger">
                                    <i class="fas fa-file-contract fa-lg"></i>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <div class="d-flex align-items-center text-muted small font-weight-bold">
                                    <span v-if="stats.pendientes > 0" class="text-warning mr-2">
                                        <i class="fas fa-clock mr-1"></i>{{ stats.pendientes }} pendientes
                                    </span>
                                    <span v-else class="text-success">
                                        <i class="fas fa-check-circle mr-1"></i>Todo al día
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="dec-circle bg-danger opacity-5"></div>
                    </div>
                </div>

                <!-- % Controladas -->
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body p-4 border-top border-success border-width-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-uppercase text-muted small font-weight-bold letter-spacing-1 mb-0">
                                    Controladas</h6>
                                <span class="badge badge-pill badge-soft-success text-success">{{ stats.controladas
                                }}</span>
                            </div>
                            <div class="position-relative pt-2 pb-3">
                                <p class="mb-0 text-dark font-weight-bold display-4">{{ Math.round((stats.controladas /
                                    (stats.total || 1)) * 100) }}%</p>
                                <small class="text-muted">Proporción de cumplimiento</small>
                            </div>
                            <div class="progress rounded-pill bg-light" style="height: 6px;">
                                <div class="progress-bar rounded-pill bg-success"
                                    :style="{ width: (stats.controladas / (stats.total || 1)) * 100 + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pendientes -->
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div
                            class="card-body p-4 border-top border-warning border-width-3 text-center d-flex flex-column justify-content-center">
                            <h6 class="text-uppercase text-muted small font-weight-bold letter-spacing-1 mb-2">Por
                                Controlar</h6>
                            <h2 class="font-weight-bold text-warning mb-1">{{ stats.pendientes }}</h2>
                            <p class="text-muted small mb-0">Obligaciones en estado pendiente</p>
                            <div class="mt-3">
                                <i class="fas fa-hourglass-half fa-2x text-warning opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inactivas/Suspendidas -->
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="card border-0 shadow-sm h-100 hover-lift bg-dark text-white">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="text-uppercase text-white-50 small font-weight-bold letter-spacing-1 mb-1">
                                    Otros Estados</h6>
                                <h2 class="font-weight-bold mb-0 display-4 text-white">{{
                                    (stats.distribucionEstado.Inactiva || 0) + (stats.distribucionEstado.Suspendida ||
                                        0) }}</h2>
                            </div>
                            <div class="mt-3">
                                <span class="badge badge-pill bg-secondary text-white">
                                    Inactivas / Suspendidas
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <!-- Distribution Chart -->
                <div class="col-lg-7 mb-4 mb-lg-0">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-header bg-danger border-0 pt-4 px-4 pb-0"
                            style="border-radius: 1rem 1rem 0 0;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="font-weight-bold text-white mb-1">Análisis de Obligaciones</h6>
                                    <p class="text-white small mb-0">Visión general y evolución histórica</p>
                                </div>
                                <div class="nav nav-pills nav-pills-sm" role="tablist">
                                    <a class="nav-link font-weight-bold px-3 py-1 mr-2 rounded-pill border border-white transition-all"
                                        :class="activeChartTab === 'distribucion' ? 'bg-white text-danger shadow-sm' : 'text-white'"
                                        href="#" @click.prevent="switchTab('distribucion')">
                                        Distribución
                                    </a>
                                    <a class="nav-link font-weight-bold px-3 py-1 rounded-pill border border-white transition-all"
                                        :class="activeChartTab === 'identificacion' ? 'bg-white text-danger shadow-sm' : 'text-white'"
                                        href="#" @click.prevent="switchTab('identificacion')">
                                        Por Año
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div v-show="activeChartTab === 'distribucion'" class="chart-container pt-4">
                                <canvas ref="chartCanvas"></canvas>
                            </div>
                            <div v-show="activeChartTab === 'identificacion'" class="chart-container pt-4">
                                <canvas ref="barChartCanvas"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden">
                        <div class="card-header bg-white border-bottom pt-4 px-4 pb-0">
                            <div class="nav nav-pills nav-pills-sm mb-3" role="tablist">
                                <a class="nav-link active font-weight-bold px-3 py-1 mr-2 rounded-pill nav-link-danger"
                                    data-toggle="pill" href="#vencidos" role="tab">
                                    <i class="fas fa-clock mr-2"></i>Acciones Venc.
                                    <span class="badge badge-light ml-2">{{ accionesVencidas.length || 0 }}</span>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0 tab-content scrollable-list">
                            <div class="tab-pane fade show active" id="vencidos" role="tabpanel">
                                <ul class="list-group list-group-flush">
                                    <li v-for="a in accionesVencidas" :key="'accion-' + a.id"
                                        class="list-group-item border-bottom-light px-4 py-3 hover-bg-gray transition-colors">
                                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 font-weight-bold text-dark text-truncate mr-2">
                                                {{ a.accion_descripcion || 'Sin descripción' }}
                                            </h6>
                                            <span class="badge badge-soft-danger flex-shrink-0">Atrasado</span>
                                        </div>
                                        <p class="mb-1 text-muted small text-truncate" style="max-width: 90%;">
                                            Obligación: {{ a.obligacion?.obligacion_documento }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-danger font-weight-bold">
                                                <i class="far fa-calendar-times mr-1"></i>{{
                                                    formatDate(a.accion_fecha_fin_planificada) }}
                                            </small>
                                            <small class="text-muted font-weight-bold">
                                                <i class="far fa-user mr-1"></i>{{ a.accion_responsable }}
                                            </small>
                                        </div>
                                    </li>
                                    <li v-if="accionesVencidas.length === 0"
                                        class="list-group-item text-center text-muted py-5 border-0">
                                        <i class="fas fa-check-circle fa-2x mb-2 text-success opacity-50"></i>
                                        <p class="mb-0 small">Sin acciones de obligaciones vencidas.</p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen por Proceso -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-danger border-0 pt-4 px-4 pb-3 d-flex justify-content-between align-items-center"
                    style="border-radius: 1rem 1rem 0 0;">
                    <div>
                        <h6 class="font-weight-bold text-white mb-1">Resumen por Proceso Asociado</h6>
                        <p class="text-white small mb-0">Carga de obligaciones por proceso</p>
                    </div>
                </div>
                <div class="card-body px-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                <tr>
                                    <th class="pl-4 border-0">Proceso</th>
                                    <th class="text-center border-0">Obligaciones Totales</th>
                                    <th class="text-center border-0">Pendientes</th>
                                    <th class="border-0" style="width: 25%;">Cumplimiento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="proceso in resumenProcesos" :key="proceso.id" class="transition-bg">
                                    <td class="pl-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-box bg-light-danger text-danger mr-3">
                                                <i class="fas fa-network-wired fa-sm"></i>
                                            </div>
                                            <span class="font-weight-bold text-dark">{{ proceso.nombre }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center font-weight-bold text-dark">{{ proceso.total }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-pill px-3 py-1 font-weight-bold"
                                            :class="proceso.pendientes > 0 ? 'badge-soft-warning' : 'badge-soft-success'">
                                            {{ proceso.pendientes }} Pendientes
                                        </span>
                                    </td>
                                    <td class="pr-4">
                                        <div class="d-flex align-items-center">
                                            <span class="small font-weight-bold mr-3 text-muted">
                                                {{ Math.round(((proceso.total - proceso.pendientes) / (proceso.total ||
                                                    1)) * 100) }}%
                                            </span>
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-success"
                                                    :style="{ width: ((proceso.total - proceso.pendientes) / (proceso.total || 1)) * 100 + '%' }">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="resumenProcesos.length === 0">
                                    <td colspan="4" class="text-center py-5 text-muted">No se encontraron procesos con
                                        obligaciones.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import {
    Chart as ChartJS,
    registerables
} from 'chart.js';

ChartJS.register(...registerables);

export default {
    name: 'DashboardObligaciones',
    data() {
        return {
            loading: true,
            mostrarTodos: false,
            esAdmin: false,
            years: [],
            selectedYear: [new Date().getFullYear()],
            activeChartTab: 'distribucion',
            stats: {
                total: 0, controladas: 0, pendientes: 0, vencidas: 0,
                distribucionEstado: {}
            },
            identificacionPorAno: [],
            accionesVencidas: [],
            resumenProcesos: [],
            chartInstance: null,
            barChartInstance: null,
        };
    },
    computed: {
        selectedYearsLabel() {
            if (!this.selectedYear || this.selectedYear.length === 0) return 'Ninguno';
            return this.selectedYear.sort().join(', ');
        }
    },
    methods: {
        async loadData() {
            this.loading = true;
            try {
                let url = '/api/dashboard/obligaciones';
                const params = new URLSearchParams();
                if (this.esAdmin) params.append('mostrarTodos', this.mostrarTodos);

                const response = await axios.get(`${url}?${params.toString()}`);
                const data = response.data;

                this.stats = data.stats;
                this.accionesVencidas = data.accionesVencidas;
                this.resumenProcesos = data.resumenProcesos;
                this.identificacionPorAno = data.identificacionPorAno;

                this.processCharts();
            } catch (error) {
                console.error('Error loading obligation dashboard data:', error);
            } finally {
                setTimeout(() => this.loading = false, 300);
            }
        },
        switchTab(tab) {
            this.activeChartTab = tab;
            this.processCharts();
        },
        processCharts() {
            if (this.activeChartTab === 'distribucion') {
                const labels = Object.keys(this.stats.distribucionEstado);
                const quantities = Object.values(this.stats.distribucionEstado);
                const colors = ['#f59e0b', '#10b981', '#ef4444', '#6b7280', '#dc2626']; // Pendiente, Controlada, Vencida, Inactiva, Suspendida

                this.$nextTick(() => {
                    setTimeout(() => this.renderDoughnutChart(labels, quantities, colors), 350);
                });
            } else {
                this.$nextTick(() => {
                    setTimeout(() => this.renderBarChart(), 350);
                });
            }
        },
        renderDoughnutChart(labels, data, colors) {
            if (this.chartInstance) {
                this.chartInstance.destroy();
                this.chartInstance = null;
            }
            const canvas = this.$refs.chartCanvas;
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            this.chartInstance = new ChartJS(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        borderWidth: 3,
                        borderColor: '#fff',
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                padding: 20,
                                font: { family: "'Inter', sans-serif", size: 12, weight: '500' },
                                color: '#475569'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    layout: { padding: 20 },
                    cutout: '75%',
                },
                plugins: [{
                    id: 'centerText',
                    beforeDraw: (chart) => {
                        const { ctx, height } = chart;
                        if (!chart.getDatasetMeta(0).data[0]) return;

                        const meta = chart.getDatasetMeta(0);
                        const centerX = meta.data[0].x;
                        const centerY = meta.data[0].y;

                        ctx.restore();

                        // Cantidad Total (Grande)
                        const fontSize = (height / 114).toFixed(2);
                        ctx.font = `bold ${fontSize}em 'Inter', sans-serif`;
                        ctx.textBaseline = "middle";
                        ctx.fillStyle = "#1e293b";

                        const total = data.reduce((a, b) => a + b, 0);
                        const text = total.toString();
                        const textX = Math.round(centerX - (ctx.measureText(text).width / 2));
                        const textY = centerY - 10;

                        ctx.fillText(text, textX, textY);

                        // Etiqueta "Obligaciones" (Pequeña)
                        const labelFontSize = (height / 250).toFixed(2);
                        ctx.font = `500 ${labelFontSize}em 'Inter', sans-serif`;
                        ctx.fillStyle = "#64748b";

                        const labelText = "Obligaciones";
                        const labelX = Math.round(centerX - (ctx.measureText(labelText).width / 2));
                        const labelY = centerY + 15;

                        ctx.fillText(labelText, labelX, labelY);
                        ctx.save();
                    }
                }]
            });
        },
        renderBarChart() {
            if (this.barChartInstance) {
                this.barChartInstance.destroy();
                this.barChartInstance = null;
            }
            const canvas = this.$refs.barChartCanvas;
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            const years = this.identificacionPorAno.map(d => d.year);

            this.barChartInstance = new ChartJS(ctx, {
                type: 'bar',
                data: {
                    labels: years,
                    datasets: [
                        { label: 'Controlada', data: this.identificacionPorAno.map(d => d.controlada), backgroundColor: '#10b981' },
                        { label: 'Pendiente', data: this.identificacionPorAno.map(d => d.pendiente), backgroundColor: '#f59e0b' },
                        { label: 'Vencida', data: this.identificacionPorAno.map(d => d.vencida), backgroundColor: '#ef4444' },
                        { label: 'Inactiva', data: this.identificacionPorAno.map(d => d.inactiva), backgroundColor: '#6b7280' },
                        { label: 'Suspendida', data: this.identificacionPorAno.map(d => d.suspendida), backgroundColor: '#dc2626' }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: {
                        x: { stacked: true, grid: { display: false } },
                        y: { stacked: true, beginAtZero: true, grid: { color: '#f1f5f9' } }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, boxWidth: 8, padding: 20 }
                        }
                    }
                }
            });
        },
        async verificarRolUsuario() {
            try {
                const response = await axios.get('/api/admin/roles'); // Reutilizando endpoint de roles o similar
                // Lógica simple para demo, idealmente usar el store
                this.esAdmin = true; // Por ahora true para asegurar carga
                this.loadData();
            } catch {
                this.esAdmin = false;
                this.loadData();
            }
        },
        formatDate(date) {
            if (!date) return 'N/A';
            return new Date(date).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
        }
    },
    mounted() {
        this.verificarRolUsuario();
    },
    beforeUnmount() {
        if (this.chartInstance) this.chartInstance.destroy();
        if (this.barChartInstance) this.barChartInstance.destroy();
    }
};
</script>

<style scoped>
.dashboard-container {
    background-color: #f3f4f6;
    min-height: 100vh;
    padding: 2rem;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

.fade-in-down {
    animation: fadeInDown 0.6s ease-out;
}

.animated-fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-15px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

.card {
    border-radius: 1rem;
    border: none;
}

.shadow-sm {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
}

.main-stat-card {
    background: white;
}

.dec-circle {
    position: absolute;
    width: 150px;
    height: 150px;
    border-radius: 50%;
    top: -50px;
    right: -50px;
    opacity: 0.1;
}

.icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-light-danger {
    background-color: rgba(220, 53, 69, 0.1);
}

.hover-lift {
    transition: transform 0.2s, box-shadow 0.2s;
}

.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
}

.nav-pills-sm .nav-link:not(.text-white) {
    font-size: 0.85rem;
    border-radius: 0.5rem;
    color: #64748b;
    background: #f8fafc;
}

/* Estilo para las pestañas en el header rojo */
.card-header.bg-danger .nav-pills-sm .nav-link {
    background: transparent;
    opacity: 0.85;
    transition: all 0.3s ease;
}

.card-header.bg-danger .nav-pills-sm .nav-link.bg-white {
    opacity: 1 !important;
}

.card-header.bg-danger .nav-pills-sm .nav-link:hover {
    opacity: 1 !important;
}

.card-header.bg-danger .nav-pills-sm .nav-link.text-white:hover {
    background-color: rgba(255, 255, 255, 0.2) !important;
}

.transition-all {
    transition: all 0.3s ease;
}

.nav-pills .nav-link-danger.active {
    background-color: #dc3545 !important;
    color: white !important;
}

.scrollable-list {
    max-height: 400px;
    overflow-y: auto;
}

.scrollable-list::-webkit-scrollbar {
    width: 5px;
}

.scrollable-list::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 10px;
}

.hover-bg-gray:hover {
    background-color: #f8fafc;
}

.border-bottom-light {
    border-bottom: 1px solid #f1f5f9;
}

.badge-soft-success {
    background-color: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.badge-soft-warning {
    background-color: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.badge-soft-danger {
    background-color: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.icon-box {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chart-container {
    height: 350px;
    width: 100%;
}

.border-width-3 {
    border-top-width: 4px !important;
}

.transition-bg {
    transition: background-color 0.15s;
}

.transition-bg:hover {
    background-color: #f9fafb;
}
</style>
