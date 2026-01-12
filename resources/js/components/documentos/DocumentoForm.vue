<template>
  <div class="text-left mb-4">
    
    <div v-if="documentoStore.loading" class="loading-spinner w-100 text-center my-5">
      <div class="spinner-border text-danger" role="status">
        <span class="sr-only">Cargando...</span>
      </div>
    </div>
    <div v-else>
      <div class="header-container">
      <h6 class="mb-0 d-flex align-items-center"> 
        <span class="text-dark">{{ documentoStore.documentoForm.cod_documento? 'Información del Documento': 'Nuevo Documento' }}</span>
        <span class="mx-2 text-secondary">
          <i class="fas fa-chevron-right fa-xs"></i>
        </span>
        <span class="text-dark">{{ documentoStore.documentoForm.cod_documento || '' }}</span>
      </h6>
    </div>     
      <form id="documento-form" @submit.prevent="saveForm">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group small">
              <label for="tipo_documento_id" class="form-label text-danger font-weight-bold">Tipo Documento</label>
              <select id="tipo_documento_id" v-model="documentoStore.documentoForm.tipo_documento_id"
                class="form-control" :class="{ 'is-invalid': documentoStore.errors.tipo_documento_id }" required
                data-toggle="tooltip" data-placement="top" title="Clasifica el tipo de documento.">
                <option value="">Seleccione Tipo Documento...</option>
                <option v-for="tipo in documentoStore.tipoDocumento" :key="tipo.id" :value="tipo.id">
                  {{ tipo.td_nombre }}
                </option>
              </select>
              <div class="invalid-feedback">{{ documentoStore.errors.tipo_documento_id ?
                documentoStore.errors.tipo_documento_id[0] : '' }}</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group small">
              <label for="fuente_documento" class="form-label text-danger font-weight-bold">Fuente</label>
              <select id="fuente_documento" v-model="documentoStore.documentoForm.fuente_documento" class="form-control"
                data-toggle="tooltip" data-placement="top" title="Fuente del documento."
                :disabled="documentoStore.isLMDE">
                <option value="">Seleccione</option>
                <option value="interno">Interno</option>
                <option value="externo">Externo</option>
              </select>
              <div class="invalid-feedback">{{ documentoStore.errors.fuente_documento ?
                documentoStore.errors.fuente_documento[0] : '' }}</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group small">
              <label for="estado_documento" class="form-label text-danger font-weight-bold">Estado</label>
              <select id="estado_documento" v-model="documentoStore.documentoForm.estado_documento" class="form-control"
                data-toggle="tooltip" data-placement="top" title="Define el estado actual del documento.">
                <option value="vigente">Vigente</option>
                <option value="derogado">Derogado</option>
              </select>
              <div class="invalid-feedback">{{ documentoStore.errors.estado_documento ?
                documentoStore.errors.estado_documento[0] : '' }}</div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group small">
              <label for="cod_documento" class="form-label text-danger font-weight-bold">Código Documento</label>
              <input type="text" id="cod_documento" v-model="documentoStore.documentoForm.cod_documento" maxlength="13"
                placeholder="Ej. D-01-01" class="form-control" required data-toggle="tooltip" data-placement="top"
                title="Código único para identificar el documento."
                :class="{ 'is-invalid': documentoStore.errors.cod_documento }" />
              <div class="invalid-feedback">{{ documentoStore.errors.cod_documento ?
                documentoStore.errors.cod_documento[0] : '' }}</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group small">
              <label for="frecuencia_revision_documento" class="form-label text-danger font-weight-bold">Frecuencia de
                revisión</label>
              <select id="frecuencia_revision_documento"
                v-model="documentoStore.documentoForm.frecuencia_revision_documento" class="form-control"
                data-toggle="tooltip" data-placement="top" title="Frecuencia de revisión del documento.">
                <option value="Trimestral">Trimestral</option>
                <option value="Semestral">Semestral</option>
                <option value="Anual">Anual</option>
                <option value="Bianual">Bianual</option>
              </select>
              <div class="invalid-feedback">{{ documentoStore.errors.frecuencia_revision_documento ?
                documentoStore.errors.frecuencia_revision_documento[0] : '' }}</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group small">
              <label for="usa_versiones_documento" class="form-label text-danger font-weight-bold">Usa versiones</label>
              <select id="usa_versiones_documento" v-model="documentoStore.documentoForm.usa_versiones_documento"
                class="form-control" data-toggle="tooltip" data-placement="top"
                title="Define si el documento usa versiones." :disabled="documentoStore.isLMDE">
                <option value="1">Si</option>
                <option value="0">No</option>
              </select>
              <div class="invalid-feedback">{{ documentoStore.errors.usa_versiones_documento ?
                documentoStore.errors.usa_versiones_documento[0] : '' }}</div>
            </div>
          </div>
        </div>

        <div class="row mt-2">
          <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="nombre_documento" class="form-label text-danger font-weight-bold">Nombre del Documento</label>
              <small class="text-muted">{{ documentoStore.documentoForm.nombre_documento?.length || 0 }}/255</small>
            </div>
            <textarea id="nombre_documento" v-model="documentoStore.documentoForm.nombre_documento" maxlength="255"
              placeholder="Escribe el nombre completo del documento." rows="2" class="form-control" required
              data-toggle="tooltip" data-placement="top"
              title="Escribe el nombre del documento en no más de 255 caracteres."
              :class="{ 'is-invalid': documentoStore.errors.nombre_documento }"></textarea>
            <div class="invalid-feedback">{{ documentoStore.errors.nombre_documento ?
              documentoStore.errors.nombre_documento[0] : '' }}</div>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="resumen_documento" class="form-label text-danger font-weight-bold">Resumen</label>
              <div class="d-flex align-items-center">
                  <button type="button" class="btn btn-sm btn-outline-info mr-2"
                      @click="improveDescription" :disabled="improvingDescription || !documentoStore.documentoForm.resumen_documento"
                      title="Mejorar redacción con IA">
                      <i class="fas fa-magic" :class="{ 'fa-spin': improvingDescription }"></i>
                      <span v-if="improvingDescription"> Mejorando...</span>
                  </button>
                  <small class="text-muted">{{ documentoStore.documentoForm.resumen_documento?.length || 0 }}/400</small>
              </div>
            </div>
            <textarea id="resumen_documento" v-model="documentoStore.documentoForm.resumen_documento" maxlength="400"
              placeholder="Describe el resumen del documento." rows="4" class="form-control" required
              data-toggle="tooltip" data-placement="top"
              title="Describe el resumen del documento en no más de 400 caracteres."
              :class="{ 'is-invalid': documentoStore.errors.resumen_documento }"></textarea>
            <div class="invalid-feedback">{{ documentoStore.errors.resumen_documento ?
              documentoStore.errors.resumen_documento[0] : '' }}</div>
          </div>
        </div>
        <div class="row mt-3" v-if="documentoStore.documentoForm.usa_versiones_documento != '1'">
          <div class="col-md-6">
            <div class="form-group small">
              <label for="instrumento_aprueba_documento">Instrumento que aprueba</label>
              <input type="text" v-model="documentoStore.documentoForm.instrumento_aprueba_documento"
                id="instrumento_aprueba_documento" class="form-control" placeholder="Ej. Resolución"
                data-toggle="tooltip" data-placement="top"
                title="Documento o instrumento legal que aprueba el documento."
                :class="{ 'is-invalid': documentoStore.errors.instrumento_aprueba_documento }">
              <div class="invalid-feedback">{{ documentoStore.errors.instrumento_aprueba_documento ?
                documentoStore.errors.instrumento_aprueba_documento[0] : '' }}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group small">
              <label for="fecha_aprobacion_documento">Fecha de aprobación</label>
              <input type="date" v-model="documentoStore.documentoForm.fecha_aprobacion_documento"
                id="fecha_aprobacion_documento" class="form-control" data-toggle="tooltip" data-placement="top"
                title="Fecha en la que el documento fue aprobado."
                :class="{ 'is-invalid': documentoStore.errors.fecha_aprobacion_documento }">
              <div class="invalid-feedback">{{ documentoStore.errors.fecha_aprobacion_documento ?
                documentoStore.errors.fecha_aprobacion_documento[0] : '' }}</div>
            </div>
          </div>
        </div>

        <div class="row mt-2" v-if="documentoStore.documentoForm.usa_versiones_documento == 0">
          <div class="col-md-12">
            <div class="form-group small">
              <label class="form-label text-danger font-weight-bold">Vínculo del Documento</label>
              <div class="mb-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" id="option_file" value="file"
                    v-model="documentoStore.documentoForm.origen_documento">
                  <label class="form-check-label" for="option_file">Subir Archivo</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" id="option_url" value="url"
                    v-model="documentoStore.documentoForm.origen_documento">
                  <label class="form-check-label" for="option_url">Usar URL</label>
                </div>
              </div>

              <div v-if="documentoStore.documentoForm.origen_documento === 'file'">
                <div
                  v-if="documentoStore.documentoForm.archivo_path_documento && documentoStore.documentoForm.origen_documento === 'file'">
                  <div class="file-display-container">
                    <span class="file-info-text text-truncate">
                      <i class="fas fa-file-alt mr-2 text-danger"></i>
                      Documento actual: <a :href="fileUrl" target="_blank" class="font-weight-bold text-danger">{{
                        fileName }}</a>
                    </span>
                    <button type="button" class="btn btn-dark btn-sm ml-2" @click="showFileUploader = true"
                      data-toggle="tooltip" data-placement="top" title="Reemplazar archivo">
                      <i class="fas fa-sync-alt"></i>
                    </button>
                  </div>
                </div>

                <div v-if="!documentoStore.documentoForm.archivo_path_documento || showFileUploader">
                  <div class="input-group">
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" id="archivo" ref="archivoInput" @change="onFileChange"
                        :class="{ 'is-invalid': documentoStore.errors.archivo_path_documento }" style="cursor: pointer;">
                      <label class="custom-file-label" for="archivo" data-browse="Examinar">
                         {{ fileName || 'Seleccionar archivo...' }}
                      </label>
                    </div>
                  </div>
                  <div class="invalid-feedback d-block" v-if="documentoStore.errors.archivo_path_documento">
                    {{ documentoStore.errors.archivo_path_documento[0] }}
                  </div>
                </div>
              </div>
              <div v-else-if="documentoStore.documentoForm.origen_documento === 'url'">
                <div class="input-group">
                  <input type="url" id="archivo_path_documento"
                    v-model="documentoStore.documentoForm.archivo_path_documento" class="form-control"
                    placeholder="Ej: http://documentos.miempresa.com/doc.pdf" :class="{ 'is-invalid': isUrlInvalid }">
                  <div class="input-group-append">
                    <button type="button" class="btn btn-outline-secondary btn-sm" @click="handleValidateUrl"
                      :disabled="!documentoStore.documentoForm.archivo_path_documento || isUrlValidating">
                      <span v-if="isUrlValidating" class="spinner-border spinner-border-sm" role="status"
                        aria-hidden="true"></span>
                      <span v-else>Validar URL</span>
                    </button>
                  </div>
                </div>
                <small v-if="validationMessage" :class="validationMessageClass">{{ validationMessage }}</small>
              </div>
            </div>
          </div>
        </div>
        <div class="text-muted mt-2">
          <small><span class="text-danger font-weight-bold">(*)</span> Es obligatorio completar los campos.</small>
        </div>
        <div class="modal-footer justify-content-center w-100">
          <button type="submit" class="btn btn-danger btn-sm" :disabled="documentoStore.loading">
            <span v-if="documentoStore.loading" class="spinner-border spinner-border-sm" role="status"
              aria-hidden="true"></span>
            {{ documentoStore.isEditing ? 'Actualizar' : 'Guardar' }}
          </button>
          <button type="button" class="btn btn-secondary btn-sm" @click="documentoStore.closeModal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useDocumentoStore } from '@/stores/documentoStore';

const documentoStore = useDocumentoStore();


const isUrlValidating = ref(false);
const isUrlInvalid = ref(false);
const validationMessage = ref('');
const validationMessageClass = ref('');
const showFileUploader = ref(false);
const improvingDescription = ref(false);

const improveDescription = async () => {
    if (!documentoStore.documentoForm.resumen_documento) return;
    
    improvingDescription.value = true;
    try {
        const improved = await documentoStore.improveDocumentSummary(documentoStore.documentoForm.resumen_documento);
        documentoStore.documentoForm.resumen_documento = improved;
    } catch (error) {
        console.error('Error improving description:', error);
        // Could add a toast notification here
    } finally {
        improvingDescription.value = false;
    }
};

// Método para manejar el cambio de archivo
const onFileChange = (e) => {
  documentoStore.documentoForm.archivo_path_documento = e.target.files[0];
};

const fileName = computed(() => {
  const file = documentoStore.documentoForm.archivo_path_documento;
  if (file) {
    if (file instanceof File) {
      return file.name;
    }
    // Asumiendo que la ruta del archivo tiene el nombre al final
    return typeof file === 'string' ? file.split('/').pop() : '';
  }
  return '';
});

const fileUrl = computed(() => {
  if (documentoStore.documentoForm.archivo_path_documento) {
    return documentoStore.documentoForm.archivo_path_documento;
  }
  return '#';
});


// Método para guardar el formulario
const saveForm = async () => {
  // Si el origen del documento es una URL, actualiza el estado de validez
  if (documentoStore.documentoForm.origen_documento === 'url') {
    documentoStore.documentoForm.enlace_valido = !isUrlInvalid.value ? 1 : 0;
  }
  // Llama a la acción del store pasando el tipo de formulario 'general'
  await documentoStore.saveDocumento('general');
};


// Método para validar la URL
const handleValidateUrl = async () => {
  isUrlValidating.value = true;
  isUrlInvalid.value = false; // Reinicia el estado de validación visual
  validationMessage.value = ''; // Limpia el mensaje de error

  try {
    const isValid = await documentoStore.validateUrl();

    if (isValid) {
      isUrlInvalid.value = false;
      validationMessage.value = '¡URL válida y accesible!';
      validationMessageClass.value = 'text-success';
    } else {
      isUrlInvalid.value = true; // Marca como inválido
      validationMessage.value = 'La URL no es válida. Por favor, revísela.';
      validationMessageClass.value = 'text-danger';
    }
  } catch (error) {
    isUrlInvalid.value = true; // Marca como inválido
    validationMessage.value = 'Ocurrió un error al validar la URL.';
    validationMessageClass.value = 'text-danger';
  } finally {
    isUrlValidating.value = false;
  }
};
watch(() => documentoStore.documentoForm.archivo_path_documento, (newVal) => {
  // Solo reinicia el estado de validación si el valor cambia
  isUrlInvalid.value = false;
  validationMessage.value = ''; // Limpia el mensaje, no lo establece a un error
  validationMessageClass.value = '';
});
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