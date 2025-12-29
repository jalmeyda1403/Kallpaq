<template>
    <Teleport to="body">
        <div ref="modalRef" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="graficoModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="graficoModalLabel">{{ headerTitle }}</h5>
                        <button type="button" class="close text-white" @click="closeModal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div v-if="loading" class="text-center py-5">
                            <i class="fas fa-spinner fa-spin fa-3x text-danger"></i>
                            <p class="mt-3 text-muted">Cargando datos del indicador...</p>
                        </div>
                        <div v-else>
                            <div class="card border-0 shadow-none mb-0">
                                <div class="card-body p-2">
                                    <Chart type="bar" :data="chartData" :options="chartOptions" class="h-25rem"
                                        :plugins="[dataLabelsPlugin]" />
                                </div>
                            </div>
                        </div>
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
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, LineElement, PointElement } from 'chart.js';
import { Modal } from 'bootstrap';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, LineElement, PointElement);

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

const headerTitle = computed(() => {
    return props.indicador ? `Gráfico de Avance: ${props.indicador.indicador_nombre}` : 'Gráfico de Avance';
});

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
            // Only draw labels for bar datasets (Valor)
            if (dataset.type === 'bar' || dataset.type === undefined) {
                meta.data.forEach((bar, index) => {
                    const value = dataset.data[index];
                    if (value !== null && value !== undefined) {
                        ctx.fillStyle = '#495057';
                        ctx.font = 'bold 11px sans-serif';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';
                        ctx.fillText(value, bar.x, bar.y - 5);
                    }
                });
            }
        });
    }
};

const loadData = async () => {
    if (!props.indicador) return;

    loading.value = true;
    try {
        const response = await axios.get(`/api/indicadores-gestion/${props.indicador.id}/avances`);
        const avances = response.data;

        // Sort by period/number
        const sortedAvances = [...avances].sort((a, b) => {
            if (a.is_periodo !== b.is_periodo) {
                return a.is_periodo - b.is_periodo;
            }
            return a.is_numero_periodo - b.is_numero_periodo;
        });

        const labels = sortedAvances.map(a => `${a.is_periodo} - P${a.is_numero_periodo}`);
        const valores = sortedAvances.map(a => parseFloat(a.is_valor));
        const metas = sortedAvances.map(a => parseFloat(a.is_meta));

        chartData.value = {
            labels: labels,
            datasets: [
                {
                    type: 'line',
                    label: 'Meta',
                    borderColor: '#dc3545', // Bootstrap Danger
                    borderWidth: 2,
                    fill: false,
                    tension: 0.1, // Less curve for cleaner look
                    pointRadius: 4,
                    pointBackgroundColor: '#dc3545',
                    data: metas,
                    order: 0 // Ensure line is on top
                },
                {
                    type: 'bar',
                    label: 'Valor',
                    backgroundColor: 'rgba(54, 162, 235, 0.7)', // Blue
                    borderColor: '#36A2EB',
                    borderWidth: 1,
                    data: valores,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8,
                    order: 1
                }
            ]
        };

        chartOptions.value = {
            maintainAspectRatio: false,
            aspectRatio: 0.6,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#495057',
                        font: {
                            size: 12
                        },
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#000',
                    bodyColor: '#000',
                    borderColor: '#ddd',
                    borderWidth: 1
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: '#495057',
                        font: {
                            weight: 'bold',
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
                        color: '#495057',
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: '#ebedef',
                        borderDash: [5, 5]
                    }
                }
            },
            layout: {
                padding: {
                    top: 40,
                    bottom: 0,
                    left: 0,
                    right: 0
                }
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
</style>
