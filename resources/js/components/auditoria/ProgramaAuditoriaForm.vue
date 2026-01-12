<template>
    <Teleport to="body">
        <div ref="modalRef" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title font-weight-bold">
                            {{ isEdit ? 'Editar Programa de Auditoría' : 'Nuevo Programa de Auditoría' }}
                        </h5>
                        <button type="button" class="close text-white" @click="close" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="savePrograma">
                        <div class="modal-body p-4">
                            <!-- Sección: Información General -->
                            <h6 class="text-dark border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle mr-1"></i> Información General
                            </h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-muted small">Año</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                        class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="number" v-model="form.pa_anio" class="form-control" :min="2020"
                                                :max="2030" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-muted small">Versión</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-code-branch"></i></span>
                                            </div>
                                            <input type="text" v-model="form.pa_version" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-muted small">Fecha Aprobación</label>
                                        <input type="date" v-model="form.pa_fecha_aprobacion" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-muted small">Estado</label>
                                        <select v-model="form.pa_estado" class="form-control" required>
                                            <option value="Borrador">Borrador</option>
                                            <option value="Aprobado">Aprobado</option>
                                            <option value="Cerrado">Cerrado</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección: Definición Estratégica -->
                            <h6 class="text-dark border-bottom pb-2 mb-3 mt-3">
                                <i class="fas fa-bullseye mr-1"></i> Definición Estratégica
                            </h6>
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="font-weight-bold text-muted small">Objetivo General</label>
                                    <small class="text-muted">
                                        {{ form.pa_objetivo_general ? form.pa_objetivo_general.length : 0 }}/1000
                                    </small>
                                </div>
                                <textarea v-model="form.pa_objetivo_general" class="form-control" rows="4"
                                    maxlength="1000"
                                    placeholder="Describa el objetivo principal del programa..."></textarea>
                            </div>
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="font-weight-bold text-muted small">Alcance</label>
                                    <small class="text-muted">
                                        {{ form.pa_alcance ? form.pa_alcance.length : 0 }}/1000
                                    </small>
                                </div>
                                <textarea v-model="form.pa_alcance" class="form-control" rows="4" maxlength="1000"
                                    placeholder="Defina el alcance de las auditorías..."></textarea>
                            </div>

                            <!-- Sección: Ejecución y Recursos -->
                            <h6 class="text-dark border-bottom pb-2 mb-3 mt-3">
                                <i class="fas fa-cogs mr-1"></i> Ejecución y Recursos
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-muted small">Métodos</label>
                                        <input type="text" v-model="form.pa_metodos" class="form-control"
                                            placeholder="Ej: Entrevistas, Muestreo...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold text-muted small">Criterios</label>
                                        <input type="text" v-model="form.pa_criterios" class="form-control"
                                            placeholder="Ej: ISO 9001:2015, Procedimientos...">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold text-muted small">Recursos / Presupuesto</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-hand-holding-usd"></i></span>
                                    </div>
                                    <textarea v-model="form.pa_recursos" class="form-control" rows="2"
                                        placeholder="Detalle los recursos necesarios..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-between">
                            <div></div>
                            <div>
                                <button type="button" class="btn btn-secondary" @click="close">
                                    <i class="fas fa-times mr-1"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-danger ml-2" :disabled="loading">
                                    <i class="fas fa-save mr-1"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, watch, computed, onMounted, onUnmounted, nextTick } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import { Modal } from 'bootstrap';

const props = defineProps({
    visible: Boolean,
    programa: Object
});

const emit = defineEmits(['update:visible', 'saved']);
const toast = useToast();
const loading = ref(false);
const modalRef = ref(null);
const modalInstance = ref(null);

const form = ref({
    pa_anio: new Date().getFullYear(),
    pa_version: '01',
    pa_fecha_aprobacion: '',
    pa_estado: 'Borrador',
    pa_objetivo_general: '',
    pa_alcance: '',
    pa_recursos: '',
    pa_metodos: '',
    pa_criterios: ''
});

const isEdit = computed(() => !!props.programa);

watch(() => props.visible, async (newVal) => {
    if (newVal) {
        if (props.programa) {
            form.value = { ...props.programa };
        } else {
            form.value = {
                pa_anio: new Date().getFullYear(),
                pa_version: '01',
                pa_fecha_aprobacion: new Date().toISOString().split('T')[0],
                pa_estado: 'Borrador',
                pa_objetivo_general: '',
                pa_alcance: '',
                pa_recursos: '',
                pa_metodos: '',
                pa_criterios: ''
            };
        }
        await nextTick();
        if (modalRef.value && !modalInstance.value) {
            modalInstance.value = new Modal(modalRef.value, {
                backdrop: 'static',
                keyboard: false
            });
        }
        modalInstance.value?.show();
    } else {
        modalInstance.value?.hide();
    }
}, { immediate: true });

// Handle 'hidden.bs.modal' to sync prop
onMounted(() => {
    if (modalRef.value) {
        modalRef.value.addEventListener('hidden.bs.modal', () => {
            emit('update:visible', false);
        });
    }
});

onUnmounted(() => {
    if (modalInstance.value) {
        modalInstance.value.dispose();
    }
});

const savePrograma = async () => {
    loading.value = true;
    try {
        if (isEdit.value) {
            await axios.put(`/api/auditoria/programas/${props.programa.id}`, form.value);
            toast.add({ severity: 'success', summary: 'Actualizado', detail: 'Programa actualizado correctamente', life: 3000 });
        } else {
            await axios.post('/api/auditoria/programas', form.value);
            toast.add({ severity: 'success', summary: 'Creado', detail: 'Programa creado correctamente', life: 3000 });
        }
        emit('saved');
        close();
    } catch (error) {
        console.error(error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo guardar el programa', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const close = () => {
    modalInstance.value?.hide();
};
</script>

<style scoped>
.custom-label {
    font-size: 0.9em !important;
    font-weight: 600 !important;
    color: #495057 !important;
}
</style>
