<template>
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-sign-out-alt mr-2"></i> Agregar Salida/Decisión
                    </h5>
                    <button type="button" class="close text-white" @click="$emit('close')">×</button>
                </div>

                <form @submit.prevent="guardar">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            Las salidas incluyen decisiones y acciones relacionadas con oportunidades de mejora, 
                            cambios en el SGC y necesidad de recursos (ISO 9001 §9.3.3).
                        </div>

                        <div class="form-group">
                            <label class="required">Tipo de Salida (ISO 9001 §9.3.3)</label>
                            <select v-model="form.tipo_salida" class="form-control" required>
                                <option value="">Seleccionar tipo de salida...</option>
                                <option v-for="(label, value) in tiposSalida" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="required">Descripción de la Decisión/Acción</label>
                            <textarea v-model="form.descripcion" class="form-control" rows="4" required
                                placeholder="Describa la decisión tomada o acción a realizar..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>Recursos Necesarios</label>
                            <textarea v-model="form.recursos_necesarios" class="form-control" rows="2"
                                placeholder="Recursos humanos, financieros, tecnológicos necesarios (opcional)"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Responsable de Implementación</label>
                            <select v-model="form.responsable_id" class="form-control">
                                <option :value="null">Sin asignar</option>
                                <option v-for="user in usuarios" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" v-model="crearCompromiso" class="mr-2">
                                Crear compromiso de seguimiento para esta salida
                            </label>
                        </div>

                        <div v-if="crearCompromiso" class="card bg-light p-3">
                            <h6>Datos del Compromiso</h6>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Descripción del Compromiso</label>
                                        <input type="text" v-model="compromisoData.descripcion" class="form-control"
                                            placeholder="Descripción breve del compromiso">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Fecha Límite</label>
                                        <input type="date" v-model="compromisoData.fecha_limite" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="$emit('close')">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-success" :disabled="isLoading">
                            <i class="fas fa-save mr-1"></i>
                            {{ isLoading ? 'Guardando...' : 'Guardar Salida' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRevisionDireccionStore } from '@/stores/revisionDireccionStore';
import axios from 'axios';

const props = defineProps({
    revisionId: { type: [Number, String], required: true }
});

const emit = defineEmits(['close', 'saved']);
const store = useRevisionDireccionStore();

const isLoading = ref(false);
const usuarios = ref([]);
const crearCompromiso = ref(false);

const tiposSalida = computed(() => store.tiposSalida);

const form = reactive({
    tipo_salida: '',
    descripcion: '',
    recursos_necesarios: '',
    responsable_id: null
});

const compromisoData = reactive({
    descripcion: '',
    fecha_limite: ''
});

onMounted(async () => {
    try {
        const res = await axios.get('/users/list');
        usuarios.value = res.data;
    } catch (err) {
        console.error('Error cargando usuarios');
    }
});

const guardar = async () => {
    isLoading.value = true;
    try {
        await store.addSalida(props.revisionId, form);
        
        // Si se marcó crear compromiso
        if (crearCompromiso.value && compromisoData.descripcion && compromisoData.fecha_limite) {
            await store.addCompromiso(props.revisionId, {
                descripcion: compromisoData.descripcion,
                fecha_limite: compromisoData.fecha_limite,
                responsable_id: form.responsable_id
            });
        }
        
        emit('saved', 'Salida agregada exitosamente');
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
