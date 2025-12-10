<template>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="#" class="brand-link">
            <img :src="'/vendor/adminlte/dist/img/kallpaq_ico.png'" alt="AdminLTE Logo"
                class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light"> KALLPAQ v1.0</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel mt-2 pb-2 mb-2 d-flex">
                <div class="info" v-if="authStore.isAuthenticated">
                    <a href="#" class="d-block text-white">
                        <i class="far fa-user nav-icon"></i>
                        {{ authStore.user.name }}
                    </a>
                    <div class="small text-right mt-1 text-white">
                        <span>
                            Rol: {{ userRole }}
                        </span>
                        <span class="mx-1">|</span>
                        <a href="/logout" class="text-white" @click.prevent="logout">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            Cerrar Sesión
                        </a>
                    </div>
                </div>
                <div class="info" v-else>
                    <router-link to="/login" class="d-block text-white">
                        <i class="far fa-user nav-icon"></i>
                        Iniciar Sesión
                    </router-link>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    
                    <!-- Documentación por Procesos -->
                    <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('documentacion') }">
                        <a href="#" class="nav-link" @click.prevent="toggleMenu('documentacion')">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>
                                Documentación por Procesos
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" v-show="isMenuOpen('documentacion')">
                            <li class="nav-item">
                                <router-link to="/inventario-publico/0" class="nav-link">
                                    <i class="nav-icon fas fa-book"></i>
                                    <p>Inventario de Procesos</p>
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link to="/procesos/mapa" class="nav-link">
                                    <i class="nav-icon fas fa-sitemap"></i>
                                    <p>Mapa de Procesos</p>
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link to="/documentos/listado" class="nav-link">
                                    <i class="nav-icon fas fa-clipboard-list"></i>
                                    <p>Listado de documentos</p>
                                </router-link>
                            </li>
                        </ul>
                    </li>

                    <template v-if="authStore.isAuthenticated">
                        <!-- Gestión de Requerimientos -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('requerimientos') }"
                            v-if="hasAnyRole(['facilitador', 'subgerente', 'especialista', 'supervisor', 'admin'])">
                            <a href="#" class="nav-link" @click.prevent="toggleMenu('requerimientos')">
                                <i class="nav-icon fas fa-tasks"></i>
                                <p>
                                    Gestión de Requerimientos
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('requerimientos')">
                                <li class="nav-item" v-if="hasAnyRole(['facilitador', 'subgerente'])">
                                    <router-link to="/requerimientos/crear" class="nav-link">
                                        <i class="far fa-edit nav-icon"></i>
                                        <p>Crear Requerimiento</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="hasAnyRole(['facilitador', 'subgerente', 'especialista', 'supervisor', 'admin'])">
                                    <router-link to="/mis-requerimientos" class="nav-link">
                                        <i class="fas fa-user-check nav-icon"></i>
                                        <p>Mis Requerimientos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="hasAnyRole(['facilitador', 'subgerente', 'especialista'])">
                                    <router-link :to="{ name: 'requerimientos.asignados', params: { rol: userRole } }" class="nav-link">
                                        <i class="fas fa-folder-open nav-icon fa-xs"></i>
                                        <p>Requerimientos Asignados</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="hasAnyRole(['facilitador', 'subgerente', 'especialista'])">
                                    <router-link :to="{ name: 'requerimientos.atendidos', params: { rol: userRole } }" class="nav-link">
                                        <i class="fas fa-check-circle fa-xs nav-icon"></i>
                                        <p>Requerimientos Atendidos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="hasAnyRole(['supervisor', 'admin'])">
                                    <router-link to="/requerimientos/index" class="nav-link">
                                        <i class="fas fa-folder-open nav-icon fa-xs"></i>
                                        <p>Bandeja de Requerimientos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link :to="{ name: 'requerimientos.seguimiento', params: { rol: userRole } }" class="nav-link">
                                        <i class="fas fa-tachometer-alt fa-xs nav-icon"></i>
                                        <p>Dashboard Requerimientos</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Gestión por Procesos -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('procesos') }">
                            <a href="#" class="nav-link" @click.prevent="toggleMenu('procesos')">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>
                                    Gestión por Procesos
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('procesos')">
                                <li class="nav-item">
                                    <router-link to="/inventario-gestion" class="nav-link">
                                        <i class="nav-icon fas fa-cubes"></i>
                                        <p>Gestión del Inventario</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/procesos/index" class="nav-link">
                                        <i class="nav-icon fas fa-clipboard-list"></i>
                                        <p>Listado de Procesos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/documentos" class="nav-link">
                                        <i class="nav-icon fas fa-clipboard-list"></i>
                                        <p>Listado de Documentos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/indicadores-gestion" class="nav-link">
                                        <i class="nav-icon fas fa-chart-bar"></i>
                                        <p>Listado de Indicadores</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/partes" class="nav-link">
                                        <i class="nav-icon fas fa-users"></i>
                                        <p>Partes Interesadas</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/dashboard/procesos" class="nav-link">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>Dashboard Procesos</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Gestión de la Mejora -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('mejora') }">
                            <a href="#" class="nav-link" @click.prevent="toggleMenu('mejora')">
                                <i class="nav-icon fas fa-sync-alt"></i>
                                <p>
                                    Gestión de la Mejora
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('mejora')">
                                <li class="nav-item" v-if="hasRole('admin')">
                                    <router-link to="/mejora" class="nav-link">
                                        <i class="fas fa-folder-open nav-icon fa-xs"></i>
                                        <p>Bandeja de Hallazgos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="hasAnyRole(['admin', 'gestor'])">
                                    <router-link to="/mis-hallazgos" class="nav-link">
                                        <i class="fas fa-user-check nav-icon fa-xs"></i>
                                        <p>Mis Hallazgos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="hasAnyRole(['admin', 'especialista'])">
                                    <router-link to="/bandeja-eficacia" class="nav-link">
                                        <i class="nav-icon fas fa-tasks"></i>
                                        <p>Verificar Eficacia Mejora</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/dashboard/mejora" class="nav-link">
                                        <i class="fas fa-tachometer-alt fa-xs nav-icon"></i>
                                        <p>Dashboard de Mejora</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Gestión de Obligaciones -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('obligaciones') }">
                            <a href="#" class="nav-link" @click.prevent="toggleMenu('obligaciones')">
                                <i class="nav-icon fas fa-clipboard-check"></i>
                                <p>
                                    Gestión de Obligaciones
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('obligaciones')">
                                <li class="nav-item" v-if="hasAnyRole(['admin', 'supervisor'])">
                                    <router-link to="/obligaciones" class="nav-link">
                                        <i class="nav-icon fas fa-folder-open"></i>
                                        <p>Bandeja de Obligaciones</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="hasAnyRole(['facilitador', 'subgerente', 'especialista', 'admin'])">
                                    <router-link to="/mis-obligaciones" class="nav-link">
                                        <i class="nav-icon fas fa-user-check"></i>
                                        <p>Mis Obligaciones</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/obligaciones/seguimiento" class="nav-link">
                                        <i class="nav-icon fas fa-tasks"></i>
                                        <p>Seguimiento de Acciones</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/dashboard/obligaciones" class="nav-link">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>Dashboard Obligaciones</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Gestión de Riesgos -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('riesgos') }">
                            <a href="#" class="nav-link" @click.prevent="toggleMenu('riesgos')">
                                <i class="nav-icon fas fa-exclamation-triangle"></i>
                                <p>
                                    Gestión de Riesgos
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('riesgos')">
                                <li class="nav-item" v-if="hasAnyRole(['admin', 'supervisor'])">
                                    <router-link to="/riesgos/index" class="nav-link">
                                        <i class="nav-icon fas fa-folder-open"></i>
                                        <p>Bandeja de Riesgos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="hasAnyRole(['facilitador', 'subgerente', 'especialista', 'gestor', 'admin'])">
                                    <router-link to="/riesgos/mis-riesgos" class="nav-link">
                                        <i class="nav-icon fas fa-user-check"></i>
                                        <p>Mis Riesgos Asignados</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="hasAnyRole(['facilitador', 'subgerente', 'especialista', 'gestor', 'admin'])">
                                    <router-link to="/riesgos/verificacion" class="nav-link">
                                        <i class="nav-icon fas fa-tasks"></i>
                                        <p>Verificar Eficacia Riesgos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/dashboard/riesgos" class="nav-link">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>Dashboard Riesgos</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                        
                        <!-- Gestión de Auditorías -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('auditoria') }">
                            <a href="#" class="nav-link" @click.prevent="toggleMenu('auditoria')">
                                <i class="nav-icon fas fa-clipboard-check"></i>
                                <p>
                                    Gestión de Auditorías
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('auditoria')">
                                <li class="nav-item">
                                    <router-link to="/programa" class="nav-link">
                                        <i class="nav-icon fas fa-calendar-alt"></i>
                                        <p>Programa de Auditoría</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Gestión de Continuidad -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('continuidad') }">
                            <a href="#" class="nav-link" @click.prevent="toggleMenu('continuidad')">
                                <i class="nav-icon fas fa-shield-alt"></i>
                                <p>
                                    Gestión de Continuidad
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('continuidad')">
                                <li class="nav-item">
                                    <router-link to="/continuidad/planes" class="nav-link">
                                        <i class="nav-icon fas fa-clipboard-list"></i>
                                        <p>Planes de Continuidad</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/continuidad/escenarios" class="nav-link">
                                        <i class="nav-icon fas fa-exclamation-circle"></i>
                                        <p>Escenarios de Riesgo</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/continuidad/activos" class="nav-link">
                                        <i class="nav-icon fas fa-tasks"></i>
                                        <p>Activos Críticos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/dashboard/continuidad" class="nav-link">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>Dashboard Continuidad</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Satisfacción del Cliente -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('satisfaccion') }">
                            <a href="#" class="nav-link" @click.prevent="toggleMenu('satisfaccion')">
                                <i class="nav-icon fas fa-smile"></i>
                                <p>
                                    Satisfacción del Cliente
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('satisfaccion')">
                                <li class="nav-item">
                                    <router-link to="/salidas-nc" class="nav-link">
                                        <i class="fas fa-exclamation-triangle fa-xs nav-icon"></i>
                                        <p>Salidas No Conformes</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/sugerencias" class="nav-link">
                                        <i class="fas fa-lightbulb fa-xs nav-icon"></i>
                                        <p>Consolidado Sugerencias</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/encuestas-satisfaccion" class="nav-link">
                                        <i class="fas fa-poll fa-xs nav-icon"></i>
                                        <p>Encuestas de Satisfacción</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Administración -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('administracion') }">
                            <a href="#" class="nav-link" @click.prevent="toggleMenu('administracion')">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>
                                    Administración
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('administracion')">
                                <li class="nav-item">
                                    <router-link to="/administracion/usuarios" class="nav-link">
                                        <i class="nav-icon fas fa-users"></i>
                                        <p>Gestionar Usuarios</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/administracion/asignacion-ouos" class="nav-link">
                                        <i class="nav-icon fas fa-sitemap"></i>
                                        <p>Asignación OUO-Procesos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/administracion/configuracion" class="nav-link">
                                        <i class="nav-icon fas fa-file-alt"></i>
                                        <p>Configuración General</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/dashboard/administracion" class="nav-link">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>Dashboard Administración</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                    </template>
                </ul>
            </nav>
        </div>
    </aside>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from '../../stores/authStore';
import { useRouter } from 'vue-router';
import axios from 'axios';

const authStore = useAuthStore();
const router = useRouter();

const userRole = computed(() => {
    return authStore.roles.length > 0 ? authStore.roles[0] : '';
});

const hasRole = (role) => authStore.hasRole(role);
const hasAnyRole = (roles) => authStore.hasAnyRole(roles);

const openMenus = ref({});

const toggleMenu = (menuKey) => {
    openMenus.value[menuKey] = !openMenus.value[menuKey];
};

const isMenuOpen = (menuKey) => {
    return !!openMenus.value[menuKey];
};

const logout = async () => {
    try {
        await axios.post('/logout');
        window.location.href = '/login'; // Force reload to clear state and go to login
    } catch (error) {
        console.error('Logout failed', error);
    }
};
</script>

<style scoped>
/* Add any specific styles here if needed, otherwise AdminLTE styles apply */
</style>
