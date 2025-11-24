<template>
  <div class="card h-100">
    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
      <h6 class="mb-0">Distribución de Hallazgos por Estado</h6>
    </div>
    <div class="card-body d-flex align-items-center justify-content-center">
      <div v-if="isLoading" class="text-center">
        <div class="spinner-border text-danger" role="status">
          <span class="sr-only">Cargando...</span>
        </div>
      </div>
      <canvas v-show="!isLoading" ref="chartCanvas" height="300"></canvas>
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
  name: 'HallazgoResumenGrafico',
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
      chartInstance: null,
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

        // Contar hallazgos por estado
        const estadoCounts = data.hallazgos.reduce((acc, hallazgo) => {
          const estado = hallazgo.hallazgo_estado || 'Sin estado';
          acc[estado] = (acc[estado] || 0) + 1;
          return acc;
        }, {});

        const estados = Object.keys(estadoCounts);
        const cantidades = Object.values(estadoCounts);

        // Mapeo de colores alineado con las tarjetas
        const colorMap = {
          'creado': '#e83e8c',           // bg-pink (rosa)
          'modificado': '#e83e8c',       // bg-pink (rosa)
          'aprobado': '#6610f2',         // bg-indigo (púrpura)
          'desestimado': '#6c757d',      // bg-secondary (gris)
          'en proceso': '#007bff',       // bg-primary (azul)
          'concluido': '#28a745',        // bg-success (verde)
          'cerrado': '#17a2b8',          // bg-info (cyan)
          'evaluado': '#17a2b8',         // bg-info (cyan)
          'Sin estado': '#f5f5f5'        // whitesmoke
        };

        // Asignar colores según el estado
        const colores = estados.map(estado => colorMap[estado] || '#6c757d');

        this.renderChart(estados, cantidades, colores);
      } catch (error) {
        console.error('Error al cargar los datos del gráfico:', error);
      } finally {
        this.isLoading = false;
      }
    },
    renderChart(labels, data, backgroundColor) {
      if (this.chartInstance) {
        this.chartInstance.destroy();
      }

      const ctx = this.$refs.chartCanvas.getContext('2d');
      this.chartInstance = new ChartJS(ctx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: data,
            backgroundColor: backgroundColor.slice(0, labels.length),
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'right',
            }
          }
        }
      });
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
  beforeUnmount() {
    if (this.chartInstance) {
      this.chartInstance.destroy();
    }
  }
};
</script>