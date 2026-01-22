<template>
    <div class="dashboard-container">
        <!-- Header Section (Unified Style) -->
        <div class="card shadow-sm border-0 mb-4 fade-in-down">
            <div class="card-header bg-white p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-shield-virus mr-2 text-primary"></i>Tablero de Gestión de Riesgos
                        </h4>
                        <p class="text-muted small mb-0">Monitoreo de Amenazas, Impactos y Tratamientos</p>
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

                        <!-- Year Selector -->
                        <div class="d-inline-flex align-items-center bg-light p-2 rounded shadow-sm">
                            <label class="mb-0 mr-2 font-weight-bold text-muted small text-uppercase">Periodo:</label>
                            <div class="position-relative d-inline-block mr-3">
                                <button
                                    class="btn btn-sm btn-outline-primary font-weight-bold dropdown-toggle shadow-sm bg-white border-0"
                                    type="button" @click="toggleYearDropdown" style="font-size: 0.85rem;">
                                    <i class="far fa-calendar-alt mr-2"></i>{{ selectedYearsLabel }}
                                </button>
                                <div v-if="isYearDropdownOpen" class="dropdown-menu show p-3 border-0 shadow-lg mt-2"
                                    style="position: absolute; right: 0; min-width: 200px; z-index: 1050; border-radius: 0.75rem;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6
                                            class="dropdown-header px-0 text-uppercase small font-weight-bold text-muted mb-0">
                                            Periodos</h6>
                                        <button class="btn btn-link btn-sm p-0 text-muted" @click="selectAllYears"
                                            style="font-size: 0.7rem;">Todas</button>
                                    </div>
                                    <div v-for="year in years" :key="year" class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input" :id="'year-' + year"
                                            :value="year" v-model="selectedYear" @change="loadData">
                                        <label class="custom-control-label font-weight-bold text-dark cursor-pointer"
                                            :for="'year-' + year" style="font-size: 0.85rem;">{{ year }}</label>
                                    </div>
                                </div>
                            </div>
                            <!-- Refresh Button -->
                            <button class="btn btn-sm btn-icon btn-light text-primary shadow-sm rounded-circle"
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
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
        </div>

        <div v-else class="animated-fade-in">
            <!-- Metrics Grid -->
            <div class="row mb-5">
                <!-- Total Riesgos -->
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden main-stat-card">
                        <div class="card-body p-4 d-flex flex-column justify-content-between position-relative z-1">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <h6 class="text-uppercase text-muted small font-weight-bold letter-spacing-1 mb-1">
                                        Total Riesgos</h6>
                                    <h2 class="font-weight-bolder text-dark mb-0 display-4">{{ stats.total }}</h2>
                                </div>
                                <div class="icon-circle bg-light-primary text-primary">
                                    <i class="fas fa-biohazard fa-lg"></i>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <div class="d-flex align-items-center text-muted small font-weight-bold">
                                    <span class="text-danger mr-2" v-if="stats.criticos > 0">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ stats.criticos }} críticos
                                        (Alto/Muy Alto)
                                    </span>
                                    <span class="text-success" v-else>
                                        <i class="fas fa-check-circle mr-1"></i>Sin riesgos críticos
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="dec-circle bg-primary opacity-5"></div>
                    </div>
                </div>

                <!-- Tasa de Atenuación / Tratamiento -->
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div class="card-body p-4 border-top border-primary border-width-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-uppercase text-muted small font-weight-bold letter-spacing-1 mb-0">En
                                    Tratamiento</h6>
                                <span class="badge badge-pill badge-soft-primary text-primary">{{ stats.enTratamiento
                                }}</span>
                            </div>
                            <div class="position-relative pt-2 pb-3">
                                <p class="mb-0 text-dark font-weight-bold display-4">{{ Math.round((stats.enTratamiento
                                    / (stats.total || 1)) * 100) }}%</p>
                                <small class="text-muted">Proporción bajo mitigación</small>
                            </div>
                            <div class="progress rounded-pill bg-light" style="height: 6px;">
                                <div class="progress-bar rounded-pill bg-primary"
                                    :style="{ width: (stats.enTratamiento / (stats.total || 1)) * 100 + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vencidos / Por Revisar -->
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="card border-0 shadow-sm h-100 hover-lift">
                        <div
                            class="card-body p-4 border-top border-warning border-width-3 text-center d-flex flex-column justify-content-center">
                            <h6 class="text-uppercase text-muted small font-weight-bold letter-spacing-1 mb-2">Por
                                Re-evaluar</h6>
                            <h2 class="font-weight-bold text-warning mb-1">{{ stats.vencidos }}</h2>
                            <p class="text-muted small mb-0">Fecha de revisión pasada</p>
                            <div class="mt-3">
                                <i class="fas fa-history fa-2x text-warning opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nivel Promedio Dashboard -->
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="card border-0 shadow-sm h-100 hover-lift bg-dark text-white">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="text-uppercase text-white-50 small font-weight-bold letter-spacing-1 mb-1">
                                    Impacto Promedio</h6>
                                <h2 class="font-weight-bold mb-0 display-4 text-white">{{ promedioRiesgoGlobal }}</h2>
                            </div>
                            <div class="mt-3">
                                <span class="badge badge-pill" :class="getBadgeClassFromValue(promedioRiesgoGlobal)">
                                    Promedio Global
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
                        <div class="card-header bg-primary border-0 pt-4 px-4 pb-3 "
                            style="border-radius: 1rem 1rem 0 0;">
                            <h6 class="font-weight-bold text-white mb-1">Distribución por Nivel de Riesgo (Inherente)
                            </h6>
                            <p class="text-white small mb-0">Mapa de calor según evaluación de probabilidad e impacto
                            </p>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="chart-container pt-4">
                                <canvas ref="chartCanvas"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Risk Alerts (Tabs Style) -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden">
                        <div class="card-header bg-white border-bottom pt-4 px-4 pb-0">
                            <div class="nav nav-pills nav-pills-sm mb-3" role="tablist">
                                <a class="nav-link active font-weight-bold px-3 py-1 mr-2 rounded-pill nav-link-primary"
                                    data-toggle="pill" href="#vencidos" role="tab">
                                    <i class="fas fa-clock mr-2"></i>Tratamientos Venc.
                                    <span class="badge badge-light ml-2">{{ accionesVencidas.length || 0 }}</span>
                                </a>
                                <a class="nav-link font-weight-bold px-3 py-1 rounded-pill nav-link-warning"
                                    data-toggle="pill" href="#riesgos-review" role="tab">
                                    <i class="fas fa-sync-alt mr-2"></i>Riesgos por Revisar
                                    <span class="badge badge-light ml-2">{{ stats.vencidos || 0 }}</span>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0 tab-content scrollable-list">
                            <!-- Acciones Vencidas Tab -->
                            <div class="tab-pane fade show active" id="vencidos" role="tabpanel">
                                <ul class="list-group list-group-flush">
                                    <li v-for="a in accionesVencidas" :key="'accion-' + a.id"
                                        class="list-group-item border-bottom-light px-4 py-3 hover-bg-gray transition-colors">
                                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 font-weight-bold text-dark text-truncate mr-2">{{
                                                a.ra_descripcion || 'Sin descripción' }}</h6>
                                            <span class="badge badge-soft-danger flex-shrink-0">Atrasado</span>
                                        </div>
                                        <p class="mb-1 text-muted small text-truncate" style="max-width: 90%;">Riesgo:
                                            {{ a.riesgo?.riesgo_cod }} - {{ a.riesgo?.riesgo_nombre }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-danger font-weight-bold">
                                                <i class="far fa-calendar-times mr-1"></i>{{
                                                    formatDate(a.ra_fecha_fin_planificada) }}
                                            </small>
                                            <small class="text-muted font-weight-bold"><i
                                                    class="far fa-user mr-1"></i>{{ a.ra_responsable }}</small>
                                        </div>
                                    </li>
                                    <li v-if="accionesVencidas.length === 0"
                                        class="list-group-item text-center text-muted py-5 border-0">
                                        <i class="fas fa-check-circle fa-2x mb-2 text-success opacity-50"></i>
                                        <p class="mb-0 small">¡Todo al día! Sin tratamientos vencidos.</p>
                                    </li>
                                </ul>
                            </div>

                            <!-- Riesgos por Revisar Tab -->
                            <div class="tab-pane fade" id="riesgos-review" role="tabpanel">
                                <ul class="list-group list-group-flush">
                                    <li v-for="r in riesgosVencidos" :key="'riesgo-' + r.id"
                                        class="list-group-item border-bottom-light px-4 py-3 hover-bg-gray transition-colors">
                                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 font-weight-bold text-dark text-truncate mr-2">{{
                                                r.riesgo_cod }}</h6>
                                            <span class="badge" :class="getBadgeClass(r.riesgo_nivel)">{{ r.riesgo_nivel
                                            }}</span>
                                        </div>
                                        <p class="mb-1 text-muted small text-truncate leading-tight">{{ r.riesgo_nombre
                                        }}</p>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-warning font-weight-bold">
                                                <i class="fas fa-calendar-day mr-1"></i>Revisión: {{
                                                    formatDate(r.riesgo_fecha_valoracion_rr) }}
                                            </small>
                                            <small class="text-muted"><i class="far fa-folder mr-1"></i>{{
                                                r.proceso?.proceso_nombre }}</small>
                                        </div>
                                    </li>
                                    <li v-if="riesgosVencidos.length === 0"
                                        class="list-group-item text-center text-muted py-5 border-0">
                                        <i class="fas fa-check-circle fa-2x mb-2 text-success opacity-50"></i>
                                        <p class="mb-0 small">¡Excelente! Todos los riesgos están evaluados.</p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen por Proceso -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary border-0 pt-4 px-4 pb-3 d-flex justify-content-between align-items-center"
                    style="border-radius: 1rem 1rem 0 0;">
                    <div>
                        <h6 class="font-weight-bold text-white mb-1">Exposición por Proceso</h6>
                        <p class="text-white small mb-0">Detalle de carga de riesgos y niveles promedio</p>
                    </div>
                </div>
                <div class="card-body px-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                <tr>
                                    <th class="pl-4 border-0">Proceso</th>
                                    <th class="text-center border-0">Riesgos Totales</th>
                                    <th class="text-center border-0">Críticos</th>
                                    <th class="text-center border-0">Valor Promedio</th>
                                    <th class="border-0" style="width: 25%;">Nivel Máximo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="proceso in resumenProcesos" :key="proceso.id" class="transition-bg">
                                    <td class="pl-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-box bg-light-primary text-primary mr-3">
                                                <i class="fas fa-project-diagram fa-sm"></i>
                                            </div>
                                            <div>
                                                <span class="d-block font-weight-bold text-dark">{{ proceso.nombre
                                                }}</span>
                                                <small class="text-muted">ID: {{ proceso.id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center font-weight-bold text-dark">{{ proceso.total }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-pill px-3 py-1 font-weight-bold"
                                            :class="proceso.criticos > 0 ? 'badge-soft-danger' : 'badge-soft-success'">
                                            {{ proceso.criticos }} Críticos
                                        </span>
                                    </td>
                                    <td class="text-center font-weight-bold text-muted">{{ proceso.promedioRiesgo }}
                                    </td>
                                    <td class="pr-4">
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-pill px-3 py-1 font-weight-bold mr-3"
                                                :class="getBadgeClass(proceso.nivelMaximo)">
                                                {{ proceso.nivelMaximo }}
                                            </span>
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar"
                                                    :class="getProgressBarClassFromNivel(proceso.nivelMaximo)"
                                                    :style="{ width: (proceso.promedioRiesgo / 100 * 100) + '%' }">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="resumenProcesos.length === 0">
                                    <td colspan="5" class="text-center py-5 text-muted">No se encontraron procesos con
                                        riesgos registrados.</td>
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
    ArcElement,
    Tooltip,
    Legend
} from 'chart.js';

ChartJS.register(ArcElement, Tooltip, Legend);

export default {
    name: 'DashboardRiesgos',
    data() {
        return {
            loading: true,
            mostrarTodos: false,
            esAdmin: false,

            // Period Selection
            years: [],
            selectedYear: [2025, 2026],
            isYearDropdownOpen: false,

            // Data Buckets
            stats: {
                total: 0, criticos: 0, enTratamiento: 0, vencidos: 0,
                distribucionNivel: {}, distribucionEstado: {}
            },
            accionesVencidas: [],
            resumenProcesos: [],
            riesgosRaw: [],

            // Chart
            chartInstance: null,
        };
    },
    computed: {
        selectedYearsLabel() {
            if (!this.selectedYear || this.selectedYear.length === 0) return 'Ninguno';
            if (this.selectedYear.length === 1) return this.selectedYear[0];
            if (this.selectedYear.length === this.years.length && this.years.length > 0) return 'Todos';
            return this.selectedYear.sort().join(', ');
        },
        riesgosVencidos() {
            return this.riesgosRaw.filter(r => r.riesgo_fecha_valoracion_rr && new Date(r.riesgo_fecha_valoracion_rr) < new Date()).slice(0, 10);
        },
        promedioRiesgoGlobal() {
            if (!this.riesgosRaw.length) return 0;
            const sum = this.riesgosRaw.reduce((acc, r) => acc + (r.riesgo_valor || 0), 0);
            return Math.round(sum / this.riesgosRaw.length * 10) / 10;
        }
    },
    methods: {
        toggleYearDropdown() {
            this.isYearDropdownOpen = !this.isYearDropdownOpen;
        },
        selectAllYears() {
            if (this.selectedYear.length === this.years.length) {
                this.selectedYear = [];
            } else {
                this.selectedYear = [...this.years];
            }
            this.loadData();
        },
        populateYears() {
            const currentYear = new Date().getFullYear();
            for (let i = currentYear; i >= 2024; i--) {
                this.years.push(i);
            }
            this.years = [...new Set(this.years)].sort((a, b) => b - a);
        },
        async loadData() {
            this.loading = true;
            try {
                let url = '/api/dashboard/riesgos';
                const params = new URLSearchParams();
                if (this.esAdmin) params.append('mostrarTodos', this.mostrarTodos);

                if (this.selectedYear && this.selectedYear.length > 0) {
                    this.selectedYear.forEach(y => params.append('year[]', y));
                }

                const response = await axios.get(`${url}?${params.toString()}`);
                const data = response.data;

                this.stats = data.stats;
                this.accionesVencidas = data.accionesVencidas;
                this.resumenProcesos = data.resumenProcesos;
                this.riesgosRaw = data.riesgos;

                this.processChart(this.stats.distribucionNivel);

            } catch (error) {
                console.error('Error loading risk dashboard data:', error);
            } finally {
                setTimeout(() => this.loading = false, 300);
            }
        },

        processChart(distribucion) {
            const labels = ['Bajo', 'Medio', 'Alto', 'Muy Alto'];
            const quantities = labels.map(l => distribucion[l] || 0);

            const colors = ['#10b981', '#f59e0b', '#ef4444', '#7f1d1d'];

            this.$nextTick(() => {
                setTimeout(() => this.renderChartInstance(labels, quantities, colors), 350);
            });
        },

        renderChartInstance(labels, data, colors) {
            if (this.chartInstance) this.chartInstance.destroy();
            const canvas = this.$refs.chartCanvas;
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            this.chartInstance = new ChartJS(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data, backgroundColor: colors, borderWidth: 3, borderColor: '#fff',
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
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
                        }
                    },
                    layout: { padding: 20 },
                    cutout: '70%'
                }
            });
        },

        // Helpers
        async verificarRolUsuario() {
            try {
                if (window.App?.user?.roles) {
                    const roles = window.App.user.roles;
                    this.esAdmin = Array.isArray(roles) ? (roles.includes('admin') || roles.includes('super-admin')) : (roles === 'admin' || roles === 'super-admin');
                } else {
                    const response = await axios.get('/api/user');
                    const roles = response.data.roles;
                    this.esAdmin = Array.isArray(roles) ? (roles.includes('admin') || roles.includes('super-admin')) : (roles === 'admin' || roles === 'super-admin');
                }
                this.loadData();
            } catch {
                this.esAdmin = false;
                this.loadData();
            }
        },
        formatDate(date) {
            if (!date) return 'N/A';
            return new Date(date).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
        },
        getBadgeClass(nivel) {
            if (nivel === 'Muy Alto') return 'badge-soft-danger text-danger bg-dark';
            if (nivel === 'Alto') return 'badge-soft-danger text-danger';
            if (nivel === 'Medio') return 'badge-soft-warning text-warning';
            if (nivel === 'Bajo') return 'badge-soft-success text-success';
            return 'badge-soft-secondary';
        },
        getBadgeClassFromValue(val) {
            if (val >= 80) return 'bg-danger text-white';
            if (val >= 48) return 'bg-warning text-dark';
            if (val >= 32) return 'bg-info text-white';
            return 'bg-success text-white';
        },
        getProgressBarClassFromNivel(nivel) {
            if (nivel === 'Muy Alto' || nivel === 'Alto') return 'bg-danger';
            if (nivel === 'Medio') return 'bg-warning';
            return 'bg-success';
        }
    },
    mounted() {
        this.populateYears();
        this.verificarRolUsuario();
    },
    beforeUnmount() {
        if (this.chartInstance) this.chartInstance.destroy();
    }
};
</script>

<style scoped>
/* Core Layout */
.dashboard-container {
    background-color: #f3f4f6;
    min-height: 100vh;
    padding: 2rem;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    color: #1f2937;
}

/* Animations */
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

/* Cards & KPI Styling */
.card {
    border-radius: 1rem;
    border: none;
}

.shadow-sm {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
}

.main-stat-card {
    overflow: hidden;
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

.hover-lift {
    transition: transform 0.2s, box-shadow 0.2s;
}

.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
}

/* Tabs & Navs */
.nav-pills-sm .nav-link {
    font-size: 0.85rem;
    border-radius: 0.5rem;
    color: #64748b;
    transition: all 0.2s;
    background: #f8fafc;
}

.nav-pills .nav-link.nav-link-primary.active {
    background-color: #3b82f6 !important;
    color: white !important;
}

.nav-pills .nav-link.nav-link-warning.active {
    background-color: #f59e0b !important;
    color: white !important;
}

/* Lists */
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

/* Custom Badges */
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

.badge-soft-primary {
    background-color: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

/* Table Utilities */
.icon-box {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-light-primary {
    background-color: rgba(59, 130, 246, 0.1);
}

.transition-bg {
    transition: background-color 0.15s ease-in-out;
}

.transition-bg:hover {
    background-color: #f9fafb;
}

.chart-container {
    height: 350px;
    width: 100%;
}

/* General Utilities */
.letter-spacing-1 {
    letter-spacing: 0.05em;
}

.leading-tight {
    line-height: 1.2;
}

.border-width-3 {
    border-top-width: 4px !important;
}
</style>
