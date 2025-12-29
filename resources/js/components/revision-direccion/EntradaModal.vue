<template>
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-sign-in-alt mr-2"></i> Agregar Entrada de Revisión
                    </h5>
                    <button type="button" class="close text-white" @click="$emit('close')">×</button>
                </div>

                <form @submit.prevent="guardar">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="required">Tipo de Entrada (ISO 9001 §9.3.2)</label>
                            <select v-model="form.tipo_entrada" class="form-control" required>
                                <option value="">Seleccionar tipo de entrada...</option>
                                <option v-for="(label, value) in tiposEntrada" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="required">Título</label>
                            <input type="text" v-model="form.titulo" class="form-control" required
                                placeholder="Título descriptivo de la entrada">
                        </div>

                        <div class="form-group">
                            <label class="required">Descripción / Información</label>
                            <textarea v-model="form.descripcion" class="form-control" rows="4" required
                                placeholder="Describa la información relevante para esta entrada de la revisión..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fuente de Información</label>
                                    <input type="text" v-model="form.fuente" class="form-control"
                                        placeholder="Ej: Informe de auditoría, Reporte mensual...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Estado</label>
                                    <select v-model="form.estado" class="form-control">
                                        <option value="pendiente">Pendiente de revisión</option>
                                        <option value="revisado">Revisado</option>
                                        <option value="aprobado">Aprobado</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Datos Cuantitativos</label>
                            <textarea v-model="form.datos" class="form-control" rows="2"
                                placeholder="Indicadores, métricas o datos relevantes (opcional)"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Conclusión / Análisis</label>
                            <textarea v-model="form.conclusion" class="form-control" rows="2"
                                placeholder="Conclusiones o análisis de esta entrada (opcional)"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="$emit('close')">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary" :disabled="isLoading">
                            <i class="fas fa-save mr-1"></i>
                            {{ isLoading ? 'Guardando...' : 'Guardar Entrada' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { useRevisionDireccionStore } from '@/stores/revisionDireccionStore';

const props = defineProps({
    revisionId: { type: [Number, String], required: true }
});

const emit = defineEmits(['close', 'saved']);
const store = useRevisionDireccionStore();

const isLoading = ref(false);
const tiposEntrada = computed(() => store.tiposEntrada);

const form = reactive({
    tipo_entrada: '',
    titulo: '',
    descripcion: '',
    fuente: '',
    datos: '',
    conclusion: '',
    estado: 'pendiente'
});

const guardar = async () => {
    isLoading.value = true;
    try {
        await store.addEntrada(props.revisionId, form);
        emit('saved', 'Entrada agregada exitosamente');
        emit('close');
    } catch (err) {
        alert('Error al guardar: ' + (err.response?.data?.message || err.message));
    } finally {
        isLoading.value = false;
    }
};
</script>

<style scoped>
label.required::after {
    content: ' *';
    color: red;
}
</style>
