<template>
    <div>
        <div class="header-container">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">Asociación de Procesos</span>
            </h6>
        </div>

        <div class="alert alert-light border shadow-sm p-3 mb-3">
            <small class="text-muted d-block"><i class="fas fa-info-circle mr-1"></i>
                <strong>Nota:</strong> Esta obligación se vinculará principalmente a un proceso, pero puede
                afectar a otros vinculándolos en esta lista.</small>
        </div>

        <p class="small text-muted">
            Este módulo permite registrar y visualizar las relaciones entre la obligación y los procesos.
            Seleccione un proceso para añadirlo a la lista.
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
                        <i class="fas fa-link"></i> Agregar
                    </button>
                </div>
            </div>
        </div>

        <div class="form-overlay-container">
            <h6 class="mb-3 font-weight-bold">Lista de procesos asociados</h6>
            <div v-if="!modelValue.length" class="text-center py-3 text-muted border rounded bg-light">
                <i class="fas fa-info-circle mr-2"></i> Esta obligación aún no tiene procesos asociados (además del
                posible principal).
            </div>
            <div v-else class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Id</th>
                            <th>Código</th>
                            <th>Nombre del Proceso</th>
                            <th class="text-center text-nowrap" style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="proceso in enrichedProcesos" :key="proceso.id">
                            <td>{{ proceso.id }}</td>
                            <td>{{ proceso.cod_proceso || '--' }}</td>
                            <td>{{ proceso.proceso_nombre }}</td>
                            <td class="text-center text-nowrap">
                                <button class="btn btn-danger btn-sm" @click="removeProceso(proceso.id)"
                                    title="Desasociar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Buscador de Procesos -->
        <modal-hijo ref="procesoModal" :fetch-url="proceso_route" target-id="id" target-desc="proceso_nombre"
            @update-target="handleProcesoSelection" />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { route } from 'ziggy-js';
import ModalHijo from '../generales/ModalHijo.vue';

const props = defineProps({
    modelValue: { // Array of IDs
        type: Array,
        default: () => []
    },
    allProcesos: { // List of all processes to lookup details by ID
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['update:modelValue']);

const selectedProceso = ref({ id: null, descripcion: '' });
const procesoModal = ref(null);
// Use ziggy route if available, or fallback. Assuming 'procesos.buscar' exists per analysis.
const proceso_route = route('procesos.buscar');

const openProcesoModal = () => {
    procesoModal.value.open();
};

const handleProcesoSelection = ({ idValue, descValue }) => {
    selectedProceso.value.id = idValue;
    selectedProceso.value.descripcion = descValue;
};

const handleAssociate = () => {
    if (!selectedProceso.value.id) return;

    // Check if already exists
    if (props.modelValue.includes(selectedProceso.value.id)) {
        // Already added, maybe alert?
        return;
    }

    const newIds = [...props.modelValue, selectedProceso.value.id];
    emit('update:modelValue', newIds);

    // Clean selection
    selectedProceso.value = { id: null, descripcion: '' };
};

const removeProceso = (id) => {
    const newIds = props.modelValue.filter(pid => pid !== id);
    emit('update:modelValue', newIds);
};

// Enrich IDs with details for display
const enrichedProcesos = computed(() => {
    return props.modelValue.map(id => {
        const found = props.allProcesos.find(p => p.id == id);
        return found || { id: id, proceso_nombre: 'Cargando...', cod_proceso: '' };
    });
});

</script>

<style scoped>
.header-container {
    padding: 0.75rem;
    margin-bottom: 1.5rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #dc3545;
    /* Red for Obligaciones */
    display: flex;
    align-items: center;
}

.table th,
.table td {
    font-size: 0.85rem;
    vertical-align: middle;
}
</style>
