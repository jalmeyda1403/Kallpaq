<template>
    <div class="container-fluid py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link to="/home"
                        class="text-danger font-weight-bold">Inicio</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Listado Obligaciones</li>
            </ol>
        </nav>

        <div class="card border-top-danger shadow-sm">
            <div class="card-header bg-white border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h5 class="card-title text-danger mb-0 font-weight-bold">
                            <i class="fas fa-gavel mr-2"></i>Lista de Obligaciones
                        </h5>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <a href="#" class="btn btn-danger btn-sm shadow-sm"
                            @click.prevent="obligacionStore.openObligacionModal()" title="Nueva Obligación">
                            <i class="fas fa-plus-circle mr-1"></i> Agregar
                        </a>
                        <button class="btn btn-dark btn-sm ml-1 shadow-sm" @click="router.push({ name: 'radar.index' })"
                            title="Radar Normativo (IA)">
                            <i class="fas fa-satellite-dish mr-1"></i> Radar IA
                        </button>
                        <button class="btn btn-outline-danger btn-sm ml-1 shadow-sm" :disabled="!selectedObligacionId"
                            @click="confirmDelete(selectedObligacionId)" title="Eliminar Obligación">
                            <i class="fas fa-trash-alt mr-1"></i> Eliminar
                        </button>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <form @submit.prevent="obligacionStore.fetchObligaciones">
                            <div class="form-row">
                                <div class="col">
                                    <input type="text" v-model="obligacionStore.filters.documento" class="form-control"
                                        placeholder="Buscar por documento...">
                                </div>
                                <div class="col">
                                    <input type="text" v-model="obligacionStore.filters.proceso" class="form-control"
                                        placeholder="Buscar por proceso...">
                                </div>
                                <div class="col">
                                    <select v-model="obligacionStore.filters.fuente" class="form-control">
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
                    <ProgressBar v-if="obligacionStore.loading" mode="indeterminate" style="height: 4px;" />
                </div>

                <DataTable ref="dt" :value="obligacionStore.obligaciones" v-model:filters="filters" paginator :rows="10"
                    :class="{ 'opacity-50 pointer-events-none': obligacionStore.loading }"
                    :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id" filterDisplay="menu"
                    :globalFilterFields="['proceso.proceso_nombre', 'documento_tecnico_normativo', 'obligacion_principal', 'consecuencia_incumplimiento', 'estado_obligacion']"
                    class="p-datatable-sm table-hover" stripedRows responsiveLayout="scroll">
                    <template #header>
                        <div class="d-flex align-items-center justify-content-end pb-2">
                            <Button type="button" icon="pi pi-download" label="Exportar" severity="secondary"
                                @click="exportCSV($event)" class="p-button-outlined p-button-secondary p-button-sm" />
                        </div>
                    </template>
                    <Column field="id" header="#" style="width:3%">
                        <template #body="{ index }">
                            <span class="text-muted small">{{ index + 1 }}</span>
                        </template>
                    </Column>
                    <Column field="proceso.proceso_nombre" header="Proceso" sortable style="width:20%">
                        <template #body="{ data }">
                            <span class="font-weight-500 text-dark">{{ data.proceso?.proceso_nombre || 'N/A' }}</span>
                        </template>
                    </Column>
                    <Column field="documento.nombre_documento" header="Documento" style="width:25%">
                        <template #body="{ data }">
                            <div class="d-flex align-items-center">
                                <i class="far fa-file-pdf text-danger mr-2 fa-lg" v-if="data.documento"></i>
                                <span v-if="data.documento" class="text-break small">{{ data.documento.nombre_documento
                                    }}</span>
                                <span v-else class="text-break small text-muted">{{ data.documento_tecnico_normativo ||
                                    'Sin documento' }}</span>
                            </div>
                        </template>
                    </Column>
                    <Column field="obligacion_principal" header="Obligación Principal" style="width:25%">
                        <template #body="{ data }">
                            <span class="small d-block text-justify text-secondary">{{ data.obligacion_principal
                                }}</span>
                        </template>
                    </Column>
                    <Column field="consecuencia_incumplimiento" header="Consecuencia" style="width:15%">
                        <template #body="{ data }">
                            <span class="small text-muted">{{ data.consecuencia_incumplimiento }}</span>
                        </template>
                    </Column>
                    <Column field="estado_obligacion" header="Estado" style="width:10%" sortable>
                        <template #body="{ data }">
                            <span :class="['badge p-2', getEstadoClass(data.estado_obligacion)]">
                                {{ ucfirst(data.estado_obligacion) }}
                            </span>
                        </template>
                    </Column>
                    <Column header="Acciones" :exportable="false" style="width:10%" class="text-center">
                        <template #body="{ data }">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-light text-danger" title="Ver Riesgos"
                                    @click.prevent="obligacionStore.openRiesgosModal(data.id)">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </button>
                                <button type="button" class="btn btn-light text-primary" title="Editar"
                                    @click.prevent="editObligacion(data)">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </div>

    <!-- Modal para agregar o editar obligación -->
    <Dialog v-model:visible="obligacionStore.showObligacionModal" :style="{ width: '750px' }"
        header="Detalles de la Obligación" :modal="true" class="p-fluid">
        <div class="p-field">
            <label for="proceso_nombre">Proceso</label>
            <InputText id="proceso_nombre" v-model="obligacionStore.form.proceso_nombre" required autofocus />
        </div>
        <div class="p-field">
            <label for="area_compliance_nombre">Área de Compliance</label>
            <InputText id="area_compliance_nombre" v-model="obligacionStore.form.area_compliance_nombre" required />
        </div>
        <div class="p-field">
            <label for="documento_tecnico_normativo">Documento Técnico Normativo</label>
            <Textarea id="documento_tecnico_normativo" v-model="obligacionStore.form.documento_tecnico_normativo"
                rows="3" cols="20" />
        </div>
        <div class="p-field">
            <label for="obligacion_principal">Obligación Principal</label>
            <Textarea id="obligacion_principal" v-model="obligacionStore.form.obligacion_principal" rows="3"
                cols="20" />
        </div>
        <div class="p-field">
            <label for="obligacion_controles">Controles Identificados</label>
            <Textarea id="obligacion_controles" v-model="obligacionStore.form.obligacion_controles" rows="3"
                cols="20" />
        </div>
        <div class="p-field">
            <label for="consecuencia_incumplimiento">Consecuencia del Incumplimiento</label>
            <Textarea id="consecuencia_incumplimiento" v-model="obligacionStore.form.consecuencia_incumplimiento"
                rows="3" cols="20" />
        </div>
        <div class="p-field">
            <label for="documento_deroga">Documento Deroga</label>
            <InputText id="documento_deroga" v-model="obligacionStore.form.documento_deroga" />
        </div>
        <div class="p-field">
            <label for="estado_obligacion">Estado de la Obligación</label>
            <Dropdown id="estado_obligacion" v-model="obligacionStore.form.estado_obligacion"
                :options="['pendiente', 'mitigada', 'controlada', 'vencida', 'inactiva', 'suspendida']"
                placeholder="Seleccione un estado" />
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" class="p-button-text"
                @click="obligacionStore.closeObligacionModal()" />
            <Button label="Guardar" icon="pi pi-check" class="p-button-primary" @click="saveObligacion" />
        </template>
    </Dialog>

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
            <Column header="Acciones">
                <template #body="{ data }">
                    <Button icon="fas fa-pencil-alt" class="p-button-rounded p-button-warning mr-1"
                        @click="editRiesgo(data)" />
                    <Button icon="fas fa-trash-alt" class="p-button-rounded p-button-danger"
                        @click="deleteRiesgo(data.id)" />
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
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useObligacionStore } from '@/stores/obligacionStore';
import { FilterMatchMode } from 'primevue/api';
import ProgressBar from 'primevue/progressbar';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';
import LoadingState from '@/components/generales/LoadingState.vue';

const router = useRouter();
const obligacionStore = useObligacionStore();
const dt = ref(null);
const selectedObligacionId = ref(null);

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

const editObligacion = (obligacion) => {
    obligacionStore.openObligacionModal(obligacion);
};

const saveObligacion = async () => {
    try {
        await obligacionStore.saveObligacion(obligacionStore.form);
        obligacionStore.closeObligacionModal();
        // Mostrar mensaje de éxito
    } catch (error) {
        // Mostrar mensaje de error
    }
};

const confirmDelete = async (id) => {
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esta acción",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        try {
            await obligacionStore.deleteObligacion(id);
            selectedObligacionId.value = null; // Limpiar selección
            Swal.fire({
                icon: 'success',
                title: 'Eliminado',
                text: 'La obligación ha sido eliminada.',
                timer: 1500,
                showConfirmButton: false
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema al eliminar la obligación.'
            });
        }
    }
};

const editRiesgo = (riesgo) => {
    // Lógica para editar riesgo (puede abrir otro modal o navegar a otra ruta)
    console.log('Editar riesgo:', riesgo);
};

const deleteRiesgo = async (id) => {
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: "Se eliminará este riesgo asociado",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        // Lógica para eliminar riesgo
        console.log('Eliminar riesgo:', id);
    }
};

onMounted(() => {
    obligacionStore.fetchObligaciones();
});
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

.badge-success {
    background-color: #28a745;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
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
</style>
