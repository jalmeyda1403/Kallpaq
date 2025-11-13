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

            <h6 class="mb-1" style="font-weight: bold;">
                ASIGNAR ESPECIALISTA DE APOYO
            </h6>
            <p class="small text-muted">
                Este módulo permite asignar a un especialista para apoyar con la gestión del hallazgo y acompñar en la identificación de acciones de mejora.
            </p>
            <!-- Mostrar Especialista Actual -->
            <div v-if="hallazgoStore.especialistaActual"
                class="d-flex align-items-center bg-light border rounded p-2 mb-3">
                <i class="fas fa-info-circle text-primary mr-2"></i>
                <p class="mb-0 small">
                    Actualmente asignado a: <strong class="text-dark">{{ hallazgoStore.especialistaActual.name
                    }}</strong>.
                </p>

            </div>
            <div v-else class="alert alert-warning border-0 small">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Aún no se ha asignado un especialista a este hallazgo.
            </div>

            <!-- Formulario para Cambiar Asignación -->
            <label class="form-label small font-weight-bold text-secondary mt-3">
                {{ hallazgoStore.especialistaActual ? 'Reasignar a un nuevo especialista:' : 'Asignar especialista:' }}
            </label>
            <div class="d-flex align-items-center my-4">
                <div class="input-group mr-3">
                    <select v-if="hallazgoStore.especialistas.length > 0" v-model="especialistaSeleccionado.id"
                        class="form-control">
                        <option value="" disabled>Seleccionar Especialista</option>
                        <option v-for="especialista in hallazgoStore.especialistas" :key="especialista.id"
                            :value="especialista.id">
                            {{ especialista.descripcion }}
                        </option>
                    </select>
                    <span v-else class="form-control text-muted">Cargando especialistas...</span>
                    <div class="input-group-append">
                        <!-- Asignar Button -->
                        <button type="button" class="btn btn-danger btn-sm" @click="confirmarAsignacion"
                            :disabled="!especialistaSeleccionado.id">
                            <i class="fas fa-save"></i> Asignar
                        </button>

                        <!-- Ver Historial Button -->
                        <button type="button" class="btn btn-secondary btn-sm" @click="showHistory = !showHistory">
                            <i class="fas fa-history"></i> {{ showHistory ? 'Ocultar Historial' : 'Historial' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales de Apoyo -->
    <!-- Historial de Asignaciones (colapsable) -->
    <div v-if="showHistory" class="card mt-4">
        <div class="card-header bg-secondary d-flex align-items-center">
            <i class="fas fa-history mr-2"></i>
            <h6 class="text-white mb-0">Historial de Asignaciones</h6>
        </div>
        <div class="card-body">
            <div v-if="hallazgoStore.historialAsignaciones.length > 0">
                <ul class="list-group list-group-flush">
                    <li v-for="movimiento in hallazgoStore.historialAsignaciones" :key="movimiento.id"
                        class="list-group-item d-flex justify-content-between align-items-center history-item">
                        <div>
                            <i class="fas fa-user-tag mr-2 text-dark"></i>
                            <span class="text-dark">{{ movimiento.comentario }}</span>
                            <small class="text-muted d-block ml-4 history-date">
                                <i class="fas fa-clock mr-1"></i> {{ formatDate(movimiento.created_at) }}
                            </small>
                        </div>
                    </li>
                </ul>
            </div>
            <div v-else class="alert alert-secondary small">
                <i class="fas fa-info-circle mr-2"></i>
                No se han registrado asignaciones para este hallazgo.
            </div>
        </div>
    </div>

</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useHallazgoStore } from '@/stores/hallazgoStore';
import { route } from 'ziggy-js';

const hallazgoStore = useHallazgoStore();

// Estado local para la selección
const especialistaSeleccionado = ref({ id: null, name: '' });
const showHistory = ref(false);

const confirmarAsignacion = () => {
    if (especialistaSeleccionado.value.id) {
        hallazgoStore.asignarEspecialista(especialistaSeleccionado.value.id);
        especialistaSeleccionado.value = { id: null, name: '' }; // Limpiar
    }
};

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

onMounted(() => {
    hallazgoStore.fetchAsignaciones();
    console.log('Especialistas cargados en el store:', hallazgoStore.especialistas);
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

.history-item {
    padding: 0.5rem 0.75rem; /* Reduced padding */
    font-size: 0.85rem; /* Smaller text */
}

.history-item .history-date {
    font-size: 0.75rem; /* Even smaller date text */
}
</style>
