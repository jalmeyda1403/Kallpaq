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
                            <a href="{{ route('obligaciones.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-exclamation-circle"></i>
                                <p> Listado de Obligaciones</p>
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
                    <li class="nav-item has-treeview {{ (request()->is('requerimientos*') || request()->is('mis-requerimientos*') || request()->is('seguimiento*')) ? 'menu-open' : '' }}">
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
                                <li class="nav-item">
                                    <a href="{{ route('requerimientos.seguimiento', ['rol' => $rol]) }}"
                                        class="nav-link">
                                        <i class="fas fa-tachometer-alt fa-xs nav-icon "></i>
                                        <p>Dashboard Requerimientos</p>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>

                    <!-- Configuración Procesos -->
                    <li
                        class="nav-item has-treeview {{ request()->is('procesos*') || request()->is('documento*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>
                                Gestión por Procesos
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('procesos.inventario') }}" class="nav-link">
                                    <i class="nav-icon fas fa-sitemap"></i>
                                    <p>Inventario de Procesos</p>
                                </a>
                            </li>


                            <li class="nav-item">
                                <a href="{{ route('procesos.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-clipboard-list"></i>
                                    <p> Listado de Procesos </p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('partes.index') }}" class="nav-link">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Partes Interesadas</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('documento.listar') }}" class="nav-link">
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
                            <!-- Bandejas para Supervisor y Admin -->
                            @if (in_array($rol, ['supervisor', 'admin']))
                                <li class="nav-item">
                                    <a href="{{ url('/vue/mejora') }}"
                                        class="nav-link {{ request()->segment(3) == 'Ncm' ? 'active' : '' }}">
                                        <i class="fas fa-folder-open nav-icon fa-xs"></i>
                                        <p>Bandeja de SMP</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('requerimientos.asignados', ['rol' => $rol]) }}"
                                        class="nav-link">
                                        <i class="fas fa-user-check nav-icon fa-xs"></i>
                                        <p>SMP Asignadas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('requerimientos.atendidos', ['rol' => $rol]) }}"
                                        class="nav-link">
                                        <i class="fas fa-check-circle fa-xs nav-icon "></i>
                                        <p>SMP Concluidas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('requerimientos.seguimiento', ['rol' => $rol]) }}"
                                        class="nav-link">
                                        <i class="fas fa-tachometer-alt fa-xs nav-icon "></i>
                                        <p>Dashboard SMP</p>
                                    </a>
                                </li>
                            @endif

                            {{-- Bandejas para Facilitador y Subgerente --}}
                            @if (in_array($rol, ['facilitador', 'subgerente']))
                                <li class="nav-item">
                                    <a href="{{ route('requerimientos.crear') }}" class="nav-link">
                                        <i class="far fa-edit nav-icon"></i>
                                        <p>Bandeja SMP</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('requerimientos.asignados', ['rol' => $rol]) }}"
                                        class="nav-link">
                                        <i class="fas fa-folder-open nav-icon fa-xs"></i>
                                        <p>SMP aprobadas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('requerimientos.atendidos', ['rol' => $rol]) }}"
                                        class="nav-link">
                                        <i class="fas fa-check-circle fa-xs nav-icon "></i>
                                        <p>SMP concluidas</p>
                                    </a>
                                </li>
                            @endif
                            {{-- Bandejas para Especialista --}}
                            @if ($rol === 'especialista')
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
                                        <p>SMP Concluidas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('requerimientos.atendidos', ['rol' => $rol]) }}"
                                        class="nav-link">
                                        <i class="fas fa-check-circle fa-xs nav-icon "></i>
                                        <p>SMP para verificar</p>
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </li>

                    <!-- Configuracion Obligaciones -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-clipboard-check"></i>
                            <p>
                                Gestiónar Obligaciones
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p> Dashboard</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-exclamation-circle"></i>
                                    <p> Listado de Obligaciones</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-flag-checkered"></i>
                                    <p> Acciones Identificadas</p>
                                </a>
                            </li>

                        </ul>
                    </li>
                    <!-- Configuracion Riesgos -->
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-exclamation-triangle"></i>
                            <p>
                                Gestionar Riesgos
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-exclamation-circle"></i>
                                    <p>Riesgos Pendientes</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-check-circle"></i>
                                    <p>Riesgos Cerrados</p>
                                </a>
                            </li>

                        </ul>
                    </li>

                    <!-- Nueva sección de Administración -->
                    <li class="nav-item has-treeview {{ request()->is('vue/facilitadores*') || request()->is('vue/mis-mejoras-facilitador*') || request()->routeIs('usuarios.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>
                                Administración
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Gestionar Usuarios</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('vue.ouo-user-assignment') }}" class="nav-link {{ request()->routeIs('vue.ouo-user-assignment') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user-tag"></i>
                                    <p>Asignación OUO-Usuario</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/vue/facilitadores" class="nav-link {{ request()->is('vue/facilitadores*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-users-cog"></i>
                                    <p>Gestionar Facilitadores</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/vue/mis-mejoras-facilitador" class="nav-link {{ request()->is('vue/mis-mejoras-facilitador*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-tasks"></i>
                                    <p>Mis Mejoras (Facilitador)</p>
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
