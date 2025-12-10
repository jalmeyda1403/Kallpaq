<template>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="card-title mb-0">Lista de Programas de Auditorías</h3>
                    </div>
                    <div class="col-md-6 text-right">
                        <Button label="Nuevo Programa" icon="pi pi-plus" class="p-button-sm p-button-primary" @click="nuevoPrograma" />
                    </div>
                </div>
            </div>

            <div class="card-body">
                <DataTable :value="programas" :paginator="true" :rows="10" :loading="loading" stripedRows tableStyle="min-width: 50rem"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    :rowsPerPageOptions="[10, 25, 50]"
                    currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registros">
                    
                    <Column field="id" header="Id" sortable></Column>
                    <Column field="periodo" header="Periodo" sortable></Column>
                    <Column field="version" header="Versión" sortable></Column>
                    <Column field="presupuesto" header="Presupuesto" sortable></Column>
                    <Column field="fecha_aprobacion" header="Fecha de Aprobación" sortable></Column>
                    <Column field="avance" header="Avance" sortable></Column>
                    <Column header="Acciones" class="text-center">
                        <template #body="slotProps">
                            <div class="d-flex justify-content-center gap-2">
                                <Button icon="pi pi-list" class="p-button-rounded p-button-info p-button-text p-button-sm" @click="verHistorial(slotProps.data)" v-tooltip.top="'Ver Historial'" />
                                <Button icon="pi pi-download" class="p-button-rounded p-button-success p-button-text p-button-sm" @click="descargarPDF(slotProps.data)" v-tooltip.top="'Descargar PDF'" />
                                <Button icon="pi pi-pencil" class="p-button-rounded p-button-warning p-button-text p-button-sm" @click="editarPrograma(slotProps.data)" v-tooltip.top="'Editar'" />
                                <Button icon="pi pi-history" class="p-button-rounded p-button-danger p-button-text p-button-sm" @click="reprogramarPrograma(slotProps.data)" v-tooltip.top="'Reprogramar'" />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import { useRouter } from 'vue-router';

const toast = useToast();
const router = useRouter();

const programas = ref([]);
const loading = ref(false);

const fetchProgramas = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/programa');
        programas.value = response.data;
    } catch (error) {
        console.error('Error fetching programas:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar los programas', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const nuevoPrograma = () => {
    // Navigate to create route (needs to be implemented in Vue)
    // router.push({ name: 'programa.create' });
    toast.add({ severity: 'info', summary: 'Info', detail: 'Funcionalidad en desarrollo', life: 3000 });
};

const verHistorial = (programa) => {
    // router.push({ name: 'programa.history', params: { id: programa.id } });
    toast.add({ severity: 'info', summary: 'Info', detail: 'Funcionalidad en desarrollo', life: 3000 });
};

const descargarPDF = (programa) => {
    // Logic to download PDF
    toast.add({ severity: 'info', summary: 'Info', detail: 'Funcionalidad en desarrollo', life: 3000 });
};

const editarPrograma = (programa) => {
    // router.push({ name: 'programa.edit', params: { id: programa.id } });
    toast.add({ severity: 'info', summary: 'Info', detail: 'Funcionalidad en desarrollo', life: 3000 });
};

const reprogramarPrograma = (programa) => {
    // Logic to reprogram
    toast.add({ severity: 'info', summary: 'Info', detail: 'Funcionalidad en desarrollo', life: 3000 });
};

onMounted(() => {
    fetchProgramas();
});
</script>

<style scoped>
.p-button-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}
</style>
