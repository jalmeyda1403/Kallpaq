<template>
    <div class="container-fluid process-map-container">
        <!-- Header Section -->
        <div class="card shadow-sm border-0 mb-4 fade-in-down">
            <div class="card-header bg-white p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-project-diagram mr-2 text-primary"></i>Mapa de Procesos
                        </h4>
                        <p class="text-muted small mb-0">Visualización interactiva de la arquitectura de procesos</p>
                    </div>
                    <div style="width: 300px;">
                        <div class="input-group">
                            <input type="text" v-model="searchQuery" class="form-control"
                                placeholder="Buscar proceso..." @keyup.enter="searchProceso">
                            <div class="input-group-append">
                                <button @click="searchProceso" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="loading" class="text-center my-5 py-5">
            <ProgressSpinner strokeWidth="4" />
        </div>

        <div v-else class="process-diagram-wrapper fade-in-up">

            <div class="d-flex flex-row justify-content-center align-items-stretch" style="gap: 2rem;">

                <!-- Inputs Column -->
                <div class="d-flex flex-column" style="width: 100px;">
                    <div
                        class="input-output-card shadow-sm h-100 d-flex flex-column align-items-center justify-content-center">
                        <div class="vertical-text">Requisitos del Cliente</div>
                        <div class="connector-arrow mt-3">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                </div>

                <!-- Processes Main Column -->
                <div class="d-flex flex-column flex-grow-1" style="max-width: 1200px;">

                    <!-- Strategic Layer -->
                    <div class="process-layer strategic-layer mb-3">
                        <div class="layer-label">
                            <span class="badge badge-pill badge-primary-light">Estratégicos</span>
                        </div>
                        <div class="d-flex flex-wrap justify-content-center gap-3 pt-2">
                            <div v-for="proceso in procesosEstrategicos" :key="proceso.id"
                                class="process-card card-strategic hover-lift"
                                :class="{ 'is-dimmed': searchQuery && !matchSearch(proceso) }">
                                <div class="card-content">
                                    <div class="d-flex justify-content-between mb-2">
                                        <div class="process-icon">
                                            <i class="fas fa-chess-knight"></i>
                                        </div>
                                        <a :href="`/caracterizacion/${proceso.id}/mcar`" class="action-icon"
                                            title="Caracterización">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                    <h6 class="process-title">{{ proceso.proceso_nombre }}</h6>
                                </div>
                                <router-link
                                    :to="{ name: 'documentos.listado', query: { buscar_proceso: proceso.proceso_nombre } }"
                                    class="process-footer">
                                    <span class="code-badge">{{ proceso.cod_proceso }}</span>
                                    <i class="fas fa-arrow-right small"></i>
                                </router-link>
                            </div>
                        </div>
                    </div>

                    <!-- Flow Connector -->
                    <div class="flow-connector my-2">
                        <div class="flow-line"></div>
                        <div class="flow-icon"><i class="fas fa-chevron-down"></i></div>
                    </div>

                    <!-- Misional Layer -->
                    <div class="process-layer misional-layer mb-3">
                        <div class="layer-label">
                            <span class="badge badge-pill badge-success-light">Misionales</span>
                        </div>
                        <div class="d-flex flex-wrap justify-content-center gap-3 pt-2">
                            <div v-for="proceso in procesosMisionales" :key="proceso.id"
                                class="process-card card-misional hover-lift"
                                :class="{ 'is-dimmed': searchQuery && !matchSearch(proceso) }">
                                <div class="card-content">
                                    <div class="d-flex justify-content-between mb-2">
                                        <div class="process-icon">
                                            <i class="fas fa-cogs"></i>
                                        </div>
                                        <a :href="`/caracterizacion/${proceso.id}/mcar`" class="action-icon"
                                            title="Caracterización">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                    <h6 class="process-title">{{ proceso.proceso_nombre }}</h6>
                                </div>
                                <router-link
                                    :to="{ name: 'documentos.listado', query: { buscar_proceso: proceso.proceso_nombre } }"
                                    class="process-footer">
                                    <span class="code-badge">{{ proceso.cod_proceso }}</span>
                                    <i class="fas fa-arrow-right small"></i>
                                </router-link>
                            </div>
                        </div>
                    </div>

                    <!-- Flow Connector -->
                    <div class="flow-connector my-2">
                        <div class="flow-line"></div>
                        <div class="flow-icon"><i class="fas fa-chevron-up"></i></div>
                    </div>

                    <!-- Support Layer -->
                    <div class="process-layer support-layer">
                        <div class="layer-label">
                            <span class="badge badge-pill badge-warning-light">Apoyo o Soporte</span>
                        </div>
                        <div class="d-flex flex-wrap justify-content-center gap-3 pt-2">
                            <div v-for="proceso in procesosApoyo" :key="proceso.id"
                                class="process-card card-support hover-lift"
                                :class="{ 'is-dimmed': searchQuery && !matchSearch(proceso) }">
                                <div class="card-content">
                                    <div class="d-flex justify-content-between mb-2">
                                        <div class="process-icon">
                                            <i class="fas fa-hands-helping"></i>
                                        </div>
                                        <a :href="`/caracterizacion/${proceso.id}/mcar`" class="action-icon"
                                            title="Caracterización">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                    <h6 class="process-title">{{ proceso.proceso_nombre }}</h6>
                                </div>
                                <router-link
                                    :to="{ name: 'documentos.listado', query: { buscar_proceso: proceso.proceso_nombre } }"
                                    class="process-footer">
                                    <span class="code-badge">{{ proceso.cod_proceso }}</span>
                                    <i class="fas fa-arrow-right small"></i>
                                </router-link>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Outputs Column -->
                <div class="d-flex flex-column" style="width: 100px;">
                    <div
                        class="input-output-card shadow-sm h-100 d-flex flex-column align-items-center justify-content-center">
                        <div class="connector-arrow left mb-3">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                        <div class="vertical-text">Satisfacción del Cliente</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Section -->
        <div class="card border-0 shadow-sm mt-5 fade-in-up delay-1">
            <div class="card-header bg-white border-0 py-4">
                <h4 class="card-title font-weight-bold text-dark">Inventario Documental</h4>
            </div>
            <div class="card-body p-0">
                <DataTable :value="inventarios" :paginator="true" :rows="10" tableStyle="min-width: 50rem"
                    class="p-datatable-modern">
                    <Column field="nombre" header="Nombre" sortable></Column>
                    <Column field="documento_aprueba" header="Resolución" sortable></Column>
                    <Column field="vigencia" header="Vigencia" sortable>
                        <template #body="slotProps">
                            <span class="badge bg-light text-dark border">{{ formatDate(slotProps.data.vigencia)
                            }}</span>
                        </template>
                    </Column>
                    <Column header="Acción" class="text-center">
                        <template #body="slotProps">
                            <button @click.prevent="openPdf(slotProps.data.enlace)"
                                class="btn btn-sm btn-outline-danger shadow-sm rounded-pill px-3">
                                <i class="fas fa-file-pdf mr-1"></i> Ver PDF
                            </button>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
        <PdfModal ref="pdfModal" />
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import PdfModal from '@/components/generales/PdfModal.vue';
import ProgressSpinner from 'primevue/progressspinner';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const loading = ref(true);
const procesos = ref([]);
const inventarios = ref([]);
const searchQuery = ref('');
const pdfModal = ref(null);
const router = useRouter();

const procesosEstrategicos = computed(() => procesos.value.filter(p => p.proceso_tipo === 'Estratégico'));
const procesosMisionales = computed(() => procesos.value.filter(p => p.proceso_tipo === 'Misional'));
const procesosApoyo = computed(() => procesos.value.filter(p => p.proceso_tipo === 'Apoyo'));

const fetchData = async () => {
    try {
        const response = await axios.get('/api/procesos/mapa');
        procesos.value = response.data.procesos;
        inventarios.value = response.data.inventarios;
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
};

const searchProceso = () => {
    if (searchQuery.value) {
        router.push({ name: 'documentos.listado', query: { buscar_proceso: searchQuery.value } });
    }
};

const matchSearch = (proceso) => {
    if (!searchQuery.value) return true;
    const term = searchQuery.value.toLowerCase();
    return proceso.proceso_nombre.toLowerCase().includes(term) ||
        proceso.cod_proceso.toLowerCase().includes(term);
};


const openPdf = (url) => {
    document.dispatchEvent(new CustomEvent('open-pdf-modal', { detail: url }));
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('es-PE');
};

onMounted(() => {
    fetchData();
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

.process-map-container {
    font-family: 'Inter', sans-serif;
    color: #343a40;
    padding-bottom: 3rem;
    background-color: #f8f9fa;
    min-height: 100vh;
}

/* Animations */
.fade-in-down {
    animation: fadeInDown 0.6s ease-out;
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

.delay-1 {
    animation-delay: 0.2s;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Side Columns */
.input-output-card {
    background: #2c3e50;
    color: white;
    border-radius: 12px;
    padding: 1rem;
    width: 100%;
    position: relative;
    overflow: hidden;
}

.vertical-text {
    writing-mode: vertical-rl;
    text-orientation: mixed;
    transform: rotate(180deg);
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
    font-size: 0.9rem;
    white-space: nowrap;
}

.connector-arrow {
    background: rgba(255, 255, 255, 0.1);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Process Layers */
.process-layer {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    position: relative;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.layer-label {
    position: absolute;
    top: -16px;
    /* Adjusted for larger badge */
    left: 20px;
    z-index: 2;
}

.layer-label .badge {
    text-transform: uppercase;
    font-size: 0.9rem;
    font-weight: 700;
    padding: 0.6em 1.2em;
    letter-spacing: 1px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.badge-primary-light {
    background: #e3f2fd;
    color: #1565c0;
}

.badge-success-light {
    background: #e8f5e9;
    color: #2e7d32;
}

.badge-warning-light {
    background: #fff8e1;
    color: #ef6c00;
}

/* Process Cards */
.process-card {
    border-radius: 12px;
    border: 0;
    display: flex;
    flex-direction: column;
    min-height: 120px;
    width: 160px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.card-strategic {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
    box-shadow: 0 4px 10px rgba(52, 152, 219, 0.3);
}

.card-misional {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    color: white;
    box-shadow: 0 4px 10px rgba(46, 204, 113, 0.3);
}

.card-support {
    background: linear-gradient(135deg, #e6a54b 0%, #dc8119 100%);
    color: white;
    box-shadow: 0 4px 10px rgba(255, 224, 178, 0.4);
}

.card-support .process-title,
.card-support .process-icon,
.card-support .process-footer,
.card-support .action-icon {
    color: white !important;
    text-shadow: none;
}

.card-support .process-footer {
    border-top: 1px solid rgba(93, 64, 55, 0.1);
}

.card-support .action-icon {
    opacity: 0.7;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.is-dimmed {
    opacity: 0.3;
    filter: grayscale(100%);
    pointer-events: none;
}

.card-content {
    padding: 1rem;
    flex: 1;
}

.process-icon {
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
    opacity: 0.9;
    color: white !important;
}

.action-icon {
    color: rgba(255, 255, 255, 0.7);
    transition: color 0.2s;
}

.action-icon:hover {
    color: white;
}

.process-title {
    font-size: 0.7rem;
    font-weight: 600;
    color: white;
    line-height: 1.3;
    margin: 0;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.process-footer {
    background: rgba(0, 0, 0, 0.1);
    padding: 0.5rem 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    text-decoration: none;
    color: rgba(255, 255, 255, 0.9);
    transition: background 0.2s;
}

.process-card:hover .process-footer {
    background: rgba(0, 0, 0, 0.2);
    color: white;
}

.code-badge {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.5px;
}

/* Flow Connectors */
.flow-connector {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 40px;
    position: relative;
    color: #cbd5e0;
}

.flow-line {
    position: absolute;
    height: 100%;
    width: 2px;
    background: #edf2f7;
    z-index: 0;
}

.flow-icon {
    background: #f8f9fa;
    padding: 5px;
    z-index: 1;
    border-radius: 50%;
    border: 1px solid #edf2f7;
    font-size: 0.8rem;
}

/* Helpers */
.badge-pill {
    padding-right: 0.8em;
    padding-left: 0.8em;
}

.gap-3 {
    gap: 1rem;
}
</style>
