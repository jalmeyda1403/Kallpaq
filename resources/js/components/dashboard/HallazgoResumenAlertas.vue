<template>
  <div class="row h-100">
    <!-- Hallazgos Vencidos -->
    <div class="col-md-6">
      <div class="card border-danger h-100">
        <div class="card-header bg-danger text-white">Hallazgos Vencidos ({{ hallazgosVencidos.total || 0 }})</div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush small">
            <li v-if="isLoadingVencidos" class="list-group-item text-center">
              <div class="spinner-border spinner-border-sm" role="status"></div>
            </li>
            <template v-else>
              <li v-for="h in hallazgosVencidos.data" :key="'vencido-' + h.id" class="list-group-item">
                <strong>{{ h.hallazgo_cod }}</strong> - {{ h.hallazgo_estado }}<br>
                <strong>Clasif.:</strong> {{ h.hallazgo_clasificacion }} -
                <strong>Identif.:</strong> {{ formatDate(h.hallazgo_fecha_identificacion) }}
              </li>
              <li v-if="hallazgosVencidos.data.length === 0" class="list-group-item text-muted">Sin hallazgos vencidos</li>
              <li v-for="i in (4 - hallazgosVencidos.data.length)" :key="'placeholder-v-' + i" class="list-group-item invisible">
                &nbsp;</li>
            </template>
          </ul>
        </div>
      </div>
    </div>

    <!-- Acciones Vencidas -->
    <div class="col-md-6">
      <div class="card border-warning h-100">
        <div class="card-header bg-warning text-dark">Acciones Vencidas ({{ accionesVencidas.total || 0 }})</div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush small">
            <li v-if="isLoadingAcciones" class="list-group-item text-center">
              <div class="spinner-border spinner-border-sm" role="status"></div>
            </li>
            <template v-else>
              <li v-for="a in accionesVencidas.data" :key="'accion-' + a.id" class="list-group-item">
                <strong>{{ a.accion_cod }}</strong> - {{ a.accion_tipo }}<br>
                <strong>Acción:</strong> {{ a.accion_nombre || a.accion_descripcion || 'Sin descripción' }}<br>
                <strong>Responsable:</strong> {{ a.accion_responsable }}<br>
                <strong>Venció:</strong> {{ formatDate(a.accion_fecha_fin_planificada) }}
              </li>
              <li v-if="accionesVencidas.data.length === 0" class="list-group-item text-muted">Sin acciones vencidas</li>
              <li v-for="i in (4 - accionesVencidas.data.length)" :key="'placeholder-a-' + i" class="list-group-item invisible">
                &nbsp;</li>
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

        // Procesar hallazgos vencidos
        this.hallazgosVencidos.data = data.hallazgos.filter(h => {
          const hallazgoAcciones = data.acciones.filter(a => a.hallazgo_id === h.id);
          return hallazgoAcciones.some(a => this.isFechaVencida(a.accion_fecha_fin_planificada));
        }).slice(0, 4); // Limitar a 4 items

        this.hallazgosVencidos.total = this.hallazgosVencidos.data.length;

        // Procesar acciones vencidas
        this.accionesVencidas.data = data.accionesVencidas.slice(0, 4); // Limitar a 4 items
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
      // Establecemos la hora a 00:00:00 para comparar solo las fechas
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