<template>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="card-title mb-0">Lista de Procesos</h3>
                    </div>
                    <div class="col-md-6 text-right">
                        <Button label="Agregar" icon="pi pi-plus-circle" class="p-button-sm p-button-primary" @click="abrirNuevoProceso" />
                    </div>
                </div>
                <div class="card-body p-0 mt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="w-50 mr-2">
                             <span class="p-input-icon-left w-100">
                                <i class="pi pi-search" />
                                <InputText v-model="filters.buscar_proceso" placeholder="Buscar por Proceso" class="w-100" @input="fetchProcesos" />
                            </span>
                        </div>
                        <div class="w-50">
                            <Dropdown v-model="filters.proceso_padre_id" :options="macroProcesos" optionLabel="proceso_nombre" optionValue="id" placeholder="Selecciona un macro proceso" class="w-100" showClear @change="fetchProcesos" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <DataTable :value="procesos" :paginator="true" :rows="25" :loading="loading" stripedRows tableStyle="min-width: 50rem"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    :rowsPerPageOptions="[10, 25, 50]"
                    currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registros">
                    
                    <Column field="cod_proceso" header="Cod Proceso" sortable></Column>
                    <Column field="proceso_nombre" header="Nombre" sortable style="width: 20%"></Column>
                    <Column field="proceso_tipo" header="Tipo" sortable></Column>
                    <Column field="proceso_sigla" header="Sigla" sortable></Column>
                    <Column field="proceso_nivel" header="Nivel" sortable></Column>
                    <Column field="responsable" header="Responsable" style="width: 20%">
                        <template #body="slotProps">
                            {{ slotProps.data.responsable || 'No hay responsable' }}
                        </template>
                    </Column>
                    <Column header="OUOs" class="text-center">
                        <template #body="slotProps">
                            <a href="#" @click.prevent="abrirOUOModal(slotProps.data.id)">
                                {{ slotProps.data.ouos_count }}
                            </a>
                        </template>
                    </Column>
                    <Column header="Subprocesos" class="text-center">
                        <template #body="slotProps">
                            <router-link :to="{ name: 'procesos.subprocesos', params: { id: slotProps.data.id } }" class="text-primary text-decoration-underline" title="Ver Subprocesos">
                                {{ slotProps.data.subprocesos_count }}
                            </router-link>
                        </template>
                    </Column>
                    <Column header="Acciones" class="text-center">
                        <template #body="slotProps">
                            <div class="d-flex justify-content-center gap-2">
                                <Button icon="pi pi-pencil" class="p-button-rounded p-button-warning p-button-text p-button-sm" @click="editarProceso(slotProps.data)" v-tooltip.top="'Editar'" />
                                <Button icon="pi pi-trash" class="p-button-rounded p-button-danger p-button-text p-button-sm" @click="eliminarProceso(slotProps.data)" v-tooltip.top="'Eliminar'" />
                                <Button icon="pi pi-link" class="p-button-rounded p-button-secondary p-button-text p-button-sm" @click="toggleMenu($event, slotProps.data)" v-tooltip.top="'Asociaciones'" />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <proceso-modal @proceso-guardado="fetchProcesos"></proceso-modal>
        
        <!-- Menu for Associations -->
        <Menu ref="menu" :model="menuItems" :popup="true" />
    </div>
</template>

<script setup>
import { ref, onMounted, reactive, watch } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import Menu from 'primevue/menu';

const toast = useToast();
const router = useRouter();

const procesos = ref([]);
const macroProcesos = ref([]);
const loading = ref(false);
const filters = reactive({
    buscar_proceso: '',
    proceso_padre_id: null
});

const menu = ref(null);
const selectedProceso = ref(null);

const menuItems = ref([
    {
        label: 'Documentación',
        icon: 'pi pi-file',
        command: () => {
            router.push({ name: 'procesos.caracterizacion', params: { id: selectedProceso.value.id } });
        }
    },
    {
        label: 'Indicadores',
        icon: 'pi pi-chart-bar',
        command: () => {
            router.push({ name: 'indicadores.listar', params: { proceso_id: selectedProceso.value.id } });
        }
    },
    {
        label: 'Obligaciones',
        icon: 'pi pi-list',
        command: () => {
            router.push({ name: 'obligaciones.listar', params: { proceso_id: selectedProceso.value.id } });
        }
    },
    {
        label: 'Riesgos',
        icon: 'pi pi-exclamation-triangle',
        command: () => {
            router.push({ name: 'riesgos.listar', params: { proceso_id: selectedProceso.value.id } });
        }
    },
    {
        label: 'Hallazgos',
        icon: 'pi pi-fire',
        command: () => {
             // Placeholder or implement route
             console.log('Hallazgos clicked for', selectedProceso.value.id);
        }
    }
]);

const toggleMenu = (event, proceso) => {
    selectedProceso.value = proceso;
    menu.value.toggle(event);
};

const fetchProcesos = async () => {
    loading.value = true;
    try {
        // If route has id param, use it as proceso_padre_id (for subprocesos view)
        // Otherwise use the filter value
        const parentId = router.currentRoute.value.params.id || filters.proceso_padre_id;
        
        const params = {
            buscar_proceso: filters.buscar_proceso,
            proceso_padre_id: parentId
        };
        const response = await axios.get('/api/procesos/index', { params });
        procesos.value = response.data;
    } catch (error) {
        console.error('Error fetching procesos:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar los procesos', life: 3000 });
    } finally {
        loading.value = false;
    }
};

// Watch for route changes to refetch data (e.g. navigating from processes to sub-processes)
watch(
  () => router.currentRoute.value.params.id,
  (newId) => {
    fetchProcesos();
  }
);

const fetchMacroProcesos = async () => {
    try {
        const response = await axios.get('/procesos/macro');
        macroProcesos.value = response.data;
    } catch (error) {
        console.error('Error fetching macro procesos:', error);
    }
};

const abrirNuevoProceso = () => {
    window.dispatchEvent(new Event('abrirProcesoModal'));
};

const editarProceso = (proceso) => {
    window.dispatchEvent(new CustomEvent('editarProceso', {
        detail: { id: proceso.id }
    }));
};

const eliminarProceso = (proceso) => {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminarlo!'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(`/procesos/${proceso.id}`)
                .then(() => {
                    Swal.fire(
                        'Eliminado!',
                        'El proceso ha sido eliminado.',
                        'success'
                    );
                    fetchProcesos();
                })
                .catch(error => {
                    console.error('Error deleting proceso:', error);
                    Swal.fire(
                        'Error!',
                        'Hubo un problema al eliminar el proceso.',
                        'error'
                    );
                });
        }
    });
};

const abrirOUOModal = (id) => {
     // Trigger Livewire modal if it still exists or replace with Vue modal
     // The original code used: Livewire.dispatchTo('asignarouo-modal', 'obtenerOUO', {id:{{ $proceso->id }}})
     // Since we are moving to Vue, we should probably have a Vue version of this modal.
     // For now, I'll log it or try to trigger the existing mechanism if possible, but Livewire might not work well inside Vue SPA if not carefully handled.
     // Ideally, we should migrate 'asignarouo-modal' to Vue.
     console.log('Open OUO Modal for', id);
     // Temporary: Alert user that this feature needs migration
     toast.add({ severity: 'info', summary: 'Info', detail: 'Funcionalidad en migración (Asignar OUO)', life: 3000 });
};

onMounted(() => {
    fetchProcesos();
    fetchMacroProcesos();
});
</script>

<style scoped>
.p-button-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}
</style>
