<template>
  <div class="dashboard-container">
    <!-- Header Section (Unified Style) -->
    <div class="card shadow-sm border-0 mb-4 fade-in-down">
      <div class="card-header bg-white p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <div>
            <h4 class="font-weight-bold text-dark mb-1">
              <i class="fas fa-chart-line mr-2 text-danger"></i>Tablero de Mejora Continua
            </h4>
            <p class="text-muted small mb-0">Monitoreo de Hallazgos, No Conformidades y Acciones</p>
          </div>
          <div class="mt-2 mt-md-0 d-flex align-items-center">
            <!-- Admin Toggle -->
            <div v-if="esAdmin" class="mr-3 bg-light p-2 rounded shadow-sm d-flex align-items-center">
              <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="mostrarTodosSwitch" v-model="mostrarTodos"
                  @change="loadData">
                <label class="custom-control-label font-weight-bold text-muted small user-select-none"
                  for="mostrarTodosSwitch">Ver Todos</label>
              </div>
            </div>

            <!-- Year Selector -->
            <div class="d-inline-flex align-items-center bg-light p-2 rounded shadow-sm">
              <label class="mb-0 mr-2 font-weight-bold text-muted small text-uppercase">Periodo:</label>
              <div class="position-relative d-inline-block mr-3">
                <button
                  class="btn btn-sm btn-outline-danger font-weight-bold dropdown-toggle shadow-sm bg-white border-0"
                  type="button" @click="toggleYearDropdown" style="font-size: 0.85rem;">
                  <i class="far fa-calendar-alt mr-2"></i>{{ selectedYearsLabel }}
                </button>
                <div v-if="isYearDropdownOpen" class="dropdown-menu show p-3 border-0 shadow-lg mt-2"
                  style="position: absolute; right: 0; min-width: 200px; z-index: 1050; border-radius: 0.75rem;">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="dropdown-header px-0 text-uppercase small font-weight-bold text-muted mb-0">Periodos</h6>
                    <button class="btn btn-link btn-sm p-0 text-muted" @click="selectAllYears"
                      style="font-size: 0.7rem;">Todas</button>
                  </div>
                  <div v-for="year in years" :key="year" class="custom-control custom-checkbox mb-2">
                    <input type="checkbox" class="custom-control-input" :id="'year-' + year" :value="year"
                      v-model="selectedYear" @change="loadData">
                    <label class="custom-control-label font-weight-bold text-dark cursor-pointer" :for="'year-' + year"
                      style="font-size: 0.85rem;">{{ year }}</label>
                  </div>
                </div>
              </div>
              <!-- Refresh Button -->
              <button class="btn btn-sm btn-icon btn-light text-danger shadow-sm rounded-circle" @click="loadData"
                title="Actualizar" style="width: 32px; height: 32px;">
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
        <!-- Total Hallazgos -->
        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
          <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden main-stat-card">
            <div class="card-body p-4 d-flex flex-column justify-content-between position-relative z-1">
              <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                  <h6 class="text-uppercase text-muted small font-weight-bold letter-spacing-1 mb-1">Total Hallazgos
                  </h6>
                  <h2 class="font-weight-bolder text-dark mb-0 display-4">{{ stats.total }}</h2>
                </div>
                <div class="icon-circle bg-light-danger text-danger">
                  <i class="fas fa-exclamation-triangle fa-lg"></i>
                </div>
              </div>
              <div class="mt-auto">
                <div class="d-flex align-items-center text-muted small font-weight-bold">
                  <span class="text-danger mr-2" v-if="hallazgosVencidos.total > 0">
                    <i class="fas fa-exclamation-circle mr-1"></i>{{ hallazgosVencidos.total }} con atraso
                  </span>
                  <span class="text-success" v-else>
                    <i class="fas fa-check-circle mr-1"></i>Sin hallazgos vencidos
                  </span>
                </div>
              </div>
            </div>
            <div class="dec-circle bg-danger opacity-5"></div>
          </div>
        </div>

        <!-- Tasa de Cierre -->
        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
          <div class="card border-0 shadow-sm h-100 hover-lift">
            <div class="card-body p-4 border-top border-danger border-width-3">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="text-uppercase text-muted small font-weight-bold letter-spacing-1 mb-0">Eficacia de Cierre
                </h6>
                <span class="badge badge-pill" :class="getBadgeClass(stats.tasaCierre)">{{ stats.tasaCierre }}%</span>
              </div>
              <div class="position-relative pt-2 pb-3">
                <div class="progress rounded-pill bg-light" style="height: 10px;">
                  <div class="progress-bar rounded-pill" :class="getBgColorClass(stats.tasaCierre)"
                    :style="{ width: stats.tasaCierre + '%' }"></div>
                </div>
              </div>
              <p class="text-muted small mb-0 mt-2 text-center font-weight-bold">Concluidos vs Cerrados</p>
            </div>
          </div>
        </div>

        <!-- Status Breakdown -->
        <div class="col-lg-6 col-md-12">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
              <div class="row h-100 align-items-center">
                <div class="col-md-2 text-center border-right mb-3 mb-md-0">
                  <h4 class="font-weight-bold text-pink mb-0">{{ stats.creados }}</h4>
                  <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.65rem;">Creados</small>
                </div>
                <div class="col-md-2 text-center border-right mb-3 mb-md-0">
                  <h4 class="font-weight-bold text-indigo mb-0">{{ stats.aprobados }}</h4>
                  <small class="text-muted text-uppercase font-weight-bold"
                    style="font-size: 0.65rem;">Aprobados</small>
                </div>
                <div class="col-md-3 text-center border-right mb-3 mb-md-0">
                  <h4 class="font-weight-bold text-primary mb-0">{{ stats.enProceso }}</h4>
                  <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.65rem;">En
                    Proceso</small>
                </div>
                <div class="col-md-2 text-center border-right mb-3 mb-md-0">
                  <h4 class="font-weight-bold text-success mb-0">{{ stats.concluidos }}</h4>
                  <small class="text-muted text-uppercase font-weight-bold"
                    style="font-size: 0.65rem;">Concluidos</small>
                </div>
                <div class="col-md-2 text-center border-right mb-3 mb-md-0">
                  <h4 class="font-weight-bold text-info mb-0">{{ stats.cerrados }}</h4>
                  <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.65rem;">Cerrados</small>
                </div>
                <div class="col-md-1 text-center">
                  <h4 class="font-weight-bold text-secondary mb-0">{{ stats.desestimados }}</h4>
                  <small class="text-muted text-uppercase font-weight-bold" style="font-size: 0.65rem;">Desest.</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row mb-5">
        <!-- Distribution Chart -->
        <div class="col-lg-7 mb-4 mb-lg-0">
          <div class="card border-0 shadow-sm h-100 hover-lift">
            <div class="card-header bg-danger border-0 pt-4 px-4 pb-3 " style="border-radius: 1rem 1rem 0 0;">
              <h6 class="font-weight-bold text-white mb-1">Distribución por Estado</h6>
              <p class="text-white small mb-0">Composición actual del universo de hallazgos</p>
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
                <a class="nav-link active font-weight-bold px-3 py-1 mr-2 rounded-pill nav-link-danger"
                  data-toggle="pill" href="#vencidos" role="tab">
                  <i class="fas fa-exclamation-circle mr-2"></i>Hallazgos Venc.
                  <span class="badge badge-light ml-2">{{ hallazgosVencidos.total || 0 }}</span>
                </a>
                <a class="nav-link font-weight-bold px-3 py-1 rounded-pill nav-link-warning" data-toggle="pill"
                  href="#acciones" role="tab">
                  <i class="fas fa-clock mr-2"></i>Acciones Venc.
                  <span class="badge badge-light ml-2">{{ accionesVencidas.total || 0 }}</span>
                </a>
              </div>
            </div>
            <div class="card-body p-0 tab-content scrollable-list">
              <!-- Hallazgos Vencidos Tab -->
              <div class="tab-pane fade show active" id="vencidos" role="tabpanel">
                <ul class="list-group list-group-flush">
                  <li v-for="h in hallazgosVencidos.data" :key="'vencido-' + h.id"
                    class="list-group-item border-bottom-light px-4 py-3 hover-bg-gray transition-colors">
                    <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                      <h6 class="mb-0 font-weight-bold text-dark">{{ h.hallazgo_cod }}</h6>
                      <span class="badge badge-soft-danger">{{ h.hallazgo_estado }}</span>
                    </div>
                    <p class="mb-1 text-muted small text-truncate" style="max-width: 90%;">{{ h.hallazgo_clasificacion
                      }}
                    </p>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                      <small class="text-danger font-weight-bold">
                        <i class="far fa-calendar-times mr-1"></i>{{ formatDate(h.hallazgo_fecha_identificacion) }}
                      </small>
                      <small class="text-muted font-weight-bold">Avance: {{ h.hallazgo_avance || 0 }}%</small>
                    </div>
                  </li>
                  <li v-if="hallazgosVencidos.total === 0" class="list-group-item text-center text-muted py-5 border-0">
                    <i class="fas fa-check-circle fa-2x mb-2 text-success opacity-50"></i>
                    <p class="mb-0 small">¡Todo al día! Sin hallazgos vencidos.</p>
                  </li>
                </ul>
              </div>

              <!-- Acciones Vencidas Tab -->
              <div class="tab-pane fade" id="acciones" role="tabpanel">
                <ul class="list-group list-group-flush">
                  <li v-for="a in accionesVencidas.data" :key="'accion-' + a.id"
                    class="list-group-item border-bottom-light px-4 py-3 hover-bg-gray transition-colors">
                    <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                      <h6 class="mb-0 font-weight-bold text-dark">{{ a.accion_cod }}</h6>
                      <span class="badge badge-soft-warning">{{ a.accion_tipo }}</span>
                    </div>
                    <p class="mb-1 text-muted small text-truncate leading-tight">{{ a.accion_nombre || 'Sin descripción'
                      }}</p>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                      <small class="text-danger font-weight-bold">
                        <i class="fas fa-calendar-times mr-1"></i>{{ formatDate(a.accion_fecha_fin_planificada) }}
                      </small>
                      <small class="text-muted"><i class="far fa-user mr-1"></i>{{ a.accion_responsable }}</small>
                    </div>
                  </li>
                  <li v-if="accionesVencidas.total === 0" class="list-group-item text-center text-muted py-5 border-0">
                    <i class="fas fa-check-circle fa-2x mb-2 text-success opacity-50"></i>
                    <p class="mb-0 small">¡Excelente! Sin acciones vencidas.</p>
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
            <h6 class="font-weight-bold text-white mb-1">Resumen por Proceso</h6>
            <p class="text-white small mb-0">Detalle de gestión y nivel de avance</p>
          </div>
        </div>
        <div class="card-body px-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light text-muted small text-uppercase font-weight-bold">
                <tr>
                  <th class="pl-4 border-0">Proceso</th>
                  <th class="text-center border-0">Hallazgos</th>
                  <th class="text-center border-0">Estado</th>
                  <th class="text-center border-0">Pendientes</th>
                  <th class="border-0" style="width: 25%;">Progreso</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="proceso in procesosData" :key="proceso.id" class="transition-bg">
                  <td class="pl-4 py-3">
                    <div class="d-flex align-items-center">
                      <div class="icon-box bg-light-danger text-danger mr-3">
                        <i class="fas fa-folder fa-sm"></i>
                      </div>
                      <div>
                        <span class="d-block font-weight-bold text-dark">{{ proceso.nombre }}</span>
                        <small class="text-muted">ID: {{ proceso.id }}</small>
                      </div>
                    </div>
                  </td>
                  <td class="text-center font-weight-bold text-dark">{{ proceso.total }}</td>
                  <td class="text-center">
                    <div class="d-flex justify-content-center">
                      <span class="badge badge-pill badge-light border mr-1" title="Abiertos">{{ proceso.abiertos
                        }}</span>
                      <span class="badge badge-pill badge-light-primary text-primary mr-1" title="En Proceso">{{
                        proceso.enProceso }}</span>
                      <span class="badge badge-pill badge-light-success text-success" title="Cerrados">{{
                        proceso.cerrados }}</span>
                    </div>
                  </td>
                  <td class="text-center">
                    <span class="badge badge-pill px-3 py-1 font-weight-bold"
                      :class="proceso.accionesPendientes > 0 ? 'badge-soft-warning' : 'badge-soft-success'">
                      {{ proceso.accionesPendientes }} Acciones
                    </span>
                  </td>
                  <td class="pr-4">
                    <div class="d-flex justify-content-between mb-1">
                      <small class="font-weight-bold text-muted">Avance</small>
                      <small class="font-weight-bold text-dark">{{ proceso.porcentajeAvance }}%</small>
                    </div>
                    <div class="progress rounded-pill bg-light" style="height: 6px;">
                      <div class="progress-bar rounded-pill" :class="getProgressBarClass(proceso.porcentajeAvance)"
                        role="progressbar" :style="{ width: proceso.porcentajeAvance + '%' }"></div>
                    </div>
                  </td>
                </tr>
                <tr v-if="procesosData.length === 0">
                  <td colspan="5" class="text-center py-5 text-muted">No se encontraron procesos registrados.</td>
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
  name: 'DashboardMejora',
  data() {
    return {
      loading: true,
      mostrarTodos: false,
      esAdmin: false,

      // Period Selection
      years: [],
      selectedYear: [new Date().getFullYear()],
      isYearDropdownOpen: false,

      // Data Buckets
      stats: {
        total: 0, creados: 0, aprobados: 0, desestimados: 0,
        enProceso: 0, concluidos: 0, cerrados: 0, tasaCierre: 0
      },
      hallazgosVencidos: { data: [], total: 0 },
      accionesVencidas: { data: [], total: 0 },
      procesosData: [],

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
      for (let i = currentYear; i >= 2024; i--) { // Or dynamic start year
        this.years.push(i);
      }
      if (!this.years.includes(currentYear)) this.years.push(currentYear);
      this.years = [...new Set(this.years)].sort((a, b) => b - a);
    },
    async loadData() {
      this.loading = true;
      try {
        let url = '/api/dashboard/mejora';
        const params = new URLSearchParams();
        if (this.esAdmin) params.append('mostrarTodos', this.mostrarTodos);

        if (this.selectedYear && this.selectedYear.length > 0) {
          this.selectedYear.forEach(y => params.append('year[]', y));
        }

        const response = await axios.get(`${url}?${params.toString()}`);
        const data = response.data;

        // Backend returns stats with specific names, we might need a slight mapping if needed
        // but let's process the hallazgos array as before for flexibility
        this.processStats(data.hallazgos);
        this.processChart(data.hallazgos);
        this.processAlerts(data);
        this.processProcesos(data);

      } catch (error) {
        console.error('Error loading dashboard data:', error);
      } finally {
        setTimeout(() => this.loading = false, 300);
      }
    },

    // 1. KPI Stats
    processStats(hallazgos) {
      this.stats.total = hallazgos.length;
      this.stats.creados = hallazgos.filter(h => ['creado', 'modificado'].includes(h.hallazgo_estado)).length;
      this.stats.aprobados = hallazgos.filter(h => h.hallazgo_estado === 'aprobado').length;
      this.stats.desestimados = hallazgos.filter(h => h.hallazgo_estado === 'desestimado').length;
      this.stats.enProceso = hallazgos.filter(h => h.hallazgo_estado === 'en proceso').length;
      this.stats.concluidos = hallazgos.filter(h => h.hallazgo_estado === 'concluido').length;
      this.stats.cerrados = hallazgos.filter(h => ['cerrado', 'evaluado'].includes(h.hallazgo_estado)).length;

      const denominador = this.stats.concluidos + this.stats.cerrados;
      this.stats.tasaCierre = denominador > 0 ? Math.round((this.stats.cerrados / denominador) * 100) : 0;
    },

    // 2. Chart Rendering
    processChart(hallazgos) {
      const estadoCounts = hallazgos.reduce((acc, h) => {
        const estado = h.hallazgo_estado || 'Sin estado';
        acc[estado] = (acc[estado] || 0) + 1;
        return acc;
      }, {});

      const labels = Object.keys(estadoCounts).map(l => l.charAt(0).toUpperCase() + l.slice(1));
      const quantities = Object.values(estadoCounts);

      const colorMap = {
        'creado': '#e83e8c', 'modificado': '#d63384',
        'aprobado': '#6f42c1', 'desestimado': '#6c757d',
        'en proceso': '#4e73df', 'concluido': '#1cc88a',
        'cerrado': '#36b9cc', 'evaluado': '#36b9cc', 'Sin estado': '#858796'
      };
      const colors = Object.keys(estadoCounts).map(e => colorMap[e] || '#858796');

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
            },
            tooltip: {
              backgroundColor: '#1e293b',
              padding: 12,
              cornerRadius: 8,
              titleFont: { size: 14, weight: 'bold' },
              bodyFont: { size: 13 }
            }
          },
          layout: { padding: 20 },
          cutout: '70%'
        }
      });
    },

    // 3. Alerts Processing
    processAlerts(data) {
      this.hallazgosVencidos.data = data.hallazgos.filter(h => {
        const hallazgoAcciones = data.acciones.filter(a => a.hallazgo_id === h.id);
        return hallazgoAcciones.some(a => this.isFechaVencida(a.accion_fecha_fin_planificada));
      }).slice(0, 10);
      this.hallazgosVencidos.total = data.hallazgos.filter(h => {
        const hallazgoAcciones = data.acciones.filter(a => a.hallazgo_id === h.id);
        return hallazgoAcciones.some(a => this.isFechaVencida(a.accion_fecha_fin_planificada));
      }).length;

      this.accionesVencidas.data = data.accionesVencidas.slice(0, 10);
      this.accionesVencidas.total = data.accionesVencidas.length;
    },

    // 4. Procesos Processing
    processProcesos(data) {
      if (data.hallazgosPorProceso && Array.isArray(data.hallazgosPorProceso)) {
        this.procesosData = data.hallazgosPorProceso.map(proceso => {
          const hallazgosDelProceso = data.hallazgos.filter(h =>
            h.procesos && h.procesos.some(p => p.id === proceso.id)
          );
          const accionesPendientes = hallazgosDelProceso.reduce((total, hallazgo) => {
            const accionesDelHallazgo = data.acciones.filter(a => a.hallazgo_id === hallazgo.id);
            return total + accionesDelHallazgo.filter(a => !['finalizada', 'desestimada'].includes(a.accion_estado)).length;
          }, 0);

          let porcentajeAvance = 0;
          if (hallazgosDelProceso.length > 0) {
            const avances = hallazgosDelProceso.map(h => h.hallazgo_avance || 0);
            porcentajeAvance = Math.round(avances.reduce((a, b) => a + b, 0) / (avances.length || 1));
          }
          return { ...proceso, accionesPendientes, porcentajeAvance };
        });
      } else {
        this.procesosData = [];
      }
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
    isFechaVencida(fecha) {
      if (!fecha) return false;
      const f = new Date(fecha); const h = new Date();
      f.setHours(0, 0, 0, 0); h.setHours(0, 0, 0, 0);
      return f < h;
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

/* KPI Custom text colors */
.text-pink {
  color: #e83e8c !important;
}

.text-indigo {
  color: #6f42c1 !important;
}

/* Tabs & Navs */
.nav-pills-sm .nav-link {
  font-size: 0.85rem;
  border-radius: 0.5rem;
  color: #64748b;
  transition: all 0.2s;
  background: #f8fafc;
}

.nav-pills .nav-link.nav-link-danger.active {
  background-color: #ef4444 !important;
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

/* Table Utilities */
.icon-box {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.bg-light-danger {
  background-color: rgba(239, 68, 68, 0.1);
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