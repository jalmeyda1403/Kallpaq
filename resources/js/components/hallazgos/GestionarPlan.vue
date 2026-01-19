<template>
    <div>
        <!-- Encabezado -->
        <div class="header-container">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">{{ hallazgoStore.hallazgoForm.hallazgo_cod || 'Hallazgo' }}</span>
                <span class="mx-2 text-secondary"><i class="fas fa-chevron-right fa-xs"></i></span>
                <span class="text-dark">Planes de Acción</span>
            </h6>
        </div>
        <div class="text-left mb-4">
            <h6 class="mb-1" style="font-weight: bold;">GESTIÓN DE PLANES DE ACCIÓN</h6>
            <p class="mb-3 text-muted" style="font-size: 0.875rem;">
                A continuación, se listan los procesos asociados a este hallazgo. Gestione el plan de acción para cada
                uno de ellos.
            </p>
        </div>

        <!-- Lista de Procesos Asociados al Hallazgo -->
        <div v-if="!hallazgoStore.hallazgoForm.procesos || !hallazgoStore.hallazgoForm.procesos.length">
            <div class="alert alert-warning">No hay procesos asociados a este hallazgo. Por favor, asócielos en la
                pestaña correspondiente.</div>
        </div>
        <div v-else class="list-group">
            <div v-for="proceso in hallazgoStore.hallazgoForm.procesos" :key="proceso.id"
                class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 font-weight-bold">{{ proceso.proceso_nombre }}</h6>
                    <small class="text-muted">{{ proceso.cod_proceso }}</small>
                </div>
                <button class="btn btn-danger btn-sm" @click="hallazgoStore.openGestionPlanModal(proceso)">
                    <i class="fas fa-tasks mr-1"></i> Plan Acción
                </button>
            </div>
        </div>

        <!-- El modal de gestión se renderiza aquí cuando es llamado por el store -->
        <PlanesAccion v-if="hallazgoStore.isGestionPlanModalOpen" :hallazgoId="hallazgoStore.hallazgoForm.id" :embedded="true" />
    </div>
</template>

<script setup>

import { useHallazgoStore } from '@/stores/hallazgoStore';
import PlanesAccion from '../acciones/PlanesAccion.vue';

const hallazgoStore = useHallazgoStore();


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
.table {
    font-size: 12px;
}

</style>
