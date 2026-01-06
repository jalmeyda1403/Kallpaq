<template>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="card-title mb-0">Documentación del Proceso</h3>
                    </div>
                    <div class="col-md-6 text-right">
                        <DataViewLayoutOptions v-model="layout" />
                    </div>
                </div>
                <hr>
                <!-- Filtros -->
                <div id="filter-form">
                    <form @submit.prevent="fetchDocumentos">
                        <div class="row g-2 align-items-center">
                            <div class="col-md">
                                <input type="text" v-model="filters.buscar_documento" class="form-control"
                                    placeholder="Buscar por nombre documento">
                            </div>
                            <div class="col-md">
                                <input type="text" v-model="filters.buscar_proceso" class="form-control"
                                    placeholder="Buscar por Proceso">
                            </div>
                            <div class="col-md">
                                <select v-model="filters.fuente" class="form-control">
                                    <option value="">Buscar por fuente</option>
                                    <option value="interno">Fuente Interna</option>
                                    <option value="externo">Fuente Externa</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <MultiSelect v-model="filters.tipo_documento" :options="tipoDocumentoOptions"
                                    optionLabel="td_nombre" optionValue="id"
                                    placeholder="Seleccione Tipo de Documento..." display="chip"
                                    class="w-100 custom-multiselect" :maxSelectedLabels="3"
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
                <div v-if="loading" class="text-center my-5">
                    <ProgressSpinner />
                </div>
                <div v-else-if="!documentos || documentos.length === 0" class="text-center my-5">
                    <p class="text-muted">No se encontraron documentos.</p>
                </div>
                <div v-else>
                    <!-- Manual List/Grid View -->
                    <div class="row"
                        :class="{ 'row-cols-1': layout === 'list', 'row-cols-md-2 row-cols-xl-3 g-4': layout === 'grid' }">
                        <div v-for="(doc, index) in documentos" :key="doc.id" class="col">

                            <!-- List View Item -->
                            <div v-if="layout === 'list'" class="document-list-item"
                                :class="{ 'border-bottom-divider': index < documentos.length - 1 }">
                                <div class="p-3 d-flex flex-column">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <div class="d-flex align-items-center flex-grow-1 me-3">
                                            <div class="d-flex flex-column flex-grow-1">
                                                <h6 class="mb-0 text-truncate-multiline text-dark font-weight-bold">
                                                    {{ doc.nombre_documento }}
                                                </h6>
                                                <small class="badge-container mt-1">
                                                    <span class="badge badge-pill badge-danger text-white">{{
                                                        doc.tipo_documento?.td_nombre || 'N/A' }}</span>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end flex-shrink-0 gap-2">
                                            <Button v-if="getPdfPath(doc)" icon="pi pi-file-pdf"
                                                label="PDF"
                                                class="btn-action-pill p-button-outlined p-button-danger p-button-sm px-3"
                                                @click="openPdf(doc)" v-tooltip.top="'Ver PDF'" />
                                            <Button icon="pi pi-paperclip"
                                                label="Anexos"
                                                class="btn-action-pill p-button-outlined p-button-secondary p-button-sm px-3"
                                                @click="openAnexosModal(doc)" v-tooltip.top="'Anexos'" />
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-end mt-1 mb-2">
                                        <div class="text-muted small text-nowrap me-3">
                                            <span class="fw-bold">{{ doc.cod_documento }}</span>
                                            <span class="mx-1">|</span>
                                            v{{ String(doc.ultima_version?.version || 0).padStart(2, '0') }}
                                            <span class="mx-1">|</span>
                                            <i class="fas fa-layer-group me-1"></i> Área: {{
                                                doc.area_compliance?.area_compliance_nombre || 'N/A' }}
                                            <span class="mx-1">|</span>
                                            <i class="fas fa-calendar-alt me-1"></i> Publicado: {{
                                            doc.usa_versiones_documento == '1' ? 
                                            (doc.ultima_version ? formatDate(doc.ultima_version.fecha_publicacion) : '') : 
                                            formatDate(doc.pubished_at) }}
                                        </div>
                                    </div>

                                    <p class="card-text text-muted small resumen-truncado flex-grow-1 mb-0">
                                        {{ doc.resumen_documento || 'Sin resumen disponible.' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Grid View Item -->
                            <div v-else class="card h-100 card-documento">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0 me-2">{{ doc.nombre_documento }}</h6>
                                        <span class="badge bg-danger text-white flex-shrink-0">{{
                                            doc.tipo_documento?.td_nombre || 'N/A' }}</span>
                                    </div>

                                    <div class="text-muted small mb-2">
                                        <span class="fw-bold">{{ doc.cod_documento }}</span>
                                        <span class="mx-1">|</span>
                                        <i class="fas fa-code-branch me-1"></i>
                                        v{{ String(doc.ultima_version?.version || 0).padStart(2, '0') }}
                                    </div>

                                    <p class="card-text text-muted flex-grow-1" style="font-size: 13px;">
                                        {{ doc.resumen_documento || 'Sin resumen disponible.' }}
                                    </p>

                                    <div class="mt-auto">
                                        <div
                                            class="d-flex justify-content-between align-items-center small text-muted mb-3">
                                            <div class="d-flex align-items-center" title="Área temática">
                                                <i class="fas fa-layer-group me-1 text-secondary"></i>
                                                <span>{{ doc.area_compliance?.area_compliance_nombre || 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center" title="Fecha de Publicación">
                                                <i class="fas fa-calendar-alt me-1 text-secondary"></i>
                                                <span> Publicado: {{ doc.usa_versiones_documento == '1' ? 
                                                    (doc.ultima_version ? formatDate(doc.ultima_version.fecha_publicacion) : '') : 
                                                    formatDate(doc.pubished_at) }}</span>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-center gap-2">
                                            <Button v-if="getPdfPath(doc)" label="PDF" icon="pi pi-file-pdf"
                                                class="btn-action-pill p-button-outlined p-button-danger p-button-sm px-3"
                                                @click="openPdf(doc)" />
                                            <Button label="Anexos" icon="pi pi-paperclip"
                                                class="btn-action-pill p-button-outlined p-button-secondary p-button-sm px-3"
                                                @click="openAnexosModal(doc)" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <Paginator :rows="8" :totalRecords="totalRecords" :first="first" @page="onPage" class="mt-4" />
                </div>
            </div>
        </div>
        <PdfModal ref="pdfModal" />
        
        <!-- Anexos Modal Bootstrap Standard -->
        <div class="modal fade" id="anexosModal" tabindex="-1" aria-labelledby="anexosModalLabel" aria-hidden="true" ref="anexosModalRef">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-danger text-white">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-paperclip mr-2"></i>
                            <h5 class="modal-title font-weight-bold" id="anexosModalLabel">Anexos del Documento</h5>
                        </div>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" @click="closeAnexosModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="p-3 bg-light border-bottom">
                            <h6 class="mb-1 text-dark font-weight-bold">{{ selectedDoc?.nombre_documento }}</h6>
                            <small class="text-secondary">{{ selectedDoc?.cod_documento }}</small>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="bg-light text-secondary small text-uppercase">
                                    <tr>
                                        <th class="px-4 py-3 border-0">Nombre del Anexo</th>
                                        <th class="py-3 border-0 text-center">Código</th>
                                        <th class="py-3 border-0 text-center">Versión</th>
                                        <th class="py-3 border-0 text-center">Publicado</th>
                                        <th class="px-4 py-3 border-0 text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="anexo in selectedDoc?.anexos" :key="anexo.id">
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-pdf text-danger mr-3 fa-lg"></i>
                                                <span class="font-weight-medium text-dark">{{ anexo.da_nombre }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 text-center">
                                            <span class="badge badge-light border text-secondary px-2 py-1 small">
                                                {{ anexo.da_codigo }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-center">
                                            <span class="badge badge-pill badge-danger-soft text-danger px-2 py-1 font-weight-bold">
                                                v{{ anexo.da_version }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-center small text-muted">
                                            {{ formatDate(anexo.da_fecha_publicacion) }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <a :href="getAnexoUrl(anexo)" target="_blank" 
                                               class="btn btn-outline-danger btn-sm rounded-circle d-inline-flex align-items-center justify-content-center"
                                               style="width: 34px; height: 34px;"
                                               title="Descargar">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr v-if="!selectedDoc?.anexos || selectedDoc.anexos.length === 0">
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                            <p class="mb-0">No se han encontrado anexos para este documento.</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" @click="closeAnexosModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import PdfModal from '@/components/generales/PdfModal.vue';
import { useToast } from 'primevue/usetoast';
import { useRoute } from 'vue-router';
import { Modal } from 'bootstrap';

// PrimeVue Components
import Paginator from 'primevue/paginator';
import DataViewLayoutOptions from 'primevue/dataviewlayoutoptions';
import ProgressSpinner from 'primevue/progressspinner';
import Tag from 'primevue/tag';
import Button from 'primevue/button';
import MultiSelect from 'primevue/multiselect';

const toast = useToast();
const currentRoute = useRoute();
const documentos = ref([]);
const loading = ref(false);
const layout = ref('grid');
const totalRecords = ref(0);
const first = ref(0);
const pdfModal = ref(null);
const anexosModalRef = ref(null);
let anexosModalInstance = null;
const selectedDoc = ref(null);

const filters = reactive({
    buscar_documento: '',
    buscar_proceso: '',
    fuente: '',
    tipo_documento: []
});

const tipoDocumentoOptions = ref([]);

const fetchTiposDocumento = async () => {
    try {
        console.log('Fetching tipos documento...');
        const response = await axios.get('/buscartipodocumento');
        console.log('Tipos documento fetched:', response.data);
        tipoDocumentoOptions.value = response.data;
    } catch (error) {
        console.error('Error fetching tipos documento:', error);
    }
};

const fetchDocumentos = async (event) => {
    loading.value = true;
    console.log('Starting fetchDocumentos...');
    const page = event?.page ? event.page + 1 : 1;
    try {
        const params = {
            page: page,
            buscar_documento: filters.buscar_documento,
            buscar_proceso: filters.buscar_proceso,
            fuente: filters.fuente,
            tipo_documento: filters.tipo_documento
        };
        console.log('Request params:', params);
        // Add timeout of 10 seconds
        const response = await axios.get('/api/documentos/buscar', { params, timeout: 10000 });
        console.log('Documentos response:', response.data);
        documentos.value = response.data.data;
        totalRecords.value = response.data.total;
    } catch (error) {
        console.error('Error fetching documentos:', error);
        if (error.code === 'ECONNABORTED') {
            toast.add({ severity: 'error', summary: 'Timeout', detail: 'La solicitud tardó demasiado', life: 3000 });
        } else {
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar los documentos: ' + error.message, life: 3000 });
        }
    } finally {
        console.log('Finished fetchDocumentos');
        loading.value = false;
    }
};

const onPage = (event) => {
    fetchDocumentos(event);
};

const getPdfPath = (documento) => {
    if (documento.usa_versiones_documento && documento.ultima_version) {
        return documento.ultima_version.archivo_path;
    }
    return documento.archivo_path_documento;
};

const openPdf = (documento) => {
    const path = getPdfPath(documento);
    if (path) {
        // Usar la lógica de DocumentoIndex.vue
        let url = '#';
        if (path.startsWith('http://') || path.startsWith('https://')) {
            url = path;
        } else {
            url = route('documento.mostrar', { path: path });
        }

        if (url !== '#') {
            window.open(url, '_blank');
        }
    }
};

const openAnexosModal = (documento) => {
    selectedDoc.value = documento;
    if (anexosModalInstance) {
        anexosModalInstance.show();
    }
};

const closeAnexosModal = () => {
    if (anexosModalInstance) {
        anexosModalInstance.hide();
    }
};

const getAnexoUrl = (anexo) => {
    if (!anexo.da_archivo_ruta) return '#';
    
    // Si la ruta ya es completa (http), retornarla
    if (anexo.da_archivo_ruta.startsWith('http')) return anexo.da_archivo_ruta;
    
    // Los anexos se guardan en el disco 'public', por lo que son accesibles vía /storage/
    return `/storage/${anexo.da_archivo_ruta}`;
};

const showRelated = (documento) => {
    openAnexosModal(documento);
};

const getSeverity = (documento) => {
    return 'success';
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('es-PE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

onMounted(() => {
    fetchTiposDocumento();

    // Check URL params for initial filters using Vue Router
    if (currentRoute.query.buscar_proceso) {
        filters.buscar_proceso = currentRoute.query.buscar_proceso;
    }

    if (anexosModalRef.value) {
        anexosModalInstance = new Modal(anexosModalRef.value, {
            backdrop: 'static',
            keyboard: false
        });
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
/* Estilo Profesional para Tarjetas y Controles */
.card-documento {
    background-color: #fff;
    border: 1px solid #e4ebf3;
    border-radius: .4rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
    transition: transform .2s ease-in-out, box-shadow .2s ease-in-out;
}

.card-documento:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, .08);
}

.card-documento .card-title {
    color: #343a40;
    font-weight: 600;
}

/* Modal Estilos Profesionales */
.bg-light-danger {
    background-color: #fff5f5;
}

.shadow-inner {
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
}

.anexo-item {
    background-color: #fff;
    border-color: #f0f0f0 !important;
}

.anexo-item:hover {
    border-color: #f5c2c7 !important;
    background-color: #fffafa;
}

.transition-all {
    transition: all 0.2s ease-in-out;
}

.hover-shadow:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
}

.smallest {
    font-size: 0.75rem;
}

.badge-dark-grey {
    background-color: #6c757d;
    border-radius: 4px;
}

.anexo-title {
    font-size: 1.05rem;
    letter-spacing: -0.01em;
}

.uppercase {
    text-transform: uppercase;
}

/* Iconos de cambio de vista */
.view-options a {
    color: #adb5bd;
    font-size: 1.2rem;
    transition: color .2s ease;
}

.view-options a:hover,
.view-options a.active {
    color: #c82333;
    /* Rojo oscuro para estado activo/hover */
}

/* Botones de íconos cuadrados */
.btn-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    padding: 0;
}

#filter-form {
    transition: opacity 0.5s ease-in-out;
}

/* Custom styles for PrimeVue MultiSelect to match Bootstrap inputs */
:deep(.custom-multiselect) {
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    padding: 0;
    font-size: 13px;
}

:deep(.custom-multiselect .p-multiselect-label) {
    padding: 0.375rem 0.75rem;
    font-size: 13px;
    line-height: 1.5;
}

:deep(.custom-multiselect .p-multiselect-label.p-placeholder) {
    font-size: 13px;
}

:deep(.p-multiselect-panel .p-multiselect-items .p-multiselect-item) {
    font-size: 11px;
    padding: 0.25rem 0.5rem;
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

:deep(.custom-multiselect:not(.p-disabled):hover) {
    border-color: #b3b7bb;
}

:deep(.custom-multiselect:not(.p-disabled).p-focus) {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* List Item Styles */
.document-list-item {
    transition: background-color 0.2s;
}

.document-list-item:hover {
    background-color: #f8f9fa;
}

.border-bottom-divider {
    border-bottom: 1px solid #d6d6d6;
}

:deep(.anexos-dialog-profesional .p-dialog-footer) {
    padding: 1rem 1.5rem;
    border-top: 1px solid #f0f0f0;
}

.badge-danger-soft {
    background-color: #fff5f5;
    color: #dc3545;
}

.font-weight-medium {
    font-weight: 500;
}

.align-middle {
    vertical-align: middle !important;
}

/* Ensure the action buttons are consistent pills with premium UX */
.btn-action-pill {
    border-radius: 50px !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    height: 34px !important;
    padding: 0 1.25rem !important;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    border-width: 1.5px !important;
    background: transparent !important;
}

:deep(.btn-action-pill.p-button-outlined.p-button-danger) {
    color: #dc3545 !important;
    border-color: #dc3545 !important;
}

:deep(.btn-action-pill.p-button-outlined.p-button-danger:hover) {
    background: #dc3545 !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3) !important;
    transform: translateY(-1px);
}

:deep(.btn-action-pill.p-button-outlined.p-button-secondary) {
    color: #6c757d !important;
    border-color: #6c757d !important;
}

:deep(.btn-action-pill.p-button-outlined.p-button-secondary:hover) {
    background: #6c757d !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3) !important;
    transform: translateY(-1px);
}
</style>
