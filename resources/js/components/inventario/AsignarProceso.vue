<template>
  <div class="container-fluid">
    <div class="text-left mb-4">
      <div class="header-container">
        <h6 class="mb-0 d-flex align-items-center">
          <span class="text-dark">{{ inventarioStore.modalTitle }}</span>
          <span class="mx-2 text-secondary">
            <i class="fas fa-chevron-right fa-xs"></i>
          </span>
          <span class="text-dark">{{ inventarioStore.currentInventario?.nombre || 'Nuevo Inventario' }}</span>
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
            <div class="card-body">
              <div class="row">
                <div class="col-12">
                  <h6 class="text-left"><b>1. Seleccionar Procesos</b></h6>
                </div>
              </div>

              <!-- Filtros -->
              <div class="filters mb-4">
                <div class="row g-2">

                  <div class="col-md-3">
                    <input v-model="searchTerm" type="text" class="form-control" placeholder="Buscar procesos...">
                  </div>

                  <div class="col-md-3">
                    <select v-model="nivelFilter" class="form-control">
                      <option value="">Nivel</option>
                      <option value="0">Nivel 0</option>
                      <option value="1">Nivel 1</option>
                    </select>
                  </div>

                  <div class="col-md-3">
                    <select v-model="estadoFilter" class="form-control">
                      <option value="">Todos los estados</option>
                      <option value="1">Vigente</option>
                      <option value="0">Inactivo</option>
                    </select>
                  </div>

                  <div class="col-md-3 d-flex align-items-center justify-content-end">
                    <button class="btn btn-dark btn-sm ml-1" @click="applyFilters">
                      <i class="fas fa-filter me-1"></i> Filtrar
                    </button>

                    <button class="btn btn-primary btn-sm ml-2" @click="saveProcessAssociation"
                      :disabled="!inventarioStore.currentInventario.id || selectedProcesos.length === 0">
                      <i class="fas fa-save me-1"></i> Guardar
                    </button>
                  </div>

                </div>
              </div>
<div class="row">
  <div class="col-md-6">
    <div class="form-group d-flex flex-column flex-grow-1">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center">
                      <input type="checkbox" v-model="allAvailableSelected" id="selectAllAvailable" class="mr-2">
                      <label for="selectAllAvailable" class="form-label font-weight-bold mb-0">Procesos
                        Disponibles</label>
                    </div>
                    <small class="text-muted">{{ filteredProcesos.length }} de {{ allProcesos.length }}
                      procesos</small>
                  </div>

                  <div class="border rounded p-2 flex-grow-1" style="max-height: 400px; overflow-y: auto;">
                    <div v-for="proceso in filteredProcesos" :key="proceso.id" class="mb-2">
                      <div class="d-flex justify-content-between align-items-center small">
                        <div>
                          <input type="checkbox" :id="'proc_' + proceso.id" :value="proceso.id"
                            v-model="selectedProcesos" @change="handleProcessSelection(proceso)"
                            :disabled="isProcessInInventario(proceso.id)">
                          <label :for="'proc_' + proceso.id" class="ml-2 mb-0 small">
                            <i v-if="getNivelIcon(proceso.proceso_nivel)"
                              :class="getNivelIcon(proceso.proceso_nivel)"></i>
                            <span
                              :class="[getNivelClass(proceso.proceso_nivel), getEstadoClass(proceso.proceso_estado)]">{{
                              proceso.cod_proceso }}</span>
                            - {{ proceso.proceso_nombre }}
                          </label>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary"
                          @click="toggleChildView(proceso.id)" v-if="hasChildren(proceso.id)">
                          <i :class="expandedProcesos.includes(proceso.id) ? 'fas fa-minus' : 'fas fa-plus'"></i>
                        </button>
                      </div>

                      <!-- Vista de hijos si está expandida -->
                      <div v-if="expandedProcesos.includes(proceso.id)" class="ml-4 mt-2">
                        <div v-for="hijo in getChildProcesos(proceso.id)" :key="hijo.id" class="mb-1">
                          <input type="checkbox" :id="'proc_' + hijo.id" :value="hijo.id" v-model="selectedProcesos"
                            @change="handleProcessSelection(hijo)" :disabled="isProcessInInventario(hijo.id)">
                          <label :for="'proc_' + hijo.id" class="ml-2 mb-0 small">
                            <i v-if="getNivelIcon(hijo.proceso_nivel)" :class="getNivelIcon(hijo.proceso_nivel)"></i>
                            <span :class="[getNivelClass(hijo.proceso_nivel), getEstadoClass(hijo.proceso_estado)]">{{
                              hijo.cod_proceso }}</span>
                            - {{ hijo.proceso_nombre }}
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group d-flex flex-column flex-grow-1">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center">
                      <input type="checkbox" v-model="allSelectedDeselected" id="deselectAllSelected" class="mr-2">
                      <label for="deselectAllSelected" class="form-label font-weight-bold mb-0">Procesos
                        Seleccionados</label>
                    </div>
                    <small class="text-muted">{{ selectedProcesos.length }} procesos seleccionados</small>
                  </div>

                  <div class="border rounded p-2 flex-grow-1" style="max-height: 400px; overflow-y: auto;">
                    <div v-for="procesoId in selectedProcesos" :key="procesoId" class="mb-2 p-2 border rounded small">
                      <div class="d-flex justify-content-between align-items-center small">
                        <div>
                          <i v-if="getNivelIcon(getProcesoById(procesoId)?.proceso_nivel)"
                            :class="getNivelIcon(getProcesoById(procesoId)?.proceso_nivel)"></i>
                          <span
                            :class="[getNivelClass(getProcesoById(procesoId)?.proceso_nivel), getEstadoClass(getProcesoById(procesoId)?.proceso_estado)]">
                            {{ getProcesoById(procesoId)?.cod_proceso }}
                          </span>
                          - {{ getProcesoById(procesoId)?.proceso_nombre }}
                        </div>
                        <button type="button" class="btn btn-xs btn-outline-danger" @click="removeProcess(procesoId)">
                          <i class="fas fa-times"></i>
                        </button>
                      </div>

                      <!-- Mostrar hijos incluidos -->
                      <div v-if="getProcesoChildren(procesoId).length > 0" class="ml-3 mt-1">
                        <small class="text-muted">Hijos incluidos:</small>
                        <div v-for="hijoId in getProcesoChildren(procesoId)" :key="hijoId" class="ml-2 small">
                          <i v-if="getNivelIcon(getProcesoById(hijoId)?.proceso_nivel)"
                            :class="getNivelIcon(getProcesoById(hijoId)?.proceso_nivel)"></i>
                          <span
                            :class="[getNivelClass(getProcesoById(hijoId)?.proceso_nivel), getEstadoClass(getProcesoById(hijoId)?.proceso_estado)]">
                            {{ getProcesoById(hijoId)?.cod_proceso }}
                          </span>
                          - {{ getProcesoById(hijoId)?.proceso_nombre }}
                        </div>
                      </div>
                    </div>

                    <div v-if="selectedProcesos.length === 0" class="text-center text-muted py-4">
                      <i class="fas fa-inbox fa-2x mb-2"></i>
                      <p>No hay procesos seleccionados</p>
                    </div>
                  </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useInventarioStore } from '@/stores/inventarioStore';
import axios from 'axios';

const inventarioStore = useInventarioStore();

// Reactive data
const allProcesos = ref([]);
const selectedProcesos = ref([]);
const localLoading = ref(false);
const expandedProcesos = ref([]);
const searchTerm = ref('');
const nivelFilter = ref('');
const estadoFilter = ref(''); // Por defecto mostrar todos los estados
const selectAllAvailable = ref(false);

// Función para cargar procesos disponibles
const loadProcesos = async () => {
  if (!inventarioStore.currentInventario?.id) return;

  localLoading.value = true;
  try {
    const response = await axios.get(`/api/inventarios/${inventarioStore.currentInventario.id}/procesos-disponibles`);
    allProcesos.value = response.data.map(proc => ({
      ...proc,
      estado_texto: proc.proceso_estado == 1 ? 'Vigente' : proc.proceso_estado == 0 ? 'Inactivo' : (proc.estado === 1 ? 'Vigente' : proc.estado === 0 ? 'Inactivo' : 'Desconocido')
    }));

    // Cargar procesos ya asociados
    const asociadosResponse = await axios.get(`/api/inventarios/${inventarioStore.currentInventario.id}/procesos-asociados`);
    selectedProcesos.value = asociadosResponse.data.map(proc => proc.id);

    // Asegurar que los procesos asociados también estén en allProcesos
    // Combinar los datos de los procesos asociados con los datos originales de allProcesos
    const processedAssociated = asociadosResponse.data.map(associatedProc => {
      // Buscar si el proceso ya existe en allProcesos para mantener su proceso_estado original
      const existingProc = allProcesos.value.find(p => p.id === associatedProc.id);

      return {
        ...existingProc,  // Mantener los datos originales del proceso
        ...associatedProc,  // Sobrescribir con datos específicos de la asociación
        estado_texto: existingProc?.proceso_estado == 1 ? 'Vigente' : existingProc?.proceso_estado == 0 ? 'Inactivo' : (associatedProc.estado === 1 ? 'Vigente' : associatedProc.estado === 0 ? 'Inactivo' : 'Desconocido')
      };
    });

    // Combinar procesos disponibles con los asociados, priorizando la información original del proceso
    allProcesos.value = [...processedAssociated, ...allProcesos.value.filter(proc => !processedAssociated.some(p => p.id === proc.id))];
  } catch (error) {
    console.error('Error al cargar procesos:', error);
  } finally {
    localLoading.value = false;
  }
};

// Procesos filtrados
const filteredProcesos = computed(() => {
  return allProcesos.value.filter(proceso => {
    const matchesSearch = !searchTerm.value ||
      proceso.proceso_nombre.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      proceso.cod_proceso.toLowerCase().includes(searchTerm.value.toLowerCase());

    const matchesNivel = !nivelFilter.value || proceso.proceso_nivel?.toString() === nivelFilter.value;
    const matchesEstado = !estadoFilter.value || proceso.proceso_estado?.toString() === estadoFilter.value?.toString() || proceso.estado?.toString() === estadoFilter.value?.toString();

    // Solo procesos base (nivel 0-1) y no ya asociados
    return matchesSearch && matchesNivel && matchesEstado && proceso.proceso_nivel != null && proceso.proceso_nivel <= 1 && !selectedProcesos.value.includes(proceso.id);
  });
});

// Computed para el checkbox "Seleccionar todos"
const allAvailableSelected = computed({
  get: () => {
    const available = filteredProcesos.value;
    return available.length > 0 && available.every(proc => selectedProcesos.value.includes(proc.id));
  },
  set: (value) => {
    const available = filteredProcesos.value;
    if (value) {
      // Seleccionar todos los disponibles
      available.forEach(proc => {
        if (!selectedProcesos.value.includes(proc.id)) {
          selectedProcesos.value.push(proc.id);
          handleProcessSelection(proc); // Manejar hijos si aplica
        }
      });
    } else {
      // Deseleccionar todos los disponibles
      available.forEach(proc => {
        const index = selectedProcesos.value.indexOf(proc.id);
        if (index > -1) {
          selectedProcesos.value.splice(index, 1);

          // Si es un proceso base, también remover sus hijos
          if (proc.proceso_nivel != null && proc.proceso_nivel <= 1) {
            const hijos = getDirectChildren(proc.id, 3);
            hijos.forEach(hijoId => {
              const hijoIndex = selectedProcesos.value.indexOf(hijoId);
              if (hijoIndex > -1) {
                selectedProcesos.value.splice(hijoIndex, 1);
              }
            });
          }
        }
      });
    }
  }
});

// Procesos base seleccionados
const selectedBaseProcesos = computed(() => {
  return selectedProcesos.value.map(id => getProcesoById(id)).filter(proc => proc && proc.proceso_nivel != null && proc.proceso_nivel <= 1);
});

// Computed para el checkbox "Deseleccionar todos"
const allSelectedDeselected = computed({
  get: () => selectedProcesos.value.length > 0,
  set: (value) => {
    // When this checkbox is clicked, we always deselect all
    selectedProcesos.value = [];
  }
});

// Contador de hijos seleccionados
const selectedChildrenCount = computed(() => {
  return selectedProcesos.value.reduce((count, procesoId) => {
    const proceso = getProcesoById(procesoId);
    if (proceso && proceso.proceso_nivel != null && proceso.proceso_nivel <= 1) {
      // Contar hijos de este proceso base
      const hijos = getDirectChildren(proceso.id, 3); // Hasta nivel 3
      return count + hijos.length;
    }
    return count;
  }, 0);
});

// Funciones auxiliares
const getProcesoById = (id) => {
  return allProcesos.value.find(p => p.id === id);
};

const getChildProcesos = (parentId) => {
  return allProcesos.value.filter(p => p.proceso_padre_id == parentId);
};

const hasChildren = (id) => {
  return getChildProcesos(id).length > 0;
};

const isProcessInInventario = (processId) => {
  return selectedProcesos.value.includes(processId);
};

const getNivelIcon = (nivel) => {
  switch (nivel) {
    case 0: return 'fas fa-cube';
    case 1: return 'fas fa-sitemap';
    case 2: return 'fas fa-project-diagram';
    case 3: return 'fas fa-circle';
    default: return null; // No icon for default cases
  }
};

const getNivelClass = (nivel) => {
  switch (nivel) {
    case 0: return 'font-weight-bold';
    case 1: return 'font-weight-semibold';
    default: return '';
  }
};

// Función para obtener clase CSS según estado del proceso
const getEstadoClass = (estado) => {
  return estado == 1 ? 'text-primary' : estado == 0 ? 'text-danger' : 'text-muted';
};

const toggleChildView = (processId) => {
  const index = expandedProcesos.value.indexOf(processId);
  if (index > -1) {
    expandedProcesos.value.splice(index, 1);
  } else {
    expandedProcesos.value.push(processId);
  }
};

// Obtener hijos directos (y sus hijos) de un proceso base
const getDirectChildren = (parentId, maxLevel) => {
  const children = [];

  const findChildren = (pid, currentLevel) => {
    if (currentLevel >= maxLevel) return;

    const directChildren = allProcesos.value.filter(p => p.proceso_padre_id == pid);
    directChildren.forEach(child => {
      children.push(child.id);
      findChildren(child.id, currentLevel + 1);
    });
  };

  findChildren(parentId, 0);
  return children;
};

// Manejar selección de un proceso base
const handleProcessSelection = (proceso) => {
  if (selectedProcesos.value.includes(proceso.id)) {
    // Añadir procesos hijos hasta nivel 3
    const hijos = getDirectChildren(proceso.id, 3);
    hijos.forEach(hijoId => {
      if (!selectedProcesos.value.includes(hijoId)) {
        selectedProcesos.value.push(hijoId);
      }
    });
  } else {
    // Quitar procesos hijos asociados
    const hijos = getDirectChildren(proceso.id, 3);
    hijos.forEach(hijoId => {
      const index = selectedProcesos.value.indexOf(hijoId);
      if (index > -1) {
        selectedProcesos.value.splice(index, 1);
      }
    });
  }
};

// Obtener hijos incluidos de un proceso base
const getProcesoChildren = (procesoId) => {
  const proceso = getProcesoById(procesoId);
  if (!proceso || proceso.proceso_nivel == null || proceso.proceso_nivel > 1) return [];

  return selectedProcesos.value
    .map(id => getProcesoById(id))
    .filter(proc => proc && proc.proceso_padre_id == procesoId)
    .map(proc => proc.id);
};

// Remover un proceso individualmente
const removeProcess = (procesoId) => {
  const index = selectedProcesos.value.indexOf(procesoId);
  if (index > -1) {
    selectedProcesos.value.splice(index, 1);

    // Si es un proceso base, también remover sus hijos
    const proceso = getProcesoById(procesoId);
    if (proceso && proceso.proceso_nivel != null && proceso.proceso_nivel <= 1) {
      const hijos = getDirectChildren(procesoId, 3);
      hijos.forEach(hijoId => {
        const hijoIndex = selectedProcesos.value.indexOf(hijoId);
        if (hijoIndex > -1) {
          selectedProcesos.value.splice(hijoIndex, 1);
        }
      });
    }
  }
};

// Aplicar filtros
const applyFilters = () => {
  // Los filtros se aplican automáticamente gracias al computed
};

// Limpiar selección
const clearSelection = () => {
  selectedProcesos.value = [];
};

// Guardar asociación de procesos
const saveProcessAssociation = async () => {
  if (!inventarioStore.currentInventario?.id) return;

  localLoading.value = true;
  try {
    await axios.post(`/api/inventarios/${inventarioStore.currentInventario.id}/procesos/sync`, {
      procesos_ids: selectedProcesos.value
    });

    alert('Procesos asociados al inventario correctamente.');
  } catch (error) {
    console.error('Error al asociar procesos:', error);
    alert('Error al asociar procesos: ' + (error.response?.data?.message || error.message));
  } finally {
    localLoading.value = false;
  }
};

// Hook de ciclo de vida
onMounted(async () => {
  await loadProcesos();
});
</script>

<style scoped>
.header-container {
  padding: 0.75rem;
  margin-bottom: 1.5rem;
  background-color: #f8f9fa;
  border-radius: 0.25rem;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  border-left: 0.5rem solid #ff851b;
  display: flex;
  align-items: center;
}

.form-control {
  font-size: 0.9rem;
}

.font-weight-semibold {
  font-weight: 500;
}

.loading-spinner {
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Estilos para listas de procesos */
.border {
  border: 1px solid #ced4da;
}

.badge {
  font-size: 0.7em;
}

/* Efectos hover para interactividad */
.card:hover {
  transition: all 0.2s ease-in-out;
}
</style>