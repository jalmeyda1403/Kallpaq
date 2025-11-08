<template>
  <div class="modal fade" id="detalleEspecialistaModal" tabindex="-1" aria-labelledby="detalleEspecialistaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="detalleEspecialistaModalLabel">Detalle de Requerimientos</h5>
          <button type="button" class="close text-white" aria-label="Close" @click="closeModal">
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
            <div class="d-flex align-items-center bg-light border rounded p-2 mb-3">
              <p class="mb-0 small">
                <i class="fas fa-info-circle text-primary mr-2 "></i>
                Requerimientos asignados a <strong class="text-dark">{{ especialista.nombre }} </strong>
              </p>
            </div>

            <div class="table-responsive">
              <table class="table table-sm table-hover table-bordered align-middle text-center">
                <thead class="thead-light" >
                  <tr >
                    <th>ID</th>
                    <th style="width: 30%">Asunto</th>
                    <th>Estado</th>
                    <th>Avance</th>
                    <th style="width: 15%">Fecha Límite</th>
                    <th>Días Restantes</th>
                    <th>Fecha Fin</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="req in requerimientos" :key="req.id">
                    <td>{{ req.id }}</td>
                    <td class="text-left">{{ req.asunto }}</td>
                    <td>{{ req.estado }}</td>
                    <td>{{ req.avance ? req.avance.avance_registrado : 0 }}%</td>
                    <td>{{ formatDate(req.fecha_limite) }}</td>
                    <td>
                      <span v-if="req.estado === 'asignado'">{{ calcularDiasRestantes(req.fecha_limite) }}</span>
                      <span v-else>N/A</span>
                    </td>
                    <td>{{ formatDate(req.fecha_fin) }}</td>
                  </tr>
                  <tr v-if="requerimientos.length === 0">
                    <td colspan="7">No hay requerimientos para mostrar.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
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
  data() {
    return {
      isLoading: false,
      especialista: {},
      requerimientos: [],
      anio: null,
      modal: null,
    };
  },
  methods: {
    openModal(detail) {
      this.especialista = {};
      this.requerimientos = [];
      this.anio = detail.anio;
      this.fetchDetalle(detail.id, detail.anio);
      this.modal.show();
    },
    closeModal() {
      this.modal.hide();
    },
    fetchDetalle(especialistaId, anio) {
      this.isLoading = true;
      axios.get(route('dashboard.detalleEspecialista', { especialista: especialistaId, anio: anio }, false, Ziggy))
        .then(response => {
          this.especialista = response.data.especialista;
          this.requerimientos = response.data.requerimientos;
        })
        .catch(error => {
          console.error('Error al cargar los detalles del especialista:', error);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      const date = new Date(dateString);
      return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
    },
    calcularDiasRestantes(fechaLimite) {
      if (!fechaLimite) return 'N/A';
      const hoy = new Date();
      const limite = new Date(fechaLimite);
      const diffTime = limite.getTime() - hoy.getTime();
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      if (diffDays < 0) {
        return `Vencido por ${Math.abs(diffDays)} días`;
      }
      return diffDays;
    }
  },
  mounted() {
    this.modal = new Modal(document.getElementById('detalleEspecialistaModal'));
    window.addEventListener('mostrarDetalleEspecialista', (event) => {
      this.openModal(event.detail);
    });
  },
  beforeUnmount() {
    window.removeEventListener('mostrarDetalleEspecialista', this.openModal);
  }
};
</script>
