<template>
    <div class="modal fade" ref="modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">

        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Historial de Asignaciones</h5>
                    <button type="button" class="close" @click="hallazgoStore.closeHistorialModal">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted">
                        Esta es la bitácora de todos los especialistas que han sido asignados a este hallazgo a lo largo
                        del tiempo.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>Fecha de Asignación</th>
                                    <th>Especialista Asignado</th>
                                    <th>Asignado Por</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!hallazgoStore.historialAsignaciones.length">
                                    <td colspan="3" class="text-center text-muted">No hay registros en el historial.
                                    </td>
                                </tr>
                                <tr v-for="item in hallazgoStore.historialAsignaciones" :key="item.id">
                                    <td>{{ formatDateTime(item.created_at) }}</td>
                                    <td>{{ item.especialista ? item.especialista.name : 'N/A' }}</td>
                                    <td>{{ item.asignado_por ? item.asignado_por.name : 'Sistema' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        @click="hallazgoStore.closeHistorialModal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useHallazgoStore } from '@/stores/hallazgoStore';
import Modal from 'bootstrap/js/dist/modal';

const hallazgoStore = useHallazgoStore();
const modal = ref(null);
let modalInstance = null;

const formatDateTime = (dateTimeString) => {
    if (!dateTimeString) return 'N/A';
    const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' };
    return new Date(dateTimeString).toLocaleString('es-PE', options);
};

onMounted(() => {
    modalInstance = new Modal(modal.value);
    modalInstance.show();
    modal.value.addEventListener('hidden.bs.modal', () => {
        hallazgoStore.closeHistorialModal();
    });
});

onUnmounted(() => {
    if (modalInstance) {
        modalInstance.dispose();
    }
});
</script>
<style scoped>
.table {
    font-size: 13px;
}
</style>