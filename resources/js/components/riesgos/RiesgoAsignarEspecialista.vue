<template>
    <div>
        <!-- Encabezado -->
        <div class="header-container">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">{{ formatBreadcrumbId(store.riesgoForm.id) }}</span>
                <span class="mx-2 text-secondary"><i class="fas fa-chevron-right fa-xs"></i></span>
                <span class="text-dark">Asignación de Especialista</span>
            </h6>
        </div>

        <div class="form-overlay-container mt-4">
            <div v-if="store.loadingAsignaciones" class="loading-overlay">
                <div class="spinner-border text-danger" role="status"></div>
            </div>

            <!-- Sección para Asignar/Cambiar Especialista -->

            <h6 class="mb-1" style="font-weight: bold;">
                ASIGNAR ESPECIALISTA DE APOYO
            </h6>
            <p class="small text-muted">
                Este módulo permite asignar a un especialista para apoyar con la gestión del riesgo y acompañar en la
                identificación de acciones de tratamiento.
            </p>
            <!-- Mostrar Especialista Actual -->
            <div v-if="store.especialistaActual" class="d-flex align-items-center bg-light border rounded p-2 mb-3">
                <i class="fas fa-info-circle text-primary mr-2"></i>
                <p class="mb-0 small">
                    Actualmente asignado a: <strong class="text-dark">{{ store.especialistaActual.name
                        }}</strong>.
                </p>

            </div>
            <div v-else class="alert alert-warning border-0 small">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Aún no se ha asignado un especialista a este riesgo.
            </div>

            <!-- Formulario para Cambiar Asignación -->
            <label class="form-label small font-weight-bold text-secondary mt-3">
                {{ store.especialistaActual ? 'Reasignar a un nuevo especialista:' : 'Asignar especialista:' }}
            </label>
            <div class="d-flex align-items-center my-4">
                <div class="input-group mr-3">
                    <select v-if="store.especialistas.length > 0" v-model="especialistaSeleccionado.id"
                        class="form-control">
                        <option value="" disabled>Seleccionar Especialista</option>
                        <option v-for="especialista in store.especialistas" :key="especialista.id"
                            :value="especialista.id">
                            {{ especialista.name || especialista.descripcion }}
                        </option>
                    </select>
                    <span v-else class="form-control text-muted">Cargando especialistas...</span>
                    <div class="input-group-append">
                        <!-- Asignar Button -->
                        <button type="button" class="btn btn-danger btn-sm" @click="confirmarAsignacion"
                            :disabled="!especialistaSeleccionado.id">
                            <i class="fas fa-save"></i> Asignar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRiesgoStore } from '@/stores/riesgoStore';

const store = useRiesgoStore();

// Estado local para la selección
const especialistaSeleccionado = ref({ id: null, name: '' });

const formatBreadcrumbId = (id) => {
    if (!id) return 'Nuevo Riesgo';
    return `R${id.toString().padStart(6, '0')}`;
};

const confirmarAsignacion = () => {
    if (especialistaSeleccionado.value.id) {
        store.asignarEspecialista(especialistaSeleccionado.value.id);
        especialistaSeleccionado.value = { id: null, name: '' }; // Limpiar
    }
};

onMounted(() => {
    store.fetchAsignaciones();
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
    border-left: 0.5rem solid #dc3545;
    display: flex;
    align-items: center;
}
</style>
