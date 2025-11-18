<template>
    <div>
        <div class="form-group">
            <input type="text" v-model="searchTerm" class="form-control" placeholder="Buscar procesos..." />
        </div>

        <DataTable :value="filteredProcesos" v-model:selection="selectedProcesos" dataKey="id" :paginator="true"
            :rows="10" :rowsPerPageOptions="[10, 20, 50]"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown">
            <Column selectionMode="multiple" headerStyle="width: 3%"></Column>
            <Column field="cod_proceso" header="Código" style="width: 5%"></Column>
            <Column field="proceso_nombre" header="Nombre" style="width: 30%" bodyClass="text-left">
            </Column>
        </DataTable>

        <div class="d-flex justify-content-end mt-3">
            <button type="button" class="btn btn-secondary mr-2"
                @click="inventarioStore.closeAssignProcessModal">Cancelar</button>
            <button type="button" class="btn btn-primary" @click="save">Guardar</button>
        </div>
    </div>
</template>
<style scoped>
::v-deep td.text-left,
::v-deep td.text-left>div,
::v-deep td.text-left>span {
    text-align: left !important;
}
</style>

<script setup>
import { ref, computed } from 'vue';
import { useInventarioStore } from '@/stores/inventarioStore';

// PrimeVue components
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const inventarioStore = useInventarioStore();

const selectedProcesos = ref([]);
const searchTerm = ref('');

const filteredProcesos = computed(() => {
    if (!searchTerm.value) {
        return inventarioStore.availableProcesses;
    }
    return inventarioStore.availableProcesses.filter(proceso =>
        proceso.proceso_nombre.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
        proceso.cod_proceso.toLowerCase().includes(searchTerm.value.toLowerCase())
    );
});

const save = () => {
    inventarioStore.assignProcesos(selectedProcesos.value);
};
</script>

