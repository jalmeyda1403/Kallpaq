<template>
    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="card shadow-sm border-0 mb-4 fade-in-down">
            <div class="card-header bg-white p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-chart-pie mr-2 text-primary"></i>BSC del Sistema de Gestión
                        </h4>
                        <p class="text-muted small mb-0">Monitoreo estratégico de indicadores, objetivos y perspectivas
                        </p>
                    </div>
                    <div class="mt-2 mt-md-0">
                        <div class="d-inline-flex align-items-center bg-light p-2 rounded shadow-sm">
                            <label class="mb-0 mr-2 font-weight-bold text-muted small text-uppercase">Periodo:</label>
                            <select v-model="selectedYear" @change="loadData"
                                class="custom-select custom-select-sm year-select w-auto border-0 bg-transparent font-weight-bold text-primary"
                                style="cursor: pointer; box-shadow: none;">
                                <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                            </select>
                            <button class="btn btn-sm btn-icon btn-link text-primary p-0 ml-3" @click="loadData"
                                title="Actualizar">
                                <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
        </div>

        <div v-else class="animated-fade-in">

            <!-- BLOCK 1: Main Process Performance (Merged) -->
            <div class="card main-card shadow-sm border-0 overflow-hidden mb-4">
                <div
                    class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0 section-header">Desempeño Global de Procesos</h6>
                        <p class="text-muted small mb-0">Cumplimiento General y por Tipo de Proceso</p>
                    </div>

                    <!-- Dynamic Alert for Lowest Performance (Moved to Header) -->
                    <div v-if="lowestType"
                        class="alert-inline d-flex align-items-center animated-slide-in ml-auto mt-2 mt-md-0 bg-light-danger rounded p-1 pr-3 border-danger-light">
                        <div class="alert-icon-wrapper-sm bg-danger text-white mr-2">
                            <i class="fas fa-exclamation-triangle fa-sm"></i>
                        </div>
                        <div>
                            <small class="text-danger font-weight-bold d-block leading-tight"
                                style="font-size: 0.65rem;">Atención</small>
                            <span class="font-weight-bold text-dark small">
                                Menor: <strong class="text-danger">{{ lowestType.tipo }}</strong>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">

                    <div class="row align-items-center">
                        <!-- Left: General Gauge -->
                        <div class="col-lg-3 col-md-12 text-center border-right-lg mb-3 mb-lg-0 py-2">
                            <h5 class="text-uppercase text-muted font-weight-bold letter-spacing-1 mb-3">Global</h5>
                            <div class="gauge-wrapper mx-auto">
                                <svg viewBox="0 0 36 36" class="circular-chart large-chart">
                                    <path class="circle-bg"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    <path class="circle" :stroke-dasharray="general + ', 100'"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                        :class="getColorClass(general)" />
                                    <text x="18" y="20.35" class="percentage-large">{{ parseFloat(general).toFixed(1)
                                        }}%</text>
                                </svg>
                            </div>
                            <div class="mt-2 text-muted small">{{ getTotalProcesses() }} Procesos activos</div>
                        </div>

                        <!-- Right: Process Types Breakdown -->
                        <div class="col-lg-9 col-md-12">
                            <div class="row">
                                <div class="col-md-4 mb-3 mb-md-0" v-for="type in processTypes" :key="type.tipo">
                                    <div class="process-subcard p-3 rounded h-100 hover-bg-light transition-all border">
                                        <div class="text-center mb-2">
                                            <h6 class="text-primary font-weight-bold text-uppercase mb-2 process-title">
                                                {{ type.tipo }}
                                                <span class="badge badge-pill badge-light ml-2 border">{{
                                                    type.cantidad_procesos }}</span>
                                            </h6>
                                            <div class="gauge-container-medium mx-auto mb-2">
                                                <svg viewBox="0 0 36 36" class="circular-chart medium-chart">
                                                    <path class="circle-bg"
                                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                                    <path class="circle" :stroke-dasharray="type.promedio + ', 100'"
                                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                                        :class="getColorClass(type.promedio)" />
                                                    <text x="18" y="22" class="percentage-medium">{{
                                                        parseInt(type.promedio) }}%</text>
                                                </svg>
                                            </div>
                                        </div>
                                        <!-- Level breakdown -->
                                        <div class="row no-gutters text-center pt-2 border-top w-100 mt-auto">
                                            <div class="col-4 border-right">
                                                <span class="d-block font-weight-bold text-dark small">{{
                                                    type.niveles?.n0 || 0 }}</span>
                                                <small class="text-muted d-block" style="font-size: 0.65rem;">N0</small>
                                            </div>
                                            <div class="col-4 border-right">
                                                <span class="d-block font-weight-bold text-dark small">{{
                                                    type.niveles?.n1 || 0 }}</span>
                                                <small class="text-muted d-block" style="font-size: 0.65rem;">N1</small>
                                            </div>
                                            <div class="col-4">
                                                <span class="d-block font-weight-bold text-dark small">{{
                                                    type.niveles?.n2 || 0 }}</span>
                                                <small class="text-muted d-block" style="font-size: 0.65rem;">N2</small>
                                            </div>
                                        </div>
                                        <div class="progress-bar-bottom mt-2 rounded"
                                            :class="getLineColorClass(type.promedio)" style="height:4px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BLOCK 2: Detailed Metrics Grid -->
            <div class="row">
                <!-- Indicator Types -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card content-card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                            <h6 class="font-weight-bold text-dark mb-0 section-header">Resultados de los Indicadores
                            </h6>
                            <p class="text-muted small mb-0">Tipos de Indicador</p>
                        </div>
                        <div class="card-body px-4 pt-3">
                            <div v-for="item in indicatorTypes" :key="item.nombre"
                                class="metric-row d-flex justify-content-between align-items-center mb-2 p-2 rounded hover-bg"
                                :class="{ 'opacity-60': item.count === 0 }">
                                <div class="d-flex align-items-center w-60">
                                    <div class="status-dot mr-3 flex-shrink-0" :class="getBgColorClass(item.promedio)">
                                    </div>
                                    <div>
                                        <span class="font-weight-bold text-dark d-block small">{{ item.nombre }}</span>
                                        <small class="text-muted" style="font-size: 0.7rem;">{{ item.count }}
                                            Indicadores</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="font-weight-bold text-dark h6 mb-0 mr-3">{{ parseInt(item.promedio)
                                        }}%</span>
                                    <div class="gauge-mini-wrapper">
                                        <svg viewBox="0 0 36 36" class="circular-chart mini-chart">
                                            <path class="circle-bg"
                                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                            <path class="circle" :stroke-dasharray="item.promedio + ', 100'"
                                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                                :class="getColorClass(item.promedio)" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SIG Systems Chart -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card content-card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                            <h6 class="font-weight-bold text-dark mb-0 section-header">Sistemas de Gestión</h6>
                            <p class="text-muted small mb-0">Comparativa de desempeño</p>
                        </div>
                        <div class="card-body px-4 pt-3 d-flex align-items-center justify-content-center">
                            <Chart type="bar" :data="sigChartData" :options="sigChartOptions" class="w-100"
                                style="height: 250px;" />
                        </div>
                    </div>
                </div>

                <!-- PEI Radar Chart -->
                <div class="col-lg-4 col-md-12 mb-4">
                    <div class="card content-card shadow-sm border-0 h-100">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                            <h6 class="font-weight-bold text-dark mb-0 section-header">Plan Estratégico</h6>
                            <p class="text-muted small mb-0">Objetivos Estratégicos (Radar)</p>
                        </div>
                        <div class="card-body px-4 pt-3 d-flex align-items-center justify-content-center">
                            <div v-if="peiPerspectives.length > 0" class="w-100" style="height: 250px;">
                                <Chart type="radar" :data="peiChartData" :options="peiChartOptions" />
                            </div>
                            <div v-else class="text-center text-muted">
                                <small>No hay datos configurados</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Chart from 'primevue/chart';

const general = ref(0);
const processTypes = ref([]);
const lowestType = ref(null);
const indicatorTypes = ref([]);
const sigData = ref([]);
const peiPerspectives = ref([]);
const years = ref([]);
const selectedYear = ref(new Date().getFullYear());
const loading = ref(true);

// --- SIG Chart CONFIG ---
const sigChartData = computed(() => {
    return {
        labels: sigData.value.map(s => s.sistema),
        datasets: [
            {
                label: 'Promedio %',
                data: sigData.value.map(s => s.promedio_sistema),
                backgroundColor: sigData.value.map(s => {
                    if (s.promedio_sistema >= 90) return '#1cc88a';
                    if (s.promedio_sistema >= 70) return '#f6c23e';
                    return '#e74a3b';
                }),
                borderRadius: 4,
                borderSkipped: false,
                barThickness: 20
            }
        ]
    };
});

const sigChartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: '#ffffff',
            titleColor: '#5a5c69',
            bodyColor: '#5a5c69',
            borderColor: '#eaecf4',
            borderWidth: 1,
            callbacks: { label: (context) => `Promedio: ${context.raw}%` }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            max: 100,
            grid: { color: '#eaecf4', drawBorder: false },
            ticks: { color: '#858796', font: { size: 10 }, stepSize: 20 }
        },
        x: {
            grid: { display: false },
            ticks: { color: '#5a5c69', font: { weight: 'bold', size: 10 } }
        }
    }
});

// --- PEI Radar CONFIG ---
const peiChartData = computed(() => {
    return {
        labels: peiPerspectives.value.map(p => {
            // Truncate long names for radar labels
            const name = p.nombre;
            return name.length > 20 ? name.substring(0, 20) + '...' : name;
        }),
        datasets: [
            {
                label: 'Cumplimiento',
                data: peiPerspectives.value.map(p => p.promedio),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(54, 162, 235, 1)'
            }
        ]
    };
});

const peiChartOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false }
    },
    scales: {
        r: {
            angleLines: { color: '#eaecf4' },
            grid: { color: '#eaecf4' },
            pointLabels: {
                color: '#5a5c69',
                font: { size: 10, weight: 'bold' }
            },
            suggestedMin: 0,
            suggestedMax: 100,
            ticks: { stepSize: 20, display: false } // Hide numeric rings for cleaner look
        }
    }
});


const loadData = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/dashboard-procesos', {
            params: { year: selectedYear.value }
        });
        const data = response.data;

        general.value = data.general;
        lowestType.value = data.lowest_type;
        processTypes.value = data.process_types;
        indicatorTypes.value = data.indicator_types;
        sigData.value = data.sig_objectives;
        peiPerspectives.value = data.pei_perspectives;
        years.value = data.years;

    } catch (error) {
        console.error("Error loading dashboard data", error);
    } finally {
        setTimeout(() => loading.value = false, 300);
    }
};

const getTotalProcesses = () => {
    return processTypes.value.reduce((acc, t) => acc + t.cantidad_procesos, 0);
};

const getColorClass = (value) => {
    if (value >= 90) return 'success';
    if (value >= 70) return 'warning';
    return 'danger';
};

const getBgColorClass = (value) => {
    if (value >= 90) return 'bg-success';
    if (value >= 70) return 'bg-warning';
    return 'bg-danger';
};

const getLineColorClass = (value) => {
    if (value >= 90) return 'line-success';
    if (value >= 70) return 'line-warning';
    return 'line-danger';
};

onMounted(() => {
    loadData();
});
</script>

<style scoped>
/* Core Layout */
.dashboard-container {
    background-color: #f8f9fa;
    min-height: 100vh;
    padding: 1.5rem;
    font-family: 'Inter', sans-serif;
}

/* Animations */
.fade-in-down {
    animation: fadeInDown 0.6s ease-out;
}

.animated-fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

.animated-slide-in {
    animation: slideInLeft 0.5s ease-out;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
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

@keyframes slideInLeft {
    from {
        transform: translateX(-20px);
        opacity: 0;
    }

    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Cards Styling */
.main-card,
.process-card,
.content-card {
    border-radius: 12px;
    background: white;
}

.process-subcard {
    background: #fff;
    border-color: #eaecf4 !important;
}

.process-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.lift-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
}

/* Alerts */
.alert-inline {
    border: 1px solid rgba(220, 53, 69, 0.2);
    transition: all 0.3s;
}

.bg-light-danger {
    background-color: rgba(220, 53, 69, 0.05);
}

.border-danger-light {
    border-color: rgba(220, 53, 69, 0.15);
}

.alert-icon-wrapper-sm {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);
}

/* SVG Gauges */
.gauge-wrapper {
    width: 160px;
    height: 160px;
}

/* Slightly larger for main block */
.gauge-container-medium {
    width: 90px;
    height: 90px;
}

.gauge-mini-wrapper {
    width: 32px;
    height: 32px;
}

.circular-chart {
    display: block;
    margin: 0 auto;
    max-width: 100%;
    max-height: 250px;
}

.circle-bg {
    fill: none;
    stroke: #e9ecef;
    stroke-width: 2.5;
}

.circle {
    fill: none;
    stroke-width: 2.5;
    stroke-linecap: round;
    animation: progress 1.2s ease-out forwards;
}

.circle.success {
    stroke: #1cc88a;
}

.circle.warning {
    stroke: #f6c23e;
}

.circle.danger {
    stroke: #e74a3b;
}

.percentage-large {
    fill: #5a5c69;
    font-weight: 800;
    font-size: 0.5em;
    /* Reduced from 0.55em */
    text-anchor: middle;
}

.percentage-medium {
    fill: #5a5c69;
    font-weight: 700;
    font-size: 0.55em;
    /* Reduced from 0.65em */
    text-anchor: middle;
}

@keyframes progress {
    0% {
        stroke-dasharray: 0 100;
    }
}

/* Progress Bottom Line */
.progress-bar-bottom {
    height: 6px;
    width: 100%;
    transition: width 0.3s;
}

.line-success {
    background: #1cc88a;
}

.line-warning {
    background: #f6c23e;
}

.line-danger {
    background: #e74a3b;
}

/* Status Dots & Utils */
.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.2;
}

.leading-tight {
    line-height: 1.25;
}

.letter-spacing-1 {
    letter-spacing: 1px;
}

/* Interactive Rows */
.hover-bg:hover {
    background-color: #f8f9fc;
}

.hover-bg-light:hover {
    background-color: #fafbfc;
    border-color: #d1d3e2 !important;
}

.opacity-60 {
    opacity: 0.6;
}

/* Utilities */
.border-right-lg {
    border-right: 0;
}

@media (min-width: 992px) {
    .border-right-lg {
        border-right: 1px solid #eaecf4;
    }
}
</style>
