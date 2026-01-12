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
                        <a href="#" class="nav-link" :class="{ 'active': isModuleActive('/inventario-publico') || isModuleActive('/procesos/mapa') }" @click.prevent="toggleMenu('documentacion')">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>
                                Documentación por Procesos
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" v-show="isMenuOpen('documentacion')">
                            <li class="nav-item">
                                <router-link to="/inventario-publico/0" class="nav-link" active-class="active">
                                    <i class="nav-icon fas fa-book"></i>
                                    <p>Inventario de Procesos</p>
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link to="/procesos/mapa" class="nav-link" active-class="active">
                                    <i class="nav-icon fas fa-sitemap"></i>
                                    <p>Mapa de Procesos</p>
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link to="/documentos/listado" class="nav-link" active-class="active">
                                    <i class="nav-icon fas fa-clipboard-list"></i>
                                    <p>Listado de documentos</p>
                                </router-link>
                            </li>
                        </ul>
                    </li>

                    <template v-if="authStore.isAuthenticated">
                        <!-- Gestión de Requerimientos -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('requerimientos') }"
                            v-if="canAccessModule('requerimientos')">
                            <a href="#" class="nav-link" :class="{ 'active': isModuleActive('requerimientos') || isModuleActive('mis-requerimientos') }" @click.prevent="toggleMenu('requerimientos')">
                                <i class="nav-icon fas fa-tasks"></i>
                                <p>
                                    Gestión de Requerimientos
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('requerimientos')">
                                <!-- Bandeja de Requerimientos -->
                                <li class="nav-item"
                                    v-if="hasRole('admin')">
                                    <router-link to="/requerimientos/index" class="nav-link" active-class="active">
                                        <i class="fas fa-folder-open nav-icon fa-xs"></i>
                                        <p>Bandeja de Requerimientos</p>
                                    </router-link>
                                </li>
                                <!-- Crear Requerimiento -->
                                <li class="nav-item">
                                    <router-link to="/requerimientos/crear" class="nav-link" active-class="active">
                                        <i class="far fa-edit nav-icon"></i>
                                        <p>Crear Requerimiento</p>
                                    </router-link>
                                </li>
                                <!-- Mis Requerimientos -->
                                <li class="nav-item"
                                    v-if="hasAnyRole(['facilitador', 'subgerente', 'especialista', 'supervisor', 'admin'])">
                                    <router-link to="/mis-requerimientos" class="nav-link" active-class="active">
                                        <i class="fas fa-user-check nav-icon"></i>
                                        <p>Mis Requerimientos</p>
                                    </router-link>
                                </li>
                                <!-- Mis Req. Asignados (Especialista) -->
                                <li class="nav-item" v-if="hasAnyRole(['especialista', 'admin'])">
                                    <router-link to="/requerimientos/especialista" class="nav-link" active-class="active">
                                        <i class="fas fa-user-cog nav-icon"></i>
                                        <p>Mis Req. Asignados</p>
                                    </router-link>
                                </li>
                                <!-- Dashboard Requerimientos -->
                                <li class="nav-item" v-if="hasRole('admin')">
                                    <router-link :to="{ name: 'requerimientos.seguimiento', params: { rol: userRole } }"
                                        class="nav-link" active-class="active">
                                        <i class="fas fa-tachometer-alt fa-xs nav-icon"></i>
                                        <p>Dashboard Requerimientos</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Gestión por Procesos -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('procesos') }">
                            <a href="#" class="nav-link" :class="{ 'active': isModuleActive('/inventario-gestion') || isModuleActive('/procesos/index') || isModuleActive('/documentos') || isModuleActive('/indicadores-gestion') }" @click.prevent="toggleMenu('procesos')">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>
                                    Gestión por Procesos
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('procesos')">
                                <li class="nav-item" v-if="hasRole('admin')">
                                    <router-link to="/inventario-gestion" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-cubes"></i>
                                        <p>Gestión del Inventario</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="hasRole('admin')">
                                    <router-link to="/procesos/index" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-clipboard-list"></i>
                                        <p>Listado de Procesos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="hasRole('admin')">
                                    <router-link to="/documentos" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-clipboard-list"></i>
                                        <p>Listado de Documentos</p>
                                    </router-link>
                                </li>
                                <!-- Listado de Documentos Externos (LMDE) -->
                                <li class="nav-item" v-if="hasAnyRole(['facilitador', 'admin'])">
                                    <router-link to="/lmde" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-book-open"></i>
                                        <p>Listado de Doc Externos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/indicadores-gestion" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-chart-bar"></i>
                                        <p>Listado de Indicadores</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/partes" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-users"></i>
                                        <p>Partes Interesadas</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="hasAnyRole(['admin', 'especialista'])">
                                    <router-link to="/dashboard/procesos" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>Dashboard Procesos</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Gestión de la Mejora -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('mejora') }">
                            <a href="#" class="nav-link" :class="{ 'active': isModuleActive('/mejora') || isModuleActive('/mis-hallazgos') || isModuleActive('/bandeja-eficacia') }" @click.prevent="toggleMenu('mejora')">
                                <i class="nav-icon fas fa-sync-alt"></i>
                                <p>
                                    Gestión de la Mejora
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('mejora')">
                                <li class="nav-item">
                                    <router-link to="/mejora" class="nav-link" active-class="active">
                                        <i class="fas fa-folder-open nav-icon fa-xs"></i>
                                        <p>Bandeja de SMP</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="hasAnyRole(['admin', 'especialista', 'facilitador', 'propietario'])">
                                    <router-link to="/mis-hallazgos" class="nav-link" active-class="active">
                                        <i class="fas fa-user-check nav-icon fa-xs"></i>
                                        <p>Mis Solicitudes de Mejora</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="hasAnyRole(['admin', 'especialista'])">
                                    <router-link to="/bandeja-eficacia" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-tasks"></i>
                                        <p>Verificar Eficacia Mejora</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/dashboard/mejora" class="nav-link" active-class="active">
                                        <i class="fas fa-tachometer-alt fa-xs nav-icon"></i>
                                        <p>Dashboard de Mejora</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Gestión de Obligaciones -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('obligaciones') }">
                            <a href="#" class="nav-link" :class="{ 'active': isModuleActive('/obligaciones') || isModuleActive('/mis-obligaciones') || isModuleActive('/radar-obligaciones') }" @click.prevent="toggleMenu('obligaciones')">
                                <i class="nav-icon fas fa-clipboard-check"></i>
                                <p>
                                    Gestión de Obligaciones
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('obligaciones')">
                                <li class="nav-item">
                                    <router-link to="/obligaciones" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-folder-open"></i>
                                        <p>Bandeja de Obligaciones</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/mis-obligaciones" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-user-check"></i>
                                        <p>Mis Obligaciones</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/obligaciones/seguimiento" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-tasks"></i>
                                        <p>Seguimiento de Acciones</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/dashboard/obligaciones" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>Dashboard Obligaciones</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Gestión de Riesgos -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('riesgos') }">
                            <a href="#" class="nav-link" :class="{ 'active': isModuleActive('/riesgos') }" @click.prevent="toggleMenu('riesgos')">
                                <i class="nav-icon fas fa-exclamation-triangle"></i>
                                <p>
                                    Gestión de Riesgos
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('riesgos')">
                                <li class="nav-item">
                                    <router-link to="/riesgos/index" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-folder-open"></i>
                                        <p>Bandeja de Riesgos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/riesgos/mis-riesgos" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-user-check"></i>
                                        <p>Mis Riesgos Asignados</p>
                                    </router-link>
                                </li>
                                <li class="nav-item"
                                    v-if="hasAnyRole(['facilitador', 'subgerente', 'especialista', 'gestor', 'admin'])">
                                    <router-link to="/riesgos/verificacion" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-tasks"></i>
                                        <p>Verificar Eficacia Riesgos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/dashboard/riesgos" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>Dashboard Riesgos</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Gestión de Auditorías -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('auditoria') }" v-if="!hasRole('facilitador')">
                            <a href="#" class="nav-link" :class="{ 'active': isModuleActive('/programa') }" @click.prevent="toggleMenu('auditoria')">
                                <i class="nav-icon fas fa-clipboard-check"></i>
                                <p>
                                    Gestión de Auditorías
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('auditoria')">
                                <li class="nav-item">
                                    <router-link to="/programa" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-calendar-alt"></i>
                                        <p>Programa de Auditoría</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Gestión de Continuidad -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('continuidad') }" v-if="!hasRole('facilitador')">
                            <a href="#" class="nav-link" :class="{ 'active': isModuleActive('/continuidad') }" @click.prevent="toggleMenu('continuidad')">
                                <i class="nav-icon fas fa-shield-alt"></i>
                                <p>
                                    Gestión de Continuidad
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('continuidad')">
                                <li class="nav-item">
                                    <router-link to="/continuidad/planes" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-clipboard-list"></i>
                                        <p>Planes de Continuidad</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/continuidad/escenarios" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-exclamation-circle"></i>
                                        <p>Escenarios de Riesgo</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/continuidad/activos" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-layer-group"></i>
                                        <p>Activos Críticos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/continuidad/pruebas" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-vial"></i>
                                        <p>Pruebas y Ejercicios</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/dashboard/continuidad" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>Dashboard Continuidad</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Satisfacción del Cliente -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('satisfaccion') }">
                            <a href="#" class="nav-link" :class="{ 'active': isModuleActive('/salidas-nc') || isModuleActive('/sugerencias') || isModuleActive('/encuestas-satisfaccion') }" @click.prevent="toggleMenu('satisfaccion')">
                                <i class="nav-icon fas fa-smile"></i>
                                <p>
                                    Satisfacción del Cliente
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('satisfaccion')">
                                <li class="nav-item">
                                    <router-link to="/encuestas-satisfaccion" class="nav-link" active-class="active">
                                        <i class="fas fa-poll fa-xs nav-icon"></i>
                                        <p>Encuestas de Satisfacción</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/sugerencias" class="nav-link" active-class="active">
                                        <i class="fas fa-lightbulb fa-xs nav-icon"></i>
                                        <p>Consolidado Sugerencias</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/salidas-nc" class="nav-link" active-class="active">
                                        <i class="fas fa-exclamation-triangle fa-xs nav-icon"></i>
                                        <p>Salidas No Conformes</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/reportes-satisfaccion" class="nav-link" active-class="active">
                                        <i class="fas fa-file-alt fa-xs nav-icon"></i>
                                        <p>Reporte Trimestral</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Alta Dirección -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('direccion') }"
                            v-if="hasAnyRole(['admin', 'especialista', 'propietario', 'subgerente'])">
                            <a href="#" class="nav-link" :class="{ 'active': isModuleActive('/revision-direccion') }" @click.prevent="toggleMenu('direccion')">
                                <i class="nav-icon fas fa-user-tie"></i>
                                <p>
                                    Alta Dirección
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('direccion')">
                                <li class="nav-item">
                                    <router-link to="/revision-direccion" class="nav-link" active-class="active">
                                        <i class="fas fa-calendar-check nav-icon"></i>
                                        <p>Revisión por la Dirección</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Administración -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('administracion') }" v-if="!hasRole('facilitador')">
                            <a href="#" class="nav-link" :class="{ 'active': isModuleActive('/administracion') }" @click.prevent="toggleMenu('administracion')">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>
                                    Administración
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('administracion')">
                                <li class="nav-item">
                                    <router-link to="/administracion/usuarios" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-users"></i>
                                        <p>Gestionar Usuarios</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/administracion/asignacion-ouos" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-sitemap"></i>
                                        <p>Asignación OUO-Procesos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/administracion/configuracion" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-file-alt"></i>
                                        <p>Configuración General</p>
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <router-link to="/dashboard/administracion" class="nav-link" active-class="active">
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
import { ref, computed, onMounted, watch } from 'vue';
import { useAuthStore } from '../../stores/authStore';
import { useRouter } from 'vue-router';
import axios from 'axios';

const authStore = useAuthStore();
const router = useRouter();

const userRole = computed(() => {
    return authStore.primaryRole || (authStore.roles.length > 0 ? authStore.roles[0] : '');
});

const hasRole = (role) => authStore.hasRole(role);
const hasAnyRole = (roles) => authStore.hasAnyRole(roles);
const canAccessModule = (module) => authStore.canAccessModule(module);

const openMenus = ref({});

const toggleMenu = (menuKey) => {
    openMenus.value[menuKey] = !openMenus.value[menuKey];
};

const isMenuOpen = (menuKey) => {
    return !!openMenus.value[menuKey];
};

const isModuleActive = (pathPrefix) => {
    return router.currentRoute.value.path.includes(pathPrefix);
};

const logout = async () => {
    try {
        await axios.post('/logout');
        window.location.href = '/login'; // Force reload to clear state and go to login
    } catch (error) {
        console.error('Logout failed', error);
    }
};

const updateOpenMenus = (path) => {
    if (path.includes('requerimientos')) openMenus.value['requerimientos'] = true;
    if (path.includes('procesos') || path.includes('inventario-gestion') || path.includes('documentos') || path.includes('indicadores-gestion') || path.includes('lmde') || path.includes('partes')) openMenus.value['procesos'] = true;
    if (path.includes('mejora') || path.includes('mis-hallazgos') || path.includes('bandeja-eficacia')) openMenus.value['mejora'] = true;
    if (path.includes('obligaciones')) openMenus.value['obligaciones'] = true;
    if (path.includes('riesgos')) openMenus.value['riesgos'] = true;
    if (path.includes('programa')) openMenus.value['auditoria'] = true;
    if (path.includes('continuidad')) openMenus.value['continuidad'] = true;
    if (path.includes('salidas-nc') || path.includes('sugerencias') || path.includes('encuestas-satisfaccion')) openMenus.value['satisfaccion'] = true;
    if (path.includes('revision-direccion')) openMenus.value['direccion'] = true;
    if (path.includes('administracion')) openMenus.value['administracion'] = true;
};

onMounted(() => {
    updateOpenMenus(router.currentRoute.value.path);
});

watch(() => router.currentRoute.value.path, (newPath) => {
    updateOpenMenus(newPath);
}, { immediate: true });
</script>

<style scoped>
/* Transiciones suaves para todos los elementos del menú */
.nav-pills .nav-link {
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); /* Movimiento fluido */
    border-radius: 6px;
    margin-bottom: 4px;
    border: 1px solid transparent; /* Evita saltos al agregar bordes */
}

/* --- Menú Padre Activo --- */
.nav-pills .nav-link.active,
.nav-pills .show > .nav-link {
    /* Gradiente moderno en rojo "Premium" */
    background: linear-gradient(135deg, #d32f2f 0%, #b71c1c 100%) !important;
    color: #fff !important;
    
    /* Efecto 3D sutil para elevación */
    box-shadow: 0 4px 15px rgba(211, 47, 47, 0.35);
    
    /* Pequeño desplazamiento para feedback táctil visual */
    transform: translateX(2px);
}

/* Hover en Menú Padre (No activo) */
.nav-pills .nav-link:not(.active):hover {
    background-color: rgba(255, 255, 255, 0.08);
    transform: translateX(2px);
}

/* --- Submenú (Hijo) --- */
.nav-treeview {
    background-color: rgba(0, 0, 0, 0.15); /* Fondo ligeramente más oscuro para el contenedor hijo */
    border-radius: 8px;
    margin-top: 5px;
    padding: 5px 0;
}

.nav-treeview > .nav-item > .nav-link {
    padding-left: 2.8rem; /* Mayor indentación para jerarquía */
    font-size: 0.95em;
    opacity: 0.85; /* Texto un poco apagado por defecto */
}

/* --- Submenú Activo --- */
/* --- Submenú Activo --- */
.nav-treeview > .nav-item > .nav-link.active,
.nav-treeview > .nav-item > .nav-link.active:hover {
    /* Estilo "Mouseover" / Sutil solicitado */
    background-color: rgba(255, 255, 255, 0.15) !important; /* Un poco más visible que el hover normal */
    
    /* Texto Blanco para mantener consistencia con el tema oscuro */
    color: #fff !important;
    
    font-weight: 600;
    opacity: 1;
    
    /* Borde de acento a la izquierda (Opcional, pero ayuda a identificar activo) */
    /* Mantenemos el rojo pero sutil o lo quitamos si se prefiere totalmente plano. 
       Lo mantendré rojo para identificarlo, pero el fondo es lo critico. */
    border-left: 3px solid #d32f2f;
    
    box-shadow: none; 
}

/* Iconos en estado activo */
.nav-pills .nav-link.active .nav-icon {
    animation: pulse 1s infinite; /* Animación sutil opcional si te gusta */
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
</style>