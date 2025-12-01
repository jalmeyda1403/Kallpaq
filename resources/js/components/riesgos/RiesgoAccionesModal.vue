<template>
    <Dialog v-model:visible="visible" :style="{ width: '900px' }" header="Plan de Acción del Riesgo" :modal="true"
        class="p-fluid">
        <div class="mb-3">
            <h5>Riesgo: {{ riesgo?.riesgo_nombre }}</h5>
            <small class="text-muted">{{ riesgo?.riesgo_cod }} - {{ riesgo?.proceso?.proceso_nombre }}</small>
        </div>

        <DataTable :value="acciones" :loading="loading" responsiveLayout="scroll" class="p-datatable-sm">
            <template #header>
                <div class="text-right">
                    <Button label="Nueva Acción" icon="pi pi-plus" class="p-button-sm p-button-success"
                        @click="openForm(null)" />
                </div>
            </template>
            <Column field="ra_descripcion" header="Acción"></Column>
            <Column field="ra_responsable" header="Responsable"></Column>
            <Column field="ra_fecha_fin_planificada" header="Fecha Fin"></Column>
            <Column field="ra_estado" header="Estado">
                <template #body="{ data }">
                    <span :class="['badge', getEstadoClass(data.ra_estado)]">{{ data.ra_estado }}</span>
                </template>
            </Column>
            <Column header="Opciones" style="width: 120px">
                <template #body="{ data }">
                    <Button icon="pi pi-pencil" class="p-button-rounded p-button-warning p-button-text mr-1"
                        @click="openForm(data)" />
                    <Button icon="pi pi-trash" class="p-button-rounded p-button-danger p-button-text"
                        @click="confirmDelete(data)" />
                </template>
            </Column>
        </DataTable>

        <!-- Formulario Inline o Modal anidado para Acción -->
        <Dialog v-model:visible="showForm" :header="editingAccion ? 'Editar Acción' : 'Nueva Acción'" :modal="true"
            :style="{ width: '500px' }">
            <div class="p-field">
                <label>Descripción</label>
                <Textarea v-model="form.ra_descripcion" rows="2" />
            </div>
            <div class="p-field">
                <label>Responsable</label>
                <InputText v-model="form.ra_responsable" />
            </div>
            <div class="p-field">
                <label>Correo Responsable</label>
                <InputText v-model="form.ra_responsable_correo" />
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="p-field">
                        <label>Fecha Inicio</label>
                        <InputText type="date" v-model="form.ra_fecha_inicio" />
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-field">
                        <label>Fecha Fin Planificada</label>
                        <InputText type="date" v-model="form.ra_fecha_fin_planificada" />
                    </div>
                </div>
            </div>
            <div class="p-field">
                <label>Estado</label>
                <Dropdown v-model="form.ra_estado"
                    :options="['programada', 'desestimada', 'en proceso', 'implementada']" />
            </div>
            <div class="p-field">
                <label>Comentario</label>
                <Textarea v-model="form.ra_comentario" rows="2" />
            </div>
            <template #footer>
                <Button label="Cancelar" icon="pi pi-times" class="p-button-text" @click="showForm = false" />
                <Button label="Guardar" icon="pi pi-check" @click="saveAccion" />
            </template>
        </Dialog>
    </Dialog>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import { useRiesgoStore } from '@/stores/riesgoStore';

// PrimeVue
import Dialog from 'primevue/dialog';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';

const props = defineProps({
    show: Boolean,
    riesgo: Object
});

const emit = defineEmits(['update:show']);

const store = useRiesgoStore();
const visible = ref(false);
const loading = ref(false);
const acciones = ref([]);
const showForm = ref(false);
const editingAccion = ref(false);

const form = reactive({
    id: null,
    ra_descripcion: '',
    ra_responsable: '',
    ra_responsable_correo: '',
    ra_fecha_inicio: '',
    ra_fecha_fin_planificada: '',
    ra_estado: 'programada',
    ra_comentario: ''
});

watch(() => props.show, async (val) => {
    visible.value = val;
    if (val && props.riesgo) {
        await loadAcciones();
    }
});

watch(visible, (val) => {
    emit('update:show', val);
});

const loadAcciones = async () => {
    loading.value = true;
    try {
        acciones.value = await store.fetchAcciones(props.riesgo.id);
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
};

const openForm = (accion) => {
    if (accion) {
        editingAccion.value = true;
        Object.assign(form, accion);
    } else {
        editingAccion.value = false;
        Object.assign(form, {
            id: null,
            ra_descripcion: '',
            ra_responsable: '',
            ra_responsable_correo: '',
            ra_fecha_inicio: '',
            ra_fecha_fin_planificada: '',
            ra_estado: 'programada',
            ra_comentario: ''
        });
    }
    showForm.value = true;
};

const saveAccion = async () => {
    try {
        await store.saveAccion(props.riesgo.id, form);
        showForm.value = false;
        await loadAcciones();
    } catch (error) {
        console.error(error);
    }
};

const confirmDelete = async (accion) => {
    if (confirm('¿Eliminar esta acción?')) {
        try {
            await store.deleteAccion(accion.id);
            await loadAcciones();
        } catch (error) {
            console.error(error);
        }
    }
};

const getEstadoClass = (estado) => {
    switch (estado) {
        case 'programada': return 'badge-secondary';
        case 'en proceso': return 'badge-warning';
        case 'implementada': return 'badge-success';
        case 'desestimada': return 'badge-danger';
        default: return 'badge-light';
    }
};
</script>
