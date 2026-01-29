<template>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="#" class="brand-link border-bottom-0">
            <img :src="'/vendor/adminlte/dist/img/kallpaq_ico.png'" alt="Kallpaq Logo"
                class="brand-image img-circle elevation-3" style="opacity: .9">
            <span class="brand-text font-weight-bold text-white"> KALLPAQ v1.0</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel mt-2 pb-3 mb-3 d-flex">
                <div class="info w-100" v-if="authStore.isAuthenticated">
                    <div class="d-flex align-items-center justify-content-between mb-1" style="flex-wrap: nowrap;">
                        <a href="#" class="d-block text-white mb-0 text-truncate mr-2"
                            style="max-width: calc(100% - 40px);">
                            <i class="fas fa-user-circle nav-icon mr-1"></i>
                            {{ formattedUserName }}
                        </a>
                        <a href="/logout" class="logout-power-btn flex-shrink-0" @click.prevent="logout"
                            title="Cerrar Sesión">
                            <i class="fas fa-power-off"></i>
                        </a>
                    </div>
                    <div class="d-flex align-items-center mt-2">
                        <div class="d-flex align-items-center ml-auto">
                            <i class="fas fa-shield-alt text-white opacity-75 mr-2" style="font-size: 0.8rem;"></i>
                            <div class="role-selector-wrapper">
                                <Dropdown v-if="authStore.roles.length > 1" v-model="activeRole"
                                    :options="authStore.roles" @change="changeRole" class="role-dropdown-modern" :pt="{
                                        root: { class: 'bg-transparent border-0 p-0 shadow-none d-flex align-items-center' },
                                        label: { class: 'text-white font-weight-bold p-0 small-text text-right' },
                                        trigger: { class: 'text-white-50 ml-1' },
                                        panel: { class: 'bg-white shadow-lg border-0 rounded' },
                                        item: { class: 'text-dark small py-2' }
                                    }">
                                    <template #value="slotProps">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <span v-if="slotProps.value" class="text-white">{{
                                                formatRoleName(slotProps.value) }}</span>
                                        </div>
                                    </template>
                                    <template #option="slotProps">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-tag x-small-text mr-2 text-danger opacity-75"></i>
                                            <span>{{ formatRoleName(slotProps.option) }}</span>
                                        </div>
                                    </template>
                                </Dropdown>
                                <span v-else class="text-white-50 small font-weight-bold">
                                    {{ formatRoleName(authStore.currentRole) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Login Link if not authenticated -->
                <div class="info" v-else>
                    <router-link to="/login" class="d-block text-white p-2">
                        <i class="far fa-user nav-icon mr-1"></i>
                        Iniciar Sesión
                    </router-link>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" role="menu">

                    <!-- Documentación por Procesos -->
                    <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('documentacion') }"
                        v-if="canViewModule('documentacion')">
                        <a href="#" class="nav-link"
                            :class="{ 'active': isModuleActive('/inventario-publico') || isModuleActive('/procesos/mapa') }"
                            @click.prevent="toggleMenu('documentacion')">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>
                                Documentación por Procesos
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" v-show="isMenuOpen('documentacion')">
                            <li class="nav-item">
                                <router-link to="/inventario-publico/0" class="nav-link" active-class="active"
                                    v-if="authStore.can('menu.documentacion.inventario')">
                                    <i class="nav-icon fas fa-book"></i>
                                    <p>Inventario de Procesos</p>
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link to="/procesos/mapa" class="nav-link" active-class="active"
                                    v-if="authStore.can('menu.documentacion.mapa')">
                                    <i class="nav-icon fas fa-sitemap"></i>
                                    <p>Mapa de Procesos</p>
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link to="/documentos/listado" class="nav-link" active-class="active"
                                    v-if="authStore.can('menu.documentacion.documentos')">
                                    <i class="nav-icon fas fa-clipboard-list"></i>
                                    <p>Listado de documentos</p>
                                </router-link>
                            </li>
                        </ul>
                    </li>

                    <template v-if="authStore.isAuthenticated">
                        <!-- Gestión de Requerimientos -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('requerimientos') }"
                            v-if="canViewModule('requerimientos')">
                            <a href="#" class="nav-link"
                                :class="{ 'active': isModuleActive('requerimientos') || isModuleActive('mis-requerimientos') }"
                                @click.prevent="toggleMenu('requerimientos')">
                                <i class="nav-icon fas fa-tasks"></i>
                                <p>
                                    Gestión de Requerimientos
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('requerimientos')">
                                <!-- Bandeja de Requerimientos -->
                                <li class="nav-item" v-if="authStore.can('menu.requerimientos.bandeja')">
                                    <router-link to="/requerimientos/index" class="nav-link" active-class="active">
                                        <i class="fas fa-folder-open nav-icon fa-xs"></i>
                                        <p>Bandeja de Requerimientos</p>
                                    </router-link>
                                </li>
                                <!-- Mis Requerimientos -->
                                <li class="nav-item" v-if="authStore.can('menu.requerimientos.mis_requerimientos')">
                                    <router-link to="/mis-requerimientos" class="nav-link" active-class="active">
                                        <i class="fas fa-user-check nav-icon"></i>
                                        <p>Mis Requerimientos</p>
                                    </router-link>
                                </li>
                                <!-- Crear Requerimiento -->
                                <li class="nav-item" v-if="authStore.can('menu.requerimientos.crear')">
                                    <router-link to="/requerimientos/crear" class="nav-link" active-class="active">
                                        <i class="far fa-edit nav-icon"></i>
                                        <p>Crear Requerimiento</p>
                                    </router-link>
                                </li>
                                <!-- Mis Req. Asignados (Especialista) -->
                                <li class="nav-item" v-if="authStore.can('menu.requerimientos.mis_asignados')">
                                    <router-link to="/requerimientos/especialista" class="nav-link"
                                        active-class="active">
                                        <i class="fas fa-user-cog nav-icon"></i>
                                        <p>Mis Req. Asignados</p>
                                    </router-link>
                                </li>
                                <!-- Dashboard Requerimientos -->
                                <li class="nav-item" v-if="authStore.can('menu.requerimientos.dashboard')">
                                    <router-link :to="{ name: 'requerimientos.seguimiento', params: { rol: userRole } }"
                                        class="nav-link" active-class="active">
                                        <i class="fas fa-tachometer-alt fa-xs nav-icon"></i>
                                        <p>Dashboard Requerimientos</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Gestión por Procesos -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('procesos') }"
                            v-if="canViewModule('procesos')">
                            <a href="#" class="nav-link"
                                :class="{ 'active': isModuleActive('/inventario-gestion') || isModuleActive('/procesos/index') || isModuleActive('/documentos') || isModuleActive('/indicadores-gestion') }"
                                @click.prevent="toggleMenu('procesos')">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>
                                    Gestión por Procesos
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('procesos')">
                                <li class="nav-item" v-if="authStore.can('menu.procesos.inventario')">
                                    <router-link to="/inventario-gestion" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-cubes"></i>
                                        <p>Gestión del Inventario</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.procesos.listado')">
                                    <router-link to="/procesos/index" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-clipboard-list"></i>
                                        <p>Listado de Procesos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.procesos.documentos')">
                                    <router-link to="/documentos" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-clipboard-list"></i>
                                        <p>Listado de Documentos</p>
                                    </router-link>
                                </li>
                                <!-- Listado de Documentos Externos (LMDE) -->
                                <li class="nav-item" v-if="authStore.can('menu.procesos.lmde')">
                                    <router-link to="/lmde" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-book-open"></i>
                                        <p>Listado de Doc Externos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.procesos.indicadores')">
                                    <router-link to="/indicadores-gestion" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-chart-bar"></i>
                                        <p>Listado de Indicadores</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.procesos.partes')">
                                    <router-link to="/partes" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-users"></i>
                                        <p>Partes Interesadas</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.procesos.dashboard')">
                                    <router-link to="/dashboard/procesos" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>Dashboard Procesos</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Gestión de la Mejora -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('mejora') }"
                            v-if="canViewModule('mejora')">
                            <a href="#" class="nav-link"
                                :class="{ 'active': isModuleActive('/mejora') || isModuleActive('/mis-hallazgos') || isModuleActive('/bandeja-eficacia') }"
                                @click.prevent="toggleMenu('mejora')">
                                <i class="nav-icon fas fa-sync-alt"></i>
                                <p>
                                    Gestión de la Mejora
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('mejora')">
                                <li class="nav-item" v-if="authStore.can('menu.mejora.bandeja')">
                                    <router-link to="/mejora" class="nav-link" active-class="active">
                                        <i class="fas fa-folder-open nav-icon fa-xs"></i>
                                        <p>Bandeja de SMP</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.mejora.mis_solicitudes')">
                                    <router-link to="/mis-hallazgos" class="nav-link" active-class="active">
                                        <i class="fas fa-user-check nav-icon fa-xs"></i>
                                        <p>Mis Solicitudes de Mejora</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.mejora.mis_asignados')">
                                    <router-link to="/bandeja-eficacia" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-tasks"></i>
                                        <p>Mis SMP Asignadas</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.mejora.dashboard')">
                                    <router-link to="/dashboard/mejora" class="nav-link" active-class="active">
                                        <i class="fas fa-tachometer-alt fa-xs nav-icon"></i>
                                        <p>Dashboard de Mejora</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Gestión de Obligaciones -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('obligaciones') }"
                            v-if="canViewModule('obligaciones')">
                            <a href="#" class="nav-link"
                                :class="{ 'active': isModuleActive('/obligaciones') || isModuleActive('/mis-obligaciones') || isModuleActive('/radar-obligaciones') }"
                                @click.prevent="toggleMenu('obligaciones')">
                                <i class="nav-icon fas fa-clipboard-check"></i>
                                <p>
                                    Gestión de Obligaciones
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('obligaciones')">
                                <li class="nav-item" v-if="authStore.can('menu.obligaciones.bandeja')">
                                    <router-link to="/obligaciones" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-folder-open"></i>
                                        <p>Bandeja de Obligaciones</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.obligaciones.mis_obligaciones')">
                                    <router-link to="/mis-obligaciones" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-building"></i>
                                        <p>Mis Obligaciones (UO)</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.obligaciones.mis_asignados')">
                                    <router-link to="/obligaciones/asignadas" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-user-check"></i>
                                        <p>Mis Obligaciones Asignadas</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.obligaciones.dashboard')">
                                    <router-link to="/dashboard/obligaciones" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>Dashboard Obligaciones</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Gestión de Riesgos -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('riesgos') }"
                            v-if="canViewModule('riesgos')">
                            <a href="#" class="nav-link" :class="{ 'active': isModuleActive('/riesgos') }"
                                @click.prevent="toggleMenu('riesgos')">
                                <i class="nav-icon fas fa-exclamation-triangle"></i>
                                <p>
                                    Gestión de Riesgos
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('riesgos')">
                                <li class="nav-item" v-if="authStore.can('menu.riesgos.bandeja')">
                                    <router-link to="/riesgos/index" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-folder-open"></i>
                                        <p>Bandeja de Riesgos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.riesgos.mis_riesgos')">
                                    <router-link to="/riesgos/mis-riesgos" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-building"></i>
                                        <p>Mis Riesgos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.riesgos.mis_asignados')">
                                    <router-link to="/riesgos/asignados" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-user-check"></i>
                                        <p>Mis Riesgos Asignados</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.riesgos.dashboard')">
                                    <router-link to="/dashboard/riesgos" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>Dashboard Riesgos</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Gestión de Continuidad -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('continuidad') }"
                            v-if="canViewModule('continuidad')">
                            <a href="#" class="nav-link" :class="{ 'active': isModuleActive('/continuidad') }"
                                @click.prevent="toggleMenu('continuidad')">
                                <i class="nav-icon fas fa-shield-alt"></i>
                                <p>
                                    Gestión de Continuidad
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('continuidad')">
                                <li class="nav-item" v-if="authStore.can('menu.continuidad.planes')">
                                    <router-link to="/continuidad/planes" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-clipboard-list"></i>
                                        <p>Planes de Continuidad</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.continuidad.escenarios')">
                                    <router-link to="/continuidad/escenarios" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-exclamation-circle"></i>
                                        <p>Escenarios de Riesgo</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.continuidad.activos')">
                                    <router-link to="/continuidad/activos" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-layer-group"></i>
                                        <p>Activos Críticos</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.continuidad.pruebas')">
                                    <router-link to="/continuidad/pruebas" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-vial"></i>
                                        <p>Pruebas y Ejercicios</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.continuidad.dashboard')">
                                    <router-link to="/dashboard/continuidad" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>Dashboard Continuidad</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Satisfacción del Cliente -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('satisfaccion') }"
                            v-if="canViewModule('satisfaccion')">
                            <a href="#" class="nav-link" :class="{ 'active': isModuleActive('/satisfaccion') }"
                                @click.prevent="toggleMenu('satisfaccion')">
                                <i class="nav-icon fas fa-smile"></i>
                                <p>
                                    Satisfacción del Cliente
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('satisfaccion')">
                                <li class="nav-item" v-if="authStore.can('menu.satisfaccion.encuestas')">
                                    <router-link to="/encuestas-satisfaccion" class="nav-link" active-class="active">
                                        <i class="fas fa-poll fa-xs nav-icon"></i>
                                        <p>Encuestas de Satisfacción</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.satisfaccion.sugerencias')">
                                    <router-link to="/sugerencias" class="nav-link" active-class="active">
                                        <i class="fas fa-lightbulb fa-xs nav-icon"></i>
                                        <p>Consolidado Sugerencias</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.satisfaccion.salidas_nc')">
                                    <router-link to="/salidas-nc" class="nav-link" active-class="active">
                                        <i class="fas fa-exclamation-triangle fa-xs nav-icon"></i>
                                        <p>Salidas No Conformes</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.satisfaccion.reporte')">
                                    <router-link to="/reportes-satisfaccion" class="nav-link" active-class="active">
                                        <i class="fas fa-file-alt fa-xs nav-icon"></i>
                                        <p>Reporte Trimestral</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Gestión de Auditorías -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('auditoria') }"
                            v-if="canViewModule('auditoria')">
                            <a href="#" class="nav-link" :class="{ 'active': isModuleActive('/auditoria') }"
                                @click.prevent="toggleMenu('auditoria')">
                                <i class="nav-icon fas fa-clipboard-check"></i>
                                <p>
                                    Gestión de Auditorías
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('auditoria')">
                                <li class="nav-item" v-if="authStore.can('menu.auditoria.programa')">
                                    <router-link to="/programa" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-calendar-alt"></i>
                                        <p>Programa Anual</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.auditoria.auditores')">
                                    <router-link to="/auditor/listado" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-user-tie"></i>
                                        <p>Listado de Auditores</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.auditoria.normas')">
                                    <router-link to="/auditoria/normas" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-book"></i>
                                        <p>Normas Auditables</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Alta Dirección -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('direccion') }"
                            v-if="canViewModule('direccion')">
                            <a href="#" class="nav-link" :class="{ 'active': isModuleActive('/revision-direccion') }"
                                @click.prevent="toggleMenu('direccion')">
                                <i class="nav-icon fas fa-user-tie"></i>
                                <p>
                                    Alta Dirección
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('direccion')">
                                <li class="nav-item" v-if="authStore.can('menu.direccion.revision')">
                                    <router-link to="/revision-direccion" class="nav-link" active-class="active">
                                        <i class="fas fa-calendar-check nav-icon"></i>
                                        <p>Revisión por la Dirección</p>
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- Administración -->
                        <li class="nav-item has-treeview" :class="{ 'menu-open': isMenuOpen('administracion') }"
                            v-if="canViewModule('administracion')">
                            <a href="#" class="nav-link" :class="{ 'active': isModuleActive('/administracion') }"
                                @click.prevent="toggleMenu('administracion')">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>
                                    Administración
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" v-show="isMenuOpen('administracion')">
                                <li class="nav-item" v-if="authStore.can('menu.administracion.usuarios')">
                                    <router-link to="/administracion/usuarios" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-users"></i>
                                        <p>Gestionar Usuarios</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.administracion.roles')">
                                    <router-link to="/administracion/roles" class="nav-link" active-class="active">
                                        <i class="nav-icon fas fa-user-tag"></i>
                                        <p>Gestionar Roles</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.administracion.asignacion')">
                                    <router-link to="/administracion/asignacion-ouos" class="nav-link"
                                        active-class="active">
                                        <i class="nav-icon fas fa-sitemap"></i>
                                        <p>Gestionar OUO</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.administracion.configuracion')">
                                    <router-link to="/administracion/configuracion" class="nav-link"
                                        active-class="active">
                                        <i class="nav-icon fas fa-file-alt"></i>
                                        <p>Configuración General</p>
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="authStore.can('menu.administracion.dashboard')">
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
import { useUIStore } from '../../stores/uiStore';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Dropdown from 'primevue/dropdown';

const authStore = useAuthStore();
const uiStore = useUIStore();
const router = useRouter();

const activeRole = computed({
    get: () => authStore.currentRole,
    set: (val) => authStore.setActiveRole(val)
});

const userRole = computed(() => {
    return authStore.currentRole;
});

const formattedUserName = computed(() => {
    if (!authStore.user?.name) return '';
    const parts = authStore.user.name.trim().split(/\s+/);
    return parts.length > 2 ? parts.slice(0, 2).join(' ') : authStore.user.name;
});

const changeRole = () => {
    // El v-model ya actualiza el store mediante el setter del computed
    // Solo manejamos la navegación aquí
    router.push({ name: 'home' });
};

const hasRole = (role) => authStore.hasRole(role);
const hasAnyRole = (roles) => authStore.hasAnyRole(roles);
const canAccessModule = (module) => authStore.canAccessModule(module);

const formatRoleName = (role) => {
    if (!role) return '';
    // Capitalizar y limpiar guiones
    return role.split(/[_-]/)
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ');
};

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

const canViewModule = (module) => {
    // Si tiene el permiso padre explícito
    if (authStore.can(`menu.${module}`)) return true;

    // O si tiene AL MENOS UN permiso hijo visible en el sidebar
    const childPermissions = {
        documentacion: ['menu.documentacion.inventario', 'menu.documentacion.mapa', 'menu.documentacion.documentos'],
        requerimientos: ['menu.requerimientos.bandeja', 'menu.requerimientos.crear', 'menu.requerimientos.mis_requerimientos', 'menu.requerimientos.mis_asignados', 'menu.requerimientos.dashboard'],
        procesos: ['menu.procesos.inventario', 'menu.procesos.listado', 'menu.procesos.documentos', 'menu.procesos.indicadores', 'menu.procesos.caracterizacion'],
        mejora: ['menu.mejora.bandeja', 'menu.mejora.mis_solicitudes', 'menu.mejora.mis_asignados'],
        obligaciones: ['menu.obligaciones.bandeja', 'menu.obligaciones.mis_obligaciones', 'menu.obligaciones.mis_asignados', 'menu.obligaciones.radar'],
        riesgos: ['menu.riesgos.bandeja', 'menu.riesgos.mis_riesgos', 'menu.riesgos.mis_asignados', 'menu.riesgos.dashboard'],
        continuidad: ['menu.continuidad.planes', 'menu.continuidad.escenarios', 'menu.continuidad.activos', 'menu.continuidad.pruebas'],
        satisfaccion: ['menu.satisfaccion.encuestas', 'menu.satisfaccion.sugerencias', 'menu.satisfaccion.salidas_nc'],
        auditoria: ['menu.auditoria.programa', 'menu.auditoria.gantt', 'menu.auditoria.plan', 'menu.auditoria.auditores', 'menu.auditoria.normas'],
        direccion: ['menu.direccion.revisiones'],
        administracion: ['menu.administracion.usuarios', 'menu.administracion.roles', 'menu.administracion.asignacion', 'menu.administracion.configuracion', 'menu.administracion.dashboard']
    };

    const specificChildren = childPermissions[module] || [];
    return specificChildren.some(permission => authStore.can(permission));
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
    if (path.includes('riesgos')) openMenus.value['riesgos'] = true;
    if (path.includes('programa') || path.includes('gantt') || path.includes('especifica')) openMenus.value['auditoria'] = true;
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
/* Estilos para unificar el header y el brand */
.main-header {
    border-bottom: 1px solid #eee !important;
    background-color: #ffffff !important;
}

.brand-link {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    background: transparent !important;
}

/* --- Estilos Generales de Enlaces --- */
.nav-pills .nav-link {
    transition: all 0.2s ease;
    border-radius: 6px;
    margin-bottom: 2px;
    color: rgba(255, 255, 255, 0.8);
}

/* --- Menú Padre ACTIVO (Solo si está seleccionado) --- */
.nav-sidebar>.nav-item>.nav-link.active {
    background: linear-gradient(135deg, #d32f2f 0%, #b71c1c 100%) !important;
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(211, 47, 47, 0.2);
    border-radius: 6px;
}

/* --- Menú Padre ABIERTO pero no seleccionado --- */
.nav-item.menu-open>.nav-link:not(.active) {
    background: transparent !important;
    color: #fff !important;
    box-shadow: none;
}

/* --- Menú Padre NO Activo (Hover ligero sin cambio de fondo) --- */
.nav-pills .nav-link:not(.active):hover {
    color: #fff;
    background-color: transparent;
}

/* --- Contenedor de Submenú --- */
.nav-treeview {
    background-color: transparent;
    padding: 2px 0;
}

/* --- Enlace de Submenú (Hijo) --- */
.nav-treeview>.nav-item>.nav-link {
    padding-left: 2.5rem;
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.7);
}

/* --- Submenú ACTIVO (Seleccionado) --- */
.nav-treeview>.nav-item>.nav-link.active {
    background-color: rgba(255, 255, 255, 0.1) !important;
    color: #fff !important;
    font-weight: 600;
    box-shadow: none;
    border-left: none;
    /* Quitamos el borde para mayor limpieza según la nueva idea */
}

/* --- Submenú NO Activo (Hover ligero) --- */
.nav-treeview>.nav-item>.nav-link:not(.active):hover {
    color: #fff;
    background-color: transparent;
}

/* Animaciones sutiles */
.nav-link i {
    transition: transform 0.2s ease;
}

.nav-link.active i {
    transform: scale(1.1);
}

/* Iconos en estado activo */
.nav-pills .nav-link.active .nav-icon {
    animation: pulse 1s infinite;
    /* Animación sutil opcional si te gusta */
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.05);
    }

    100% {
        transform: scale(1);
    }
}

.user-panel {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.user-panel .info {
    padding: 5px 15px;
}

.x-small-text {
    font-size: 0.85rem;
}

/* Estilos para el Dropdown Moderno */
:deep(.role-dropdown-modern) {
    height: auto !important;
    line-height: 1.2 !important;
}

:deep(.role-dropdown-modern .p-dropdown-label) {
    transition: all 0.2s ease;
    cursor: pointer;
    border-bottom: 1px dashed rgba(255, 255, 255, 0.4);
    padding-bottom: 1px;
    margin-right: 5px;
}

:deep(.role-dropdown-modern .p-dropdown-trigger) {
    width: auto !important;
    color: rgba(255, 255, 255, 0.6) !important;
    font-size: 0.7rem !important;
}

:deep(.role-dropdown-modern:hover .p-dropdown-label) {
    border-bottom-color: #ff4d4d;
    color: #fff !important;
}

/* Panel de Opciones con mayor contraste */
:deep(.p-dropdown-panel) {
    background: #ffffff !important;
    border: 1px solid #ddd !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
    margin-top: 5px !important;
}

:deep(.p-dropdown-item) {
    margin: 2px 5px !important;
    padding: 8px 12px !important;
    border-radius: 4px !important;
    color: #333 !important;
    font-weight: 500 !important;
}

:deep(.p-dropdown-item:hover) {
    background: #fdf2f2 !important;
    color: #d32f2f !important;
}

:deep(.p-dropdown-item.p-highlight) {
    background: #d32f2f !important;
    color: #ffffff !important;
}

/* Botón de Salida (Power Off) */
.logout-power-btn {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    color: rgba(255, 255, 255, 0.6);
    transition: all 0.3s ease;
}

.logout-power-btn:hover {
    background: #d32f2f;
    color: white !important;
    border-color: #d32f2f;
    box-shadow: 0 0 10px rgba(211, 47, 47, 0.4);
    transform: translateY(-1px);
}

.small-text {
    font-size: 0.85rem;
}

.hover-danger:hover {
    color: #ff4d4d !important;
    transition: color 0.2s;
}

.font-weight-600 {
    font-weight: 600;
}

.brand-link {
    height: 57px;
    /* Altura estándar de navbar en AdminLTE */
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.brand-image {
    margin-top: 0 !important;
}
</style>