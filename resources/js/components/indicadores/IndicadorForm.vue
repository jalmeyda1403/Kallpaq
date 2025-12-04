<template>
    <Teleport to="body">
        <div ref="modalRef" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="indicadorModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="indicadorModalLabel">
                            {{ isEdit ? 'Editar Indicador' : 'Nuevo Indicador' }}</h5>
                        <button type="button" class="close text-white" @click="close" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="save">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Proceso <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" v-model="form.proceso_nombre"
                                                readonly placeholder="Seleccione un proceso"
                                                :required="!form.proceso_id">
                                            <div class="input-group-append">
                                                <button class="btn btn-dark" type="button" @click="openProcesoModal">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                <button class="btn btn-danger" type="button" v-if="form.proceso_id"
                                                    @click="clearProceso">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Nombre del Indicador <span
                                                class="text-danger">*</span></label>
                                        <input type="text" v-model="form.indicador_nombre" class="form-control"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Indicador SIG</label>
                                        <MultiSelect v-model="form.indicador_sig" :options="sigOptions"
                                            placeholder="Seleccione sistemas" display="chip" class="w-100"
                                            appendTo="body" panelClass="custom-dropdown-panel" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="font-weight-bold custom-label">Descripción <span
                                            class="text-danger">*</span></label>
                                    <small class="form-text text-muted">
                                        {{ form.indicador_descripcion ? form.indicador_descripcion.length : 0 }}/300
                                    </small>
                                </div>
                                <textarea v-model="form.indicador_descripcion" class="form-control" rows="3" required
                                    @input="updateCharCount" maxlength="300"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Frecuencia <span
                                                class="text-danger">*</span></label>
                                        <select v-model="form.indicador_frecuencia" class="form-control" required>
                                            <option value="" disabled>Selecciona...</option>
                                            <option value="mensual">Mensual</option>
                                            <option value="trimestral">Trimestral</option>
                                            <option value="semestral">Semestral</option>
                                            <option value="anual">Anual</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Meta <span
                                                class="text-danger">*</span></label>
                                        <input type="text" v-model="form.indicador_meta" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Tipo Indicador <span
                                                class="text-danger">*</span></label>
                                        <select v-model="form.indicador_tipo_indicador" class="form-control" required>
                                            <option value="" disabled>Selecciona...</option>
                                            <option value="producto">Producto</option>
                                            <option value="servicio">Servicio</option>
                                            <option value="resultado">Resultado</option>
                                            <option value="calidad">Calidad</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Tipo Agregación<span
                                                class="text-danger">*</span></label>
                                        <select v-model="form.indicador_tipo_agregacion" class="form-control" required>
                                            <option value="" disabled>Selecciona...</option>
                                            <option value="acumulada">Acumulada</option>
                                            <option value="no acumulada">No acumulada</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Parámetro Medida<span
                                                class="text-danger">*</span></label>
                                        <select v-model="form.indicador_parametro_medida" class="form-control" required>
                                            <option value="" disabled>Selecciona...</option>
                                            <option value="ratio">Ratio</option>
                                            <option value="porcentaje">Porcentaje</option>
                                            <option value="numero">Número</option>
                                            <option value="indice">Índice</option>
                                            <option value="tasa">Tasa</option>
                                            <option value="promedio">Promedio</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Sentido<span
                                                class="text-danger">*</span></label>
                                        <select v-model="form.indicador_sentido" class="form-control" required>
                                            <option value="" disabled>Selecciona...</option>
                                            <option value="ascendente">Ascendente</option>
                                            <option value="lineal">Lineal</option>
                                            <option value="descendente">Descendente</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold custom-label">Fórmula <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" v-model="form.indicador_formula" class="form-control"
                                        placeholder="Ej: (V1/V2)*100" readonly required>
                                    <div class="input-group-append">
                                        <button class="btn btn-warning" type="button" @click="openFormulaModal">
                                            <i class="fas fa-calculator"></i> Formular
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6" v-if="form.indicador_var1">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Variable 1 (V1)</label>
                                        <input type="text" v-model="form.indicador_var1" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6" v-if="form.indicador_var2">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Variable 2 (V2)</label>
                                        <input type="text" v-model="form.indicador_var2" class="form-control" readonly>
                                    </div>
                                </div>
                                <!-- Show more if they exist, though modal handles up to 6 -->
                                <div class="col-md-6" v-if="form.indicador_var3">
                                    <div class="form-group">
                                        <label class="font-weight-bold custom-label">Variable 3 (V3)</label>
                                        <input type="text" v-model="form.indicador_var3" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer d-flex justify-content-between">
                            <div></div> <!-- Empty div for spacing -->
                            <div>
                                <button type="button" class="btn btn-secondary" @click="close">
                                    <i class="fas fa-times mr-1"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-danger ml-2" :disabled="!isValid">
                                    <i class="fas fa-save mr-1"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modales Hijos -->
        <ModalHijo ref="modalProceso" :fetch-url="processRoute" targetId="proceso_id" targetDesc="proceso_nombre"
            @update-target="onProcesoSelected" />

        <FormulaModal v-if="showFormulaModal" :initial-formula="form.indicador_formula"
            :initial-variables="getVariablesObject()" @close="showFormulaModal = false"
            @update-formula="onFormulaUpdated" />
    </Teleport>
</template>

<script setup>
import { ref, onMounted, computed, watch, nextTick, onUnmounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import ModalHijo from '@/components/generales/ModalHijo.vue';
import FormulaModal from './FormulaModal.vue';
import MultiSelect from 'primevue/multiselect';
import { Modal } from 'bootstrap';

const props = defineProps({
    visible: Boolean,
    indicador: Object,
    procesoId: [Number, String] // Optional initial process ID
});

const emit = defineEmits(['close', 'saved']);

const modalProceso = ref(null);
const showFormulaModal = ref(false);
const processRoute = '/buscarProcesos'; // Adjust route as needed based on web.php
const modalRef = ref(null);
const modalInstance = ref(null);

const isEdit = computed(() => !!props.indicador);

// Options for dropdowns - these are now defined in the template as options
const sigOptions = ['SGAS', 'SGCM', 'SGC', 'SGSI', 'SGCO'];

const form = ref({
    proceso_id: '',
    proceso_nombre: '',
    indicador_nombre: '',
    indicador_descripcion: '',
    indicador_frecuencia: '',
    indicador_meta: '',
    indicador_tipo_indicador: '',
    indicador_sig: [], // Array for multiselect
    indicador_tipo_agregacion: '',
    indicador_parametro_medida: '',
    indicador_sentido: '',
    indicador_formula: '',
    indicador_var1: '',
    indicador_var2: '',
    indicador_var3: '',
    indicador_var4: '',
    indicador_var5: '',
    indicador_var6: '',
});

const resetForm = () => {
    form.value = {
        proceso_id: props.procesoId || '',
        proceso_nombre: '', // Would need to fetch name if ID provided, or leave empty
        indicador_nombre: '',
        indicador_descripcion: '',
        indicador_frecuencia: '',
        indicador_meta: '',
        indicador_tipo_indicador: '',
        indicador_sig: [],
        indicador_tipo_agregacion: '',
        indicador_parametro_medida: '',
        indicador_sentido: '',
        indicador_formula: '',
        indicador_var1: '',
        indicador_var2: '',
        indicador_var3: '',
        indicador_var4: '',
        indicador_var5: '',
        indicador_var6: '',
    };
};

watch(() => props.indicador, (newVal) => {
    if (newVal) {
        // Explicitly map fields to ensure reactivity and handle potential missing values
        form.value.id = newVal.id;
        form.value.proceso_id = newVal.proceso_id;
        form.value.proceso_nombre = newVal.proceso?.proceso_nombre || '';
        form.value.indicador_nombre = newVal.indicador_nombre;
        form.value.indicador_descripcion = newVal.indicador_descripcion;
        form.value.indicador_frecuencia = newVal.indicador_frecuencia || '';
        form.value.indicador_meta = newVal.indicador_meta;
        form.value.indicador_tipo_indicador = newVal.indicador_tipo_indicador || '';
        form.value.indicador_tipo_agregacion = newVal.indicador_tipo_agregacion || '';
        form.value.indicador_parametro_medida = newVal.indicador_parametro_medida || '';
        form.value.indicador_sentido = newVal.indicador_sentido || '';
        form.value.indicador_formula = newVal.indicador_formula;
        form.value.indicador_var1 = newVal.indicador_var1;
        form.value.indicador_var2 = newVal.indicador_var2;
        form.value.indicador_var3 = newVal.indicador_var3;
        form.value.indicador_var4 = newVal.indicador_var4;
        form.value.indicador_var5 = newVal.indicador_var5;
        form.value.indicador_var6 = newVal.indicador_var6;

        // Handle JSON parsing for multi-select
        if (typeof newVal.indicador_sig === 'string') {
            try {
                form.value.indicador_sig = JSON.parse(newVal.indicador_sig) || [];
            } catch (e) {
                form.value.indicador_sig = [];
            }
        } else if (Array.isArray(newVal.indicador_sig)) {
            form.value.indicador_sig = newVal.indicador_sig;
        } else {
            form.value.indicador_sig = [];
        }
    } else {
        resetForm();
    }
}, { immediate: true });

watch(() => props.visible, async (newVal) => {
    if (newVal) {
        // When visible becomes true, we wait for the next tick to ensure DOM is updated
        await nextTick();
        if (modalRef.value) {
            // Create new modal instance if one doesn't already exist
            if (!modalInstance.value) {
                modalInstance.value = new Modal(modalRef.value, {
                    backdrop: 'static', // Prevent closing by clicking outside
                    keyboard: false,    // Prevent closing by pressing ESC
                    focus: false        // Disable focus enforcement to allow nested modals
                });
            }
            modalInstance.value.show();
        }
    } else {
        // When visible becomes false, hide the modal if it's open
        if (modalInstance.value) {
            modalInstance.value.hide();
        }
    }
}, { immediate: true });

// Handle the modal hidden event to sync the show prop
onMounted(() => {
    if (modalRef.value) {
        modalRef.value.addEventListener('hidden.bs.modal', () => {
            emit('close');
        });
    }
});

// Remove the modal instance when component is unmounted to prevent memory leaks
onUnmounted(() => {
    if (modalInstance.value) {
        modalInstance.value.dispose();
        modalInstance.value = null;
    }
});

const openProcesoModal = () => {
    modalProceso.value.open();
};

const onProcesoSelected = (payload) => {
    form.value.proceso_id = payload.idValue;
    form.value.proceso_nombre = payload.descValue;
};

const clearProceso = () => {
    form.value.proceso_id = null;
    form.value.proceso_nombre = '';
};

const close = () => {
    // Remove focus from the button to prevent ARIA warnings when modal hides
    if (document.activeElement instanceof HTMLElement) {
        document.activeElement.blur();
    }

    if (modalInstance.value) {
        modalInstance.value.hide();
    } else {
        emit('close');
    }
};

const updateCharCount = () => {
    // This function is called on input to update the character count
    // Vue automatically handles the reactivity so we just need to ensure the input is bound correctly
};

const openFormulaModal = () => {
    showFormulaModal.value = true;
};

const getVariablesObject = () => {
    return {
        indicador_var1: form.value.indicador_var1,
        indicador_var2: form.value.indicador_var2,
        indicador_var3: form.value.indicador_var3,
        indicador_var4: form.value.indicador_var4,
        indicador_var5: form.value.indicador_var5,
        indicador_var6: form.value.indicador_var6,
    };
};

const onFormulaUpdated = (payload) => {
    form.value.indicador_formula = payload.formula;
    form.value.indicador_var1 = payload.variables.indicador_var1;
    form.value.indicador_var2 = payload.variables.indicador_var2;
    form.value.indicador_var3 = payload.variables.indicador_var3;
    form.value.indicador_var4 = payload.variables.indicador_var4;
    form.value.indicador_var5 = payload.variables.indicador_var5;
    form.value.indicador_var6 = payload.variables.indicador_var6;
};

const save = async () => {
    try {
        const payload = { ...form.value };
        // Convert array to JSON string for storage if backend expects string, or keep array if casted
        // Assuming backend handles JSON casting or expects string:
        // payload.indicador_sig = JSON.stringify(payload.indicador_sig);

        if (isEdit.value) {
            await axios.put(`/api/indicadores-gestion/${props.indicador.id}`, payload);
        } else {
            await axios.post('/api/indicadores-gestion', payload);
        }
        Swal.fire('Éxito', 'Indicador guardado correctamente', 'success');
        emit('saved');
        close();
    } catch (error) {
        console.error('Error guardando indicador:', error);
        Swal.fire('Error', 'No se pudo guardar el indicador', 'error');
    }
};

onMounted(() => {
    // If creating new and processId prop is passed, might want to fetch name
    if (!props.indicador && props.procesoId) {
        // logic to fetch process name if needed, or just rely on user selecting it
    }
});

const isValid = computed(() => {
    return (
        form.value.proceso_id &&
        form.value.indicador_nombre &&
        form.value.indicador_descripcion &&
        form.value.indicador_frecuencia &&
        form.value.indicador_meta &&
        form.value.indicador_tipo_indicador &&
        form.value.indicador_tipo_agregacion &&
        form.value.indicador_parametro_medida &&
        form.value.indicador_sentido &&
        form.value.indicador_formula
    );
});
</script>

<style scoped>
.custom-label {
    font-size: 0.9em;
    font-weight: 600;
    color: #495057;
    letter-spacing: 0.2px;
}

/* Ensure PrimeVue components take full width in form groups */
:deep(.p-multiselect) {
    width: 100%;
    border-radius: 0.25rem;
    border: 1px solid #ced4da;
    box-shadow: none;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

/* Focus state matching Bootstrap */
:deep(.p-multiselect:not(.p-disabled).p-focus) {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Adjust padding inside the components */
:deep(.p-multiselect .p-multiselect-label) {
    padding: 0.375rem 0.75rem;
    font-size: 1rem !important;
    line-height: 1.5;
    color: #000000 !important;
    font-weight: 500;
}

/* Placeholder specific style */
:deep(.p-multiselect .p-multiselect-label.p-placeholder) {
    color: #6c757d !important;
    font-size: 0.875rem !important;
}

/* Adjust chips in MultiSelect */
:deep(.p-multiselect.p-multiselect-chip .p-multiselect-token) {
    padding: 0.1rem 0.5rem;
    margin-right: 0.25rem;
    background: #000000 !important;
    color: #ffffff !important;
    border-radius: 0.2rem;
    font-size: 0.875rem;
}

/* Hover state */
:deep(.p-multiselect:not(.p-disabled):hover) {
    border-color: #ced4da;
}
</style>

<style>
/* Global styles for the appended panels */
.custom-dropdown-panel .p-multiselect-item {
    font-size: 0.875rem !important;
}
</style>
