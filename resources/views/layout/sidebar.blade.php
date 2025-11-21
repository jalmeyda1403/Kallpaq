<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="{{ asset('vendor/adminlte/dist/img/kallpaq_ico.png') }}" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"> KALLPAQ v1.0</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-2 pb-2 mb-2 d-flex">

            <div class="info">

                @if (Auth::check())
                    <a href="#" class="d-block text-white">
                        <i class="far fa-user nav-icon"></i>
                        {{ auth()->user()->name }}
                    </a>

                    <div class="small text-right mt-1 text-white">

                        <span>
                            Rol: {{ ucwords(auth()->user()->getRoleNames()->first()) }}
                        </span>

                        <span class="mx-1">|</span>

                        <a href="{{ route('logout') }}" class="text-white">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            Cerrar Sesión
                        </a>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="d-block text-white">
                        <i class="far fa-user nav-icon"></i>
                        Iniciar Sesión
                    </a>
                @endif
            </div>

        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <!-- Vista Usuarios No logueados -->
                <li class="nav-item has-treeview menu-open">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            Documentación por Procesos
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">

                         <li class="nav-item">
                            <a href="/vue/inventario-publico/0" class="nav-link"> <!-- Nueva línea -->
                                <i class="nav-icon fas fa-book"></i> <!-- O el ícono que prefieras -->
                                <p>Inventario de Procesos</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('procesos.mapa') }}" class="nav-link">
                                <i class="nav-icon fas fa-sitemap"></i>
                                <p>Mapa de Procesos</p>
                            </a>
                        </li>


                        <li class="nav-item">

                            <a href="{{ route('documento.buscar') }}" class="nav-link">                 
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p> Listado de documentos </p>
                            </a>
                        </li>

                       
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Listado de Indicadores</p>
                            </a>
                        </li>



                    </ul>
                </li>
                @if (Auth::check())
                    @php
                        $rol = auth()->user()->getRoleNames()->first();
                    @endphp
                    <!-- Configuración Requerimientos-->
                    <li
                        class="nav-item has-treeview {{ request()->is('requerimientos*') || request()->is('mis-requerimientos*') || request()->is('seguimiento*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-tasks"></i>
                            <p>
                                Gestión de Requerimientos
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            {{-- Bandejas para Facilitador y Subgerente --}}
                            @if (in_array($rol, ['facilitador', 'subgerente']))
                                <li class="nav-item">
                                    <a href="{{ route('requerimientos.crear') }}" class="nav-link">
                                        <i class="far fa-edit nav-icon"></i>
                                        <p>Crear Requerimiento</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/vue/mis-requerimientos" class="nav-link">
                                        <i class="fas fa-user-check nav-icon"></i>
                                        <p>Mis Requerimientos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('requerimientos.asignados', ['rol' => $rol]) }}" class="nav-link">
                                        <i class="fas fa-folder-open nav-icon fa-xs"></i>
                                        <p>Requerimientos Asignados</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('requerimientos.atendidos', ['rol' => $rol]) }}"
                                        class="nav-link">
                                        <i class="fas fa-check-circle fa-xs nav-icon "></i>
                                        <p>Requerimientos Atendidos</p>
                                    </a>
                                </li>
                            @endif

                            {{-- Bandejas para Especialista --}}
                            @if ($rol === 'especialista')
                                <li class="nav-item">
                                    <a href="/vue/mis-requerimientos" class="nav-link">
                                        <i class="fas fa-user-check nav-icon"></i>
                                        <p>Mis Requerimientos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('requerimientos.asignados', ['rol' => $rol]) }}"
                                        class="nav-link">
                                        <i class="fas fa-folder-open nav-icon fa-xs"></i>
                                        <p>Asignados a Mí</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('requerimientos.atendidos', ['rol' => $rol]) }}"
                                        class="nav-link">
                                        <i class="fas fa-check-circle fa-xs nav-icon "></i>
                                        <p>Requerimientos Atendidos</p>
                                    </a>
                                </li>
                            @endif
                            {{-- Bandejas para Supervisor y Admin --}}
                            @if (in_array($rol, ['supervisor', 'admin']))
                                <li class="nav-item">
                                    <a href="{{ url('/vue/requerimientos/index') }}" class="nav-link">
                                        <i class="fas fa-folder-open nav-icon fa-xs"></i>
                                        <p>Bandeja de Requerimientos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/vue/mis-requerimientos" class="nav-link">
                                        <i class="fas fa-user-check nav-icon"></i>
                                        <p>Mis Requerimientos</p>
                                    </a>
                                </li>
                            @endif

                            {{-- Dashboard para todos los roles, al final del módulo --}}
                            <li class="nav-item">
                                <a href="{{ route('requerimientos.seguimiento', ['rol' => $rol]) }}"
                                    class="nav-link">
                                    <i class="fas fa-tachometer-alt fa-xs nav-icon "></i>
                                    <p>Dashboard Requerimientos</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Configuración Procesos -->
                    <li
                        class="nav-item has-treeview {{ request()->is('procesos*') || request()->is('documento*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>
                                Gestión por Procesos
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <!-- 1. Gestión del Inventario -->
                            <li class="nav-item">
                                <a href="/vue/inventario-gestion" class="nav-link">
                                    <i class="nav-icon fas fa-cubes"></i>
                                    <p>Gestión del Inventario</p>
                                </a>
                            </li>
                            <!-- 2. Gestión de Procesos (listado de procesos) -->
                            <li class="nav-item">
                                <a href="{{ route('procesos.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-clipboard-list"></i>
                                    <p>Listado de Procesos</p>
                                </a>
                            </li>
                            <!-- 3. Gestión de Documentos (listado de documentos) -->
                            <li class="nav-item">
                                <a href="{{ url('/vue/documentos') }}" class="nav-link">
                                    <i class="nav-icon fas fa-clipboard-list"></i>
                                    <p>Listado de Documentos</p>
                                </a>
                            </li>
                            <!-- 4. Gestión del Desempeño (indicadores) -->
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-chart-bar"></i>
                                    <p>Listado de Indicadores</p>
                                </a>
                            </li>
                            <!-- 5. Partes Interesadas -->
                            <li class="nav-item">
                                <a href="{{ route('partes.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Partes Interesadas</p>
                                </a>
                            </li>
                            <!-- 6. Dashboard -->
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard Procesos</p>
                                </a>
                            </li>
                        </ul>
                    </li>


                    <!-- Configuración Mejora (SMP)  -->
                    <li
                        class="nav-item has-treeview {{ request()->is('smp*') || request()->is('mejora*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-sync-alt"></i>
                            <p>
                                Gestión de la Mejora
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if (in_array($rol, ['admin']))
                                <li class="nav-item">
                                    <a href="{{ url('/vue/mejora') }}"
                                        class="nav-link">
                                        <i class="fas fa-folder-open nav-icon fa-xs"></i>
                                        <p>Bandeja de Hallazgos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/vue/mis-hallazgos"
                                        class="nav-link">
                                        <i class="fas fa-user-check nav-icon fa-xs"></i>
                                        <p>Mis Hallazgos</p>
                                    </a>
                                </li>
                            @endif

                            @if (in_array($rol, ['gestor']))
                                <li class="nav-item">
                                    <a href="/vue/mis-hallazgos"
                                        class="nav-link">
                                        <i class="fas fa-user-check nav-icon fa-xs"></i>
                                        <p>Mis Hallazgos</p>
                                    </a>
                                </li>
                            @endif

                            @if (in_array($rol, ['auditor']))
                                <li class="nav-item">
                                    <a href="{{ url('/vue/mejora?evaluacion=true') }}"
                                        class="nav-link">
                                        <i class="fas fa-check-double nav-icon fa-xs"></i>
                                        <p>Evaluación de Eficacia</p>
                                    </a>
                                </li>
                            @endif

                            {{-- Dashboard para todos los roles, al final del módulo --}}
                            <li class="nav-item">
                                <a href="#"
                                    class="nav-link">
                                    <i class="fas fa-tachometer-alt fa-xs nav-icon "></i>
                                    <p>Dashboard de Mejora</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Configuracion Obligaciones -->
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-clipboard-check"></i>
                            <p>
                                Gestión de Obligaciones
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if (in_array($rol, ['admin', 'supervisor']))
                                <li class="nav-item">
                                    <a href="{{ url('/vue/obligaciones') }}" class="nav-link">
                                        <i class="nav-icon fas fa-folder-open"></i>
                                        <p>Bandeja de Obligaciones</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-plus-circle"></i>
                                        <p>Registrar Nueva Obligación</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-chart-line"></i>
                                        <p>Evaluación de Obligaciones</p>
                                    </a>
                                </li>
                            @endif

                            @if (in_array($rol, ['facilitador', 'subgerente', 'especialista']))
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-user-check"></i>
                                        <p>Mis Obligaciones Asignadas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-tasks"></i>
                                        <p>Seguimiento de Acciones</p>
                                    </a>
                                </li>
                            @endif

                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-flag-checkered"></i>
                                    <p>Acciones Identificadas</p>
                                </a>
                            </li>

                            <!-- Dashboard al final del módulo -->
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard Obligaciones</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- Configuracion Riesgos -->
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-exclamation-triangle"></i>
                            <p>
                                Gestión de Riesgos
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if (in_array($rol, ['admin', 'supervisor']))
                                <li class="nav-item">
                                    <a href="{{ url('/vue/riesgos') }}" class="nav-link">
                                        <i class="nav-icon fas fa-folder-open"></i>
                                        <p>Bandeja de Riesgos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-plus-circle"></i>
                                        <p>Registrar Nuevo Riesgo</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-chart-line"></i>
                                        <p>Evaluación de Riesgos</p>
                                    </a>
                                </li>
                            @endif

                            @if (in_array($rol, ['facilitador', 'subgerente', 'especialista']))
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-user-check"></i>
                                        <p>Mis Riesgos Asignados</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon fas fa-tasks"></i>
                                        <p>Seguimiento de Acciones</p>
                                    </a>
                                </li>
                            @endif

                            <!-- Dashboard al final del módulo -->
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard Riesgos</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Gestión de Continuidad -->
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-shield-alt"></i>
                            <p>
                                Gestión de Continuidad
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-clipboard-list"></i>
                                    <p>Planes de Continuidad</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-exclamation-circle"></i>
                                    <p>Escenarios de Riesgo</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-tasks"></i>
                                    <p>Activos Críticos</p>
                                </a>
                            </li>

                            <!-- Dashboard al final del módulo -->
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard Continuidad</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Nueva sección de Administración -->
                    <li class="nav-item has-treeview {{ request()->is('vue/administracion*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>
                                Administración
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/vue/administracion/usuarios') }}"
                                    class="nav-link {{ request()->is('vue/administracion/usuarios*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Gestionar Usuarios</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/vue/administracion/asignacion-ouos') }}"
                                    class="nav-link {{ request()->is('vue/administracion/asignacion-ouos*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-sitemap"></i>
                                    <p>Asignación OUO-Procesos</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-file-alt"></i>
                                    <p>Configuración General</p>
                                </a>
                            </li>

                            <!-- Dashboard al final del módulo -->
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard Administración</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                @endif




            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
