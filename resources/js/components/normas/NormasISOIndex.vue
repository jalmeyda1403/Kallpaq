<template>
    <div class="container-fluid">
        <div class="content-header p-0 mb-3">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Normas Auditables</h1>
                </div>
            </div>
        </div>

        <div class="card card-outline card-danger shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-secondary font-weight-bold">Listado de Normas</h5>
                    <button class="btn btn-sm btn-dark shadow-sm" @click="openModal">
                        <i class="fas fa-plus mr-1"></i> Nueva Norma
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <DataTable :value="normas" :paginator="true" :rows="10" responsiveLayout="scroll" class="p-datatable-sm"
                    :loading="loading" stripe>
                    <Column field="nombre" header="Norma" sortable></Column>
                    <Column field="descripcion" header="Descripción"></Column>
                    <Column field="requisitos_count" header="Requisitos" sortable class="text-center">
                        <template #body="slotProps">
                            <span class="badge badge-info">{{ slotProps.data.requisitos_count }}</span>
                        </template>
                    </Column>
                    <Column header="Acciones" class="text-center" style="width: 100px;">
                        <template #body="slotProps">
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-sm btn-outline-primary border-0 mr-1"
                                    @click="editNorma(slotProps.data)" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger border-0"
                                    @click="confirmDelete(slotProps.data)" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </template>
                    </Column>
                    <template #empty>
                        <div class="text-center p-3 text-muted">
                            No hay normas registradas.
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>

        <NormasISOForm ref="formModal" @saved="fetchNormas" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import NormasISOForm from './NormasISOForm.vue';

const normas = ref([]);
const loading = ref(false);
const formModal = ref(null);

const fetchNormas = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/auditoria/normas');
        normas.value = response.data;
    } catch (e) {
        console.error("Error fetching normas", e);
    } finally {
        loading.value = false;
    }
};

const openModal = () => {
    formModal.value.open();
};

const editNorma = (norma) => {
    formModal.value.open(norma);
};

const confirmDelete = (norma) => {
    Swal.fire({
        title: '¿Eliminar Norma?',
        text: `Se eliminará la norma ${norma.nombre} y sus requisitos.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.delete(`/api/auditoria/normas/${norma.id}`);
                fetchNormas();
                Swal.fire('Eliminada', 'La norma ha sido eliminada.', 'success');
            } catch (e) {
                Swal.fire('Error', 'No se pudo eliminar la norma.', 'error');
            }
        }
    });
};

onMounted(() => {
    fetchNormas();
});
</script>

<style scoped>
.btn-dark {
    background-color: #343a40;
    border-color: #343a40;
}
</style>
