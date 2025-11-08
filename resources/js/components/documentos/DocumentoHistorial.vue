<template>
    <div>
        <div class="header-container">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">{{ documentoStore.documentoForm.nombre_documento || 'Documento' }}</span>
                <span class="mx-2 text-secondary"><i class="fas fa-chevron-right fa-xs"></i></span>
                <span class="text-dark">Historial de Cambios (Trazabilidad)</span>
            </h6>
        </div>

        <div class="form-overlay-container mt-4">
            <div v-if="documentoStore.loadingHistorial" class="loading-overlay">
                <div class="spinner-border text-danger" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>

            <div v-if="!documentoStore.historial.length && !documentoStore.loadingHistorial" class="text-muted small">
                No hay registros en el historial para este documento.
            </div>
            <div v-else class="table-responsive">
              <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in documentoStore.historial" :key="item.id">
                            <td class="text-nowrap">{{ formatDateTime(item.created_at) }}</td>
                            <td>{{ item.usuario ? item.usuario.codigo : 'Sistema' }}</td>
                            <td><span class="badge" :class="getBadgeClass(item.accion)">{{ item.accion }}</span></td>
                            <td>{{ item.descripcion }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useDocumentoStore } from '@/stores/documentoStore';

const documentoStore = useDocumentoStore();

onMounted(() => {
    documentoStore.fetchHistorial();
});

// Función de utilidad para formatear la fecha
const formatDateTime = (dateTimeString) => {
    if (!dateTimeString) return 'N/A';
    const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' };
    return new Date(dateTimeString).toLocaleString('es-PE', options);
};

// Función de utilidad para dar color a las acciones
const getBadgeClass = (accion) => {
    switch (accion.toUpperCase()) {
        case 'CREACIÓN': return 'bg-success text-white';
        case 'ACTUALIZACIÓN': return 'bg-warning text-dark';
        case 'NUEVA VERSIÓN': return 'bg-info text-white';
        case 'ELIMINACIÓN': return 'bg-danger text-white';
        default: return 'bg-secondary text-white';
    }
};
</script>

<style scoped>
/* Estilos similares a los otros componentes */
.form-overlay-container {
    position: relative;
    min-height: 150px;
}
.table th,
.table td {
    font-size: 0.8rem;
    vertical-align: middle;
}

.table td input[type="checkbox"] {
    transform: scale(0.9);
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

.header-container {
    padding: 0.75rem;
    margin-bottom: 1.5rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #ff851b;
    display: flex;
    align-items: center;
}

.badge {
    font-size: 0.4;
}
</style>