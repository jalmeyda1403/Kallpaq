<template>
    <div class="modal fade show" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);"
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-search-plus mr-2"></i> Análisis de Causa Raíz
                    </h5>
                    <button type="button" class="close text-white" @click="hallazgoStore.closeCausaRaizModal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Descripción del Hallazgo</label>
                        <textarea class="form-control" rows="3" :value="hallazgoStore.hallazgoForm.hallazgo_descripcion" readonly></textarea>
                    </div>
                    <div v-if="validationError" class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ validationError }}
                        <button type="button" class="close" @click="validationError = ''" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div v-if="hallazgoStore.loadingPlan" class="text-center my-5">
                        <div class="spinner-border text-danger" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>
                    <div v-else>
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
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="hallazgoStore.closeCausaRaizModal">Cancelar</button>
                    <button type="button" class="btn btn-danger" @click="handleSaveCausaRaiz">
                        <i class="fas fa-save mr-1"></i> Guardar Análisis
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'; // Import ref
import { useHallazgoStore } from '@/stores/hallazgoStore';

const hallazgoStore = useHallazgoStore();
const validationError = ref(''); // New ref

const validateCausaRaiz = () => {
    validationError.value = ''; // Clear previous errors
    const causa = hallazgoStore.causaRaiz;

    if (causa.causa_metodo === 'cinco_porques') {
        if (!causa.causa_por_que1 || !causa.causa_por_que2 || !causa.causa_por_que3) {
            validationError.value = 'Para la metodología "5 Porqués", debe completar al menos los primeros 3 "¿Por qué?".';
            return false;
        }
    } else if (causa.causa_metodo === 'ishikawa') {
        let filledFields = 0;
        const ishikawaFields = [
            causa.causa_mano_obra,
            causa.causa_metodologias,
            causa.causa_materiales,
            causa.causa_maquinas,
            causa.causa_medicion,
            causa.causa_medio_ambiente
        ];
        ishikawaFields.forEach(field => {
            if (field && field.trim() !== '') {
                filledFields++;
            }
        });
        if (filledFields < 3) {
            validationError.value = 'Para la metodología "Ishikawa", debe registrar al menos 3 campos de causa.';
            return false;
        }
    }
    return true;
};

const handleSaveCausaRaiz = () => {
    if (validateCausaRaiz()) {
        hallazgoStore.saveCausaRaiz();
    }
};
</script>

<style scoped>
.modal {
    display: block;
}
</style>