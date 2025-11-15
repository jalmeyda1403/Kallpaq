<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active" aria-current="page">Listado Obligaciones</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title mb-0">Lista de Obligaciones</h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <a href="#" class="btn btn-primary btn-sm" @click.prevent="obligacionStore.openObligacionModal()"
                            title="Nueva Obligación">
                            <i class="fas fa-plus-circle"></i> Agregar
                        </a>
                        <button class="btn btn-danger btn-sm ml-1" :disabled="!selectedObligacionId"
                            @click="confirmDelete(selectedObligacionId)" title="Eliminar Obligación">
                            <i class="fas fa-trash-alt"></i> Eliminar
                        </button>
                    </div>
                </div>
                <hr>
                <form @submit.prevent="obligacionStore.fetchObligaciones">
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
                    :globalFilterFields="['proceso.proceso_nombre', 'documento_tecnico_normativo', 'obligacion_principal', 'consecuencia_incumplimiento', 'estado_obligacion']">
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
                    <Column field="proceso.proceso_nombre" header="Proceso" sortable style="width:20%">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="text" @input="filterCallback()"
                                class="p-column-filter" placeholder="Buscar por Proceso" />
                        </template>
                    </Column>
                    <Column field="documento_tecnico_normativo" header="Documento Técnico" style="width:20%">
                    </Column>
                    <Column field="obligacion_principal" header="Obligación Principal" style="width:20%">
                    </Column>
                    <Column field="consecuencia_incumplimiento" header="Consecuencia del Incumplimiento" style="width:20%">
                    </Column>
                    <Column field="estado_obligacion" header="Estado" style="width:7%">
                        <template #body="{ data }">
                            <span :class="['badge', getEstadoClass(data.estado_obligacion)]">
                                {{ ucfirst(data.estado_obligacion) }}
                            </span>
                        </template>
                    </Column>
                    <Column header="Acciones" :exportable="false" style="width:8%">
                        <template #body="{ data }">
                            <a href="#" title="Ver Riesgos" class="btn btn-danger btn-sm mr-1"
                                @click.prevent="obligacionStore.openRiesgosModal(data.id)">
                                <i class="fas fa-exclamation-triangle"></i>
                            </a>
                            <a href="#" title="Editar Obligación" class="btn btn-warning btn-sm"
                                @click.prevent="editObligacion(data)">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </div>

    <!-- Modal para agregar o editar obligación -->
    <Dialog v-model:visible="obligacionStore.showObligacionModal" :style="{ width: '750px' }" header="Detalles de la Obligación"
        :modal="true" class="p-fluid">
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
            <Textarea id="documento_tecnico_normativo" v-model="obligacionStore.form.documento_tecnico_normativo" rows="3" cols="20" />
        </div>
        <div class="p-field">
            <label for="obligacion_principal">Obligación Principal</label>
            <Textarea id="obligacion_principal" v-model="obligacionStore.form.obligacion_principal" rows="3" cols="20" />
        </div>
        <div class="p-field">
            <label for="obligacion_controles">Controles Identificados</label>
            <Textarea id="obligacion_controles" v-model="obligacionStore.form.obligacion_controles" rows="3" cols="20" />
        </div>
        <div class="p-field">
            <label for="consecuencia_incumplimiento">Consecuencia del Incumplimiento</label>
            <Textarea id="consecuencia_incumplimiento" v-model="obligacionStore.form.consecuencia_incumplimiento" rows="3" cols="20" />
        </div>
        <div class="p-field">
            <label for="documento_deroga">Documento Deroga</label>
            <InputText id="documento_deroga" v-model="obligacionStore.form.documento_deroga" />
        </div>
        <div class="p-field">
            <label for="estado_obligacion">Estado de la Obligación</label>
            <Dropdown id="estado_obligacion" v-model="obligacionStore.form.estado_obligacion" :options="['pendiente', 'mitigada', 'controlada', 'vencida', 'inactiva', 'suspendida']" placeholder="Seleccione un estado" />
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" class="p-button-text" @click="obligacionStore.closeObligacionModal()" />
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
                    <Button icon="fas fa-pencil-alt" class="p-button-rounded p-button-warning mr-1" @click="editRiesgo(data)" />
                    <Button icon="fas fa-trash-alt" class="p-button-rounded p-button-danger" @click="deleteRiesgo(data.id)" />
                </template>
            </Column>
        </DataTable>
        <template #footer>
            <Button label="Cerrar" icon="pi pi-times" class="p-button-text" @click="obligacionStore.closeRiesgosModal()" />
        </template>
    </Dialog>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useObligacionStore } from '@/stores/obligacionStore';
import { FilterMatchMode } from 'primevue/api';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';

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
    if (confirm('¿Estás seguro de que quieres eliminar esta obligación?')) {
        try {
            await obligacionStore.deleteObligacion(id);
            selectedObligacionId.value = null; // Limpiar selección
            // Mostrar mensaje de éxito
        } catch (error) {
            // Mostrar mensaje de error
        }
    }
};

const editRiesgo = (riesgo) => {
    // Lógica para editar riesgo (puede abrir otro modal o navegar a otra ruta)
    console.log('Editar riesgo:', riesgo);
};

const deleteRiesgo = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar este riesgo?')) {
        // Lógica para eliminar riesgo
        console.log('Eliminar riesgo:', id);
    }
};

onMounted(() => {
    obligacionStore.fetchObligaciones();
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

.badge-success { background-color: #28a745; }
.badge-warning { background-color: #ffc107; }
.badge-danger { background-color: #dc3545; }
.badge-info { background-color: #17a2b8; }
.badge-primary { background-color: #007bff; }
.badge-secondary { background-color: #6c757d; }
.badge-dark { background-color: #343a40; }
</style>
