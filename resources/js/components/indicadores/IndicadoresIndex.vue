<template>
    <div class="container-fluid py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link :to="{ name: 'home' }"
                        class="text-danger font-weight-bold">Inicio</router-link></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Indicadores de Gestión</li>
            </ol>
        </nav>
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line text-danger mr-2"></i> Listado de Indicadores
                        </h3>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-primary btn-sm" @click="openCreateModal">
                            <i class="fas fa-plus"></i> Nuevo Indicador
                        </button>
                    </div>
                </div>
                <hr>
                <form @submit.prevent="loadIndicadores">
                    <div class="form-row">
                        <div class="col">
                            <input type="text" class="form-control" v-model="filters.search"
                                placeholder="Buscar por proceso o descripción...">
                        </div>
                        <div class="col">
                            <select class="form-control" v-model="filters.year">
                                <option value="">Todos los Años</option>
                                <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                            </select>
                        </div>
                        <div class="col">
                            <select class="form-control" v-model="filters.indicador_sig">
                                <option value="">Todos los Sistemas</option>
                                <option v-for="sig in sigOptions" :key="sig" :value="sig">{{ sig }}</option>
                            </select>
                        </div>
                        <div class="col">
                            <select class="form-control" v-model="filters.mes">
                                <option value="">Todos los Meses</option>
                                <option v-for="(mes, index) in meses" :key="index" :value="index + 1">{{ mes }}</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn bg-dark">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="h-1 mb-2">
                    <ProgressBar v-if="loading" mode="indeterminate" style="height: 4px;" />
                </div>
                <DataTable :value="indicadores" :class="{ 'opacity-50 pointer-events-none': loading }" :paginator="true"
                    :rows="10" stripedRows
                    paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                    :rowsPerPageOptions="[10, 20, 50]" responsiveLayout="scroll"
                    currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords}">

                    <Column field="indicador_nombre" header="Nombre" :sortable="true"></Column>
                    <Column v-if="!procesoId" field="proceso.proceso_nombre" header="Proceso" :sortable="true"></Column>
                    <Column field="indicador_frecuencia" header="Frecuencia" :sortable="true"></Column>
                    <Column header="Periodo">
                        <template #body="slotProps">
                            {{ slotProps.data.ultimo_periodo ?? 'N/A' }}
                        </template>
                    </Column>
                    <Column header="Meta Anual / Meta (Periodo)">
                        <template #body="slotProps">
                            <span class="font-weight-bold">{{ slotProps.data.indicador_meta }}</span>
                            <span class="text-muted mx-1">/</span>
                            <span>{{ slotProps.data.ultima_meta ?? 'N/A' }}</span>
                        </template>
                    </Column>
                    <Column header="Valor (Periodo)">
                        <template #body="slotProps">
                            <span :class="getValorClass(slotProps.data)">
                                {{ slotProps.data.ultimo_valor ?? 'N/A' }}
                            </span>
                        </template>
                    </Column>
                    <Column header="Cumplimiento">
                        <template #body="slotProps">
                            <span v-if="slotProps.data.ultimo_valor && slotProps.data.ultima_meta"
                                :class="getComplianceBadge(slotProps.data).class">
                                {{ getComplianceBadge(slotProps.data).text }}
                            </span>
                            <span v-else class="text-muted">N/A</span>
                        </template>
                    </Column>
                    <Column header="Acciones" style="width: 150px">
                        <template #body="slotProps">
                            <a href="#" class="mr-3 d-inline-block" @click.prevent="editIndicador(slotProps.data)"
                                title="Editar">
                                <i class="fas fa-pencil-alt text-warning fa-lg"></i>
                            </a>
                            <a href="#" class="mr-3 d-inline-block" @click.prevent="openAvanceModal(slotProps.data)"
                                title="Registrar Avance">
                                <i class="fas fa-clipboard-list text-info fa-lg"></i>
                            </a>
                            <a href="#" class="mr-3 d-inline-block" @click.prevent="openGraficoModal(slotProps.data)"
                                title="Ver Gráfico">
                                <i class="fas fa-chart-line text-success fa-lg"></i>
                            </a>
                            <a href="#" class="mr-3 d-inline-block" @click.prevent="deleteIndicador(slotProps.data)"
                                title="Eliminar">
                                <i class="fas fa-trash-alt text-danger fa-lg"></i>
                            </a>
                        </template>
                    </Column>
                </DataTable>
            </div>

            <!-- Modales -->
            <IndicadorForm v-if="showFormModal" :visible="showFormModal" :indicador="selectedIndicador"
                :proceso-id="procesoId" :pei-options="peiOptions" :sig-plan-options="sigPlanOptions"
                @close="closeFormModal" @saved="loadIndicadores" />
            <IndicadorAvanceForm v-if="showAvanceModal" :visible="showAvanceModal" :indicador="selectedIndicador"
                @close="closeAvanceModal" @saved="loadIndicadores" />
            <IndicadorGrafico v-if="showGraficoModal" :visible="showGraficoModal" :indicador="selectedIndicador"
                @close="closeGraficoModal" />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ProgressBar from 'primevue/progressbar';
import Swal from 'sweetalert2';
import IndicadorForm from './IndicadorForm.vue';
import IndicadorAvanceForm from './IndicadorAvanceForm.vue';
import IndicadorGrafico from './IndicadorGrafico.vue';
import LoadingState from '@/components/generales/LoadingState.vue';

const props = defineProps({
    procesoId: {
        type: [Number, String],
        default: null
    }
});

const indicadores = ref([]);
const loading = ref(true);
const showFormModal = ref(false);
const showAvanceModal = ref(false);
const showGraficoModal = ref(false);
const selectedIndicador = ref(null);
const peiOptions = ref([]);
const sigPlanOptions = ref([]);

const filters = ref({
    year: new Date().getFullYear(),
    frecuencia: '',
    periodo: '',
    mes: '',
    year: new Date().getFullYear(),
    frecuencia: '',
    periodo: '',
    mes: '',
    search: '',
    indicador_sig: ''
});

const sigOptions = ['SGAS', 'SGCM', 'SGC', 'SGSI', 'SGCO'];

const meses = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
];

const years = computed(() => {
    const currentYear = new Date().getFullYear();
    const startYear = 2025;
    const yearsArr = [];
    const endYear = currentYear < 2025 ? 2025 : currentYear;
    for (let y = startYear; y <= endYear; y++) {
        yearsArr.push(y);
    }
    return yearsArr;
});

let debounceTimer = null;
const debounceLoad = () => {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        loadIndicadores();
    }, 500);
};

const loadIndicadores = async () => {
    loading.value = true;
    try {
        const params = { ...filters.value };
        if (props.procesoId) {
            params.proceso_id = props.procesoId;
        }
        const response = await axios.get('/api/indicadores-gestion', { params });
        indicadores.value = response.data.indicadores;
        peiOptions.value = response.data.planificaciones_pei;
        sigPlanOptions.value = response.data.planificaciones_sig;
    } catch (error) {
        console.error('Error cargando indicadores:', error);
        Swal.fire('Error', 'No se pudieron cargar los indicadores', 'error');
    } finally {
        loading.value = false;
    }
};

watch(() => props.procesoId, () => {
    loadIndicadores();
});

const openCreateModal = () => {
    selectedIndicador.value = null;
    showFormModal.value = true;
};

const editIndicador = (indicador) => {
    selectedIndicador.value = { ...indicador };
    showFormModal.value = true;
};

const deleteIndicador = (indicador) => {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esto",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.delete(`/api/indicadores-gestion/${indicador.id}`);
                Swal.fire('Eliminado', 'El indicador ha sido eliminado.', 'success');
                loadIndicadores();
            } catch (error) {
                Swal.fire('Error', 'No se pudo eliminar el indicador', 'error');
            }
        }
    });
};

const openAvanceModal = (indicador) => {
    selectedIndicador.value = indicador;
    showAvanceModal.value = true;
};

const openGraficoModal = (indicador) => {
    selectedIndicador.value = indicador;
    showGraficoModal.value = true;
};

const closeFormModal = () => {
    showFormModal.value = false;
    selectedIndicador.value = null;
};

const closeAvanceModal = () => {
    showAvanceModal.value = false;
    selectedIndicador.value = null;
};

const closeGraficoModal = () => {
    showGraficoModal.value = false;
    selectedIndicador.value = null;
};

const getValorClass = (indicador) => {
    if (!indicador.ultimo_valor) return '';
    return 'badge badge-info';
};

const getComplianceBadge = (indicador) => {
    const valor = parseFloat(indicador.ultimo_valor);
    const meta = parseFloat(indicador.ultima_meta);

    if (isNaN(valor) || isNaN(meta) || meta === 0) {
        return { class: 'badge badge-secondary', text: 'N/A' };
    }

    const porcentaje = (valor / meta) * 100;

    if (porcentaje <= 70) {
        return { class: 'badge badge-danger', text: porcentaje.toFixed(1) };
    } else if (porcentaje > 70 && porcentaje <= 95) {
        return { class: 'badge badge-warning', text: porcentaje.toFixed(1) };
    } else {
        return { class: 'badge badge-success', text: porcentaje.toFixed(1) };
    }
};

onMounted(() => {
    loadIndicadores();
});
</script>

<style scoped>
.badge {
    font-size: 0.75rem;
    /* Increased font size */
    padding: 0.5em 0.7em;
}

:deep(.p-datatable .p-datatable-tbody > tr > td) {
    padding: 0.65rem !important;
}

.opacity-50 {
    opacity: 0.5;
}

.pointer-events-none {
    pointer-events: none;
}
</style>
