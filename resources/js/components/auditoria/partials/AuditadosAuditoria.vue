<template>
    <div class="h-100 d-flex flex-column">
        <!-- Header Estilo Breadcrumb -->
        <div class="header-container mb-3 d-flex justify-content-between align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0 bg-transparent" style="font-size: 1rem;">
                    <li class="breadcrumb-item">
                        <a href="#" @click.prevent="$emit('back')" class="text-danger font-weight-bold">
                            Auditoría: {{ auditCode }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-dark font-weight-bold" aria-current="page">
                        {{ processName }} / Auditados
                    </li>
                </ol>
            </nav>
        </div>

        <div class="card border-0 shadow-none">
            <div class="card-body p-0">
                <p class="text-muted small mb-4 italic">
                    Registre las personas que participarán como auditados en esta actividad de auditoría. Puede agregar
                    filas manualmente o importar desde Excel.
                </p>

                <!-- Acciones Principales -->
                <div class="d-flex align-items-center mb-4 flex-wrap">
                    <button class="btn btn-dark btn-sm mr-2 mb-2" @click="addRow">
                        <i class="fas fa-plus mr-1"></i> Agregar Fila
                    </button>
                    <label class="btn btn-outline-success btn-sm mb-2 mr-2 cursor-pointer mb-0">
                        <i class="fas fa-file-excel mr-1"></i> Subir desde Excel
                        <input type="file" @change="importExcel" accept=".xlsx, .xls" hidden>
                    </label>
                    <button class="btn btn-link btn-sm text-muted p-0 mb-2" @click="downloadTemplate"
                        title="Descargar Plantilla Excel">
                        <i class="fas fa-download mr-1"></i> <small>Descargar Plantilla</small>
                    </button>
                </div>

                <div class="form-overlay-container">
                    <div v-if="loading" class="loading-overlay">
                        <div class="spinner-border text-danger" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>

                    <h6 class="mb-3 font-weight-bold small text-secondary text-uppercase">Participantes registrados</h6>

                    <div v-if="!auditados.length && !loading" class="text-center py-5 border rounded bg-light">
                        <i class="fas fa-users fa-3x text-light mb-3"></i>
                        <p class="text-muted mb-0">No hay auditados registrados para esta actividad.</p>
                        <small class="text-secondary">Use los botones superiores para agregar participantes.</small>
                    </div>

                    <div v-else class="table-responsive">
                        <table class="table table-sm table-bordered table-hover">
                            <thead class="bg-light text-secondary small font-weight-bold">
                                <tr>
                                    <th style="width: 60px;" class="text-center">#</th>
                                    <th>Nombres y Apellidos</th>
                                    <th>Cargo / Función</th>
                                    <th>Correo Electrónico (Opcional)</th>
                                    <th class="text-center" style="width: 80px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <tr v-for="(row, index) in auditados" :key="index">
                                    <td class="text-center align-middle">{{ index + 1 }}</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm border-light"
                                            v-model="row.nombre" placeholder="Ej: Juan Pérez">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm border-light"
                                            v-model="row.cargo" placeholder="Ej: Especialista TI">
                                    </td>
                                    <td>
                                        <input type="email" class="form-control form-control-sm border-light"
                                            v-model="row.correo" placeholder="ejemplo@correo.com">
                                    </td>
                                    <td class="text-center align-middle">
                                        <button class="btn btn-link text-danger p-0" title="Eliminar"
                                            @click="removeRow(index)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 small text-secondary" v-if="auditados.length > 0">
                        Total Registrados: <span class="badge badge-info">{{ auditados.length }}</span>
                    </div>
                </div>

                <div class="modal-footer justify-content-center bg-light border-top mt-5 p-3"
                    style="margin: 0 -1.5rem -1rem -1.5rem; border-bottom-right-radius: 0.3rem;">
                    <button class="btn btn-danger btn-sm px-5 shadow-sm" @click="saveAuditados" :disabled="saving">
                        <i v-if="saving" class="fas fa-spinner fa-spin mr-1"></i>
                        <i v-else class="fas fa-save mr-1"></i>
                        Guardar Cambios
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm ml-2 px-4 shadow-sm" @click="$emit('back')">
                        <i class="fas fa-times mr-1"></i> Volver
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import * as XLSX from 'xlsx';

const props = defineProps({
    agendaId: { type: Number, required: true },
    auditCode: { type: String, default: '---' },
    processName: { type: String, default: 'Cargando...' }
});

const emit = defineEmits(['back']);
const toast = useToast();

const auditados = ref([]);
const saving = ref(false);
const loading = ref(false);

const loadData = async () => {
    loading.value = true;
    try {
        const auditadosRes = await axios.get(`/api/auditoria/ejecucion/auditados/${props.agendaId}`);
        auditados.value = auditadosRes.data;
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar los datos', life: 3000 });
    } finally {
        loading.value = false;
    }
};



const addRow = () => {
    auditados.value.push({ nombre: '', cargo: '', correo: '' });
};

const removeRow = (index) => {
    auditados.value.splice(index, 1);
};

const saveAuditados = async () => {
    // Validar datos básicos
    const invalid = auditados.value.some(r => !r.nombre || !r.cargo);
    if (invalid) {
        toast.add({ severity: 'warn', summary: 'Atención', detail: 'Nombre y Cargo son obligatorios en todas las filas', life: 3000 });
        return;
    }

    saving.value = true;
    try {
        await axios.post(`/api/auditoria/ejecucion/auditados/${props.agendaId}/sync`, {
            auditados: auditados.value
        });
        toast.add({ severity: 'success', summary: 'Guardado', detail: 'Lista de auditados actualizada', life: 3000 });
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo guardar la lista', life: 3000 });
    } finally {
        saving.value = false;
    }
};

const importExcel = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (evt) => {
        const bstr = evt.target.result;
        const wb = XLSX.read(bstr, { type: 'binary' });
        const wsname = wb.SheetNames[0];
        const ws = wb.Sheets[wsname];
        const data = XLSX.utils.sheet_to_json(ws);

        // Mapear headers comunes de excel (Nombres, Cargo, Correo)
        const imported = data.map(row => ({
            nombre: row['Nombres'] || row['Nombre'] || row['NOMBRE'] || '',
            cargo: row['Cargo'] || row['CARGO'] || '',
            correo: row['Correo'] || row['CORREO'] || row['Email'] || ''
        })).filter(r => r.nombre !== '');

        if (imported.length > 0) {
            auditados.value = [...auditados.value, ...imported];
            toast.add({ severity: 'info', summary: 'Importado', detail: `Se agregaron ${imported.length} filas desde Excel`, life: 3000 });
        } else {
            toast.add({ severity: 'warn', summary: 'Atención', detail: 'No se encontraron datos válidos en el Excel (Use columnas: Nombres, Cargo, Correo)', life: 5000 });
        }
    };
    reader.readAsBinaryString(file);
    e.target.value = ''; // Reset input
};

const downloadTemplate = () => {
    const data = [
        { Nombres: '', Cargo: '', Correo: '' }
    ];
    const ws = XLSX.utils.json_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Auditados");
    XLSX.writeFile(wb, "Plantilla_Auditados.xlsx");
    toast.add({ severity: 'info', summary: 'Plantilla', detail: 'Descargando plantilla de Excel...', life: 2000 });
};

onMounted(loadData);
</script>

<style scoped>
.italic {
    font-style: italic;
}

.cursor-pointer {
    cursor: pointer;
}

.header-container {
    padding: 0.75rem 1rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #ff851b;
    display: flex;
    align-items: center;
}

.form-overlay-container {
    position: relative;
    min-height: 200px;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10;
}

.table th,
.table td {
    vertical-align: middle;
}

input.form-control-sm:focus {
    background-color: #fff !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    border-color: #dc3545 !important;
}
</style>
