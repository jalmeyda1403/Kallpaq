<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="{{asset('vendor/adminlte/dist/img/kallpaq_ico.png')}}" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"> KALLPAQ v1.0</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">

            <div class="info">

                @if (Auth::check())
                    <a href="#" class="d-block"> <i class="far fa-user nav-icon"></i> {{ auth()->user()->name }}</a>
                    <span class="d-block small text-right">Bienvenido {{ auth()->user()->getRoleNames()->first() }}</span>
                    <span class="d-block small text-right"> <a href="{{ route('logout') }}" >
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            Cerrar Sesión
                        </a></span>
                @endif
            </div>

        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                @if (Auth::check())
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            Gestión de Procesos
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('usuario.mapear-procesos', auth()->user()->id) }}" class="nav-link">
                                <i class="fas fa-sitemap"></i>
                                <p> Mapa de Procesos</p>
                            </a>
                        </li>
                       
                        <li class="nav-item">
                          
                            <a href="{{ route('usuario.listar-procesos', auth()->user()->id) }}" class="nav-link">
                                <i class="fas fa-clipboard-list"></i>
                                <p> Listar Procesos</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('requerimientos.index', auth()->user()->id) }}" class="nav-link">
                                <i class="fas fa-file-alt"></i>
                                <p> Generar Requerimiento</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-tasks"></i>
                                <p> Seguimiento Requerimiento</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                <!-- Configuración Auditorias SIG (SMP) 
                 <li class="nav-item">
                    <a href="#" class="nav-link">                       
                        <i class="nav-icon fas fa-search"></i>
                        <p>
                            Gestión de Auditorias
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('programa.index') }}" class="nav-link">
                                <i class="fas fa-calendar-alt"></i>
                            <p>Programa de Auditoría</p>
                            </a>
                        </li>
                    </ul>
                </li>
                   -->
                <!-- Configuración Mejora (SMP)  -->
                <li class="nav-item has-treeview {{ request()->is('smp*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-arrow-circle-up"></i>

                        <p>
                            Gestión de la Mejora
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('smp.dashboard') }}" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard Mejora</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('smp.index', ['clasificacion' => 'NCM,Ncme']) }}"
                                class="nav-link {{ request()->segment(3) == 'Ncm' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-times-circle"></i>
                                <p>No Conformidades</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('smp.index', ['clasificacion' => 'Obs']) }}"
                                class="nav-link {{ request()->segment(3) == 'Obs' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-exclamation-triangle"></i>
                                <p>Observaciones</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('smp.index', ['clasificacion' => 'Odm']) }}"
                                class="nav-link {{ request()->segment(3) == 'Odm' ? 'active' : '' }}">
                                <i class="nav-icon fas fa-lightbulb"></i>
                                <p>Oportunidades de Mejora</p>
                            </a>
                        </li>

                    </ul>
                </li>
                <!-- Configuración Indicadores -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>
                            Gestión de Resultados
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('indicadores.index') }}" class="nav-link">
                                <i class="fas fa-chart-bar"></i>
                                <p>Listado de Indicadores</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-exclamation-circle"></i>
                                <p>Acciones Pendientes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-check-circle"></i>
                                <p>Acciones Cerradas</p>
                            </a>
                        </li>

                    </ul>
                </li>

                 <!-- Configuracion Contexto -->
                 <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-project-diagram"></i>
                        <p>
                            Análisis de Contexto
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('contexto.index') }}" class="nav-link">
                                <i class="fas fa-sliders-h"></i>
                                <p>Determinación Contexto</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-sliders-h"></i>
                                <p>Analisis de Contexto</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Configuracion Riesgos -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-exclamation-triangle"></i>
                        <p>
                            Gestión de Riesgos
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-exclamation-circle"></i>
                                <p>Riesgos Pendientes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-check-circle"></i>
                                <p>Riesgos Cerrados</p>
                            </a>
                        </li>
                       
                    </ul>
                </li>

                <!-- Administración de Usuarios -->
                <li class="nav-item has-treeview {{ request()->routeIs('usuarios.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Gestión de Usuarios
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('usuarios.index') }}" class="nav-link">
                                <i class="fas fa-users"></i>
                                <p>Listado de Usuarios</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/usuarios/roles-permisos') }}" class="nav-link">
                                <i class="fas fa-user-shield"></i>
                                <p>Roles y Permisos</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/usuarios/roles-permisos') }}" class="nav-link">
                                <i class="fas fa-user-shield"></i>
                                <p>Listado de Roles</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/usuarios/roles-permisos') }}" class="nav-link">
                                <i class="fas fa-user-shield"></i>
                                <p>Listado de Permisos</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>