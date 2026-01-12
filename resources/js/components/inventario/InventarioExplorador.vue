<template>
    <div class="container-fluid">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom pt-4 pb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-folder-open text-primary mr-2"></i>Explorador de Documentación
                        </h5>
                        <p class="text-muted small mb-0" v-if="parentProcess">
                            {{ parentProcess.cod_proceso }} - {{ parentProcess.proceso_nombre }}
                        </p>
                        <p class="text-muted small mb-0" v-else>Cargando información del proceso...</p>
                    </div>
                    <div>
                        <button class="btn btn-secondary btn-sm shadow-sm" @click="goBack">
                            <i class="fas fa-arrow-left mr-1"></i> Volver al Mapa
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body bg-light">
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando subprocesos y documentación...</p>
                </div>

                <div v-else-if="subProcesos.length === 0" class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3 opacity-50"></i>
                    <h5 class="text-muted">No se encontraron subprocesos para este proceso.</h5>
                </div>

                <div v-else class="accordion" id="accordionSubprocesos">
                    <div v-for="(subproceso, index) in subProcesos" :key="subproceso.id"
                        class="card border-0 mb-3 shadow-sm rounded-lg overflow-hidden">

                        <!-- Process Header (Accordion Trigger) -->
                        <div class="card-header bg-white p-0" :id="'heading-' + subproceso.id">
                            <h2 class="mb-0">
                                <button
                                    class="btn btn-block text-left p-4 d-flex align-items-center focus:outline-none transition-colors hover:bg-gray-50"
                                    type="button" data-toggle="collapse" :data-target="'#collapse-' + subproceso.id"
                                    :aria-expanded="index === 0 ? 'true' : 'false'"
                                    :aria-controls="'collapse-' + subproceso.id" @click="loadDocuments(subproceso)">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-soft mr-3"
                                        style="width: 48px; height: 48px; min-width: 48px;">
                                        <i :class="subproceso.isOpen ? 'fas fa-folder-open' : 'fas fa-folder'"
                                            class="text-primary fa-lg"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="font-weight-bold text-dark mb-1">{{ subproceso.cod_proceso }} - {{
                                            subproceso.proceso_nombre }}</h6>
                                        <span class="badge badge-pill badge-light text-muted border">
                                            <i class="fas fa-layer-group mr-1"></i> Nivel {{ subproceso.proceso_nivel }}
                                        </span>
                                    </div>
                                    <i class="fas fa-chevron-down text-muted transition-transform"></i>
                                </button>
                            </h2>
                        </div>

                        <!-- Process Body (Documents & Subprocesses Level 2) -->
                        <div :id="'collapse-' + subproceso.id" class="collapse" :class="{ 'show': index === 0 }"
                            :aria-labelledby="'heading-' + subproceso.id" data-parent="#accordionSubprocesos">
                            <div class="card-body bg-white border-top">
                                <!-- Level 2 Subprocesses Section -->
                                <div v-if="subproceso.subprocesos_count > 0" class="mb-4">
                                    <h6
                                        class="text-uppercase text-primary font-weight-bold text-xs mb-3 border-bottom pb-2">
                                        <i class="fas fa-sitemap mr-2"></i>Subprocesos (Nivel {{
                                        subproceso.proceso_nivel + 1 }})
                                    </h6>

                                    <div v-if="subproceso.loadingChildren" class="text-center py-2">
                                        <div class="spinner-border spinner-border-sm text-secondary" role="status">
                                        </div>
                                        <span class="ml-2 text-muted small">Cargando subprocesos...</span>
                                    </div>

                                    <div v-else class="accordion" :id="'accordion-lvl2-' + subproceso.id">
                                        <div v-for="child in subproceso.children" :key="child.id"
                                            class="card border mb-2 shadow-sm rounded">
                                            <!-- Level 2 Header -->
                                            <div class="card-header bg-gray-50 p-0" :id="'heading-lvl2-' + child.id">
                                                <h2 class="mb-0">
                                                    <button
                                                        class="btn btn-block text-left p-3 d-flex align-items-center focus:outline-none"
                                                        type="button" data-toggle="collapse"
                                                        :data-target="'#collapse-lvl2-' + child.id"
                                                        aria-expanded="false"
                                                        :aria-controls="'collapse-lvl2-' + child.id"
                                                        @click="loadDocuments(child)">
                                                        <i :class="child.isOpen ? 'fas fa-folder-open' : 'fas fa-folder'"
                                                            class="text-secondary mr-3 ml-1"></i>
                                                        <div class="flex-grow-1">
                                                            <div class="font-weight-bold text-dark text-sm">{{
                                                                child.cod_proceso }} - {{ child.proceso_nombre }}</div>
                                                        </div>
                                                        <i class="fas fa-chevron-down text-muted small"></i>
                                                    </button>
                                                </h2>
                                            </div>

                                            <!-- Level 2 Body (Documents) -->
                                            <div :id="'collapse-lvl2-' + child.id" class="collapse"
                                                :aria-labelledby="'heading-lvl2-' + child.id"
                                                :data-parent="'#accordion-lvl2-' + subproceso.id">
                                                <div class="card-body bg-white p-3">
                                                    <div v-if="child.loadingDocs" class="text-center py-2">
                                                        <div class="spinner-border spinner-border-sm text-secondary"
                                                            role="status"></div>
                                                    </div>
                                                    <div v-else-if="!child.documentos || child.documentos.length === 0"
                                                        class="text-center text-muted small py-2">
                                                        <i class="far fa-file-excel mr-1"></i> No hay documentos.
                                                    </div>
                                                    <div v-else>
                                                        <div v-for="(docs, tipo) in groupDocumentsByType(child.documentos)"
                                                            :key="tipo + child.id" class="mb-3">
                                                            <div
                                                                class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                                                {{ tipo }}</div>
                                                            <ul class="list-unstyled mb-0 pl-3 border-left ml-1">
                                                                <li v-for="doc in docs" :key="doc.id" class="mb-1">
                                                                    <a :href="doc.enlaceActual" target="_blank"
                                                                        class="d-flex align-items-center text-decoration-none text-dark hover:text-primary">
                                                                        <i :class="getDocumentIcon(doc)"
                                                                            class="mr-2 fa-sm"></i>
                                                                        <div class="d-flex align-items-center flex-wrap">
                                                                            <span class="text-sm border-right pr-2 mr-2">{{ doc.codigo }} - {{ doc.nombre }} (v{{ doc.versionActual }})</span>
                                                                            <button v-if="doc.anexos && doc.anexos.length > 0" 
                                                                                    @click.stop.prevent="openAnexosModal(doc)"
                                                                                    class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-pill shadow-xs"
                                                                                    title="Ver Anexos">
                                                                                <i class="fas fa-paperclip mr-1"></i> {{ doc.anexos.length }}
                                                                            </button>
                                                                        </div>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Documents Section for Level 1 -->
                                <div v-if="subproceso.loadingDocs" class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                    <span class="ml-2 text-muted small">Cargando documentos...</span>
                                </div>

                                <div v-else-if="!subproceso.documentos || subproceso.documentos.length === 0"
                                    class="text-center py-4 bg-light rounded border border-dashed">
                                    <p class="text-muted mb-0"><i class="far fa-file-excel mr-2"></i>No hay
                                        documentación asociada.</p>
                                </div>

                                <div v-else>
                                    <!-- Document Categories -->
                                    <div class="row">
                                        <div class="col-12"
                                            v-for="(docs, tipo) in groupDocumentsByType(subproceso.documentos)"
                                            :key="tipo">
                                            <h6
                                                class="text-uppercase text-secondary font-weight-bold text-xs mt-3 mb-2 border-bottom pb-1">
                                                {{ tipo }}
                                            </h6>
                                            <div class="list-group list-group-flush">
                                                <a v-for="doc in docs" :key="doc.id" :href="doc.enlaceActual"
                                                    target="_blank"
                                                    class="list-group-item list-group-item-action border-0 px-2 py-2 rounded mb-1 d-flex align-items-center doc-item">
                                                    <div class="mr-3 text-center" style="width: 30px;">
                                                        <i :class="getDocumentIcon(doc)" class="fa-lg"></i>
                                                    </div>
                                                    <div class="text-truncate">
                                                        <div class="text-dark font-weight-medium text-sm">{{ doc.nombre
                                                            }}</div>
                                                        <div class="text-muted text-xs">
                                                            {{ doc.codigo }} <span class="mx-1">•</span> Ver. {{
                                                                doc.versionActual }}
                                                        </div>
                                                    </div>
                                                    <div class="ml-auto pl-2 d-flex align-items-center">
                                                        <button v-if="doc.anexos && doc.anexos.length > 0" 
                                                                @click.stop.prevent="openAnexosModal(doc)"
                                                                class="btn btn-xs btn-outline-secondary mr-2 py-0 px-2 rounded-pill shadow-xs"
                                                                title="Ver Anexos">
                                                            <i class="fas fa-paperclip mr-1"></i> {{ doc.anexos.length }}
                                                        </button>
                                                        <i class="fas fa-external-link-alt text-muted small"></i>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Anexos Modal -->
        <div class="modal fade" id="anexosModal" tabindex="-1" role="dialog" aria-hidden="true" ref="anexosModalRef">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-dark text-white border-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-white rounded-circle p-2 mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-paperclip text-dark"></i>
                            </div>
                            <div>
                                <h5 class="modal-title font-weight-bold mb-0">Documentos Anexos</h5>
                                <p class="mb-0 small opacity-75" v-if="selectedDoc">{{ selectedDoc.codigo }} - {{ selectedDoc.nombre }}</p>
                            </div>
                        </div>
                        <button type="button" class="close text-white opacity-100" @click="closeAnexosModal" aria-label="Cerrar">
                            <span aria-hidden="true" style="font-size: 1.5rem;">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div v-if="selectedDoc && selectedDoc.anexos && selectedDoc.anexos.length > 0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light text-muted">
                                        <tr>
                                            <th class="border-0 px-4 py-3 text-xs text-uppercase font-weight-bold">Código / Nombre</th>
                                            <th class="border-0 py-3 text-xs text-uppercase font-weight-bold text-center">Versión</th>
                                            <th class="border-0 py-3 text-xs text-uppercase font-weight-bold text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="anexo in selectedDoc.anexos" :key="anexo.id" class="transition-colors">
                                            <td class="px-4 py-3 border-top-0 border-bottom align-middle">
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-3 text-center" style="width: 32px;">
                                                        <i :class="getDocumentIcon({extension: getExtension(anexo.da_archivo_path)})" class="fa-lg"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold text-dark text-sm mb-0">{{ anexo.da_nombre }}</div>
                                                        <div class="text-muted text-xs">{{ anexo.da_codigo }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3 border-top-0 border-bottom align-middle text-center">
                                                <span class="badge badge-pill badge-light border px-3">v{{ anexo.da_version }}</span>
                                            </td>
                                            <td class="py-3 border-top-0 border-bottom align-middle text-center">
                                                <a :href="anexo.da_archivo_path ? `/documento/mostrar/${anexo.da_archivo_path}` : '#'" 
                                                   target="_blank"
                                                   class="btn btn-sm btn-action-compact rounded-pill">
                                                    <i class="fas fa-eye mr-1"></i> Abrir
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div v-else class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3 opacity-25"></i>
                            <h6 class="text-muted">No hay anexos disponibles para este documento.</h6>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light py-3">
                        <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill" @click="closeAnexosModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { route as ziggyRoute } from 'ziggy-js';
import { Modal } from 'bootstrap';

const props = defineProps({
    id: {
        type: [String, Number],
        required: true
    }
});

const route = useRoute();
const router = useRouter();

const parentProcess = ref(null);
const subProcesos = ref([]);
const loading = ref(true);
const selectedDoc = ref(null);
const anexosModalRef = ref(null);
let anexosModalInstance = null;

const fetchParentProcess = async () => {
    try {
        // Fetch specific process. Assuming such endpoint exists or using show
        // 'procesos.show' returns JSON of the process
        const response = await axios.get(`/proceso/${props.id}/show`);
        parentProcess.value = response.data;
    } catch (error) {
        console.error("Error fetching parent process:", error);
    }
};

const fetchSubProcesos = async () => {
    try {
        // Filter by parent_id using the apiIndex endpoint which supports filters
        const response = await axios.get('/api/procesos/index', {
            params: {
                proceso_padre_id: props.id
            }
        });
        // Initialize properties for documents
        subProcesos.value = response.data.map(proc => ({
            ...proc,
            ...proc,
            documentos: [], // Will hold fetched documents
            children: [], // Will hold level 2 processes
            docsLoaded: false,
            loadingDocs: false,
            loadingChildren: false,
            childrenLoaded: false,
            isOpen: false // Initial state
        }));

        // Auto-load documents for the first item if exists
        if (subProcesos.value.length > 0) {
            subProcesos.value[0].isOpen = true; // Open the first one
            loadDocuments(subProcesos.value[0]);
        }

    } catch (error) {
        console.error('Error fetching subprocesos:', error);
    } finally {
        loading.value = false;
    }
};

const loadDocuments = async (subproceso) => {
    // Toggle state: Close others, open this one (or toggle if same)
    // However, Bootstrap data-parent handles the accordion logic visually. 
    // We just need to sync our isOpen state.

    // If we rely on Bootstrap's accordion behavior (which closes others):
    // We should set all others to false and this one to true (if it wasn't already open).
    // Or if clicking an already open one, it closes.

    // Simple logic:
    // 1. If clicking a closed one: Close all others, Open this one.
    // 2. If clicking an open one: Close this one.

    const wasOpen = subproceso.isOpen;

    // Reset all to false
    subProcesos.value.forEach(p => p.isOpen = false);

    // If it wasn't open, now it is. If it was open, it stays false (closed).
    if (!wasOpen) {
        subproceso.isOpen = true;
    }

    // Load children if they exist and haven't been loaded
    if (subproceso.subprocesos_count > 0 && !subproceso.childrenLoaded && !subproceso.loadingChildren) {
        subproceso.loadingChildren = true;
        try {
            const response = await axios.get('/api/procesos/index', {
                params: { proceso_padre_id: subproceso.id }
            });
            subproceso.children = response.data.map(child => ({
                ...child,
                documentos: [],
                docsLoaded: false,
                loadingDocs: false,
                isOpen: false
            }));
            subproceso.childrenLoaded = true;
        } catch (e) {
            console.error('Error loading children', e);
        } finally {
            subproceso.loadingChildren = false;
        }
    }

    if (subproceso.docsLoaded || subproceso.loadingDocs) return;

    subproceso.loadingDocs = true;
    try {
        const response = await axios.get(`/procesos/${subproceso.id}/listardocumentos`);

        // Transform the response to be cleaner if needed
        subproceso.documentos = response.data.map(doc => {
            const lastVersion = doc.ultima_version;
            const filePath = lastVersion ? lastVersion.archivo_path : '';
            
            return {
                id: doc.id,
                codigo: doc.cod_documento, 
                nombre: doc.nombre_documento, 
                tipo: doc.tipo_documento ? doc.tipo_documento.nombre : 'Documento',
                // Access latest version info safely using correct column names from DocumentoVersion model
                versionActual: lastVersion ? lastVersion.version : 'N/A',
                enlaceActual: filePath ? `/documento/mostrar/${filePath}` : '#',
                extension: getExtension(filePath),
                anexos: doc.anexos || []
            };
        });

        subproceso.docsLoaded = true;
    } catch (error) {
        console.error(`Error fetching documents for process ${subproceso.id}:`, error);
    } finally {
        subproceso.loadingDocs = false;
    }
};

const groupDocumentsByType = (docs) => {
    return docs.reduce((groups, doc) => {
        const type = doc.tipo || 'Otros';
        if (!groups[type]) {
            groups[type] = [];
        }
        groups[type].push(doc);
        return groups;
    }, {});
};

const getExtension = (filename) => {
    if (!filename || typeof filename !== 'string') return '';
    const parts = filename.split('.');
    return parts.length > 1 ? parts.pop().toLowerCase() : '';
};

const getDocumentIcon = (doc) => {
    const ext = doc.extension || '';
    if (['pdf'].includes(ext)) return 'fas fa-file-pdf text-danger';
    if (['doc', 'docx'].includes(ext)) return 'fas fa-file-word text-primary';
    if (['xls', 'xlsx'].includes(ext)) return 'fas fa-file-excel text-success';
    if (['ppt', 'pptx'].includes(ext)) return 'fas fa-file-powerpoint text-warning';
    return 'fas fa-file-alt text-secondary';
};

const openAnexosModal = (doc) => {
    selectedDoc.value = doc;
    if (anexosModalInstance) {
        anexosModalInstance.show();
    }
};

const closeAnexosModal = () => {
    if (anexosModalInstance) {
        anexosModalInstance.hide();
    }
};

const goBack = () => {
    router.go(-1); // Simplest way to go back to history state (likely the map map)
};

onMounted(async () => {
    if (anexosModalRef.value) {
        anexosModalInstance = new Modal(anexosModalRef.value, {
            backdrop: 'static',
            keyboard: false
        });
    }
    await Promise.all([fetchParentProcess(), fetchSubProcesos()]);
});

</script>

<style scoped>
.accordion .card-header button[aria-expanded="true"] {
    background-color: #f8f9fa;
    color: #4a5568;
}

.accordion .card-header i.fa-chevron-down {
    transition: transform 0.3s ease;
}

.accordion .card-header button[aria-expanded="true"] i.fa-chevron-down {
    transform: rotate(180deg);
}

.bg-primary-soft {
    background-color: rgba(66, 153, 225, 0.1);
}

.text-xs {
    font-size: 0.75rem;
}

.text-sm {
    font-size: 0.875rem;
}

.doc-item:hover {
    background-color: #f1f5f9 !important;
}

.btn-xs {
    padding: 0.1rem 0.35rem;
    font-size: 0.65rem;
}

.shadow-xs {
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.opacity-75 { opacity: 0.75; }
.opacity-100 { opacity: 1 !important; }

.btn-action-compact {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    color: #495057;
    font-size: 0.75rem;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-action-compact:hover {
    background-color: #e9ecef;
    color: #212529;
    border-color: #adb5bd;
    transform: translateY(-1px);
}

.transition-colors {
    transition: background-color 0.2s ease;
}
</style>
