<template>
    <div class="container-fluid py-4 min-vh-100 bg-light-gray animate-fade-in">
        <!-- Breadcrumb Superior -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-light py-2 px-3 rounded">
                <li class="breadcrumb-item"><router-link to="/home" class="text-muted"><i class="fas fa-home mr-1"></i>
                        Inicio</router-link></li>
                <li class="breadcrumb-item"><router-link to="/revision-direccion" class="text-muted">Revisiones por
                        la Dirección</router-link></li>
                <li class="breadcrumb-item active font-weight-bold text-dark">{{ revision?.codigo || 'Detalle' }}</li>
            </ol>
        </nav>

        <ErrorState v-if="error" :message="error" @retry="cargarRevision" />

        <!-- Skeleton Loading Structure (Mejora la percepción de velocidad) -->
        <div v-if="isLoading && !revision" class="animate-pulse">
            <!-- Skeleton Banner -->
            <div class="card border-0 shadow-sm rounded-xl mb-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="row no-gutters">
                        <div class="col-lg-8 p-4">
                            <div class="bg-light rounded mb-3" style="height: 32px; width: 60%;"></div>
                            <div class="d-flex mb-4">
                                <div class="bg-light rounded mr-2" style="height: 24px; width: 100px;"></div>
                                <div class="bg-light rounded" style="height: 24px; width: 80px;"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="bg-light rounded" style="height: 50px;"></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light rounded" style="height: 50px;"></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light rounded" style="height: 50px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 bg-light p-4"></div>
                    </div>
                </div>
            </div>
            <!-- Skeleton Tabs -->
            <div class="bg-white rounded shadow-sm mb-4" style="height: 60px;"></div>
        </div>

        <!-- Contenido Principal -->
        <template v-else-if="revision">

            <!-- Banner de Cabecera Premium -->
            <div class="card border-0 shadow-sm rounded-xl overflow-hidden mb-4">
                <div class="card-body p-0">
                    <div class="row no-gutters">
                        <div class="col-lg-8 p-4 d-flex flex-column justify-content-center">
                            <div class="d-flex align-items-center mb-3">
                                <div>
                                    <h3 class="h3 font-weight-bold mb-0 text-dark">{{ revision.titulo }}</h3>
                                    <div class="d-flex align-items-center mt-1">
                                        <span class="text-muted mr-3 small"><i class="fas fa-fingerprint mr-1"></i> {{
                                            revision.codigo }}</span>
                                        <span class="badge badge-pill px-3 shadow-none border"
                                            :class="'badge-' + getEstadoColor(revision.estado)">
                                            {{ getEstadoLabel(revision.estado) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3 mt-md-0">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="d-flex align-items-center pr-3 border-right h-100">
                                        <div class="icon-circle-md bg-white border mr-3"><i
                                                class="fas fa-user text-muted"></i></div>
                                        <div>
                                            <p class="mb-0 text-muted extra-small font-weight-bold text-uppercase">
                                                Responsable</p>
                                            <p class="mb-0 font-weight-bold text-dark small">{{
                                                revision.responsable?.name ||
                                                'No asignado' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="d-flex align-items-center pr-3 border-right h-100">
                                        <div class="icon-circle-md bg-white border mr-3"><i
                                                class="fas fa-calendar-alt text-muted"></i></div>
                                        <div>
                                            <p class="mb-0 text-muted extra-small font-weight-bold text-uppercase">
                                                Programado</p>
                                            <p class="mb-0 font-weight-bold text-dark small">{{
                                                formatDate(revision.fecha_programada) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center h-100">
                                        <div class="icon-circle-md bg-white border mr-3"><i
                                                class="fas fa-clock text-muted"></i></div>
                                        <div>
                                            <p class="mb-0 text-muted extra-small font-weight-bold text-uppercase">
                                                Periodo Fiscal</p>
                                            <p class="mb-0 font-weight-bold text-dark small">{{ revision.periodo }} - {{
                                                revision.anio }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna de Avance y Acciones Rápidas -->
                        <div
                            class="col-lg-4 bg-dark text-white p-4 d-flex flex-column justify-content-center align-items-center text-center">
                            <h6 class="extra-small font-weight-bold text-uppercase text-white-50 mb-3 tracking-widest">
                                Avance del Cumplimiento</h6>
                            <div class="position-relative mb-3">
                                <svg class="progress-ring" width="120" height="120">
                                    <circle class="progress-ring__circle_bg" stroke="rgba(255,255,255,0.1)"
                                        stroke-width="8" fill="transparent" r="50" cx="60" cy="60" />
                                    <circle class="progress-ring__circle" stroke="#FFC107" stroke-width="8"
                                        fill="transparent" r="50" cx="60" cy="60"
                                        :style="{ strokeDasharray: 314, strokeDashoffset: 314 - (314 * (revision.avance_general || 0) / 100) }" />
                                </svg>
                                <div class="progress-ring__text h3 font-weight-bold mb-0">{{ revision.avance_general ||
                                    0
                                }}%</div>
                            </div>
                            <div class="d-flex mt-2">
                                <button v-if="revision.estado === 'realizada'" @click="subirActa"
                                    class="btn btn-warning btn-sm rounded-pill px-4 font-weight-bold shadow-sm mr-2 transition-all hover-up">
                                    <i class="fas fa-file-upload mr-1"></i> Acta
                                </button>
                                <button @click="router.push('/revision-direccion')"
                                    class="btn btn-outline-light btn-sm rounded-pill px-4 transition-all">
                                    <i class="fas fa-chevron-left mr-1"></i> Volver
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation Premium -->
            <div class="custom-tabs-container mb-4 sticky-top-tabs">
                <div class="tabs-scroll-wrapper">
                    <ul class="nav nav-pills custom-pills d-flex align-items-center">
                        <li class="nav-item mr-2">
                            <a class="nav-link" :class="{ active: activeTab === 'entradas' }"
                                @click.prevent="activeTab = 'entradas'" href="#">
                                <i class="fas fa-sign-in-alt mr-2"></i> Entradas
                                <span class="tab-badge"
                                    :class="activeTab === 'entradas' ? 'bg-white text-primary' : 'bg-primary text-white'">
                                    {{ revision.entradas?.length || 0 }}
                                </span>
                            </a>
                        </li>
                        <li class="nav-item mr-2">
                            <a class="nav-link" :class="{ active: activeTab === 'salidas' }"
                                @click.prevent="activeTab = 'salidas'" href="#">
                                <i class="fas fa-sign-out-alt mr-2"></i> Salidas y Decisiones
                                <span class="tab-badge"
                                    :class="activeTab === 'salidas' ? 'bg-white text-success' : 'bg-success text-white'">
                                    {{ revision.salidas?.length || 0 }}
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" :class="{ active: activeTab === 'compromisos' }"
                                @click.prevent="activeTab = 'compromisos'" href="#">
                                <i class="fas fa-tasks mr-2"></i> Compromisos
                                <span class="tab-badge"
                                    :class="activeTab === 'compromisos' ? 'bg-white text-warning' : 'bg-warning text-dark'">
                                    {{ revision.compromisos?.length || 0 }}
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tab Panels -->
            <div class="tab-content">

                <!-- ENTRADAS PANEL -->
                <div v-if="activeTab === 'entradas'" class="animate-slide-up">
                    <div class="card border-0 shadow-sm rounded-xl mb-4 overflow-hidden mt-2">
                        <div
                            class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between border-bottom">
                            <div>
                                <h4 class="h5 font-weight-bold text-dark mb-1">Entradas de la Revisión</h4>
                                <p class="text-muted small mb-0">Requisitos especificados según la norma ISO 9001:2015
                                    cláusula 9.3.2</p>
                            </div>
                            <button
                                class="btn btn-primary rounded-pill px-4 font-weight-bold shadow-sm transition-all hover-up"
                                @click="abrirEntradaModal(null)">
                                <i class="fas fa-plus mr-2"></i> Agregar Entrada
                            </button>
                        </div>
                        <div class="card-body bg-light-soft px-4 py-4">
                            <div v-if="!revision.entradas?.length" class="empty-state text-center py-5">
                                <div class="empty-state-icon bg-white shadow-sm mb-3 mx-auto">
                                    <i class="fas fa-inbox fa-3x text-primary opacity-3"></i>
                                </div>
                                <h5 class="font-weight-bold">Sin Entradas Registradas</h5>
                                <p class="text-muted small">Inicie la gestión agregando la primera entrada informativa
                                    para esta revisión.</p>
                                <button class="btn btn-primary rounded-pill px-4 mt-2" @click="abrirEntradaModal(null)">
                                    Comenzar ahora
                                </button>
                            </div>

                            <div v-else class="row">
                                <div v-for="entrada in revision.entradas" :key="entrada.id"
                                    class="col-xl-4 col-md-6 mb-4">
                                    <div
                                        class="card h-100 border-0 shadow-sm rounded-xl overflow-hidden hover-shadow-lg transition-all">
                                        <div class="card-body p-4 bg-white">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <i :class="getEntradaIcon(entrada.tipo_entrada)"
                                                    class="text-primary mb-0"
                                                    style="font-size: 1.75rem; line-height: 1; display: inline-block !important;"></i>
                                                <div class="dropdown">
                                                    <button class="btn btn-link text-muted p-0" data-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right shadow border-0">
                                                        <a class="dropdown-item py-2" href="#"
                                                            @click.prevent="abrirEntradaModal(entrada)">
                                                            <i class="fas fa-edit mr-2 text-warning"></i> Editar
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item py-2 text-danger" href="#"
                                                            @click.prevent="confirmarEliminarEntrada(entrada.id)">
                                                            <i class="fas fa-trash-alt mr-2"></i> Eliminar
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <h6 class="font-weight-bold text-dark mb-1">{{ entrada.titulo }}</h6>
                                            <small class="text-primary font-weight-bold d-block mb-3">{{
                                                tiposEntrada[entrada.tipo_entrada] || entrada.tipo_entrada }}</small>

                                            <p class="text-muted small mb-3 description-truncate">{{ entrada.descripcion
                                                }}</p>

                                            <div v-if="entrada.conclusion"
                                                class="bg-light p-2 rounded small mb-3 info-border-left">
                                                <strong>Análisis:</strong> {{ entrada.conclusion }}
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between mt-auto">
                                                <span class="badge badge-pill px-2 py-1 small"
                                                    :class="getEntradaEstadoClass(entrada.estado)">
                                                    {{ entrada.estado }}
                                                </span>
                                                <small class="text-muted extra-small"><i
                                                        class="fas fa-calendar mr-1"></i> {{
                                                            formatDate(entrada.created_at) }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SALIDAS PANEL -->
                <div v-if="activeTab === 'salidas'" class="animate-slide-up">
                    <div class="card border-0 shadow-sm rounded-xl mb-4 overflow-hidden mt-2">
                        <div
                            class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between border-bottom">
                            <div>
                                <h4 class="h5 font-weight-bold text-dark mb-1">Salidas y Decisiones</h4>
                                <p class="text-muted small mb-0">Resultados de la revisión según ISO 9001:2015 cláusula
                                    9.3.3</p>
                            </div>
                            <button
                                class="btn btn-success rounded-pill px-4 font-weight-bold shadow-sm transition-all hover-up"
                                @click="abrirSalidaModal(null)">
                                <i class="fas fa-plus mr-2"></i> Agregar Salida
                            </button>
                        </div>
                        <div class="card-body bg-light-soft px-4 py-4">
                            <div v-if="!revision.salidas?.length" class="empty-state text-center py-5">
                                <div class="empty-state-icon bg-white shadow-sm mb-3 mx-auto">
                                    <i class="fas fa-sign-out-alt fa-3x text-success opacity-3"></i>
                                </div>
                                <h5 class="font-weight-bold">Sin Salidas ni Decisiones</h5>
                                <p class="text-muted small">Registre las conclusiones, necesidades de recursos o cambios
                                    necesarios resultantes de la reunión.</p>
                                <button class="btn btn-success rounded-pill px-4 mt-2" @click="abrirSalidaModal(null)">
                                    Registrar salida
                                </button>
                            </div>

                            <div v-else class="row">
                                <div v-for="salida in revision.salidas" :key="salida.id" class="col-xl-4 col-md-6 mb-4">
                                    <div
                                        class="card h-100 border-0 shadow-sm rounded-xl overflow-hidden hover-shadow-lg transition-all">
                                        <div class="card-body p-4 bg-white">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i :class="getSalidaIcon(salida.tipo_salida)"
                                                        class="text-success mb-0"
                                                        style="font-size: 1.75rem; line-height: 1; display: inline-block !important;"></i>
                                                    <span v-if="salida.compromisos?.length > 0"
                                                        class="badge badge-warning ml-3 px-2 py-1 shadow-xs animate-pulse"
                                                        title="Compromiso generado">
                                                        <i class="fas fa-tasks mr-1"></i> Compromiso
                                                    </span>
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-link text-muted p-0" data-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right shadow border-0">
                                                        <a class="dropdown-item py-2" href="#"
                                                            @click.prevent="abrirSalidaModal(salida)">
                                                            <i class="fas fa-edit mr-2 text-warning"></i> Editar
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item py-2 text-danger" href="#"
                                                            @click.prevent="confirmarEliminarSalida(salida.id)">
                                                            <i class="fas fa-trash-alt mr-2"></i> Eliminar
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <h6 class="font-weight-bold text-dark mb-1 description-truncate-2">{{
                                                salida.descripcion }}</h6>
                                            <small class="text-success font-weight-bold d-block mb-3">{{
                                                tiposSalida[salida.tipo_salida] || 'Decisión' }}</small>

                                            <div v-if="salida.recursos_necesarios"
                                                class="bg-light p-2 rounded small mb-3 border-left border-success">
                                                <strong>Recursos:</strong> {{ salida.recursos_necesarios }}
                                            </div>

                                            <div
                                                class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top">
                                                <div v-if="salida.compromisos?.length > 0"
                                                    class="d-flex align-items-center">
                                                    <div
                                                        class="avatar-xs mr-2 bg-warning text-white border-0 shadow-xs">
                                                        {{ salida.compromisos[0].responsable?.name?.charAt(0) || '?' }}
                                                    </div>
                                                    <div class="overflow-hidden">
                                                        <p class="mb-0 extra-small font-weight-bold text-dark text-truncate"
                                                            style="max-width: 140px;">
                                                            {{ salida.compromisos[0].responsable?.name || 'No asignado'
                                                            }}
                                                        </p>
                                                        <small class="text-muted italic d-block"
                                                            style="font-size: 0.65rem; line-height: 1;">Responsable
                                                            compromiso</small>
                                                    </div>
                                                </div>
                                                <div v-else class="d-flex align-items-center">
                                                    <div class="avatar-xs mr-2 bg-light-gray text-muted border-0"><i
                                                            class="fas fa-info"></i></div>
                                                    <small class="text-muted extra-small font-weight-bold">Decisión
                                                        informativa</small>
                                                </div>

                                                <div class="text-right ml-auto">
                                                    <template v-if="salida.compromisos?.length > 0">
                                                        <p class="mb-0 extra-small font-weight-bold text-danger">
                                                            Vencimiento</p>
                                                        <small class="text-dark font-weight-bold">{{
                                                            formatDate(salida.compromisos[0].fecha_limite) }}</small>
                                                    </template>
                                                    <template v-else>
                                                        <p class="mb-0 extra-small font-weight-bold text-muted">
                                                            Registrado</p>
                                                        <small class="text-muted">{{ formatDate(salida.created_at)
                                                        }}</small>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- COMPROMISOS PANEL -->
                <div v-if="activeTab === 'compromisos'" class="animate-slide-up">
                    <div class="card border-0 shadow-sm rounded-xl mb-4 overflow-hidden mt-2">
                        <div
                            class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between border-bottom">
                            <div>
                                <h4 class="h5 font-weight-bold text-dark mb-1">Listado de Compromisos</h4>
                                <p class="text-muted small mb-0">Seguimiento detallado de tareas asignadas con plazos
                                    definidos</p>
                            </div>
                            <button
                                class="btn btn-warning rounded-pill px-4 font-weight-bold shadow-sm transition-all hover-up"
                                @click="abrirCompromisoModal(null)">
                                <i class="fas fa-plus mr-2"></i> Nuevo Compromiso
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div v-if="!revision.compromisos?.length" class="empty-state text-center py-5">
                                <div class="empty-state-icon bg-light-warning mb-3 mx-auto">
                                    <i class="fas fa-tasks fa-3x text-warning opacity-3"></i>
                                </div>
                                <h5 class="font-weight-bold">Sin Compromisos Pendientes</h5>
                                <p class="text-muted small">No se han registrado compromisos de seguimiento para esta
                                    revisión aún.</p>
                                <button class="btn btn-warning rounded-pill px-4 mt-2"
                                    @click="abrirCompromisoModal(null)">
                                    Crear compromiso
                                </button>
                            </div>

                            <div v-else class="table-responsive">
                                <table class="table table-hover table-custom align-middle mb-0">
                                    <thead class="bg-gray-50 border-bottom">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-uppercase extra-small font-weight-bold text-muted">
                                                ID / Código</th>
                                            <th
                                                class="px-4 py-3 text-uppercase extra-small font-weight-bold text-muted">
                                                Descripción</th>
                                            <th
                                                class="px-4 py-3 text-uppercase extra-small font-weight-bold text-muted">
                                                Responsable</th>
                                            <th
                                                class="px-4 py-3 text-uppercase extra-small font-weight-bold text-muted">
                                                Vencimiento</th>
                                            <th
                                                class="px-4 py-3 text-uppercase extra-small font-weight-bold text-muted">
                                                Sistemas</th>
                                            <th
                                                class="px-4 py-3 text-uppercase extra-small font-weight-bold text-muted text-center">
                                                Avance</th>
                                            <th
                                                class="px-4 py-3 text-uppercase extra-small font-weight-bold text-muted text-center">
                                                Estado</th>
                                            <th
                                                class="px-4 py-3 text-uppercase extra-small font-weight-bold text-muted text-right">
                                                Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="comp in revision.compromisos" :key="comp.id"
                                            :class="{ 'row-vencida': comp.estado === 'vencido' }">
                                            <td class="px-4 py-3 font-weight-bold text-primary">{{ comp.codigo }}</td>
                                            <td class="px-4 py-3 small text-dark font-weight-medium min-w-300">
                                                {{ comp.descripcion }}
                                                <div v-if="comp.recursos_necesarios"
                                                    class="extra-small text-muted mt-1 italic">
                                                    <i class="fas fa-box-open mr-1"></i> {{ comp.recursos_necesarios }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 small">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm mr-2 text-uppercase">{{
                                                        comp.responsable?.name?.charAt(0) || '?' }}</div>
                                                    {{ comp.responsable?.name || '-' }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 small">
                                                <div
                                                    :class="{ 'text-danger font-weight-bold': comp.estado === 'vencido' }">
                                                    {{ formatDate(comp.fecha_limite) }}
                                                </div>
                                                <div v-if="comp.dias_restantes !== null && comp.estado !== 'completado'"
                                                    class="badge-timer mt-1"
                                                    :class="getTimerBadgeClass(comp.dias_restantes)">
                                                    <i class="far fa-clock mr-1"></i> {{ comp.dias_restantes }}d rest.
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 small">
                                                <div v-if="comp.sistemas_gestion && comp.sistemas_gestion.length">
                                                    <span v-for="sys in comp.sistemas_gestion" :key="sys"
                                                        class="badge badge-light border mr-1">
                                                        {{ sys }}
                                                    </span>
                                                </div>
                                                <span v-else class="text-muted text-xs">-</span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="d-inline-flex flex-column align-items-center">
                                                    <div class="progress progress-table mb-1 shadow-inner">
                                                        <div class="progress-bar"
                                                            :class="comp.avance >= 100 ? 'bg-success' : 'bg-info'"
                                                            :style="{ width: (comp.avance || 0) + '%' }"></div>
                                                    </div>
                                                    <strong class="extra-small">{{ comp.avance || 0 }}%</strong>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="badge badge-pill shadow-xs px-3 py-1 font-weight-bold"
                                                    :class="getCompromisoEstadoClass(comp.estado)">
                                                    {{ comp.estado }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right white-space-nowrap">
                                                <button @click="verSeguimientos(comp)"
                                                    class="btn btn-icon-round btn-light-info mr-1"
                                                    title="Gestionar Seguimiento">
                                                    <i class="fas fa-stream"></i>
                                                </button>
                                                <button @click="abrirCompromisoModal(comp)"
                                                    class="btn btn-icon-round btn-light-warning"
                                                    title="Editar Definición">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Modales Dinámicos con Soporte de Edición -->
        <EntradaModal v-if="showEntradaModal" :revision-id="revision?.id" :entrada="entradaEditar"
            @saved="onDataUpdated" @close="cerrarEntradaModal" />

        <SalidaModal v-if="showSalidaModal" :revision-id="revision?.id" :salida="salidaEditar" @saved="onDataUpdated"
            @close="cerrarSalidaModal" />

        <CompromisoModal v-if="showCompromisoModal" :revision-id="revision?.id" :compromiso="compromisoEditar"
            @saved="onDataUpdated" @close="cerrarCompromisoModal" />

        <SeguimientoModal v-if="showSeguimientoModal" :compromiso="compromisoSeguimiento"
            @updated="handleSeguimientoUpdated" @close="showSeguimientoModal = false" />

        <!-- Toast de notificación opcional -->
        <div v-show="successMessage" class="toast-container position-fixed bottom-0 right-0 p-3" style="z-index: 2000;">
            <div class="toast show animate-slide-up border-0 shadow-lg rounded-lg overflow-hidden">
                <div class="toast-header bg-success text-white">
                    <i class="fas fa-check-circle mr-2"></i>
                    <strong class="mr-auto text-white">Éxito</strong>
                    <button type="button" class="ml-2 mb-1 close text-white"
                        @click="successMessage = ''">&times;</button>
                </div>
                <div class="toast-body bg-white">{{ successMessage }}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useRevisionDireccionStore } from '@/stores/revisionDireccionStore';
import LoadingState from '@/components/generales/LoadingState.vue';
import ErrorState from '@/components/generales/ErrorState.vue';
import EntradaModal from './EntradaModal.vue';
import SalidaModal from './SalidaModal.vue';
import CompromisoModal from './CompromisoModal.vue';
import SeguimientoModal from './SeguimientoModal.vue';
import Swal from 'sweetalert2';

const route = useRoute();
const router = useRouter();
const store = useRevisionDireccionStore();

const activeTab = ref('entradas');
const successMessage = ref('');

// Estados de modales
const showEntradaModal = ref(false);
const showSalidaModal = ref(false);
const showCompromisoModal = ref(false);
const showSeguimientoModal = ref(false);

// Refs para datos a editar
const entradaEditar = ref(null);
const salidaEditar = ref(null);
const compromisoEditar = ref(null);
const compromisoSeguimiento = ref(null);

// Computed
const isLoading = computed(() => store.isLoading);
const error = computed(() => store.error);
const revision = computed(() => store.revisionActual);
const tiposEntrada = computed(() => store.tiposEntrada);
const tiposSalida = computed(() => store.tiposSalida);

// Métodos
const cargarRevision = async () => {
    try {
        await store.fetchRevision(route.params.id);
        // Cargar parámetros en paralelo
        store.fetchTiposEntrada();
        store.fetchTiposSalida();
    } catch (err) {
        console.error('Error al cargar la revisión', err);
    }
};

const onDataUpdated = (msg) => {
    successMessage.value = msg;
    setTimeout(() => { successMessage.value = ''; }, 4000);
    cargarRevision();
};

const handleSeguimientoUpdated = () => {
    if (compromisoSeguimiento.value && revision.value?.compromisos) {
        const actualizado = revision.value.compromisos.find(c => c.id === compromisoSeguimiento.value.id);
        if (actualizado) compromisoSeguimiento.value = actualizado;
    }
};

// Acciones Entradas
const abrirEntradaModal = (entrada = null) => {
    entradaEditar.value = entrada;
    showEntradaModal.value = true;
};
const cerrarEntradaModal = () => {
    showEntradaModal.value = false;
    entradaEditar.value = null;
};
const confirmarEliminarEntrada = async (id) => {
    const result = await Swal.fire({
        title: '¿Eliminar entrada?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        try {
            await store.deleteEntrada(id);
            Swal.fire('Eliminado', 'La entrada ha sido removida.', 'success');
        } catch (err) {
            Swal.fire('Error', 'No se pudo eliminar la entrada.', 'error');
        }
    }
};

// Acciones Salidas
const abrirSalidaModal = (salida = null) => {
    salidaEditar.value = salida;
    showSalidaModal.value = true;
};
const cerrarSalidaModal = () => {
    showSalidaModal.value = false;
    salidaEditar.value = null;
};
const confirmarEliminarSalida = async (id) => {
    const result = await Swal.fire({
        title: '¿Eliminar salida?',
        text: 'Se eliminará la decisión. Si tiene compromisos asociados, estos permanecerán.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        try {
            await store.deleteSalida(id);
            Swal.fire('Eliminado', 'Salida eliminada correctamente.', 'success');
        } catch (err) {
            Swal.fire('Error', 'Error al eliminar salida.', 'error');
        }
    }
};

// Acciones Compromisos
const abrirCompromisoModal = (comp = null) => {
    compromisoEditar.value = comp;
    showCompromisoModal.value = true;
};
const cerrarCompromisoModal = () => {
    showCompromisoModal.value = false;
    compromisoEditar.value = null;
};
const verSeguimientos = (comp) => {
    compromisoSeguimiento.value = comp;
    showSeguimientoModal.value = true;
};

const subirActa = () => {
    Swal.fire({
        title: 'Subir Acta de Revisión',
        input: 'file',
        inputAttributes: { 'accept': 'application/pdf', 'aria-label': 'Subir archivo PDF del acta' },
        showCancelButton: true,
        confirmButtonText: 'Subir',
        showLoaderOnConfirm: true,
        preConfirm: async (file) => {
            if (!file) return Swal.showValidationMessage('Debe seleccionar un archivo');
            const formData = new FormData();
            formData.append('acta', file);
            try {
                await store.subirActa(revision.value.id, formData);
                return true;
            } catch (err) {
                Swal.showValidationMessage(`Error: ${err.message}`);
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire('¡Éxito!', 'El acta ha sido cargada correctamente.', 'success');
            cargarRevision();
        }
    });
};

// Helpers
const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const getEstadoColor = (estado) => {
    const colors = {
        programada: 'info',
        aprobada: 'info',
        en_preparacion: 'info',
        realizada: 'success',
        cancelada: 'secondary'
    };
    return colors[estado] || 'primary';
};

const getEstadoLabel = (estado) => {
    const labels = {
        programada: 'Programada',
        aprobada: 'Aprobada',
        en_preparacion: 'En Preparación',
        realizada: 'Realizada',
        cancelada: 'Cancelada'
    };
    return labels[estado] || estado;
};

const getEntradaIcon = (tipo) => {
    const icons = {
        estado_acciones_anteriores: 'fas fa-history',
        cambios_contexto_externo: 'fas fa-globe-americas',
        cambios_contexto_interno: 'fas fa-university',
        retroalimentacion_partes_interesadas: 'fas fa-user-friends',
        desempeno_procesos: 'fas fa-tasks',
        no_conformidades_acciones_correctivas: 'fas fa-shield-alt',
        resultados_auditorias: 'fas fa-clipboard-check',
        satisfaccion_cliente: 'fas fa-smile-beam',
        oportunidades_mejora: 'fas fa-rocket',
        recursos: 'fas fa-tools',
        eficacia_acciones_riesgos: 'fas fa-exclamation-circle'
    };
    return icons[tipo] || 'fas fa-dot-circle';
};

const getSalidaIcon = (tipo) => {
    const icons = {
        oportunidades_mejora: 'fas fa-chart-line',
        cambios_sistema: 'fas fa-sync-alt',
        necesidad_recursos: 'fas fa-box-open',
        otros: 'fas fa-check-double'
    };
    return icons[tipo] || 'fas fa-sign-out-alt';
};

const getEntradaEstadoClass = (e) => ({ 'badge-warning-soft': e === 'pendiente', 'badge-info-soft': e === 'revisado', 'badge-success-soft': e === 'aprobado' });
const getCompromisoEstadoClass = (e) => {
    const classes = {
        'programada': 'badge-info',
        'en_proceso': 'badge-info',
        'pendiente': 'badge-warning',
        'completado': 'badge-success',
        'vencido': 'badge-danger',
        'cancelado': 'badge-secondary'
    };
    return classes[e] || 'badge-secondary';
};
const getTimerBadgeClass = (d) => d <= 3 ? 'timer-danger' : d <= 7 ? 'timer-warning' : 'timer-normal';

onMounted(cargarRevision);
</script>

<style scoped>
.bg-light-gray {
    background-color: #f8f9fa;
}

.bg-light-soft {
    background-color: #f4f6f9;
}

.rounded-xl {
    border-radius: 1rem !important;
}

.shadow-xs {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Progress Ring */
.progress-ring {
    transform: rotate(-90deg);
}

.progress-ring__circle {
    transition: stroke-dashoffset 0.35s;
    transform-origin: 50% 50%;
}

.progress-ring__text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
}

/* Tabs Navigation */
.custom-tabs-container {
    border-bottom: 2px solid #e9ecef;
}

.custom-pills .nav-link {
    border-radius: 0;
    border-bottom: 3px solid transparent;
    font-weight: 600;
    color: #6c757d;
    padding: 1rem 1.5rem;
    transition: all 0.3s;
}

.custom-pills .nav-link.active {
    background: transparent !important;
    color: #007bff !important;
    border-bottom-color: #007bff;
}

.tab-badge {
    margin-left: 8px;
    font-size: 0.75rem;
    padding: 2px 8px;
    border-radius: 10px;
    font-weight: bold;
}

/* Card Styles */
.hover-shadow-lg:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
}

.icon-circle-md {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.badge-status {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Truncado de texto avanzado */
.description-truncate {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    height: 3.5rem;
}

.description-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    height: 3rem;
}

/* Info Borders */
.info-border-left {
    border-left: 3px solid #007bff;
}

.badge-warning-soft {
    background: #fff3cd;
    color: #856404;
}

.badge-info-soft {
    background: #d1ecf1;
    color: #0c5460;
}

.badge-success-soft {
    background: #d4edda;
    color: #155724;
}

/* Table Styles */
.table-custom thead th {
    font-size: 0.7rem;
    border: none;
}

.table-custom tbody tr {
    transition: all 0.2s;
}

.avatar-sm {
    width: 32px;
    height: 32px;
    background: #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: bold;
    color: #495057;
}

.avatar-xs {
    width: 24px;
    height: 24px;
    background: #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.6rem;
    font-weight: bold;
    color: #6c757d;
}

.progress-table {
    height: 6px;
    width: 60px;
    border-radius: 3px;
    background: #e9ecef;
}

.btn-icon-round {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
}

.btn-light-info {
    background: #e1f5fe;
    color: #0288d1;
}

.btn-light-warning {
    background: #fffde7;
    color: #fbc02d;
}

/* Timer Badges */
.badge-timer {
    font-size: 0.7rem;
    padding: 2px 6px;
    border-radius: 4px;
    display: inline-block;
}

.timer-danger {
    background: #ffebee;
    color: #c62828;
}

.timer-warning {
    background: #fff3e0;
    color: #ef6c00;
}

.timer-normal {
    background: #f1f8e9;
    color: #33691e;
}

/* Animations */
.animate-fade-in {
    animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

.animate-slide-up {
    animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }

    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.hover-up:hover {
    transform: translateY(-2px);
}

.sticky-top-tabs {
    position: sticky;
    top: 0;
    z-index: 1020;
    background: #f8f9fa;
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: .7;
    }
}

.shadow-xs {
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}
</style>
