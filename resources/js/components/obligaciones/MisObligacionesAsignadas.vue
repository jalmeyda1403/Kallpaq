<template>
    <div class="container-fluid py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link to="/home"
                        class="text-danger font-weight-bold">Inicio</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Mis Obligaciones Asignadas</li>
            </ol>
        </nav>

        <div class="card border-top-danger shadow-sm">
            <div class="card-header bg-white border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h5 class="card-title text-danger mb-0 font-weight-bold">
                            <i class="fas fa-user-check mr-2"></i>Mis Obligaciones Asignadas (Responsable)
                        </h5>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <form @submit.prevent="search">
                            <div class="form-row">
                                <div class="col">
                                    <input type="text" v-model="localFilters.documento" class="form-control"
                                        placeholder="Buscar por documento...">
                                </div>
                                <div class="col">
                                    <input type="text" v-model="localFilters.proceso" class="form-control"
                                        placeholder="Buscar por proceso...">
                                </div>
                                <div class="col">
                                    <select v-model="localFilters.fuente" class="form-control">
                                        <option value="">Todas las fuentes</option>
                                        <option value="interno">Fuente Interna</option>
                                        <option value="externo">Fuente Externa</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn bg-dark text-white shadow-sm">
                                        <i class="fas fa-search mr-1"></i> Buscar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="h-1 mb-2">
                    <ProgressBar v-if="loading" mode="indeterminate" style="height: 4px;" />
                </div>

                <div v-if="!loading" class="animate-fade-in">
                    <DataTable ref="dt" :value="obligaciones" :paginator="true" :rows="10"
                        :class="{ 'opacity-50 pointer-events-none': loading }" :rowsPerPageOptions="[5, 10, 20, 50]"
                        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} obligaciones"
                        responsiveLayout="scroll" class="p-datatable-sm table-hover" stripedRows>

                        <template #header>
                            <div class="d-flex align-items-center justify-content-end pb-2">
                                <Button type="button" icon="pi pi-download" label="Exportar" severity="secondary"
                                    @click="exportCSV($event)" class="p-button-outlined p-button-secondary p-button-sm">
                                </Button>
                            </div>
                        </template>

                        <Column field="id" header="#" sortable style="width:5%">
                            <template #body="{ index }">
                                <span class="text-muted small">{{ index + 1 }}</span>
                            </template>
                        </Column>
                        <Column field="procesos" header="Procesos" sortable style="width:20%">
                            <template #body="{ data }">
                                <div v-if="data.procesos && data.procesos.length" class="d-flex flex-wrap">
                                    <span v-for="p in data.procesos" :key="p.id"
                                        class="badge badge-light border text-danger mr-1 mb-1 small px-2 py-1">
                                        <i class="fas fa-network-wired mr-1 opacity-50"></i>{{ p.proceso_nombre }}
                                    </span>
                                </div>
                                <span v-else class="text-muted small">N/A</span>
                            </template>
                        </Column>
                        <Column field="obligacion_principal" header="Obligación Principal" sortable style="width:30%">
                            <template #body="{ data }">
                                <div class="d-flex flex-column">
                                    <span class="text-secondary small text-justify">{{ data.obligacion_principal
                                        }}</span>
                                    <template v-if="data.obligacion_documento">
                                        <small class="text-muted mt-1">
                                            <i class="fas fa-file-alt mr-1"></i>{{ data.obligacion_documento }}
                                        </small>
                                    </template>
                                </div>
                            </template>
                        </Column>

                        <Column header="Avance Acciones" style="width:15%" class="text-center">
                            <template #body="{ data }">
                                <span class="font-weight-bold text-dark">
                                    {{ data.acciones_completadas_count }} / {{ data.acciones_total_count }}
                                </span>
                                <div class="progress progress-xs mt-1" style="height: 4px;">
                                    <div class="progress-bar bg-success"
                                        :style="{ width: calculateProgress(data) + '%' }">
                                    </div>
                                </div>
                            </template>
                        </Column>

                        <Column field="obligacion_estado" header="Estado" sortable style="width:10%">
                            <template #body="{ data }">
                                <span :class="['badge p-2', getEstadoClass(data.obligacion_estado)]">
                                    {{ data.obligacion_estado }}
                                </span>
                            </template>
                        </Column>

                        <Column header="Acciones" :exportable="false" style="width:10%" class="text-center">
                            <template #body="{ data }">
                                <button type="button" class="btn btn-light text-danger btn-sm border shadow-xs"
                                    title="Ver Riesgos" @click.prevent="store.openRiesgosModal(data.id)">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </button>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar los riesgos de la obligacion -->
    <Dialog v-model:visible="store.showRiesgosModal" :style="{ width: '750px' }" header="Riesgos Asociados"
        :modal="true" class="p-fluid">
        <DataTable :value="store.riesgos" responsiveLayout="scroll">
            <Column field="codigo" header="Código"></Column>
            <Column field="riesgo_tipo" header="Tipo"></Column>
            <Column field="riesgo_nombre" header="Nombre Riesgo"></Column>
            <Column field="factor.nombre" header="Factor"></Column>
            <Column field="probabilidad" header="Probabilidad"></Column>
            <Column field="impacto" header="Impacto"></Column>
            <Column field="riesgo_valoracion" header="Valoración">
                <template #body="{ data }">
                    <span :class="['valoracion-circle', 'badge-' + data.semaforo]"></span>
                    {{ data.riesgo_valoracion }}
                </template>
            </Column>
        </DataTable>
        <template #footer>
            <Button label="Cerrar" icon="pi pi-times" class="p-button-text" @click="store.closeRiesgosModal()" />
        </template>
    </Dialog>

</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useObligacionStore } from '@/stores/obligacionStore';
import ProgressBar from 'primevue/progressbar';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';

const store = useObligacionStore();
const obligaciones = computed(() => store.obligaciones);
const loading = computed(() => store.loading);
const dt = ref(null);

const localFilters = ref({
    documento: '',
    proceso: '',
    fuente: ''
});

const calculateProgress = (data) => {
    const total = data.acciones_total_count || 0;
    const completed = data.acciones_completadas_count || 0;
    if (total === 0) return 0;
    return Math.round((completed / total) * 100);
};

const search = () => {
    store.filters = { ...localFilters.value };
    store.fetchMisObligaciones({ scope: 'specialist' });
};

onMounted(() => {
    store.fetchMisObligaciones({ scope: 'specialist' });
});

const exportCSV = () => {
    dt.value.exportCSV();
};

const getEstadoClass = (estado) => {
    switch (estado) {
        case 'pendiente': return 'bg-secondary';
        case 'mitigada': return 'bg-warning';
        case 'controlada': return 'bg-success';
        case 'inactiva':
        case 'suspendida': return 'bg-dark';
        case 'vencida': return 'bg-danger';
        default: return 'bg-light';
    }
};
</script>

<style scoped>
.border-top-danger {
    border-top: 3px solid #dc3545 !important;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.valoracion-circle {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 5px;
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(5px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
