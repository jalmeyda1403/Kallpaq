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
        <div v-if="!documentoStore.showVersionForm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0" style="font-weight: bold;">
                    LISTA DE VERSIONES
                </h6>
                <button class="btn btn-danger btn-sm" @click="openNuevaVersionForm">
                    <i class="fas fa-plus"></i> Nueva Versión
                </button>
            </div>
            <div v-if="documentoStore.loadingVersiones" class="text-center">
                <div class="spinner-border text-danger" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>
            <div v-else-if="documentoStore.versiones.length === 0" class="small text-left">
                No hay versiones registradas para este documento.
            </div>
            <div v-else class="table-responsive">
                  <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Versión</th>
                            <th>Control de Cambios</th>
                            <th>Fecha de Aprobación</th>
                            <th>Fecha de Vigencia</th>
                            <th>Enlace</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="version in documentoStore.versiones" :key="version.id">
                            <td>{{ version.dv_version }}</td>
                            <td>{{ version.dv_control_cambios || 'N/A' }}</td>
                            <td>{{ version.dv_fecha_aprobacion || 'N/A' }}</td>
                            <td>{{ version.dv_fecha_vigencia || 'N/A' }}</td>
                            <td>
                                <a v-if="version.dv_archivo_path" :href="version.dv_archivo_path" target="_blank"
                                    class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-secondary" @click="editVersion(version)">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-else>
            <h6 class="mb-3 font-weight-bold">
                {{ documentoStore.isEditingVersion ? 'EDITAR VERSIÓN' : 'NUEVA VERSIÓN' }}
            </h6>
            <form @submit.prevent="saveVersion">
                <div class="form-group mb-3">
                    <label for="dv_version">Versión</label>
                    <input type="text" class="form-control" id="dv_version"
                        v-model="documentoStore.versionForm.dv_version" required>
                </div>
                <div class="form-group mb-3">
                    <label for="dv_archivo_path">Archivo</label>
                    <input type="file" class="form-control" id="dv_archivo_path" @change="handleFileUpload">
                </div>
                <div class="form-group mb-3">
                    <label for="dv_control_cambios">Control de Cambios</label>
                    <textarea class="form-control" id="dv_control_cambios"
                        v-model="documentoStore.versionForm.dv_control_cambios" rows="3"></textarea>
                </div>
                <div class="form-group mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="dv_enlace_valido"
                        v-model="documentoStore.versionForm.dv_enlace_valido">
                    <label class="form-check-label" for="dv_enlace_valido">Enlace Válido</label>
                </div>
                <div class="form-group mb-3">
                    <label for="dv_instrumento_aprueba">Instrumento que Aprueba</label>
                    <input type="text" class="form-control" id="dv_instrumento_aprueba"
                        v-model="documentoStore.versionForm.dv_instrumento_aprueba">
                </div>
                <div class="row">
                    <div class="form-group mb-3 col-md-6">
                        <label for="dv_fecha_aprobacion">Fecha de Aprobación</label>
                        <input type="date" class="form-control" id="dv_fecha_aprobacion"
                            v-model="documentoStore.versionForm.dv_fecha_aprobacion">
                    </div>
                    <div class="form-group mb-3 col-md-6">
                        <label for="dv_fecha_vigencia">Fecha de Vigencia</label>
                        <input type="date" class="form-control" id="dv_fecha_vigencia"
                            v-model="documentoStore.versionForm.dv_fecha_vigencia">
                    </div>
                </div>
                <div class="modal-footer justify-content-center w-100">
                    <button type="button" class="btn btn-secondary me-2" @click="cancelForm">Cancelar</button>
                    <button type="submit" class="btn btn-danger" :disabled="documentoStore.loading">
                        <span v-if="documentoStore.loading" class="spinner-border spinner-border-sm" role="status"
                            aria-hidden="true"></span>
                        {{ documentoStore.isEditingVersion ? 'Actualizar ' : 'Guardar ' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
<script setup>
import { onMounted } from 'vue';
import { useDocumentoStore } from '@/stores/documentoStore';

const documentoStore = useDocumentoStore();

// Llama a la acción para cargar las versiones al montar el componente
onMounted(() => {
    // Aquí es donde corriges el código.

    documentoStore.fetchVersiones(documentoStore.documentoForm.id);

});

const openNuevaVersionForm = () => {
    documentoStore.openVersionForm();
};

const editVersion = (version) => {
    documentoStore.editVersion(version);
};

const cancelForm = () => {
    documentoStore.closeVersionForm();
};

const saveVersion = () => {
    documentoStore.saveVersion();
};

const handleFileUpload = (event) => {
    documentoStore.versionForm.dv_archivo = event.target.files[0];
};

</script>


<style scoped>
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
    /* Fondo semi-transparente */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1050;
    /* Asegura que esté por encima del formulario */
}

/* Estilos de los campos de formulario */
.form-group small {
    font-size: 0.75rem;
}

.form-label.text-danger {
    font-weight: bold;
}

.table th,
.table td {
    font-size: 0.8rem;
    vertical-align: middle;
}

.table td input[type="checkbox"] {
    transform: scale(0.9);
}

.header-container {
    padding: 0.75rem;
    margin-bottom: 1.5rem;
    background-color: #f8f9fa;
    /* Color gris claro de Bootstrap */
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #ff851b;
    /* Esta línea crea la franja naranja */
    display: flex;
    /* Para centrar verticalmente el contenido si es necesario */
    align-items: center;
}
</style>
