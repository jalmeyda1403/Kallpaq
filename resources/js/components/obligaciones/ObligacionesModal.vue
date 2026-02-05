<template>
    <Teleport to="body">
        <div class="modal fade" tabindex="-1" ref="modalRef" aria-hidden="true" style="z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <!-- Bootstrap Header Red Danger -->
                    <div class="modal-header bg-danger text-white py-3">
                        <h5 class="modal-title font-weight-bold">
                            <i class="fas fa-balance-scale mr-2"></i> Gestión de Obligación
                            <span v-if="form.obligacion_estado" class="badge ml-2 text-capitalize"
                                :class="getEstadoBadgeClass(form.obligacion_estado)">
                                {{ form.obligacion_estado.replace('_', ' ') }}
                            </span>
                        </h5>

                        <div class="ml-auto d-flex align-items-center">
                            <select v-if="form.id"
                                class="form-control form-control-sm mr-3 font-weight-bold text-uppercase"
                                style="width: auto; height: 30px; padding-top: 2px;" v-model="selectedState"
                                @change="attemptStateChange">
                                <option v-for="st in availableStates" :key="st" :value="st">
                                    {{ st.replace('_', ' ').toUpperCase() }}
                                </option>
                            </select>

                            <button type="button" class="close text-white opacity-100" @click="closeModal"
                                @mousedown.prevent aria-label="Close" style="opacity: 1;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="modal-body p-0" style="max-height: 75vh;">
                        <div class="d-flex" style="height: 75vh; overflow: hidden;">
                            <!-- Sidebar Navigation -->
                            <div class="nav flex-column nav-pills bg-light border-right p-3" style="width: 250px;"
                                role="tablist" aria-orientation="vertical">

                                <h6 class="text-secondary mx-3 mt-2">GENERAL</h6>
                                <a class="nav-link" href="#" :class="{ 'text-danger active': activeTab === 'general' }"
                                    @click.prevent="activeTab = 'general'" role="tab">
                                    <i class="fas fa-file-alt"></i> Datos Generales
                                </a>

                                <hr class="my-2 border-secondary mx-3 opacity-50">

                                <h6 class="text-secondary mx-3 mt-3">ASOCIACIONES</h6>
                                <a class="nav-link" href="#" :class="{ 'text-danger active': activeTab === 'procesos' }"
                                    @click.prevent="activeTab = 'procesos'" role="tab">
                                    <i class="fas fa-network-wired"></i> Asociar a Procesos
                                </a>

                                <a class="nav-link" href="#"
                                    :class="{ 'text-danger active': activeTab === 'criticidad' }"
                                    @click.prevent="activeTab = 'criticidad'" role="tab">
                                    <i class="fas fa-clipboard-check"></i> Evaluación de Criticidad
                                </a>
                                <a class="nav-link" href="#"
                                    :class="{ 'text-danger active': activeTab === 'evaluacion' }"
                                    @click.prevent="activeTab = 'evaluacion'" role="tab">
                                    <i class="fas fa-exclamation-triangle"></i> Evaluación (Riesgos)
                                </a>
                                <a class="nav-link" href="#" :class="{ 'text-danger active': activeTab === 'acciones' }"
                                    @click.prevent="activeTab = 'acciones'" role="tab">
                                    <i class="fas fa-tasks"></i> Plan de Acción
                                </a>
                                <a class="nav-link" href="#"
                                    :class="{ 'text-danger active': activeTab === 'controles' }"
                                    @click.prevent="activeTab = 'controles'" role="tab">
                                    <i class="fas fa-shield-alt"></i> Controles
                                </a>
                            </div>

                            <!-- Content Area -->
                            <div class="flex-grow-1 p-4" style="overflow-y: auto; height: 100%;">
                                <!-- Tab: General -->
                                <div v-if="activeTab === 'general'">
                                    <h6 class="text-danger font-weight-bold mb-3 border-bottom pb-2">Datos Generales
                                    </h6>
                                    <ObligacionForm v-model="form" :procesos="procesos" :areas="areas"
                                        :subareas="subareas" :loading="loadingListas"
                                        @open-ai-assistant="openAIAssistant" />
                                </div>

                                <!-- Tab: Procesos (Multi-select) -->
                                <div v-if="activeTab === 'procesos'">
                                    <AsignarProcesosObligacion v-model="form.procesos_ids" :all-procesos="procesos" />
                                </div>

                                <!-- Tab: Evaluacion Criticidad (ISO 37301) -->
                                <div v-if="activeTab === 'criticidad'">
                                    <ObligacionEvaluacionForm v-if="form.id" :obligacion-id="form.id"
                                        :evaluation="form.evaluacion_actual"
                                        :read-only="form.obligacion_estado === 'controlada' || form.obligacion_estado === 'inactiva'"
                                        @evaluated="handleEvaluated" />
                                    <div v-else class="alert alert-warning">
                                        Guarde la obligación antes de realizar la evaluación.
                                    </div>
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

                    <div class="modal-footer bg-light border-top-0 py-3" v-if="activeTab !== 'criticidad'">
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

        <!-- Asistente de IA -->
        <AsistenteIAObligacionModal @selected="onIAObligacionSelected" />
    </Teleport>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, watch, computed, nextTick } from 'vue';
import { useObligacionStore } from '../../stores/obligacionStore';
import { Modal } from 'bootstrap';
import axios from 'axios';
import Swal from 'sweetalert2';
import ObligacionForm from './ObligacionForm.vue';
import AsignarProcesosObligacion from './AsignarProcesosObligacion.vue'; // Import component
import ControlSelector from '../controles/ControlSelector.vue';
import AsistenteIAObligacionModal from './AsistenteIAObligacionModal.vue';
import ObligacionEvaluacionForm from './ObligacionEvaluacionForm.vue';

// Toast configuration
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
});

const props = defineProps({
    show: Boolean,
    obligacion: Object
});

const emit = defineEmits(['close', 'saved']);
const modalRef = ref(null);
const modalInstance = ref(null);
const activeTab = ref('general');
const saving = ref(false);

const store = useObligacionStore();

// Data Lists from store
const procesos = computed(() => store.procesos);
const areas = computed(() => store.areas);
const subareas = computed(() => store.subareas);
const loadingListas = computed(() => store.loadingListas);
const riesgos = ref([]);
const controlesData = ref([]); // Store full objects for direct controls
const acciones = ref([]);


const form = reactive({
    id: null,
    // proceso_id: '', // Deprecated. Use procesos_ids
    procesos_ids: [],
    area_compliance_id: '',
    subarea_compliance_id: '',
    obligacion_documento: '',
    obligacion_principal: '',
    obligacion_tipo: 'Legal',
    obligacion_estado: 'pendiente',
    obligacion_consecuencia: '',
    obligacion_documento_deroga: '',
    obligacion_frecuencia: null,
    obligacion_fecha_identificacion: null,
    controles_ids: [],
    evaluacion_actual: null
});

const searchProceso = ref('');
const selectedState = ref(null);
const availableStates = [
    'identificada', 'evaluada', 'en_tratamiento', 'controlada',
    'no_controlada', 'suspendida', 'inactiva'
];

const attemptStateChange = async () => {
    if (selectedState.value === form.obligacion_estado) return;

    // Confirmación previa
    const result = await Swal.fire({
        title: '¿Cambiar estado?',
        text: `¿Desea cambiar el estado de la obligación a ${selectedState.value.toUpperCase().replace('_', ' ')}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar'
    });

    if (!result.isConfirmed) {
        selectedState.value = form.obligacion_estado;
        return;
    }

    try {
        const response = await axios.post(`/api/obligaciones/${form.id}/cambiar-estado`, {
            nuevo_estado: selectedState.value
        });

        form.obligacion_estado = response.data.nuevo_estado;
        Toast.fire({ icon: 'success', title: 'Estado actualizado correctamente' });

    } catch (error) {
        console.error(error);
        selectedState.value = form.obligacion_estado; // Revertir
        Swal.fire({
            icon: 'error',
            title: 'No se pudo cambiar el estado',
            text: error.response?.data?.error || 'Error desconocido'
        });
    }
};

watch(() => form.obligacion_estado, (val) => {
    selectedState.value = val;
});

const getEstadoBadgeClass = (estado) => {
    const map = {
        identificada: 'badge-secondary',
        evaluada: 'badge-info',
        en_tratamiento: 'badge-primary',
        controlada: 'badge-success',
        no_controlada: 'badge-danger',
        suspendida: 'badge-warning',
        inactiva: 'badge-dark',
        pendiente: 'badge-secondary', // Legacy
        mitigada: 'badge-success', // Legacy
        vencida: 'badge-danger' // Legacy
    };
    return map[estado] || 'badge-light';
};

const getSemaforoClass = (v) => {
    if (!v) return 'badge-secondary';
    v = v.toString().toLowerCase();
    if (v.includes('bajo') || v.includes('leve')) return 'badge-success';
    if (v.includes('medio') || v.includes('moderado')) return 'badge-warning';
    if (v.includes('alto') || v.includes('crítico') || v.includes('grave')) return 'badge-danger';
    return 'badge-info';
};

const handleEvaluated = (data) => {
    form.evaluacion_actual = data.evaluacion;
    if (data.nuevo_estado) {
        form.obligacion_estado = data.nuevo_estado;
    }
};



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
        obligacion_documento: '',
        obligacion_principal: '',
        obligacion_tipo: 'legal',
        obligacion_estado: 'pendiente',
        obligacion_consecuencia: '',
        obligacion_documento_deroga: '',
        obligacion_frecuencia: null,
        obligacion_fecha_identificacion: null,
        controles_ids: [],
        evaluacion_actual: null
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
        obligacion_documento: data.obligacion_documento,
        obligacion_principal: data.obligacion_principal,
        obligacion_tipo: data.obligacion_tipo,
        obligacion_estado: data.obligacion_estado,
        obligacion_consecuencia: data.obligacion_consecuencia,
        obligacion_documento_deroga: data.obligacion_documento_deroga,
        obligacion_frecuencia: data.obligacion_frecuencia,
        obligacion_fecha_identificacion: data.obligacion_fecha_identificacion,
        evaluacion_actual: data.evaluacion_actual,
        controles_ids: data.controles ? data.controles.map(c => c.id) : [],
    });

    if (data.id) {
        fetchRiesgos(data.id);
        fetchAcciones(data.id);
    }
};

const fetchListas = async () => {
    await store.ensureListas();
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
    } catch (e) { }
};

watch(() => props.show, async (newVal) => {
    if (newVal) {
        resetForm();
        await fetchListas();
        if (props.obligacion) {
            await nextTick();
            populateForm(props.obligacion);
        }
        if (modalInstance.value) modalInstance.value.show();
    } else {
        if (modalInstance.value) modalInstance.value.hide();
    }
});

// Watch para cuando cambia el ID de la obligación mientras el modal está abierto
watch(() => props.obligacion?.id, async (newId, oldId) => {
    if (props.show && newId && newId !== oldId) {
        resetForm();
        await nextTick();
        if (props.obligacion) {
            populateForm(props.obligacion);
        }
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

const openAIAssistant = () => {
    const modalElement = document.getElementById('asistenteIAModal');
    if (modalElement) {
        const bsModal = new Modal(modalElement);
        bsModal.show();
    } else {
        console.error('Modal element not found');
        Swal.fire('Error', 'No se pudo abrir el asistente de IA.', 'error');
    }
};

const onIAObligacionSelected = (result) => {
    form.obligacion_principal = result;
    Toast.fire({
        icon: 'success',
        title: 'Obligación principal actualizada por Jaris.'
    });
};


onMounted(() => {
    modalInstance.value = new Modal(modalRef.value, { backdrop: 'static', keyboard: false });
});

onUnmounted(() => { if (modalInstance.value) modalInstance.value.dispose(); });



</script>

<style scoped>
/* Estilos del menú lateral */
.nav-pills .nav-link {
    color: #495057;
    cursor: pointer;
    margin-bottom: 0.5rem;
    border-radius: 0.25rem;
    transition: all 0.2s;
    text-align: left !important;
}

.nav-pills .nav-link:hover {
    background-color: #e9ecef;
}

.nav-pills .nav-link.active {
    background-color: #fff;
    border-left: 4px solid #dc3545;
    color: #dc3545 !important;
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.nav-pills .nav-link i {
    width: 20px;
    margin-right: 10px;
    text-align: center;
}

.nav-pills h6 {
    text-transform: uppercase;
    font-size: 0.7rem;
    margin-bottom: 0.5rem;
    letter-spacing: 0.05rem;
    text-align: left !important;
    color: #6c757d;
}

/* Removido modal-body-scrollable - ahora el scroll está en el contenedor interno */

.border-left-danger {
    border-left: 4px solid #dc3545;
}
</style>
