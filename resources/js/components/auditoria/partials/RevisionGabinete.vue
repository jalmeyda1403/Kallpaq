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
            <button class="btn btn-sm btn-outline-secondary ml-3" @click="loadHallazgos" :disabled="loading"
                style="min-width: 120px;">
                <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }"></i> Refrescar
            </button>
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
                                        {{ item.requisito_referencia }} - {{ item.norma_referencia }}
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
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
    auditId: {
        type: Number,
        required: true
    }
});

const hallazgos = ref([]);
const loading = ref(true);
const makingSummary = ref({});

const loadHallazgos = async () => {
    loading.value = true;
    try {
        const response = await axios.get(`/api/auditoria/hallazgos/revision/${props.auditId}`);
        hallazgos.value = response.data.map(h => {
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
    }
});

watch(() => props.auditId, (newVal) => {
    if (newVal) loadHallazgos();
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
