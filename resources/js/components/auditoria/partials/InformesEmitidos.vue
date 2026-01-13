<template>
    <div>
        <div class="header-container mb-4">
            <h6 class="mb-0 d-flex align-items-center">
                <span class="text-dark">Informes Emitidos</span>
            </h6>
        </div>

        <div class="card border-0 shadow-none">
            <div class="card-body text-center py-5">
                <i class="fas fa-file-pdf fa-4x text-light mb-3"></i>
                <h5 class="text-secondary font-weight-bold">Gestión de Informes</h5>
                <p class="text-muted small mb-4 italic">Esta funcionalidad permitirá el registro y seguimiento de los
                    informes de auditoría (Preliminar, Final y Plan de Acción).</p>
                <div class="alert alert-warning d-inline-block small">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Módulo en desarrollo
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
</script>

<style scoped>
.header-container {
    padding: 0.75rem 1rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-left: 0.5rem solid #ff851b;
    display: flex;
    align-items: center;
}
</style>
