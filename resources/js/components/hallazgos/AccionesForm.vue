<template>
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">{{ titulo }}</h6>
            <p class="card-text text-muted small">{{ descripcion }}</p>
        </div>
        <div class="card-body">
            <form @submit.prevent="handleSubmit" class="mb-3">
                <div class="form-group">
                    <textarea class="form-control" rows="2" placeholder="Describe la acción..." v-model="nuevaAccion.accion_descripcion" required></textarea>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <input type="text" class="form-control form-control-sm" placeholder="Responsable" v-model="nuevaAccion.accion_responsable">
                    </div>
                    <div class="col-md-4 form-group">
                         <input type="date" class="form-control form-control-sm" v-model="nuevaAccion.accion_fecha_fin_planificada">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-danger btn-sm btn-block"><i class="fas fa-plus"></i> Agregar Acción</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Descripción</th>
                            <th>Responsable</th>
                            <th>Plazo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!acciones.length">
                            <td colspan="5" class="text-center text-muted">No hay acciones registradas.</td>
                        </tr>
                        <tr v-for="accion in acciones" :key="accion.id">
                            <td>{{ accion.accion_descripcion }}</td>
                            <td>{{ accion.accion_responsable || 'N/A' }}</td>
                            <td>{{ accion.accion_fecha_fin_planificada || 'N/A' }}</td>
                            <td><span class="badge badge-pill badge-info">{{ accion.accion_estado }}</span></td>
                            <td>
                                <button class="btn btn-secondary btn-sm" @click="$emit('editar', accion.id)"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-danger btn-sm" @click="$emit('eliminar', accion.id)"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue';

const props = defineProps({
    titulo: String,
    descripcion: String,
    acciones: Array,
    tipoAccion: Number,
});

const emit = defineEmits(['guardar', 'eliminar', 'editar']);

// Estado local para el formulario de la nueva acción
const nuevaAccion = ref({
    accion_descripcion: '',
    accion_responsable: '',
    accion_fecha_fin_planificada: '',
    tipo_accion: props.tipoAccion, // Se asigna automáticamente
    accion_estado: 'pendiente' // Estado por defecto
});

const handleSubmit = () => {
    // Emitimos el evento con los datos de la nueva acción
    emit('guardar', { ...nuevaAccion.value });
    
    // Reseteamos el formulario
    nuevaAccion.value.accion_descripcion = '';
    nuevaAccion.value.accion_responsable = '';
    nuevaAccion.value.accion_fecha_fin_planificada = '';
};
</script>

<style scoped>
.form-group {
    margin-bottom: 0.5rem;
}
.table {
    font-size: 12px;
}

</style>