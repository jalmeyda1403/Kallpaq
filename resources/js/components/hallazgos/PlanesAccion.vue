<template>
    <div class="modal fade show" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);"
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
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
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="fas fa-search-plus mr-2"></i>Análisis de Causa Raíz (Obligatorio)</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Metodología de Análisis</label>
                                    <select class="form-control form-control-sm" v-model="hallazgoStore.causaRaiz.causa_metodo">
                                        <option value="cinco_porques">5 Porqués</option>
                                        <option value="ishikawa">Diagrama de Ishikawa (6M)</option>
                                    </select>
                                </div>

                                <div v-if="hallazgoStore.causaRaiz.causa_metodo === 'cinco_porques'">
                                    <div class="form-group" v-for="i in 5" :key="i">
                                        <label>¿Por qué #{{ i }}?</label>
                                        <input type="text" class="form-control form-control-sm" v-model="hallazgoStore.causaRaiz[`causa_por_que${i}`]">
                                    </div>
                                </div>

                                <div v-if="hallazgoStore.causaRaiz.causa_metodo === 'ishikawa'">
                                    <div class="row">
                                        <div class="col-md-6 form-group"><label>Mano de Obra</label><textarea class="form-control form-control-sm" v-model="hallazgoStore.causaRaiz.causa_mano_obra"></textarea></div>
                                        <div class="col-md-6 form-group"><label>Metodologías</label><textarea class="form-control form-control-sm" v-model="hallazgoStore.causaRaiz.causa_metodologias"></textarea></div>
                                        <div class="col-md-6 form-group"><label>Materiales</label><textarea class="form-control form-control-sm" v-model="hallazgoStore.causaRaiz.causa_materiales"></textarea></div>
                                        <div class="col-md-6 form-group"><label>Máquinas</label><textarea class="form-control form-control-sm" v-model="hallazgoStore.causaRaiz.causa_maquinas"></textarea></div>
                                        <div class="col-md-6 form-group"><label>Medición</label><textarea class="form-control form-control-sm" v-model="hallazgoStore.causaRaiz.causa_medicion"></textarea></div>
                                        <div class="col-md-6 form-group"><label>Medio Ambiente</label><textarea class="form-control form-control-sm" v-model="hallazgoStore.causaRaiz.causa_medio_ambiente"></textarea></div>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label class="font-weight-bold">Causa Raíz Identificada</label>
                                    <textarea class="form-control" rows="3" v-model="hallazgoStore.causaRaiz.causa_resultado" placeholder="Describa aquí la causa raíz principal concluida del análisis."></textarea>
                                </div>
                                <button class="btn btn-secondary btn-sm" @click="hallazgoStore.saveCausaRaiz">
                                    <i class="fas fa-save mr-1"></i> Guardar Análisis
                                </button>
                            </div>
                        </div>

                        <AccionesForm
                            titulo="Acciones Inmediatas"
                            descripcion="Acciones para contener el problema de forma inmediata."
                            :acciones="accionesInmediatas"
                            :tipo-accion="1"
                            @guardar="guardarNuevaAccion"
                            @eliminar="eliminarAccion"
                        />

                        <AccionesForm
                            v-if="esClasificacionNCM"
                            titulo="Acciones Correctivas"
                            descripcion="Acciones para eliminar la causa raíz y evitar la recurrencia."
                            :acciones="accionesCorrectivas"
                            :tipo-accion="2"
                            @guardar="guardarNuevaAccion"
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
</template>

<script setup>
import { computed } from 'vue';
import { useHallazgoStore } from '@/stores/hallazgoStore';
import AccionesForm from './AccionesForm.vue'; // Componente reutilizable que crearemos

const hallazgoStore = useHallazgoStore();

// Computed property para la lógica condicional principal
const esClasificacionNCM = computed(() => {
    const clasificacion = hallazgoStore.hallazgoForm.hallazgo_clasificacion;
    return ['NCM', 'Ncme'].includes(clasificacion);
});

// Computed properties para filtrar las acciones por tipo
const accionesInmediatas = computed(() => {
    return hallazgoStore.accionesDelPlan.filter(a => a.tipo_accion == 1); // 1 para Inmediata
});

const accionesCorrectivas = computed(() => {
    return hallazgoStore.accionesDelPlan.filter(a => a.tipo_accion == 2); // 2 para Correctiva
});

// Función para delegar el guardado de una nueva acción al store
const guardarNuevaAccion = (accionData) => {
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