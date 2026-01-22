<template>
    <div class="container-fluid py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link to="/">Inicio</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Listado de Auditores</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-4 text-md-left">
                        <h3 class="card-title mb-0">Gestión de Auditores</h3>
                    </div>
                    <div class="col-md-8 text-md-right">
                        <div class="d-flex justify-content-end align-items-center">
                            <div class="input-group input-group-sm mr-2" style="max-width: 250px;">
                                <input type="text" v-model="buscar" class="form-control" placeholder="Buscar..."
                                    @keyup.enter="fetchAuditores">
                                <div class="input-group-append">
                                    <button class="btn btn-default" type="button" @click="fetchAuditores">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <button class="btn btn-primary btn-sm" @click="abrirNuevoAuditor">
                                <i class="fas fa-plus-circle"></i> Nuevo Auditor
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="h-1 mb-2">
                    <ProgressBar v-if="loading" mode="indeterminate" style="height: 4px;" />
                </div>
                <DataTable :value="auditores" :paginator="true" :rows="10"
                    :class="{ 'opacity-50 pointer-events-none': loading }" tableStyle="min-width: 50rem"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    :rowsPerPageOptions="[10, 25, 50]"
                    currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} auditores"
                    class="p-datatable-sm p-datatable-striped p-datatable-hoverable-rows">

                    <Column field="user.name" header="Auditor" sortable style="width:35%">
                        <template #body="slotProps">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle-sm mr-2">
                                    {{ slotProps.data.user.name.charAt(0) }}
                                </div>
                                <div>
                                    <span class="font-weight-bold text-dark d-block">{{ slotProps.data.user.name
                                    }}</span>
                                    <small class="text-muted">{{ slotProps.data.user.email }}</small>
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="created_at" header="Fecha Registro" sortable style="width:20%; text-align: center;">
                        <template #body="slotProps">
                            <span class="text-muted small">
                                {{ formatDate(slotProps.data.created_at) }}
                            </span>
                        </template>
                    </Column>
                    <Column header="Acciones" class="text-center" style="width:15%">
                        <template #body="slotProps">
                            <div class="d-flex justify-content-center action-buttons">
                                <button class="btn btn-outline-info btn-xs mr-1" @click="verDetalle(slotProps.data)"
                                    title="Historial">
                                    <i class="fas fa-history"></i>
                                </button>
                                <button class="btn btn-outline-warning btn-xs mr-1"
                                    @click="editarAuditor(slotProps.data)" title="Editar">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-xs" @click="confirmDelete(slotProps.data)"
                                    title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </template>
                    </Column>

                    <template #empty>
                        <div class="text-center p-4 text-muted italic">
                            No se encontraron auditores registrados.
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>

        <AuditoresForm ref="formModal" @saved="fetchAuditores" />
        <AuditorDetalleModal ref="detalleModal" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ProgressBar from 'primevue/progressbar';
import { useToast } from 'primevue/usetoast';
import Swal from 'sweetalert2';

import AuditoresForm from './AuditoresForm.vue';
import AuditorDetalleModal from './AuditorDetalleModal.vue';

const toast = useToast();
const auditores = ref([]);
const loading = ref(false);
const buscar = ref('');

const formModal = ref(null);
const detalleModal = ref(null);

const fetchAuditores = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/auditores', { params: { buscar: buscar.value } });
        auditores.value = response.data;
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar la lista de auditores', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const abrirNuevoAuditor = () => {
    formModal.value.open();
};

const editarAuditor = (auditor) => {
    formModal.value.open(auditor);
};

const verDetalle = (auditor) => {
    detalleModal.value.open(auditor.id);
};

const confirmDelete = (auditor) => {
    Swal.fire({
        title: '¿Eliminar Auditor?',
        text: `Se retirará a ${auditor.user.name} de la lista de auditores.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.delete(`/api/auditores/${auditor.id}`);
                Swal.fire('Eliminado', 'El auditor ha sido retirado correctamente.', 'success');
                fetchAuditores();
            } catch (e) {
                Swal.fire('Error', 'No se pudo eliminar el auditor.', 'error');
            }
        }
    });
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

onMounted(fetchAuditores);
</script>

<style scoped>
.action-buttons .btn-xs {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    line-height: 1.5;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.action-buttons .btn-xs:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.avatar-circle-sm {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #d32f2f 0%, #b71c1c 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
}

.italic {
    font-style: italic;
}

/* Custom loader styles */
.p-datatable-loading-overlay {
    background: rgba(255, 255, 255, 0.7) !important;
}

.p-datatable-loading-icon {
    color: #dc3545 !important;
    font-size: 2.5rem !important;
}
</style>
