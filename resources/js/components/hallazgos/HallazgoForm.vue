<template>
  <div class="text-left mb-4">
    <div class="header-container">
      <h6 class="mb-0 d-flex align-items-center">
        <span class="text-dark">{{ hallazgoStore.modalTitle }}</span>
        <span class="mx-2 text-secondary">
          <i class="fas fa-chevron-right fa-xs"></i>
        </span>
        <span class="text-dark">{{ hallazgoStore.hallazgoForm.hallazgo_cod }}</span>
      </h6>
    </div>
    <div v-if="localLoading" class="loading-spinner w-100 text-center my-5">
      <div class="spinner-border text-danger" role="status">
        <span class="sr-only">Cargando...</span>
      </div>
    </div>
    <div v-else>
      <div class="row">
        <div class="col-12">
          <form @submit.prevent="hallazgoStore.saveHallazgo">
            <fieldset :disabled="hallazgoStore.isReadOnly" class="border-0 p-0 m-0">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    <h6 class="text-left"><b>1. Identificación de la Mejora</b></h6>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="hallazgo_origen">Origen</label>
                      <select v-model="hallazgoStore.hallazgoForm.hallazgo_origen" class="form-control"
                        :class="{ 'is-invalid': hallazgoStore.errors.hallazgo_origen }" id="hallazgo_origen" required>
                        <option value="" disabled>Seleccione un origen...</option>
                        <option v-for="option in hallazgoOrigenOptions" :key="option.value" :value="option.value">
                          {{ option.text }}
                        </option>
                      </select>
                      <div class="invalid-feedback" v-if="hallazgoStore.errors.hallazgo_origen">
                        {{ hallazgoStore.errors.hallazgo_origen[0] }}
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="hallazgo_clasificacion">Clasificación</label>
                      <select v-model="hallazgoStore.hallazgoForm.hallazgo_clasificacion" class="form-control"
                        :class="{ 'is-invalid': hallazgoStore.errors.hallazgo_clasificacion }"
                        id="hallazgo_clasificacion" required>
                        <option value="NCM">No Conformidad Mayor</option>
                        <option value="Ncme">No Conformidad Menor</option>
                        <option value="Obs">Observación</option>
                        <option value="Odm">Oportunidad de Mejora</option>
                      </select>
                      <div class="invalid-feedback" v-if="hallazgoStore.errors.hallazgo_clasificacion">
                        {{ hallazgoStore.errors.hallazgo_clasificacion[0] }}
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="hallazgo_fecha_identificacion">Fecha de Identificación</label>
                      <input type="date" v-model="hallazgoStore.hallazgoForm.hallazgo_fecha_identificacion"
                        class="form-control"
                        :class="{ 'is-invalid': hallazgoStore.errors.hallazgo_fecha_identificacion }"
                        id="hallazgo_fecha_identificacion" required>
                      <div class="invalid-feedback" v-if="hallazgoStore.errors.hallazgo_fecha_identificacion">
                        {{ hallazgoStore.errors.hallazgo_fecha_identificacion[0] }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="informe_id">Informe o documento</label>
                      <input type="text" v-model="hallazgoStore.hallazgoForm.informe_id" class="form-control"
                        :class="{ 'is-invalid': hallazgoStore.errors.informe_id }" id="informe_id"
                        placeholder="Código o nombre del informe relacionado">

                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="auditor_id">Auditor que identificó</label>
                      <select v-model="hallazgoStore.hallazgoForm.auditor_id" class="form-control"
                        :class="{ 'is-invalid': hallazgoStore.errors.auditor_id }" id="auditor_id">
                        <option value="">Seleccione un auditor...</option>
                        <option v-for="auditor in hallazgoStore.auditores" :key="auditor.id" :value="auditor.id">
                          {{ auditor.descripcion }} </option>
                      </select>
                      <div class="invalid-feedback" v-if="hallazgoStore.errors.auditor_id">
                        {{ hallazgoStore.errors.auditor_id[0] }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group small">
                      <label for="hallazgo_sig" class="form-label font-weight-bold">Sistemas de Gestión</label>
                      <MultiSelect v-model="hallazgoStore.hallazgoForm.hallazgo_sig" :options="sigOptions"
                        optionLabel="label" optionValue="value" placeholder="Seleccione los sistemas..." display="chip"
                        class="w-100 custom-multiselect" :class="{ 'p-invalid': hallazgoStore.errors.hallazgo_sig }" />
                      <div class="invalid-feedback d-block" v-if="hallazgoStore.errors.hallazgo_sig">
                        {{ hallazgoStore.errors.hallazgo_sig[0] }}
                      </div>
                    </div>
                  </div>

                </div>
                <hr>
                <div class="row">
                  <div class="col-12">
                    <h6 class="text-left"><b>2. Detalle de la Mejora</b></h6>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="hallazgo_resumen">Resumen </label>
                        <small class="text-muted">{{ hallazgoStore.hallazgoForm.hallazgo_resumen?.length || 0
                          }}/255</small>
                      </div>

                      <textarea v-model="hallazgoStore.hallazgoForm.hallazgo_resumen" class="form-control"
                        :class="{ 'is-invalid': hallazgoStore.errors.hallazgo_resumen }" id="hallazgo_resumen" rows="3"
                        required
                        placeholder="Redacte un resumen de la mejora, breve y conciso, enfocándose en la naturaleza del hallazgo. Ej: Falta de procedimiento para gestión de cambios en el área de producción."></textarea>
                      <div class="invalid-feedback" v-if="hallazgoStore.errors.hallazgo_resumen">
                        {{ hallazgoStore.errors.hallazgo_resumen[0] }}
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="hallazgo_descripcion">Descripción (Condición)</label>
                        <small class="text-muted">{{ hallazgoStore.hallazgoForm.hallazgo_descripcion?.length || 0
                          }}/1000</small>
                      </div>

                      <textarea v-model="hallazgoStore.hallazgoForm.hallazgo_descripcion" class="form-control"
                        maxlength="1000" :class="{ 'is-invalid': hallazgoStore.errors.hallazgo_descripcion }"
                        id="hallazgo_descripcion" rows="10" required
                        placeholder="Decriba los hechos del hallazgo, el 'que' y 'donde' : Ej: Durante la auditoría interna se observó que no existe un procedimiento formal documentado para registrar y evaluar los cambios en la maquinaria de producción, lo que podría afectar la calidad del producto y la seguridad operacional. "></textarea>
                      <div class="invalid-feedback" v-if="hallazgoStore.errors.hallazgo_descripcion">
                        {{ hallazgoStore.errors.hallazgo_descripcion[0] }}
                      </div>
                    </div>
                  </div>

                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="hallazgo_criterio">Referencia (Criterio)</label>
                        <small class="text-muted">{{ hallazgoStore.hallazgoForm.hallazgo_criterio?.length || 0
                          }}/500</small>
                      </div>

                      <textarea v-model="hallazgoStore.hallazgoForm.hallazgo_criterio" class="form-control"
                        maxlength="500" :class="{ 'is-invalid': hallazgoStore.errors.hallazgo_criterio }"
                        id="hallazgo_criterio" rows="6"
                        placeholder="Citar la referencia normativa, legal o interna específica que se incumple o que serviría de base para la mejora.Ej: ISO 9001:2015 - Cláusula 6.3 Planificación de los cambios / Procedimiento P-PRD-001 'Control de Producción', punto 4.1"></textarea>
                      <div class="invalid-feedback" v-if="hallazgoStore.errors.hallazgo_criterio">
                        {{ hallazgoStore.errors.hallazgo_criterio[0] }}
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="evidencia">Evidencias</label>
                        <small class="text-muted">{{ hallazgoStore.hallazgoForm.hallazgo_evidencia?.length || 0
                          }}/500</small>
                      </div>
                      <textarea v-model="hallazgoStore.hallazgoForm.hallazgo_evidencia" class="form-control"
                        id="hallazgo_evidencia" rows="6"
                        placeholder="Tipo de información (fechas, personas, documentos, observaciones) que respalda el hallazgo Ej: Entrevista con el jefe de producción (05/03/2024), revisión de registros de mantenimiento de las últimas 3 máquinas modificadas (sin evidencia de evaluación de cambios), observación directa en línea de producción."></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </fieldset>

            <div class="modal-footer justify-content-center w-100">
              <button v-if="!hallazgoStore.isReadOnly" type="submit" class="btn btn-danger"
                :disabled="hallazgoStore.loading">
                <span v-if="hallazgoStore.loading" class="spinner-border spinner-border-sm" role="status"
                  aria-hidden="true"></span>
                {{ hallazgoStore.isEditing ? 'Actualizar' : 'Grabar' }}
              </button>
              <button type="button" class="btn btn-secondary ml-2" @click="hallazgoStore.closeModal">
                {{ hallazgoStore.isReadOnly ? 'Cerrar' : 'Cancelar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useHallazgoStore } from '@/stores/hallazgoStore';
import MultiSelect from 'primevue/multiselect';

// Inicializa el store
const hallazgoStore = useHallazgoStore();
const localLoading = ref(false);

const hallazgoOrigenOptions = [
  { value: 'RD', text: 'RD - Revisión por la Dirección' },
  { value: 'IN', text: 'IN - Resultados de Auditorías Internas' },
  { value: 'EX', text: 'EX - Resultados de Auditorías Externas' },
  { value: 'SN', text: 'SN - Resultado de la detección de Salidas No Conformes' },
  { value: 'GI', text: 'GI - Resultado del análisis de la Gestión de indicadores / objetivos de la calidad' },
  { value: 'GR', text: 'GR - Resultado de la gestión de riesgos' },
  { value: 'SC', text: 'SC - Satisfacción, reclamos y quejas de los clientes' },
  { value: 'OT', text: 'OT - Otros' },
];

const sigOptions = [
  { value: 'sgc', label: 'ISO 9001 (Calidad)' },
  { value: 'sgas', label: 'ISO 37001 (Antisoborno)' },
  { value: 'sgcm', label: 'ISO 37301 (Compliance)' },
  { value: 'sgsi', label: 'ISO 27001 (Seguridad Información)' },
  { value: 'sgco', label: 'ISO 21001 (Calidad Educativa)' },
];

// Hook de ciclo de vida para cargar datos globales cuando el componente se monta
onMounted(() => {
  console.log("HallazgoForm Mounted.");
  // Asegurar que hallazgo_sig sea un array
  if (!Array.isArray(hallazgoStore.hallazgoForm.hallazgo_sig)) {
    hallazgoStore.hallazgoForm.hallazgo_sig = [];
  }
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

/* Estilos para el MultiSelect de PrimeVue */
:deep(.custom-multiselect) {
  font-size: 13px;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
}

:deep(.custom-multiselect .p-multiselect-label) {
  font-size: 13px;
  padding: 0.375rem 0.75rem;
}

:deep(.custom-multiselect .p-multiselect-token) {
  font-size: 12px;
  background-color: #dc3545;
  color: white;
}

:deep(.custom-multiselect .p-multiselect-token .p-multiselect-token-icon) {
  color: white;
}
</style>