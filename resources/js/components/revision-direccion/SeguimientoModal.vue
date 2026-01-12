<template>
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6); overflow-y: auto;">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <!-- Header estilo SalidasNC (Rojo + Blanco) -->
                <div class="modal-header bg-danger text-white border-0 py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-history mr-3 fa-lg"></i>
                        <div>
                            <h5 class="modal-title font-weight-bold">Seguimiento de Compromiso</h5>
                            <small class="text-white-50 font-weight-bold">{{ compromiso.codigo }}</small>
                        </div>
                    </div>
                    <button type="button" class="close text-white" @click="$emit('close')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-0 bg-light">
                    <!-- Banda Superior de Información del Compromiso -->
                    <div class="bg-white border-bottom px-4 py-3">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h6 class="text-dark font-weight-bold mb-2">{{ compromiso.descripcion }}</h6>
                                <div class="d-flex flex-wrap text-muted small">
                                    <span class="mr-3"><i class="fas fa-user-circle mr-1"></i> {{
                                        compromiso.responsable?.name }}</span>
                                    <span class="mr-3"><i class="fas fa-calendar-alt mr-1"></i> {{
                                        formatDate(compromiso.fecha_limite) }}</span>
                                    <span class="badge badge-pill" :class="getBadgeClass(compromiso.estado)">
                                        {{ getEstadoLabel(compromiso.estado) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-4 mt-3 mt-lg-0 text-lg-right">
                                <div class="d-flex align-items-center justify-content-lg-end">
                                    <span class="font-weight-bold mr-2 text-dark small text-uppercase">Avance
                                        Actual</span>
                                    <h3 class="font-weight-bold mb-0"
                                        :class="compromiso.avance >= 100 ? 'text-success' : 'text-danger'">
                                        {{ compromiso.avance }}%
                                    </h3>
                                </div>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar"
                                        :class="compromiso.avance >= 100 ? 'bg-success' : 'bg-danger'"
                                        role="progressbar" :style="{ width: compromiso.avance + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row no-gutters">
                        <!-- Left Column: Timeline Histórico -->
                        <div class="col-lg-7 border-right bg-white p-0">
                            <div class="p-3 border-bottom bg-light">
                                <h6 class="font-weight-bold text-dark mb-0"><i
                                        class="fas fa-stream mr-2 text-muted"></i>Historial de Actividades</h6>
                            </div>
                            <div class="history-panel p-4" style="height: 500px; overflow-y: auto;">
                                <div v-if="!compromiso.seguimientos?.length" class="text-center py-5 text-muted">
                                    <i class="fas fa-clipboard-list fa-3x mb-3 opacity-25"></i>
                                    <p>No hay seguimientos registrados.</p>
                                </div>

                                <div v-else class="timeline">
                                    <div v-for="seg in compromiso.seguimientos" :key="seg.id" class="timeline-item">
                                        <div class="timeline-marker bg-white border-danger"></div>
                                        <div class="timeline-content">
                                            <div class="card border border-light shadow-sm mb-3">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <strong class="text-dark small">{{ seg.usuario?.name }}</strong>
                                                        <span class="text-muted extra-small">{{
                                                            formatDate(seg.created_at, true) }}</span>
                                                    </div>
                                                    <p class="mb-2 small text-secondary">{{ seg.comentario }}</p>
                                                    <div class="badge badge-light border text-dark">
                                                        Avance: <strong>{{ seg.avance_nuevo }}%</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Formulario de Nuevo Seguimiento -->
                        <div class="col-lg-5 bg-white p-0">
                            <div class="p-3 border-bottom bg-light">
                                <h6 class="font-weight-bold text-dark mb-0"><i
                                        class="fas fa-plus-circle mr-2 text-danger"></i>Reportar Avance</h6>
                            </div>
                            <div class="p-4">
                                <div class="alert alert-light border shadow-sm mb-4">
                                    <small class="text-muted d-block mb-1">Porcentaje de Avance Logrado</small>
                                    <div class="d-flex align-items-center">
                                        <input type="range" class="custom-range flex-grow-1 mr-3" min="0" max="100"
                                            v-model.number="nuevoSeguimiento.avance_reportado">
                                        <input type="number"
                                            class="form-control form-control-sm font-weight-bold text-center"
                                            style="width: 70px;" v-model.number="nuevoSeguimiento.avance_reportado"
                                            min="0" max="100">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold custom-label">Comentarios / Evidencias</label>
                                    <textarea v-model="nuevoSeguimiento.comentario"
                                        class="form-control bg-light border-0" rows="6"
                                        placeholder="Detalle las actividades realizadas..."></textarea>
                                </div>

                                <div class="mt-4">
                                    <button class="btn btn-danger btn-block font-weight-bold py-2 shadow-sm"
                                        @click="guardarSeguimiento"
                                        :disabled="isLoading || !nuevoSeguimiento.comentario.trim()">
                                        <span v-if="isLoading"><i class="fas fa-spinner fa-spin mr-1"></i>
                                            Procesando...</span>
                                        <span v-else>Registrar Seguimiento</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
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

const formatDate = (date, withTime = false) => {
    if (!date) return '-';
    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
    if (withTime) { options.hour = '2-digit'; options.minute = '2-digit'; }
    return new Date(date).toLocaleDateString('es-PE', options);
};

const getBadgeClass = (estado) => {
    const map = { programada: 'badge-info', pendiente: 'badge-warning', completado: 'badge-success', vencido: 'badge-danger' };
    return map[estado] || 'badge-secondary';
};

const getEstadoLabel = (estado) => estado.charAt(0).toUpperCase() + estado.slice(1);

const guardarSeguimiento = async () => {
    if (!nuevoSeguimiento.comentario.trim()) return;
    isLoading.value = true;
    try {
        await store.registrarSeguimiento(props.compromiso.id, {
            comentario: nuevoSeguimiento.comentario,
            avance: nuevoSeguimiento.avance_reportado
        });
        nuevoSeguimiento.comentario = '';
        emit('updated');
        // No cerramos el modal automáticamente para permitir ver el update en el timeline si se desea, o podríamos cerrarlo.
        // UX improvement: Keep open to show history update, maybe clear form.
    } catch (err) {
        alert('Error: ' + err.message);
    } finally {
        isLoading.value = false;
    }
};
</script>

<style scoped>
.custom-label {
    font-size: 0.85em;
    font-weight: 700;
    color: #495057;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
}

.modal-content {
    border-radius: 0.5rem;
    overflow: hidden;
}

/* Timeline CSS */
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    padding-left: 30px;
    margin-bottom: 1.5rem;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: 6px;
    top: 0;
    bottom: -1.5rem;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-item:last-child::before {
    display: none;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 5px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 3px solid #dc3545;
    /* Red border matching theme */
    z-index: 1;
}

.opacity-25 {
    opacity: 0.25;
}

.extra-small {
    font-size: 0.75rem;
}

textarea {
    resize: none;
}
</style>
