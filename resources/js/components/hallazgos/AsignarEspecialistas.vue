<template>
    <div>
        <!-- Encabezado -->
        <div class="header-container">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">{{ hallazgoStore.hallazgoForm.hallazgo_cod || 'Hallazgo' }}</span>
                <span class="mx-2 text-secondary"><i class="fas fa-chevron-right fa-xs"></i></span>
                <span class="text-dark">Asignación de Especialista</span>
            </h6>
        </div>

        <div class="form-overlay-container mt-4">
            <div v-if="hallazgoStore.loadingAsignaciones" class="loading-overlay">
                <div class="spinner-border text-danger" role="status"></div>
            </div>

            <!-- Sección para Asignar/Cambiar Especialista -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title font-weight-bold mb-0">Especialista Responsable</h5>
                        <button class="btn btn-outline-secondary btn-sm" @click="hallazgoStore.openHistorialModal">
                            <i class="fas fa-history"></i> Ver Historial
                        </button>
                    </div>

                    <!-- Mostrar Especialista Actual -->
                    <div v-if="hallazgoStore.especialistaActual"
                        class="alert alert-info d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-user-check mr-2"></i>
                            <strong>{{ hallazgoStore.especialistaActual.name }}</strong>
                        </span>
                    </div>
                    <div v-else class="alert alert-warning border-0 small">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Aún no se ha asignado un especialista a este hallazgo.
                    </div>

                    <!-- Formulario para Cambiar Asignación -->
                    <label class="form-label small font-weight-bold text-secondary mt-3">
                        {{ hallazgoStore.especialistaActual ? 'Reasignar a un nuevo especialista:' : 'Asignar especialista:' }}
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Haga clic en buscar para seleccionar..."
                            :value="especialistaSeleccionado.descripcion" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-dark" @click="abrirModalBusqueda" title="Buscar especialista">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-danger" @click="confirmarAsignacion"
                                :disabled="!especialistaSeleccionado.id">
                                <i class="fas fa-save"></i> Asignar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modales de Apoyo -->
        <modal-hijo ref="modalBusqueda" :fetch-url="especialista_route" target-id="id" target-desc="name"
            @update-target="handleSeleccion" />
        <HistorialAsignacionesModal v-if="hallazgoStore.isHistorialModalOpen" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useHallazgoStore } from '@/stores/hallazgoStore';
import { route } from 'ziggy-js';
import ModalHijo from '../generales/ModalHijo.vue';
import HistorialAsignacionesModal from './AsignacionesHistorial.vue';

const hallazgoStore = useHallazgoStore();

// Estado local para la selección
const modalBusqueda = ref(null);
const especialistaSeleccionado = ref({ id: null, descripcion: '' });

// Asume que tienes una ruta para buscar usuarios. Ajústala si es necesario.
const especialista_route = route('usuario.especialistas');

const abrirModalBusqueda = () => modalBusqueda.value.open();

const handleSeleccion = ({ idValue, descValue }) => {
    especialistaSeleccionado.value = { id: idValue, descripcion: descValue };
};

const confirmarAsignacion = () => {
    if (especialistaSeleccionado.value.id) {
        hallazgoStore.asignarEspecialista(especialistaSeleccionado.value.id);
        especialistaSeleccionado.value = { id: null, descripcion: '' }; // Limpiar
    }
};

onMounted(() => {
    hallazgoStore.fetchAsignaciones();
});
</script>

<style scoped>
.form-overlay-container {
    position: relative;
    min-height: 200px;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10;
}

.header-container {
    padding: 0.75rem;
    margin-bottom: 1.5rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #ff851b;
    display: flex;
}
</style>
