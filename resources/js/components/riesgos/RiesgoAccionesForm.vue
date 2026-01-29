<template>
    <div>
        <Teleport to="body">
            <div class="modal fade" tabindex="-1" ref="modalRef" aria-hidden="true" style="z-index: 1060;">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">{{ isEditing ? 'Editar Plan de Tratamiento' :
                                'Nuevo Plan de Tratamiento' }}
                            </h5>
                            <button type="button" class="close text-white" @click="closeModal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="submitForm">
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="font-weight-bold custom-label">Descripción <span
                                                class="text-danger">*</span></label>
                                        <small class="text-muted">Caracteres: {{ form.accion_descripcion.length }} / 500</small>
                                    </div>
                                    <textarea class="form-control" v-model="form.accion_descripcion" rows="5"
                                        :maxlength="500" required></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold custom-label">Responsable <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" v-model="form.accion_responsable"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold custom-label">Correo Responsable <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-control"
                                                v-model="form.accion_responsable_correo" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold custom-label">Fecha Inicio <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" v-model="form.accion_fecha_inicio"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold custom-label">Fecha Fin Planificada <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control"
                                                v-model="form.accion_fecha_fin_planificada" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold custom-label">Estado <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" v-model="form.accion_estado" required>
                                        <option value="programada">Programada</option>
                                        <option value="en proceso">En Proceso</option>
                                        <option value="implementada">Implementada</option>
                                        <option value="desestimada">Desestimada</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="font-weight-bold custom-label">Comentario</label>
                                        <small class="text-muted">Caracteres: {{ form.accion_comentario ? form.accion_comentario.length : 0 }} / 500</small>
                                    </div>
                                    <textarea class="form-control" v-model="form.accion_comentario" rows="5"
                                        :maxlength="500"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer d-flex justify-content-between">
                                <small class="text-muted">* Campos obligatorios</small>
                                <div>
                                    <button type="button" class="btn btn-secondary" @click="closeModal">
                                        <i class="fa fa-times mr-1"></i> Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-danger ml-2" :disabled="saving">
                                        <i class="fa fa-save mr-1"></i>
                                        <span v-if="saving" class="spinner-border spinner-border-sm mr-1"
                                            role="status" aria-hidden="true"></span>
                                        {{ isEditing ? 'Actualizar' : 'Guardar' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted, onUnmounted, computed } from 'vue';
import { useRiesgoStore } from '@/stores/riesgoStore';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';

const props = defineProps({
    show: Boolean,
    actionData: Object
});

const emit = defineEmits(['close', 'saved']);

const store = useRiesgoStore();
const modalRef = ref(null);
const modalInstance = ref(null);
const saving = ref(false);

const form = reactive({
    id: null,
    accion_descripcion: '',
    accion_responsable: '',
    accion_responsable_correo: '',
    accion_fecha_inicio: '',
    accion_fecha_fin_planificada: '',
    accion_estado: 'programada',
    accion_comentario: ''
});

const isEditing = computed(() => !!form.id);

watch(() => props.show, (newVal) => {
    if (newVal) {
        if (props.actionData) {
            Object.assign(form, props.actionData);
            // Format dates
            if (form.accion_fecha_inicio) form.accion_fecha_inicio = form.accion_fecha_inicio.split('T')[0];
            if (form.accion_fecha_fin_planificada) form.accion_fecha_fin_planificada = form.accion_fecha_fin_planificada.split('T')[0];
        } else {
            resetForm();
        }

        if (modalInstance.value) {
            modalInstance.value.show();
        }
    } else {
        if (modalInstance.value) {
            modalInstance.value.hide();
        }
    }
});

onMounted(() => {
    if (modalRef.value) {
        modalInstance.value = new Modal(modalRef.value);
        modalRef.value.addEventListener('hidden.bs.modal', () => {
            emit('close');
        });
    }
});

onUnmounted(() => {
    if (modalInstance.value) {
        modalInstance.value.dispose();
    }
});

const resetForm = () => {
    form.id = null;
    form.accion_descripcion = '';
    form.accion_responsable = '';
    form.accion_responsable_correo = '';
    form.accion_fecha_inicio = '';
    form.accion_fecha_fin_planificada = '';
    form.accion_estado = 'programada';
    form.accion_comentario = '';
};

const closeModal = () => {
    if (modalInstance.value) {
        modalInstance.value.hide();
    }
    emit('close');
};

const submitForm = async () => {
    saving.value = true;
    try {
        if (isEditing.value) {
            await store.updateAccion(form.id, form);
            Swal.fire('Actualizado', 'El plan de tratamiento ha sido actualizado.', 'success');
        } else {
            await store.createAccion(store.riesgoActual.id, form);
            Swal.fire('Guardado', 'El plan de tratamiento ha sido creado.', 'success');
        }
        emit('saved');
        closeModal();
    } catch (error) {
        Swal.fire('Error', 'Hubo un problema al guardar el plan.', 'error');
    } finally {
        saving.value = false;
    }
};
</script>

<style scoped>
.custom-label {
    font-size: 0.9em !important;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif !important;
    font-weight: 600 !important;
    color: #495057 !important;
    letter-spacing: 0.2px !important;
}

.form-control {
    border: 1px solid #ced4da;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    border-radius: 0.375rem;
}

.form-control:focus {
    color: #495057;
    background-color: #fff;
    border-color: #dc3545;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.btn {
    border-radius: 0.375rem;
    font-weight: 500;
    padding: 0.375rem 0.75rem;
    transition: all 0.15s ease-in-out;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
    transform: translateY(-1px);
    box-shadow: 0 0.125rem 0.25rem rgba(220, 53, 69, 0.3);
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
    transform: translateY(-1px);
    box-shadow: 0 0.125rem 0.25rem rgba(108, 117, 125, 0.3);
}

.modal-header {
    background-color: #dc3545;
    color: white;
    border-top-left-radius: calc(0.3rem - 1px);
    border-top-right-radius: calc(0.3rem - 1px);
}

.modal-footer {
    background-color: #f8f9fa;
    padding: 1rem;
    border-bottom-right-radius: calc(0.3rem - 1px);
    border-bottom-left-radius: calc(0.3rem - 1px);
}

.form-group {
    margin-bottom: 1.1rem;
}
</style>
