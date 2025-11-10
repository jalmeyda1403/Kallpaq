<template>
  <div class="modal fade" id="requerimientoEvaluacionModal" ref="modal" tabindex="-1"
    aria-labelledby="requerimientoEvaluacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content shadow-lg">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="requerimientoEvaluacionModalLabel"><i
              class="fas fa-clipboard-check mr-2"></i>Evaluación de Complejidad</h5>
          <button type="button" class="close text-white" @click="closeModal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-4 position-relative">
          <!-- Loading Overlay -->
          <div v-if="isFetchingData" class="loading-overlay">
            <div class="spinner-border text-success" role="status">
              <span class="sr-only">Cargando...</span>
            </div>
          </div>
          <!-- 1. Mensaje de advertencia (si la evaluación no está permitida) -->
          <div v-if="!isEvaluacionPermitida" class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle mr-2 bg-warning"></i>
            <span class="mb-0 small">Solo se puede evaluar requerimientos con estado "Aprobado" o "Evaluado".</span>
          </div>
          <!-- 2. Mensajes de Estado (Éxito/Error al guardar) -->
          <div v-if="mensaje">
            <div class="alert d-flex align-items-center alert-custom"
              :class="isGuardadoExitoso ? 'alert-custom-success' : 'alert-custom-danger'" role="alert">
              <i class="fas fa-2x mr-3" :class="isGuardadoExitoso ? 'fa-check-circle' : 'fa-times-circle'"></i>
              <span>{{ mensaje }}</span>
            </div>
          </div>
          <!-- Formulario de Evaluación -->
          <div>
            <h6><strong>Instrucciones:</strong></h6>
            <p class="small">
              Selecciona una opción para cada criterio; el puntaje total y nivel de complejidad se calcularán
              automáticamente.
            </p>
            <table class="table table-bordered small mb-0">
              <thead class="thead-light text-center">
                <tr>
                  <th>Criterio</th>
                  <th>Baja (1)</th>
                  <th>Media (2)</th>
                  <th>Alta (3)</th>
                  <th>Muy Alta (4)</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>Cantidad de actividades del proceso</strong><br></br>
                    <span class="small text-muted"> Evalúa cuántas actividades principales componen el
                      requerimiento</span>
                  </td>
                  <td v-for="i in 4" :key="'act' + i" class="text-center align-middle">
                    <input type="radio" v-model.number="form.actividades" :value="i" :disabled="!isEvaluacionPermitida">
                  </td>
                </tr>
                <tr>
                  <td><strong>Cantidad de unidades orgánicas involucradas</strong><br></br>
                    <span class="small text-muted">Evalúa cuántas unidades orgánicas participan en el
                      requerimiento</span>
                  </td>

                  <td v-for="i in 4" :key="'area' + i" class="text-center align-middle">
                    <input type="radio" v-model.number="form.areas" :value="i" :disabled="!isEvaluacionPermitida">
                  </td>
                </tr>
                <tr>
                  <td><strong>Requisitos normativos aplicables</strong><br></br>
                    <span class="small text-muted"> Mide la cantidad y rigurosidad de normas que afectan el proceso
                    </span>
                  </td>
                  <td v-for="i in 4" :key="'req' + i" class="text-center align-middle">
                    <input type="radio" v-model.number="form.requisitos" :value="i" :disabled="!isEvaluacionPermitida">
                  </td>
                </tr>
                <tr>
                  <td><strong>Nivel de documentación requerida</strong><br></br>
                    <span class="small text-muted"> Evalúa la profundidad y complejidad de los documentos a elaborar o
                      modificar</span>
                  </td>
                  <td v-for="i in 4" :key="'doc' + i" class="text-center align-middle">
                    <input type="radio" v-model.number="form.documentacion" :value="i"
                      :disabled="!isEvaluacionPermitida">
                  </td>
                </tr>
                <tr>
                  <td><strong>Impacto del procedimiento</strong><br></br>
                    <span class="small text-muted">Evalúa el nivel de influencia del requerimiento respecto a otros procedimientos</span>
                  </td>
                  <td v-for="i in 4" :key="'imp' + i" class="text-center align-middle">
                    <input type="radio" v-model.number="form.impacto" :value="i" :disabled="!isEvaluacionPermitida">
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- 4. Sección de Resultados -->
          <div class="mt-3">
            <div class="alert alert-info d-flex align-items-center">
              <i class="fas fa-info-circle fa-lg mr-2"></i>
              <div>
                <p class="mb-0 small" v-if="puntajeTotal === 0 && !evaluacionExistente">Complete el formulario para
                  obtener el
                  puntaje.</p>
                <p class="mb-0 small" v-else>
                  Nivel de Complejidad: <strong>{{ nivelComplejidad || '-' }}</strong> | Puntaje total: <strong>{{
                    puntajeTotal }}</strong>
                </p>
              </div>
            </div>
          </div>

          <!-- Documentos Adjuntos -->
          <transition name="fade">
            <div v-if="uploadedFiles.length > 0" class="mt-4">
              <h6 class="mb-3"><i class="fas fa-paperclip mr-2"></i><strong>Documentos Adjuntos</strong></h6>
              <div class="list-group">
                <a v-for="(file, index) in uploadedFiles" :key="index" :href="file.path" target="_blank"
                  class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                  <div>
                    <i class="fas fa-file-alt text-secondary mr-2"></i>
                    <span>{{ file.name }}</span>
                  </div>
                  <span class="badge badge-danger badge-pill"><i class="fas fa-download mr-1"></i> Ver</span>
                </a>
              </div>
            </div>
          </transition>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary bt-sm" @click="closeModal">Cerrar</button>
          <button v-if="isEvaluacionPermitida" type="button btn-sm" @click="guardarEvaluacion" class="btn btn-danger"
            :disabled="puntajeTotal < 5 || isLoading">
            <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span v-if="isLoading">Guardando...</span>
            <span v-else>Guardar</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.alert-custom {
  border: 1px solid transparent;
  border-left-width: 5px;
  background-color: #fff;
}
.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(255, 255, 255, 0.25);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
}
</style>
<script>
import axios from 'axios';
import { Modal } from 'bootstrap';
import { route } from 'ziggy-js';
import { Ziggy } from '../../ziggy';
export default {
  data() {
    return {
      modalInstance: null,
      requerimiento_id: null,
      form: {
        actividades: null,
        areas: null,
        requisitos: null,
        documentacion: null,
        impacto: null,
      },
      puntajeTotal: 0,
      nivelComplejidad: '',
      evaluacionExistente: false,
      isLoading: false, // Para el guardado
      isFetchingData: false, // Para la carga inicial
      mensaje: '',
      isGuardadoExitoso: null,
      isEvaluacionPermitida: false,
      uploadedFiles: [],
    };
  },
  watch: {
    form: {
      handler() {
        this.calcularComplejidad();
      },
      deep: true,
    },
  },
  methods: {
    mostrarEvaluacion(requerimiento) {
      this.resetCampos();
      this.requerimiento_id = requerimiento.id;
      this.isFetchingData = true;
      const estadosPermitidos = ['aprobado', 'evaluado'];
      this.isEvaluacionPermitida = estadosPermitidos.includes(requerimiento.estado.toLowerCase());

      const evaluacionPromise = axios.get(route('requerimiento.evaluacion', { id: requerimiento.id }, false, Ziggy));
      const requerimientoPromise = axios.get(route('requerimientos.show', { id: requerimiento.id }, false, Ziggy));

      Promise.all([evaluacionPromise, requerimientoPromise])
        .then(([evaluacionResponse, requerimientoResponse]) => {
          // Procesar datos de evaluación
          if (evaluacionResponse.data && evaluacionResponse.data.id) {
            this.evaluacionExistente = true;
            this.form.actividades = evaluacionResponse.data.num_actividades;
            this.form.areas = evaluacionResponse.data.num_areas;
            this.form.requisitos = evaluacionResponse.data.num_requisitos;
            this.form.documentacion = evaluacionResponse.data.nivel_documentacion;
            this.form.impacto = evaluacionResponse.data.impacto_requerimiento;
          }

          // Procesar datos de requerimiento (archivos)
          if (requerimientoResponse.data && requerimientoResponse.data.ruta_archivo_requerimiento) {
            try {
              const files = JSON.parse(requerimientoResponse.data.ruta_archivo_requerimiento);
              if (Array.isArray(files)) {
                this.uploadedFiles = files.map(file => ({
                  name: file.name,
                  path: file.path.startsWith('/storage/') ? file.path : '/storage/' + file.path
                }));
              }
            } catch (error) {
              console.error('Error parsing ruta_archivo_requerimiento:', error);
            }
          }
        })
        .catch(error => {
          console.error('Error al cargar los datos del requerimiento:', error);
          // Opcional: mostrar un mensaje de error al usuario
        })
        .finally(() => {
          this.isFetchingData = false;
        });


      if (this.modalInstance) {
        this.modalInstance.show();
      } else {
        console.error('¡Error! La instancia del modal es nula.');
      }
    },
    calcularComplejidad() {
      const total = Object.values(this.form).reduce((acc, value) => acc + (Number(value) || 0), 0);
      this.puntajeTotal = total;
      if (total >= 5 && total <= 8) {
        this.nivelComplejidad = 'baja';
      } else if (total >= 9 && total <= 12) {
        this.nivelComplejidad = 'media';
      } else if (total >= 13 && total <= 16) {
        this.nivelComplejidad = 'alta';
      } else if (total >= 17) {
        this.nivelComplejidad = 'muy alta';
      } else {
        this.nivelComplejidad = '';
      }
    },
    guardarEvaluacion() {
      this.isLoading = true;
      this.mensaje = '';
      this.isGuardadoExitoso = null;
      axios.post(route('requerimiento.grabarEvaluacion', { id: this.requerimiento_id }, false, Ziggy), {
        ...this.form,
        complejidad_valor: this.puntajeTotal,
        complejidad_nivel: this.nivelComplejidad,
      })
        .then(response => {
          this.mensaje = response.data.message || 'Evaluación guardada con éxito.';
          this.isGuardadoExitoso = true;
          this.$emit('evaluacion-guardada');
        })
        .catch(error => {
          this.mensaje = error.response?.data?.message || 'Ocurrió un error inesperado.';
          this.isGuardadoExitoso = false;
          console.error(error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    resetCampos() {
      this.form.actividades = null;
      this.form.areas = null;
      this.form.requisitos = null;
      this.form.documentacion = null;
      this.form.impacto = null;
      this.mensaje = '';
      this.isGuardadoExitoso = null;
      this.isLoading = false;
      this.evaluacionExistente = false;
      this.puntajeTotal = 0;
      this.uploadedFiles = [];
    },
    closeModal() {
      this.modalInstance.hide();
    },
  },
  mounted() {
    this.modalInstance = new Modal(this.$refs.modal);
    document.addEventListener('mostrarEvaluacion', (event) => {
      this.mostrarEvaluacion(event.detail);
    });
  },
};
</script>