<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light py-2 px-3 rounded shadow-sm">
                <li class="breadcrumb-item"><router-link to="/home">Inicio</router-link></li>
                <li class="breadcrumb-item text-muted">Gestión de Auditorías</li>
                <li class="breadcrumb-item active" aria-current="page">Listado de Auditores</li>
            </ol>
        </nav>

        <div class="card shadow border-0">
            <div class="card-header bg-white py-4">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-left">
                        <h3 class="card-title mb-0 font-weight-bold display-5">
                            <i class="fas fa-user-shield text-danger mr-2"></i> Gestión de Auditores
                        </h3>
                    </div>
                    <div class="col-md-6 text-center text-md-right mt-3 mt-md-0">
                        <button class="btn btn-primary btn-lg shadow-sm px-4" @click="abrirNuevoAuditor">
                            <i class="fas fa-user-plus mr-1"></i> Registrar Auditor
                        </button>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="input-group" style="max-width: 450px;">
                        <input type="text" v-model="buscar" class="form-control form-control-lg border-right-0"
                            placeholder="Buscar por nombre o correo..." @keyup.enter="fetchAuditores">
                        <div class="input-group-append">
                            <button class="btn btn-dark btn-lg shadow-sm border-left-0" style="z-index: 1;"
                                type="button" @click="fetchAuditores">
                                <i class="fas fa-search px-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <DataTable :value="auditores" :paginator="true" :rows="10" stripedRows :loading="loading"
                    responsiveLayout="stack" breakpoint="960px" class="p-datatable-modern overflow-hidden"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    :rowsPerPageOptions="[10, 20, 50]"
                    currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} auditores">

                    <Column field="user.name" header="Auditores del Sistema" sortable>
                        <template #body="slotProps">
                            <div class="d-flex align-items-center py-2">
                                <div class="avatar-circle">
                                    {{ slotProps.data.user.name.charAt(0) }}
                                </div>
                                <div class="ml-3">
                                    <div class="font-weight-bold h6 mb-0">{{ slotProps.data.user.name }}</div>
                                    <small class="text-muted d-block mt-1">{{ slotProps.data.user.email }}</small>
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column field="created_at" header="Fecha Registro" sortable headerClass="text-center"
                        class="text-center">
                        <template #body="slotProps">
                            <span class="text-muted small font-weight-bold">
                                {{ formatDate(slotProps.data.created_at) }}
                            </span>
                        </template>
                    </Column>
                    <Column header="Acciones" headerClass="text-center" class="text-center" style="width: 180px;">
                        <template #body="slotProps">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-outline-info btn-sm shadow-sm"
                                    @click="verDetalle(slotProps.data)" title="Ver Historial">
                                    <i class="fas fa-history"></i>
                                </button>
                                <button class="btn btn-outline-warning btn-sm shadow-sm"
                                    @click="editarAuditor(slotProps.data)" title="Editar Auditor">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm shadow-sm ml-1"
                                    @click="confirmDelete(slotProps.data)" title="Eliminar Auditor">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </template>
                    </Column>

                    <template #empty>
                        <div class="text-center p-5">
                            <i class="fas fa-user-slash fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No se encontraron auditores registrados.</h5>
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
.avatar-circle {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #d32f2f 0%, #b71c1c 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.1rem;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.card {
    border-radius: 12px;
}

.breadcrumb {
    background: transparent !important;
    padding-left: 0 !important;
}

.gap-2 {
    gap: 0.5rem;
}

.display-5 {
    font-size: 1.75rem;
}
</style>
