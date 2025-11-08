<template>
  <div class="card h-100">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h6 class="mb-0">Evolución Mensual de Requerimientos</h6>
     <select class="form-control form-control-sm ml-auto" style="width: auto;" v-model="selectedYear" @change="fetchData">
        <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
      </select>
    </div>
    <div class="card-body d-flex align-items-center justify-content-center">
      <div v-if="isLoading" class="spinner-border text-primary" role="status">
        <span class="sr-only">Cargando...</span>
      </div>
      <canvas v-show="!isLoading" ref="chartCanvas"></canvas>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { route } from 'ziggy-js';
import { Ziggy } from '../../ziggy';
import Chart from 'chart.js/auto';

export default {
  data() {
    return {
      isLoading: false,
      chartInstance: null,
      selectedYear: new Date().getFullYear(), // Default to current year
      years: [], // To be populated
    };
  },
  created() {
    this.populateYears();
  },
  methods: {
    populateYears() {
      const currentYear = new Date().getFullYear();
      for (let i = currentYear; i >= currentYear - 5; i--) { // Last 5 years + current
        this.years.push(i);
      }
    },
    fetchData() {
      this.isLoading = true;
      axios.get(route('dashboard.resumenGrafico', { year: this.selectedYear }, false, Ziggy))
        .then(response => {
          this.renderChart(response.data);
        })
        .catch(error => {
          console.error('Error al cargar los datos del gráfico:', error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    renderChart(data) {
      if (this.chartInstance) {
        this.chartInstance.destroy();
      }
      const ctx = this.$refs.chartCanvas.getContext('2d');
      this.chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
          labels: data.labels,
          datasets: [
            {
              label: 'Asignados',
              data: data.asignados,
              borderColor: 'rgba(108, 117, 125, 1)',
              backgroundColor: 'rgba(108, 117, 125, 0.2)',
              tension: 0.3,
              fill: true,
              pointRadius: 4
            },
            {
              label: 'Fin Planificado',
              data: data.programados,
              borderColor: 'rgba(80, 200, 162, 1)',
              backgroundColor: 'rgba(80, 200, 162, 0.2)',
              tension: 0.3,
              fill: true,
              pointRadius: 4
            },
            {
              label: 'Fin Real',
              data: data.finalizados,
              borderColor: 'rgba(0, 123, 255, 1)',
              backgroundColor: 'rgba(0, 123, 255, 0.2)',
              tension: 0.3,
              fill: true,
              pointRadius: 4
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'top'
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1
              }
            }
          }
        }
      });
    }
  },
  mounted() {
    this.fetchData();
  },
};
</script>
