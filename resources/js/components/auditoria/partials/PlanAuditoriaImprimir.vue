<template>
    <div class="print-document">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td width="20%"><img :src="logoUrl" class="logo"></td>
                <td width="60%" class="title">PLAN DE AUDITORÍA {{ auditData.ae_codigo }}</td>
                <td width="20%" class="meta">
                    F. Emisión: {{ currentDate }}<br>
                    Versión: 01
                </td>
            </tr>
        </table>

        <!-- 1. OBJETIVO Y ALCANCE -->
        <div class="section-title">1. OBJETIVO Y ALCANCE</div>
        <table class="content-table">
            <tr>
                <th width="25%">Objetivo</th>
                <td>{{ auditData.ae_objetivo }}</td>
            </tr>
            <tr>
                <th>Alcance</th>
                <td>{{ auditData.ae_alcance }}</td>
            </tr>
            <tr>
                <th>Criterios (Normas)</th>
                <td>{{ formatSystem(auditData.ae_sistema) }}</td>
            </tr>
            <tr>
                <th>Fecha de Auditoría</th>
                <td>{{ formatDate(auditData.ae_fecha_inicio) }} - {{ formatDate(auditData.ae_fecha_fin) }}</td>
            </tr>
        </table>

        <!-- 2. EQUIPO AUDITOR -->
        <div class="section-title">2. EQUIPO AUDITOR</div>
        <table class="content-table">
            <thead>
                <tr>
                    <th width="25%">Rol</th>
                    <th width="35%">Nombre</th>
                    <th width="10%">Siglas</th>
                    <th width="30%">Correo</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(member, index) in equipo" :key="index">
                    <td>{{ member.aeq_rol }}</td>
                    <td>{{ member.usuario ? member.usuario.name : 'Desconocido' }}</td>
                    <td>{{ getInitials(member.usuario ? member.usuario.name : '') }}</td>
                    <td>{{ member.usuario ? member.usuario.email : '' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- 3. AGENDA -->
        <div class="section-title">3. AGENDA (CRONOGRAMA)</div>
        <table class="content-table agenda-table">
            <thead>
                <tr>
                    <th width="12%">Fecha</th>
                    <th width="12%">Horario</th>
                    <th>Actividad / Proceso</th>
                    <th width="15%">Auditor</th>
                    <th width="20%">Requisitos</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, idx) in agenda" :key="idx">
                    <td class="text-center">{{ formatDateShort(item.date) }}</td>
                    <td class="text-center">{{ item.timeStart.substring(0, 5) }} - {{ item.timeEnd.substring(0, 5) }}</td>
                    <td>{{ item.activity }}</td>
                    <td class="text-center">{{ item.auditor }}</td>
                    <td class="requisitos">{{ item.requisitos }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Signatures -->
        <div class="signatures">
            <div class="sig-box">
                Firma del Auditor Líder
            </div>
            <div class="sig-box">
                Firma del Coordinador / Responsable SIG
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    auditData: { type: Object, default: () => ({}) },
    equipo: { type: Array, default: () => [] },
    agenda: { type: Array, default: () => [] }
});

const logoUrl = '/images/logo.png';

const currentDate = new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });

const formatSystem = (val) => {
    if (Array.isArray(val)) return val.join(', ');
    return val;
};

const formatDate = (val) => {
    if (!val) return '';
    return new Date(val).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const formatDateShort = (val) => {
    if (!val) return '';
    const d = new Date(val + 'T00:00:00'); // Ensure timezone consistency
    return d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit' });
};

const getInitials = (name) => {
    if (!name) return '';
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
};
</script>

<style scoped>
.print-document {
    font-family: Arial, sans-serif;
    font-size: 11pt;
    padding: 20px;
    background: white;
    max-width: 210mm;
    margin: 0 auto;
}

.header-table {
    width: 100%;
    border-bottom: 3px solid #dc3545;
    margin-bottom: 20px;
    padding-bottom: 10px;
}

.logo {
    max-height: 50px;
}

.title {
    font-size: 14pt;
    font-weight: bold;
    color: #dc3545;
    text-align: center;
    margin: 0;
}

.meta {
    font-size: 8pt;
    text-align: right;
}

.section-title {
    background-color: #f8f9fa;
    padding: 5px;
    font-weight: bold;
    font-size: 11pt;
    border: 1px solid #dee2e6;
    border-bottom: none;
    margin-top: 20px;
}

.content-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11pt;
}

.content-table th,
.content-table td {
    border: 1px solid #dee2e6;
    padding: 5px;
    vertical-align: top;
}

.content-table th {
    background-color: #fff;
    text-align: left;
    font-weight: bold;
}

.agenda-table {
    font-size: 9pt;
}

.agenda-table th {
    background-color: #dc3545;
    color: white;
    text-align: center;
}

.agenda-table td {
    font-size: 9pt;
}

.requisitos {
    font-size: 8pt !important;
}

.text-center {
    text-align: center;
}

.signatures {
    margin-top: 100px;
    width: 100%;
    display: flex;
    justify-content: space-around;
}

.sig-box {
    width: 40%;
    border-top: 1px solid black;
    text-align: center;
    padding-top: 5px;
}

@media print {
    .print-document {
        padding: 0;
        margin: 0;
        max-width: 100%;
    }
}
</style>
