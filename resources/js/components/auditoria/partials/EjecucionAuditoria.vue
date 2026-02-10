<template>
    <div class="h-100">
        <!-- VISTA PRINCIPAL: Plan de Trabajo Consolidado -->
        <div v-if="!selectedExecutionId" class="p-0">
            <div class="header-container mb-3 d-flex justify-content-between align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0 bg-transparent">
                        <li class="breadcrumb-item"><a href="#" @click.prevent="$emit('back')"
                                class="text-danger font-weight-bold">Auditorías</a></li>
                        <li class="breadcrumb-item active text-dark" aria-current="page">
                            Ejecución: <span class="font-weight-bold ml-1">{{ executionCode }}</span>
                        </li>
                    </ol>
                </nav>
                <button class="btn btn-sm btn-outline-secondary" @click="loadData" :disabled="loading">
                    <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }"></i> Refrescar Agenda
                </button>
            </div>


            <!-- BARRA DE PROGRESO GENERAL DE LA AUDITORÍA -->
            <div class="px-3 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small font-weight-bold text-uppercase text-secondary"
                        style="letter-spacing: 0.5px;">Avance General</span>
                    <span class="badge badge-pill badge-primary shadow-sm px-3">{{ globalProgress }}%</span>
                </div>
                <div class="progress shadow-inset"
                    style="height: 12px; border-radius: 20px; background-color: #e9ecef;">
                    <div class="progress-bar bg-gradient-primary" role="progressbar"
                        :style="{ width: globalProgress + '%' }" :aria-valuenow="globalProgress" aria-valuemin="0"
                        aria-valuemax="100">
                    </div>
                </div>
            </div>


            <div class="px-3">
                <p class="text-muted small mb-4 italic">
                    Las actividades han sido consolidadas por proceso. Seleccione una unidad para comenzar la evaluación
                    y
                    gestionar la evidencia.
                </p>

                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-danger" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="text-muted mt-2">Cargando plan de trabajo...</p>
                </div>

                <div v-else class="row row-cols-1 g-3">
                    <div v-for="group in groupedActivities" :key="group.key" class="col mb-3">
                        <div class="card execution-card border-0 shadow-sm h-100" :class="{
                            'border-left-success': group.estado === 'Concluida',
                            'border-left-primary': group.estado === 'En Curso',
                            'border-left-secondary': group.estado === 'Programada'
                        }">
                            <div class="card-body p-3">
                                <div class="row align-items-center">
                                    <!-- Fecha Principal o Multifecha -->
                                    <div
                                        class="col-md-2 text-center border-right align-self-stretch d-flex flex-column justify-content-center">
                                        <div v-if="group.sessions.length === 1" class="date-box">
                                            <span class="d-block font-weight-bold h5 mb-0 text-danger">{{
                                                formatDate(group.sessions[0].aea_fecha).split(' ')[0] }}</span>
                                            <span class="d-block text-uppercase small font-weight-bold text-muted">{{
                                                formatDate(group.sessions[0].aea_fecha).split(' ')[1] }}</span>
                                            <hr class="my-1">
                                            <span class="small font-weight-bold text-dark">
                                                <i class="far fa-clock mr-1"></i>
                                                {{ group.sessions[0].aea_hora_inicio?.substring(0, 5) }}
                                            </span>
                                        </div>
                                        <div v-else class="text-muted">
                                            <i class="fas fa-calendar-alt fa-2x mb-1 text-danger"></i>
                                            <div class="small font-weight-bold">{{ group.sessions.length }} Sesiones
                                            </div>
                                            <div class="x-small">Programadas</div>
                                        </div>
                                    </div>

                                    <!-- Información del Proceso / Actividad -->
                                    <div class="col-md-7">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge" :class="getStatusClass(group.estado)">
                                                {{ group.estado }}
                                            </span>
                                            <span class="ml-2 text-muted small">
                                                <i class="fas fa-layer-group mr-1"></i>
                                                {{ group.sessions.length > 1 ? 'ACTIVIDAD MULTISESIÓN' :
                                                    group.sessions[0].aea_tipo?.toUpperCase() }}
                                            </span>
                                        </div>

                                        <h6 class="font-weight-bold text-dark mb-1">
                                            {{ group.proceso_label }}
                                        </h6>

                                        <!-- Detalle de Sesiones si son múltiples -->
                                        <div v-if="group.sessions.length > 1"
                                            class="mb-2 p-2 bg-light rounded shadow-inner border small">
                                            <div v-for="s in group.sessions" :key="s.id" class="d-inline-block mr-3">
                                                <i class="far fa-calendar-check text-success mr-1"></i>
                                                {{ formatDateShort(s.aea_fecha) }} ({{ s.aea_hora_inicio?.substring(0,
                                                    5) }})
                                            </div>
                                        </div>

                                        <div class="d-flex flex-wrap mt-2">
                                            <!-- Auditor -->
                                            <div class="info-item mr-3 mb-1">
                                                <i class="fas fa-user-tie text-secondary mr-1"></i>
                                                <span class="small text-muted font-weight-bold">Auditor:</span>
                                                <span class="small ml-1">{{ store.getAuditorForAgenda(group.sessions[0])
                                                    }}</span>
                                            </div>

                                            <!-- Requisitos consolidados -->
                                            <div v-if="group.requirements_list && group.requirements_list.length > 0"
                                                class="info-item mb-1 w-100 border-top mt-1 pt-1">
                                                <i class="fas fa-clipboard-list text-secondary mr-1"></i>
                                                <span class="small text-muted font-weight-bold">Requisitos
                                                    Planificados:</span>
                                                <div class="mt-1">
                                                    <div v-for="(req, rIdx) in group.requirements_list" :key="rIdx"
                                                        class="ml-3 small">
                                                        <span class="font-weight-bold text-dark">{{ req.norma }}:</span>
                                                        <span class="text-primary ml-1">{{ req.numerals }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Acciones -->
                                    <div class="col-md-3 text-right">
                                        <!-- PROGRESO PARA PROCESOS (Checklist) -->
                                        <div v-if="['gabinete', 'apertura', 'cierre'].indexOf(group.sessions[0].aea_tipo) === -1 && group.estado !== 'Programada'"
                                            class="mb-3">
                                            <div class="d-flex justify-content-between small mb-1">
                                                <span class="text-muted">Progreso</span>
                                                <span class="font-weight-bold">{{ group.progress }}%</span>
                                            </div>
                                            <div class="progress" style="height: 6px; border-radius: 10px;">
                                                <div class="progress-bar"
                                                    :class="group.progress === 100 ? 'bg-success' : 'bg-primary'"
                                                    :style="{ width: group.progress + '%' }"></div>
                                            </div>
                                            <small class="text-muted" style="font-size: 0.7rem;">
                                                {{ group.completed_items }} de {{ group.total_items }} verificados
                                            </small>
                                            <div class="mt-1 text-right">
                                                <span class="badge badge-light border text-muted"
                                                    title="Horas Ejecutadas Estimadas">
                                                    <i class="fas fa-hourglass-half mr-1"></i> {{ group.executed_hours
                                                    }}
                                                    Hrs
                                                </span>
                                            </div>
                                        </div>

                                        <!-- PROGRESO PARA ESPECIALES (Archivo) -->
                                        <div v-if="['gabinete', 'apertura', 'cierre'].indexOf(group.sessions[0].aea_tipo) !== -1"
                                            class="mb-3">
                                            <div v-if="group.estado === 'Concluida'"
                                                class="alert alert-success p-1 mb-2 small text-center">
                                                <i class="fas fa-check-circle mr-1"></i> Documento Registrado
                                            </div>
                                            <div v-else class="alert alert-secondary p-1 mb-2 small text-center">
                                                <i class="fas fa-info-circle mr-1"></i> Pendiente Documento
                                            </div>
                                            <div class="text-right">
                                                <span class="badge badge-light border text-muted"
                                                    title="Horas Ejecutadas Estimadas">
                                                    <i class="fas fa-hourglass-half mr-1"></i> {{ group.executed_hours
                                                    }}
                                                    Hrs
                                                </span>
                                            </div>
                                        </div>


                                        <!-- BOTONES DE ACCIÓN -->
                                        <div class="d-flex justify-content-end align-items-center flex-wrap gap-2">
                                            <!-- Bloque Administrativo (Apertura/Cierre/Gabinete) -->
                                            <div v-if="['gabinete', 'apertura', 'cierre'].indexOf(group.sessions[0].aea_tipo) !== -1"
                                                class="d-flex align-items-center">
                                                <a v-if="group.main_session.aea_archivo"
                                                    :href="'/storage/' + group.main_session.aea_archivo" target="_blank"
                                                    class="btn btn-outline-info btn-sm rounded-circle shadow-sm mr-2 d-flex align-items-center justify-content-center"
                                                    style="width: 32px; height: 32px;" v-tooltip="'Ver Documento'">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                                <button
                                                    class="btn btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                                    style="width: 32px; height: 32px;"
                                                    :class="group.estado === 'Concluida' ? 'btn-outline-primary' : 'btn-danger'"
                                                    @click="openUploadModal(group.main_session)"
                                                    v-tooltip="group.estado === 'Concluida' ? 'Cambiar Archivo' : 'Subir Documento'">
                                                    <i class="fas"
                                                        :class="group.estado === 'Concluida' ? 'fa-sync-alt' : 'fa-upload'"></i>
                                                </button>
                                            </div>

                                            <!-- Bloque Proceso -->
                                            <div v-else class="d-flex align-items-center">
                                                <!-- Botón Auditados (Solo para Ejecución) -->
                                                <button
                                                    v-if="group.main_session.aea_tipo === 'ejecucion' && group.estado !== 'Cancelada'"
                                                    class="btn btn-outline-info btn-sm rounded-circle shadow-sm mr-2 d-flex align-items-center justify-content-center"
                                                    style="width: 32px; height: 32px;"
                                                    @click="selectAuditados(group.main_session)"
                                                    v-tooltip="'Ver Auditados'">
                                                    <i class="fas fa-users"></i>
                                                </button>

                                                <!-- Primary Action (Iniciar/Continuar/Ver) -->
                                                <button v-if="group.estado === 'Concluida'"
                                                    class="btn btn-primary btn-sm rounded-circle shadow-sm mr-2 d-flex align-items-center justify-content-center"
                                                    style="width: 32px; height: 32px;"
                                                    @click="selectItem(group.main_session)"
                                                    v-tooltip="'Ver Resultados'">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button v-else-if="group.estado === 'En Curso'"
                                                    class="btn btn-success btn-sm rounded-circle shadow-sm mr-2 d-flex align-items-center justify-content-center"
                                                    style="width: 32px; height: 32px;"
                                                    @click="selectItem(group.main_session)" v-tooltip="'Continuar'">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                                <button v-else-if="group.estado === 'Cancelada'" disabled
                                                    class="btn btn-secondary btn-sm rounded-circle shadow-sm mr-2 d-flex align-items-center justify-content-center"
                                                    style="width: 32px; height: 32px;" v-tooltip="'Cancelada'">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                                <button v-else
                                                    class="btn btn-warning btn-sm rounded-circle shadow-sm mr-2 d-flex align-items-center justify-content-center text-white"
                                                    style="width: 32px; height: 32px;"
                                                    @click="selectItem(group.main_session)"
                                                    v-tooltip="'Iniciar Ejecución'">
                                                    <i class="fas fa-play"></i>
                                                </button>

                                                <!-- Botón Cancelar (Al final) -->
                                                <button v-if="!['Concluida', 'Cancelada'].includes(group.estado)"
                                                    class="btn btn-outline-danger btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                                    style="width: 32px; height: 32px;"
                                                    @click="confirmCancel(group.main_session)"
                                                    v-tooltip="'Cancelar Actividad'">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>

                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="groupedActivities.length === 0"
                            class="col-12 text-center py-5 border rounded bg-white">
                            <i class="fas fa-calendar-times fa-4x text-light mb-3"></i>
                            <h5 class="text-muted">No hay actividades en la agenda</h5>
                            <p class="text-secondary small">Asegúrese de haber planificado la agenda de la auditoría.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="selectedExecutionId && currentView === 'checklist'" class="h-100">
            <ChecklistAuditoria :agendaId="selectedExecutionId" :auditCode="executionCode"
                :processName="getProcessNameForId(selectedExecutionId)" @back="deselectProcess" />
        </div>
        <div v-else-if="selectedExecutionId && currentView === 'auditados'" class="h-100">
            <AuditadosAuditoria :agendaId="selectedExecutionId" :auditCode="executionCode"
                :processName="getProcessNameForId(selectedExecutionId)" @back="deselectProcess" />
        </div>


        <!-- MODAL DE SUBIDA DE ARCHIVOS (Apertura/Cierre/Gabinete) -->
        <div v-if="showUploadModal" class="upload-modal-backdrop" @click.self="showUploadModal = false">
            <div class="upload-modal-content animated zoomIn">
                <div class="p-4">
                    <div class="text-center mb-4">
                        <div class="icon-circle bg-light-danger mb-3">
                            <i class="fas fa-cloud-upload-alt text-danger fa-2x"></i>
                        </div>
                        <h5 class="font-weight-bold text-dark">Subir Documento de Soporte</h5>
                        <p class="text-muted small">Seleccione el archivo PDF o Imagen para la actividad:<br>
                            <strong class="text-primary">{{ activeSession?.aea_actividad }}</strong>
                        </p>
                    </div>

                    <div class="form-group mb-4">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="auditFile" @change="handleFileChange"
                                accept=".pdf,.doc,.docx,.jpg,.png,.xls,.xlsx">
                            <label class="custom-file-label" for="auditFile">
                                {{ selectedFile ? selectedFile.name : 'Elegir archivo...' }}
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button class="btn btn-light px-4 rounded-pill"
                            @click="showUploadModal = false">Cancelar</button>
                        <button class="btn btn-danger px-4 rounded-pill shadow-sm"
                            :disabled="!selectedFile || isUploading" @click="uploadSelectedFile">
                            <i v-if="isUploading" class="fas fa-spinner fa-spin mr-1"></i>
                            <i v-else class="fas fa-check mr-1"></i>
                            Subir y Finalizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>



<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useAuditoriaEjecucionStore } from '../../../stores/auditoriaEjecucionStore';
import ChecklistAuditoria from './ChecklistAuditoria.vue';
import AuditadosAuditoria from './AuditadosAuditoria.vue';
import { useToast } from 'primevue/usetoast';


const props = defineProps({
    auditId: { type: Number, required: true },
    auditData: { type: Object, default: () => ({}) }
});

const toast = useToast();
const store = useAuditoriaEjecucionStore();

// Agrupación de la Agenda para que no se repitan procesos
const groupedActivities = computed(() => {
    const rawItems = store.getExecutionList;
    const groups = {};

    rawItems.forEach(item => {
        const key = item.proceso_id ? `p-${item.proceso_id}` : `act-${item.aea_actividad}-${item.id}`;

        if (!groups[key]) {
            groups[key] = {
                key,
                proceso_label: item.proceso?.proceso_nombre || item.aea_actividad,
                estado: item.estado || 'Programada',
                sessions: [item],
                all_requirements: new Set(),
                total_items: 0,
                completed_items: 0
            };
        } else {
            groups[key].sessions.push(item);
            if (item.estado === 'En Curso' && groups[key].estado !== 'Concluida') {
                groups[key].estado = 'En Curso';
            }
            if (item.estado === 'Concluida') {
                groups[key].estado = 'Concluida';
            }
        }

        // Acumular conteos para el progreso del grupo
        groups[key].total_items += (item.total_items || 0);
        groups[key].completed_items += (item.completed_items || 0);


        // Agregar Requisitos al Set (para que sean únicos)
        if (!groups[key].all_requirements_objs) groups[key].all_requirements_objs = [];

        if (item.aea_requisito) {
            let reqs = [];
            // Safe handling for various formats
            if (Array.isArray(item.aea_requisito)) {
                reqs = item.aea_requisito;
            } else if (typeof item.aea_requisito === 'string') {
                try {
                    const parsed = JSON.parse(item.aea_requisito);
                    if (Array.isArray(parsed)) reqs = parsed;
                    else reqs = [parsed];
                } catch (e) {
                    // It's a simple string, treat as single item with no norm
                    reqs = [{ numeral: item.aea_requisito, norma: 'General' }];
                }
            } else if (typeof item.aea_requisito === 'object') {
                // Single object fallback
                reqs = [item.aea_requisito];
            }

            reqs.forEach(r => {
                if (r && typeof r === 'object') {
                    // Try to resolve Norm Name
                    let nName = r.norma || r.nombre_norma;
                    if (!nName && r.norma_id && normasMap.value[r.norma_id]) {
                        nName = normasMap.value[r.norma_id];
                    }

                    groups[key].all_requirements_objs.push({
                        norma: nName || 'N/A',
                        numeral: r.numeral || r.label || 'Req'
                    });
                } else if (typeof r === 'string') {
                    groups[key].all_requirements_objs.push({
                        norma: 'General',
                        numeral: r
                    });
                }
            });
        }
    });

    return Object.values(groups).map(g => {
        // Agrupar requisitos por norma
        const reqMap = {};
        if (g.all_requirements_objs && g.all_requirements_objs.length > 0) {
            g.all_requirements_objs.forEach(r => {
                const norma = r.norma || 'General';
                if (!reqMap[norma]) reqMap[norma] = new Set();
                reqMap[norma].add(r.numeral);
            });
        }

        g.requirements_list = Object.entries(reqMap).map(([norma, set]) => ({
            norma,
            numerals: Array.from(set).sort().join(', ')
        }));

        // Mantener compatibilidad simple si es necesario
        g.requirements = g.requirements_list.map(r => `${r.norma}: ${r.numerals}`).join(' | ');

        // BUG FIX: Asegurarnos de usar la sesión que TIENE los requisitos 
        // para que al iniciar la ejecución, el controlador los encuentre en 'aea_requisito'.
        const bestSession = g.sessions.find(s => s.aea_requisito && s.aea_requisito.length > 0) || g.sessions[0];
        g.main_session = bestSession;

        // Restore Progress Calculation for Group
        if (['gabinete', 'apertura', 'cierre'].includes(g.sessions[0].aea_tipo)) {
            g.progress = (g.estado === 'Concluida') ? 100 : 0;
        } else {
            g.progress = g.total_items > 0 ? Math.round((g.completed_items / g.total_items) * 100) : 0;
            if (g.estado === 'Concluida') g.progress = 100;
        }

        // Calcular horas ejecutadas estimadas
        let totalExecutedMinutes = 0;
        g.sessions.forEach(s => {
            if (s.aea_hora_inicio && s.aea_hora_fin && s.estado !== 'Cancelada') {
                const start = new Date(`2000-01-01T${s.aea_hora_inicio}`);
                const end = new Date(`2000-01-01T${s.aea_hora_fin}`);
                let diff = (end - start) / 1000 / 60; // minutes
                if (diff < 0) diff += 24 * 60; // Handle midnight crossing if needed, though rare here

                let sessionProgress = 0;
                if (['apertura', 'cierre', 'gabinete'].includes(s.aea_tipo)) {
                    sessionProgress = (s.estado === 'Concluida') ? 1 : 0;
                } else {
                    // Execution
                    if (s.estado === 'Concluida') sessionProgress = 1;
                    else if (s.completed_items > 0 && s.total_items > 0) {
                        sessionProgress = s.completed_items / s.total_items;
                    }
                }
                totalExecutedMinutes += diff * sessionProgress;
            }
        });
        g.executed_hours = (totalExecutedMinutes / 60).toFixed(2);

        return g;
    });
});

// Progreso Global de la Auditoría (Persistido)
const globalProgress = computed(() => {
    // Si viene en auditData (al cargar inicialmente)
    if (props.auditData && props.auditData.ae_avance !== undefined) {
        return Math.round(props.auditData.ae_avance);
    }
    // Fallback provisional si no se ha recargado
    return 0;
});



const executionCode = computed(() => {
    const firstItem = store.agendaItems[0];
    return firstItem?.auditoria?.ae_codigo || props.auditData?.ae_codigo || '---';
});

const loading = computed(() => store.loading);

// Normas Map for fallback
const normasMap = ref({});
const loadNormas = async () => {
    try {
        const url = window.route ? window.route('api.auditoria.especifica.requisitos', { id: props.auditId }) : `/api/auditoria/especifica/${props.auditId}/requisitos-disponibles`;
        const res = await axios.get(url);
        if (Array.isArray(res.data)) {
            res.data.forEach(n => {
                normasMap.value[n.id] = n.nombre;
            });
        }
    } catch (e) {
        console.error("Error loading norms map", e);
    }
};

const selectedExecutionId = computed({
    get: () => store.selectedExecutionId,
    set: (val) => store.selectedExecutionId = val
});

const loadData = () => {
    if (!props.auditId) return;
    store.loadData(props.auditId);
};

const getStatusClass = (status) => {
    switch (status) {
        case 'En Curso': return 'badge-primary';
        case 'Concluida': return 'badge-success';
        case 'Cerrado': return 'badge-dark';
        case 'Cancelada': return 'badge-secondary';
        default: return 'badge-secondary';
    }
};

const confirmCancel = (session) => {
    // Usar SweetAlert2 si está disponible (this.$swal) o window.Swal
    const swal = window.Swal || (this && this.$swal);

    if (swal) {
        swal.fire({
            title: '¿Cancelar Actividad?',
            text: "Esta acción marcará la actividad como cancelada y no podrá deshacerse.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No, mantener'
        }).then((result) => {
            if (result.isConfirmed) {
                cancelActivity(session);
            }
        });
    } else {
        if (confirm('¿Está seguro de CANCELAR esta actividad? Esto no se puede deshacer.')) {
            cancelActivity(session);
        }
    }
};

const cancelActivity = async (session) => {
    try {
        const url = window.route
            ? window.route('api.auditoria.especifica.agenda.cancelar', { id: session.id })
            : `/api/api/auditoria/especifica/${session.id}/agenda/cancelar`; // Adjusted path based on api.php group

        // Actually, route definition was: prefix('auditoria/especifica') -> '/{id}/agenda/cancelar'
        // So path is /api/auditoria/especifica/{id}/agenda/cancelar
        await axios.put(`/api/auditoria/especifica/${session.id}/agenda/cancelar`);

        toast.add({ severity: 'info', summary: 'Cancelada', detail: 'Actividad cancelada correctamente', life: 3000 });
        store.loadData(props.auditId);
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cancelar la actividad', life: 3000 });
    }
};

const currentView = ref('checklist'); // 'checklist' o 'auditados'

const selectItem = async (item) => {
    currentView.value = 'checklist';
    if (item.estado === 'Programada' || !item.estado) {
        try {
            await store.initExecution(item);
            toast.add({ severity: 'info', summary: 'Iniciado', detail: 'Se ha generado la lista de verificación consolidada', life: 3000 });
        } catch (e) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Error iniciando ejecución', life: 3000 });
        }
    } else {
        store.selectedExecutionId = item.id;
    }
};

const selectAuditados = (item) => {
    currentView.value = 'auditados';
    store.selectedExecutionId = item.id;
};

const getProcessNameForId = (agendaId) => {
    const item = store.agendaItems.find(i => i.id === agendaId);
    return item?.proceso?.pro_nombre || item?.aea_actividad || 'Actividad';
};


// LÓGICA DE SUBIDA DE ARCHIVOS
const showUploadModal = ref(false);
const activeSession = ref(null);
const selectedFile = ref(null);
const isUploading = ref(false);

const openUploadModal = (session) => {
    activeSession.value = session;
    selectedFile.value = null;
    showUploadModal.value = true;
};

const handleFileChange = (e) => {
    selectedFile.value = e.target.files[0];
};

const uploadSelectedFile = async () => {
    if (!selectedFile.value || !activeSession.value) return;

    isUploading.value = true;
    const formData = new FormData();
    formData.append('file', selectedFile.value);

    try {
        const url = window.route ? window.route('api.auditoria.ejecucion.upload-file', { id: activeSession.value.id }) : `/api/auditoria/ejecucion/upload-file/${activeSession.value.id}`;
        await axios.post(url, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Documento registrado. Actividad concluida.', life: 3000 });
        showUploadModal.value = false;
        store.loadData(props.auditId);
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo subir el archivo', life: 3000 });
    } finally {
        isUploading.value = false;
    }
};


const deselectProcess = async () => {
    const shouldReload = currentView.value === 'checklist';
    const itemId = store.selectedExecutionId;

    store.selectedExecutionId = null;
    currentView.value = 'checklist'; // Reset to default view

    if (shouldReload && itemId) {
        // Solo actualizar el item específico que fue editado
        await store.refreshAgendaItem(itemId);
    }
};


const formatDate = (dateStr) => {
    if (!dateStr) return '00 ---';
    const d = new Date(dateStr + 'T00:00:00');
    const day = d.toLocaleDateString('es-ES', { day: '2-digit' });
    const month = d.toLocaleDateString('es-ES', { month: 'short' }).replace('.', '');
    return `${day} ${month}`;
};

const formatDateShort = (dateStr) => {
    if (!dateStr) return '';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' }).replace('.', '');
};

onMounted(() => {
    loadData();
    loadNormas();
});

watch(() => props.auditId, (newId) => {
    if (newId) loadData();
});
</script>

<style scoped>
.header-container {
    padding: 0.75rem 1rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #ff851b;
    display: flex;
    align-items: center;
}

.italic {
    font-style: italic;
}

.execution-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border-radius: 10px;
    background: #fff;
    overflow: hidden;
}

.execution-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
}

.border-left-primary {
    border-left: 5px solid #007bff !important;
}

.border-left-success {
    border-left: 5px solid #28a745 !important;
}

.border-left-secondary {
    border-left: 5px solid #6c757d !important;
}

.date-box {
    line-height: 1.2;
}

.info-item {
    background: #f8f9fa;
    padding: 2px 10px;
    border-radius: 15px;
    border: 1px solid #e9ecef;
}

.badge {
    text-transform: uppercase;
    font-size: 0.7rem;
    padding: 0.4em 0.8em;
}

.x-small {
    font-size: 0.65rem;
}

.shadow-inner {
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
}

/* ESTILOS MODAL SUBIDA */
.upload-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.upload-modal-content {
    background: white;
    width: 90%;
    max-width: 450px;
    border-radius: 20px;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
    border: 1px solid #dee2e6;
}

.bg-light-danger {
    background-color: #fce8e8;
}

.icon-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.custom-file-label::after {
    content: "Buscar";
    background-color: #dc3545;
    color: white;
}

.animated {
    animation-duration: 0.3s;
    animation-fill-mode: both;
}

@keyframes zoomIn {
    from {
        opacity: 0;
        transform: scale3d(0.3, 0.3, 0.3);
    }

    50% {
        opacity: 1;
    }
}

.bg-gradient-primary {
    background: linear-gradient(90deg, #007bff, #6610f2);
}

.shadow-inset {
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
}


/* Professional Buttons */
.btn-professional {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: none;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    position: relative;
    overflow: hidden;
}

.btn-professional:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2) !important;
}

.btn-professional:active {
    transform: translateY(0) scale(0.95);
}

/* Finished: Blue Gradient */
.btn-finished {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
}

/* Processing: Green Gradient */
.btn-processing {
    background: linear-gradient(135deg, #28a745, #1e7e34);
    color: white;
}

/* Pending: Yellow Gradient */
.btn-pending {
    background: linear-gradient(135deg, #ffc107, #d39e00);
    color: white;
    /* Ensure visibility */
}

/* Cancel: Red (X) */
.btn-cancel {
    background: white;
    border: 2px solid #dc3545;
    color: #dc3545;
}

.btn-cancel:hover {
    background: #dc3545;
    color: white;
}



.zoomIn {
    animation-name: zoomIn;
}
</style>
