<template>
  <div class="modal fade" id="SeguimientoModal" ref="modal" tabindex="-1" role="dialog"
    aria-labelledby="SeguimientoModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Seguimiento del Requerimiento</h5>
          <button type="button" class="close text-white" @click="closeModal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body position-relative">
          <!-- Loading Overlay -->
          <div v-if="isLoading" class="loading-overlay">
            <div class="spinner-border text-success" role="status">
              <span class="sr-only">Cargando...</span>
            </div>
          </div>

          <!-- Timeline -->
          <div class="timeline-seguimiento">
            <div v-for="etapa in etapas" :key="etapa.porcentaje" class="timeline-item" :class="getEstado(etapa)">
              <div class="timeline-content">
                <span>{{ etapa.titulo }}</span>
                <span v-if="etapa.porcentaje > 0 && etapa.porcentaje <= 100" class="timeline-badge"
                  :class="getEstado(etapa)">
                  {{ etapa.porcentaje }}%
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary btn-sm" @click="closeModal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.timeline-seguimiento {
  position: relative;
  margin-left: 30px;
  padding-left: 20px;
  border-left: 2px solid #ccc;
}

.timeline-item {
  position: relative;
  margin-bottom: 20px;
  padding-left: 20px;
}

.timeline-item::before {
  content: "";
  position: absolute;
  left: -30px;
  top: 3px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 3px solid #ccc;
  background-color: white;
  z-index: 1;
}

.timeline-item.completado::before {
  border-color: #28a745;
  background-color: #28a745;
}

.timeline-item.actual::before {
  border-color: #28a745;
  background-color: white;
}

.timeline-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.timeline-badge {
  background-color: #28a745; /* Verde para el badge por defecto/actual */
  color: white;
  padding: 3px 10px;
  border-radius: 5px;
  font-size: 13px;
  margin-left: 10px;
}

/* Estilos para el badge en la etapa 'completado' */
.timeline-badge.completado {
  background-color: #28a745; /* Mantener el color verde si lo deseas, o usar otro */
  color: white;
}

/* Estilos para el badge en la etapa 'actual' */
.timeline-badge.actual {
  /* Usaremos un estilo diferente para el estado 'actual' */
  /* Por ejemplo, un color de borde y texto para que se destaque como "en progreso" */
  background-color: white;
  color: #28a745; /* Texto verde */
  border: 1px solid #28a745; /* Borde verde */
}

/* Estilos para etapas no completadas o futuras (si getEstado() retorna una clase para ellas, o simplemente usando los estilos base del badge) */
.timeline-badge:not(.completado):not(.actual) {
  /* Si el estado es "futuro" o "pendiente", puedes usar un color más neutro como #ccc */
  background-color: #ccc; 
  color: #333;
}

.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
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
      requerimientoId: null,
      avance: 0,
      isLoading: false,
      etapas: [
        { titulo: 'Enviado', porcentaje: 0 },
        { titulo: 'Asignado', porcentaje: 2 },
        { titulo: 'Análisis de contexto y objetivos', porcentaje: 15 },
        { titulo: 'Definición de actividades y flujo', porcentaje: 35 },
        { titulo: 'Descripción detallada del documento', porcentaje: 60 },
        { titulo: 'Diseño de formatos y anexos', porcentaje: 75 },
        { titulo: 'Revisión interna', porcentaje: 85 },
        { titulo: 'Revisión técnica', porcentaje: 90 },
        { titulo: 'Firma o aprobación', porcentaje: 95 },
        { titulo: 'Publicado', porcentaje: 100 },
        { titulo: 'Finalizado', porcentaje: 100 },
      ],
    };
  },
  methods: {
    mostrarSeguimiento(requerimiento) {
      this.requerimientoId = requerimiento.id;
      this.isLoading = true;
      axios.get(route('requerimientos.getAvance', { id: this.requerimientoId }, false, Ziggy))
        .then(response => {
          if (response.data) {
            this.avance = response.data.avance_registrado;
          } else {
            this.avance = 0;
          }
        })
        .catch(error => {
          console.error('Error al cargar el avance:', error);
        })
        .finally(() => {
          this.isLoading = false;
        });

      if (this.modalInstance) {
        this.modalInstance.show();
      } else {
        console.error('¡Error! La instancia del modal es nula.');
      }
    },
    closeModal() {
      this.modalInstance.hide();
    },
    getEstado(etapa) {
      if (this.avance >= etapa.porcentaje) {
        return 'completado';
      }
      if (this.avance >= (etapa.porcentaje - 15) && this.avance < etapa.porcentaje) {
        return 'actual';
      }
      return 'pendiente';
    },
  },
  mounted() {
    this.modalInstance = new Modal(this.$refs.modal);

    document.addEventListener('mostrarSeguimiento', (event) => {
      this.mostrarSeguimiento(event.detail);
    });
  },
};
</script>
