<template>
    <div class="container-fluid py-4">
        <div v-if="successMessage" class="alert alert-success" id="success-alert">
            {{ successMessage }}
        </div>
        <div v-if="errorMessage" class="alert alert-danger" id="error-alert">
            {{ errorMessage }}
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link :to="{ name: 'home' }"
                        class="text-danger font-weight-bold">Inicio</router-link></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Lista Maestra de Documentos Externos
                    (LMDE)</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title mb-0">Lista Maestra de Documentos Externos</h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <a href="#" class="btn btn-primary btn-sm mr-1" @click.prevent="openNewDocumentModal"
                            title="Nuevo Documento Externo">
                            <i class="fas fa-plus-circle"></i> Agregar Externo
                        </a>
                        <button class="btn btn-danger btn-sm" :disabled="!selectedDocumento"
                            @click.prevent="deleteDocumento" title="Eliminar Documento">
                            <i class="fas fa-trash-alt"></i> Eliminar
                        </button>
                    </div>
                </div>
                <br />
                <div id="filter-form">
                    <!-- Filtros del Proceso-->
                    <form @submit.prevent="fetchDocumentosWithFilters">
                        <div class="row g-2 align-items-center">
                            <div class="col-md">
                                <input type="text" v-model="filters.buscar_documento" class="form-control me-2"
                                    placeholder="Buscar por nombre documento">
                            </div>
                            <div class="col-md">
                                <input type="text" v-model="filters.buscar_proceso" class="form-control me-2"
                                    placeholder="Buscar por Proceso">
                            </div>
                            <!-- Filtro Fuente removido, siempre es Externo -->

                            <div class="col-md-4">
                                <MultiSelect v-model="filters.tipo_documento" :options="tipoDocumentoOptions"
                                    optionLabel="td_nombre" optionValue="id" placeholder="Seleccione Tipo de Documento"
                                    class="w-100 custom-multiselect" display="chip" :filter="true"
                                    panelClass="custom-multiselect-panel" />
                            </div>
                            <div class="col-md-auto">
                                <button type="submit" class="btn bg-dark btn-sm">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <div class="card-body">
                <!-- Loading State - Barra de progreso -->
                <div>
                    <div class="h-1 mb-2" style="height: 4px;">
                        <ProgressBar v-if="isLoading" mode="indeterminate" style="height: 4px;" />
                    </div>
                    <DataTable :value="documentos" selectionMode="single" v-model:selection="selectedDocumento"
                        dataKey="id" :paginator="true" :rows="rows" :totalRecords="totalRecords" :lazy="true"
                        @page="onPage" :rowsPerPageOptions="[5, 10, 20, 50]"
                        :class="{ 'opacity-50 pointer-events-none': isLoading }">


                        <Column field="cod_documento" header="Código Documento" sortable></Column>
                        <Column field="nombre_documento" header="Nombre Documento"></Column>
                        <Column header="Tipo de Documento">
                            <template #body="slotProps">
                                {{ slotProps.data.tipo_documento ?
                                    slotProps.data.tipo_documento.td_nombre :
                                    '' }}
                            </template>
                        </Column>
                        <!-- Columna Fuente removida visualmente si se desea simplificar, o dejarla para confirmación -->
                        <Column header="Fuente" class="text-center">
                            <template #body="slotProps">
                                <span class="badge badge-info">Externo</span>
                            </template>
                        </Column>
                        <Column header="Estado" class="text-center">
                            <template #body="slotProps">
                                {{ slotProps.data.estado_documento ? slotProps.data.estado_documento : '' }}

                            </template>
                        </Column>
                        <!-- Columna Versión simplificada -->
                        <Column header="Versión" class="text-center">
                            <template #body="slotProps">
                                <span class="text-muted">-</span>
                            </template>
                        </Column>
                        <Column header="Publicado" class="text-center">
                            <template #body="slotProps">
                                {{ formatDate(slotProps.data.fecha_aprobacion_documento) }}
                            </template>
                        </Column>
                        <Column header="Archivo" class="text-center">
                            <template #body="slotProps">
                                <a v-if="getFileUrl(slotProps.data) !== '#'" :href="getFileUrl(slotProps.data)"
                                    target="_blank" class="btn btn-outline-danger btn-sm border-0 rounded-circle"
                                    data-toggle="tooltip" title="Ver / Descargar Archivo">
                                    <i class="fas fa-file-pdf fa-lg"></i>
                                </a>
                                <span v-else class="text-muted"><i
                                        class="fas fa-file-excel fa-lg opacity-25"></i></span>
                            </template>
                        </Column>
                        <Column header="Acciones" class="text-center">
                            <template #body="slotProps">
                                <div class="d-flex justify-content-center gap-2">
                                    <Button icon="pi pi-pencil"
                                        class="p-button-rounded p-button-warning p-button-text p-button-sm"
                                        @click="editDocumento(slotProps.data)" v-tooltip.top="'Editar'" />
                                    <!-- Anexos y Historial removidos o simplificados para LMDE si no aplican -->
                                </div>
                            </template>
                        </Column>


                    </DataTable>
                </div>
            </div>
        </div>
    </div>
    <DocumentoModal></DocumentoModal>
</template>
<script setup>
import { onMounted, onBeforeUnmount, ref, reactive, nextTick } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';

import DocumentoModal from '@/components/documentos/DocumentoModal.vue';
import LoadingState from '@/components/generales/LoadingState.vue';
import { useDocumentoStore } from '@/stores/documentoStore'; // Importa la tienda

// PrimeVue Components
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button'; // Import Button
import Tag from 'primevue/tag'; // Re-introduce Tag
import { useToast } from 'primevue/usetoast'; // Re-introduce useToast
import MultiSelect from 'primevue/multiselect';
import ProgressBar from 'primevue/progressbar';

const documentos = ref([]);
const totalRecords = ref(0);
const rows = ref(20);
const currentPage = ref(1);
const selectedDocumento = ref(null);
const successMessage = ref(''); // REMOVE THIS
const errorMessage = ref(''); // REMOVE THIS
const documentoStore = useDocumentoStore(); // Instancia de la tienda
const filters = reactive({
    buscar_documento: '',
    buscar_proceso: '',
    fuente: 'externo', // Forzado a externo
    tipo_documento: []
});
const isLoading = ref(true);
const toast = useToast(); // Initialize Toast

// Métodos
const tipoDocumentoOptions = ref([]);

// Métodos
const fetchTiposDocumento = async () => {
    try {
        const response = await axios.get(route('tipoDocumento.buscar'));
        // Assuming the API returns an array of objects like { id: 1, nombre_tipodocumento: 'Manual' }
        tipoDocumentoOptions.value = response.data;
    } catch (error) {
        console.error('Error al cargar tipos de documento:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar los tipos de documento.', life: 3000 });
    }
};

const fetchDocumentos = async (page = 1) => {
    isLoading.value = true;
    try {
        const params = {
            ...filters,
            page: page,
            rows: rows.value
        };
        params.fuente = 'externo'; // Asegurar que siempre sea externo

        const response = await axios.get(route('api.documentos'), {
            params: params
        });
        documentos.value = response.data.data;
        totalRecords.value = response.data.total;
        currentPage.value = response.data.current_page;
    } catch (error) {
        console.error(error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Hubo un problema al cargar los documentos. Intente de nuevo más tarde.', life: 3000 });
    } finally {
        isLoading.value = false;
    }
};

const onPage = (event) => {
    rows.value = event.rows;
    // PrimeVue page is 0-indexed, Laravel is 1-indexed
    fetchDocumentos(event.page + 1);
};

const fetchDocumentosWithFilters = () => {
    fetchDocumentos(1);
};

const openNewDocumentModal = () => {
    documentoStore.openModal();
};

const editDocumento = (documento) => {
    if (documento) {
        documentoStore.openModal(documento.id);
    }
};


const deleteDocumento = async () => {
    if (selectedDocumento.value) {
        if (confirm('¿Está seguro de que desea eliminar este documento?')) {
            try {
                await axios.delete(route('api.documentos.eliminar', { id: selectedDocumento.value.id }));
                toast.add({ severity: 'success', summary: 'Éxito', detail: 'Documento eliminado con éxito.', life: 3000 });
                fetchDocumentos();
            } catch (error) {
                console.error('Error al eliminar el documento:', error);
                toast.add({ severity: 'error', summary: 'Error', detail: 'Hubo un problema al eliminar el documento.', life: 3000 });
            }
        }
    }
};

const getFileUrl = (documento) => {
    let path = documento.archivo_path_documento;

    if (!path) return '#';

    // Si el path ya es una URL externa (http), retornarla directamente
    if (path.startsWith('http://') || path.startsWith('https://')) {
        return path;
    }

    return route('documento.mostrar', { path: path });
};

const openPdfModal = (documento) => {
    const url = getFileUrl(documento);
    if (url !== '#') {
        window.open(url, '_blank');
    }
};

// Lifecycle Hooks
const formatDate = (dateString) => {
    if (!dateString) return '';
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return ''; // Check for Invalid Date
        // Adjust for timezone offset to prevent off-by-one errors if dateString is YYYY-MM-DD
        const userTimezoneOffset = date.getTimezoneOffset() * 60000;
        const adjustedDate = new Date(date.getTime() + userTimezoneOffset);

        return new Intl.DateTimeFormat('es-PE', {
            year: 'numeric', month: '2-digit', day: '2-digit', timeZone: 'UTC'
        }).format(date);
    } catch (e) {
        return '';
    }
};

onMounted(() => {
    documentoStore.setLMDE(true); // Activa modo LMDE
    fetchTiposDocumento();

    // Check for query parameters for basic filtering if needed, but keeping source fixed
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('buscar_proceso')) {
        filters.buscar_proceso = urlParams.get('buscar_proceso');
    }
    if (urlParams.has('buscar_documento')) {
        filters.buscar_documento = urlParams.get('buscar_documento');
    }

    fetchDocumentos();
});

onBeforeUnmount(() => {
    documentoStore.setLMDE(false); // Desactiva modo LMDE al salir
});
</script>


<style>
/* Non-scoped styles to target the teleported panel */
.custom-multiselect-panel .p-multiselect-items .p-multiselect-item {
    font-size: 11px !important;
    padding: 0.25rem 0.5rem !important;
}

.custom-multiselect-panel .p-multiselect-header {
    font-size: 11px !important;
    padding: 0.25rem 0.5rem !important;
}
</style>

<style scoped>
/* Estilos para el MultiSelect de PrimeVue */
:deep(.custom-multiselect) {
    font-size: 13px;
}

:deep(.custom-multiselect .p-multiselect-label) {
    font-size: 13px;
    padding: 0.375rem 0.75rem;
    /* Match Bootstrap form-control padding */
}

:deep(.custom-multiselect .p-multiselect-label.p-placeholder) {
    font-size: 13px;
}

:deep(.p-multiselect-panel .p-multiselect-items .p-multiselect-item) {
    font-size: 11px;
    padding: 0.25rem 0.5rem;
    /* Reduce padding for more compact list */
}

:deep(.p-multiselect-panel .p-multiselect-header) {
    font-size: 11px;
    padding: 0.25rem 0.5rem;
}

:deep(.custom-multiselect .p-multiselect-token) {
    font-size: 12px;
    background-color: #dc3545;
    color: white;
}

:deep(.custom-multiselect .p-multiselect-token .p-multiselect-token-icon) {
    color: white;
}

/* Existing styles */


/* Improve row spacing in PrimeVue DataTable */
:deep(.p-datatable .p-datatable-tbody > tr > td) {
    padding: 0.4rem 0.5rem !important;
    /* Reduced padding for compact rows */
}


.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.35);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1050;
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
