<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">Gestión de Facilitadores</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-success btn-sm" @click="openFormModal()">
                                <i class="fas fa-plus"></i> Nuevo Facilitador
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Cargo</th>
                                        <th>Estado</th>
                                        <th>Procesos Asignados</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="facilitadorStore.loading" class="text-center">
                                        <td colspan="6">Cargando facilitadores...</td>
                                    </tr>
                                    <tr v-else-if="!facilitadorStore.facilitadores.length" class="text-center">
                                        <td colspan="6">No hay facilitadores registrados.</td>
                                    </tr>
                                    <tr v-else v-for="facilitador in facilitadorStore.facilitadores"
                                        :key="facilitador.id">
                                        <td>{{ facilitador.id }}</td>
                                        <td>{{ facilitador.user.name }}</td>
                                        <td>{{ facilitador.cargo }}</td>
                                        <td>
                                            <span
                                                :class="{ 'badge badge-success': facilitador.estado === 'activo', 'badge badge-danger': facilitador.estado === 'inactivo' }">
                                                {{ facilitador.estado }}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-for="proceso in facilitador.procesos" :key="proceso.id"
                                                class="badge badge-info mr-1">
                                                {{ proceso.proceso_nombre }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" title="Editar Facilitador" class="mr-3 d-inline-block"
                                                @click="openFormModal(facilitador)">
                                                <i class="fas fa-pencil-alt text-warning fa-lg"></i>
                                            </a>

                                            <button class="btn btn-danger btn-sm"
                                                @click="deleteFacilitador(facilitador.id)">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <FacilitadorForm v-if="facilitadorStore.isFormModalOpen" />
</template>

<script setup>
import { onMounted } from 'vue';
import { useFacilitadorStore } from '@/stores/facilitadorStore';
import FacilitadorForm from './FacilitadorForm.vue';

const facilitadorStore = useFacilitadorStore();

onMounted(() => {
    facilitadorStore.fetchFacilitadores();
});

const openFormModal = (facilitador) => {
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
