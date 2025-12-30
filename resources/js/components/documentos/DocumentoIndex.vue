<template>
    <div class="container-fluid">
        <div v-if="successMessage" class="alert alert-success" id="success-alert">
            {{ successMessage }}
        </div>
        <div v-if="errorMessage" class="alert alert-danger" id="error-alert">
            {{ errorMessage }}
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active" aria-current="page">Documentos</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title mb-0">Documentación del Proceso</h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <a href="#" class="btn btn-primary btn-sm mr-1" @click.prevent="openNewDocumentModal"
                            title="Nuevo Documento">
                            <i class="fas fa-plus-circle"></i> Agregar
                        </a>
                        <button class="btn btn-warning btn-sm mr-1" :disabled="!selectedDocumento"
                            @click.prevent="editDocumento" title="Editar Documento">
                            <i class="fas fa-pencil-alt"></i> Editar
                        </button>
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
                            <div class="col-md">
                                <select v-model="filters.fuente" class="form-control me-2">
                                    <option value="">Buscar por fuente</option>
                                    <option value="interno">Fuente Interna</option>
                                    <option value="externo">Fuente Externa</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <MultiSelect v-model="filters.tipo_documento" :options="tipoDocumentoOptions"
                                    optionLabel="nombre_tipodocumento" optionValue="id"
                                    placeholder="Seleccione Tipo de Documento" class="w-100 custom-multiselect"
                                    display="chip" :filter="true" panelClass="custom-multiselect-panel" />
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
                <!-- Loading State - Spinner circular rojo -->
                <div>
                    <DataTable :value="documentos" selectionMode="single" v-model:selection="selectedDocumento"
                        :loading="isLoading" dataKey="id" :paginator="true" :rows="20"
                        :rowsPerPageOptions="[5, 10, 25, 50]">


                        <Column field="cod_documento" header="Código Documento" sortable></Column>
                        <Column field="nombre_documento" header="Nombre Documento"></Column>
                        <Column header="Tipo de Documento">
                            <template #body="slotProps">
                                {{ slotProps.data.tipo_documento ?
                                    slotProps.data.tipo_documento.nombre_tipodocumento :
                                    '' }}
                            </template>
                        </Column>
                        <Column header="Fuente" class="text-center">
                            <template #body="slotProps">
                                {{ slotProps.data.fuente_documento ? slotProps.data.fuente_documento : '' }}

                            </template>
                        </Column>
                        <Column header="Estado" class="text-center">
                            <template #body="slotProps">
                                {{ slotProps.data.estado_documento ? slotProps.data.estado_documento : '' }}

                            </template>
                        </Column>
                        <Column header="Versión" class="text-center">
                            <template #body="slotProps">
                                {{ slotProps.data.usa_versiones_documento && slotProps.data.ultima_version &&
                                    slotProps.data.ultima_version.dv_version ?
                                    String(slotProps.data.ultima_version.dv_version).padStart(2, '0') : '00' }}
                            </template>
                        </Column>
                        <Column header="Vigencia" class="text-center">
                            <template #body="slotProps">
                                {{ slotProps.data.usa_versiones_documento ? (slotProps.data.ultima_version ? new
                                    Intl.DateTimeFormat('es-PE', {
                                        year: 'numeric', month: '2-digit', day: '2-digit'
                                    }).format(new Date(slotProps.data.ultima_version.dv_fecha_vigencia)) : '') :
                                    (slotProps.data.fecha_vigencia_documento ? new Intl.DateTimeFormat('es-PE', {
                                        year:
                                            'numeric', month: '2-digit', day: '2-digit'
                                    }).format(new
                                        Date(slotProps.data.fecha_vigencia_documento)) : '') }}
                            </template>
                        </Column>
                        <Column header="Info" class="text-center">
                            <template #body="slotProps">
                                <i class="fas"
                                    :class="{ 'fa-check-circle text-success': slotProps.data.usa_versiones_documento ? (slotProps.data.ultima_version?.dv_enlace_valido === 1) : (slotProps.data.enlace_valido === 1), 'fa-exclamation-circle text-warning': !(slotProps.data.usa_versiones_documento ? (slotProps.data.ultima_version?.dv_enlace_valido === 1) : (slotProps.data.enlace_valido === 1)) }"
                                    :title="slotProps.data.usa_versiones_documento ? (slotProps.data.ultima_version?.dv_enlace_valido === 1 ? 'Enlace válido' : 'Enlace no válido') : (slotProps.data.enlace_valido === 1 ? 'Enlace válido' : 'Enlace no válido')"></i>
                            </template>
                        </Column>
                        <Column header="Acciones" class="text-center">
                            <template #body="slotProps">
                                <a href="#" class="px-1" @click.prevent="openPdfModal(slotProps.data)"
                                    title="Abrir Pdf"><i class="fas fa-file-pdf fa-lg text-danger"></i></a>
                                <a href="#" class="px-1" @click.prevent="showRelatedDocs(slotProps.data.id)"
                                    title="Ver Documentos Relacionados"><i class="fas fa-copy"></i></a>
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
import Tag from 'primevue/tag'; // Re-introduce Tag
import { useToast } from 'primevue/usetoast'; // Re-introduce useToast
import MultiSelect from 'primevue/multiselect';

const documentos = ref([]);
const selectedDocumento = ref(null);
const successMessage = ref(''); // REMOVE THIS
const errorMessage = ref(''); // REMOVE THIS
const documentoStore = useDocumentoStore(); // Instancia de la tienda
const filters = reactive({
    buscar_documento: '',
    buscar_proceso: '',
    fuente: '',
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

const fetchDocumentos = async () => {
    isLoading.value = true;
    try {
        // Prepare filters for API
        // MultiSelect v-model returns an array of selected values (IDs if optionValue is set)
        const params = { ...filters };

        const response = await axios.get(route('api.documentos'), {
            params: params
        });
        documentos.value = response.data;
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Hubo un problema al cargar los documentos. Intente de nuevo más tarde.', life: 3000 });
    } finally {
        isLoading.value = false;
    }
};

const fetchDocumentosWithFilters = () => {
    fetchDocumentos();
};

const openNewDocumentModal = () => {
    documentoStore.openModal();
};

const editDocumento = () => {
    if (selectedDocumento.value) {
        documentoStore.openModal(selectedDocumento.value);
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

const openPdfModal = (documento) => {
    console.log('Abrir PDF de:', documento);
    // Lógica para abrir tu modal de PDF
};

const showRelatedDocs = (documentoId) => {
    console.log('Mostrar documentos relacionados para:', documentoId);
    // Lógica para mostrar docs relacionados
};

// Lifecycle Hooks
onMounted(() => {
    fetchTiposDocumento();

    // Check for query parameters
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('buscar_proceso')) {
        filters.buscar_proceso = urlParams.get('buscar_proceso');
    }
    if (urlParams.has('buscar_documento')) {
        filters.buscar_documento = urlParams.get('buscar_documento');
    }

    fetchDocumentos();
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
    padding: 0.75rem 0.5rem !important;
    /* Adjust as needed, e.g., 0.75rem for more space */
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