<template>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title mb-0">Mapa de Procesos Vigente</h3>
                    </div>
                    <div class="input-group">
                        <input type="text" v-model="searchQuery" class="form-control me-2" placeholder="Buscar Proceso" @keyup.enter="searchProceso">
                        <button @click="searchProceso" class="btn bg-black">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div v-if="loading" class="text-center my-5">
                    <ProgressSpinner />
                </div>
                <div v-else class="row d-flex align-items-stretch">
                    <div class="col-md-1">
                        <div class="card h-100 d-flex flex-column">
                            <div class="card-body clientes">Requisitos del Cliente <p> <i class="fa fa-arrow-circle-left fa-2x" aria-hidden="true"></i></p></div>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <!-- Procesos Estratégicos -->
                        <div class="card">
                            <div class="card-header bg-estrategico">
                                <h6>Procesos Estratégicos</h6>
                            </div>
                            <div class="card-body card-procesos">
                                <div class="row justify-content-center">
                                    <div v-for="proceso in procesosEstrategicos" :key="proceso.id" class="col-lg-2 col-md-2 col-sm-4 col-6">
                                        <div class="small-box bg-estrategico">
                                            <div class="inner text-white d-flex flex-column" style="min-height: 100px;">
                                                <div>{{ proceso.proceso_nombre }}</div>
                                                <div class="mt-auto">
                                                    <a :href="`/caracterizacion/${proceso.id}/mcar`" data-toggle="tooltip" data-placement="bottom" title="Matriz de caracterización">
                                                        <i class="fas fa-network-wired fa-lg text-white"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="small-box-footer">
                                                <a :href="`/documento/buscar?buscar_proceso=${encodeURIComponent(proceso.proceso_nombre)}`" class="text-white">
                                                    {{ proceso.cod_proceso }}<i class="fas fa-arrow-circle-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <div class="circle-arrow">
                                <i class="fa fa-arrow-circle-down fa-2x" aria-hidden="true"></i>
                            </div>
                        </div>

                        <!-- Procesos Misionales -->
                        <div class="card">
                            <div class="card-header bg-misional">
                                <h6>Procesos Misionales</h6>
                            </div>
                            <div class="card-body card-procesos">
                                <div class="row justify-content-center">
                                    <div v-for="proceso in procesosMisionales" :key="proceso.id" class="col-lg-2 col-md-2 col-sm-4 col-6">
                                        <div class="small-box bg-misional">
                                            <div class="inner text-white d-flex flex-column" style="min-height: 100px;">
                                                <div>{{ proceso.proceso_nombre }}</div>
                                                <div class="mt-auto">
                                                    <a :href="`/caracterizacion/${proceso.id}/mcar`" data-toggle="tooltip" data-placement="bottom" title="Matriz de caracterización">
                                                        <i class="fas fa-network-wired fa-lg text-white"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="small-box-footer">
                                                <a :href="`/documento/buscar?buscar_proceso=${encodeURIComponent(proceso.proceso_nombre)}`" class="text-white">
                                                    {{ proceso.cod_proceso }}<i class="fas fa-arrow-circle-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <div class="circle-arrow">
                                <i class="fa fa-arrow-circle-up fa-2x" aria-hidden="true"></i>
                            </div>
                        </div>

                        <!-- Procesos de Apoyo -->
                        <div class="card">
                            <div class="card-header bg-apoyo">
                                <h6>Procesos de Apoyo</h6>
                            </div>
                            <div class="card-body card-procesos">
                                <div class="row justify-content-center">
                                    <div v-for="proceso in procesosApoyo" :key="proceso.id" class="col-lg-2 col-md-2 col-sm-4 col-6" style="top: 10px;margin-top: 10px;">
                                        <div class="small-box bg-apoyo">
                                            <div class="inner text-white d-flex flex-column" style="min-height: 100px;">
                                                <div>{{ proceso.proceso_nombre }}</div>
                                                <div class="mt-auto">
                                                    <a :href="`/caracterizacion/${proceso.id}/mcar`" data-toggle="tooltip" data-placement="bottom" title="Matriz de caracterización">
                                                        <i class="fas fa-network-wired fa-lg text-white"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="small-box-footer">
                                                <a :href="`/documento/buscar?buscar_proceso=${encodeURIComponent(proceso.proceso_nombre)}`" class="text-white">
                                                    {{ proceso.cod_proceso }}<i class="fas fa-arrow-circle-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="card h-100 d-flex flex-column">
                            <div class="card-body productos"><i class="fa fa-arrow-circle-left fa-2x" aria-hidden="true"></i>
                                <p>Productos de la CGR</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Inventario de Procesos</h4>
            </div>
            <div class="card-body">
                <DataTable :value="inventarios" :paginator="true" :rows="10" tableStyle="min-width: 50rem" class="p-datatable-sm">
                    <Column field="nombre" header="Nombre"></Column>
                    <Column field="documento_aprueba" header="Documento Aprueba"></Column>
                    <Column field="vigencia" header="Vigencia">
                        <template #body="slotProps">
                            {{ formatDate(slotProps.data.vigencia) }}
                        </template>
                    </Column>
                    <Column header="Enlace" class="text-center">
                        <template #body="slotProps">
                            <a href="#" @click.prevent="openPdf(slotProps.data.enlace)">
                                <i class="fas fa-file-pdf fa-lg" style="color:red;"></i>
                            </a>
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
.bg-estrategico { background-color: #007bff !important; color: white; }
.bg-misional { background-color: #28a745 !important; color: white; }
.bg-apoyo { background-color: #ffc107 !important; color: white; }
.clientes, .productos {
    writing-mode: vertical-rl;
    text-orientation: mixed;
    text-align: center;
    font-weight: bold;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.small-box {
    border-radius: 0.25rem;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    display: block;
    margin-bottom: 20px;
    position: relative;
}
.small-box > .inner { padding: 10px; }
.small-box .icon {
    color: rgba(0,0,0,.15);
    z-index: 0;
}
.small-box > .small-box-footer {
    background-color: rgba(0,0,0,.1);
    color: rgba(255,255,255,.8);
    display: block;
    padding: 3px 0;
    position: relative;
    text-align: center;
    text-decoration: none;
    z-index: 10;
}
.circle-arrow { color: #6c757d; }
</style>
