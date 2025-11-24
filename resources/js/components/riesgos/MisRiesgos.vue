<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Mis Riesgos Asignados</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Riesgos de mis Procesos</h3>
                </div>
                <div class="card-body">
                    <DataTable :value="riesgos" :paginator="true" :rows="10" :loading="loading"
                        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                        :rowsPerPageOptions="[10, 25, 50]"
                        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} riesgos">
                        
                        <Column field="riesgo_cod" header="Código" sortable></Column>
                        <Column field="nombre" header="Nombre" sortable></Column>
                        <Column field="proceso.proceso_nombre" header="Proceso" sortable></Column>
                        <Column field="riesgo_valoracion" header="Nivel Inicial" sortable>
                            <template #body="{ data }">
                                <span :class="['badge', getBadgeClass(data.riesgo_valoracion)]">{{ data.riesgo_valoracion }}</span>
                            </template>
                        </Column>
                        <Column field="estado_riesgo_rr" header="Eficacia" sortable>
                             <template #body="{ data }">
                                <span v-if="data.estado_riesgo_rr" :class="data.estado_riesgo_rr === 'Con Eficacia' ? 'badge badge-success' : 'badge badge-danger'">
                                    {{ data.estado_riesgo_rr }}
                                </span>
                                <span v-else class="text-muted">Pendiente</span>
                            </template>
                        </Column>
                        <Column header="Gestión">
                            <template #body="{ data }">
                                <button class="btn btn-sm btn-primary" @click="verDetalle(data)">
                                    <i class="fas fa-tasks"></i> Gestionar
                                </button>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalle -->
    <div class="modal fade" id="misRiesgosModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Gestión de Riesgo</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
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
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const store = useRiesgoStore();
const riesgos = computed(() => store.riesgos);
const loading = computed(() => store.loading);
const riesgoSeleccionado = ref(null);

onMounted(() => {
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
    $('#misRiesgosModal').modal('show');
};
</script>
