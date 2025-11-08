<template>
  <div class="row h-100">
    <!-- Vencidos -->
    <div class="col-md-6">
      <div class="card border-danger h-100">
        <div class="card-header bg-danger text-white">Requerimientos Vencidos ({{ vencidos.total || 0 }})</div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush small">
            <li v-if="isLoadingVencidos" class="list-group-item text-center">
              <div class="spinner-border spinner-border-sm" role="status"></div>
            </li>
            <template v-else>
              <li v-for="r in vencidos.data" :key="'vencido-' + r.id" class="list-group-item">
                <strong>Req: {{ r.id }}</strong> - Avance: {{ r.avance ? r.avance.avance_registrado : 0 }}% <br>
                <strong>Inicio:</strong> {{ formatDate(r.fecha_asignacion) }} -
                <strong>Venció:</strong> {{ formatDate(r.fecha_limite) }}
              </li>
              <li v-if="vencidos.data.length === 0" class="list-group-item text-muted">Sin requerimientos vencidos</li>
              <li v-for="i in (4 - vencidos.data.length)" :key="'placeholder-v-' + i" class="list-group-item invisible">
                &nbsp;</li>
            </template>
          </ul>
        </div>
        <div class="card-footer d-flex justify-content-center">
          <pagination-controls :paginator="vencidos" @change-page="fetchVencidos"></pagination-controls>
        </div>
      </div>
    </div>

    <!-- En Riesgo -->
    <div class="col-md-6">
      <div class="card border-warning h-100">
        <div class="card-header bg-warning">Requerimientos en Riesgo ({{ enRiesgo.total || 0 }})</div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush small">
            <li v-if="isLoadingEnRiesgo" class="list-group-item text-center">
              <div class="spinner-border spinner-border-sm" role="status"></div>
            </li>
            <template v-else>
              <li v-for="r in enRiesgo.data" :key="'riesgo-' + r.id" class="list-group-item">
                <strong>Req: {{ r.id }}</strong> - Avance: {{ r.avance ? r.avance.avance_registrado : 0 }}%<br>
                <strong>Asignado a:</strong> {{ r.especialista ? r.especialista.sigla : 'Sin asignar' }}<br>
                <strong>Fecha:</strong> {{ formatDate(r.fecha_asignacion) }} - {{ formatDate(r.fecha_limite) }}
              </li>
              <li v-if="enRiesgo.data.length === 0" class="list-group-item text-muted">Sin requerimientos en riesgo</li>
              <li v-for="i in (4 - enRiesgo.data.length)" :key="'placeholder-r-' + i" class="list-group-item invisible">
                &nbsp;</li>
            </template>
          </ul>
        </div>
        <div class="card-footer d-flex justify-content-center">
          <pagination-controls :paginator="enRiesgo" @change-page="fetchEnRiesgo"></pagination-controls>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { route } from 'ziggy-js';
import { Ziggy } from '../../ziggy';

// Componente hijo para la paginación
const PaginationControls = {
  props: ['paginator'],
  template: `
    <nav v-if="paginator && paginator.last_page > 1">
      <ul class="pagination pagination-sm mb-0">
        <li class="page-item" :class="{ disabled: paginator.current_page === 1 }">
          <a class="page-link" href="#" @click.prevent="$emit('change-page', paginator.current_page - 1)">&laquo;</a>
        </li>
        <li v-for="page in paginator.last_page" :key="page" class="page-item" :class="{ active: page === paginator.current_page }">
          <a class="page-link" href="#" @click.prevent="$emit('change-page', page)">{{ page }}</a>
        </li>
        <li class="page-item" :class="{ disabled: paginator.current_page === paginator.last_page }">
          <a class="page-link" href="#" @click.prevent="$emit('change-page', paginator.current_page + 1)">&raquo;</a>
        </li>
      </ul>
    </nav>
  `
};

export default {
  components: {
    PaginationControls,
  },
  data() {
    return {
      isLoadingVencidos: false,
      isLoadingEnRiesgo: false,
      vencidos: { data: [] },
      enRiesgo: { data: [] },
    };
  },
  methods: {
    fetchVencidos(page = 1) {
      this.isLoadingVencidos = true;
      axios.get(route('dashboard.alertas', { type: 'vencidos', page }, false, Ziggy))
        .then(response => {
          console.log('Datos recibidos para Vencidos:', response.data);
          this.vencidos = response.data;
        })
        .catch(error => console.error('Error al cargar requerimientos vencidos:', error))
        .finally(() => this.isLoadingVencidos = false);
    },
    fetchEnRiesgo(page = 1) {
      this.isLoadingEnRiesgo = true;
      axios.get(route('dashboard.alertas', { type: 'enRiesgo', page }, false, Ziggy))
        .then(response => {
          console.log('Datos recibidos para En Riesgo:', response.data);
          this.enRiesgo = response.data;
        })
        .catch(error => console.error('Error al cargar requerimientos en riesgo:', error))
        .finally(() => this.isLoadingEnRiesgo = false);
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      const date = new Date(dateString);
      return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }
  },
  mounted() {
    this.fetchVencidos();
    this.fetchEnRiesgo();
  },
};
</script>
