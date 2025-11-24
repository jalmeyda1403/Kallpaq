<template>
    <Teleport to="body">
        <div
            ref="modalRef"
            class="modal fade"
            tabindex="-1"
            role="dialog"
            aria-labelledby="salidaNCModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="salidaNCModalLabel">{{ modalTitle }}</h5>
                        <button type="button" class="close text-white" @click="close" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="submitForm">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Código</label>
                                        <input type="text" v-model="form.snc_codigo" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Tipo <span class="text-danger">*</span></label>
                                        <select v-model="form.snc_tipo" class="form-control" required>
                                            <option value="" disabled>Selecciona...</option>
                                            <option value="producto">Producto</option>
                                            <option value="servicio">Servicio</option>
                                            <option value="proceso">Proceso</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Descripción <span class="text-danger">*</span></label>
                                <textarea v-model="form.snc_descripcion" class="form-control" rows="3" required placeholder="Ingrese la descripción de la no conformidad..."></textarea>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Producto / Servicio <span class="text-danger">*</span></label>
                                <input type="text" v-model="form.snc_producto_servicio" class="form-control" required placeholder="Ingrese el producto o servicio afectado...">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Cantidad Afectada</label>
                                        <input type="number" v-model="form.snc_cantidad_afectada" class="form-control" min="0" placeholder="Ingrese la cantidad afectada...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Fecha Detección <span class="text-danger">*</span></label>
                                        <input type="date" v-model="form.snc_fecha_deteccion" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Responsable <span class="text-danger">*</span></label>
                                        <select v-model="form.snc_responsable_id" class="form-control" required>
                                            <option value="" disabled>Selecciona un responsable...</option>
                                            <option v-for="user in users" :key="user.id" :value="user.id">
                                                {{ user.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Clasificación <span class="text-danger">*</span></label>
                                        <select v-model="form.snc_clasificacion" class="form-control" required>
                                            <option value="" disabled>Selecciona...</option>
                                            <option value="crítica">Crítica</option>
                                            <option value="mayor">Mayor</option>
                                            <option value="menor">Menor</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold">Estado <span class="text-danger">*</span></label>
                                <select v-model="form.snc_estado" class="form-control" required>
                                    <option value="registrada">Registrada</option>
                                    <option value="en análisis">En Análisis</option>
                                    <option value="en tratamiento">En Tratamiento</option>
                                    <option value="tratada">Tratada</option>
                                    <option value="cerrada">Cerrada</option>
                                </select>
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
    </Teleport>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, computed, nextTick } from 'vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import { Modal } from 'bootstrap';

const props = defineProps({
    show: Boolean,
    snc: Object, // null for create, object for edit
});

const emit = defineEmits(['update:show', 'saved']);

const form = ref({
    snc_codigo: '',
    snc_descripcion: '',
    snc_producto_servicio: '',
    snc_cantidad_afectada: null,
    snc_fecha_deteccion: null,
    snc_responsable_id: null,
    snc_tipo: '',
    snc_clasificacion: '',
    snc_estado: 'registrada',
});

const users = ref([]);
const modalRef = ref(null);
const modalInstance = ref(null);

const modalTitle = ref('Nueva Salida No Conforme');

const loadUsers = async () => {
    try {
        const res = await axios.get(route('api.usuarios.index'));
        users.value = res.data;
    } catch (e) {
        console.error(e);
    }
};

onMounted(() => {
    loadUsers();
});

watch(() => props.snc, (newVal) => {
    if (newVal) {
        modalTitle.value = 'Editar Salida No Conforme';
        form.value = { ...newVal };
    } else {
        modalTitle.value = 'Nueva Salida No Conforme';
        form.value = {
            snc_codigo: '',
            snc_descripcion: '',
            snc_producto_servicio: '',
            snc_cantidad_afectada: null,
            snc_fecha_deteccion: null,
            snc_responsable_id: null,
            snc_tipo: '',
            snc_clasificacion: '',
            snc_estado: 'registrada',
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
            snc_codigo: '',
            snc_descripcion: '',
            snc_producto_servicio: '',
            snc_cantidad_afectada: null,
            snc_fecha_deteccion: null,
            snc_responsable_id: null,
            snc_tipo: '',
            snc_clasificacion: '',
            snc_estado: 'registrada',
        };
        modalTitle.value = props.snc ? 'Editar Salida No Conforme' : 'Nueva Salida No Conforme';
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
    return (
        form.value.snc_descripcion &&
        form.value.snc_producto_servicio &&
        form.value.snc_tipo &&
        form.value.snc_clasificacion &&
        form.value.snc_estado
    );
});

const submitForm = async () => {
    try {
        if (props.snc) {
            await axios.put(route('api.salidas-nc.update', props.snc.id), form.value);
        } else {
            await axios.post(route('api.salidas-nc.store'), form.value);
        }
        // Show success feedback - using vanilla alert since toast was from PrimeVue
        alert('Operación completada exitosamente');
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
