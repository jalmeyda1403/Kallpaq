<template>
    <div>
        <div class="header-container">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">{{ documentoStore.documentoForm.cod_documento ? 'Anexos del Documento' :
                    'Nuevo Documento' }}</span>
                <span class="mx-2 text-secondary">
                    <i class="fas fa-chevron-right fa-xs"></i>
                </span>
                <span class="text-dark">{{ documentoStore.documentoForm.cod_documento || '' }}</span>
            </h6>
        </div>

        <div class="text-left mb-4">
            <h6 class="mb-1" style="font-weight: bold;">
                DOCUMENTOS ANEXOS
            </h6>
            <p class="mb-3 text-muted" style="font-size: 0.875rem;">
                Este módulo permite gestionar los documentos anexos (formatos, matrices, infografías, etc.) que dependen
                de este documento.
            </p>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom-0 d-flex align-items-center py-3">
                <h5 class="card-title font-weight-bold mb-0 text-dark">
                    Listado de Anexos
                </h5>
                <div class="ml-auto d-flex align-items-center">
                    <div class="custom-control custom-switch mr-3">
                        <input type="checkbox" class="custom-control-input" id="showTrashedSwitch" v-model="showTrashed" @change="loadAnexos">
                        <label class="custom-control-label small text-secondary" for="showTrashedSwitch">Ver Eliminados</label>
                    </div>
                    <button class="btn btn-danger btn-sm shadow-sm" @click="openCreateModal"
                        :disabled="!documentoStore.documentoForm.id || showTrashed">
                        <i class="fas fa-plus"></i> Nuevo Anexo
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <DataTable :value="anexos" :loading="loading" responsiveLayout="scroll" class="p-datatable-sm p-datatable-hover"
                    :rowHover="true" stripedRows>
                    <template #empty>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                            <p v-if="showTrashed">No hay anexos eliminados.</p>
                            <p v-else>No hay anexos registrados para este documento.</p>
                        </div>
                    </template>
                    <Column field="da_codigo" header="Código" sortable>
                        <template #body="slotProps">
                            <span class="font-weight-bold text-dark">{{ slotProps.data.da_codigo }}</span>
                        </template>
                    </Column>
                    <Column field="da_tipo" header="Tipo" sortable>
                        <template #body="slotProps">
                            <span :class="getBadgeClass(slotProps.data.da_tipo)">
                                {{ slotProps.data.da_tipo }}
                            </span>
                        </template>
                    </Column>
                    <Column field="da_nombre" header="Nombre" sortable></Column>
                    <Column field="da_fecha_publicacion" header="Fecha P.">
                         <template #body="slotProps">
                            <small>{{ slotProps.data.da_fecha_publicacion || '-' }}</small>
                        </template>
                    </Column>
                    <Column field="da_version" header="Versión" class="text-center">
                        <template #body="slotProps">
                            <span class="badge badge-pill badge-light border">v{{ slotProps.data.da_version }}</span>
                        </template>
                    </Column>
                    <Column header="Archivo" class="text-center">
                        <template #body="slotProps">
                            <a :href="getFileUrl(slotProps.data.da_archivo_ruta)" target="_blank"
                                class="btn btn-outline-danger btn-sm border-0 rounded-circle" 
                                data-toggle="tooltip" title="Ver / Descargar Archivo">
                                <i class="fas fa-file-pdf fa-lg"></i>
                            </a>
                        </template>
                    </Column>
                    <Column field="da_observacion" header="Última Obs.">
                        <template #body="slotProps">
                            <small class="text-muted d-block text-truncate" style="max-width: 150px;" :title="slotProps.data.da_observacion">
                                {{ slotProps.data.da_observacion || '-' }}
                            </small>
                        </template>
                    </Column>
                    <Column header="Acciones" class="text-center" style="width: 140px;">
                        <template #body="slotProps">
                            <!-- Helper template to group buttons -->
                            <div v-if="!showTrashed">
                                <button class="btn btn-warning btn-sm mr-1" @click="openEditModal(slotProps.data)"
                                    title="Nueva Versión / Editar">
                                    <i class="pi pi-pencil text-white"></i>
                                </button>
                                <button class="btn btn-info btn-sm mr-1" @click="openHistorySidebar(slotProps.data)"
                                    title="Ver Historial">
                                    <i class="pi pi-clock text-white"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" @click="deleteAnexo(slotProps.data.id)"
                                    title="Eliminar">
                                    <i class="pi pi-trash"></i>
                                </button>
                            </div>
                            <div v-else>
                                 <button class="btn btn-info btn-sm mr-1" @click="openHistorySidebar(slotProps.data)"
                                    title="Ver Historial">
                                    <i class="pi pi-clock text-white"></i>
                                </button>
                                <button class="btn btn-success btn-sm" @click="restoreAnexo(slotProps.data.id)"
                                    title="Restaurar">
                                    <i class="fas fa-trash-restore"></i>
                                </button>
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <!-- Modal Crear/Editar Anexo (Bootstrap Standard) -->
        <div class="modal fade" id="anexoModal" tabindex="-1" role="dialog" aria-labelledby="anexoModalLabel"
            aria-hidden="true" ref="anexoModalRef">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title font-weight-bold" id="anexoModalLabel">
                            <i :class="editMode ? 'fas fa-sync-alt' : 'fas fa-plus-circle'"></i>
                            {{ editMode ? 'Actualizar Anexo / Nueva Versión' : 'Nuevo Anexo' }}
                        </h5>
                        <button type="button" class="close text-white" @click="closeModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-light">
                        <form @submit.prevent="saveAnexo">
                            <div class="card p-3 border-0 shadow-sm bg-white mb-3">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="form-group small">
                                            <label for="da_nombre" class="font-weight-bold text-danger">Nombre del Anexo <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="da_nombre" v-model="form.da_nombre" class="form-control"
                                                :disabled="editMode"
                                                required placeholder="Ej: Matriz de Identificación de Riesgos" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                       <div class="form-group small">
                                            <label for="da_fecha_publicacion" class="font-weight-bold text-danger">F. Publicación <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" id="da_fecha_publicacion" v-model="form.da_fecha_publicacion" class="form-control" required />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                       <div class="form-group small">
                                            <label for="da_tipo" class="font-weight-bold text-danger">Tipo <span
                                                    class="text-danger">*</span></label>
                                            <select id="da_tipo" v-model="form.da_tipo" class="form-control" required :disabled="editMode">
                                                <option value="" disabled>Seleccione...</option>
                                                <option value="FORMATO">FORMATO</option>
                                                <option value="MATRIZ">MATRIZ</option>
                                                <option value="INFOGRAFIA">INFOGRAFÍA</option>
                                                <option value="LISTADO">LISTADO</option>
                                                <option value="OTROS">OTROS</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card p-3 border-0 shadow-sm bg-white">
                                <h6 class="font-weight-bold small text-secondary mb-3 border-bottom pb-2">
                                    {{ editMode ? 'Gestión de Versión' : 'Archivo Inicial' }}
                                </h6>
                                <div class="row">
                                    <div class="col-md-12" v-if="editMode">
                                        <div class="alert alert-warning py-2 px-3 small">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Versión actual: <strong>v{{ currentVersion }}</strong>. 
                                            Al subir un nuevo archivo, se generará la <strong>v{{ parseInt(currentVersion) + 1 }}</strong> automáticamente y la versión anterior pasará al historial.
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group small">
                                            <label for="file" class="font-weight-bold text-danger">
                                                Archivo
                                                <span v-if="!editMode" class="text-danger">*</span>
                                                <span v-if="editMode" class="badge badge-secondary ml-1">Opcional</span>
                                            </label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="file"
                                                    @change="onFileChange" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.png">
                                                <label class="custom-file-label text-truncate" for="file" data-browse="Examinar">
                                                    {{ selectedFileName || 'Seleccionar archivo...' }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group small mb-0">
                                            <label for="da_observacion" class="font-weight-bold text-danger">Control de Cambios</label>
                                            <textarea id="da_observacion" v-model="form.da_observacion" class="form-control"
                                                rows="2"
                                                placeholder="Describa brevemente los cambios o el motivo de la versión..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-center w-100">
                         <button type="button" class="btn btn-danger btn-sm" @click="saveAnexo" :disabled="saving">
                            <span v-if="saving" class="spinner-border spinner-border-sm mr-1" role="status"
                                aria-hidden="true"></span>
                            {{ saving ? 'Guardando...' : 'Guardar' }}
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" @click="closeModal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Sidebar (Custom Implementation) -->
        <transition name="fade">
            <div v-if="showHistorySidebarRef" class="sidebar-backdrop" @click="closeHistorySidebar"></div>
        </transition>

        <transition name="slide-right">
            <div v-if="showHistorySidebarRef" class="sidebar-panel bg-white shadow-lg">
                <div class="sidebar-header bg-danger text-white d-flex justify-content-between align-items-center p-3">
                    <h5 class="m-0 font-weight-bold">
                        <i class="fas fa-history mr-2"></i>Historial de Versiones
                    </h5>
                    <button type="button" class="close text-white" @click="closeHistorySidebar">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="sidebar-body p-0">
                    <div class="table-responsive">
                         <table class="table table-hover mb-0">
                            <thead class="bg-light text-secondary">
                                <tr>
                                    <th class="border-top-0 border-bottom-0">Nombre</th>
                                    <th class="border-top-0 border-bottom-0 text-center">Version</th>
                                    <th class="border-top-0 border-bottom-0">Fecha Pub.</th>
                                    <th class="border-top-0 border-bottom-0 text-center">Archivo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in historyList" :key="item.id">
                                    <td class="v-middle">
                                        <div class="font-weight-bold text-dark">{{ item.da_nombre }}</div>
                                        <small class="text-muted d-block" v-if="item.da_observacion">
                                            {{ item.da_observacion }}
                                        </small>
                                    </td>
                                    <td class="font-weight-bold text-center v-middle">
                                        <span class="badge badge-light border">v{{ item.da_version }}</span>
                                    </td>
                                    <td class="v-middle">{{ item.da_fecha_publicacion || '-' }}</td>
                                    <td class="text-center v-middle">
                                        <a :href="getFileUrl(item.da_archivo_ruta)" target="_blank"
                                            class="btn btn-sm btn-outline-danger rounded-circle">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr v-if="historyList.length === 0">
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fas fa-history fa-2x mb-3 opacity-50"></i>
                                        <p class="mb-0">No se encontraron versiones anteriores.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from 'vue';
import { useDocumentoStore } from '@/stores/documentoStore';
import { route } from 'ziggy-js';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { useToast } from 'primevue/usetoast';
import { Modal } from 'bootstrap';

const documentoStore = useDocumentoStore();
const toast = useToast();

const anexos = ref([]);
const historyList = ref([]);
const loading = ref(false);
const saving = ref(false);
const editMode = ref(false);
const showTrashed = ref(false);
const currentVersion = ref(1);
const selectedFile = ref(null);
const selectedFileName = ref('');

// Sidebar state
const showHistorySidebarRef = ref(false);

// Bootstrap Modal References
const anexoModalRef = ref(null);
let modalInstance = null;

const form = reactive({
    id: null,
    da_nombre: '',
    da_tipo: '',
    da_fecha_publicacion: '',
    da_observacion: ''
});

const loadAnexos = async () => {
    if (!documentoStore.documentoForm.id) return;
    loading.value = true;
    try {
        const response = await axios.get(route('documento.anexos.index', { 
            documentoId: documentoStore.documentoForm.id
        }), {
            params: { trashed: showTrashed.value ? 1 : 0 }
        });
        anexos.value = response.data;
    } catch (error) {
        console.error("Error loading anexos", error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar los anexos.', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const getBadgeClass = (tipo) => {
    switch(tipo) {
        case 'FORMATO': return 'badge badge-primary px-2 py-1';
        case 'MATRIZ': return 'badge badge-success px-2 py-1';
        case 'INFOGRAFIA': return 'badge badge-info px-2 py-1';
        case 'LISTADO': return 'badge badge-secondary px-2 py-1';
        case 'OTROS': return 'badge badge-dark px-2 py-1';
        default: return 'badge badge-light border px-2 py-1';
    }
};

const getFileUrl = (path) => {
    if (!path) return '#';
    return `/storage/${path}`;
};

const openCreateModal = () => {
    editMode.value = false;
    currentVersion.value = 1;

    form.id = null;
    form.da_nombre = '';
    form.da_tipo = '';
    const today = new Date();
    form.da_fecha_publicacion = today.toISOString().split('T')[0];
    
    form.da_observacion = '';
    
    selectedFile.value = null;
    selectedFileName.value = '';
    
    const fileInput = document.getElementById('file');
    if (fileInput) fileInput.value = '';
    
    modalInstance.show();
};

const openEditModal = (anexo) => {
    editMode.value = true;
    currentVersion.value = anexo.da_version;

    form.id = anexo.id;
    form.da_nombre = anexo.da_nombre;
    form.da_tipo = anexo.da_tipo;
    form.da_fecha_publicacion = anexo.da_fecha_publicacion || new Date().toISOString().split('T')[0];
    form.da_observacion = ''; 
    
    selectedFile.value = null;
    selectedFileName.value = '';
    
    const fileInput = document.getElementById('file');
    if (fileInput) fileInput.value = '';

    modalInstance.show();
};

// Sidebar Logic
const openHistorySidebar = async (anexo) => {
    try {
        const response = await axios.get(route('documento.anexos.history', { codigo: anexo.da_codigo }));
        historyList.value = response.data;
        showHistorySidebarRef.value = true;
    } catch (error) {
        console.error("Error loading history", error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar el historial.', life: 3000 });
    }
};

const closeHistorySidebar = () => {
    showHistorySidebarRef.value = false;
};

const closeModal = () => {
    modalInstance.hide();
};

const onFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        selectedFile.value = file;
        selectedFileName.value = file.name;
    } else {
        selectedFile.value = null;
        selectedFileName.value = '';
    }
};

const saveAnexo = async () => {
    if (!form.da_nombre) {
        toast.add({ severity: 'warn', summary: 'Atención', detail: 'El nombre es obligatorio.', life: 3000 });
        return;
    }
    if (!form.da_tipo) {
        toast.add({ severity: 'warn', summary: 'Atención', detail: 'El tipo es obligatorio.', life: 3000 });
        return;
    }
    if (!form.da_fecha_publicacion) {
        toast.add({ severity: 'warn', summary: 'Atención', detail: 'La fecha de publicación es obligatoria.', life: 3000 });
        return;
    }
    if (!editMode.value && !selectedFile.value) {
        toast.add({ severity: 'warn', summary: 'Atención', detail: 'El archivo es obligatorio para crear.', life: 3000 });
        return;
    }

    saving.value = true;
    const formData = new FormData();
    formData.append('documento_id', documentoStore.documentoForm.id);
    formData.append('da_nombre', form.da_nombre);
    formData.append('da_tipo', form.da_tipo);
    formData.append('da_fecha_publicacion', form.da_fecha_publicacion);
    if (form.da_observacion) formData.append('da_observacion', form.da_observacion);
    if (selectedFile.value) formData.append('file', selectedFile.value);

    try {
        if (editMode.value) {
            await axios.post(route('documento.anexos.update', { id: form.id }), formData);
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Anexo actualizado.', life: 3000 });
        } else {
            await axios.post(route('documento.anexos.store'), formData);
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Anexo creado correctamente.', life: 3000 });
        }
        closeModal();
        loadAnexos();
    } catch (error) {
        console.error("Error saving anexo", error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Ocurrió un error al guardar o verifique los datos.', life: 3000 });
    } finally {
        saving.value = false;
    }
};

const deleteAnexo = async (id) => {
    if (!confirm("¿Está seguro de eliminar este anexo?")) return;

    try {
        await axios.delete(route('documento.anexos.destroy', { id }));
        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Anexo eliminado.', life: 3000 });
        loadAnexos();
    } catch (error) {
        console.error("Error deleting anexo", error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Error al eliminar.', life: 3000 });
    }
};

const restoreAnexo = async (id) => {
    if (!confirm("¿Está seguro de restaurar este anexo?")) return;

    try {
        await axios.post(route('documento.anexos.restore', { id }));
        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Anexo restaurado.', life: 3000 });
        loadAnexos();
    } catch (error) {
        console.error("Error restoring anexo", error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Error al restaurar.', life: 3000 });
    }
};

onMounted(() => {
    loadAnexos();
    if (anexoModalRef.value) {
        modalInstance = new Modal(anexoModalRef.value, {
            backdrop: 'static',
            keyboard: false
        });
    }
});
</script>

<style scoped>
.header-container {
    padding: 0.75rem;
    margin-bottom: 1.5rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #ff851b;
    display: flex;
    align-items: center;
}

.custom-file-label::after {
    content: "Examinar";
}

/* Sidebar Styles */
.sidebar-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0,0,0,0.4);
    z-index: 1040;
}

.sidebar-panel {
    position: fixed;
    top: 0;
    right: 0;
    width: 600px;
    height: 100vh;
    background: #fff;
    z-index: 1050;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

.v-middle {
    vertical-align: middle !important;
}

/* Transitions */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter, .fade-leave-to {
    opacity: 0;
}

.slide-right-enter-active, .slide-right-leave-active {
    transition: transform 0.3s ease-out;
}
.slide-right-enter, .slide-right-leave-to {
    transform: translateX(100%);
}
</style>