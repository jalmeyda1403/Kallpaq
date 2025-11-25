<template>
    <Teleport to="body">
        <div
            ref="modalRef"
            class="modal fade"
            tabindex="-1"
            role="dialog"
            aria-labelledby="tratamientoModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title" id="tratamientoModalLabel">Tratamiento de Salida No Conforme</h5>
                        <button type="button" class="close text-white" @click="close" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="submitForm">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Tratamiento</label>
                                <select v-model="form.snc_tratamiento" class="form-control">
                                    <option value="">Selecciona un tipo de tratamiento...</option>
                                    <option value="corrección">Corrección</option>
                                    <option value="reproceso">Reproceso</option>
                                    <option value="reclasificación">Reclasificación</option>
                                    <option value="rechazo">Rechazo</option>
                                    <option value="concesión">Concesión</option>
                                    <option value="pendiente">Pendiente</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Descripción del Tratamiento</label>
                                <textarea v-model="form.snc_descripcion_tratamiento" class="form-control" rows="3" placeholder="Ingrese la descripción del tratamiento aplicado..."></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Fecha de Tratamiento</label>
                                        <input type="date" v-model="form.snc_fecha_tratamiento" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Costo Estimado</label>
                                        <input type="number" v-model="form.snc_costo_estimado" class="form-control" min="0" step="0.01" placeholder="Ingrese el costo estimado...">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Requiere Acción Correctiva</label>
                                        <select v-model="form.snc_requiere_accion_correctiva" class="form-control">
                                            <option :value="null">Seleccionar...</option>
                                            <option :value="true">Sí</option>
                                            <option :value="false">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Fecha de Cierre</label>
                                        <input type="date" v-model="form.snc_fecha_cierre" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Observaciones</label>
                                <textarea v-model="form.snc_observaciones" class="form-control" rows="3" placeholder="Observaciones generales..."></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label class="font-weight-bold">Evidencia</label>
                                <input type="file" @change="onFileChange" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                <small v-if="form.snc_evidencia" class="form-text text-muted">Archivo actual: {{ form.snc_evidencia }}</small>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-between">
                            <div></div> <!-- Empty div for spacing -->
                            <div>
                                <button type="button" class="btn btn-secondary" @click="close">
                                    <i class="fas fa-times mr-1"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-warning ml-2" :disabled="!isValid">
                                    <i class="fas fa-save mr-1"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, computed, nextTick } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import { Modal } from 'bootstrap';

const props = defineProps({
    show: Boolean,
    snc: Object, // objeto de salida no conforme
});

const emit = defineEmits(['update:show', 'saved']);

const form = ref({
    snc_tratamiento: '',
    snc_descripcion_tratamiento: '',
    snc_fecha_tratamiento: null,
    snc_costo_estimado: null,
    snc_requiere_accion_correctiva: null,
    snc_fecha_cierre: null,
    snc_observaciones: '',
    snc_evidencia: null
});

const modalRef = ref(null);
const modalInstance = ref(null);

onMounted(() => {
    // Cargar datos iniciales si es edición
});

watch(() => props.snc, (newVal) => {
    if (newVal) {
        // Cargar datos del tratamiento desde la SNC
        form.value = {
            snc_tratamiento: newVal.snc_tratamiento || '',
            snc_descripcion_tratamiento: newVal.snc_descripcion_tratamiento || '',
            snc_fecha_tratamiento: newVal.snc_fecha_tratamiento || null,
            snc_costo_estimado: newVal.snc_costo_estimado || null,
            snc_requiere_accion_correctiva: newVal.snc_requiere_accion_correctiva,
            snc_fecha_cierre: newVal.snc_fecha_cierre || null,
            snc_observaciones: newVal.snc_observaciones || '',
            snc_evidencia: newVal.snc_evidencia || null
        };
    }
});

watch(() => props.show, async (newVal) => {
    if (newVal) {
        // When show becomes true, we wait for the next tick to ensure DOM is updated
        await nextTick();
        if (modalRef.value) {
            // Create new modal instance if one doesn't already exist
            if (!modalInstance.value) {
                modalInstance.value = new Modal(modalRef.value, {
                    backdrop: 'static', // Prevent closing by clicking outside
                    keyboard: false     // Prevent closing by pressing ESC
                });
            }
            modalInstance.value.show();
        }
    } else {
        // When show becomes false, hide the modal if it's open
        if (modalInstance.value) {
            modalInstance.value.hide();
        }

        // reset form when modal is closed
        form.value = {
            snc_tratamiento: '',
            snc_descripcion_tratamiento: '',
            snc_fecha_tratamiento: null,
            snc_costo_estimado: null,
            snc_requiere_accion_correctiva: null,
            snc_fecha_cierre: null,
            snc_observaciones: '',
            snc_evidencia: null
        };
    }
}, { immediate: true });

// Handle the modal hidden event to sync the show prop
onMounted(() => {
    if (modalRef.value) {
        modalRef.value.addEventListener('hidden.bs.modal', () => {
            emit('update:show', false);
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

const close = () => {
    if (modalInstance.value) {
        modalInstance.value.hide();
    } else {
        emit('update:show', false);
    }
};

const isValid = computed(() => {
    return true; // Validación personalizada según sea necesario
});

// Funciones para manejar el archivo de evidencia
const onFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Aquí puedes procesar el archivo si es necesario
        // Por ahora, solo lo guardamos en el formulario
        form.value.snc_evidencia = file;
    }
};

const submitForm = async () => {
    try {
        // Creamos un FormData para manejar la subida de archivos
        const formData = new FormData();
        
        // Agregamos todos los campos del formulario
        for (const key in form.value) {
            if (form.value[key] !== null && form.value[key] !== undefined) {
                // Si es un archivo, lo agregamos directamente
                // Si es un objeto o array, lo convertimos a string
                if (key === 'snc_evidencia' && form.value[key] instanceof File) {
                    formData.append(key, form.value[key], form.value[key].name);
                } else if (typeof form.value[key] === 'object' && !(form.value[key] instanceof Date)) {
                    // Si es un objeto que no es una fecha, lo convertimos a JSON
                    formData.append(key, JSON.stringify(form.value[key]));
                } else {
                    formData.append(key, form.value[key]);
                }
            }
        }

        // Actualizamos la salida no conforme con la información de tratamiento
        await axios.put(route('api.salidas-nc.update', props.snc.id), formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        
        // Show success feedback
        alert('Tratamiento actualizado exitosamente');
        emit('saved');
        close();
    } catch (e) {
        alert('No se pudo guardar: ' + (e.response?.data?.message || e.message));
        console.error(e);
    }
};
</script>

<style scoped>
/* Custom styles if needed */
</style>