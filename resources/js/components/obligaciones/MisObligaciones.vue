<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light py-2 px-3 rounded">
                <li class="breadcrumb-item"><router-link to="/home">Inicio</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Mis Obligaciones</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title mb-0">Mis Obligaciones Asignadas</h3>
                    </div>
                </div>
                <hr>
                <form @submit.prevent="obligacionStore.fetchMisObligaciones">
                    <div class="form-row">
                        <div class="col">
                            <input type="text" v-model="obligacionStore.filters.documento" class="form-control"
                                placeholder="Buscar por nombre documento">
                        </div>
                        <div class="col">
                            <input type="text" v-model="obligacionStore.filters.proceso" class="form-control"
                                placeholder="Buscar por Proceso">
                        </div>
                        <div class="col">
                            <select v-model="obligacionStore.filters.fuente" class="form-control">
                                <option value="">Buscar por fuente</option>
                                <option value="interno">Fuente Interna</option>
                                <option value="externo">Fuente Externa</option>
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
                <DataTable ref="dt" :value="obligacionStore.obligaciones" v-model:filters="filters" paginator :rows="10"
                    :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id" filterDisplay="menu"
                    :globalFilterFields="['proceso.proceso_nombre', 'documento_tecnico_normativo', 'obligacion_principal', 'consecuencia_incumplimiento', 'estado_obligacion']"
                    :loading="obligacionStore.loading">
                    <template #header>
                        <div class="d-flex align-items-center">
                            <Button type="button" icon="pi pi-download" label="Descargar CSV" severity="secondary"
                                @click="exportCSV($event)" class="btn btn-secondary ml-auto">
                            </Button>
                        </div>
                    </template>
                    <Column field="id" header="Item" style="width:5%">
                        <template #body="{ data, index }">
                            {{ index + 1 }}
                        </template>
                    </Column>
                    <Column field="proceso.proceso_nombre" header="Proceso" sortable style="width:25%">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="text" @input="filterCallback()"
                                class="p-column-filter" placeholder="Buscar por Proceso" />
                        </template>
                    </Column>
                    <Column field="documento.nombre_documento" header="Documento" style="width:20%">
                        <template #body="{ data }">
                            <span v-if="data.documento">{{ data.documento.nombre_documento }}</span>
                            <span v-else>{{ data.documento_tecnico_normativo }}</span>
                        </template>
                    </Column>
                    <Column field="obligacion_principal" header="Obligación Principal" style="width:25%">
                    </Column>
                    <Column field="consecuencia_incumplimiento" header="Consecuencia del Incumplimiento"
                        style="width:20%">
                    </Column>
                    <Column field="estado_obligacion" header="Estado" style="width:10%">
                        <template #body="{ data }">
                            <span :class="['badge', getEstadoClass(data.estado_obligacion)]">
                                {{ ucfirst(data.estado_obligacion) }}
                            </span>
                        </template>
                    </Column>
                    <Column header="Acciones" :exportable="false" style="width:10%">
                        <template #body="{ data }">
                            <a href="#" title="Ver Riesgos" class="btn btn-danger btn-sm mr-1"
                                @click.prevent="obligacionStore.openRiesgosModal(data.id)">
                                <i class="fas fa-exclamation-triangle"></i>
                            </a>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar los riesgos de la obligacion -->
    <Dialog v-model:visible="obligacionStore.showRiesgosModal" :style="{ width: '750px' }" header="Riesgos Asociados"
        :modal="true" class="p-fluid">
        <DataTable :value="obligacionStore.riesgos" responsiveLayout="scroll">
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
            <Button label="Cerrar" icon="pi pi-times" class="p-button-text"
                @click="obligacionStore.closeRiesgosModal()" />
        </template>
    </Dialog>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useObligacionStore } from '@/stores/obligacionStore';
import { FilterMatchMode } from 'primevue/api';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';

const router = useRouter();
const obligacionStore = useObligacionStore();
const dt = ref(null);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    'proceso.proceso_nombre': { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    documento_tecnico_normativo: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    obligacion_principal: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    consecuencia_incumplimiento: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
    estado_obligacion: { value: null, matchMode: FilterMatchMode.STARTS_WITH },
});

const getEstadoClass = (estado) => {
    switch (estado) {
        case 'pendiente': return 'bg-secondary';
        case 'mitigada': return 'bg-warning';
        case 'controlada': return 'bg-primary';
        case 'inactiva':
        case 'suspendida': return 'bg-dark';
        case 'vencida': return 'bg-danger';
        default: return '';
    }
};


const ucfirst = (string) => {
    if (!string) return '';
    return string.charAt(0).toUpperCase() + string.slice(1);
};

const exportCSV = () => {
    dt.value.exportCSV();
};

onMounted(() => {
    obligacionStore.fetchMisObligaciones();
});
</script>

<style scoped>
.selected {
    background-color: #ECECEC;
}

.table-obligaciones {
    font-size: 12px;
}

.table-riesgos {
    font-size: 12px;
}

.valoracion-circle {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 5px;
}

.badge-success {
    background-color: #28a745;
}

.badge-warning {
    background-color: #ffc107;
}

.badge-danger {
    background-color: #dc3545;
}

.badge-info {
    background-color: #17a2b8;
}

.badge-primary {
    background-color: #007bff;
}

.badge-secondary {
    background-color: #6c757d;
}

.badge-dark {
    background-color: #343a40;
}

/* Custom loader styles - remove opacity and change color to red */
/* Remove the semi-transparent overlay that dims the table content during loading */
.p-datatable-loading-overlay {
    background: rgba(255, 255, 255, 0) !important;
    /* Make background completely transparent */
}

/* Change the loader icon to red */
.p-datatable-loading-icon {
    color: red !important;
    font-size: 2rem !important;
}
</style>
