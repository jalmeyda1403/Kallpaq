<template>
    <div>
        <div class="header-container">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">{{ documentoStore.documentoForm.cod_documento ? 'Versiones del Documento' :
                    'Nuevo Documento' }}</span>
                <span class="mx-2 text-secondary">
                    <i class="fas fa-chevron-right fa-xs"></i>
                </span>
                <span class="text-dark">{{ documentoStore.documentoForm.cod_documento || '' }}</span>
            </h6>
        </div>
        <div class="text-left mb-4">
            <h6 class="mb-1" style="font-weight: bold;">
                GESTIÓN DE VERSIONES
            </h6>
            <p class="mb-3 text-muted" style="font-size: 0.875rem;">
                Asociar versiones al documento permite un control de cambios y un registro histórico de las
                modificaciones.
            </p>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom-0 d-flex align-items-center py-3">
                <h5 class="card-title font-weight-bold mb-0 text-dark">
                    Listado de Versiones
                </h5>
                <div class="ml-auto d-flex align-items-center">
                    <div class="custom-control custom-switch mr-3">
                        <input type="checkbox" class="custom-control-input" id="showTrashedVersions" v-model="showTrashed" @change="loadVersiones">
                        <label class="custom-control-label small text-secondary" for="showTrashedVersions">Ver Eliminados</label>
                    </div>
                    <button class="btn btn-outline-danger btn-sm shadow-sm" @click="openNuevaVersionModal"
                            :disabled="!documentoStore.documentoForm.id || showTrashed" style="border-radius: 20px;">
                        <i class="fas fa-plus mr-1"></i> Nueva Versión
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <DataTable :value="documentoStore.versiones" :loading="documentoStore.loadingVersiones"
                           responsiveLayout="scroll" class="p-datatable-sm p-datatable-hover" :rowHover="true" stripedRows>
                    <template #empty>
                         <div class="text-center py-5 text-muted">
                            <i class="fas fa-code-branch fa-3x mb-3 text-secondary opacity-50"></i>
                            <p v-if="showTrashed">No hay versiones eliminadas.</p>
                            <p v-else>No hay versiones registradas.</p>
                        </div>
                    </template>

                    <Column field="version" header="Versión" sortable style="width: 100px;">
                        <template #body="slotProps">
                            <span class="badge badge-pill badge-danger shadow-sm px-3 py-1">v{{ slotProps.data.version }}</span>
                        </template>
                    </Column>
                    <Column field="fecha_publicacion" header="F. Publicación" sortable style="width: 120px;"></Column>
                    <Column field="instrumento_aprueba" header="Instrumento" sortable></Column>
                    <Column field="fecha_aprobacion" header="F. Aprobación" sortable style="width: 120px;">
                         <template #body="slotProps">
                            {{ slotProps.data.fecha_aprobacion || '-' }}
                        </template>
                    </Column>
                    <Column field="control_cambios" header="Control de Cambios">
                         <template #body="slotProps">
                            <small class="text-muted d-block text-truncate" style="max-width: 250px;" :title="slotProps.data.control_cambios">
                                {{ slotProps.data.control_cambios || '-' }}
                            </small>
                        </template>
                    </Column>
                    
                    <Column header="Archivo" class="text-center" style="width: 100px;">
                        <template #body="slotProps">
                            <a :href="getFileUrl(slotProps.data)" target="_blank"
                               class="btn btn-outline-danger btn-sm border-0 rounded-circle"
                               data-toggle="tooltip" title="Ver archivo">
                                <i class="fas fa-file-pdf fa-lg"></i>
                            </a>
                        </template>
                    </Column>

                    <Column header="Acciones" class="text-center" style="width: 120px;">
                        <template #body="slotProps">
                            <div v-if="!showTrashed" class="btn-group" role="group">
                                <button class="btn btn-warning btn-sm mr-1" @click="editVersion(slotProps.data)"
                                        data-toggle="tooltip" title="Editar">
                                    <i class="pi pi-pencil text-white"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" @click="deleteVersion(slotProps.data.id)"
                                        data-toggle="tooltip" title="Eliminar">
                                    <i class="pi pi-trash"></i>
                                </button>
                            </div>
                            <div v-else>
                                <button class="btn btn-success btn-sm shadow-sm" @click="restoreVersion(slotProps.data.id)"
                                        data-toggle="tooltip" title="Restaurar">
                                    <i class="fas fa-trash-restore mr-1"></i> Restaurar
                                </button>
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <!-- Modal Nueva Versión / Editar (Bootstrap Standard) -->
        <div class="modal fade" id="versionModal" tabindex="-1" role="dialog" aria-labelledby="versionModalLabel"
            aria-hidden="true" ref="versionModalRef">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title font-weight-bold" id="versionModalLabel">
                            <i :class="documentoStore.isEditingVersion ? 'fas fa-sync-alt' : 'fas fa-plus-circle'"></i>
                            {{ documentoStore.isEditingVersion ? 'Editar Versión' : 'Nueva Versión' }}
                        </h5>
                        <button type="button" class="close text-white" @click="closeModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-light">
                        <form @submit.prevent="saveVersion">
                            <div class="card p-3 border-0 shadow-sm bg-white mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3 text-left small">
                                            <label for="dv_version" class="font-weight-bold text-danger">Versión <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="dv_version"
                                                v-model="documentoStore.versionForm.version" required readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                       <div class="form-group mb-3 text-left small">
                                            <label for="dv_instrumento_aprueba" class="font-weight-bold text-dark">Instrumento que Aprueba</label>
                                            <input type="text" class="form-control" id="dv_instrumento_aprueba" placeholder="Ej: R.D. N° 001-2025"
                                                v-model="documentoStore.versionForm.instrumento_aprueba">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3 text-left small">
                                            <label for="dv_fecha_aprobacion" class="font-weight-bold text-dark">Fecha de Aprobación</label>
                                            <input type="date" class="form-control" id="dv_fecha_aprobacion"
                                                v-model="documentoStore.versionForm.fecha_aprobacion">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3 text-left small">
                                            <label for="dv_fecha_vigencia" class="font-weight-bold text-danger">Fecha de Publicación <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="dv_fecha_vigencia"
                                                v-model="documentoStore.versionForm.fecha_publicacion" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card p-3 border-0 shadow-sm bg-white">
                                <h6 class="font-weight-bold small text-secondary mb-3 border-bottom pb-2">
                                    Detalles del Archivo
                                </h6>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3 text-left small">
                                            <label for="dv_archivo_path" class="font-weight-bold text-danger">
                                                Archivo
                                                <span v-if="!documentoStore.isEditingVersion" class="text-danger">*</span>
                                                <span v-if="documentoStore.isEditingVersion" class="badge badge-secondary ml-1">Opcional</span>
                                            </label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="dv_archivo_path" @change="handleFileUpload" accept=".pdf,.doc,.docx,.xls,.xlsx">
                                                <label class="custom-file-label text-truncate" for="dv_archivo_path" data-browse="Examinar">
                                                    {{ selectedFileName || 'Seleccionar archivo...' }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-3 text-left small">
                                            <label for="dv_control_cambios" class="font-weight-bold text-dark">Control de Cambios</label>
                                            <textarea class="form-control" id="dv_control_cambios"
                                                v-model="documentoStore.versionForm.control_cambios" rows="2" placeholder="Describa los cambios realizados..."></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                         <div class="form-group mb-0 form-check text-left small">
                                            <input type="checkbox" class="form-check-input" id="dv_enlace_valido"
                                                v-model="documentoStore.versionForm.enlace_valido">
                                            <label class="form-check-label" for="dv_enlace_valido">Enlace Válido</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer justify-content-center w-100 p-0 pt-3 border-0">
                                <button type="submit" class="btn btn-danger btn-sm" :disabled="documentoStore.loading">
                                    <span v-if="documentoStore.loading" class="spinner-border spinner-border-sm mr-1" role="status"
                                        aria-hidden="true"></span>
                                    {{ documentoStore.isEditingVersion ? 'Actualizar Versión' : 'Guardar Versión' }}
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm ms-2" @click="closeModal">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
import { useDocumentoStore } from '@/stores/documentoStore';
import { Modal } from 'bootstrap';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Swal from 'sweetalert2';
import { route } from 'ziggy-js';

export default {
    components: {
        DataTable,
        Column
    },
    setup() {
        const documentoStore = useDocumentoStore();
        return { documentoStore };
    },
    data() {
        return {
            modalInstance: null,
            showTrashed: false,
            selectedFileName: ''
        };
    },
    mounted() {
        this.loadVersiones();
        const modalElement = document.getElementById('versionModal');
        if (modalElement) {
            this.modalInstance = new Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });
        }
    },
    methods: {
        loadVersiones() {
             if (this.documentoStore.documentoForm.id) {
                this.documentoStore.fetchVersiones(this.documentoStore.documentoForm.id, this.showTrashed);
            }
        },
        getFileUrl(version) {
            if (!version?.archivo_path) return '#';
            return route('documento.mostrar', { path: version.archivo_path });
        },
        openNuevaVersionModal() {
            this.documentoStore.openVersionForm();
            this.selectedFileName = '';
            if (this.modalInstance) this.modalInstance.show();
        },
        async saveVersion() {
            try {
                await this.documentoStore.saveVersion();
                if (this.modalInstance) this.modalInstance.hide();
            } catch (error) {
                console.error("Error saving version:", error);
            }
        },
        editVersion(version) {
            this.documentoStore.editVersion(version);
            if (this.modalInstance) this.modalInstance.show();
        },
        async deleteVersion(id) {
            const result = await Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción enviará la versión a la papelera.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });

            if (result.isConfirmed) {
                try {
                    await this.documentoStore.deleteVersion(id);
                     this.loadVersiones();
                } catch (error) {
                    // Error handled in store
                }
            }
        },
        async restoreVersion(id) {
             const result = await Swal.fire({
                title: '¿Restaurar versión?',
                text: "La versión volverá a estar activa.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, restaurar',
                cancelButtonText: 'Cancelar'
            });

            if (result.isConfirmed) {
                try {
                    await this.documentoStore.restoreVersion(id);
                    this.loadVersiones();
                } catch (error) {
                    console.error(error);
                }
            }
        },
        handleFileUpload(event) {
             const file = event.target.files[0];
            if (file) {
                this.documentoStore.versionForm.archivo = file;
                this.selectedFileName = file.name;
            } else {
                 this.documentoStore.versionForm.archivo = null;
                 this.selectedFileName = '';
            }
        },
        closeModal() {
            if (this.modalInstance) this.modalInstance.hide();
            this.documentoStore.closeVersionForm();
        }
    }
};
</script>


<style scoped>
.custom-file-label::after {
    content: "Examinar";
}
/* Estilos para el overlay del spinner */
.form-overlay-container {
    position: relative;
    min-height: 200px;
    /* Asegura que el contenedor tenga una altura mínima */
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
</style>
