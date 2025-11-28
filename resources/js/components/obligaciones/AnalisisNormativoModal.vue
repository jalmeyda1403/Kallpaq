<template>
    <Teleport to="body">
        <div ref="modalRef" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-robot mr-2"></i>Análisis Normativo (IA)
                        </h5>
                        <button type="button" class="close text-white" @click="close" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="submitForm">
                        <div class="modal-body">
                            <div v-if="norma">
                                <!-- Información de la Norma -->
                                <div class="alert alert-light border">
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-file-alt fa-2x text-info mr-3"></i>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 font-weight-bold ">{{ norma.titulo }}</h6>
                                            <p class="mb-1 text-muted small">
                                                <strong>Número:</strong> {{ norma.numero_norma || 'Sin número' }} |
                                                <strong>Emisor:</strong> {{ norma.organismo_emisor }}
                                                <span v-if="norma.url_fuente" class="ml-2">
                                                    | <a :href="norma.url_fuente" target="_blank" class="text-info">
                                                        <i class="fas fa-external-link-alt mr-1"></i>Ver Fuente
                                                    </a>
                                                </span>
                                            </p>
                                            <p class="mb-0"><strong>Resumen IA:</strong> {{ norma.resumen_ia }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="mode === 'approve'">


                                    <!-- 1. Código del Documento y Estado -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold custom-label">
                                                    Código del Documento <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" v-model="form.cod_documento" class="form-control"
                                                    placeholder="Ej: LEY-001-2024" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold custom-label">
                                                    Estado del Documento <span class="text-danger">*</span>
                                                </label>
                                                <select v-model="form.estado_documento" class="form-control" required>
                                                    <option value="">Seleccione...</option>
                                                    <option value="vigente">Vigente</option>
                                                    <option value="derogado">Derogado</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 2. Análisis de Aplicabilidad -->
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">
                                            <i class="fas fa-user-check mr-1"></i>Análisis de Aplicabilidad
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea v-model="form.analisis_humano" class="form-control" rows="5" required
                                            placeholder="Justifique por qué esta norma aplica o no a la organización..."></textarea>
                                    </div>

                                    <!-- 3. Riesgo y Tipo de Obligación -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold custom-label">
                                                    Riesgo Inherente
                                                </label>
                                                <select v-model="form.nivel_riesgo_inherente" class="form-control">
                                                    <option value="Bajo">Bajo</option>
                                                    <option value="Medio">Medio</option>
                                                    <option value="Alto">Alto</option>
                                                    <option value="Muy Alto">Muy Alto</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold custom-label">
                                                    Tipo Obligación
                                                </label>
                                                <select v-model="form.tipo_obligacion" class="form-control">
                                                    <option value="Legal">Legal</option>
                                                    <option value="Contractual">Contractual</option>
                                                    <option value="Voluntaria">Voluntaria</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 4. Obligación Principal -->
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">
                                            <i class="fas fa-clipboard-list mr-1"></i>Obligación Principal
                                        </label>
                                        <textarea v-model="form.obligacion_principal" class="form-control" rows="5"
                                            placeholder="Describa la obligación principal derivada de esta norma"></textarea>
                                    </div>

                                    <!-- 5. Proceso Afectado con ModalHijo -->
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">
                                            Proceso Impactado
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="text" v-model="processName" class="form-control" readonly
                                                placeholder="Seleccionar proceso..." :required="!form.proceso_id">
                                            <div class="input-group-append">
                                                <button class="btn btn-dark" type="button" @click="openProcessModal">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                                <button class="btn btn-danger" type="button" v-if="form.proceso_id"
                                                    @click="clearProcess">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 6. Área de Compliance y Subárea -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold custom-label">
                                                    Área de Compliance
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select v-model="form.area_compliance_id" class="form-control" required
                                                    @change="onAreaChange">
                                                    <option value="">Seleccione...</option>
                                                    <option v-for="area in areasCompliance" :key="area.id"
                                                        :value="area.id">
                                                        {{ area.area_compliance_nombre }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold custom-label">
                                                    Subárea de Compliance
                                                </label>
                                                <select v-model="form.subarea_compliance_id" class="form-control"
                                                    :disabled="!form.area_compliance_id">
                                                    <option value="">Seleccione...</option>
                                                    <option v-for="subarea in subareasCompliance" :key="subarea.id"
                                                        :value="subarea.id">
                                                        {{ subarea.subarea_compliance_nombre }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modo Rechazo -->
                                <div v-else>
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">
                                            <i class="fas fa-user-check mr-1"></i>Justificación del Rechazo
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea v-model="form.analisis_humano" class="form-control" rows="3" required
                                            placeholder="Explique por qué esta norma NO aplica a la organización..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="close">
                                <i class="fa fa-times mr-1"></i> Cancelar
                            </button>
                            <button type="submit" :class="mode === 'approve' ? 'btn btn-danger' : 'btn btn-danger'">
                                <i class="fa fa-check mr-1"></i>
                                {{ mode === 'approve' ? 'Crear Obligación' : 'Confirmar Rechazo' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ModalHijo para seleccionar procesos -->
        <ModalHijo ref="processModal" :fetch-url="processRoute" target-id="id" target-desc="proceso_nombre"
            @update-target="handleProcessSelected" />
    </Teleport>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue';
import { Modal } from 'bootstrap';
import ModalHijo from '@/components/generales/ModalHijo.vue';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
    norma: Object,
    mode: String // 'approve' or 'reject'
});

const emit = defineEmits(['close', 'confirm']);

const modalRef = ref(null);
const modalInstance = ref(null);
const processName = ref('');
const processModal = ref(null);

const processRoute = route('procesos.buscar');

const form = ref({
    analisis_humano: '',
    cod_documento: '',
    estado_documento: 'vigente',
    obligacion_principal: '',
    nivel_riesgo_inherente: 'Medio',
    tipo_obligacion: 'Legal',
    proceso_id: null,
    area_compliance_id: null,
    subarea_compliance_id: null
});

// Data lists
const areasCompliance = ref([]);
const subareasCompliance = ref([]);

// Fetch Áreas de Compliance
const fetchAreas = async () => {
    try {
        const response = await axios.get('/api/areas-compliance');
        areasCompliance.value = response.data;
    } catch (error) {
        console.error('Error fetching areas:', error);
    }
};

// Fetch Subáreas de Compliance (filtered by area)
const fetchSubareas = async (areaId) => {
    if (!areaId) {
        subareasCompliance.value = [];
        return;
    }
    try {
        const response = await axios.get(`/api/areas-compliance/${areaId}/subareas`);
        subareasCompliance.value = response.data;
    } catch (error) {
        console.error('Error fetching subareas:', error);
    }
};

// Handle area change
const onAreaChange = () => {
    form.value.subarea_compliance_id = null;
    fetchSubareas(form.value.area_compliance_id);
};

// Funciones para manejar el proceso
const openProcessModal = () => {
    processModal.value.open();
};

const handleProcessSelected = (data) => {
    form.value.proceso_id = data.idValue;
    processName.value = data.descValue;
};

const clearProcess = () => {
    form.value.proceso_id = null;
    processName.value = '';
};

watch(() => props.show, async (newVal) => {
    if (newVal && props.norma) {
        form.value = {
            analisis_humano: '',
            cod_documento: props.norma.numero_norma || '',
            estado_documento: 'vigente',
            obligacion_principal: props.norma.resumen_ia,
            nivel_riesgo_inherente: 'Medio',
            tipo_obligacion: 'Legal',
            proceso_id: null,
            area_compliance_id: null,
            subarea_compliance_id: null
        };
        processName.value = '';

        await nextTick();
        if (modalRef.value) {
            if (!modalInstance.value) {
                modalInstance.value = new Modal(modalRef.value, {
                    backdrop: 'static',
                    keyboard: false
                });
            }
            modalInstance.value.show();
        }
    } else if (!newVal) {
        if (modalInstance.value) {
            modalInstance.value.hide();
        }
    }
});

const close = () => {
    if (document.activeElement instanceof HTMLElement) {
        document.activeElement.blur();
    }
    emit('close');
};

import Swal from 'sweetalert2';

// ... (existing imports)

// ...

const validateForm = () => {
    if (!form.value.analisis_humano) {
        Swal.fire('Error', 'El análisis de aplicabilidad es obligatorio.', 'warning');
        return false;
    }

    if (props.mode === 'approve') {
        if (!form.value.cod_documento) {
            Swal.fire('Error', 'El código del documento es obligatorio.', 'warning');
            return false;
        }
        if (!form.value.estado_documento) {
            Swal.fire('Error', 'El estado del documento es obligatorio.', 'warning');
            return false;
        }
        if (!form.value.proceso_id) {
            Swal.fire('Error', 'Debe seleccionar un proceso afectado.', 'warning');
            return false;
        }
        if (!form.value.area_compliance_id) {
            Swal.fire('Error', 'Debe seleccionar un área de compliance.', 'warning');
            return false;
        }
    }
    return true;
};

const submitForm = async () => {
    if (!validateForm()) return;

    try {
        console.log('Submitting form:', form.value);
        emit('confirm', { id: props.norma.id, ...form.value });
        close();
    } catch (error) {
        console.error('Error submitting form:', error);
        Swal.fire('Error', 'Error al procesar: ' + (error.response?.data?.message || error.message), 'error');
    }
};

onMounted(() => {
    fetchAreas();

    // Listener para restaurar el scroll cuando ModalHijo se cierra
    const handleModalHidden = (e) => {
        // Verificar si el modal que se está ocultando es el ModalHijo
        // Usamos el evento 'hidden.bs.modal' que es disparado por Bootstrap
        if (e.target !== modalRef.value && e.target.classList.contains('modal')) {
            // Si el body no tiene la clase 'modal-open' pero nuestro modal padre está abierto,
            // restauramos la clase y el padding adecuado para el scroll
            if (!document.body.classList.contains('modal-open') &&
                modalInstance.value && modalInstance.value._isShown) {
                document.body.classList.add('modal-open');

                // También restauramos el padding derecho si fue modificado por Bootstrap
                const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;
                if (scrollBarWidth > 0) {
                    document.body.style.paddingRight = scrollBarWidth + 'px';
                }
            }
        }
    };

    // Añadir listener para el evento de ocultar modal
    document.addEventListener('hidden.bs.modal', handleModalHidden);

    if (modalRef.value) {
        modalRef.value.addEventListener('hidden.bs.modal', close);
    }

    // Guardar la función para poder removerla en onUnmounted
    modalRef.value._handleModalHidden = handleModalHidden;
});

onUnmounted(() => {
    modalInstance.value?.dispose();

    // Remover el listener global
    if (modalRef.value && modalRef.value._handleModalHidden) {
        document.removeEventListener('hidden.bs.modal', modalRef.value._handleModalHidden);
    }
});
</script>

<style scoped>
.custom-label {
    font-size: 0.9em !important;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif !important;
    font-weight: 600 !important;
    color: #070707 !important;
    letter-spacing: 0.2px !important;
}
</style>
