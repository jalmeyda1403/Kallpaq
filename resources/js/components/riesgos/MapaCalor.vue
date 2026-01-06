<template>
    <div class="card-body p-0 bg-white">
        <div class="container-fluid">
            <div class="row no-gutters">
                <!-- Heat Map Section -->
                <div class="col-lg-8 p-5 d-flex flex-column align-items-center bg-white">
                    
                    <!-- Filter Control (Pill Design) -->
                    <div class="d-flex justify-content-center mb-5">
                        <div class="nav nav-pills custom-pills">
                            <a class="nav-link" :class="{ active: matrizFilter === '' }" 
                               @click.prevent="matrizFilter = ''" href="#">Todas</a>
                            <a class="nav-link" :class="{ active: matrizFilter === 'estrategica' }" 
                               @click.prevent="matrizFilter = 'estrategica'" href="#">Estratégica</a>
                            <a class="nav-link" :class="{ active: matrizFilter === 'tactica' }" 
                               @click.prevent="matrizFilter = 'tactica'" href="#">Táctica</a>
                        </div>
                    </div>

                    <div class="heatmap-wrapper position-relative">
                        <!-- Eje Y Label -->
                        <div class="axis-y-label">
                            <span class="text-uppercase tracking-wider text-muted font-weight-bold" style="letter-spacing: 2px;">Probabilidad</span>
                            <i class="fas fa-long-arrow-alt-up ml-2 text-muted"></i>
                        </div>

                        <!-- Eje X Label -->
                        <div class="axis-x-label">
                            <span class="text-uppercase tracking-wider text-muted font-weight-bold" style="letter-spacing: 2px;">Impacto</span>
                            <i class="fas fa-long-arrow-alt-right ml-2 text-muted"></i>
                        </div>

                        <!-- Matriz Container -->
                        <div class="heatmap-container shadow-lg">
                            <!-- Grid Overlay -->
                            <div class="heatmap-grid" style="display: grid; grid-template-columns: repeat(10, 1fr); grid-template-rows: repeat(10, 1fr); gap: 2px; padding: 2px; width: 560px; height: 560px;">
                                <!-- Generación de celdas invertida para Y (10 a 1) y normal para X (1 a 10) -->
                                <template v-for="y in 10">
                                    <div v-for="x in 10" :key="`cell-${x}-${11-y}`"
                                         class="heatmap-cell position-relative d-flex justify-content-center align-items-center"
                                         :class="getCellClass(x, 11-y)"
                                         :title="`Impacto: ${x}, Probabilidad: ${11-y}`"
                                         @click="getRiesgosEnCelda(x, 11-y).length > 0 ? mostrarRiesgos(x, 11-y) : null">
                                        
                                        <!-- Risk Counter Badge -->
                                        <transition name="scale">
                                            <div v-if="getRiesgosEnCelda(x, 11-y).length > 0" 
                                                 class="risk-counter"
                                                 :class="{'active': celdaSeleccionada.x === x && celdaSeleccionada.y === (11-y)}"
                                                  @click.stop="mostrarRiesgos(x, 11-y)">
                                                {{ getRiesgosEnCelda(x, 11-y).length }}
                                            </div>
                                        </transition>
                                    </div>
                                </template>
                            </div>

                            <!-- Axis Numbers Y -->
                            <div class="axis-numbers-y">
                                <div v-for="n in 10" :key="'y-'+(11-n)" class="axis-num">{{ 11-n }}</div>
                            </div>
                            <!-- Axis Numbers X -->
                            <div class="axis-numbers-x">
                                <div v-for="n in 10" :key="'x-'+n" class="axis-num">{{ n }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Modern Legend -->
                    <div class="mt-5 d-flex justify-content-center w-100">
                        <div class="legend-bar d-flex align-items-center px-4 py-3 rounded-pill bg-light shadow-sm">
                            <div class="d-flex align-items-center mx-3">
                                <div class="legend-dot risk-low rounded-circle mr-2"></div>
                                <span class="text-muted font-weight-bold small">Bajo</span>
                            </div>
                            <div class="d-flex align-items-center mx-3">
                                <div class="legend-dot risk-medium rounded-circle mr-2"></div>
                                <span class="text-muted font-weight-bold small">Medio</span>
                            </div>
                            <div class="d-flex align-items-center mx-3">
                                <div class="legend-dot risk-high rounded-circle mr-2"></div>
                                <span class="text-muted font-weight-bold small">Alto</span>
                            </div>
                            <div class="d-flex align-items-center mx-3">
                                <div class="legend-dot risk-critical rounded-circle mr-2"></div>
                                <span class="text-muted font-weight-bold small">Muy Alto</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar: Details -->
                <div class="col-lg-4 border-left bg-light-gray position-relative" style="min-height: 600px; background-color: #f8f9fa;">
                    <transition name="fade" mode="out-in">
                        <!-- Empty State (Only if no risks at all in the filter) -->
                        <div v-if="riesgosListados.length === 0" key="empty" class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-4">
                            <div class="icon-circle mb-4 bg-white shadow-sm text-primary">
                                <i class="fas fa-chart-area fa-2x"></i>
                            </div>
                            <h5 class="font-weight-bold text-dark mb-2">No se encontraron riesgos</h5>
                            <p class="text-muted mb-0" style="max-width: 250px;">No hay riesgos registrados para el criterio seleccionado.</p>
                        </div>
                        
                        <!-- Data List -->
                        <div v-else key="list" class="d-flex flex-column h-100">
                            <!-- Professional Header -->
                            <div class="p-4 border-bottom bg-white shadow-sm z-index-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="font-weight-bold text-dark m-0 d-flex align-items-center">
                                        <i class="fas fa-list-ul mr-2 text-primary"></i>
                                        {{ listTitle }}
                                    </h6>
                                    <span v-if="celdaSeleccionada.x !== 0" class="badge badge-pill badge-primary px-3 py-2">
                                        {{ celdaSeleccionada.x }} x {{ celdaSeleccionada.y }}
                                    </span>
                                </div>
                                <p class="small text-muted mb-0" v-if="celdaSeleccionada.x !== 0">Listado de riesgos clasificados en este nivel.</p>
                                <p class="small text-muted mb-0" v-else>Mostrando todos los riesgos del filtro seleccionado.</p>
                            </div>

                            <div class="overflow-auto flex-grow-1 p-3 custom-scrollbar">
                                <div v-for="riesgo in displayedRiesgos" :key="riesgo.id" 
                                     class="card border-0 shadow-sm mb-3 risk-card hover-lift">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge badge-light border text-muted">{{ riesgo.riesgo_cod }}</span>
                                            <span :class="getBadgeClass(riesgo.riesgo_nivel)" class="badge-pill px-2" style="font-size: 10px;">{{ riesgo.riesgo_nivel }}</span>
                                        </div>
                                        <h6 class="text-dark font-weight-bold mb-2" style="font-size: 14px; line-height: 1.4;">{{ riesgo.riesgo_nombre }}</h6>
                                        <div class="d-flex align-items-center text-muted small">
                                            <i class="fas fa-layer-group mr-2 text-primary-light"></i>
                                            <span class="text-truncate">{{ riesgo.proceso?.proceso_nombre || 'Sin Proceso' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pagination -->
                            <div v-if="totalPages > 1" class="p-3 border-top bg-white">
                                <nav>
                                    <ul class="pagination pagination-sm justify-content-center m-0">
                                        <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                            <a class="page-link border-0 rounded-circle mx-1" href="#" @click.prevent="prevPage"><i class="fas fa-chevron-left"></i></a>
                                        </li>
                                        <li class="page-item disabled"><span class="page-link border-0 bg-transparent">{{ currentPage }} / {{ totalPages }}</span></li>
                                        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                            <a class="page-link border-0 rounded-circle mx-1" href="#" @click.prevent="nextPage"><i class="fas fa-chevron-right"></i></a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';

const props = defineProps({
    riesgos: {
        type: Array,
        required: true,
        default: () => []
    }
});

const celdaSeleccionada = ref({ x: 0, y: 0 });
const riesgosListados = ref([]); // Changed name for clarity
const matrizFilter = ref('');

// Pagination variables
const currentPage = ref(1);
const itemsPerPage = 5;

// Computed properties for pagination
const totalPages = computed(() => Math.ceil(riesgosListados.value.length / itemsPerPage));
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage);
const endIndex = computed(() => Math.min(startIndex.value + itemsPerPage, riesgosListados.value.length));
const displayedRiesgos = computed(() => riesgosListados.value.slice(startIndex.value, endIndex.value));

const filteredRiesgos = computed(() => {
    if (!matrizFilter.value) return props.riesgos;
    return props.riesgos.filter(r => r.riesgo_matriz === matrizFilter.value);
});

const getRiesgosEnCelda = (impacto, probabilidad) => {
    return filteredRiesgos.value.filter(r => r.riesgo_impacto === impacto && r.riesgo_probabilidad === probabilidad);
};

// Update list when filter changes
watch(matrizFilter, () => {
    celdaSeleccionada.value = { x: 0, y: 0 };
    riesgosListados.value = filteredRiesgos.value; // Show all by default
    currentPage.value = 1;
});

// Update list when props change (initial load)
watch(() => props.riesgos, () => {
    riesgosListados.value = filteredRiesgos.value;
}, { immediate: true });

const setPage = (pageNum) => {
    if (pageNum >= 1 && pageNum <= totalPages.value) currentPage.value = pageNum;
};
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++; };
const prevPage = () => { if (currentPage.value > 1) currentPage.value--; };

const mostrarRiesgos = (x, y) => {
    // If clicking same cell, deselect and show all
    if (celdaSeleccionada.value.x === x && celdaSeleccionada.value.y === y) {
        celdaSeleccionada.value = { x: 0, y: 0 };
        riesgosListados.value = filteredRiesgos.value;
    } else {
        celdaSeleccionada.value = { x, y };
        riesgosListados.value = getRiesgosEnCelda(x, y);
    }
    currentPage.value = 1;
};

// Gradient Logic for UX
// Instead of flat colors, we use classes that map to refined CSS gradients
const getCellClass = (impacto, probabilidad) => {
    const valor = impacto * probabilidad;
    if (valor >= 80) return 'cell-critical';
    if (valor >= 48) return 'cell-high';
    if (valor >= 32) return 'cell-medium';
    return 'cell-low';
};

const getBadgeClass = (nivel) => {
    if (!nivel) return 'badge-light';
    const n = nivel.toLowerCase();
    if (n === 'muy alto') return 'badge-danger';
    if (n === 'alto') return 'badge-warning text-white'; // Orange sometimes better as warning in bootstrap
    if (n === 'medio') return 'badge-warning';
    return 'badge-success';
};

// Helper for title
const listTitle = computed(() => {
    if (celdaSeleccionada.value.x !== 0) {
        return `Riesgos en Celda (${celdaSeleccionada.value.x}, ${celdaSeleccionada.value.y})`;
    }
    if (matrizFilter.value === 'estrategica') return 'Riesgos Estratégicos';
    if (matrizFilter.value === 'tactica') return 'Riesgos Tácticos';
    return 'Todos los Riesgos';
});
</script>

<style scoped>
/* --- Legend Colors Matching Cells --- */
.risk-low {
    background: linear-gradient(135deg, #6bcf7f 0%, #27ae60 100%);
}
.risk-medium {
    background: linear-gradient(135deg, #feca57 0%, #ff9f43 100%);
}
.risk-high {
    background: linear-gradient(135deg, #ff9f43 0%, #ff6b6b 100%);
}
.risk-critical {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5253 100%);
}

.legend-dot {
    width: 12px;
    height: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* --- Layout & Grid --- */
.heatmap-wrapper {
    padding: 20px;
    display: inline-block;
}

.heatmap-container {
    background: #fff;
    border-radius: 16px;
    padding: 4px;
    overflow: hidden;
}

.heatmap-cell {
    border-radius: 4px;
    cursor: default;
    transition: all 0.2s ease-out;
    opacity: 0.9;
}

.heatmap-cell:hover {
    transform: scale(1.15);
    z-index: 10;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border-radius: 6px;
    opacity: 1;
}

/* --- Refined Color Palette (Vibrant yet Professional) --- */
.cell-critical {
    background: linear-gradient(145deg, #ff5e62, #ff9966);
    background: #ff5252;
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5253 100%);
}
.cell-high {
    background: linear-gradient(135deg, #ff9f43 0%, #ff6b6b 100%);
    background: #ff9f43;
}
.cell-medium {
    background: linear-gradient(135deg, #feca57 0%, #ff9f43 100%);
    background: #feca57;
}
.cell-low {
    background: linear-gradient(135deg, #6bcf7f 0%, #27ae60 100%);
    background: #27ae60;
}

.bg-orange { background-color: #ff9f43 !important; }

/* Scale Animation */
.risk-counter {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: white;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 800;
    color: #444;
    cursor: pointer;
    transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.risk-counter:hover {
    transform: scale(1.2);
    color: #000;
}
.risk-counter.active {
    background: #343a40;
    color: #fff;
    transform: scale(1.2);
}

/* Axis Labels & Numbers */
.axis-y-label {
    position: absolute;
    /* Center of element (width 100%) is at 50% of parent width.
       left: -50% moves that center to 0px (left edge of parent).
       margin-left: -50px adds the gap outside. */
    left: -50%;
    margin-left: -40px; 
    top: 50%;
    transform: translateY(-50%) rotate(-90deg);
    white-space: nowrap;
    text-align: center;
    width: 100%;
    pointer-events: none;
}
.axis-x-label {
    position: absolute;
    bottom: -50px;
    left: 50%;
    transform: translateX(-50%);
}
.axis-numbers-y {
    position: absolute;
    left: -25px;
    top: 0;
    bottom: 0;
    display: flex;
    flex-direction: column;
    justify-content: space-around;
    padding-top: 10px;
    padding-bottom: 10px;
}
.axis-numbers-x {
    position: absolute;
    bottom: -25px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: space-around;
    padding-left: 10px;
    padding-right: 10px;
}
.axis-num {
    font-size: 12px;
    color: #adb5bd;
    font-weight: 600;
}

/* Custom Pills */
.custom-pills .nav-link {
    border-radius: 50px;
    color: #6c757d;
    font-weight: 500;
    padding: 8px 24px;
    transition: all 0.2s;
}
.custom-pills .nav-link.active {
    background-color: #212529;
    color: #fff;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

/* Sidebar Styles */
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #dee2e6;
    border-radius: 10px;
}
.icon-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.hover-lift {
    transition: transform 0.2s, box-shadow 0.2s;
}
.hover-lift:hover {
    transform: translateY(-3px);
    box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .08) !important;
}

/* Transitions */
.scale-enter-active, .scale-leave-active {
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.scale-enter-from, .scale-leave-to {
    opacity: 0;
    transform: scale(0);
}
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
