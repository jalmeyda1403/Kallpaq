<template>
    <div>
        <div class="header-container mb-4">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">Planificación Detallada (Cronograma)</span>
            </h6>
        </div>

        <div v-if="loading" class="text-center my-5">
            <div class="spinner-border text-danger" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
        </div>

        <div v-else>
            <!-- Tiempos de Reunión -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body bg-light rounded shadow-sm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group small mb-0">
                                <label class="form-label text-danger font-weight-bold p-0 m-0">Reunión de
                                    Apertura</label>
                                <input type="datetime-local" v-model="auditData.ae_reunion_apertura"
                                    class="form-control" @change="updateAuditTimes" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group small mb-0">
                                <label class="form-label text-danger font-weight-bold p-0 m-0">Reunión de Cierre</label>
                                <input type="datetime-local" v-model="auditData.ae_reunion_cierre" class="form-control"
                                    @change="updateAuditTimes" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Cronograma -->
            <div class="card border-0 shadow-none">
                <div class="card-body p-0">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex">
                            <button class="btn btn-outline-danger btn-sm mr-2 shadow-sm" @click="addRow">
                                <i class="fas fa-plus mr-1"></i> Agregar Fila
                            </button>
                            <button class="btn btn-outline-info btn-sm shadow-sm" @click="syncWithProcesos">
                                <i class="fas fa-sync-alt mr-1"></i> Sincronizar Procesos
                            </button>
                        </div>
                        <div class="bg-white p-2 border rounded shadow-sm">
                            <span class="font-weight-bold text-secondary mr-2">HH Totales:</span>
                            <span class="badge badge-danger" style="font-size: 1rem;">{{ totalHH }} h</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover bg-white text-center table-planning">
                            <thead class="bg-secondary text-white table-header-premium">
                                <tr>
                                    <th class="align-middle" style="width: 40px;">#</th>
                                    <th class="align-middle" style="min-width: 150px;">Proceso / Actividad</th>
                                    <th class="align-middle" style="min-width: 130px;">Auditor</th>
                                    <th class="align-middle" style="width: 100px;">H. Inicio</th>
                                    <th class="align-middle" style="width: 100px;">H. Fin</th>
                                    <th class="align-middle">Requisitos</th>
                                    <th v-for="day in days" :key="day.date" class="align-middle" style="width: 60px;">
                                        {{ formatDateHeader(day.date) }}
                                    </th>
                                    <th class="align-middle" style="width: 40px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, idx) in agenda" :key="idx">
                                    <td class="align-middle font-weight-bold small text-muted">{{ idx + 1 }}</td>
                                    <td>
                                        <select v-model="row.aea_actividad"
                                            class="form-control form-control-xs border-0"
                                            @change="handleActivityChange(row)">
                                            <option value="">Seleccione...</option>
                                            <option v-for="p in procesosAuditados" :key="p.id"
                                                :value="p.pro_nombre || p.proceso_nombre">
                                                {{ p.pro_codigo || p.cod_proceso }} - {{ p.pro_nombre ||
                                                    p.proceso_nombre }}
                                            </option>
                                            <option value="Reunión de Apertura">Reunión de Apertura</option>
                                            <option value="Reunión de Cierre">Reunión de Cierre</option>
                                            <option value="Trabajo de Gabinete">Trabajo de Gabinete</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select v-model="row.aea_auditor" class="form-control form-control-xs border-0">
                                            <option value="">Por definir</option>
                                            <option v-for="m in equipo" :key="m.id" :value="m.usuario?.name || 'User'">
                                                {{ m.usuario?.name }}
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="time" v-model="row.aea_hora_inicio"
                                            class="form-control form-control-xs border-0 text-center">
                                    </td>
                                    <td>
                                        <input type="time" v-model="row.aea_hora_fin"
                                            class="form-control form-control-xs border-0 text-center">
                                    </td>
                                    <td>
                                        <input type="text" v-model="row.aea_requisito"
                                            class="form-control form-control-xs border-0 text-center"
                                            placeholder="Norma...">
                                    </td>
                                    <td v-for="day in days" :key="'ch-' + idx + '-' + day.date"
                                        class="align-middle px-0" style="min-width: 45px;">
                                        <div class="checkbox-wrapper">
                                            <input type="checkbox" v-model="row.schedule[day.date]" class="check-input">
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <button class="btn btn-link btn-xs text-danger p-0" @click="removeRow(idx)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Auditor Load Balance -->
                    <div class="row mt-5" v-if="auditorBalance.length > 0">
                        <div class="col-md-6">
                            <div class="p-3 bg-white border rounded shadow-sm h-100">
                                <h6 class="font-weight-bold text-dark mb-4 d-flex align-items-center">
                                    <i class="fas fa-chart-bar text-danger mr-2"></i> Balance de Carga (HH por Auditor)
                                </h6>
                                <div v-for="aud in auditorBalance" :key="aud.name" class="mb-4">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="font-weight-bold text-dark">{{ aud.name }}</span>
                                        <span class="badge badge-danger px-2">{{ aud.hours }} h</span>
                                    </div>
                                    <div class="progress" style="height: 12px; border-radius: 6px;">
                                        <div class="progress-bar bg-gradient-danger shadow-sm" role="progressbar"
                                            :style="{ width: aud.percentage + '%' }" :aria-valuenow="aud.hours"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-white border rounded shadow-sm h-100">
                                <h6 class="font-weight-bold text-dark mb-4 d-flex align-items-center">
                                    <i class="fas fa-list-ol text-danger mr-2"></i> Agenda Diaria Programada
                                </h6>
                                <div style="max-height: 300px; overflow-y: auto;">
                                    <div v-for="day in days" :key="'sum-' + day.date" class="mb-4">
                                        <div class="bg-light p-2 font-weight-bold small border-bottom mb-2">
                                            {{ formatDateHeader(day.date) }}
                                        </div>
                                        <div v-for="item in getAgendaForDay(day.date)" :key="item.id"
                                            class="d-flex align-items-center mb-2 pl-2">
                                            <div class="badge badge-outline-secondary mr-2 py-1 px-2"
                                                style="font-size: 0.7rem; border: 1px solid #ced4da;">
                                                {{ item.time }}
                                            </div>
                                            <div class="small">
                                                <span class="font-weight-bold">{{ item.actividad }}</span>
                                                <span class="text-muted ml-2">({{ item.auditor }})</span>
                                            </div>
                                        </div>
                                        <div v-if="getAgendaForDay(day.date).length === 0"
                                            class="small text-muted italic pl-2">
                                            Sin actividades programadas
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-center bg-light border-top mt-5 p-3"
                        style="margin: 0 -1.5rem -1rem -1.5rem; border-bottom-right-radius: 0.3rem;">
                        <button class="btn btn-danger btn-sm px-5 shadow-sm" @click="save" :disabled="saving">
                            <i v-if="saving" class="fas fa-spinner fa-spin mr-1"></i>
                            <i v-else class="fas fa-save mr-1"></i>
                            Guardar Planificación
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm ml-2 px-4 shadow-sm"
                            @click="$emit('close')">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, defineProps, defineEmits, computed, watch } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

const props = defineProps(['auditId']);
const emit = defineEmits(['saved', 'close']);
const toast = useToast();

const loading = ref(false);
const saving = ref(false);
const auditData = ref({});
const equipo = ref([]);
const procesosAuditados = ref([]);
const agenda = ref([]);
const days = ref([]);

const totalHH = computed(() => {
    let hours = 0;
    agenda.value.forEach(row => {
        const diff = getTimeDiff(row.aea_hora_inicio, row.aea_hora_fin);
        const dayCount = Object.values(row.schedule).filter(v => v === true).length;
        hours += (diff * dayCount);
    });
    return parseFloat(hours.toFixed(2));
});

const auditorBalance = computed(() => {
    const balance = {};
    agenda.value.forEach(row => {
        if (!row.aea_auditor) return;
        const diff = getTimeDiff(row.aea_hora_inicio, row.aea_hora_fin);
        const dayCount = Object.values(row.schedule).filter(v => v === true).length;
        const hh = diff * dayCount;
        balance[row.aea_auditor] = (balance[row.aea_auditor] || 0) + hh;
    });

    const list = Object.entries(balance).map(([name, hours]) => ({ name, hours }));
    const max = Math.max(...list.map(a => a.hours), 1);
    return list.map(a => ({
        ...a,
        percentage: Math.min(100, (a.hours / max) * 100)
    })).sort((a, b) => b.hours - a.hours);
});

const getAgendaForDay = (date) => {
    const list = [];
    agenda.value.forEach(row => {
        if (row.schedule[date]) {
            list.push({
                time: `${row.aea_hora_inicio} - ${row.aea_hora_fin}`,
                actividad: row.aea_actividad,
                auditor: row.aea_auditor,
                start: row.aea_hora_inicio
            });
        }
    });
    return list.sort((a, b) => a.start.localeCompare(b.start));
};

const getTimeDiff = (start, end) => {
    if (!start || !end) return 0;
    const [h1, m1] = start.split(':').map(Number);
    const [h2, m2] = end.split(':').map(Number);
    const diff = (h2 * 60 + m2) - (h1 * 60 + m1);
    return Math.max(0, diff / 60);
};

const formatDateHeader = (dateStr) => {
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric' });
};

const calculateDays = (start, end) => {
    const list = [];
    let curr = new Date(start + 'T00:00:00');
    const last = new Date(end + 'T00:00:00');

    if (isNaN(curr.getTime()) || isNaN(last.getTime())) return [];

    let count = 0;
    while (curr <= last && count < 30) { // Safety limit 30 days
        const dayOfWeek = curr.getDay();
        // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
        if (dayOfWeek !== 0 && dayOfWeek !== 6) {
            list.push({ date: curr.toISOString().split('T')[0] });
        }
        curr.setDate(curr.getDate() + 1);
        count++;
    }
    return list;
};

const initSchedule = () => {
    const sched = {};
    days.value.forEach(d => {
        sched[d.date] = false;
    });
    return sched;
};

const addRow = () => {
    agenda.value.push({
        proceso_id: null,
        aea_actividad: '',
        aea_auditor: '',
        aea_requisito: '',
        aea_tipo: 'ejecucion',
        aea_hora_inicio: '08:30',
        aea_hora_fin: '17:30',
        schedule: initSchedule()
    });
};

const syncWithProcesos = () => {
    // 1. Add rows for processes that are not in agenda
    procesosAuditados.value.forEach(p => {
        const name = p.pro_nombre || p.proceso_nombre;
        const exists = agenda.value.some(row => row.aea_actividad === name);
        if (!exists) {
            agenda.value.push({
                proceso_id: p.id,
                aea_actividad: name,
                aea_auditor: '',
                aea_requisito: '',
                aea_tipo: 'ejecucion',
                aea_hora_inicio: '08:30',
                aea_hora_fin: '17:30',
                schedule: initSchedule()
            });
        }
    });

    // 2. Remove rows whose activity is a process that is no longer associated
    const validNames = procesosAuditados.value.map(p => p.pro_nombre || p.proceso_nombre);
    // Add default activities we want to keep
    validNames.push('Reunión de Apertura', 'Reunión de Cierre', 'Trabajo de Gabinete');

    agenda.value = agenda.value.filter(row => {
        if (!row.aea_actividad) return true; // keep empty rows
        return validNames.includes(row.aea_actividad);
    });

    toast.add({ severity: 'info', summary: 'Sincronizado', detail: 'Agenda sincronizada con procesos auditados', life: 3000 });
};

const removeRow = (idx) => {
    agenda.value.splice(idx, 1);
};

const handleActivityChange = (row) => {
    const proc = procesosAuditados.value.find(p => (p.pro_nombre || p.proceso_nombre) === row.aea_actividad);
    row.proceso_id = proc ? proc.id : null;

    if (row.aea_actividad === 'Reunión de Apertura') row.aea_tipo = 'apertura';
    else if (row.aea_actividad === 'Reunión de Cierre') row.aea_tipo = 'cierre';
    else if (row.aea_actividad === 'Trabajo de Gabinete') row.aea_tipo = 'gabinete';
    else row.aea_tipo = 'ejecucion';
};

const loadData = async () => {
    if (!props.auditId) return;
    loading.value = true;
    try {
        const response = await axios.get(`/api/auditorias/${props.auditId}`);
        const data = response.data;
        auditData.value = {
            ae_reunion_apertura: data.ae_reunion_apertura ? data.ae_reunion_apertura.slice(0, 16) : '',
            ae_reunion_cierre: data.ae_reunion_cierre ? data.ae_reunion_cierre.slice(0, 16) : '',
            ae_fecha_inicio: data.ae_fecha_inicio?.substring(0, 10),
            ae_fecha_fin: data.ae_fecha_fin?.substring(0, 10)
        };

        equipo.value = data.equipo || [];
        procesosAuditados.value = data.procesos || [];

        // Generate day columns based on audit dates
        days.value = calculateDays(auditData.value.ae_fecha_inicio, auditData.value.ae_fecha_fin);

        // Map existing agenda or create default
        if (data.agenda && data.agenda.length > 0) {
            // Complex mapping: The DB has one row per specific time slot, 
            // but the UI has one row per activity/team with checkable days.
            // For now, let's just start with empty rows or a simple group.
            // If the user wants to group by activity + auditor + ouo:
            const grouped = {};
            data.agenda.forEach(a => {
                const key = `${a.aea_actividad}-${a.aea_auditor}-${a.aea_hora_inicio}-${a.aea_hora_fin}`;
                if (!grouped[key]) {
                    grouped[key] = {
                        proceso_id: a.proceso_id || null,
                        aea_actividad: a.aea_actividad,
                        aea_auditor: a.aea_auditor,
                        aea_requisito: a.aea_requisito,
                        aea_tipo: a.aea_tipo || 'ejecucion',
                        aea_hora_inicio: a.aea_hora_inicio.substring(0, 5),
                        aea_hora_fin: a.aea_hora_fin.substring(0, 5),
                        schedule: initSchedule()
                    };
                }
                const date = a.aea_fecha;
                if (grouped[key].schedule[date] !== undefined) {
                    grouped[key].schedule[date] = true;
                }
            });
            agenda.value = Object.values(grouped);
        } else {
            addRow();
        }
    } catch (e) {
        console.error("Error loading planning data", e);
    } finally {
        loading.value = false;
    }
};

const updateAuditTimes = async () => {
    try {
        await axios.put(`/api/auditorias/${props.auditId}`, {
            ae_reunion_apertura: auditData.value.ae_reunion_apertura,
            ae_reunion_cierre: auditData.value.ae_reunion_cierre
        });
    } catch (e) {
        console.error("Error updating audit times", e);
    }
};

const save = async () => {
    saving.value = true;
    try {
        // Flatten agenda back to DB format (one row per checked slot)
        const flatAgenda = [];
        agenda.value.forEach(row => {
            Object.entries(row.schedule).forEach(([date, checked]) => {
                if (checked) {
                    flatAgenda.push({
                        ae_id: props.auditId,
                        proceso_id: row.proceso_id || null,
                        aea_fecha: date,
                        aea_hora_inicio: row.aea_hora_inicio,
                        aea_hora_fin: row.aea_hora_fin,
                        aea_actividad: row.aea_actividad,
                        aea_auditado: null,
                        aea_auditor: row.aea_auditor,
                        aea_requisito: row.aea_requisito,
                        aea_tipo: row.aea_tipo
                    });
                }
            });
        });

        await axios.put(`/api/auditorias/${props.auditId}/agenda`, { agenda: flatAgenda });

        // Also update total HH in main audit record
        await axios.put(`/api/auditorias/${props.auditId}`, { ae_horas_hombre: totalHH.value });

        toast.add({ severity: 'success', summary: 'Guardado', detail: 'Planificación actualizada' });
        emit('saved');
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Fallo guardando planificación' });
    } finally {
        saving.value = false;
    }
};

onMounted(loadData);
watch(() => props.auditId, loadData);

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

.form-control-xs {
    height: calc(1.5em + 0.5rem + 2px);
    padding: 0.25rem 0.5rem;
    font-size: 0.85rem;
}

.table-planning {
    font-size: 0.9rem;
}

.table-header-premium {
    background: linear-gradient(180deg, #4b545c 0%, #343a40 100%) !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.75rem;
}

.table th,
.table td {
    padding: 0.4rem 0.2rem !important;
    vertical-align: middle;
}

.checkbox-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
    width: 100%;
}

.check-input {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: #dc3545;
}

.table thead th {
    white-space: nowrap;
    border-bottom: 2px solid #dc3545 !important;
}

.bg-gradient-danger {
    background: linear-gradient(90deg, #ff416c 0%, #ff4b2b 100%) !important;
}

.progress {
    background-color: #e9ecef;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
}

.italic {
    font-style: italic;
}

.btn-xs {
    padding: 0.1rem 0.3rem;
    font-size: 0.75rem;
}
</style>
