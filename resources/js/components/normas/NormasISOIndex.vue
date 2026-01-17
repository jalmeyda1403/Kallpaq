<template>
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-light py-2 px-3 rounded">
                <li class="breadcrumb-item"><router-link to="/">Inicio</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Normas Auditables</li>
            </ol>
        </nav>

        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6 text-md-left">
                        <h3 class="card-title mb-0">Listado de Normas Auditables</h3>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <button class="btn btn-primary btn-sm ml-1" @click="openModal">
                            <i class="fas fa-plus-circle"></i> Nueva Norma
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <DataTable :value="normas" :paginator="true" :rows="10" :loading="loading" tableStyle="min-width: 50rem"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    :rowsPerPageOptions="[10, 25, 50]"
                    currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registros"
                    class="p-datatable-sm p-datatable-striped p-datatable-hoverable-rows">

                    <Column field="nombre" header="Norma" sortable style="width:20%">
                        <template #body="slotProps">
                            <span class="font-weight-bold text-dark">{{ slotProps.data.nombre }}</span>
                        </template>
                    </Column>
                    <Column field="descripcion" header="Descripción" style="width:50%">
                        <template #body="slotProps">
                            <span class="text-muted" style="font-size: 0.70rem;">{{ slotProps.data.descripcion ||
                                'Sin descripción' }}</span>
                        </template>
                    </Column>
                    <Column field="requisitos_count" header="Requisitos" sortable
                        style="width:15%; text-align: center;">
                        <template #body="slotProps">
                            <span class="badge badge-info px-3 py-1">{{ slotProps.data.requisitos_count }}</span>
                        </template>
                    </Column>
                    <Column header="Acciones" class="text-center" style="width:15%">
                        <template #body="slotProps">
                            <div class="d-flex justify-content-center action-buttons">
                                <button class="btn btn-outline-warning btn-xs mr-1" @click="editNorma(slotProps.data)"
                                    title="Editar">
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
