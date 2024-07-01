<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="{{asset('vendor/adminlte/dist/img/kallpaq_ico.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light"> KALLPAQ v1.0</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            
            <div class="info">
                 
                @if (Auth::check() )
                <a href="#" class="d-block">  <i class="far fa-user nav-icon"></i> {{ auth()->user()->name }}</a>
                <span class="d-block small text-right">Bienvenido {{ auth()->user()->getRoleNames()->first() }}</span>
                @endif
            </div>
            
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            Configuración General
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-calendar nav-icon"></i>
                                <p>Periodo Actual</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-check-circle nav-icon"></i>
                                <p>Estados</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-users-cog nav-icon"></i>
                                <p>Roles</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-lock nav-icon"></i>
                                <p>Permisos</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Configuracion Riesgos -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-exclamation-triangle"></i>
                        <p>
                            Gestión Riesgos
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-check-circle nav-icon"></i>
                                <p>Estados</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-chart-pie nav-icon"></i>
                                <p>Matriz de Riesgos</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-tachometer-alt nav-icon"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                    </ul>
                </li>
                     
                <!-- Configuración Hallazgos -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-search"></i>
                        <p>
                            Gestión Hallazgos
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('/admin/hallazgos/estados') }}" class="nav-link">
                            <i class="fas fa-check-circle nav-icon"></i>
                                <p>Estados</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/hallazgos/matriz') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Matriz Hallazgos</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/hallazgos/oportunidades') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Matriz Oportunidades</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/hallazgos/dashboard') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Configuración Indicadores -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>
                            Gestión Indicadores
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('/admin/indicadores/estados') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Estados</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('indicadores.index') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Matriz de Indicadores</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/indicadores/dashboard') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Administración de Usuarios -->
                <li class="nav-item has-treeview {{ request()->routeIs('usuarios.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Gestión Usuarios
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
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>