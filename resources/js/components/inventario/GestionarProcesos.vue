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
              
              <div class="row mb-3">
                <div class="col-md-12">
                  <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <label class="form-label font-weight-bold">Filtros de Procesos</label>
                    </div>
                    
                    <div class="form-row">
                      <div class="col-md-4">
                        <input 
                          type="text" 
                          v-model="searchTerm" 
                          class="form-control" 
                          placeholder="Buscar procesos..."
                        >
                      </div>
                      <div class="col-md-3">
                        <select v-model="nivelFilter" class="form-control">
                          <option value="">Todos los niveles</option>
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
                      <div class="col-md-2">
                        <button class="btn btn-primary btn-block" @click="applyFilters">
                          <i class="fas fa-filter"></i> Filtrar
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <label class="form-label font-weight-bold">Procesos Disponibles</label>
                      <small class="text-muted">{{ filteredProcesos.length }} de {{ allProcesos.length }} procesos</small>
                    </div>
                    
                    <div class="border rounded p-2" style="max-height: 400px; overflow-y: auto;">
                      <div v-for="proceso in filteredProcesos" :key="proceso.id" class="mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                          <div>
                            <input 
                              type="checkbox" 
                              :id="'proc_' + proceso.id" 
                              :value="proceso.id"
                              v-model="selectedProcesos"
                              @change="handleProcessSelection(proceso)"
                              :disabled="isProcessInInventario(proceso.id)"
                            >
                            <label :for="'proc_' + proceso.id" class="ml-2 mb-0">
                              <i :class="getNivelIcon(proceso.proceso_nivel)"></i>
                              <span :class="getNivelClass(proceso.proceso_nivel)">{{ proceso.cod_proceso }}</span>
                              - {{ proceso.proceso_nombre }}
                              <span class="badge badge-secondary ml-1">{{ proceso.proceso_nivel }}</span>
                              <span class="badge badge-info ml-1">{{ proceso.estado_texto }}</span>
                            </label>
                          </div>
                          <button 
                            type="button" 
                            class="btn btn-sm btn-outline-primary"
                            @click="toggleChildView(proceso.id)"
                            v-if="hasChildren(proceso.id)"
                          >
                            <i :class="expandedProcesos.includes(proceso.id) ? 'fas fa-minus' : 'fas fa-plus'"></i>
                          </button>
                        </div>
                        
                        <!-- Vista de hijos si está expandida -->
                        <div v-if="expandedProcesos.includes(proceso.id)" class="ml-4 mt-2">
                          <div v-for="hijo in getChildProcesos(proceso.id)" :key="hijo.id" class="mb-1">
                            <input 
                              type="checkbox" 
                              :id="'proc_' + hijo.id" 
                              :value="hijo.id"
                              v-model="selectedProcesos"
                              @change="handleProcessSelection(hijo)"
                              :disabled="isProcessInInventario(hijo.id)"
                            >
                            <label :for="'proc_' + hijo.id" class="ml-2 mb-0">
                              <i :class="getNivelIcon(hijo.proceso_nivel)"></i>
                              <span :class="getNivelClass(hijo.proceso_nivel)">{{ hijo.cod_proceso }}</span>
                              - {{ hijo.proceso_nombre }}
                              <span class="badge badge-secondary ml-1">{{ hijo.proceso_nivel }}</span>
                              <span class="badge badge-info ml-1">{{ hijo.estado_texto }}</span>
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <label class="form-label font-weight-bold">Procesos Seleccionados</label>
                      <small class="text-muted">{{ selectedProcesos.length }} procesos seleccionados</small>
                    </div>
                    
                    <div class="border rounded p-2" style="max-height: 400px; overflow-y: auto;">
                      <div v-for="procesoId in selectedProcesos" :key="procesoId" class="mb-2 p-2 border rounded">
                        <div class="d-flex justify-content-between align-items-center">
                          <div>
                            <i :class="getNivelIcon(getProcesoById(procesoId)?.proceso_nivel)"></i>
                            <span :class="getNivelClass(getProcesoById(procesoId)?.proceso_nivel)">
                              {{ getProcesoById(procesoId)?.cod_proceso }}
                            </span>
                            - {{ getProcesoById(procesoId)?.proceso_nombre }}
                            <span class="badge badge-secondary ml-1">{{ getProcesoById(procesoId)?.proceso_nivel }}</span>
                          </div>
                          <button 
                            type="button" 
                            class="btn btn-sm btn-outline-danger"
                            @click="removeProcess(procesoId)"
                          >
                            <i class="fas fa-times"></i>
                          </button>
                        </div>
                        
                        <!-- Mostrar hijos incluidos -->
                        <div v-if="getProcesoChildren(procesoId).length > 0" class="ml-3 mt-1">
                          <small class="text-muted">Hijos incluidos:</small>
                          <div v-for="hijoId in getProcesoChildren(procesoId)" :key="hijoId" class="ml-2">
                            <i :class="getNivelIcon(getProcesoById(hijoId)?.proceso_nivel)"></i>
                            <span :class="getNivelClass(getProcesoById(hijoId)?.proceso_nivel)">
                              {{ getProcesoById(hijoId)?.cod_proceso }}
                            </span>
                            - {{ getProcesoById(hijoId)?.proceso_nombre }}
                            <span class="badge badge-secondary ml-1">{{ getProcesoById(hijoId)?.proceso_nivel }}</span>
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

              <div class="row mt-4">
                <div class="col-12">
                  <h6 class="text-left"><b>2. Resumen y Acciones</b></h6>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="card card-body bg-light">
                    <h6 class="font-weight-bold">Resumen de Selección</h6>
                    <p class="mb-1">Total procesos seleccionados: <strong>{{ selectedProcesos.length }}</strong></p>
                    <p class="mb-1">Procesos base (nivel 0-1): <strong>{{ selectedBaseProcesos.length }}</strong></p>
                    <p class="mb-0">Procesos hijos incluidos: <strong>{{ selectedChildrenCount }}</strong></p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card card-body bg-light">
                    <h6 class="font-weight-bold">Acciones</h6>
                    <div class="d-flex gap-2">
                      <button 
                        class="btn btn-success flex-grow-1"
                        @click="saveProcessAssociation"
                        :disabled="!inventarioStore.currentInventario.id || selectedProcesos.length === 0"
                      >
                        <i class="fas fa-save"></i> Guardar Asociación
                      </button>
                      <button 
                        class="btn btn-secondary"
                        @click="clearSelection"
                        :disabled="selectedProcesos.length === 0"
                      >
                        <i class="fas fa-eraser"></i> Limpiar
                      </button>
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
const estadoFilter = ref('1'); // Por defecto solo mostrar procesos vigentes

// Función para cargar procesos disponibles
const loadProcesos = async () => {
  if (!inventarioStore.currentInventario?.id) return;
  
  localLoading.value = true;
  try {
    const response = await axios.get(`/api/inventarios/${inventarioStore.currentInventario.id}/procesos-disponibles`);
    allProcesos.value = response.data.map(proc => ({
      ...proc,
      estado_texto: proc.estado === 1 ? 'Vigente' : 'Inactivo'
    }));
    
    // Cargar procesos ya asociados
    const asociadosResponse = await axios.get(`/api/inventarios/${inventarioStore.currentInventario.id}/procesos-asociados`);
    selectedProcesos.value = asociadosResponse.data.map(proc => proc.id);
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
    const matchesEstado = !estadoFilter.value || proceso.estado?.toString() === estadoFilter.value;
    
    // Solo procesos base (nivel 0-1) y no ya asociados
    return matchesSearch && matchesNivel && matchesEstado && proceso.proceso_nivel != null && proceso.proceso_nivel <= 1 && !selectedProcesos.value.includes(proceso.id);
  });
});

// Procesos base seleccionados
const selectedBaseProcesos = computed(() => {
  return selectedProcesos.value.map(id => getProcesoById(id)).filter(proc => proc && proc.proceso_nivel != null && proc.proceso_nivel <= 1);
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
    default: return 'fas fa-asterisk';
  }
};

const getNivelClass = (nivel) => {
  switch (nivel) {
    case 0: return 'font-weight-bold';
    case 1: return 'font-weight-semibold';
    default: return '';
  }
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
      procesos: selectedProcesos.value
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