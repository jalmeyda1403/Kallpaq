<template>
  <div class="modal fade" id="requerimientoAvanceModal" tabindex="-1" aria-labelledby="requerimientoAvanceModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="requerimientoAvanceModalLabel">Registro de Avance del Requerimiento</h5>
          <button type="button" class="close text-white" @click="closeModal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body position-relative">
          <div v-if="isReadOnly && requerimientoEstado === 'desestimado'" class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="fas fa-info-circle mr-2"></i>
            <span class="mb-0 small">
              Requerimiento Desestimado el {{ formatDate(requerimiento.updated_at) }}.
              Comentario: {{ requerimiento.comentario }}.
              <a :href="getDismissalFileUrl()" target="_blank" class="alert-link ms-2">Ver Documento</a>
            </span>
          </div>

          <div v-if="isReadOnly && requerimientoEstado === 'atendido'" class="alert alert-info d-flex align-items-center" role="alert">
            <i class="fas fa-info-circle mr-2"></i>
            <span class="mb-0 small">
              Requerimiento Finalizado el {{ formatDate(requerimiento.fecha_fin) }}.
            </span>
          </div>

          <!-- Loading Overlay -->
          <div v-if="isLoading" class="loading-overlay">
            <div class="spinner-border text-primary" role="status">
              <span class="sr-only">Cargando...</span>
            </div>
          </div>

          <table class="table table-sm table-bordered small mb-0 align-middle">
            <thead class="thead-light text-center">
              <tr>
                <th style="width: 45%">Avance</th>
                <th>Comentarios</th>
              </tr>
            </thead>
            <tbody class="align-middle">
              <tr v-for="(info, campo) in etapas" :key="campo">
                <td>
                  <div class="form-check">
                    <input class="form-check-input bg-primary border-primary" type="checkbox"
                      v-model="estadoAvance[campo]" :id="campo" :disabled="isReadOnly">
                    <label class="form-check-label font-weight-bold" :for="campo">
                      {{ info.titulo }} ({{ info.peso }}%)
                      <div class="text-muted small mb-0">{{ info.descripcion }}</div>
                    </label>
                  </div>
                </td>
                <td>
                  <textarea v-model="comentarios[campo]" rows="2" class="form-control form-control-sm w-100"
                    style="resize: vertical;" placeholder="Comentario..." :disabled="isReadOnly"></textarea>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <div class="form-group mt-4 mb-0">
                    <label><strong>Avance Registrado</strong></label>
                    <div class="d-flex align-items-center">
                      <div class="flex-fill pr-2">
                        <div class="progress" style="height: 24px;">
                          <div class="progress-bar bg-success" role="progressbar"
                            :style="{ width: avanceRegistrado + '%' }">
                            {{ avanceRegistrado }}%
                          </div>
                        </div>
                      </div>
                      <div>
                        <button class="btn btn-warning btn-sm ml-2" @click="openEvidenciasModal">
                          <i class="fas fa-folder-open"></i> Evidencias
                        </button>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="modal-footer d-flex justify-content-between">
          <div>
            <button v-if="!isReadOnly" type="button" class="btn btn-danger btn-sm"
              @click="toggleDismissalForm">Desestimar</button>
            <button v-if="!isReadOnly" type="button" class="btn btn-success btn-sm ml-1"
              @click="finalizarRequerimiento">Finalizar</button>
          </div>
          <div>
            <button type="button" class="btn btn-secondary btn-sm" @click="closeModal">Cerrar</button>
            <button v-if="!isReadOnly" type="button" class="btn btn-primary btn-sm ml-1" @click="guardarAvance">Guardar
              Avance</button>
          </div>
        </div>
        <div class="collapse p-3" id="desestimarForm" :class="{ 'show': showDismissalForm }">
          <hr>
          <p class="mb-3 small text-muted">Adjunte el documento de desestimación y un comentario para justificar la
            acción.</p>
          <div class="mb-3">
            <label for="desestimacion-comentario" class="form-label">Comentario</label>
            <textarea class="form-control" id="desestimacion-comentario" v-model="dismissalComment" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Documento de Desestimación</label>
            <div class="drop-area" :class="{ 'is-dragging': isDraggingDismissal }" @dragover.prevent="dragoverDismissal"
              @dragleave.prevent="dragleaveDismissal" @drop.prevent="dropDismissal" @click="openDismissalFileInput">
              <div class="text-center">
                <i class="fas fa-cloud-upload-alt fa-3x text-secondary"></i>
                <p class="mt-2">Arrastra y suelta un archivo aquí, o haz clic para seleccionar uno.</p>
              </div>
              <input type="file" ref="dismissalFileInput" class="d-none" @change="handleDismissalFileSelected">
            </div>
            <div v-if="dismissalFile" class="mt-2">
              <strong>Archivo seleccionado:</strong> {{ dismissalFile.name }}
            </div>
          </div>
          <button type="button" class="btn btn-warning" @click="confirmarDesestimacion"
            :disabled="!dismissalComment || !dismissalFile">
            Confirmar Desestimación
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { Modal } from 'bootstrap';
import { route } from 'ziggy-js';
import { Ziggy } from '../../ziggy';


export default {
  props: {
    requerimientoId: {
      type: Number,
      default: null,
    },
  },
  data() {
    return {
      isLoading: false,
      modal: null,
      currentRequerimientoId: null,
      etapas: {
        levantamiento: { titulo: 'Levantamiento de Información', descripcion: 'Reuniones y recopilación de datos iniciales.', peso: 15 },
        contexto: { titulo: 'Análisis de Contexto', descripcion: 'Análisis del entorno y partes interesadas.', peso: 15 },
        caracterizacion: { titulo: 'Caracterización del Proceso', descripcion: 'Modelado y documentación del proceso.', peso: 25 },
        formatos: { titulo: 'Creación de Formatos', descripcion: 'Diseño y creación de formatos y plantillas.', peso: 20 },
        revision_interna: { titulo: 'Revisión Interna', descripcion: 'Revisión por el equipo de calidad.', peso: 10 },
        revision_tecnica: { titulo: 'Revisión Técnica', descripcion: 'Revisión por el área técnica.', peso: 5 },
        firma: { titulo: 'Firma de Documentos', descripcion: 'Firma de la documentación final.', peso: 5 },
        publicacion: { titulo: 'Publicación', descripcion: 'Publicación en el portal de la organización.', peso: 5 },
      },
      estadoAvance: {},
      comentarios: {},
      showDismissalForm: false,
      dismissalComment: '',
      dismissalFile: null,
      isDraggingDismissal: false,
      requerimientoEstado: null,
      requerimiento: {},
    };
  },
  computed: {
    isReadOnly() {
      return ['desestimado', 'atendido'].includes(this.requerimientoEstado);
    },
    avanceRegistrado() {
      let total = 0;
      for (const campo in this.estadoAvance) {
        if (this.estadoAvance[campo]) {
          total += this.etapas[campo].peso;
        }
      }
      return total;
    },
  },
  methods: {
    openModal() {
      this.fetchData();
      this.showDismissalForm = false;
      this.modal.show();
    },
    closeModal() {
      this.modal.hide();
    },
    fetchData() {
      if (!this.currentRequerimientoId) return;
      this.isLoading = true;
      axios.get(route('requerimientos.show', { id: this.currentRequerimientoId }, false, Ziggy))
        .then(response => {
          this.requerimiento = response.data;
          this.requerimientoEstado = this.requerimiento.estado;
          const avance = this.requerimiento.avance;
          if (avance) {
            this.estadoAvance = {
              levantamiento: !!avance.levantamiento,
              contexto: !!avance.contexto,
              caracterizacion: !!avance.caracterizacion,
              formatos: !!avance.formatos,
              revision_interna: !!avance.revision_interna,
              revision_tecnica: !!avance.revision_tecnica,
              firma: !!avance.firma,
              publicacion: !!avance.publicacion,
            };
            this.comentarios = {
              levantamiento: avance.comentario_levantamiento,
              contexto: avance.comentario_contexto,
              caracterizacion: avance.comentario_caracterizacion,
              formatos: avance.comentario_formatos,
              revision_interna: avance.comentario_revision_interna,
              revision_tecnica: avance.comentario_revision_tecnica,
              firma: avance.comentario_firma,
              publicacion: avance.comentario_publicacion,
            };
          } else {
            // Clear UI if no avance record
            this.estadoAvance = {};
            this.comentarios = {};
          }
        })
        .catch(error => {
          console.error('Error al cargar los datos del requerimiento:', error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    getDismissalFileUrl() {
      if (this.requerimiento.ruta_archivo_desistimacion) {
        return `/storage/${this.requerimiento.ruta_archivo_desistimacion}`;
      }
      return '#';
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      const date = new Date(dateString);
      return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
    },
    guardarAvance() {
      const data = {
        ...this.estadoAvance,
        comentario_levantamiento: this.comentarios.levantamiento,
        comentario_contexto: this.comentarios.contexto,
        comentario_caracterizacion: this.comentarios.caracterizacion,
        comentario_formatos: this.comentarios.formatos,
        comentario_revision_interna: this.comentarios.revision_interna,
        comentario_revision_tecnica: this.comentarios.revision_tecnica,
        comentario_firma: this.comentarios.firma,
        comentario_publicacion: this.comentarios.publicacion,
        avance_registrado: this.avanceRegistrado,
      };

      axios.post(route('requerimientos.guardarAvance', { id: this.currentRequerimientoId }, false, Ziggy), data)
        .then(response => {
          this.closeModal();
          this.$emit('avance-guardado');
        })
        .catch(error => {
          console.error('Error al guardar el avance:', error);
        });
    },
    openEvidenciasModal() {
      document.dispatchEvent(new CustomEvent('mostrarEvidenciasModal', {
        detail: {
          requerimientoId: this.currentRequerimientoId,
          isReadOnly: this.isReadOnly
        }
      }));
    },
    toggleDismissalForm() {
      this.showDismissalForm = !this.showDismissalForm;
      if (!this.showDismissalForm) {
        this.dismissalComment = '';
        this.dismissalFile = null;
      }
    },
    dragoverDismissal() {
      this.isDraggingDismissal = true;
    },
    dragleaveDismissal() {
      this.isDraggingDismissal = false;
    },
    dropDismissal(event) {
      this.isDraggingDismissal = false;
      const file = event.dataTransfer.files[0];
      this.handleDismissalFileSelected({ target: { files: [file] } });
    },
    openDismissalFileInput() {
      this.$refs.dismissalFileInput.click();
    },
    handleDismissalFileSelected(event) {
      this.dismissalFile = event.target.files[0];
    },
    confirmarDesestimacion() {
      if (!this.dismissalComment || !this.dismissalFile) {
        alert('Por favor, ingrese un comentario y adjunte un documento para desestimar.');
        return;
      }

      const formData = new FormData();
      formData.append('comentario', this.dismissalComment);
      formData.append('file', this.dismissalFile);

      axios.post(route('requerimientos.desestimar', { id: this.currentRequerimientoId }, false, Ziggy), formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })
        .then(() => {
          this.closeModal();
          this.$emit('avance-guardado');
        })
        .catch(error => {
          console.error('Error al desestimar el requerimiento:', error);
        });
    },
    finalizarRequerimiento() {
      if (this.avanceRegistrado < 100) {
        alert('El requerimiento no puede ser finalizado hasta que el avance sea del 100%.');
        return;
      }

      if (confirm('¿Está seguro de que desea finalizar este requerimiento?')) {
        axios.post(route('requerimientos.finalizar', { id: this.currentRequerimientoId }, false, Ziggy))
          .then(() => {
            this.closeModal();
            this.$emit('avance-guardado');
          })
          .catch(error => {
            if (error.response && error.response.data && error.response.data.message) {
              alert(error.response.data.message);
            } else {
              console.error('Error al finalizar el requerimiento:', error);
            }
          });
      }
    },
  },
  mounted() {
    this.modal = new Modal(this.$el);
    this.handleOpenModal = (event) => {
      // Prevenir múltiples aperturas
      if (this.modal._isShown) return;
      this.currentRequerimientoId = event.detail.id;
      this.requerimientoEstado = event.detail.estado;
      this.openModal();
    };
    document.addEventListener('abrirAvanceRequerimientoModal', this.handleOpenModal);
  },
  beforeUnmount() {
    document.removeEventListener('abrirAvanceRequerimientoModal', this.handleOpenModal);
    if (this.modal) {
      this.modal.dispose();
    }
  },
};
</script>

<style scoped>
.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(255, 255, 255, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
}
</style>

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
