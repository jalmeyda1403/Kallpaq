<template>
  <form @submit.prevent="saveInventario">
    <div class="form-body">
      <div class="form-group">
        <label for="nombre">Nombre *</label>
        <input type="text" class="form-control" id="nombre" v-model="inventarioForm.nombre" required :disabled="disabledForm">
      </div>
      <div class="form-group">
        <label for="descripcion">Descripción</label>
        <textarea class="form-control" id="descripcion" rows="3" v-model="inventarioForm.descripcion" :disabled="disabledForm"></textarea>
      </div>
      <div class="form-group">
        <label for="documento_aprueba">Documento de Aprobación</label>
        <input type="file" class="form-control" id="documento_aprueba" @change="onFileChange" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.rtf,.odt,.ods,.odp" :disabled="disabledForm">
        <div v-if="inventarioForm.documento_aprueba_nombre" class="mt-2">
          <small class="text-muted">Archivo seleccionado: {{ inventarioForm.documento_aprueba_nombre }}</small>
        </div>
      </div>
      <div class="form-group">
        <label for="enlace">Enlace</label>
        <input type="url" class="form-control" id="enlace" v-model="inventarioForm.enlace" placeholder="https://..." :disabled="disabledForm">
      </div>
      <div class="form-group">
        <label for="vigencia">Vigencia *</label>
        <input type="date" class="form-control" id="vigencia" v-model="inventarioForm.vigencia" required :disabled="disabledForm">
      </div>
      <div class="form-group" v-if="inventarioForm.estado_flujo">
        <label for="estado_flujo">Estado Flujo</label>
        <select class="form-control" id="estado_flujo" v-model="inventarioForm.estado_flujo" :disabled="disabledForm">
          <option value="borrador">Borrador</option>
          <option value="aprobado">Aprobado</option>
          <option value="cerrado">Cerrado</option>
        </select>
      </div>
      <div class="form-group" v-if="inventarioForm.estado !== undefined">
        <label for="estado">Estado (Vigente)</label>
        <select class="form-control" id="estado" v-model="inventarioForm.estado" :disabled="disabledForm">
          <option :value="1">Vigente</option>
          <option :value="0">Inactivo</option>
        </select>
      </div>
    </div>

    <div class="modal-footer" v-if="!disabledForm">
      <button type="button" class="btn btn-secondary" @click="cancelForm">Cancelar</button>
      <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
  </form>
</template>

<script setup>
import { ref, watch } from 'vue';

// Props
const props = defineProps({
  inventarioData: {
    type: Object,
    default: () => ({
      id: null,
      nombre: '',
      descripcion: '',
      documento_aprueba: null,
      documento_aprueba_nombre: '',
      enlace: '',
      vigencia: '',
      estado_flujo: 'borrador',
      estado: 1
    })
  },
  isEditing: {
    type: Boolean,
    default: false
  },
  disabledForm: {
    type: Boolean,
    default: false
  }
});

// Emit
const emit = defineEmits(['saved', 'cancelled']);

// Reactive data
const inventarioForm = ref({ ...props.inventarioData });

// Watch for changes in the prop to update local data
watch(() => props.inventarioData, (newVal) => {
  inventarioForm.value = { ...newVal };
  // If we're dealing with an existing record and have a document URL, extract the filename
  if (newVal.documento_aprueba && typeof newVal.documento_aprueba === 'string' && !newVal.documento_aprueba_nombre) {
    inventarioForm.value.documento_aprueba_nombre = newVal.documento_aprueba.split('/').pop();
  }
}, { deep: true });

// Methods
const onFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    inventarioForm.value.documento_aprueba = file;
    inventarioForm.value.documento_aprueba_nombre = file.name;
  } else {
    inventarioForm.value.documento_aprueba = null;
    inventarioForm.value.documento_aprueba_nombre = '';
  }
};

const saveInventario = async () => {
  // Aquí se debería llamar a la API para guardar el inventario
  // Por ahora emitimos un evento para que el componente padre maneje la lógica
  emit('saved', { ...inventarioForm.value });
};

const cancelForm = () => {
  emit('cancelled');
};
</script>

<style scoped>
.form-body {
  padding: 1rem;
}
</style>