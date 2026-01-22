<template>
    <div class="container-fluid home-dashboard">
        <div class="row mb-4">
            <div class="col-12 text-center mt-5 mb-4">
                <h1 class="font-weight-bold text-dark display-4">Sistema Integrado de Gestión</h1>
                <p class="lead text-muted">Seleccione un módulo para comenzar a trabajar</p>
                <div class="divider mx-auto custom-divider"></div>
            </div>
        </div>

        <div class="row justify-content-center">
            <!-- Documentación -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" v-if="authStore.canAccessModule('documentacion')">
                <div class="card h-100 module-card shadow-sm border-0" @click="navigateTo('documentos.index')">
                    <div class="card-body text-center d-flex flex-column align-items-center justify-content-center p-4">
                        <div class="icon-circle bg-light-blue mb-3">
                            <i class="fas fa-file-alt fa-3x text-primary"></i>
                        </div>
                        <h4 class="card-title font-weight-bold text-dark">Documentación</h4>
                        <p class="card-text text-muted small">Gestión y control de documentos del sistema.</p>
                        <button class="btn btn-outline-primary btn-sm mt-auto rounded-pill px-4">Ingresar</button>
                    </div>
                </div>
            </div>

            <!-- Procesos -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" v-if="authStore.canAccessModule('procesos')">
                <div class="card h-100 module-card shadow-sm border-0" @click="navigateTo('inventario.gestion')">
                    <div class="card-body text-center d-flex flex-column align-items-center justify-content-center p-4">
                        <div class="icon-circle bg-light-orange mb-3">
                            <i class="fas fa-project-diagram fa-3x text-orange"></i>
                        </div>
                        <h4 class="card-title font-weight-bold text-dark">Procesos</h4>
                        <p class="card-text text-muted small">Mapa de procesos, fichas y diagramas.</p>
                        <button class="btn btn-outline-orange btn-sm mt-auto rounded-pill px-4">Ingresar</button>
                    </div>
                </div>
            </div>

            <!-- Requerimientos -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" v-if="authStore.canAccessModule('requerimientos')">
                <div class="card h-100 module-card shadow-sm border-0" @click="navigateTo('requerimientos.index')">
                    <div class="card-body text-center d-flex flex-column align-items-center justify-content-center p-4">
                        <div class="icon-circle bg-light-purple mb-3">
                            <i class="fas fa-tasks fa-3x text-purple"></i>
                        </div>
                        <h4 class="card-title font-weight-bold text-dark">Requerimientos</h4>
                        <p class="card-text text-muted small">Gestión de tickets y solicitudes de servicio.</p>
                        <button class="btn btn-outline-purple btn-sm mt-auto rounded-pill px-4">Ingresar</button>
                    </div>
                </div>
            </div>

            <!-- Gestión de la Mejora (Hallazgos) -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" v-if="authStore.canAccessModule('mejora')">
                <div class="card h-100 module-card shadow-sm border-0" @click="navigateTo('hallazgos.index')">
                    <div class="card-body text-center d-flex flex-column align-items-center justify-content-center p-4">
                        <div class="icon-circle bg-light-success mb-3">
                            <i class="fas fa-clipboard-check fa-3x text-success"></i>
                        </div>
                        <h4 class="card-title font-weight-bold text-dark">Mejora Continua</h4>
                        <p class="card-text text-muted small">Control de hallazgos y planes de acción.</p>
                        <button class="btn btn-outline-success btn-sm mt-auto rounded-pill px-4">Ingresar</button>
                    </div>
                </div>
            </div>

            <!-- Riesgos -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" v-if="authStore.canAccessModule('riesgos')">
                <div class="card h-100 module-card shadow-sm border-0" @click="navigateTo('riesgos.index')">
                    <div class="card-body text-center d-flex flex-column align-items-center justify-content-center p-4">
                        <div class="icon-circle bg-light-danger mb-3">
                            <i class="fas fa-exclamation-triangle fa-3x text-danger"></i>
                        </div>
                        <h4 class="card-title font-weight-bold text-dark">Gestión de Riesgos</h4>
                        <p class="card-text text-muted small">Identificación y tratamiento de riesgos.</p>
                        <button class="btn btn-outline-danger btn-sm mt-auto rounded-pill px-4">Ingresar</button>
                    </div>
                </div>
            </div>

            <!-- Obligaciones -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" v-if="authStore.canAccessModule('obligaciones')">
                <div class="card h-100 module-card shadow-sm border-0" @click="navigateTo('obligaciones.index')">
                    <div class="card-body text-center d-flex flex-column align-items-center justify-content-center p-4">
                        <div class="icon-circle bg-light-info mb-3">
                            <i class="fas fa-balance-scale fa-3x text-info"></i>
                        </div>
                        <h4 class="card-title font-weight-bold text-dark">Obligaciones</h4>
                        <p class="card-text text-muted small">Cumplimiento normativa y legal.</p>
                        <button class="btn btn-outline-info btn-sm mt-auto rounded-pill px-4">Ingresar</button>
                    </div>
                </div>
            </div>


            <!-- Auditoría -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" v-if="authStore.canAccessModule('auditoria')">
                <div class="card h-100 module-card shadow-sm border-0" @click="navigateTo('programa.index')">
                    <div class="card-body text-center d-flex flex-column align-items-center justify-content-center p-4">
                        <div class="icon-circle bg-light-teal mb-3">
                            <i class="fas fa-search-dollar fa-3x text-teal"></i>
                        </div>
                        <h4 class="card-title font-weight-bold text-dark">Auditoría</h4>
                        <p class="card-text text-muted small">Planificación y ejecución de auditorías.</p>
                        <button class="btn btn-outline-teal btn-sm mt-auto rounded-pill px-4">Ingresar</button>
                    </div>
                </div>
            </div>

            <!-- Continuidad -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" v-if="authStore.canAccessModule('continuidad')">
                <div class="card h-100 module-card shadow-sm border-0" @click="navigateTo('continuidad.planes')">
                    <div class="card-body text-center d-flex flex-column align-items-center justify-content-center p-4">
                        <div class="icon-circle bg-light-warning mb-3">
                            <i class="fas fa-infinity fa-3x text-warning"></i>
                        </div>
                        <h4 class="card-title font-weight-bold text-dark">Continuidad</h4>
                        <p class="card-text text-muted small">Planes de continuidad de negocio.</p>
                        <button class="btn btn-outline-warning btn-sm mt-auto rounded-pill px-4">Ingresar</button>
                    </div>
                </div>
            </div>

            <!-- Satisfacción (Cliente) -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" v-if="authStore.canAccessModule('satisfaccion')">
                <div class="card h-100 module-card shadow-sm border-0" @click="navigateTo('encuestas.index')">
                    <div class="card-body text-center d-flex flex-column align-items-center justify-content-center p-4">
                        <div class="icon-circle bg-light-pink mb-3">
                            <i class="fas fa-smile fa-3x text-pink"></i>
                        </div>
                        <h4 class="card-title font-weight-bold text-dark">Satisfacción</h4>
                        <p class="card-text text-muted small">Encuestas y reportes de satisfacción.</p>
                        <button class="btn btn-outline-pink btn-sm mt-auto rounded-pill px-4">Ingresar</button>
                    </div>
                </div>
            </div>

            <!-- Revisión Dirección -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" v-if="authStore.canAccessModule('direccion')">
                <div class="card h-100 module-card shadow-sm border-0" @click="navigateTo('revision-direccion.index')">
                    <div class="card-body text-center d-flex flex-column align-items-center justify-content-center p-4">
                        <div class="icon-circle bg-light-indigo mb-3">
                            <i class="fas fa-chart-line fa-3x text-indigo"></i>
                        </div>
                        <h4 class="card-title font-weight-bold text-dark">Alta Dirección</h4>
                        <p class="card-text text-muted small">Revisiones por la dirección e informes.</p>
                        <button class="btn btn-outline-indigo btn-sm mt-auto rounded-pill px-4">Ingresar</button>
                    </div>
                </div>
            </div>

            <!-- Administración -->
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4" v-if="authStore.canAccessModule('administracion')">
                <div class="card h-100 module-card shadow-sm border-0"
                    @click="navigateTo('administracion.usuarios.index')">
                    <div class="card-body text-center d-flex flex-column align-items-center justify-content-center p-4">
                        <div class="icon-circle bg-light-dark mb-3">
                            <i class="fas fa-cogs fa-3x text-dark"></i>
                        </div>
                        <h4 class="card-title font-weight-bold text-dark">Administración</h4>
                        <p class="card-text text-muted small">Gestión de usuarios y configuraciones.</p>
                        <button class="btn btn-outline-dark btn-sm mt-auto rounded-pill px-4">Ingresar</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import { useAuthStore } from '../stores/authStore';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const navigateTo = (routeName) => {
    router.push({ name: routeName });
};
</script>

<style scoped>
.home-dashboard {
    background-color: #f4f6f9;
    min-height: calc(100vh - 57px);
    /* Adjust based on navbar height */
    padding-bottom: 50px;
}

.custom-divider {
    height: 4px;
    width: 60px;
    background-color: #dc3545;
    border-radius: 2px;
}

.module-card {
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    cursor: pointer;
    background: #fff;
    border-radius: 12px;
}

.module-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 14px 28px rgba(0, 0, 0, 0.1), 0 10px 10px rgba(0, 0, 0, 0.08) !important;
}

.icon-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.module-card:hover .icon-circle {
    transform: scale(1.1);
}

/* Custom Colors for Icons/Buttons */
.bg-light-blue {
    background-color: #e3f2fd;
}

.bg-light-orange {
    background-color: #fff3e0;
}

.bg-light-purple {
    background-color: #f3e5f5;
}

.bg-light-success {
    background-color: #e8f5e9;
}

.bg-light-danger {
    background-color: #ffebee;
}

.bg-light-info {
    background-color: #e1f5fe;
}

.bg-light-teal {
    background-color: #e0f2f1;
}

.bg-light-warning {
    background-color: #fff8e1;
}

.bg-light-pink {
    background-color: #fce4ec;
}

.bg-light-indigo {
    background-color: #e8eaf6;
}

.bg-light-dark {
    background-color: #eceff1;
}

.text-orange {
    color: #ff9800;
}

.text-purple {
    color: #9c27b0;
}

.text-teal {
    color: #009688;
}

.text-pink {
    color: #e91e63;
}

.text-indigo {
    color: #3f51b5;
}

.btn-outline-orange {
    color: #ff9800;
    border-color: #ff9800;
}

.btn-outline-orange:hover {
    background-color: #ff9800;
    color: #fff;
}

.btn-outline-purple {
    color: #9c27b0;
    border-color: #9c27b0;
}

.btn-outline-purple:hover {
    background-color: #9c27b0;
    color: #fff;
}

.btn-outline-success:hover {
    background-color: #28a745;
    color: #fff;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    color: #fff;
}

.btn-outline-info:hover {
    background-color: #17a2b8;
    color: #fff;
}

.btn-outline-teal {
    color: #009688;
    border-color: #009688;
}

.btn-outline-teal:hover {
    background-color: #009688;
    color: #fff;
}

.btn-outline-warning {
    color: #ffc107;
    border-color: #ffc107;
}

.btn-outline-warning:hover {
    background-color: #ffc107;
    color: #fff;
}

.btn-outline-pink {
    color: #e91e63;
    border-color: #e91e63;
}

.btn-outline-pink:hover {
    background-color: #e91e63;
    color: #fff;
}

.btn-outline-indigo {
    color: #3f51b5;
    border-color: #3f51b5;
}

.btn-outline-indigo:hover {
    background-color: #3f51b5;
    color: #fff;
}

.btn-outline-dark:hover {
    background-color: #343a40;
    color: #fff;
}
</style>
