<template>
    <div>
        <div class="header-container">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">{{ hallazgoStore.modalTitle }}</span>
                <span class="mx-2 text-secondary">
                    <i class="fas fa-chevron-right fa-xs"></i>
                </span>
                <span class="text-dark">{{ hallazgoStore.hallazgoForm.hallazgo_cod }}</span>
            </h6>
        </div>
        <h6 class="mb-1" style="font-weight: bold;">
            ASOCIACIÓN DE PROCESOS
        </h6>
        <p class="small text-muted">
            Este módulo permite registrar y visualizar las relaciones entre los hallazgos y los procesos para asegurar
            una mejor
            trazabilidad y gestión.
        </p>
        <div class="d-flex align-items-center my-4">
            <div class="input-group mr-3">
                <input type="hidden" v-model="selectedProceso.id" />
                <input type="text" class="form-control" placeholder="Seleccione el Proceso a Asociar"
                    v-model="selectedProceso.descripcion" readonly />
                <div class="input-group-append">
                    <button type="button" class="btn btn-dark" @click="openProcesoModal">
                        <i class="fas fa-search"></i>
                    </button>
                    <button type="button" class="btn btn-danger" :disabled="!selectedProceso.id"
                        @click="handleAssociate">
                        <i class="fas fa-link"></i> Asociar
                    </button>
                </div>
            </div>
        </div>

        <div class="form-overlay-container">
            <div v-if="hallazgoStore.loadingProcesos" class="loading-overlay">
                <div class="spinner-border text-danger" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>

            <h6 class="mb-3 font-weight-bold">Lista de procesos asociados</h6>
            <div v-if="!hallazgoStore.associatedProcesos.length && !hallazgoStore.loadingProcesos"
                class="text-muted small">
                Este Hallazgo aún no tiene procesos asociados.
            </div>
            <div v-else class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Id</th>
                            <th>Código del Proceso</th>
                            <th>Nombre del Proceso</th>
                            <th class="text-center text-nowrap">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="proceso in hallazgoStore.associatedProcesos" :key="proceso.id">
                            <td>{{ proceso.id }}</td>
                            <td>{{ proceso.cod_proceso }}</td>
                            <td>{{ proceso.proceso_nombre }}</td>
                            <td class="text-center text-nowrap">
                                <button class="btn btn-danger btn-sm"
                                    @click="hallazgoStore.disociarProceso(proceso.id)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <modal-hijo ref="procesoModal" :fetch-url="proceso_route" target-id="id" target-desc="proceso_nombre"
            @update-target="handleProcesoSelection" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useHallazgoStore } from '@/stores/hallazgoStore';
import { route } from 'ziggy-js';
import ModalHijo from '../generales/ModalHijo.vue';

const hallazgoStore = useHallazgoStore();
const selectedProceso = ref({ id: null, descripcion: '' });
const procesoModal = ref(null); // Re-added ref
const proceso_route = route('procesos.buscar');

const openProcesoModal = () => {
    procesoModal.value.open();
};

const handleProcesoSelection = ({ idValue, descValue }) => {
    selectedProceso.value.id = idValue;
    selectedProceso.value.descripcion = descValue;
};

const handleAssociate = async () => {
    if (!selectedProceso.value.id) return;
    await hallazgoStore.asociarProceso(selectedProceso.value.id);
    selectedProceso.value = { id: null, descripcion: '' };
};

onMounted(() => {
    hallazgoStore.fetchAssociatedProcesos();
});
</script>

<style scoped>
.form-overlay-container {
    position: relative;
    min-height: 150px;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10;
}

.header-container {
    padding: 0.75rem;
    margin-bottom: 1.5rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #ff851b;
    display: flex;
    align-items: center;
}

.table th,
.table td {
    font-size: 0.8rem;
    vertical-align: middle;
}

.table td input[type="checkbox"] {
    transform: scale(0.9);
}
</style>