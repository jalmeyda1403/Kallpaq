<template>
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h6 class="mb-0">{{ titulo }}</h6>
        </div>
        <div class="card-body">
            <p class="card-text text-muted small">{{ descripcion }}</p>
            <form @submit.prevent="handleSubmit" class="mb-3">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label for="accion_tipo">Tipo de Acción</label>
                        <select class="form-control" id="accion_tipo" :disabled="disabled" :required="!disabled"
                            v-model="nuevaAccion.accion_tipo">
                            <option value="">Seleccione un tipo...</option>
                            <option value="inmediata">Inmediata</option>
                            <option value="correctiva">Correctiva</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label for="accion_descripcion">Descripción de la Acción</label>
                        <div class="input-group">
                            <textarea class="form-control" id="accion_descripcion" rows="5"
                                placeholder="Describe la acción para atacar la causa raíz..."
                                v-model="nuevaAccion.accion_descripcion" :disabled="disabled" :required="!disabled"
                                maxlength="200">
                            </textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <small class="form-text text-muted">{{ nuevaAccion.accion_descripcion.length }} /
                                200</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="accion_responsable">Responsable</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text" class="form-control form-control-sm" id="accion_responsable"
                                placeholder="Responsable" v-model="nuevaAccion.accion_responsable" :disabled="disabled"
                                :required="!disabled">
                        </div>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="accion_fecha_inicio">Fecha Inicio</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                            <input type="date" class="form-control form-control-sm" id="accion_fecha_inicio"
                                :disabled="disabled" :required="!disabled" v-model="nuevaAccion.accion_fecha_inicio">
                        </div>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="accion_fecha_fin_planificada">Fecha Fin Planificada</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                            </div>
                            <input type="date" class="form-control form-control-sm" id="accion_fecha_fin_planificada"
                                :disabled="disabled" :required="!disabled"
                                v-model="nuevaAccion.accion_fecha_fin_planificada">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary btn-sm mr-2" @click="resetForm"
                            v-if="editingAccionId" :disabled="disabled">
                            <i class="fas fa-times"></i> Cancelar Edición
                        </button>
                        <button type="submit" class="btn btn-danger btn-sm" :disabled="disabled">
                            <i class="fas fa-plus"></i> {{ editingAccionId ? 'Actualizar Acción' : 'Agregar Acción' }}
                        </button>
                    </div>
                </div>
            </form>

            <div v-if="disabled" class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Las acciones están deshabilitadas para este estado de hallazgo
            </div>

            <div v-if="validationError" class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ validationError }}
                <button type="button" class="close" @click="validationError = ''" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Código</th>
                            <th>Descripción de la Acción</th>
                            <th>Tipo de Acción</th>
                            <th>Responsable</th>
                            <th>F. Inicio</th>
                            <th>F. Fin</th>
                            <th>Estado</th>
                            <th>Ciclo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!acciones.length">
                            <td colspan="9" class="text-center text-muted">No hay acciones registradas.</td>
                        </tr>
                        <tr v-for="accion in acciones" :key="accion.id">
                            <td style="width: 10%;">{{ accion.accion_cod }}</td>
                            <td>{{ accion.accion_descripcion }}</td>
                            <td>
                                <span v-if="accion.accion_tipo" class="badge"
                                    :class="getTipoAccionClass(accion.accion_tipo)">
                                    {{ getTipoAccionLabel(accion.accion_tipo) }}
                                </span>
                                <span v-else class="text-muted">N/A</span>
                            </td>
                            <td>{{ accion.accion_responsable || 'N/A' }}</td>
                            <td>{{ formatDate(accion.accion_fecha_inicio) }}</td>
                            <td>{{ formatDate(accion.accion_fecha_fin_planificada) }}</td>
                            <td><span class="badge badge-pill badge-info">{{ accion.accion_estado }}</span></td>
                            <td style="width: 5%;">{{ accion.accion_ciclo }} </td>
                            <td style="width: 10%;">
                                <button class="btn btn-secondary btn-sm ml-1" @click="editAccion(accion)"
                                    :disabled="disabled">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm ml-1" @click="$emit('eliminar', accion.id)"
                                    :disabled="disabled">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useHallazgoStore } from '@/stores/hallazgoStore';

const props = defineProps({
    titulo: String,
    descripcion: String,
    acciones: Array,
    tipoAccion: Number,
    disabled: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['guardar', 'eliminar', 'editar', 'update']); // Add 'update' event

const hallazgoStore = useHallazgoStore();
const validationError = ref(''); // New ref for validation errors

// Estado local para el formulario de la nueva acción
const nuevaAccion = ref({
    accion_cod: '',
    accion_descripcion: '',
    accion_responsable: '',
    accion_fecha_inicio: new Date().toISOString().slice(0, 10), // Fecha actual por defecto
    accion_fecha_fin_planificada: '',
    accion_tipo: props.tipoAccion, // Se asigna automáticamente
    accion_estado: 'programado', // Estado por defecto
    accion_ciclo: 1, // Ciclo por defecto
});

const editingAccionId = ref(null); // New ref for editing

// Propiedad computada para generar el siguiente código de acción
const nextAccionCod = computed(() => {
    const hallazgoCod = hallazgoStore.hallazgoForm.hallazgo_cod;
    const numAcciones = props.acciones.length + 1; // Contar las acciones existentes + 1 para la nueva
    return `${hallazgoCod}-${String(numAcciones).padStart(3, '0')}`;
});

const validateAccion = () => {
    validationError.value = ''; // Clear previous errors
    const accion = nuevaAccion.value;

    if (!accion.accion_descripcion || accion.accion_descripcion.trim() === '') {
        validationError.value = 'La descripción de la acción es obligatoria.';
        return false;
    }
    if (!accion.accion_responsable || accion.accion_responsable.trim() === '') {
        validationError.value = 'El responsable de la acción es obligatorio.';
        return false;
    }
    if (!accion.accion_fecha_inicio || accion.accion_fecha_inicio.trim() === '') {
        validationError.value = 'La fecha de inicio de la acción es obligatoria.';
        return false;
    }
    if (!accion.accion_fecha_fin_planificada || accion.accion_fecha_fin_planificada.trim() === '') {
        validationError.value = 'La fecha fin planificada de la acción es obligatoria.';
        return false;
    }
    return true;
};

const handleSubmit = () => {
    if (disabled) {
        validationError.value = 'No se pueden agregar o actualizar acciones en este estado de hallazgo.';
        return; // Stop if actions are disabled
    }

    if (!validateAccion()) {
        return; // Stop if validation fails
    }

    if (editingAccionId.value) {
        console.log('Submitting updated accion:', { id: editingAccionId.value, ...nuevaAccion.value }); // Add this log
        // Actualizar acción existente
        emit('update', { id: editingAccionId.value, ...nuevaAccion.value });
    } else {
        console.log('Submitting new accion:', { ...nuevaAccion.value }); // Add this log
        // Asignar el código de acción generado solo para nuevas acciones
        nuevaAccion.value.accion_cod = nextAccionCod.value;
        // Emitimos el evento con los datos de la nueva acción
        emit('guardar', { ...nuevaAccion.value });
    }

    // Reseteamos el formulario
    resetForm();
};

const editAccion = (accion) => {
    console.log('Editing accion:', accion); // Add this log
    editingAccionId.value = accion.id;
    nuevaAccion.value.accion_cod = accion.accion_cod;
    nuevaAccion.value.accion_tipo = accion.accion_tipo;
    nuevaAccion.value.accion_descripcion = accion.accion_descripcion;
    nuevaAccion.value.accion_responsable = accion.accion_responsable;
    nuevaAccion.value.accion_fecha_inicio = accion.accion_fecha_inicio;
    nuevaAccion.value.accion_fecha_fin_planificada = accion.accion_fecha_fin_planificada;
    nuevaAccion.value.accion_estado = accion.accion_estado;
    nuevaAccion.value.accion_ciclo = accion.accion_ciclo;
};

const resetForm = () => {
    editingAccionId.value = null;
    nuevaAccion.value.accion_tipo = '';
    nuevaAccion.value.accion_descripcion = '';
    nuevaAccion.value.accion_responsable = '';
    nuevaAccion.value.accion_fecha_inicio = new Date().toISOString().slice(0, 10);
    nuevaAccion.value.accion_fecha_fin_planificada = '';
    nuevaAccion.value.accion_estado = 'programado';
    nuevaAccion.value.accion_ciclo = 1;
};

const formatDate = (dateString) => {
    if (!dateString) {
        return '';
    }
    const date = new Date(dateString);
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const year = date.getFullYear();

    return `${day}/${month}/${year}`;
};

const getTipoAccionClass = (tipo) => {
    switch (tipo) {
        case 'inmediata': return 'badge-danger';
        case 'correctiva': return 'badge-warning';
        default: return 'badge-secondary';
    }
};

const getTipoAccionLabel = (tipo) => {
    const labels = {
        'inmediata': 'Inmediata',
        'correctiva': 'Correctiva'
    };
    return labels[tipo] || tipo;
};
</script>

<style scoped>
.form-group {
    margin-bottom: 0.5rem;
}

.table {
    font-size: 12px;
}

.table .badge {
    font-size: 12px;
    font-weight: 400;
}
</style>