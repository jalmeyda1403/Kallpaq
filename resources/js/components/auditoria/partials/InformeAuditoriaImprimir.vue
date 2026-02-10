<template>
    <div class="print-document">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td width="20%"><img :src="logoUrl" class="logo"></td>
                <td width="60%" class="title">INFORME DE AUDITORÍA {{ auditData.codigo }}</td>
                <td width="20%" class="meta">
                    F. Emisión: {{ currentDate }}<br>
                    Versión: 01
                </td>
            </tr>
        </table>

        <!-- 1. RESUMEN EJECUTIVO -->
        <div class="section-title">1. RESUMEN EJECUTIVO</div>
        <div class="content-box">
            {{ informe.resumen_ejecutivo || 'No especificado' }}
        </div>

        <!-- 2. ALCANCE Y CRITERIOS -->
        <div class="section-title">2. ALCANCE Y CRITERIOS</div>
        <div class="content-box">
            {{ formattedCriterios }}
        </div>

        <!-- 3. PROCESOS AUDITADOS -->
        <div class="section-title">3. PROCESOS AUDITADOS</div>
        <table class="content-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>Proceso</th>
                    <th width="15%">Fecha</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(proceso, index) in procesosAuditados" :key="index">
                    <td>{{ index + 1 }}</td>
                    <td>{{ proceso.nombre }}</td>
                    <td>{{ proceso.fecha_auditoria }}</td>
                </tr>
                <tr v-if="procesosAuditados.length === 0">
                    <td colspan="3" class="text-center text-muted">No se registraron procesos con checklist completo.
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- 4. LISTA DE AUDITADOS -->
        <div class="section-title">4. LISTA DE AUDITADOS</div>
        <table class="content-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>Nombre</th>
                    <th>Cargo</th>
                    <th>Proceso</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(auditado, index) in auditados" :key="index">
                    <td>{{ index + 1 }}</td>
                    <td>{{ auditado.nombre }}</td>
                    <td>{{ auditado.cargo }}</td>
                    <td>{{ auditado.proceso }}</td>
                </tr>
            </tbody>
        </table>

        <!-- 5. CONFORMIDADES -->
        <div class="section-title">5. HALLAZGOS DE CONFORMIDAD</div>
        <div v-if="hallazgosConformidad.length > 0">
            <div v-for="(grupo, index) in hallazgosConformidad" :key="index" class="mb-3">
                <div class="sub-section-title">{{ grupo.proceso }}</div>
                <ul class="hallazgos-list">
                    <li v-for="(item, idx) in grupo.items" :key="idx">
                        <strong>{{ item.norma }} - Req. {{ item.requisito }}:</strong> {{ item.evidencia }}
                    </li>
                </ul>
            </div>
        </div>
        <div v-else class="content-box text-muted small">No se registraron hallazgos de conformidad.</div>

        <!-- 6. OPORTUNIDADES DE MEJORA -->
        <div class="section-title">6. OPORTUNIDADES DE MEJORA</div>
        <div v-if="oportunidadesMejora.length > 0">
            <div v-for="(grupo, index) in oportunidadesMejora" :key="index" class="mb-3">
                <div class="sub-section-title">{{ grupo.proceso }}</div>
                <table class="content-table" style="margin-bottom: 10px;">
                    <thead>
                        <tr>
                            <th width="15%">Sistema</th>
                            <th width="10%">Req.</th>
                            <th>Oportunidad de Mejora / Evidencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, idx) in grupo.items" :key="idx">
                            <td>{{ item.norma || 'General' }}</td>
                            <td>{{ item.requisito }}</td>
                            <td>
                                <div>{{ item.hallazgo }}</div>
                                <div class="small text-muted mt-1">Evidencia: {{ item.evidencia }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-else class="content-box text-muted small">No se registraron oportunidades de mejora.</div>

        <!-- 7. NO CONFORMIDADES -->
        <div class="section-title">7. HALLAZGOS DE NO CONFORMIDAD</div>
        <div v-if="hallazgosNoConformidad.length > 0">
            <div v-for="(grupo, index) in hallazgosNoConformidad" :key="index" class="mb-3">
                <div class="sub-section-title">{{ grupo.proceso }}</div>
                <table class="content-table">
                    <thead>
                        <tr>
                            <th width="15%">Sistema</th>
                            <th width="10%">Req.</th>
                            <th>Hallazgo / Evidencia</th>
                            <th width="10%">Clasif.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, idx) in grupo.items" :key="idx">
                            <td>{{ item.norma || 'General' }}</td>
                            <td>{{ item.requisito }}</td>
                            <td>
                                <div>{{ item.hallazgo }}</div>
                                <div class="small text-muted mt-1"><strong>Evidencia:</strong> {{ item.evidencia }}
                                </div>
                            </td>
                            <td class="text-center">{{ item.hallazgo_clasificacion || item.clasificacion || 'N/A' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-else class="content-box text-muted small">No se registraron no conformidades.</div>

        <!-- 8. CONCLUSIONES -->
        <div class="section-title">8. CONCLUSIONES</div>
        <div class="content-box">
            {{ informe.conclusiones || 'No especificado' }}
        </div>

        <!-- 9. RECOMENDACIONES -->
        <div class="section-title">9. RECOMENDACIONES</div>
        <div class="content-box">
            {{ informe.recomendaciones || 'No especificado' }}
        </div>

        <!-- 10. EQUIPO AUDITOR -->
        <div class="section-title">10. EQUIPO AUDITOR</div>
        <table class="content-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>Nombre</th>
                    <th>Rol</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="auditData.equipo && auditData.equipo.length > 0" v-for="(miembro, index) in auditData.equipo"
                    :key="index">
                    <td>{{ index + 1 }}</td>
                    <td>
                        {{ miembro.auditor?.user?.name || miembro.usuario?.name || 'Desconocido' }}
                    </td>
                    <td>{{ miembro.aeq_rol || 'Auditor' }}</td>
                </tr>
                <tr v-else>
                    <td colspan="3" class="text-center text-muted">No se ha registrado el equipo auditor.</td>
                </tr>
            </tbody>
        </table>

        <!-- Firmas -->
        <div class="firmas-container mt-5">
            <table width="100%">
                <tr>
                    <td width="100%" align="center">
                        <div class="firma-line"></div>
                        <div class="firma-name font-weight-bold">
                            {{ elaboradoPor || 'Líder de Auditoría' }}
                        </div>
                        <div class="firma-cargo small">Auditor Líder</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    auditData: { type: Object, required: true },
    auditados: { type: Array, default: () => [] },
    procesosAuditados: { type: Array, default: () => [] },
    hallazgosConformidad: { type: Array, default: () => [] },
    hallazgosNoConformidad: { type: Array, default: () => [] },
    oportunidadesMejora: { type: Array, default: () => [] },
    informe: { type: Object, required: true },
    elaboradoPor: { type: String, default: '' },
    aprobadoPor: { type: String, default: '' }
});

const logoUrl = '/images/logo.png';
const currentDate = new Date().toLocaleDateString();

// Computed for formatting criteria
const formattedCriterios = computed(() => {
    if (!props.informe.alcance_criterios) return 'No especificado';
    return props.informe.alcance_criterios.replace(/\n/g, ', ');
});

</script>

<style scoped>
.print-document {
    padding: 0;
    color: #333;
    font-family: 'Arial', sans-serif;
    line-height: 1.5;
}

.header-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    border: 2px solid #000;
}

.header-table td {
    padding: 10px;
    border: 1px solid #000;
    vertical-align: middle;
}

.logo {
    max-height: 50px;
}

.title {
    text-align: center;
    font-weight: bold;
    font-size: 16px;
    text-transform: uppercase;
}

.meta {
    font-size: 11px;
}

.section-title {
    background-color: #f2f2f2;
    padding: 5px 10px;
    font-weight: bold;
    border: 1px solid #ccc;
    margin-top: 15px;
    margin-bottom: 5px;
    text-transform: uppercase;
    font-size: 13px;
}

.sub-section-title {
    font-weight: bold;
    border-bottom: 1px solid #eee;
    padding-bottom: 2px;
    margin-bottom: 8px;
    font-size: 12px;
    color: #444;
}

.content-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 15px;
}

.content-table th,
.content-table td {
    border: 1px solid #ccc;
    padding: 6px 10px;
    font-size: 12px;
    text-align: left;
}

.content-table th {
    background-color: #f9f9f9;
}

.content-box {
    border: 1px solid #ccc;
    padding: 10px;
    font-size: 12px;
    white-space: pre-wrap;
    min-height: 40px;
}

.hallazgos-list {
    margin-top: 0;
    margin-bottom: 10px;
    padding-left: 20px;
}

.hallazgos-list li {
    font-size: 12px;
    margin-bottom: 5px;
}

.firmas-container {
    margin-top: 40px;
}

.firma-line {
    width: 200px;
    border-bottom: 1px solid #000;
    margin-bottom: 5px;
}

.firma-name {
    font-size: 12px;
}

.firma-cargo {
    font-size: 10px;
    color: #666;
}

@media print {
    .print-document {
        width: 100%;
    }
}
</style>
