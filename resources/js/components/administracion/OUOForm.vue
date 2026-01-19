<template>
    <div class="modal fade" tabindex="-1" ref="modalEl" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="overflow-y: auto;">
                <div class="modal-header bg-danger text-white py-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box mr-3 bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                            style="width: 40px; height: 40px;">
                            <i class="fas" :class="ouo ? 'fa-edit text-danger' : 'fa-plus text-danger'"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 font-weight-bold">
                                {{ modalTitle }}
                            </h5>
                            <small class="text-white-50">{{ modalSubtitle }}</small>
                        </div>
                    </div>
                    <button type="button" class="close text-white outline-none" aria-label="Close" @click="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light p-4">
                    <form @submit.prevent="submitForm">
                        <div class="form-row">
                            <!-- Código OUO -->
                            <div class="form-group col-md-4 mb-3">
                                <label for="ouo_codigo" class="font-weight-600 small text-uppercase text-muted">
                                    Código OUO <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control bg-white border-0 shadow-sm px-3 py-4"
                                    id="ouo_codigo" v-model="form.ouo_codigo" required maxlength="255"
                                    placeholder="EJ: G01">
                            </div>

                            <!-- Nombre OUO -->
                            <div class="form-group col-md-8 mb-3">
                                <label for="ouo_nombre" class="font-weight-600 small text-uppercase text-muted">
                                    Nombre OUO <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control bg-white border-0 shadow-sm px-3 py-4"
                                    id="ouo_nombre" v-model="form.ouo_nombre" required maxlength="255"
                                    placeholder="Ej: Gerencia General">
                            </div>
                        </div>

                        <div class="form-row">
                            <!-- OUO Padre -->
                            <div class="form-group col-md-8 mb-3">
                                <label for="ouo_padre" class="font-weight-600 small text-uppercase text-muted">OUO
                                    Padre</label>
                                <select id="ouo_padre" class="form-control bg-white border-0 shadow-sm px-3"
                                    style="height: 50px;" v-model="form.ouo_padre">
                                    <option :value="null">-- Ninguno (Raíz) --</option>
                                    <option v-for="padre in store.ouoPadresForDropdown" :key="padre.id"
                                        :value="padre.id">
                                        {{ padre.ouo_nombre }}
                                    </option>
                                </select>
                            </div>

                            <!-- Nivel Jerárquico -->
                            <div class="form-group col-md-4 mb-3">
                                <label for="nivel_jerarquico" class="font-weight-600 small text-uppercase text-muted">
                                    Nivel Jerárquico <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control bg-white border-0 shadow-sm px-3 py-4"
                                    id="nivel_jerarquico" v-model="form.nivel_jerarquico" required min="1">
                            </div>
                        </div>

                        <hr class="my-4 border-light">

                        <h6 class="text-muted text-uppercase small font-weight-bold mb-3">Vigencia y Documentos</h6>

                        <div class="form-row">
                            <!-- Fecha Vigencia Inicio -->
                            <div class="form-group col-md-6 mb-3">
                                <label for="fecha_vigencia_inicio" class="font-weight-600 small text-uppercase text-muted">
                                    Inicio Vigencia <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control bg-white border-0 shadow-sm px-3 py-4"
                                    id="fecha_vigencia_inicio" v-model="form.fecha_vigencia_inicio" required>
                            </div>

                            <!-- Fecha Vigencia Fin -->
                            <div class="form-group col-md-6 mb-3">
                                <label for="fecha_vigencia_fin" class="font-weight-600 small text-uppercase text-muted">Fin
                                    Vigencia</label>
                                <input type="date" class="form-control bg-white border-0 shadow-sm px-3 py-4"
                                    id="fecha_vigencia_fin" v-model="form.fecha_vigencia_fin">
                            </div>
                        </div>

                        <div class="form-row">
                            <!-- Documento Alta -->
                            <div class="form-group col-md-6 mb-3">
                                <label for="doc_vigencia_alta" class="font-weight-600 small text-uppercase text-muted">Doc.
                                    Vigencia Alta</label>
                                <input type="text" class="form-control bg-white border-0 shadow-sm px-3 py-4"
                                    id="doc_vigencia_alta" v-model="form.doc_vigencia_alta" maxlength="255"
                                    placeholder="Resolución N°...">
                            </div>

                            <!-- Documento Baja -->
                            <div class="form-group col-md-6 mb-3">
                                <label for="doc_vigencia_baja" class="font-weight-600 small text-uppercase text-muted">Doc.
                                    Vigencia Baja</label>
                                <input type="text" class="form-control bg-white border-0 shadow-sm px-3 py-4"
                                    id="doc_vigencia_baja" v-model="form.doc_vigencia_baja" maxlength="255">
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <div class="custom-control custom-switch pt-2">
                                <input type="checkbox" class="custom-control-input" id="estadoSwitch" v-model="form.estado"
                                    :true-value="1" :false-value="0">
                                <label class="custom-control-label font-weight-600 text-muted" for="estadoSwitch">
                                    Estado: <span :class="form.estado ? 'text-success' : 'text-danger'">{{ form.estado ?
                                        'Activo' : 'Inactivo' }}</span>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-white border-0 py-3">
                    <button type="button" class="btn btn-outline-secondary px-4 border-0" @click="close">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-danger px-4 shadow-sm font-weight-bold" @click="submitForm">
                        <span v-if="saving" class="spinner-border spinner-border-sm mr-1" role="status"
                            aria-hidden="true"></span>
                        <i v-else class="fas fa-save mr-1"></i> {{ ouo ? 'Actualizar' : 'Guardar' }} OUO
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch, reactive } from 'vue';
import { useAsignacionOuoStore } from '@/stores/asignacionOuoStore';
import { Modal } from 'bootstrap';
import Swal from 'sweetalert2';

const props = defineProps({
    ouo: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['saved', 'close']);
const store = useAsignacionOuoStore();
const saving = ref(false);
const modalEl = ref(null);
let modalInstance = null;

const modalTitle = computed(() => props.ouo ? 'Editar Unidad Orgánica' : 'Nueva Unidad Orgánica');
const modalSubtitle = computed(() => props.ouo ? 'Actualice los datos de la estructura' : 'Registre una nueva área en el sistema');

const form = ref({
    ouo_codigo: '',
    ouo_nombre: '',
    ouo_padre: null,
    nivel_jerarquico: 1,
    fecha_vigencia_inicio: '',
    fecha_vigencia_fin: '',
    doc_vigencia_alta: '',
    doc_vigencia_baja: '',
    estado: 1
});

const initForm = () => {
    if (props.ouo) {
        form.value = { ...props.ouo };
        if (form.value.ouo_padre === undefined) form.value.ouo_padre = null;
    } else {
        resetForm();
    }
};

const resetForm = () => {
    form.value = {
        ouo_codigo: '',
        ouo_nombre: '',
        ouo_padre: null,
        nivel_jerarquico: 1,
        fecha_vigencia_inicio: new Date().toISOString().split('T')[0],
        fecha_vigencia_fin: '',
        doc_vigencia_alta: '',
        doc_vigencia_baja: '',
        estado: 1
    };
};

watch(() => props.ouo, initForm);

// Auto-set state to inactive if validity end document or date is provided
watch([() => form.value.doc_vigencia_baja, () => form.value.fecha_vigencia_fin], ([newDoc, newDate]) => {
    if (newDoc || newDate) {
        form.value.estado = 0;
    }
});


onMounted(() => {
    if (modalEl.value) {
        modalInstance = new Modal(modalEl.value, {
            backdrop: 'static',
            keyboard: false
        });
    }
    store.fetchOuoPadresForDropdown();
});

const open = () => {
    initForm();
    modalInstance.show();
};

const close = () => {
    modalInstance.hide();
    emit('close');
};

const submitForm = async () => {
    saving.value = true;
    try {
        if (props.ouo) {
            await store.updateOuo(props.ouo.id, form.value);
            Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: 'La OUO ha sido actualizada correctamente.',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            await store.createOuo(form.value);
            Swal.fire({
                icon: 'success',
                title: '¡Creado!',
                text: 'La OUO ha sido creada correctamente.',
                timer: 1500,
                showConfirmButton: false
            });
        }
        emit('saved');
        close();
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: store.error || 'Ocurrió un error al guardar.',
        });
    } finally {
        saving.value = false;
    }
};

defineExpose({
    open,
    close
});
</script>

<style scoped>
.required:after {
    content: " *";
    color: red;
}
</style>
