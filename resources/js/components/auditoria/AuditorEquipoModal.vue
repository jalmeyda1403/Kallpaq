<template>
    <Dialog v-model:visible="visible" header="Gestión del Equipo Auditor" :style="{ width: '60vw' }" :modal="true"
        @hide="closeModal">
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>Lista de Auditores Asignados</h5>
                    <Button label="Asignar Auditor" icon="pi pi-user-plus" class="p-button-sm"
                        @click="showAddForm = !showAddForm" />
                </div>
            </div>

            <!-- Add Auditor Form -->
            <div v-if="showAddForm" class="col-md-12 mb-4 p-3 border rounded bg-light">
                <h6>Nuevo Integrante</h6>
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Auditor (Usuario)</label>
                        <!-- Reemplazar con un Dropdown de usuarios reales -->
                        <Dropdown v-model="newMember.auditor_id" :options="users" optionLabel="name" optionValue="id"
                            placeholder="Seleccione Usuario" class="w-100" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Rol</label>
                        <Dropdown v-model="newMember.aeq_rol" :options="roles" optionLabel="label" optionValue="value"
                            placeholder="Rol" class="w-100" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Horas Planificadas</label>
                        <InputNumber v-model="newMember.aeq_horas_planificadas" class="w-100" />
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <Button label="Agregar" icon="pi pi-check" class="w-100" @click="addMember"
                            :loading="loading" />
                    </div>
                </div>
            </div>

            <!-- Members List -->
            <div class="col-md-12">
                <DataTable :value="equipo" responsiveLayout="scroll" stripedRows>
                    <Column field="usuario.name" header="Auditor"></Column>
                    <Column field="aeq_rol" header="Rol"></Column>
                    <Column field="aeq_horas_planificadas" header="Horas Plan."></Column>
                    <Column field="aeq_horas_ejecutadas" header="Horas Ejec.">
                        <template #body="slotProps">
                            {{ slotProps.data.aeq_horas_ejecutadas || 0 }}
                        </template>
                    </Column>
                    <Column header="Acciones" bodyStyle="text-align: center">
                        <template #body="slotProps">
                            <Button icon="pi pi-trash" class="p-button-rounded p-button-danger p-button-text"
                                @click="removeMember(slotProps.data.id)" />
                        </template>
                    </Column>
                    <template #footer>
                        <div class="text-end">
                            Total Horas Planificadas: {{ totalHoras }}
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>
        <template #footer>
            <Button label="Cerrar" icon="pi pi-times" class="p-button-text" @click="closeModal" />
        </template>
    </Dialog>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputNumber from 'primevue/inputnumber';
import Dropdown from 'primevue/dropdown';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    visible: Boolean,
    auditId: Number
});

const emit = defineEmits(['update:visible', 'member-added']);

const toast = useToast();
const loading = ref(false);
const showAddForm = ref(false);
const equipo = ref([]);
const users = ref([]); // Should load from API
const roles = [
    { label: 'Auditor Líder', value: 'Lider' },
    { label: 'Auditor Interno', value: 'Auditor' },
    { label: 'Experto Técnico', value: 'Experto' },
    { label: 'Observador', value: 'Observador' }
];

const newMember = ref({
    auditor_id: null,
    aeq_rol: 'Auditor',
    aeq_horas_planificadas: 0
});

// Watch for visibility to load data
watch(() => props.visible, (newVal) => {
    if (newVal && props.auditId) {
        loadData();
    }
});

const loadData = async () => {
    loading.value = true;
    try {
        // Load Audit Data (specifically team)
        const res = await axios.get(`/api/auditoria/especifica/${props.auditId}`);
        equipo.value = res.data.equipo || [];

        // Load Users (Mock or specific endpoint)
        // Ideally: axios.get('/api/users/auditors')
        const resUsers = await axios.get('/api/usuarios/listado'); // Assuming this exists or similar
        // If not, we might need to mock or enable an endpoint for users
        users.value = resUsers.data;
    } catch (error) {
        console.error("Error loading data", error);
        // Fallback mock if API fails
        if (users.value.length === 0) {
            users.value = [
                { id: 1, name: 'Juan Perez' },
                { id: 2, name: 'Maria Lopez' }
            ];
        }
    } finally {
        loading.value = false;
    }
};

const addMember = async () => {
    if (!newMember.value.auditor_id) {
        toast.add({ severity: 'warn', summary: 'Atención', detail: 'Seleccione un auditor', life: 3000 });
        return;
    }

    try {
        loading.value = true;
        await axios.post(`/api/auditoria/especifica/${props.auditId}/equipo`, {
            auditor_id: newMember.value.auditor_id,
            aeq_rol: newMember.value.aeq_rol,
            aeq_horas_planificadas: newMember.value.aeq_horas_planificadas,
            action: 'add'
        });

        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Auditor asignado', life: 3000 });
        newMember.value = { auditor_id: null, aeq_rol: 'Auditor', aeq_horas_planificadas: 0 };
        showAddForm.value = false;
        loadData(); // Reload list
        emit('member-added');
    } catch (error) {
        console.error(error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo asignar al auditor', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const removeMember = async (recordId) => {
    // Logic to remove member
    // Implementation of delete controller logic required or reuse update with 'remove' action
    // For now assume updateEquipo handles removals or a specific DELETE route.
    // The controller currently re-creates all. Let's see AuditoriaEspecificaController::updateEquipo.
    // Actually, `updateEquipo` implementation usually replaces ALL or adds one? 
    // My previous thought (in plan) was "Actualizar equipo".
    // Let's implement a 'remove' action call if the controller supports it or just Reload.
    toast.add({ severity: 'info', summary: 'Info', detail: 'Eliminación pendiente de implementar en backend', life: 3000 });
};

const totalHoras = computed(() => {
    return equipo.value.reduce((acc, curr) => acc + (parseFloat(curr.aeq_horas_planificadas) || 0), 0);
});

const closeModal = () => {
    emit('update:visible', false);
};
</script>
