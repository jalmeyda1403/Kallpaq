<template>
    <div class="dashboard-container">
        <!-- Header Section (Unified Style) -->
        <div class="card shadow-sm border-0 mb-4 fade-in-down">
            <div class="card-header bg-white p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-tasks mr-2 text-danger"></i>Dashboard de Requerimientos
                        </h4>
                        <p class="text-muted small mb-0">Monitoreo estratégico del cumplimiento y estado de requerimientos</p>
                    </div>
                    <div class="mt-2 mt-md-0">
                        <div class="d-inline-flex align-items-center bg-light p-2 rounded shadow-sm">
                            <label class="mb-0 mr-2 font-weight-bold text-muted small text-uppercase">Periodo:</label>
                            <div class="position-relative d-inline-block mr-3">
                                <button class="btn btn-sm btn-outline-danger font-weight-bold dropdown-toggle shadow-sm bg-white border-0" type="button" @click="toggleYearDropdown" style="font-size: 0.85rem;">
                                    <i class="far fa-calendar-alt mr-2"></i>{{ selectedYearsLabel }}
                                </button>
                                <div v-if="isYearDropdownOpen" class="dropdown-menu show p-3 border-0 shadow-lg mt-2" style="position: absolute; right: 0; min-width: 200px; z-index: 1050; border-radius: 0.75rem;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="dropdown-header px-0 text-uppercase small font-weight-bold text-muted mb-0">Periodos</h6>
                                        <button class="btn btn-link btn-sm p-0 text-muted" @click="selectAllYears" style="font-size: 0.7rem;">Todas</button>
                                    </div>
                                    <div v-for="year in years" :key="year" class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input" :id="'year-' + year" :value="year" v-model="selectedYear" @change="refreshAllData">
                                        <label class="custom-control-label font-weight-bold text-dark cursor-pointer" :for="'year-' + year" style="font-size: 0.85rem;">{{ year }}</label>
                                    </div>
                                </div>
                            </div>
                            <!-- Refresh Button -->
                            <button class="btn btn-sm btn-icon btn-light text-danger shadow-sm rounded-circle" @click="refreshAllData" title="Actualizar" style="width: 32px; height: 32px;">
                                <i class="fas fa-sync-alt" :class="{ 'fa-spin': isLoadingAll }"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metrics Grid -->
        <div class="row mb-5 animated-fade-in">
            <!-- Total Card -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden main-stat-card">
                    <div class="card-body p-4 d-flex flex-column justify-content-between position-relative z-1">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h6 class="text-uppercase text-muted small font-weight-bold letter-spacing-1 mb-1">Total Requ.</h6>
                                <h2 class="font-weight-bolder text-dark mb-0 display-4">{{ stats.total }}</h2>
                            </div>
                            <div class="icon-circle bg-light-danger text-danger">
                                <i class="fas fa-layer-group fa-lg"></i>
                            </div>
                        </div>
                        <div class="mt-auto">
                            <div class="d-flex align-items-center text-muted small font-weight-bold">
                                <span class="text-danger mr-2" v-if="stats.sin_asignar > 0">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ stats.sin_asignar }} sin asignar
                                </span>
                                <span class="text-success" v-else>
                                    <i class="fas fa-check-circle mr-1"></i>Todos asignados
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Decorative Background Circle -->
                    <div class="dec-circle bg-danger opacity-5"></div>
                </div>
            </div>

            <!-- Global Efficiency Card -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-uppercase text-muted small font-weight-bold letter-spacing-1 mb-0">Eficacia Global</h6>
                            <span class="badge badge-pill" :class="getBadgeClass(stats.eficacia)">{{ stats.eficacia }}%</span>
                        </div>
                        <div class="position-relative pt-2 pb-3">
                            <div class="progress rounded-pill bg-light" style="height: 10px;">
                                <div class="progress-bar rounded-pill" role="progressbar" 
                                    :class="getBgColorClass(stats.eficacia)"
                                    :style="{ width: stats.eficacia + '%' }"></div>
                            </div>
                        </div>
                        <p class="text-muted small mb-0 mt-2">Promedio de cierre a tiempo</p>
                    </div>
                </div>
            </div>

            <!-- Status Breakdown -->
            <div class="col-lg-6 col-md-12">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="row h-100 align-items-center">
                            <div class="col-md-3 text-center border-right-md mb-3 mb-md-0">
                                <h4 class="font-weight-bold text-success mb-0">{{ stats.finalizados }}</h4>
                                <small class="text-muted text-uppercase font-weight-bold">Finalizados</small>
                            </div>
                            <div class="col-md-3 text-center border-right-md mb-3 mb-md-0">
                                <h4 class="font-weight-bold text-info mb-0">{{ stats.enProceso }}</h4>
                                <small class="text-muted text-uppercase font-weight-bold">En Proceso</small>
                            </div>
                            <div class="col-md-3 text-center border-right-md mb-3 mb-md-0">
                                <h4 class="font-weight-bold text-danger mb-0">{{ stats.vencidos }}</h4>
                                <small class="text-muted text-uppercase font-weight-bold">Vencidos</small>
                            </div>
                            <div class="col-md-3 text-center">
                                <h4 class="font-weight-bold text-secondary mb-0">{{ stats.desestimados }}</h4>
                                <small class="text-muted text-uppercase font-weight-bold">Desestimados</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <!-- Main Chart -->
            <div class="col-lg-7 mb-4 mb-lg-0">
                <div class="card border-0 shadow-sm h-100 hover-lift card-chart-wrapper">
                    <div class="card-header bg-danger border-0 pt-4 px-4 pb-3 d-flex justify-content-between align-items-center" style="border-radius: 1rem 1rem 0 0;">
                        <div>
                            <h6 class="font-weight-bold text-white mb-1">Evolución Mensual</h6>
                            <p class="text-white small mb-0">Desempeño acumulado durante el periodo {{ selectedYearsLabel }}</p>
                        </div>
                        <!-- Year selector removed from here, now global -->
                    </div>
                    <div class="card-body px-4 pb-4 chart-container position-relative">
                         <div v-if="isLoadingChart" class="d-flex align-items-center justify-content-center h-100 position-absolute w-100" style="top:0; left:0; z-index:10; background:rgba(255,255,255,0.8);">
                            <div class="spinner-border text-danger" role="status"></div>
                        </div>
                        <canvas ref="chartCanvas"></canvas>
                    </div>
                </div>
            </div>

            <!-- Risk Alerts -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                        <div class="nav nav-pills nav-pills-sm" role="tablist">
                            <a class="nav-link active font-weight-bold px-3 py-1 mr-2 rounded-pill nav-link-danger" data-toggle="pill" href="#vencidos" role="tab">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Vencidos
                                <span class="badge badge-light ml-2">{{ vencidos.total || 0 }}</span>
                            </a>
                            <a class="nav-link font-weight-bold px-3 py-1 rounded-pill nav-link-warning" data-toggle="pill" href="#riesgo" role="tab">
                                <i class="fas fa-clock mr-2"></i>En Riesgo
                                <span class="badge badge-light ml-2">{{ enRiesgo.total || 0 }}</span>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0 tab-content scrollable-list">
                        <!-- Vencidos Tab -->
                        <div class="tab-pane fade show active" id="vencidos" role="tabpanel">
                            <ul class="list-group list-group-flush">
                                <li v-if="isLoadingVencidos" class="list-group-item text-center py-5 border-0">
                                    <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
                                </li>
                                <template v-else>
                                    <li v-for="r in vencidos.data" :key="'vencido-' + r.id" class="list-group-item border-bottom-light px-4 py-3 hover-bg-gray transition-colors">
                                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 font-weight-bold text-dark">Req #{{ r.id }}</h6>
                                            <span class="badge badge-soft-danger">Vencido</span>
                                        </div>
                                        <p class="mb-1 text-muted small text-truncate" style="max-width: 90%;">{{ r.asunto || 'Sin asunto' }}</p>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-danger font-weight-bold">
                                                <i class="far fa-calendar-times mr-1"></i>{{ formatDate(r.fecha_limite) }}
                                            </small>
                                            <small class="text-muted font-weight-bold">Avance: {{ r.avance ? r.avance.avance_registrado : 0 }}%</small>
                                        </div>
                                    </li>
                                    <li v-if="vencidos.data.length === 0" class="list-group-item text-center text-muted py-5 border-0">
                                        <i class="fas fa-check-circle fa-2x mb-2 text-success opacity-50"></i>
                                        <p class="mb-0 small">¡Excelente! No hay requerimientos vencidos.</p>
                                    </li>
                                </template>
                            </ul>
                            <!-- Pagination Helper for Vencidos -->
                            <div v-if="vencidos.last_page > 1" class="px-3 py-2 border-top bg-light d-flex justify-content-center">
                                <button class="btn btn-sm btn-link text-muted" :disabled="vencidos.current_page === 1" @click.prevent="fetchVencidos(vencidos.current_page - 1)"><i class="fas fa-chevron-left"></i></button>
                                <span class="align-self-center small font-weight-bold mx-2">{{ vencidos.current_page }} / {{ vencidos.last_page }}</span>
                                <button class="btn btn-sm btn-link text-muted" :disabled="vencidos.current_page === vencidos.last_page" @click.prevent="fetchVencidos(vencidos.current_page + 1)"><i class="fas fa-chevron-right"></i></button>
                            </div>
                        </div>

                        <!-- En Riesgo Tab -->
                        <div class="tab-pane fade" id="riesgo" role="tabpanel">
                             <ul class="list-group list-group-flush">
                                <li v-if="isLoadingEnRiesgo" class="list-group-item text-center py-5 border-0">
                                    <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
                                </li>
                                <template v-else>
                                    <li v-for="r in enRiesgo.data" :key="'riesgo-' + r.id" class="list-group-item border-bottom-light px-4 py-3 hover-bg-gray transition-colors">
                                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 font-weight-bold text-dark">Req #{{ r.id }}</h6>
                                             <div class="d-flex align-items-center">
                                                <div class="avatar-xs rounded-circle bg-light-primary text-primary d-flex align-items-center justify-content-center mr-2 font-weight-bold" style="width:24px; height:24px; font-size:10px;">
                                                    {{ r.especialista ? getInitials(r.especialista.nombre) : '?' }}
                                                </div>
                                            </div>
                                        </div>
                                        <p class="mb-1 text-muted small text-truncate">{{ r.asunto || 'Sin asunto' }}</p>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-warning font-weight-bold">
                                                <i class="far fa-clock mr-1"></i>{{ formatDate(r.fecha_limite) }}
                                            </small>
                                             <div class="progress rounded-pill bg-light" style="width: 60px; height: 6px;">
                                                <div class="progress-bar bg-warning rounded-pill" role="progressbar" :style="{ width: (r.avance ? r.avance.avance_registrado : 0) + '%' }"></div>
                                            </div>
                                        </div>
                                    </li>
                                     <li v-if="enRiesgo.data.length === 0" class="list-group-item text-center text-muted py-5 border-0">
                                        <i class="fas fa-shield-alt fa-2x mb-2 text-success opacity-50"></i>
                                        <p class="mb-0 small">Todo bajo control.</p>
                                    </li>
                                </template>
                            </ul>
                            <!-- Pagination Helper for Riesgo -->
                            <div v-if="enRiesgo.last_page > 1" class="px-3 py-2 border-top bg-light d-flex justify-content-center">
                                <button class="btn btn-sm btn-link text-muted" :disabled="enRiesgo.current_page === 1" @click.prevent="fetchEnRiesgo(enRiesgo.current_page - 1)"><i class="fas fa-chevron-left"></i></button>
                                <span class="align-self-center small font-weight-bold mx-2">{{ enRiesgo.current_page }} / {{ enRiesgo.last_page }}</span>
                                <button class="btn btn-sm btn-link text-muted" :disabled="enRiesgo.current_page === enRiesgo.last_page" @click.prevent="fetchEnRiesgo(enRiesgo.current_page + 1)"><i class="fas fa-chevron-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Specialists Table (Year selector removed as it's now global) -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-danger border-0 pt-4 px-4 pb-3 d-flex justify-content-between align-items-center" style="border-radius: 1rem 1rem 0 0;">
                <div>
                   <h6 class="font-weight-bold text-white mb-1">Desempeño por Especialista</h6>
                   <p class="text-white small mb-0">Productividad y eficacia del equipo</p>
                </div>
            </div>
            <div class="card-body px-0">
                <div v-if="isLoadingEspecialistas" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <div v-else class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                            <tr>
                                <th class="pl-4 border-0">Especialista</th>
                                <th class="text-center border-0">Carga</th>
                                <th class="text-center border-0">Estadísticas</th>
                                <th class="text-center border-0" style="width: 25%;">Avance</th>
                                <th class="text-right pr-4 border-0">Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="esp in especialistas" :key="esp.id" class="transition-bg">
                                <td class="pl-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle shadow-sm mr-3 d-flex align-items-center justify-content-center text-white font-weight-bold position-relative"
                                            :class="getAvatarColor(esp.nombre)"
                                            style="width: 40px; height: 40px; font-size: 14px;">
                                            {{ getInitials(esp.nombre) }}
                                        </div>
                                        <div>
                                            <span class="d-block font-weight-bold text-dark">{{ esp.nombre }}</span>
                                            <small class="text-muted">{{ esp.sigla }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-light-primary px-3 py-1 font-weight-bold">{{ esp.total_asignados }} Asig.</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center small">
                                        <span class="mx-2 text-danger font-weight-bold trigger-tooltip" title="Vencidos"><i class="fas fa-exclamation-triangle mr-1"></i>{{ esp.total_vencidos }}</span>
                                        <span class="mx-2 text-success font-weight-bold trigger-tooltip" title="Finalizados"><i class="fas fa-check mr-1"></i>{{ esp.total_finalizados }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="px-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small class="font-weight-bold text-muted">Avance</small>
                                            <small class="font-weight-bold text-dark">{{ esp.promedioAvance }}%</small>
                                        </div>
                                        <div class="progress rounded-pill bg-light" style="height: 6px;">
                                            <div class="progress-bar rounded-pill" :class="getProgressBarClass(esp.promedioAvance)" role="progressbar" :style="{ width: esp.promedioAvance + '%' }"></div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between mb-1 mt-2">
                                            <small class="font-weight-bold text-muted">Eficacia</small>
                                            <small class="font-weight-bold text-purple">{{ esp.efectividad }}%</small>
                                        </div>
                                        <div class="progress rounded-pill bg-light" style="height: 6px;">
                                            <div class="progress-bar bg-purple rounded-pill" role="progressbar" :style="{ width: esp.efectividad + '%' }"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right pr-4">
                                     <button class="btn btn-sm btn-light btn-icon shadow-sm text-primary hover-primary" @click.prevent="openDetalleModal(esp)">
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Detalle -->
        <div class="modal fade" ref="detalleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
                    <div class="modal-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                        <div>
                             <h5 class="modal-title font-weight-bold text-dark">Detalle de Requerimientos</h5>
                             <p class="text-muted small mb-0" v-if="detalleEspecialista.nombre">Asignado a: <strong class="text-primary">{{ detalleEspecialista.nombre }}</strong></p>
                        </div>
                        <button type="button" class="close text-muted" @click="closeDetalleModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body px-4 pt-4 pb-5">
                       <div v-if="isLoadingDetalle" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                         <div v-else class="table-responsive">
                            <table class="table table-hover table-sm align-middle mb-0">
                                <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                                    <tr>
                                        <th class="border-0 rounded-left pl-3">ID</th>
                                        <th class="border-0">Asunto</th>
                                        <th class="text-center border-0">Estado</th>
                                        <th class="text-center border-0">Límite</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="req in detalleRequerimientos" :key="req.id">
                                        <td class="pl-3 font-weight-bold text-muted">#{{ req.id }}</td>
                                        <td class="text-truncate" style="max-width: 250px;" :title="req.asunto">{{ req.asunto }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-pill font-weight-normal px-2"
                                                :class="{
                                                    'badge-soft-success text-success': req.estado === 'finalizado',
                                                    'badge-soft-warning text-warning': req.estado === 'en_proceso' || req.estado === 'asignado',
                                                    'badge-soft-danger text-danger': req.estado === 'vencido' || (req.estado === 'asignado' && new Date(req.fecha_limite) < new Date()),
                                                }">
                                                {{ req.estado }}
                                            </span>
                                        </td>
                                        <td class="text-center small font-weight-bold">
                                            {{ formatDate(req.fecha_limite) }}
                                        </td>
                                    </tr>
                                    <tr v-if="detalleRequerimientos.length === 0">
                                        <td colspan="4" class="text-center text-muted py-3">Sin resultados</td>
                                    </tr>
                                </tbody>
                            </table>
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
            isLoadingAll: false,
            // Stats
            isLoadingStats: false,
            stats: { total: 0, sin_asignar: 0, desestimados: 0, enProceso: 0, vencidos: 0, finalizados: 0, eficacia: 0 },
            
            // Chart
            isLoadingChart: false,
            chartInstance: null,
            
            // Global Filter
            years: [],
            selectedYear: [new Date().getFullYear()],
            isYearDropdownOpen: false,
            
            // Alertas
            isLoadingVencidos: false,
            isLoadingEnRiesgo: false,
            vencidos: { data: [], total: 0, current_page: 1, last_page: 1 },
            enRiesgo: { data: [], total: 0, current_page: 1, last_page: 1 },
            
            // Especialistas
            isLoadingEspecialistas: false,
            especialistas: [],
            
            // Modal Detalle
            detalleModal: null,
            isLoadingDetalle: false,
            detalleEspecialista: {},
            detalleRequerimientos: [],
        };
    },
    computed: {
        selectedYearsLabel() {
            if (!this.selectedYear || this.selectedYear.length === 0) return 'Ninguno';
            if (this.selectedYear.length === 1) return this.selectedYear[0];
            if (this.selectedYear.length === this.years.length && this.years.length > 0) return 'Todos';
            return this.selectedYear.sort().join(', ');
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
            this.refreshAllData();
        },
        refreshAllData() {
            this.isLoadingAll = true;
            Promise.all([
                this.fetchStats(),
                this.fetchChartData(),
                this.fetchVencidos(),
                this.fetchEnRiesgo(),
                this.fetchEspecialistas()
            ]).finally(() => {
                this.isLoadingAll = false;
            });
        },
        populateYears() {
            const currentYear = new Date().getFullYear();
            for (let i = currentYear; i >= currentYear - 5; i--) { this.years.push(i); }
        },
        // API Calls
        fetchStats() {
            this.isLoadingStats = true;
            return axios.get(route('dashboard.resumenGeneral', { year: this.selectedYear }, false, Ziggy))
                .then(response => this.stats = response.data)
                .catch(error => console.error('Error stats:', error))
                .finally(() => this.isLoadingStats = false);
        },
        fetchChartData() {
            this.isLoadingChart = true;
            return axios.get(route('dashboard.resumenGrafico', { year: this.selectedYear }, false, Ziggy))
                .then(response => this.renderChart(response.data))
                .catch(error => console.error('Error chart:', error))
                .finally(() => this.isLoadingChart = false);
        },
        fetchVencidos(page = 1) {
            this.isLoadingVencidos = true;
            return axios.get(route('dashboard.alertas', { type: 'vencidos', page, year: this.selectedYear }, false, Ziggy))
                .then(response => this.vencidos = response.data)
                .catch(error => console.error('Error vencidos:', error))
                .finally(() => this.isLoadingVencidos = false);
        },
        fetchEnRiesgo(page = 1) {
            this.isLoadingEnRiesgo = true;
            return axios.get(route('dashboard.alertas', { type: 'enRiesgo', page, year: this.selectedYear }, false, Ziggy))
                .then(response => this.enRiesgo = response.data)
                .catch(error => console.error('Error en riesgo:', error))
                .finally(() => this.isLoadingEnRiesgo = false);
        },
        fetchEspecialistas() {
            this.isLoadingEspecialistas = true;
            return axios.get(route('dashboard.resumenEspecialistas', { anio: this.selectedYear }, false, Ziggy))
                .then(response => {
                    this.especialistas = response.data.especialistas;
                })
                .catch(error => console.error('Error especialistas:', error))
                .finally(() => this.isLoadingEspecialistas = false);
        },

        // Chart Rendering (Same logic, new colors)
        renderChart(data) {
            if (this.chartInstance) this.chartInstance.destroy();
            const ctx = this.$refs.chartCanvas.getContext('2d');
            
            const gradientAsignados = ctx.createLinearGradient(0, 0, 0, 400);
            gradientAsignados.addColorStop(0, 'rgba(108, 117, 125, 0.1)');
            gradientAsignados.addColorStop(1, 'rgba(108, 117, 125, 0)');

            const gradientFinalizados = ctx.createLinearGradient(0, 0, 0, 400);
            gradientFinalizados.addColorStop(0, 'rgba(56, 189, 248, 0.4)'); 
            gradientFinalizados.addColorStop(1, 'rgba(56, 189, 248, 0)');

            this.chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        { 
                            label: 'Asignados', 
                            data: data.asignados, 
                            borderColor: '#94a3b8', 
                            backgroundColor: gradientAsignados,
                            borderWidth: 2,
                            tension: 0.4, 
                            fill: true, 
                            pointRadius: 0,
                            pointHoverRadius: 6 
                        },
                        { 
                            label: 'Finalizados (Real)', 
                            data: data.finalizados, 
                            borderColor: '#0ea5e9', 
                            backgroundColor: gradientFinalizados,
                            borderWidth: 3,
                            tension: 0.4, 
                            fill: true, 
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#ffffff',
                            pointBorderWidth: 2
                        },
                         { 
                            label: 'Fin Planificado', 
                            data: data.programados, 
                            borderColor: '#10b981', 
                            borderDash: [5, 5],
                            borderWidth: 2,
                            tension: 0.4, 
                            fill: false, 
                            pointRadius: 0,
                            pointHoverRadius: 4 
                        }
                    ]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false, 
                    interaction: { mode: 'index', intersect: false },
                    plugins: { 
                        legend: { position: 'top', align: 'end', labels: { boxWidth: 10, usePointStyle: true, font: {family: "'Inter', sans-serif", size: 11} } },
                        tooltip: { backgroundColor: '#1e293b', padding: 12, titleFont: { size: 13, family: "'Inter', sans-serif" }, bodyFont: { size: 12, family: "'Inter', sans-serif" }, cornerRadius: 8, displayColors: true }
                    }, 
                    scales: { 
                        y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#e2e8f0', drawBorder: false }, ticks: { font: {family: "'Inter', sans-serif", size: 11}, color: '#64748b', padding: 10 } },
                        x: { grid: { display: false }, ticks: { font: {family: "'Inter', sans-serif", size: 11}, color: '#64748b' } }
                    } 
                }
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
        getBgColorClass(value) {
             if (value >= 80) return 'bg-success';
            if (value >= 50) return 'bg-warning';
            return 'bg-danger';
        },
        getBadgeClass(value) {
             if (value >= 80) return 'badge-soft-success text-success';
            if (value >= 50) return 'badge-soft-warning text-warning';
            return 'badge-soft-danger text-danger';
        },
        getInitials(name) {
            if (!name) return '';
            const words = name.trim().split(/\s+/);
            if(words.length === 1) return words[0].substring(0,2).toUpperCase();
            return (words[0][0] + words[1][0]).toUpperCase();
        },
        getAvatarColor(name) {
             const colors = ['bg-primary', 'bg-info', 'bg-secondary', 'bg-dark', 'bg-indigo'];
            let hash = 0;
            for (let i = 0; i < name.length; i++) { hash = name.charCodeAt(i) + ((hash << 5) - hash); }
            const index = Math.abs(hash) % colors.length;
            return colors[index];
        },
        
        // Modal
        openDetalleModal(especialista) {
            this.isLoadingDetalle = true;
            this.detalleEspecialista = {};
            this.detalleRequerimientos = [];
            this.detalleModal?.show();
            axios.get(route('dashboard.detalleEspecialista', { especialista: especialista.id, anio: this.selectedYear }, false, Ziggy))
                .then(response => {
                    this.detalleEspecialista = response.data.especialista;
                    this.detalleRequerimientos = response.data.requerimientos;
                })
                .catch(error => console.error('Error detalle:', error))
                .finally(() => this.isLoadingDetalle = false);
        },
        closeDetalleModal() { this.detalleModal?.hide(); }
    },
    mounted() {
        this.populateYears();
        this.refreshAllData();
        if (this.$refs.detalleModal) { this.detalleModal = new Modal(this.$refs.detalleModal); }
    },
    beforeUnmount() {
        if (this.chartInstance) this.chartInstance.destroy();
        if (this.detalleModal) this.detalleModal.dispose();
    }
};
</script>

<style scoped>
/* Font & Base */
.dashboard-container {
    background-color: #f3f4f6; /* Gray 100 */
    min-height: 100vh;
    padding: 2rem;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    color: #1f2937;
}

/* Animations */
.fade-in-down { animation: fadeInDown 0.6s ease-out; }
.animated-fade-in { animation: fadeIn 0.5s ease-in-out; }
@keyframes fadeInDown { from { opacity: 0; transform: translateY(-15px); } to { opacity: 1; transform: translateY(0); } }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

/* Cards */
.card { border-radius: 1rem; }
.shadow-sm { box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1), 0 1px 2px 0 rgba(0,0,0,0.06) !important; }
.main-stat-card { overflow: hidden; background: white; }
.dec-circle {
    position: absolute; width: 150px; height: 150px; border-radius: 50%;
    top: -50px; right: -50px; opacity: 0.1;
}
.icon-circle {
    width: 48px; height: 48px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
}
.hover-lift { transition: transform 0.2s, box-shadow 0.2s; }
.hover-lift:hover { transform: translateY(-4px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1) !important; }

/* Tabs & Navs */
.nav-pills-sm .nav-link { 
    font-size: 0.85rem; border-radius: 0.5rem; color: #64748b; 
    transition: all 0.2s;
}
.nav-pills-sm .nav-link.active {
    background-color: #f1f5f9; color: #1e293b;
}

/* Lists */
.scrollable-list { max-height: 400px; overflow-y: auto; }
.scrollable-list::-webkit-scrollbar { width: 6px; }
.scrollable-list::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 3px; }
.hover-bg-gray:hover { background-color: #f8fafc; }
.border-bottom-light { border-bottom: 1px solid #f1f5f9; }

/* Custom Badges */
.badge-soft-success { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
.badge-soft-warning { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.badge-soft-danger { background-color: rgba(239, 68, 68, 0.1); color: #ef4444; }
.badge-light-primary { background-color: rgba(14, 165, 233, 0.1); color: #0ea5e9; }
.badge-light-danger { background-color: rgba(239, 68, 68, 0.1); color: #ef4444; }

/* Utilities */
.tracking-tight { letter-spacing: -0.025em; }
.letter-spacing-1 { letter-spacing: 0.05em; }
.bg-purple { background-color: #8b5cf6 !important; }
.text-purple { color: #8b5cf6 !important; }
.bg-indigo { background: #6366f1; }
.border-right-md { border-right: 0; }
@media (min-width: 768px) { .border-right-md { border-right: 1px solid #f1f5f9; } }
.chart-container { height: 350px; width: 100%; }
.transition-bg { transition: background-color 0.15s ease-in-out; }
.transition-bg:hover { background-color: #f9fafb; }
.trigger-tooltip { cursor: help; }
.hover-primary:hover { color: #0ea5e9 !important; background: #f0f9ff; }
.bg-light-danger { background-color: rgba(239, 68, 68, 0.1); }

/* Custom Colored Tabs */
.nav-pills .nav-link.nav-link-danger.active {
    background-color: #ef4444 !important;
    color: white !important;
}
.nav-pills .nav-link.nav-link-danger.active .badge {
    color: #ef4444 !important;
    background-color: white !important;
}

.nav-pills .nav-link.nav-link-warning.active {
    background-color: #f59e0b !important;
    color: white !important;
}
.nav-pills .nav-link.nav-link-warning.active .badge {
    color: #f59e0b !important;
    background-color: white !important;
}
</style>
