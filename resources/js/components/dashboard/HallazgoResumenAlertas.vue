<template>
  <div class="row h-100">
    <!-- Hallazgos Vencidos -->
    <div class="col-md-6 mb-3 mb-md-0">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0 d-flex align-items-center">
          <div class="d-flex align-items-center justify-content-center bg-light-danger rounded-circle mr-3"
            style="width: 40px; height: 40px;">
            <i class="fas fa-exclamation-circle text-danger"></i>
          </div>
          <div>
            <h6 class="font-weight-bold text-dark mb-0 section-header">Hallazgos Vencidos</h6>
            <p class="text-muted small mb-0">{{ hallazgosVencidos.total || 0 }} registros pendientes</p>
          </div>
        </div>
        <div class="card-body px-4 pt-3 pb-4">
          <ul class="list-group list-group-flush small">
            <li v-if="isLoadingVencidos" class="list-group-item text-center border-0">
              <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            </li>
            <template v-else>
              <li v-for="h in hallazgosVencidos.data" :key="'vencido-' + h.id"
                class="list-group-item border-0 px-0 py-3 border-bottom-dashed">
                <div class="d-flex justify-content-between">
                  <span class="font-weight-bold text-dark">{{ h.hallazgo_cod }}</span>
                  <span class="badge badge-pill badge-light-danger text-danger">{{ h.hallazgo_estado }}</span>
                </div>
                <div class="mt-1 text-secondary">
                  <div class="text-truncate">{{ h.hallazgo_clasificacion }}</div>
                  <small class="text-muted"><i class="far fa-calendar-alt mr-1"></i>{{
                    formatDate(h.hallazgo_fecha_identificacion) }}</small>
                </div>
              </li>
              <li v-if="hallazgosVencidos.data.length === 0"
                class="list-group-item text-center text-muted border-0 py-4">
                <i class="fas fa-check-circle text-success mb-2 fa-2x"></i>
                <p class="mb-0">¡Todo al día! Sin hallazgos vencidos.</p>
              </li>
            </template>
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
            <li v-if="isLoadingAcciones" class="list-group-item text-center border-0">
              <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            </li>
            <template v-else>
              <li v-for="a in accionesVencidas.data" :key="'accion-' + a.id"
                class="list-group-item border-0 px-0 py-3 border-bottom-dashed">
                <div class="d-flex justify-content-between mb-1">
                  <span class="font-weight-bold text-dark">{{ a.accion_cod }}</span>
                  <span class="badge badge-pill badge-light-warning text-warning">{{ a.accion_tipo }}</span>
                </div>
                <p class="mb-1 text-dark leading-tight">{{ a.accion_nombre || a.accion_descripcion || 'Sin descripción'
                  }}</p>
                <div class="d-flex justify-content-between align-items-center mt-2">
                  <small class="text-muted"><i class="far fa-user mr-1"></i>{{ a.accion_responsable }}</small>
                  <small class="text-danger font-weight-bold">{{ formatDate(a.accion_fecha_fin_planificada) }}</small>
                </div>
              </li>
              <li v-if="accionesVencidas.data.length === 0"
                class="list-group-item text-center text-muted border-0 py-4">
                <i class="fas fa-check-circle text-success mb-2 fa-2x"></i>
                <p class="mb-0">¡Excelente! Sin acciones vencidas.</p>
              </li>
            </template>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'HallazgoResumenAlertas',
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
      isLoadingVencidos: false,
      isLoadingAcciones: false,
      hallazgosVencidos: { data: [] },
      accionesVencidas: { data: [] },
    };
  },
  methods: {
    async fetchData() {
      this.isLoadingVencidos = true;
      this.isLoadingAcciones = true;

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

        this.hallazgosVencidos.data = data.hallazgos.filter(h => {
          const hallazgoAcciones = data.acciones.filter(a => a.hallazgo_id === h.id);
          return hallazgoAcciones.some(a => this.isFechaVencida(a.accion_fecha_fin_planificada));
        }).slice(0, 4);

        this.hallazgosVencidos.total = this.hallazgosVencidos.data.length;

        this.accionesVencidas.data = data.accionesVencidas.slice(0, 4);
        this.accionesVencidas.total = data.accionesVencidas.length;
      } catch (error) {
        console.error('Error al cargar alertas de hallazgos:', error);
      } finally {
        this.isLoadingVencidos = false;
        this.isLoadingAcciones = false;
      }
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      const date = new Date(dateString);
      return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
    },
    isFechaVencida(fecha) {
      if (!fecha) return false;
      const fechaFin = new Date(fecha);
      const hoy = new Date();
      hoy.setHours(0, 0, 0, 0);
      fechaFin.setHours(0, 0, 0, 0);
      return fechaFin < hoy;
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
.bg-light-danger {
  background-color: rgba(220, 53, 69, 0.1) !important;
}

.bg-light-warning {
  background-color: rgba(255, 193, 7, 0.1) !important;
}

.badge-light-danger {
  background-color: rgba(220, 53, 69, 0.1);
  color: #dc3545;
}

.badge-light-warning {
  background-color: rgba(255, 193, 7, 0.1);
  color: #ffc107;
}

.border-bottom-dashed {
  border-bottom: 1px dashed #e9ecef;
}

.leading-tight {
  line-height: 1.25;
}
</style>