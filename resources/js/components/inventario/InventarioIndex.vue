<template>
  <div class="container-fluid">
    <div v-if="successMessage" class="alert alert-success" id="success-alert">
      {{ successMessage }}
    </div>
    <div v-if="errorMessage" class="alert alert-danger" id="error-alert">
      {{ errorMessage }}
    </div>

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active" aria-current="page">Inventario de Procesos</li>
      </ol>
    </nav>

    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-md-6 text-md-left">
            <h3 class="card-title mb-0">Gestión del Inventario</h3>
          </div>
          <div class="col-md-6 text-md-right">
            <a href="#" class="btn btn-primary" @click.prevent="openNewInventarioModal"
              title="Nuevo Inventario">
              <i class="fas fa-plus-circle"></i> Agregar Inventario
            </a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <DataTable
          :value="inventarios"
          :loading="loading"
          stripedRows
          paginator
          :rows="10"
          :rowsPerPageOptions="[10, 20, 50]"
          responsiveLayout="scroll"
        >
          <Column field="id" header="ID" sortable></Column>
          <Column field="nombre" header="Nombre" ></Column>
          <Column field="descripcion" header="Descripción"  style="width: 25%;"></Column>
          <Column header="Documento de Aprobación">
            <template #body="slotProps">
              <a v-if="slotProps.data.documento_aprueba" :href="slotProps.data.documento_aprueba" target="_blank" rel="noopener noreferrer" download>
                {{ getFileName(slotProps.data.documento_aprueba) }}
              </a>
              <span v-else>Sin documento</span>
            </template>
          </Column>
          <Column field="vigencia" header="Vigencia" >
            <template #body="slotProps">
              {{ formatDate(slotProps.data.vigencia) }}
            </template>
          </Column>
          <Column field="estado" header="Estado">
            <template #body="slotProps">
              <span :class="{
                'badge badge-success': slotProps.data.estado === 1,
                'badge badge-secondary': slotProps.data.estado === 0,
              }">
                {{ slotProps.data.estado === 1 ? 'Vigente' : 'Inactivo' }}
              </span>
            </template>
          </Column>
          <Column field="procesos_count" header="# Procesos" class="text-center">
            <template #body="slotProps">
              {{ slotProps.data.procesos_count || 0 }}
            </template>
          </Column>
          <Column header="Acciones" :exportable="false" style="min-width: 160px">
            <template #body="slotProps">
              <a href="#" :title="slotProps.data.estado_flujo === 'borrador' ? 'Editar Inventario' : 'No se puede editar - estado: ' + slotProps.data.estado_flujo" class="mr-3 d-inline-block"
                @click.prevent="editInventario(slotProps.data)"
                :class="{'disabled-link': slotProps.data.estado_flujo !== 'borrador'}"
                :style="slotProps.data.estado_flujo !== 'borrador' ? {'pointer-events': 'none', 'opacity': '0.5'} : {}">
                <i class="fas fa-edit" :class="{'text-warning': slotProps.data.estado_flujo === 'borrador', 'text-muted': slotProps.data.estado_flujo !== 'borrador'}"></i>
              </a>
              <a href="#" title="Gestionar Procesos" class="mr-3 d-inline-block"
                @click.prevent="manageProcesses(slotProps.data.id)" :disabled="slotProps.data.estado_flujo !== 'borrador'">
                <i class="fas fa-tasks text-info fa-lg"></i>
              </a>
              <a href="#" title="Ver Procesos" class="mr-3 d-inline-block"
                @click.prevent="viewProcesses(slotProps.data.id)" :disabled="slotProps.data.estado_flujo !== 'aprobado'">
                <i class="fas fa-list text-primary fa-lg"></i>
              </a>
              <a href="#" title="Eliminar Inventario" class="mr-3 d-inline-block"
                @click.prevent="deleteInventario(slotProps.data.id)" :disabled="slotProps.data.estado_flujo !== 'borrador'">
                <i class="fas fa-trash-alt text-danger fa-lg"></i>
              </a>
            </template>
          </Column>
        </DataTable>
      </div>
    </div>
    <InventarioModal></InventarioModal>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref } from 'vue';
import axios from 'axios';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

import InventarioModal from '@/components/inventario/InventarioModal.vue';
import { useInventarioStore } from '@/stores/inventarioStore'; // Importa la tienda

const inventarios = ref([]);
const successMessage = ref('');
const errorMessage = ref('');
const inventarioStore = useInventarioStore();
const loading = ref(true);

const formatDate = (dateString) => {
  if (!dateString) {
    return '';
  }
  const date = new Date(dateString);
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const year = date.getFullYear();

  return `${day}/${month}/${year}`;
};

const capitalizeFirst = (str) => {
  if (!str) return str;
  return str.charAt(0).toUpperCase() + str.slice(1);
};

const getFileName = (filePath) => {
  if (!filePath) return '';
  // Extraer el nombre del archivo de la ruta
  const parts = filePath.split('/');
  return parts[parts.length - 1];
};

// Métodos
const fetchInventarios = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/inventarios');
    inventarios.value = response.data;
  } catch (error) {
    console.error('Error al obtener los inventarios:', error);
    errorMessage.value = 'Hubo un problema al cargar los inventarios.';
  } finally {
    loading.value = false;
  }
};

const openNewInventarioModal = () => {
  inventarioStore.openModal();
};

const editInventario = (inventario) => {
  inventarioStore.openModal(inventario);
};

const deleteInventario = async (id) => {
  if (confirm('¿Estás seguro de que quieres eliminar este inventario en borrador?')) {
    try {
      await axios.delete(`/api/inventarios/${id}`);
      // Recargar la lista de inventarios
      fetchInventarios();
      successMessage.value = 'Inventario eliminado correctamente.';
    } catch (error) {
      console.error('Error deleting inventario:', error);
      // Mostrar mensaje de error al usuario
      errorMessage.value = 'Error al eliminar el inventario: ' + error.message;
    }
  }
};

const manageProcesses = (id) => {
  // Implementar lógica para gestionar procesos
  console.log('Gestionar procesos para inventario ID:', id);
};

const viewProcesses = (id) => {
  // Implementar lógica para ver procesos
  console.log('Ver procesos para inventario ID:', id);
};

// --- CICLO DE VIDA ---
onMounted(() => {
  fetchInventarios();
  window.addEventListener('inventarios-actualizados', fetchInventarios); // Add event listener
});

onBeforeUnmount(() => {
  window.removeEventListener('inventarios-actualizados', fetchInventarios); // Remove event listener
});
</script>
