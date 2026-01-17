<template>
    <div>
        <div class="header-container mb-4">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">Procesos Auditados</span>
            </h6>
        </div>

        <div class="card border-0 shadow-none">
            <div class="card-body p-0">
                <p class="text-muted small mb-4 italic">
                    Busque y asocie los procesos institucionales que serán evaluados en el alcance de esta auditoría.
                </p>

                <!-- Buscador y Selector -->
                <div class="d-flex align-items-center mb-4">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Seleccione el Proceso a Asociar"
                            v-model="selectedProceso.descripcion" readonly />
                        <div class="input-group-append">
                            <button type="button" class="btn btn-dark" @click="openProcesoModal" title="Buscar Proceso">
                                <i class="fas fa-search"></i>
                            </button>
                            <button type="button" class="btn btn-danger" :disabled="!selectedProceso.id"
                                @click="handleAssociate">
                                <i class="fas fa-link mr-1"></i> Asociar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-overlay-container">
                    <div v-if="loadingData" class="loading-overlay">
                        <div class="spinner-border text-danger" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>

                    <h6 class="mb-3 font-weight-bold small text-secondary text-uppercase">Procesos asociados al plan
                    </h6>
                    <div v-if="!associatedProcesos.length && !loadingData"
                        class="text-center py-5 border rounded bg-light">
                        <i class="fas fa-project-diagram fa-3x text-light mb-3"></i>
                        <p class="text-muted mb-0">Esta auditoría aún no tiene procesos asociados.</p>
                        <small class="text-secondary">Use el buscador superior para agregar procesos.</small>
                    </div>

                    <div v-else class="table-responsive">
                        <table class="table table-sm table-bordered table-hover">
                            <thead class="bg-light text-secondary small font-weight-bold">
                                <tr>
                                    <th style="width: 80px;" class="text-center">ID</th>
                                    <th>Código</th>
                                    <th>Nombre del Proceso</th>
                                    <th class="text-center" style="width: 80px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <tr v-for="proceso in associatedProcesos" :key="proceso.id">
                                    <td class="text-center">{{ proceso.id }}</td>
                                    <td class="font-weight-bold">{{ proceso.pro_codigo || proceso.cod_proceso }}</td>
                                    <td>{{ proceso.pro_nombre || proceso.proceso_nombre }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-link text-danger p-0" title="Eliminar asociación"
                                            @click="disociarProceso(proceso.id)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 small text-secondary" v-if="associatedProcesos.length > 0">
                        Total Seleccionados: <span class="badge badge-info">{{ associatedProcesos.length }}</span>
                    </div>
                </div>

                <div class="modal-footer justify-content-center bg-light border-top mt-5 p-3"
                    style="margin: 0 -1.5rem -1rem -1.5rem; border-bottom-right-radius: 0.3rem;">
                    <button class="btn btn-danger btn-sm px-5 shadow-sm" @click="save" :disabled="saving">
                        <i v-if="saving" class="fas fa-spinner fa-spin mr-1"></i>
                        <i v-else class="fas fa-save mr-1"></i>
                        Guardar Selección
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm ml-2 px-4 shadow-sm" @click="$emit('close')">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                </div>
            </div>
        </div>

        <modal-hijo ref="procesoModal" :fetch-url="proceso_route" target-id="id" target-desc="proceso_nombre"
            @update-target="handleProcesoSelection" />
    </div>
</template>

<script setup>
import { ref, onMounted, defineProps, defineEmits, watch } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import { route } from 'ziggy-js';
import ModalHijo from '../../generales/ModalHijo.vue';

const props = defineProps(['auditId', 'auditData', 'auditStatus', 'loading']);
const emit = defineEmits(['saved', 'close']);
const toast = useToast();

const loadingData = ref(false);
const saving = ref(false);
const associatedProcesos = ref([]);
const selectedProceso = ref({ id: null, descripcion: '' });
const procesoModal = ref(null);
const proceso_route = route('procesos.buscar');

const openProcesoModal = () => {
    procesoModal.value.open();
};

const handleProcesoSelection = ({ idValue, descValue }) => {
    selectedProceso.value.id = idValue;
    selectedProceso.value.descripcion = descValue;
    handleAssociate();
};

const handleAssociate = () => {
    if (!selectedProceso.value.id) return;

    // Check if already associated
    if (associatedProcesos.value.some(p => p.id === selectedProceso.value.id)) {
        toast.add({ severity: 'warn', summary: 'Duplicado', detail: 'El proceso ya está asociado', life: 3000 });
        return;
    }

    // Since ModalHijo only gives id and description, we might need more data 
    // but usually DocumentoProcesos just pushes the minimal info or re-fetches.
    // For consistency with the table, we'll try to find full info or just use what we have.
    associatedProcesos.value.push({
        id: selectedProceso.value.id,
        pro_nombre: selectedProceso.value.descripcion,
        pro_codigo: '---' // Will be refreshed on save/load
    });

    selectedProceso.value = { id: null, descripcion: '' };
};

const disociarProceso = (id) => {
    associatedProcesos.value = associatedProcesos.value.filter(p => p.id !== id);
};

const loadAudit = async () => {
    if (!props.auditId) return;

    if (props.auditData && props.auditData.procesos) {
        associatedProcesos.value = props.auditData.procesos;
        return;
    }

    loadingData.value = true;
    try {
        const response = await axios.get(`/api/auditorias/${props.auditId}`);
        if (response.data.procesos) {
            associatedProcesos.value = response.data.procesos;
        }
    } catch (e) {
        console.error("Error loading audit", e);
    } finally {
        loadingData.value = false;
    }
};

watch(() => props.auditData, (newVal) => {
    if (newVal && newVal.procesos) {
        associatedProcesos.value = newVal.procesos;
        loadingData.value = false; // Data is here, stop loading
    }
}, { immediate: true });

watch(() => props.loading, (newVal) => {
    // Only show loader if we don't have data yet
    if (newVal && associatedProcesos.value.length === 0) {
        loadingData.value = true;
    } else if (!newVal) {
        loadingData.value = false;
    }
}, { immediate: true });

const save = async () => {
    if (!props.auditId) return;
    saving.value = true;
    try {
        await axios.put(`/api/auditorias/${props.auditId}`, {
            procesos: associatedProcesos.value.map(p => p.id)
        });
        toast.add({ severity: 'success', summary: 'Guardado', detail: 'Procesos actualizados correctamente', life: 3000 });
        emit('saved');
        await loadAudit(); // Refresh to get full data (like codes)
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo guardando procesos', life: 3000 });
    } finally {
        saving.value = false;
    }
};

onMounted(async () => {
    if (props.auditId) await loadAudit();
});
</script>

<style scoped>
.italic {
    font-style: italic;
}

.cursor-pointer {
    cursor: pointer;
}

.header-container {
    padding: 0.75rem 1rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #ff851b;
    display: flex;
    align-items: center;
}

.form-overlay-container {
    position: relative;
    min-height: 200px;
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

.table th,
.table td {
    vertical-align: middle;
}
</style>
