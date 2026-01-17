<template>
    <div class="h-100 d-flex flex-column">
        <!-- Header Breadcrumb -->
        <div class="header-container mb-3 d-flex justify-content-between align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0 bg-transparent" style="font-size: 1rem;">
                    <li class="breadcrumb-item">
                        <a href="#" @click.prevent="$emit('back')" class="text-danger font-weight-bold">
                            Informes
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-dark font-weight-bold" aria-current="page">
                        {{ informe.codigo || 'Nuevo Informe' }}
                    </li>
                </ol>
            </nav>
            <div>
                <span class="badge mr-2" :class="getEstadoBadge(informe.estado)">
                    {{ informe.estado }}
                </span>
                <button class="btn btn-sm btn-outline-secondary" @click="$emit('back')">
                    <i class="fas fa-arrow-left mr-1"></i> Volver
                </button>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="flex-grow-1 overflow-auto">
            <div class="container-fluid">
                <div class="row">
                    <!-- Navegación de Secciones -->
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm sticky-top" style="top: 1rem;">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0"><i class="fas fa-list mr-2"></i>Secciones</h6>
                            </div>
                            <div class="list-group list-group-flush">
                                <a href="#" v-for="seccion in secciones" :key="seccion.id"
                                    @click.prevent="seccionActiva = seccion.id"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                    :class="{ 'active': seccionActiva === seccion.id }">
                                    <span>
                                        <i :class="seccion.icon" class="mr-2"></i>
                                        {{ seccion.titulo }}
                                    </span>
                                    <i v-if="isSeccionCompleta(seccion.id)"
                                        class="fas fa-check-circle text-success"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido de Secciones -->
                    <div class="col-md-9">
                        <!-- Sección 1: Resumen Ejecutivo -->
                        <div v-show="seccionActiva === 'resumen'" class="card border-0 shadow-sm mb-4">
                            <div
                                class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-file-alt text-danger mr-2"></i>1. Resumen Ejecutivo
                                </h5>
                                <button class="btn btn-outline-primary btn-sm"
                                    @click="generarConIA('resumen_ejecutivo')" :disabled="generando.resumen_ejecutivo">
                                    <i :class="generando.resumen_ejecutivo ? 'fas fa-spinner fa-spin' : 'fas fa-magic'"
                                        class="mr-1"></i>
                                    {{ generando.resumen_ejecutivo ? 'Generando...' : 'Generar con IA' }}
                                </button>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control" rows="20" v-model="informe.resumen_ejecutivo"
                                    style="min-height: 480px; resize: vertical;" placeholder="Resumen conciso..."
                                    @blur="guardarSeccion('resumen_ejecutivo')"></textarea>
                            </div>
                        </div>

                        <!-- Sección 2: Alcance y Criterios -->
                        <div v-show="seccionActiva === 'alcance'" class="card border-0 shadow-sm mb-4">
                            <div
                                class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-bullseye text-danger mr-2"></i>2. Alcance y Criterios
                                </h5>
                                <button class="btn btn-outline-primary btn-sm"
                                    @click="generarConIA('alcance_criterios')" :disabled="generando.alcance_criterios">
                                    <i :class="generando.alcance_criterios ? 'fas fa-spinner fa-spin' : 'fas fa-magic'"
                                        class="mr-1"></i>
                                    {{ generando.alcance_criterios ? 'Generando...' : 'Generar con IA' }}
                                </button>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control" rows="20" v-model="informe.alcance_criterios"
                                    style="min-height: 480px; resize: vertical;" placeholder="Defina alcance..."
                                    @blur="guardarSeccion('alcance_criterios')"></textarea>
                            </div>
                        </div>

                        <!-- Sección 3: Procesos Auditados -->
                        <div v-show="seccionActiva === 'procesos_auditados'" class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0"><i class="fas fa-clipboard-list text-danger mr-2"></i>Procesos
                                    Auditados</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">Procesos que han completado el ciclo de auditoría (100%
                                    Checklist)</p>
                                <div v-if="!datosInforme.procesos_auditados || datosInforme.procesos_auditados.length === 0"
                                    class="alert alert-warning">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    No hay procesos con el checklist al 100% en esta auditoría.
                                </div>
                                <div v-else class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Proceso</th>
                                                <th>Fecha Auditoría</th>
                                                <th class="text-center">Checks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(proceso, index) in datosInforme.procesos_auditados"
                                                :key="index">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ proceso.nombre }}</td>
                                                <td>{{ proceso.fecha_auditoria }}</td>
                                                <td class="text-center">
                                                    <span class="badge badge-success">{{ proceso.total_preguntas }}
                                                        items</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Sección 4: Lista de Auditados -->
                        <div v-show="seccionActiva === 'auditados'" class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0"><i class="fas fa-users text-danger mr-2"></i>4. Lista de Auditados</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">Participantes entrevistados durante la auditoría</p>
                                <div v-if="!datosInforme.auditados || datosInforme.auditados.length === 0"
                                    class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    No hay auditados registrados. Registre los participantes en la sección de Ejecución.
                                </div>
                                <div v-else class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Nombre</th>
                                                <th>Cargo</th>
                                                <th>Correo</th>
                                                <th>Proceso</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(auditado, index) in datosInforme.auditados" :key="index">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ auditado.nombre }}</td>
                                                <td>{{ auditado.cargo }}</td>
                                                <td>{{ auditado.correo || 'N/A' }}</td>
                                                <td class="small">{{ auditado.proceso }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Sección 5: Hallazgos de Conformidad -->
                        <div v-show="seccionActiva === 'conformidad'" class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0">
                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                    5. Hallazgos de Conformidad ({{ totalConformidades }})
                                </h5>
                            </div>
                            <div class="card-body">
                                <div
                                    v-if="datosInforme.hallazgos_conformidad && datosInforme.hallazgos_conformidad.length > 0">
                                    <div v-for="(grupo, index) in datosInforme.hallazgos_conformidad"
                                        :key="'conf-' + index" class="mb-4">
                                        <h6 class="text-uppercase font-weight-bold text-success border-bottom pb-2">
                                            {{ grupo.proceso }}
                                        </h6>
                                        <div v-for="(item, idx) in grupo.items" :key="'item-' + idx"
                                            class="border-left border-success pl-3 mb-3">
                                            <p class="small text-muted mb-1">{{ item.norma }} - Req. {{ item.requisito
                                            }}</p>
                                            <p class="small mb-0">{{ item.evidencia }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="alert alert-info small">No hay conformidades registradas</div>
                            </div>
                        </div>

                        <!-- Sección 6: Oportunidades de Mejora -->
                        <div v-show="seccionActiva === 'oportunidades'" class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0">
                                    <i class="fas fa-lightbulb text-warning mr-2"></i>
                                    6. Oportunidades de Mejora ({{ totalOportunidades }})
                                </h5>
                            </div>
                            <div class="card-body">
                                <div
                                    v-if="datosInforme.oportunidades_mejora && datosInforme.oportunidades_mejora.length > 0">
                                    <div v-for="(grupo, index) in datosInforme.oportunidades_mejora"
                                        :key="'opm-' + index" class="mb-4">
                                        <h6 class="text-uppercase font-weight-bold text-warning border-bottom pb-2">
                                            {{ grupo.proceso }}
                                        </h6>
                                        <div v-for="(item, idx) in grupo.items" :key="'item-' + idx"
                                            class="card border-warning mb-3">
                                            <div class="card-body p-3">
                                                <p class="small text-muted mb-1">{{ item.norma }} - Req. {{
                                                    item.requisito
                                                    }}</p>
                                                <p class="small mb-2"><strong>Oportunidad:</strong> {{ item.hallazgo }}
                                                </p>
                                                <p class="small mb-0"><strong>Evidencia:</strong> {{ item.evidencia }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="alert alert-info small">No hay oportunidades de mejora registradas
                                </div>
                            </div>
                        </div>

                        <!-- Sección 7: Hallazgos de No Conformidad -->
                        <div v-show="seccionActiva === 'no_conformidad'" class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0">
                                    <i class="fas fa-exclamation-triangle text-danger mr-2"></i>
                                    7. Hallazgos de No Conformidad ({{ totalNoConformidades }})
                                </h5>
                            </div>
                            <div class="card-body">
                                <div
                                    v-if="datosInforme.hallazgos_no_conformidad && datosInforme.hallazgos_no_conformidad.length > 0">
                                    <div v-for="(grupo, index) in datosInforme.hallazgos_no_conformidad"
                                        :key="'nc-' + index" class="mb-4">
                                        <h6 class="text-uppercase font-weight-bold text-danger border-bottom pb-2">
                                            {{ grupo.proceso }}
                                        </h6>
                                        <div v-for="(item, idx) in grupo.items" :key="'item-' + idx"
                                            class="card border-danger mb-3">
                                            <div class="card-body p-3">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-1">
                                                            <span class="badge badge-secondary">{{ item.norma }} - Req.
                                                                {{ item.requisito }}</span>
                                                            <span v-if="item.hallazgo_clasificacion" class="badge"
                                                                :class="{
                                                                    'badge-danger': item.hallazgo_clasificacion === 'NCM',
                                                                    'badge-warning': item.hallazgo_clasificacion === 'NCMe' || item.hallazgo_clasificacion === 'Obs',
                                                                    'badge-info': item.hallazgo_clasificacion === 'Odm',
                                                                    'badge-secondary': item.hallazgo_clasificacion === 'N/A'
                                                                }">
                                                                {{ item.hallazgo_clasificacion }}
                                                            </span>
                                                        </div>
                                                        <p class="small mb-2"><strong>Hallazgo:</strong> {{
                                                            item.hallazgo }}
                                                        </p>
                                                        <p class="small mb-0"><strong>Evidencia:</strong> {{
                                                            item.evidencia
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="alert alert-success small">No hay no conformidades registradas</div>
                            </div>
                        </div>

                        <!-- Sección 8: Conclusiones -->
                        <div v-show="seccionActiva === 'conclusiones'" class="card border-0 shadow-sm mb-4">
                            <div
                                class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-clipboard-check text-danger mr-2"></i>8. Conclusiones
                                </h5>
                                <button class="btn btn-outline-primary btn-sm" @click="generarConIA('conclusiones')"
                                    :disabled="generando.conclusiones">
                                    <i :class="generando.conclusiones ? 'fas fa-spinner fa-spin' : 'fas fa-magic'"
                                        class="mr-1"></i>
                                    {{ generando.conclusiones ? 'Generando...' : 'Generar con IA' }}
                                </button>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control" rows="20" v-model="informe.conclusiones"
                                    style="min-height: 480px; resize: vertical;"
                                    placeholder="Las conclusiones se generarán automáticamente con IA..."
                                    @blur="guardarSeccion('conclusiones')"></textarea>
                            </div>
                        </div>

                        <!-- Sección 9: Recomendaciones -->
                        <div v-show="seccionActiva === 'recomendaciones'" class="card border-0 shadow-sm mb-4">
                            <div
                                class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-tasks text-danger mr-2"></i>9. Recomendaciones</h5>
                                <button class="btn btn-outline-primary btn-sm" @click="generarConIA('recomendaciones')"
                                    :disabled="generando.recomendaciones">
                                    <i :class="generando.recomendaciones ? 'fas fa-spinner fa-spin' : 'fas fa-magic'"
                                        class="mr-1"></i>
                                    {{ generando.recomendaciones ? 'Generando...' : 'Generar con IA' }}
                                </button>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control" rows="20" v-model="informe.recomendaciones"
                                    style="min-height: 480px; resize: vertical;"
                                    placeholder="Las recomendaciones se generarán automáticamente con IA..."
                                    @blur="guardarSeccion('recomendaciones')"></textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Footer con Acciones -->
            <div class="bg-white border-top p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-secondary btn-sm mr-2" @click="guardarSeccion(seccionActiva)">
                            <i class="fas fa-save mr-1"></i> Guardar Cambios
                        </button>
                        <button class="btn btn-dark btn-sm" @click="abrirImpresion">
                            <i class="fas fa-print mr-1"></i> Vista Previa / Exportar
                        </button>
                    </div>
                    <button class="btn btn-secondary btn-sm" @click="$emit('back')">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </button>
                </div>
            </div>
            <InformeAuditoriaPrintModal ref="printModalRef" />
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import Swal from 'sweetalert2';
import InformeAuditoriaPrintModal from './InformeAuditoriaPrintModal.vue';

const props = defineProps({
    informeId: { type: Number, required: true }
});

const emit = defineEmits(['back']);
const toast = useToast();

const informe = ref({});
const datosInforme = ref({
    auditoria: {},
    auditados: [],
    procesos_auditados: [],
    hallazgos_conformidad: [],
    hallazgos_no_conformidad: [],
    oportunidades_mejora: []
});
const seccionActiva = ref('resumen');
const generando = ref({
    resumen_ejecutivo: false,
    alcance_criterios: false,
    conclusiones: false,
    conclusiones: false,
    recomendaciones: false
});
const printModalRef = ref(null);

const secciones = [
    { id: 'resumen', titulo: '1. Resumen Ejecutivo', icon: 'fas fa-file-alt' },
    { id: 'alcance', titulo: '2. Alcance y Criterios', icon: 'fas fa-bullseye' },
    { id: 'procesos_auditados', titulo: '3. Procesos Auditados', icon: 'fas fa-clipboard-list' },
    { id: 'auditados', titulo: '4. Lista de Auditados', icon: 'fas fa-users' },
    { id: 'conformidad', titulo: '5. Conformidades', icon: 'fas fa-check-circle' },
    { id: 'oportunidades', titulo: '6. Oportunidades de Mejora', icon: 'fas fa-lightbulb' },
    { id: 'no_conformidad', titulo: '7. No Conformidades', icon: 'fas fa-exclamation-triangle' },
    { id: 'conclusiones', titulo: '8. Conclusiones', icon: 'fas fa-clipboard-check' },
    { id: 'recomendaciones', titulo: '9. Recomendaciones', icon: 'fas fa-tasks' }
];

const totalConformidades = computed(() => {
    return datosInforme.value.hallazgos_conformidad?.reduce((sum, grupo) => sum + grupo.items.length, 0) || 0;
});

const totalOportunidades = computed(() => {
    return datosInforme.value.oportunidades_mejora?.reduce((sum, grupo) => sum + grupo.items.length, 0) || 0;
});

const totalNoConformidades = computed(() => {
    return datosInforme.value.hallazgos_no_conformidad?.reduce((sum, grupo) => sum + grupo.items.length, 0) || 0;
});



const loadInforme = async () => {
    try {
        // Primero cargamos el informe para obtener el ae_id (ID de la auditoría)
        const informeRes = await axios.get(`/api/auditoria/informes/${props.informeId}`);
        informe.value = informeRes.data;

        // Ahora usamos informe.value.ae_id para obtener los datos de la auditoría
        const datosRes = await axios.get(`/api/auditoria/informes/datos/${informe.value.ae_id}`);
        datosInforme.value = datosRes.data;

        if (datosInforme.value.hallazgos_no_conformidad) {
            datosInforme.value.hallazgos_no_conformidad = datosInforme.value.hallazgos_no_conformidad.map(grupo => ({
                ...grupo,
                items: grupo.items.map(item => ({
                    ...item,
                    seleccionado: false,
                    clasificacion: ''
                }))
            }));
        }
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar el informe', life: 3000 });
    }
};

const generarInformeConIA = async (seccion) => {
    generando.value[seccion] = true;
    try {
        // Preparar contexto completo del informe
        const contextoCompleto = {
            auditoria: datosInforme.value.auditoria,
            auditados: datosInforme.value.auditados,
            alcance: informe.value.alcance_criterios || '',

            // Estadísticas de hallazgos
            total_conformidades: totalConformidades.value,
            total_oportunidades: totalOportunidades.value,
            total_no_conformidades: totalNoConformidades.value,

            // Hallazgos agrupados por proceso
            hallazgos_conformidad: datosInforme.value.hallazgos_conformidad,
            hallazgos_no_conformidad: datosInforme.value.hallazgos_no_conformidad,
            oportunidades_mejora: datosInforme.value.oportunidades_mejora,

            // Secciones ya completadas (para contexto)
            alcance_criterios: informe.value.alcance_criterios,
            resumen_ejecutivo: informe.value.resumen_ejecutivo,
            conclusiones: informe.value.conclusiones,
            recomendaciones: informe.value.recomendaciones
        };

        const response = await axios.post('/api/auditoria/informes/generar-seccion', {
            seccion,
            datos: contextoCompleto
        });

        informe.value[seccion] = response.data.contenido;
        await guardarSeccion(seccion);
        toast.add({ severity: 'success', summary: 'Generado', detail: 'Contenido generado con IA', life: 3000 });
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo generar con IA', life: 3000 });
    } finally {
        generando.value[seccion] = false;
    }
};

// Mantener compatibilidad con llamadas existentes
const generarConIA = generarInformeConIA;

const guardarSeccion = async (campo) => {
    try {
        await axios.put(`/api/auditoria/informes/${props.informeId}`, {
            [campo]: informe.value[campo]
        });
    } catch (e) {
        console.error('Error guardando sección:', e);
    }
};


const abrirImpresion = async () => {
    // Sincronizamos antes de imprimir si es necesario
    try {
        await axios.put(`/api/auditoria/informes/${props.informeId}`, {
            hallazgos_conformidad: datosInforme.value.hallazgos_conformidad,
            hallazgos_no_conformidad: datosInforme.value.hallazgos_no_conformidad,
            oportunidades_mejora: datosInforme.value.oportunidades_mejora,
            procesos_auditados: datosInforme.value.procesos_auditados,
            auditados: datosInforme.value.auditados
        });

        printModalRef.value.open({
            auditoria: datosInforme.value.auditoria,
            auditados: datosInforme.value.auditados,
            procesos_auditados: datosInforme.value.procesos_auditados,
            hallazgos_conformidad: datosInforme.value.hallazgos_conformidad,
            hallazgos_no_conformidad: datosInforme.value.hallazgos_no_conformidad,
            oportunidades_mejora: datosInforme.value.oportunidades_mejora,
            informe_content: informe.value,
            auditId: props.informeId,
            elaboradoPor: informe.value.elaborado_por?.name || '',
            aprobadoPor: informe.value.aprobado_por?.name || ''
        });
    } catch (e) {
        console.error('Error sincronizando antes de imprimir:', e);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron sincronizar los datos para la impresión', life: 3000 });
    }
};


const crearSMPsOportunidades = async () => {
    const seleccionados = [];
    datosInforme.value.oportunidades_mejora.forEach(grupo => {
        grupo.items.forEach(item => {
            if (item.seleccionado) {
                seleccionados.push({
                    checklist_id: item.checklist_id,
                    proceso_id: grupo.proceso_id,
                    clasificacion: 'OdM' // Clasificación fija para Oportunidades de Mejora
                });
            }
        });
    });

    if (seleccionados.length === 0) {
        toast.add({ severity: 'warn', summary: 'Atención', detail: 'Seleccione al menos una oportunidad', life: 3000 });
        return;
    }

    const result = await Swal.fire({
        title: 'Crear SMPs',
        text: `Se crearán ${seleccionados.length} SMP(s) de Oportunidad de Mejora`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        confirmButtonText: 'Sí, crear',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        creandoSMPsOportunidades.value = true;
        try {
            await axios.post(`/api/auditoria/informes/${props.informeId}/crear-smps`, {
                hallazgos_seleccionados: seleccionados
            });

            toast.add({ severity: 'success', summary: 'Creadas', detail: `${seleccionados.length} SMP(s) creadas exitosamente`, life: 3000 });

            // Desmarcar oportunidades
            datosInforme.value.oportunidades_mejora.forEach(grupo => {
                grupo.items.forEach(item => item.seleccionado = false);
            });
        } catch (e) {
            toast.add({ severity: 'error', summary: 'Error', detail: e.response?.data?.error || 'No se pudieron crear las SMPs', life: 3000 });
        } finally {
            creandoSMPsOportunidades.value = false;
        }
    }
};

const isSeccionCompleta = (seccionId) => {
    const mapeo = {
        'resumen': 'resumen_ejecutivo',
        'alcance': 'alcance_criterios',
        'auditados': 'auditados',
        'conformidad': 'conformidad',
        'oportunidades': 'oportunidades',
        'no_conformidad': 'no_conformidad',
        'conclusiones': 'conclusiones',
        'recomendaciones': 'recomendaciones'
    };

    if (seccionId === 'auditados') {
        return datosInforme.value.auditados && datosInforme.value.auditados.length > 0;
    }
    if (seccionId === 'procesos_auditados') {
        return datosInforme.value.procesos_auditados && datosInforme.value.procesos_auditados.length > 0;
    }
    if (seccionId === 'conformidad') {
        return totalConformidades.value > 0;
    }
    if (seccionId === 'oportunidades') {
        return totalOportunidades.value > 0;
    }
    if (seccionId === 'no_conformidad') {
        return totalNoConformidades.value > 0;
    }

    return informe.value[mapeo[seccionId]] && informe.value[mapeo[seccionId]].length > 10;
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

onMounted(loadInforme);
</script>

<style scoped>
.header-container {
    padding: 0.75rem 1rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #ff851b;
}

.list-group-item.active {
    background-color: #dc3545;
    border-color: #dc3545;
}

.list-group-item {
    font-size: 0.75rem;
    padding: 0.35rem 0.5rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Oportunidades de Mejora puede tener 2 líneas */
.list-group-item[title*="Oportunidades"],
.list-group-item:nth-child(6) {
    white-space: normal;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    line-height: 1.1;
}

.sticky-top {
    position: sticky;
}
</style>
