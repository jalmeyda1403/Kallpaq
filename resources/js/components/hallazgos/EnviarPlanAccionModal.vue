<template>
  <div class="modal fade" :class="{ 'show': visible }" :style="{ display: visible ? 'block' : 'none' }" tabindex="-1"
    role="dialog" data-backdrop="static" data-keyboard="false" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">
            <i class="fas fa-file-contract mr-2"></i>
            Enviar Plan de Acción
          </h5>
          <button type="button" class="close text-white" @click="cerrarModal">
            <span class="text-white">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Importante:</strong> Debe adjuntar el plan de acción firmado en formato PDF para enviarlo al
            sistema.
          </div>

          <form @submit.prevent="enviarPlan">
            <div class="form-group">
              <label for="archivoPDF" class="font-weight-bold text-muted">Adjuntar Plan de Acción Firmado</label>
              <div class="custom-file">
                <input type="file" class="custom-file-input" id="archivoPDF" @change="onFileChange" accept=".pdf"
                  required>
                <label class="custom-file-label" for="archivoPDF" :class="{ 'text-muted': !archivoSeleccionado }">
                  {{ archivoSeleccionado ? archivoSeleccionado.name : 'Seleccionar archivo PDF...' }}
                </label>
              </div>
              <small class="form-text text-muted">Formato permitido: PDF. Tamaño máximo: 10MB.</small>
            </div>

            <div class="form-group" v-if="archivoSeleccionado">
              <label class="font-weight-bold text-muted">Resumen del Archivo</label>
              <div class="card bg-light">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <i class="fas fa-file-pdf text-danger mr-2"></i>
                      <span class="font-weight-bold">{{ archivoSeleccionado.name }}</span>
                    </div>
                    <div>
                      <small class="text-muted">{{ formatFileSize(archivoSeleccionado.size) }}</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="cerrarModal">
            <i class="fas fa-times mr-1"></i>Cancelar
          </button>
          <button type="button" class="btn btn-danger" @click="enviarPlan"
            :disabled="!archivoSeleccionado || estaEnviando">
            <i class="fas fa-paper-plane mr-1" v-if="!estaEnviando"></i>
            <i class="fas fa-spinner fa-spin mr-1" v-else></i>
            {{ estaEnviando ? 'Enviando...' : 'Enviar Plan' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
  visible: {
    type: Boolean,
    required: true
  },
  hallazgoId: {
    type: [Number, String],
    required: true
  }
});

const emit = defineEmits(['cerrar', 'plan-enviado']);

const archivoSeleccionado = ref(null);
const estaEnviando = ref(false);

const onFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    // Validar que sea un PDF
    if (file.type !== 'application/pdf') {
      alert('Por favor seleccione un archivo PDF válido.');
      return;
    }

    // Validar tamaño (máximo 10MB)
    if (file.size > 10 * 1024 * 1024) {
      alert('El archivo es demasiado grande. El tamaño máximo es de 10MB.');
      return;
    }

    archivoSeleccionado.value = file;
  }
};

const cerrarModal = () => {
  archivoSeleccionado.value = null;
  emit('cerrar');
};

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const enviarPlan = async () => {
  if (!archivoSeleccionado.value) {
    alert('Por favor seleccione un archivo PDF para enviar.');
    return;
  }

  estaEnviando.value = true;

  const formData = new FormData();
  formData.append('archivo', archivoSeleccionado.value);
  formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

  try {
    // Primero subimos el archivo
    const uploadResponse = await axios.post(`/hallazgos/${props.hallazgoId}/adjuntos`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });

    // Luego actualizamos el estado del hallazgo a "aprobado"
    const response = await axios.post(`/hallazgos/${props.hallazgoId}/aprobar`);

    // Emitir evento de éxito
    emit('plan-enviado');

    // Mensaje de éxito
    alert('Plan de acción enviado y hallazgo aprobado exitosamente.');

    // Cerrar modal
    cerrarModal();
  } catch (error) {
    console.error('Error al enviar el plan de acción:', error);
    let mensajeError = 'Hubo un problema al enviar el plan de acción.';

    if (error.response?.data?.message) {
      mensajeError = error.response.data.message;
    } else if (error.response?.status === 422) {
      mensajeError = 'Archivo no válido. Verifique el formato y tamaño.';
    }

    alert(mensajeError);
  } finally {
    estaEnviando.value = false;
  }
};
</script>

<style scoped>
.custom-file-label {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>