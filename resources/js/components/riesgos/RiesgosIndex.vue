<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Bandeja de Riesgos (Admin)</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="riesgosTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="listado-tab" data-toggle="pill" href="#listado" role="tab" aria-controls="listado" aria-selected="true">
                                <i class="fas fa-list mr-2"></i>Listado General
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="mapa-tab" data-toggle="pill" href="#mapa" role="tab" aria-controls="mapa" aria-selected="false">
                                <i class="fas fa-th mr-2"></i>Mapa de Calor
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="riesgosTabsContent">
                        <div class="tab-pane fade show active" id="listado" role="tabpanel" aria-labelledby="listado-tab">
                            <DataTable :value="riesgos" :paginator="true" :rows="10" :loading="loading"
                                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                                :rowsPerPageOptions="[10, 25, 50]"
                                currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} riesgos">
                                
                                <Column field="riesgo_cod" header="Código" sortable></Column>
                                <Column field="nombre" header="Nombre" sortable></Column>
                                <Column field="proceso.proceso_nombre" header="Proceso" sortable></Column>
                                <Column field="riesgo_valoracion" header="Nivel" sortable>
                                    <template #body="{ data }">
                                        <span :class="['badge', getBadgeClass(data.riesgo_valoracion)]">{{ data.riesgo_valoracion }}</span>
                                    </template>
                                </Column>
                                <Column field="estado" header="Estado" sortable></Column>
                                <Column header="Acciones">
                                    <template #body="{ data }">
                                        <button class="btn btn-sm btn-info" @click="verDetalle(data)">
                                            <i class="fas fa-eye"></i> Ver
                                        </button>
                                    </template>
                                </Column>
                            </DataTable>
                        </div>
                        <div class="tab-pane fade" id="mapa" role="tabpanel" aria-labelledby="mapa-tab">
                            <MapaCalor :riesgos="riesgos" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalle -->
    <div class="modal fade" id="riesgoModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gestión de Riesgo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <RiesgoDetalle v-if="riesgoSeleccionado" :riesgoId="riesgoSeleccionado.id" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRiesgoStore } from '../../stores/riesgoStore';
import RiesgoDetalle from './RiesgoDetalle.vue';
import MapaCalor from './MapaCalor.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const store = useRiesgoStore();
const riesgos = computed(() => store.riesgos);
const loading = computed(() => store.loading);
const riesgoSeleccionado = ref(null);

onMounted(() => {
    // Para admin, usamos la ruta general o mis-riesgos que devuelve todo si es admin
    store.fetchMisRiesgos(); 
});

const getBadgeClass = (valoracion) => {
    if (valoracion === 'Muy Alto') return 'badge-danger';
    if (valoracion === 'Alto') return 'badge-warning';
    if (valoracion === 'Medio') return 'badge-info';
    return 'badge-success';
};

const verDetalle = (riesgo) => {
    riesgoSeleccionado.value = riesgo;
    $('#riesgoModal').modal('show');
};
</script>

<style>
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
