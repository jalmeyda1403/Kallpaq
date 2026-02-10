<template>
    <div>
        <div class="header-container mb-4">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">Cronograma de Actividades</span>
            </h6>
        </div>

        <div v-if="loading" class="text-center my-5">
            <div class="spinner-border text-danger" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
        </div>

        <div v-else>


            <!-- Navegación de Pestañas Internas -->
            <ul class="nav nav-tabs mb-4 px-2 border-bottom-0 shadow-sm bg-white rounded-top">
                <li class="nav-item">
                    <a class="nav-link text-dark"
                        :class="{ 'active font-weight-bold border-danger border-bottom-0': localActiveTab === 'cronograma' }"
                        @click="localActiveTab = 'cronograma'" href="javascript:void(0)">
                        <i class="fas fa-calendar-alt mr-2 text-danger"></i> Cronograma
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark"
                        :class="{ 'active font-weight-bold border-danger border-bottom-0': localActiveTab === 'balance' }"
                        @click="localActiveTab = 'balance'" href="javascript:void(0)">
                        <i class="fas fa-chart-pie mr-2 text-danger"></i> Balance de Carga
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark"
                        :class="{ 'active font-weight-bold border-danger border-bottom-0': localActiveTab === 'agenda' }"
                        @click="localActiveTab = 'agenda'" href="javascript:void(0)">
                        <i class="fas fa-list-ul mr-2 text-danger"></i> Vista Agenda
                    </a>
                </li>
            </ul>

            <!-- Sección de Cronograma -->
            <div v-show="localActiveTab === 'cronograma'" class="card border-0 shadow-none">
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
                                    <th class="align-middle" style="min-width: 130px;">Observador</th>
                                    <th class="align-middle" style="width: 100px;">H. Inicio</th>
                                    <th class="align-middle" style="width: 100px;">H. Fin</th>
                                    <th class="align-middle">Requisitos</th>
                                    <th v-for="day in days" :key="day.date" class="align-middle" style="width: 60px;">
                                        {{ formatDateShort(day.date) }}
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
                                        <div v-if="['apertura', 'cierre', 'gabinete'].includes(row.aea_tipo)">
                                            <span class="badge badge-light border text-muted py-1 px-2 d-block">
                                                <i class="fas fa-users mr-1"></i> Todo el equipo
                                            </span>
                                        </div>
                                        <select v-else v-model="row.auditor_id"
                                            class="form-control form-control-xs border-0"
                                            @change="updateAuditorName(row)">
                                            <option :value="null">Por definir</option>
                                            <option v-for="m in auditoresTeam" :key="m.auditor_id"
                                                :value="m.auditor_id">
                                                {{ m.auditor?.user?.name || m.usuario?.name || 'Desconocido' }}
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <div v-if="['apertura', 'cierre', 'gabinete'].includes(row.aea_tipo)">
                                            <span class="text-muted small">-</span>
                                        </div>
                                        <select v-else v-model="row.observador_id"
                                            class="form-control form-control-xs border-0">
                                            <option :value="null">Ninguno</option>
                                            <option v-for="m in observadoresTeam" :key="'obs-' + m.auditor_id"
                                                :value="m.auditor_id">
                                                {{ m.auditor?.user?.name || m.usuario?.name || 'Desconocido' }}
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
                                    <td class="text-center" @dragover.prevent="dragOverIndex = idx"
                                        @drop="handleDrop($event, row)"
                                        :style="dragOverIndex === idx ? 'background-color: #e3f2fd; border: 2px dashed #17a2b8;' : ''"
                                        @dragleave="dragOverIndex = null">
                                        <button v-if="!row.aea_requisito"
                                            class="btn btn-outline-secondary btn-xs rounded-pill"
                                            @click="openRequisitosModal(row)" title="Seleccionar Requisitos">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <div v-else class="d-flex align-items-center justify-content-center"
                                            draggable="true" @dragstart="handleDragStart($event, row)"
                                            style="cursor: grab;" title="Arrastra para copiar a otra fila">
                                            <span class="badge badge-info text-truncate cursor-pointer"
                                                style="max-width: 100px;" @click="openRequisitosModal(row)"
                                                :title="getFormattedRequisitosTooltip(row.aea_requisito)">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                {{ getFormattedRequisitosLabel(row.aea_requisito) }}
                                            </span>
                                            <i class="fas fa-times-circle text-danger ml-1 cursor-pointer small"
                                                @click.stop="clearRequisitos(row)" title="Limpiar"></i>
                                        </div>
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
                </div>
            </div>

            <!-- Sección de Balance de Carga -->
            <div v-show="localActiveTab === 'balance'" class="row">
                <div class="col-md-5">
                    <div class="p-4 bg-white border rounded shadow-sm h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="font-weight-bold text-dark mb-0 d-flex align-items-center">
                                <i class="fas fa-chart-bar text-danger mr-3 fa-lg"></i> Carga por Auditor
                            </h6>
                            <button v-if="selectedAuditors.size > 0"
                                class="btn btn-xs btn-outline-secondary rounded-pill" @click="clearAuditorSelection"
                                title="Mostrar Todos">
                                <i class="fas fa-times mr-1"></i> Ver Todos
                            </button>
                        </div>

                        <div class="row">
                            <div v-for="aud in auditorBalance" :key="aud.id"
                                class="col-md-12 mb-3 cursor-pointer user-select-none"
                                @click="toggleAuditorSelection(aud.id)">
                                <div class="p-2 rounded transition-all"
                                    :class="selectedAuditors.has(aud.id) ? 'bg-light-primary border-primary border' : 'hover-bg-light'">

                                    <div class="d-flex justify-content-between mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle mr-2"
                                                :class="selectedAuditors.has(aud.id) ? 'text-primary' : 'text-muted opacity-25'"></i>
                                            <div>
                                                <span class="font-weight-bold text-dark small text-uppercase d-block">{{
                                                    aud.name }}</span>
                                                <span v-if="aud.isObserver" class="badge badge-secondary"
                                                    style="font-size: 0.6rem;">Observador</span>
                                            </div>
                                        </div>
                                        <span class="badge px-3 py-1 align-self-start"
                                            :class="aud.isObserver ? 'badge-secondary' : 'badge-danger'">
                                            {{ aud.hours }} h
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 8px; border-radius: 6px;">
                                        <div class="progress-bar shadow-sm" role="progressbar"
                                            :class="aud.isObserver ? 'bg-secondary' : (selectedAuditors.has(aud.id) ? 'bg-primary' : 'bg-gradient-danger')"
                                            :style="{ width: aud.percentage + '%' }" :aria-valuenow="aud.hours"></div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="auditorBalance.length === 0" class="col-12 py-5 text-center text-muted italic">
                                No hay actividades asignadas para calcular el balance.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="p-4 bg-white border rounded shadow-sm h-100">
                        <h6 class="font-weight-bold text-dark mb-4 d-flex align-items-center justify-content-between">
                            <span>
                                <i class="fas fa-chart-line text-info mr-3 fa-lg"></i> HH Totales por Día
                            </span>
                            <span v-if="selectedAuditors.size > 0" class="badge badge-primary font-weight-normal">
                                Filtrado por {{ selectedAuditors.size }} {{ selectedAuditors.size === 1 ? 'persona' :
                                    'personas' }}
                            </span>
                        </h6>
                        <div style="height: 350px; position: relative;">
                            <canvas ref="chartCanvas"></canvas>
                        </div>
                        <div v-if="days.length === 0" class="py-5 text-center text-muted italic">
                            No hay fechas definidas para mostrar el gráfico.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Vista Agenda -->
            <div v-show="localActiveTab === 'agenda'" class="row">
                <div class="col-md-7">
                    <div class="p-4 bg-white border rounded shadow-sm">
                        <h6 class="font-weight-bold text-dark mb-4 d-flex align-items-center">
                            <i class="fas fa-list-ol text-danger mr-3 fa-lg"></i> Agenda Consolidada por Día
                        </h6>
                        <div class="row">
                            <div v-for="day in days" :key="'sum-' + day.date" class="col-md-6 mb-4">
                                <div class="card border shadow-none h-100">
                                    <div
                                        class="card-header bg-light py-2 px-3 font-weight-bold small d-flex justify-content-between align-items-center w-100">
                                        <span class="text-uppercase text-secondary mr-auto"
                                            style="letter-spacing: 0.5px;">{{ formatDateHeader(day.date) }}</span>
                                        <span class="badge badge-danger badge-pill px-3 shadow-sm ml-2">{{
                                            getAgendaForDay(day.date).length }} Actividades</span>
                                    </div>
                                    <div class="card-body p-2" style="max-height: 250px; overflow-y: auto;">
                                        <div v-for="item in getAgendaForDay(day.date)" :key="item.id"
                                            class="d-flex align-items-center mb-2 pl-2 border-bottom pb-2 last-no-border">
                                            <div class="badge badge-outline-secondary mr-2 py-1 px-2"
                                                style="font-size: 0.7rem; border: 1px solid #ced4da; min-width: 85px;">
                                                <i class="far fa-clock mr-1"></i> {{ item.time }}
                                            </div>
                                            <div class="small w-100">
                                                <div class="font-weight-bold text-truncate" style="max-width: 180px;">{{
                                                    item.actividad }}</div>
                                                <div class="text-muted small"><i class="fas fa-user-edit mr-1"></i> {{
                                                    item.auditor }}</div>
                                            </div>
                                        </div>
                                        <div v-if="getAgendaForDay(day.date).length === 0"
                                            class="small text-muted italic p-3 text-center">
                                            Sin actividades programadas
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer justify-content-center bg-light border-top mt-5 p-3"
                style="margin: 0 -1.5rem -1rem -1.5rem; border-bottom-right-radius: 0.3rem;">
                <button type="button" class="btn btn-outline-danger btn-sm ml-2 px-4 shadow-sm" @click="openPrintModal">
                    <i class="fas fa-print mr-1"></i> Vista de Impresión
                </button>
                <button class="btn btn-danger btn-sm px-5 shadow-sm" @click="save" :disabled="saving">
                    <i v-if="saving" class="fas fa-spinner fa-spin mr-1"></i>
                    <i v-else class="fas fa-save mr-1"></i>
                    Guardar Planificación
                </button>
                <button type="button" class="btn btn-secondary btn-sm ml-2 px-4 shadow-sm" @click="$emit('close')">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </button>
            </div>
        </div>
    </div>

    <RequisitosSelectionModal ref="requisitosModal" :audit-id="auditId" @selected="handleRequisitosSelected" />

    <!-- Hidden PDF Template -->
    <div style="position: fixed; left: -9999px; top: -9999px;">
        <div ref="pdfTemplate" id="pdf-content"
            style="width: 210mm; padding: 15mm; background: white; color: black; font-family: Arial, sans-serif; font-size: 10pt;">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-danger"
                style="border-bottom-width: 3px !important;">
                <div style="width: 200px;">
                    <img :src="'/images/logo.png'" alt="Logo" style="max-height: 50px; max-width: 100%;">
                </div>
                <div class="text-center flex-grow-1">
                    <h6 class="font-weight-bold text-danger m-0">Plan de Auditoría {{ auditData.ae_codigo }}</h6>
                </div>
                <div class="text-right" style="font-size: 8pt; width: 200px;">
                    <div>F. Emisión: {{ formatDateFull(new Date()) }}</div>
                    <div>Versión: 01</div>
                </div>
            </div>

            <!-- 1. OBJETIVO Y ALCANCE -->
            <div class="mb-3">
                <div class="bg-light p-1 border border-bottom-0 font-weight-bold">1. OBJETIVO Y ALCANCE</div>
                <table class="table table-bordered table-sm w-100 mb-0">
                    <tr>
                        <th class="w-25">Objetivo</th>
                        <td>{{ auditData.ae_objetivo }}</td>
                    </tr>
                    <tr>
                        <th>Alcance</th>
                        <td>{{ auditData.ae_alcance }}</td>
                    </tr>
                    <tr>
                        <th>Criterios (Normas)</th>
                        <td>{{ Array.isArray(auditData.ae_sistema) ? auditData.ae_sistema.join(', ') :
                            auditData.ae_sistema }}</td>
                    </tr>
                    <tr>
                        <th>Fecha de Auditoría</th>
                        <td>{{ formatDateFull(auditData.ae_fecha_inicio) }} - {{ formatDateFull(auditData.ae_fecha_fin)
                            }}</td>
                    </tr>
                </table>
            </div>

            <!-- 2. EQUIPO AUDITOR -->
            <div class="mb-3">
                <div class="bg-light p-1 border border-bottom-0 font-weight-bold">2. EQUIPO AUDITOR</div>
                <table class="table table-bordered table-sm w-100 mb-0">
                    <thead>
                        <tr>
                            <th class="w-25">Rol</th>
                            <th class="w-35">Nombre</th>
                            <th class="w-10">Siglas</th>
                            <th class="w-30">Correo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="member in equipo" :key="member.id">
                            <td>{{ member.aeq_rol }}</td>
                            <td>{{ member.auditor?.user?.name || member.usuario?.name || 'Desconocido' }}</td>
                            <td>{{ getInitials(member.auditor?.user?.name || member.usuario?.name || '') }}</td>
                            <td>{{ member.auditor?.user?.email || member.usuario?.email || '' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- 3. AGENDA (CRONOGRAMA) -->
            <div class="mb-5">
                <div class="bg-light p-1 border border-bottom-0 font-weight-bold">3. AGENDA (CRONOGRAMA)</div>
                <table class="table table-bordered table-sm w-100 agenda-table">
                    <thead class="bg-danger text-white">
                        <tr>
                            <th class="text-center" style="width: 12%">Fecha</th>
                            <th class="text-center" style="width: 12%">Horario</th>
                            <th>Actividad / Proceso</th>
                            <th class="text-center" style="width: 15%">Auditor</th>
                            <th style="width: 20%">Requisitos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in sortedPdfItems" :key="item.key">
                            <td class="text-center">{{ formatDateFull(item.date) }}</td>
                            <td class="text-center">{{ item.timeStart.substring(0, 5) }} - {{ item.timeEnd.substring(0,
                                5) }}</td>
                            <td>{{ item.activity }}</td>
                            <td class="text-center">{{ item.auditor }}</td>
                            <td style="font-size: 8pt !important;">{{ item.requisitos }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Signatures -->
            <div style="display: flex; justify-content: space-between; margin-top: 100px; page-break-inside: avoid;">
                <div class="text-center" style="width: 40%; border-top: 1px solid black; padding-top: 5px;">
                    Firma del Auditor Líder
                </div>
                <div class="text-center" style="width: 40%; border-top: 1px solid black; padding-top: 5px;">
                    Firma del Coordinador / Responsable SIG
                </div>
            </div>
        </div>
    </div>
    <!-- Print Preview Modal -->
    <PlanAuditoriaPrintModal ref="printModalRef" />
</template>

<script setup>
import { ref, onMounted, defineProps, defineEmits, computed, watch, nextTick } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import Chart from 'chart.js/auto';
import RequisitosSelectionModal from './RequisitosSelectionModal.vue';
import PlanAuditoriaPrintModal from './PlanAuditoriaPrintModal.vue';
import jsPDF from 'jspdf';
import html2pdf from 'html2pdf.js';

const props = defineProps(['auditId', 'auditData', 'auditStatus', 'loading', 'programaId']);
const emit = defineEmits(['saved', 'close']);
const toast = useToast();

const loading = ref(false);
const localActiveTab = ref('cronograma');
const chartCanvas = ref(null);
let chartInstance = null;
const saving = ref(false);
const auditData = ref({});
const equipo = ref([]);
const procesosAuditados = ref([]);
const dragOverIndex = ref(null);
const agenda = ref([]);
const days = ref([]);
const requisitosModal = ref(null);
const currentRowForRequisitos = ref(null);
const pdfTemplate = ref(null);
const printModalRef = ref(null);

const auditoresTeam = computed(() => {
    return equipo.value.filter(m => m.aeq_rol !== 'Observador');
});

const observadoresTeam = computed(() => {
    return equipo.value.filter(m => m.aeq_rol === 'Observador');
});

const totalHH = computed(() => {
    let hours = 0;
    agenda.value.forEach(row => {
        const diff = getTimeDiff(row.aea_hora_inicio, row.aea_hora_fin);
        const dayCount = Object.values(row.schedule).filter(v => v === true).length;

        if (['apertura', 'cierre', 'gabinete'].includes(row.aea_tipo)) {
            hours += (diff * dayCount * equipo.value.length);
        } else {
            // Auditor + Observer (if any)
            let multiplier = 0;
            if (row.auditor_id) multiplier++;
            if (row.observador_id) multiplier++;
            hours += (diff * dayCount * multiplier);
        }
    });
    return parseFloat(hours.toFixed(2));
});

const selectedAuditors = ref(new Set());

const toggleAuditorSelection = (auditorId) => {
    if (selectedAuditors.value.has(auditorId)) {
        selectedAuditors.value.delete(auditorId);
    } else {
        selectedAuditors.value.add(auditorId);
    }
    // Trigger chart update
    renderChart();
};

const clearAuditorSelection = () => {
    selectedAuditors.value.clear();
    renderChart();
};

const auditorBalance = computed(() => {
    const balance = {};

    // Initialize for all team members
    equipo.value.forEach(m => {
        balance[m.auditor_id] = {
            id: m.auditor_id,
            name: m.auditor?.user?.name || m.usuario?.name || 'Desconocido',
            role: m.aeq_rol,
            isObserver: m.aeq_rol === 'Observador',
            hours: 0
        };
    });

    agenda.value.forEach(row => {
        const diff = getTimeDiff(row.aea_hora_inicio, row.aea_hora_fin);
        const dayCount = Object.values(row.schedule).filter(v => v === true).length;
        const hh = diff * dayCount;

        if (['apertura', 'cierre', 'gabinete'].includes(row.aea_tipo)) {
            // Add to ALL members
            Object.values(balance).forEach(aud => {
                aud.hours += hh;
            });
        } else {
            // Auditor
            if (row.auditor_id && balance[row.auditor_id]) {
                balance[row.auditor_id].hours += hh;
            }
            // Observer
            if (row.observador_id && balance[row.observador_id]) {
                balance[row.observador_id].hours += hh;
            }
        }
    });

    const list = Object.values(balance).map(a => ({
        ...a,
        hours: parseFloat(a.hours.toFixed(2))
    }));

    const max = Math.max(...list.map(a => a.hours), 1);

    return list.map(a => ({
        ...a,
        percentage: Math.min(100, (a.hours / max) * 100)
    })).sort((a, b) => b.hours - a.hours);
});

const totalHHPerDay = computed(() => {
    if (!days.value) return [];

    return days.value.map(day => {
        let total = 0;
        agenda.value.forEach(row => {
            if (row.schedule && row.schedule[day.date]) {
                const hh = getTimeDiff(row.aea_hora_inicio, row.aea_hora_fin);

                // Filtering Logic
                if (['apertura', 'cierre', 'gabinete'].includes(row.aea_tipo)) {
                    // For administrative, sum hours for each SELECTED member (or all if none selected)
                    equipo.value.forEach(member => {
                        if (selectedAuditors.value.size === 0 || selectedAuditors.value.has(member.auditor_id)) {
                            total += hh;
                        }
                    });
                } else {
                    // Execution
                    if (row.auditor_id) {
                        if (selectedAuditors.value.size === 0 || selectedAuditors.value.has(row.auditor_id)) {
                            total += hh;
                        }
                    }
                    if (row.observador_id) {
                        if (selectedAuditors.value.size === 0 || selectedAuditors.value.has(row.observador_id)) {
                            total += hh;
                        }
                    }
                }
            }
        });
        return {
            label: formatDateShort(day.date),
            total: parseFloat(total.toFixed(2))
        };
    });
});

const renderChart = () => {
    if (!chartCanvas.value) return;

    if (chartInstance) {
        chartInstance.destroy();
    }

    const data = totalHHPerDay.value;
    const isLarge = data.length > 10;
    const chartType = isLarge ? 'line' : 'bar';

    // Different color if filtering
    const isFiltered = selectedAuditors.value.size > 0;
    const baseColor = isFiltered ? 'rgba(0, 123, 255, 0.7)' : 'rgba(220, 53, 69, 0.7)';
    const borderColor = isFiltered ? 'rgba(0, 123, 255, 1)' : 'rgba(220, 53, 69, 1)';
    const bgLarge = isFiltered ? 'rgba(0, 123, 255, 0.2)' : 'rgba(220, 53, 69, 0.2)';

    chartInstance = new Chart(chartCanvas.value, {
        type: chartType,
        data: {
            labels: data.map(d => d.label),
            datasets: [{
                label: isFiltered ? 'HH Filtradas' : 'HH Totales',
                data: data.map(d => d.total),
                backgroundColor: isLarge ? bgLarge : baseColor,
                borderColor: borderColor,
                borderWidth: 2,
                borderRadius: isLarge ? 0 : 5,
                barThickness: isLarge ? undefined : 30,
                fill: isLarge,
                tension: 0.4,
                pointRadius: isLarge ? 3 : 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: isFiltered },
                tooltip: {
                    callbacks: {
                        label: (context) => ` ${context.parsed.y} HH`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Horas Hombre (HH)' },
                    grid: { color: '#f0f0f0' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
};

watch([localActiveTab, totalHHPerDay], ([tab]) => {
    if (tab === 'balance') {
        setTimeout(renderChart, 100);
    }
});

const getAgendaForDay = (date) => {
    const list = [];
    agenda.value.forEach(row => {
        if (row.schedule[date]) {
            let auditorName = 'Por definir';
            if (['apertura', 'cierre', 'gabinete'].includes(row.aea_tipo)) {
                auditorName = 'Equipo Completo';
            } else if (row.auditor_id) {
                const m = equipo.value.find(x => x.auditor_id === row.auditor_id);
                auditorName = m ? (m.auditor?.user?.name || m.usuario?.name || 'User') : 'Desconocido';

                if (row.observador_id) {
                    const o = equipo.value.find(x => x.auditor_id === row.observador_id);
                    const obsName = o ? (o.auditor?.user?.name || o.usuario?.name || 'User') : 'Desconocido';
                    auditorName += ` + Obs: ${obsName}`;
                }
            }

            list.push({
                time: `${row.aea_hora_inicio} - ${row.aea_hora_fin}`,
                actividad: row.aea_actividad,
                auditor: auditorName,
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
    return d.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
};

const formatDateShort = (dateStr) => {
    const d = new Date(dateStr + 'T00:00:00');
    // Get day name (short) and take first letter uppercase
    let dayName = d.toLocaleDateString('es-ES', { weekday: 'short' });
    let letter = dayName.charAt(0).toUpperCase();

    // Handle Miércoles (usually 'mié' -> 'M'). If you want X for Wednesday, handle manually.
    // Standard Spanish calendars often use L M X J V S D.
    // If we rely on automatic first letter, Martes and Miércoles are both 'M'.
    // Let's implement a simple map for clarity if preferred, or stick to first letter.
    // Given "letra del dia" typically implies L M X J V S D in planning context:
    const days = ['D', 'L', 'M', 'X', 'J', 'V', 'S'];
    letter = days[d.getDay()];

    return `${letter} ${d.getDate()}`;
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
        auditor_id: null,
        observador_id: null,
        aea_hora_inicio: '08:00',
        aea_hora_fin: '17:00',
        aea_requisito: [],
        aea_tipo: 'ejecucion',
        itemIds: {},
        schedule: initSchedule() // Ensure explicit initialization based on days
    });
    sortAgenda();
};

const updateAuditorName = (row) => {
    // Logic kept for reactivity if needed, but 'aea_auditor' is removed from DB.
    // If UI uses it for local display:
    // We can rely on select display text.
    // However, if other parts of this component rely on 'aea_auditor' as text, we might need it client-side temporarily or computed.
    // But user Deleted it. So we should NOT rely on it.
};

const sortAgenda = () => {
    const order = {
        'apertura': 1,
        'ejecucion': 2,
        'gabinete': 3,
        'cierre': 4
    };

    agenda.value.sort((a, b) => {
        const valA = order[a.aea_tipo] || 2;
        const valB = order[b.aea_tipo] || 2;

        if (valA !== valB) return valA - valB;

        // For 'ejecucion' (or equal types), sort by Earliest Date first
        const getEarliestDate = (row) => {
            if (!row.schedule) return '9999-99-99';
            // Get dates where value is true
            const dates = Object.entries(row.schedule)
                .filter(([d, val]) => val)
                .map(([d]) => d);
            if (dates.length === 0) return '9999-99-99';
            return dates.sort()[0];
        };

        const dateA = getEarliestDate(a);
        const dateB = getEarliestDate(b);

        if (dateA !== dateB) return dateA.localeCompare(dateB);

        // Then by time
        return (a.aea_hora_inicio || '').localeCompare(b.aea_hora_inicio || '');
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
                auditor_id: null,
                observador_id: null,
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
    sortAgenda();
};

const removeRow = (idx) => {
    agenda.value.splice(idx, 1);
};

const handleActivityChange = (row) => {
    const proc = procesosAuditados.value.find(p => (p.pro_nombre || p.proceso_nombre) === row.aea_actividad);
    row.proceso_id = proc ? proc.id : null;

    if (row.aea_actividad === 'Reunión de Apertura') {
        row.aea_tipo = 'apertura';
        row.auditor_id = null;
    }
    else if (row.aea_actividad === 'Reunión de Cierre') {
        row.aea_tipo = 'cierre';
        row.auditor_id = null;
    }
    else if (row.aea_actividad === 'Trabajo de Gabinete') {
        row.aea_tipo = 'gabinete';
        row.auditor_id = null;
    }
    else {
        row.aea_tipo = 'ejecucion';
    }
    sortAgenda();
};

const openRequisitosModal = (row) => {
    currentRowForRequisitos.value = row;
    requisitosModal.value.open(row.aea_requisito);
};

const handleRequisitosSelected = (items) => {
    if (currentRowForRequisitos.value) {
        const storedValue = items.map(i => ({
            id: i.id, // This is nr_id
            norma_id: i.norma_id,
            numeral: i.numeral,
            norma: i.nombre_norma
        }));
        currentRowForRequisitos.value.aea_requisito = storedValue;
    }
};

const clearRequisitos = (row) => {
    row.aea_requisito = null;
};

const getFormattedRequisitosLabel = (reqs) => {
    if (!reqs) return '';
    if (typeof reqs === 'string') return reqs;
    if (Array.isArray(reqs)) {
        if (reqs.length === 1) return reqs[0].numeral;
        return `${reqs.length} reqs`;
    }
    return '...';
};

const getFormattedRequisitosTooltip = (reqs) => {
    if (!reqs) return '';
    if (typeof reqs === 'string') return reqs;
    if (Array.isArray(reqs)) {
        return reqs.map(r => `${r.norma}: ${r.numeral}`).join('\n');
    }
    return '';
};

const handleDragStart = (event, row) => {
    if (!row.aea_requisito) {
        event.preventDefault();
        return;
    }
    event.dataTransfer.effectAllowed = 'copy';
    event.dataTransfer.setData('application/json', JSON.stringify(row.aea_requisito));
};

const handleDrop = (event, row) => {
    dragOverIndex.value = null;
    const data = event.dataTransfer.getData('application/json');
    if (data) {
        try {
            const parsed = JSON.parse(data);
            if (Array.isArray(parsed)) {
                // Clone deeply to avoid reference issues
                row.aea_requisito = JSON.parse(JSON.stringify(parsed));
                toast.add({ severity: 'success', summary: 'Copiado', detail: 'Requisitos copiados', life: 2000 });
            }
        } catch (e) {
            console.error('Drop error', e);
        }
    }
};

const getInitials = (name) => {
    if (!name) return '';
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
};

const sortedPdfItems = computed(() => {
    const items = [];
    agenda.value.forEach((row, idx) => {
        Object.entries(row.schedule).forEach(([date, checked]) => {
            if (checked) {
                let reqLabel = '';
                if (Array.isArray(row.aea_requisito)) {
                    reqLabel = row.aea_requisito.map(r => r.numeral).join(', ');
                } else {
                    reqLabel = row.aea_requisito || '';
                }

                let auditorName = '';
                if (row.auditor_id) {
                    const m = equipo.value.find(x => x.auditor_id === row.auditor_id);
                    auditorName = m ? (m.auditor?.user?.name || m.usuario?.name || '') : '';
                }

                items.push({
                    key: `${idx}-${date}`,
                    date: date,
                    timeStart: row.aea_hora_inicio,
                    timeEnd: row.aea_hora_fin,
                    activity: row.aea_actividad,
                    auditor: getInitials(auditorName),
                    requisitos: reqLabel
                });
            }
        });
    });

    return items.sort((a, b) => {
        if (a.date !== b.date) return a.date.localeCompare(b.date);
        return a.timeStart.localeCompare(b.timeStart);
    });
});

const openPrintModal = () => {
    printModalRef.value.open(
        props.auditData,
        equipo.value,
        sortedPdfItems.value,
        props.auditId
    );
};

const downloadPdf = () => {
    const content = pdfTemplate.value.innerHTML;
    const printWindow = window.open('', '_blank');

    if (!printWindow) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Habilite las ventanas emergentes.', life: 3000 });
        return;
    }

    printWindow.document.write(`
        <html>
        <head>
            <title>Plan de Auditoría ${props.auditData.ae_codigo || ''}</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
            <style>
                body { background: #525659; display: flex; justify-content: center; padding-top: 20px; font-family: Arial, sans-serif; }
                #print-container { 
                    background: white; 
                    width: 210mm; 
                    min-height: 297mm; 
                    padding: 15mm; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.5); 
                    font-size: 10pt;
                    font-family: Arial, sans-serif;
                }
                @media print {
                    body { background: white; display: block; padding: 0; }
                    #print-container { width: 100%; min-height: auto; box-shadow: none; padding: 0; margin: 0; }
                    .no-print { display: none !important; }
                    * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                }
            </style>
        </head>
        <body>
            <div class="no-print" style="position: fixed; top: 10px; right: 10px; display: flex; gap: 10px; z-index: 1000;">
                <button onclick="window.print()" class="btn btn-primary shadow">
                    <i class="fas fa-print mr-1"></i> Imprimir / PDF
                </button>
                <button onclick="exportWord()" class="btn btn-info shadow text-white">
                    <i class="fas fa-file-word mr-1"></i> Descargar Word
                </button>
            </div>
            
            <div id="print-container">
                ${content}
            </div>

            <script>
                function exportWord() {
                    var preHtml = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Plan de Auditoría</title></head><body>";
                    var postHtml = "</body></html>";
                    var html = preHtml + document.getElementById("print-container").innerHTML + postHtml;

                    var url = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(html);
                    
                    var link = document.createElement("a");
                    document.body.appendChild(link);
                    link.href = url;
                    link.download = 'Plan_Auditoria.doc';
                    link.click();
                    document.body.removeChild(link);
                }
            <\/script>
        <\/body>

        <\/html>
`);
    printWindow.document.close();
};


const loadData = async () => {
    if (!props.auditId) return;

    // Si ya tenemos los datos via props, los usamos directamente
    if (props.auditData && Object.keys(props.auditData).length > 0) {
        assignAuditData(props.auditData);
        return;
    }

    loading.value = true;
    try {
        const response = await axios.get(`/api/auditorias/${props.auditId}`);
        assignAuditData(response.data);
    } catch (e) {
        console.error("Error loading planning data", e);
    } finally {
        loading.value = false;
    }
};

const assignAuditData = (data) => {
    auditData.value = {
        ...data,
        ae_fecha_inicio: data.ae_fecha_inicio?.substring(0, 10),
        ae_fecha_fin: data.ae_fecha_fin?.substring(0, 10)
    };

    equipo.value = data.equipo || [];
    procesosAuditados.value = data.procesos || [];

    // Generate day columns based on audit dates
    days.value = calculateDays(auditData.value.ae_fecha_inicio, auditData.value.ae_fecha_fin);

    // Map existing agenda or create default
    if (data.agenda && data.agenda.length > 0) {
        const grouped = {};
        data.agenda.forEach(a => {
            // Use auditor_id in key if available, else aea_auditor
            const audKey = a.auditor_id || 'no-auditor';
            const obsKey = a.observador_id || 'no-observer';
            const key = `${a.aea_actividad}-${audKey}-${obsKey}-${a.aea_hora_inicio}-${a.aea_hora_fin}`;
            if (!grouped[key]) {
                // Try to recover proceso_id if missing (legacy data healing)
                let pId = a.proceso_id;
                if (!pId && a.aea_actividad) {
                    const foundP = procesosAuditados.value.find(p => (p.pro_nombre || p.proceso_nombre) === a.aea_actividad);
                    if (foundP) pId = foundP.id;
                }

                grouped[key] = {
                    proceso_id: pId || null,
                    aea_actividad: a.aea_actividad,
                    aea_auditor: a.aea_auditor, // Legacy/Display
                    auditor_id: a.auditor_id,
                    observador_id: a.observador_id, // NEW
                    aea_requisito: a.aea_requisito,
                    aea_tipo: a.aea_tipo || 'ejecucion',
                    aea_hora_inicio: a.aea_hora_inicio.substring(0, 5),
                    aea_hora_fin: a.aea_hora_fin.substring(0, 5),
                    schedule: initSchedule(),
                    itemIds: {} // Initialize itemIds map
                };
            }
            const date = a.aea_fecha;
            if (grouped[key].schedule[date] !== undefined) {
                grouped[key].schedule[date] = true;
                grouped[key].itemIds[date] = a.id; // Store the ID for this specific date/activity
            }
        });
        agenda.value = Object.values(grouped);
        sortAgenda();
    } else {
        agenda.value = [];
        addRow();
    }
};

const formatDateFull = (dateStr) => {
    if (!dateStr) return '';
    try {
        // Handle Date object or String
        const d = new Date(dateStr);
        // Correct time zone offset issue by appending time if missing?
        // Or using split-reverse-join for guaranteed local date
        if (typeof dateStr === 'string' && dateStr.includes('-')) {
            const [y, m, d] = dateStr.substring(0, 10).split('-');
            return `${d}/${m}/${y}`;
        }
        if (isNaN(d.getTime())) return dateStr;
        return d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
    } catch (e) { return dateStr; }
};

watch(() => props.auditData, (newVal) => {
    if (newVal && Object.keys(newVal).length > 0) {
        assignAuditData(newVal);
        loading.value = false;
    }
}, { immediate: true });

watch(() => props.loading, (newVal) => {
    // Only show loader if we don't have agenda data yet
    if (newVal && agenda.value.length === 0) {
        loading.value = true;
    } else if (!newVal) {
        loading.value = false;
    }
}, { immediate: true });



const save = async () => {
    saving.value = true;
    try {
        // Flatten agenda back to DB format (one row per checked slot)
        const flatAgenda = [];
        agenda.value.forEach(row => {
            Object.entries(row.schedule).forEach(([date, checked]) => {
                if (checked) {
                    // Try to find the original ID if this slot was loaded from DB
                    // The 'checked' value in schedule is just boolean true? 
                    // Or we need to store the ID in the schedule or look it up.
                    // Actually, 'schedule' structure is likely { 'YYYY-MM-DD': boolean }. 
                    // This implies we lose the ID if we just store boolean.
                    // We need to change how we store state or how we find the ID.

                    // Let's assume we can look up the ID from a separate map or change schedule structure?
                    // Changing schedule structure is risky for the grid renderer.

                    // Alternative: We have `originalAgendaItems` maybe?
                    // Let's look at `loadData` again.
                    // It seems we map data to `agenda.value`.

                    // If we can't easily track ID per cell, we might perform "Soft Sync" on backend (match by Activity+Date+Time).
                    // But if user changes time, we lose match.

                    // BETTER: Use `row.ids` map if possible?
                    // In `initAgenda` (lines 535+), we see:
                    // `row.schedule[item.aea_fecha] = true;`
                    // We should change this to store the ID or an object.

                    // Let's pause and use a lookup since we don't want to break the v-model of checkboxes if they expect boolean.
                    // Wait, `v-model="row.schedule[day.date]"` on a checkbox expects boolean (or array).

                    // Quick fix: Add `row.itemIds` object { 'YYYY-MM-DD': id } to the row structure.

                    flatAgenda.push({
                        id: row.itemIds ? row.itemIds[date] : null, // Send ID if exists
                        ae_id: props.auditId,
                        proceso_id: row.proceso_id || null,
                        aea_fecha: date,
                        aea_hora_inicio: row.aea_hora_inicio,
                        aea_hora_fin: row.aea_hora_fin,
                        aea_actividad: row.aea_actividad,
                        aea_auditado: null,
                        auditor_id: row.auditor_id,
                        observador_id: row.observador_id || null,
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
    height: calc(1.4em + 0.5rem + 2px);
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
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
    width: 15px;
    height: 15px;
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
