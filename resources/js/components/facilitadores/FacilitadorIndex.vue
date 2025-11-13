<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a>Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Gestión de Facilitadores</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-md-left">
                                <h3 class="card-title mb-0">Lista de Facilitadores</h3>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <button type="button" class="btn btn-primary btn-sm" @click="openFormModal()">
                                    <i class="fas fa-plus"></i> Nuevo Facilitador
                                </button>
                            </div>
                        </div>
                        <hr>
                        <form @submit.prevent="applyServerFilters">
                            <div class="form-row">
                                <div class="col">
                                    <input type="text" class="form-control" placeholder="Buscar por nombre..." v-model="serverFilters.name">
                                </div>
                                <div class="col">
                                    <select class="form-control" v-model="serverFilters.estado">
                                        <option value="">Todos los estados</option>
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn bg-dark">
                                        <i class="fas fa-search"></i> Filtrar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        <DataTable :value="facilitadorStore.facilitadores" :paginator="true" :rows="10"
                            dataKey="id" v-model:filters="filters" filterDisplay="menu" :loading="facilitadorStore.loading"
                            :globalFilterFields="['id', 'user.name', 'cargo', 'estado']">
                            <template #empty>
                                No hay facilitadores registrados.
                            </template>
                            <template #loading>
                                Cargando facilitadores...
                            </template>
                            <Column field="id" header="ID" sortable style="width: 10%"></Column>
                            <Column field="user.name" header="Usuario" sortable filterField="user.name" style="width: 25%">
                                <template #body="{ data }">
                                    {{ data.user.name }}
                                </template>
                                <template #filter="{ filterModel, filterCallback }">
                                    <InputText v-model="filterModel.value" @keydown.enter="filterCallback()" class="p-column-filter" placeholder="Buscar por usuario"/>
                                </template>
                            </Column>
                            <Column field="cargo" header="Cargo" sortable style="width: 15%"></Column>
                            <Column field="estado" header="Estado" sortable style="width: 15%">
                                <template #body="{ data }">
                                    <span :class="['badge', data.estado === 'activo' ? 'badge-success' : 'badge-danger']">
                                        {{ data.estado }}
                                    </span>
                                </template>
                            </Column>
                            <Column field="procesos" header="Procesos Asignados" style="width: 25%">
                                <template #body="{ data }">
                                    <span v-for="proceso in data.procesos" :key="proceso.id" class="badge badge-info mr-1">
                                        {{ proceso.proceso_nombre }}
                                    </span>
                                </template>
                            </Column>
                            <Column header="Acciones" style="width: 10%">
                                <template #body="{ data }">
                                    <a href="#" title="Editar Facilitador" class="mr-3 d-inline-block" @click.prevent="openFormModal(data)">
                                        <i class="fas fa-pencil-alt text-warning fa-lg"></i>
                                    </a>
                                    <a href="#" title="Eliminar Facilitador" class="d-inline-block" @click.prevent="deleteFacilitador(data.id)">
                                        <i class="fas fa-trash text-danger fa-lg"></i>
                                    </a>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <FacilitadorForm v-if="facilitadorStore.isFormModalOpen" />
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useFacilitadorStore } from '@/stores/facilitadorStore';
import FacilitadorForm from './FacilitadorForm.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import { FilterMatchMode } from 'primevue/api';

const facilitadorStore = useFacilitadorStore();

const serverFilters = ref({
    name: '',
    estado: '',
});

const filters = ref({
    'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
    'user.name': { value: null, matchMode: FilterMatchMode.STARTS_WITH },
});

const applyServerFilters = () => {
    facilitadorStore.fetchFacilitadores(serverFilters.value);
};

onMounted(() => {
    facilitadorStore.fetchFacilitadores();
});

const openFormModal = (facilitador = null) => {
    facilitadorStore.openFormModal(facilitador);
};

const deleteFacilitador = (id) => {
    if (confirm('¿Está seguro de que desea eliminar este facilitador?')) {
        facilitadorStore.deleteFacilitador(id);
    }
};
</script>

<style scoped>
/* Estilos específicos del componente si son necesarios */
</style>
