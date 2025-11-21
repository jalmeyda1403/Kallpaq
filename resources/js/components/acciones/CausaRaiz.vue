<template>
    <div class="card mt-3">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">Análisis de Causa Raíz</h5>
        </div>
        <div class="card-body">
            <!-- Modo Vista: Solo mostrar resultado -->
            <div v-if="!isEditing && hasCausa">
                <div class="alert alert-info d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Método utilizado:</strong> {{ getMetodoLabel(causa.causa_metodo) }}
                    </div>
                    <button class="btn btn-warning btn-sm" @click="enableEdit">
                        <i class="fas fa-edit"></i> Editar Análisis
                    </button>
                </div>
                <div class="form-group">
                    <label><strong>Análisis Final (Causa Raíz):</strong></label>
                    <div class="border p-3 bg-light rounded">
                        {{ causa.causa_resultado || 'No especificado' }}
                    </div>
                </div>
            </div>

            <!-- Modo Edición: Formulario completo -->
            <div v-else>
                <div class="form-group">
                    <label>Método de Análisis</label>
                    <select v-model="causa.causa_metodo" class="form-control" @change="resetFields">
                        <option value="">Seleccione un método...</option>
                        <option value="cinco_porques">5 Porqués</option>
                        <option value="ishikawa">Ishikawa (6M)</option>
                    </select>
                </div>

                <!-- 5 Porqués -->
                <div v-if="causa.causa_metodo === 'cinco_porques'">
                    <div class="form-group" v-for="i in 5" :key="i">
                        <label>Por qué {{ i }}:</label>
                        <input type="text" v-model="causa['causa_por_que' + i]" class="form-control">
                    </div>
                </div>

                <!-- Ishikawa (6M) -->
                <div v-if="causa.causa_metodo === 'ishikawa'">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mano de Obra</label>
                                <textarea v-model="causa.causa_mano_obra" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Metodologías</label>
                                <textarea v-model="causa.causa_metodologias" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Materiales</label>
                                <textarea v-model="causa.causa_materiales" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Máquinas</label>
                                <textarea v-model="causa.causa_maquinas" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Medición</label>
                                <textarea v-model="causa.causa_medicion" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Medio Ambiente</label>
                                <textarea v-model="causa.causa_medio_ambiente" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Análisis Final (Causa Raíz) - Obligatorio para ambos métodos -->
                <div v-if="causa.causa_metodo" class="form-group">
                    <label><strong>Análisis Final (Causa Raíz): <span class="text-danger">*</span></strong></label>
                    <textarea v-model="causa.causa_resultado" class="form-control" rows="4" 
                              placeholder="Describa la causa raíz identificada después del análisis..." 
                              required></textarea>
                    <small class="form-text text-muted">
                        Este es el resultado final del análisis. Describa la causa raíz identificada.
                    </small>
                </div>

                <div class="mt-3 text-right">
                    <button v-if="hasCausa" class="btn btn-secondary mr-2" @click="cancelEdit">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button class="btn btn-primary" @click="saveCausa" :disabled="isSaving || !canSave">
                        <i class="fas fa-save"></i> {{ isSaving ? 'Guardando...' : 'Guardar Análisis' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import Swal from 'sweetalert2';

const props = defineProps({
    hallazgoId: {
        type: [Number, String],
        required: true
    }
});

const causa = reactive({
    causa_metodo: '',
    causa_por_que1: '',
    causa_por_que2: '',
    causa_por_que3: '',
    causa_por_que4: '',
    causa_por_que5: '',
    causa_mano_obra: '',
    causa_metodologias: '',
    causa_materiales: '',
    causa_maquinas: '',
    causa_medicion: '',
    causa_medio_ambiente: '',
    causa_resultado: ''
});

const isSaving = ref(false);
const isEditing = ref(true);
const hasCausa = ref(false);

const canSave = computed(() => {
    return causa.causa_metodo && causa.causa_resultado && causa.causa_resultado.trim().length > 0;
});

const getMetodoLabel = (metodo) => {
    const labels = {
        'cinco_porques': '5 Porqués',
        'ishikawa': 'Ishikawa (6M)'
    };
    return labels[metodo] || metodo;
};

const fetchCausa = async () => {
    try {
        const response = await axios.get(route('hallazgos.causas.listar', { hallazgo: props.hallazgoId }));
        if (response.data) {
            Object.assign(causa, response.data);
            hasCausa.value = true;
            isEditing.value = false;
        } else {
            hasCausa.value = false;
            isEditing.value = true;
        }
    } catch (error) {
        console.error('Error al cargar el análisis de causa:', error);
        hasCausa.value = false;
        isEditing.value = true;
    }
};

const saveCausa = async () => {
    if (!canSave.value) {
        Swal.fire('Advertencia', 'Debe completar el método de análisis y el análisis final (causa raíz).', 'warning');
        return;
    }

    isSaving.value = true;
    try {
        await axios.post(route('hallazgos.causas.storeOrUpdate', { hallazgo: props.hallazgoId }), causa);
        Swal.fire('Éxito', 'Análisis de causa guardado correctamente', 'success');
        hasCausa.value = true;
        isEditing.value = false;
    } catch (error) {
        console.error('Error al guardar:', error);
        Swal.fire('Error', 'No se pudo guardar el análisis', 'error');
    } finally {
        isSaving.value = false;
    }
};

const enableEdit = () => {
    isEditing.value = true;
};

const cancelEdit = () => {
    fetchCausa(); // Reload original data
    isEditing.value = false;
};

const resetFields = () => {
    // Clear method-specific fields when switching methods
    // BUT preserve causa_resultado as it's common to both methods
    causa.causa_por_que1 = '';
    causa.causa_por_que2 = '';
    causa.causa_por_que3 = '';
    causa.causa_por_que4 = '';
    causa.causa_por_que5 = '';
    causa.causa_mano_obra = '';
    causa.causa_metodologias = '';
    causa.causa_materiales = '';
    causa.causa_maquinas = '';
    causa.causa_medicion = '';
    causa.causa_medio_ambiente = '';
    // DO NOT reset causa_resultado - it's common to both methods
};

onMounted(() => {
    fetchCausa();
});
</script>

