<template>
  <div class="row text-white">
    <div class="col-md-12 mb-3 d-flex justify-content-end">
      <div v-if="esAdmin" class="d-flex align-items-center">
        <label class="form-check-label text-dark mr-2" for="mostrarTodosSwitch">
          <i class="fas fa-users mr-1"></i>Mostrar todos los hallazgos (Administrador)
        </label>
        <div class="custom-control custom-switch">
          <input
            type="checkbox"
            class="custom-control-input"
            id="mostrarTodosSwitch"
            :checked="mostrarTodos"
            @change="toggleMostrarTodos"
          >
          <label class="custom-control-label" for="mostrarTodosSwitch"></label>
        </div>
      </div>
    </div>
    <!-- Total y Creados -->
    <div class="col-md-2 col-sm-6 mb-3">
      <div class="summary-card bg-pink text-white rounded shadow p-3 text-center">
        <div v-if="isLoading" class="spinner-border spinner-border-sm text-light" role="status"></div>
        <div v-else>
          <div class="mb-2">
            <i class="fas fa-exclamation-triangle fa-2x"></i>
          </div>
          <div class="d-flex justify-content-around align-items-center">
            <div>
              <h4 class="mb-0">{{ stats.total }} |</h4>
              <small>Total | </small>
            </div>
            <div>
              <h4 class="mb-0">{{ stats.creados }}</h4>
              <small>Creados</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Aprobados -->
    <div class="col-md-2 col-sm-6 mb-3">
      <div class="summary-card bg-indigo">
        <div v-if="isLoading" class="spinner-border spinner-border-sm text-light" role="status"></div>
        <div v-else>
          <i class="fas fa-check-square"></i>
          <h4>{{ stats.aprobados }}</h4>
          <div class="small-box-footer">Aprobados</div>
        </div>
      </div>
    </div>

    <!-- Desestimados -->
    <div class="col-md-2 col-sm-6 mb-3">
      <div class="summary-card bg-secondary">
        <div v-if="isLoading" class="spinner-border spinner-border-sm text-light" role="status"></div>
        <div v-else>
          <i class="fas fa-ban"></i>
          <h4>{{ stats.desestimados }}</h4>
          <div class="small-box-footer">Desestimados</div>
        </div>
      </div>
    </div>

    <!-- En Proceso -->
    <div class="col-md-2 col-sm-6 mb-3">
      <div class="summary-card bg-primary">
        <div v-if="isLoading" class="spinner-border spinner-border-sm text-light" role="status"></div>
        <div v-else>
          <i class="fas fa-sync-alt"></i>
          <h4>{{ stats.enProceso }}</h4>
          <div class="small-box-footer">En Proceso</div>
        </div>
      </div>
    </div>

    <!-- Concluidos -->
    <div class="col-md-2 col-sm-6 mb-3">
      <div class="summary-card bg-success">
        <div v-if="isLoading" class="spinner-border spinner-border-sm text-light" role="status"></div>
        <div v-else>
          <i class="fas fa-flag-checkered"></i>
          <h4>{{ stats.concluidos }}</h4>
          <div class="small-box-footer">Concluidos</div>
        </div>
      </div>
    </div>

    <!-- Cerrados y Tasa de Cierre -->
    <div class="col-md-2 col-sm-6 mb-3">
      <div class="summary-card bg-info text-white rounded shadow p-3 text-center">
        <div v-if="isLoading" class="spinner-border spinner-border-sm text-light" role="status"></div>
        <div v-else>
          <div class="mb-2">
            <i class="fas fa-check-circle fa-2x"></i>
          </div>
          <div class="d-flex justify-content-around align-items-center">
            <div>
              <h4 class="mb-0">{{ stats.cerrados }}</h4>
              <small>Cerrados</small>
            </div>
            <div class="text-center" style="min-width: 10px;">
              <h4 class="mb-0">|</h4>
              <small>|</small>
            </div>
            <div>
              <h4 class="mb-0">{{ stats.tasaCierre }}%</h4>
              <small>Tasa Cierre</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.summary-card {
  border-radius: 5px;
  padding: 1rem;
  color: #fff;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 120px;
  text-align: center;
  transition: transform 0.2s;
}

.summary-card:hover {
  transform: translateY(-3px);
}

.summary-card i {
  font-size: 1.5rem;
  margin-bottom: 0.2rem;
}

.summary-card h4 {
  margin: 0;
  font-size: 1.6rem;
}

.summary-card p {
  margin: 0;
  font-size: 0.9rem;
}

.small-box-footer {
  font-size: 0.8rem;
}

.bg-teal {
  background-color: #20c997 !important;
}

.bg-indigo {
  background-color: #6610f2 !important;
}

.bg-pink {
  background-color: #e83e8c !important;
}
</style>

<script>
import axios from 'axios';

export default {
  name: 'HallazgoResumenGeneral',
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
      stats: {
        total: 0,
        creados: 0,
        aprobados: 0,
        desestimados: 0,
        enProceso: 0,
        concluidos: 0,
        cerrados: 0,
        tasaCierre: 0,
      },
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

        // Calcular estadísticas según los nuevos criterios
        this.stats.total = data.hallazgos.length;
        this.stats.creados = data.hallazgos.filter(h => h.hallazgo_estado === 'creado' || h.hallazgo_estado === 'modificado').length;
        this.stats.aprobados = data.hallazgos.filter(h => h.hallazgo_estado === 'aprobado').length;
        this.stats.desestimados = data.hallazgos.filter(h => h.hallazgo_estado === 'desestimado').length;
        this.stats.enProceso = data.hallazgos.filter(h => h.hallazgo_estado === 'en proceso').length;
        this.stats.concluidos = data.hallazgos.filter(h => h.hallazgo_estado === 'concluido').length;
        this.stats.cerrados = data.hallazgos.filter(h => h.hallazgo_estado === 'cerrado').length;
        
        // Tasa de Cierre = cerrados / (concluidos + cerrados)
        const denominador = this.stats.concluidos + this.stats.cerrados;
        this.stats.tasaCierre = denominador > 0 ? Math.round((this.stats.cerrados / denominador) * 100) : 0;
      } catch (error) {
        console.error('Error al cargar el resumen general de hallazgos:', error);
      } finally {
        this.isLoading = false;
      }
    },
    toggleMostrarTodos(event) {
      // Emitir evento para que el componente padre lo gestione
      this.$emit('update:mostrarTodos', event.target.checked);
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