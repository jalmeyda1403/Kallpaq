<template>
  <div class="card shadow-sm border-0 h-100">
    <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
      <h6 class="font-weight-bold text-dark mb-0 section-header">Distribución de Hallazgos</h6>
      <p class="text-muted small mb-0">Estado actual de registros</p>
    </div>
    <div class="card-body px-4 pb-4 d-flex align-items-center justify-content-center">
      <div v-if="isLoading" class="text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="sr-only">Cargando...</span>
        </div>
      </div>
      <div v-show="!isLoading" class="w-100" style="height: 300px; position: relative;">
        <canvas ref="chartCanvas"></canvas>
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

        const estadoCounts = data.hallazgos.reduce((acc, hallazgo) => {
          const estado = hallazgo.hallazgo_estado || 'Sin estado';
          acc[estado] = (acc[estado] || 0) + 1;
          return acc;
        }, {});

        const estados = Object.keys(estadoCounts);
        const cantidades = Object.values(estadoCounts);

        // Updated colors to be slightly more pastel/modern if needed, 
        // keeping mostly consistent but ensuring good contrast
        const colorMap = {
          'creado': '#e83e8c',
          'modificado': '#d63384',
          'aprobado': '#6f42c1',
          'desestimado': '#6c757d',
          'en proceso': '#4e73df', // Bootstrap Primary / AdminLTE Blue
          'concluido': '#1cc88a',  // Green
          'cerrado': '#36b9cc',    // Cyan
          'evaluado': '#36b9cc',
          'Sin estado': '#858796'
        };

        const colores = estados.map(estado => colorMap[estado] || '#858796');

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
            backgroundColor: backgroundColor,
            borderWidth: 2,
            borderColor: '#ffffff',
            hoverBorderColor: '#ffffff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'right',
              labels: {
                boxWidth: 12,
                usePointStyle: true,
                font: {
                  family: "'Inter', sans-serif",
                  size: 11
                },
                padding: 20
              }
            }
          },
          layout: {
            padding: 20
          },
          cutout: '70%' // Thinner doughnut for modern look
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

<style scoped>
/* Scoped styles mainly handled by classes */
</style>