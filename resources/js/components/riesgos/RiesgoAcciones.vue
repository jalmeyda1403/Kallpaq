<template>
    <div class="container-fluid">
        <!-- Encabezado -->
        <div class="header-container">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">{{ formatBreadcrumbId(store.riesgoActual ? store.riesgoActual.id : null)
                    }}</span>
                <span class="mx-2 text-secondary"><i class="fas fa-chevron-right fa-xs"></i></span>
                <span class="text-dark">Planes de Tratamiento</span>
            </h6>
        </div>
        <div class="text-left mb-4">
            <h6 class="mb-1" style="font-weight: bold;">PLANES DE TRATAMIENTO</h6>
            <p class="mb-3 text-muted" style="font-size: 0.875rem;">
                Gestione las acciones necesarias para mitigar el riesgo identificado.
            </p>
        </div>

        <div class="row mb-3">
            <div class="col-12 text-right">
                <button class="btn btn-primary btn-sm" @click="openModal()">
                    <i class="fas fa-plus"></i> Añadir Acciones
                </button>
            </div>
        </div>

        <DataTable :value="store.acciones" :paginator="true" :rows="5" :loading="store.loadingAcciones"
            responsiveLayout="scroll" class="p-datatable-sm">
            <template #empty>
                No hay planes de tratamiento registrados.
            </template>
            <Column field="ra_descripcion" header="Descripción"></Column>
            <Column field="ra_responsable" header="Responsable"></Column>
            <Column header="Fecha Inicio">
                <template #body="slotProps">
                    {{ formatDate(slotProps.data.ra_fecha_inicio) }}
                </template>
            </Column>
            <Column header="Fecha Fin">
                <template #body="slotProps">
                    <div v-if="slotProps.data.ra_fecha_fin_reprogramada">
                        <span class="text-danger" style="text-decoration: line-through;">
                            {{ formatDate(slotProps.data.ra_fecha_fin_planificada) }}
                        </span>
                        <br>
                        <span class="text-success font-weight-bold">
                            {{ formatDate(slotProps.data.ra_fecha_fin_reprogramada) }}
                        </span>
                    </div>
                    <div v-else>
                        {{ formatDate(slotProps.data.ra_fecha_fin_planificada) }}
                    </div>
                </template>
            </Column>
            <Column header="Estado">
                <template #body="slotProps">
                    <span class="badge" :class="getEstadoBadgeClass(slotProps.data.ra_estado)">
                        {{ slotProps.data.ra_estado }}
                    </span>
                </template>
            </Column>
            <Column header="Acciones" headerStyle="width: 150px; text-align: center" bodyStyle="text-align: center">
                <template #body="slotProps">
                    <button class="btn btn-sm btn-info mr-1" @click="openModal(slotProps.data)" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-warning mr-1" @click="openReprogramarModal(slotProps.data)"
                        title="Reprogramar">
                        <i class="fas fa-clock"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" @click="confirmDelete(slotProps.data)" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </template>
            </Column>
        </DataTable>

        <!-- Modal para Crear/Editar Acción -->
        <Teleport to="body">
            <div class="modal fade" tabindex="-1" ref="actionModal" aria-hidden="true" style="z-index: 1060;">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">{{ editing ? 'Editar Plan de Tratamiento' : 'Nuevo Plan Tratamiento'
                            }}</h5>
                            <button type="button" class="close text-white" @click="closeModal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="saveAccion">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">Descripción <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control" v-model="form.ra_descripcion" rows="3"
                                        required></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Responsable</label>
                                            <input type="text" class="form-control" v-model="form.ra_responsable">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Correo Responsable</label>
                                            <input type="email" class="form-control"
                                                v-model="form.ra_responsable_correo">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Fecha Inicio</label>
                                            <input type="date" class="form-control" v-model="form.ra_fecha_inicio">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Fecha Fin Planificada</label>
                                            <input type="date" class="form-control"
                                                v-model="form.ra_fecha_fin_planificada">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Estado <span class="text-danger">*</span></label>
                                    <select class="form-control" v-model="form.ra_estado" required>
                                        <option value="programada">Programada</option>
                                        <option value="en proceso">En Proceso</option>
                                        <option value="implementada">Implementada</option>
                                        <option value="desestimada">Desestimada</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Comentario</label>
                                    <textarea class="form-control" v-model="form.ra_comentario" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
                                <button type="submit" class="btn btn-danger" :disabled="saving">
                                    <span v-if="saving" class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                    {{ editing ? 'Actualizar' : 'Guardar' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Modal Reprogramar -->
        <Teleport to="body">
            <div class="modal fade" tabindex="-1" ref="reprogramarModalRef" aria-hidden="true" style="z-index: 1060;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title">Reprogramar Acción</h5>
                            <button type="button" class="close text-white" @click="closeReprogramarModal"
                                aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="submitReprogramacion">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">Tipo de Acción <span
                                            class="text-danger">*</span></label>
                                    <select v-model="reprogramarForm.actionType" class="form-control" required>
                                        <option value="reprogramar">Reprogramar Fecha</option>
                                        <option value="desestimar">Desestimar Acción</option>
                                    </select>
                                </div>
                                <div class="form-group" v-if="reprogramarForm.actionType === 'reprogramar'">
                                    <label class="font-weight-bold">Nueva Fecha Fin <span
                                            class="text-danger">*</span></label>
                                    <input v-model="reprogramarForm.ra_fecha_fin_reprogramada" type="date"
                                        class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Justificación <span
                                            class="text-danger">*</span></label>
                                    <textarea v-model="reprogramarForm.ra_justificacion" class="form-control" rows="3"
                                        required></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Evidencia (Opcional)</label>
                                    <input type="file" class="form-control-file" @change="handleFileUpload">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    @click="closeReprogramarModal">Cancelar</button>
                                <button type="submit" class="btn btn-primary" :disabled="savingReprogramacion">
                                    <span v-if="savingReprogramacion" class="spinner-border spinner-border-sm"
                                        role="status" aria-hidden="true"></span>
                                    Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useRiesgoStore } from '@/stores/riesgoStore';
import Swal from 'sweetalert2';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const store = useRiesgoStore();

const formatBreadcrumbId = (id) => {
    if (!id) return 'Riesgo';
    return `R${id.toString().padStart(6, '0')}`;
};

const actionModal = ref(null);
const reprogramarModalRef = ref(null);
const editing = ref(false);
const saving = ref(false);
const savingReprogramacion = ref(false);

const form = reactive({
    id: null,
    ra_descripcion: '',
    ra_responsable: '',
    ra_responsable_correo: '',
    ra_fecha_inicio: '',
    ra_fecha_fin_planificada: '',
    ra_estado: 'programada',
    ra_comentario: ''
});

const reprogramarForm = reactive({
    id: null,
    actionType: 'reprogramar',
    ra_fecha_fin_reprogramada: '',
    ra_justificacion: '',
    ra_evidencia: null
});

onMounted(() => {
    if (store.riesgoActual && store.riesgoActual.id) {
        store.fetchAcciones(store.riesgoActual.id);
    }
});

const openModal = (accion = null) => {
    if (accion) {
        editing.value = true;
        Object.assign(form, accion);
        // Format dates for input
        if (form.ra_fecha_inicio) form.ra_fecha_inicio = form.ra_fecha_inicio.split('T')[0];
        if (form.ra_fecha_fin_planificada) form.ra_fecha_fin_planificada = form.ra_fecha_fin_planificada.split('T')[0];
    } else {
        editing.value = false;
        resetForm();
    }
    $(actionModal.value).modal('show');
};

const closeModal = () => {
    $(actionModal.value).modal('hide');
    resetForm();
};

const resetForm = () => {
    form.id = null;
    form.ra_descripcion = '';
    form.ra_responsable = '';
    form.ra_responsable_correo = '';
    form.ra_fecha_inicio = '';
    form.ra_fecha_fin_planificada = '';
    form.ra_estado = 'programada';
    form.ra_comentario = '';
    editing.value = false;
};

const saveAccion = async () => {
    saving.value = true;
    try {
        if (editing.value) {
            await store.updateAccion(form.id, form);
            Swal.fire('Actualizado', 'El plan de tratamiento ha sido actualizado.', 'success');
        } else {
            await store.createAccion(store.riesgoActual.id, form);
            Swal.fire('Guardado', 'El plan de tratamiento ha sido creado.', 'success');
        }
        closeModal();
    } catch (error) {
        Swal.fire('Error', 'Hubo un problema al guardar el plan.', 'error');
    } finally {
        saving.value = false;
    }
};

const confirmDelete = (accion) => {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await store.deleteAccion(accion.id);
                Swal.fire('Eliminado!', 'El plan ha sido eliminado.', 'success');
            } catch (error) {
                Swal.fire('Error', 'No se pudo eliminar el plan.', 'error');
            }
        }
    });
};

// Reprogramación Logic
const openReprogramarModal = (action) => {
    reprogramarForm.id = action.id;
    reprogramarForm.actionType = 'reprogramar';
    reprogramarForm.ra_fecha_fin_reprogramada = '';
    reprogramarForm.ra_justificacion = '';
    reprogramarForm.ra_evidencia = null;
    $(reprogramarModalRef.value).modal('show');
};

const closeReprogramarModal = () => {
    $(reprogramarModalRef.value).modal('hide');
};

const handleFileUpload = (event) => {
    reprogramarForm.ra_evidencia = event.target.files[0];
};

const submitReprogramacion = async () => {
    savingReprogramacion.value = true;
    try {
        const formData = new FormData();
        formData.append('actionType', reprogramarForm.actionType);
        formData.append('ra_justificacion', reprogramarForm.ra_justificacion);
        if (reprogramarForm.actionType === 'reprogramar') {
            formData.append('ra_fecha_fin_reprogramada', reprogramarForm.ra_fecha_fin_reprogramada);
        }
        if (reprogramarForm.ra_evidencia) {
            formData.append('ra_evidencia', reprogramarForm.ra_evidencia);
        }

        await store.reprogramarAccion(reprogramarForm.id, formData);
        closeReprogramarModal();
        Swal.fire('Éxito', 'Acción gestionada correctamente', 'success');
    } catch (error) {
        Swal.fire('Error', 'No se pudo gestionar la acción', 'error');
    } finally {
        savingReprogramacion.value = false;
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString();
};

const getEstadoBadgeClass = (status) => {
    const badges = {
        'programada': 'badge badge-secondary',
        'en proceso': 'badge badge-primary',
        'implementada': 'badge badge-success',
        'desestimada': 'badge badge-danger'
    };
    return badges[status] || 'badge badge-secondary';
};
</script>

<style scoped>
/* No specific styles needed for Bootstrap modal */
.header-container {
    padding: 0.75rem;
    margin-bottom: 1.5rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #dc3545;
    display: flex;
    align-items: center;
}
</style>