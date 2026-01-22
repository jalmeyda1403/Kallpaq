<template>
    <div>
        <div class="d-flex align-items-center justify-content-between my-4">
            <div class="d-flex align-items-center flex-grow-1">
                <div class="input-group mr-3" style="max-width: 400px;">
                    <input type="text" class="form-control" placeholder="Seleccione el Proceso a Asociar..."
                        v-model="selectedProcess.nombre" readonly @click="openProcesoModal" />
                    <div class="input-group-append">
                        <button type="button" class="btn btn-dark shadow-sm" @click="openProcesoModal"
                            title="Buscar Proceso">
                            <i class="fas fa-search"></i> Seleccionar
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-success shadow px-4 font-weight-bold"
                :disabled="saving || assignedProcesses.length === 0" @click="saveAllChanges">
                <i class="fas" :class="saving ? 'fa-spinner fa-spin' : 'fa-save'"></i> Guardar Cambios
            </button>
        </div>

        <!-- PrimeVue DataTable for Assigned Processes -->
        <DataTable :value="assignedProcesses" dataKey="id" responsiveLayout="scroll"
            class="p-datatable-sm shadow-sm rounded border" :loading="loading"
            :rowClass="data => data.isNew ? 'table-success' : ''">
            <template #empty>
                <div class="text-center p-4">
                    <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay procesos asignados a esta unidad orgánica.</p>
                </div>
            </template>
            <template #loading>
                <div class="d-flex justify-content-center align-items-center p-5">
                    <div class="spinner-grow text-danger" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            </template>
            <Column field="codigo" header="Código" style="width:10%"
                headerClass="small font-weight-bold text-uppercase"></Column>
            <Column field="nombre" header="Nombre" style="width:25%"
                headerClass="small font-weight-bold text-uppercase">
                <template #body="{ data }">
                    <div class="font-weight-bold">{{ data.nombre }}</div>
                </template>
            </Column>

            <!-- Responsabilidades columns -->
            <Column header="P" style="width:5%; text-align: center;" headerStyle="text-align: center;"
                headerClass="small font-weight-bold text-uppercase" title="Propietario">
                <template #body="{ data }">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" :id="`p-${data.id}`"
                            v-model="data.propietario">
                        <label class="custom-control-label" :for="`p-${data.id}`"></label>
                    </div>
                </template>
            </Column>
            <Column header="D" style="width:5%; text-align: center;" headerClass="small font-weight-bold text-uppercase"
                title="Delegado">
                <template #body="{ data }">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" :id="`d-${data.id}`"
                            v-model="data.delegado">
                        <label class="custom-control-label" :for="`d-${data.id}`"></label>
                    </div>
                </template>
            </Column>
            <Column header="E" style="width:5%; text-align: center;" headerStyle="text-align: center;"
                headerClass="small font-weight-bold text-uppercase" title="Ejecutor">
                <template #body="{ data }">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" :id="`e-${data.id}`"
                            v-model="data.ejecutor">
                        <label class="custom-control-label" :for="`e-${data.id}`"></label>
                    </div>
                </template>
            </Column>

            <!-- Individual Columns for Management Systems -->
            <Column v-for="system in managementSystems" :key="system.value" :header="system.label"
                style="width:5%; text-align: center;" headerStyle="text-align: center;"
                headerClass="small font-weight-bold text-uppercase">
                <template #body="{ data }">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" :id="`sys-${data.id}-${system.value}`"
                            v-model="data[system.value]">
                        <label class="custom-control-label border-danger"
                            :for="`sys-${data.id}-${system.value}`"></label>
                    </div>
                </template>
            </Column>

            <Column header="Acciones" style="width:10%; text-align: center;" headerStyle="text-align: center;"
                headerClass="small font-weight-bold text-uppercase">
                <template #body="{ index }">
                    <button class="btn btn-outline-danger btn-sm rounded-circle shadow-sm"
                        @click="removeProcessLocal(index)" title="Quitar">
                        <i class="fas fa-times"></i>
                    </button>
                </template>
            </Column>
        </DataTable>

        <ModalHijo ref="procesoModal" :fetch-url="proceso_route" target-id="id" target-desc="descripcion"
            @update-target="handleProcesoSelection">

        </ModalHijo>
    </div>
</template>

<script>
import { ref, reactive, onMounted, watch } from 'vue';
import { route } from 'ziggy-js';
import ModalHijo from '../generales/ModalHijo.vue';
import axios from 'axios';
import Swal from 'sweetalert2';
// PrimeVue Imports
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
// Removed BlockUI and ProgressSpinner imports
// import BlockUI from 'primevue/blockui'; 
// import ProgressSpinner from 'primevue/progressspinner'; 


export default {
    name: 'Procesoslist',
    props: {
        ouo: {
            type: Object,
            required: true
        }
    },
    components: {
        ModalHijo, // Register ModalHijo
        DataTable,
        Column,
        // Removed BlockUI and ProgressSpinner registration
        // BlockUI, 
        // ProgressSpinner 
    },
    setup(props, { emit }) {
        const procesoModal = ref(null);
        const selectedProcess = ref({ id: null, nombre: '', codigo: '' });
        const assignedProcesses = reactive([]);
        const loading = ref(true);
        const saving = ref(false);

        const proceso_route = route('procesos.buscar');

        const managementSystems = [
            { label: 'SGC', value: 'sgc' },
            { label: 'SGAS', value: 'sgas' },
            { label: 'SGCM', value: 'sgcm' },
            { label: 'SGSI', value: 'sgsi' },
            { label: 'SGCO', value: 'sgco' },
        ];

        const fetchAssignedProcesses = async () => {
            if (!props.ouo || !props.ouo.id) {
                assignedProcesses.splice(0);
                return;
            }
            loading.value = true;
            try {
                const response = await axios.get(route('ouos.procesos.index', props.ouo.id));
                assignedProcesses.splice(0);
                response.data.forEach(p => {
                    assignedProcesses.push({
                        id: p.id,
                        codigo: p.cod_proceso,
                        nombre: p.proceso_nombre,
                        propietario: !!p.pivot.propietario,
                        delegado: !!p.pivot.delegado,
                        ejecutor: !!p.pivot.ejecutor,
                        sgc: !!p.pivot.sgc,
                        sgas: !!p.pivot.sgas,
                        sgcm: !!p.pivot.sgcm,
                        sgsi: !!p.pivot.sgsi,
                        sgco: !!p.pivot.sgco,
                        isNew: false
                    });
                });
            } catch (error) {
                console.error('Error fetching assigned processes:', error);
                Swal.fire('Error', 'Error al cargar los procesos.', 'error');
            } finally {
                loading.value = false;
            }
        };

        onMounted(() => {
            fetchAssignedProcesses();
        });

        watch(() => props.ouo, (newOuo) => {
            if (newOuo) fetchAssignedProcesses();
        });

        const openProcesoModal = () => {
            procesoModal.value.open();
        };

        const handleProcesoSelection = (data) => {
            const processId = data.idValue;
            const isDuplicate = assignedProcesses.some(p => p.id === processId);

            if (isDuplicate) {
                Swal.fire('Aviso', 'Este proceso ya está en la lista.', 'info');
                return;
            }

            const descParts = data.descValue.split(' - ');
            const codigo = descParts.length > 1 ? descParts[0] : '';
            const nombre = descParts.length > 1 ? descParts.slice(1).join(' - ') : data.descValue;

            assignedProcesses.unshift({
                id: processId,
                codigo: codigo || 'N/A',
                nombre: nombre,
                propietario: false,
                delegado: false,
                ejecutor: false,
                sgc: false,
                sgas: false,
                sgcm: false,
                sgsi: false,
                sgco: false,
                isNew: true
            });

            selectedProcess.value = { id: null, nombre: '', codigo: '' };
        };

        const removeProcessLocal = (index) => {
            assignedProcesses.splice(index, 1);
        };

        const saveAllChanges = async () => {
            saving.value = true;
            try {
                const payload = assignedProcesses.map(p => ({
                    id: p.id,
                    propietario: p.propietario,
                    delegado: p.delegado,
                    ejecutor: p.ejecutor,
                    sgc: p.sgc,
                    sgas: p.sgas,
                    sgcm: p.sgcm,
                    sgsi: p.sgsi,
                    sgco: p.sgco,
                }));

                await axios.post(route('ouos.procesos.sync', props.ouo.id), {
                    procesos: payload
                });

                Swal.fire('Éxito', 'Todos los procesos han sido sincronizados.', 'success');
                await fetchAssignedProcesses();
                emit('processes-updated');
            } catch (error) {
                console.error('Error saving processes:', error);
                Swal.fire('Error', 'No se pudieron guardar las asignaciones de procesos.', 'error');
            } finally {
                saving.value = false;
            }
        };

        return {
            procesoModal,
            selectedProcess,
            assignedProcesses,
            loading,
            saving,
            proceso_route,
            managementSystems,
            openProcesoModal,
            handleProcesoSelection,
            removeProcessLocal,
            saveAllChanges,
        };
    },
};
</script>

<style scoped>
/* Add any specific styles for this component here */
.p-datatable .p-datatable-tbody>tr>td {
    padding: 1.5rem 1rem;
    margin-bottom: 1rem;
    /* Further increased vertical padding */
}

/* Ensure form-check-inline elements have some margin for better spacing */
.form-check-inline {
    margin-right: 1rem;
    /* Increased margin */
    margin-bottom: 0.5rem;
    /* Add some vertical margin for wrapping */
}

/* Adjust button margins for better spacing */
.btn-sm {
    margin-left: 0.25rem;
    margin-right: 0.25rem;
}
</style>
