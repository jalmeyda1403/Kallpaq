<!DOCTYPE html>
<html>

<head>
    @include('layout.header')
    @livewireStyles
    @stack('styles')
   
</head>

<body class="sidebar-mini layout-fixed" style="height: auto;">
    <div class="wrapper">

        <!-- Navbar -->
        @include('layout.navbar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('layout.sidebar')


        <!-- Main content -->
        <div class="content-wrapper">
            @yield('content')
        </div>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
        </aside>

        <!-- Footer -->
        <footer class="main-footer">
            <strong>¡Mejorando Juntos!</strong>
            <div class="float-right d-none d-sm-inline-block">
                <b>Version  1.0 </b>
            </div>
        </footer>


    </div>

    @include('layout.footer')
    @livewireScripts
    @stack('scripts')

</body>

</html>
