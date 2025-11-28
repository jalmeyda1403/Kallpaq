<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><router-link :to="{ name: 'documentos.index' }">Inicio</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Asignación de Usuarios y Procesos a OUOs</li>
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
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <form @submit.prevent="applyFilters">
                                    <div class="form-row">
                                        <div class="col">
                                            <input type="text" name="search" id="search" class="form-control"
                                                placeholder="Buscar por Unidad Orgánica" v-model="store.filters.search">
                                        </div>
                                        <div class="col">
                                            <select name="ouo_padre_id" id="ouo_padre_id" class="form-control"
                                                v-model="store.filters.ouo_padre_id">
                                                <option :value="null">Todas las OUOs Padre</option>
                                                <option v-for="ouoPadre in store.ouoPadresForDropdown"
                                                    :key="ouoPadre.id" :value="ouoPadre.id">
                                                    {{ ouoPadre.ouo_nombre }}
                                                </option>
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
                        <DataTable ref="dt" :value="store.ouos" :lazy="true" :paginator="true" :rows="20"
                            :totalRecords="store.pagination.total"
                            :first="(store.pagination.currentPage - 1) * store.pagination.perPage" @page="onPage"
                            :rowsPerPageOptions="[5, 10, 20, 50]" dataKey="id"
                            :globalFilterFields="['id', 'ouo_nombre', 'ouo_padre_nombre']"
                            :loading="store.loading">
                            <Column field="id" header="ID" style="width:5%"></Column>
                            <Column field="ouo_nombre" header="Nombre OUO" sortable style="width:30%"></Column>
                            <Column field="ouo_padre_nombre" header="OUO Padre" sortable style="width:30%"></Column>
                            <Column field="procesos_count" header="# Procesos" sortable
                                style="width:10%; text-align: center;"></Column>
                            <Column field="users_count" header="# Usuarios" sortable
                                style="width:10%; text-align: center;"></Column>
                            <Column header="Acciones" :exportable="false" style="width:10%">
                                <template #body="{ data }">
                                    <a href="#" title="Asignar Usuarios"
                                        class="mr-1 d-inline-block btn-modal-trigger p-2"
                                        @click.prevent="assignUsers(data)">
                                        <i class="fas fa-user fa-lg text-primary"></i>
                                    </a>
                                    <a href="#" title="Asignar Procesos"
                                        class="mr-1 d-inline-block btn-modal-trigger p-2"
                                        @click.prevent="assignProcesses(data)">
                                        <i class="fas fa-cogs fa-lg text-dark"></i>
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
    </teleport>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAsignacionOuoStore } from '@/stores/asignacionOuoStore';

// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

// Custom Components
import Procesoslist from './Procesoslist.vue';
import Usuarioslist from './Usuarioslist.vue'; // Import Usuarioslist

const router = useRouter();
const store = useAsignacionOuoStore();
const dt = ref(null); // Reference to the DataTable component

const showAssignProcessesModal = ref(false); // New ref for modal visibility
const showAssignUsersModal = ref(false); // New ref for user modal visibility
const currentOuo = ref(null); // New ref to store the OUO being edited

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

const handleProcessesUpdated = () => {
    // This method will be called when Procesoslist.vue emits an event
    // indicating that processes have been updated/saved.
    // Here you might want to re-fetch the OUOs to update the process count.
    store.fetchOuos();
    closeAssignProcessesModal();
};

const handleUsersUpdated = () => {
    // This method will be called when Usuarioslist.vue emits an event
    // indicating that users have been updated/saved.
    // Here you might want to re-fetch the OUOs to update the user count.
    store.fetchOuos();
    closeAssignUsersModal();
};
</script>

<style scoped>
/* Estilos específicos del componente aquí */

/* Custom loader styles - remove opacity and change color to red */
/* Remove the semi-transparent overlay that dims the table content during loading */
.p-datatable-loading-overlay {
    background: rgba(255, 255, 255, 0) !important;
    /* Make background completely transparent */
}

/* Change the loader icon to red */
.p-datatable-loading-icon {
    color: red !important;
    font-size: 2rem !important;
}
</style>
