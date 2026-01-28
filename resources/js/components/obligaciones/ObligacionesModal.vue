<template>
    <Teleport to="body">
        <div class="modal fade" tabindex="-1" ref="modalRef" aria-hidden="true" style="z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                    <!-- Bootstrap Header Red Danger -->
                    <div class="modal-header bg-danger text-white py-3">
                        <h5 class="modal-title font-weight-bold">
                            <i class="fas fa-balance-scale mr-2"></i> Gestión de Obligación
                        </h5>
                        <button type="button" class="close text-white opacity-100" @click="closeModal"
                            aria-label="Close" style="opacity: 1;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body bg-light p-0">
                        <div class="d-flex h-100">
                            <!-- Sidebar Navigation -->
                            <div class="bg-white border-right" style="width: 260px; min-height: 600px;">
                                <div class="nav flex-column nav-pills py-3" id="v-pills-tab" role="tablist"
                                    aria-orientation="vertical">

                                    <h6 class="text-secondary mx-3 mt-2">GENERAL</h6>
                                    <a class="nav-link" href="#"
                                        :class="{ 'text-danger active': activeTab === 'general' }"
                                        @click.prevent="activeTab = 'general'" role="tab">
                                        <i class="fas fa-file-alt"></i> Datos Generales
                                    </a>

                                    <hr class="my-2 border-secondary mx-3 opacity-50">

                                    <h6 class="text-secondary mx-3 mt-3">ASOCIACIONES</h6>
                                    <a class="nav-link" href="#"
                                        :class="{ 'text-danger active': activeTab === 'procesos' }"
                                        @click.prevent="activeTab = 'procesos'" role="tab">
                                        <i class="fas fa-network-wired"></i> Asociar a Procesos
                                    </a>
                                    <a class="nav-link" href="#"
                                        :class="{ 'text-danger active': activeTab === 'evaluacion' }"
                                        @click.prevent="activeTab = 'evaluacion'" role="tab">
                                        <i class="fas fa-exclamation-triangle"></i> Evaluación (Riesgos)
                                    </a>
                                    <a class="nav-link" href="#"
                                        :class="{ 'text-danger active': activeTab === 'acciones' }"
                                        @click.prevent="activeTab = 'acciones'" role="tab">
                                        <i class="fas fa-tasks"></i> Plan de Acción
                                    </a>
                                    <a class="nav-link" href="#"
                                        :class="{ 'text-danger active': activeTab === 'controles' }"
                                        @click.prevent="activeTab = 'controles'" role="tab">
                                        <i class="fas fa-shield-alt"></i> Controles
                                    </a>
                                </div>
                            </div>

                            <!-- Content Area -->
                            <div class="col-md-9 px-4 py-4">
                                <!-- Tab: General -->
                                <div v-if="activeTab === 'general'">
                                    <h6 class="text-danger font-weight-bold mb-3 border-bottom pb-2">Datos Generales
                                    </h6>
                                    <ObligacionForm v-model="form" :procesos="procesos" :areas="areas"
                                        :subareas="subareas" />
                                </div>

                                <!-- Tab: Procesos (Multi-select) -->
                                <div v-if="activeTab === 'procesos'">
                                    <AsignarProcesosObligacion v-model="form.procesos_ids" :all-procesos="procesos" />
                                </div>

                                <!-- Tab: Evaluacion (Riesgos) -->
                                <div v-if="activeTab === 'evaluacion'">
                                    <h6 class="text-danger font-weight-bold mb-3 border-bottom pb-2">
                                        Evaluación de Riesgo de Incumplimiento
                                    </h6>

                                    <div class="d-flex justify-content-between mb-3">
                                        <div>
                                            <span class="text-muted small">Riesgos vinculados: {{ riesgos.length
                                            }}</span>
                                        </div>
                                        <button class="btn btn-danger btn-sm" @click="openRiesgoModal">
                                            <i class="fas fa-plus mr-1"></i> Crear/Asociar Riesgo
                                        </button>
                                    </div>

                                    <div v-if="riesgos.length === 0" class="text-center py-5">
                                        <img src="https://illustrations.popsy.co/gray/cushion.svg"
                                            style="height: 120px; opacity: 0.6;" class="mb-3">
                                        <p class="text-muted">No se han asociado riesgos a esta obligación.</p>
                                    </div>

                                    <div v-else class="list-group shadow-sm">
                                        <div v-for="riesgo in riesgos" :key="riesgo.id"
                                            class="list-group-item list-group-item-action flex-column align-items-start border-left-danger p-3 mb-2 rounded border-0 shadow-sm">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1 text-dark font-weight-bold">{{ riesgo.riesgo_nombre }}
                                                </h6>
                                                <span class="badge" :class="getSemaforoClass(riesgo.riesgo_valoracion)">
                                                    {{ riesgo.riesgo_valoracion }}
                                                </span>
                                            </div>
                                            <p class="mb-1 text-muted small">{{ riesgo.riesgo_descripcion }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab: Acciones -->
                                <div v-if="activeTab === 'acciones'">
                                    <div
                                        class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                        <h6 class="text-danger font-weight-bold mb-0">Plan de Acción</h6>
                                        <button class="btn btn-danger btn-sm shadow-sm" @click="openAccionModal">
                                            <i class="fas fa-plus mr-1"></i> Nueva Acción
                                        </button>
                                    </div>

                                    <div v-if="!form.id" class="alert alert-warning">
                                        Guarde la obligación antes de agregar acciones.
                                    </div>

                                    <div v-else>
                                        <div v-if="acciones.length === 0" class="text-center py-5 text-muted">
                                            Sin acciones registradas para esta obligación.
                                        </div>
                                        <table v-else class="table table-hover table-sm small border shadow-sm">
                                            <thead class="bg-white">
                                                <tr>
                                                    <th>Descripción</th>
                                                    <th>Responsable</th>
                                                    <th>Fecha Fin</th>
                                                    <th>Estado</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="accion in acciones" :key="accion.id">
                                                    <td>
                                                        {{ accion.accion_descripcion }}
                                                        <span v-if="accion.es_control_permanente"
                                                            class="badge badge-info ml-1"
                                                            title="Establece un Control Permanente">
                                                            <i class="fas fa-shield-alt"></i> Control
                                                        </span>
                                                    </td>
                                                    <td>{{ accion.responsable?.name || 'N/A' }}</td>
                                                    <td>{{ accion.accion_fecha_fin_planificada || 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge"
                                                            :class="getEstadoBadgeClass(accion.accion_estado)">
                                                            {{ accion.accion_estado }}
                                                        </span>
                                                    </td>
                                                    <td class="text-right">
                                                        <button class="btn btn-xs btn-light text-danger"><i
                                                                class="fas fa-pencil-alt"></i></button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Tab: Controles -->
                                <!-- Tab: Controles -->
                                <div v-if="activeTab === 'controles'">
                                    <h6 class="text-danger font-weight-bold mb-3 border-bottom pb-2">Controles de
                                        Cumplimiento (ISO 37301)</h6>

                                    <!-- Controles Directos -->
                                    <div class="mb-4">
                                        <ControlSelector v-model="form.controles_ids"
                                            label="Controles de Cumplimiento Directos"
                                            helperText="Vincule controles del catálogo maestro que aplican directamente a esta obligación." />
                                    </div>

                                    <!-- Vista Consolidada -->
                                    <h6 class="small font-weight-bold text-muted text-uppercase mb-3 mt-4">Matriz
                                        Consolidada de Controles</h6>

                                    <div v-if="riesgos.length === 0 && !controlesData.length"
                                        class="alert alert-info small">
                                        No se han detectado controles (directos o vía riesgos) para esta obligación.
                                    </div>

                                    <div v-else class="table-responsive">
                                        <table class="table table-sm table-hover border small">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Origen</th>
                                                    <th>Control</th>
                                                    <th>Tipo</th>
                                                    <th>Referencia</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Directos -->
                                                <tr v-for="ctrl in controlesData" :key="'dir-' + ctrl.id"
                                                    class="table-success">
                                                    <td><span class="badge badge-success">Directo</span></td>
                                                    <td class="font-weight-bold">{{ ctrl.nombre }}</td>
                                                    <td><span class="badge badge-light border">{{ ctrl.tipo }}</span>
                                                    </td>
                                                    <td class="text-muted">Obligación Principal</td>
                                                </tr>

                                                <!-- Vía Riesgos -->
                                                <template v-for="riesgo in riesgos" :key="'r-'+riesgo.id">
                                                    <tr v-for="control in riesgo.controles" :key="'rc-' + control.id">
                                                        <td><span class="badge badge-warning">Riesgo</span></td>
                                                        <td class="font-weight-bold">{{ control.nombre }}</td>
                                                        <td><span class="badge badge-light border">{{ control.tipo
                                                        }}</span></td>
                                                        <td class="text-muted small">{{ riesgo.riesgo_nombre }}</td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top-0 py-3">
                        <button type="button" class="btn btn-secondary px-4 checkbox-shadow"
                            @click="closeModal">Cancelar</button>
                        <button type="button" class="btn btn-danger px-4 shadow-sm" @click="submitForm"
                            :disabled="saving">
                            <i class="fas fa-save mr-1"></i>
                            {{ saving ? 'Guardando...' : 'Guardar Obligación' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, watch, computed } from 'vue';
import { Modal } from 'bootstrap';
import axios from 'axios';
import Swal from 'sweetalert2';
import ObligacionForm from './ObligacionForm.vue';
import AsignarProcesosObligacion from './AsignarProcesosObligacion.vue'; // Import component
import ControlSelector from '../controles/ControlSelector.vue';

const props = defineProps({
    show: Boolean,
    obligacion: Object
});

const emit = defineEmits(['close', 'saved']);
const modalRef = ref(null);
const modalInstance = ref(null);
const activeTab = ref('general');
const saving = ref(false);

// Data Lists
const procesos = ref([]);
const areas = ref([]);
const subareas = ref([]); // All subareas loaded once
const riesgos = ref([]);
const controlesData = ref([]); // Store full objects for direct controls
const acciones = ref([]);
const searchProceso = ref('');

const form = reactive({
    id: null,
    // proceso_id: '', // Deprecated. Use procesos_ids
    procesos_ids: [],
    area_compliance_id: '',
    subarea_compliance_id: '',
    documento_tecnico_normativo: '',
    obligacion_principal: '',
    tipo_obligacion: 'Legal',
    estado_obligacion: 'pendiente',
    consecuencia_incumplimiento: '',
    frecuencia_revision: null,
    controles_ids: [],
});

const filteredProcesos = computed(() => {
    if (!searchProceso.value) return procesos.value;
    return procesos.value.filter(p => p.proceso_nombre.toLowerCase().includes(searchProceso.value.toLowerCase()));
});

const resetForm = () => {
    Object.assign(form, {
        id: null,
        procesos_ids: [],
        area_compliance_id: '',
        subarea_compliance_id: '',
        documento_tecnico_normativo: '',
        obligacion_principal: '',
        tipo_obligacion: 'Legal',
        estado_obligacion: 'pendiente',
        consecuencia_incumplimiento: '',
        frecuencia_revision: null,
        controles_ids: [],
    });
    riesgos.value = [];
    acciones.value = [];
    activeTab.value = 'general';
};

const populateForm = (data) => {
    Object.assign(form, {
        id: data.id,
        procesos_ids: data.procesos ? data.procesos.map(p => p.id) : [], // If loaded with relation
        area_compliance_id: data.area_compliance_id,
        subarea_compliance_id: data.subarea_compliance_id,
        documento_tecnico_normativo: data.documento_tecnico_normativo,
        obligacion_principal: data.obligacion_principal,
        tipo_obligacion: data.tipo_obligacion,
        estado_obligacion: data.estado_obligacion,
        consecuencia_incumplimiento: data.consecuencia_incumplimiento,
        frecuencia_revision: data.frecuencia_revision,
        controles_ids: data.controles ? data.controles.map(c => c.id) : [],
    });

    if (data.id) {
        fetchRiesgos(data.id);
        fetchAcciones(data.id);
    }
};

const fetchListas = async () => {
    try {
        const [procRes, areaRes, subRes] = await Promise.all([
            axios.get('/api/procesos'), // Ensure this returns all
            axios.get('/api/areas-compliance'),
            axios.get('/api/subareas-compliance') // Dedicated endpoint
        ]);
        procesos.value = procRes.data;
        areas.value = areaRes.data;
        // Prioritize dedicated endpoint data
        subareas.value = subRes.data || [];

        // Fallback or merge if areas had nested subareas (optional, removing complexity)
        if (subareas.value.length === 0 && areaRes.data && areaRes.data.length > 0 && areaRes.data[0].subareas) {
            subareas.value = areaRes.data.flatMap(a => a.subareas);
        }
    } catch (e) { console.error("Error loading lists", e); }
};

const fetchRiesgos = async (id) => {
    try {
        const res = await axios.get(`/api/obligaciones/${id}/riesgos`);
        riesgos.value = res.data.riesgos;
        controlesData.value = res.data.controles_directos; // Full objects
        form.controles_ids = res.data.controles_directos.map(c => c.id);
    } catch (e) { }
};

const fetchAcciones = async (id) => {
    try {
        const res = await axios.get(`/api/obligaciones/${id}/acciones`);
        acciones.value = res.data;
        console.log('acciones', res.data)
    } catch (e) { }
};

watch(() => props.show, (newVal) => {
    if (newVal) {
        resetForm();
        fetchListas();
        if (props.obligacion) populateForm(props.obligacion);
        if (modalInstance.value) modalInstance.value.show();
    } else {
        if (modalInstance.value) modalInstance.value.hide();
    }
});

const closeModal = () => { emit('close'); };

const submitForm = async () => {
    saving.value = true;
    try {
        if (form.id) {
            await axios.put(`/api/obligaciones/${form.id}`, form);
        } else {
            const res = await axios.post('/api/obligaciones', form);
            form.id = res.data.obligacion.id;
        }
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Obligación guardada', showConfirmButton: false, timer: 1500 });
        emit('saved');
    } catch (e) {
        Swal.fire('Error', 'No se pudo guardar la obligación', 'error');
    } finally {
        saving.value = false;
    }
};

const openRiesgoModal = () => {
    // Logic to open Risk Modal / Association
    Swal.fire('Info', 'Funcionalidad de Crear/Asociar Riesgo pendiente de implementación en RiskModule', 'info');
};

const openAccionModal = () => {
    // Logic to open Action Modal
    Swal.fire('Info', 'Funcionalidad de Crear Acción pendiente de implementación final en AccionesModule', 'info');
};


onMounted(() => {
    modalInstance.value = new Modal(modalRef.value, { backdrop: 'static', keyboard: false });
});

onUnmounted(() => { if (modalInstance.value) modalInstance.value.dispose(); });

// Utility
const getEstadoBadgeClass = (s) => ({ 'programada': 'badge-secondary', 'implementada': 'badge-success', 'en proceso': 'badge-warning' }[s] || 'badge-light');
const getSemaforoClass = (v) => 'badge-dark';

</script>

<style scoped>
/* Estilos del menú lateral */
.nav-pills .nav-link {
    font-size: 0.9rem;
    padding: 0.75rem 1rem;
    border-radius: 0.25rem;
    text-align: left !important;
    transition: background-color 0.2s ease-in-out;
}

.nav-pills .nav-link:not(.active):hover {
    background-color: #f8f9fa;
    color: #000;
}

.nav-pills .nav-link.active {
    background-color: #fff;
    font-weight: bold;
    color: #dc3545 !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.nav-link i {
    width: 1.5rem;
    text-align: left !important;
}

.nav-pills h6 {
    text-transform: uppercase;
    font-size: 0.7rem;
    margin-bottom: 0.5rem;
    letter-spacing: 0.05rem;
    text-align: left !important;
}

.modal-body-scrollable {
    height: 90vh;
    /* Ajusta este valor según lo necesites */
    overflow-y: auto;
}

.border-left-danger {
    border-left: 4px solid #dc3545;
}
</style>
