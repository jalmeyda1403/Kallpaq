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
                    <div class="card content-card shadow-sm border-0 h-100 hover-lift transition-all">
                        <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                            <h6 class="font-weight-bold text-dark mb-0 section-header">Plan Estratégico</h6>
                            <p class="text-muted small mb-0">Desempeño de Objetivos Estratégicos</p>
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

            <!-- BLOCK 3: Stakeholder Requirements Compliance -->
            <div class="card main-card shadow-sm border-0 mb-4 overflow-hidden" v-if="stakeholdersData">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <h6 class="font-weight-bold text-dark mb-0 section-header">Cumplimiento de Requisitos</h6>
                    <p class="text-muted small mb-0">Gestión de Partes Interesadas</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row align-items-center">
                        <!-- Global Stats -->
                        <div class="col-md-3 border-right-lg text-center mb-4 mb-md-0 py-3">
                            <div class="position-relative d-inline-block">
                                <h1 class="font-weight-bolder text-dark mb-0 display-4 tracking-tight">{{ stakeholdersData.global_percentage }}<span class="text-muted h4">%</span></h1>
                            </div>
                            <span class="badge badge-pill badge-light-primary text-primary font-weight-bold px-3 py-2 mb-3 mt-1">
                                Cumplimiento Global
                            </span>
                            
                            <div class="row no-gutters mt-3 mx-4">
                                <div class="col-6 border-right">
                                    <span class="d-block font-weight-bold h5 text-dark mb-0">{{ stakeholdersData.total_requirements }}</span>
                                    <small class="text-muted text-uppercase font-weight-bold" style="font-size: 10px">Total</small>
                                </div>
                                <div class="col-6">
                                    <span class="d-block font-weight-bold h5 text-success mb-0">{{ stakeholdersData.implemented_requirements }}</span>
                                    <small class="text-muted text-uppercase font-weight-bold" style="font-size: 10px">Impl.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Top Stakeholders List -->
                        <div class="col-md-9 px-lg-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-uppercase text-muted font-weight-bold small letter-spacing-1 mb-0">Top 5 - Mayor Volumetría</h6>
                            </div>
                            <div class="table-responsive rounded-lg border-0">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                        <tr>
                                            <th class="border-0 pl-4 py-3">Parte Interesada</th>
                                            <th class="border-0 text-center py-3">Requisitos</th>
                                            <th class="border-0 py-3" style="width: 40%">Progreso</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="stakeholder in stakeholdersData.details.slice(0, 5)" :key="stakeholder.id" class="border-bottom-custom transition-bg">
                                            <td class="pl-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-initial rounded-circle mr-3 shadow-sm d-flex align-items-center justify-content-center text-white font-weight-bold" 
                                                         :class="getAvatarColor(stakeholder.nombre)"
                                                         style="width: 32px; height: 32px; min-width: 32px; font-size: 12px;">
                                                        {{ getInitials(stakeholder.nombre) }}
                                                    </div>
                                                    <div>
                                                        <span class="font-weight-bold text-dark d-block text-truncate" style="max-width: 200px">{{ stakeholder.nombre }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center py-3">
                                                <div class="d-flex flex-column">
                                                    <span class="font-weight-bold text-dark">{{ stakeholder.implementado }}/{{ stakeholder.total }}</span>
                                                </div>
                                            </td>
                                            <td class="py-3 pr-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 rounded-pill bg-light" style="height: 8px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);">
                                                        <div class="progress-bar rounded-pill" role="progressbar" 
                                                            :class="getBgColorClass(stakeholder.porcentaje)"
                                                            :style="{ width: stakeholder.porcentaje + '%' }"></div>
                                                    </div>
                                                    <span class="ml-3 font-weight-bold text-dark small" style="width: 35px">{{ stakeholder.porcentaje }}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="stakeholdersData.details.length === 0">
                                            <td colspan="3" class="text-center text-muted py-4">No hay información disponible</td>
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
const stakeholdersData = ref(null);
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
                borderRadius: 6, // Softer bars
                borderSkipped: false,
                barThickness: 24 // Thicker bars
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
            titleColor: '#2d3748',
            bodyColor: '#718096',
            borderColor: '#e2e8f0',
            borderWidth: 1,
            padding: 10,
            displayColors: false,
            callbacks: { label: (context) => `Cumplimiento: ${context.raw}%` }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            max: 100,
            grid: { color: '#f7fafc', drawBorder: false },
            ticks: { color: '#a0aec0', font: { size: 11 }, stepSize: 20 }
        },
        x: {
            grid: { display: false },
            ticks: { color: '#4a5568', font: { weight: '600', size: 11 } }
        }
    }
});

// --- PEI Radar CONFIG ---
const peiChartData = computed(() => {
    return {
        labels: peiPerspectives.value.map(p => {
            const name = p.nombre;
            return name.length > 15 ? name.substring(0, 15) + '...' : name;
        }),
        datasets: [
            {
                label: 'Cumplimiento',
                data: peiPerspectives.value.map(p => p.promedio),
                backgroundColor: 'rgba(66, 153, 225, 0.2)', // Taildwind Blue 500 alpha
                borderColor: '#4299e1', 
                pointBackgroundColor: '#4299e1',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#4299e1',
                pointRadius: 4,
                pointHoverRadius: 6
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
            angleLines: { color: '#edf2f7' },
            grid: { color: '#edf2f7' },
            pointLabels: {
                color: '#4a5568',
                font: { size: 11, weight: '600' }
            },
            suggestedMin: 0,
            suggestedMax: 100,
            ticks: { display: false, stepSize: 20 }
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
        stakeholdersData.value = data.stakeholders_compliance;
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

// Utils for Avatars
const getInitials = (name) => {
    if (!name) return '';
    const words = name.trim().split(/\s+/);
    if(words.length === 1) return words[0].substring(0,2).toUpperCase();
    return (words[0][0] + words[1][0]).toUpperCase();
};

const getAvatarColor = (name) => {
    const colors = ['bg-primary', 'bg-danger', 'bg-success', 'bg-warning text-dark', 'bg-info', 'bg-secondary'];
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    const index = Math.abs(hash) % colors.length;
    return colors[index];
};

onMounted(() => {
    loadData();
});
</script>

<style scoped>
/* Modern Font Stack */
.dashboard-container {
    background-color: #f3f4f6; /* Gray 100 */
    min-height: 100vh;
    padding: 2rem;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    color: #1f2937; /* Gray 800 */
}

/* Animations */
.fade-in-down { animation: fadeInDown 0.6s ease-out; }
.animated-fade-in { animation: fadeIn 0.5s ease-in-out; }
.animated-slide-in { animation: slideInLeft 0.5s ease-out; }

@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-15px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes slideInLeft {
    from { transform: translateX(-15px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

/* Base Card Styling */
.main-card, .process-card, .content-card, .card {
    border-radius: 1rem; /* 16px */
}

.shadow-sm {
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
}

/* Hover Lift Effect */
.hover-lift {
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s;
}
.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
}

.transition-all { transition-property: all; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 200ms; }

/* Custom Badge */
.badge-light-primary {
    background-color: rgba(66, 153, 225, 0.1);
    color: #3182ce;
}

/* Table Polish */
.table-hover tbody tr:hover {
    background-color: #f9fafb;
}
.transition-bg {
    transition: background-color 0.15s ease-in-out;
}

/* SVG Gauges - Refined */
.gauge-wrapper { width: 150px; height: 150px; }
.gauge-container-medium { width: 80px; height: 80px; }
.gauge-mini-wrapper { width: 30px; height: 30px; }

.circular-chart { display: block; margin: 0 auto; max-width: 100%; max-height: 250px; }
.circle-bg { fill: none; stroke: #edf2f7; stroke-width: 2.5; }
.circle { fill: none; stroke-width: 2.5; stroke-linecap: round; animation: progress 1.2s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
.circle.success { stroke: #10b981; }
.circle.warning { stroke: #f59e0b; }
.circle.danger { stroke: #ef4444; }

.percentage-large { fill: #2d3748; font-weight: 800; font-size: 0.5em; text-anchor: middle; }
.percentage-medium { fill: #4a5568; font-weight: 700; font-size: 0.55em; text-anchor: middle; }

@keyframes progress { 0% { stroke-dasharray: 0 100; } }

/* Process Subcards */
.process-subcard {
    background: #ffffff;
    border: 1px solid #edf2f7 !important;
    border-radius: 0.75rem;
}

/* Utilities */
.tracking-tight { letter-spacing: -0.025em; }
.letter-spacing-1 { letter-spacing: 0.05em; }
.rounded-xl { border-radius: 0.75rem; }

.border-right-lg { border-right: 0; }
@media (min-width: 992px) {
    .border-right-lg { border-right: 1px solid #edf2f7; }
}

.status-dot { width: 8px; height: 8px; border-radius: 50%; }
.bg-success { background-color: #10b981 !important; }
.bg-warning { background-color: #f59e0b !important; }
.bg-danger { background-color: #ef4444 !important; }

/* Scrollbars inside cards if needed */
.table-responsive::-webkit-scrollbar { height: 6px; }
.table-responsive::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 3px; }
</style>
