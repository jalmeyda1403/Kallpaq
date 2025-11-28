<template>
    <Dialog v-model:visible="visible" :style="{ width: '800px' }" :header="isEditing ? 'Editar Riesgo' : 'Nuevo Riesgo'"
        :modal="true" class="p-fluid">
        <div class="p-field">
            <label for="proceso">Proceso</label>
            <div class="p-inputgroup">
                <InputText id="proceso" v-model="form.proceso_nombre" readonly placeholder="Seleccione un proceso" />
                <Button icon="pi pi-search" class="p-button-secondary" @click="openProcesoModal" />
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="p-field">
                    <label for="riesgo_cod">Código</label>
                    <InputText id="riesgo_cod" v-model="form.riesgo_cod" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-field">
                    <label for="riesgo_tipo">Tipo de Riesgo</label>
                    <Dropdown id="riesgo_tipo" v-model="form.riesgo_tipo" :options="tiposRiesgo"
                        placeholder="Seleccione tipo" />
                </div>
            </div>
        </div>

        <div class="p-field">
            <label for="riesgo_nombre">Nombre del Riesgo</label>
            <Textarea id="riesgo_nombre" v-model="form.riesgo_nombre" rows="2" />
        </div>

        <div class="p-field">
            <label for="factor">Factor de Riesgo</label>
            <Dropdown id="factor" v-model="form.factor_id" :options="factores" optionLabel="nombre" optionValue="id"
                placeholder="Seleccione factor" />
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="p-field">
                    <label for="probabilidad">Probabilidad (1-10)</label>
                    <InputNumber id="probabilidad" v-model="form.probabilidad" :min="1" :max="10" showButtons />
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-field">
                    <label for="impacto">Impacto (1-10)</label>
                    <InputNumber id="impacto" v-model="form.impacto" :min="1" :max="10" showButtons />
                </div>
            </div>
        </div>

        <div class="p-field">
            <label for="controles">Controles Actuales</label>
            <Textarea id="controles" v-model="form.controles" rows="3" />
        </div>

        <div class="p-field">
            <label for="riesgo_tratamiento">Estrategia de Tratamiento</label>
            <Dropdown id="riesgo_tratamiento" v-model="form.riesgo_tratamiento" :options="estrategias"
                placeholder="Seleccione estrategia" />
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" class="p-button-text" @click="closeModal" />
            <Button label="Guardar" icon="pi pi-check" class="p-button-primary" @click="save" :loading="loading" />
        </template>
    </Dialog>

    <ModalHijo ref="modalProceso" fetchUrl="/api/procesos/list" targetId="proceso_id" targetDesc="proceso_nombre"
        @update-target="onProcesoSelected" />
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue';
import { useRiesgoStore } from '@/stores/riesgoStore';
import ModalHijo from '@/components/generales/ModalHijo.vue';

// PrimeVue
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';
import InputNumber from 'primevue/inputnumber';
import Button from 'primevue/button';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
    riesgo: Object
});

const emit = defineEmits(['update:show', 'saved']);

const store = useRiesgoStore();
const visible = ref(false);
const isEditing = ref(false);
const loading = ref(false);
const modalProceso = ref(null);
const factores = ref([]);

const form = reactive({
    id: null,
    proceso_id: null,
    proceso_nombre: '',
    riesgo_cod: '',
    riesgo_tipo: '',
    riesgo_nombre: '',
    factor_id: null,
    probabilidad: 1,
    impacto: 1,
    controles: '',
    riesgo_tratamiento: ''
});

const tiposRiesgo = ['Estratégico', 'Operativo', 'Financiero', 'Cumplimiento', 'Tecnológico', 'Corrupción/Soborno'];
const estrategias = ['Mitigar', 'Evitar', 'Transferir', 'Aceptar'];

// Watchers
watch(() => props.show, (val) => {
    visible.value = val;
    if (val) {
        if (props.riesgo) {
            isEditing.value = true;
            Object.assign(form, props.riesgo);
            form.proceso_nombre = props.riesgo.proceso?.proceso_nombre || '';
        } else {
            isEditing.value = false;
            resetForm();
        }
        fetchFactores();
    }
});

watch(visible, (val) => {
    emit('update:show', val);
});

// Methods
const resetForm = () => {
    Object.assign(form, {
        id: null,
        proceso_id: null,
        proceso_nombre: '',
        riesgo_cod: '',
        riesgo_tipo: '',
        riesgo_nombre: '',
        factor_id: null,
        probabilidad: 1,
        impacto: 1,
        controles: '',
        riesgo_tratamiento: ''
    });
};

const fetchFactores = async () => {
    try {
        // Asumiendo que existe un endpoint para factores, si no, habría que crearlo o mockearlo
        // Por ahora hardcodeamos o intentamos llamar a una API existente
        // const response = await axios.get('/api/factores'); 
        // factores.value = response.data;

        // Mock temporarl si no existe endpoint
        factores.value = [
            { id: 1, nombre: 'Externo' },
            { id: 2, nombre: 'Procesos' },
            { id: 3, nombre: 'Personas' },
            { id: 4, nombre: 'Sistemas' },
            { id: 5, nombre: 'Eventos Externos' }
        ];
    } catch (e) {
        console.error(e);
    }
};

const openProcesoModal = () => {
    modalProceso.value.open();
};

const onProcesoSelected = (payload) => {
    form.proceso_id = payload.idValue;
    form.proceso_nombre = payload.descValue;
};

const closeModal = () => {
    visible.value = false;
};

const save = async () => {
    loading.value = true;
    try {
        await store.saveRiesgo(form);
        emit('saved');
        closeModal();
    } catch (error) {
        console.error(error);
        // Manejar error (toast)
    } finally {
        loading.value = false;
    }
};
</script>
