<template>
    <div class="container-fluid py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link :to="{ name: 'home' }"
                        class="text-danger font-weight-bold">Inicio</router-link></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Asignación de Usuarios y Procesos a
                    OUOs</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-md-left">
                                <h3 class="card-title mb-0">Gestión de OUOs para Asignación</h3>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <button class="btn btn-secondary btn-sm ml-1" @click="downloadTemplate">
                                    <i class="fas fa-file-download"></i> Plantilla
                                </button>
                                <button class="btn btn-success btn-sm ml-1" @click="triggerFileUpload">
                                    <i class="fas fa-file-excel"></i> Importar Excel
                                </button>
                                <input type="file" ref="fileInput" class="d-none" accept=".xlsx, .xls, .csv"
                                    @change="handleFileUpload">
                                <button class="btn btn-primary btn-sm ml-1" @click="openCreateModal">
                                    <i class="fas fa-plus-circle"></i> Nueva OUO
                                </button>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <form @submit.prevent="applyFilters">
                                    <div class="form-row">
                                        <div class="col-md-5">
                                            <input type="text" name="search" id="search" class="form-control"
                                                placeholder="Buscar por Unidad Orgánica" v-model="store.filters.search">
                                        </div>
                                        <div class="col-md-3">
                                            <select name="ouo_padre_id" id="ouo_padre_id" class="form-control"
                                                v-model="store.filters.ouo_padre_id">
                                                <option :value="null">Todas las OUOs Padre</option>
                                                <option v-for="ouoPadre in store.ouoPadresForDropdown"
                                                    :key="ouoPadre.id" :value="ouoPadre.id">
                                                    {{ ouoPadre.ouo_nombre }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select name="estado" id="estado" class="form-control"
                                                v-model="store.filters.estado">
                                                <option value="">Todos los Estados</option>
                                                <option value="1">Activo</option>
                                                <option value="0">Inactivo</option>
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn bg-dark">
                                                <i class="fas fa-search"></i> Buscar
                                            </button>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="h-1 mb-2">
                            <ProgressBar v-if="store.loading" mode="indeterminate" style="height: 4px;" />
                        </div>
                        <DataTable ref="dt" :value="store.ouos" :lazy="true" :paginator="true" :rows="20"
                            :class="{ 'opacity-50 pointer-events-none': store.loading }"
                            :totalRecords="store.pagination.total"
                            :first="(store.pagination.currentPage - 1) * store.pagination.perPage" @page="onPage"
                            :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id"
                            :globalFilterFields="['id', 'ouo_nombre', 'ouo_padre_nombre']">
                            <Column field="id" header="ID" style="width:5%"></Column>
                            <Column field="ouo_codigo" header="Código" sortable style="width:10%"></Column>
                            <Column field="ouo_nombre" header="Nombre OUO" sortable style="width:25%"></Column>
                            <Column field="ouo_padre_nombre" header="OUO Padre" sortable style="width:30%"></Column>
                            <Column field="procesos_count" header="# Procesos" sortable
                                style="width:10%; text-align: center;"></Column>
                            <Column field="users_count" header="# Usuarios" sortable
                                style="width:10%; text-align: center;"></Column>
                            <Column header="Acciones" :exportable="false" style="width:10%">
                                <template #body="{ data }">
                                    <a href="#" title="Editar OUO" class="mr-1 d-inline-block btn-modal-trigger p-2"
                                        @click.prevent="openEditModal(data)">
                                        <i class="fas fa-edit fa-lg text-warning"></i>
                                    </a>
                                    <a href="#" title="Asignar Procesos"
                                        class="mr-1 d-inline-block btn-modal-trigger p-2"
                                        @click.prevent="assignProcesses(data)">
                                        <i class="fas fa-cogs fa-lg text-dark"></i>
                                    </a>
                                    <a href="#" title="Asignar Usuarios"
                                        class="mr-1 d-inline-block btn-modal-trigger p-2"
                                        @click.prevent="assignUsers(data)">
                                        <i class="fas fa-users-cog fa-lg text-primary"></i>
                                    </a>
                                </template>
                            </Column>
                            <template #empty>
                                No se encontraron OUOs.
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for Assigning Processes -->
    <teleport to="body">
        <div v-if="showAssignProcessesModal" class="modal fade show d-block" tabindex="-1" role="dialog"
            aria-labelledby="assignProcessesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="assignProcessesModalLabel">Asignar Procesos a OUO: {{
                            currentOuo?.ouo_nombre }}</h5>
                        <button type="button" class="close" @click="closeAssignProcessesModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <Procesoslist :ouo="currentOuo" @processes-updated="handleProcessesUpdated" />
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <small class="text-muted">(*) P: Propietario, D: Delegado, E: Ejecutor</small>

                            <button type="button" class="btn btn-secondary btn-sm" @click="closeAssignProcessesModal">
                                Cerrar
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div v-if="showAssignProcessesModal" class="modal-backdrop fade show"></div>

        <!-- Modal for Assigning Users -->
        <div v-if="showAssignUsersModal" class="modal fade show d-block" tabindex="-1" role="dialog"
            aria-labelledby="assignUsersModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="assignUsersModalLabel">Asignar Usuarios a OUO: {{
                            currentOuo?.ouo_nombre
                        }}</h5>
                        <button type="button" class="close" @click="closeAssignUsersModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <Usuarioslist :ouo="currentOuo" @users-updated="handleUsersUpdated" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm"
                            @click="closeAssignUsersModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="showAssignUsersModal" class="modal-backdrop fade show"></div>

        <!-- Modal for Create/Edit OUO -->
        <OUOForm ref="ouoFormRef" :ouo="currentOuoForForm" @saved="handleOuoSaved" @close="closeOuoFormModal" />

    </teleport>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAsignacionOuoStore } from '@/stores/asignacionOuoStore';
import Swal from 'sweetalert2';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ProgressBar from 'primevue/progressbar';

// Custom Components
import Procesoslist from './Procesoslist.vue';
import Usuarioslist from './Usuarioslist.vue'; // Import Usuarioslist
import OUOForm from './OUOForm.vue';

const router = useRouter();
const store = useAsignacionOuoStore();
const dt = ref(null); // Reference to the DataTable component

const showAssignProcessesModal = ref(false); // New ref for modal visibility
const showAssignUsersModal = ref(false); // New ref for user modal visibility
const currentOuo = ref(null); // New ref to store the OUO being edited
const fileInput = ref(null);

const downloadTemplate = () => {
    store.downloadTemplate();
};

const triggerFileUpload = () => {
    fileInput.value.click();
};

const handleFileUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    Swal.fire({
        title: '¿Importar OUOs?',
        text: 'Se importarán las Unidades Orgánicas desde el archivo seleccionado.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Sí, importar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('file', file);

            try {
                await store.importOuos(formData);
                Swal.fire('Importado', 'OUOs importadas correctamente.', 'success');
            } catch (error) {
                console.error('Error importing:', error);
                Swal.fire('Error', 'Error al importar OUOs.', 'error');
            } finally {
                event.target.value = null;
            }
        } else {
            event.target.value = null;
        }
    });
};

onMounted(() => {
    store.fetchOuos();
    store.fetchOuoPadresForDropdown(); // Fetch parent OUOs for the dropdown
});

const onPage = (event) => {
    store.setPage(event.page + 1);
    store.setPerPage(event.rows);
};

const applyFilters = () => {
    store.pagination.currentPage = 1; // Reset to first page on filter application
    store.fetchOuos();
};

const resetFilters = () => {
    store.resetFilters();
    store.fetchOuos();
};

const assignUsers = (ouo) => {
    console.log('Asignar usuarios a OUO:', ouo);
    currentOuo.value = ouo;
    showAssignUsersModal.value = true;
};

const assignProcesses = (ouo) => {
    console.log('Asignar procesos a OUO:', ouo);
    currentOuo.value = ouo;
    showAssignProcessesModal.value = true;
};

const closeAssignProcessesModal = () => {
    showAssignProcessesModal.value = false;
    currentOuo.value = null;
    // Optionally, re-fetch OUOs to update process count if needed
    // store.fetchOuos();
};

const closeAssignUsersModal = () => {
    showAssignUsersModal.value = false;
    currentOuo.value = null;
    // Optionally, re-fetch OUOs to update user count if needed
    // store.fetchOuos();
};

const currentOuoForForm = ref(null);
const ouoFormRef = ref(null);

const openCreateModal = () => {
    currentOuoForForm.value = null;
    ouoFormRef.value.open();
};

const openEditModal = (ouo) => {
    currentOuoForForm.value = ouo;
    ouoFormRef.value.open();
};

const closeOuoFormModal = () => {
    currentOuoForForm.value = null;
};

const handleOuoSaved = () => {
    store.fetchOuos();
    closeOuoFormModal();
};

const handleProcessesUpdated = () => {
    // This method will be called when Procesoslist.vue emits an event
    // indicating that processes have been updated/saved.
    // Refresh the OUOs to update the process count without closing the modal.
    store.fetchOuos();
};

const handleUsersUpdated = () => {
    // This method will be called when Usuarioslist.vue emits an event
    // indicating that users have been updated/saved.
    // Refresh the OUOs to update the user count without closing the modal.
    store.fetchOuos();
};
</script>

<style scoped>
/* Estilos específicos del componente aquí */
</style>
