<template>
    <div class="modal fade" id="mapaActoresModal" tabindex="-1" aria-hidden="true" ref="modalElement">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-chart-scatter mr-2"></i> Mapa de Actores (Matriz Poder / Interés)
                    </h5>
                    <button type="button" class="close text-white" @click="closeModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body position-relative">
                                    <!-- Canvas wrapper -->
                                    <div style="position: relative; height: 60vh; width: 100%">
                                        <canvas ref="chartCanvas"></canvas>
                                    </div>
                                    
                                    <!-- Overlays Removed for clarity with complex regions -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-white font-weight-bold text-danger border-bottom-0">
                                    Leyenda
                                </div>
                                <div class="card-body p-3 small overflow-auto" style="max-height: 60vh;">
                                    <ul class="list-unstyled">
                                        <li class="mb-3">
                                            <span class="badge badge-danger p-2 mr-2">I</span> <strong>Jugadores Clave</strong>
                                            <p class="text-muted mb-0">Gestión Proactiva</p>
                                        </li>
                                        <li class="mb-3">
                                            <span class="badge badge-warning text-dark p-2 mr-2">II</span> <strong>Satisfacer</strong>
                                            <p class="text-muted mb-0">Mantener Satisfecho</p>
                                        </li>
                                        <li class="mb-3">
                                            <span class="badge badge-info text-white p-2 mr-2">III</span> <strong>Informar</strong>
                                            <p class="text-muted mb-0">Mantener Informado</p>
                                        </li>
                                        <li class="mb-3">
                                            <span class="badge badge-secondary p-2 mr-2">IV</span> <strong>Monitorear</strong>
                                            <p class="text-muted mb-0">Mínimo Esfuerzo</p>
                                        </li>
                                    </ul>
                                    <hr>
                                    <p class="text-muted font-italic">
                                        <i class="fas fa-info-circle"></i> Los puntos pueden tener un ligero desplazamiento aleatorio para evitar superposiciones.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Footer with Print Action -->
                <div class="modal-footer bg-light">
                    <button class="btn btn-outline-secondary btn-sm" @click="closeModal">Cerrar</button>
                    <!-- Future: Export PDF -->
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch, onBeforeUnmount } from 'vue';
import { Modal } from 'bootstrap';
import Chart from 'chart.js/auto';

const props = defineProps({
    visible: Boolean,
    partes: Array
});

const emit = defineEmits(['close']);

const modalElement = ref(null);
let modalInstance = null;
const chartCanvas = ref(null);
let chartInstance = null;

onMounted(() => {
    if (modalElement.value) {
        modalInstance = new Modal(modalElement.value, { backdrop: 'static', keyboard: false });
        // Listen close event
        modalElement.value.addEventListener('hidden.bs.modal', (e) => {
            if (e.target === modalElement.value) {
                 emit('close');
            }
        });
    }
});

// Watch visible prop to open/close
watch(() => props.visible, (val) => {
    if (val) {
        modalInstance?.show();
        // Delay chart render slightly for modal animation
        setTimeout(() => {
            renderChart();
        }, 300);
    } else {
        modalInstance?.hide();
    }
});

const textToVal = (val) => {
    const v = val?.toLowerCase() || 'bajo';
    if (v === 'alto') return 3;
    if (v === 'medio') return 2;
    return 1;
};

const renderChart = () => {
    if (chartInstance) {
        chartInstance.destroy();
    }

    if (!chartCanvas.value) return;

    // Prepare Data
    // Helper for Initials
    const getInitials = (name) => {
        if (!name) return '';
        const words = name.trim().split(/\s+/);
        if(words.length === 1) return words[0].substring(0,2).toUpperCase();
        return (words[0][0] + words[1][0]).toUpperCase();
    };

    // Prepare Data
    const dataPoints = props.partes.map(p => {
        const jitterX = (Math.random() - 0.5) * 0.3;
        const jitterY = (Math.random() - 0.5) * 0.3;
        const xBase = textToVal(p.pi_nivel_interes);
        const yBase = textToVal(p.pi_nivel_influencia);
        
        // Count requirements
        const numReq = p.expectativas ? p.expectativas.length : 0;
        // Dynamic radius: Base 15, max 30. Scale log or linear. 
        // Let's do Base 12 + (numReq * 2), capped at 35.
        const radius = Math.min(12 + (numReq * 2), 35);
        
        // Requirements summary for tooltip
        const reqSummary = p.expectativas && p.expectativas.length > 0 
            ? p.expectativas.slice(0, 3).map(e => `• ${e.exp_descripcion.substring(0, 40)}...`).join('\n') + (p.expectativas.length > 3 ? `\n(+${p.expectativas.length - 3} más)` : '')
            : 'Sin requisitos registrados';

        return {
            x: xBase + jitterX,
            y: yBase + jitterY,
            label: p.pi_nombre,
            initials: getInitials(p.pi_nombre), 
            description: p.pi_descripcion,
            numReq: numReq,
            reqSummary: reqSummary,
            radius: radius,
            originalX: xBase, 
            originalY: yBase
        };
    });
    
    // Plugin to draw Initials
    const initialsPlugin = {
        id: 'initials',
        afterDatasetsDraw: (chart) => {
            const { ctx } = chart;
            ctx.save();
            ctx.font = 'bold 10px Arial';
            ctx.fillStyle = '#fff';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            
            const meta = chart.getDatasetMeta(0);
            meta.data.forEach((point, index) => {
                const data = chart.data.datasets[0].data[index];
                // Only draw text if radius is big enough
                if (data.radius >= 12) {
                    ctx.fillText(data.initials, point.x, point.y);
                }
            });
            ctx.restore();
        }
    };

    // KEEP quadrantPlugin...
    const quadrantPlugin = {
        id: 'quadrants',
        beforeDraw: (chart) => {
            const {ctx, chartArea: {left, top, right, bottom}, scales: {x, y}} = chart;
            // Grid Split Points
            const x25 = x.getPixelForValue(2.5);
            const x15 = x.getPixelForValue(1.5);
            const y25 = y.getPixelForValue(2.5);
            const y15 = y.getPixelForValue(1.5);

            ctx.save();
            
            // C1: JUGADOR CLAVE (Red)
            ctx.fillStyle = 'rgba(220, 53, 69, 0.1)'; 
            ctx.fillRect(x15, top, right - x15, y25 - top);
            ctx.fillRect(x25, y25, right - x25, y15 - y25);

            // C2: SATISFACER (Orange/Yellow)
            ctx.fillStyle = 'rgba(255, 193, 7, 0.15)';
            ctx.fillRect(left, top, x15 - left, y15 - top);
            
            // C3: INFORMAR (Blue)
            ctx.fillStyle = 'rgba(23, 162, 184, 0.15)';
            ctx.fillRect(x25, y15, right - x25, bottom - y15); 
            ctx.fillRect(x15, y25, x25 - x15, y15 - y25); 

            // C4: MONITOREAR (Grey)
            ctx.fillStyle = 'rgba(108, 117, 125, 0.15)';
            ctx.fillRect(left, y15, x25 - left, bottom - y15);

            // Draw Splits
            ctx.strokeStyle = '#999';
            ctx.lineWidth = 1;
            ctx.setLineDash([5, 5]);
            ctx.beginPath();
            ctx.moveTo(x15, top); ctx.lineTo(x15, bottom);
            ctx.moveTo(x25, top); ctx.lineTo(x25, bottom);
            ctx.moveTo(left, y15); ctx.lineTo(right, y15);
            ctx.moveTo(left, y25); ctx.lineTo(right, y25);
            ctx.stroke();
            ctx.restore();
        }
    };

    chartInstance = new Chart(chartCanvas.value, {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Actores',
                data: dataPoints,
                backgroundColor: (ctx) => {
                    const v = ctx.raw;
                    if(!v) return '#000';
                    const x = v.originalX; 
                    const y = v.originalY; 
                    if (y === 3) {
                        if (x >= 2) return '#dc3545'; // C1
                        return '#ffc107'; // C2
                    } else if (y === 2) {
                        if (x === 3) return '#dc3545'; // C1
                        if (x === 2) return '#17a2b8'; // C3
                        return '#ffc107'; // C2
                    } else { // Low
                        if (x === 3) return '#17a2b8'; // C3
                        return '#6c757d'; // C4
                    }
                },
                pointRadius: (ctx) => ctx.raw ? ctx.raw.radius : 15, // Dynamic Radius
                pointHoverRadius: (ctx) => ctx.raw ? ctx.raw.radius + 3 : 18
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    min: 0.5,
                    max: 3.5,
                    title: { display: true, text: 'Nivel de Interés', font: {weight: 'bold'} },
                    ticks: {
                        callback: (val) => {
                            if(val === 1) return 'BAJO';
                            if(val === 2) return 'MEDIO';
                            if(val === 3) return 'ALTO';
                            return '';
                        },
                        stepSize: 1
                    },
                    grid: { display:false } 
                },
                y: {
                    min: 0.5,
                    max: 3.5,
                    title: { display: true, text: 'Nivel de Influencia (Poder)', font: {weight: 'bold'} },
                    ticks: {
                        callback: (val) => {
                            if(val === 1) return 'BAJO';
                            if(val === 2) return 'MEDIO';
                            if(val === 3) return 'ALTO';
                            return '';
                        },
                         stepSize: 1
                    },
                    grid: { display:false }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: (ctx) => { 
                            return `${ctx.raw.label} (Req: ${ctx.raw.numReq})`; 
                        },
                        afterLabel: (ctx) => { 
                            return '\n' + ctx.raw.reqSummary; 
                        }
                    },
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 10,
                    bodyFont: { size: 12 }
                },
                legend: { display: false }
            }
        },
        plugins: [quadrantPlugin, initialsPlugin] // Added Plugin
    });
};

const closeModal = () => {
    emit('close');
};

</script>

<style scoped>
</style>
