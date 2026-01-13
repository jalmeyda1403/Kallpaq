<template>
    <div ref="modalRef" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-history mr-2"></i> Historial de Participación: {{ auditorName }}
                    </h5>
                    <button type="button" class="close text-white" @click="close" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <!-- Auditor Info Card -->
                    <div class="card mb-4 border-0 shadow-sm rounded-lg overflow-hidden">
                        <div class="card-body bg-white">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="avatar-large">
                                        {{ auditorName.charAt(0) }}
                                    </div>
                                </div>
                                <div class="col">
                                    <h4 class="mb-1 font-weight-bold">{{ auditorName }}</h4>
                                    <p class="text-muted mb-0"><i class="fas fa-envelope mr-1"></i> {{ auditorEmail }}
                                    </p>
                                </div>
                                <div class="col-auto text-right">
                                    <div class="stat-box px-3 py-2 bg-light rounded text-center">
                                        <div class="h3 mb-0 font-weight-bold text-danger">{{ historial.length }}</div>
                                        <small class="text-muted text-uppercase font-weight-bold">Auditorías</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- History Table -->
                    <div class="card border-0 shadow-sm rounded-lg">
                        <div class="card-body p-0">
                            <DataTable :value="historial" stripedRows responsiveLayout="stack" breakpoint="960px"
                                class="p-datatable-sm overflow-hidden rounded-lg" :loading="loading">
                                <Column field="codigo" header="Cod. Auditoría" style="width: 15%">
                                    <template #body="{ data }">
                                        <span class="badge badge-secondary py-1 px-2">{{ data.codigo }}</span>
                                    </template>
                                </Column>
                                <Column field="procesos" header="Proceso(s) Auditado(s)" style="width: 30%">
                                    <template #body="{ data }">
                                        <div class="small font-weight-bold">{{ data.procesos || 'N/A' }}</div>
                                    </template>
                                </Column>
                                <Column field="rol" header="Rol Desempeñado" style="width: 15%">
                                    <template #body="{ data }">
                                        <span class="small font-weight-bold">{{ data.rol }}</span>
                                    </template>
                                </Column>
                                <Column header="Periodo de Auditoría" style="width: 20%">
                                    <template #body="{ data }">
                                        <div class="small">
                                            <i class="far fa-calendar-alt text-muted mr-1"></i>
                                            {{ formatDate(data.fecha_inicio) }} al {{ formatDate(data.fecha_fin) }}
                                        </div>
                                    </template>
                                </Column>
                                <Column header="Estado" style="width: 10%">
                                    <template #body>
                                        <span class="badge badge-success px-2">Finalizado</span>
                                    </template>
                                </Column>

                                <template #empty>
                                    <div class="text-center p-5">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">No se encontraron participaciones previas en
                                            auditorías.</p>
                                    </div>
                                </template>
                            </DataTable>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top-0">
                    <button type="button" class="btn btn-secondary px-4 shadow-sm" @click="close">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const modalRef = ref(null);
const bootstrapModal = ref(null);
const loading = ref(false);
const auditorName = ref('');
const auditorEmail = ref('');
const historial = ref([]);

const open = async (auditorId) => {
    loading.value = true;
    if (!bootstrapModal.value) {
        bootstrapModal.value = new bootstrap.Modal(modalRef.value);
    }
    bootstrapModal.value.show();

    try {
        const response = await axios.get(`/api/auditores/${auditorId}`);
        auditorName.value = response.data.auditor.user.name;
        auditorEmail.value = response.data.auditor.user.email;
        historial.value = response.data.historial;
    } catch (e) {
        console.error("Error loading auditor detail", e);
    } finally {
        loading.value = false;
    }
};

const close = () => {
    if (bootstrapModal.value) {
        bootstrapModal.value.hide();
    }
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

defineExpose({ open });
</script>

<style scoped>
.avatar-large {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #d32f2f 0%, #b71c1c 100%);
    color: white;
    font-size: 2rem;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.stat-box {
    min-width: 100px;
}

.modal-header.bg-dark {
    background-color: #2c3e50 !important;
}
</style>
