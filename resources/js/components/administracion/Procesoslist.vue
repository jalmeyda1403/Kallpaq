<template>
    <div>
        <div class="d-flex align-items-center my-4">
            <div class="input-group mr-3">
                <input type="hidden" v-model="selectedProcess.id" />
                <input type="text" class="form-control" placeholder="Seleccione el Proceso a Asociar"
                    v-model="selectedProcess.nombre" readonly />
                <div class="input-group-append">
                    <button type="button" class="btn btn-dark" @click="openProcesoModal">
                        <i class="fas fa-search"></i>
                    </button>
                    <button type="button" class="btn btn-danger" :disabled="!selectedProcess.id" @click="addProcess">
                        <i class="fas fa-link"></i> Añadir
                    </button>
                </div>
            </div>
        </div>

        <!-- PrimeVue DataTable for Assigned Processes -->
        <DataTable :value="assignedProcesses" dataKey="id" responsiveLayout="scroll" class="p-datatable-sm" :loading="loading">
            <template #empty>
                No hay procesos asignados.
            </template>
            <template #loading>
                <div class="d-flex justify-content-center align-items-center p-4">
                    <div class="spinner-border text-danger" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            </template>
            <Column field="codigo" header="Código" style="width:10%"></Column>
            <Column field="nombre" header="Nombre" style="width:25%"></Column>
            <Column header="P (*)" style="width:5%; text-align: center;" headerStyle="text-align: center;">
                <template #body="{ data }">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" :id="`propietario-${data.id}`"
                            v-model="data.propietario">
                        <label class="form-check-label" :for="`propietario-${data.id}`"></label>
                    </div>
                </template>
            </Column>
            <Column header="D (*)" style="width:5%; text-align: center;" >
                <template #body="{ data }">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" :id="`delegado-${data.id}`"
                            v-model="data.delegado">
                        <label class="form-check-label" :for="`delegado-${data.id}`"></label>
                    </div>
                </template>
            </Column>
            <Column header="E (*)" style="width:5%; text-align: center;" headerStyle="text-align: center;">
                <template #body="{ data }">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" :id="`ejecutor-${data.id}`"
                            v-model="data.ejecutor">
                        <label class="form-check-label" :for="`ejecutor-${data.id}`"></label>
                    </div>
                </template>
            </Column>
            <!-- Individual Columns for Management Systems -->
            <Column v-for="system in managementSystems" :key="system.value" :header="system.label"
                style="width:5%; text-align: center;" headerStyle="text-align: center;">
                <template #body="{ data }">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox"
                            :id="`system-${data.id}-${system.value}`" v-model="data[system.value]">
                        <label class="form-check-label" :for="`system-${data.id}-${system.value}`"></label>
                    </div>
                </template>
            </Column>
            <Column header="Acciones" style="width:15%; text-align: center;" headerStyle="text-align: center;">
                <template #body="{ data, index }">
                    <button class="btn btn-danger btn-sm ml-1" @click="removeProcess(index)">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button type="button" class="btn btn-dark btn-sm ml-1" @click="updateProcessRow(data)">
                        <i class="fas fa-save"></i>
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
        const loading = ref(true); // Initialize loading state

        const proceso_route = route('procesos.buscar');

        const managementSystems = [
            { label: 'SGC', value: 'sgc' },
            { label: 'SGAS', value: 'sgas' },
            { label: 'SGCM', value: 'sgcm' },
            { label: 'SGSI', value: 'sgsi' },
            { label: 'SGCO', value: 'sgco' },
        ];

        // Fetch assigned processes when component mounts or ouo prop changes
        const fetchAssignedProcesses = async () => {
            if (!props.ouo || !props.ouo.id) {
                assignedProcesses.splice(0); // Clear array if no OUO is selected
                return;
            }
            loading.value = true; // Set loading to true before fetching
            try {
                const response = await axios.get(route('ouos.procesos.index', props.ouo.id));
                // Clear existing processes and populate with fetched data
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
                    });
                });
            } catch (error) {
                console.error('Error fetching assigned processes:', error);
                Swal.fire('Error', 'Error al cargar los procesos asignados.', 'error');
            } finally {
                loading.value = false; // Set loading to false after fetching (whether success or error)
            }
        };

        onMounted(() => {
            fetchAssignedProcesses();
        });

        watch(() => props.ouo, (newOuo, oldOuo) => {
            if (newOuo && newOuo.id !== (oldOuo ? oldOuo.id : null)) {
                fetchAssignedProcesses();
            }
        }, { deep: true });


        const openProcesoModal = () => {
            procesoModal.value.open();
        };

        const handleProcesoSelection = (data) => {
            // Parse descValue to extract codigo and nombre
            const descParts = data.descValue.split(' - ');
            const codigo = descParts.length > 1 ? descParts[0] : '';
            const nombre = descParts.length > 1 ? descParts.slice(1).join(' - ') : data.descValue;

            selectedProcess.value = { id: data.idValue, nombre: nombre, codigo: codigo };
        };

        const addProcess = async () => {
            if (selectedProcess.value.id) {
                const isDuplicate = assignedProcesses.some(
                    (p) => p.id === selectedProcess.value.id
                );

                if (!isDuplicate) {
                    const newProcess = {
                        id: selectedProcess.value.id,
                        codigo: selectedProcess.value.codigo || 'N/A',
                        nombre: selectedProcess.value.nombre,
                        propietario: false,
                        delegado: false,
                        ejecutor: false,
                        sgc: false,
                        sgas: false,
                        sgcm: false,
                        sgsi: false,
                        sgco: false,
                    };

                    // Optimistically add to local array
                    assignedProcesses.push(newProcess);
                    selectedProcess.value = { id: null, nombre: '', codigo: '' };

                    // Immediately save this single new process to the backend
                    try {
                        if (!props.ouo || !props.ouo.id) {
                            Swal.fire('Atención', 'No hay OUO seleccionada para añadir el proceso.', 'warning');
                            return;
                        }

                        // Send all current assigned processes to sync
                        await axios.post(route('ouos.procesos.sync', props.ouo.id), {
                            procesos: assignedProcesses.map(p => ({
                                id: p.id,
                                propietario: p.propietario,
                                delegado: p.delegado,
                                ejecutor: p.ejecutor,
                                sgc: p.sgc,
                                sgas: p.sgas,
                                sgcm: p.sgcm,
                                sgsi: p.sgsi,
                                sgco: p.sgco,
                            }))
                        });

                        Swal.fire('Guardado', 'Proceso añadido y guardado con éxito.', 'success');
                        await fetchAssignedProcesses(); // Refresh the list from DB
                        emit('processes-updated'); // Notify parent to update counts
                    } catch (error) {
                        console.error('Error adding and saving process:', error);
                        Swal.fire('Error', 'Error al añadir y guardar el proceso.', 'error');
                        // Revert local addition if save fails
                        assignedProcesses.splice(assignedProcesses.indexOf(newProcess), 1);
                    }
                } else {
                    Swal.fire('Atención', 'Este proceso ya ha sido añadido.', 'warning');
                }
            }
        };

        const removeProcess = async (index) => {
            Swal.fire({
                title: '¿Desvincular Proceso?',
                text: `Se desvinculará este proceso de la OUO.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, desvincular',
                cancelButtonText: 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const removedProcess = assignedProcesses[index];
                    assignedProcesses.splice(index, 1); // Remove from local array

                    try {
                        if (!props.ouo || !props.ouo.id) {
                            Swal.fire('Error', 'No hay OUO seleccionada para eliminar el proceso.', 'error');
                            return;
                        }

                        // Send the updated list of assigned processes to the sync endpoint
                        await axios.post(route('ouos.procesos.sync', props.ouo.id), {
                            procesos: assignedProcesses.map(p => ({
                                id: p.id,
                                propietario: p.propietario,
                                delegado: p.delegado,
                                ejecutor: p.ejecutor,
                                sgc: p.sgc,
                                sgas: p.sgas,
                                sgcm: p.sgcm,
                                sgsi: p.sgsi,
                                sgco: p.sgco,
                            }))
                        });

                        Swal.fire('Desvinculado', 'Proceso eliminado de la asignación con éxito.', 'success');
                        await fetchAssignedProcesses(); // Refresh the list from DB
                        emit('processes-updated'); // Notify parent to update counts
                    } catch (error) {
                        console.error('Error removing process:', error);
                        Swal.fire('Error', 'Error al eliminar el proceso de la asignación.', 'error');
                        // Revert local removal if save fails
                        assignedProcesses.splice(index, 0, removedProcess);
                    }
                }
            });
        };

        const updateProcessRow = async (processToUpdate) => {
            if (!props.ouo || !props.ouo.id || !processToUpdate || !processToUpdate.id) {
                Swal.fire('Error', 'No hay OUO o proceso seleccionado para guardar los cambios.', 'error');
                return;
            }

            try {
                await axios.put(route('ouos.procesos.updatePivot', { ouo: props.ouo.id, proceso: processToUpdate.id }), {
                    propietario: processToUpdate.propietario,
                    delegado: processToUpdate.delegado,
                    ejecutor: processToUpdate.ejecutor,
                    sgc: processToUpdate.sgc,
                    sgas: processToUpdate.sgas,
                    sgcm: processToUpdate.sgcm,
                    sgsi: processToUpdate.sgsi,
                    sgco: processToUpdate.sgco,
                });

                Swal.fire('Actualizado', 'Cambios del proceso guardados con éxito.', 'success');
                // No need to fetchAssignedProcesses as only this row was updated and local state is already correct
            } catch (error) {
                console.error('Error saving process row:', error);
                Swal.fire('Error', 'Error al guardar los cambios del proceso.', 'error');
            }
        };

        return {
            procesoModal,
            selectedProcess,
            assignedProcesses,
            loading, // Ensure loading is returned
            proceso_route,
            managementSystems,
            openProcesoModal,
            handleProcesoSelection,
            addProcess,
            removeProcess,
            updateProcessRow,
        };
    },
};
</script>

<style scoped>
/* Add any specific styles for this component here */
.p-datatable .p-datatable-tbody > tr > td {
    padding: 1.5rem 1rem;
    margin-bottom: 1rem; /* Further increased vertical padding */
}

/* Ensure form-check-inline elements have some margin for better spacing */
.form-check-inline {
    margin-right: 1rem; /* Increased margin */
    margin-bottom: 0.5rem; /* Add some vertical margin for wrapping */
}

/* Adjust button margins for better spacing */
.btn-sm {
    margin-left: 0.25rem;
    margin-right: 0.25rem;
}
</style>       

        
