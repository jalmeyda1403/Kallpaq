<template>
    <div class="compromisos-container p-3">
        <!-- HEADER / BREADCRUMB -->
        <div class="header-container mb-3">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">Requisito</span>
                <span class="mx-2 text-secondary"><i class="fas fa-chevron-right fa-xs"></i></span>
                <span class="text-muted small text-truncate" style="max-width: 300px;">Gestión de Compromisos</span>
            </h6>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                 <h6 class="mb-1 font-weight-bold text-uppercase text-dark">Planes de Acción</h6>
                 <p class="mb-0 text-muted small">
                    Defina las acciones necesarias para dar cumplimiento al requisito.
                </p>
            </div>
            <button class="btn btn-sm btn-danger shadow-sm" @click="openForm()" v-if="!showForm">
                <i class="fas fa-plus mr-1"></i> Nuevo Compromiso
            </button>
        </div>

        <!-- FORMULARIO (ADD / EDIT) -->
        <div v-if="showForm" class="card border-danger shadow-sm mb-4 animate__animated animate__fadeIn">
            <div class="card-header bg-danger text-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 small font-weight-bold">
                    <i class="fas fa-edit mr-1"></i> {{ editMode ? 'Editar Compromiso' : 'Nuevo Compromiso' }}
                </h6>
                <button type="button" class="close text-white small" aria-label="Close" @click="cancelForm">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card-body bg-light p-3">
                <div class="form-group small">
                    <label class="font-weight-bold text-dark mb-1">Descripción de la Acción <span class="text-danger">*</span></label>
                    <textarea v-model="form.ec_descripcion" class="form-control" rows="2" placeholder="Describa la acción a realizar..."></textarea>
                </div>
                
                <div class="form-row small">
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold mb-1">Fecha Límite</label>
                        <input type="date" v-model="form.ec_fecha_limite" class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold mb-1">Estado</label>
                        <select v-model="form.ec_estado" class="form-control font-weight-bold" :class="statusClass(form.ec_estado)">
                            <option value="pendiente" class="text-secondary">Pendiente</option>
                            <option value="en_proceso" class="text-warning">En Proceso</option>
                            <option value="completado" class="text-success">Completado</option>
                        </select>
                    </div>
                </div>

                <!-- AVANCE -->
                <div class="form-group small mb-2">
                    <label class="font-weight-bold text-dark mb-1">Avance / Evidencia</label>
                    <textarea v-model="form.ec_avance" class="form-control" rows="2" placeholder="Registre el avance o evidencia..."></textarea>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-sm btn-outline-secondary mr-2" @click="cancelForm">Cancelar</button>
                    <button class="btn btn-sm btn-danger px-4" @click="save">
                        <i class="fas fa-save mr-1"></i> Guardar
                    </button>
                </div>
            </div>
        </div>

        <!-- LISTA DE COMPROMISOS -->
        <div class="table-responsive border rounded bg-white shadow-sm" v-if="compromisos.length > 0">
            <table class="table table-sm table-hover mb-0 small">
                <thead class="thead-light">
                    <tr>
                        <th style="border-top: 0;">Descripción</th>
                        <th style="width: 15%; border-top: 0;">Meta</th>
                        <th style="width: 30%; border-top: 0;">Avance</th>
                        <th style="width: 10%; border-top: 0;" class="text-center">Estado</th>
                        <th style="width: 100px; border-top: 0;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="comp in compromisos" :key="comp.id">
                        <td class="align-middle border-right">{{ comp.ec_descripcion }}</td>
                        <td class="align-middle text-muted border-right">{{ formatDate(comp.ec_fecha_limite) }}</td>
                        <td class="align-middle border-right">
                            <span v-if="comp.ec_avance" class="text-dark d-block" style="white-space: pre-wrap;">{{ comp.ec_avance }}</span>
                            <span v-else class="text-muted font-italic small">- Sin avance -</span>
                        </td>
                        <td class="align-middle text-center border-right">
                            <span class="badge badge-pill px-2 py-1" :class="badgeClass(comp.ec_estado)">
                                {{ formatStatus(comp.ec_estado) }}
                            </span>
                        </td>
                        <td class="align-middle text-center">
                            <button class="btn btn-link text-primary btn-sm p-0 mr-2" @click="openForm(comp)" title="Editar">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button class="btn btn-link text-danger btn-sm p-0" @click="remove(comp.id)" title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- EMPTY STATE -->
        <div v-else-if="!showForm" class="text-center py-5 bg-light rounded border dashed-border mt-3">
            <i class="fas fa-clipboard-list fa-3x text-muted mb-3 opacity-50"></i>
            <p class="text-muted mb-0 small">No hay compromisos registrados.</p>
        </div>
    </div>
</template>

<script setup>
import { ref, defineProps, watch, defineEmits } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
    requirementId: {
        type: Number,
        required: true
    },
    initialData: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['update-list']);

const compromisos = ref([]);
const showForm = ref(false);
const editMode = ref(false);
const form = ref({});

// Init
watch(() => props.initialData, (val) => {
    compromisos.value = val ? [...val] : [];
}, { immediate: true, deep: true });

const openForm = (comp = null) => {
    if (comp) {
        editMode.value = true;
        form.value = JSON.parse(JSON.stringify(comp)); 
    } else {
        editMode.value = false;
        form.value = {
            ec_descripcion: '',
            ec_fecha_limite: '',
            ec_estado: 'pendiente',
            ec_avance: ''
        };
    }
    showForm.value = true;
};

const cancelForm = () => {
    showForm.value = false;
    form.value = {};
};

const save = async () => {
    if (!form.value.ec_descripcion) {
        Swal.fire('Atención', 'La descripción es obligatoria', 'warning');
        return;
    }

    try {
        const payload = {
            expectativa_id: props.requirementId,
            ...form.value
        };

        let response;
        if (editMode.value && form.value.id) {
            response = await axios.put(`/api/compromisos/${form.value.id}`, payload);
            const idx = compromisos.value.findIndex(c => c.id === form.value.id);
            if (idx !== -1) compromisos.value[idx] = response.data.compromiso;
        } else {
            // Need expectation ID
            if(!props.requirementId) {
                 Swal.fire('Error', 'Requisito no identificado', 'error');
                 return;
            }
            response = await axios.post('/api/compromisos', payload);
            compromisos.value.push(response.data.compromiso);
        }

        showForm.value = false;
        emit('update-list', compromisos.value);
        
        Swal.fire({
            icon: 'success',
            title: 'Guardado',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500
        });

    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'No se pudo guardar el compromiso', 'error');
    }
};

const remove = async (id) => {
    const result = await Swal.fire({
        title: '¿Eliminar?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        try {
            await axios.delete(`/api/compromisos/${id}`);
            compromisos.value = compromisos.value.filter(c => c.id !== id);
            emit('update-list', compromisos.value);
            
            Swal.fire({
                icon: 'success',
                title: 'Eliminado',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500
            });
        } catch (error) {
            Swal.fire('Error', 'No se pudo eliminar', 'error');
        }
    }
};

// Utilities
const formatDate = (date) => date || '-';
const formatStatus = (status) => {
    const map = { 'pendiente': 'Pendiente', 'en_proceso': 'En Proceso', 'completado': 'Completado' };
    return map[status] || status;
};
const badgeClass = (status) => {
    return {
        'badge-secondary': status === 'pendiente',
        'badge-warning': status === 'en_proceso',
        'badge-success': status === 'completado'
    };
};
const statusClass = (status) => {
     return {
        'text-secondary': status === 'pendiente',
        'text-warning': status === 'en_proceso',
        'text-success': status === 'completado'
    };
}
</script>

<style scoped>
.header-container {
    padding: 0.75rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    border-left: 5px solid #dc3545;
    display: flex;
    align-items: center;
}
.dashed-border {
    border: 2px dashed #dee2e6 !important;
}
</style>
