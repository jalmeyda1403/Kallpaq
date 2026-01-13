<template>
    <div ref="modalRef" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold">
                        {{ isEdit ? 'Editar Norma' : 'Nueva Norma Auditable' }}
                    </h5>
                    <button type="button" class="close text-white" aria-label="Close" @click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <div class="card card-outline card-secondary shadow-sm mb-3">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Nombre de la Norma <span
                                        class="text-danger">*</span></label>
                                <input v-model="form.nombre" type="text" class="form-control"
                                    placeholder="Ej. ISO 9001:2015">
                            </div>
                            <div class="form-group">
                                <label>Descripción</label>
                                <textarea v-model="form.descripcion" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                            <h6 class="font-weight-bold m-0 text-dark">Requisitos de la Norma</h6>
                            <div>
                                <button class="btn btn-sm btn-outline-info mr-2" @click="addRequisito">
                                    <i class="fas fa-plus"></i> Agregar Manual
                                </button>
                                <button class="btn btn-sm btn-primary shadow-sm" @click="generateAI"
                                    :disabled="generating">
                                    <i class="fas fa-magic mr-1" :class="{ 'fa-spin': generating }"></i>
                                    {{ generating ? 'Generando...' : 'Generar Requisitos con IA' }}
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                <table class="table table-bordered table-hover table-sm m-0 header-fixed">
                                    <thead class="bg-light sticky-top">
                                        <tr>
                                            <th style="width: 100px;">Numeral</th>
                                            <th style="width: 30%;">Denominación</th>
                                            <th>Detalle del Requisito</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(req, index) in form.requisitos" :key="index">
                                            <td class="p-1">
                                                <input type="text" v-model="req.numeral"
                                                    class="form-control form-control-sm border-0 bg-transparent">
                                            </td>
                                            <td class="p-1">
                                                <input type="text" v-model="req.denominacion"
                                                    class="form-control form-control-sm border-0 bg-transparent">
                                            </td>
                                            <td class="p-1">
                                                <textarea v-model="req.detalle"
                                                    class="form-control form-control-sm border-0 bg-transparent"
                                                    rows="1"></textarea>
                                            </td>
                                            <td class="text-center align-middle">
                                                <button class="btn btn-xs btn-outline-danger border-0"
                                                    @click="removeRequisito(index)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="form.requisitos.length === 0">
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                No hay requisitos. Usa el botón "Generar con IA" o agrega manualmente.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
                    <button type="button" class="btn btn-dark" @click="save" :disabled="saving">
                        <i class="fas fa-save mr-1"></i> {{ saving ? 'Guardando...' : 'Guardar Norma' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const emit = defineEmits(['saved']);

const modalRef = ref(null);
let modalInstance = null;

const form = ref({
    id: null,
    nombre: '',
    descripcion: '',
    requisitos: []
});

const saving = ref(false);
const generating = ref(false);
const isEdit = ref(false);

const open = (norma = null) => {
    isEdit.value = !!norma;
    if (norma) {
        // Load full details via API to get requirements if not present
        loadNorma(norma.id);
    } else {
        resetForm();
    }

    if (!modalInstance) {
        modalInstance = new bootstrap.Modal(modalRef.value, { backdrop: 'static', keyboard: false });
    }
    modalInstance.show();
};

const resetForm = () => {
    form.value = {
        id: null,
        nombre: '',
        descripcion: '',
        requisitos: []
    };
};

const loadNorma = async (id) => {
    try {
        const response = await axios.get(`/api/auditoria/normas/${id}`);
        form.value = response.data;
    } catch (e) {
        console.error("Error loading update", e);
        Swal.fire('Error', 'No se pudo cargar la norma.', 'error');
    }
};

const closeModal = () => {
    modalInstance?.hide();
};

const addRequisito = () => {
    form.value.requisitos.push({ numeral: '', denominacion: '', detalle: '' });
};

const removeRequisito = (index) => {
    form.value.requisitos.splice(index, 1);
};

const generateAI = async () => {
    if (!form.value.nombre) {
        Swal.fire('Atención', 'Ingresa el nombre de la norma primero.', 'warning');
        return;
    }

    generating.value = true;
    try {
        const response = await axios.post('/api/auditoria/normas/generate', { nombre_norma: form.value.nombre });
        if (Array.isArray(response.data)) {
            if (form.value.requisitos.length > 0) {
                const result = await Swal.fire({
                    title: 'Requisitos existentes',
                    text: '¿Deseas reemplazar los requisitos actuales o agregar los nuevos?',
                    icon: 'question',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: 'Reemplazar',
                    denyButtonText: 'Agregar',
                    cancelButtonText: 'Cancelar'
                });

                if (result.isConfirmed) {
                    form.value.requisitos = response.data;
                } else if (result.isDenied) {
                    form.value.requisitos = [...form.value.requisitos, ...response.data];
                }
            } else {
                form.value.requisitos = response.data;
            }
            Swal.fire('Generado', `Se han cargado ${response.data.length} requisitos.`, 'success');
        } else {
            Swal.fire('Error', 'La respuesta de la IA no tuvo el formato esperado.', 'error');
        }
    } catch (e) {
        Swal.fire('Error', 'No se pudieron generar los requisitos.', 'error');
    } finally {
        generating.value = false;
    }
};

const save = async () => {
    if (!form.value.nombre) return;
    saving.value = true;
    try {
        if (isEdit.value) {
            await axios.put(`/api/auditoria/normas/${form.value.id}`, form.value);
        } else {
            await axios.post('/api/auditoria/normas', form.value);
        }
        Swal.fire('Guardado', 'La norma se ha guardado correctamente.', 'success');
        emit('saved');
        closeModal();
    } catch (e) {
        Swal.fire('Error', 'No se pudo guardar la norma.', 'error');
    } finally {
        saving.value = false;
    }
};

defineExpose({ open });

onMounted(() => {
    // Initial setup if needed
});
</script>

<style scoped>
.header-fixed thead th {
    position: sticky;
    top: 0;
    z-index: 1;
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}
</style>
