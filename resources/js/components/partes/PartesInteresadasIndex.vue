<template>
    <div class="container-fluid fade-in py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white shadow-sm py-2 px-3 rounded-lg border mb-4">
                <li class="breadcrumb-item"><router-link to="/home"
                        class="text-danger font-weight-bold">Inicio</router-link></li>
                <li class="breadcrumb-item active" aria-current="page">Partes Interesadas</li>
            </ol>
        </nav>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0 font-weight-bold text-dark">Partes Interesadas</h5>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <button class="btn btn-info btn-sm shadow-sm mr-2" @click="showMapModal = true">
                            <i class="fas fa-sitemap mr-1"></i> Mapa de Actores
                        </button>
                        <button class="btn btn-primary btn-sm shadow-sm" @click="store.openFormModal()">
                            <i class="fas fa-plus-circle mr-1"></i> Nueva Parte
                        </button>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="mt-3">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link" :class="{ active: filterNorma === 'all' }" href="#"
                                @click.prevent="filterNorma = 'all'">Todos</a>
                        </li>
                        <li class="nav-item" v-for="norma in normasList" :key="norma">
                            <a class="nav-link" :class="{ active: filterNorma === norma }" href="#"
                                @click.prevent="filterNorma = norma">{{ norma }}</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="h-1 mb-2">
                    <ProgressBar v-if="store.loading" mode="indeterminate" style="height: 4px;" />
                </div>

                <div class="p-3">
                    <DataTable :value="filteredPartes" :paginator="true" :rows="10" responsiveLayout="scroll"
                        :class="{ 'opacity-50 pointer-events-none': store.loading }" class="p-datatable-sm"
                        :rowsPerPageOptions="[5, 10, 20]"
                        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                        currentPageReportTemplate="{first}-{last} de {totalRecords}">

                        <template #empty>
                            <div class="text-center py-5">
                                <img :src="'/images/empty-state.svg'" alt="No data"
                                    style="max-width: 150px; opacity: 0.5" class="mb-3">
                                <h6 class="text-muted">No se encontraron partes interesadas</h6>
                                <p class="small text-muted">Comienza agregando una nueva parte interesada.</p>
                            </div>
                        </template>

                        <Column field="pi_nombre" header="Nombre">
                            <template #body="slotProps">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-initial rounded-circle mr-2 shadow-sm d-flex align-items-center justify-content-center text-white font-weight-bold"
                                        :class="getAvatarColor(slotProps.data.pi_nombre)"
                                        style="width: 28px; height: 28px; min-width: 28px; font-size: 11px;">
                                        {{ getInitials(slotProps.data.pi_nombre) }}
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark">{{ slotProps.data.pi_nombre }}</div>
                                        <div class="small text-muted text-truncate" style="max-width: 200px">{{
                                            slotProps.data.pi_descripcion }}</div>
                                    </div>
                                </div>
                            </template>
                        </Column>

                        <Column field="pi_tipo" header="Tipo">
                            <template #body="slotProps">
                                <span class="badge badge-pill badge-soft-secondary">{{
                                    slotProps.data.pi_tipo.toUpperCase() }}</span>
                            </template>
                        </Column>

                        <Column header="Matriz Poder/Interés">
                            <template #body="slotProps">
                                <div v-if="calculateCuadrante(slotProps.data)" class="d-flex align-items-center">
                                    <div class="quadrant-badge mr-2 text-center text-white font-weight-bold rounded-circle d-flex align-items-center justify-content-center"
                                        :class="getCuadranteClass(calculateCuadrante(slotProps.data).cuadrante)"
                                        style="width: 28px; height: 28px; font-size: 12px;">
                                        {{ calculateCuadrante(slotProps.data).cuadrante }}
                                    </div>
                                    <span class="small font-weight-bold"
                                        :class="getTextClass(calculateCuadrante(slotProps.data).cuadrante)">
                                        {{ calculateCuadrante(slotProps.data).valoracion }}
                                    </span>
                                </div>
                                <span v-else class="badge badge-light">Sin Clasificar</span>
                            </template>
                        </Column>

                        <Column header="Normas">
                            <template #body="slotProps">
                                <div class="d-flex flex-wrap">
                                    <span v-for="norma in getNormas(slotProps.data)" :key="norma"
                                        class="badge badge-outlined-info mr-1 mb-1">
                                        {{ norma }}
                                    </span>
                                </div>
                            </template>
                        </Column>

                        <Column header="Requisitos" style="width: 100px; text-align: center">
                            <template #body="slotProps">
                                <span class="font-weight-bold text-dark">
                                    {{ getRequisitosImplementados(slotProps.data) }}/{{
                                        getTotalRequisitos(slotProps.data) }}
                                </span>
                            </template>
                        </Column>

                        <Column header="% Cumplimiento" style="width: 110px">
                            <template #body="slotProps">
                                <div class="text-center">
                                    <div class="font-weight-bold text-dark mb-1" style="font-size: 10px;">
                                        {{ getCumplimientoPercentage(slotProps.data) }}%
                                    </div>
                                    <div class="progress rounded-pill shadow-sm"
                                        style="height: 6px; background-color: #e9ecef;">
                                        <div class="progress-bar rounded-pill"
                                            :class="getProgressBarClass(getCumplimientoPercentage(slotProps.data))"
                                            :style="{ width: getCumplimientoPercentage(slotProps.data) + '%' }"
                                            role="progressbar"
                                            :aria-valuenow="getCumplimientoPercentage(slotProps.data)" aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Column>

                        <Column header="Acciones" style="width: 160px; text-align: center">
                            <template #body="slotProps">
                                <div class="d-flex justify-content-center gap-2">

                                    <Button icon="pi pi-pencil"
                                        class="p-button-rounded p-button-warning p-button-text p-button-sm"
                                        @click="store.openFormModal(slotProps.data)" v-tooltip.top="'Editar'" />
                                    <Button icon="pi pi-list"
                                        class="p-button-rounded p-button-info p-button-text p-button-sm"
                                        @click="openReqModal(slotProps.data)" v-tooltip.top="'Requisitos'" />
                                    <Button icon="pi pi-trash"
                                        class="p-button-rounded p-button-danger p-button-text p-button-sm"
                                        @click="store.deleteParte(slotProps.data.id)" v-tooltip.top="'Eliminar'" />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>

        <!-- Modal Form -->
        <ParteInteresadaForm />

        <!-- Modal Mapa Actores -->
        <MapaActoresModal :visible="showMapModal" :partes="store.partes" @close="showMapModal = false" />

        <!-- Modal Requisitos -->
        <PartesRequisitosModal :visible="showReqModal" :parte="selectedParte" :filter-context="filterNorma"
            @close="showReqModal = false" />

    </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import { useParteStore } from '@/stores/parteInteresadaStore';
import ParteInteresadaForm from './ParteInteresadaForm.vue';
import MapaActoresModal from './MapaActoresModal.vue';
import PartesRequisitosModal from './PartesRequisitosModal.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import ProgressBar from 'primevue/progressbar';
import LoadingState from '@/components/generales/LoadingState.vue';

const store = useParteStore();
const filterNorma = ref('all');
const showMapModal = ref(false);
const showReqModal = ref(false);
const selectedParte = ref(null);

const normasList = ['ISO 9001', 'ISO 37001', 'ISO 37301', 'ISO 27001', 'ISO 21001', 'Innovación'];

onMounted(() => {
    store.fetchPartes();
});

const openReqModal = (parte) => {
    selectedParte.value = parte;
    showReqModal.value = true;
};

const getInitials = (name) => {
    if (!name) return '';
    const parts = name.split(' ').filter(part => part.length > 2 || part.toUpperCase() === part); // Basic filter for connectors like "de", "la" unless they are part of a name? 
    // Actually, simpler: take first two words.
    // User Example: PJ Poder Judicial -> PJ.
    const words = name.trim().split(/\s+/);
    if (words.length === 1) return words[0].substring(0, 2).toUpperCase();
    return (words[0][0] + words[1][0]).toUpperCase();
};

const getAvatarColor = (name) => {
    // Basic hash for color
    const colors = ['bg-primary', 'bg-danger', 'bg-success', 'bg-warning text-dark', 'bg-info', 'bg-secondary'];
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    const index = Math.abs(hash) % colors.length;
    return colors[index];
};

const calculateCuadrante = (parte) => {
    if (!parte.pi_nivel_influencia || !parte.pi_nivel_interes) return null;
    const inf = parte.pi_nivel_influencia.toLowerCase();
    const int = parte.pi_nivel_interes.toLowerCase();

    // Matrix Logic
    // Infl (Y) | Int (X)
    // High=3, Med=2, Low=1

    let c = '';

    if (inf === 'alto') {
        if (int === 'alto' || int === 'medio') c = 'I'; // C1: High/High, High/Med
        else c = 'II'; // C2: High/Low
    } else if (inf === 'medio') {
        if (int === 'alto') c = 'I'; // C1: Med/High
        else if (int === 'medio') c = 'III'; // C3: Med/Med
        else c = 'II'; // C2: Med/Low
    } else { // Low
        if (int === 'alto') c = 'III'; // C3: Low/High
        else c = 'IV'; // C4: Low/Med, Low/Low
    }

    /* 
       Mapping:
       C1 = I (Jugador Clave)
       C2 = II (Satisfacer)
       C3 = III (Informar)
       C4 = IV (Monitorear)
    */

    let label = '';
    if (c === 'I') label = 'Jugador Clave';
    else if (c === 'II') label = 'Satisfacer';
    else if (c === 'III') label = 'Informar';
    else if (c === 'IV') label = 'Monitorear';

    return {
        cuadrante: c,
        valoracion: label
    };
};

const getCuadranteClass = (c) => {
    switch (c) {
        case 'I': return 'bg-danger text-white shadow-sm';
        case 'II': return 'bg-warning text-dark shadow-sm';
        case 'III': return 'bg-info text-white shadow-sm'; // Teal/Cyan (Informar)
        case 'IV': return 'bg-secondary text-white shadow-sm'; // Grey (Monitorear)
        default: return 'bg-light text-muted';
    }
};

const getTextClass = (c) => {
    switch (c) {
        case 'I': return 'text-danger';
        case 'II': return 'text-warning-dark'; // Custom?
        default: return 'text-muted';
    }
}



const getNormas = (parte) => {
    if (!parte.expectativas) return [];
    const allNormas = parte.expectativas.flatMap(e => e.exp_normas || []);
    return [...new Set(allNormas)];
};

const filteredPartes = computed(() => {
    if (filterNorma.value === 'all') return store.partes;

    return store.partes.filter(parte => {
        const normas = getNormas(parte);
        return normas.some(n => n.includes(filterNorma.value) || filterNorma.value.includes(n));
    });
});

const getFilteredExpectativas = (parte) => {
    if (!parte.expectativas) return [];
    if (filterNorma.value === 'all') return parte.expectativas;

    return parte.expectativas.filter(e => {
        const normas = e.exp_normas || [];
        return normas.some(n => n.includes(filterNorma.value) || filterNorma.value.includes(n));
    });
};

const getTotalRequisitos = (parte) => {
    return getFilteredExpectativas(parte).length;
};

const getRequisitosImplementados = (parte) => {
    const filtered = getFilteredExpectativas(parte);
    return filtered.filter(e => e.exp_estado === 'implementado').length;
};

const getCumplimientoPercentage = (parte) => {
    const total = getTotalRequisitos(parte);
    if (total === 0) return 0;
    const implementados = getRequisitosImplementados(parte);
    return Math.round((implementados / total) * 100);
};

const getProgressBarClass = (percentage) => {
    if (percentage >= 80) return 'bg-success';
    if (percentage >= 50) return 'bg-warning';
    if (percentage > 0) return 'bg-danger';
    return 'bg-secondary';
};
</script>

<style scoped>
/* Standard Bootstrap/AdminLTE Overrides if needed */
.card-header-tabs {
    margin-bottom: -1rem;
    /* Align tabs with bottom border */
    border-bottom: 0;
}

.nav-tabs .nav-link {
    border: none;
    border-bottom: 2px solid transparent;
    color: #6c757d;
}

.nav-tabs .nav-link:hover {
    color: #495057;
}

.nav-tabs .nav-link.active {
    color: #dc3545;
    border-bottom: 2px solid #dc3545;
    background: transparent;
    font-weight: bold;
}
</style>
