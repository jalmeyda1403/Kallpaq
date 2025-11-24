<template>
    <div class="modal fade" id="accionCreateModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-plus mr-2"></i>Nueva Acción</h5>
                    <button type="button" class="close text-white" @click="closeModal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveAccion">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Proceso Afectado <span class="text-danger">*</span></label>
                                    <select v-model="form.proceso_id" class="form-control" required>
                                        <option value="" disabled>Seleccione un proceso...</option>
                                        <option v-for="proceso in procesos" :key="proceso.id" :value="proceso.id">
                                            {{ proceso.proceso_nombre }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Tipo de Acción <span class="text-danger">*</span></label>
                                    <select v-model="form.accion_tipo" class="form-control" required>
                                        <option value="" disabled>Seleccione un tipo...</option>
                                        <option value="inmediata">Inmediata</option>
                                        <option value="correctiva">Correctiva</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Descripción de la Acción <span class="text-danger">*</span></label>
                            <textarea v-model="form.accion_descripcion" class="form-control" required rows="3" placeholder="Describa la acción a tomar para abordar la causa raíz..."></textarea>
                            <small class="form-text text-muted">Caracteres: {{ form.accion_descripcion.length }}/200</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Responsable <span class="text-danger">*</span></label>
                                    <input type="text" v-model="form.accion_responsable" class="form-control" required placeholder="Nombre del responsable">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Estado</label>
                                    <input type="text" class="form-control" value="Programada" readonly disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Fecha de Inicio <span class="text-danger">*</span></label>
                                    <input type="date" v-model="form.accion_fecha_inicio" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Fecha Fin Planificada <span class="text-danger">*</span></label>
                                    <input type="date" v-model="form.accion_fecha_fin_planificada" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary mr-2" @click="closeModal">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-danger" :disabled="isSaving">
                                <i class="fas fa-save mr-1" v-if="!isSaving"></i>
                                {{ isSaving ? 'Guardando...' : 'Guardar Acción' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import Swal from 'sweetalert2';

const props = defineProps({
    hallazgoId: {
        type: [Number, String],
        required: true
    },
    procesos: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['accion-creada']);

const form = reactive({
    proceso_id: '',
    accion_tipo: '',
    accion_descripcion: '',
    accion_responsable: '',
    accion_fecha_inicio: '',
    accion_fecha_fin_planificada: ''
});

const isSaving = ref(false);

const saveAccion = async () => {
    if (!form.proceso_id) {
        Swal.fire('Error', 'Debe seleccionar un proceso.', 'error');
        return;
    }

    if (!form.accion_tipo) {
        Swal.fire('Error', 'Debe seleccionar un tipo de acción.', 'error');
        return;
    }

    isSaving.value = true;
    try {
        await axios.post(route('hallazgos.acciones.store', {
            hallazgo: props.hallazgoId,
            proceso: form.proceso_id
        }), form);

        Swal.fire('Éxito', 'Acción creada correctamente', 'success');
        closeModal();
        emit('accion-creada');
        resetForm();
    } catch (error) {
        console.error('Error al crear acción:', error);
        Swal.fire('Error', 'No se pudo crear la acción', 'error');
    } finally {
        isSaving.value = false;
    }
};

const closeModal = () => {
    $('#accionCreateModal').modal('hide');
};

const resetForm = () => {
    form.proceso_id = props.procesos.length > 0 ? props.procesos[0].id : '';
    form.accion_tipo = '';
    form.accion_descripcion = '';
    form.accion_responsable = '';
    form.accion_fecha_inicio = '';
    form.accion_fecha_fin_planificada = '';
};

// Listen for open event
onMounted(() => {
    document.addEventListener('open-create-accion-modal', () => {
        // Auto-select first process if available and none selected
        if (!form.proceso_id && props.procesos.length > 0) {
            form.proceso_id = props.procesos[0].id;
        }
        $('#accionCreateModal').modal('show');
    });
});
</script>
