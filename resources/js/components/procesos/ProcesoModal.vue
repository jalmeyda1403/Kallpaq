<template>
  <div class="modal fade" tabindex="-1" aria-labelledby="procesoModalLabel" aria-hidden="true" ref="modal"
    id="procesoModal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">{{ modalTitle }}</h5>
          <button type="button" class="close text-white" aria-label="Close" @click="closeModal">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body modal-body-scrollable d-flex">
          <div v-if="loading" class="loading-spinner w-100 text-center my-5">
            <div class="spinner-border text-danger" role="status">
              <span class="sr-only">Cargando...</span>
            </div>
          </div>
          <template v-else>
            <div class="col-md-3 border-right p-0">
              <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <h6 class="text-secondary mx-3 mt-2">General</h6>
                <a class="nav-link" :class="{ 'text-danger active': activeComponent === 'ProcesoForm' }"
                  @click="setActiveComponent('ProcesoForm')" id="v-pills-principal-tab" role="tab">
                  <i class="fas fa-info-circle"></i> Información General
                </a>

                <hr class="my-2 border-secondary">

                <h6 class="text-secondary mx-3 mt-3">Asociaciones</h6>
                <div :class="{ 'disabled-links': !procesoId }">
                  <a class="nav-link" :class="{ 'text-danger active': activeComponent === 'ResponsablesList' }"
                    @click="setActiveComponent('ResponsablesList')" id="v-pills-responsables-tab" role="tab">
                    <i class="fas fa-users-cog"></i> Responsables
                  </a>
                  <a class="nav-link" :class="{ 'text-danger active': activeComponent === 'RiesgosList' }"
                    @click="setActiveComponent('RiesgosList')" id="v-pills-riesgos-tab" role="tab">
                    <i class="fas fa-exclamation-triangle"></i> Riesgos
                  </a>
                  <a class="nav-link" :class="{ 'text-danger active': activeComponent === 'NormasList' }"
                    @click="setActiveComponent('NormasList')" id="v-pills-normas-tab" role="tab">
                    <i class="fas fa-gavel"></i> Obligaciones
                  </a>
                  <a class="nav-link" :class="{ 'text-danger active': activeComponent === 'DocumentosList' }"
                    @click="setActiveComponent('DocumentosList')" id="v-pills-documentos-tab" role="tab">
                    <i class="fas fa-file-alt"></i> Documentos
                  </a>
                  <a class="nav-link" :class="{ 'text-danger active': activeComponent === 'IndicadoresList' }"
                    @click="setActiveComponent('IndicadoresList')" id="v-pills-indicadores-tab" role="tab">
                    <i class="fas fa-chart-line"></i> Indicadores
                  </a>
                  <a class="nav-link" :class="{ 'text-danger active': activeComponent === 'MejorasList' }"
                    @click="setActiveComponent('MejorasList')" id="v-pills-solicitudes-tab" role="tab">
                    <i class="fas fa-lightbulb"></i> Solicitudes de Mejora
                  </a>
                </div>
              </div>
            </div>
            <div class="col-md-9 px-4">
              <component :is="activeComponent" ref="dynamicComponent" :proceso-id="procesoId" @form-submitted="closeModal"
                @close-modal="closeModal"></component>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Estilo para deshabilitar visualmente y funcionalmente los enlaces */
.disabled-links {
  pointer-events: none; /* Deshabilita el click */
  opacity: 0.5; /* Atenúa visualmente los enlaces */
}
</style>

<script>
import { Modal } from 'bootstrap';
import ProcesoForm from './ProcesoForm.vue';
import ResponsablesList from './ResponsablesList.vue';
import RiesgosList from '../riesgos/RiesgosList.vue';
import NormasList from '../obligaciones/NormasList.vue';
import DocumentosList from '../documentos/DocumentosList.vue';
import IndicadoresList from '../indicadores/IndicadoresList.vue';
import MejorasList from '../mejoras/MejorasList.vue';

export default {
  // Aquí se registran los componentes para que puedan ser cargados dinámicamente
  components: {
    ProcesoForm,
    ResponsablesList,
    RiesgosList,
    NormasList,
    DocumentosList,
    IndicadoresList,
    MejorasList,
  },
  props: {
    idProceso: {
      type: [Number, String],
      default: null,
    },
  },
  data() {
    return {
      modalInstance: null,
      modalTitle: "Nuevo Proceso",
      btnName: "Guardar",
      activeComponent: 'ProcesoForm', // ¡El componente activo por defecto!
      procesoId: null, // Propiedad local para pasar al hijo
    };
  },
  watch: {
    idProceso: {
      immediate: true,
      handler(newVal) {
        this.procesoId = newVal;
      }
    }
  },
  mounted() {
    this.modalInstance = new Modal(this.$refs.modal, {
      backdrop: 'static',
      keyboard: false
    });
    window.addEventListener('abrirProcesoModal', () => {
      this.openNewProcesoModal();
    });
    window.addEventListener('editarProceso', (event) => {
      this.openEditProcesoModal(event.detail.id);
    });
    // Lógica para restaurar el scroll del padre al cerrar un modal hijo
    const setupChildModalListener = (modalRef) => {
      const modalEl = this.$refs[modalRef]?.$refs.modalEl;
      if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', () => {
          document.body.classList.add("modal-open");
        });
      }
    };
    setupChildModalListener('modalPei');
    setupChildModalListener('modalProceso');
  },
  methods: {
    setActiveComponent(componentName) {
      this.activeComponent = componentName;
    },
    openNewProcesoModal() {
      this.procesoId = null; // Para un nuevo proceso, el ID es nulo
      this.modalTitle = "Nuevo Proceso";
      this.btnName = "Guardar";
      this.activeComponent = 'ProcesoForm';
      this.modalInstance.show();
    },
    openEditProcesoModal(id) {
      this.procesoId = id; // Asignamos el ID para editar
      this.modalTitle = "Editar Proceso";
      this.btnName = "Actualizar";
      this.activeComponent = 'ProcesoForm';
      this.modalInstance.show();
    },
    closeModal() {
      this.modalInstance.hide();
      // Resetear el estado para la próxima apertura
      this.procesoId = null;
      this.activeComponent = 'ProcesoForm';
    },
    submitCurrentForm() {
      // Llama al método submitForm() del componente hijo actual
      if (this.$refs.dynamicComponent && typeof this.$refs.dynamicComponent.submitForm === 'function') {
        this.$refs.dynamicComponent.submitForm();
      }
    }
  },
};
</script>

<style scoped>
/* Estilos del menú lateral */
.nav-pills .nav-link {
  font-size: 0.9rem;
  padding: 0.75rem 1rem;
  border-radius: 0.25rem;
  text-align: left !important;
  transition: background-color 0.2s ease-in-out;
}

.nav-pills .nav-link:not(.active):hover {
  background-color: #f8f9fa;
  color: #000;
}

.nav-pills .nav-link.active {
  background-color: #fff;
  font-weight: bold;
  color: #dc3545 !important;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.nav-link i {
  width: 1.5rem;
  text-align: left !important;
}

.nav-pills h6 {
  text-transform: uppercase;
  font-size: 0.7rem;
  margin-bottom: 0.5rem;
  letter-spacing: 0.05rem;
  text-align: left !important;
}

/* Estilos de los campos de formulario */
.form-group small {
  font-size: 0.75rem;
}

.form-label.text-danger {
  font-weight: bold;
}

.disabled-links {
  pointer-events: none;
  /* Deshabilita el click */
  opacity: 0.5;
  /* Atenúa visualmente los enlaces */
}
.modal-body-scrollable {
    height: 90vh; /* Ajusta este valor según lo necesites */
    overflow-y: auto;
}
</style>