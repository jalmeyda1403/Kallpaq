<template>
    <div class="modal fade" id="accionCreateModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Acción</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveAccion">
                        <div class="form-group">
                            <label>Proceso Afectado</label>
                            <select v-model="form.proceso_id" class="form-control" required>
                                <option value="" disabled>Seleccione un proceso...</option>
                                <option v-for="proceso in procesos" :key="proceso.id" :value="proceso.id">
                                    {{ proceso.proceso_nombre }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Descripción de la Acción</label>
                            <textarea v-model="form.accion_descripcion" class="form-control" required rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Responsable</label>
                            <input type="text" v-model="form.accion_responsable" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Fecha de Inicio</label>
                            <input type="date" v-model="form.accion_fecha_inicio" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Fecha Fin Planificada</label>
                            <input type="date" v-model="form.accion_fecha_fin_planificada" class="form-control" required>
                        </div>
                        <div class="text-right">
                            <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" :disabled="isSaving">
                                {{ isSaving ? 'Guardando...' : 'Guardar' }}
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

    isSaving.value = true;
    try {
        await axios.post(route('hallazgos.acciones.store', { 
            hallazgo: props.hallazgoId, 
            proceso: form.proceso_id 
        }), form);
        
        Swal.fire('Éxito', 'Acción creada correctamente', 'success');
        $('#accionCreateModal').modal('hide');
        emit('accion-creada');
        resetForm();
    } catch (error) {
        console.error('Error al crear acción:', error);
        Swal.fire('Error', 'No se pudo crear la acción', 'error');
    } finally {
        isSaving.value = false;
    }
};

const resetForm = () => {
    form.proceso_id = props.procesos.length > 0 ? props.procesos[0].id : '';
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
