<template>
    <div class="card border-0 shadow-none">
        <div class="header-container mb-3">
            <div>
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-search-plus mr-2"></i>Revisión de Gabinete (Hallazgos)
                </h5>
                <small class="text-muted d-block mt-1">Revise y afine la redacción de los Hallazgos de No Conformidad y
                    Oportunidades de
                    Mejora.</small>
            </div>
            <div class="d-flex">
                <button class="btn btn-sm btn-primary ml-3 shadow-sm" @click="openNewHallazgoModal"
                    style="min-width: 140px;">
                    <i class="fas fa-plus-circle mr-1"></i> Nuevo Hallazgo
                </button>
                <button class="btn btn-sm btn-outline-secondary ml-2" @click="loadHallazgos" :disabled="loading"
                    style="min-width: 120px;">
                    <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }"></i> Refrescar
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div v-if="loading" class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                <p class="mt-2 text-muted">Cargando hallazgos...</p>
            </div>

            <div v-else-if="hallazgos.length === 0" class="text-center py-5 text-muted">
                <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                <p>No se encontraron hallazgos de No Conformidad ni Oportunidades de Mejora para revisar.</p>
            </div>

            <div v-else>
                <div class="accordion" id="accordionHallazgos">
                    <div v-for="(item, index) in hallazgos" :key="item.id" class="card border-0 mb-3 shadow-sm rounded">
                        <!-- HEADER DEL CARD -->
                        <div class="card-header bg-white collapsed cursor-pointer py-3"
                            :data-target="`#collapse${index}`" data-toggle="collapse">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center w-100">
                                    <div class="mr-3">
                                        <span class="badge badge-pill"
                                            :class="item.estado_cumplimiento === 'No Conforme' ? 'badge-danger' : 'badge-warning'">
                                            {{ item.estado_cumplimiento }}
                                        </span>
                                    </div>
                                    <div class="mr-3 font-weight-bold text-dark" style="font-size: 1.1em;">
                                        {{ item.requisito_referencia }}
                                        <span class="text-muted font-weight-normal mx-2">|</span>
                                        <span class="text-info"><i class="fas fa-project-diagram mr-1"></i>{{
                                            item.agenda?.proceso?.proceso_nombre || 'Proceso no definido' }}</span>
                                    </div>

                                    <!-- Clasificación Dropdown (Stop Propagation to prevent collapse toggle) -->
                                    <div class="ml-auto mr-4" @click.stop>
                                        <select v-model="item.hallazgo_clasificacion"
                                            @change="onClasificacionChange(item)"
                                            class="form-control form-control-sm border-primary text-primary font-weight-bold"
                                            style="width: 120px;">
                                            <option value="N/A">Clasif.</option>
                                            <option value="NCM">NCM</option>
                                            <option value="NCMe">NCMe</option>
                                            <option value="Odm">Odm</option>
                                            <option value="Obs">Obs</option>
                                        </select>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-down text-muted"></i>
                            </div>
                        </div>

                        <div :id="`collapse${index}`" class="collapse" data-parent="#accordionHallazgos">
                            <div class="card-body bg-light">
                                <div class="row">
                                    <!-- COLUMNA IZQUIERDA: HISTORIAL -->
                                    <div class="col-md-4 border-right">
                                        <h6 class="text-muted small text-uppercase font-weight-bold mb-3">Hallazgo
                                            Original (Historial)</h6>
                                        <div class="bg-white p-3 rounded shadow-sm mb-3">
                                            <small class="text-muted d-block mb-1 font-weight-bold">Descripción
                                                Detectada:</small>
                                            <div class="text-secondary font-italic small">
                                                {{ item.hallazgo_detectado }}
                                            </div>
                                        </div>
                                        <div class="bg-white p-3 rounded shadow-sm">
                                            <small class="text-muted d-block mb-1 font-weight-bold">Evidencia
                                                Registrada:</small>
                                            <div class="text-secondary font-italic small">
                                                {{ item.evidencia_registrada || 'Sin evidencia' }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- COLUMNA DERECHA: EDICIÓN (NUEVO DISEÑO) -->
                                    <div class="col-md-8">
                                        <h6 class="text-primary small text-uppercase font-weight-bold mb-3">
                                            <i class="fas fa-pen mr-1"></i>Redacción Final (Para Informe)
                                        </h6>

                                        <!-- 1. Resumen -->
                                        <div class="form-group">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="d-flex align-items-center">
                                                    <label
                                                        class="font-weight-bold small text-dark mb-0 mr-2">Resumen</label>
                                                    <button class="btn btn-xs btn-outline-info"
                                                        @click="generateSummary(item)"
                                                        :disabled="makingSummary[item.id]"
                                                        title="Generar resumen con IA basado en la descripción">
                                                        <i class="fas"
                                                            :class="makingSummary[item.id] ? 'fa-spinner fa-spin' : 'fa-magic'"></i>
                                                        IA
                                                    </button>
                                                </div>
                                                <small class="text-muted">{{ (item.hallazgo_resumen || '').length
                                                }}/255</small>
                                            </div>
                                            <textarea class="form-control" v-model="item.hallazgo_resumen" rows="2"
                                                maxlength="255"
                                                placeholder="Redacte un resumen breve y conciso..."></textarea>
                                        </div>

                                        <!-- 2. Descripción (Condición) -->
                                        <div class="form-group">
                                            <div class="d-flex justify-content-between">
                                                <label class="font-weight-bold small text-dark">Descripción
                                                    (Condición)</label>
                                                <small class="text-muted">{{ (item.hallazgo_redaccion || '').length
                                                }}/1000</small>
                                            </div>
                                            <textarea class="form-control" v-model="item.hallazgo_redaccion" rows="5"
                                                maxlength="1000"
                                                placeholder="Describa los hechos del hallazgo, el 'qué' y 'dónde'..."></textarea>
                                        </div>

                                        <div class="row">
                                            <!-- 3. Referencia (Criterio) -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="d-flex justify-content-between">
                                                        <label class="font-weight-bold small text-dark">Referencia
                                                            (Criterio)</label>
                                                        <small class="text-muted">{{ (item.criterio_redaccion ||
                                                            '').length }}/500</small>
                                                    </div>
                                                    <textarea class="form-control" v-model="item.criterio_redaccion"
                                                        rows="4" maxlength="500"
                                                        placeholder="Citar la referencia normativa..."></textarea>
                                                </div>
                                            </div>

                                            <!-- 4. Evidencias -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="d-flex justify-content-between">
                                                        <label
                                                            class="font-weight-bold small text-dark">Evidencias</label>
                                                        <small class="text-muted">{{ (item.evidencia_redaccion ||
                                                            '').length }}/500</small>
                                                    </div>
                                                    <textarea class="form-control" v-model="item.evidencia_redaccion"
                                                        rows="4" maxlength="500"
                                                        placeholder="Tipo de información que respalda el hallazgo..."></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-right mt-2 border-top pt-3">
                                            <button class="btn btn-primary shadow-sm px-4"
                                                @click="guardarRedaccion(item)" :disabled="item.guardando">
                                                <i class="fas mr-1"
                                                    :class="item.guardando ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                                                {{ item.guardando ? 'Guardando...' : 'Guardar Cambios' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- NEW HALLAZGO MODAL -->
        <div class="modal fade" id="newHallazgoModal" tabindex="-1" role="dialog" aria-hidden="true"
            data-backdrop="static" ref="newHallazgoModalRef">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-plus-circle mr-2"></i>Registrar Nuevo
                            Hallazgo (Gabinete)</h5>
                        <button type="button" class="close text-white" @click="closeModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-light">
                        <form @submit.prevent="saveNewHallazgo">
                            <div class="card shadow-sm border-0 mb-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold small text-uppercase">Proceso
                                                    (Agenda)</label>
                                                <select class="form-control" v-model="newHallazgo.agenda_id" required>
                                                    <option value="" disabled>Seleccione un proceso...</option>
                                                    <option v-for="proc in agendaProcesos" :key="proc.agenda_id"
                                                        :value="proc.agenda_id">
                                                        {{ proc.proceso_nombre }} ({{ proc.tipo }})
                                                    </option>
                                                </select>
                                                <small class="form-text text-muted">Seleccione el proceso auditado al
                                                    que pertenece el hallazgo.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-weight-bold small text-uppercase">Norma
                                                    Referencia</label>
                                                <select class="form-control" v-model="newHallazgo.norma_referencia"
                                                    required>
                                                    <option value="" disabled>Seleccione...</option>
                                                    <option v-for="norma in normasAuditables" :key="norma.id"
                                                        :value="norma.na_nombre">
                                                        {{ norma.na_nombre }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="font-weight-bold small text-uppercase">Numeral /
                                                    Requisito</label>
                                                <select class="form-control" v-model="newHallazgo.requisito_referencia"
                                                    required :disabled="!newHallazgo.norma_referencia">
                                                    <option value="" disabled>Seleccione...</option>
                                                    <option v-for="req in availableRequisitos" :key="req.id"
                                                        :value="req.nr_numeral">
                                                        {{ req.nr_numeral }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" v-if="requisitoDetalle">
                                        <div class="col-12">
                                            <div class="form-group alert alert-secondary p-2 mb-3">
                                                <label class="font-weight-bold small text-muted mb-1">Detalle del
                                                    Requisito
                                                    ({{ newHallazgo.requisito_referencia }})</label>
                                                <p class="mb-0 small" style="white-space: pre-wrap;">{{ requisitoDetalle
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold small text-uppercase">Tipo</label>
                                                <select class="form-control" v-model="newHallazgo.estado_cumplimiento"
                                                    required>
                                                    <option value="No Conforme">No Conforme</option>
                                                    <option value="Oportunidad de Mejora">Oportunidad de Mejora
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="font-weight-bold small text-uppercase">Clasificación
                                                    Inicial</label>
                                                <select v-model="newHallazgo.hallazgo_clasificacion"
                                                    class="form-control">
                                                    <option value="">Seleccione...</option>
                                                    <option value="NCM">NCM (No Conformidad Mayor)</option>
                                                    <option value="NCMe">NCMe (No Conformidad Menor)</option>
                                                    <option value="Odm">Odm (Oportunidad de Mejora)</option>
                                                    <option value="Obs">Obs (Observación)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold small text-uppercase">Descripción del
                                            Hallazgo</label>
                                        <textarea class="form-control" v-model="newHallazgo.hallazgo_detectado" rows="4"
                                            required placeholder="Describa el hallazgo detectado..."></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold small text-uppercase">Evidencia</label>
                                        <textarea class="form-control" v-model="newHallazgo.evidencia_registrada"
                                            rows="2" placeholder="Describa la evidencia..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right mt-3">
                                <button type="button" class="btn btn-secondary mr-2"
                                    @click="closeModal">Cancelar</button>
                                <button type="submit" class="btn btn-primary px-4" :disabled="savingHallazgo">
                                    <i class="fas" :class="savingHallazgo ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                                    Guardar Hallazgo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';

const props = defineProps({
    auditId: {
        type: Number,
        required: true
    }
});

const hallazgos = ref([]);
const agendaProcesos = ref([]);
const normasAuditables = ref([]);
const availableRequisitos = ref([]);
const requisitoDetalle = ref('');
const loading = ref(true);
const makingSummary = ref({});
const savingHallazgo = ref(false);

const newHallazgo = ref({
    agenda_id: '',
    norma_referencia: '',
    requisito_referencia: '',
    hallazgo_detectado: '',
    evidencia_registrada: '',
    estado_cumplimiento: 'No Conforme',
    hallazgo_clasificacion: ''
});

// ...

const loadNormas = async () => {
    try {
        const response = await axios.get(`/api/auditorias/${props.auditId}/normas`);
        normasAuditables.value = response.data;
    } catch (error) {
        console.error("Error loading norms", error);
    }
};



const newHallazgoModalRef = ref(null);
let newHallazgoModalInstance = null;

const loadHallazgos = async () => {
    loading.value = true;
    try {
        const response = await axios.get(`/api/auditoria/hallazgos/revision/${props.auditId}`);
        let data = response.data;

        // Ordenar: Primero por Numeral (requisito_referencia) y luego por Proceso Nombre
        data.sort((a, b) => {
            // Comparar Numerales (simple string compare for now, ideally semantic version compare)
            const numA = a.requisito_referencia || '';
            const numB = b.requisito_referencia || '';
            const numComparison = numA.localeCompare(numB, undefined, { numeric: true, sensitivity: 'base' });

            if (numComparison !== 0) return numComparison;

            // Si numerales iguales, comparar por proceso usuario
            const procA = a.agenda?.proceso?.proceso_nombre || '';
            const procB = b.agenda?.proceso?.proceso_nombre || '';
            return procA.localeCompare(procB);
        });

        hallazgos.value = data.map(h => {
            // Lógica para valor por defecto
            let defaultClasificacion = h.hallazgo_clasificacion || 'N/A';

            // Si es una Oportunidad de Mejora y no tiene clasificación, asignar Odm
            if (defaultClasificacion === 'N/A' || !defaultClasificacion) {
                if (h.estado_cumplimiento === 'OM' || h.estado_cumplimiento === 'Oportunidad de Mejora') {
                    defaultClasificacion = 'Odm';
                }
            }

            return {
                ...h,
                // Valores por defecto si no existen
                hallazgo_redaccion: h.hallazgo_redaccion || h.hallazgo_detectado,
                hallazgo_resumen: h.hallazgo_resumen || '',
                criterio_redaccion: h.criterio_redaccion || h.requisito_referencia,
                evidencia_redaccion: h.evidencia_redaccion || h.evidencia_registrada,
                hallazgo_clasificacion: defaultClasificacion,
                guardando: false,
                guardado: false
            };
        });
    } catch (error) {
        console.error('Error cargando hallazgos:', error);
        Swal.fire('Error', 'No se pudieron cargar los hallazgos para revisión', 'error');
    } finally {
        loading.value = false;
    }
};

const loadAgendaProcesos = async () => {
    try {
        const response = await axios.get(`/api/auditorias/${props.auditId}/agenda-procesos`);
        agendaProcesos.value = response.data;
    } catch (error) {
        console.error("Error loading agenda processes", error);
    }
};

const openNewHallazgoModal = () => {
    // Reset form
    newHallazgo.value = {
        agenda_id: '',
        norma_referencia: '',
        requisito_referencia: '',
        hallazgo_detectado: '',
        evidencia_registrada: '',
        estado_cumplimiento: 'No Conforme',
        hallazgo_clasificacion: ''
    };

    // Ensure Modal is initialized
    if (!newHallazgoModalInstance && newHallazgoModalRef.value) {
        newHallazgoModalInstance = new Modal(newHallazgoModalRef.value, {
            backdrop: 'static',
            keyboard: false
        });
    }

    if (newHallazgoModalInstance) {
        newHallazgoModalInstance.show();
    } else {
        console.error("Modal element reference is missing.");
    }

    if (agendaProcesos.value.length === 0) {
        loadAgendaProcesos();
    }
    if (normasAuditables.value.length === 0) {
        loadNormas();
    }
};

const closeModal = () => {
    newHallazgoModalInstance?.hide();
};

const saveNewHallazgo = async () => {
    savingHallazgo.value = true;
    try {
        await axios.post('/api/auditorias/hallazgos/gabinete', newHallazgo.value);

        closeModal();

        Swal.fire('Éxito', 'Hallazgo registrado correctamente.', 'success');
        loadHallazgos(); // Recargar lista
    } catch (error) {
        console.error("Error saving hallazgo", error);
        Swal.fire('Error', 'No se pudo registrar el hallazgo.', 'error');
    } finally {
        savingHallazgo.value = false;
    }
};

const generateSummary = async (item) => {
    if (!item.hallazgo_redaccion || item.hallazgo_redaccion.length < 5) {
        Swal.fire('Atención', 'Se requiere una descripción (condición) para generar el resumen.', 'warning');
        return;
    }

    makingSummary.value[item.id] = true;
    try {
        const url = `/api/auditoria/ejecucion/generate-summary`;
        const response = await axios.post(url, { text: item.hallazgo_redaccion });
        item.hallazgo_resumen = response.data.summary;
        // Opcional: Toast de éxito
    } catch (error) {
        console.error('Error generando resumen:', error);
        Swal.fire('Error', 'No se pudo generar el resumen con IA.', 'error');
    } finally {
        makingSummary.value[item.id] = false;
    }
};

const onClasificacionChange = (item) => {
    if (item.hallazgo_clasificacion === 'Odm') {
        item.estado_cumplimiento = 'Oportunidad de Mejora';
    }
};

const guardarRedaccion = async (item) => {
    if (!item.hallazgo_redaccion || item.hallazgo_redaccion.trim() === '') {
        Swal.fire('Atención', 'La redacción no puede estar vacía', 'warning');
        return;
    }

    item.guardando = true;
    item.guardado = false;
    try {
        await axios.put(`/api/auditoria/hallazgos/${item.id}/redaccion`, {
            hallazgo_redaccion: item.hallazgo_redaccion,
            hallazgo_resumen: item.hallazgo_resumen,
            criterio_redaccion: item.criterio_redaccion,
            evidencia_redaccion: item.evidencia_redaccion,
            hallazgo_clasificacion: item.hallazgo_clasificacion,
            estado_cumplimiento: item.estado_cumplimiento // Enviar estado actualizado
        });
        item.guardado = true;
        // Feedback visual temporal
        setTimeout(() => { item.guardado = false; }, 2000);

        // Opcional: Toast notificación
        // toast.fire(...)
    } catch (error) {
        console.error('Error guardando redacción:', error);
        Swal.fire('Error', 'No se pudo guardar la redacción', 'error');
    } finally {
        item.guardando = false;
    }
};

onMounted(() => {
    if (props.auditId) {
        loadHallazgos();
        loadAgendaProcesos();
    }
});

watch(() => props.auditId, (newVal) => {
    if (newVal) {
        loadHallazgos();
        loadAgendaProcesos();
    }
});

watch(() => newHallazgo.value.norma_referencia, (newVal) => {
    if (!newVal) {
        availableRequisitos.value = [];
        return;
    }
    const norma = normasAuditables.value.find(n => n.na_nombre === newVal);
    if (norma && norma.requisitos) {
        availableRequisitos.value = norma.requisitos;
    } else {
        availableRequisitos.value = [];
    }
    // Si cambia la norma, limpiar requisito seleccionado si no existe en la nueva lista
    // (O simplemente limpiar siempre para obligar a reseleccionar)
    newHallazgo.value.requisito_referencia = '';
    requisitoDetalle.value = '';
});

watch(() => newHallazgo.value.requisito_referencia, (newVal) => {
    if (!newVal) {
        requisitoDetalle.value = '';
        return;
    }
    const req = availableRequisitos.value.find(r => r.nr_numeral === newVal);
    if (req) {
        requisitoDetalle.value = req.nr_detalle || '';
    } else {
        requisitoDetalle.value = '';
    }
});

</script>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}

.accordion .card-header:hover {
    background-color: #e9ecef !important;
}

.header-container {
    padding: 0.75rem 1rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #17a2b8;
    /* Cyan for Gabinete/Review to distinguish from Execution (Orange) */
    display: flex;
    align-items: center;
    justify-content: space-between;
}
</style>
