<template>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Dashboard de Satisfacción del Cliente</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <!-- KPI Cards -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ averageNPS }}</h3>
                                <p>NPS Promedio Global</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ totalSurveys }}</h3>
                                <p>Total Encuestas</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-poll"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- NPS Trend Chart -->
                    <div class="col-md-6">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Evolución del NPS</h3>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="position: relative; height:300px;">
                                    <canvas ref="npsChartCanvas"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Averages Chart -->
                    <div class="col-md-6">
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Promedio por Conductor</h3>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="position: relative; height:300px;">
                                    <canvas ref="categoryChartCanvas"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, onUnmounted } from 'vue';
import { useEncuestasStore } from '@/stores/encuestasStore';
import Chart from 'chart.js/auto';

const encuestasStore = useEncuestasStore();
const npsChartCanvas = ref(null);
const categoryChartCanvas = ref(null);
let npsChartInstance = null;
let categoryChartInstance = null;

const averageNPS = computed(() => {
    const data = encuestasStore.dashboardData.nps_trend;
    if (!data || data.length === 0) return 0;
    const sum = data.reduce((acc, curr) => acc + parseFloat(curr.avg_nps), 0);
    return (sum / data.length).toFixed(2);
});

const totalSurveys = computed(() => {
    // This is a bit of a hack since dashboardData doesn't strictly return total count, 
    // but we can infer or add it to the API. 
    // For now, let's just use the length of trend points as a proxy or 0 if empty.
    // Ideally, we should fetch this from the API.
    // Let's assume the store has 'encuestas' loaded if we visited the index, but maybe not here.
    // Let's just return '-' for now or update API to return it.
    // Actually, let's calculate it from the nps_trend if it had count, but it has avg.
    return '-';
});

const renderNpsChart = () => {
    if (npsChartInstance) npsChartInstance.destroy();

    const data = encuestasStore.dashboardData.nps_trend;
    const labels = data.map(d => `${d.periodo} ${d.anio}`);
    const values = data.map(d => d.avg_nps);

    npsChartInstance = new Chart(npsChartCanvas.value, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'NPS Score',
                data: values,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false, // NPS can be negative
                    suggestedMin: -100,
                    suggestedMax: 100
                }
            }
        }
    });
};

const renderCategoryChart = () => {
    if (categoryChartInstance) categoryChartInstance.destroy();

    const data = encuestasStore.dashboardData.category_averages;
    const labels = data.map(d => d.categoria);
    const values = data.map(d => d.avg_score);

    categoryChartInstance = new Chart(categoryChartCanvas.value, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Promedio Puntaje',
                data: values,
                backgroundColor: '#28a745',
                borderColor: '#28a745',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 10 // Assuming 0-10 scale
                }
            }
        }
    });
};

onMounted(async () => {
    await encuestasStore.fetchDashboardData();
    renderNpsChart();
    renderCategoryChart();
});

onUnmounted(() => {
    if (npsChartInstance) npsChartInstance.destroy();
    if (categoryChartInstance) categoryChartInstance.destroy();
});
</script>
