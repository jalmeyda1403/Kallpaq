<template>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Plan de Auditoría Específica</h3>
                    <Button label="Volver al Programa" icon="pi pi-arrow-left" class="p-button-sm p-button-secondary"
                        @click="goBack" />
                </div>
            </div>
            <div class="card-body">
                <div v-if="loading" class="text-center p-4">
                    <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
                </div>
                <div v-else>
                    <!-- Header Info -->
                    <div class="row mb-4 border-bottom pb-3">
                        <div class="col-md-8">
                            <h4>{{ auditoria.ae_objetivo }}</h4>
                            <p class="text-muted mb-1"><strong>Alcance:</strong> {{ auditoria.ae_alcance }}</p>
                            <p class="text-muted mb-0"><strong>Criterios:</strong> {{ auditoria.ae_criterios }}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="badge bg-primary fs-6 mb-2">{{ auditoria.ae_codigo }}</div>
                            <div>{{ formatDate(auditoria.ae_fecha_inicio) }} - {{ formatDate(auditoria.ae_fecha_fin) }}
                            </div>
                            <div class="mt-2">
                                <Button label="Equipo" icon="pi pi-users" class="p-button-sm p-button-outlined me-2"
                                    @click="openEquipoModal" />
                                <Button label="Evaluar" icon="pi pi-star"
                                    class="p-button-sm p-button-outlined me-2 p-button-warning"
                                    @click="openEvaluacionModal" />
                                <Button label="Guardar Agenda" icon="pi pi-save" class="p-button-sm p-button-success"
                                    @click="saveAgenda" />
                            </div>
                        </div>
                    </div>

                    <!-- Agenda Editor / Timeline -->
                    <h5 class="mb-3">Agenda / Cronograma Detallado</h5>

                    <DataTable :value="agendaItems" editMode="row" dataKey="id" v-model:editingRows="editingRows"
                        @row-edit-save="onRowEditSave" responsiveLayout="scroll" showGridlines>
                        <Column field="aea_fecha" header="Fecha">
                            <template #editor="{ data, field }">
                                <InputText v-model="data[field]" type="date" class="form-control form-control-sm" />
                            </template>
                            <template #body="slotProps">
                                {{ formatDate(slotProps.data.aea_fecha) }}
                            </template>
                        </Column>
                        <Column field="aea_hora_inicio" header="Inicio">
                            <template #editor="{ data, field }">
                                <InputText v-model="data[field]" type="time" class="form-control form-control-sm" />
                            </template>
                        </Column>
                        <Column field="aea_hora_fin" header="Fin">
                            <template #editor="{ data, field }">
                                <InputText v-model="data[field]" type="time" class="form-control form-control-sm" />
                            </template>
                        </Column>
                        <Column field="aea_actividad" header="Actividad / Proceso">
                            <template #editor="{ data, field }">
                                <InputText v-model="data[field]" class="form-control form-control-sm" />
                            </template>
                        </Column>
                        <Column field="aea_auditado" header="Auditado (Rol)">
                            <template #editor="{ data, field }">
                                <InputText v-model="data[field]" class="form-control form-control-sm" />
                            </template>
                        </Column>
                        <Column field="aea_auditor" header="Auditor">
                            <template #editor="{ data, field }">
                                <InputText v-model="data[field]" class="form-control form-control-sm" />
                            </template>
                        </Column>
                        <Column field="aea_requisito" header="Requisito / ISO">
                            <template #editor="{ data, field }">
                                <InputText v-model="data[field]" class="form-control form-control-sm" />
                            </template>
                        </Column>
                        <Column :rowEditor="true" style="width:10%; min-width:8rem" bodyStyle="text-align:center">
                        </Column>
                        <Column bodyStyle="text-align:center">
                            <template #body="slotProps">
                                <Button icon="pi pi-trash"
                                    class="p-button-rounded p-button-danger p-button-text p-button-sm"
                                    @click="deleteRow(slotProps.index)" />
                            </template>
                        </Column>
                    </DataTable>

                    <div class="mt-3">
                        <Button label="Agregar Actividad" icon="pi pi-plus" class="p-button-sm p-button-outlined"
                            @click="addRow" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Equipo Modal -->
        <AuditorEquipoModal v-if="equipoModalVisible" :visible="equipoModalVisible" :auditId="Number(aeId)"
            @update:visible="equipoModalVisible = $event" @member-added="loadAuditoria" />

        <!-- Evaluacion Modal -->
        <EvaluacionAuditorModal v-if="evaluacionModalVisible" :visible="evaluacionModalVisible" :auditId="Number(aeId)"
            :auditores="auditoria.equipo || []" @update:visible="evaluacionModalVisible = $event" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Dialog from 'primevue/dialog';
import AuditorEquipoModal from './AuditorEquipoModal.vue';
import EvaluacionAuditorModal from './EvaluacionAuditorModal.vue';

const route = useRoute();
const router = useRouter();
const toast = useToast();

const loading = ref(false);
const auditoria = ref({});
const agendaItems = ref([]);
const editingRows = ref([]);
const equipoModalVisible = ref(false);
const evaluacionModalVisible = ref(false);

const aeId = route.params.id;

onMounted(() => {
    loadAuditoria();
});

const loadAuditoria = async () => {
    loading.value = true;
    try {
        const res = await axios.get(`/api/auditoria/especifica/${aeId}`);
        auditoria.value = res.data;
        agendaItems.value = res.data.agenda || [];
    } catch (e) {
        console.error(e);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar la auditoría', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString();
}

const addRow = () => {
    const newId = Math.random(); // Temp ID
    agendaItems.value.push({
        id: newId,
        aea_fecha: auditoria.value.ae_fecha_inicio,
        aea_hora_inicio: '09:00',
        aea_hora_fin: '10:00',
        aea_actividad: '',
        aea_auditado: '',
        aea_auditor: '',
        aea_requisito: ''
    });
};

const deleteRow = (index) => {
    agendaItems.value.splice(index, 1);
};

const onRowEditSave = (event) => {
    let { newData, index } = event;
    agendaItems.value[index] = newData;
};

const saveAgenda = async () => {
    try {
        await axios.post(`/api/auditoria/especifica/${aeId}/agenda`, {
            agenda: agendaItems.value
        });
        toast.add({ severity: 'success', summary: 'Guardado', detail: 'Agenda actualizada', life: 3000 });
        loadAuditoria();
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Falló al guardar', life: 3000 });
    }
};

const goBack = () => {
    router.go(-1);
};

const openEquipoModal = () => {
    equipoModalVisible.value = true;
};

const openEvaluacionModal = () => {
    evaluacionModalVisible.value = true;
};
</script>
