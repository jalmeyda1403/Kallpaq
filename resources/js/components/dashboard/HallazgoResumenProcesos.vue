<template>
  <div class="card shadow-sm border-0 h-100">
    <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
      <h6 class="font-weight-bold text-dark mb-0 section-header">Resumen por Proceso</h6>
      <p class="text-muted small mb-0">Detalle de gestión y avance</p>
    </div>
    <div class="card-body px-4 pt-3 pb-4">
      <div v-if="isLoading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="sr-only">Cargando...</span>
        </div>
      </div>
      <div v-else class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th class="border-0 font-weight-bold text-secondary small text-uppercase py-3 pl-3"
                style="min-width: 200px;">Proceso</th>
              <th class="border-0 font-weight-bold text-secondary small text-uppercase py-3 text-center">Estado</th>
              <th class="border-0 font-weight-bold text-secondary small text-uppercase py-3 text-center">Acciones</th>
              <th class="border-0 font-weight-bold text-secondary small text-uppercase py-3 text-center"
                style="min-width: 150px;">Progreso</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="proceso in procesosData" :key="proceso.id" class="border-bottom-light">
              <td class="pl-3 py-3">
                <span class="d-block font-weight-bold text-dark">{{ proceso.nombre }}</span>
                <small class="text-muted">Total Hallazgos: <strong>{{ proceso.total }}</strong></small>
              </td>
              <td class="py-3 text-center">
                <div class="d-flex justify-content-center">
                  <span class="badge badge-pill badge-light border mr-1" title="Abiertos">{{ proceso.abiertos }}</span>
                  <span class="badge badge-pill badge-light-primary text-primary mr-1" title="En Proceso">{{
                    proceso.enProceso }}</span>
                  <span class="badge badge-pill badge-light-success text-success" title="Cerrados">{{ proceso.cerrados
                    }}</span>
                </div>
              </td>
              <td class="py-3 text-center">
                <span class="font-weight-bold" :class="proceso.accionesPendientes > 0 ? 'text-warning' : 'text-muted'">
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
</template>

<script>
import axios from 'axios';

export default {
  name: 'HallazgoResumenProcesos',
  props: {
    esAdmin: {
      type: Boolean,
      default: false
    },
    mostrarTodos: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      isLoading: false,
      procesosData: [],
    };
  },
  methods: {
    async fetchData() {
      this.isLoading = true;
      try {
        let url = '/api/dashboard/mejora';
        const params = [];

        if (this.esAdmin) {
          params.push(`mostrarTodos=${this.mostrarTodos}`);
        }

        if (params.length > 0) {
          url += '?' + params.join('&');
        }

        const response = await axios.get(url);
        const data = response.data;

        if (data.hallazgosPorProceso && Array.isArray(data.hallazgosPorProceso)) {
          this.procesosData = data.hallazgosPorProceso.map(proceso => {
            const hallazgosDelProceso = data.hallazgos.filter(h =>
              h.procesos && h.procesos.some(p => p.id === proceso.id)
            );

            const accionesPendientes = hallazgosDelProceso.reduce((total, hallazgo) => {
              const accionesDelHallazgo = data.acciones.filter(a => a.hallazgo_id === hallazgo.id);
              const pendientes = accionesDelHallazgo.filter(a =>
                !['finalizada', 'desestimada'].includes(a.accion_estado)
              ).length;
              return total + pendientes;
            }, 0);

            let porcentajeAvance = 0;
            if (hallazgosDelProceso.length > 0) {
              const avances = hallazgosDelProceso.map(h => h.hallazgo_avance || 0);
              porcentajeAvance = Math.round(avances.reduce((a, b) => a + b, 0) / avances.length);
            }

            return {
              ...proceso,
              accionesPendientes,
              porcentajeAvance
            };
          });
        } else {
          this.procesosData = [];
        }
      } catch (error) {
        console.error('Error al cargar el resumen por procesos:', error);
      } finally {
        this.isLoading = false;
      }
    },
    getProgressBarClass(value) {
      if (value >= 80) return 'bg-success';
      if (value >= 50) return 'bg-warning';
      return 'bg-danger';
    }
  },
  watch: {
    mostrarTodos() {
      this.fetchData();
    }
  },
  mounted() {
    this.fetchData();
  },
};
</script>

<style scoped>
.badge-light-primary {
  background-color: rgba(78, 115, 223, 0.1);
  color: #4e73df;
}

.badge-light-success {
  background-color: rgba(28, 200, 138, 0.1);
  color: #1cc88a;
}

.border-dashed {
  border-style: dashed !important;
}

.border-bottom-light {
  border-bottom: 1px solid #f8f9fc;
}
</style>