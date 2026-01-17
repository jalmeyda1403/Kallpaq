<template>
    <div>
        <div class="header-container mb-4">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">Elaboración de Informes</span>
            </h6>
        </div>

        <div class="card border-0 shadow-none">
            <div class="card-body p-0">
                <p class="text-muted small mb-4 italic">
                    Gestione los informes de auditoría interna. Genere informes profesionales con asistencia de IA.
                </p>

                <!-- Botón Nuevo Informe -->
                <div class="mb-4">
                    <button class="btn btn-danger btn-sm shadow-sm" @click="crearNuevoInforme" :disabled="creando">
                        <i v-if="creando" class="fas fa-spinner fa-spin mr-1"></i>
                        <i v-else class="fas fa-plus-circle mr-1"></i>
                        Nuevo Informe
                    </button>
                </div>

                <div class="form-overlay-container">
                    <div v-if="loading" class="loading-overlay">
                        <div class="spinner-border text-danger" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                    </div>

                    <h6 class="mb-3 font-weight-bold small text-secondary text-uppercase">Informes Registrados</h6>

                    <div v-if="!informes.length && !loading" class="text-center py-5 border rounded bg-light">
                        <i class="fas fa-file-alt fa-3x text-light mb-3"></i>
                        <p class="text-muted mb-0">No hay informes registrados para esta auditoría.</p>
                        <small class="text-secondary">Cree un nuevo informe para comenzar.</small>
                    </div>

                    <div v-else class="table-responsive">
                        <table class="table table-sm table-bordered table-hover">
                            <thead class="bg-light text-secondary small font-weight-bold">
                                <tr>
                                    <th style="width: 150px;">Código</th>
                                    <th style="width: 120px;">Estado</th>
                                    <th style="width: 120px;">Fecha Creación</th>
                                    <th>Elaborado Por</th>
                                    <th style="width: 280px;" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <tr v-for="informe in informes" :key="informe.id">
                                    <td class="font-weight-bold">{{ informe.codigo }}</td>
                                    <td>
                                        <span class="badge" :class="getEstadoBadge(informe.estado)">
                                            {{ informe.estado }}
                                        </span>
                                    </td>
                                    <td>{{ formatDate(informe.created_at) }}</td>
                                    <td>{{ informe.elaborado_por?.name || 'N/A' }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-link btn-sm text-primary p-0 mr-2"
                                            title="Editar Contenido" @click="editarInforme(informe.id)">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>

                                        <!-- Acciones de Flujo -->
                                        <button v-if="informe.estado === 'Borrador'"
                                            class="btn btn-outline-warning btn-xs mr-1"
                                            @click="cambiarEstado(informe, 'En Revisión')">
                                            <i class="fas fa-paper-plane mr-1"></i> Enviar
                                        </button>
                                        <button v-if="informe.estado === 'En Revisión'"
                                            class="btn btn-outline-success btn-xs mr-1"
                                            @click="cambiarEstado(informe, 'Aprobado')">
                                            <i class="fas fa-check mr-1"></i> Aprobar
                                        </button>
                                        <button v-if="informe.estado === 'Aprobado'" class="btn btn-danger btn-xs mr-1"
                                            @click="cambiarEstado(informe, 'Emitido')">
                                            <i class="fas fa-certificate mr-1"></i> Emitir
                                        </button>
                                        <button v-if="informe.estado !== 'Emitido'"
                                            class="btn btn-link btn-sm text-danger p-0" title="Eliminar"
                                            @click="eliminarInforme(informe.id)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 small text-secondary" v-if="informes.length > 0">
                        Total de Informes: <span class="badge badge-info">{{ informes.length }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import Swal from 'sweetalert2';

const props = defineProps({
    auditId: { type: Number, required: true }
});

const emit = defineEmits(['ver-informe']);
const toast = useToast();

const informes = ref([]);
const loading = ref(false);
const creando = ref(false);

const loadInformes = async () => {
    loading.value = true;
    try {
        const response = await axios.get(`/api/auditoria/informes/auditoria/${props.auditId}`);
        informes.value = response.data;
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar los informes', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const crearNuevoInforme = async () => {
    creando.value = true;
    try {
        const response = await axios.post('/api/auditoria/informes', {
            ae_id: props.auditId
        });
        toast.add({ severity: 'success', summary: 'Creado', detail: 'Informe creado exitosamente', life: 3000 });
        await loadInformes();
        editarInforme(response.data.id);
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo crear el informe', life: 3000 });
    } finally {
        creando.value = false;
    }
};

const editarInforme = (informeId) => {
    emit('ver-informe', informeId);
};

const cambiarEstado = async (informeItem, nuevoEstado) => {
    const msg = nuevoEstado === 'Emitido' ? 'Una vez emitido, el informe no podrá ser modificado.' : `¿Cambiar el estado a ${nuevoEstado}?`;

    const result = await Swal.fire({
        title: `Confirmar: ${nuevoEstado}`,
        text: msg,
        icon: nuevoEstado === 'Emitido' ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        try {
            loading.value = true;
            const payload = {
                estado: nuevoEstado,
                fecha_emision: nuevoEstado === 'Emitido' ? new Date().toISOString().split('T')[0] : null
            };

            // Para emitir, intentamos capturar los últimos datos de la auditoría
            if (nuevoEstado === 'Emitido') {
                const datosRes = await axios.get(`/api/auditoria/informes/datos/${informeItem.ae_id}`);
                payload.hallazgos_conformidad = datosRes.data.hallazgos_conformidad;
                payload.hallazgos_no_conformidad = datosRes.data.hallazgos_no_conformidad;
                payload.oportunidades_mejora = datosRes.data.oportunidades_mejora;
                payload.procesos_auditados = datosRes.data.procesos_auditados;
                payload.auditados = datosRes.data.auditados;
            }

            await axios.put(`/api/auditoria/informes/${informeItem.id}`, payload);
            toast.add({ severity: 'success', summary: 'Actualizado', detail: `Estado actualizado a ${nuevoEstado}`, life: 3000 });
            await loadInformes();
        } catch (e) {
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo actualizar el estado', life: 3000 });
        } finally {
            loading.value = false;
        }
    }
};

const eliminarInforme = async (informeId) => {
    const result = await Swal.fire({
        title: '¿Eliminar Informe?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        try {
            await axios.delete(`/api/auditoria/informes/${informeId}`);
            toast.add({ severity: 'success', summary: 'Eliminado', detail: 'Informe eliminado', life: 3000 });
            await loadInformes();
        } catch (e) {
            toast.add({ severity: 'error', summary: 'Error', detail: e.response?.data?.error || 'No se pudo eliminar', life: 3000 });
        }
    }
};

const getEstadoBadge = (estado) => {
    const badges = {
        'Borrador': 'badge-secondary',
        'En Revisión': 'badge-warning',
        'Aprobado': 'badge-success',
        'Emitido': 'badge-primary'
    };
    return badges[estado] || 'badge-secondary';
};

const formatDate = (dateStr) => {
    if (!dateStr) return 'N/A';
    return new Date(dateStr).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: '2-digit'
    });
};

onMounted(loadInformes);
</script>

<style scoped>
.italic {
    font-style: italic;
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
</style>
