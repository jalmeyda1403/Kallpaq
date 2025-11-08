<template>
  <div class="modal fade" id="evidenciasModal" tabindex="-1" aria-labelledby="evidenciasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="evidenciasModalLabel">Evidencias del Requerimiento</h5>
          <button type="button" class="close text-white" @click="closeModal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div v-if="isLoading" class="text-center">
            <div class="spinner-border text-primary" role="status">
              <span class="sr-only">Cargando...</span>
            </div>
          </div>
          <div v-else>
            <div v-if="!readOnly" class="mb-3">
              <div class="drop-area" :class="{ 'is-dragging': isDragging }" @dragover.prevent="dragover"
                @dragleave.prevent="dragleave" @drop.prevent="drop" @click="openFileInput">
                <div class="text-center">
                  <i class="fas fa-cloud-upload-alt fa-3x text-secondary"></i>
                  <p class="mt-2">Arrastra y suelta un archivo aquí, o haz clic para seleccionar uno.</p>
                  <small class="text-muted">Tamaño máximo permitido: 10MB</small>
                </div>
                <input type="file" ref="fileInput" class="d-none" @change="handleFileSelected">
              </div>
            </div>

            <div v-if="selectedFile && !readOnly" class="mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <strong>Archivo seleccionado:</strong> {{ selectedFile.name }} ({{ (selectedFile.size / 1024 /
                  1024).toFixed(2) }} MB)
                </div>
                <button class="btn btn-primary btn-sm" @click="uploadFile" :disabled="isUploading">
                  <span v-if="isUploading" class="spinner-border spinner-border-sm" role="status"
                    aria-hidden="true"></span>
                  <span v-else>Subir</span>
                </button>
              </div>
            </div>

            <div v-if="isUploading" class="progress mb-3">
              <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                :style="{ width: uploadPercentage + '%' }" :aria-valuenow="uploadPercentage" aria-valuemin="0"
                aria-valuemax="100">{{ uploadPercentage }}%</div>
            </div>
            <h6>Evidencias existentes</h6>
            <div class="table-responsive">
              <table class="table table-sm table-striped table-hover">
                <thead>
                  <tr>
                    <th style="width: 10%;">Tipo</th>
                    <th>Nombre</th>
                    <th class="text-center">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(file, index) in files" :key="index">
                    <td class="text-center">
                      <i :class="getFileIcon(file.name)" class="fa-lg"></i>
                    </td>
                    <td>{{ file.name }}</td>
                    <td class="text-center">
                      <a :href="getDownloadUrl(file)" class="btn btn-primary btn-sm" title="Descargar">
                        <i class="fas fa-download"></i>
                      </a>
                      <button v-if="!readOnly" class="btn btn-danger btn-sm ml-2" @click="deleteFile(file)"
                        title="Eliminar">
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                  <tr v-if="!files || files.length === 0">
                    <td colspan="3" class="text-center text-muted">No hay evidencias.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="closeModal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { route } from 'ziggy-js';
import { Ziggy } from '../../ziggy';
import { Modal } from 'bootstrap';

export default {
  props: {
    requerimientoId: {
      type: Number,
      default: null,
    },
    isReadOnly: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      modal: null,
      currentRequerimientoId: null,
      files: [],
      isUploading: false,
      uploadPercentage: 0,
      maxFileSize: 10 * 1024 * 1024, // 10MB in bytes
      isLoading: false,
      readOnly: false,
      isDragging: false,
      selectedFile: null,
    };
  },
  methods: {
    openModal() {
      this.fetchFiles();
      this.modal.show();
    },
    closeModal() {
      this.modal.hide();
    },
    fetchFiles() {
      if (!this.currentRequerimientoId) return;
      this.isLoading = true;
      axios.get(route('requerimientos.evidencias.listar', { id: this.currentRequerimientoId }, false, Ziggy))
        .then(response => {
          this.files = response.data;
        })
        .catch(error => {
          console.error('Error al cargar las evidencias:', error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    dragover() {
      this.isDragging = true;
    },
    dragleave() {
      this.isDragging = false;
    },
    drop(event) {
      this.isDragging = false;
      const file = event.dataTransfer.files[0];
      this.handleFileSelected({ target: { files: [file] } });
    },
    openFileInput() {
      this.$refs.fileInput.click();
    },
    handleFileSelected(event) {
      const file = event.target.files[0];
      if (!file) return;

      if (file.size > this.maxFileSize) {
        alert('El archivo excede el tamaño máximo permitido (10MB).');
        this.$refs.fileInput.value = ''; // Reset file input
        return;
      }

      this.selectedFile = file;
    },
    uploadFile() {
      if (!this.selectedFile) return;

      const formData = new FormData();
      formData.append('file', this.selectedFile);

      this.isUploading = true;
      axios.post(route('requerimientos.evidencias.subir', { id: this.currentRequerimientoId }, false, Ziggy), formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
        onUploadProgress: (progressEvent) => {
          this.uploadPercentage = parseInt(Math.round((progressEvent.loaded * 100) / progressEvent.total));
        },
      })
        .then(response => {
          this.files = response.data;
          this.selectedFile = null;
          this.$refs.fileInput.value = ''; // Reset file input
        })
        .catch(error => {
          console.error('Error al subir la evidencia:', error);
        })
        .finally(() => {
          this.isUploading = false;
          this.uploadPercentage = 0;
        });
    },
    getFileIcon(fileName) {
      const extension = fileName.split('.').pop().toLowerCase();
      switch (extension) {
        case 'pdf':
          return 'fas fa-file-pdf text-danger';
        case 'doc':
        case 'docx':
          return 'fas fa-file-word text-primary';
        case 'xls':
        case 'xlsx':
          return 'fas fa-file-excel text-success';
        case 'ppt':
        case 'pptx':
          return 'fas fa-file-powerpoint text-warning';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
          return 'fas fa-file-image text-info';
        default:
          return 'fas fa-file text-secondary';
      }
    },
    getDownloadUrl(file) {
      return route('requerimientos.evidencias.descargar', { id: this.currentRequerimientoId, file_path: file.path }, false, Ziggy);
    },

    deleteFile(file) {
      axios.delete(route('requerimientos.evidencias.eliminar', { id: this.currentRequerimientoId, file_path: file.path }, false, Ziggy))
        .then(() => {
          this.files = this.files.filter(f => f.path !== file.path);
        })
        .catch(error => {
          console.error('Error al eliminar la evidencia:', error);
        });
    },
  },
  mounted() {
    this.modal = new Modal(this.$el);
    document.addEventListener('mostrarEvidenciasModal', (event) => {
      this.currentRequerimientoId = event.detail.requerimientoId;
      this.readOnly = event.detail.isReadOnly;
      this.openModal();
    });

    this.$el.addEventListener('hidden.bs.modal', () => {
      if (document.getElementById('requerimientoAvanceModal').classList.contains('show')) {
        document.body.classList.add('modal-open');
      }
    });
  },
};
</script>

<style scoped>
.drop-area {
  border: 2px dashed #ccc;
  border-radius: 10px;
  padding: 40px;
  cursor: pointer;
  transition: background-color 0.2s ease-in-out;
}

.drop-area.is-dragging {
  background-color: #f0f0f0;
}
</style>
