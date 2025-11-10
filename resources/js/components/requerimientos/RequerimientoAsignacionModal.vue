<template>
  <div class="modal fade" id="requerimientoAsignacionModal" tabindex="-1"
    aria-labelledby="requerimientoAsignacionModalLabel" aria-hidden="true" ref="modal" data-backdrop="static"
    data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content shadow-lg">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="requerimientoAsignacionModalLabel">
            <i class="fas fa-user-plus mr-2"></i>Asignar Especialista
          </h5>
          <button type="button" class="close text-white" @click="closeModal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-4">
          <!-- 1. Mensaje de advertencia (si la asignación no está permitida) -->
          <div v-if="!isAsignacionPermitida">
            <div class="alert alert-warning d-flex" role="alert">
              <i class="fas fa-exclamation-triangle mr-2 bg-warning"></i>
              <span class="mb-0 small">{{ mensaje }}</span>
            </div>
          </div>

          <!-- 2. Mensaje de Éxito (después de guardar) -->
          <div v-else-if="isAsignacionExitosa">
            <div class="alert alert-success d-flex align-items-center" role="alert">
              <i class="fas fa-check-circle fa-2x mr-2"></i>
              <span class="mb-0 small">
                ¡Asignación exitosa! <br>
                El requerimiento ha sido asignado a {{ especialistaSeleccionado.nombres }}.
              </span>
            </div>
          </div>

          <!-- 3. Formulario de Asignación -->
          <div v-else>
            <div v-if="modoReasignacion && especialistaSeleccionado"
              class="d-flex align-items-center bg-light border rounded p-2 mb-3">
              <i class="fas fa-info-circle text-primary mr-2"></i>
              <p class="mb-0 small">
                Actualmente asignado a: <strong class="text-dark">{{ especialistaSeleccionado.name }}</strong>.
              </p>
            </div>
            <div class="form-group">
              <label for="especialista_id_select" class="font-weight-bold">Especialista:</label>
              <select id="especialista_id_select" v-model="especialista_id" class="form-control">
                <option value="">Seleccione un especialista...</option>
                <option v-for="especialista in especialistas" :key="especialista.id" :value="especialista.id">
                  {{ especialista.nombres }}
                </option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="closeModal">
            {{ isAsignacionExitosa ? 'Cerrar' : 'Cancelar' }}
          </button>
          <button v-if="isAsignacionPermitida && !isAsignacionExitosa" type="button" class="btn btn-primary"
            @click="guardarSeleccion" :disabled="isAsignacionInvalida || isLoading">
            <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span v-if="isLoading"> Asignando...</span>
            <span v-else>{{ modoReasignacion ? 'Reasignar' : 'Asignar' }}</span>
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
    especialistas: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      modalInstance: null,
      requerimiento_id: null,
      especialista_id: '',
      especialistaSeleccionado: null,
      modoReasignacion: false,
      mensaje: '',
      isAsignacionExitosa: false,
      isLoading: false,
      isAsignacionPermitida: false,
    };
  },
  computed: {
    isAsignacionInvalida() {
      if (!this.isAsignacionPermitida) return true;
      if (!this.especialista_id) return true;
      if (this.modoReasignacion && this.especialistaSeleccionado && this.especialista_id == this.especialistaSeleccionado.id) return true;
      return false;
    }
  },
  methods: {
    mostrarAsignacion(requerimiento) {
      this.requerimiento_id = requerimiento.id;
      this.especialistaSeleccionado = requerimiento.especialista;
      this.modoReasignacion = !!requerimiento.especialista;
      this.mensaje = '';
      this.isAsignacionExitosa = false; // Resetear estado de éxito
      this.especialista_id = '';

      const estadosPermitidos = ['evaluado', 'asignado'];
      if (estadosPermitidos.includes(requerimiento.estado.toLowerCase())) {
        this.isAsignacionPermitida = true;
      } else {
        this.isAsignacionPermitida = false;
        this.mensaje = 'Solo se puede asignar requerimientos con estado "Evaluado" o "Asignado".';
      }

      if (this.modalInstance) {
        this.modalInstance.show();
      } else {
        console.error('¡Error! La instancia del modal es nula.');
      }
    },
    guardarSeleccion() {
      this.isLoading = true;
      axios.post(route('requerimiento.asignar', { id: this.requerimiento_id }, false, Ziggy), {
        especialista_id: this.especialista_id
      })
        .then(response => {
          this.isAsignacionExitosa = true;
          // Actualizar el especialista seleccionado para mostrarlo en el mensaje de éxito
          this.especialistaSeleccionado = this.especialistas.find(e => e.id == this.especialista_id);
          this.$emit('asignacion-guardada');
        })
        .catch(error => {
          this.mensaje = error.response.data.message || 'Error al guardar. Intente nuevamente.';
          this.isAsignacionExitosa = false;
          console.error(error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    closeModal() {
      this.modalInstance.hide();
    }
  },
  mounted() {
    this.modalInstance = new Modal(this.$refs.modal, {
      backdrop: 'static',
      keyboard: false
    });

    document.addEventListener('mostrarAsignacion', (event) => {
      this.mostrarAsignacion(event.detail);
    });
  }
};
</script>