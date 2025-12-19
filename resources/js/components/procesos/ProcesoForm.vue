<template>
  <div>
    <h6 class="mb-3 font-weight-bold d-flex align-items-center">
      <span class="text-secondary">{{ proceso_nombre }}</span>
      <span class="mx-2 text-secondary">
        <i class="fas fa-chevron-right fa-xs"></i>
      </span>
      <span class="text-dark">Información General</span>
    </h6>
    <div class="form-overlay-container">
      <div v-if="loading" class="loading-overlay">
        <div class="spinner-border text-primary" role="status">
          <span class="sr-only">Cargando...</span>
        </div>
      </div>
      <form id="proceso-form" @submit.prevent="submitForm">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group small">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="cod_proceso" class="form-label text-danger font-weight-bold p-0 m-0">Código de
                  Proceso</label>
              </div>
              <input type="text" id="cod_proceso" v-model="form.cod_proceso" maxlength="13" placeholder="Ej. G-M-01-01"
                class="form-control" required data-toggle="tooltip" data-placement="top"
                title="Código único para identificar el proceso." />
              <small v-if="errors.cod_proceso" class="text-danger">{{ errors.cod_proceso }}</small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group small">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="proceso_sigla" class="form-label text-danger font-weight-bold p-0 m-0">Sigla</label>
                <small class="text-muted">{{ form.proceso_sigla?.length || 0 }}/5</small>
              </div>
              <input type="text" id="proceso_sigla" v-model="form.proceso_sigla" maxlength="5" placeholder="Ej. GMI"
                class="form-control" required data-toggle="tooltip" data-placement="top"
                title="Sigla corta del proceso, máximo 5 caracteres." />

              <small v-if="errors.proceso_sigla" class="text-danger">{{ errors.proceso_sigla }}</small>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group small">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="proceso_tipo" class="form-label text-danger font-weight-bold p-0 m-0">Tipo de
                  Proceso</label>
              </div>
              <select id="proceso_tipo" v-model="form.proceso_tipo" class="form-control" required data-toggle="tooltip"
                data-placement="top" title="Clasifica el tipo de proceso según su rol en la organización.">
                <option value="Misional">Misional</option>
                <option value="Estratégico">Estratégico</option>
                <option value="Apoyo">Apoyo</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group small">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="proceso_estado" class="form-label text-danger font-weight-bold p-0 m-0">Estado</label>
              </div>
              <select id="proceso_estado" v-model="form.proceso_estado" class="form-control" data-toggle="tooltip"
                data-placement="top" title="Define si el proceso está activo o inactivo.">
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group small">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="proceso_nivel" class="form-label text-danger font-weight-bold p-0 m-0">Nivel de
                  Proceso</label>
              </div>
              <select id="proceso_nivel" class="form-control" v-model="form.proceso_nivel" required
                data-toggle="tooltip" data-placement="top" title="Selecciona el nivel jerárquico del proceso.">
                <option v-for="i in 4" :key="i - 1" :value="i - 1">
                  {{ i - 1 === 0 ? 'Macroproceso' : 'Proceso nivel ' + (i - 1) }}
                </option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group small">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="proceso_nombre_padre" class="form-label font-weight-bold p-0 m-0">Proceso Padre</label>
              </div>
              <input type="hidden" id="cod_proceso_padre" v-model="form.cod_proceso_padre" />
              <div class="input-group">
                <input type="text" id="proceso_nombre_padre" class="form-control" v-model="form.proceso_nombre_padre"
                  readonly data-toggle="tooltip" data-placement="top"
                  title="Selecciona el proceso al que pertenece este proceso." />
                <div class="input-group-append">
                  <button type="button" @click="openProcesoModal" class="btn btn-dark"
                    :disabled="form.proceso_nivel == 0">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="proceso_nombre" class="form-label text-danger font-weight-bold p-0 m-0 small">Nombre del
                Proceso</label>
              <small class="text-muted">{{ form.proceso_nombre?.length || 0 }}/200</small>
            </div>

            <textarea id="proceso_nombre" v-model="form.proceso_nombre" maxlength="200"
              placeholder="Escribe el nombre completo del proceso." rows="2" class="form-control" required
              data-toggle="tooltip" data-placement="top"
              title="Escribe el nombre del proceso en no más de 200 caracteres."></textarea>

            <small v-if="errors.proceso_nombre" class="text-danger">{{ errors.proceso_nombre }}</small>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="proceso_producto" class="form-label text-danger font-weight-bold p-0 m-0 small">Producto y/o Servicio</label>
              <small class="text-muted">{{ form.proceso_producto?.length || 0 }}/500</small>
            </div>

            <textarea id="proceso_producto" v-model="form.proceso_producto" maxlength="500"
              placeholder="Describe el producto o servicio del proceso." rows="3" class="form-control" required
              data-toggle="tooltip" data-placement="top"
              title="Describe el producto o servicio del proceso in no más de 500 caracteres."></textarea>

            <small v-if="errors.proceso_producto" class="text-danger">{{ errors.proceso_producto }}</small>
          </div>
        </div>
        
        <div class="row mt-3">
          <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="proceso_objetivo" class="form-label text-danger font-weight-bold p-0 m-0 small">Objetivo del
                Proceso</label>
              <small class="text-muted">{{ form.proceso_objetivo?.length || 0 }}/500</small>
            </div>

            <textarea id="proceso_objetivo" v-model="form.proceso_objetivo" maxlength="500"
              placeholder="Describe el objetivo del proceso." rows="3" class="form-control" required
              data-toggle="tooltip" data-placement="top"
              title="Describe el objetivo del proceso en no más de 500 caracteres."></textarea>

            <small v-if="errors.proceso_objetivo" class="text-danger">{{ errors.proceso_objetivo }}</small>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="planificacion_pei_nombre" class="form-label text-danger font-weight-bold p-0 m-0 small">Objetivo
                Estratégico</label>
            </div>
            <input type="hidden" v-model="form.planificacion_pei_id" />
            <div class="input-group">
              <input type="text" id="planificacion_pei_nombre" class="form-control"
                v-model="form.planificacion_pei_nombre" readonly />
              <div class="input-group-append">
                <button type="button" class="btn btn-dark" @click="openObjetivoModal" data-toggle="tooltip"
                  data-placement="top" title="Busca y selecciona un objetivo estratégico.">
                  <i class="fas fa-search"></i>
                </button>
              </div>
            </div>
          </div>
        </div>


        <div class="text-muted mt-2">
          <small><span class="text-danger font-weight-bold">(*)</span> Es obligatorio completar los
            campos.</small>
        </div>
        <modal-hijo ref="modalProceso" :fetch-url="proceso_route" target-id="cod_proceso_padre"
          target-desc="proceso_nombre_padre" @update-target="updateTargetFields">
        </modal-hijo>
        <modal-hijo ref="modalPei" :fetch-url="pei_route" target-id="planificacion_pei_id"
          target-desc="planificacion_pei_nombre" @update-target="updateTargetFields">
        </modal-hijo>

        <div class="modal-footer justify-content-center w-100">
          <button type="submit" class="btn btn-danger btn-sm">Guardar</button>
          <button type="button" class="btn btn-secondary btn-sm" @click="close">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</template>
<script>
import { route } from 'ziggy-js';
import ModalHijo from '../generales/ModalHijo.vue'; // Asegúrate de importar el componente hijo

export default {
  components: {
    emits: ['form-submitted', 'close-modal'],
    ModalHijo,
  },
  props: {
    procesoId: { type: [Number, String], default: null } // Ahora es una prop
  },

  data() {
    return {
      pei_route: route('objetivoPEI.buscar'),
      proceso_route: route('procesos.buscar'),
      form: {
        proceso_tipo: "Misional",
        proceso_estado: "1",
        cod_proceso: "",
        proceso_sigla: "",
        proceso_nombre: "",
        proceso_objetivo: "",
        proceso_producto: "",
        planificacion_pei_id: null,
        planificacion_pei_nombre: "",
        proceso_nivel: 0, // Asegúrate de tener este campo
        cod_proceso_padre: null,
        proceso_nombre_padre: "",
      },
      errors: {},
      loading: false,
      proceso_nombre: '', // Nombre del proceso para mostrar en el encabezado
    };
  },
  watch: {
    procesoId: {
      immediate: true,
      handler(newVal) {
        if (newVal) {
          this.resetForm(); // Limpiamos primero...
          this.loadProceso(newVal); // ...y luego cargamos los nuevos datos.
          this.btnName = 'Actualizar';
        } else {
          this.resetForm(); // Al abrir en modo 'Nuevo', limpiamos.
          this.btnName = 'Guardar';
        }
      }
    }
  },
  mounted() {

    const setupChildModalListener = (modalRef) => {
      const modalEl = this.$refs[modalRef]?.$refs.modalEl;
      if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', () => {
          document.body.classList.add("modal-open");
        });
      }
    };

    // Aplica el listener a cada modal hijo
    setupChildModalListener('modalPei');
    setupChildModalListener('modalProceso');

  },
  methods: {
    resetForm() {
      // Reinicia el formulario a su estado inicial vacío
      this.form = {
        proceso_tipo: "Misional",
        proceso_estado: "1",
        cod_proceso: "",
        proceso_sigla: "",
        proceso_nombre: "",
        proceso_objetivo: "",
        proceso_producto: "",
        planificacion_pei_id: null,
        planificacion_pei_nombre: "",
        proceso_nivel: 0,
        cod_proceso_padre: null,
        proceso_nombre_padre: "",
      };
      this.errors = {};
      this.loading = false;
    },
    updateTargetFields({ targetId, targetDesc, idValue, descValue }) {
      this.form[targetId] = idValue;
      this.form[targetDesc] = descValue;
    },
    openObjetivoModal() {
      this.$refs.modalPei.open();
    },
    openProcesoModal() {
      this.$refs.modalProceso.open();
    },

    close() {
      this.$emit('close-modal'); // Emite un evento para que el padre cierre el modal
    },
    async loadProceso(id) {

      this.errors = {}; // Limpia errores anteriores
      this.loading = true;
      try {
        // Realiza la petición GET al endpoint de tu API para obtener el proceso
        const response = await fetch(route("procesos.show", id), {
          headers: { "X-Requested-With": "XMLHttpRequest" },
        });

        // Si la respuesta no es exitosa (ej. 404), lanza un error
        if (!response.ok) {
          throw new Error("Error cargando el proceso. Es posible que el registro no exista.");
        }

        const data = await response.json();

        // Mapea los datos recibidos a las propiedades del formulario
        this.proceso_nombre = data.proceso_nombre;
        this.form.proceso_tipo = data.proceso_tipo;
        this.form.proceso_estado = String(data.proceso_estado); // Asegura que sea un string para el select
        this.form.cod_proceso = data.cod_proceso;
        this.form.proceso_sigla = data.proceso_sigla;
        this.form.proceso_nombre = data.proceso_nombre;
        this.form.proceso_nombre = data.proceso_nombre;
        this.form.proceso_objetivo = data.proceso_objetivo;
        this.form.proceso_producto = data.proceso_producto;
        this.form.proceso_nivel = data.proceso_nivel; // Asigna el nivel
        this.form.cod_proceso_padre = data.cod_proceso_padre; // Asigna el id del padre
        this.form.proceso_nombre_padre = data.proceso_nombre_padre; // Asigna el nombre del padre
        this.form.planificacion_pei_id = data.planificacion_pei_id;
        this.form.planificacion_pei_nombre = data.planificacion_pei?.planificacion_pei_nombre;
      } catch (error) {

        alert("Error: " + error.message);
        console.error(error);
        // Si hay un error, puedes emitir un evento para cerrar el modal o mostrar un mensaje
      } finally {
        this.loading = false;
      }
    },
    // En el script de ProcesoForm.vue
    async submitForm() {
      this.loading = true;
      this.errors = {};

      try {
        let url;
        let method;
        if (this.procesoId) {
          url = route("proceso.update", this.procesoId);
          method = "PUT";
        } else {
          url = route("proceso.store");
          method = "POST";
        }

        const response = await fetch(url, {
          method,
          headers: {
            "Content-Type": "application/json",
            // ¡IMPORTANTE! Agrega el token CSRF para seguridad
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            Accept: "application/json",
          },
          body: JSON.stringify(this.form), // Envía los datos del formulario
        });

        if (response.status === 422) {
          const data = await response.json();
          this.errors = data.errors || {};
          throw new Error("Errores de validación");
        }

        if (!response.ok) {
          throw new Error("Error al guardar el proceso.");
        }

        alert("Proceso guardado correctamente");
        this.$emit("form-submitted"); // Notifica al padre que se cerrará el modal

      } catch (error) {
        if (error.message !== "Errores de validación") {
          alert(error.message);
        }
      } finally {
        this.loading = false;
      }
    }
  },
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


</style>