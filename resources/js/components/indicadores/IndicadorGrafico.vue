<template>
    <Teleport to="body">
        <div ref="modalRef" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title font-weight-bold">
                            Gráfico de Tendencia <br>
                            <small class="text-white-50" style="font-size: 0.85rem;">{{ indicador?.indicador_nombre
                                }}</small>
                        </h5>
                        <button type="button" class="close text-white" @click="closeModal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body bg-light-soft px-4 py-4">
                        <div v-if="loading" class="text-center py-5">
                            <div class="spinner-border text-danger mb-3" role="status"
                                style="width: 3rem; height: 3rem;">
                                <span class="sr-only">Cargando...</span>
                            </div>
                            <h6 class="text-muted font-weight-bold">Generando análisis gráfico...</h6>
                        </div>

                        <div v-else-if="!hasData" class="text-center py-5">
                            <i class="fas fa-chart-bar fa-4x text-muted opacity-3 mb-3"></i>
                            <h5 class="text-muted">No hay datos suficientes para generar el gráfico</h5>
                            <button class="btn btn-outline-danger mt-3" @click="closeModal">Cerrar</button>
                        </div>

                        <div v-else>
                            <!-- Summary Statistics Cards -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body p-3 d-flex align-items-center">
                                            <div class="rounded-circle bg-primary-soft p-3 mr-3 text-primary">
                                                <i class="fas fa-history fa-lg"></i>
                                            </div>
                                            <div>
                                                <small
                                                    class="text-muted text-uppercase font-weight-bold">Mediciones</small>
                                                <h4 class="mb-0 font-weight-bold">{{ stats.count }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body p-3 d-flex align-items-center">
                                            <div class="rounded-circle bg-info-soft p-3 mr-3 text-info">
                                                <i class="fas fa-trophy fa-lg"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted text-uppercase font-weight-bold">Máximo
                                                    Logro</small>
                                                <h4 class="mb-0 font-weight-bold">{{ stats.max }}%</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body p-3 d-flex align-items-center">
                                            <div class="rounded-circle bg-warning-soft p-3 mr-3 text-warning">
                                                <i class="fas fa-flag-checkered fa-lg"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted text-uppercase font-weight-bold">Meta
                                                    Promedio</small>
                                                <h4 class="mb-0 font-weight-bold">{{ stats.avgMeta }}%</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Chart Container -->
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-0 pt-4 pb-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="font-weight-bold text-dark mb-1">Evolución Histórica</h6>
                                            <p class="text-muted small mb-0">Comparativa de Meta vs. Valor Real obtenido
                                                por periodo</p>
                                        </div>
                                        <div class="badge badge-light border px-3 py-2">
                                            <i class="fas fa-calendar-alt mr-1"></i> {{ currentYear }}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <Chart type="bar" :data="chartData" :options="chartOptions" class="h-25rem"
                                        :plugins="[dataLabelsPlugin]" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-white border-top-0 py-3">
                        <button type="button" class="btn btn-secondary px-4" @click="closeModal">
                            <i class="fas fa-times mr-1"></i> Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, watch, computed, onMounted, onUnmounted, nextTick } from 'vue';
import Chart from 'primevue/chart';
import axios from 'axios';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, LineElement, PointElement, Filler } from 'chart.js';
import { Modal } from 'bootstrap';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, LineElement, PointElement, Filler);

const props = defineProps({
    visible: {
        type: Boolean,
        required: true
    },
    indicador: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['close']);

const loading = ref(false);
const chartData = ref({});
const chartOptions = ref({});
const modalRef = ref(null);
const modalInstance = ref(null);
const stats = ref({ count: 0, average: 0, max: 0, avgMeta: 0 });
const hasData = ref(false);
const currentYear = new Date().getFullYear();

const closeModal = () => {
    if (modalInstance.value) {
        modalInstance.value.hide();
    } else {
        emit('close');
    }
};

// Custom plugin to draw data labels on top of bars
const dataLabelsPlugin = {
    id: 'customDataLabels',
    afterDatasetsDraw(chart, args, options) {
        const { ctx } = chart;
        chart.data.datasets.forEach((dataset, i) => {
            const meta = chart.getDatasetMeta(i);
            // Only draw labels for bar datasets (Valor) within visible range
            if (!meta.hidden && (dataset.type === 'bar' || dataset.type === undefined)) {
                meta.data.forEach((bar, index) => {
                    const value = dataset.data[index];
                    if (value !== null && value !== undefined) {
                        ctx.fillStyle = '#495057';
                        ctx.font = 'bold 10px "Inter", sans-serif';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';
                        ctx.fillText(value + '%', bar.x, bar.y - 5);
                    }
                });
            }
        });
    }
};

const calculateStats = (valores, metas) => {
    if (!valores.length) return;

    // Count
    stats.value.count = valores.length;

    // Average
    const sum = valores.reduce((a, b) => a + b, 0);
    stats.value.average = (sum / valores.length).toFixed(2);

    // Max
    stats.value.max = Math.max(...valores).toFixed(2);

    // Average Meta
    const sumMeta = metas.reduce((a, b) => a + b, 0);
    stats.value.avgMeta = (sumMeta / metas.length).toFixed(2);
};

const loadData = async () => {
    if (!props.indicador) return;

    loading.value = true;
    hasData.value = false;

    try {
        const response = await axios.get(`/api/indicadores-gestion/${props.indicador.id}/avances`);
        const avances = response.data;

        if (!avances || avances.length === 0) {
            loading.value = false;
            return;
        }

        hasData.value = true;

        // Sort by period/number
        const sortedAvances = [...avances].sort((a, b) => {
            if (a.is_periodo !== b.is_periodo) {
                return a.is_periodo - b.is_periodo;
            }
            return a.is_numero_periodo - b.is_numero_periodo;
        });

        const labels = sortedAvances.map(a => `P${a.is_numero_periodo}-${a.is_periodo.toString().substr(-2)}`);
        const valores = sortedAvances.map(a => parseFloat(a.is_valor));
        const metas = sortedAvances.map(a => parseFloat(a.is_meta));

        calculateStats(valores, metas);

        chartData.value = {
            labels: labels,
            datasets: [
                {
                    type: 'line',
                    label: 'Meta Establecida',
                    borderColor: '#dc3545',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#dc3545',
                    pointBorderWidth: 2,
                    data: metas,
                    order: 0
                },
                {
                    type: 'bar',
                    label: 'Valor Obtenido',
                    backgroundColor: (ctx) => {
                        const canvas = ctx.chart.ctx;
                        const gradient = canvas.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, 'rgba(54, 162, 235, 0.8)');
                        gradient.addColorStop(1, 'rgba(54, 162, 235, 0.2)');
                        return gradient;
                    },
                    borderColor: '#36A2EB',
                    borderWidth: 0,
                    borderRadius: 4,
                    hoverBackgroundColor: '#36A2EB',
                    data: valores,
                    barPercentage: 0.5,
                    categoryPercentage: 0.8,
                    order: 1
                }
            ]
        };

        chartOptions.value = {
            maintainAspectRatio: false,
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    align: 'center',
                    labels: {
                        color: '#6c757d',
                        font: {
                            family: "'Inter', sans-serif",
                            size: 11,
                            weight: '500'
                        },
                        usePointStyle: true,
                        boxWidth: 8,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#1e293b',
                    titleFont: { size: 13, weight: 'bold', family: "'Inter', sans-serif" },
                    bodyColor: '#475569',
                    bodyFont: { size: 12, family: "'Inter', sans-serif" },
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: true,
                    boxPadding: 4,
                    callbacks: {
                        label: function (context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += context.parsed.y + '%';
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: '#64748b',
                        font: {
                            family: "'Inter', sans-serif",
                            size: 11
                        }
                    },
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#64748b',
                        font: {
                            family: "'Inter', sans-serif",
                            size: 11
                        },
                        callback: function (value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        color: '#f1f5f9',
                        borderDash: [0, 0]
                    },
                    border: {
                        display: false
                    }
                }
            },
            layout: {
                padding: 10
            }
        };

    } catch (error) {
        console.error('Error loading chart data:', error);
    } finally {
        loading.value = false;
    }
};

watch(() => props.visible, async (newVal) => {
    if (newVal) {
        loadData();
        await nextTick();
        if (modalRef.value) {
            if (!modalInstance.value) {
                modalInstance.value = new Modal(modalRef.value, {
                    backdrop: 'static',
                    keyboard: false,
                    focus: false
                });
            }
            modalInstance.value.show();
        }
    } else {
        if (modalInstance.value) {
            modalInstance.value.hide();
        }
    }
}, { immediate: true });

onMounted(() => {
    if (modalRef.value) {
        modalRef.value.addEventListener('hidden.bs.modal', () => {
            emit('close');
        });
    }
});

onUnmounted(() => {
    if (modalInstance.value) {
        modalInstance.value.dispose();
        modalInstance.value = null;
    }
});
</script>

<style scoped>
.h-25rem {
    height: 25rem;
}

.bg-light-soft {
    background-color: #f8f9fa;
}

.bg-primary-soft {
    background-color: rgba(13, 110, 253, 0.1);
}

.bg-success-soft {
    background-color: rgba(25, 135, 84, 0.1);
}

.bg-info-soft {
    background-color: rgba(13, 202, 240, 0.1);
}

.bg-warning-soft {
    background-color: rgba(255, 193, 7, 0.1);
}

.shadow-sm {
    box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
}

.text-white-50 {
    color: rgba(255, 255, 255, 0.75) !important;
}
</style>
