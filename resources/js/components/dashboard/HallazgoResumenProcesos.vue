<template>
  <div class="card h-100">
    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
      <h6 class="mb-0">Resumen por Proceso</h6>
    </div>
    <div class="card-body d-flex align-items-center justify-content-center">
      <div v-if="isLoading" class="text-center">
        <div class="spinner-border text-danger" role="status">
          <span class="sr-only">Cargando...</span>
        </div>
      </div>
      <div v-else class="table-responsive">
        <table class="table table-sm table-striped table-hover table-bordered align-middle text-center">
          <thead class="thead-light">
            <tr>
              <th>Proceso</th>
              <th>Abiertos</th>
              <th>En Proceso</th>
              <th>Cerrados</th>
              <th>Total</th>
              <th>Acciones Pendientes</th>
              <th>% Avance</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="proceso in procesosData" :key="proceso.id">
              <td class="text-left">{{ proceso.nombre }}</td>
              <td>{{ proceso.abiertos }}</td>
              <td>{{ proceso.enProceso }}</td>
              <td>{{ proceso.cerrados }}</td>
              <td><strong>{{ proceso.total }}</strong></td>
              <td>{{ proceso.accionesPendientes }}</td>
              <td>
                <div class="text-center">
                  <strong>{{ proceso.porcentajeAvance }}%</strong>
                  <div class="progress" style="height: 8px; margin-top: 5px;">
                    <div class="progress-bar" :class="getProgressBarClass(proceso.porcentajeAvance)" role="progressbar"
                      :style="{ width: proceso.porcentajeAvance + '%' }" :aria-valuenow="proceso.porcentajeAvance" aria-valuemin="0"
                      aria-valuemax="100"></div>
                  </div>
                </div>
              </td>
            </tr>
            <tr v-if="procesosData.length === 0">
              <td colspan="7">No hay datos de hallazgos por proceso.</td>
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
        // Incluir parámetros de filtrado en la solicitud
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

        // Usar los datos de procesos que ya vienen filtrados y procesados desde el backend
        if (data.hallazgosPorProceso && Array.isArray(data.hallazgosPorProceso)) {
          // Calcular acciones pendientes y porcentaje de avance para cada proceso
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

            // Calcular porcentaje de avance promedio
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