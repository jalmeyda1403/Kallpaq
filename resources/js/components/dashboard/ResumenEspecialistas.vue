<template>
  <div class="card h-100">
    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
      <h6 class="mb-0">Resumen por Especialista</h6>
      <select v-model="anioSeleccionado" id="anioSeleccionado" class="form-control form-control-sm ml-auto"
        style="width: auto;">
        <option v-for="anio in aniosDisponibles" :key="anio" :value="anio">{{ anio }}</option>
      </select>
    </div>
    <div class="card-body d-flex align-items-center justify-content-center">
      <div v-if="isLoading" class="text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="sr-only">Cargando...</span>
        </div>
      </div>
      <div v-else class="table-responsive">
        <table class="table table-sm  table-striped table-hover table-bordered align-middle text-center table-requerimientos">
          <thead class="thead-light">
            <tr>
              <th>Especialista</th>
              <th>Asignados</th>
              <th>Vencidos</th>
              <th>Finalizados</th>
              <th>Desestimados</th>
              <th>Avance Pendientes</th>
              <th>Eficacia</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="esp in especialistas" :key="esp.id">
              <td class="text-left">
                <img :src="esp.foto_url" alt="foto" class="img-circle elevation-2" width="40" height="40"
                  style="margin-right: 10px;">
                {{ esp.nombre }} ({{ esp.sigla }})
              </td>
              <td>{{ esp.total_asignados }}</td>
              <td><span :class="{'badge bg-danger': esp.total_vencidos > 0}">{{ esp.total_vencidos }}</span></td>
              <td>{{ esp.total_finalizados }}</td>
              <td><span :class="{'badge bg-danger': esp.total_desestimados > 0}">{{ esp.total_desestimados }}</span></td>
              <td>
                <div class="text-center">
                  <strong>{{ esp.promedioAvance }}%</strong>
                  <div class="progress" style="height: 8px; margin-top: 5px;">
                    <div class="progress-bar" :class="getProgressBarClass(esp.promedioAvance)" role="progressbar"
                      :style="{ width: esp.promedioAvance + '%' }" :aria-valuenow="esp.promedioAvance" aria-valuemin="0"
                      aria-valuemax="100"></div>
                  </div>
                </div>
              </td>
              <td>
                <div class="text-center">
                  <strong>{{ esp.efectividad }}%</strong>
                  <div class="progress" style="height: 8px; margin-top: 5px;">
                    <div class="progress-bar" :class="getProgressBarClass(esp.efectividad)" role="progressbar"
                      :style="{ width: esp.efectividad + '%' }" :aria-valuenow="esp.efectividad" aria-valuemin="0"
                      aria-valuemax="100"></div>
                  </div>
                </div>
              </td>
              <td>
                <a href="#" class="btn btn-outline-danger btn-sm" @click.prevent="verDetalle(esp)">
                  <i class="fas fa-eye"></i> Ver detalle
                </a>
              </td>
            </tr>
            <tr v-if="especialistas.length === 0">
              <td colspan="8">No hay especialistas registrados para el año seleccionado.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>


</template>

<script>
import axios from 'axios';
import { route } from 'ziggy-js';
import { Ziggy } from '../../ziggy';

export default {
  data() {
    return {
      isLoading: false,
      especialistas: [],
      aniosDisponibles: [],
      anioSeleccionado: new Date().getFullYear(),
    };
  },
  watch: {
    anioSeleccionado() {
      this.fetchData();
    },
  },
  methods: {
    fetchData() {
      this.isLoading = true;
      axios.get(route('dashboard.resumenEspecialistas', { anio: this.anioSeleccionado }, false, Ziggy))
        .then(response => {
          console.log('Datos recibidos del backend:', response.data);
          this.especialistas = response.data.especialistas;
          this.aniosDisponibles = response.data.aniosDisponibles;
        })
        .catch(error => {
          console.error('Error al cargar el resumen por especialistas:', error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    getProgressBarClass(value) {
      if (value >= 80) return 'bg-success';
      if (value >= 50) return 'bg-warning';
      return 'bg-danger';
    },
    verDetalle(especialista) {
      // Dispatch event to open the detail modal
      window.dispatchEvent(new CustomEvent('mostrarDetalleEspecialista', {
        detail: { id: especialista.id, anio: this.anioSeleccionado }
      }));
    }
  },
  mounted() {
    console.log('Componente ResumenEspecialistas montado.');
    this.fetchData();
  },
};
</script>
