<template>
    <!-- Contenedor del Modal con scroll habilitado -->
    <div class="modal fade show d-block" tabindex="-1" role="dialog"
        style="background: rgba(0,0,0,0.6); overflow-y: auto;">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg rounded-xl overflow-hidden">

                <!-- Encabezado con estética premium -->
                <div class="modal-header bg-dark text-white border-0 py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-info rounded p-2 mr-3 shadow-sm">
                            <i class="fas fa-history text-white"></i>
                        </div>
                        <div>
                            <h5 class="modal-title font-weight-bold mb-0">Gestión de Seguimiento</h5>
                            <small class="text-info font-weight-bold">{{ compromiso.codigo }}</small>
                        </div>
                    </div>
                    <button type="button" class="close text-white opacity-1" @click="$emit('close')" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-0 bg-light">
                    <!-- Banda informativa superior -->
                    <div class="bg-white border-bottom px-4 py-3 shadow-xs">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h6 class="text-dark font-weight-bold mb-1">{{ compromiso.descripcion }}</h6>
                                <div class="d-flex flex-wrap align-items-center mt-2 mt-md-0">
                                    <span class="mr-3 small"><i class="fas fa-user-circle mr-1 text-muted"></i>
                                        <strong>Responsable:</strong> {{ compromiso.responsable?.name }}</span>
                                    <span class="mr-3 small"><i class="fas fa-calendar-alt mr-1 text-muted"></i>
                                        <strong>Fecha Límite:</strong> {{ formatDate(compromiso.fecha_limite) }}</span>
                                    <span class="badge badge-pill font-weight-bold shadow-sm px-3"
                                        :class="getBadgeClass(compromiso.estado)">
                                        {{ getEstadoLabel(compromiso.estado) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-4 mt-3 mt-lg-0">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="small font-weight-bold text-muted uppercase tracking-wider">Avance
                                        Consolidado</span>
                                    <span class="small font-weight-bold"
                                        :class="compromiso.avance >= 100 ? 'text-success' : 'text-info'">{{
                                            compromiso.avance }}%</span>
                                </div>
                                <div class="progress rounded-pill overflow-hidden shadow-inner"
                                    style="height: 12px; background-color: #eee;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated shadow-sm"
                                        :class="compromiso.avance >= 100 ? 'bg-success' : 'bg-info'" role="progressbar"
                                        :style="{ width: compromiso.avance + '%' }" :aria-valuenow="compromiso.avance"
                                        aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="row">
                            <!-- Columna de Historial (Timeline) -->
                            <div class="col-lg-7">
                                <h6 class="font-weight-bold text-dark d-flex align-items-center mb-4">
                                    <i class="fas fa-stream mr-2 text-info"></i> Historial de Actividades
                                    <span class="badge badge-light border ml-auto font-weight-normal">{{
                                        compromiso.seguimientos?.length || 0 }} registros</span>
                                </h6>

                                <!-- Estado vacío -->
                                <div v-if="!compromiso.seguimientos?.length"
                                    class="text-center py-5 bg-white rounded-lg shadow-sm border mt-2">
                                    <div class="text-muted opacity-3 mb-3">
                                        <i class="fas fa-clipboard-list fa-3x"></i>
                                    </div>
                                    <h6 class="text-muted font-weight-bold">Sin registros previos</h6>
                                    <p class="text-muted small px-4 mb-0">Aún no se han reportado avances para este
                                        compromiso. Utilice el panel lateral para registrar el primero.</p>
                                </div>

                                <!-- Lista de seguimientos con Timeline Moderno -->
                                <div v-else class="history-scroll-panel pr-3"
                                    style="max-height: 520px; overflow-y: auto;">
                                    <div class="modern-timeline">
                                        <div v-for="(seg, index) in compromiso.seguimientos" :key="seg.id"
                                            class="mt-item">
                                            <!-- Línea conectora -->
                                            <div class="mt-line" v-if="index !== compromiso.seguimientos.length - 1">
                                            </div>
                                            <!-- Punto de la línea de tiempo -->
                                            <div class="mt-dot shadow-sm"
                                                :class="index === 0 ? 'bg-info pulse' : 'bg-secondary opacity-5'"></div>

                                            <div
                                                class="card border-0 shadow-xs mb-3 rounded-lg flex-grow-1 overflow-hidden transition-all hover-shadow">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <div class="text-dark font-weight-bold small mb-0">{{
                                                                seg.usuario?.name || 'Usuario desconocido' }}</div>
                                                            <div class="text-muted extra-small"><i
                                                                    class="far fa-clock mr-1"></i> {{
                                                                        formatDate(seg.created_at, true) }}</div>
                                                        </div>
                                                        <div class="badge-soft-info border px-2 py-1 rounded">
                                                            <span class="font-weight-bold" style="font-size: 0.9rem;">{{
                                                                seg.avance_nuevo }}%</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-secondary small mb-0 p-2 bg-light rounded info-border-left"
                                                        style="line-height: 1.5;">
                                                        {{ seg.comentario }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Columna de Registro de Formulario -->
                            <div class="col-lg-5 mt-4 mt-lg-0">
                                <div class="card border-0 shadow-sm rounded-lg overflow-hidden sticky-top"
                                    style="top: 20px;">
                                    <div class="card-header bg-white border-0 pt-4 pb-0">
                                        <h6 class="font-weight-bold text-dark d-flex align-items-center m-0">
                                            <i class="fas fa-plus-circle mr-2 text-info"></i> Reportar Nuevo Avance
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <form @submit.prevent="guardarSeguimiento">
                                            <div class="form-group mb-4">
                                                <div class="d-flex justify-content-between align-items-end mb-2">
                                                    <label
                                                        class="extra-small font-weight-bold text-muted text-uppercase mb-0">Nivel
                                                        de Avance (%)</label>
                                                    <span class="h4 font-weight-bold text-info mb-0">{{
                                                        nuevoSeguimiento.avance_reportado }}%</span>
                                                </div>
                                                <!-- Range slider para mejor UX -->
                                                <input type="range" class="custom-range range-info mb-3" min="0"
                                                    max="100" step="1"
                                                    v-model.number="nuevoSeguimiento.avance_reportado">
                                                <!-- Input numérico opcional -->
                                                <div
                                                    class="input-group input-group-sm mb-2 shadow-xs rounded overflow-hidden">
                                                    <div class="input-group-prepend">
                                                        <span
                                                            class="input-group-text bg-white border-right-0 text-muted"><i
                                                                class="fas fa-edit"></i></span>
                                                    </div>
                                                    <input type="number"
                                                        v-model.number="nuevoSeguimiento.avance_reportado"
                                                        class="form-control border-left-0 font-weight-bold text-info no-focus-outline"
                                                        min="0" max="100" step="1" required>
                                                </div>
                                            </div>

                                            <div class="form-group mb-4">
                                                <label
                                                    class="extra-small font-weight-bold text-muted text-uppercase mb-2">Descripción
                                                    de Actividades</label>
                                                <textarea v-model="nuevoSeguimiento.comentario"
                                                    class="form-control border shadow-xs no-focus-outline" rows="6"
                                                    required
                                                    placeholder="Detalle los avances logrados, tareas concluidas o recursos empleados..."></textarea>
                                            </div>

                                            <button type="submit"
                                                class="btn btn-info btn-block py-3 shadow font-weight-bold transition-all"
                                                :disabled="isLoading || !nuevoSeguimiento.comentario.trim()">
                                                <span v-if="isLoading">
                                                    <i class="fas fa-spinner fa-spin mr-1"></i> Procesando...
                                                </span>
                                                <span v-else>
                                                    <i class="fas fa-check-circle mr-1"></i> Guardar Seguimiento
                                                </span>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Tooltip/Info panel -->
                                <div class="mt-4 p-3 bg-white rounded-lg border shadow-xs small text-muted">
                                    <div class="d-flex">
                                        <div class="bg-warning-soft rounded-circle px-2 py-1 mr-2 mt-1">
                                            <i class="fas fa-lightbulb text-warning small"></i>
                                        </div>
                                        <div>
                                            <strong>Tip de Registro:</strong> Sea específico en sus comentarios para
                                            facilitar la auditoría y control de cumplimiento.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer del Modal -->
                <div class="modal-footer bg-white border-top py-3 justify-content-center">
                    <button type="button" class="btn btn-dark px-5 rounded-pill shadow-sm transition-all hover-up"
                        @click="$emit('close')">
                        Finalizar Revisión
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

/**
 * Formatear fecha al estilo peruano con opción de hora
 */
const formatDate = (date, withTime = false) => {
    if (!date) return '-';
    const options = {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    };
    if (withTime) {
        options.hour = '2-digit',
            options.minute = '2-digit'
    }
    return new Date(date).toLocaleDateString('es-PE', options);
};

/**
 * Obtener clase de badge según estado
 */
const getBadgeClass = (estado) => {
    const classes = {
        programada: 'badge-info',
        en_proceso: 'badge-info',
        pendiente: 'badge-warning',
        completado: 'badge-success',
        vencido: 'badge-danger',
        cancelado: 'badge-secondary'
    };
    return classes[estado] || 'badge-secondary';
};

/**
 * Obtener etiqueta amigable del estado
 */
const getEstadoLabel = (estado) => {
    const labels = {
        programada: 'Programada',
        en_proceso: 'En Proceso',
        pendiente: 'Pendiente',
        completado: 'Completado',
        vencido: 'Vencido',
        cancelado: 'Cancelado'
    };
    return labels[estado] || estado;
};

/**
 * Guardar el nuevo registro de seguimiento
 */
const guardarSeguimiento = async () => {
    if (!nuevoSeguimiento.comentario.trim()) {
        return;
    }

    isLoading.value = true;
    try {
        const payload = {
            comentario: nuevoSeguimiento.comentario,
            avance: nuevoSeguimiento.avance_reportado
        };
        await store.registrarSeguimiento(props.compromiso.id, payload);
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
/* Utilidades de Diseño Premium */
.rounded-xl {
    border-radius: 1rem !important;
}

.extra-small {
    font-size: 0.7rem;
}

.shadow-xs {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.opacity-1 {
    opacity: 1 !important;
}

.opacity-3 {
    opacity: 0.3 !important;
}

.opacity-5 {
    opacity: 0.5 !important;
}

.shadow-inner {
    box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
}

.uppercase {
    text-transform: uppercase;
}

.tracking-wider {
    letter-spacing: 0.05em;
}

.no-focus-outline:focus {
    outline: none;
    box-shadow: none;
    border-color: #17a2b8 !important;
}

/* Implementación de Scroll Fix */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1050;
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    overflow-y: auto;
    /* IMPORTANTE: Habilita el scroll del modal */
    outline: 0;
}

/* Timeline Moderno Estilizado */
.modern-timeline {
    position: relative;
    padding-left: 10px;
}

.mt-item {
    position: relative;
    padding-left: 25px;
    display: flex;
}

.mt-line {
    position: absolute;
    left: 4.5px;
    top: 25px;
    bottom: -15px;
    width: 1.5px;
    background: #e9ecef;
    z-index: 1;
}

.mt-dot {
    position: absolute;
    left: 0;
    top: 15px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    z-index: 2;
    border: 2px solid #fff;
}

.info-border-left {
    border-left: 3px solid #17a2b8;
}

.badge-soft-info {
    background-color: rgba(23, 162, 184, 0.08);
    color: #17a2b8;
}

.bg-warning-soft {
    background-color: rgba(255, 193, 7, 0.15);
}

/* Range Input UI Enhancement */
.range-info {
    height: 5px;
}

.range-info::-webkit-slider-thumb {
    background: #17a2b8;
    width: 16px;
    height: 16px;
}

/* Micro-animaciones */
.transition-all {
    transition: all 0.3s ease;
}

.hover-up:hover {
    transform: translateY(-2px);
}

.hover-shadow:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
}

.pulse {
    box-shadow: 0 0 0 0 rgba(23, 162, 184, 0.7);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(23, 162, 184, 0.7);
    }

    70% {
        transform: scale(1);
        box-shadow: 0 0 0 10px rgba(23, 162, 184, 0);
    }

    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(23, 162, 184, 0);
    }
}

/* Scroll personalización */
.history-scroll-panel::-webkit-scrollbar {
    width: 5px;
}

.history-scroll-panel::-webkit-scrollbar-track {
    background: transparent;
}

.history-scroll-panel::-webkit-scrollbar-thumb {
    background: #e0e0e0;
    border-radius: 10px;
}

.history-scroll-panel::-webkit-scrollbar-thumb:hover {
    background: #ccc;
}

.sticky-top {
    z-index: 5;
}
</style>
