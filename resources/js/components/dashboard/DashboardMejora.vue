<template>
  <div class="dashboard-container">
    <!-- Header Section -->
    <div class="card shadow-sm border-0 mb-4 fade-in-down">
      <div class="card-header bg-white p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <div>
            <h4 class="font-weight-bold text-dark mb-1">
              <i class="fas fa-chart-line mr-2 text-primary"></i>Tablero de Mejora Continua
            </h4>
            <p class="text-muted small mb-0">Gestión de Hallazgos, No Conformidades y Acciones</p>
          </div>
          <div class="mt-2 mt-md-0" v-if="esAdmin">
            <div class="d-inline-flex align-items-center bg-light p-2 rounded shadow-sm">
              <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="mostrarTodosSwitch" v-model="mostrarTodos"
                  @change="loadData">
                <label class="custom-control-label font-weight-bold text-muted small user-select-none"
                  for="mostrarTodosSwitch">Mostrar todos (Admin)</label>
              </div>
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

      <!-- BLOCK 1: KPIs (Merged from HallazgoResumenGeneral) -->
      <div class="row text-white mb-4">
        <!-- 1. Total & Creados (Split Layout) -->
        <div class="col-xl-2 col-md-4 col-6 mb-3 mb-xl-0">
          <div class="card shadow-sm border-0 h-100 bg-pink text-white kpi-card">
            <div class="card-body p-3 text-center d-flex flex-column align-items-center justify-content-center">
              <div class="mb-2"><i class="fas fa-exclamation-triangle fa-2x"></i></div>
              <div class="d-flex justify-content-around align-items-center w-100">
                <div>
                  <h4 class="mb-0 font-weight-bold">{{ stats.total }}</h4>
                  <small class="text-white-50 text-uppercase font-weight-bold" style="font-size: 0.65rem;">Total</small>
                </div>
                <div class="border-right border-white-50 mx-2" style="height: 30px;"></div>
                <div>
                  <h4 class="mb-0 font-weight-bold">{{ stats.creados }}</h4>
                  <small class="text-white-50 text-uppercase font-weight-bold"
                    style="font-size: 0.65rem;">Creados</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 2. Aprobados -->
        <div class="col-xl-2 col-md-4 col-6 mb-3 mb-xl-0">
          <div class="card shadow-sm border-0 h-100 bg-indigo text-white kpi-card">
            <div class="card-body p-3 text-center d-flex flex-column align-items-center justify-content-center">
              <div class="mb-2"><i class="fas fa-check-square fa-2x"></i></div>
              <h4 class="mb-0 font-weight-bold">{{ stats.aprobados }}</h4>
              <small class="text-white-50 text-uppercase font-weight-bold">Aprobados</small>
            </div>
          </div>
        </div>

        <!-- 3. Desestimados -->
        <div class="col-xl-2 col-md-4 col-6 mb-3 mb-xl-0">
          <div class="card shadow-sm border-0 h-100 bg-secondary text-white kpi-card">
            <div class="card-body p-3 text-center d-flex flex-column align-items-center justify-content-center">
              <div class="mb-2"><i class="fas fa-ban fa-2x"></i></div>
              <h4 class="mb-0 font-weight-bold">{{ stats.desestimados }}</h4>
              <small class="text-white-50 text-uppercase font-weight-bold">Desestimados</small>
            </div>
          </div>
        </div>

        <!-- 4. En Proceso -->
        <div class="col-xl-2 col-md-4 col-6 mb-3 mb-xl-0">
          <div class="card shadow-sm border-0 h-100 bg-primary text-white kpi-card">
            <div class="card-body p-3 text-center d-flex flex-column align-items-center justify-content-center">
              <div class="mb-2"><i class="fas fa-sync-alt fa-2x"></i></div>
              <h4 class="mb-0 font-weight-bold">{{ stats.enProceso }}</h4>
              <small class="text-white-50 text-uppercase font-weight-bold">En Proceso</small>
            </div>
          </div>
        </div>

        <!-- 5. Concluidos -->
        <div class="col-xl-2 col-md-4 col-6 mb-3 mb-xl-0">
          <div class="card shadow-sm border-0 h-100 bg-success text-white kpi-card">
            <div class="card-body p-3 text-center d-flex flex-column align-items-center justify-content-center">
              <div class="mb-2"><i class="fas fa-flag-checkered fa-2x"></i></div>
              <h4 class="mb-0 font-weight-bold">{{ stats.concluidos }}</h4>
              <small class="text-white-50 text-uppercase font-weight-bold">Concluidos</small>
            </div>
          </div>
        </div>

        <!-- 6. Cerrados & Tasa Cierre (Split Layout) -->
        <div class="col-xl-2 col-md-4 col-6 mb-3 mb-xl-0">
          <div class="card shadow-sm border-0 h-100 bg-info text-white kpi-card">
            <div class="card-body p-3 text-center d-flex flex-column align-items-center justify-content-center">
              <div class="mb-2"><i class="fas fa-check-circle fa-2x"></i></div>
              <div class="d-flex justify-content-around align-items-center w-100">
                <div>
                  <h4 class="mb-0 font-weight-bold">{{ stats.cerrados }}</h4>
                  <small class="text-white-50 text-uppercase font-weight-bold"
                    style="font-size: 0.65rem;">Cerrados</small>
                </div>
                <div class="border-right border-white-50 mx-2" style="height: 30px;"></div>
                <div>
                  <h4 class="mb-0 font-weight-bold">{{ stats.tasaCierre }}%</h4>
                  <small class="text-white-50 text-uppercase font-weight-bold" style="font-size: 0.65rem;">Tasa</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Charts Section (Merged from HallazgoResumenGrafico) -->
        <div class="col-lg-5 mb-4">
          <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
              <h6 class="font-weight-bold text-dark mb-0 section-header">Distribución de Hallazgos</h6>
              <p class="text-muted small mb-0">Estado actual de registros</p>
            </div>
            <div class="card-body px-4 pb-4 d-flex align-items-center justify-content-center">
              <div class="w-100" style="height: 300px; position: relative;">
                <canvas ref="chartCanvas"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Alerts Section (Merged from HallazgoResumenAlertas) -->
        <div class="col-lg-7 mb-4">
          <div class="row h-100">
            <!-- Hallazgos Vencidos -->
            <div class="col-md-6 mb-4 mb-md-0">
              <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0 d-flex align-items-center">
                  <div class="d-flex align-items-center justify-content-center bg-light-danger rounded-circle mr-3"
                    style="width: 40px; height: 40px;">
                    <i class="fas fa-exclamation-circle text-danger"></i>
                  </div>
                  <div>
                    <h6 class="font-weight-bold text-dark mb-0 section-header">Hallazgos Vencidos</h6>
                    <p class="text-muted small mb-0">Pendientes de atención</p>
                  </div>
                </div>
                <div class="card-body px-4 pt-3 pb-4">
                  <ul class="list-group list-group-flush small">
                    <li v-for="h in hallazgosVencidos.data" :key="'vencido-' + h.id"
                      class="list-group-item border-0 px-0 py-3 border-bottom-dashed">
                      <div class="d-flex justify-content-between">
                        <span class="font-weight-bold text-dark">{{ h.hallazgo_cod }}</span>
                        <span class="badge badge-pill badge-light-danger text-danger">{{ h.hallazgo_estado }}</span>
                      </div>
                      <div class="mt-1 text-secondary">
                        <div class="text-truncate" :title="h.hallazgo_clasificacion">{{ h.hallazgo_clasificacion }}
                        </div>
                        <small class="text-muted"><i class="far fa-calendar-alt mr-1"></i>{{
                          formatDate(h.hallazgo_fecha_identificacion) }}</small>
                      </div>
                    </li>
                    <li v-if="hallazgosVencidos.data.length === 0"
                      class="list-group-item text-center text-muted border-0 py-4">
                      <i class="fas fa-check-circle text-success mb-2 fa-2x"></i>
                      <p class="mb-0">¡Todo al día! Sin hallazgos privados.</p>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Acciones Vencidas -->
            <div class="col-md-6">
              <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0 d-flex align-items-center">
                  <div class="d-flex align-items-center justify-content-center bg-light-warning rounded-circle mr-3"
                    style="width: 40px; height: 40px;">
                    <i class="fas fa-clock text-warning"></i>
                  </div>
                  <div>
                    <h6 class="font-weight-bold text-dark mb-0 section-header">Acciones Vencidas</h6>
                    <p class="text-muted small mb-0">{{ accionesVencidas.total || 0 }} acciones criticas</p>
                  </div>
                </div>
                <div class="card-body px-4 pt-3 pb-4">
                  <ul class="list-group list-group-flush small">
                    <li v-for="a in accionesVencidas.data" :key="'accion-' + a.id"
                      class="list-group-item border-0 px-0 py-3 border-bottom-dashed">
                      <div class="d-flex justify-content-between mb-1">
                        <span class="font-weight-bold text-dark">{{ a.accion_cod }}</span>
                        <span class="badge badge-pill badge-light-warning text-warning">{{ a.accion_tipo }}</span>
                      </div>
                      <p class="mb-1 text-dark leading-tight">{{ a.accion_nombre || a.accion_descripcion ||
                        'Sin descripción' }}</p>
                      <div class="d-flex justify-content-between align-items-center mt-2">
                        <small class="text-muted"><i class="far fa-user mr-1"></i>{{ a.accion_responsable }}</small>
                        <small class="text-danger font-weight-bold">{{ formatDate(a.accion_fecha_fin_planificada)
                        }}</small>
                      </div>
                    </li>
                    <li v-if="accionesVencidas.data.length === 0"
                      class="list-group-item text-center text-muted border-0 py-4">
                      <i class="fas fa-check-circle text-success mb-2 fa-2x"></i>
                      <p class="mb-0">¡Excelente! Sin acciones vencidas.</p>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Processes Detail (Merged from HallazgoResumenProcesos) -->
      <div class="row">
        <div class="col-12 mb-4">
          <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
              <h6 class="font-weight-bold text-dark mb-0 section-header">Resumen por Proceso</h6>
              <p class="text-muted small mb-0">Detalle de gestión y avance</p>
            </div>
            <div class="card-body px-4 pt-3 pb-4">
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="bg-light">
                    <tr>
                      <th class="border-0 font-weight-bold text-secondary small text-uppercase py-3 pl-3"
                        style="min-width: 200px;">Proceso</th>
                      <th class="border-0 font-weight-bold text-secondary small text-uppercase py-3 text-center">Estado
                      </th>
                      <th class="border-0 font-weight-bold text-secondary small text-uppercase py-3 text-center">
                        Acciones</th>
                      <th class="border-0 font-weight-bold text-secondary small text-uppercase py-3 text-center"
                        style="min-width: 150px;">Progreso</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="proceso in procesosData" :key="proceso.id" class="border-bottom-light">
                      <td class="pl-3 py-3">
                        <span class="d-block font-weight-bold text-dark">{{ proceso.nombre }}</span>
                        <small class="text-muted">Total: <strong>{{ proceso.total }}</strong></small>
                      </td>
                      <td class="py-3 text-center">
                        <div class="d-flex justify-content-center">
                          <span class="badge badge-pill badge-light border mr-1" title="Abiertos">{{ proceso.abiertos
                          }}</span>
                          <span class="badge badge-pill badge-light-primary text-primary mr-1" title="En Proceso">{{
                            proceso.enProceso }}</span>
                          <span class="badge badge-pill badge-light-success text-success" title="Cerrados">{{
                            proceso.cerrados }}</span>
                        </div>
                      </td>
                      <td class="py-3 text-center">
                        <span class="font-weight-bold"
                          :class="proceso.accionesPendientes > 0 ? 'text-warning' : 'text-muted'">
                          {{ proceso.accionesPendientes }}
                        </span>
                        <small class="d-block text-muted" style="font-size: 0.65rem;">Pendientes</small>
                      </td>
                      <td class="py-3 pr-3 text-center">
                        <div class="d-flex align-items-center justify-content-center">
                          <span class="font-weight-bold small mr-2">{{ proceso.porcentajeAvance }}%</span>
                          <div class="progress flex-grow-1" style="height: 6px; width: 80px;">
                            <div class="progress-bar rounded" :class="getProgressBarClass(proceso.porcentajeAvance)"
                              role="progressbar" :style="{ width: proceso.porcentajeAvance + '%' }"
                              :aria-valuenow="proceso.porcentajeAvance" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                        </div>
                      </td>
                    </tr>
                    <tr v-if="procesosData.length === 0">
                      <td colspan="4" class="text-center py-5 text-muted">No hay datos para mostrar.</td>
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
  methods: {
    async loadData() {
      this.loading = true;
      try {
        let url = '/api/dashboard/mejora';
        const params = [];
        if (this.esAdmin) params.push(`mostrarTodos=${this.mostrarTodos}`);
        if (params.length > 0) url += '?' + params.join('&');

        const response = await axios.get(url);
        const data = response.data;

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
      this.stats.cerrados = hallazgos.filter(h => h.hallazgo_estado === 'cerrado').length;

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

      const labels = Object.keys(estadoCounts);
      const quantities = Object.values(estadoCounts);

      const colorMap = {
        'creado': '#e83e8c', 'modificado': '#d63384',
        'aprobado': '#6f42c1', 'desestimado': '#6c757d',
        'en proceso': '#4e73df', 'concluido': '#1cc88a',
        'cerrado': '#36b9cc', 'evaluado': '#36b9cc', 'Sin estado': '#858796'
      };
      const colors = labels.map(e => colorMap[e] || '#858796');

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
            data: data, backgroundColor: colors, borderWidth: 2, borderColor: '#fff'
          }]
        },
        options: {
          responsive: true, maintainAspectRatio: false,
          plugins: {
            legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 10, padding: 20, font: { family: "'Inter', sans-serif" } } }
          },
          layout: { padding: 10 },
          cutout: '75%'
        }
      });
    },

    // 3. Alerts Processing
    processAlerts(data) {
      this.hallazgosVencidos.data = data.hallazgos.filter(h => {
        const hallazgoAcciones = data.acciones.filter(a => a.hallazgo_id === h.id);
        return hallazgoAcciones.some(a => this.isFechaVencida(a.accion_fecha_fin_planificada));
      }).slice(0, 4);
      this.hallazgosVencidos.total = this.hallazgosVencidos.data.length;

      this.accionesVencidas.data = data.accionesVencidas.slice(0, 4);
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
            porcentajeAvance = Math.round(avances.reduce((a, b) => a + b, 0) / avances.length);
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
          this.esAdmin = Array.isArray(window.App.user.roles) ? window.App.user.roles.includes('admin') : window.App.user.roles === 'admin';
        } else {
          const response = await axios.get('/api/user');
          const roles = response.data.roles;
          this.esAdmin = Array.isArray(roles) ? roles.includes('admin') : roles === 'admin';
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
    }
  },
  mounted() {
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

/* Cards & KPI Styling */
.kpi-card {
  border-radius: 8px;
  transition: transform 0.2s;
}

.kpi-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
}

/* Custom Backgrounds for KPIs */
.bg-pink {
  background-color: #e83e8c !important;
}

.bg-indigo {
  background-color: #6610f2 !important;
}

.text-xs {
  font-size: .7rem;
}

.text-white-50 {
  color: rgba(255, 255, 255, 0.8) !important;
}

/* Badges & Tables */
.badge-light-primary {
  background-color: rgba(78, 115, 223, 0.1);
  color: #4e73df;
}

.badge-light-success {
  background-color: rgba(28, 200, 138, 0.1);
  color: #1cc88a;
}

.badge-light-danger {
  background-color: rgba(220, 53, 69, 0.1);
  color: #dc3545;
}

.badge-light-warning {
  background-color: rgba(255, 193, 7, 0.1);
  color: #ffc107;
}

.bg-light-danger {
  background-color: rgba(220, 53, 69, 0.1) !important;
}

.bg-light-warning {
  background-color: rgba(255, 193, 7, 0.1) !important;
}

.border-bottom-dashed {
  border-bottom: 1px dashed #e9ecef;
}

.border-bottom-light {
  border-bottom: 1px solid #f8f9fc;
}

.leading-tight {
  line-height: 1.25;
}
</style>