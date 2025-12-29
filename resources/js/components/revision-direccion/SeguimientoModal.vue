<template>
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-history mr-2"></i> 
                        Seguimiento del Compromiso: {{ compromiso.codigo }}
                    </h5>
                    <button type="button" class="close text-white" @click="$emit('close')">×</button>
                </div>

                <div class="modal-body">
                    <!-- Información del compromiso -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6>{{ compromiso.descripcion }}</h6>
                                    <small class="text-muted">
                                        Responsable: {{ compromiso.responsable?.name }} | 
                                        Fecha límite: {{ formatDate(compromiso.fecha_limite) }}
                                    </small>
                                </div>
                                <div class="col-md-4 text-right">
                                    <span class="badge badge-lg" :class="'badge-' + compromiso.estado_color">
                                        {{ compromiso.estado }}
                                    </span>
                                    <div class="mt-2">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-info" 
                                                 :style="{ width: compromiso.avance + '%' }">
                                                {{ compromiso.avance }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historial de seguimientos -->
                    <h6>Historial de Seguimientos</h6>
                    <div v-if="!compromiso.seguimientos?.length" class="text-center py-3 text-muted">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <p>No hay registros de seguimiento</p>
                    </div>
                    
                    <div v-else class="timeline">
                        <div v-for="seg in compromiso.seguimientos" :key="seg.id" class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ formatDate(seg.fecha) }}</strong>
                                    <span class="badge badge-info">{{ seg.avance_reportado }}%</span>
                                </div>
                                <p class="mb-1">{{ seg.comentario }}</p>
                                <small class="text-muted">Por: {{ seg.usuario?.name }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Nuevo seguimiento -->
                    <div class="card mt-3 bg-light">
                        <div class="card-header">
                            <i class="fas fa-plus mr-1"></i> Registrar Nuevo Seguimiento
                        </div>
                        <div class="card-body">
                            <form @submit.prevent="guardarSeguimiento">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Avance (%)</label>
                                            <input type="number" v-model.number="nuevoSeguimiento.avance_reportado" 
                                                   class="form-control" min="0" max="100" step="5" required>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <label>Comentario</label>
                                            <input type="text" v-model="nuevoSeguimiento.comentario" 
                                                   class="form-control" required
                                                   placeholder="Describa el avance o actividades realizadas...">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <button type="submit" class="btn btn-info btn-sm" :disabled="isLoading">
                                        <i class="fas fa-save mr-1"></i>
                                        {{ isLoading ? 'Guardando...' : 'Registrar Seguimiento' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="$emit('close')">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRevisionDireccionStore } from '@/stores/revisionDireccionStore';

const props = defineProps({
    compromiso: { type: Object, required: true }
});

const emit = defineEmits(['close', 'updated']);
const store = useRevisionDireccionStore();

const isLoading = ref(false);

const nuevoSeguimiento = reactive({
    avance_reportado: props.compromiso.avance || 0,
    comentario: ''
});

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('es-PE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const guardarSeguimiento = async () => {
    if (!nuevoSeguimiento.comentario.trim()) {
        alert('Ingrese un comentario');
        return;
    }
    
    isLoading.value = true;
    try {
        await store.registrarSeguimiento(props.compromiso.id, nuevoSeguimiento);
        nuevoSeguimiento.comentario = '';
        emit('updated');
    } catch (err) {
        alert('Error al registrar seguimiento: ' + (err.response?.data?.message || err.message));
    } finally {
        isLoading.value = false;
    }
};
</script>

<style scoped>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}
.timeline-item {
    position: relative;
    margin-bottom: 15px;
}
.timeline-marker {
    position: absolute;
    left: -26px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #17a2b8;
    border: 2px solid #fff;
}
.timeline-content {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
}
</style>
