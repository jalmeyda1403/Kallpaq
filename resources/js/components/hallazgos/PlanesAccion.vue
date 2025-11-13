<template>
    <div class="modal fade show" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);"
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        Gestionar Plan de Acción: {{ hallazgoStore.procesoParaGestionar?.proceso_nombre }}
                    </h5>
                    <button type="button" class="close text-white" @click="hallazgoStore.closeGestionPlanModal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div v-if="hallazgoStore.loadingPlan" class="text-center my-5">
                        <div class="spinner-border text-danger" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                    <div v-else>
                        <div v-if="esClasificacionNCM" class="card mb-4">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0"><i class="fas fa-search-plus mr-2"></i>Análisis de Causa Raíz (Obligatorio)</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold">Descripción del Hallazgo</label>
                                    <textarea class="form-control" rows="3" :value="hallazgoStore.hallazgoForm.hallazgo_descripcion" readonly></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Causa Raíz Identificada</label>
                                    <textarea class="form-control" rows="3" v-model="hallazgoStore.causaRaiz.causa_resultado" readonly placeholder="Describa aquí la causa raíz principal concluida del análisis."></textarea>
                                </div>
                                <button class="btn btn-danger btn-sm" @click="hallazgoStore.openCausaRaizModal">
                                    <i class="fas fa-edit mr-1"></i> Realizar Análisis
                                </button>
                            </div>
                        </div>

                        <AccionesForm
                            v-if="esClasificacionNCM"
                            titulo="Plan de Acción"
                            descripcion="Acciones para eliminar la causa raíz y evitar la recurrencia."
                            :acciones="hallazgoStore.accionesDelPlan"
                            @guardar="guardarNuevaAccion"
                            @update="guardarNuevaAccion"
                            @eliminar="eliminarAccion"
                            class="mt-4"
                        />
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="hallazgoStore.closeGestionPlanModal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <CausaRaizModal v-if="hallazgoStore.isCausaRaizModalOpen" />
</template>

<script setup>
import { computed } from 'vue';
import { useHallazgoStore } from '@/stores/hallazgoStore';
import AccionesForm from './AccionesForm.vue';
import CausaRaizModal from './CausaRaizModal.vue'; // Import CausaRaizModal

const hallazgoStore = useHallazgoStore();

// Computed property para la lógica condicional principal
const esClasificacionNCM = computed(() => {
    const clasificacion = hallazgoStore.hallazgoForm.hallazgo_clasificacion;
    return ['NCM', 'Ncme'].includes(clasificacion);
});

// Función para delegar el guardado de una nueva acción al store
const guardarNuevaAccion = (accionData) => {
    console.log('guardarNuevaAccion called with:', accionData); // Add this log
    hallazgoStore.saveAccion(accionData);
};

// Función para delegar la eliminación al store
const eliminarAccion = (accionId) => {
    hallazgoStore.deleteAccion(accionId);
};
</script>

<style scoped>
.modal {
    display: block;
}
</style>