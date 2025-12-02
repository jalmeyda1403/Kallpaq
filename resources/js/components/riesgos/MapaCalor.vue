<template>
    <div class="card-body p-0">
        <div class="container-fluid">
            <div class="row">
                <!-- Heat Map Section -->
                <div class="col-md-8 p-4">
                    <!-- Filter Control -->
                    <div class="d-flex justify-content-center mb-3">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-secondary" :class="{ active: matrizFilter === '' }">
                                <input type="radio" name="options" id="option1" autocomplete="off"
                                    @click="matrizFilter = ''" :checked="matrizFilter === ''"> Todas
                            </label>
                            <label class="btn btn-outline-secondary"
                                :class="{ active: matrizFilter === 'estrategica' }">
                                <input type="radio" name="options" id="option2" autocomplete="off"
                                    @click="matrizFilter = 'estrategica'"
                                    :checked="matrizFilter === 'Estratégestrategicaica'">
                                Estratégica
                            </label>
                            <label class="btn btn-outline-secondary" :class="{ active: matrizFilter === 'tactica' }">
                                <input type="radio" name="options" id="option3" autocomplete="off"
                                    @click="matrizFilter = 'tactica'" :checked="matrizFilter === 'tactica'"> Táctica
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center">
                        <div class="position-relative" style="width: 600px; height: 600px;">
                            <!-- Eje Y Label -->
                            <div class="position-absolute"
                                style="left: -40px; top: 50%; transform: rotate(-90deg) translateX(50%); font-weight: bold;">
                                Probabilidad
                            </div>

                            <!-- Eje X Label -->
                            <div class="position-absolute"
                                style="bottom: -30px; left: 50%; transform: translateX(-50%); font-weight: bold;">
                                Impacto
                            </div>

                            <!-- Matriz -->
                            <div class="d-flex flex-column h-100 w-100 border">
                                <div v-for="y in 10" :key="'row-' + (11 - y)" class="d-flex flex-grow-1">
                                    <div v-for="x in 10" :key="'cell-' + x + '-' + (11 - y)"
                                        class="flex-grow-1 border-right border-bottom position-relative cell-hover"
                                        :class="getCellClass(x, 11 - y)" :title="`Imp: ${x}, Prob: ${11 - y}`">

                                        <!-- Riesgos en esta celda -->
                                        <div v-if="getRiesgosEnCelda(x, 11 - y).length > 0"
                                            class="d-flex justify-content-center align-items-center h-100 w-100">
                                            <span class="badge badge-light border shadow-sm rounded-circle p-2"
                                                style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: pointer;"
                                                @click="mostrarRiesgos(x, 11 - y)">
                                                {{ getRiesgosEnCelda(x, 11 - y).length }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Escalas Eje Y -->
                            <div class="position-absolute h-100 d-flex flex-column justify-content-between py-3"
                                style="left: -20px; top: 0;">
                                <span v-for="n in 10" :key="'y-' + (11 - n)" class="small">{{ 11 - n }}</span>
                            </div>

                            <!-- Escalas Eje X -->
                            <div class="position-absolute w-100 d-flex justify-content-between px-3"
                                style="bottom: -20px; left: 0;">
                                <span v-for="n in 10" :key="'x-' + n" class="small">{{ n }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Leyenda -->
                    <div class="mt-4 d-flex justify-content-center">
                        <div class="mr-3"><span class="badge badge-success mr-1">&nbsp;&nbsp;</span> Bajo</div>
                        <div class="mr-3"><span class="badge badge-warning mr-1">&nbsp;&nbsp;</span> Medio</div>
                        <div class="mr-3"><span class="badge badge-orange mr-1">&nbsp;&nbsp;</span> Alto</div>
                        <div><span class="badge badge-danger mr-1">&nbsp;&nbsp;</span> Muy Alto</div>
                    </div>
                </div>

                <!-- Riesgos List Section -->
                <div class="col-md-4 border-left">
                    <div v-if="riesgosCelda.length === 0"
                        class="d-flex align-items-center justify-content-center h-100">
                        <div class="text-center">
                            <i class="fas fa-mouse-pointer text-muted fa-3x mb-3"></i>
                            <h5 class="text-muted">Haz clic en un círculo</h5>
                            <p class="text-muted">Haz clic en un círculo numerado en el mapa para ver los riesgos
                                asociados</p>
                        </div>
                    </div>
                    <div v-else class="p-3">
                        <h6 class="font-weight-bold mb-3">
                            <i class="fas fa-list mr-2"></i>Riesgos (Impacto: {{ celdaSeleccionada.x }}, Probabilidad:
                            {{ celdaSeleccionada.y }})
                        </h6>
                        <div class="list-group list-group-flush">
                            <div v-for="riesgo in displayedRiesgos" :key="riesgo.id"
                                class="list-group-item list-group-item-action border-0 py-1">
                                <div class="d-flex w-100 justify-content-between flex-column">
                                    <div class="d-flex w-100 justify-content-between">
                                        <div>
                                            <small class="text-muted">{{ riesgo.riesgo_cod }}</small>
                                        </div>
                                        <div class="text-justify flex-fill pl-2">
                                            <span class="small">{{ riesgo.riesgo_nombre }}</span>
                                        </div>
                                    </div>
                                    <p class="mb-0 mt-1 small" style="font-size: 13px">
                                        <i class="fas fa-project-diagram text-muted mr-1"></i>
                                        {{ riesgo.proceso?.proceso_nombre || 'N/A' }}
                                    </p>
                                    <small class="text-muted" style="font-size: 0.8em;">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        Nivel: <span :class="getBadgeClass(riesgo.riesgo_nivel)">{{
                                            riesgo.riesgo_nivel }}</span>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination Controls -->
                        <nav v-if="riesgosCelda.length > 0" class="mt-3">
                            <ul class="pagination justify-content-center pagination-sm">
                                <li class="page-item" :class="{ 'disabled': currentPage === 1 }">
                                    <a class="page-link" href="#" @click.prevent="prevPage" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>

                                <li class="page-item" v-for="pageNum in totalPages" :key="pageNum"
                                    :class="{ 'active': pageNum === currentPage }">
                                    <a class="page-link" href="#" @click.prevent="setPage(pageNum)">{{ pageNum }}</a>
                                </li>

                                <li class="page-item" :class="{ 'disabled': currentPage === totalPages }">
                                    <a class="page-link" href="#" @click.prevent="nextPage" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="text-center text-muted small mt-1">
                                Mostrando {{ startIndex + 1 }} a {{ endIndex }} de {{ riesgosCelda.length }} riesgos
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    riesgos: {
        type: Array,
        required: true,
        default: () => []
    }
});

const celdaSeleccionada = ref({ x: 0, y: 0 });
const riesgosCelda = ref([]);
const showModal = ref(false);
const matrizFilter = ref('');

// Pagination variables
const currentPage = ref(1);
const itemsPerPage = 5;

// Computed properties for pagination
const totalPages = computed(() => {
    return Math.ceil(riesgosCelda.value.length / itemsPerPage);
});

const startIndex = computed(() => {
    return (currentPage.value - 1) * itemsPerPage;
});

const endIndex = computed(() => {
    return Math.min(startIndex.value + itemsPerPage, riesgosCelda.value.length);
});

const displayedRiesgos = computed(() => {
    return riesgosCelda.value.slice(startIndex.value, endIndex.value);
});

const filteredRiesgos = computed(() => {
    if (!matrizFilter.value) return props.riesgos;
    return props.riesgos.filter(r => r.riesgo_matriz === matrizFilter.value);
});

const getRiesgosEnCelda = (impacto, probabilidad) => {
    return filteredRiesgos.value.filter(r => r.riesgo_impacto === impacto && r.riesgo_probabilidad === probabilidad);
};

watch(matrizFilter, () => {
    if (celdaSeleccionada.value.x !== 0 && celdaSeleccionada.value.y !== 0) {
        riesgosCelda.value = getRiesgosEnCelda(celdaSeleccionada.value.x, celdaSeleccionada.value.y);
        currentPage.value = 1;
    }
});

const setPage = (pageNum) => {
    if (pageNum >= 1 && pageNum <= totalPages.value) {
        currentPage.value = pageNum;
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

const mostrarRiesgos = (x, y) => {
    celdaSeleccionada.value = { x, y };
    riesgosCelda.value = getRiesgosEnCelda(x, y);
    showModal.value = true;
    currentPage.value = 1; // Reset to first page when selecting new cell
};

const getCellClass = (impacto, probabilidad) => {
    const valor = impacto * probabilidad;
    if (valor >= 80) return 'bg-danger-light';
    if (valor >= 48) return 'bg-orange-light';
    if (valor >= 32) return 'bg-warning-light';
    return 'bg-success-light';
};

const getBadgeClass = (nivel) => {
    if (!nivel) return 'badge badge-secondary';
    const n = nivel.toLowerCase();
    if (n === 'muy alto') return 'badge badge-danger';
    if (n === 'alto') return 'badge badge-orange';
    if (n === 'medio') return 'badge badge-warning';
    return 'badge badge-success';
};
</script>

<style scoped>
.bg-danger-light {
    background-color: rgba(220, 53, 69, 0.2);
}

.bg-warning-light {
    background-color: rgba(255, 193, 7, 0.2);
}

.bg-info-light {
    background-color: rgba(23, 162, 184, 0.2);
}

.bg-success-light {
    background-color: rgba(40, 167, 69, 0.2);
}

.bg-orange-light {
    background-color: rgba(253, 126, 20, 0.2);
}

.cell-hover:hover {
    filter: brightness(0.95);
    cursor: default;
}

/* Badge colors */
.badge-orange {
    background-color: #fd7e14;
    color: white;
}
</style>
